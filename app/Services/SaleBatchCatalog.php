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
        $supplierReturnedSerials = \App\ItemReturn::where('product_id', $productId)
            ->where('return_type', 'supplier')
            ->where('status', 1)
            ->whereNotNull('serial_no')
            ->where('serial_no', '!=', '')
            ->pluck('serial_no')
            ->map(function ($s) { return trim((string) $s); })
            ->filter()
            ->values()
            ->all();

        $units = ProductWarrantySerial::whereIn('grn_product_id', array_keys($lineBatches))
            ->where(function ($query) use ($unitIds, $supplierReturnedSerials) {
                $query->where(function ($q) use ($supplierReturnedSerials) {
                    $q->where('status', 0);
                    if (!empty($supplierReturnedSerials)) {
                        $q->whereNotIn('serial_number', $supplierReturnedSerials);
                    }
                })->orWhereIn('id', $unitIds);
            })
            ->orderBy('id')->get();
        $returnedBatchIds = \App\StockTransaction::whereIn('inventory_id', $batches->pluck('id'))
            ->where('stock_master_id', 5)
            ->where('quantity', '>', 0)
            ->where('status', 1)
            ->pluck('inventory_id')
            ->flip();

        $mapped = $batches->map(function ($batch) use ($units, $lineBatches, $quantities, $returnedBatchIds) {
            $isDamagedReturn = strpos((string) $batch->supplier_invoice_number, 'SRN-DMG-') === 0;
            $isGoodReturn = strpos((string) $batch->supplier_invoice_number, 'SRN-RET-') === 0;
            $hasReturnStock = $returnedBatchIds->has($batch->id);
            $returnLabel = $isDamagedReturn
                ? 'RETURNED - DAMAGED (REDUCED PRICE)'
                : (($isGoodReturn || $hasReturnStock) ? 'RETURNED ITEM' : null);

            $batchUnits = $units->filter(function ($unit) use ($lineBatches, $batch) {
                return ($lineBatches[$unit->grn_product_id] ?? null) == $batch->id;
            })->map(function ($unit) use ($batch) {
                $arr = $unit->toArray();
                $arr['inventory_id'] = $batch->id;
                return $arr;
            })->values()->toArray();

            return [
                'id' => $batch->id,
                'code' => 'B' . str_pad($batch->id, 6, '0', STR_PAD_LEFT),
                'product_id' => $batch->product_id,
                'grn_id' => $batch->grn_id,
                'invoice_number' => $batch->supplier_invoice_number,
                'date' => $batch->date,
                'purchase_price' => $batch->purchase_price,
                'sale_price' => $batch->sale_price,
                'hire_price' => $batch->hire_price,
                'remain_qty' => $batch->remain_qty,
                'available_qty' => $batch->remain_qty + ($quantities[$batch->id] ?? 0),
                'is_returned' => $isDamagedReturn || $isGoodReturn || $hasReturnStock,
                'is_damaged_return' => $isDamagedReturn,
                'return_label' => $returnLabel,
                'units' => $batchUnits,
                '_owned_qty' => $quantities[$batch->id] ?? 0,
            ];
        });

        // Group normal batches with identical prices (purchase_price, sale_price, hire_price) into a single FIFO entry;
        // keep different-priced batches and returned batches (SRN-RET- / SRN-DMG-) separate.
        return $mapped->groupBy(function ($b) {
            if ($b['is_returned']) {
                return 'ret_' . $b['id'];
            }
            return number_format((float) $b['purchase_price'], 2, '.', '') . '|' .
                   number_format((float) $b['sale_price'], 2, '.', '') . '|' .
                   number_format((float) $b['hire_price'], 2, '.', '');
        })->map(function ($group) {
            if ($group->count() === 1) {
                $first = $group->first();
                unset($first['_owned_qty']);
                return $first;
            }

            // Prefer owned batch on edit, otherwise oldest (FIFO)
            $primary = $group->first(function ($b) { return $b['_owned_qty'] > 0; }) ?: $group->first();
            $primary['code'] = 'B' . str_pad($primary['id'], 6, '0', STR_PAD_LEFT) . ' (' . $group->count() . ' batches)';
            $primary['remain_qty'] = $group->sum('remain_qty');
            $primary['available_qty'] = $group->sum('available_qty');
            $primary['is_returned'] = $group->contains('is_returned', true);
            $primary['return_label'] = $primary['is_returned'] ? 'CONTAINS RETURNED ITEM' : null;
            $primary['units'] = $group->pluck('units')->flatten(1)->values()->toArray();
            unset($primary['_owned_qty']);
            return $primary;
        })->values();
    }
}
