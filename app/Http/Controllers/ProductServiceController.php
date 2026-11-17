<?php

namespace App\Http\Controllers;


use App\Warehouse;
use App\ProductCategory;
use App\ProductSubCategory;
use App\CustomField;
use App\ProductService;
use App\ProductServiceCategory;
use App\ProductServiceUnit;
use App\Tax;
use Illuminate\Http\Request;

class ProductServiceController extends Controller
{
    public function index(Request $request) {
        if(\Auth::user()->can('manage product')) {
            $productServices = ProductService::with('categoryDetails', 'subCategoryDetails', 'warehouseDetails')
                                             ->where('created_by', '=', \Auth::user()->creatorId())
                                             ->get();

            return view('productservice.index', compact('productServices'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create() {
        if(\Auth::user()->can('create product & service')) {
            $category     = ProductCategory::where('created_by', '=', \Auth::user()->creatorId())->where('status', '=', 1)->get()->pluck('name', 'id');

            $productSubCategories  = ProductSubCategory::where('created_by', '=', \Auth::user()->creatorId())->where('status', '=', 1)->get()->pluck('name', 'id');
            
            $warehouse = Warehouse::where('status', '=', 1)->get()->pluck('name', 'id');

            $maxId = ProductService::max('id');
            $productCode = "PRO".str_pad(($maxId+100001), 6, "0", STR_PAD_LEFT);

            return view('productservice.create', compact('category', 'productSubCategories', 'warehouse', 'productCode'));

        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request) {
        if(\Auth::user()->can('create product & service')) {
            $rules = [
                'name' => 'required',
                'sale_price' => 'required|numeric',
                'purchase_price' => 'required|numeric',
                'quantity' => 'required|numeric',
                'product_image_1' => 'required',
                'product_image_2' => 'required',
                'product_image_3' => 'required',
                'warranty_period' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('productservice.index')->with('error', $messages->first());
            }

            if($request->has('product_image_1')){
                $productImagePath_1 = self::uploadProductImage_1($request);
            } else {
                $productImagePath_1 = "assets/img/products/default.png";
            }

            if($request->has('product_image_2')){
                $productImagePath_2 = self::uploadProductImage_2($request);
            } else {
                $productImagePath_2 = "assets/img/products/default.png";
            }

            if($request->has('product_image_3')){
                $productImagePath_3 = self::uploadProductImage_3($request);
            } else {
                $productImagePath_3 = "assets/img/products/default.png";
            }
            if($request->has('product_document')){
                $productdocument = self::uploadProductdocument($request);
            } else {
                $productdocument = "assets/img/products/default.png";
            }

            $productService  = new ProductService();
            $productService->name = $request->name;
            $productService->product_id = $request->product_id;
            $productService->barcode = $request->barcode;
            $productService->sale_price = $request->sale_price;
            $productService->purchase_price = $request->purchase_price;
            $productService->quantity = $request->quantity;
            $productService->ware_house_id = $request->warehouse;
            $productService->image_1 = $productImagePath_1;
            $productService->image_2 = $productImagePath_2;
            $productService->image_3 = $productImagePath_3;
            $productService->warranty_period = $request->warranty_period;
            $productService->warranty_doc = $productdocument;
            $productService->description = $request->description;
            $productService->product_category_id = $request->category;
            $productService->product_sub_category_id = $request->sub_category;
            $productService->status = 1;
            $productService->created_by = \Auth::user()->creatorId();
            $productService->save();

            return redirect()->route('productservice.index')->with('success', __('Product successfully created.'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function uploadProductImage_1(Request $request) {
        $file = $request->file('product_image_1');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    public function uploadProductImage_2(Request $request) {
        $file = $request->file('product_image_2');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    public function uploadProductImage_3(Request $request) {
        $file = $request->file('product_image_3');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    public function uploadProductdocument(Request $request) {
        $file = $request->file('product_document');

        $destinationPath = 'assets/documents/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    public function show($id) { 
        $productService = ProductService::find($id);
        if(\Auth::user()->can('edit product & service')) {
            if($productService->created_by == \Auth::user()->creatorId()) {
                $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 0)->get()->pluck('name', 'id');

                $unit     = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

                $tax      = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

                $productService->customField = CustomField::getData($productService, 'product');

                $customFields                = CustomField::where('module', '=', 'product')->get();

                $productService->tax_id      = explode(',', $productService->tax_id);

                return view('productservice.show', compact('category', 'unit', 'tax', 'productService', 'customFields'));

            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }

        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function edit($id) {
        $productService = ProductService::find($id);

        if(\Auth::user()->can('edit product & service')) {
            if($productService->created_by == \Auth::user()->creatorId()) {
                $category = ProductCategory::where('created_by', '=', \Auth::user()->creatorId())->where('status', 1)->get()->pluck('name', 'id');

                $subcategory = ProductSubCategory::where('created_by', '=', \Auth::user()->creatorId())->where('status', 1)->get()->pluck('name', 'id');

                $warehouse = Warehouse::where('status', '=', 1)->get()->pluck('name', 'id');

                return view('productservice.edit', compact('category', 'productService','subcategory','warehouse'));

            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }

        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id) {
        $productService = ProductService::find($id);
        if(\Auth::user()->can('edit product & service')) {
            if($productService->created_by == \Auth::user()->creatorId()) {
                $rules = [
                    'name' => 'required',
                    'sale_price' => 'required|numeric',
                    'purchase_price' => 'required|numeric',
                    'quantity' => 'required|numeric',
                    'warranty_period' => 'required',
                ];

                $validator = \Validator::make($request->all(), $rules);

                if($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('productservice.index')->with('error', $messages->first());
                }
                
                if($request->has('product_image_1')){
                    $productImagePath_1 = self::updateProductImage_1($request);
                } else {
                    $productImagePath_1 = "assets/img/products/default.png";
                }

                if($request->has('product_image_2')){
                    $productImagePath_2 = self::updateProductImage_2($request);
                } else {
                    $productImagePath_2 = "assets/img/products/default.png";
                }

                if($request->has('product_image_3')){
                    $productImagePath_3 = self::updateProductImage_3($request);
                } else {
                    $productImagePath_3 = "assets/img/products/default.png";
                }

                if($request->has('product_document')){
                    $productdocument = self::updateProductdocument($request);
                } else {
                    $productdocument = "assets/img/products/default.png";
                }

                $productService = ProductService::find($id);
                $productService->name = $request->name;
                $productService->description = $request->description;
                $productService->sale_price = $request->sale_price;
                $productService->purchase_price = $request->purchase_price;
                $productService->product_category_id  = $request->product_category_id;
                $productService->product_sub_category_id = $request->product_sub_category_id;
                $productService->quantity = $request->quantity;
                $productService->ware_house_id = $request->ware_house_id;
                $productService->warranty_period = $request->warranty_period;
                $productService->created_by = \Auth::user()->creatorId();
                $productService->save();

                return redirect()->route('productservice.index')->with('success', __('Product successfully updated.'));

            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateProductImage_1(Request $request) {
        $file = $request->file('product_image_1');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    public function updateProductImage_2(Request $request) {
        $file = $request->file('product_image_2');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    public function updateProductImage_3(Request $request) {
        $file = $request->file('product_image_3');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    public function updateProductdocument(Request $request) {
        $file = $request->file('product_document');

        $destinationPath = 'assets/documents/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }
    
    public function destroy($id) {
        if(\Auth::user()->can('delete product & service')) {
            $productService = ProductService::find($id);
            if($productService->created_by == \Auth::user()->creatorId()) {
                $productService->delete();
                
                return redirect()->route('productservice.index')->with('success', __('Product successfully deleted.'));

            } else {   
                $productService->status = 0;
                $productService->update();

                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function unblock($id){
        $bankAccount = ProductService::find($id);
        $bankAccount->status = 1;
        $bankAccount->update();

        return redirect()->route('productservice.index')->with('success', __('Product successfully blocked.'));   
    }

    public function unblocktwo($id){
        $bankAccount = ProductService::find($id);
        $bankAccount->status = 0;
        $bankAccount->update();

        return redirect()->route('productservice.index')->with('success', __('Product successfully unblocked.')); 
    }
}
