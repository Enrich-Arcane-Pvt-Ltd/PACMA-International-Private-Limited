<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('create sale')) {
            $branchId = \Auth::user()->branchId();

            $products = Product::with(['categoryDetails', 'subCategoryDetails', 'brands'])
                ->with(['inventory' => function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                        ->orderBy('date', 'asc');
                }])
                ->get();

            foreach ($products as $product) {
                $inventories = $product->inventory;
                // dd($inventories);
                if ($inventories->isNotEmpty()) {
                    $oldestInventory = $inventories->sortBy('date')->first(); // Oldest by date
                    $newestInventory = $inventories->sortByDesc('date')->first(); // Newest by date

                    // Check if the oldest inventory has remain_qty > 0
                    $product->sale_price = $oldestInventory->remain_qty > 0
                        ? $oldestInventory->sale_price
                        : $newestInventory->sale_price;
                    $product->hire_price = $oldestInventory->remain_qty > 0
                        ? $oldestInventory->hire_price
                        : $newestInventory->hire_price;
                    $product->discount_price = $product->hire_price ? ($product->hire_price - $product->sale_price) : null;
                } else {
                    // No inventories, use the latest inventory's sale_price
                    $latestInventory = $product->inventory->sortByDesc('date')->first();
                    $product->sale_price = $latestInventory ? $latestInventory->sale_price : null;
                    $product->hire_price = $latestInventory ? $latestInventory->hire_price : null;
                    $product->discount_price = $product->hire_price ? ($product->hire_price - $product->sale_price) : null;
                }
            }

            $currencySymbol = $this->currencySymbol;

            return view('priceList.index', compact('products', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
