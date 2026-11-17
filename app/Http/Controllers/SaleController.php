<?php

namespace App\Http\Controllers;

use App\AdvancePayment;
use App\Sale;
use App\Customer;
use App\Product;
use App\TimePeriod;
use App\InterestRate;
use App\Invoice;
// use App\HirePurchaseContract;
use App\Installment;
use App\Receipt;
use App\User;
use App\Inventory;
use App\BankAccount;
use App\SaleItem;
use App\CardInterestExpense;
use App\FinancialEntry;
use App\HirePurchaseDetail;
use App\ProductCategory;
use App\ProductWarranty;
use App\StockTransaction;
use App\Utility;
use App\WaittingSale;
use App\ProductWarrantySerial;
use App\SMS;
use App\Setting;
use App\GrnProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    protected $currencySymbol;

    public function __construct(Setting $setting)
    {
        $this->currencySymbol = $setting::where('name', 'site_currency_symbol')->value('value');
    }

    // public function index()
    // {
    //     // dd(ProductWarrantySerial::where('status', 0)->get(), "warra");
    //     if (\Auth::user()->can('manage sale')) {
    //         // $query = Sale::select(['sales.id', 'sales.sale_id', 'customers.name', 'sales.sale_type', 'sales.status', 'sales.invoice_id', 'invoices.invoice_code',
    //         //                     'sales.customer_type', 'sales.customer_name']);
    //         $query = Sale::select(['sales.*', 'customers.name', 'invoices.invoice_code'])
    //             ->leftJoin('customers', 'customers.id', 'sales.customer_id')
    //             ->leftJoin('invoices', 'invoices.id', 'sales.invoice_id')
    //             ->whereIn('sales.status', [1, 2, 3]);

    //         if (\Auth::user()->branchId() != 0) {
    //             $query->where('sales.branch_id', '=', \Auth::user()->branchId());
    //         }

    //         $sales = $query->orderBy('sales.id', 'DESC')->paginate(100);
    //         return view('sale.index', compact('sales'));
    //     } else {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    public function directSales()
    {
        return $this->getSalesData(0, 'sale.direct_sales'); // direct_sales view
    }

    public function hireSales()
    {
        return $this->getSalesData(1, 'sale.hire_sales'); // hire_sales view
    }

    private function getSalesData($saleType, $view)
    {
        if (\Auth::user()->can('manage sale')) {
            $query = Sale::select(['sales.*', 'customers.name', 'invoices.invoice_code'])
                ->leftJoin('customers', 'customers.id', 'sales.customer_id')
                ->leftJoin('invoices', 'invoices.id', 'sales.invoice_id')
                ->where('sales.sale_type', $saleType)
                ->whereIn('sales.status', [1, 2, 3]);

            if (\Auth::user()->branchId() != 0) {
                $query->where('sales.branch_id', '=', \Auth::user()->branchId());
            }

            $sales = $query->orderBy('sales.id', 'DESC')->paginate(100);
            return view($view, compact('sales'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function getWarrantySerials($productId)
    {
        $branchId = \Auth::user()->branchId();
        $grnQuery = \App\Grn::query();
        if (!empty($branchId)) {
            $grnQuery->where('branch_id', $branchId);
        }
        $grnProducts = GrnProduct::where('product_id', $productId)
            ->whereIn('grn_id', $grnQuery->select('id'))->get();

        if ($grnProducts->isEmpty() && !empty($branchId)) {
            $grnProducts = GrnProduct::where('product_id', $productId)->get();
        }

        if ($grnProducts->isEmpty()) {
            return response()->json(["No related GRN products found"]); // No related GRN product found
        }

        $grnProductIds = $grnProducts->pluck('id');
        // Retrieve warranty serials
        $warrantySerials = ProductWarrantySerial::whereIn('grn_product_id', $grnProductIds)
            ->where('status', 0)
            ->orderBy('id')
            ->get(['id', 'grn_product_id', 'serial_number', 'warranty_number', 'warranty_period', 'warranty_document', 'additional_warranty_description']);

        // Check if warranty serials are found
        if ($warrantySerials->isEmpty()) {
            return response()->json(["message" => "No warranty serials found"], 404);
        }

        $lines = $grnProducts->keyBy('id');
        $available = $warrantySerials->filter(function ($unit) use ($lines) {
            try {
                $batch = \App\Services\GrnStockBatch::find($lines->get($unit->grn_product_id));
            } catch (\Illuminate\Validation\ValidationException $error) { return false; }
            if ((int) $batch->status !== 1 || $batch->remain_qty < 1) return false;
            $unit->inventory_id = $batch->id;
            $unit->purchase_price = $batch->purchase_price;
            $unit->sale_price = $batch->sale_price;
            $unit->hire_price = $batch->hire_price;
            $unit->remain_qty = $batch->remain_qty;
            return true;
        })->values();
        return response()->json($available);
    }

    private function getSaleProductsCatalog($branchId)
    {
        $invQuery = Inventory::select('product_id', \DB::raw('SUM(remain_qty) as remain_qty'))
            ->where('status', 1);
        if (!empty($branchId)) {
            $invQuery->where('branch_id', $branchId);
        }
        $stockMap = $invQuery->groupBy('product_id')->pluck('remain_qty', 'product_id');

        $allProducts = Product::with(['brands', 'productCategory', 'productSubCategory', 'categoryDetails', 'subCategoryDetails'])
            ->where('status', 1)
            ->get();

        return $allProducts->map(function ($prod) use ($stockMap) {
            $item = new Inventory;
            $item->product_id = $prod->id;
            $item->remain_qty = (float) ($stockMap[$prod->id] ?? 0);
            $item->setRelation('products', $prod);
            return $item;
        })->sortBy(function ($item) {
            return ($item->products->productCategory->name ?? '') . ' - ' . ($item->products->name ?? '');
        });
    }

    public function create()
    {
        if (\Auth::user()->can('create sale')) {
            $customers = Customer::whereIn('type', [0, 2])->where('sale_customer_type', 0)->where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();

            // Get the most recent customer by assuming the latest creation or update time
            $recentCustomer = $customers->sortByDesc('created_at')->first();

            $guarantors = Customer::whereIn('type', [1, 2])->where('sale_customer_type', 0)->where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();

            $saleTypes = Sale::$saleType;

            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');


            $users = User::where('is_active', 1)->whereNotIn('type', ['company', 'super admin'])->get();

            $productWarranties = ProductWarranty::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('warranty_card_no')->get();

            $products = $this->getSaleProductsCatalog(\Auth::user()->branchId());

            $timePeriods = TimePeriod::where('status', 1)->get();
            return view('sale.create', compact('customers', 'recentCustomer', 'guarantors', 'saleTypes', 'products', 'timePeriods', 'banks', 'users', 'productWarranties'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function directSaleCreate()
    {

        if (\Auth::user()->can('create sale')) {
            $customers = Customer::whereIn('type', [0, 2])->where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();

            $guarantors = Customer::whereIn('type', [1, 2])->where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();

            $saleTypes = Sale::$saleType;
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('bank', 'id');

            $banks->prepend('Select Bank', '');

            $users = User::where('is_active', 1)->whereNotIn('type', ['company', 'super admin'])->get();

            // $productWarranties = ProductWarranty::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('warranty_card_no')->get();
            $productWarranties = ProductWarrantySerial::where('status', 0)->get();
            // dd($productWarranties);

            $products = $this->getSaleProductsCatalog(\Auth::user()->branchId());

            $timePeriods = TimePeriod::where('status', 1)->get();

            return view('sale.direct_sale_create', compact('customers', 'guarantors', 'saleTypes', 'products', 'timePeriods', 'banks', 'users', 'productWarranties'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getDetails($id)
    {
        $branchId = \Auth::user()->branchId();
        $query = Inventory::select('id As inventory_id', 'product_id', 'sale_price', 'hire_price', 'purchase_price', \DB::raw('SUM(remain_qty) as remain_qty'))
            ->with('products', 'products.categoryDetails', 'grns.grnProducts')
            ->where('status', 1)
            ->where('remain_qty', '>', 0)
            ->where('product_id', $id);

        if (!empty($branchId)) {
            $query->where('branch_id', $branchId);
        }

        $products = $query->groupBy('product_id')->get();
        if ($products->isEmpty() && !empty($branchId)) {
            $products = Inventory::select('id As inventory_id', 'product_id', 'sale_price', 'hire_price', 'purchase_price', \DB::raw('SUM(remain_qty) as remain_qty'))
                ->with('products', 'products.categoryDetails', 'grns.grnProducts')
                ->where('status', 1)
                ->where('remain_qty', '>', 0)
                ->where('product_id', $id)
                ->groupBy('product_id')
                ->get();
        }

        return response()->json($products ? $products : [])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function getInterestRate($id)
    {
        $InterestRate = InterestRate::find($id);
        echo json_encode($InterestRate);
    }

    function customerNumber()
    {
        $latest = Customer::latest()->first();

        if (!$latest) {
            return 1;
        }
        return $latest->id + 1;
    }

    public function store(Request $request)
    {
        return \DB::transaction(function () use ($request) {
            return $this->storeWithStockBatch($request);
        });
    }

    protected function storeWithStockBatch(Request $request)
    {
        // dd($request);
        $userId = Auth::user()->id;
        if (\Auth::user()->can('create sale')) {
            $saleType = $request->sale_type;
            // Cash Sales ------------------------------------------------------
            if ($saleType == 0) {
                if ($request->modal_type == 1) {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'date_of_sale' => 'required',
                            'customer_type' => 'required',
                            'sale_type' => 'required',
                            'product' => 'required',
                            'unit_price' => 'required',
                            'quantity' => 'required',
                            'gross_amount' => 'required',
                            'net_amount' => 'required',
                            'bank' => 'required',
                            'inventory_id' => 'required',
                            'paid_amount' => 'required',
                            'paid_amount_in_words' => 'required',
                            'payment_method' => 'required',
                        ]
                    );
                } elseif ($request->modal_type == 2) {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'date_of_sale' => 'required',
                            'customer_type' => 'required',
                            'bank' => 'required',
                            'paid_amount' => 'required',
                            'paid_amount_in_words' => 'required',
                            'payment_method' => 'required',
                        ]
                    );
                }

                if ($request->customer_type == 1) {
                    $validator2 = \Validator::make(
                        $request->all(),
                        [
                            'customer' => 'required',
                        ]
                    );

                    if ($validator2->fails()) {
                        $messages = $validator2->getMessageBag();
                        return redirect()->back()->with('error', $messages->first());
                    }
                }
            }
            // Hire Sales ------------------------------------------------------
            elseif ($saleType == 1) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'date_of_sale' => 'required',
                        'customer' => 'required',
                        'guarantor' => 'required',
                        'sale_type' => 'required',
                        'product' => 'required',
                        'unit_price' => 'required',
                        'quantity' => 'required',
                        'gross_amount' => 'required',
                        'gross_hire_price' => 'required',
                        'net_amount' => 'required',
                        'bank' => 'required',
                        'inventory_id' => 'required',
                        'paid_amount' => 'required',
                        'paid_amount_in_words' => 'required',
                        'payment_method' => 'required',
                        'down_payment' => 'required',
                        'outstanding_amount' => 'required',
                        'installment' => 'required',
                        'interest_rate' => 'required',
                        'interest_amount' => 'required',
                        'total_due_amount' => 'required',
                        'monthly_installment' => 'required',
                        'installment_date' => 'required',
                        'due_date' => 'required',
                    ]
                );
            }

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if ($request->sale_type == 1) {
                if ((float)$request->down_payment > (float)$request->net_amount) {
                    return redirect()->back()->with('error', __('Down Payment cannot exceed the Cash Price / Net Amount!'));
                }
                if ((float)$request->gross_hire_price < (float)$request->net_amount) {
                    return redirect()->back()->with('error', __('Gross Hire Price cannot be less than the Cash Price / Net Amount!'));
                }
            }

            if ($request->modal_type == 2 && $request->sale_type == 0) {
                $items = [];
                $requested = [];
                $selectedUnits = [];
                foreach ($request->items as $item) {
                    $item = \App\Services\SaleStockBatch::prepare($item);
                    $batchId = $item['inventory_id'];
                    if (!empty($item['product_warranty_serial_id'])) {
                        $unitId = $item['product_warranty_serial_id'];
                        if (in_array($unitId, $selectedUnits)) {
                            throw \Illuminate\Validation\ValidationException::withMessages(['items' => __('The same serial cannot be sold twice in one sale.')]);
                        }
                        $selectedUnits[] = $unitId;
                    }
                    $requested[$batchId] = ($requested[$batchId] ?? 0) + $item['quantity'];
                    if ($requested[$batchId] > $item['available_quantity']) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['items' => __('Quantity exceeds available stock for this batch.')]);
                    }
                    $items[] = $item;
                }
                $request->merge(['items' => $items]);
            } else {
                $request->merge(\App\Services\SaleStockBatch::prepare($request->all()));
            }
            if ($request->quantity > $request->available_quantity) {
                return redirect()->back()->with('error', __('No Available Quantity!'));
            }

            if (isset($request->waitting_sale_id)) {
                $userId = $request->created_by;
            }

            // new working customer
            if ($request->customer_type == 2) {
                $newCustomer = new customer();
                $newCustomer->customer_id = $this->customerNumber();
                $newCustomer->sale_customer_type = 1; //direct sale customer
                $newCustomer->title = $request->customer_title;
                $newCustomer->name = ucwords(strtolower(trim($request->customer_name)));
                $newCustomer->billing_name = ucwords(strtolower(trim($request->customer_name)));
                $newCustomer->billing_address =  ucwords(strtolower(trim($request->customer_address)));
                $newCustomer->nic_no = strtoupper($request->customer_nic);
                $newCustomer->address =  ucwords(strtolower(trim($request->customer_address)));
                $newCustomer->phone_mobile = $request->customer_phone_no;
                $newCustomer->branch_id = \Auth::user()->branchId();
                $newCustomer->created_by = \Auth::user()->authId();
                $newCustomer->save();
            }
            // END:new working customer

            $maxId = Invoice::max('id');
            if ($request->sale_type == 0) {
                $invoiceCode = "R" . str_pad(($maxId + 1), 6, "0", STR_PAD_LEFT);
            } elseif ($request->sale_type == 1) {
                $invoiceCode = "H" . str_pad(($maxId + 1), 6, "0", STR_PAD_LEFT);
            }

            $invoice = new Invoice();
            $invoice->branch_id = \Auth::user()->branchId();
            $invoice->invoice_code = $invoiceCode;
            if ($request->customer_type == 1) {
                $invoice->customer_id = $request->customer;
            } elseif ($request->customer_type == 2) {
                $invoice->customer_id = $newCustomer->id;
            }
            $invoice->guarantor_id = $request->guarantor;
            $invoice->bank_id = $request->bank;
            $invoice->inventory_id = $request->inventory_id;
            $invoice->issue_date = $request->date_of_sale;
            $invoice->due_date = $request->due_date;
            $invoice->status = 1;
            $invoice->created_by = $userId;
            $invoice->save();

            $invoiceId = $invoice->id;

            $status = 1;

            if ($request->payment_method == 'CARD') {
                if ($request->card_charge_amount != '') {
                    $cardCharge = new CardInterestExpense();
                    $cardCharge->amount = $request->card_charge_amount;
                    $cardCharge->invoice_id = $invoiceId;
                    $cardCharge->status = 1;
                    $cardCharge->save();
                }
            }

            $maxSaleId = Sale::max('id');
            $saleCode = "S" . str_pad(($maxSaleId + 1), 6, "0", STR_PAD_LEFT);

            if (isset($request->waitting_sale_id)) {
                $userId = $request->created_by;
                $waittingSale = WaittingSale::find($request->waitting_sale_id);
                $waittingSale->sale_id = $maxSaleId;
                $waittingSale->sale_code = $saleCode;
                $waittingSale->invoice_id = $invoiceId;
                $waittingSale->status = 2;
                $waittingSale->approved_rejected_by = \Auth::user()->id;
                $waittingSale->update();
            }

            if ($request->modal_type == 1) {

                $sale             = new Sale();
                $sale->invoice_id       = $invoiceId;
                $sale->sale_id       = $saleCode;
                $sale->date_of_sale       = $request->date_of_sale;
                if ($request->customer_type == 1) {
                    $sale->customer_type       = $request->customer_type;
                    $sale->customer_id       = $request->customer;
                } elseif ($request->customer_type == 2) {
                    $sale->customer_type       = 1;
                    $sale->customer_id       = $newCustomer->id;
                }
                $sale->guarantor_id       = $request->guarantor;
                $sale->guarantor_2_id       = $request->guarantor_2;
                $sale->guarantor_3_id       = $request->guarantor_3;
                $sale->guarantor_1_known_duration = $request->guarantor_1_known_duration;
                $sale->guarantor_2_known_duration = $request->guarantor_2_known_duration;
                $sale->witness_1_name       = $request->witness_1_name;
                $sale->witness_1_address    = $request->witness_1_address;
                $sale->witness_1_nic        = $request->witness_1_nic;
                $sale->witness_2_name       = $request->witness_2_name;
                $sale->witness_2_address    = $request->witness_2_address;
                $sale->witness_2_nic        = $request->witness_2_nic;
                $sale->officer_id       = $request->officer_id;
                $sale->introducer_id       = $request->introducer_id;
                $sale->sale_type         = $request->sale_type;
                $sale->product_id     = $request->product;
                $sale->branch_id       = \Auth::user()->branchId();
                $sale->purchase_price     = $request->purchase_price;
                $sale->unit_price     = $request->unit_price;
                $sale->quantity     = $request->quantity;
                $sale->gross_amount     = $request->gross_amount;
                $sale->gross_hire_price = $request->gross_hire_price;
                $sale->discount     = $request->discount;
                $sale->net_amount     = $request->net_amount;
                $sale->advance_amount     = $request->advance_amount;

                $sale->paid_amount     = max(0, (float)$request->paid_amount);
                $sale->paid_amount_in_words     = $request->paid_amount_in_words;
                $sale->payment_method     = $request->payment_method;
                if ($request->payment_method == 'CARD') {
                    $sale->card_type     = $request->card_type;
                    if ($request->card_charge_amount != '') {
                        $sale->card_expence_id     = $cardCharge->id;
                    }
                    $sale->card_charge_amount     = $request->card_charge_amount;
                    $sale->total_amount     = $request->total_amount;
                } elseif ($request->payment_method == 'CHEQUE') {
                    $sale->cheque_number     = $request->cheque_number;
                    $sale->cheque_realised_date     = $request->realised_date;
                }


                if ($request->sale_type == 1) {
                    $excessAdvance = max(0, (float)$request->advance_amount - (float)$request->down_payment);
                    $status = 2;
                    $sale->hire_purchase_index     = $request->hire_purchase_index;
                    $sale->down_payment     = $request->down_payment;
                    $sale->outstanding_amount  = max(0, (float)$request->outstanding_amount);
                    $sale->installment     = $request->installment;
                    $sale->interest_rate     = max(0, (float)$request->interest_rate);
                    $sale->interest_amount     = max(0, (float)$request->interest_amount);
                    $sale->total_due_amount     = max(0, (float)$request->total_due_amount);
                    $sale->current_due_amount     = max(0, (float)$request->total_due_amount - $excessAdvance);
                    $sale->due_paid_amount     = $excessAdvance;
                    $sale->monthly_installment     = $request->monthly_installment;
                    $sale->installment_date     = $request->installment_date;
                    $sale->due_date     = $request->due_date;
                    $sale->document_charges     = $request->document_charges;
                }

                $sale->status     = $status;
                $sale->created_by     = $userId;
                $sale->bank_id     = $request->bank;
                $sale->inventory_id     = $request->inventory_id;
                $sale->traveling_charges     = $request->traveling_charges;
                $sale->modal_type     = $request->modal_type;
                $sale->save();



                $quantity = $request->quantity;
                while ($quantity > 0) {
                    $inventory = Inventory::where('branch_id', \Auth::user()->branchId())->where('status', 1)->lockForUpdate()->findOrFail($request->inventory_id);

                    if ($inventory->remain_qty > $quantity) {
                        $inventory->remain_qty = $inventory->remain_qty - $quantity;
                        $inventory->update();

                        $stockTransaction = new StockTransaction();
                        $stockTransaction->stock_master_id = 2;
                        $stockTransaction->inventory_id = $inventory->id;
                        $stockTransaction->product_id = $request->product;
                        $stockTransaction->quantity = $quantity;
                        $stockTransaction->date = $request->date_of_sale;
                        $stockTransaction->branch_id = \Auth::user()->branchId();
                        $stockTransaction->created_by     = \Auth::user()->id;
                        $stockTransaction->status     = 1;
                        $stockTransaction->save();

                        $quantity = 0;
                    } elseif ($inventory->remain_qty <= $quantity) {

                        $stockTransaction = new StockTransaction();
                        $stockTransaction->stock_master_id = 2;
                        $stockTransaction->inventory_id = $inventory->id;
                        $stockTransaction->product_id = $request->product;
                        $stockTransaction->quantity = $inventory->remain_qty;
                        $stockTransaction->date = $request->date_of_sale;
                        $stockTransaction->branch_id = \Auth::user()->branchId();
                        $stockTransaction->created_by     = $userId;
                        $stockTransaction->status     = 1;
                        $stockTransaction->save();

                        $quantity = $quantity - $inventory->remain_qty;
                        $inventory->remain_qty = 0;
                        $inventory->status = 0;
                        $inventory->update();
                    }
                }

                $productDetail = Product::find($request->product);

                // Cash Sales
                if ($request->sale_type == 0) {
                    $cashAmount = $request->net_amount - $request->advance_amount;

                    $backAccount = BankAccount::find($request->bank);
                    $productCategory = ProductCategory::find($productDetail->product_category_id);

                    Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 1, $cashAmount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 1, $cashAmount, 'credit');

                    if ($request->advance_amount != '' && $request->advance_amount > 0) {
                        Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, 5, $productCategory->income_chart_of_account_id, $sale->id, $invoiceId, $request->customer, 1, $request->advance_amount, 'debit');
                        Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, 5, $sale->id, $invoiceId, $request->customer, 1, $request->advance_amount, 'credit');
                    }

                    if ($request->discount != '' && $request->discount > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, 6, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 1, $request->discount, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, 6, $sale->id, $request->product, $invoiceId, $request->customer, 1, $request->discount, 'credit');
                    }

                    Utility::accountSalesTransaction($request->date_of_sale, 7, $productCategory->inventory_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 1, $request->purchase_price * $request->quantity, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_chart_of_account_id, 7, $sale->id, $request->product, $invoiceId, $request->customer, 1, $request->purchase_price * $request->quantity, 'credit');
                }

                // -----Hire Sales ------------------------
                elseif ($request->sale_type == 1) {
                    $cashAmount = max(0, (float)$request->down_payment - (float)$request->advance_amount);

                    // if($request->payment_method == 'CARD' || $request->payment_method == 'ONLINEPAYMENT') {
                    //     $backAccount = BankAccount::find($request->bank);
                    //     $productCategory = ProductCategory::find($productDetail->product_category_id);

                    //     Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $request->inventory_loan_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $cashAmount, 'debit');
                    //     Utility::accountSalesTransaction($request->date_of_sale, $request->inventory_loan_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $cashAmount, 'credit');

                    //     if ($request->advance_amount != '' && $request->advance_amount > 0) {
                    //         Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, 5, $request->inventory_loan_chart_of_account_id, $sale->id, $invoiceId, $request->customer, 2, $request->advance_amount, 'debit');
                    //         Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, $request->inventory_loan_chart_of_account_id, 5, $sale->id, $invoiceId, $request->customer, 2, $request->advance_amount, 'credit');
                    //     }

                    //     if ($request->discount != '' && $request->discount > 0) {
                    //         Utility::accountSalesTransaction($request->date_of_sale, 6, $request->inventory_loan_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->discount, 'debit');
                    //         Utility::accountSalesTransaction($request->date_of_sale, $request->inventory_loan_chart_of_account_id, 6, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->discount, 'credit');
                    //     }

                    //     if ($request->document_charges != '' && $request->document_charges > 0) {
                    //         Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $productCategory->dnl_charges_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->document_charges, 'debit');
                    //         Utility::accountSalesTransaction($request->date_of_sale, $productCategory->dnl_charges_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->document_charges, 'credit');
                    //     }
                    // }

                    $backAccount = BankAccount::find($request->bank);
                    $productCategory = ProductCategory::find($productDetail->product_category_id);

                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_control_chart_of_account_id, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $productCategory->inventory_loan_control_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'credit');

                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_chart_of_account_id, $productCategory->inventory_loan_control_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_control_chart_of_account_id, $productCategory->inventory_loan_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'credit');

                    if ($request->discount != '' && $request->discount > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, 6, $productCategory->inventory_loan_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->discount, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_chart_of_account_id, 6, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->discount, 'credit');
                    }

                    if ($request->document_charges != '' && $request->document_charges > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $productCategory->dnl_charges_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->document_charges, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->dnl_charges_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->document_charges, 'credit');
                    }

                    if ($request->down_payment != '' && $request->down_payment > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->down_payment, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->down_payment, 'credit');
                    }

                    Utility::accountSalesTransaction($request->date_of_sale, 7, $productCategory->inventory_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->purchase_price * $request->quantity, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_chart_of_account_id, 7, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->purchase_price * $request->quantity, 'credit');
                }

                if ($request->has('warranty_document')) {
                    $warrantyDocument = self::uploadWarrantyDocument($request);
                } else {
                    $warrantyDocument = "assets/img/products/default.png";
                }
                // Serial/Warranty :START
                $serialNumber = null;
                if (isset($request->waitting_sale_id)) {
                    $serialNumber = $request->product_warranty_serial_id ?? null;
                } elseif ($request->get('serial_card_no')) {
                    $serialNumber = ProductWarrantySerial::where('id', $request->product_warranty_serial_id)
                        ->first(['id'])->id ?? null;
                }
                // END

                if (!$request->hasFile('warranty_document') && $serialNumber) {
                    $receivedUnit = ProductWarrantySerial::find($serialNumber);
                    $warrantyDocument = optional($receivedUnit)->warranty_document ?: $warrantyDocument;
                }
                $hirePurchaseDetails = new HirePurchaseDetail();
                $hirePurchaseDetails->sale_id = $sale->id;
                // $hirePurchaseDetails->product_warranty_id = $request->product_warranty_id;
                $hirePurchaseDetails->warranty_period = $request->warranty_period;
                $hirePurchaseDetails->warranty_charges = $request->warranty_charges;
                $hirePurchaseDetails->warranty_document = $warrantyDocument;
                $hirePurchaseDetails->additional_warranty_description = $request->additional_warranty_description;
                if ($serialNumber) {
                    $hirePurchaseDetails->product_warranty_id = $serialNumber; // Assuming 'id' is the primary key
                    \DB::table('product_warranty_serials')
                        ->where('id', $serialNumber)
                        ->update(['status' => 1]);
                } else {
                    // Handle the case where the serial number does not exist
                    // e.g., set to null or throw an exception
                    $hirePurchaseDetails->product_warranty_id = null; // or handle as needed
                }
                $hirePurchaseDetails->save();

                if ($request->traveling_charges) {
                    $financialEntry = new FinancialEntry();
                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Traveling Charges';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->payment_method = 'CASH';
                    $financialEntry->amount = $request->traveling_charges;
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'traveling_charges';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->save();
                }
            } elseif ($request->modal_type == 2) {
                $sale             = new Sale();
                $sale->invoice_id       = $invoiceId;
                $sale->sale_id       = $saleCode;
                $sale->date_of_sale       = $request->date_of_sale;
                $sale->sale_type         = $request->sale_type;
                $sale->branch_id       = \Auth::user()->branchId();
                if ($request->customer_type == 1) {
                    $sale->customer_type       = $request->customer_type;
                    $sale->customer_id       = $request->customer;
                } elseif ($request->customer_type == 2) {
                    $sale->customer_type       = 1;
                    $sale->customer_id       = $newCustomer->id;
                }
                $sale->introducer_id       = $request->introducer_id;
                $sale->sale_type         = $request->sale_type;
                $sale->advance_amount     = $request->advance_amount;
                $sale->net_amount     = $request->fullTotalOfPayments;
                $sale->status     = $status;
                $sale->created_by     = $userId;
                $sale->bank_id     = $request->bank;
                // $sale->inventory_id     = $request->inventory_id;
                $sale->traveling_charges     = $request->traveling_charges;
                $sale->modal_type     = $request->modal_type;

                $sale->paid_amount     = $request->paid_amount;
                $sale->paid_amount_in_words     = $request->paid_amount_in_words;
                $sale->payment_method     = $request->payment_method;
                if ($request->payment_method == 'CARD') {
                    $sale->card_type     = $request->card_type;
                    if ($request->card_charge_amount != '') {
                        $sale->card_expence_id     = $cardCharge->id;
                    }
                    $sale->card_charge_amount     = $request->card_charge_amount;
                    $sale->total_amount     = $request->total_amount;
                } elseif ($request->payment_method == 'CHEQUE') {
                    $sale->cheque_number     = $request->cheque_number;
                    $sale->cheque_realised_date     = $request->realised_date;
                }

                $sale->save();

                if ($request->customer_type == 1) {
                    $customer = $request->customer;
                } elseif ($request->customer_type == 2) {
                    $customer = $newCustomer->id;
                }

                $itemCount = count($request->items);
                $totAdvance = $request->advance_amount;

                foreach ($request->items as $key => $item) {
                    $saleItem = new SaleItem();
                    $saleItem->sale_id =  $sale->id;
                    $saleItem->invoice_id =  $invoiceId;
                    $saleItem->date_of_sale =  $request->date_of_sale;
                    $saleItem->product_id = $item['product'];
                    $saleItem->branch_id = \Auth::user()->branchId();
                    $saleItem->purchase_price = $item['purchase_price'];
                    $saleItem->unit_price = $item['hire_price'];
                    $saleItem->quantity = $item['quantity'];
                    $saleItem->gross_amount = $item['gross_amount'];
                    $saleItem->discount = $item['discount'];
                    $saleItem->net_amount = $item['net_amount'];
                    $saleItem->status = 1;
                    $saleItem->inventory_id = $item['inventory_id'];

                    $warranty = null;
                    // Retrieve the warranty ID based on the serial number
                    if (!empty($item['serial_card_no'])) {
                        $warranty = \DB::table('product_warranty_serials')
                            ->where('id', $item['product_warranty_serial_id'])
                            ->first(); // Retrieve the first matching warranty

                        if ($warranty) {
                            // Save the warranty ID instead of the serial number
                            $saleItem->product_warranty_id = $warranty->id; // Save the ID of the warranty
                            // Update the status of the serial number
                            \DB::table('product_warranty_serials')
                                ->where('id', $warranty->id)
                                ->update(['status' => 1]);
                        } else {
                            // Handle the case where the warranty is not found
                            $saleItem->product_warranty_id = null; // or take appropriate action
                        }
                    } else {
                        $saleItem->product_warranty_id = null; // Handle appropriately if no serial number is selected
                    }

                    if (isset($item['warranty_document'])) {
                        $file = $item['warranty_document'];
                        $destinationPath = 'assets/documents/products';

                        $randomNumber = rand(1000, 9999);
                        $fileName = 'warranty_document' . "_" . $randomNumber . '.' . $file->getClientOriginalExtension();
                        $file->move($destinationPath, $fileName);
                        $saleItem->warranty_document = $destinationPath . '/' . $fileName;
                    } else {
                        $saleItem->warranty_document = optional($warranty)->warranty_document ?: "assets/img/products/default.png";
                    }

                    // $saleItem->product_warranty_id = $item['product_warranty_id'];
                    $saleItem->warranty_period = $item['warranty_period'];
                    $saleItem->warranty_charges = $item['warranty_charges'];
                    $saleItem->additional_warranty_description = $item['additional_warranty_description'];
                    $saleItem->created_by = $userId;
                    $saleItem->save();

                    $discount = $item['discount'];
                    $netAmount = $item['net_amount'];
                    $costAmount = $item['purchase_price'] * $item['quantity'];

                    $quantity = $item['quantity'];
                    $saleItemProduct = $item['product'];

                    while ($quantity > 0) {
                        $userBranchId = \Auth::user()->branchId();
                        $invQuery = Inventory::where('status', 1)->lockForUpdate();
                        if (!empty($userBranchId)) {
                            $branchInv = (clone $invQuery)->where('branch_id', $userBranchId)->find($item['inventory_id']);
                            $inventory = $branchInv ?: $invQuery->findOrFail($item['inventory_id']);
                        } else {
                            $inventory = $invQuery->findOrFail($item['inventory_id']);
                        }

                        if ($inventory->remain_qty > $quantity) {
                            $inventory->remain_qty = $inventory->remain_qty - $quantity;
                            $inventory->update();

                            $stockTransaction = new StockTransaction();
                            $stockTransaction->stock_master_id = 2;
                            $stockTransaction->inventory_id = $inventory->id;
                            $stockTransaction->product_id = $item['product'];
                            $stockTransaction->quantity = $quantity;
                            $stockTransaction->date = $request->date_of_sale;
                            $stockTransaction->branch_id = \Auth::user()->branchId();
                            $stockTransaction->created_by     = \Auth::user()->id;
                            $stockTransaction->status     = 1;
                            $stockTransaction->save();

                            $quantity = 0;
                        } elseif ($inventory->remain_qty <= $quantity) {

                            $stockTransaction = new StockTransaction();
                            $stockTransaction->stock_master_id = 2;
                            $stockTransaction->inventory_id = $inventory->id;
                            $stockTransaction->product_id = $item['product'];
                            $stockTransaction->quantity = $inventory->remain_qty;
                            $stockTransaction->date = $request->date_of_sale;
                            $stockTransaction->branch_id = \Auth::user()->branchId();
                            $stockTransaction->created_by     = \Auth::user()->id;
                            $stockTransaction->status     = 1;
                            $stockTransaction->save();

                            $quantity = $quantity - $inventory->remain_qty;
                            $inventory->remain_qty = 0;
                            $inventory->status = 0;
                            $inventory->update();
                        }
                    }
                    // stock account transaction
                    $productDetail = Product::find($saleItemProduct);
                    $advanceAmount = 0;
                    $itemKey = $key + 1;

                    if ($request->advance_amount != '' && $request->advance_amount > 0) {
                        if ($itemCount != $itemKey) {
                            $advanceAmount = round(($request->advance_amount / ($request->fullTotalOfPayments)) * $netAmount, 2);
                            $totAdvance -= $advanceAmount;
                        } else if ($itemCount == $itemKey) {
                            $advanceAmount = $totAdvance;
                        }
                    }

                    $cashAmount = $netAmount - $advanceAmount;

                    // if($request->payment_method == 'CARD' || $request->payment_method == 'ONLINEPAYMENT'){
                    //     $backAccount = BankAccount::find($request->bank);
                    //     $productCategory = ProductCategory::find($productDetail->product_category_id);

                    //     Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $request->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $cashAmount, 'debit');
                    //     Utility::accountSalesTransaction($request->date_of_sale, $request->income_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $cashAmount, 'credit');

                    //     if ($request->advance_amount != '' && $request->advance_amount > 0) {
                    //         Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, 5, $request->income_chart_of_account_id, $sale->id, $invoiceId, $request->customer, 2, $request->advance_amount, 'debit');
                    //         Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, $request->income_chart_of_account_id, 5, $sale->id, $invoiceId, $request->customer, 2, $request->advance_amount, 'credit');
                    //     }

                    //     if ($request->discount != '' && $request->discount > 0) {
                    //         Utility::accountSalesTransaction($request->date_of_sale, 6, $request->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->discount, 'debit');
                    //         Utility::accountSalesTransaction($request->date_of_sale, $request->income_chart_of_account_id, 6, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->discount, 'credit');
                    //     }

                    //     if ($request->document_charges != '' && $request->document_charges > 0) {
                    //         Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $productCategory->inventory_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->document_charges, 'debit');
                    //         Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->document_charges, 'credit');
                    //     }

                    // }

                    if ($request->customer_type == 1) {
                        $customer_id = $request->customer;
                    } elseif ($request->customer_type == 2) {
                        $customer_id = $newCustomer->id;
                    }

                    $backAccount = BankAccount::find($request->bank);
                    $bankCoaId = optional($backAccount)->chart_of_account_id ?: 1;
                    $productCategory = ProductCategory::find($productDetail->product_category_id);

                    Utility::accountSalesTransaction($request->date_of_sale, $bankCoaId, $productCategory->income_chart_of_account_id, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $cashAmount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $bankCoaId, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $cashAmount, 'credit');

                    if ($request->advance_amount != '' && $request->advance_amount > 0) {
                        Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, 5, $productCategory->income_chart_of_account_id, $sale->id, $invoiceId, $customer_id, 1, $request->advance_amount, 'debit');
                        Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, 5, $sale->id, $invoiceId, $customer_id, 1, $request->advance_amount, 'credit');
                    }

                    if ($discount != '' && $discount > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, 6, $productCategory->income_chart_of_account_id, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $discount, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, 6, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $discount, 'credit');
                    }

                    Utility::accountSalesTransaction($request->date_of_sale, 7, $productCategory->inventory_chart_of_account_id, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $costAmount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_chart_of_account_id, 7, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $costAmount, 'credit');

                    if ($request->customer_type == 1) {
                        $customerId = $request->customer;
                    } elseif ($request->customer_type == 2) {
                        $customerId = $newCustomer->id;
                    }

                    $financialEntry = new FinancialEntry();
                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Cash';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->invoice_id = $invoice->id;
                    $financialEntry->payment_method = $request->payment_method;
                    $financialEntry->customer_id = $customerId;
                    $financialEntry->product_id = $item['product'];
                    $financialEntry->amount = $cashAmount; //$item['net_amount'];
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'cash_sale';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->save();
                }

                if ($request->traveling_charges) {
                    $financialEntry = new FinancialEntry();
                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Traveling Charges';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->payment_method = 'CASH';
                    $financialEntry->amount = $request->traveling_charges;
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'traveling_charges';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->save();
                }
            }

            $saleId = Sale::max('id');
            if ($request->sale_type == 1) {
                // $monthly_interest = $request->interest_amount / $request->installment;
                // $hirePurchaseContract = new HirePurchaseContract();
                // $hirePurchaseContract->invoice_id       = $invoiceId;
                // $hirePurchaseContract->sale_id       = $saleId;
                // $hirePurchaseContract->date_of_sale       = $request->date_of_sale;
                // $hirePurchaseContract->customer_id       = $request->customer;
                // $hirePurchaseContract->guarantor_id       = $request->guarantor;
                // // $hirePurchaseContract->sale_type         = $request->sale_type;
                // $hirePurchaseContract->product_id     = $request->product;
                // $hirePurchaseContract->unit_price     = $request->unit_price;
                // // $hirePurchaseContract->quantity     = $request->quantity;
                // // $hirePurchaseContract->gross_amount     = $request->gross_amount;
                // // $hirePurchaseContract->discount     = $request->discount;
                // $hirePurchaseContract->net_amount     = $request->net_amount;
                // $hirePurchaseContract->down_payment     = $request->down_payment;
                // // $hirePurchaseContract->outstanding_amount  = $request->outstanding_amount;
                // $hirePurchaseContract->time_period     = $request->installment;
                // $hirePurchaseContract->interest_rate     = $request->interest_rate;
                // $hirePurchaseContract->interest_amount     = $request->interest_amount;
                // $hirePurchaseContract->monthly_interest     = $monthly_interest;
                // $hirePurchaseContract->total_due_amount     = $request->total_due_amount;
                // $hirePurchaseContract->monthly_installment     = $request->monthly_installment;
                // // $hirePurchaseContract->installment_date     = $request->installment_date;
                // // $hirePurchaseContract->due_date     = $request->due_date;
                // $hirePurchaseContract->status     = 2;
                // $hirePurchaseContract->save();

                // $hirePurchaseId = HirePurchaseContract::max('id');

                $dateofSale = explode('-', $request->date_of_sale);
                $sale_yr = $dateofSale[0];
                $sale_mn = $dateofSale[1];
                $sale_day = $request->installment_date;

                $installment_date = Carbon::create($sale_yr, $sale_mn, $sale_day);

                $excessAdvance = max(0, (float)$request->advance_amount - (float)$request->down_payment);
                $remAdvance = $excessAdvance;

                for ($i = 0; $i < $request->installment; $i++) {
                    $newInstallmentDate = $installment_date->addMonth(1)->format('Y-m-d');

                    $installment = new Installment();
                    $installment->branch_id = \Auth::user()->branchId();
                    $installment->sale_id = $saleId;
                    $installment->amount = $request->monthly_installment;
                    if ($remAdvance >= (float)$request->monthly_installment) {
                        $installment->status = 1;
                        $installment->paid_date = $request->date_of_sale;
                        $installment->total_amount_to_pay = 0;
                        $installment->released_amount = $request->monthly_installment;
                        $remAdvance -= (float)$request->monthly_installment;
                    } elseif ($remAdvance > 0) {
                        $installment->status = 2;
                        $installment->total_amount_to_pay = (float)$request->monthly_installment - $remAdvance;
                        $installment->released_amount = $remAdvance;
                        $remAdvance = 0;
                    } else {
                        $installment->status = 2;
                        $installment->total_amount_to_pay = $request->monthly_installment;
                    }

                    if ($i == 0) {
                        $installment->total_due_amount = max(0, (float)$request->total_due_amount - $excessAdvance);
                    }
                    $installment->installment_date = $newInstallmentDate;
                    $installment->invoice_id = $invoiceId;
                    $installment->save();
                }

                if ($request->paid_amount > 0) {
                    $maxId = Receipt::max('id');
                    $receiptCode = "DPR" . str_pad(($maxId + 1), 6, "0", STR_PAD_LEFT);

                    $receipts = new Receipt();
                    $receipts->branch_id = \Auth::user()->branchId();
                    $receipts->receipt_code = $receiptCode;
                    $receipts->customer_id = $request->customer;
                    $receipts->invoice_id = $invoiceId;
                    $receipts->issue_date = $request->date_of_sale;
                    $receipts->installment_due_date = $request->date_of_sale;
                    $receipts->status = 1;
                    $receipts->installment_amount = $request->down_payment;
                    $receipts->extra_charges = $request->document_charges;
                    $receipts->total_amount_to_pay = $request->down_payment;
                    $receipts->paid_amount = $request->paid_amount;
                    $receipts->paid_amount_in_words = $request->paid_amount_in_words;
                    $receipts->payment_method = $request->payment_method;

                    if ($request->payment_method == 'CARD') {
                        $receipts->card_type            = $request->card_type;
                        $receipts->card_charge_amount   = $request->card_charge_amount;
                        $receipts->total_amount         = $request->total_amount;
                    } elseif ($request->payment_method == 'CHEQUE') {
                        $receipts->cheque_number        = $request->cheque_number;
                    }

                    $receipts->balance_amount = $request->total_due_amount;
                    $receipts->description = 'Down Payment';
                    $receipts->created_by = $userId;
                    $receipts->save();

                    $financialEntry = new FinancialEntry();
                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Down Payment';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->invoice_id = $invoice->id;
                    $financialEntry->receipt_id = $receipts->id;
                    $financialEntry->payment_method = $request->payment_method;
                    $financialEntry->customer_id = $request->customer ?: null;
                    $financialEntry->product_id = $request->product;
                    $financialEntry->amount = $request->paid_amount; //down_payment;
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'down_payment';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->save();
                }

                if ($request->document_charges > 0) {
                    $newMaxId = Receipt::max('id');
                    $newReceiptCode = "DOC" . str_pad(($newMaxId + 1), 6, "0", STR_PAD_LEFT);

                    $newReceipts = new Receipt();
                    $newReceipts->branch_id = \Auth::user()->branchId();
                    $newReceipts->receipt_code = $newReceiptCode;
                    $newReceipts->customer_id = $request->customer;
                    $newReceipts->invoice_id = $invoiceId;
                    $newReceipts->issue_date = $request->date_of_sale;
                    $newReceipts->installment_due_date = $request->date_of_sale;
                    $newReceipts->status = 1;
                    // $newReceipts->installment_amount = $request->down_payment;
                    // $newReceipts->extra_charges = $request->document_charges;
                    $newReceipts->total_amount_to_pay = $request->document_charges;
                    $newReceipts->paid_amount = $request->document_charges;
                    $newReceipts->paid_amount_in_words = $request->document_charges_in_words;
                    $newReceipts->payment_method = $request->payment_method;
                    $newReceipts->balance_amount = $request->total_due_amount;
                    $newReceipts->description = 'Document Charges';
                    $newReceipts->created_by = $userId;
                    $newReceipts->save();

                    $financialEntry = new FinancialEntry();
                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Document Charges';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->receipt_id = $newReceipts->id;
                    $financialEntry->payment_method = 'CASH';
                    $financialEntry->customer_id = $request->customer ?: null;
                    $financialEntry->amount = $request->document_charges;
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'document_charges';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->save();
                }
            }

            if ($saleType == 1 && !empty($request->product_warranty_serial_id)) {
                ProductWarrantySerial::where('id', $request->product_warranty_serial_id)->update(['status' => 1]);
            }

            if ($request->advance_id != '') {
                $advanceIds = json_decode($request->advance_id);
            } else {
                $advanceIds = [];
            }
            $advances = AdvancePayment::whereIn('id', $advanceIds)->get();
            if (count($advances) > 0) {
                foreach ($advances as $advance) {
                    $advance->status = 0;
                    $advance->sale_id = $sale->id;
                    $advance->update();
                }
            }

            // return redirect()->route('sale.index')->with(['success'=> 'Sale successfully created.', 'pageNo'=>'0']);
            $cus = Customer::find($request->customer);
            if (isset($cus->phone_mobile)) {
                SMS::sendSMS($cus->phone_mobile, 'Dear Customer, Thank you for your purchasing, Invoice Number ' . $invoiceCode);
            }

            return redirect()->route($saleType == 0 ? 'sales_direct.index' : 'sales_hire.index')->with('success', __('Sale successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function uploadWarrantyDocument(Request $request)
    {
        $file = $request->file('warranty_document');

        $destinationPath = 'assets/documents/products';

        $randomNumber = rand(1000, 9999);
        $fileName = 'warranty_document' . "_" . $randomNumber . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);

        return $destinationPath . '/' . $fileName;
    }

    public function view($id)
    {

        if (\Auth::user()->can('view sale')) {
            $sales = Sale::with([
                'guarantor1',
                'guarantor2',
                'guarantor3',
                'saleItems' => function ($query) {
                    $query->with('warranty')->where('status', 1);
                }
            ])->where('id', $id)->first();

            $introducer = Sale::select('users.name')
                ->join('users', 'sales.introducer_id', 'users.id')
                ->where('sales.id', $id)
                ->first();

            $hirePurchaseDetail = HirePurchaseDetail::where('sale_id', $id)->first();
            $currencySymbol = $this->currencySymbol;
            // dd($hirePurchaseDetail, $sales);
            return view('sale.view', compact('sales', 'introducer', 'hirePurchaseDetail', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {

        if (\Auth::user()->can('edit sale')) {
            $directCustomers = Customer::whereIn('type', [0, 2])->where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get();
            $hireCustomers = Customer::whereIn('type', [0, 2])->where('sale_customer_type', 0)->where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get();
            $guarantors = Customer::whereIn('type', [1, 2])->where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get();
            $saleTypes = Sale::$saleType;
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $users = User::where('is_active', 1)->whereNotIn('type', ['company', 'super admin'])->get();

            $productWarranties = ProductWarranty::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('warranty_card_no')->get();

            $products = $this->getSaleProductsCatalog(\Auth::user()->branchId());

            $timePeriods = TimePeriod::where('status', 1)->get();

            $hirePurchaseDetail = HirePurchaseDetail::where('sale_id', $id)->first();

            $salesCheck = Sale::with('receipts')->where('branch_id', Auth::user()->branchId())->findOrFail($id);
            foreach (\App\Services\SaleBatchCatalog::ownedRows($salesCheck)->pluck('product_id')->unique() as $productId) {
                if (!$products->contains('product_id', $productId)) {
                    $product = Product::with('productCategory', 'productSubCategory')->find($productId);
                    if ($product) {
                        $savedProduct = new Inventory;
                        $savedProduct->product_id = $productId;
                        $savedProduct->setRelation('products', $product);
                        $products->push($savedProduct);
                    }
                }
            }
            if ($salesCheck->modal_type == 1) {
                $sales = $salesCheck;
                $customers = $hireCustomers;
                $sale = Sale::with(['products', 'hirePurchaseDetails'])
                    ->where('id', $id) // Replace with your sale identifier
                    ->first();
                // $serialNumbers = ProductWarrantySerial::where('grn_product_id', $sale->hirePurchaseDetails->product_warranty_id)
                //     ->get();
                $serialNumbers = ProductWarrantySerial::join('grn_products', 'product_warranty_serials.grn_product_id', '=', 'grn_products.id')
                    ->where('grn_products.product_id', $sale->product_id)
                    ->where('product_warranty_serials.status', 0)
                    ->select('product_warranty_serials.*') // Select only necessary columns from product_warranty_serials
                    ->get();
                return view('sale.edit', compact('sales', 'customers', 'guarantors', 'saleTypes', 'products', 'timePeriods', 'banks', 'users', 'hirePurchaseDetail', 'productWarranties', 'sale', 'serialNumbers'));
            } elseif ($salesCheck->modal_type == 2) {
                $sales = Sale::with('saleItems.inventory', 'receipts', 'saleItems.products.brands', 'saleItems.warranty')->find($id);
                $sales->setRelation('saleItems', $sales->saleItems->where('status', 1)->values());
                $productWarrantySerials = ProductWarrantySerial::whereIn('id', $sales->saleItems->pluck('product_warranty_id'))->get();
                $warrantyData = [];
                foreach ($sales->saleItems as $item) {
                    $grnProducts = GrnProduct::where('product_id', $item->product_id)->get();

                    if (!$grnProducts) {
                        return response()->json(["No related GRN products found"]); // No related GRN product found
                    }

                    $grnProductIds = $grnProducts->pluck('id');
                    // Retrieve warranty serials from GRN products
                    $query1 = ProductWarrantySerial::whereIn('grn_product_id', $grnProductIds)
                        ->where('status', 0)
                        ->select(['serial_number', 'warranty_number']);

                    // Second query
                    $query2 = ProductWarrantySerial::where('id', $item->product_warranty_id)
                        ->select(['serial_number', 'warranty_number']);

                    // Combine the two queries using union
                    $combinedWarrantyData = $query1->union($query2)->get();
                    // Check if any warranty serials are found
                    if ($combinedWarrantyData->isEmpty()) {
                        return response()->json(["message" => "No warranty serials found"], 404);
                    }

                    // Store the combined data
                    $warrantyData[$item->product_id] = $combinedWarrantyData;
                    // Check if warranty serials are found
                    if ($warrantyData[$item->product_id]->isEmpty()) {
                        return response()->json(["message" => "No warranty serials found"], 404);
                    }
                }

                $customers = $directCustomers;
                return view('sale.direct_sale_edit', compact('sales', 'customers', 'guarantors', 'saleTypes', 'products', 'timePeriods', 'banks', 'users', 'hirePurchaseDetail', 'productWarranties', 'productWarrantySerials', 'warrantyData'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function itemDelete(Request $request)
    {

        $item =  SaleItem::find($request->sale_item_id);
        $qty = $item->quantity;
        $inventory = Inventory::find($item->inventory_id);

        $stkTrans = StockTransaction::where('inventory_id', $item->inventory_id)->where('quantity', $item->quantity)->where('stock_master_id', 2)->where('status', 1)->first();
        if ($stkTrans) {
            $stkTrans->status = 0;
            $stkTrans->update();
        }

        $currentQty = $inventory->remain_qty;
        $newQty = $currentQty + $qty;
        $inventory->remain_qty = $newQty;
        $inventory->status = 1;
        $inventory->update();
        $item->status = 0;
        $item->update();

        return json_encode('success');
    }

    public function update(Request $request)
    {
        return DB::transaction(function () use ($request) {
            return $this->updateWithStockBatch($request);
        });
    }

    protected function updateWithStockBatch(Request $request)
    {

        $userId = Auth::user()->id;
        if (\Auth::user()->can('edit sale')) {
            $saleType = $request->sale_type;
            if ($saleType == 0) {
                if ($request->modal_type == 1) {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'date_of_sale' => 'required',
                            'customer_type' => 'required',
                            'sale_type' => 'required',
                            'product' => 'required',
                            'unit_price' => 'required',
                            'quantity' => 'required',
                            'gross_amount' => 'required',
                            'net_amount' => 'required',
                            'bank' => 'required',
                            'inventory_id' => 'required',
                            'paid_amount' => 'required',
                            'paid_amount_in_words' => 'required',
                            'payment_method' => 'required',
                        ]
                    );
                } elseif ($request->modal_type == 2) {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'date_of_sale' => 'required',
                            'customer_type' => 'required',
                            'bank' => 'required',
                            'paid_amount' => 'required',
                            'paid_amount_in_words' => 'required',
                            'payment_method' => 'required',
                        ]
                    );
                }
                if ($request->customer_type == 1) {
                    $validator2 = \Validator::make(
                        $request->all(),
                        [
                            'customer' => 'required',
                        ]
                    );

                    if ($validator2->fails()) {
                        $messages = $validator2->getMessageBag();
                        return redirect()->back()->with('error', $messages->first());
                    }
                }
            } elseif ($saleType == 1) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'date_of_sale' => 'required',
                        'customer' => 'required',
                        'guarantor' => 'required',
                        'sale_type' => 'required',
                        'product' => 'required',
                        'unit_price' => 'required',
                        'quantity' => 'required',
                        'gross_amount' => 'required',
                        'net_amount' => 'required',
                        'bank' => 'required',
                        'inventory_id' => 'required',
                        'paid_amount' => 'required',
                        'paid_amount_in_words' => 'required',
                        'payment_method' => 'required',
                        'down_payment' => 'required',
                        'outstanding_amount' => 'required',
                        'installment' => 'required',
                        'interest_rate' => 'required',
                        'interest_amount' => 'required',
                        'total_due_amount' => 'required',
                        'monthly_installment' => 'required',
                        'installment_date' => 'required',
                        'due_date' => 'required',
                    ]
                );
            }

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if ($request->sale_type == 1) {
                if ((float)$request->down_payment > (float)$request->net_amount) {
                    return redirect()->back()->with('error', __('Down Payment cannot exceed the Cash Price / Net Amount!'));
                }
                if ((float)$request->gross_hire_price < (float)$request->net_amount) {
                    return redirect()->back()->with('error', __('Gross Hire Price cannot be less than the Cash Price / Net Amount!'));
                }
            }

            $checkInstallments = Installment::where('sale_id', $request->sale_id)->get();

            if ($checkInstallments != null) {
                foreach ($checkInstallments as $checkInstallment) {
                    if ($checkInstallment->status == 1) {
                        return redirect()->back()->with('error', __("Could not Proceed! Some installments have been already settled."));
                    }
                }
            }

            $existingSale = Sale::where('branch_id', Auth::user()->branchId())->lockForUpdate()->findOrFail($request->sale_id);
            abort_unless((int) $request->modal_type === (int) $existingSale->modal_type && (int) $request->sale_type === (int) $existingSale->sale_type, 422);
            $request->merge(['invoice_id' => $existingSale->invoice_id]);
            \App\Services\SaleBatchEdit::prepare($request, $existingSale);
            $invoice = Invoice::find($request->invoice_id);
            $invoice->customer_id = $request->customer;
            $invoice->guarantor_id = $request->guarantor;
            $invoice->bank_id = $request->bank;
            $invoice->inventory_id = $request->inventory_id;
            $invoice->issue_date = $request->date_of_sale;
            $invoice->due_date = $request->due_date;
            $invoice->status = 1;
            $invoice->created_by = $userId;
            $invoice->update();

            $invoiceId = $request->invoice_id;

            $status = 1;

            $cardExpenceId = $request->card_expence_id;
            if ($request->payment_method == 'CARD') {
                if ($request->card_charge_amount != '') {
                    $cardCharge = CardInterestExpense::where('id', $cardExpenceId)->first();
                    if ($cardCharge) {
                        $cardCharge->amount = $request->card_charge_amount;
                        $cardCharge->status = 1;
                        $cardCharge->update();
                    } else {
                        $newCardCharge = new CardInterestExpense();
                        $newCardCharge->amount = $request->card_charge_amount;
                        $newCardCharge->invoice_id = $request->invoice_id;
                        $newCardCharge->status = 1;
                        $newCardCharge->save();

                        $cardExpenceId = $newCardCharge->id;
                    }
                }
            } else {
                // CardInterestExpense::where('id', $cardExpenceId)->delete();
                $cardChage = CardInterestExpense::find($cardExpenceId);
                if ($cardChage) {
                    $cardChage->status = 0;
                    $cardChage->update();
                }
            }

            if ($request->modal_type == 1) {

                $sale = Sale::find($request->sale_id);
                $sale->date_of_sale       = $request->date_of_sale;
                $sale->customer_type       = $request->customer_type;
                if ($request->customer_type == 1) {
                    $sale->customer_id       = $request->customer;
                } elseif ($request->customer_type == 2) {
                    $sale->customer_name       = $request->customer;
                    $sale->customer_phone_no       = $request->customer;
                }
                $sale->guarantor_id       = $request->guarantor;
                $sale->guarantor_2_id       = $request->guarantor_2;
                $sale->guarantor_3_id       = $request->guarantor_3;
                $sale->guarantor_1_known_duration = $request->guarantor_1_known_duration;
                $sale->guarantor_2_known_duration = $request->guarantor_2_known_duration;
                $sale->witness_1_name       = $request->witness_1_name;
                $sale->witness_1_address    = $request->witness_1_address;
                $sale->witness_1_nic        = $request->witness_1_nic;
                $sale->witness_2_name       = $request->witness_2_name;
                $sale->witness_2_address    = $request->witness_2_address;
                $sale->witness_2_nic        = $request->witness_2_nic;
                $sale->introducer_id       = $request->introducer_id;
                $sale->sale_type         = $request->sale_type;
                $sale->product_id     = $request->product;
                $sale->purchase_price     = $request->purchase_price;
                $sale->unit_price     = $request->unit_price;
                $sale->quantity     = $request->quantity;
                $sale->gross_amount     = $request->gross_amount;
                $sale->discount     = $request->discount;
                $sale->net_amount     = $request->net_amount;
                $sale->advance_amount     = $request->advance_amount;

                $sale->paid_amount     = max(0, (float)$request->paid_amount);
                $sale->paid_amount_in_words     = $request->paid_amount_in_words;
                $sale->payment_method     = $request->payment_method;
                if ($request->payment_method == 'CARD') {
                    $sale->card_type     = $request->card_type;
                    $sale->card_expence_id     = $cardExpenceId;
                    $sale->card_charge_amount     = $request->card_charge_amount;
                    $sale->total_amount     = $request->total_amount;
                } elseif ($request->payment_method == 'CHEQUE') {
                    $sale->cheque_number     = $request->cheque_number;
                    $sale->cheque_realised_date     = $request->realised_date;
                }

                if ($request->sale_type == 1) {
                    $excessAdvance = max(0, (float)$request->advance_amount - (float)$request->down_payment);
                    $status = 2;
                    $sale->hire_purchase_index     = $request->hire_purchase_index;
                    $sale->down_payment     = $request->down_payment;
                    $sale->outstanding_amount  = max(0, (float)$request->outstanding_amount);
                    $sale->installment     = $request->installment;
                    $sale->interest_rate     = max(0, (float)$request->interest_rate);
                    $sale->interest_amount     = max(0, (float)$request->interest_amount);
                    $sale->total_due_amount     = max(0, (float)$request->total_due_amount);
                    $sale->current_due_amount     = max(0, (float)$request->total_due_amount - $excessAdvance);
                    $sale->due_paid_amount     = $excessAdvance;
                    $sale->monthly_installment     = $request->monthly_installment;
                    $sale->installment_date     = $request->installment_date;
                    $sale->due_date     = $request->due_date;
                    $sale->document_charges     = $request->document_charges;
                }

                $sale->status     = $status;
                $sale->created_by     = $userId;
                $sale->bank_id     = $request->bank;
                $sale->inventory_id     = $request->inventory_id;
                $sale->traveling_charges     = $request->traveling_charges;
                $sale->modal_type     = $request->modal_type;
                $sale->update();

                Utility::deleteAccountSalesTransaction($request->sale_id);
                $productDetail = Product::find($request->product);

                if ($request->sale_type == 0) {

                    $cashAmount = $request->net_amount - $request->advance_amount;

                    $backAccount = BankAccount::find($request->bank);
                    $productCategory = ProductCategory::find($productDetail->product_category_id);

                    Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 1, $cashAmount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 1, $cashAmount, 'credit');

                    if ($request->advance_amount != '' && $request->advance_amount > 0) {
                        Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, 5, $productCategory->income_chart_of_account_id, $sale->id, $invoiceId, $request->customer, 1, $request->advance_amount, 'debit');
                        Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, 5, $sale->id, $invoiceId, $request->customer, 1, $request->advance_amount, 'credit');
                    }

                    if ($request->discount != '' && $request->discount > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, 6, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 1, $request->discount, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, 6, $sale->id, $request->product, $invoiceId, $request->customer, 1, $request->discount, 'credit');
                    }

                    Utility::accountSalesTransaction($request->date_of_sale, 7, $productCategory->inventory_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 1, $request->purchase_price * $request->quantity, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_chart_of_account_id, 7, $sale->id, $request->product, $invoiceId, $request->customer, 1, $request->purchase_price * $request->quantity, 'credit');
                } else if ($request->sale_type == 1) {

                    $cashAmount = max(0, (float)$request->down_payment - (float)$request->advance_amount);

                    $backAccount = BankAccount::find($request->bank);
                    $productCategory = ProductCategory::find($productDetail->product_category_id);

                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_control_chart_of_account_id, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $productCategory->inventory_loan_control_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'credit');

                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_chart_of_account_id, $productCategory->inventory_loan_control_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_control_chart_of_account_id, $productCategory->inventory_loan_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'credit');

                    if ($request->discount != '' && $request->discount > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, 6, $productCategory->inventory_loan_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->discount, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_chart_of_account_id, 6, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->discount, 'credit');
                    }

                    if ($request->document_charges != '' && $request->document_charges > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $productCategory->dnl_charges_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->document_charges, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->dnl_charges_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->document_charges, 'credit');
                    }

                    if ($request->down_payment != '' && $request->down_payment > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, $backAccount->chart_of_account_id, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->down_payment, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $backAccount->chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->down_payment, 'credit');
                    }

                    Utility::accountSalesTransaction($request->date_of_sale, 7, $productCategory->inventory_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->purchase_price * $request->quantity, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_chart_of_account_id, 7, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->purchase_price * $request->quantity, 'credit');
                }

                $hirePurchaseDetails = HirePurchaseDetail::where('sale_id', $sale->id)->first();

                if ($request->has('warranty_document')) {
                    $warrantyDocument = self::uploadWarrantyDocument($request);
                    $hirePurchaseDetails->warranty_document = $warrantyDocument;
                }

                $hirePurchaseDetails->product_warranty_id = $request->product_warranty_serial_id;
                if ($request->product_warranty_serial_id) {
                    ProductWarrantySerial::where('id', $request->product_warranty_serial_id)->update(['status' => 1]);
                }
                if (!$request->hasFile('warranty_document') && $request->warranty_document_path) {
                    $hirePurchaseDetails->warranty_document = $request->warranty_document_path;
                }
                $hirePurchaseDetails->warranty_period = $request->warranty_period;
                $hirePurchaseDetails->warranty_charges = $request->warranty_charges;
                $hirePurchaseDetails->additional_warranty_description = $request->additional_warranty_description;
                $hirePurchaseDetails->update();


                // HirePurchaseDetail::create(['product_warranty_id'=>$request->product_warranty_id]);

                // $hirePurchase = HirePurchaseDetail::where('sale_id', $sale->id)->first();
                // dd([$hirePurchase, $hirePurchaseDetails]);


                if ($request->down_payment) {
                    $financialEntry = FinancialEntry::where('sale_id', $sale->id)->where('entry_type', 'down_payment')->first();
                    $cashAmount = $request->down_payment - $request->advance_amount;
                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Down Payment';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->invoice_id = $invoice->id;
                    $financialEntry->payment_method = $request->payment_method;
                    $financialEntry->customer_id = $request->customer ?: null;
                    $financialEntry->product_id = $request->product;
                    $financialEntry->amount = $cashAmount; //$request->down_payment;
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'down_payment';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->update();
                }

                if ($request->document_charges) {
                    $financialEntry = FinancialEntry::where('sale_id', $sale->id)->where('entry_type', 'document_charges')->first();

                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Document Charges';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->payment_method = 'CASH';
                    $financialEntry->customer_id = $request->customer ?: null;
                    $financialEntry->amount = $request->document_charges;
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'document_charges';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->update();
                }

                if ($request->traveling_charges) {
                    $financialEntry = FinancialEntry::where('sale_id', $sale->id)->where('entry_type', 'traveling_charges')->first();
                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Traveling Charges';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->payment_method = 'CASH';
                    $financialEntry->amount = $request->traveling_charges;
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'traveling_charges';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->update();
                }
            } elseif ($request->modal_type == 2) {

                $sale             = Sale::find($request->sale_id);
                $sale->date_of_sale       = $request->date_of_sale;
                $sale->customer_type       = $request->customer_type;
                if ($request->customer_type == 1) {
                    $sale->customer_id       = $request->customer;
                    $customer = $request->customer;
                } elseif ($request->customer_type == 2) {
                    $sale->customer_name       = $request->customer_name;
                    $sale->customer_phone_no       = $request->customer_phone_no;
                    $customer = 1;
                }
                $sale->introducer_id       = $request->introducer_id;
                $sale->advance_amount     = $request->advance_amount;
                $sale->net_amount     = $request->fullTotalOfPayments;
                $sale->status     = $status;
                $sale->created_by     = $userId;
                $sale->bank_id     = $request->bank;
                $sale->traveling_charges     = $request->traveling_charges;
                $sale->modal_type     = $request->modal_type;

                $sale->paid_amount     = $request->paid_amount;
                $sale->paid_amount_in_words     = $request->paid_amount_in_words;
                $sale->payment_method     = $request->payment_method;
                if ($request->payment_method == 'CARD') {
                    $sale->card_type     = $request->card_type;
                    $sale->card_expence_id     = $cardExpenceId;
                    $sale->card_charge_amount     = $request->card_charge_amount;
                    $sale->total_amount     = $request->total_amount;
                } elseif ($request->payment_method == 'CHEQUE') {
                    $sale->cheque_number     = $request->cheque_number;
                    $sale->cheque_realised_date     = $request->realised_date;
                }

                $sale->update();

                $saleItems = SaleItem::where('sale_id', $request->sale_id)->where('status', 1)->get();
                foreach ($saleItems as $saleItem) {

                    $saleItem->status = 0;
                    $saleItem->update();
                }

                Utility::deleteAccountSalesTransaction($request->sale_id);

                $itemCount = count($request->items);
                $totAdvance = $request->advance_amount;
                foreach ($request->items as $key => $item) {
                    $saleItem = new SaleItem();
                    $saleItem->sale_id =  $request->sale_id;
                    $saleItem->invoice_id =  $request->invoice_id;
                    $saleItem->date_of_sale =  $request->date_of_sale;
                    $saleItem->product_id = $item['product'];
                    $saleItem->branch_id = \Auth::user()->branchId();
                    $saleItem->purchase_price = $item['purchase_price'];
                    $saleItem->unit_price = $item['unit_price'];
                    $saleItem->quantity = $item['quantity'];
                    $saleItem->gross_amount = $item['gross_amount'];
                    $saleItem->discount = $item['discount'];
                    $saleItem->net_amount = $item['net_amount'];
                    $saleItem->status = 1;
                    $saleItem->inventory_id = $item['inventory_id'];

                    $serialData = ProductWarrantySerial::find($item['product_warranty_serial_id'] ?? null);

                    if ($serialData) {
                        $serialData->status = 1;
                        $serialData->save();
                        $saleItem->product_warranty_id = $serialData->id;
                    } else {
                        $saleItem->product_warranty_id = null; // Handle appropriately if no serial number is selected
                    }
                    if (isset($item['warranty_document'])) {
                        $file = $item['warranty_document'];
                        $destinationPath = 'assets/documents/products';

                        $randomNumber = rand(1000, 9999);
                        $fileName = 'warranty_document' . "_" . $randomNumber . '.' . $file->getClientOriginalExtension();
                        $file->move($destinationPath, $fileName);
                        $saleItem->warranty_document = $destinationPath . '/' . $fileName;
                    }

                    if (!isset($item['warranty_document']) && !empty($item['warranty_document_path'])) {
                        $saleItem->warranty_document = $item['warranty_document_path'];
                    }
                    // $saleItem->product_warranty_id = $item['product_warranty_id'];
                    $saleItem->warranty_period = $item['warranty_period'];
                    $saleItem->warranty_charges = $item['warranty_charges'];
                    $saleItem->additional_warranty_description = $item['additional_warranty_description'];
                    $saleItem->created_by = $userId;
                    $saleItem->save();

                    $saleItemProduct = $item['product'];
                    $costAmount = $item['purchase_price'] * $item['quantity'];
                    $discount = $item['discount'];
                    $netAmount = $item['net_amount'];
                    $productDetail = Product::find($saleItemProduct);
                    $advanceAmount = 0;
                    $itemKey = $key + 1;
                    if ($request->advance_amount != '' && $request->advance_amount > 0) {
                        if ($itemCount != $itemKey) {
                            $advanceAmount = round(($request->advance_amount / $request->fullTotalOfPayments) * $netAmount, 2);
                            $totAdvance -= $advanceAmount;
                        } else if ($itemCount == $itemKey) {
                            $advanceAmount = $totAdvance;
                        }
                    }

                    $cashAmount = $netAmount - $advanceAmount;

                    if ($request->customer_type == 1) {
                        $customer_id = $request->customer;
                    } elseif ($request->customer_type == 2) {
                        $customer_id = null;
                    }

                    $backAccount = BankAccount::find($request->bank);
                    $bankCoaId = optional($backAccount)->chart_of_account_id ?: 1;
                    $productCategory = ProductCategory::find($productDetail->product_category_id);

                    Utility::accountSalesTransaction($request->date_of_sale, $bankCoaId, $productCategory->income_chart_of_account_id, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $cashAmount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $bankCoaId, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $cashAmount, 'credit');

                    if ($request->advance_amount != '' && $request->advance_amount > 0) {
                        Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, 5, $productCategory->income_chart_of_account_id, $sale->id, $invoiceId, $customer_id, 1, $request->advance_amount, 'debit');
                        Utility::accountAdvancePaymentSettleTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, 5, $sale->id, $invoiceId, $customer_id, 1, $request->advance_amount, 'credit');
                    }

                    if ($discount != '' && $discount > 0) {
                        Utility::accountSalesTransaction($request->date_of_sale, 6, $productCategory->income_chart_of_account_id, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $discount, 'debit');
                        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, 6, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $discount, 'credit');
                    }

                    Utility::accountSalesTransaction($request->date_of_sale, 7, $productCategory->inventory_chart_of_account_id, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $costAmount, 'debit');
                    Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_chart_of_account_id, 7, $sale->id, $saleItemProduct, $invoiceId, $customer_id, 1, $costAmount, 'credit');

                    $financialEntry = FinancialEntry::where('sale_id', $sale->id)->where('entry_type', 'cash_sale')->first();

                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Cash';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->invoice_id = $invoice->id;
                    $financialEntry->payment_method = $request->payment_method;
                    $financialEntry->customer_id = $customer_id;
                    $financialEntry->product_id = $item['product'];
                    $financialEntry->amount = $cashAmount; //$item['net_amount'];
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'cash_sale';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->update();
                }

                if ($request->traveling_charges) {
                    $financialEntry = FinancialEntry::where('sale_id', $sale->id)->where('entry_type', 'traveling_charges')->first();

                    $financialEntry->date = $request->date_of_sale;
                    $financialEntry->type = 'Traveling Charges';
                    $financialEntry->sale_id = $sale->id;
                    $financialEntry->payment_method = 'CASH';
                    $financialEntry->amount = $request->traveling_charges;
                    $financialEntry->debit_credit = 'debit';
                    $financialEntry->entry_type = 'traveling_charges';
                    $financialEntry->branch_id = \Auth::user()->branchId();
                    $financialEntry->created_by = \Auth::user()->id;
                    $financialEntry->update();
                }
            }

            if ($request->sale_type == 1) {

                $insts = Installment::where('sale_id', $request->sale_id)->get();
                foreach ($insts as $inst) {
                    $inst->status = 0;
                    $inst->update();
                }

                $dateofSale = explode('-', $request->date_of_sale);
                $sale_yr = $dateofSale[0];
                $sale_mn = $dateofSale[1];
                $sale_day = $request->installment_date;

                $installment_date = Carbon::create($sale_yr, $sale_mn, $sale_day);

                $excessAdvance = max(0, (float)$request->advance_amount - (float)$request->down_payment);
                $remAdvance = $excessAdvance;

                for ($i = 0; $i < $request->installment; $i++) {
                    $newInstallmentDate = $installment_date->addMonth(1)->format('Y-m-d');

                    $installment = new Installment();
                    $installment->branch_id = \Auth::user()->branchId();
                    $installment->sale_id = $request->sale_id;
                    $installment->amount = $request->monthly_installment;
                    if ($remAdvance >= (float)$request->monthly_installment) {
                        $installment->status = 1;
                        $installment->paid_date = $request->date_of_sale;
                        $installment->total_amount_to_pay = 0;
                        $installment->released_amount = $request->monthly_installment;
                        $remAdvance -= (float)$request->monthly_installment;
                    } elseif ($remAdvance > 0) {
                        $installment->status = 2;
                        $installment->total_amount_to_pay = (float)$request->monthly_installment - $remAdvance;
                        $installment->released_amount = $remAdvance;
                        $remAdvance = 0;
                    } else {
                        $installment->status = 2;
                        $installment->total_amount_to_pay = $request->monthly_installment;
                    }

                    if ($i == 0) {
                        $installment->total_due_amount = max(0, (float)$request->total_due_amount - $excessAdvance);
                    }
                    $installment->installment_date = $newInstallmentDate;
                    $installment->invoice_id = $request->invoice_id;
                    $installment->save();
                }

                $receipts = Receipt::where('invoice_id', $request->invoice_id)->where('receipt_code', 'LIKE', 'DPR' . '%')->first();
                if ($receipts) {
                    $receipts->customer_id = $request->customer;
                    $receipts->issue_date = $request->date_of_sale;
                    $receipts->installment_due_date = $request->date_of_sale;
                    $receipts->status = 1;
                    $receipts->installment_amount = $request->down_payment;
                    $receipts->extra_charges = $request->document_charges;
                    $receipts->total_amount_to_pay = $request->down_payment;
                    $receipts->paid_amount = $request->paid_amount;
                    $receipts->paid_amount_in_words = $request->paid_amount_in_words;
                    $receipts->payment_method = $request->payment_method;

                    if ($request->payment_method == 'CARD') {
                        $receipts->card_type            = $request->card_type;
                        $receipts->card_charge_amount   = $request->card_charge_amount;
                        $receipts->total_amount         = $request->total_amount;
                    } elseif ($request->payment_method == 'CHEQUE') {
                        $receipts->cheque_number        = $request->cheque_number;
                    }

                    $receipts->balance_amount = $request->total_due_amount;
                    $receipts->description = 'Down Payment';
                    $receipts->created_by = $userId;
                    $receipts->update();
                }


                $newReceipts = Receipt::where('invoice_id', $request->invoice_id)->where('receipt_code', 'LIKE', 'DOC' . '%')->first();
                if ($newReceipts) {
                    if ($request->document_charges > 0) {

                        $newReceipts->customer_id = $request->customer;
                        $newReceipts->issue_date = $request->date_of_sale;
                        $newReceipts->installment_due_date = $request->date_of_sale;
                        $newReceipts->status = 1;
                        $newReceipts->total_amount_to_pay = $request->document_charges;
                        $newReceipts->paid_amount = $request->document_charges;
                        $newReceipts->paid_amount_in_words = $request->document_charges_in_words;
                        $newReceipts->payment_method = $request->payment_method;
                        $newReceipts->balance_amount = $request->total_due_amount;
                        $newReceipts->description = 'Document Charges';
                        $newReceipts->created_by = $userId;
                        $newReceipts->update();
                    } else {
                        $newReceipts->status = 0;
                        $newReceipts->update();
                    }
                }
            }
            return redirect()->back()->with('success', __('Sale successfully Edited.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function authenticate(Request $request)
    {
        $email = $request->login_details[0];
        $password = $request->login_details[1];

        $user = User::where('email', $email)->first();

        if ($user == null) {
            return json_encode('email_error');
        } elseif ($user != null) {
            if ($user->type == 'company' || $user->type == 'Admin' || $user->type == 'Branch Manager' || $user->type == 'Sales Executive' || $user->type == 'Branch Accountant') {
                if (Hash::check($password, $user->password)) {
                    return json_encode('success');
                } else {
                    return json_encode('password_error');
                }
            } else {
                return json_encode('user_error');
            }
        }
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            return $this->destroyWithStockBatch($id);
        });
    }

    protected function destroyWithStockBatch($id)
    {
        if (\Auth::user()->can('delete sale')) {
            $sale = Sale::where('branch_id', Auth::user()->branchId())->whereIn('status', [1, 2, 3])->lockForUpdate()->findOrFail($id);
            \App\Services\SaleBatchEdit::prepare(Request::create('/sale/cancel', 'POST', ['date_of_sale' => $sale->date_of_sale]), $sale, true);
            $sale->status = 0;
            $sale->update();

            $receipts = Receipt::where('invoice_id', $sale->invoice_id)->get();
            foreach ($receipts as $receipt) {
                $receipt->status = 0;
                $receipt->update();
            }

            $installments = Installment::where('sale_id', $sale->id)->get();
            foreach ($installments as $installment) {
                $installment->status = 0;
                $installment->update();
            }

            SaleItem::where('sale_id', $sale->id)->where('status', 1)->update(['status' => 0]);

            $invoices = Invoice::where('id', $sale->invoice_id)->get();
            foreach ($invoices as $invoice) {
                $invoice->status = 0;
                $invoice->update();
            }

            $advances = AdvancePayment::where('sale_id', $id)->get();
            foreach ($advances as $advance) {
                if ($advance->status == 0) {
                    $advance->status = 1;
                    $advance->update();
                }
            }

            $financialEntries = FinancialEntry::where('sale_id', $sale->id)->get();
            foreach ($financialEntries as $financialEntry) {
                $financialEntry->delete();
            }

            Utility::deleteAccountSalesTransaction($id);
            return redirect()->back()->with('success', __('Sale successfully Deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productIdByBarcode($id)
    {
        $productId = Product::select('id')->where('barcode', 'LIKE', '%' . $id . '%')->get();
        return json_encode($productId);
    }

    public function getCustAdvance($id)
    {

        $advances = AdvancePayment::where('status', 1)->where('customer_id', $id)->get();
        if (count($advances) > 0) {
            $ids = [];
            $amount = 0;
            foreach ($advances as $advance) {
                $ids[] = $advance->id;
                $amount += $advance->advance_amount;
            }
            return json_encode(array('id' => $ids, 'advance_amount' => $amount));
        } else {
            return json_encode(null);
        }
    }

    public function hirePurchaseIndex($customer, $date)
    {
        $index = Sale::where('sale_type', 1)->whereIn('status', [1, 2, 3])->where('customer_id', $customer)->where('date_of_sale', '<=', $date)->count();
        return json_encode($index);
    }

    public function setPrivouse($sale_id)
    {

        $sale = Sale::find($sale_id);

        $productDetail = Product::find($request->product);
        $productCategory = ProductCategory::find($productDetail->product_category_id);


        Utility::accountSalesTransaction($sale->date_of_sale, $productCategory->inventory_loan_control_chart_of_account_id, $productCategory->income_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'debit');
        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->income_chart_of_account_id, $productCategory->inventory_loan_control_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'credit');

        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_chart_of_account_id, $productCategory->inventory_loan_control_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'debit');
        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_loan_control_chart_of_account_id, $productCategory->inventory_loan_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->gross_amount, 'credit');

        Utility::accountSalesTransaction($request->date_of_sale, 7, $productCategory->inventory_chart_of_account_id, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->purchase_price * $request->quantity, 'debit');
        Utility::accountSalesTransaction($request->date_of_sale, $productCategory->inventory_chart_of_account_id, 7, $sale->id, $request->product, $invoiceId, $request->customer, 2, $request->purchase_price * $request->quantity, 'credit');
    }

    public function updatePrintStatus($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Update print status based on its current value
        if ($invoice->print_status == 0) {
            $invoice->print_status = 1;
        } elseif ($invoice->print_status == 1) {
            $invoice->print_status = 2;
        } elseif ($invoice->print_status == 3) {
            $invoice->print_status = 2;
        }

        $invoice->save();

        return response()->json(['status' => 'success', 'message' => 'Print status updated successfully']);
    }

    public function adminUpdatePrintStatus($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Set print status to 3
        $invoice->print_status = 3;
        $invoice->save();

        return response()->json(['status' => 'success', 'message' => 'Print status updated to 3 successfully']);
    }
}
