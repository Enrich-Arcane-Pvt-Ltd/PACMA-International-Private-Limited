<?php

namespace App\Http\Controllers;

use App\ProductBrand;
use App\Product;
use App\Sale;
use App\Bill;
use App\Invoice;
use App\ProductCategory;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage product'))
        {
            $productBrands = ProductBrand::orderBy('name')->get();
            return view('productBrand.index', compact('productBrands'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create product'))
        {
            return view('productBrand.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create product'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                 ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $product_brand   = new ProductBrand();
            $product_brand->product_brand_id  = $this->productBrandNumber();
            $product_brand->name         = $request->name;
            $product_brand->status       = 1;
            $product_brand->created_by = \Auth::user()->id;
            $product_brand->save();

            return redirect()->route('product-brand.index')->with('success', __('Product model successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function productBrandNumber()
    {
    $latest = ProductBrand::latest()->first();

    if(!$latest)
    {
        return 1;
    }
        return $latest->product_brand_id + 1;
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit product'))
        {
            $productBrand = ProductBrand::find($id);
            return view('productBrand.edit', compact('productBrand'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit product'))
        {
            $productBrand = ProductBrand::find($id);
            // if($productBrand->created_by == \Auth::user()->creatorId())
            // {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $productBrand->name  = $request->name;
                $productBrand->save();

                return redirect()->route('product-brand.index')->with('success', __('Product model successfully updated !!!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function block($id)
    {
        if(\Auth::user()->can('block product'))
        {
            $productBrand = ProductBrand::find($id);
            // if($productBrand->created_by == \Auth::user()->creatorId())
            // {
                if($productBrand->type == 0)
                {
                    $productBrand = Product::where('product_category_id', $productBrand->id)->first();
                }
                else
                {
                    $productBrand = Sale::where('product_category_id', $productBrand->id)->first();
                }

                if(!empty($productBrand))
                {
                    return redirect()->back()->with('error', __('This model is already assigned. So please remove this model related data.'));
                }
                else
                {
                    $productBrand = ProductBrand::find($id);
                    $productBrand->status = 0;
                    $productBrand->update();
                    return redirect()->back()->with('success', __('Product model successfully blocked !!!'));
                }
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block product'))
        {
            $productBrand = ProductBrand::find($id);
            $productBrand->status = 1;
            $productBrand->update();
            return redirect()->back()->with('success', __('Product model successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($id)
    {
        if(\Auth::user()->can('delete product'))
        {
            $category = ProductCategory::find($id);
            if($category->created_by == \Auth::user()->creatorId())
            {

                if($category->type == 0)
                {
                    $categories = ProductCategory::where('product_category_id', $category->id)->first();
                }
                elseif($category->type == 1)
                {
                    $categories = Invoice::where('category_id', $category->id)->first();
                }
                else
                {
                    $categories = Bill::where('category_id', $category->id)->first();
                }

                if(!empty($categories))
                {
                    return redirect()->back()->with('error', __('this category is already assign so please move or remove this category related data.'));
                }

                $category->delete();

                return redirect()->route('product-category.index')->with('success', __('Product Category successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
