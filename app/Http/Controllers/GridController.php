<?php

namespace App\Http\Controllers;

use App\CustomField;
use App\ProductService;
use App\ProductServiceCategory;
use App\ProductServiceUnit;
use App\Tax;
use Illuminate\Http\Request;

class GridController extends Controller
{
    public function grid(Request $request)
    {
        if(\Auth::user()->can('manage product & service'))
        {
            $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 0)->get()->pluck('name', 'id');
            if(!empty($request->category))
            {

                $productServices = ProductService::where('created_by', '=', \Auth::user()->creatorId())->where('category_id', $request->category)->get();
            }
            else
            {
                $productServices = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get();
            }

            return view('productservice.grid', compact('productServices', 'category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
       
    }

    public function detail($id)
    {
        if(\Auth::user()->can('manage product & service'))
        {
            $productServices = ProductService::find($id);

            return view('productservice.detail', compact('productServices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
       
    }
}