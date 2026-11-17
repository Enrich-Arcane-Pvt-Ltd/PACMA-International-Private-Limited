<?php

namespace App\Services;

use App\Inventory;
use App\ProductWarrantySerial;
use App\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SaleBatchEdit
{
    // Caller runs this inside the sale update transaction, before changing financial records.
    public static function prepare(Request $request, $sale, $cancel = false)
    {
        $rows = SaleBatchCatalog::ownedRows($sale);
        $oldUnits = $rows->pluck('product_warranty_id')->filter();
        if ((int) $sale->modal_type !== 2) {
            $oldUnits = \App\HirePurchaseDetail::where('sale_id', $sale->id)->pluck('product_warranty_id')->filter();
        }
        $previous = $rows->groupBy('inventory_id')->map(function ($group) { return $group->sum('quantity'); });
        $inventories = Inventory::where('branch_id', auth()->user()->branchId())->whereIn('id', $previous->keys())
            ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
        foreach ($previous as $id => $quantity) {
            if (!$inventories->has($id) || $quantity <= 0) self::invalid();
            $batch = $inventories[$id];
            if ($batch->remain_qty + $quantity > $batch->grn_qty) self::invalid();
            // Restore the saved allocation in this transaction; never trust posted pre_qty/pre_inventory_id.
            $batch->remain_qty += $quantity;
            $batch->status = 1;
            $batch->save();
        }
        ProductWarrantySerial::whereIn('id', $oldUnits)->where('status', 1)->update(['status' => 0]);
        $posted = $cancel ? [] : ((int) $sale->modal_type === 2 ? $request->items : [$request->all()]);
        if (!is_array($posted) || (!$cancel && !$posted)) self::invalid();
        $prepared = [];
        $selectedUnits = [];
        $next = [];
        foreach ($posted as $item) {
            $item = SaleStockBatch::prepare($item);
            $id = $item['inventory_id'];
            $saved = (int) $sale->modal_type === 2 ? $rows->firstWhere('id', $item['sale_item_id'] ?? null) : $sale;
            if ($saved && (int) $saved->inventory_id === (int) $id && (int) $saved->product_id === (int) $item['product']) {
                $item['purchase_price'] = $saved->purchase_price;
            }
            $next[$id] = ($next[$id] ?? 0) + $item['quantity'];
            if ($next[$id] > $item['available_quantity']) self::invalid();
            if (!empty($item['product_warranty_serial_id'])) {
                if (in_array($item['product_warranty_serial_id'], $selectedUnits)) self::invalid();
                $selectedUnits[] = $item['product_warranty_serial_id'];
            }
            $prepared[] = $item;
        }
        $old = $previous->all(); ksort($old); ksort($next);
        $same = count($old) === count($next);
        foreach ($old as $id => $quantity) if (!isset($next[$id]) || (float) $next[$id] !== (float) $quantity) $same = false;
        $oldShape = $rows->map(function ($row) { return $row->inventory_id . ':' . $row->quantity; })->sort()->values()->all();
        $newShape = collect($prepared)->map(function ($item) { return $item['inventory_id'] . ':' . $item['quantity']; })->sort()->values()->all();
        $same = $same && $oldShape === $newShape;
        if (!$same || (string) $sale->date_of_sale !== (string) $request->date_of_sale) {
            $matches = collect();
            // Old records have no sale foreign key on stock movements. Refuse ambiguous matches.
            $groups = $rows->groupBy(function ($row) { return $row->inventory_id . ':' . $row->quantity; });
            foreach ($groups as $group) {
                $row = $group->first();
                $movements = StockTransaction::where('inventory_id', $row->inventory_id)->where('stock_master_id', 2)
                    ->where('status', 1)->where('quantity', $row->quantity)->whereDate('date', $sale->date_of_sale)->lockForUpdate()->get();
                if ($movements->count() !== $group->count()) self::invalid();
                $matches = $matches->merge($movements);
            }
            StockTransaction::whereIn('id', $matches->pluck('id'))->update(['status' => 0]);
            foreach ($prepared as $item) {
                $movement = new StockTransaction;
                $movement->stock_master_id = 2; $movement->inventory_id = $item['inventory_id'];
                $movement->product_id = $item['product']; $movement->quantity = $item['quantity'];
                $movement->date = $request->date_of_sale; $movement->branch_id = $sale->branch_id;
                $movement->created_by = auth()->id(); $movement->status = 1; $movement->save();
            }
        }
        foreach ($next as $id => $quantity) {
            $batch = Inventory::where('branch_id', $sale->branch_id)->lockForUpdate()->findOrFail($id);
            $batch->remain_qty -= $quantity;
            $batch->status = $batch->remain_qty > 0 ? 1 : 0;
            $batch->save();
        }
        if ($cancel) return;
        if ((int) $sale->modal_type === 2) $request->merge(['items' => $prepared]);
        else $request->merge($prepared[0]);
    }

    private static function invalid()
    {
        throw ValidationException::withMessages(['batch_id' => __('Cannot safely update this stock allocation. Check the selected batch and quantity; older movements may require reconciliation.')]);
    }
}
