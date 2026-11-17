<?php

namespace App\Http\Controllers;

use App\AdvancePayment;
use App\BankAccount;
use App\CardInterestExpense;
use App\GeneralLedgerAccount;
use App\ChequeBook;
use App\ChequeBookCheque;
use App\Customer;
use App\FinancialEntry;
use App\Loan;
use App\Pawning;
use App\PaymentVoucher;
use App\Service;
use App\Utility;
use App\Vender;
use App\PettyCashFund;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentVoucheController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index()
    {
        if (\Auth::user()->can('manage payment voucher')) {

            $vouchers = PaymentVoucher::with('supplier', 'cheque')->where('branch_id', \Auth::user()->branchId())->whereNotIn('status', [0])->orderBy('id', 'DESC')->get();
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $currencySymbol = $this->currencySymbol;
            return view('paymentVoucher.index', compact('vouchers', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create payment voucher')) {
            $supplier = Vender::orderBy('name')->get()->pluck('name', 'id');
            $supplier->prepend('Select Supplier', '');
            $customers = Customer::where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            $accounts = GeneralLedgerAccount::where('status', 1)->orderBy('account_name')->get()->pluck('account_name', 'id');
            $accounts->prepend('Select Chart Of Account', '');
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');

            $pettyCashFund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
            $pettyCashAccountIds = [207, 230];

            return view('paymentVoucher.create', compact('supplier', 'accounts', 'customers', 'banks', 'pettyCashFund', 'pettyCashAccountIds'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    private function isPettyCashAccount($accountId)
    {
        if ($accountId == 230 || $accountId == 207) {
            return true;
        }
        $account = GeneralLedgerAccount::find($accountId);
        if ($account && ($account->description == '230' || stripos($account->account_name, 'Petty Cash') !== false)) {
            return true;
        }
        return false;
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create payment voucher')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'voucher_date' => 'required',
                    'account' => 'required',
                    'amount' => 'required',
                    'description' => 'required',
                    'bank' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $isPettyCash = $this->isPettyCashAccount($request->account);
            if ($isPettyCash) {
                $pettyCashFund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
                $floatAmount = $pettyCashFund ? (float)$pettyCashFund->fund_amount : 0;
                if ($floatAmount <= 0) {
                    return redirect()->back()->with('error', __('Petty Cash Float is not configured. Please set the Petty Cash Float in Company Settings first.'));
                }
                if ($request->amount > $floatAmount) {
                    return redirect()->back()->with('error', __('The Amount cannot be more than Petty Cash Float (' . number_format($floatAmount, 2) . ').'));
                }
            }

            if ($request->payment_method == 'CHEQ') {
                $book = ChequeBook::where('status', 1)->where('cheque_bank_id', $request->bank)->first();
                if (!isset($book)) {
                    return redirect()->back()->with('error', __('There are no cheques left in this bank!'));
                }
                $cheqBookCheque = ChequeBookCheque::where('status', 1)->where('cheque_book_id', $book->id)->where('id', $request->cheque_book_cheque_id)->first();
                if (!isset($cheqBookCheque)) {
                    return redirect()->back()->with('error', __('There are no cheques left in this bank!'));
                }
            }

            $paymentVoucher             = new PaymentVoucher();
            $paymentVoucher->date    = $request->voucher_date;
            $paymentVoucher->chart_of_account_id    = $request->account;
            $paymentVoucher->payment_method    = $request->payment_method;

            if ($request->payment_method == 'CHEQ') {
                $paymentVoucher->cross_type       = $request->cross_type;
                $paymentVoucher->cheque_number    = $cheqBookCheque->cheque_number;
            }

            $paymentVoucher->pay_type    = $request->pay_type;
            if ($request->pay_type == 'supplier') {
                $paymentVoucher->vender_id    = $request->supplier_id;
            } elseif ($request->pay_type == 'customer') {
                $paymentVoucher->customer_id    = $request->customer_id;
            } elseif ($request->pay_type == 'other') {
                $paymentVoucher->pay_to    = $request->name;
            }
            if (isset($request->loan_id)) {
                $paymentVoucher->loan_id    = $request->loan_id;
                $loan = Loan::find($request->loan_id);
                $loan->bank_id    = $request->bank;
                $loan->update();
            }
            if (isset($request->pawning_id)) {
                $paymentVoucher->pawning_id    = $request->pawning_id;
                $pawning = Pawning::find($request->pawning_id);
                $pawning->bank_id    = $request->bank;
                $pawning->update();
            }
            if (isset($request->advance_id)) {
                $paymentVoucher->advance_id    = $request->advance_id;
            }
            $paymentVoucher->description    = $request->description;
            $paymentVoucher->amount    = $request->amount;
            $paymentVoucher->amount_in_word    = $request->amount_in_word;
            $paymentVoucher->bank_id    = $request->bank;
            if ($request->payment_method == 'CASH') {
                $paymentVoucher->status    = 2;
            } else {
                $paymentVoucher->status    = 1;
            }
            $paymentVoucher->created_by = \Auth::user()->id;
            $paymentVoucher->branch_id = \Auth::user()->branchId();
            $paymentVoucher->save();

            if ($request->pay_type == 'supplier') {
                $paidTo    = $request->supplier_id;
            } elseif ($request->pay_type == 'customer') {
                $paidTo    = $request->customer_id;
            } else {
                $paidTo = null;
            }

            $paymentMethod = $request->payment_method;
            $voucherDate = $request->voucher_date;
            $account = $request->account;
            $amount = $request->amount;
            $backAccount = BankAccount::find($request->bank);
            $backAccountChartId = $backAccount->chart_of_account_id;

            if ($paymentMethod === 'CHEQ') {
                $cheqBookCheque->payment_voucher_id = $paymentVoucher->id;
                $cheqBookCheque->status = 3;
                $cheqBookCheque->update();
            }

            if (isset($request->advance_id)) {
                $advance = AdvancePayment::find($request->advance_id);
                $advance->return_date = $voucherDate;
                $advance->status = 2;
                $advance->update();

                if ($paymentMethod === 'CASH') {
                    Utility::accountAdvancePaymentTransaction($voucherDate, $account, $backAccountChartId, $advance->id, $advance->customer_id, 3, $amount, 'debit');
                    Utility::accountAdvancePaymentTransaction($voucherDate, $backAccountChartId, $account, $advance->id, $advance->customer_id, 3, $amount, 'credit');
                }
            }

            if ($isPettyCash) {
                Utility::addPettyCashFund($amount);
                Utility::accountPettyCashFundTransaction($voucherDate, $account, $backAccountChartId, $amount, $paymentVoucher->id, 'debit');
                Utility::accountPettyCashFundTransaction($voucherDate, $backAccountChartId, $account, $amount, $paymentVoucher->id, 'credit');
            } else if ($paymentMethod === 'CHEQ') {
                if (isset($request->loan_id) || $request->form_type === 'payment') {
                    Utility::accountPaymentVoucherTransaction($voucherDate, $account, $backAccountChartId, $amount, $paymentVoucher->id, $request->pay_type, $paidTo, 'debit');
                    Utility::accountPaymentVoucherTransaction($voucherDate, $backAccountChartId, $account, $amount, $paymentVoucher->id, $request->pay_type, $paidTo, 'credit');
                }
            } else if ($paymentMethod === 'CASH' && !isset($request->pawning_id) && !isset($request->loan_id) && !isset($request->advance_id)) {
                Utility::accountPaymentVoucherTransaction($voucherDate, $account, $backAccountChartId, $amount, $paymentVoucher->id, $request->pay_type, $paidTo, 'debit');
                Utility::accountPaymentVoucherTransaction($voucherDate, $backAccountChartId, $account, $amount, $paymentVoucher->id, $request->pay_type, $paidTo, 'credit');
            }

            if ($paymentMethod === 'CASH') {
                $financialEntry = new FinancialEntry();
                $financialEntry->date = $request->voucher_date;
                $financialEntry->type = 'Payment Voucher';
                $financialEntry->payment_method = 'CASH';
                $financialEntry->customer_id = $request->customer_id ?: null;
                $financialEntry->vendor_id = $request->supplier_id ?: null;
                $financialEntry->payment_voucher_id = $paymentVoucher->id;
                $financialEntry->remarks = $request->description ?: null;
                $financialEntry->amount = $amount;
                $financialEntry->debit_credit = 'credit';
                $financialEntry->entry_type = 'payment_voucher';
                $financialEntry->branch_id = \Auth::user()->branchId();
                $financialEntry->created_by = \Auth::user()->id;
                $financialEntry->save();
            }

            return redirect()->route('payment_voucher.index')->with('success', __('Payment Voucher successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit payment voucher')) {
            // $services   = Service::select('*', \DB::raw("CONCAT(name,' - ',account_no) AS name"))->get()->pluck('name', 'id');
            // $services->prepend('Select Service', '');
            $supplier = Vender::orderBy('name')->get()->pluck('name', 'id');
            $supplier->prepend('Select Supplier', '');
            $customers = Customer::where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            $accounts = GeneralLedgerAccount::where('status', 1)->orderBy('account_name')->get()->pluck('account_name', 'id');
            $accounts->prepend('Select Chart Of Account', '');
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');

            $voucher = PaymentVoucher::with('chequeBookCheque')->find($id);

            return view('paymentVoucher.edit', compact('voucher', 'supplier', 'accounts', 'customers', 'banks'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit payment voucher')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'voucher_date' => 'required',
                    // 'service_id' => 'required',
                    'account' => 'required',
                    // 'payment_method' => 'required',
                    'amount' => 'required',
                    'description' => 'required',
                    'bank' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if ($request->pre_cheque_book_cheque_id != $request->cheque_book_cheque_id) {
                $book = ChequeBook::where('status', 1)->where('cheque_bank_id', $request->bank)->first();
                if (!isset($book)) {
                    return redirect()->back()->with('error', __('There are no cheques left in this bank!'));
                }
                $cheqBookCheque = ChequeBookCheque::where('status', 1)->where('cheque_book_id', $book->id)->where('id', $request->cheque_book_cheque_id)->first();
                if (!isset($cheqBookCheque)) {
                    return redirect()->back()->with('error', __('There are no cheques left in this bank!'));
                }

                $preCheqBookCheque = ChequeBookCheque::find($request->pre_cheque_book_cheque_id);
                $preCheqBookCheque->payment_voucher_id = null;
                $preCheqBookCheque->status = 1;
                $preCheqBookCheque->update();
            }

            $paymentVoucher = PaymentVoucher::find($id);
            $paymentVoucher->date    = $request->voucher_date;
            $paymentVoucher->chart_of_account_id    = $request->account;
            $paymentVoucher->cross_type             = $request->cross_type;

            if ($request->pre_cheque_book_cheque_id != $request->cheque_book_cheque_id) {
                $paymentVoucher->cheque_number    = $cheqBookCheque->cheque_number;
            }

            $paymentVoucher->pay_type    = $request->pay_type;
            if ($request->pay_type == 'supplier') {
                $paymentVoucher->vender_id    = $request->supplier_id;
            } elseif ($request->pay_type == 'customer') {
                $paymentVoucher->customer_id    = $request->customer_id;
            } elseif ($request->pay_type == 'other') {
                $paymentVoucher->pay_to    = $request->name;
            }
            if (isset($request->loan_id)) {
                $paymentVoucher->loan_id    = $request->loan_id;
                $loan = Loan::find($request->loan_id);
                $loan->bank_id    = $request->bank;
                $loan->update();
            }
            if (isset($request->pawning_id)) {
                $paymentVoucher->pawning_id    = $request->pawning_id;
                $pawning = Pawning::find($request->pawning_id);
                $pawning->bank_id    = $request->bank;
                $pawning->update();
            }
            $paymentVoucher->description    = $request->description;
            $paymentVoucher->amount    = $request->amount;
            $paymentVoucher->amount_in_word    = $request->amount_in_word;
            $paymentVoucher->bank_id    = $request->bank;
            $paymentVoucher->update();

            if ($request->pre_cheque_book_cheque_id != $request->cheque_book_cheque_id) {
                $cheqBookCheque->payment_voucher_id = $paymentVoucher->id;
                $cheqBookCheque->status = 3;
                $cheqBookCheque->update();
            }

            return redirect()->back()->with('success', __('Payment Voucher successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete payment voucher')) {
            $paymentVoucher = PaymentVoucher::with('chequeBookCheque')->find($id);
            $paymentVoucher->status = 0;
            $paymentVoucher->update();

            if (isset($paymentVoucher->chequeBookCheque)) {
                $paymentVoucher->chequeBookCheque->payment_voucher_id = null;
                $paymentVoucher->chequeBookCheque->status = 1;
                $paymentVoucher->chequeBookCheque->update();
            }

            if ($this->isPettyCashAccount($paymentVoucher->chart_of_account_id)) {
                Utility::addPettyCashFund(-$paymentVoucher->amount);
                Utility::deleteAccountPaymentVoucherTransaction($paymentVoucher->id);
            }

            if ($paymentVoucher->payment_method == 'CASH') {
                Utility::deleteAccountPaymentVoucherTransaction($paymentVoucher->id);

                $financialEntry = FinancialEntry::where('payment_voucher_id', $paymentVoucher->id)->where('entry_type', 'payment_voucher')->first();
                if ($financialEntry) {
                    $financialEntry->delete();
                }
            }

            return redirect()->back()->with('success', 'Payment Voucher successfully deleted.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function print($id)
    {
        $cheques = PaymentVoucher::with('chequeBookCheque', 'supplier')->where('id', $id)->first();
        $currencySymbol = $this->currencySymbol;
        return view('paymentVoucher.payment_voucher', compact('cheques', 'currencySymbol'));
    }

    public function getPettyCashFund()
    {
        $pettyCashFund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
        $pettyCashAccountIds = [207, 230];

        return response()->json([
            'fund_amount' => $pettyCashFund ? (float)$pettyCashFund->fund_amount : 0,
            'balance_amount' => $pettyCashFund ? (float)$pettyCashFund->balance_amount : 0,
            'petty_cash_account_ids' => $pettyCashAccountIds,
        ]);
    }
}
