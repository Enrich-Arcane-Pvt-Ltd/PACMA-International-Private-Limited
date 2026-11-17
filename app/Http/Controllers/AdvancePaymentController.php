<?php

namespace App\Http\Controllers;

use App\AdvancePayment;
use App\Customer;
use App\Product;
use App\Receipt;
use App\CardInterestExpense;
use App\BankAccount;
use App\FinancialEntry;
use App\GeneralLedgerAccount;
use App\Setting;
use App\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdvancePaymentController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index()
    {
        if (\Auth::user()->can('manage advances')) {
            $advances = AdvancePayment::where('branch_id', \Auth::user()->branchId())->whereNotIn('status', [9])->orderBy('id', 'DESC')->get();
            $currencySymbol = $this->currencySymbol;
            return view('advance_payments.index', compact('advances', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create advances')) {

            // Fetch customers based on branch and status
            if (\Auth::user()->branchId() == 0) {
                $customers = Customer::where('status', 1)->where('type', 0)->get();
            } else {
                $customers = Customer::where('branch_id', \Auth::user()->branchId())
                    ->where('status', 1)
                    ->whereIn('type', [0, 2])
                    ->get();
            }
            $recentCustomer = $customers->sortByDesc('created_at')->first();

            // Fetch bank accounts associated with the user’s branch
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');

            // Fetch all active products without filtering by branch
            $products = Product::where('status', 1)->get(); // Retrieve all active products

            return view('advance_payments.create', compact('customers', 'recentCustomer', 'banks', 'products'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //     public function create()
    // {
    //     if (\Auth::user()->can('create advances')) {
    //         // Debug log to check if code is entering this section
    //         \Log::info('Entering Advance Payment create function.');

    //         // Fetch customers based on branch and status
    //         if (\Auth::user()->branchId() == 0) {
    //             $customers = Customer::where('status', 1)->where('type', 0)->get();
    //         } else {
    //             $customers = Customer::where('branch_id', \Auth::user()->branchId())
    //                                  ->where('status', 1)
    //                                  ->whereIn('type', [0, 2])
    //                                  ->get();
    //         }

    //         // Debug log to see fetched customers
    //         \Log::info('Fetched Customers:', ['customers' => $customers->toArray()]);

    //         // Fetch bank accounts
    //         $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
    //         $banks->prepend('Select Bank', '');

    //         // Debug log to see fetched banks
    //         \Log::info('Fetched Banks:', ['banks' => $banks]);

    //         // Fetch all active products
    //         $products = Product::where('status', 1)->get();

    //         // Debug log to see fetched products
    //         \Log::info('Fetched Products:', ['products' => $products->toArray()]);

    //         return view('advance_payments.create', compact('customers', 'banks', 'products'));
    //     } else {
    //         \Log::info('User does not have permission to create advances.');
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create advances')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'current_date' => 'required',
                    'customer' => 'required',
                    'advance_amount' => 'required',
                    'advance_amount_in_words' => 'required',
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

            $maxId = Receipt::max('id');

            $receiptCode = "A" . str_pad(($maxId + 1), 6, "0", STR_PAD_LEFT);


            $receipts = new Receipt();
            $receipts->branch_id = \Auth::user()->branchId();
            $receipts->receipt_code = $receiptCode;
            $receipts->issue_date = $request->current_date;
            $receipts->customer_id = $request->customer;
            $receipts->paid_amount = $request->advance_amount;
            $receipts->paid_amount_in_words = $request->advance_amount_in_words;
            $receipts->payment_method = $request->payment_method;
            $receipts->status = 1;
            $receipts->description = "Advance Payment";
            $receipts->created_by = \Auth::user()->id;
            $receipts->save();

            $advances = new AdvancePayment();
            $advances->receipt_id = $receipts->id;
            $advances->customer_id = $request->customer;
            $advances->branch_id = \Auth::user()->branchId();
            $advances->advance_date = $request->current_date;
            $advances->advance_amount = $request->advance_amount;
            $advances->advance_amount_in_words = $request->advance_amount_in_words;
            $advances->payment_method = $request->payment_method;
            $advances->note = $request->note;
            $advances->bank_id = $request->bank;
            if ($request->payment_method == 'CARD') {
                $advances->card_type            = $request->card_type;
                if ($request->card_charge_amount != '') {
                    $advances->card_expence_id      = $cardCharge->id;
                }
                $advances->card_charge_amount   = $request->card_charge_amount;
                $advances->total_amount         = $request->total_amount;
            } elseif ($request->payment_method == 'CHEQUE') {
                $advances->cheque_number        = $request->cheque_number;
                $advances->cheque_realised_date     = $request->realised_date;
            }
            $advances->status = 1;
            $advances->created_by = \Auth::user()->id;
            $advances->save();

            // if($request->payment_method == 'CASH'){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 309, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            // } else if($request->payment_method == 'CARD' || $request->payment_method == 'ONLINEPAYMENT'){

            $backAccount = BankAccount::find($request->bank);
            Utility::accountAdvancePaymentTransaction($request->current_date, $backAccount->chart_of_account_id, 5, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            Utility::accountAdvancePaymentTransaction($request->current_date, 5, $backAccount->chart_of_account_id, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');

            // if($request->bank == 1){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 228, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 228, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            // } else if($request->bank == 2){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 227, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 227, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            // } else if($request->bank == 3){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 229, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 229, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            // }

            // } else if($request->payment_method == 'CHEQUE'){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 309, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');

            //     if($request->bank == 1){
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 228, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 309, 228, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            //     } else if($request->bank == 2){
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 227, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 309, 227, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            //     } else if($request->bank == 3){
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 229, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 309, 229, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            //     }
            // }

            $financialEntry = new FinancialEntry();
            $financialEntry->date = $request->current_date;
            $financialEntry->type = 'Advance Payment';
            $financialEntry->payment_method = $request->payment_method;
            $financialEntry->customer_id = $request->customer_id ?: null;
            $financialEntry->receipt_id = $receipts->id;
            $financialEntry->advance_payment_id = $advances->id;
            $financialEntry->amount = $request->advance_amount;
            $financialEntry->debit_credit = 'debit';
            $financialEntry->entry_type = 'advance_payment';
            $financialEntry->branch_id = \Auth::user()->branchId();
            $financialEntry->created_by = \Auth::user()->id;
            $financialEntry->save();

            return redirect()->back()->with('success', __('Advance Successfully Paid.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {

        // Retrieve the advance payment with customer and product details
        $advance = AdvancePayment::with(['customer', 'product'])->find($id);

        if (!$advance) {
            return redirect()->back()->with('error', __('Advance payment not found.'));
        }

        // Fetch customers based on the user's branch ID
        $customers = (\Auth::user()->branchId() == 0)
            ? Customer::where('status', 1)->where('type', 0)->get()
            : Customer::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();

        // Retrieve bank accounts for the user's branch
        $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->pluck('bank', 'id');
        $banks->prepend('Select Bank', '');

        // Return a view template specifically for read-only display
        return view('advance_payments.view', compact('customers', 'advance', 'banks'));
    }



    public function edit($id)
    {
        if (\Auth::user()->can('edit advances')) {

            $advance = AdvancePayment::find($id);

            if (\Auth::user()->branchId() == 0) {
                $customers = Customer::where('status', 1)->where('type', 0)->get();
            } else if (\Auth::user()->branchId() != 0) {
                $customers = Customer::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            }
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            return view('advance_payments.edit', compact('customers', 'advance', 'banks'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request)
    {
        if (\Auth::user()->can('edit advances')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'current_date' => 'required',
                    'advance_amount' => 'required',
                    'advance_amount_in_words' => 'required',
                    'payment_method' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $advances = AdvancePayment::find($request->advance_id);

            $checkCardCharge = CardInterestExpense::find($advances->card_expence_id);
            if ($checkCardCharge) {
                $checkCardCharge->delete();
            }

            if ($request->payment_method == 'CARD') {
                if ($request->card_charge_amount != '') {
                    $cardCharge = new CardInterestExpense();
                    $cardCharge->amount = $request->card_charge_amount;
                    $cardCharge->customer_id = $request->customer;
                    $cardCharge->save();
                }
            }

            $advances->advance_date = $request->current_date;
            $advances->advance_amount = $request->advance_amount;
            $advances->advance_amount_in_words = $request->advance_amount_in_words;
            $advances->payment_method = $request->payment_method;
            $advances->note = $request->note;
            $advances->bank_id = $request->bank;
            if ($request->payment_method == 'CARD') {
                $advances->card_type            = $request->card_type;
                if ($request->card_charge_amount != '') {
                    $advances->card_expence_id      = $cardCharge->id;
                }
                $advances->card_charge_amount   = $request->card_charge_amount;
                $advances->total_amount         = $request->total_amount;
            } elseif ($request->payment_method == 'CHEQUE') {
                $advances->cheque_number        = $request->cheque_number;
                $advances->cheque_realised_date     = $request->realised_date;
            }
            $advances->created_by = \Auth::user()->id;
            $advances->update();

            $receipts = Receipt::find($advances->receipt_id);
            $receipts->issue_date = $request->current_date;
            $receipts->paid_amount = $request->advance_amount;
            $receipts->paid_amount_in_words = $request->advance_amount_in_words;
            $receipts->payment_method = $request->payment_method;
            $receipts->created_by = \Auth::user()->id;
            $receipts->update();

            Utility::deleteAccountAdvancePaymentTransaction($advances->id);

            // if($request->payment_method == 'CASH'){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 309, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            // } else if($request->payment_method == 'CARD' || $request->payment_method == 'ONLINEPAYMENT'){
            $backAccount = BankAccount::find($request->bank);
            Utility::accountAdvancePaymentTransaction($request->current_date, $backAccount->chart_of_account_id, 5, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            Utility::accountAdvancePaymentTransaction($request->current_date, 5, $backAccount->chart_of_account_id, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');

            // if($request->bank == 1){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 228, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 228, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            // } else if($request->bank == 2){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 227, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 227, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            // } else if($request->bank == 3){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 229, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 229, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            // }

            // } else if($request->payment_method == 'CHEQUE'){
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 309, 275, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //     Utility::accountAdvancePaymentTransaction( $request->current_date, 275, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');

            //     if($request->bank == 1){
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 228, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 309, 228, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            //     } else if($request->bank == 2){
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 227, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 309, 227, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            //     } else if($request->bank == 3){
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 229, 309, $advances->id, $request->customer, 3, $request->advance_amount, 'debit');
            //         Utility::accountAdvancePaymentTransaction( $request->realised_date, 309, 229, $advances->id, $request->customer, 3, $request->advance_amount, 'credit');
            //     }
            // }

            $financialEntry = FinancialEntry::where('advance_payment_id', $advances->id)->where('entry_type', 'advance_payment')->first();

            $financialEntry->date = $request->current_date;
            $financialEntry->type = 'Advance Payment';
            $financialEntry->payment_method = $request->payment_method;
            $financialEntry->customer_id = $request->customer_id ?: null;
            $financialEntry->receipt_id = $receipts->id;
            $financialEntry->advance_payment_id = $advances->id;
            $financialEntry->amount = $request->advance_amount;
            $financialEntry->debit_credit = 'debit';
            $financialEntry->entry_type = 'advance_payment';
            $financialEntry->branch_id = \Auth::user()->branchId();
            $financialEntry->created_by = \Auth::user()->id;
            $financialEntry->update();

            return redirect()->back()->with('success', __('Advance Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function receipt($id, $command)
    {
        $receipt = Receipt::with('advance')->where('id', $id)->first();

        $user = Receipt::select('users.name')
            ->join('users', 'users.id', 'receipts.created_by')
            ->where('receipts.id', $id)
            ->first();

        $advance = AdvancePayment::where('receipt_id', $receipt->id)->first();

        return view('advance_payments.receipt', compact('receipt', 'user', 'command', 'advance'));
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete advances')) {
            // $returnDate = Carbon::now()->format('Y-m-d');
            $advance = AdvancePayment::find($id);
            // $advance->return_date = $returnDate;
            $advance->status = 9;
            $advance->update();

            $receipts = Receipt::find($advance->receipt_id);
            if ($receipts) {
                $receipts->status = 0;
                $receipts->update();
            }

            $checkCardCharge = CardInterestExpense::find($advance->card_expence_id);
            if ($checkCardCharge) {
                $checkCardCharge->delete();
            }

            $financialEntry = FinancialEntry::where('advance_payment_id', $advance->id)->where('entry_type', 'advance_payment')->first();
            if ($financialEntry) {
                $financialEntry->delete();
            }

            Utility::deleteAccountAdvancePaymentTransaction($id);
            // Utility::accountAdvancePaymentTransaction( $returnDate, 275, 309, $advance->id, $advance->customer_id, 3, $advance->advance_amount, 'debit');
            // Utility::accountAdvancePaymentTransaction( $returnDate, 309, 275, $advance->id, $advance->customer_id, 3, $advance->advance_amount, 'credit');

            return redirect()->back()->with('success', __('Advance Successfully Returned!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function voucherCreate($id)
    {
        if (\Auth::user()->can('create payment voucher') || \Auth::user()->can('return advances')) {
            // $supplier = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0, 2])->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');
            $advance = AdvancePayment::find($id);
            $chequeBanks = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $accounts = GeneralLedgerAccount::orderBy('account_name')->get()->pluck('account_name', 'id');
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $chartAccount = 275;

            return view('advance_payments.create_advance_return_payment_voucher', compact('advance', 'chequeBanks', 'customers', 'accounts', 'banks', 'chartAccount'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function purchaseDetails($id)
    {
        if (\Auth::user()->can('manage sale')) {
            $advance = AdvancePayment::find($id);

            return view('advance_payments.purchase_details', compact('advance'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function returnDetails($id)
    {
        if (\Auth::user()->can('manage sale')) {
            $advance = AdvancePayment::find($id);

            return view('advance_payments.return_details', compact('advance'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function updatePrintStatus($id)
    {
        $advances = AdvancePayment::findOrFail($id);

        // Update print status based on its current value
        if ($advances->print_status == 0) {
            $advances->print_status = 1;
        } elseif ($advances->print_status == 1) {
            $advances->print_status = 2;
        } elseif ($advances->print_status == 3) {
            $advances->print_status = 2;
        }

        $advances->save();

        return response()->json(['status' => 'success', 'message' => 'Print status updated successfully']);
    }

    public function adminUpdatePrintStatus($id)
    {
        $advances = AdvancePayment::findOrFail($id);

        // Set print status to 3
        $advances->print_status = 3;
        $advances->save();

        return response()->json(['status' => 'success', 'message' => 'Print status updated to 3 successfully']);
    }
}
