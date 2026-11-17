<?php

namespace App\Http\Controllers;

use App\ProductBrand;
use App\ProductCategory;
use App\ProductSubCategory;
use App\Product;
use App\Inventory;
use App\Sale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (\Auth::user()->can('manage product')) {
            $products = Product::with('productBrand', 'productCategory', 'productSubCategory')
                ->orderBy('name')
                ->get();

            return view('product.index', compact('products'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create product')) {
            $productBrand = ProductBrand::where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $productCategory = ProductCategory::where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $productSubCategory = ProductSubCategory::where('status', 1)->orderBy('name')->get(['id', 'name', 'product_category_id']);

            $maxId = Product::max('product_id');
            $productCode = "PROD" . str_pad(($maxId + 1), 5, "0", STR_PAD_LEFT);

            return view('product.create', compact('productBrand', 'productCategory', 'productSubCategory', 'productCode'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create product')) {
            $rules = [
                'product_brand' => 'required',
                'product_category' => ['required', 'integer', Rule::exists('product_categories', 'id')->where('status', 1)],
                'product_sub_category' => [
                    'required',
                    'integer',
                    Rule::exists('product_sub_categories', 'id')->where(function ($query) use ($request) {
                        $query->where('product_category_id', $request->input('product_category'))
                            ->where('status', 1);
                    }),
                ],
                'name' => 'required',
                'barcode' => 'required|unique:products',
                // 'sale_price' => 'required|numeric',
                // 'purchase_price' => 'required|numeric',
                'description' => 'required',
                // 'product_image_1' => 'required',
                // 'product_image_2' => 'required',
                // 'product_image_3' => 'required',
                'warranty_period' => 'nullable|numeric',
            ];

            if (empty($request->barcode)) {
                $maxId = Product::max('product_id');
                $request->merge(['barcode' => 'PROD' . str_pad(($maxId + 1), 5, '0', STR_PAD_LEFT)]);
            }

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('product.index')->with('error', $messages->first());
            }

            if ($request->has('product_image_1')) {
                $productImagePath_1 = self::uploadProductImage_1($request);
            } else {
                $productImagePath_1 = "assets/img/products/default.png";
            }

            if ($request->has('product_image_2')) {
                $productImagePath_2 = self::uploadProductImage_2($request);
            } else {
                $productImagePath_2 = "assets/img/products/default.png";
            }

            if ($request->has('product_image_3')) {
                $productImagePath_3 = self::uploadProductImage_3($request);
            } else {
                $productImagePath_3 = "assets/img/products/default.png";
            }

            if ($request->hasFile('product_document')) {
                $productdocument = self::uploadProductdocument($request);
            } else {
                $productdocument = "assets/img/products/default.png";
            }

            $product = new Product();
            $product->product_id = $this->productNumber();
            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->serial_number = $request->serial_number;
            // $product->sale_price = $request->sale_price;
            // $product->purchase_price = $request->purchase_price;
            $product->description = $request->description;
            $product->image_1 = $productImagePath_1;
            $product->image_2 = $productImagePath_2;
            $product->image_3 = $productImagePath_3;
            $product->warranty_period = $request->input('warranty_period') ?? 0;
            $product->warranty_document = $productdocument;
            $product->additional_warranty_description = $request->additional_warranty_description;
            $product->product_brand_id = $request->product_brand;
            $product->product_category_id = $request->product_category;
            $product->product_sub_category_id = $request->product_sub_category;
            // $product->product_color = $request->product_color;
            $product->status = 1;
            $product->created_by = \Auth::user()->id;
            $product->save();

            return redirect()->route('product.index')->with('success', __('Product successfully created !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function uploadProductImage_1(Request $request)
    {
        $file = $request->file('product_image_1');
        $destinationPath = 'assets/img/products';
        if (!file_exists(public_path($destinationPath))) {
            mkdir(public_path($destinationPath), 0777, true);
        }

        $fileName = time() . '_1_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($destinationPath), $fileName);

        return $destinationPath . '/' . $fileName;
    }

    public function uploadProductImage_2(Request $request)
    {
        $file = $request->file('product_image_2');
        $destinationPath = 'assets/img/products';
        if (!file_exists(public_path($destinationPath))) {
            mkdir(public_path($destinationPath), 0777, true);
        }

        $fileName = time() . '_2_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($destinationPath), $fileName);

        return $destinationPath . '/' . $fileName;
    }

    public function uploadProductImage_3(Request $request)
    {
        $file = $request->file('product_image_3');
        $destinationPath = 'assets/img/products';
        if (!file_exists(public_path($destinationPath))) {
            mkdir(public_path($destinationPath), 0777, true);
        }

        $fileName = time() . '_3_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($destinationPath), $fileName);

        return $destinationPath . '/' . $fileName;
    }

    public function uploadProductdocument(Request $request)
    {
        $file = $request->file('product_document');

        $destinationPath = public_path('assets/documents/products');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move($destinationPath, $fileName);

        return 'assets/documents/products/' . $fileName;
    }

    function productNumber()
    {
        $latest = Product::latest()->first();

        if (!$latest) {
            return 1;
        }

        return $latest->product_id + 1;
    }

    public function show($id)
    {
        if (\Auth::user()->can('view product')) {
            $product = Product::findOrFail($id);
            $warrantyUnits = \App\ProductWarrantySerial::whereHas('product', function ($query) use ($id) {
                $query->where('product_id', $id)
                    ->whereIn('grn_id', \App\Grn::where('branch_id', \Auth::user()->branchId())->select('id'));
            })->with('product')->orderByDesc('id')->get();

            return view('product.show', compact('product', 'warrantyUnits'));
        } else {
            // return response()->json(['error' => __('Permission denied.')], 401);
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit product')) {
            $product = Product::find($id);
            $productBrand = ProductBrand::orderBy('name')->get()->pluck('name', 'id');
            $productCategory = ProductCategory::orderBy('name')->get()->pluck('name', 'id');
            $allSubCategories = ProductSubCategory::orderBy('name')->get(['id', 'name', 'product_category_id']);
            $productSubCategory = ProductSubCategory::where('product_category_id', $product->product_category_id)
                ->orderBy('name')
                ->get()
                ->pluck('name', 'id');

            return view('product.edit', compact('productBrand', 'productCategory', 'productSubCategory', 'allSubCategories', 'product'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Product $product)
    {
        if (\Auth::user()->can('edit product')) {
            $rules = [
                'product_brand_id' => 'required',
                'product_category_id' => 'required',
                'product_sub_category_id' => 'required',
                'name' => 'required',
                'barcode' => ['required', Rule::unique('products', 'barcode')->ignore($product->id)],
                // 'sale_price' => 'required|numeric',
                // 'purchase_price' => 'required|numeric',
                'description' => 'required',
                'warranty_period' => 'nullable|numeric',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('product.index')->with('error', $messages->first());
            }

            if ($request->has('product_image_1')) {
                $productImagePath_1 = self::updateProductImage_1($request, $product->image_1);
            }

            if ($request->has('product_image_2')) {
                $productImagePath_2 = self::updateProductImage_2($request, $product->image_2);
            }

            if ($request->has('product_image_3')) {
                $productImagePath_3 = self::updateProductImage_3($request, $product->image_3);
            }

            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->serial_number = $request->serial_number;
            // $product->sale_price = $request->sale_price;
            // $product->purchase_price = $request->purchase_price;
            $product->description = $request->description;
            if ($request->has('product_image_1')) {
                $product->image_1 = $productImagePath_1;
            }
            if ($request->has('product_image_2')) {
                $product->image_2 = $productImagePath_2;
            }
            if ($request->has('product_image_3')) {
                $product->image_3 = $productImagePath_3;
            }
            if ($request->hasFile('product_document')) {
                $product->warranty_document = self::updateProductdocument($request, $product->warranty_document);
            }

            $product->warranty_period = $request->input('warranty_period') ?? 0;
            $product->additional_warranty_description = $request->additional_warranty_description;
            $product->product_brand_id = $request->product_brand_id;
            $product->product_category_id  = $request->product_category_id;
            $product->product_sub_category_id = $request->product_sub_category_id;
            // $product->product_color = $request->product_color;
            $product->created_by = \Auth::user()->id;
            $product->save();

            return redirect()->route('product.index')->with('success', __('Product successfully updated !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateProductImage_1(Request $request, $oldImage = null)
    {
        $file = $request->file('product_image_1');
        $destinationPath = 'assets/img/products';
        if (!file_exists(public_path($destinationPath))) {
            mkdir(public_path($destinationPath), 0777, true);
        }

        if ($oldImage && $oldImage != 'assets/img/products/default.png') {
            $oldFilePath = public_path($oldImage);
            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }

        $fileName = time() . '_1_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($destinationPath), $fileName);

        return $destinationPath . '/' . $fileName;
    }

    public function updateProductImage_2(Request $request, $oldImage = null)
    {
        $file = $request->file('product_image_2');
        $destinationPath = 'assets/img/products';
        if (!file_exists(public_path($destinationPath))) {
            mkdir(public_path($destinationPath), 0777, true);
        }

        if ($oldImage && $oldImage != 'assets/img/products/default.png') {
            $oldFilePath = public_path($oldImage);
            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }

        $fileName = time() . '_2_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($destinationPath), $fileName);

        return $destinationPath . '/' . $fileName;
    }


    public function updateProductImage_3(Request $request, $oldImage = null)
    {
        $file = $request->file('product_image_3');
        $destinationPath = 'assets/img/products';
        if (!file_exists(public_path($destinationPath))) {
            mkdir(public_path($destinationPath), 0777, true);
        }

        if ($oldImage && $oldImage != 'assets/img/products/default.png') {
            $oldFilePath = public_path($oldImage);
            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }

        $fileName = time() . '_3_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($destinationPath), $fileName);

        return $destinationPath . '/' . $fileName;
    }


    public function updateProductdocument(Request $request, $oldDocument = null)
    {
        $file = $request->file('product_document');

        $destinationPath = public_path('assets/documents/products');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if ($oldDocument && $oldDocument != 'assets/img/products/default.png') {
            $oldFilePath = public_path($oldDocument);
            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move($destinationPath, $fileName);

        return 'assets/documents/products/' . $fileName;
    }



    public function block($id)
    {
        if (\Auth::user()->can('block product')) {
            $product = Product::find($id);
            // if($product->created_by == \Auth::user()->creatorId())
            // {
            if ($product->type == 0) {
                $product = Inventory::where('product_id', $product->id)->first();
            } else {
                $product = Sale::where('product_id', $product->id)->first();
            }

            if (!empty($product)) {
                return redirect()->back()->with('error', __('This product is already assigned. So please remove this product related data.'));
            } else {
                $product = Product::find($id);
                $product->status = 0;
                $product->update();
                return redirect()->back()->with('success', __('Product successfully blocked !!!'));
            }
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function unblock($id)
    {
        if (\Auth::user()->can('block product')) {
            $product = Product::find($id);
            $product->status = 1;
            $product->update();
            return redirect()->back()->with('success', __('Product successfully unblocked !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function destroy($id)
    {
        if (\Auth::user()->canDeleteProduct()) {
            $productService = Product::find($id);
            if (!$productService) {
                return redirect()->back()->with('error', __('Product not found.'));
            }

            // Check if product has transactions
            $hasSales = \DB::table('sales')->where('product_id', $id)->exists()
                || \DB::table('sale_items')->where('product_id', $id)->exists()
                || \DB::table('invoice_products')->where('product_id', $id)->exists();
            $hasGrns = \DB::table('grn_products')->where('product_id', $id)->exists()
                || \DB::table('bill_products')->where('product_id', $id)->exists();
            $hasStock = \DB::table('stock_transactions')->where('product_id', $id)->exists();

            if ($hasSales || $hasGrns || $hasStock) {
                return redirect()->back()->with('error', __('Cannot delete item because this product has associated sales, GRN, or stock transactions. You can block the product instead.'));
            }

            // Remove uploaded images from disk if exists
            foreach (['image_1', 'image_2', 'image_3', 'warranty_document'] as $imgField) {
                if (!empty($productService->$imgField) && file_exists(public_path($productService->$imgField))) {
                    @unlink(public_path($productService->$imgField));
                }
            }

            $productService->delete();

            return redirect()->route('product.index')->with('success', __('Product successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
