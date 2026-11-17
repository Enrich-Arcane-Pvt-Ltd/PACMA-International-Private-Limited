<?php

namespace App\Http\Controllers;

use App\ProductCategory;
use App\ProductSubCategory;
use App\Product;
use App\Bill;
use App\Invoice;
use App\ProductService;
use App\ProductServiceCategory;
use Illuminate\Http\Request;

class ProductSubCategoryController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage product'))
        {
            $productSubCategories = ProductSubCategory::with('productCategory')
                                                        ->orderBy('name')
                                                        ->get();
            return view('productSubCategory.index', compact('productSubCategories'));
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
            $productCategory = ProductCategory::where('status',1)->get()->pluck('name', 'id');
            return view('productSubCategory.create', compact('productCategory'));
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
                                   'product_category' => 'required',
                                   'name' => 'required|max:1024',
                                 ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $product_sub_category     = new ProductSubCategory();
            $product_sub_category->product_sub_category_id  = $this->productSubCategoryNumber();
            $product_sub_category->name         = $request->name;
            $product_sub_category->status       = 1;
            $product_sub_category->product_category_id = $request->product_category;
            $product_sub_category->created_by = \Auth::user()->id;
            $product_sub_category->save();

            return redirect()->route('product-sub-category.index')->with('success', __('Product Sub Category successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function productSubCategoryNumber()
    {
        $highest = 0;
        foreach (ProductSubCategory::pluck('product_sub_category_id') as $number) {
            // Older databases may contain prefixed codes as well as plain numbers.
            if (preg_match('/(\d+)$/', trim((string) $number), $matches)) {
                $highest = max($highest, (int) $matches[1]);
            }
        }

        return $highest + 1;
    }



    public function edit($id)
    {
        if(\Auth::user()->can('edit product'))
        {
            $productCategory = ProductCategory::where('status', 1)->get()->pluck('name', 'id');
            $productSubCategory = ProductSubCategory::find($id);
            return view('productSubCategory.edit', compact('productCategory','productSubCategory'));
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
            $productSubCategory = ProductSubCategory::find($id);
            // if($productSubCategory->created_by == \Auth::user()->creatorId())
            // {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:1024',

                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $productSubCategory->product_category_id  = $request->product_category_id;
                $productSubCategory->name  = $request->name;
                $productSubCategory->save();

                return redirect()->route('product-sub-category.index')->with('success', __('Sub Category successfully updated !!!'));
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
            $productSubCategory = ProductSubCategory::find($id);
            // if($productSubCategory->created_by == \Auth::user()->creatorId())
            // {
                if($productSubCategory->type == 0)
                {
                    $productSubCategory = Product::where('product_sub_category_id', $productSubCategory->id)->first();
                }

                if(!empty($productSubCategory))
                {
                    return redirect()->back()->with('error', __('This sub category is already assigned. So please remove this sub category related data.'));
                }
                else
                {
                    $productSubCategory = ProductSubCategory::find($id);
                    $productSubCategory->status = 0;
                    $productSubCategory->update();
                    return redirect()->back()->with('success', __('Product sub category successfully blocked !!!'));
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
            $productSubCategory = ProductSubCategory::find($id);
            $productSubCategory->status = 1;
            $productSubCategory->update();
            return redirect()->back()->with('success', __('Product sub category successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function destroy($id)
    {
        if (!\Auth::user()->canDeleteProductCategory(true)) {
            $message = __('Only administrators can delete sub categories. Please contact an administrator.');
            return request()->expectsJson() ? response()->json(['message' => $message], 403)
                : redirect()->route('product-sub-category.index')->with('error', $message);
        }
        if (\Auth::user()->can('delete product') || \Auth::user()->can('delete product sub category') || \Auth::user()->can('edit product') || \Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $subCategory = ProductSubCategory::find($id);
            if (!$subCategory) {
                return redirect()->back()->with('error', __('Sub category not found.'));
            }

            // Unlink all products assigned to this sub category
            Product::where('product_sub_category_id', $id)->update(['product_sub_category_id' => 0]);
            ProductService::where('product_sub_category_id', $id)->update(['product_sub_category_id' => null]);

            $subCategory->delete();

            return redirect()->route('product-sub-category.index')->with('success', __('Product Sub Category successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
