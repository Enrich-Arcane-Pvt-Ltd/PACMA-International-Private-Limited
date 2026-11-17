<?php

namespace App\Services;

use App\Inventory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class GrnStockBatch
{
    public static function find($line, $lock = false)
    {
        $query = Inventory::where('grn_id', $line->grn_id)->where('product_id', $line->product_id);
        $query->where('branch_id', \App\Grn::withTrashed()->where('id', $line->grn_id)->value('branch_id'));
        if ($lock) $query->lockForUpdate();
        $linked = Schema::hasColumn('inventories', 'grn_product_id');
        if ($linked) {
            $batch = (clone $query)->where('grn_product_id', $line->id)->first();
            if ($batch) return $batch;
            $query->whereNull('grn_product_id');
        }
        $matches = $query->get();
        if ($matches->count() > 1) {
            $matches = $matches->filter(function ($row) use ($line) {
                return (int) $row->grn_qty === (int) $line->received_qty
                    && abs((float) $row->purchase_price - (float) $line->grn_cost) < 0.005
                    && abs((float) $row->sale_price - (float) $line->sale_price) < 0.005
                    && abs((float) $row->hire_price - (float) $line->hire_price) < 0.005;
            });
        }
        if ($matches->count() !== 1) {
            throw ValidationException::withMessages(['items' => __('Cannot identify this GRN stock batch. Please reconcile its inventory records.')]);
        }
        $batch = $matches->first();
        if ($linked && $lock) {
            $batch->grn_product_id = $line->id;
            $batch->save();
        }
        return $batch;
    }
}
