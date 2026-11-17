<?php

namespace App\Http\Controllers;

use App\Grn;
use App\GrnProduct;
use App\CustomField;
use Illuminate\Http\Request;
use App\Setting;
use App\BankAccount;
use App\Bill;
use App\BillPayment;
use App\BillProduct;

use App\Mail\BillPaymentCreate;
use App\Mail\BillSend;
use App\Mail\VenderBillSend;
use App\ProductService;
use App\ProductServiceCategory;
use App\Transaction;
use App\Utility;
use App\Vender;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GrnEditController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function edit($id)
    {

        $grn_id      = \Auth::user()->grnNumberFormat($this->grnId());
        $grn = Grn::where('id', $id)->first();
        $grnOrderProucts = GrnProduct::with('items')->where('grn_id', '=', $id)->get();
        $venders          = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $customFields      = CustomField::where('module', '=', 'grn')->get();
        $currencySymbol = $this->currencySymbol;
        // echo $grnOrderProucts[0];
        return view('grn.edit', ['grn' => $grn, 'grnOrderProucts' => $grnOrderProucts, 'grn_id' => $grn_id, 'vender' => $venders, 'product_services' => $product_services, 'customFields' => $customFields, 'currencySymbol' => $currencySymbol]);
    }


    public function update(Request $request, Grn $grn)
    {
        if (\Auth::user()->can('edit bill')) {

            if ($grn->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'vender_id' => 'required',
                        //    'items' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('grn.index')->with('error', $messages->first());
                }
                $grn->vender_id      = $request->vender_id;
                $grn->product_id      = $request->product_id;
                $grn->received_date  = $request->received_date;
                $grn->product_price  = $request->product_price;
                // $grn->discount   = $request->discount;
                // $grn->tax   = $request->tax;
                $grn->bill_id   = $request->bill_id;
                // $purchase->discount_apply = isset($request->discount_apply) ? 1 : 0;
                $grn->save();
                CustomField::saveData($grn, $request->customField);
                $products = $request->items;

                for ($i = 0; $i < count($products); $i++) {
                    $grnProduct = GrnProduct::find($products[$i]['id']);
                    if ($grnProduct == null) {
                        $grnProduct                 = new GrnProduct();
                        $grnProduct->grn_id    = $grn->id;
                        $grnProduct->product_id = $products[$i]['item'];
                        // $grn->tax   = $request->tax;
                        // $grn->tax   = $request->tax;
                    }

                    if (isset($products[$i]['item'])) {
                        $grnProduct->product_id = $products[$i]['item'];
                    }

                    $grnProduct->quantity    = $products[$i]['quantity'];
                    $grnProduct->ordered_quantity    = $products[$i]['ordered_quantity'];
                    $grnProduct->received_quantity    = $products[$i]['received_quantity'];
                    $grnProduct->product_price       = $products[$i]['product_price'];
                    $billProduct->tax         = $products[$i]['tax'];
                    $billProduct->discount    = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    $grnProduct->save();
                }


                return redirect()->back()->with('success', __('Purchase successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
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
        if (\Auth::user()->can('delete bill product')) {
            BillProduct::where('id', '=', $request->id)->delete();

            return redirect()->back()->with('success', __('Bill product successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function grn($grn_id)
    {
        $settings = Utility::settings();
        $grnId   = Crypt::decrypt($grn_id);

        $grn  = Grn::where('id', $grnId)->first();
        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $grn->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $vendor = $grn->vender;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach ($grn->items as $product) {
            $item           = new \stdClass();
            $item->name     = !empty($product->product()) ? $product->product()->name : '';
            $item->quantity = $product->quantity;
            $item->tax      = $product->tax;
            $item->discount = $product->discount;
            $item->price    = $product->price;
            $item->ordered_quantity    = $product->ordered_quantity;
            $item->received_quantity   = $product->received_quantity;

            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            $taxes     = \Utility::tax($item->tax);
            $itemTaxes = [];
            foreach ($taxes as $tax) {
                $taxPrice      = \Utility::taxRate($tax->rate, $item->price, $item->quantity);
                $totalTaxPrice += $taxPrice;

                $itemTax['name']  = $tax->name;
                $itemTax['rate']  = $tax->rate . '%';
                $itemTax['price'] = \App\Utility::priceFormat($settings, number_format($taxPrice, Utility::getValByName('decimal_number')));
                $itemTaxes[]      = $itemTax;


                if (array_key_exists($tax->name, $taxesData)) {
                    $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
                } else {
                    $taxesData[$tax->name] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;

            $items[] = $item;
        }

        $grn->items         = $items;
        $grn->totalTaxPrice = $totalTaxPrice;
        $grn->totalQuantity = $totalQuantity;
        $grn->totalRate     = $totalRate;
        $grn->totalDiscount = $totalDiscount;
        $grn->taxesData     = $taxesData;

        //Set your logo
        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        if ($grn) {
            $color      = '#' . $settings['bill_color'];
            $font_color = Utility::getFontColor($color);

            return view('bill.templates.' . $settings['bill_template'], compact('bill', 'color', 'settings', 'vendor', 'img', 'font_color'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function items(Request $request)
    {
        $items = GrnProduct::where('grn_id', $request->grn_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }




    function grnId()
    {
        $latest = Grn::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->grn_id + 1;
    }



    public function show($id)
    {

        if (\Auth::user()->can('show bill')) { {
                $grn = Grn::where('id', $id)->first();
                $grnOrderProucts = GrnProduct::with('items')->where('id', '=', $id)->get();
                $vendor      = $grn->vender;
                // $iteams      = $grn->items;

                $iteams =  $grnOrderProucts;
                return view('grn.show', compact('grn', 'vendor', 'iteams', 'grnOrderProucts'));
            }
        }
    }
}
