<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductPricesController extends Controller
{
    public function index(Request $request)
    {
        if(\Auth::user()->can('manage stock')) {
            $products = Product::orderBy('name')->get();

            return view('product_prices.index', compact('products'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
