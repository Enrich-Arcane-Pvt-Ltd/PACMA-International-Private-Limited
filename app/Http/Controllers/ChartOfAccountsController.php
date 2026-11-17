<?php

namespace App\Http\Controllers;

use App\AccountType;
use App\BankAccount;
use App\DetailType;
use App\GeneralLedgerAccount;
use App\ProductCategory;
use App\ProductCategoryIncomeAccount;
use App\ProductCategoryInventoryAccount;
use Illuminate\Http\Request;

class ChartOfAccountsController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage chart of account'))
        {
            $chartOfAccounts = GeneralLedgerAccount::where('status', 1)->get();
            return view('chart_of_accounts.index', ['chartOfAccounts' => $chartOfAccounts]);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create chart of account'))
        {
            $accountTypes = AccountType::orderBy('account_type_name')->where('status', 1)->get()->pluck('account_type_name', 'id');
            $accountTypes->prepend('-- Select Account Type --', '');

            $detailTypes = collect([]);
            $detailTypes->prepend('-- Select Detail Type --', '');

            $bankAccounts = BankAccount::orderBy('bank')->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->bank . ' - ' . $item->branch->name];
                            });
            $bankAccounts->prepend('-- Select Bank Account --', '');

            return view('chart_of_accounts.create', compact('accountTypes', 'detailTypes', 'bankAccounts'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getDetailType(Request $request)
    {
        $detailTypes = DetailType::where('account_type_id', $request->account_type_id)->orderBy('detail_type_name')->get();
        return response()->json($detailTypes);
    }

    public function getProductCategories(Request $request)
    {
        if ($request->detail_type_id == 6) { // Inventory
            // $productCategories = ProductCategory::where('status', 1)->whereNull('inventory_chart_of_account_id')->get();
            $productCategories = ProductCategory::where('status', 1)
                                ->whereNull('inventory_chart_of_account_id')
                                ->orWhere('inventory_chart_of_account_id', $request->chart_of_account_id)
                                ->get();

            if(isset($request->chart_of_account_id)){
                foreach ($productCategories as $productCategory) {
                    $inventory_chart_of_account = ProductCategoryInventoryAccount::where('product_category_id', $productCategory->id)->where('chart_of_account_id', $request->chart_of_account_id)->first();
                    $productCategory->checked = false;
                    if ($inventory_chart_of_account) {
                        $productCategory->checked = true;
                    }
                }
            }
        }
        else if ($request->detail_type_id == 88) { // Sales of Product Income
            // $productCategories = ProductCategory::where('status', 1)->whereNull('income_chart_of_account_id')->get();
            $productCategories = ProductCategory::where('status', 1)->whereNull('income_chart_of_account_id')->orWhere('income_chart_of_account_id', $request->chart_of_account_id)->get();
        }


        if ($request->detail_type_id == 140) {
            $productCategories = ProductCategory::where('status', 1)->whereNull('inventory_loan_chart_of_account_id')->orWhere('inventory_loan_chart_of_account_id', $request->chart_of_account_id)->get();
            foreach ($productCategories as $productCategory) {
                if($productCategory->inventory_loan_chart_of_account_id == null){
                    $productCategory->checked = false;
                }
                else{
                    $productCategory->checked = true;
                }
            }
        }

        if ($request->detail_type_id == 141) {
            $productCategories = ProductCategory::where('status', 1)->whereNull('inventory_loan_control_chart_of_account_id')->orWhere('inventory_loan_control_chart_of_account_id', $request->chart_of_account_id)->get();
            foreach ($productCategories as $productCategory) {
                if($productCategory->inventory_loan_control_chart_of_account_id == null){
                    $productCategory->checked = false;
                }
                else{
                    $productCategory->checked = true;
                }
            }
        }

        if ($request->detail_type_id == 96) {
            $productCategories = ProductCategory::where('status', 1)->whereNull('dnl_charges_chart_of_account_id')->orWhere('dnl_charges_chart_of_account_id', $request->chart_of_account_id)->get();
            foreach ($productCategories as $productCategory) {
                if($productCategory->dnl_charges_chart_of_account_id == null){
                    $productCategory->checked = false;
                }
                else{
                    $productCategory->checked = true;
                }
            }
        }
        if ($request->detail_type_id == 142) {
            $productCategories = ProductCategory::where('status', 1)->whereNull('installment_collection_control_chart_of_account_id')->orWhere('installment_collection_control_chart_of_account_id', $request->chart_of_account_id)->get();
            foreach ($productCategories as $productCategory) {
                if($productCategory->installment_collection_control_chart_of_account_id == null){
                    $productCategory->checked = false;
                }
                else{
                    $productCategory->checked = true;
                }
            }
        }
        if ($request->detail_type_id == 143){
            $productCategories = ProductCategory::where('status', 1)->whereNull('interest_income_control_chart_of_account_id')->orWhere('interest_income_control_chart_of_account_id', $request->chart_of_account_id)->get();
            foreach ($productCategories as $productCategory) {
                if($productCategory->interest_income_control_chart_of_account_id == null){
                    $productCategory->checked = false;
                }
                else{
                    $productCategory->checked = true;
                }
            }
        }

        return response()->json($productCategories);
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create chart of account'))
        {
            $rules = [
                'account_type_id' => 'required',
                'detail_type_id' => 'required',
                'account_name' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $chartOfAccount = new GeneralLedgerAccount();
            $chartOfAccount->account_name = $request->account_name;
            $chartOfAccount->account_type_id = $request->account_type_id;
            $chartOfAccount->detail_type_id = $request->detail_type_id;
            $chartOfAccount->description = $request->description;
            $chartOfAccount->bank_account_id = $request->bank_account_id ?: null;
            $chartOfAccount->created_by = \Auth::user()->id;
            $chartOfAccount->save();

            if ($request->bank_account_id) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                $bankAccount->chart_of_account_id = $chartOfAccount->id;
                $bankAccount->update();
            }

            if ($request->product_category) {
                foreach ($request->product_category as $categoryId) {
                    $productCategory = ProductCategory::find($categoryId);

                    if($request->account_type_id == 2 && $request->detail_type_id == 6){
                        ProductCategoryInventoryAccount::firstOrCreate([
                            'chart_of_account_id' => $chartOfAccount->id,
                            'product_category_id' => $categoryId
                        ]);
                        $productCategory->inventory_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->account_type_id == 11 && $request->detail_type_id == 88){
                        ProductCategoryIncomeAccount::firstOrCreate([
                            'chart_of_account_id' => $chartOfAccount->id,
                            'product_category_id' => $categoryId
                        ]);
                        $productCategory->income_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->detail_type_id == 140){
                        $productCategory->inventory_loan_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->detail_type_id == 96){
                        $productCategory->dnl_charges_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->detail_type_id == 141){
                        $productCategory->inventory_loan_control_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->detail_type_id == 142){
                        $productCategory->installment_collection_control_chart_of_account_id = $chartOfAccount->id;
                    }
                    if($request->detail_type_id == 143){
                        $productCategory->interest_income_control_chart_of_account_id = $chartOfAccount->id;
                    }


                    $productCategory->update();
                }
            }

            return redirect()->back()->with('success', __('Chart of Account successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit chart of account'))
        {
            $chartOfAccount = GeneralLedgerAccount::find($id);

            $accountTypes = AccountType::orderBy('account_type_name')->where('status', 1)->get()->pluck('account_type_name', 'id');
            $accountTypes->prepend('-- Select Account Type --', '');

            $detailTypes = DetailType::where('account_type_id', $chartOfAccount->account_type_id)->orderBy('detail_type_name')->get();

            $bankAccounts = BankAccount::orderBy('bank')->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->bank . ' - ' . $item->branch->name];
                            });
            $bankAccounts->prepend('-- Select Bank Account --', '');

            $productCategories = ProductCategory::where('status', 1)->whereNull('income_chart_of_account_id')->orWhere('income_chart_of_account_id', $id)->get();

            return view('chart_of_accounts.edit', compact('chartOfAccount', 'accountTypes', 'detailTypes', 'bankAccounts', 'productCategories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit chart of account'))
        {
            $rules = [
                'account_type_id' => 'required',
                'detail_type_id' => 'required',
                'account_name' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $chartOfAccount = GeneralLedgerAccount::find($id);
            $chartOfAccount->account_name = $request->account_name;
            $chartOfAccount->account_type_id = $request->account_type_id;
            $chartOfAccount->detail_type_id = $request->detail_type_id;
            $chartOfAccount->description = $request->description;
            $chartOfAccount->bank_account_id = $request->bank_account_id ?: null;
            $chartOfAccount->created_by = \Auth::user()->id;
            $chartOfAccount->update();

            if ($request->bank_account_id) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                $bankAccount->chart_of_account_id = $chartOfAccount->id;
                $bankAccount->update();
            }



            if ($request->product_category) {
                if($request->account_type_id == 11 && $request->detail_type_id == 88){
                    ProductCategoryInventoryAccount::where('chart_of_account_id', $chartOfAccount->id)
                                                ->delete();
                }

                if($request->detail_type_id == 140){
                    ProductCategory::where('inventory_loan_chart_of_account_id', $chartOfAccount->id)
                                    ->update([
                                        'inventory_loan_chart_of_account_id' => null
                                    ]);
                }

                if($request->detail_type_id == 96){
                    ProductCategory::where('dnl_charges_chart_of_account_id', $chartOfAccount->id)
                                    ->update([
                                        'dnl_charges_chart_of_account_id' => null
                                    ]);
                }
                if($request->detail_type_id == 141){
                    ProductCategory::where('inventory_loan_control_chart_of_account_id', $chartOfAccount->id)
                                    ->update([
                                        'inventory_loan_control_chart_of_account_id' => null
                                    ]);
                }
                if($request->detail_type_id == 142){
                    ProductCategory::where('installment_collection_control_chart_of_account_id', $chartOfAccount->id)
                                    ->update([
                                        'installment_collection_control_chart_of_account_id' => null
                                    ]);
                }
                if($request->detail_type_id == 143){
                    ProductCategory::where('interest_income_control_chart_of_account_id', $chartOfAccount->id)
                                    ->update([
                                        'interest_income_control_chart_of_account_id' => null
                                    ]);
                }

                foreach ($request->product_category as $categoryId) {
                    $productCategory = ProductCategory::find($categoryId);

                    if($request->detail_type_id == 6){


                        ProductCategoryInventoryAccount::firstOrCreate([
                            'chart_of_account_id' => $chartOfAccount->id,
                            'product_category_id' => $categoryId
                        ]);
                        $productCategory->inventory_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->account_type_id == 11 && $request->detail_type_id == 88){
                        ProductCategoryIncomeAccount::firstOrCreate([
                            'chart_of_account_id' => $chartOfAccount->id,
                            'product_category_id' => $categoryId
                        ]);
                        $productCategory->income_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->detail_type_id == 140){
                        $productCategory->inventory_loan_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->detail_type_id == 96){
                        $productCategory->dnl_charges_chart_of_account_id = $chartOfAccount->id;
                    }

                    if($request->detail_type_id == 141){
                        $productCategory->inventory_loan_control_chart_of_account_id = $chartOfAccount->id;
                    }
                    if($request->detail_type_id == 142){
                        $productCategory->installment_collection_control_chart_of_account_id = $chartOfAccount->id;
                    }
                    if($request->detail_type_id == 143){
                        $productCategory->interest_income_control_chart_of_account_id = $chartOfAccount->id;
                    }

                    $productCategory->update();
                }
            }

            return redirect()->back()->with('success', __('Chart of Account successfully updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
