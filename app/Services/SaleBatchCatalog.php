<?php

namespace App\Services;

use App\GrnProduct;
use App\Inventory;
use App\ProductWarrantySerial;
use App\Sale;
use App\SaleItem;

class SaleBatchCatalog
{
    public static function ownedRows($sale)
    {
        if (!$sale) return collect();
        return (int) $sale->modal_type === 2
            ? SaleItem::where('sale_id', $sale->id)->where('status', 1)->get()
            : collect([$sale]);
    }

    public static function list($productId, $sale = null)
    {
        $owned = self::ownedRows($sale);
        $quantities = $owned->groupBy('inventory_id')->map(function ($rows) { return $rows->sum('quantity'); });
        $unitIds = $owned->pluck('product_warranty_id')->filter();
        if ($sale && (int) $sale->modal_type !== 2) {
            $unitIds = \App\HirePurchaseDetail::where('sale_id', $sale->id)->pluck('product_warranty_id');
        }
        $branchId = auth()->user() ? auth()->user()->branchId() : null;
        $batchQuery = Inventory::with('grns')
            ->where('product_id', $productId)
            ->where(function ($query) use ($quantities) {
                $query->where(function ($active) {
                    $active->where('status', 1)->where('remain_qty', '>', 0);
                })->orWhereIn('id', $quantities->keys());
            })
            ->orderBy('id');

        if (!empty($branchId)) {
            $batches = (clone $batchQuery)->where('branch_id', $branchId)->get();
            if ($batches->isEmpty()) {
                $batches = $batchQuery->get();
            }
        } else {
            $batches = $batchQuery->get();
        }
        $lines = GrnProduct::where('product_id', $productId)->whereIn('grn_id', $batches->pluck('grn_id'))->get();
        $lineBatches = [];
        foreach ($lines as $line) {
            try { $lineBatches[$line->id] = GrnStockBatch::find($line)->id; }
            catch (\Illuminate\Validation\ValidationException $e) { /* Do not guess legacy batch ownership. */ }
        }
        $units = ProductWarrantySerial::whereIn('grn_product_id', array_keys($lineBatches))
            ->where(function ($query) use ($unitIds) { $query->where('status', 0)->orWhereIn('id', $unitIds); })
            ->orderBy('id')->get();
        return $batches->map(function ($batch) use ($units, $lineBatches, $quantities) {
            return [
                'id' => $batch->id, 'code' => 'B' . str_pad($batch->id, 6, '0', STR_PAD_LEFT),
                'product_id' => $batch->product_id, 'grn_id' => $batch->grn_id,
                'invoice_number' => $batch->supplier_invoice_number, 'date' => $batch->date,
                'purchase_price' => $batch->purchase_price, 'sale_price' => $batch->sale_price, 'hire_price' => $batch->hire_price,
                'remain_qty' => $batch->remain_qty, 'available_qty' => $batch->remain_qty + ($quantities[$batch->id] ?? 0),
                'units' => $units->filter(function ($unit) use ($lineBatches, $batch) {
                    return ($lineBatches[$unit->grn_product_id] ?? null) == $batch->id;
                })->values()->toArray(),
            ];
        });
    }
}
