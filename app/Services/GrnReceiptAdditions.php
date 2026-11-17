<?php

namespace App\Services;

use App\Grn;
use App\GrnProduct;
use App\Inventory;
use App\Product;
use App\ProductWarrantySerial;
use App\StockTransaction;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GrnReceiptAdditions
{
    public function save(Request $request, $id)
    {
        abort_unless(auth()->user()->can('edit grn'), 403);
        $isAdmin = auth()->user()->canEditGrnCostAndQuantity();
        $uploaded = [];
        try {
            DB::transaction(function () use ($request, $id, &$uploaded, $isAdmin) {
                $grn = Grn::where('branch_id', auth()->user()->branchId())->lockForUpdate()->findOrFail($id);
                $lines = GrnProduct::where('grn_id', $id)->lockForUpdate()->get()->keyBy('id');
                $inventories = Inventory::where('grn_id', $id)->lockForUpdate()->get();
                $invoiced = $inventories->contains(function ($inventory) { return $inventory->grn_qty != $inventory->remain_qty; });
                abort_if($invoiced && !$isAdmin, 403, 'Only administrators can edit invoiced GRNs.');
                $previousVendor = $grn->vender_id;
                $previousDate = $grn->required_by;
                foreach (['vender_id', 'order_date', 'required_by', 'status'] as $field) {
                    if (!$request->has($field)) continue;
                    if ($invoiced) {
                        $this->check((string) $request->input($field) === (string) $grn->$field,
                            'After invoicing, supplier, dates and status must stay unchanged.');
                    } else $grn->$field = $request->input($field);
                }
                $warranties = json_decode($request->input('serial_warranty_data', '{}'), true) ?: [];
                GrnWarrantyRules::validate($request->items, $warranties, $lines);
                $seen = [];
                $seenKeys = [];
                $total = 0;
                foreach ($request->items as $item) {
                    $line = !empty($item['id']) ? $lines->get($item['id']) : null;
                    $this->check(empty($item['id']) || $line, 'Invalid GRN item.');
                    $key = GrnWarrantyRules::key($item);
                    $this->check(!in_array($key, $seenKeys), 'Duplicate stock batch key.');
                    $seenKeys[] = $key;
                    $previousAmount = $line ? (float) $line->received_qty * (float) $line->grn_cost : 0;
                    $previousProduct = $line ? $line->product_id : $item['item'];
                    if ($line) {
                        if (!$isAdmin) {
                            abort_if((float) $line->unit_cost !== (float) $item['unit_cost']
                                || (int) $line->received_qty !== (int) $item['quantity'], 403,
                                'Only administrators can change Unit Cost or Quantity.');
                        }
                        $this->check(!in_array($line->id, $seen), 'Duplicate GRN item.');
                        $seen[] = $line->id;
                        $inventory = GrnStockBatch::find($line, true);
                        $this->check((int) $inventory->grn_qty === (int) $line->received_qty, 'Stock quantity does not match the GRN. Please reconcile this batch first.');
                        $consumed = (int) $inventory->grn_qty - (int) $inventory->remain_qty;
                        $this->check($consumed === 0 || (int) $line->product_id === (int) $item['item'], 'A product with sold/transferred units cannot be replaced.');
                        $this->check((int) $item['receive_quantity'] >= $consumed && (int) $item['quantity'] === (int) $item['receive_quantity'],
                            'Quantity cannot be less than the number of sold/transferred units.');
                        $delta = (int) $item['receive_quantity'] - (int) $line->received_qty;
                    } else {
                        abort_unless($isAdmin, 403, 'Only administrators can add GRN items.');
                        $this->check(\Illuminate\Support\Facades\Schema::hasColumn('inventories', 'grn_product_id')
                            || !$lines->contains('product_id', $item['item']), 'Run the stock batch migration before adding another batch of this product.');
                        $line = new GrnProduct;
                        $line->grn_id = $id;
                        $line->product_id = $item['item'];
                        $inventory = new Inventory;
                        $inventory->grn_id = $id;
                        $inventory->product_id = $item['item'];
                        $inventory->branch_id = $grn->branch_id;
                        $inventory->grn_qty = 0;
                        $inventory->remain_qty = 0;
                        $inventory->date = $grn->order_date;
                        $inventory->purchase_price = $item['grn_cost'];
                        $inventory->sale_price = $item['sale_price'];
                        $inventory->hire_price = $item['hire_price'];
                        $inventory->status = 1;
                        $delta = (int) $item['receive_quantity'];
                    }
                    $oldUnits = $line->exists ? ProductWarrantySerial::where('grn_product_id', $line->id)->lockForUpdate()->get()->keyBy('id') : collect();
                    $units = $warranties[$key] ?? [];
                    $this->check(is_array($units), 'Invalid serial and warranty details.');
                    // Repeated saves can contain newly added serials without their new IDs.
                    // Match those to this row's existing units instead of replacing them.
                    foreach ($units as &$data) {
                        if (empty($data['id']) && !empty($data['serial'])) {
                            $match = $oldUnits->firstWhere('serial_number', $data['serial']);
                            if ($match) $data['id'] = $match->id;
                        }
                    }
                    unset($data);
                    if ($delta > 0) {
                        $this->check(count($units) === (int) $item['receive_quantity'], 'Add serial and warranty details for every received unit before saving.');
                    }
                    $submittedIds = collect($units)->pluck('id')->filter()->all();
                    foreach ($oldUnits as $old) {
                        if ((int) $old->status !== 0) $this->check(in_array($old->id, $submittedIds), 'Sold unit serial/warranty records must be retained.');
                    }
                    $newUnits = array_filter($units, function ($unit) { return empty($unit['id']); });
                    $this->check(count($newUnits) >= max($delta, 0) && count($units) === (int) $item['receive_quantity'],
                        'Serial/warranty entries must match received units.');
                    foreach (['unit_cost', 'grn_cost', 'sale_price', 'hire_price', 'quantity'] as $field) $line->$field = $item[$field];
                    $line->product_id = $item['item'];
                    $line->received_qty = $item['receive_quantity'];
                    $line->amount = $line->received_qty * $line->grn_cost;
                    $line->save();
                    if (\Illuminate\Support\Facades\Schema::hasColumn('inventories', 'grn_product_id')) $inventory->grn_product_id = $line->id;
                    $inventory->grn_qty += $delta;
                    $inventory->remain_qty += $delta;
                    $inventory->status = $inventory->remain_qty > 0 ? 1 : 0;
                    $inventory->supplier_invoice_number = $request->supplier_invoice_number;
                    $inventory->date = $grn->order_date;
                    $inventory->product_id = $item['item'];
                    $inventory->purchase_price = $item['grn_cost'];
                    $inventory->sale_price = $item['sale_price'];
                    $inventory->hire_price = $item['hire_price'];
                    $inventory->save();
                    $unitIds = [];
                    foreach ($units as $index => $data) {
                        $unit = !empty($data['id']) ? $oldUnits->get($data['id']) : null;
                        $this->check(empty($data['id']) || $unit, 'Serial record does not belong to this GRN item.');
                        if ($unit) {
                            $this->check(!in_array($unit->id, $unitIds), 'Duplicate unit record.');
                            $unitIds[] = $unit->id;
                        }
                        $file = $request->file("warranty_documents.{$key}.{$index}");
                        if ($unit && (int) $unit->status !== 0) {
                            $this->check((string) $unit->serial_number === (string) ($data['serial'] ?? '')
                                && (string) $unit->warranty_number === (string) ($data['warranty'] ?? '') && !$file,
                                'Serial and warranty details of sold units cannot be changed.');
                            continue;
                        }
                        if (!$unit) {
                            $this->check(!empty($data['serial']), 'Serial Number / Color is required for every new unit.');
                            $unit = new ProductWarrantySerial;
                            $unit->grn_product_id = $line->id;
                            $unit->status = 0;
                        }
                        $unit->serial_number = $data['serial'] ?? null;
                        $unit->warranty_number = $data['warranty'] ?? null;
                        $unit->warranty_period = isset($data['warranty_period']) && $data['warranty_period'] !== '' ? $data['warranty_period'] : null;
                        $unit->additional_warranty_description = $data['additional_warranty_description'] ?? null;
                        if ($file) {
                            $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('assets/documents/warranty'), $name);
                            $unit->warranty_document = 'assets/documents/warranty/' . $name;
                            $uploaded[] = public_path($unit->warranty_document);
                        }
                        $unit->save();
                        $unitIds[] = $unit->id;
                    }
                    ProductWarrantySerial::where('grn_product_id', $line->id)->where('status', 0)
                        ->whereNotIn('id', $unitIds)->delete();
                    StockTransaction::where('inventory_id', $inventory->id)->where('stock_master_id', 1)
                        ->where('status', 1)->update(['date' => $grn->order_date]);
                    if ($delta !== 0 || (int) $previousProduct !== (int) $line->product_id) {
                        $receipt = StockTransaction::where('inventory_id', $inventory->id)->where('stock_master_id', 1)->where('status', 1)->first() ?: new StockTransaction;
                        $receipt->stock_master_id = 1;
                        $receipt->inventory_id = $inventory->id;
                        $receipt->product_id = $line->product_id;
                        $receipt->quantity = $inventory->grn_qty;
                        $receipt->date = $grn->order_date;
                        $receipt->branch_id = $grn->branch_id;
                        $receipt->created_by = auth()->id();
                        $receipt->status = 1;
                        $receipt->save();
                    }
                    if ((int) $previousProduct !== (int) $line->product_id) {
                        $this->adjustAccounts($grn, $previousProduct, -$previousAmount);
                        $this->adjustAccounts($grn, $line->product_id, $line->amount);
                    } else $this->adjustAccounts($grn, $line->product_id, (float) $line->amount - $previousAmount);
                    $total += $line->amount;
                }
                foreach ($lines->except($seen) as $removed) {
                    abort_unless($isAdmin, 403, 'Only administrators can remove GRN items.');
                    $inventory = GrnStockBatch::find($removed, true);
                    $this->check((int) $inventory->grn_qty === (int) $inventory->remain_qty
                        && !ProductWarrantySerial::where('grn_product_id', $removed->id)->where('status', '!=', 0)->exists(),
                        'A product with sold/transferred units cannot be removed.');
                    StockTransaction::where('inventory_id', $inventory->id)->where('stock_master_id', 1)->update(['status' => 0]);
                    $this->adjustAccounts($grn, $removed->product_id, -(float) $removed->amount);
                    ProductWarrantySerial::where('grn_product_id', $removed->id)->delete();
                    $removed->delete();
                    $inventory->delete();
                }
                $grn->supplier_invoice_number = $request->supplier_invoice_number;
                if ((string) $previousVendor !== (string) $grn->vender_id || (string) $previousDate !== (string) $grn->required_by) {
                    \App\AccountTransaction::where('grn_id', $id)->where('status', 1)
                        ->update(['vender_id' => $grn->vender_id, 'date' => $grn->required_by]);
                }
                $grn->total_amount = $total;
                $grn->save();
            });
        } catch (\Throwable $error) {
            foreach ($uploaded as $path) if (is_file($path)) unlink($path);
            throw $error;
        }
    }

    private function check($condition, $message)
    {
        if (!$condition) throw ValidationException::withMessages(['items' => __($message)]);
    }

    private function adjustAccounts($grn, $productId, $difference)
    {
        if (abs($difference) < 0.005) return;
        $product = Product::with('categoryDetails')->findOrFail($productId);
        $this->check($product->categoryDetails && $product->categoryDetails->inventory_chart_of_account_id, 'Configure the product inventory account before updating stock.');
        $account = $product->categoryDetails->inventory_chart_of_account_id;
        Utility::accountGrnTransaction($grn->required_by, $account, 208, $grn->id, $productId, $grn->vender_id, 10, abs($difference), $difference > 0 ? 'debit' : 'credit');
        Utility::accountGrnTransaction($grn->required_by, 208, $account, $grn->id, $productId, $grn->vender_id, 10, abs($difference), $difference > 0 ? 'credit' : 'debit');
    }
}
