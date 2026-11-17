<?php

namespace App\Services;

use App\AccountTransaction;
use App\Grn;
use App\GrnProduct;
use App\Inventory;
use App\ProductWarrantySerial;
use App\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GrnDeletion
{
    public function delete($id)
    {
        abort_unless(auth()->user()->canDeleteGrn(), 403);
        DB::transaction(function () use ($id) {
            $grnQuery = Grn::query();
            $branchId = auth()->user()->branchId();
            if (!empty($branchId)) {
                $grnQuery->where('branch_id', $branchId);
            }
            $grn = $grnQuery->lockForUpdate()->findOrFail($id);
            $inventory = Inventory::where('grn_id', $id)->lockForUpdate()->get();
            $lines = GrnProduct::where('grn_id', $id)->lockForUpdate()->get();
            $units = ProductWarrantySerial::whereIn('grn_product_id', $lines->pluck('id'))->lockForUpdate()->get();
            if ($inventory->contains(function ($row) { return $row->grn_qty != $row->remain_qty; })
                || $units->contains(function ($unit) { return (int) $unit->status !== 0; })) {
                throw ValidationException::withMessages(['grn' => __('Cannot delete this GRN because units have already been sold or transferred.')]);
            }
            StockTransaction::whereIn('inventory_id', $inventory->pluck('id'))->where('status', 1)->update(['status' => 0]);
            AccountTransaction::where('grn_id', $id)->where('status', 1)->update(['status' => 0]);
            // Preserve referenced IDs and warranty documents for audit/history.
            Inventory::whereIn('id', $inventory->pluck('id'))->update(['status' => 0, 'remain_qty' => 0]);
            ProductWarrantySerial::whereIn('id', $units->pluck('id'))->update(['status' => 2]);
            GrnProduct::whereIn('id', $lines->pluck('id'))->delete();
            $grn->delete();
        });
    }
}
