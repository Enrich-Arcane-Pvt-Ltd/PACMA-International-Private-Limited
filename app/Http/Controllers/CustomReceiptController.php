<?php

namespace App\Http\Controllers;

use App\CustomReceipt;
use App\Customer;
use App\BankAccount;
use App\GeneralLedgerAccount;
use App\CardInterestExpense;
use App\FinancialEntry;
use App\Setting;
use App\Utility;
use App\Vender;
use Illuminate\Http\Request;

class CustomReceiptController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }
    public function index()
    {
        if (\Auth::user()->can('manage receipt')) {
            $receipts = CustomReceipt::where('branch_id', \Auth::user()->branchId())->where('status', 1)->orderBy('id', 'DESC')->get();
            $currencySymbol = $this->currencySymbol;
            return view('custom_receipts.index', compact('receipts', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create receipt')) {
            // $customers = Customer::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $supplier = Vender::orderBy('name')->get()->pluck('name', 'id');
            $supplier->prepend('Select Supplier', '');
            $customers = Customer::where('branch_id', \Auth::user()->branchId())->where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $accounts = GeneralLedgerAccount::where('status', 1)->orderBy('account_name')->get()->pluck('account_name', 'id');
            $accounts->prepend('Select Chart Of Account', '');
            return view('custom_receipts.create', compact('customers', 'banks', 'accounts', 'supplier'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create receipt')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'current_date' => 'required',
                    'account' => 'required',
                    'paid_amount' => 'required',
                    'paid_amount_in_words' => 'required',
                    'payment_method' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if ($request->payment_method == 'CARD') {
                if ($request->card_charge_amount != '') {
                    $cardCharge = new CardInterestExpense();
                    $cardCharge->amount = $request->card_charge_amount;
                    $cardCharge->customer_id = $request->customer;
                    $cardCharge->status = 1;
                    $cardCharge->save();
                }
            }

            $maxId = CustomReceipt::max('id');
            $receiptCode = "RCP" . str_pad(($maxId + 1), 6, "0", STR_PAD_LEFT);

            $receipts = new CustomReceipt();
            $receipts->branch_id = \Auth::user()->branchId();
            $receipts->chart_of_account_id = $request->account;
            $receipts->receipt_code = $receiptCode;
            $receipts->issue_date = $request->current_date;
            $receipts->receipt_type = $request->receipt_type;
            if ($request->receipt_type == 'supplier') {
                $receipts->vender_id    = $request->supplier_id;
            } elseif ($request->receipt_type == 'customer') {
                $receipts->customer_id    = $request->customer_id;
            } elseif ($request->receipt_type == 'other') {
                $receipts->receipt_from    = $request->name;
                $receipts->receipt_from_phone    = $request->phone;
            }
            $receipts->paid_amount = $request->paid_amount;
            $receipts->paid_amount_in_words = $request->paid_amount_in_words;
            $receipts->payment_method = $request->payment_method;
            $receipts->bank_id = $request->bank;
            if ($request->payment_method == 'CARD') {
                $receipts->card_type            = $request->card_type;
                if ($request->card_charge_amount != '') {
                    $receipts->card_expence_id      = $cardCharge->id;
                }
                $receipts->card_charge_amount   = $request->card_charge_amount;
                $receipts->total_amount         = $request->total_amount;
            } elseif ($request->payment_method == 'CHEQUE') {
                $receipts->cheque_number        = $request->cheque_number;
                $receipts->cheque_realised_date     = $request->realised_date;
            }
            $receipts->status = 1;
            $receipts->description = $request->description;
            $receipts->created_by = \Auth::user()->id;
            $receipts->save();

            if ($request->receipt_type == 'supplier') {
                $receiptFrom    = $request->supplier_id;
            } elseif ($request->receipt_type == 'customer') {
                $receiptFrom    = $request->customer_id;
            } else {
                $receiptFrom = null;
            }

            // if($request->payment_method == 'CASH'){
            //     Utility::accountReceiptTransaction( $request->current_date, 309, $request->account, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 309, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            // } else if($request->payment_method == 'CARD' || $request->payment_method == 'ONLINEPAYMENT'){
            $backAccount = BankAccount::find($request->bank);
            Utility::accountReceiptTransaction($request->current_date, $backAccount->chart_of_account_id, $request->account, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            Utility::accountReceiptTransaction($request->current_date, $request->account, $backAccount->chart_of_account_id, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');

            // if($request->bank == 1){
            //     Utility::accountReceiptTransaction( $request->current_date, 228, $request->account, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 228, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            // } else if($request->bank == 2){
            //     Utility::accountReceiptTransaction( $request->current_date, 227, $request->account, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 227, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            // } else if($request->bank == 3){
            //     Utility::accountReceiptTransaction( $request->current_date, 229, $request->account, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 229, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            // }

            // } else if($request->payment_method == 'CHEQUE'){
            //     Utility::accountReceiptTransaction( $request->current_date, 309, $request->account, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 309, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');

            //     if($request->bank == 1){
            //         Utility::accountReceiptTransaction( $request->realised_date, 228, 309, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //         Utility::accountReceiptTransaction( $request->realised_date, 309, 228, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            //     } else if($request->bank == 2){
            //         Utility::accountReceiptTransaction( $request->realised_date, 227, 309, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //         Utility::accountReceiptTransaction( $request->realised_date, 309, 227, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            //     } else if($request->bank == 3){
            //         Utility::accountReceiptTransaction( $request->realised_date, 229, 309, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //         Utility::accountReceiptTransaction( $request->realised_date, 309, 229, $receipts->id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            //     }
            // }

            $financialEntry = new FinancialEntry();
            $financialEntry->date = $request->current_date;
            $financialEntry->type = 'Receipts';
            $financialEntry->payment_method = $request->payment_method;
            $financialEntry->customer_id = $request->customer_id ?: null;
            $financialEntry->vendor_id = $request->supplier_id ?: null;
            $financialEntry->custom_receipt_id = $receipts->id;
            $financialEntry->remarks = $request->description ?: null;
            $financialEntry->amount = $request->paid_amount;
            $financialEntry->debit_credit = 'debit';
            $financialEntry->entry_type = 'custom_receipt';
            $financialEntry->branch_id = \Auth::user()->branchId();
            $financialEntry->created_by = \Auth::user()->id;
            $financialEntry->save();

            return redirect()->back()->with('success', __('Receipt Successfully Created!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit receipt')) {
            // $customers = Customer::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $supplier = Vender::orderBy('name')->get()->pluck('name', 'id');
            $supplier->prepend('Select Supplier', '');
            $customers = Customer::where('branch_id', \Auth::user()->branchId())->where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $accounts = GeneralLedgerAccount::where('status', 1)->orderBy('account_name')->get()->pluck('account_name', 'id');
            $accounts->prepend('Select Chart Of Account', '');
            $receipt = CustomReceipt::find($id);
            return view('custom_receipts.edit', compact('customers', 'supplier', 'banks', 'accounts', 'receipt'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request)
    {
        if (\Auth::user()->can('edit receipt')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'current_date' => 'required',
                    'account' => 'required',
                    'paid_amount' => 'required',
                    'paid_amount_in_words' => 'required',
                    'payment_method' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $receipt = CustomReceipt::find($request->receipt_id);

            $checkCardCharge = CardInterestExpense::find($receipt->card_expence_id);
            if ($checkCardCharge) {
                $checkCardCharge->delete();
            }

            if ($request->payment_method == 'CARD') {
                if ($request->card_charge_amount != '') {
                    $cardCharge = new CardInterestExpense();
                    $cardCharge->amount = $request->card_charge_amount;
                    $cardCharge->customer_id = $request->customer;
                    $cardCharge->status = 1;
                    $cardCharge->save();
                }
            }

            $receipts = CustomReceipt::find($request->receipt_id);
            $receipts->chart_of_account_id = $request->account;
            $receipts->issue_date = $request->current_date;
            $receipts->receipt_type = $request->receipt_type;
            if ($request->receipt_type == 'supplier') {
                $receipts->vender_id    = $request->supplier_id;
            } elseif ($request->receipt_type == 'customer') {
                $receipts->customer_id    = $request->customer_id;
            } elseif ($request->receipt_type == 'other') {
                $receipts->receipt_from    = $request->name;
                $receipts->receipt_from_phone    = $request->phone;
            }
            $receipts->paid_amount = $request->paid_amount;
            $receipts->paid_amount_in_words = $request->paid_amount_in_words;
            $receipts->payment_method = $request->payment_method;
            $receipts->bank_id = $request->bank;
            if ($request->payment_method == 'CARD') {
                $receipts->card_type            = $request->card_type;
                if ($request->card_charge_amount != '') {
                    $receipts->card_expence_id      = $cardCharge->id;
                }
                $receipts->card_charge_amount   = $request->card_charge_amount;
                $receipts->total_amount         = $request->total_amount;
            } elseif ($request->payment_method == 'CHEQUE') {
                $receipts->cheque_number        = $request->cheque_number;
                $receipts->cheque_realised_date     = $request->realised_date;
            }
            $receipts->description = $request->description;
            $receipts->update();

            if ($request->receipt_type == 'supplier') {
                $receiptFrom    = $request->supplier_id;
            } elseif ($request->receipt_type == 'customer') {
                $receiptFrom    = $request->customer_id;
            } else {
                $receiptFrom = null;
            }

            Utility::deleteReceiptTransaction($request->receipt_id);

            // if($request->payment_method == 'CASH'){
            //     Utility::accountReceiptTransaction( $request->current_date, 309, $request->account, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 309, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            // } else if($request->payment_method == 'CARD' || $request->payment_method == 'ONLINEPAYMENT'){
            $backAccount = BankAccount::find($request->bank);
            Utility::accountReceiptTransaction($request->current_date, $backAccount->chart_of_account_id, $request->account, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            Utility::accountReceiptTransaction($request->current_date, $request->account, $backAccount->chart_of_account_id, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');

            // if($request->bank == 1){
            //     Utility::accountReceiptTransaction( $request->current_date, 228, $request->account, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 228, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            // } else if($request->bank == 2){
            //     Utility::accountReceiptTransaction( $request->current_date, 227, $request->account, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 227, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            // } else if($request->bank == 3){
            //     Utility::accountReceiptTransaction( $request->current_date, 229, $request->account, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 229, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            // }

            // } else if($request->payment_method == 'CHEQUE'){
            //     Utility::accountReceiptTransaction( $request->current_date, 309, $request->account, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //     Utility::accountReceiptTransaction( $request->current_date, $request->account, 309, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');

            //     if($request->bank == 1){
            //         Utility::accountReceiptTransaction( $request->realised_date, 228, 309, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //         Utility::accountReceiptTransaction( $request->realised_date, 309, 228, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            //     } else if($request->bank == 2){
            //         Utility::accountReceiptTransaction( $request->realised_date, 227, 309, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //         Utility::accountReceiptTransaction( $request->realised_date, 309, 227, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            //     } else if($request->bank == 3){
            //         Utility::accountReceiptTransaction( $request->realised_date, 229, 309, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'debit');
            //         Utility::accountReceiptTransaction( $request->realised_date, 309, 229, $request->receipt_id, $receiptFrom, $request->receipt_type, 17, $request->paid_amount, 'credit');
            //     }
            // }

            $financialEntry = FinancialEntry::where('custom_receipt_id', $receipts->id)->where('entry_type', 'custom_receipt')->first();

            $financialEntry->date = $request->current_date;
            $financialEntry->type = 'Receipts';
            $financialEntry->payment_method = $request->payment_method;
            $financialEntry->customer_id = $request->customer_id ?: null;
            $financialEntry->vendor_id = $request->supplier_id ?: null;
            $financialEntry->custom_receipt_id = $receipts->id;
            $financialEntry->remarks = $request->description ?: null;
            $financialEntry->amount = $request->paid_amount;
            $financialEntry->debit_credit = 'debit';
            $financialEntry->entry_type = 'custom_receipt';
            $financialEntry->branch_id = \Auth::user()->branchId();
            $financialEntry->created_by = \Auth::user()->id;
            $financialEntry->update();

            return redirect()->back()->with('success', __('Receipt Successfully Updated!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function receipt($id, $command = 'view')
    {
        $receipt = CustomReceipt::with(['customer', 'vender'])->find($id);

        $user = CustomReceipt::select('users.name')
            ->join('users', 'users.id', 'custom_receipts.created_by')
            ->where('custom_receipts.id', $id)
            ->first();
        return view('custom_receipts.receipt', compact('receipt', 'user', 'command'));
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete receipt')) {

            $receipts = CustomReceipt::find($id);
            if ($receipts) {
                $receipts->status = 0;
                $receipts->update();
            }

            $checkCardCharge = CardInterestExpense::find($receipts->card_expence_id);
            if ($checkCardCharge) {
                $checkCardCharge->delete();
            }

            $financialEntry = FinancialEntry::where('custom_receipt_id', $receipts->id)->where('entry_type', 'custom_receipt')->first();
            if ($financialEntry) {
                $financialEntry->delete();
            }

            Utility::deleteReceiptTransaction($id);

            return redirect()->back()->with('success', __('Receipt Successfully Deleted!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
