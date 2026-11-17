<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Stock;
use App\ProductCategory;
use App\ProductSubCategory;
use App\Product;
use App\Warehouse;
use App\CustomField;
use App\Inventory;
use App\ProductService;
use App\ProductServiceUnit;
use App\Setting;
use App\StockTransfer;
use App\StockTransferItem;
use App\Tax;
use App\StockTransaction;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    protected $currencySymbol;


    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage stock')) {
            $products = Product::with([
                'categoryDetails',
                'subCategoryDetails',
                'brands',
                'inventory' => function ($query) {
                    $query->select('product_id', \DB::raw('SUM(remain_qty) as remain_qty'))
                        ->where('status', 1)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->groupBy('product_id');
                }
            ])->get();

            return view('stock.index', compact('products'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create product & service')) {
            $productCategory = ProductCategory::where('status', 1)->get()->pluck('name', 'id');
            $productSubCategory = ProductSubCategory::where('status', 1)->get()->pluck('name', 'id');

            $maxId = Product::max('product_id');
            $productCode = "PROD" . str_pad(($maxId + 00001), 5, "0", STR_PAD_LEFT);

            return view('stock.create', compact('productCategory', 'productSubCategory', 'productCode'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create product & service')) {
            $rules = [
                'product_category' => 'required',
                'product_sub_category' => 'required',
                'name' => 'required',
                'barcode' => 'required',
                'sale_price' => 'required|numeric',
                'purchase_price' => 'required|numeric',
                'description' => 'required',
                'product_image_1' => 'required',
                'product_image_2' => 'required',
                'product_image_3' => 'required',
                'warranty_period' => 'required|numeric',
                'product_document' => 'required',
                'additional_warranty_details' => 'required',
            ];

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
            if ($request->has('product_document')) {
                $productdocument = self::uploadProductdocument($request);
            } else {
                $productdocument = "assets/img/products/default.png";
            }

            $product = new Product();
            $product->product_id = $this->productNumber();
            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->sale_price = $request->sale_price;
            $product->purchase_price = $request->purchase_price;
            $product->description = $request->description;
            $product->image_1 = $productImagePath_1;
            $product->image_2 = $productImagePath_2;
            $product->image_3 = $productImagePath_3;
            $product->warranty_period = $request->warranty_period;
            $product->warranty_document = $productdocument;
            $product->additional_warranty_details = $request->additional_warranty_details;
            $product->product_category_id = $request->product_category;
            $product->product_sub_category_id = $request->product_sub_category;
            $product->status = 1;
            $product->created_by = \Auth::user()->creatorId();
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

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath . '/' . $file->getClientOriginalName();
    }

    public function uploadProductImage_2(Request $request)
    {
        $file = $request->file('product_image_2');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath . '/' . $file->getClientOriginalName();
    }

    public function uploadProductImage_3(Request $request)
    {
        $file = $request->file('product_image_3');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath . '/' . $file->getClientOriginalName();
    }

    public function uploadProductdocument(Request $request)
    {
        $file = $request->file('product_document');

        $destinationPath = 'assets/documents/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath . '/' . $file->getClientOriginalName();
    }


    function productNumber()
    {
        $latest = Product::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();

        if (!$latest) {
            return 1;
        }
        return $latest->product_id + 1;
    }




    public function show($id)
    {
        $productService = Product::find($id);
        if (\Auth::user()->can('edit product & service')) {
            // if($productService->created_by == \Auth::user()->creatorId()) {
            // $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 0)->get()->pluck('name', 'id');

            $unit     = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $tax      = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $productService->customField = CustomField::getData($productService, 'product');

            $customFields                = CustomField::where('module', '=', 'product')->get();

            $productService->tax_id      = explode(',', $productService->tax_id);

            return view('productservice.show', compact('unit', 'tax', 'productService', 'customFields'));

            // } else {
            //     return response()->json(['error' => __('Permission denied.')], 401);
            // }

        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }



    public function quantity($id)
    {
        $userBranchId = \Auth::user()->branchId();

        if (!$userBranchId) {
            $branch = null;
            $current_inventory = collect();
            $last_purchase = null;
        } else {
            $branch = Branch::find($userBranchId);

            $current_inventory = Inventory::query()
                ->where('product_id', $id)
                ->where('branch_id', $userBranchId)
                ->whereIn('status', [0, 1])
                ->orderBy('id')->get();

            $last_purchase = Inventory::select('purchase_price as last_purchase_price', 'hire_price as last_hire_price', 'sale_price as last_sale_price')
                ->where('product_id', $id)
                ->where('branch_id', $userBranchId)
                ->latest()
                ->first();
        }
        $currencySymbol = $this->currencySymbol;
        return view('stock.quantity', compact('branch', 'current_inventory', 'last_purchase', 'currencySymbol'));
    }

    public function transfer()
    {
        // die(var_dump('works'));
        if (\Auth::user()->can('create stock transfer')) {
            $branches = Branch::whereNotIn('id', [\Auth::user()->branchId()])->where('status', 1)->get();
            if (\Auth::user()->branchId() == 0) {
                $products = Inventory::select('product_id', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products.brands', 'products.productCategory')
                    ->where('status', 1)
                    ->groupBy('product_id')
                    ->get()
                    ->sortBy(function ($item) {
                        return ($item->products->productCategory->name ?? '') . ' - ' . ($item->products->name ?? '');
                    });
            } else if (\Auth::user()->branchId() != 0) {
                $products = Inventory::select('product_id', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products.brands', 'products.productCategory')
                    ->where('branch_id', \Auth::user()->branchId())
                    ->where('status', 1)
                    ->groupBy('product_id')
                    ->get()
                    ->sortBy(function ($item) {
                        return ($item->products->productCategory->name ?? '') . ' - ' . ($item->products->name ?? '');
                    });
            }

            return view('stock.transfer', compact('branches', 'products'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function inventoryProducts($id)
    {
        $products = Inventory::select('id AS inventory_id', 'product_id', 'purchase_price', 'sale_price', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products')
            ->where('branch_id', \Auth::user()->branchId())
            ->where('status', 1)
            ->where('product_id', $id)
            ->groupBy('purchase_price', 'sale_price')
            ->get();
        return json_encode($products);
    }

    public function getInventoryDetail($purchasePrice, $salePrice, $productId)
    {
        // echo json_encode('works');
        $products = Inventory::select('product_id', 'purchase_price', 'sale_price', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products')
            ->where('branch_id', \Auth::user()->branchId())
            ->where('status', 1)
            ->where('product_id', $productId)
            ->where('purchase_price', $purchasePrice)
            ->where('sale_price', $salePrice)
            // ->where('product_id', $request->product_id)
            // ->where('purchase_price', $request->purchase_price)
            ->groupBy('purchase_price', 'product_id', 'sale_price')
            ->first();
        return json_encode($products);
        // return json_encode($request->all());
    }

    public function itemTransfer(Request $request)
    {
        if (\Auth::user()->can('create stock transfer')) {
            $trasfers = new StockTransfer();
            $trasfers->date = $request->date_of_transfer;
            $trasfers->transfer_to = $request->branch;
            $trasfers->total_amount = $request->total_amount;
            $trasfers->branch_id = \Auth::user()->branchId();
            $trasfers->created_by = \Auth::user()->id;
            $trasfers->save();

            foreach ($request->items as $items) {
                $inventoryId = $items['inventory_products'];

                $stockItems = new StockTransferItem();
                $stockItems->stock_transfer_id = $trasfers->id;
                $stockItems->inventory_id = $inventoryId;
                $stockItems->product_id = $items['product'];
                $stockItems->purchase_price = $items['purchase_price'];
                $stockItems->sale_price = $items['sale_price'];
                $stockItems->quantity = $items['quantity'];
                $stockItems->amount = $items['net_amount'];
                $stockItems->save();

                $newInventory = new Inventory();
                $newInventory->product_id = $items['product'];
                $newInventory->stock_transfer_id = $stockItems->id;
                $newInventory->tranfer_from = \Auth::user()->branchId();
                $newInventory->branch_id = $request->branch;
                $newInventory->grn_qty = $items['quantity'];
                $newInventory->date = $request->date_of_transfer;
                $newInventory->remain_qty = $items['quantity'];
                $newInventory->purchase_price = $items['purchase_price'];
                $newInventory->sale_price = $items['sale_price'];
                $newInventory->status = 1;
                $newInventory->save();

                $stockTransaction = new StockTransaction();
                $stockTransaction->stock_master_id = 4;
                $stockTransaction->inventory_id = $newInventory->id;
                $stockTransaction->product_id = $items['product'];
                $stockTransaction->quantity = $items['quantity'];
                $stockTransaction->date = $request->date_of_transfer;
                $stockTransaction->branch_id = $request->branch;
                $stockTransaction->created_by     = \Auth::user()->id;
                $stockTransaction->status     = 1;
                $stockTransaction->save();

                $productId = $items['product'];
                $purchasePrise = $items['purchase_price'];
                $salePrice = $items['sale_price'];
                $quantity = $items['quantity'];

                $purchasePrice = $purchasePrise * $quantity;

                while ($quantity > 0) {
                    $inventory = Inventory::where('product_id', $productId)
                        ->where('purchase_price', $purchasePrise)
                        ->where('sale_price', $salePrice)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('status', 1)
                        ->first();
                    if ($inventory) {
                        if ($inventory->remain_qty > $quantity) {
                            $newQty = $inventory->remain_qty - $quantity;
                            $inventory->remain_qty = $newQty;
                            $inventory->update();

                            $stockTransaction = new StockTransaction();
                            $stockTransaction->stock_master_id = 3;
                            $stockTransaction->inventory_id = $inventory->id;
                            $stockTransaction->product_id = $inventory->product_id;
                            $stockTransaction->quantity = $quantity;
                            $stockTransaction->date = $request->date_of_transfer;
                            $stockTransaction->branch_id = \Auth::user()->branchId();
                            $stockTransaction->created_by     = \Auth::user()->id;
                            $stockTransaction->status     = 1;
                            $stockTransaction->save();

                            $quantity = 0;
                        } else if ($inventory->remain_qty <= $quantity) {

                            $stockTransaction = new StockTransaction();
                            $stockTransaction->stock_master_id = 3;
                            $stockTransaction->inventory_id = $inventory->id;
                            $stockTransaction->product_id = $inventory->product_id;
                            $stockTransaction->quantity = $inventory->remain_qty;
                            $stockTransaction->date = $request->date_of_transfer;
                            $stockTransaction->branch_id = \Auth::user()->branchId();
                            $stockTransaction->created_by     = \Auth::user()->id;
                            $stockTransaction->status     = 1;
                            $stockTransaction->save();

                            $newQty = $quantity - $inventory->remain_qty;
                            $inventory->remain_qty = 0;
                            $inventory->status = 0;
                            $inventory->update();
                            $quantity = $newQty;
                        }
                    } else {
                        $quantity = 0;
                    }
                }

                // Account Transactions
                $productDetail = Product::find($productId);
                $recieverBranch = $request->branch;
                $senderBranch = \Auth::user()->branchId();

                $productCategory = ProductCategory::find($productDetail->product_category_id);

                $currentBranchCA = Branch::find($senderBranch);
                Utility::accountStockTransferTransaction(
                    $request->date_of_transfer,
                    $currentBranchCA->chart_of_account_id,
                    $productCategory->inventory_chart_of_account_id,
                    $trasfers->id,
                    $productId,
                    $senderBranch,
                    13,
                    $purchasePrice,
                    'debit'
                );

                Utility::accountStockTransferTransaction(
                    $request->date_of_transfer,
                    $productCategory->inventory_chart_of_account_id,
                    $currentBranchCA->chart_of_account_id,
                    $trasfers->id,
                    $productId,
                    $senderBranch,
                    13,
                    $purchasePrice,
                    'credit'
                );

                $transferBranchCA = Branch::find($recieverBranch);
                Utility::accountStockTransferTransaction(
                    $request->date_of_transfer,
                    $productCategory->inventory_chart_of_account_id,
                    $transferBranchCA->chart_of_account_id,
                    $trasfers->id,
                    $productId,
                    $recieverBranch,
                    13,
                    $purchasePrice,
                    'debit'
                );

                Utility::accountStockTransferTransaction(
                    $request->date_of_transfer,
                    $transferBranchCA->chart_of_account_id,
                    $productCategory->inventory_chart_of_account_id,
                    $trasfers->id,
                    $productId,
                    $recieverBranch,
                    13,
                    $purchasePrice,
                    'credit'
                );

                // if($productDetail->product_category_id == 1){
                //     Utility::accountStockTransferTransaction($request->date_of_transfer, 175, 175, $trasfers->id, $productId, $recieverBranch, 13,
                //                                             $purchasePrise, 'debit');
                //     Utility::accountStockTransferTransaction($request->date_of_transfer, 175, 175, $trasfers->id, $productId, $senderBranch, 13,
                //                                             $purchasePrise, 'credit');
                // } elseif($productDetail->product_category_id == 2){
                //     Utility::accountStockTransferTransaction($request->date_of_transfer, 174, 174, $trasfers->id, $productId, $recieverBranch, 13,
                //                                             $purchasePrise, 'debit');
                //     Utility::accountStockTransferTransaction($request->date_of_transfer, 174, 174, $trasfers->id, $productId, $senderBranch, 13,
                //                                             $purchasePrise, 'credit');
                // } elseif($productDetail->product_category_id == 3){
                //     Utility::accountStockTransferTransaction($request->date_of_transfer, 176, 176, $trasfers->id, $productId, $recieverBranch, 13,
                //                                             $purchasePrise, 'debit');
                //     Utility::accountStockTransferTransaction($request->date_of_transfer, 176, 176, $trasfers->id, $productId, $senderBranch, 13,
                //                                             $purchasePrise, 'credit');
                // }
            }

            return redirect()->back()->with('success', __('Product Transfer Successfully.'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function stockTransfers()
    {
        if (\Auth::user()->can('view stock transfer')) {
            $transfers = StockTransfer::where('branch_id', \Auth::user()->branchId())->orderBy('id', 'DESC')->get();
            $currencySymbol = $this->currencySymbol;
            return view('stock.stock_transfers', compact('transfers', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function stockTransferInvoice($id, $command)
    {
        $transfers = StockTransfer::with('stockTransItems')->find($id);
        $user = StockTransfer::select('users.name')
            ->join('users', 'users.id', 'stock_transfers.created_by')
            ->where('stock_transfers.id', $id)
            ->first();
        // dd($id);
        return view('stock.print_view_invoice', compact('transfers', 'user', 'command'));
    }

    public function stockTransferReceipt($id, $command)
    {
        $img         = asset(Storage::url('uploads/logo/small_logo.png'));
        $settings = Utility::settings();
        $color      = '#' . $settings['bill_color'];
        $font_color = Utility::getFontColor($color);
        $transfers = StockTransfer::with('stockTransItems')->find($id);
        $user = StockTransfer::select('users.name', 'users.id')
            ->join('users', 'users.id', 'stock_transfers.created_by')
            ->where('stock_transfers.id', $id)
            ->first();
        // dd($id);
        return view('stock.print_view_receipt', compact('transfers', 'user', 'command', 'color', 'settings', 'img', 'font_color'));
        // return view('stock.print_view_invoice', compact('transfers', 'user', 'command'));
    }


    public function edit($id)
    {
        $productService = ProductService::find($id);

        if (\Auth::user()->can('edit product & service')) {
            if ($productService->created_by == \Auth::user()->creatorId()) {
                $category = ProductCategory::where('created_by', '=', \Auth::user()->creatorId())->where('status', 1)->get()->pluck('name', 'id');

                $subcategory = ProductSubCategory::where('created_by', '=', \Auth::user()->creatorId())->where('status', 1)->get()->pluck('name', 'id');

                $warehouse = Warehouse::where('status', '=', 1)->get()->pluck('name', 'id');

                return view('productservice.edit', compact('category', 'productService', 'subcategory', 'warehouse'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        $productService = ProductService::find($id);
        if (\Auth::user()->can('edit product & service')) {
            if ($productService->created_by == \Auth::user()->creatorId()) {
                $rules = [
                    'name' => 'required',
                    'sale_price' => 'required|numeric',
                    'purchase_price' => 'required|numeric',
                    'quantity' => 'required|numeric',
                    'warranty_period' => 'required',
                ];

                $validator = \Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('productservice.index')->with('error', $messages->first());
                }

                if ($request->has('product_image_1')) {
                    $productImagePath_1 = self::updateProductImage_1($request);
                } else {
                    $productImagePath_1 = "assets/img/products/default.png";
                }

                if ($request->has('product_image_2')) {
                    $productImagePath_2 = self::updateProductImage_2($request);
                } else {
                    $productImagePath_2 = "assets/img/products/default.png";
                }

                if ($request->has('product_image_3')) {
                    $productImagePath_3 = self::updateProductImage_3($request);
                } else {
                    $productImagePath_3 = "assets/img/products/default.png";
                }

                if ($request->has('product_document')) {
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

    public function updateProductImage_1(Request $request)
    {
        $file = $request->file('product_image_1');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath . '/' . $file->getClientOriginalName();
    }

    public function updateProductImage_2(Request $request)
    {
        $file = $request->file('product_image_2');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath . '/' . $file->getClientOriginalName();
    }

    public function updateProductImage_3(Request $request)
    {
        $file = $request->file('product_image_3');

        $destinationPath = 'assets/img/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath . '/' . $file->getClientOriginalName();
    }

    public function updateProductdocument(Request $request)
    {
        $file = $request->file('product_document');

        $destinationPath = 'assets/documents/products';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath . '/' . $file->getClientOriginalName();
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete product & service')) {
            $productService = ProductService::find($id);
            if ($productService->created_by == \Auth::user()->creatorId()) {
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

    public function unblock($id)
    {
        $bankAccount = ProductService::find($id);
        $bankAccount->status = 1;
        $bankAccount->update();

        return redirect()->route('productservice.index')->with('success', __('Product successfully blocked.'));
    }

    public function unblocktwo($id)
    {
        $bankAccount = ProductService::find($id);
        $bankAccount->status = 0;
        $bankAccount->update();

        return redirect()->route('productservice.index')->with('success', __('Product successfully unblocked.'));
    }
}
