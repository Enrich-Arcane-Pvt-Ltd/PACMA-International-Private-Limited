<?php

namespace App\Http\Controllers;

use App\Setting;
use App\PurchaseOrder;
use App\PurchaseOrderProduct;
use App\BankAccount;
use App\Bill;
use App\BillPayment;
use App\BillProduct;
use App\Branch;
use App\CustomField;
use App\Mail\BillPaymentCreate;
use App\Mail\BillSend;
use App\Mail\VenderBillSend;
use App\Product;
use App\ProductService;
use App\ProductServiceCategory;
use App\Transaction;
use App\Utility;
use App\Vender;
use App\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderController extends Controller
{
    protected $currencySymbol;


    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage purchase order')) {
            $purchOrders = PurchaseOrder::where('branch_id', \Auth::user()->branchId())->orderBy('id', 'DESC')->get();
            // $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $vender->prepend('All', '');

            // $status = Bill::$statues;

            // $query = Bill::where('created_by', '=', \Auth::user()->creatorId());
            // if(!empty($request->vender))
            // {
            //     $query->where('vender_id', '=', $request->vender);
            // }
            // if(!empty($request->bill_date))
            // {
            //     $date_range = explode(' - ', $request->bill_date);
            //     $query->whereBetween('bill_date', $date_range);
            // }

            // if(!empty($request->status))
            // {
            //     $query->where('status', '=', $request->status);
            // }
            // $bills = $query->get();

            // return view('purchaseOrder.index', compact('bills', 'vender', 'status'));
            $currencySymbol = $this->currencySymbol;
            return view('purchaseOrder.index', compact('purchOrders', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function create($vendorId)
    {
        if (\Auth::user()->can('create purchase order')) {
            $venders     = Vender::get()->pluck('name', 'id');
            $venders->prepend('Select Vender', '');

            $product_services = Product::select('id', DB::raw("CONCAT(barcode, ' - ', name) as fullname"))
                ->where('status', 1)
                ->orderBy('name')
                ->pluck('fullname', 'id');
            $product_services->prepend(__('Select Product'), '');
            $currencySymbol = $this->currencySymbol;
            return view('purchaseOrder.create', compact('venders', 'product_services', 'vendorId', 'currencySymbol'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function productDetails(Request $request)
    {
        $product = Product::with('productCategory', 'categoryDetails', 'brands')->find($request->product_id);
        if ($product) {
            $product->product_name = $product->name ?? '';
            $product->product_model = optional($product->brands)->name ?? '';
        }
        return json_encode($product);
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create purchase order')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'vender_id' => 'required',
                    'order_date' => 'required',
                    // 'required_by' => 'required',
                    'status' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $purchaseOrder            = new PurchaseOrder();
            $purchaseOrder->order_id   = $this->orderNumber();
            $purchaseOrder->branch_id = \Auth::user()->branchId();
            $purchaseOrder->vender_id = $request->vender_id;
            $purchaseOrder->order_date      = $request->order_date;
            $purchaseOrder->status         = $request->status;
            $purchaseOrder->required_by       = $request->required_by;
            $purchaseOrder->total_amount    = $request->total_amount;
            // $purchaseOrder->order_number   = !empty($request->order_number) ? $request->order_number : 0;
            // $purchaseOrder->discount_apply = isset($request->discount_apply) ? 1 : 0;
            $purchaseOrder->created_by     = \Auth::user()->creatorId();
            $purchaseOrder->save();
            // CustomField::saveData($bill, $request->customField);
            $purchOder = PurchaseOrder::where('branch_id', '=', \Auth::user()->branchId())->latest()->first();

            $products = $request->items;
            for ($i = 0; $i < count($products); $i++) {
                $amount = $products[$i]['quantity'] * $products[$i]['unit_cost'];

                $purchOrderProduct              = new PurchaseOrderProduct();
                $purchOrderProduct->purchase_order_id     = $purchOder->id;
                $purchOrderProduct->product_id  = $products[$i]['item'];
                $purchOrderProduct->quantity    = $products[$i]['quantity'];
                // $purchOrderProduct->tax         = $products[$i]['tax'];
                // $purchOrderProduct->discount    = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                $purchOrderProduct->unit_cost       = $products[$i]['unit_cost'];
                $purchOrderProduct->amount       = $amount;
                // $purchOrderProduct->description = $products[$i]['description'];
                $purchOrderProduct->save();
            }

            return redirect()->route('purchase-order.index', $purchOder->id)->with('success', __('Purchase Order successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function venderNumber()
    {
        $latest = Vender::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->customer_id + 1;
    }

    // public function show(Bill $bill)
    public function show($id)
    {
        if (\Auth::user()->can('view purchase order')) {
            $purchorders = PurchaseOrder::with('vender', 'branch', 'purchaseProducts', 'purchaseProducts.products')->find($id);

            if ($purchorders->branch_id = \Auth::user()->branchId()) {
                $creators = Branch::where('id', \Auth::user()->branchId())->first();
                // $billPayment = BillPayment::where('bill_id', $bill->id)->first();
                // $vendor      = $bill->vender;
                // $iteams      = $bill->items;

                // return view('bill.view', compact('bill', 'vendor', 'iteams', 'billPayment'));
                $currencySymbol = $this->currencySymbol;
                return view('purchaseOrder.view', compact('purchorders', 'creators', 'currencySymbol'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit purchase order')) {
            $purchase_order_number      = \Auth::user()->purchaseOrderNumberFormat($id);
            $venders          = Vender::get()->pluck('name', 'id');
            $product_services = Product::select('id', DB::raw("CONCAT(barcode, ' - ', name) as fullname"))
                ->where('status', 1)
                ->orderBy('name')
                ->pluck('fullname', 'id');
            $product_services->prepend(__('Select Product'), '');

            $purchorders = PurchaseOrder::with('purchaseProducts', 'purchaseProducts.products')->find($id);
            $currencySymbol = $this->currencySymbol;
            return view('purchaseOrder.edit', compact('venders', 'product_services', 'purchorders', 'purchase_order_number', 'currencySymbol'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (\Auth::user()->can('edit purchase order')) {

            if ($purchaseOrder->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'vender_id' => 'required',
                        'order_date' => 'required',
                        // 'required_by' => 'required',
                        'status' => 'required',
                        'items' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('purchase-order.index')->with('error', $messages->first());
                }

                $purchaseOrder->vender_id = $request->vender_id;;
                $purchaseOrder->order_date      = $request->order_date;
                $purchaseOrder->status         = $request->status;
                $purchaseOrder->required_by       = $request->required_by;
                $purchaseOrder->total_amount    = $request->total_amount;
                $purchaseOrder->created_by     = \Auth::user()->creatorId();
                $purchaseOrder->update();

                $products = $request->items;

                for ($i = 0; $i < count($products); $i++) {
                    $purchOrderProduct = PurchaseOrderProduct::find($products[$i]['id']);

                    $amount = $products[$i]['quantity'] * $products[$i]['unit_cost'];
                    if ($purchOrderProduct == null) {
                        $newPurchOrderProduct              = new PurchaseOrderProduct();
                        $newPurchOrderProduct->purchase_order_id     = $purchaseOrder->id;
                        if (isset($products[$i]['item'])) {
                            $newPurchOrderProduct->product_id = $products[$i]['item'];
                        }
                        $newPurchOrderProduct->quantity    = $products[$i]['quantity'];
                        $newPurchOrderProduct->unit_cost       = $products[$i]['unit_cost'];
                        $newPurchOrderProduct->amount       = $amount;
                        $newPurchOrderProduct->save();
                    } elseif ($purchOrderProduct != null) {
                        $purchOrderProduct->purchase_order_id     = $purchaseOrder->id;
                        if (isset($products[$i]['item'])) {
                            $purchOrderProduct->product_id = $products[$i]['item'];
                        }
                        $purchOrderProduct->quantity    = $products[$i]['quantity'];
                        $purchOrderProduct->unit_cost       = $products[$i]['unit_cost'];
                        $purchOrderProduct->amount       = $amount;
                        $purchOrderProduct->update();
                    }

                    // $billProduct->quantity    = $products[$i]['quantity'];
                    // $billProduct->tax         = $products[$i]['tax'];
                    // $billProduct->discount    = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    // $billProduct->price       = $products[$i]['price'];
                    // $billProduct->description = $products[$i]['description'];
                    // $billProduct->save();

                }


                return redirect()->route('purchase-order.edit', $purchaseOrder->id)->with('success', __('Bill successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // public function destroy(Bill $bill)
    public function destroy($id)
    {
        $purchorders = PurchaseOrder::find($id);
        // die(var_dump($purchorders->id));
        if (\Auth::user()->can('delete purchase order')) {
            if ($purchorders->branch_id == \Auth::user()->branchId()) {
                $purchorders->delete();

                $purchProducts = PurchaseOrderProduct::where('purchase_order_id', $id)->get();
                foreach ($purchProducts as $purchProduct) {
                    $purchProduct->delete();
                }
                // if($bill->vender_id != 0)
                // {
                //     Utility::userBalance('vendor', $bill->vender_id, $bill->getTotal(), 'debit');
                // }
                // BillProduct::where('bill_id', '=', $bill->id)->delete();

                return redirect()->route('purchase-order.index')->with('success', __('Purchase Orders successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function billNumber()
    {
        $latest = Bill::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->bill_id + 1;
    }

    function orderNumber()
    {
        $latest = PurchaseOrder::where('branch_id', '=', \Auth::user()->branchId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->id + 1;
    }

    public function product(Request $request)
    {
        $data['product']     = $product = ProductService::find($request->product_id);
        $data['unit']        = !empty($product->unit()) ? $product->unit()->name : '';
        $data['taxRate']     = $taxRate = $product->taxRate($product->tax_id);
        $data['taxes']       = $product->tax($product->tax_id);
        $salePrice           = $product->sale_price;
        $quantity            = 1;
        $taxPrice            = ($taxRate / 100) * ($salePrice * $quantity);
        $data['totalAmount'] = ($salePrice * $quantity) + $taxPrice;

        return json_encode($data);
    }

    public function productDestroy(Request $request)
    {
        if (\Auth::user()->can('delete purchase order')) {
            PurchaseOrderProduct::where('id', '=', $request->id)->delete();

            return redirect()->back()->with('success', __('Bill product successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function sent($id)
    {
        if (\Auth::user()->can('send bill')) {
            $bill            = Bill::where('id', $id)->first();
            $bill->send_date = date('Y-m-d');
            $bill->status    = 1;
            $bill->save();

            $vender = Vender::where('id', $bill->vender_id)->first();

            $bill->name = !empty($vender) ? $vender->name : '';
            $bill->bill = \Auth::user()->billNumberFormat($bill->bill_id);

            $billId    = Crypt::encrypt($bill->id);
            $bill->url = route('bill.pdf', $billId);

            Utility::userBalance('vendor', $vender->id, $bill->getTotal(), 'credit');

            try {
                Mail::to($vender->email)->send(new BillSend($bill));
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Bill successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function resent($id)
    {
        if (\Auth::user()->can('send bill')) {
            $bill = Bill::where('id', $id)->first();

            $vender = Vender::where('id', $bill->vender_id)->first();

            $bill->name = !empty($vender) ? $vender->name : '';
            $bill->bill = \Auth::user()->billNumberFormat($bill->bill_id);

            $billId    = Crypt::encrypt($bill->id);
            $bill->url = route('bill.pdf', $billId);

            try {
                Mail::to($vender->email)->send(new BillSend($bill));
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Bill successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payment($bill_id)
    {
        if (\Auth::user()->can('create payment bill')) {
            $bill    = Bill::where('id', $bill_id)->first();
            $venders = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('bill.payment', compact('venders', 'categories', 'accounts', 'bill'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function createPayment(Request $request, $bill_id)
    {
        if (\Auth::user()->can('create payment bill')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'date' => 'required',
                    'amount' => 'required',
                    'account_id' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $billPayment                 = new BillPayment();
            $billPayment->bill_id        = $bill_id;
            $billPayment->date           = $request->date;
            $billPayment->amount         = $request->amount;
            $billPayment->account_id     = $request->account_id;
            $billPayment->payment_method = 0;
            $billPayment->reference      = $request->reference;
            $billPayment->description    = $request->description;
            $billPayment->save();

            $bill  = Bill::where('id', $bill_id)->first();
            $due   = $bill->getDue();
            $total = $bill->getTotal();

            if ($bill->status == 0) {
                $bill->send_date = date('Y-m-d');
                $bill->save();
            }

            if ($due <= 0) {
                $bill->status = 4;
                $bill->save();
            } else {
                $bill->status = 3;
                $bill->save();
            }
            $billPayment->user_id    = $bill->vender_id;
            $billPayment->user_type  = 'Vender';
            $billPayment->type       = 'Partial';
            $billPayment->created_by = \Auth::user()->id;
            $billPayment->payment_id = $billPayment->id;
            $billPayment->category   = 'Bill';
            $billPayment->account    = $request->account_id;
            Transaction::addTransaction($billPayment);

            $vender = Vender::where('id', $bill->vender_id)->first();

            $payment         = new BillPayment();
            $payment->name   = $vender['name'];
            $payment->method = '-';
            $payment->date   = \Auth::user()->dateFormat($request->date);
            $payment->amount = \Auth::user()->priceFormat($request->amount);
            $payment->bill   = 'bill ' . \Auth::user()->billNumberFormat($billPayment->bill_id);

            Utility::userBalance('vendor', $bill->vender_id, $request->amount, 'debit');

            Utility::bankAccountBalance($request->account_id, $request->amount, 'debit');

            try {
                Mail::to($vender['email'])->send(new BillPaymentCreate($payment));
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Payment successfully added.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }
    }

    public function paymentDestroy(Request $request, $bill_id, $payment_id)
    {

        if (\Auth::user()->can('delete payment bill')) {
            $payment = BillPayment::find($payment_id);
            BillPayment::where('id', '=', $payment_id)->delete();

            $bill = Bill::where('id', $bill_id)->first();

            $due   = $bill->getDue();
            $total = $bill->getTotal();

            if ($due > 0 && $total != $due) {
                $bill->status = 3;
            } else {
                $bill->status = 2;
            }

            Utility::userBalance('vendor', $bill->vender_id, $payment->amount, 'credit');
            Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

            $bill->save();
            $type = 'Partial';
            $user = 'Vender';
            Transaction::destroyTransaction($payment_id, $type, $user);

            return redirect()->back()->with('success', __('Payment successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function venderBill(Request $request)
    {
        if (\Auth::user()->can('manage vender bill')) {

            $status = Bill::$statues;

            $query = Bill::where('vender_id', '=', \Auth::user()->vender_id)->where('status', '!=', '0')->where('created_by', \Auth::user()->creatorId());

            if (!empty($request->vender)) {
                $query->where('id', '=', $request->vender);
            }
            if (!empty($request->bill_date)) {
                $date_range = explode(' - ', $request->bill_date);
                $query->whereBetween('bill_date', $date_range);
            }

            if (!empty($request->status)) {
                $query->where('status', '=', $request->status);
            }
            $bills = $query->get();


            return view('bill.index', compact('bills', 'status', 'vender'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function venderBillShow($bill_id)
    {
        if (\Auth::user()->can('show bill')) {
            $bill = Bill::where('id', $bill_id)->first();

            if ($bill->created_by == \Auth::user()->creatorId()) {
                $vendor = $bill->vender;
                $iteams = $bill->items;

                return view('bill.view', compact('bill', 'vendor', 'iteams'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vender(Request $request)
    {
        $vender = Vender::where('id', '=', $request->id)->first();
        $branch = Branch::where('id', '=', \Auth::user()->branchId())->first();

        return view('purchaseOrder.vender_detail', compact('vender', 'branch'));
    }


    public function venderBillSend($bill_id)
    {
        return view('vender.bill_send', compact('bill_id'));
    }

    public function venderBillSendMail(Request $request, $bill_id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $email = $request->email;
        $bill  = Bill::where('id', $bill_id)->first();

        $vender     = Vender::where('id', $bill->vender_id)->first();
        $bill->name = !empty($vender) ? $vender->name : '';
        $bill->bill = \Auth::user()->billNumberFormat($bill->bill_id);

        $billId    = Crypt::encrypt($bill->id);
        $bill->url = route('bill.pdf', $billId);

        try {
            Mail::to($email)->send(new VenderBillSend($bill));
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        return redirect()->back()->with('success', __('Bill successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function shippingDisplay(Request $request, $id)
    {
        $bill = Bill::find($id);

        if ($request->is_display == 'true') {
            $bill->billing_display = 1;
        } else {
            $bill->billing_display = 0;
        }
        $bill->save();

        return redirect()->back()->with('success', __('Shipping address status successfully changed.'));
    }

    public function duplicate($bill_id)
    {
        if (\Auth::user()->can('duplicate bill')) {
            $bill = Bill::where('id', $bill_id)->first();

            $duplicateBill                   = new Bill();
            $duplicateBill->bill_id          = $this->billNumber();
            $duplicateBill->vender_id        = $bill['vender_id'];
            $duplicateBill->bill_date        = date('Y-m-d');
            $duplicateBill->due_date         = $bill['due_date'];
            $duplicateBill->send_date        = null;
            $duplicateBill->category_id      = $bill['category_id'];
            $duplicateBill->order_number     = $bill['order_number'];
            $duplicateBill->status           = 0;
            $duplicateBill->billing_display = $bill['billing_display'];
            $duplicateBill->created_by       = $bill['created_by'];
            $duplicateBill->save();

            if ($duplicateBill) {
                $billProduct = BillProduct::where('bill_id', $bill_id)->get();
                foreach ($billProduct as $product) {
                    $duplicateProduct             = new BillProduct();
                    $duplicateProduct->bill_id    = $duplicateBill->id;
                    $duplicateProduct->product_id = $product->product_id;
                    $duplicateProduct->quantity   = $product->quantity;
                    $duplicateProduct->tax        = $product->tax;
                    $duplicateProduct->discount   = $product->discount;
                    $duplicateProduct->price      = $product->price;
                    $duplicateProduct->save();
                }
            }


            return redirect()->back()->with('success', __('Bill duplicate successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewBill($template, $color)
    {
        $objUser  = \Auth::user();
        $settings = Utility::settings();
        $bill     = new Bill();

        $vendor                   = new \stdClass();
        $vendor->email            = '<Email>';
        $vendor->shipping_name    = '<Vendor Name>';
        $vendor->shipping_country = '<Country>';
        $vendor->shipping_state   = '<State>';
        $vendor->shipping_city    = '<City>';
        $vendor->shipping_phone   = '<Vendor Phone Number>';
        $vendor->shipping_zip     = '<Zip>';
        $vendor->shipping_address = '<Address>';
        $vendor->billing_name     = '<Vendor Name>';
        $vendor->billing_country  = '<Country>';
        $vendor->billing_state    = '<State>';
        $vendor->billing_city     = '<City>';
        $vendor->billing_phone    = '<Vendor Phone Number>';
        $vendor->billing_zip      = '<Zip>';
        $vendor->billing_address  = '<Address>';

        $totalTaxPrice = 0;
        $taxesData     = [];
        $items         = [];
        for ($i = 1; $i <= 3; $i++) {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach ($taxes as $k => $tax) {
                $taxPrice         = 10;
                $totalTaxPrice    += $taxPrice;
                $itemTax['name']  = 'Tax ' . $k;
                $itemTax['rate']  = '10 %';
                $itemTax['price'] = '$10';
                $itemTaxes[]      = $itemTax;
                if (array_key_exists('Tax ' . $k, $taxesData)) {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                } else {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $bill->bill_id    = 1;
        $bill->issue_date = date('Y-m-d H:i:s');
        $bill->due_date   = date('Y-m-d H:i:s');
        $bill->items      = $items;

        $bill->totalTaxPrice = 60;
        $bill->totalQuantity = 3;
        $bill->totalRate     = 300;
        $bill->totalDiscount = 10;
        $bill->taxesData     = $taxesData;

        $preview      = 1;
        $color        = '#' . $color;
        $font_color   = Utility::getFontColor($color);
        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        return view('bill.templates.' . $template, compact('bill', 'preview', 'color', 'img', 'settings', 'vendor', 'font_color'));
    }

    public function pdf($purchase_order_id)
    {
        $settings = Utility::settings();
        $orderId   = Crypt::decrypt($purchase_order_id);

        $purchase_order  = PurchaseOrder::with('vender', 'purchaseProducts', 'purchaseProducts.products')->where('id', $orderId)->first();
        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $purchase_order->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $vendor = $purchase_order->vender;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach ($purchase_order->purchaseProducts as $product) {
            $item           = new \stdClass();
            $item->name     = !empty($product->products) ? $product->products->name : '';
            $item->quantity = $product->quantity;
            $item->unit_cost    = $product->unit_cost;
            $item->amount    = $product->amount;

            $items[] = $item;
        }

        $purchase_order->items         = $items;

        //Set your logo
        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        if ($purchase_order) {
            $color      = '#' . $settings['bill_color'];
            $font_color = Utility::getFontColor($color);

            return view('purchaseOrder.templates.' . $settings['bill_template'], compact('purchase_order', 'color', 'settings', 'vendor', 'img', 'font_color'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function print($purchase_order_id)
    {
        $settings = Utility::settings();
        $orderId   = Crypt::decrypt($purchase_order_id);

        $purchase_order  = PurchaseOrder::with('vender', 'purchaseProducts', 'purchaseProducts.products')->where('id', $orderId)->first();
        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $purchase_order->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $vendor = $purchase_order->vender;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach ($purchase_order->purchaseProducts as $product) {
            $item           = new \stdClass();
            $item->name     = !empty($product->products) ? $product->products->name : '';
            $item->quantity = $product->quantity;
            $item->unit_cost    = $product->unit_cost;
            $item->amount    = $product->amount;

            $items[] = $item;
        }

        $purchase_order->items         = $items;

        //Set your logo
        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        if ($purchase_order) {
            $print = 1;
            $color      = '#' . $settings['bill_color'];
            $font_color = Utility::getFontColor($color);
            $currencySymbol = $this->currencySymbol;
            return view('purchaseOrder.templates.' . $settings['bill_template'], compact('purchase_order', 'print', 'color', 'settings', 'vendor', 'img', 'font_color', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // SMS
    public function sms($purchase_order_id)
    {
        $settings = Utility::settings();
        $orderId  = Crypt::decrypt($purchase_order_id);

        $purchase_order = PurchaseOrder::with('vender', 'purchaseProducts', 'purchaseProducts.products')
            ->where('id', $orderId)
            ->first();
        $data = DB::table('settings')->where('created_by', '=', $purchase_order->created_by)->get();

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        $vendor = $purchase_order->vender;

        $items = [];
        foreach ($purchase_order->purchaseProducts as $product) {
            $item = new \stdClass();
            $item->name = !empty($product->products) ? $product->products->name : '';
            $item->quantity = $product->quantity;
            $item->unit_cost = $product->unit_cost;
            $item->amount = $product->amount;
            $item->barcode = !empty($product->products) ? $product->products->barcode : '';

            $items[] = $item;
        }

        // Prepare SMS message
        $total = 0;
        $smsContent = "Dear " . ($vendor->billing_name ?? 'Supplier') . ",\n\n";
        $smsContent .= "  \n\n";
        $smsContent .= "New Purchase Order Details From " . config('app.name', 'PACMA International Private Limited') . "\n\n";
        $smsContent .= "    \n\n";
        $smsContent .= "---ITEMS LIST--- \n";
        $smsContent .= "   \n\n";

        foreach ($items as $item) {
            $smsContent .= "# Product: " . $item->barcode . " - " . $item->name . "\n";
            $smsContent .= "# Price: " . number_format($item->unit_cost, 2) . "\n";
            $smsContent .= "# Quantity: " . $item->quantity . "\n";
            $smsContent .= "# Amount: " . number_format($item->amount, 2) . "\n";
            $smsContent .= "**************************\n";

            // Calculate total
            $total += $item->amount;
        }
        // Add total and thank you note
        $smsContent .= "   \n";
        $smsContent .= "\n- Total Amount: LKR." . number_format($total, 2) . "\n";
        $smsContent .= "- PO Number: " . \App\Utility::purchaseOrderNumberFormat($settings, $purchase_order->id) . "\n";
        $smsContent .= "- Order Date: " . ($purchase_order->order_date ?? "-n/a-") . "\n";
        $smsContent .= "                    \n\n";
        $smsContent .= "\nThank you!\n(This is an auto-generated message, please do not reply.)";

        if ($purchase_order) {
            try {
                // $response = SMS::sendSMS('94710682696', $smsContent);
                $response = SMS::sendSMS($vendor->contact_phone, $smsContent);
                if ($response == true) {
                    return redirect()->back()->with('success', __('SMS sent successfully'));
                } else {
                    return redirect()->back()->with('error', __('Failed to send SMS.'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Failed to send SMS: ') . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    // End:SMS

    // Manual Whatsapp
    public function whatsappManual($purchase_order_id)
    {
        $settings = Utility::settings();
        $orderId  = Crypt::decrypt($purchase_order_id);

        $purchase_order = PurchaseOrder::with('vender', 'purchaseProducts', 'purchaseProducts.products')
            ->where('id', $orderId)
            ->first();
        $data = DB::table('settings')->where('created_by', '=', $purchase_order->created_by)->get();

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        $vendor = $purchase_order->vender;

        $items = [];
        foreach ($purchase_order->purchaseProducts as $product) {
            $item = new \stdClass();
            $item->name = !empty($product->products) ? $product->products->name : '';
            $item->quantity = $product->quantity;
            $item->unit_cost = $product->unit_cost;
            $item->amount = $product->amount;
            $item->barcode = !empty($product->products) ? $product->products->barcode : '';

            $items[] = $item;
        }

        // Prepare WhatsApp message
        $total = 0;
        $whatsappMessage = "*New Purchase Order* \n\n";
        $whatsappMessage .= "Hello " . ($vendor->billing_name ?? 'Supplier') . ", this is our new purchase order. Below are the details: \n\n";
        foreach ($items as $item) {
            $whatsappMessage .= "# *Product*: " . $item->barcode . " - " . $item->name . " \n";
            $whatsappMessage .= "# *Price*: LKR " . number_format($item->unit_cost, 2) . " \n";
            $whatsappMessage .= "# *Quantity*: " . $item->quantity . "\n";
            $whatsappMessage .= "# *Amount*: LKR " . number_format($item->amount, 2) . " \n";
            $whatsappMessage .= "----------------------------- \n";

            // Calculate total
            $total += $item->amount;
        }
        $whatsappMessage .= "- *Total Amount*: LKR " . number_format($total, 2) . "\n";
        $whatsappMessage .= "- *PO Number*: " . \App\Utility::purchaseOrderNumberFormat($settings, $purchase_order->id) . "\n";
        $whatsappMessage .= "- *Order Date*: " . ($purchase_order->order_date ?? "-n/a-") . "\n";
        $whatsappMessage .= "\n Thank you!";

        $encodedMessage = urlencode($whatsappMessage);


        $whatsappNumber = SMS::validateNumber('94710682696');

        $whatsappLink = "https://wa.me/{$whatsappNumber}?text=" . $encodedMessage;
        // dd($encodedMessage);
        if ($whatsappNumber == null) {
            return redirect()->back()->with('error', __('Failed to send Whatsapp Message.'));
        } else {
            return redirect()->away($whatsappLink);
        }
    }

    // End:Whatsapp
    public function saveBillTemplateSettings(Request $request)
    {
        $post = $request->all();
        unset($post['_token']);

        if (isset($post['bill_template']) && (!isset($post['bill_color']) || empty($post['bill_color']))) {
            $post['bill_color'] = "ffffff";
        }

        foreach ($post as $key => $data) {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $data,
                    $key,
                    \Auth::user()->creatorId(),
                ]
            );
        }

        return redirect()->back()->with('success', __('Bill Setting updated successfully'));
    }

    public function items(Request $request)
    {
        $items = PurchaseOrderProduct::where('purchase_order_id', $request->purchase_order_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }

    public function lastPurchasePrice(Request $request)
    {
        $lastPurchasePrice = PurchaseOrderProduct::select('unit_cost as last_purchase_price')->where('product_id', $request->product_id)->latest()->first();

        return json_encode($lastPurchasePrice);
    }
}
