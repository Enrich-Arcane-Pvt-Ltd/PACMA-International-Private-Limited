<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\Sale;
use App\SaleItem;
use App\StockTransaction;
use App\Services\SaleBatchCatalog;
use Illuminate\Http\Request;

class StockBatchController extends Controller
{
    public function options(Request $request, $product)
    {
        abort_unless(auth()->user()->can('create sale') || auth()->user()->can('edit sale'), 403);
        $sale = null;
        if ($request->filled('sale_id')) {
            abort_unless(auth()->user()->can('edit sale'), 403);
            $saleQuery = Sale::query();
            $branchId = auth()->user()->branchId();
            if (!empty($branchId)) {
                $saleQuery->where('branch_id', $branchId);
            }
            $sale = $saleQuery->findOrFail($request->sale_id);
        }
        $selectedProduct = \App\Product::with('categoryDetails')->findOrFail($product);
        return response()->json([
            'name' => $selectedProduct->name,
            'down_payment_rate' => (float) optional($selectedProduct->categoryDetails)->down_payment / 100,
            'batches' => SaleBatchCatalog::list($product, $sale)
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function show(Request $request, $id)
    {
        abort_unless(auth()->user()->can('manage stock'), 403);
        $dates = $request->validate(['from' => 'nullable|date_format:Y-m-d', 'to' => 'nullable|date_format:Y-m-d' . ($request->filled('from') ? '|after_or_equal:from' : '')]);
        $branchId = auth()->user()->branchId();
        $batchQuery = Inventory::with('products', 'grns', 'branch');
        if (!empty($branchId)) {
            $batchQuery->where('branch_id', $branchId);
        }
        $batch = $batchQuery->findOrFail($id);
        $movements = StockTransaction::where('inventory_id', $id)->where('status', 1);
        $cash = SaleItem::with('sale', 'invoice')->where('inventory_id', $id)->where('status', 1)
            ->whereHas('sale', function ($query) { $query->whereIn('status', [1, 2, 3]); });
        $single = Sale::with('invoice')->where('inventory_id', $id)->whereIn('status', [1, 2, 3])->where('modal_type', 1);
        foreach (['from' => '>=', 'to' => '<='] as $field => $operator) {
            if (!empty($dates[$field])) {
                $movements->whereDate('date', $operator, $dates[$field]);
                $cash->whereDate('date_of_sale', $operator, $dates[$field]);
                $single->whereDate('date_of_sale', $operator, $dates[$field]);
            }
        }
        $movements = $movements->orderBy('date')->orderBy('id')->get();
        $cashRows = $cash->get();
        $singleRows = $single->get();
        $units = collect();
        $warning = null;
        $lines = \App\GrnProduct::withTrashed()->where('grn_id', $batch->grn_id)->where('product_id', $batch->product_id)->get();
        foreach ($lines as $line) {
            try {
                if (\App\Services\GrnStockBatch::find($line)->id == $batch->id) {
                    $units = $units->merge(\App\ProductWarrantySerial::where('grn_product_id', $line->id)->get());
                }
            } catch (\Illuminate\Validation\ValidationException $e) { $warning = __('Legacy serial records could not be matched to this batch.'); }
        }
        return view('stock.batch', compact('batch', 'movements', 'cashRows', 'singleRows', 'units', 'warning'));
    }
}
