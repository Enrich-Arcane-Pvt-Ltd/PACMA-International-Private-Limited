<?php

namespace App\Http\Controllers;

use App\Grn;
use App\GrnProduct;
use App\CustomField;
use Illuminate\Http\Request;
use App\BankAccount;
use App\Bill;
use App\BillPayment;
use App\BillProduct;
use App\Inventory;
use App\PurchaseOrder;
use App\Setting;
use App\Mail\BillPaymentCreate;
use App\Mail\BillSend;
use App\Mail\VenderBillSend;
use App\Product;
use App\ProductService;
use App\ProductServiceCategory;
use App\ProductCategory;
use App\ProductWarrantySerial;
use App\StockTransaction;
use App\Transaction;
use App\Utility;
use App\Vender;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class GrnController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage grn')) {

            // $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $vender = Vender::get()->pluck('name', 'id');
            $vender->prepend('All', '');

            $status = Grn::$statues;

            // $query = Grn::with('GrnProducts')->where('branch_id', '=', \Auth::user()->branchId())->limit(1);
            $query = Grn::with('vender', 'grnProducts.products')->where('branch_id', '=', \Auth::user()->branchId());
            // dd($query->get());

            if (!empty($request->vender)) {
                $query->where('vender_id', '=', $request->vender);
            }
            if (!empty($request->purchase_date)) {
                $date_range = explode(' - ', $request->purchase_date);
                $query->whereBetween('purchase_date', $date_range);
            }
            if (!empty($request->status)) {
                $query->where('status', '=', $request->status);
            }
            $grn = $query->orderByDesc('id')->get();

            return view('grn.index', compact('grn', 'vender', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit grn')) {
            $grn_number = \Auth::user()->grnNumberFormat($id);
            $grns = Grn::with([
                'GrnProducts.products.brands',
            ])->where('branch_id', \Auth::user()->branchId())->find($id);

            if (!$grns) {
                return redirect()->back()->with('error', __('GRN not found.'));
            }

            // Format numeric fields for display
            foreach ($grns->GrnProducts as $product) {
                $product->unit_cost = sprintf('%.2f', $product->unit_cost ?? 0);
                $product->grn_cost = sprintf('%.2f', $product->grn_cost ?? 0);
                $product->sale_price = sprintf('%.2f', $product->sale_price ?? 0);
                $product->hire_price = sprintf('%.2f', $product->hire_price ?? 0);
            }

            $venders = Vender::orderBy('name')->pluck('name', 'id');
            $product_services = Product::select('id', DB::raw("CONCAT(barcode, ' - ', name) as fullname"))
                ->where(function ($query) use ($grns) {
                    $query->where('status', 1)->orWhereIn('id', $grns->GrnProducts->pluck('product_id'));
                })->orderBy('name')->pluck('fullname', 'id');
            $product_services->prepend(__('Select Product'), '');
            $warrantyProductTypes = Product::with('categoryDetails')->whereIn('id', $product_services->keys()->filter())->get()
                ->mapWithKeys(function ($product) {
                    return [$product->id => (bool) preg_match('/furniture/i', optional($product->categoryDetails)->name ?? '')];
                });
            $currencySymbol = $this->currencySymbol;
            return view('grn.edit', compact('grn_number', 'grns', 'venders', 'product_services', 'currencySymbol', 'warrantyProductTypes'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }



    function grnId()
    {
        $latest = Grn::latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->grn_id + 1;
    }

    public function createManual()
    {
        if (\Auth::user()->can('create grn')) {
            $venders = Vender::orderBy('name')->pluck('name', 'id');
            $venders->prepend(__('Select Supplier'), '');

            $product_services = Product::select('id', DB::raw("CONCAT(barcode, ' - ', name) as fullname"))
                ->where('status', 1)
                ->orderBy('name')
                ->pluck('fullname', 'id');
            $product_services->prepend(__('Select Product'), '');

            $currencySymbol = $this->currencySymbol;

            return view('grn.create_manual', compact('venders', 'product_services', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        return \App\Services\GrnInvoiceNumberGuard::run($request, function () use ($request) {
            return $this->storeReceipt($request);
        });
    }

    protected function storeReceipt(Request $request)
    {
        // dd($request->all());
        if (\Auth::user()->can('create grn')) {
            $number = $request->input('supplier_invoice_number');
            if (is_string($number) && trim($number) !== '') {
                $existing = $this->editableGrnForInvoice($number);
                if ($existing) {
                    return redirect()->route('grnOrderEdit.OrderEdit', $existing->id)->with('info',
                        __('Supplier Invoice Number :number already exists. Use Add Item to add products to this GRN.', ['number' => e($existing->supplier_invoice_number)]));
                }
            }
            $validator = \Validator::make(
                $request->all(),
                [
                    'vender_id' => 'required',
                    'order_date' => 'required',
                    'required_by' => 'required',
                    'supplier_invoice_number' => ['required', 'string', 'max:255', new \App\Rules\UniqueGrnInvoiceNumber],
                    'items' => 'required|array',
                    'items.*.unit_cost' => 'required|numeric|min:0',
                    'status' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $uploadedWarrantyFiles = [];
            try {
                return DB::transaction(function () use ($request, &$uploadedWarrantyFiles) {
            $grn            = new Grn();
            $grn->branch_id   = \Auth::user()->branchId();
            $grn->order_id = !empty($request->order_id) ? $request->order_id : null;
            $grn->vender_id = $request->vender_id;
            $grn->order_date      = $request->order_date;
            $grn->status         = $request->status;
            $grn->approval_status = 'pending';
            $grn->required_by       = $request->required_by;
            $grn->supplier_invoice_number       = $request->supplier_invoice_number;
            $grn->total_amount    = $request->total_amount;
            $grn->created_by     = \Auth::user()->id;
            $grn->save();

            $newGrn = $grn;

            $products = $request->items;

            for ($i = 0; $i < count($products); $i++) {
                $amount = round($products[$i]['receive_quantity'] * $products[$i]['grn_cost']);

                $grnProduct              = new GrnProduct();
                // $grnProduct->purchase_order_id     = $newGrn->id;
                $grnProduct->grn_id     = $newGrn->id;
                $grnProduct->product_id  = $products[$i]['item'];
                $grnProduct->quantity    = isset($products[$i]['quantity']) && $products[$i]['quantity'] > 0 ? $products[$i]['quantity'] : $products[$i]['receive_quantity'];
                $grnProduct->received_qty    = $products[$i]['receive_quantity'];
                $grnProduct->unit_cost       = isset($products[$i]['unit_cost']) ? $products[$i]['unit_cost'] : $products[$i]['grn_cost'];
                $grnProduct->grn_cost       = $products[$i]['grn_cost'];
                $grnProduct->sale_price       = $products[$i]['sale_price'];
                $grnProduct->hire_price       = $products[$i]['hire_price'];
                $grnProduct->amount       = $amount;
                $grnProduct->save();

                // Extract serial and warranty data from request
                if ($request->has('serial_warranty_data') && !empty($request->serial_warranty_data)) {
                    $serialWarrantyData = json_decode($request->serial_warranty_data, true);
                    $productKey = null;
                    if (isset($products[$i]['id']) && isset($serialWarrantyData['product_' . $products[$i]['id']])) {
                        $productKey = 'product_' . $products[$i]['id'];
                    } elseif (isset($serialWarrantyData['product_' . $products[$i]['item']])) {
                        $productKey = 'product_' . $products[$i]['item'];
                    }

                    if ($productKey && isset($serialWarrantyData[$productKey])) {
                        $serialsList = $serialWarrantyData[$productKey];
                        for ($j = 0; $j < count($serialsList); $j++) {
                            $data = $serialsList[$j];
                            if (!empty($data['serial']) || !empty($data['warranty'])
                                || !empty($data['warranty_period']) || !empty($data['additional_warranty_description'])
                                || $request->hasFile("warranty_documents.{$productKey}.{$j}")
                                || $request->hasFile("warranty_document_{$productKey}_{$j}")) {
                                $warrantySerial = new ProductWarrantySerial();
                                $warrantySerial->grn_product_id = $grnProduct->id;
                                $warrantySerial->serial_number = $data['serial'] ?? null;
                                $warrantySerial->warranty_number = !empty($data['warranty']) ? $data['warranty'] : null;
                                $warrantySerial->warranty_period = !empty($data['warranty_period']) ? (int)$data['warranty_period'] : null;
                                $warrantySerial->additional_warranty_description = !empty($data['additional_warranty_description']) ? $data['additional_warranty_description'] : null;

                                // Handle uploaded warranty card document
                                $docFile = null;
                                if ($request->hasFile("warranty_documents.{$productKey}.{$j}")) {
                                    $docFile = $request->file("warranty_documents.{$productKey}.{$j}");
                                } elseif ($request->hasFile("warranty_document_{$productKey}_{$j}")) {
                                    $docFile = $request->file("warranty_document_{$productKey}_{$j}");
                                }

                                if ($docFile && $docFile->isValid()) {
                                    $destinationPath = public_path('assets/documents/warranty');
                                    if (!file_exists($destinationPath)) {
                                        mkdir($destinationPath, 0777, true);
                                    }
                                    $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '', $docFile->getClientOriginalName());
                                    $fileName = \Illuminate\Support\Str::uuid() . '_' . $cleanName;
                                    $docFile->move($destinationPath, $fileName);
                                    $warrantySerial->warranty_document = 'assets/documents/warranty/' . $fileName;
                                    $uploadedWarrantyFiles[] = public_path($warrantySerial->warranty_document);
                                }

                                $warrantySerial->status = 0; // Assuming active status
                                $warrantySerial->save();
                            }
                        }
                    }
                }

                $inventory = new Inventory();
                $inventory->grn_id = $newGrn->id;
                if (\Illuminate\Support\Facades\Schema::hasColumn('inventories', 'grn_product_id')) $inventory->grn_product_id = $grnProduct->id;
                $inventory->product_id = $products[$i]['item'];
                $inventory->branch_id = \Auth::user()->branchId();
                $inventory->grn_qty = $products[$i]['receive_quantity'];
                $inventory->date = $request->order_date;
                $inventory->remain_qty = $products[$i]['receive_quantity'];
                $inventory->purchase_price = $products[$i]['grn_cost'];
                $inventory->sale_price = $products[$i]['sale_price'];
                $inventory->hire_price = $products[$i]['hire_price'];
                $inventory->supplier_invoice_number = $request->supplier_invoice_number;
                $inventory->status = 0; // Activated upon Head Office approval
                $inventory->save();

                $stockTransaction = new StockTransaction();
                $stockTransaction->stock_master_id = 1;
                $stockTransaction->inventory_id = $inventory->id;
                $stockTransaction->product_id = $products[$i]['item'];
                $stockTransaction->quantity = $products[$i]['receive_quantity'];
                $stockTransaction->date = $request->order_date;
                $stockTransaction->branch_id = \Auth::user()->branchId();
                $stockTransaction->created_by     = \Auth::user()->id;
                $stockTransaction->status     = 0; // Activated upon Head Office approval
                $stockTransaction->save();

                // Account Transactions
                $productDetail = Product::find($products[$i]['item']);
                $productCategory = ProductCategory::find($productDetail->product_category_id);

                Utility::accountGrnTransaction(
                    $request->required_by,
                    $productCategory->inventory_chart_of_account_id,
                    208,
                    $newGrn->id,
                    $products[$i]['item'],
                    $request->vender_id,
                    10,
                    $amount,
                    'debit'
                );
                Utility::accountGrnTransaction(
                    $request->required_by,
                    208,
                    $productCategory->inventory_chart_of_account_id,
                    $newGrn->id,
                    $products[$i]['item'],
                    $request->vender_id,
                    10,
                    $amount,
                    'credit'
                );
                \App\AccountTransaction::where('grn_id', $newGrn->id)->update(['status' => 0]);
            }

            if (!empty($request->order_id)) {
                $purchaseOrder = PurchaseOrder::find($request->order_id);
                if ($purchaseOrder) {
                    $purchaseOrder->status = 4;
                    $purchaseOrder->update();
                }
            }

            return redirect()->route('grn.index', $newGrn->id)->with('success', __('GRN successfully created.'));
                });
            } catch (\Throwable $error) {
                foreach ($uploadedWarrantyFiles as $path) if (is_file($path)) unlink($path);
                throw $error;
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    private function editableGrnForInvoice($number)
    {
        $user = \Auth::user();
        if (!$user->can('edit grn') || !$user->canEditGrnCostAndQuantity()) return null;
        return Grn::where('branch_id', $user->branchId())
            ->whereRaw('LOWER(TRIM(supplier_invoice_number)) = LOWER(?)', [trim($number)])
            ->orderByDesc('id')->first();
    }

    public function lookupInvoice(Request $request)
    {
        abort_unless(\Auth::user()->can('create grn'), 403);
        $data = $request->validate(['supplier_invoice_number' => 'required|string|max:255']);
        $existing = $this->editableGrnForInvoice($data['supplier_invoice_number']);
        return response()->json(['redirect' => $existing ? route('grnOrderEdit.OrderEdit', $existing->id) : null])
            ->header('Cache-Control', 'no-store');
    }

    public function updateInvoiceNumber(Request $request, $id)
    {
        return \App\Services\GrnInvoiceNumberGuard::run($request, function () use ($request, $id) {
            return $this->saveInvoiceNumber($request, $id);
        });
    }

    protected function saveInvoiceNumber(Request $request, $id)
    {
        abort_unless(\Auth::user()->can('edit grn'), 403);
        $grn = Grn::where('branch_id', \Auth::user()->branchId())->findOrFail($id);
        $data = $request->validate(['supplier_invoice_number' => ['required', 'string', 'max:255', new \App\Rules\UniqueGrnInvoiceNumber($grn->id)]]);
        DB::transaction(function () use ($grn, $data) {
            $grn->supplier_invoice_number = $data['supplier_invoice_number'];
            $grn->save();
            Inventory::where('grn_id', $grn->id)->update([
                'supplier_invoice_number' => $data['supplier_invoice_number'],
            ]);
        });

        return response()->json(['message' => __('Supplier invoice number updated.')]);
    }

    public function update(Request $request, $id)
    {
        return \App\Services\GrnInvoiceNumberGuard::run($request, function () use ($request, $id) {
            return $this->updateReceipt($request, $id);
        });
    }

    protected function updateReceipt(Request $request, $id)
    {
        abort_unless(\Auth::user()->can('edit grn'), 403);
        // Find the existing GRN by its ID
        $grn = Grn::where('branch_id', \Auth::user()->branchId())->find($id);

        // Check if the GRN exists
        if (!$grn) {
            return redirect()->back()->with('error', __('GRN not found.'));
        }

        // Validate the incoming request
        $validator = \Validator::make(
            $request->all(),
            [
                'vender_id' => 'required',
                'supplier_invoice_number' => ['required', 'string', 'max:255', new \App\Rules\UniqueGrnInvoiceNumber($grn->id)],
                'items' => 'required|array',
                'items.*.id' => 'nullable|integer',
                'items.*.item' => 'required|integer|exists:products,id',
                'items.*.batch_key' => 'nullable|string|regex:/^[a-zA-Z0-9_]+$/|distinct',
                'items.*.unit_cost' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.receive_quantity' => 'required|integer|min:1',
                'items.*.grn_cost' => 'required|numeric|min:0',
                'items.*.sale_price' => 'required|numeric|min:0',
                'items.*.hire_price' => 'required|numeric|min:0',
                'warranty_documents.*.*' => 'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:10240',
            ]
        );

        // If validation fails, return with error messages
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->route('grn.index')->with('error', $messages->first());
        }

        $existingLines = GrnProduct::where('grn_id', $id)->get()->keyBy('id');
        $serialPayload = json_decode($request->input('serial_warranty_data', '{}'), true) ?: [];
        foreach ($request->items as $item) {
            if ((int) $item['receive_quantity'] !== (int) $item['quantity']) {
                throw \Illuminate\Validation\ValidationException::withMessages(['items' => __('Quantity and Received Quantity must be the same.')]);
            }
            $line = $existingLines->get($item['id'] ?? null);
            abort_unless(empty($item['id']) || $line, 422);
            abort_if(!$line && !\Auth::user()->canEditGrnCostAndQuantity(), 403, 'Only administrators can add GRN items.');
            if (!$line) {
                $units = $serialPayload[\App\Services\GrnWarrantyRules::key($item)] ?? [];
                if (count($units) !== (int) $item['receive_quantity'] || collect($units)->contains(function ($unit) { return empty($unit['serial']); })) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['items' => __('Add serial and warranty details for every new received unit.')]);
                }
            }
            if (!\Auth::user()->canEditGrnCostAndQuantity()) {
                abort_if((float) $item['unit_cost'] !== (float) $line->unit_cost
                    || (int) $item['quantity'] !== (int) $line->received_qty, 403,
                    'Only administrators can change Unit Cost or Quantity.');
            }
        }

        $request->merge(['total_amount' => collect($request->items)->sum(function ($item) {
            return $item['receive_quantity'] * $item['grn_cost'];
        })]);

        $hasConsumedStock = Inventory::where('grn_id', $id)->whereColumn('grn_qty', '!=', 'remain_qty')->exists();
        if ($hasConsumedStock && !\Auth::user()->canEditGrnCostAndQuantity()) {
            return redirect()->route('grn.index')->with('error', __('Could not proceed! Some products have already been sold.'));
        }
        app(\App\Services\GrnReceiptAdditions::class)->save($request, $id);
        return redirect()->route('grn.show', [$id])->with('success', __('GRN successfully updated. Existing invoiced units were preserved.'));
    }
    public function genarateGrn($id)
    {

        if (\Auth::user()->can('create grn')) {
            // Fetch the purchase order number based on the user's format
            $purchase_order_number = \Auth::user()->purchaseOrderNumberFormat($id);

            // Retrieve vendors and product services for the dropdowns
            $venders = Vender::orderBy('name')->pluck('name', 'id');
            $product_services = Product::orderBy('name')->pluck('barcode', 'id');

            // Fetch the purchase order along with its related purchase products and their products
            $purchorders = PurchaseOrder::with('purchaseProducts', 'purchaseProducts.products', 'purchaseProducts.products.brands', 'purchaseProducts.products.productCategory', 'purchaseProducts.products.categoryDetails')->find($id);
            if (!$purchorders) {
                return redirect()->route('purchase-orders.create-grn')->with('error', 'Purchase order not found.');
            }

            $product = optional($purchorders->purchaseProducts->first())->products;

            // Format unit_cost and other prices to always include two decimal places
            $purchorders->purchaseProducts->each(function ($purchaseProduct) {
                $lastPurchase = Inventory::select(
                    'purchase_price as last_purchase_price',
                    'hire_price as last_hire_price',
                    'sale_price as last_sale_price'
                )
                    ->where('product_id', $purchaseProduct->product_id)
                    ->where('branch_id', \Auth::user()->branch_id) // Use branch filtering if needed
                    ->latest()
                    ->first();

                // Format prices with two decimal places
                $purchaseProduct->last_purchase_price = number_format($lastPurchase->last_purchase_price ?? 0, 2, '.', '');
                $cashPrice = floatval($lastPurchase->last_sale_price ?? ($purchaseProduct->products->sale_price ?? 0));
                $hirePrice = round(floatval($lastPurchase->last_hire_price ?? ($purchaseProduct->products->hire_price ?? 0)));
                $purchaseProduct->sale_price = number_format($cashPrice, 2, '.', '');
                $purchaseProduct->hire_price = number_format($hirePrice, 2, '.', '');

                // Format unit_cost (if applicable)
                $purchaseProduct->unit_cost = number_format($purchaseProduct->unit_cost, 2, '.', '');
                $purchaseProduct->grn_cost = number_format($purchaseProduct->unit_cost, 2, '.', '');
                $purchaseProduct->receive_quantity = number_format($purchaseProduct->quantity ?? 0);
                $catName = ($purchaseProduct->products && $purchaseProduct->products->productCategory) ? $purchaseProduct->products->productCategory->name : (($purchaseProduct->products && $purchaseProduct->products->categoryDetails) ? $purchaseProduct->products->categoryDetails->name : '');
                $purchaseProduct->is_furniture = preg_match('/furniture/i', $catName) ? 1 : 0;
                $purchaseProduct->product_name = $purchaseProduct->products ? $purchaseProduct->products->name : '';
                $purchaseProduct->product_model = ($purchaseProduct->products && $purchaseProduct->products->brands) ? $purchaseProduct->products->brands->name : '';
            });
            $currencySymbol = $this->currencySymbol;
            // Return the GRN creation view with the required data
            return view('grn.create', compact('venders', 'product_services', 'purchorders', 'purchase_order_number', 'product', 'currencySymbol'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function viewPurchaseOrders()
    {
        if (\Auth::user()->can('view purchase order')) {
            $purchOrders = PurchaseOrder::where('status', 3)->where('branch_id', \Auth::user()->branchId())->get();
            $currencySymbol = $this->currencySymbol;
            return view('grn.purchaseToGrn', compact('purchOrders', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function showGrn($id)
    {
        if (\Auth::user()->can('view grn')) {
            // Fetch the GRN data, including related products and serial/warranty data
            $purchorders = Grn::with([
                'vender',
                'branch',
                'GrnProducts.products',
                'GrnProducts.productWarrantySerial' // Assuming you have this relationship set up
            ])->find($id);
            // dd($purchorders);

            // Check if the current user's branch matches the GRN's branch
            if ($purchorders->branch_id == \Auth::user()->branchId()) {
                $creators = Setting::get();
                $currencySymbol = $this->currencySymbol;
                // Pass the GRN data to the view
                return view('grn.view', compact('purchorders', 'creators', 'currencySymbol'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (!\Auth::user()->canDeleteGrn()) {
            $message = __('Only administrators with delete permission can delete GRNs. Please contact an administrator.');
            return request()->expectsJson()
                ? response()->json(['message' => $message], 403)
                : redirect()->route('grn.index')->with('error', $message);
        }
        app(\App\Services\GrnDeletion::class)->delete($id);
        return redirect()->route('grn.index')->with('success', __('GRN successfully deleted.'));
    }
    public function items(Request $request)
    {
        $items = GrnProduct::where('grn_id', $request->grn_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }

    public function pdf($purchase_order_id)
    {
        $settings = Utility::settings();
        $orderId   = Crypt::decrypt($purchase_order_id);

        $purchase_order  = Grn::with('vender', 'branch', 'GrnProducts.products')->where('id', $orderId)->first();
        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $purchase_order->created_by);
        $data1 = $data->get();
        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $vendor = $purchase_order->vender;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach ($purchase_order->GrnProducts as $product) {
            $item               = new \stdClass();
            $item->name         = !empty($product->products) ? $product->products->name : '';
            $item->quantity     = $product->quantity;
            $item->unit_cost    = $product->unit_cost;
            $item->grn_cost     = $product->grn_cost;
            $item->sale_price   = $product->sale_price;
            $item->hire_price    = $product->hire_price;
            $item->received_qty = $product->received_qty;
            $item->amount       = $product->amount;

            $items[] = $item;
        }

        $purchase_order->items         = $items;

        //Set your logo
        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        if ($purchase_order) {
            $receivedByName = $this->getReceivedBy($purchase_order->created_by);
            $color      = '#' . $settings['bill_color'];
            $font_color = Utility::getFontColor($color);

            return view('grn.templates.' . $settings['bill_template'], compact('purchase_order', 'color', 'settings', 'vendor', 'img', 'font_color', 'receivedByName'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function print($purchase_order_id)
    {
        $settings = Utility::settings();
        $orderId   = Crypt::decrypt($purchase_order_id);

        $purchase_order  = Grn::with('vender', 'branch', 'GrnProducts.products')->where('id', $orderId)->first();
        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $purchase_order->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $vendor = $purchase_order->vender;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach ($purchase_order->GrnProducts as $product) {
            $item           = new \stdClass();
            $item->name     = !empty($product->products) ? $product->products->name : '';
            $item->quantity = $product->quantity;
            $item->unit_cost    = $product->unit_cost;
            $item->grn_cost    = $product->grn_cost;
            $item->sale_price    = $product->sale_price;
            $item->hire_price    = $product->hire_price;
            $item->received_qty    = $product->received_qty;
            $item->amount    = $product->amount;

            $items[] = $item;
        }

        $purchase_order->items         = $items;

        //Set your logo
        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        if ($purchase_order) {
            $receivedByName = $this->getReceivedBy($purchase_order->created_by);
            $print = 1;
            $color      = '#' . $settings['bill_color'];
            $font_color = Utility::getFontColor($color);

            return view('grn.templates.' . $settings['bill_template'], compact('purchase_order', 'print', 'color', 'settings', 'vendor', 'img', 'font_color', 'receivedByName'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function approveHeadOffice($id)
    {
        if (!\Auth::user()->hasRole(['super admin', 'company', 'Admin'])) {
            return redirect()->back()->with('error', __('Only Head Office can approve GRNs.'));
        }

        $grn = Grn::findOrFail($id);
        DB::transaction(function () use ($grn) {
            $grn->approval_status = 'approved';
            $grn->approved_by = \Auth::id();
            $grn->approved_at = now();
            $grn->save();

            $inventoryIds = Inventory::where('grn_id', $grn->id)->pluck('id');
            Inventory::where('grn_id', $grn->id)->update(['status' => 1]);
            if ($inventoryIds->isNotEmpty()) {
                StockTransaction::whereIn('inventory_id', $inventoryIds)->update(['status' => 1]);
            }
            \App\AccountTransaction::where('grn_id', $grn->id)->update(['status' => 1]);
        });

        return redirect()->back()->with('success', __('GRN approved by Head Office and stock activated.'));
    }

    public function rejectHeadOffice($id)
    {
        if (!\Auth::user()->hasRole(['super admin', 'company', 'Admin'])) {
            return redirect()->back()->with('error', __('Only Head Office can reject GRNs.'));
        }

        $grn = Grn::findOrFail($id);
        DB::transaction(function () use ($grn) {
            $grn->approval_status = 'rejected';
            $grn->approved_by = \Auth::id();
            $grn->approved_at = now();
            $grn->save();

            $inventoryIds = Inventory::where('grn_id', $grn->id)->pluck('id');
            Inventory::where('grn_id', $grn->id)->update(['status' => 0]);
            if ($inventoryIds->isNotEmpty()) {
                StockTransaction::whereIn('inventory_id', $inventoryIds)->update(['status' => 0]);
            }
            Utility::deleteAccountGrnTransaction($grn->id);
        });

        return redirect()->back()->with('success', __('GRN rejected by Head Office.'));
    }

    public function getReceivedBy($userId)
    {
        $dataReceiveBy = DB::table('users')->select('name')->where('id', $userId)->first();
        return $dataReceiveBy->name;
    }


    public function getSerialWarranty(Request $request)
    {
        $productId = $request->get('product_id');
        // Fetch the warranty and serial data from the database
        $warrantySerials = ProductWarrantySerial::where('grn_product_id', $productId)->get();
        // Return the data as JSON response
        return response()->json($warrantySerials);
    }

    public function updateSerialWarranty(Request $request)
    {
        $serialNumbers = $request->get('serial_numbers');
        $warrantyNumbers = $request->get('warranty_numbers');
        $serialIds = $request->get('serial_ids');

        // Loop through each serial number and update the corresponding record
        foreach ($serialIds as $index => $id) {
            $warrantySerial = ProductWarrantySerial::find($id);
            if ($warrantySerial) {
                $warrantySerial->serial_number = $serialNumbers[$index];
                $warrantySerial->warranty_number = $warrantyNumbers[$index];
                $warrantySerial->save();
            }
        }

        return response()->json(['success' => true]);
    }
}

// old functions if need

    // public function edit($id)
    // {
    //     if (\Auth::user()->can('edit grn')) {
    //         $grn_number = \Auth::user()->grnNumberFormat($id);
    //         $grns = Grn::with([
    //             'purchOrder.purchaseProducts',
    //             'GrnProducts.products',
    //             'GrnProducts.productWarrantySerial' // Load the serial & warranty data
    //         ])->find($id);
    //         $venders = Vender::orderBy('name')->get()->pluck('name', 'id');
    //         $product_services = Product::orderBy('name')->where('status', 1)->get()->pluck('name', 'id');

    //         return view('grn.edit', compact('grn_number', 'grns', 'venders', 'product_services'));
    //     } else {
    //         return redirect()->back()->with('error', __('Permission Denied.'));
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     dd($request->all());
    //     // Find the existing GRN by its ID
    //     $grn = Grn::find($id);

    //     // Check if the GRN exists
    //     if (!$grn) {
    //         return redirect()->back()->with('error', __('GRN not found.'));
    //     }

    //     // Validate the incoming request
    //     $validator = \Validator::make(
    //         $request->all(),
    //         [
    //             'vender_id' => 'required',
    //             'supplier_invoice_number' => 'required',
    //             'items' => 'required|array',
    //         ]
    //     );

    //     // If validation fails, return with error messages
    //     if ($validator->fails()) {
    //         $messages = $validator->getMessageBag();
    //         return redirect()->route('grn.index')->with('error', $messages->first());
    //     }

    //     // Prevent updates if the product has been sold
    //     $checkInventories = Inventory::where('grn_id', $id)->get();
    //     foreach ($checkInventories as $checkInventory) {
    //         if ($checkInventory->grn_qty != $checkInventory->remain_qty) {
    //             return redirect()->route('grn.index')->with('error', __("Could not proceed! Some products have been already sold."));
    //         }
    //     }

    //     // Update the basic GRN information
    //     $grn->vender_id = $request->vender_id;
    //     $grn->order_date = $request->order_date;
    //     $grn->status = $request->status;
    //     $grn->required_by = $request->required_by;
    //     $grn->supplier_invoice_number = $request->supplier_invoice_number;
    //     $grn->total_amount = $request->total_amount;
    //     $grn->update();

    //     // Delete existing GRNProducts and Inventory entries for the current GRN
    //     GrnProduct::where('grn_id', $id)->delete();
    //     Inventory::where('grn_id', $id)->delete();

    //     // Handle the updated items and create new GRNProduct and Inventory entries
    //     $products = $request->items;

    //     foreach ($products as $productData) {
    //         $amount = $productData['receive_quantity'] * $productData['grn_cost'];

    //         // Create new GRNProduct entries
    //         $grnProduct = new GrnProduct();
    //         $grnProduct->grn_id = $id;
    //         $grnProduct->product_id = $productData['item'];
    //         $grnProduct->quantity = $productData['quantity'];
    //         $grnProduct->received_qty = $productData['receive_quantity'];
    //         $grnProduct->unit_cost = $productData['unit_cost'];
    //         $grnProduct->grn_cost = $productData['grn_cost'];
    //         $grnProduct->sale_price = $productData['sale_price'];
    //         $grnProduct->hire_price = $productData['hire_price'];
    //         $grnProduct->amount = $amount;
    //         $grnProduct->save();

    //         // Create new Inventory entries
    //         $inventory = new Inventory();
    //         $inventory->grn_id = $id;
    //         $inventory->product_id = $productData['item'];
    //         $inventory->branch_id = \Auth::user()->branchId();
    //         $inventory->grn_qty = $productData['receive_quantity'];
    //         $inventory->date = $request->order_date;
    //         $inventory->remain_qty = $productData['receive_quantity'];
    //         $inventory->purchase_price = $productData['grn_cost'];
    //         $inventory->sale_price = $productData['sale_price'];
    //         $inventory->hire_price = $productData['hire_price'];
    //         $inventory->supplier_invoice_number = $request->supplier_invoice_number;
    //         $inventory->status = 1;
    //         $inventory->save();

    //        // Process serial and warranty data
    //         if ($request->has('serial_warranty_data')) {
    //             $serialWarrantyData = json_decode($request->serial_warranty_data, true);

    //             if (isset($serialWarrantyData['product_' . $products[$i]['id']][$j])) {
    //                 $data = $serialWarrantyData['product_' . $products[$i]['id']][$j];

    //                 if (isset($data['serial']) && isset($data['warranty'])) {
    //                     $warrantySerial = new ProductWarrantySerial();
    //                     $warrantySerial->grn_product_id = $grnProduct->id;
    //                     $warrantySerial->serial_number = $data['serial'];
    //                     $warrantySerial->warranty_number = $data['warranty'];
    //                     $warrantySerial->status = 0; // Assuming active status
    //                     $warrantySerial->save();
    //                 }
    //             }
    //         }

    //         // Create StockTransaction entries
    //         $stockTransaction = new StockTransaction();
    //         $stockTransaction->stock_master_id = 1;
    //         $stockTransaction->inventory_id = $inventory->id;
    //         $stockTransaction->product_id = $productData['item'];
    //         $stockTransaction->quantity = $productData['receive_quantity'];
    //         $stockTransaction->date = $request->order_date;
    //         $stockTransaction->branch_id = \Auth::user()->branchId();
    //         $stockTransaction->created_by = \Auth::user()->id;
    //         $stockTransaction->status = 1;
    //         $stockTransaction->save();

    //         // Handle Account Transactions (debit/credit)
    //         $productDetail = Product::find($productData['item']);
    //         $productCategory = ProductCategory::find($productDetail->product_category_id);

    //         Utility::accountGrnTransaction(
    //             $request->required_by,
    //             $productCategory->inventory_chart_of_account_id,
    //             208,
    //             $id,
    //             $productData['item'],
    //             $request->vender_id,
    //             10,
    //             $amount,
    //             'debit'
    //         );
    //         Utility::accountGrnTransaction(
    //             $request->required_by,
    //             208,
    //             $productCategory->inventory_chart_of_account_id,
    //             $id,
    //             $productData['item'],
    //             $request->vender_id,
    //             10,
    //             $amount,
    //             'credit'
    //         );
    //     }

    //     return redirect()->route('grn.show', [$id])->with('success', __('GRN successfully updated.'));
    // }


    //     public function update(Request $request, $id)
    // {
    //     $grn = Grn::find($id);
    //     if (\Auth::user()->can('edit grn')) {
    //         if ($grn->branch_id == \Auth::user()->branchId()) {
    //             $validator = \Validator::make(
    //                 $request->all(),
    //                 [
    //                     'vender_id' => 'required',
    //                     'supplier_invoice_number' => 'required',
    //                     'items' => 'required',
    //                 ]
    //             );
    //             if ($validator->fails()) {
    //                 $messages = $validator->getMessageBag();
    //                 return redirect()->route('grn.index')->with('error', $messages->first());
    //             }

    //             // Check if any products have been sold, so we cannot update them
    //             $checkInventories = Inventory::where('grn_id', $id)->get();
    //             foreach ($checkInventories as $checkInventory) {
    //                 if ($checkInventory->grn_qty != $checkInventory->remain_qty) {
    //                     return redirect()->route('grn.index')->with('error', __("Some products have already been sold, so the GRN cannot be modified."));
    //                 }
    //             }

    //             // Update GRN
    //             $grn->vender_id = $request->vender_id;
    //             $grn->order_date = $request->order_date;
    //             $grn->status = $request->status;
    //             $grn->required_by = $request->required_by;
    //             $grn->supplier_invoice_number = $request->supplier_invoice_number;
    //             $grn->total_amount = $request->total_amount;
    //             $grn->created_by = \Auth::user()->id;
    //             $grn->update();

    //             // Preserve serial and warranty data by looping through existing GRN products
    //             $existingGrnProducts = GrnProduct::where('grn_id', $id)->get();
    //             foreach ($existingGrnProducts as $existingProduct) {
    //                 $existingProduct->delete(); // Clear the old products and serial warranty data
    //             }

    //             $products = $request->items;
    //             $serialWarrantyData = json_decode($request->serial_warranty_data, true);

    //             foreach ($products as $productData) {
    //                 $amount = $productData['receive_quantity'] * $productData['grn_cost'];

    //                 // Create a new GRNProduct
    //                 $grnProduct = new GrnProduct();
    //                 $grnProduct->grn_id = $id;
    //                 $grnProduct->product_id = $productData['item'];
    //                 $grnProduct->quantity = $productData['quantity'];
    //                 $grnProduct->received_qty = $productData['receive_quantity'];
    //                 $grnProduct->unit_cost = $productData['unit_cost'];
    //                 $grnProduct->grn_cost = $productData['grn_cost'];
    //                 $grnProduct->sale_price = $productData['sale_price'];
    //                 $grnProduct->hire_price = $productData['hire_price'];
    //                 $grnProduct->amount = $amount;
    //                 $grnProduct->save();

    //                 // Create a new Inventory record
    //                 $inventory = new Inventory();
    //                 $inventory->grn_id = $id;
    //                 $inventory->product_id = $productData['item'];
    //                 $inventory->branch_id = \Auth::user()->branchId();
    //                 $inventory->grn_qty = $productData['receive_quantity'];
    //                 $inventory->date = $request->order_date;
    //                 $inventory->remain_qty = $productData['receive_quantity'];
    //                 $inventory->purchase_price = $productData['grn_cost'];
    //                 $inventory->sale_price = $productData['sale_price'];
    //                 $inventory->supplier_invoice_number = $request->supplier_invoice_number;
    //                 $inventory->status = 0; // Activated upon Head Office approval
    //                 $inventory->save();

    //                 // Handle serial and warranty data
    //                 if (isset($serialWarrantyData['product_' . $productData['item']])) {
    //                     foreach ($serialWarrantyData['product_' . $productData['item']] as $serialWarranty) {
    //                         $warrantySerial = new ProductWarrantySerial();
    //                         $warrantySerial->grn_product_id = $grnProduct->id;
    //                         $warrantySerial->serial_number = $serialWarranty['serial'];
    //                         $warrantySerial->warranty_number = $serialWarranty['warranty'];
    //                         $warrantySerial->status = 0; // Assuming active status
    //                         $warrantySerial->save();
    //                     }
    //                 }
    //             }

    //             return redirect()->route('grn.show', [$id])->with('success', __('GRN successfully updated.'));
    //         } else {
    //             return redirect()->back()->with('error', __('Permission denied.'));
    //         }
    //     } else {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }


    // public function update(Request $request, $id)
    // {
    //     $grn = Grn::find($id);
    //     if (\Auth::user()->can('edit grn')) {
    //         // Validation and other update logic...

    //         // Remove existing GRN Products and Inventory only if they are completely being replaced
    //         $checkInventries = Inventory::where('grn_id', $id)->get();
    //         if ($checkInventries != null) {
    //             foreach ($checkInventries as $checkInventry) {
    //                 if ($checkInventry->grn_qty != $checkInventry->remain_qty) {
    //                     return redirect()->route('grn.index')->with('error', __("Could not Proceed! Some products have been already sold."));
    //                 }
    //             }
    //         }

    //         $grn->vender_id = $request->vender_id;
    //         $grn->order_date = $request->order_date;
    //         $grn->status = $request->status;
    //         $grn->required_by = $request->required_by;
    //         $grn->supplier_invoice_number = $request->supplier_invoice_number;
    //         $grn->total_amount = $request->total_amount;
    //         $grn->created_by = \Auth::user()->id;
    //         $grn->update();

    //         $products = $request->items;
    //         $serialWarrantyData = json_decode($request->serial_warranty_data, true);

    //         // Handle GRN products and update
    //         foreach ($products as $product) {
    //             $amount = $product['receive_quantity'] * $product['grn_cost'];

    //             // Create new GrnProduct or update the existing one
    //             $grnProduct = GrnProduct::where('grn_id', $id)->where('product_id', $product['item'])->first();
    //             if (!$grnProduct) {
    //                 $grnProduct = new GrnProduct(); // If not found, create new
    //             }

    //             $grnProduct->grn_id = $id;
    //             $grnProduct->product_id = $product['item'];
    //             $grnProduct->quantity = $product['quantity'];
    //             $grnProduct->received_qty = $product['receive_quantity'];
    //             $grnProduct->unit_cost = $product['unit_cost'];
    //             $grnProduct->grn_cost = $product['grn_cost'];
    //             $grnProduct->sale_price = $product['sale_price'];
    //             $grnProduct->hire_price = $product['hire_price'];
    //             $grnProduct->amount = $amount;
    //             $grnProduct->save();

    //             // Handle serial and warranty numbers
    //             if (isset($serialWarrantyData['product_' . $product['item']])) {
    //                 foreach ($serialWarrantyData['product_' . $product['item']] as $serialWarranty) {
    //                     if (isset($serialWarranty['serial']) && isset($serialWarranty['warranty'])) {
    //                         $warrantySerial = ProductWarrantySerial::where('grn_product_id', $grnProduct->id)
    //                             ->where('serial_number', $serialWarranty['serial'])
    //                             ->first();
    //                         if (!$warrantySerial) {
    //                             $warrantySerial = new ProductWarrantySerial();
    //                             $warrantySerial->grn_product_id = $grnProduct->id;
    //                         }

    //                         // Update or create the serial and warranty number
    //                         $warrantySerial->serial_number = $serialWarranty['serial'];
    //                         $warrantySerial->warranty_number = $serialWarranty['warranty'];
    //                         $warrantySerial->status = 0;  // Active status
    //                         $warrantySerial->save();
    //                     }
    //                 }
    //             }
    //         }

    //         // Return to the GRN view page with a success message
    //         return redirect()->route('grn.show', [$id])->with('success', __('GRN successfully updated.'));
    //     } else {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }



    // public function update(Request $request, $id)
    // {
    //     $grn = Grn::find($id);
    //     if (\Auth::user()->can('edit grn')) {

    //         if ($grn->branch_id == \Auth::user()->branchId()) {
    //             $validator = \Validator::make(
    //                 $request->all(),
    //                 [
    //                     'vender_id' => 'required',
    //                     'supplier_invoice_number' => 'required',
    //                     'items' => 'required',
    //                 ]
    //             );
    //             if ($validator->fails()) {
    //                 $messages = $validator->getMessageBag();

    //                 return redirect()->route('grn.index')->with('error', $messages->first());
    //             }

    //             $checkInventries = Inventory::where('grn_id', $id)->get();
    //             if ($checkInventries != null) {
    //                 foreach ($checkInventries as $checkInventry) {
    //                     if ($checkInventry->grn_qty != $checkInventry->remain_qty) {
    //                         return redirect()->route('grn.index')->with('error', __("Could not Proceed! Some products have been already sold."));
    //                     }
    //                 }
    //             }

    //             $grn->vender_id = $request->vender_id;
    //             $grn->order_date      = $request->order_date;
    //             $grn->status         = $request->status;
    //             $grn->required_by       = $request->required_by;
    //             $grn->supplier_invoice_number       = $request->supplier_invoice_number;
    //             $grn->total_amount    = $request->total_amount;
    //             $grn->created_by     = \Auth::user()->id;
    //             // $purchase->discount_apply = isset($request->discount_apply) ? 1 : 0;
    //             $grn->update();
    //             CustomField::saveData($grn, $request->customField);
    //             $products = $request->items;

    //             $removeGrnProducts = GrnProduct::where('grn_id', $id)->get();
    //             if ($removeGrnProducts != null) {
    //                 foreach ($removeGrnProducts as $removeProducts) {
    //                     $removeProducts->delete();
    //                 }
    //             }

    //             $removeInventries = Inventory::where('grn_id', $id)->get();
    //             if ($removeInventries != null) {
    //                 foreach ($removeInventries as $removeInventry) {
    //                     $stkTrans = StockTransaction::where('inventory_id', $removeInventry->id)->where('status', 1)->first();
    //                     $stkTrans->status = 0;
    //                     $stkTrans->update();
    //                     $removeInventry->delete();
    //                 }
    //             }

    //             // Delete Account transactions
    //             Utility::deleteAccountGrnTransaction($id);

    //             for ($i = 0; $i < count($products); $i++) {
    //                 // $grnProduct = GrnProduct::find($products[$i]['id']);
    //                 // if($grnProduct == null)
    //                 // {
    //                 //     $grnProduct                 = new GrnProduct();
    //                 //     $grnProduct->grn_id    = $grn->id;
    //                 //     $grnProduct->product_id = $products[$i]['item'];
    //                 // }

    //                 // if(isset($products[$i]['item']))
    //                 // {
    //                 //     $grnProduct->product_id = $products[$i]['item'];
    //                 // }

    //                 // $grnProduct->quantity    = $products[$i]['quantity'];
    //                 // // $purchaseProduct->tax         = $products[$i]['tax'];
    //                 // // $purchaseProduct->discount    = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
    //                 // $grnProduct->price       = $products[$i]['price'];

    //                 // $grnProduct->save();
    //                 $amount = $products[$i]['receive_quantity'] * $products[$i]['grn_cost'];

    //                 $grnProduct              = new GrnProduct();
    //                 // $grnProduct->purchase_order_id     = $newGrn->id;
    //                 $grnProduct->grn_id     = $id;
    //                 $grnProduct->product_id  = $products[$i]['item'];
    //                 $grnProduct->quantity    = $products[$i]['quantity'];
    //                 $grnProduct->received_qty    = $products[$i]['receive_quantity'];
    //                 $grnProduct->unit_cost       = $products[$i]['unit_cost'];
    //                 $grnProduct->grn_cost       = $products[$i]['grn_cost'];
    //                 $grnProduct->sale_price       = $products[$i]['sale_price'];
    //                 $grnProduct->hire_price = $products[$i]['hire_price'];
    //                 $grnProduct->amount       = $amount;
    //                 $grnProduct->save();

    //                 $inventory = new Inventory();
    //                 $inventory->grn_id = $id;
    //                 $inventory->product_id = $products[$i]['item'];
    //                 $inventory->branch_id = \Auth::user()->branchId();
    //                 $inventory->grn_qty = $products[$i]['receive_quantity'];
    //                 $inventory->date = $request->order_date;
    //                 $inventory->remain_qty = $products[$i]['receive_quantity'];
    //                 $inventory->purchase_price = $products[$i]['grn_cost'];
    //                 $inventory->sale_price = $products[$i]['sale_price'];
    //                 $inventory->supplier_invoice_number = $request->supplier_invoice_number;
    //                 $inventory->status = 0; // Activated upon Head Office approval
    //                 $inventory->save();

    //                 $stockTransaction = new StockTransaction();
    //                 $stockTransaction->stock_master_id = 1;
    //                 $stockTransaction->inventory_id = $inventory->id;
    //                 $stockTransaction->product_id = $products[$i]['item'];
    //                 $stockTransaction->quantity = $products[$i]['receive_quantity'];
    //                 $stockTransaction->date = $request->order_date;
    //                 $stockTransaction->branch_id = \Auth::user()->branchId();
    //                 $stockTransaction->created_by     = \Auth::user()->id;
    //                 $stockTransaction->status     = 0; // Activated upon Head Office approval
    //                 $stockTransaction->save();

    //                 // Account Transactions
    //                 $productDetail = Product::find($products[$i]['item']);
    //                 $productCategory = ProductCategory::find($productDetail->product_category_id);

    //                 Utility::accountGrnTransaction(
    //                     $request->required_by,
    //                     $productCategory->inventory_chart_of_account_id,
    //                     208,
    //                     $id,
    //                     $products[$i]['item'],
    //                     $request->vender_id,
    //                     10,
    //                     $amount,
    //                     'debit'
    //                 );
    //                 Utility::accountGrnTransaction(
    //                     $request->required_by,
    //                     208,
    //                     $productCategory->inventory_chart_of_account_id,
    //                     $id,
    //                     $products[$i]['item'],
    //                     $request->vender_id,
    //                     10,
    //                     $amount,
    //                     'credit'
    //                 );

    //                 // if($productDetail->product_category_id == 1){
    //                 //     Utility::accountGrnTransaction( $request->required_by, 175, 316, $id, $products[$i]['item'], $request->vender_id, 10,
    //                 //                             $amount, 'debit');
    //                 //     Utility::accountGrnTransaction($request->required_by, 316, 175, $id, $products[$i]['item'], $request->vender_id, 10,
    //                 //                             $amount, 'credit');
    //                 // } elseif($productDetail->product_category_id == 2){
    //                 //     Utility::accountGrnTransaction( $request->required_by, 174, 316, $id, $products[$i]['item'], $request->vender_id, 10,
    //                 //                             $amount, 'debit');
    //                 //     Utility::accountGrnTransaction($request->required_by, 316, 174, $id, $products[$i]['item'], $request->vender_id, 10,
    //                 //                             $amount, 'credit');
    //                 // } elseif($productDetail->product_category_id == 3){
    //                 //     Utility::accountGrnTransaction( $request->required_by, 176, 316, $id, $products[$i]['item'], $request->vender_id, 10,
    //                 //                             $amount, 'debit');
    //                 //     Utility::accountGrnTransaction($request->required_by, 316, 176, $id, $products[$i]['item'], $request->vender_id, 10,
    //                 //                             $amount, 'credit');
    //                 // }
    //             }
    //             // return redirect()->back()->with('success', __('Grn successfully updated.'));
    //             return redirect()->route('grn.show', [$id])->with('success', __('Grn successfully updated.'));
    //         } else {
    //             return redirect()->back()->with('error', __('Permission denied.'));
    //         }
    //     } else {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     $grn = Grn::find($id);

    //     if (\Auth::user()->can('edit grn')) {
    //         if ($grn->branch_id == \Auth::user()->branchId()) {
    //             // Validate incoming data
    //             $validator = \Validator::make(
    //                 $request->all(),
    //                 [
    //                     'vender_id' => 'required',
    //                     'supplier_invoice_number' => 'required',
    //                     'items' => 'required',
    //                 ]
    //             );

    //             if ($validator->fails()) {
    //                 $messages = $validator->getMessageBag();
    //                 return redirect()->route('grn.index')->with('error', $messages->first());
    //             }

    //             // Check if any products have been sold and cannot be modified
    //             $checkInventories = Inventory::where('grn_id', $id)->get();
    //             foreach ($checkInventories as $checkInventory) {
    //                 if ($checkInventory->grn_qty != $checkInventory->remain_qty) {
    //                     return redirect()->route('grn.index')->with('error', __("Some products have already been sold, so the GRN cannot be modified."));
    //                 }
    //             }

    //             // Update GRN details
    //             $grn->vender_id = $request->vender_id;
    //             $grn->order_date = $request->order_date;
    //             $grn->status = $request->status;
    //             $grn->required_by = $request->required_by;
    //             $grn->supplier_invoice_number = $request->supplier_invoice_number;
    //             $grn->total_amount = $request->total_amount;
    //             $grn->created_by = \Auth::user()->id;
    //             $grn->update();

    //             // Delete old GRN products, inventory, and transactions
    //             GrnProduct::where('grn_id', $id)->delete();
    //             Inventory::where('grn_id', $id)->delete();
    //             Utility::deleteAccountGrnTransaction($id);

    //             // Handle new GRN products and inventory
    //             $products = $request->items;
    //             $serialWarrantyData = json_decode($request->serial_warranty_data, true);

    //             foreach ($products as $productData) {
    //                 $amount = $productData['receive_quantity'] * $productData['grn_cost'];

    //                 // Create a new GRNProduct
    //                 $grnProduct = new GrnProduct();
    //                 $grnProduct->grn_id = $id;
    //                 $grnProduct->product_id = $productData['item'];
    //                 $grnProduct->quantity = $productData['quantity'];
    //                 $grnProduct->received_qty = $productData['receive_quantity'];
    //                 $grnProduct->unit_cost = $productData['unit_cost'];
    //                 $grnProduct->grn_cost = $productData['grn_cost'];
    //                 $grnProduct->sale_price = $productData['sale_price'];
    //                 $grnProduct->hire_price = $productData['hire_price'];
    //                 $grnProduct->amount = $amount;
    //                 $grnProduct->save();

    //                 // Create a new Inventory record
    //                 $inventory = new Inventory();
    //                 $inventory->grn_id = $id;
    //                 $inventory->product_id = $productData['item'];
    //                 $inventory->branch_id = \Auth::user()->branchId();
    //                 $inventory->grn_qty = $productData['receive_quantity'];
    //                 $inventory->date = $request->order_date;
    //                 $inventory->remain_qty = $productData['receive_quantity'];
    //                 $inventory->purchase_price = $productData['grn_cost'];
    //                 $inventory->sale_price = $productData['sale_price'];
    //                 $inventory->supplier_invoice_number = $request->supplier_invoice_number;
    //                 $inventory->status = 0; // Activated upon Head Office approval
    //                 $inventory->save();

    //                 // Create a new StockTransaction
    //                 $stockTransaction = new StockTransaction();
    //                 $stockTransaction->stock_master_id = 1;
    //                 $stockTransaction->inventory_id = $inventory->id;
    //                 $stockTransaction->product_id = $productData['item'];
    //                 $stockTransaction->quantity = $productData['receive_quantity'];
    //                 $stockTransaction->date = $request->order_date;
    //                 $stockTransaction->branch_id = \Auth::user()->branchId();
    //                 $stockTransaction->created_by = \Auth::user()->id;
    //                 $stockTransaction->status = 1;
    //                 $stockTransaction->save();

    //                 // Handle Account Transactions
    //                 $productDetail = Product::find($productData['item']);
    //                 $productCategory = ProductCategory::find($productDetail->product_category_id);

    //                 Utility::accountGrnTransaction(
    //                     $request->required_by,
    //                     $productCategory->inventory_chart_of_account_id,
    //                     208,
    //                     $id,
    //                     $productData['item'],
    //                     $request->vender_id,
    //                     10,
    //                     $amount,
    //                     'debit'
    //                 );
    //                 Utility::accountGrnTransaction(
    //                     $request->required_by,
    //                     208,
    //                     $productCategory->inventory_chart_of_account_id,
    //                     $id,
    //                     $productData['item'],
    //                     $request->vender_id,
    //                     10,
    //                     $amount,
    //                     'credit'
    //                 );

    //                 // Handle Serial and Warranty Data
    //                 if (isset($serialWarrantyData[$productData['item']])) {
    //                     foreach ($serialWarrantyData[$productData['item']] as $serialWarranty) {
    //                         $warrantySerial = ProductWarrantySerial::updateOrCreate(
    //                             ['grn_product_id' => $grnProduct->id, 'serial_number' => $serialWarranty['serial']],
    //                             ['warranty_number' => $serialWarranty['warranty'], 'status' => 0] // Assuming status 0 means active
    //                         );
    //                     }
    //                 }
    //             }

    //             return redirect()->route('grn.show', [$id])->with('success', __('GRN successfully updated.'));
    //         } else {
    //             return redirect()->back()->with('error', __('Permission denied.'));
    //         }
    //     } else {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }
