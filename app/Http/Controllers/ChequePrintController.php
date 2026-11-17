<?php

namespace App\Http\Controllers;

use App\AdvancePayment;
use App\Cheque;
use App\BankAccount;
use App\CardInterestExpense;
use App\Vender;
use App\Utility;
use App\ChequeBook;
use App\ChequeBookCheque;
use App\Customer;
use App\FinancialEntry;
use App\Loan;
use App\Pawning;
use App\PaymentVoucher;
use App\Receipt;
use App\Setting;
use Illuminate\Http\Request;

class ChequePrintController extends Controller
{

    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index()
    {
        if (\Auth::user()->can('create cheque')) {
            $cheques = Cheque::with('vender', 'customer', 'account', 'voucher.account', 'paymentVoucher.account')->whereNotIn('status', [9])->where('branch_id', \Auth::user()->branchId())->orderBy('id', 'DESC')->get();
            $currencySymbol = $this->currencySymbol;
            return view('chequePrint.index', compact('cheques', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function create()
    {
        if (\Auth::user()->can('create cheque')) {
            $supplier = Vender::orderBy('name')->get()->pluck('name', 'id');
            $chequeBanks = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $customers = Customer::where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            $vouchers = PaymentVoucher::with('chequeBookCheque')->where('payment_method', 'CHEQ')->where('status', 1)->get();
            return view('chequePrint.create', compact('supplier', 'chequeBanks', 'vouchers', 'customers'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create cheque')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'cross_type' => 'required',
                    'date' => 'required',
                    'pay_type' => 'required',
                    'amount' => 'required|numeric',
                    'amount_in_word' => 'required',
                    'description' => 'required',
                    'bank' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $book = ChequeBook::where('status', 1)->where('cheque_bank_id', $request->bank)->first();
            if (!isset($book)) {
                return redirect()->back()->with('error', __('There are no cheques left in this bank!'));
            }
            $cheqBookCheque = ChequeBookCheque::whereIn('status', [1, 3])->where('cheque_book_id', $book->id)->first();

            $cheque                         = new Cheque();
            $cheque->cheque_id              = $this->chequeNumber();
            $cheque->chart_of_account_id    = $request->chart_of_account_id;
            $cheque->payment_voucher_id     = $request->voucher;

            if (empty($cheque->payment_voucher_id) && !empty($cheqBookCheque->payment_voucher_id)) {
                $cheque->payment_voucher_id = $cheqBookCheque->payment_voucher_id;
            }

            if (empty($cheque->chart_of_account_id) && !empty($cheque->payment_voucher_id)) {
                $linkedVoucher = PaymentVoucher::find($cheque->payment_voucher_id);
                if ($linkedVoucher) {
                    $cheque->chart_of_account_id = $linkedVoucher->chart_of_account_id;
                }
            }

            $cheque->cross_type             = $request->cross_type;
            $cheque->date                   = $request->date;
            $cheque->pay_type               = $request->pay_type;
            if ($request->pay_type == 'supplier') {
                $cheque->vender_id          = $request->supplier;
            } elseif ($request->pay_type == 'other') {
                $cheque->pay          = $request->name;
            } elseif ($request->pay_type == 'customer') {
                $cheque->customer_id          = $request->customer_id;
            }
            // $cheque->pay          = $request->name;
            // if(isset($request->customer)){
            //     $cheque->customer_id           = $request->customer;
            // } elseif(isset($request->supplier)) {
            //     $cheque->vender_id           = $request->supplier;
            // }
            $cheque->description      = $request->description;
            $cheque->amount           = $request->amount;
            $cheque->amount_in_word   = $request->amount_in_word;
            $cheque->cheque_bank_id   = $request->bank;
            $cheque->status           = 1;
            $cheque->cheque_number    = $cheqBookCheque->cheque_number;
            $cheque->status           = 1;
            $cheque->created_by       = \Auth::user()->id;
            $cheque->branch_id = \Auth::user()->branchId();
            $cheque->save();

            $cheqBookCheque->cheque_id = $cheque->id;
            $cheqBookCheque->status = 0;
            $cheqBookCheque->update();

            if ($cheqBookCheque->cheque_number == $book->end_at) {
                $book->status = 0;
                $book->update();
            }

            if ($request->voucher != '') {
                $voucher = PaymentVoucher::find($request->voucher);
                $voucher->cheque_id = $cheque->id;
                $voucher->status = 2;
                $voucher->update();

                if (empty($cheque->chart_of_account_id) && !empty($voucher->chart_of_account_id)) {
                    $cheque->chart_of_account_id = $voucher->chart_of_account_id;
                    $cheque->update();
                }
            }
            if ($request->loan_id != '') {
                $loan = Loan::find($request->loan_id);
                $loan->cheque_id = $cheque->id;
                $loan->update();

                $recMaxId = Receipt::max('id');
                $receiptCode = "DOC" . str_pad(($recMaxId + 1), 6, "0", STR_PAD_LEFT);

                $receipts = new Receipt();
                $receipts->branch_id = \Auth::user()->branchId();
                $receipts->receipt_code = $receiptCode;
                $receipts->customer_id = $loan->customer_id;
                $receipts->loan_id = $request->loan_id;
                $receipts->issue_date = $request->date;
                $receipts->installment_due_date = $request->date;
                $receipts->status = 1;
                $receipts->total_amount_to_pay = $loan->document_charges;
                $receipts->paid_amount = $loan->document_charges;
                $receipts->paid_amount_in_words = $loan->document_charges_in_words;
                $receipts->payment_method = 'CASH';
                $receipts->description = 'Document Charges';
                $receipts->created_by = \Auth::user()->id;
                $receipts->save();

                $backAccount = BankAccount::find($request->bank);
                // Abimani
                if ($request->chart_of_account_id == 252) {
                    // if($request->bank == 1){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 228, $request->voucher, $loan->id, $loan->customer_id, 5, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 228, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 5, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 228, 12, $request->voucher, $loan->id, $loan->customer_id, 5, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 12, 228, $request->voucher, $loan->id, $loan->customer_id, 5, $loan->document_charges, 'credit');
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 227, $request->voucher, $loan->id, $loan->customer_id, 5, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 227, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 5, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 227, 12, $request->voucher, $loan->id, $loan->customer_id, 5, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 12, 227, $request->voucher, $loan->id, $loan->customer_id, 5, $loan->document_charges, 'credit');
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 229, $request->voucher, $loan->id, $loan->customer_id, 5, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 229, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 5, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 229, 12, $request->voucher, $loan->id, $loan->customer_id, 5, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 12, 229, $request->voucher, $loan->id, $loan->customer_id, 5, $loan->document_charges, 'credit');
                    // }

                    // Utility::accountLoanTransaction($request->date, 166, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 5, $request->amount, 'debit');
                    // Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 166, $request->voucher, $loan->id, $loan->customer_id, 5, $request->amount, 'credit');
                }
                // SME
                elseif ($request->chart_of_account_id == 15) {
                    Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, $backAccount->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'debit');
                    Utility::accountLoanTransaction($request->date, $backAccount->chart_of_account_id, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'credit');

                    Utility::accountLoanTransaction($request->date, $backAccount->chart_of_account_id, 24, $request->voucher, $loan->id, $loan->customer_id, 4, $loan->document_charges, 'debit');
                    Utility::accountLoanTransaction($request->date, 24, $backAccount->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 4, $loan->document_charges, 'credit');

                    // if($request->bank == 1){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 228, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 228, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 228, 13, $request->voucher, $loan->id, $loan->customer_id, 4, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 13, 228, $request->voucher, $loan->id, $loan->customer_id, 4, $loan->document_charges, 'credit');
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 227, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 227, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 227, 13, $request->voucher, $loan->id, $loan->customer_id, 4, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 13, 227, $request->voucher, $loan->id, $loan->customer_id, 4, $loan->document_charges, 'credit');
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 229, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 229, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 229, 13, $request->voucher, $loan->id, $loan->customer_id, 4, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 13, 229, $request->voucher, $loan->id, $loan->customer_id, 4, $loan->document_charges, 'credit');
                    // }

                    Utility::accountLoanTransaction($request->date, 19, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'debit');
                    Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 19, $request->voucher, $loan->id, $loan->customer_id, 4, $request->amount, 'credit');
                }
                // Vehicle
                elseif ($request->chart_of_account_id == 16) {
                    Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, $backAccount->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'debit');
                    Utility::accountLoanTransaction($request->date, $backAccount->chart_of_account_id, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'credit');

                    Utility::accountLoanTransaction($request->date, $backAccount->chart_of_account_id, 25, $request->voucher, $loan->id, $loan->customer_id, 6, $loan->document_charges, 'debit');
                    Utility::accountLoanTransaction($request->date, 25, $backAccount->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 6, $loan->document_charges, 'credit');

                    // if($request->bank == 1){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 228, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 228, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 228, 14, $request->voucher, $loan->id, $loan->customer_id, 6, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 14, 228, $request->voucher, $loan->id, $loan->customer_id, 6, $loan->document_charges, 'credit');
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 227, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 227, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 227, 14, $request->voucher, $loan->id, $loan->customer_id, 6, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 14, 227, $request->voucher, $loan->id, $loan->customer_id, 6, $loan->document_charges, 'credit');
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 229, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 229, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 229, 14, $request->voucher, $loan->id, $loan->customer_id, 6, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 14, 229, $request->voucher, $loan->id, $loan->customer_id, 6, $loan->document_charges, 'credit');
                    // }

                    Utility::accountLoanTransaction($request->date, 20, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'debit');
                    Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 20, $request->voucher, $loan->id, $loan->customer_id, 6, $request->amount, 'credit');
                }
                // Land
                elseif ($request->chart_of_account_id == 17) {
                    Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, $backAccount->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'debit');
                    Utility::accountLoanTransaction($request->date, $backAccount->chart_of_account_id, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'credit');

                    Utility::accountLoanTransaction($request->date, $backAccount->chart_of_account_id, 26, $request->voucher, $loan->id, $loan->customer_id, 7, $loan->document_charges, 'debit');
                    Utility::accountLoanTransaction($request->date, 26, $backAccount->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 7, $loan->document_charges, 'credit');

                    // if($request->bank == 1){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 228, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 228, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 228, 15, $request->voucher, $loan->id, $loan->customer_id, 7, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 15, 228, $request->voucher, $loan->id, $loan->customer_id, 7, $loan->document_charges, 'credit');
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 227, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 227, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 227, 15, $request->voucher, $loan->id, $loan->customer_id, 7, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 15, 227, $request->voucher, $loan->id, $loan->customer_id, 7, $loan->document_charges, 'credit');
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 229, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 229, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 229, 15, $request->voucher, $loan->id, $loan->customer_id, 7, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 15, 229, $request->voucher, $loan->id, $loan->customer_id, 7, $loan->document_charges, 'credit');
                    // }

                    Utility::accountLoanTransaction($request->date, 21, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'debit');
                    Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 21, $request->voucher, $loan->id, $loan->customer_id, 7, $request->amount, 'credit');
                }
                // Employment
                elseif ($request->chart_of_account_id == 18) {
                    Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, $backAccount->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'debit');
                    Utility::accountLoanTransaction($request->date, $backAccount->chart_of_account_id, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'credit');

                    Utility::accountLoanTransaction($request->date, $backAccount->chart_of_account_id, 27, $request->voucher, $loan->id, $loan->customer_id, 8, $loan->document_charges, 'debit');
                    Utility::accountLoanTransaction($request->date, 27, $backAccount->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 8, $loan->document_charges, 'credit');

                    // if($request->bank == 1){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 228, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 228, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 228, 16, $request->voucher, $loan->id, $loan->customer_id, 8, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 16, 228, $request->voucher, $loan->id, $loan->customer_id, 8, $loan->document_charges, 'credit');
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 227, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 227, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 227, 16, $request->voucher, $loan->id, $loan->customer_id, 8, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 16, 227, $request->voucher, $loan->id, $loan->customer_id, 8, $loan->document_charges, 'credit');
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 229, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 229, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'credit');

                    //     Utility::accountLoanTransaction($request->date, 229, 16, $request->voucher, $loan->id, $loan->customer_id, 8, $loan->document_charges, 'debit');
                    //     Utility::accountLoanTransaction($request->date, 16, 229, $request->voucher, $loan->id, $loan->customer_id, 8, $loan->document_charges, 'credit');
                    // }

                    Utility::accountLoanTransaction($request->date, 22, $request->chart_of_account_id, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'debit');
                    Utility::accountLoanTransaction($request->date, $request->chart_of_account_id, 22, $request->voucher, $loan->id, $loan->customer_id, 8, $request->amount, 'credit');
                }
            }
            // gold loan
            if ($request->pawning_id != '') {
                $pawning = Pawning::find($request->pawning_id);
                $pawning->cheque_id = $cheque->id;
                $pawning->update();

                Utility::accountPawningTransaction($request->date, $request->chart_of_account_id, $backAccount->chart_of_account_id, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'debit');
                Utility::accountPawningTransaction($request->date, $backAccount->chart_of_account_id, $request->chart_of_account_id, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'credit');


                // if($request->bank == 1){
                //     Utility::accountPawningTransaction($request->date, $request->chart_of_account_id, 228, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'debit');
                //     Utility::accountPawningTransaction($request->date, 228, $request->chart_of_account_id, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'credit');
                // } else if($request->bank == 2){
                //     Utility::accountPawningTransaction($request->date, $request->chart_of_account_id, 227, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'debit');
                //     Utility::accountPawningTransaction($request->date, 227, $request->chart_of_account_id, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'credit');
                // } else if($request->bank == 3){
                //     Utility::accountPawningTransaction($request->date, $request->chart_of_account_id, 229, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'debit');
                //     Utility::accountPawningTransaction($request->date, 229, $request->chart_of_account_id, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'credit');
                // }

                Utility::accountPawningTransaction($request->date, 23, $request->chart_of_account_id, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'debit');
                Utility::accountPawningTransaction($request->date, $request->chart_of_account_id, 23, $request->voucher, $pawning->id, $pawning->customer_id, 9, $request->amount, 'credit');
            }

            if ($request->advance_id != '') {
                $advance = AdvancePayment::find($request->advance_id);

                $checkCardCharge = CardInterestExpense::find($advance->card_expence_id);
                if ($checkCardCharge) {
                    $checkCardCharge->delete();
                }

                // Utility::accountAdvancePaymentTransaction( $request->date, $request->chart_of_account_id, 309, $advance->id, $advance->customer_id, 3, $request->amount, 'debit');
                // Utility::accountAdvancePaymentTransaction( $request->date, 309, $request->chart_of_account_id, $advance->id, $advance->customer_id, 3, $request->amount, 'credit');

                $backAccount = BankAccount::find($request->bank);
                Utility::accountAdvancePaymentTransaction($request->date, $request->chart_of_account_id, $backAccount->chart_of_account_id, $advance->id, $advance->customer_id, 3, $request->amount, 'debit');
                Utility::accountAdvancePaymentTransaction($request->date, $backAccount->chart_of_account_id, $request->chart_of_account_id, $advance->id, $advance->customer_id, 3, $request->amount, 'credit');

                // if($request->bank == 1){
                //     Utility::accountAdvancePaymentTransaction($request->date, $request->chart_of_account_id, 228, $advance->id, $advance->customer_id, 3, $request->amount, 'debit');
                //     Utility::accountAdvancePaymentTransaction($request->date, 228, $request->chart_of_account_id, $advance->id, $advance->customer_id, 3, $request->amount, 'credit');
                // } else if($request->bank == 2){
                //     Utility::accountAdvancePaymentTransaction($request->date, $request->chart_of_account_id, 227, $advance->id, $advance->customer_id, 3, $request->amount, 'debit');
                //     Utility::accountAdvancePaymentTransaction($request->date, 227, $request->chart_of_account_id, $advance->id, $advance->customer_id, 3, $request->amount, 'credit');
                // } else if($request->bank == 3){
                //     Utility::accountAdvancePaymentTransaction($request->date, $request->chart_of_account_id, 229, $advance->id, $advance->customer_id, 3, $request->amount, 'debit');
                //     Utility::accountAdvancePaymentTransaction($request->date, 229, $request->chart_of_account_id, $advance->id, $advance->customer_id, 3, $request->amount, 'credit');
                // }
            }

            if (in_array($request->chart_of_account_id, [207, 230])) {
                Utility::addPettyCashFund($request->amount);
                $backAccount = BankAccount::find($request->bank);
                Utility::accountPettyCashFundTransaction($request->date, $request->chart_of_account_id, $backAccount->chart_of_account_id, $request->amount, $request->voucher, 'debit');
                Utility::accountPettyCashFundTransaction($request->date, $backAccount->chart_of_account_id, $request->chart_of_account_id, $request->amount, $request->voucher, 'credit');


                // if($request->bank == 1){
                //     Utility::accountPettyCashFundTransaction($request->date, $request->chart_of_account_id, 228, $request->amount, $request->voucher, 'debit');
                //     Utility::accountPettyCashFundTransaction($request->date, 228, $request->chart_of_account_id, $request->amount, $request->voucher, 'credit');
                // } else if($request->bank == 2){
                //     Utility::accountPettyCashFundTransaction($request->date, $request->chart_of_account_id, 227, $request->amount, $request->voucher, 'debit');
                //     Utility::accountPettyCashFundTransaction($request->date, 227, $request->chart_of_account_id, $request->amount, $request->voucher, 'credit');
                // } else if($request->bank == 3){
                //     Utility::accountPettyCashFundTransaction($request->date, $request->chart_of_account_id, 229, $request->amount, $request->voucher, 'debit');
                //     Utility::accountPettyCashFundTransaction($request->date, 229, $request->chart_of_account_id, $request->amount, $request->voucher, 'credit');
                // }
            }

            if ($request->pay_type == 'supplier') {
                $paidTo          = $request->supplier;
            } elseif ($request->pay_type == 'customer') {
                $paidTo          = $request->customer_id;
            } else {
                $paidTo          = null;
            }

            if ($request->advance_id == '' &&  $request->pawning_id == '' && $request->loan_id == '' && $request->chart_of_account_id != 230) {
                $backAccount = BankAccount::find($request->bank);
                Utility::accountPaymentVoucherTransaction($request->date, $request->chart_of_account_id, $backAccount->chart_of_account_id, $request->amount, $request->voucher, $request->pay_type, $paidTo, 'debit');
                Utility::accountPaymentVoucherTransaction($request->date, $backAccount->chart_of_account_id, $request->chart_of_account_id, $request->amount, $request->voucher, $request->pay_type, $paidTo, 'credit');

                // if($request->bank == 1){
                //     Utility::accountPaymentVoucherTransaction($request->date, $request->chart_of_account_id, 228, $request->amount, $request->voucher, $request->pay_type, $paidTo, 'debit');
                //     Utility::accountPaymentVoucherTransaction($request->date, 228, $request->chart_of_account_id, $request->amount, $request->voucher, $request->pay_type, $paidTo, 'credit');
                // } else if($request->bank == 2){
                //     Utility::accountPaymentVoucherTransaction($request->date, $request->chart_of_account_id, 227, $request->amount, $request->voucher, $request->pay_type, $paidTo, 'debit');
                //     Utility::accountPaymentVoucherTransaction($request->date, 227, $request->chart_of_account_id, $request->amount, $request->voucher, $request->pay_type, $paidTo, 'credit');
                // } else if($request->bank == 3){
                //     Utility::accountPaymentVoucherTransaction($request->date, $request->chart_of_account_id, 229, $request->amount, $request->voucher, $request->pay_type, $paidTo, 'debit');
                //     Utility::accountPaymentVoucherTransaction($request->date, 229, $request->chart_of_account_id, $request->amount, $request->voucher, $request->pay_type, $paidTo, 'credit');
                // }
            }

            $financialEntry = new FinancialEntry();
            $financialEntry->date = $request->date;
            $financialEntry->type = 'Cheque Issued';
            $financialEntry->payment_method = 'CHEQUE';
            $financialEntry->cheque_id = $cheque->id;
            $financialEntry->customer_id = $request->customer_id ?: null;
            $financialEntry->vendor_id = $request->supplier_id ?: null;
            $financialEntry->payment_voucher_id = $request->voucher ? $voucher->id : null;
            $financialEntry->remarks = $request->description ?: null;
            $financialEntry->amount = $request->amount;
            $financialEntry->debit_credit = 'credit';
            $financialEntry->entry_type = 'cheque_issue';
            $financialEntry->branch_id = \Auth::user()->branchId();
            $financialEntry->created_by = \Auth::user()->id;
            $financialEntry->save();

            return redirect()->back()->with('success', __('Cheque successfully created !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function chequeNumber()
    {
        $latest = Cheque::latest()->first();

        if (!$latest) {
            return 1;
        }
        return $latest->id + 1;
    }


    public function destroy(bankTransfer $bankTransfer)
    {
        if (\Auth::user()->can('delete cheque')) {
            // if($bankTransfer->created_by == \Auth::user()->creatorId())
            // {
            $bankTransfer->delete();

            Utility::bankAccountBalance($bankTransfer->from_account, $bankTransfer->amount, 'credit');
            Utility::bankAccountBalance($bankTransfer->to_account, $bankTransfer->amount, 'debit');
            return redirect()->route('bank-transfer.index')->with('success', __('Bank transfer successfully deleted !!!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id, $command)
    {
        if (\Auth::user()->can('view cheque')) {
            $cheques = Cheque::with('vender', 'customer')->where('id', $id)->first();
            if ($cheques->cheque_bank_id == 1 || $cheques->cheque_bank_id == 3 || $cheques->cheque_bank_id == 4) {
                return view('chequePrint.cheque_print_commertial', compact('cheques', 'command'));
            } elseif ($cheques->cheque_bank_id == 2) {
                return view('chequePrint.cheque_print_selan', compact('cheques', 'command'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function cancel($id)
    {
        if (\Auth::user()->can('cancel cheque')) {
            $cheques = Cheque::find($id);
            $cheques->status = 0;
            $cheques->update();
            $voucher = PaymentVoucher::find($cheques->payment_voucher_id);
            $voucher->status = 3;
            $voucher->update();
            $chequeBookCheque = ChequeBookCheque::where('cheque_id', $id)->first();
            if ($chequeBookCheque) {
                $chequeBookCheque->status = 2;
                $chequeBookCheque->update();
            }

            if ($voucher->chart_of_account_id == 230) {
                Utility::addPettyCashFund(-$cheques->amount);
            }
            Utility::deleteAccountPaymentVoucherTransaction($cheques->payment_voucher_id);

            $financialEntry = FinancialEntry::where('payment_voucher_id', $voucher->id)->first();
            if ($financialEntry) {
                $financialEntry->delete();
            }

            return redirect()->back()->with('success', __('Cheque successfully Canceled !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getChequeNumber($id)
    {
        $book = ChequeBook::where('status', 1)->where('cheque_bank_id', $id)->first();
        if (!isset($book)) {
            return json_encode($book);
        }
        $cheqBookCheque = ChequeBookCheque::where('status', 1)->where('cheque_book_id', $book->id)->first();
        return json_encode($cheqBookCheque);
    }

    public function getVoucherDetails($id)
    {
        $paymentVoucher = PaymentVoucher::with('chequeBookCheque')->find($id);
        return json_encode($paymentVoucher);
    }
    public function paymentVoucher($id)
    {
        $cheques = Cheque::with('vender', 'customer', 'bank')->where('id', $id)->first();
        return view('chequePrint.payment_voucher', compact('cheques'));
    }
}
