<?php

namespace App\Http\Controllers;

use App\ProductCategory;
use App\ProductSubCategory;
use App\Product;
use App\Sale;
use App\Bill;
use App\Invoice;
use App\ProductService;
use App\ProductServiceCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage product'))
        {
            $productCategories = ProductCategory::orderBy('name')->get();
            return view('productCategory.index', compact('productCategories'));
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
            return view('productCategory.create');
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
                                   'name' => 'required|max:1024',
                                   'down_payment' => 'required',
                                 ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $product_category = new ProductCategory();
            $product_category->product_category_id  = $this->productCategoryNumber();
            $product_category->name = $request->name;
            $product_category->down_payment = $request->down_payment;
            $product_category->status = 1;
            $product_category->created_by = \Auth::user()->id;

            $product_category->income_chart_of_account_id = 4;
            $product_category->inventory_chart_of_account_id = 8;
            $product_category->inventory_loan_chart_of_account_id = 10;
            $product_category->dnl_charges_chart_of_account_id = 11;
            $product_category->inventory_loan_control_chart_of_account_id = 12;
            $product_category->installment_collection_control_chart_of_account_id = 13;
            $product_category->interest_income_control_chart_of_account_id = 14;
            $product_category->save();

            return redirect()->route('product-category.index')->with('success', __('Product category successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function productCategoryNumber()
    {
    $latest = ProductCategory::latest()->first();

    if(!$latest)
    {
        return 1;
    }
        return $latest->product_category_id + 1;
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit product'))
        {
            $productCategory = ProductCategory::find($id);
            return view('productCategory.edit', compact('productCategory'));
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
            $productCategory = ProductCategory::find($id);
            // if($productCategory->created_by == \Auth::user()->creatorId())
            // {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:1024',
                                       'down_payment' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $productCategory->name  = $request->name;
                $productCategory->down_payment  = $request->down_payment;
                $productCategory->save();

                return redirect()->route('product-category.index')->with('success', __('Product category successfully updated !!!'));
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
            $productCategory = ProductCategory::find($id);
            // if($productCategory->created_by == \Auth::user()->creatorId())
            // {
                if($productCategory->type == 0)
                {
                    $productCategory = ProductSubCategory::where('product_category_id', $productCategory->id)->first();
                }
                else
                {
                    $productCategory = Product::where('product_category_id', $productCategory->id)->first();
                }

                if(!empty($productCategory))
                {
                    return redirect()->back()->with('error', __('This category is already assigned. So please remove this category related data.'));
                }
                else
                {
                    $productCategory = ProductCategory::find($id);
                    $productCategory->status = 0;
                    $productCategory->update();
                    return redirect()->back()->with('success', __('Product category successfully blocked !!!'));
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
            $productCategory = ProductCategory::find($id);
            $productCategory->status = 1;
            $productCategory->update();
            return redirect()->back()->with('success', __('Product category successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($id)
    {
        if (!\Auth::user()->canDeleteProductCategory()) {
            $message = __('Only administrators can delete categories. Please contact an administrator.');
            return request()->expectsJson() ? response()->json(['message' => $message], 403)
                : redirect()->route('product-category.index')->with('error', $message);
        }
        if (\Auth::user()->can('delete product') || \Auth::user()->can('delete product category') || \Auth::user()->can('edit product') || \Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $category = ProductCategory::find($id);
            if (!$category) {
                return redirect()->back()->with('error', __('Category not found.'));
            }

            // Unlink all products assigned to this category
            Product::where('product_category_id', $id)->update([
                'product_category_id' => 0,
                'product_sub_category_id' => 0
            ]);
            ProductService::where('product_category_id', $id)->update([
                'product_category_id' => null,
                'product_sub_category_id' => null
            ]);

            // Unlink products belonging to child sub categories and delete child sub categories
            $subCategoryIds = ProductSubCategory::where('product_category_id', $id)->pluck('id');
            if ($subCategoryIds->count() > 0) {
                Product::whereIn('product_sub_category_id', $subCategoryIds)->update(['product_sub_category_id' => 0]);
                ProductService::whereIn('product_sub_category_id', $subCategoryIds)->update(['product_sub_category_id' => null]);
                ProductSubCategory::whereIn('id', $subCategoryIds)->delete();
            }

            $category->delete();

            return redirect()->route('product-category.index')->with('success', __('Product Category successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
