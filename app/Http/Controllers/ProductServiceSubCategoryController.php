<?php

namespace App\Http\Controllers;

use App\Bill;
use App\Invoice;
use App\ProductService;
use App\ProductServiceCategory;
use Illuminate\Http\Request;

class ProductServiceSubCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductServiceCategory::get();

        return view('ProductServiceSubCategory.index', ['categories'=>$categories]);
       
    }


}
