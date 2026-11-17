<?php

namespace App\Services;

use App\Product;
use App\ProductWarrantySerial;
use Illuminate\Validation\ValidationException;

class GrnWarrantyRules
{
    public static function key($item)
    {
        return !empty($item['batch_key']) ? $item['batch_key'] : 'product_' . $item['item'];
    }
    public static function validate($items, $payload, $existingLines)
    {
        $products = Product::with('categoryDetails')->whereIn('id', collect($items)->pluck('item'))->get()->keyBy('id');
        $existingUnits = ProductWarrantySerial::whereIn('grn_product_id', $existingLines->keys())->get()->keyBy('id');
        foreach ($items as $item) {
            $product = $products->get($item['item']);
            $furniture = $product && preg_match('/furniture/i', optional($product->categoryDetails)->name ?? '');
            $units = $payload[self::key($item)] ?? [];
            if (!is_array($units) || count($units) !== (int) $item['receive_quantity']) {
                throw ValidationException::withMessages(['items' => __('Add serial and warranty details for every received unit.')]);
            }
            foreach ($units as $index => $unit) {
                $old = $existingUnits->get($unit['id'] ?? null);
                if ($old && $old->grn_product_id == ($item['id'] ?? null) && (int) $old->status !== 0) continue;
                if (trim((string) ($unit['serial'] ?? '')) === '') {
                    throw ValidationException::withMessages(['items' => __('Serial Number / Color is required for every unit.')]);
                }
                if (!$furniture && trim((string) ($unit['warranty'] ?? '')) === '') {
                    throw ValidationException::withMessages(['items' => __('Warranty Card Number is required for non-furniture items.')]);
                }
            }
        }
    }
}
