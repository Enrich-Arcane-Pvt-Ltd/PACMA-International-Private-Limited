<?php

namespace App\Services;

use App\GrnProduct;
use App\Inventory;
use App\ProductWarrantySerial;
use Illuminate\Validation\ValidationException;

class SaleStockBatch
{
    public static function prepare(array $item)
    {
        $branch = auth()->user()->branchId();
        $batchId = $item['batch_id'] ?? ($item['inventory_id'] ?? null);
        $batchQuery = Inventory::where('product_id', $item['product'])
            ->where('status', 1)->where('remain_qty', '>', 0)->lockForUpdate();
        if (!empty($branch)) {
            $branchBatch = (clone $batchQuery)->where('branch_id', $branch);
            if ($branchBatch->exists()) {
                $batchQuery = $branchBatch;
            }
        }
        if (!empty($batchId)) {
            $batch = (clone $batchQuery)->find($batchId);
            if (!$batch) {
                $batch = Inventory::where('product_id', $item['product'])->where('status', 1)->where('remain_qty', '>', 0)->lockForUpdate()->find($batchId);
            }
        } else {
            $batch = $batchQuery->orderBy('id', 'asc')->first();
        }
        if (!$batch) self::invalid();

        $unit = null;
        if (!empty($item['serial_card_no'])) {
            $grnQuery = \App\Grn::query();
            if (!empty($branch)) {
                $grnQuery->where('branch_id', $branch);
            }
            $query = ProductWarrantySerial::where('serial_number', $item['serial_card_no'])->where('status', 0)
                ->whereIn('grn_product_id', GrnProduct::where('product_id', $item['product'])
                    ->whereIn('grn_id', $grnQuery->select('id'))->select('id'));
            if (!empty($item['product_warranty_serial_id'])) $query->where('id', $item['product_warranty_serial_id']);
            $units = $query->lockForUpdate()->get();
            if ($units->count() !== 1) {
                // Fallback if branch filter was too strict
                $fallbackQuery = ProductWarrantySerial::where('serial_number', $item['serial_card_no'])->where('status', 0)->lockForUpdate();
                if (!empty($item['product_warranty_serial_id'])) $fallbackQuery->where('id', $item['product_warranty_serial_id']);
                $units = $fallbackQuery->get();
            }
            if ($units->count() !== 1) self::invalid();
            $unit = $units->first();
            $unitBatch = GrnStockBatch::find(GrnProduct::findOrFail($unit->grn_product_id), true);
            if (!$batch || $unitBatch->id != $batch->id) self::invalid();
        }
        $quantity = (float) ($item['quantity'] ?? 0);
        if (!$batch || (int) $batch->status !== 1 || $quantity < 1 || floor($quantity) !== $quantity
            || $quantity > $batch->remain_qty) self::invalid();
        $item['batch_id'] = $batch->id;
        $item['inventory_id'] = $batch->id;
        $item['purchase_price'] = $batch->purchase_price;
        $item['available_quantity'] = $batch->remain_qty;
        if ($unit) {
            $item['product_warranty_serial_id'] = $unit->id;
            $item['warranty_document_path'] = $unit->warranty_document;
        }
        return $item;
    }

    private static function invalid()
    {
        throw ValidationException::withMessages(['product' => __('The selected stock batch or serial is unavailable. Select an available batch and quantity again.')]);
    }
}
