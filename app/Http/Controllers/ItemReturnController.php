<?php

namespace App\Http\Controllers;

use App\GeneralLedgerAccount;
use App\Inventory;
use App\Invoice;
use App\ItemReturn;
use App\Product;
use App\Sale;
use App\SaleItem;
use App\SalesReturn;
use App\StockTransaction;
use App\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ItemReturnController extends Controller
{
    public function index(){
        if(\Auth::user()->can('manage item return') || \Auth::user()->can('create stock adjustment'))
        {
            // $showData = 'No';
            $itemReturns = ItemReturn::where('status', 1)->get();
            return view('item_returns.index', compact('itemReturns'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function search(Request $request){
        if(\Auth::user()->can('create item return'))
        {
            $showData = 'Yes';
            $presentData = 'No';
            $invoiceNo = $request->invoice_number;
            if($request->invoice_number != ''){
                $invoice = Invoice::with(['sales.saleItems'=> function($query){
                                        $query->where('status', 1);
                                    }])
                                    ->where('invoice_code', $request->invoice_number)
                                    ->where('status', 1)
                                    ->first();
                if($invoice){
                    if($invoice->branch_id == \Auth::user()->branchId()){
                        $presentData = 'Yes';
                        return view('item_returns.index', compact('showData', 'presentData', 'invoiceNo', 'invoice'));
                    } else {
                        return redirect()->back()->with('error', __('Permission denied - Different Branch.'));
                    }
                }
            }
            return view('item_returns.index', compact('showData', 'presentData', 'invoiceNo'));
            // dd('works');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create(){
        if(\Auth::user()->can('create item return') || \Auth::user()->can('create stock adjustment'))
        {
            // $modalType = $request->model_type;
            // if (isset($request->checked_sale)) {
            //     $items = Sale::with('products')->whereIn('id', $request->checked_sale)->get();
            //     return view('item_returns.create', compact('items', 'modalType'));
            // }
            // if (isset($request->checked_saleItem)) {
            //     $items = SaleItem::with('products')->whereIn('id', $request->checked_saleItem)->get();
            //     return view('item_returns.create', compact('items', 'modalType'));
            // }
            $invoices = Invoice::where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();
            $accounts = GeneralLedgerAccount::select('id','account_name AS name')->where('status', 1)->orderBy('account_name')->get();
            $products = Product::with('brands')->where('status', 1)->get()->sortBy('brands.name');
            $venders = \App\Vender::orderBy('name')->get();
            $grns = \App\Grn::with('vender')->where('branch_id', \Auth::user()->branchId())->orderBy('id', 'desc')->get();
            return view('item_returns.create', compact('accounts', 'products', 'invoices', 'venders', 'grns'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function supplierReturnStore(Request $request)
    {
        if (!\Auth::user()->can('create item return') && !\Auth::user()->can('create stock adjustment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'date' => 'required|date',
            'vender_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $branchId = \Auth::user()->branchId();
        $qty = (int) $request->quantity;

        $invQuery = Inventory::where('product_id', $request->product_id)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->where('remain_qty', '>', 0);
        if (!empty($request->grn_id)) {
            $invQuery->where('grn_id', $request->grn_id);
        }
        $inventories = $invQuery->orderBy('id', 'asc')->get();

        if ($inventories->sum('remain_qty') < $qty) {
            return redirect()->back()->with('error', __('Insufficient stock available for supplier return.'));
        }

        \DB::transaction(function () use ($request, $branchId, $qty, $inventories) {
            $remainingToDeduct = $qty;
            $firstInvId = 0;

            foreach ($inventories as $inv) {
                if ($remainingToDeduct <= 0) {
                    break;
                }
                if ($firstInvId === 0) {
                    $firstInvId = $inv->id;
                }
                $deduct = min($remainingToDeduct, (int) $inv->remain_qty);
                $inv->remain_qty -= $deduct;
                $inv->save();
                $remainingToDeduct -= $deduct;

                $stk = new StockTransaction();
                $stk->stock_master_id = 1;
                $stk->inventory_id = $inv->id;
                $stk->product_id = $request->product_id;
                $stk->quantity = -$deduct;
                $stk->date = $request->date;
                $stk->branch_id = $branchId;
                $stk->created_by = \Auth::id();
                $stk->status = 1;
                $stk->save();
            }

            $item = new ItemReturn();
            $item->return_type = 'supplier';
            $item->date = $request->date;
            $item->customer_id = 0;
            $item->vender_id = $request->vender_id;
            $item->invoice_id = 0;
            $item->grn_id = $request->grn_id ?: null;
            $item->inventory_id = $firstInvId;
            $item->product_id = $request->product_id;
            $item->serial_no = $request->serial_no ?? '';
            $item->account_id = 0;
            $item->debit_credit = 0;
            $item->related_account_id = 0;
            $item->related_debit_credit = 0;
            $item->narration = $request->narration ?? ('Supplier Return (Qty: ' . $qty . ')');
            $item->amount = $request->amount;
            $item->branch_id = $branchId;
            $item->status = 1;
            $item->created_by = \Auth::id();
            $item->save();
        });

        return redirect()->route('item_return.index')->with('success', __('Supplier return recorded successfully.'));
    }

    public function invoiceSearch($id){
        $invoice = Invoice::with(['sales.products','sales.saleItems.products'=> function($query){
                                    $query->where('status', 1);
                                }])
                                ->where('id', $id)
                                ->where('status', 1)
                                ->first();
        return json_encode($invoice);
    }

    public function createItemForItem(Request $request){
        if(\Auth::user()->can('create item return'))
        {
            $modalType = $request->model_type;
            $products = Inventory::select('product_id', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products.brands')
                                        ->where('branch_id', \Auth::user()->branchId())
                                        ->where('status',1)
                                        ->groupBy('product_id')
                                        ->get()
                                        ->sortBy('products.brands.name');

            if (isset($request->checked_sale)) {
                $items = Sale::with('products')->whereIn('id', $request->checked_sale)->get();
                return view('item_returns.create_item_for_item', compact('items', 'modalType', 'products'));
            }
            if (isset($request->checked_saleItem)) {
                $items = SaleItem::with('products')->whereIn('id', $request->checked_saleItem)->get();
                return view('item_returns.create_item_for_item', compact('items', 'modalType', 'products'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request){
        if(\Auth::user()->can('create item return'))
        {
            $rules = [
                'date' => 'required',
                'invoice_id' => 'required',
                'inventory_id' => 'required',
                'serial_no' => 'required',
                'product_id' => 'required',
                'account_id' => 'required',
                'debit_credit' => 'required',
                'related_account_id' => 'required',
                'related_debit_credit' => 'required',
                'amount' => 'required',
                'narration' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $item = new ItemReturn();
            $item->date                 = $request->date;
            $item->invoice_id           = $request->invoice_id;
            $item->inventory_id         = $request->inventory_id;
            $item->product_id           = $request->product_id;
            $item->serial_no            = $request->serial_no;
            $item->customer_id          = $request->customer_id;
            $item->account_id           = $request->account_id;
            $item->debit_credit         = $request->debit_credit;
            $item->related_account_id   = $request->related_account_id;
            $item->related_debit_credit = $request->related_debit_credit;
            $item->amount               = $request->amount;
            $item->narration            = $request->narration;
            $item->status               = 1;
            $item->branch_id            = \Auth::user()->branchId();
            $item->created_by           = \Auth::user()->id;
            $item->save();

            Utility::itemReturnAccountTransation($request->date, $request->account_id, $request->debit_credit,  $request->related_account_id, $request->related_debit_credit, $request->amount, $item->id, $request->customer_id);

            return redirect()->back()->with('success', __('Item Return Saved Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id){
        if(\Auth::user()->can('edit item return'))
        {
            $invoices = Invoice::where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();
            $accounts = GeneralLedgerAccount::select('id','account_name AS name')->where('status', 1)->orderBy('account_name')->get();
            $products = Product::where('status', 1)->orderBy('name')->get();
            $itemReturn = ItemReturn::where('id', $id)->first();

            return view('item_returns.edit', compact('accounts', 'products', 'invoices', 'itemReturn'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request){
        if(\Auth::user()->can('edit item return'))
        {
            $rules = [
                'date' => 'required',
                'invoice_id' => 'required',
                'inventory_id' => 'required',
                'product_id' => 'required',
                'serial_no' => 'required',
                'account_id' => 'required',
                'debit_credit' => 'required',
                'related_account_id' => 'required',
                'related_debit_credit' => 'required',
                'amount' => 'required',
                'narration' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $item = ItemReturn::find($request->item_return_id);
            $item->date                 = $request->date;
            $item->invoice_id           = $request->invoice_id;
            $item->inventory_id         = $request->inventory_id;
            $item->product_id           = $request->product_id;
            $item->serial_no            = $request->serial_no;
            $item->customer_id          = $request->customer_id;
            $item->account_id           = $request->account_id;
            $item->debit_credit         = $request->debit_credit;
            $item->related_account_id   = $request->related_account_id;
            $item->related_debit_credit = $request->related_debit_credit;
            $item->amount               = $request->amount;
            $item->narration            = $request->narration;
            $item->update();

            Utility::deleteItemReturnAccountTransation($request->item_return_id);
            Utility::itemReturnAccountTransation($request->date, $request->account_id, $request->debit_credit,  $request->related_account_id, $request->related_debit_credit, $request->amount, $item->id, $request->customer_id);

            return redirect()->back()->with('success', __('Item Return Updated Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id){
        if(\Auth::user()->can('delete item return'))
        {
            $item = ItemReturn::find($id);
            $item->status = 0;
            $item->update();
            Utility::deleteItemReturnAccountTransation($id);
            return redirect()->back()->with('success', __('Item Return Deleted Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // public function store(Request $request){
    //     if(\Auth::user()->can('create item return'))
    //     {
    //         foreach($request->item as $item){
    //             $salesReturns = new SalesReturn();
    //             $salesReturns->branch_id = \Auth::user()->branchId();
    //             $salesReturns->sale_id = $item['sale_id'];
    //             $salesReturns->invoice_id = $item['invoice_id'];
    //             $salesReturns->product_id = $item['product_id'];
    //             $salesReturns->inventory_id = $item['inventory_id'];
    //             $salesReturns->unit_price = $item['unit_price'];
    //             $salesReturns->discount = $item['discount'];
    //             $salesReturns->return_qty = $item['return_qty'];
    //             $salesReturns->return_amount = $item['return_amount'];
    //             $salesReturns->created_by = \Auth::user()->id;
    //             $salesReturns->save();

    //             $inventoryId = $item['inventory_id'];
    //             $saleId = $item['sale_id'];
    //             $invoiceId = $item['invoice_id'];
    //             $returnAmount = $item['return_amount'];
    //             $itemQty = $item['item_qty'];
    //             $returnQty = $item['return_qty'];

    //             $inventory = Inventory::find($inventoryId);
    //             if($inventory){
    //                 // $remainQty = $inventory->remain_qty;
    //                 $newRemainQty = $inventory->remain_qty + $returnQty;
    //                 $inventory->remain_qty = $newRemainQty;
    //                 $inventory->status = 1;
    //                 $inventory->update();
    //             }

    //             // $stkTrans = StockTransaction::where('inventory_id', $inventoryId)->where('quantity', $itemQty)->where('status', 1)->latest()->first();
    //             // if($stkTrans){
    //             //     $stkTrans->status = 0;
    //             //     $stkTrans->update();
    //             // }
    //             $stockTransaction = new StockTransaction();
    //             $stockTransaction->stock_master_id = 5;
    //             $stockTransaction->inventory_id = $inventoryId;
    //             $stockTransaction->product_id = $item['product_id'];
    //             $stockTransaction->quantity = $returnQty;
    //             $stockTransaction->date = Carbon::now()->format('Y-m-d');
    //             $stockTransaction->branch_id = \Auth::user()->branchId();
    //             $stockTransaction->created_by     = \Auth::user()->id;
    //             $stockTransaction->status     = 1;
    //             $stockTransaction->save();

    //             if($request->model_type == 2){
    //                 $saleItemId = $item['item_id'];

    //                 $saleItems = SaleItem::find($saleItemId);
    //                 if($returnQty == $itemQty){
    //                     $saleItems->status = 0;
    //                 } else {
    //                     $saleItems->quantity = $itemQty - $returnQty;
    //                     $saleItems->gross_amount = $saleItems->unit_price * ($itemQty - $returnQty);
    //                     $saleItems->net_amount = $saleItems->net_amount - $returnAmount;
    //                     $saleItems->discount = 0;
    //                 }
    //                 $saleItems->update();
    //             }


    //             $sales = Sale::find($saleId);
    //             if($sales->sale_type == 0){
    //                 $newSaleNetAmount = $sales->net_amount - $returnAmount;
    //                 $sales->net_amount = $newSaleNetAmount;
    //                 if($newSaleNetAmount <= 0){
    //                     $sales->status = 4;
    //                     $invoice = Invoice::find($invoiceId);
    //                     $invoice->status = 0;
    //                     $invoice->update();
    //                 }
    //             } elseif($sales->sale_type == 1){
    //                 $sales->status = 4;
    //                 $invoice = Invoice::find($invoiceId);
    //                 $invoice->status = 0;
    //                 $invoice->update();
    //             }

    //             $sales->update();
    //         }
    //         return redirect()->route('item_return.index')->with('success', __('Items Returned Succsessfully!.'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    public function storeItemForItem(Request $request){
        if(\Auth::user()->can('create item return'))
        {
            $saleId = '';
            $invoiceId = '';
            foreach($request->return_item as $item){
                $salesReturns = new SalesReturn();
                $salesReturns->branch_id = \Auth::user()->branchId();
                $salesReturns->sale_id = $item['sale_id'];
                $salesReturns->invoice_id = $item['invoice_id'];
                $salesReturns->product_id = $item['product_id'];
                $salesReturns->inventory_id = $item['inventory_id'];
                $salesReturns->unit_price = $item['unit_price'];
                $salesReturns->discount = $item['discount'];
                $salesReturns->return_qty = $item['return_qty'];
                $salesReturns->return_amount = $item['return_amount'];
                $salesReturns->created_by = \Auth::user()->id;
                $salesReturns->save();

                $inventoryId = $item['inventory_id'];
                $saleId = $item['sale_id'];
                $invoiceId = $item['invoice_id'];
                $returnAmount = $item['return_amount'];
                $itemQty = $item['item_qty'];
                $returnQty = $item['return_qty'];

                $returnInventory = Inventory::find($inventoryId);
                if($returnInventory){
                    // $remainQty = $returnInventory->remain_qty;
                    $newRemainQty = $returnInventory->remain_qty + $returnQty;
                    $returnInventory->remain_qty = $newRemainQty;
                    $returnInventory->status = 1;
                    $returnInventory->update();
                }

                $stockTransaction = new StockTransaction();
                $stockTransaction->stock_master_id = 5;
                $stockTransaction->inventory_id = $inventoryId;
                $stockTransaction->product_id = $item['product_id'];
                $stockTransaction->quantity = $returnQty;
                $stockTransaction->date = Carbon::now()->format('Y-m-d');
                $stockTransaction->branch_id = \Auth::user()->branchId();
                $stockTransaction->created_by     = \Auth::user()->id;
                $stockTransaction->status     = 1;
                $stockTransaction->save();

                if($request->model_type == 2){
                    $saleItemId = $item['item_id'];

                    $saleItems = SaleItem::find($saleItemId);
                    if($returnQty == $itemQty){
                        $saleItems->status = 0;
                    } else {
                        $saleItems->quantity = $itemQty - $returnQty;
                        $saleItems->gross_amount = $saleItems->unit_price * ($itemQty - $returnQty);
                        $saleItems->net_amount = $saleItems->net_amount - $returnAmount;
                        $saleItems->discount = 0;
                    }
                    $saleItems->update();
                }

                //==================> sales functions===========
                // $sales = Sale::find($saleId);

                // $newSaleNetAmount = $sales->net_amount - $returnAmount;
                // $sales->net_amount = $newSaleNetAmount;
                // $sales->update();
            }

            if($request->model_type == 1){
                foreach($request->items as $item){
                    $sale                 = Sale::find($saleId);
                    $sale->product_id     = $item['product'];
                    $sale->unit_price     = $item['new_unit_price'];
                    $sale->quantity       = $item['new_quantity'];
                    $sale->gross_amount   = $item['new_gross_amount'];
                    $sale->discount       = $item['new_discount'];
                    $sale->net_amount     = $item['new_net_amount'];
                    $sale->update();

                    $quantity = $item['new_quantity'];
                    $saleProduct = $item['product'];
                    while ($quantity > 0) {
                    $inventory = Inventory::where('product_id', $saleProduct)->where('branch_id', \Auth::user()->branchId())->where('status', 1)->first();

                        if($inventory->remain_qty > $quantity){
                            $inventory->remain_qty = $inventory->remain_qty - $quantity;
                            $inventory->update();

                            $stockTransaction = new StockTransaction();
                            $stockTransaction->stock_master_id = 2;
                            $stockTransaction->inventory_id = $inventory->id;
                            $stockTransaction->product_id = $saleProduct;
                            $stockTransaction->quantity = $quantity;
                            $stockTransaction->date = Carbon::now()->format('Y-m-d');
                            $stockTransaction->branch_id = \Auth::user()->branchId();
                            $stockTransaction->created_by     = \Auth::user()->id;
                            $stockTransaction->status     = 1;
                            $stockTransaction->save();

                            $quantity = 0;

                        } elseif ($inventory->remain_qty <= $quantity){

                            $stockTransaction = new StockTransaction();
                            $stockTransaction->stock_master_id = 2;
                            $stockTransaction->inventory_id = $inventory->id;
                            $stockTransaction->product_id = $saleProduct;
                            $stockTransaction->quantity = $inventory->remain_qty;
                            $stockTransaction->date = Carbon::now()->format('Y-m-d');
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
                }

            } else if($request->model_type == 2){
                foreach($request->items as $item){
                    $saleItem = new SaleItem();
                    $saleItem->sale_id =  $saleId;
                    $saleItem->invoice_id =  $invoiceId;
                    $saleItem->date_of_sale =  Carbon::now()->format('Y-m-d');
                    $saleItem->product_id     = $item['product'];
                    $saleItem->branch_id       = \Auth::user()->branchId();
                    $saleItem->unit_price     = $item['new_unit_price'];
                    $saleItem->quantity     = $item['new_quantity'];
                    $saleItem->gross_amount     = $item['new_gross_amount'];
                    $saleItem->discount     = $item['new_discount'];
                    $saleItem->net_amount     = $item['new_net_amount'];
                    $saleItem->status     = 1;
                    $saleItem->inventory_id     = $item['new_inventory_id'];
                    $saleItem->created_by     = \Auth::user()->id;
                    $saleItem->save();

                    $newInventoryId = $item['new_inventory_id'];
                    $quantity = $item['new_quantity'];
                    $saleItemProduct = $item['product'];
                    while ($quantity > 0) {
                    // $inventory = Inventory::where('product_id', $saleItemProduct)->where('branch_id', \Auth::user()->branchId())->where('status', 1)->first();
                    $inventory = Inventory::find($newInventoryId);

                            if($inventory->remain_qty > $quantity){
                                $inventory->remain_qty = $inventory->remain_qty - $quantity;
                                $inventory->update();

                                $stockTransaction = new StockTransaction();
                                $stockTransaction->stock_master_id = 2;
                                $stockTransaction->inventory_id = $inventory->id;
                                $stockTransaction->product_id = $item['product'];
                                $stockTransaction->quantity = $quantity;
                                $stockTransaction->date = Carbon::now()->format('Y-m-d');
                                $stockTransaction->branch_id = \Auth::user()->branchId();
                                $stockTransaction->created_by     = \Auth::user()->id;
                                $stockTransaction->status     = 1;
                                $stockTransaction->save();

                                $quantity = 0;
                            } elseif ($inventory->remain_qty <= $quantity){

                                $stockTransaction = new StockTransaction();
                                $stockTransaction->stock_master_id = 2;
                                $stockTransaction->inventory_id = $inventory->id;
                                $stockTransaction->product_id = $item['product'];
                                $stockTransaction->quantity = $inventory->remain_qty;
                                $stockTransaction->date = Carbon::now()->format('Y-m-d');
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
                }
            }
            return redirect()->route('item_return.index')->with('success', __('Items Returned Succsessfully!.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
