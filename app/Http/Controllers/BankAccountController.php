<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\BillPayment;
use App\InvoicePayment;
use App\Payment;
use App\Revenue;
use App\Setting;
use App\Transaction;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index()
    {
        if (\Auth::user()->can('manage bank account')) {
            // $bankAccounts = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get();
            $bankAccounts = BankAccount::where('branch_id', \Auth::user()->branchId())->get();
            $currencySymbol = $this->currencySymbol;
            return view('bankAccount.index', compact('bankAccounts', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create bank account')) {
            return view('bankAccount.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create bank account')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'bank_holder' => 'required',
                    'bank' => 'required',
                    'account_no' => 'required',
                    'opening_balance' => 'required',
                    'address' => 'required',
                    'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('bank-account.index')->with('error', $messages->first());
            }

            $bankAccount                  = new BankAccount();
            $bankAccount->bank_account_id = $this->bankAccountNumber();
            $bankAccount->branch_id       = \Auth::user()->branchId();
            $bankAccount->bank_holder     = $request->bank_holder;
            $bankAccount->bank            = $request->bank;
            $bankAccount->account_no      = $request->account_no;
            $bankAccount->opening_balance = $request->opening_balance;
            $bankAccount->address         = $request->address;
            $bankAccount->phone           = $request->phone;
            $bankAccount->status          = 1;
            $bankAccount->created_by      = \Auth::user()->authId();
            $bankAccount->save();

            $glAccount = new \App\GeneralLedgerAccount();
            $glAccount->account_name    = $request->bank . ' - ' . $request->account_no;
            $glAccount->account_type_id = 3;
            $glAccount->detail_type_id  = 15;
            $glAccount->description     = $request->bank_holder;
            $glAccount->bank_account_id = $bankAccount->id;
            $glAccount->status          = 1;
            $glAccount->created_by      = \Auth::user()->authId();
            $glAccount->save();

            $bankAccount->chart_of_account_id = $glAccount->id;
            $bankAccount->save();

            return redirect()->route('bank-account.index')->with('success', __('Bank account successfully created !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function bankAccountNumber()
    {
        // $latest = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        $latest = BankAccount::latest()->first();

        if (!$latest) {
            return 1;
        }
        return $latest->id + 1;
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit bank account')) {
            $bankAccount = BankAccount::find($id);
            return view('bankAccount.edit', compact('bankAccount'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, BankAccount $bankAccount)
    {
        if (\Auth::user()->can('edit bank account')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'bank_holder' => 'required',
                    'bank' => 'required',
                    'account_no' => 'required',
                    'opening_balance' => 'required',
                    'address' => 'required',
                    'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('bank-account.index')->with('error', $messages->first());
            }

            $bankAccount->bank_holder     = $request->bank_holder;
            $bankAccount->bank            = $request->bank;
            $bankAccount->account_no      = $request->account_no;
            $bankAccount->opening_balance = $request->opening_balance;
            $bankAccount->address         = $request->address;
            $bankAccount->phone           = $request->phone;
            $bankAccount->created_by      = \Auth::user()->authId();
            $bankAccount->save();

            return redirect()->route('bank-account.index')->with('success', __('Bank account successfully updated !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function block($id)
    {
        if (\Auth::user()->can('block bank account')) {
            $bankAccount = BankAccount::find($id);
            if ($bankAccount->created_by == \Auth::user()->creatorId()) {
                $revenue        = Revenue::where('account_id', $bankAccount->id)->first();
                $invoicePayment = InvoicePayment::where('account_id', $bankAccount->id)->first();
                $transaction    = Transaction::where('account', $bankAccount->id)->first();
                $payment        = Payment::where('account_id', $bankAccount->id)->first();
                $billPayment    = BillPayment::first();

                if (!empty($revenue) && !empty($invoicePayment) && !empty($transaction) && !empty($payment) && !empty($billPayment)) {
                    return redirect()->back()->with('error', __('This bank account is already assigned. So please remove this bank account related data !!!'));
                } else {
                    $bankAccount->status = 0;
                    $bankAccount->update();
                    return redirect()->back()->with('success', __('Bank Account successfully blocked !!!'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function unblock($id)
    {
        if (\Auth::user()->can('block bank account')) {
            $customer = BankAccount::find($id);
            $customer->status = 1;
            $customer->update();
            return redirect()->back()->with('success', __('Bank Account successfully unblocked !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function destroy(BankAccount $bankAccount)
    {
        if (\Auth::user()->can('delete bank account')) {
            if ($bankAccount->created_by == \Auth::user()->creatorId()) {
                $revenue        = Revenue::where('account_id', $bankAccount->id)->first();
                $invoicePayment = InvoicePayment::where('account_id', $bankAccount->id)->first();
                $transaction    = Transaction::where('account', $bankAccount->id)->first();
                $payment        = Payment::where('account_id', $bankAccount->id)->first();
                $billPayment    = BillPayment::first();

                if (!empty($revenue) && !empty($invoicePayment) && !empty($transaction) && !empty($payment) && !empty($billPayment)) {
                    return redirect()->back()->with('error', __('This bank account is already assigned. So please remove this bank account related data !!!'));
                } else {
                    $bankAccount->delete();
                    return redirect()->back()->with('success', __('Bank account successfully deleted !!!'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
