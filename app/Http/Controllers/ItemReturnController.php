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
            $query = ItemReturn::whereIn('status', [1, 2, 3]);
            if (\Auth::user()->branchId() != 0) {
                $query->where('branch_id', \Auth::user()->branchId());
            }
            $itemReturns = $query->orderBy('id', 'desc')->get();
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
            $returnedByInvoice = ItemReturn::whereIn('status', [1, 2])
                ->where('invoice_id', '>', 0)
                ->get(['invoice_id', 'product_id', 'serial_no'])
                ->groupBy('invoice_id');

            $invoices = Invoice::with(['sales.saleItems' => function ($q) {
                    $q->where('status', 1);
                }])
                ->where('status', 1)
                ->where('branch_id', \Auth::user()->branchId())
                ->get()
                ->filter(function ($inv) use ($returnedByInvoice) {
                    $sale = $inv->sales;
                    if (!$sale || (int) $sale->status === 4) {
                        return false;
                    }
                    $returned = $returnedByInvoice->get($inv->id, collect());
                    if ((int) $sale->modal_type === 1) {
                        return $returned->where('product_id', $sale->product_id)->isEmpty();
                    }
                    if ((int) $sale->modal_type === 2) {
                        $activeItems = $sale->saleItems;
                        if ($activeItems->isEmpty()) {
                            return false;
                        }
                        $returnedProducts = $returned->pluck('product_id')->all();
                        $remaining = $activeItems->filter(function ($si) use ($returnedProducts) {
                            return !in_array($si->product_id, $returnedProducts);
                        });
                        return $remaining->isNotEmpty();
                    }
                    return true;
                })->values();
            $accounts = GeneralLedgerAccount::select('id','account_name')->where('status', 1)->orderBy('account_name')->get();
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

        $firstInvId = $inventories->first()->id ?? 0;

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
        $item->account_id = 87;
        $item->debit_credit = 1;
        $item->related_account_id = 8;
        $item->related_debit_credit = 0;
        $item->narration = $request->narration ?? ('Supplier Return (Qty: ' . $qty . ')');
        $item->meta = json_encode(['return_qty' => $qty]);
        $item->amount = $request->amount;
        $item->branch_id = $branchId;
        $item->status = 2; // Pending Admin Approval
        $item->created_by = \Auth::id();
        $item->save();

        return redirect()->route('item_return.index')->with('success', __('Supplier return submitted for Admin approval!'));
    }

    public function supplierFilterData(Request $request)
    {
        $branchId = \Auth::user()->branchId();
        $venderId = $request->get('vender_id');
        $grnId = $request->get('grn_id');
        $productId = $request->get('product_id');

        $invQuery = Inventory::with('products')
            ->where('status', 1)
            ->where('remain_qty', '>', 0);
        if ($branchId != 0) {
            $invQuery->where('branch_id', $branchId);
        }

        if (!empty($grnId)) {
            $invQuery->where('grn_id', $grnId);
        } elseif (!empty($venderId)) {
            $grnIds = \App\Grn::where('vender_id', $venderId)
                ->where('branch_id', $branchId)
                ->pluck('id');
            $invQuery->whereIn('grn_id', $grnIds);
        }

        $inventories = $invQuery->get();

        $products = $inventories->groupBy('product_id')->map(function ($rows) {
            $first = $rows->first();
            $prod = $first->products;
            $avail = (int) $rows->sum('remain_qty');
            return [
                'id' => $first->product_id,
                'label' => ($prod ? ($prod->barcode . ' - ' . $prod->name) : ('Product #' . $first->product_id)) . ' (Avail: ' . $avail . ')',
                'avail' => $avail,
                'cost' => (float) $first->purchase_price,
            ];
        })->values();

        $serials = [];
        if (!empty($productId)) {
            $gpQuery = \App\GrnProduct::where('product_id', $productId);
            if (!empty($grnId)) {
                $gpQuery->where('grn_id', $grnId);
            } elseif (!empty($venderId)) {
                $grnIds = \App\Grn::where('vender_id', $venderId)
                    ->where('branch_id', $branchId)
                    ->pluck('id');
                $gpQuery->whereIn('grn_id', $grnIds);
            }
            $gpIds = $gpQuery->pluck('id');

            if ($gpIds->isNotEmpty()) {
                $serials = \App\ProductWarrantySerial::whereIn('grn_product_id', $gpIds)
                    ->where('status', 0)
                    ->whereNotNull('serial_number')
                    ->where('serial_number', '!=', '')
                    ->orderBy('id', 'desc')
                    ->limit(100)
                    ->pluck('serial_number')
                    ->unique()
                    ->values()
                    ->toArray();
            }
        }

        return response()->json([
            'products' => $products,
            'serials' => $serials,
        ]);
    }

    public function invoiceSearch($id){
        $invoice = Invoice::with([
                                    'sales.products',
                                    'sales.hirePurchaseDetails.warranty',
                                    'sales.saleItems.products' => function($query){
                                        $query->where('status', 1);
                                    },
                                    'sales.saleItems.warranty',
                                ])
                                ->where('id', $id)
                                ->where('status', 1)
                                ->first();

        if ($invoice && $invoice->sales) {
            $returnedProductIds = ItemReturn::where('invoice_id', $id)
                ->whereIn('status', [1, 2])
                ->pluck('product_id')
                ->all();

            if ((int) $invoice->sales->modal_type === 1) {
                if ((int) $invoice->sales->status === 4 || in_array($invoice->sales->product_id, $returnedProductIds)) {
                    return json_encode(null);
                }
            } elseif ((int) $invoice->sales->modal_type === 2) {
                $filtered = $invoice->sales->saleItems->filter(function ($si) use ($returnedProductIds) {
                    return !in_array($si->product_id, $returnedProductIds);
                })->values();
                $invoice->sales->setRelation('saleItems', $filtered);
                if ($filtered->isEmpty()) {
                    return json_encode(null);
                }
            }
        }

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

            if ($request->item_condition === 'damaged_resell') {
                $rules['resell_sale_price'] = 'required|numeric|min:0';
            }

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $condition = $request->item_condition ?? 'account_only';
            $qty = max(1, (int) ($request->return_qty ?? 1));
            $narration = $request->narration;
            if ($condition === 'good') {
                $narration .= ' [Condition: Good - Restocked]';
            } elseif ($condition === 'damaged_resell') {
                $narration .= ' [Condition: Damaged - Resell @ ' . number_format((float) $request->resell_sale_price, 2) . ']';
            } elseif ($condition === 'damaged_scrap') {
                $narration .= ' [Condition: Damaged - Scrap / Removed]';
            }

            $item = new ItemReturn();
            $item->return_type          = 'customer';
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
            $item->narration            = $narration;
            $item->meta                 = json_encode([
                'return_qty'        => $qty,
                'item_condition'    => $condition,
                'resell_sale_price' => $request->resell_sale_price,
                'resell_hire_price' => $request->resell_hire_price,
            ]);
            $item->status               = 2; // Pending Admin Approval
            $item->branch_id            = \Auth::user()->branchId();
            $item->created_by           = \Auth::user()->id;
            $item->save();

            return redirect()->back()->with('success', __('Item Return submitted for Admin approval!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function edit($id){
        if(\Auth::user()->can('edit item return'))
        {
            $itemReturn = ItemReturn::where('id', $id)->first();
            $returnedByInvoice = ItemReturn::whereIn('status', [1, 2])
                ->where('id', '!=', $id)
                ->where('invoice_id', '>', 0)
                ->get(['invoice_id', 'product_id', 'serial_no'])
                ->groupBy('invoice_id');

            $invoices = Invoice::with(['sales.saleItems' => function ($q) {
                    $q->where('status', 1);
                }])
                ->where('status', 1)
                ->where('branch_id', \Auth::user()->branchId())
                ->get()
                ->filter(function ($inv) use ($returnedByInvoice) {
                    $sale = $inv->sales;
                    if (!$sale || (int) $sale->status === 4) {
                        return false;
                    }
                    $returned = $returnedByInvoice->get($inv->id, collect());
                    if ((int) $sale->modal_type === 1) {
                        return $returned->where('product_id', $sale->product_id)->isEmpty();
                    }
                    if ((int) $sale->modal_type === 2) {
                        $activeItems = $sale->saleItems;
                        if ($activeItems->isEmpty()) {
                            return false;
                        }
                        $returnedProducts = $returned->pluck('product_id')->all();
                        $remaining = $activeItems->filter(function ($si) use ($returnedProducts) {
                            return !in_array($si->product_id, $returnedProducts);
                        });
                        return $remaining->isNotEmpty();
                    }
                    return true;
                })->values();
            $accounts = GeneralLedgerAccount::select('id','account_name')->where('status', 1)->orderBy('account_name')->get();
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
            $item = ItemReturn::findOrFail($id);
            \DB::transaction(function () use ($item, $id) {
                $wasApproved = ((int) $item->status === 1);

                $item->status = 0;
                $item->save();

                Utility::deleteItemReturnAccountTransation($id);

                if ($wasApproved) {
                    if (($item->return_type ?? 'customer') === 'supplier') {
                        $meta = json_decode($item->meta, true) ?: [];
                        $qty = max(1, (int) ($meta['return_qty'] ?? 1));
                        $inv = Inventory::find($item->inventory_id);
                        if ($inv) {
                            $inv->remain_qty += $qty;
                            $inv->status = 1;
                            $inv->save();
                        }
                        StockTransaction::where('product_id', $item->product_id)
                            ->where('stock_master_id', 1)
                            ->where('date', $item->date)
                            ->where('quantity', '<', 0)
                            ->where('status', 1)
                            ->update(['status' => 0]);
                    } else {
                        SalesReturn::where('invoice_id', $item->invoice_id)
                            ->where('product_id', $item->product_id)
                            ->where('return_amount', $item->amount)
                            ->delete();

                        \App\FinancialEntry::where('invoice_id', $item->invoice_id)
                            ->where('entry_type', 'item_return')
                            ->where('amount', $item->amount)
                            ->delete();

                        $createdInvs = Inventory::whereIn('supplier_invoice_number', ['SRN-RET-' . $item->id, 'SRN-DMG-' . $item->id])->get();
                        foreach ($createdInvs as $cInv) {
                            $cInv->remain_qty = 0;
                            $cInv->status = 0;
                            $cInv->save();
                            StockTransaction::where('inventory_id', $cInv->id)->where('stock_master_id', 5)->update(['status' => 0]);
                        }

                        if (!empty($item->serial_no)) {
                            \App\ProductWarrantySerial::where('serial_number', $item->serial_no)
                                ->where('status', 0)
                                ->update(['status' => 1]);
                        }

                        $sale = Sale::where('invoice_id', $item->invoice_id)->first();
                        if ($sale) {
                            if ((int) $sale->sale_type === 0) {
                                $sale->net_amount = (float) $sale->net_amount + (float) $item->amount;
                            }
                            if ((int) $sale->status === 4) {
                                $sale->status = 1;
                            }
                            $sale->save();
                        }
                    }
                }
            });

            return redirect()->back()->with('success', __('Item Return Deleted Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function approve($id)
    {
        if (!\Auth::user()->can('approve authorization') && !\Auth::user()->can('manage authorization') && \Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $item = ItemReturn::findOrFail($id);
        if ((int) $item->status !== 2) {
            return redirect()->back()->with('error', __('This Item Return has already been processed.'));
        }

        $meta = json_decode($item->meta, true) ?: [];
        $qty = max(1, (int) ($meta['return_qty'] ?? 1));

        if (($item->return_type ?? 'customer') === 'supplier') {
            $invQuery = Inventory::where('product_id', $item->product_id)
                ->where('branch_id', $item->branch_id)
                ->where('status', 1)
                ->where('remain_qty', '>', 0);
            if (!empty($item->grn_id)) {
                $invQuery->where('grn_id', $item->grn_id);
            }
            $inventories = $invQuery->orderBy('id', 'asc')->get();

            if ($inventories->sum('remain_qty') < $qty) {
                return redirect()->back()->with('error', __('Insufficient stock available to approve this supplier return.'));
            }

            \DB::transaction(function () use ($item, $qty, $inventories) {
                $remainingToDeduct = $qty;
                foreach ($inventories as $inv) {
                    if ($remainingToDeduct <= 0) {
                        break;
                    }
                    $deduct = min($remainingToDeduct, (int) $inv->remain_qty);
                    $inv->remain_qty -= $deduct;
                    $inv->save();
                    $remainingToDeduct -= $deduct;

                    $stk = new StockTransaction();
                    $stk->stock_master_id = 1;
                    $stk->inventory_id = $inv->id;
                    $stk->product_id = $item->product_id;
                    $stk->quantity = -$deduct;
                    $stk->date = $item->date;
                    $stk->branch_id = $item->branch_id;
                    $stk->created_by = $item->created_by;
                    $stk->status = 1;
                    $stk->save();
                }

                if ((float) $item->amount > 0) {
                    Utility::itemReturnAccountTransation($item->date, 87, 1, 8, 0, (float) $item->amount, $item->id, 0);
                }

                $item->status = 1;
                $item->approved_by = \Auth::id();
                $item->approved_at = Carbon::now();
                $item->save();
            });
        } else {
            $condition = $meta['item_condition'] ?? 'account_only';
            $resellSalePrice = $meta['resell_sale_price'] ?? 0;
            $resellHirePrice = $meta['resell_hire_price'] ?? null;

            \DB::transaction(function () use ($item, $qty, $condition, $resellSalePrice, $resellHirePrice) {
                Utility::itemReturnAccountTransation($item->date, $item->account_id, $item->debit_credit, $item->related_account_id, $item->related_debit_credit, $item->amount, $item->id, $item->customer_id);

                $sale = Sale::where('invoice_id', $item->invoice_id)->first();
                $salesReturn = new SalesReturn();
                $salesReturn->branch_id = $item->branch_id;
                $salesReturn->sale_id = $sale ? $sale->id : 0;
                $salesReturn->invoice_id = $item->invoice_id;
                $salesReturn->product_id = $item->product_id;
                $salesReturn->inventory_id = $item->inventory_id;
                $salesReturn->unit_price = $qty > 0 ? round((float) $item->amount / $qty, 2) : (float) $item->amount;
                $salesReturn->discount = 0;
                $salesReturn->return_qty = $qty;
                $salesReturn->return_amount = (float) $item->amount;
                $salesReturn->created_by = $item->created_by;
                $salesReturn->save();

                if ($sale) {
                    if ($sale->modal_type == 2) {
                        $saleItem = SaleItem::where('sale_id', $sale->id)
                            ->where('product_id', $item->product_id)
                            ->where('status', 1)
                            ->first();
                        if ($saleItem) {
                            if ($qty >= (int) $saleItem->quantity) {
                                $saleItem->status = 0;
                            } else {
                                $saleItem->quantity = (int) $saleItem->quantity - $qty;
                                $saleItem->gross_amount = $saleItem->unit_price * $saleItem->quantity;
                                $saleItem->net_amount = max(0, (float) $saleItem->net_amount - (float) $item->amount);
                            }
                            $saleItem->save();
                        }
                    }

                    if ($sale->sale_type == 0) {
                        $sale->net_amount = max(0, (float) $sale->net_amount - (float) $item->amount);
                        if ($sale->net_amount <= 0) {
                            $sale->status = 4;
                        }
                    } elseif ((float) $item->amount >= (float) $sale->net_amount) {
                        $sale->status = 4;
                    }
                    $sale->save();
                }

                if (((int) $item->account_id === 1 && (int) $item->debit_credit === 0) ||
                    ((int) $item->related_account_id === 1 && (int) $item->related_debit_credit === 0)) {
                    $financialEntry = new \App\FinancialEntry();
                    $financialEntry->date = $item->date;
                    $financialEntry->type = 'Item Return';
                    $financialEntry->payment_method = 'CASH';
                    $financialEntry->customer_id = $item->customer_id ?: null;
                    $financialEntry->invoice_id = $item->invoice_id;
                    $financialEntry->sale_id = $sale ? $sale->id : null;
                    $financialEntry->remarks = $item->narration;
                    $financialEntry->amount = (float) $item->amount;
                    $financialEntry->debit_credit = 'credit';
                    $financialEntry->entry_type = 'item_return';
                    $financialEntry->branch_id = $item->branch_id;
                    $financialEntry->created_by = $item->created_by;
                    $financialEntry->save();
                }

                if ($condition === 'good') {
                    $origInv = Inventory::find($item->inventory_id);

                    $newInv = new Inventory();
                    $newInv->grn_id = $origInv ? $origInv->grn_id : 0;
                    $newInv->grn_product_id = $origInv ? $origInv->grn_product_id : null;
                    $newInv->product_id = $item->product_id;
                    $newInv->branch_id = $item->branch_id;
                    $newInv->grn_qty = $qty;
                    $newInv->remain_qty = $qty;
                    $newInv->date = $item->date;
                    $newInv->purchase_price = $origInv ? $origInv->purchase_price : 0;
                    $newInv->sale_price = $origInv ? $origInv->sale_price : 0;
                    $newInv->hire_price = $origInv ? $origInv->hire_price : 0;
                    $newInv->supplier_invoice_number = 'SRN-RET-' . $item->id;
                    $newInv->status = 1;
                    $newInv->save();

                    $stockTransaction = new StockTransaction();
                    $stockTransaction->stock_master_id = 5;
                    $stockTransaction->inventory_id = $newInv->id;
                    $stockTransaction->product_id = $item->product_id;
                    $stockTransaction->quantity = $qty;
                    $stockTransaction->date = $item->date;
                    $stockTransaction->branch_id = $item->branch_id;
                    $stockTransaction->created_by = $item->created_by;
                    $stockTransaction->status = 1;
                    $stockTransaction->save();

                    if ($origInv && (float) $origInv->purchase_price > 0 && !in_array((int) $item->account_id, [7, 8]) && !in_array((int) $item->related_account_id, [7, 8])) {
                        Utility::itemReturnAccountTransation($item->date, 8, 1, 7, 0, (float) $origInv->purchase_price * $qty, $item->id, $item->customer_id);
                    }

                    if (!empty($item->serial_no)) {
                        \App\ProductWarrantySerial::where('serial_number', $item->serial_no)
                            ->where('status', 1)
                            ->update(['status' => 0]);
                    }
                } elseif ($condition === 'damaged_resell') {
                    $origInv = Inventory::find($item->inventory_id);

                    $newInv = new Inventory();
                    $newInv->grn_id = $origInv ? $origInv->grn_id : 0;
                    $newInv->grn_product_id = $origInv ? $origInv->grn_product_id : null;
                    $newInv->product_id = $item->product_id;
                    $newInv->branch_id = $item->branch_id;
                    $newInv->grn_qty = $qty;
                    $newInv->remain_qty = $qty;
                    $newInv->date = $item->date;
                    $newInv->purchase_price = $origInv ? $origInv->purchase_price : 0;
                    $newInv->sale_price = (float) $resellSalePrice;
                    $newInv->hire_price = ($resellHirePrice !== null && $resellHirePrice !== '')
                        ? (float) $resellHirePrice
                        : (float) $resellSalePrice;
                    $newInv->supplier_invoice_number = 'SRN-DMG-' . $item->id;
                    $newInv->status = 1;
                    $newInv->save();

                    $stockTransaction = new StockTransaction();
                    $stockTransaction->stock_master_id = 5;
                    $stockTransaction->inventory_id = $newInv->id;
                    $stockTransaction->product_id = $item->product_id;
                    $stockTransaction->quantity = $qty;
                    $stockTransaction->date = $item->date;
                    $stockTransaction->branch_id = $item->branch_id;
                    $stockTransaction->created_by = $item->created_by;
                    $stockTransaction->status = 1;
                    $stockTransaction->save();

                    if (!empty($item->serial_no)) {
                        \App\ProductWarrantySerial::where('serial_number', $item->serial_no)
                            ->where('status', 1)
                            ->update(['status' => 0]);
                    }
                }

                $item->status = 1;
                $item->approved_by = \Auth::id();
                $item->approved_at = Carbon::now();
                $item->save();
            });
        }

        return redirect()->back()->with('success', __('Item Return Approved Successfully!'));
    }

    public function reject(Request $request, $id)
    {
        if (!\Auth::user()->can('approve authorization') && !\Auth::user()->can('manage authorization') && \Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $item = ItemReturn::findOrFail($id);
        if ((int) $item->status !== 2) {
            return redirect()->back()->with('error', __('This Item Return has already been processed.'));
        }

        $item->status = 3;
        $item->rejection_reason = $request->rejection_reason ?? null;
        $item->approved_by = \Auth::id();
        $item->approved_at = Carbon::now();
        $item->save();

        return redirect()->back()->with('success', __('Item Return Rejected.'));
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
