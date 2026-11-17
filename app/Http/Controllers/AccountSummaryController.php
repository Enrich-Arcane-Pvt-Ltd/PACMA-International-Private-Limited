<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AccountGroups;
use App\AccountSections;
use App\BankAccount;
use App\Bill;
use App\BillProduct;
use App\Branch;
use App\Center;
use App\GeneralLedgerAccount;
use App\Customer;
use App\Receipt;
use App\Invoice;
use App\InvoiceProduct;
use App\Payment;
use App\ProductServiceCategory;
use App\Revenue;
use App\Tax;
use App\Vender;
use App\Sale;
use App\Installment;
use App\Loan;
use App\LoanInstallment;
use App\PettyCash;
use App\PawningReceipt;
use App\ProductCategory;
use App\User;
use App\Stock;
use App\TrialBalance;
use App\GeneralJournal;
use App\AccountTransaction;
use App\BankRecEntry;
use App\BankReconciliation;
use App\LedgerAccount;
use App\ControlAccount;
use App\Setting;
use App\Utility;
use Carbon\Carbon;
use DateTime;

class AccountSummaryController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }
    public function yearMonth()
    {

        $month[] = __('April');
        $month[] = __('May');
        $month[] = __('June');
        $month[] = __('July');
        $month[] = __('August');
        $month[] = __('September');
        $month[] = __('October');
        $month[] = __('November');
        $month[] = __('December');
        $month[] = __('January');
        $month[] = __('February');
        $month[] = __('March');

        return $month;
    }

    public function yearList()
    {
        $starting_year = date('Y', strtotime('-5 year'));
        $ending_year   = date('Y');

        foreach (range($ending_year, $starting_year) as $year) {
            $starting_year[$ending_year] = $year;
            $years[$year] = $year;
        }

        if (date('m') > 6) {
            $year = date('Y') + 1;
        } else {
            $year = date('Y');
        }
        return $years;
    }

    //LEDGER ACCOUNTS
    public function ledgerAccountIndex()
    {
        if (\Auth::user()->can('view ledger account')) {
            $showTable = 'No';
            $accounts = GeneralLedgerAccount::whereNotIn('id', [15, 16, 17, 18, 12])->orderBy('account_name')->get()->pluck('account_name', 'id');
            // $accounts = GeneralLedgerAccount::whereNotIn('id', [251,252,253,254,255,256,257,258,259,285,288,313,314])->orderBy('account_name')->get()->pluck('account_name', 'id');
            $accounts->prepend('-- Select Account Name --', '');
            $currencySymbol = $this->currencySymbol;
            return view('ledgerAccount.index', compact('showTable', 'accounts', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function ledgerAccountSearch(Request $request)
    {
        if (\Auth::user()->can('view ledger account')) {
            $accounts = GeneralLedgerAccount::whereNotIn('id', [15, 16, 17, 18, 12])->orderBy('account_name')->get()->pluck('account_name', 'id');
            // $accounts = ChartOfAccount::whereNotIn('id', [251,252,253,254,255,256,257,258,259,285,288,313,314])->orderBy('name')->get()->pluck('name', 'id');
            $accounts->prepend('-- Select Account Name --', '');
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = date("d", strtotime($start_date));
                $endDate = date("d", strtotime($end_date));
                $chartOfAccount = $request->account_id;
                $showTable = 'Yes';
                $openingBalance = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                    ->where('branch_id', \Auth::user()->branchId())
                    ->where('chart_of_account_id', $request->account_id)
                    ->where('status', 1)
                    ->where('date', '<', $start_date)
                    ->get();

                $ledgeraccounts = AccountTransaction::with('sales', 'related_chart_of_account')->where('branch_id', \Auth::user()->branchId())
                    ->where('chart_of_account_id', $request->account_id)
                    ->where('status', 1)
                    ->where('date', '>=', $start_date)
                    ->where('date', '<=', $end_date)
                    ->get();
                $currencySymbol = $this->currencySymbol;
                return view('ledgerAccount.index', compact('showTable', 'start_date', 'end_date', 'chartOfAccount', 'ledgeraccounts', 'accounts', 'openingBalance', 'currencySymbol'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //CONTROL ACCOUNTS
    public function controlAccountIndex()
    {
        if (\Auth::user()->can('view control account')) {
            $showTable = 'No';
            $accounts = GeneralLedgerAccount::whereNotIn('id', [15, 16, 17, 18, 12])->orderBy('account_name')->get()->pluck('account_name', 'id');
            $currencySymbol = $this->currencySymbol;
            // $accounts = ChartOfAccount::whereIn('id', [251,252,253,254,255,256,257,258,259,285,288,313,314])->orderBy('name')->get()->pluck('name', 'id');
            $accounts->prepend('-- Select Control Account --', '');
            return view('controlAccount.index', compact('showTable', 'accounts', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function controlAccountSearch(Request $request)
    {
        if (\Auth::user()->can('view control account')) {
            $accounts = GeneralLedgerAccount::whereNotIn('id', [15, 16, 17, 18, 12])->orderBy('account_name')->get()->pluck('account_name', 'id');

            // $accounts = ChartOfAccount::whereIn('id', [251,252,253,254,255,256,257,258,259,285,288,313,314])->orderBy('name')->get()->pluck('name', 'id');
            $accounts->prepend('-- Select Account Name --', '');
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = date("d", strtotime($start_date));
                $endDate = date("d", strtotime($end_date));
                $chartOfAccount = $request->account_id;
                $showTable = 'Yes';
                $controlaccounts = AccountTransaction::with('related_chart_of_account')->where('branch_id', \Auth::user()->branchId())
                    ->where('chart_of_account_id', $request->account_id)
                    ->where('status', 1)
                    ->where('date', '>=', $start_date)
                    ->where('date', '<=', $end_date)
                    ->get();
                $currencySymbol = $this->currencySymbol;
                return view('controlAccount.index', compact('showTable', 'start_date', 'end_date', 'chartOfAccount', 'controlaccounts', 'accounts', 'currencySymbol'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //================================== BANK RECONCILIATION ===============================>

    public function bankRecIndex()
    {
        if (\Auth::user()->can('manage bank rec')) {
            $bankAccounts = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $accountIds = [];
            foreach ($bankAccounts as $bankAccount) {
                $accountIds[] = $bankAccount->chart_of_account_id;
                // if($bankAccount->id == 1){
                //     $accountIds[] = 228;
                // } else if($bankAccount->id == 2){
                //     $accountIds[] = 227;
                // } else if($bankAccount->id == 3){
                //     $accountIds[] = 229;
                // }
            }
            $banks = GeneralLedgerAccount::with('bankRecs')->whereIn('id', $accountIds)->orderBy('account_name')->get();

            return view('bankRec.index', compact('banks'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bankRecCreate()
    {
        if (\Auth::user()->can('create bank rec')) {
            $bankAccounts = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $accountIds = [];
            foreach ($bankAccounts as $bankAccount) {
                $accountIds[] = $bankAccount->chart_of_account_id;
                // if($bankAccount->id == 1){
                //     $accountIds[] = 228;
                // } else if($bankAccount->id == 2){
                //     $accountIds[] = 227;
                // } else if($bankAccount->id == 3){
                //     $accountIds[] = 229;
                // }
            }
            $accounts = GeneralLedgerAccount::whereIn('id', $accountIds)->orderBy('account_name')->get()->pluck('account_name', 'id');
            $accounts->prepend('-- Select Banck Account --', '');
            return view('bankRec.create', compact('accounts'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    // public function bankRecEntriesIndex(){
    //     if(\Auth::user()->can('manage bank rec'))
    //     {
    //         $showTable = 'No';
    //         $accounts = ChartOfAccount::whereIn('id', [227,228,229])->orderBy('name')->get()->pluck('name', 'id');
    //         $accounts->prepend('-- Select Banck Account --', '');
    //         return view('bankRec.bank_entries', compact('showTable','accounts'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    public function bankRecEntriesSearch(Request $request)
    {
        if (\Auth::user()->can('create bank rec')) {
            // $accounts = ChartOfAccount::whereIn('id', [227,228,229])->orderBy('name')->get()->pluck('name', 'id');
            // $accounts->prepend('-- Select Banck Account --', '');

            if (isset($request->account_id) && isset($request->start_date) && isset($request->end_date) && isset($request->bank_statement_balance)) {
                $showTable = 'Yes';
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $chartOfAccount = $request->account_id;
                $statementBalance = $request->bank_statement_balance;
                $currentMonth = Carbon::parse($end_date)->format('Y-m');

                // ==================== Validation Start (cannot do it usin one query ) ===================>
                $recCheck1 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
                    ->where('start_date', $start_date)
                    ->where('end_date', $end_date)
                    ->where('month', Carbon::parse($end_date)->format('Y-m'))->first();

                $recCheck2 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
                    ->whereYear('start_date', Carbon::parse($start_date)->format('Y'))
                    ->whereMonth('start_date', Carbon::parse($start_date)->format('m'))->first();

                $recCheck3 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
                    ->whereYear('start_date', Carbon::parse($end_date)->format('Y'))
                    ->whereMonth('start_date', Carbon::parse($end_date)->format('m'))->first();

                $recCheck4 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
                    ->whereYear('end_date', Carbon::parse($start_date)->format('Y'))
                    ->whereMonth('end_date', Carbon::parse($start_date)->format('m'))->first();

                $recCheck5 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
                    ->whereYear('end_date', Carbon::parse($end_date)->format('Y'))
                    ->whereMonth('end_date', Carbon::parse($end_date)->format('m'))->first();

                if (isset($recCheck1) || isset($recCheck2) || isset($recCheck3) || isset($recCheck4) || isset($recCheck5)) {
                    return redirect()->back()->with('error', __('Reconciliation Allready Done!'));
                }
                // ==================== Validation end ===================>

                // $bankRecs = BankRecEntry::with('related_chart_of_account', 'branch', 'type')
                //                                 ->where('status', 0)
                //                                 ->where('branch_id', \Auth::user()->branchId())
                //                                 ->where('chart_of_account_id', $chartOfAccount)
                //                                 ->where('date', '<=', $end_date)
                //                                 ->get();

                $accId = BankRecEntry::where('branch_id', \Auth::user()->branchId())
                    ->where('chart_of_account_id', $chartOfAccount)
                    ->where('status', 1)
                    ->where('date', '<=', $end_date)
                    ->get()->pluck('acccount_transaction_id');
                $transactions = AccountTransaction::with('related_chart_of_account', 'branch', 'type', 'customer')
                    ->where('branch_id', \Auth::user()->branchId())
                    ->whereNotIn('id', $accId)
                    ->where('status', 1)
                    ->where('chart_of_account_id', $chartOfAccount)
                    ->where('date', '<=', $end_date)
                    ->get();

                $lastRec = BankReconciliation::where('month', '<', $currentMonth)->where('chart_of_account_id', $chartOfAccount)->orderBy('month', 'DESC')->first();

                if ($lastRec) {
                    $startDateBalance = $lastRec->end_balance;
                } else {
                    $openingBalance = 0;
                    // if($chartOfAccount == 227){
                    //     $bankAcount = BankAccount::find(2);
                    // } elseif($chartOfAccount == 228) {
                    //     $bankAcount = BankAccount::find(1);
                    // } elseif($chartOfAccount == 229) {
                    //     $bankAcount = BankAccount::find(3);
                    // }

                    // if($bankAcount){
                    //     $openingBalance = $bankAcount->opening_balance;
                    // } else {
                    //     $openingBalance = 0;
                    // }

                    $startClearedDiposits = BankRecEntry::where('status', 1)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('chart_of_account_id', $chartOfAccount)
                        ->where('debit_credit', 1)
                        ->where('date', '<', $start_date)
                        ->get()->sum('amount');

                    $startClearedWithdrawals = BankRecEntry::where('status', 1)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('chart_of_account_id', $chartOfAccount)
                        ->where('debit_credit', 0)
                        ->where('date', '<', $start_date)
                        ->get()->sum('amount');

                    $startDateBalance = $openingBalance + $startClearedDiposits - $startClearedWithdrawals;
                }

                return view('bankRec.bank_entries', compact('showTable', 'start_date', 'end_date', 'chartOfAccount', 'statementBalance', 'startDateBalance', 'transactions'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function storeEntry(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $chartOfAccount = $request->account_id;
        $currentMonth = Carbon::parse($end_date)->format('Y-m');

        // ==================== Validation Start (cannot do it usin one query ) ===================>
        $recCheck1 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
            ->where('start_date', $start_date)
            ->where('end_date', $end_date)
            ->where('month', Carbon::parse($end_date)->format('Y-m'))->first();

        $recCheck2 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
            ->whereYear('start_date', Carbon::parse($start_date)->format('Y'))
            ->whereMonth('start_date', Carbon::parse($start_date)->format('m'))->first();

        $recCheck3 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
            ->whereYear('start_date', Carbon::parse($end_date)->format('Y'))
            ->whereMonth('start_date', Carbon::parse($end_date)->format('m'))->first();

        $recCheck4 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
            ->whereYear('end_date', Carbon::parse($start_date)->format('Y'))
            ->whereMonth('end_date', Carbon::parse($start_date)->format('m'))->first();

        $recCheck5 = BankReconciliation::where('chart_of_account_id', $chartOfAccount)
            ->whereYear('end_date', Carbon::parse($end_date)->format('Y'))
            ->whereMonth('end_date', Carbon::parse($end_date)->format('m'))->first();

        if (isset($recCheck1) || isset($recCheck2) || isset($recCheck3) || isset($recCheck4) || isset($recCheck5)) {
            return redirect()->route('bank_rec.index')->with('error', __('Reconciliation Allready Done!'));
        }
        // ==================== Validation end ===================>

        $bankRec = new BankReconciliation();
        $bankRec->chart_of_account_id = $chartOfAccount;
        $bankRec->month = $currentMonth;
        $bankRec->start_date = $start_date;
        $bankRec->end_date = $end_date;

        $bankRec->start_balance = $request->start_balance;
        $bankRec->add_in_period = $request->total_debit;
        $bankRec->less_in_period = $request->total_credit;
        $bankRec->add_cleared_in_period = $request->cleared_debit;
        $bankRec->less_cleared_in_period = $request->cleared_credit;
        $bankRec->add_unclered = $request->uncleared_credit;
        $bankRec->less_unclered = $request->uncleared_debit;
        $bankRec->end_balance = $request->end_balance;
        $bankRec->statement_balance = $request->statement_balance;
        $bankRec->difference = $request->diference;

        $bankRec->status = 1;
        $bankRec->created_by = \Auth::user()->id;
        $bankRec->save();

        foreach ($request->checked_bank_rec as $rec) {
            if (isset($rec['rec_check'])) {
                $status = 1;
            } else {
                $status = 0;
            }
            // $bankRec = BankRecEntry::where('acccount_transaction_id', $rec['transaction_id'])->first();
            // if($bankRec){
            //     $bankRec->realised_month = Carbon::parse($end_date)->format('Y-m');
            //     $bankRec->start_date = $start_date;
            //     $bankRec->end_date = $end_date;
            //     $bankRec->status = $status;
            //     $bankRec->created_by = \Auth::user()->id;
            //     $bankRec->update();
            // } else {
            $accountTransaction = AccountTransaction::find($rec['transaction_id']);
            $newBankRec = new BankRecEntry();
            $newBankRec->realised_month = Carbon::parse($end_date)->format('Y-m');
            $newBankRec->bank_rec_id = $bankRec->id;
            $newBankRec->start_date = $start_date;
            $newBankRec->end_date = $end_date;
            $newBankRec->date = $accountTransaction->date;
            $newBankRec->acccount_transaction_id = $accountTransaction->id;
            $newBankRec->chart_of_account_id = $accountTransaction->chart_of_account_id;
            $newBankRec->related_chart_of_account_id = $accountTransaction->related_chart_of_account_id;
            $newBankRec->transaction_type_id = $accountTransaction->transaction_type_id;
            $newBankRec->branch_id = $accountTransaction->branch_id;
            $newBankRec->debit_credit = $accountTransaction->debit_credit;
            $newBankRec->amount = $accountTransaction->amount;
            $newBankRec->status = $status;
            $newBankRec->created_by = \Auth::user()->id;
            $newBankRec->save();
            // }
        }

        // $clearedDiposits = BankRecEntry::where('status', 1)
        //                     ->where('branch_id', \Auth::user()->branchId())
        //                     ->where('chart_of_account_id', $chartOfAccount)
        //                     ->where('debit_credit', 1)
        //                     ->where('bank_rec_id', $bankRec->id)
        //                     // ->where('date', '>=', $start_date)
        //                     // ->where('date', '<=', $end_date)
        //                     ->get()->sum('amount');

        // $clearedWithdrawals = BankRecEntry::where('status', 1)
        //                     ->where('branch_id', \Auth::user()->branchId())
        //                     ->where('chart_of_account_id', $chartOfAccount)
        //                     ->where('debit_credit', 0)
        //                     ->where('bank_rec_id', $bankRec->id)
        //                     // ->where('date', '>=', $start_date)
        //                     // ->where('date', '<=', $end_date)
        //                     ->get()->sum('amount');

        // $unClearDiposit = BankRecEntry::where('status', 0)
        //                     ->where('branch_id', \Auth::user()->branchId())
        //                     ->where('chart_of_account_id', $chartOfAccount)
        //                     ->where('debit_credit', 1)
        //                     ->where('bank_rec_id', $bankRec->id)
        //                     // ->where('date', '>=', $start_date)
        //                     // ->where('date', '<=', $end_date)
        //                     ->get()->sum('amount');

        // $unClearedWithdrawals = BankRecEntry::where('status', 0)
        //                     ->where('branch_id', \Auth::user()->branchId())
        //                     ->where('chart_of_account_id', $chartOfAccount)
        //                     ->where('debit_credit', 0)
        //                     ->where('bank_rec_id', $bankRec->id)
        //                     // ->where('date', '>=', $start_date)
        //                     // ->where('date', '<=', $end_date)
        //                     ->get()->sum('amount');



        return redirect()->route('bank_rec.statement', $bankRec->id)->with('success', __('The Bank Entries Saved Successfully!'));
    }

    // public function checkEntry($accId){
    //     $bankRec = BankRecEntry::where('acccount_transaction_id', $accId)->first();
    //     if($bankRec){
    //         $bankRec->status = 1;
    //         $bankRec->created_by = \Auth::user()->id;
    //         $bankRec->update();
    //         return json_encode(array('id'=>$bankRec->id, 'response'=>'success'));
    //     } else {
    //         $accountTransaction = AccountTransaction::find($accId);
    //         $newbankRec = new BankRecEntry();
    //         $newbankRec->date = $accountTransaction->date;
    //         $newbankRec->acccount_transaction_id = $accountTransaction->id;
    //         $newbankRec->chart_of_account_id = $accountTransaction->chart_of_account_id;
    //         $newbankRec->related_chart_of_account_id = $accountTransaction->related_chart_of_account_id;
    //         $newbankRec->transaction_type_id = $accountTransaction->transaction_type_id;
    //         $newbankRec->branch_id = $accountTransaction->branch_id;
    //         $newbankRec->debit_credit = $accountTransaction->debit_credit;
    //         $newbankRec->amount = $accountTransaction->amount;
    //         $newbankRec->status = 1;
    //         $newbankRec->created_by = \Auth::user()->id;
    //         $newbankRec->save();

    //         return json_encode(array('id'=>$newbankRec->id, 'response'=>'success'));
    //     }
    // }

    // public function uncheckEntry($accId){
    //     $bankRec = BankRecEntry::find($accId);
    //     if($bankRec){
    //         $bankRec->status = 0;
    //         $bankRec->update();
    //         return json_encode(array('id'=>$bankRec->acccount_transaction_id, 'response'=>'success'));
    //     } else {
    //         return json_encode(array('response'=>'error'));
    //     }
    // }

    // public function bankRecClearedEntriesIndex(){
    //     if(\Auth::user()->can('view bank rec'))
    //     {
    //         $showTable = 'No';
    //         $accounts = ChartOfAccount::whereIn('id', [227,228,229])->orderBy('name')->get()->pluck('name', 'id');
    //         $accounts->prepend('-- Select Banck Account --', '');
    //         return view('bankRec.cleared_entries', compact('showTable','accounts'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    // public function bankRecClearedEntriesSearch(Request $request){
    //     if(\Auth::user()->can('view bank rec'))
    //     {
    //         $accounts = ChartOfAccount::whereIn('id', [227,228,229])->orderBy('name')->get()->pluck('name', 'id');
    //         $accounts->prepend('-- Select Banck Account --', '');

    //         if(isset( $request->account_id) && isset( $request->start_date) && isset( $request->end_date)){
    //             $showTable = 'Yes';
    //             $start_date = $request->start_date;
    //             $end_date = $request->end_date;
    //             $chartOfAccount = $request->account_id;

    //             $bankRecs = BankRecEntry::with('related_chart_of_account', 'branch', 'type')
    //                                             ->where('branch_id', \Auth::user()->branchId())
    //                                             ->where('status', 1)
    //                                             ->where('chart_of_account_id', $request->account_id)
    //                                             ->where('date', '>=', $start_date)
    //                                             ->where('date', '<=', $end_date)
    //                                             ->get();
    //         }
    //         return view('bankRec.cleared_entries', compact('showTable','accounts','start_date','end_date','chartOfAccount','bankRecs'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }

    // }

    // public function bankRecUnclearedEntriesIndex(){
    //     if(\Auth::user()->can('view bank rec'))
    //     {
    //         $showTable = 'No';
    //         $accounts = ChartOfAccount::whereIn('id', [227,228,229])->orderBy('name')->get()->pluck('name', 'id');
    //         $accounts->prepend('-- Select Banck Account --', '');
    //         return view('bankRec.uncleared_entries', compact('showTable','accounts'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    // public function bankRecUnclearedEntriesSearch(Request $request){
    //     if(\Auth::user()->can('view bank rec'))
    //     {
    //         $accounts = ChartOfAccount::whereIn('id', [227,228,229])->orderBy('name')->get()->pluck('name', 'id');
    //         $accounts->prepend('-- Select Banck Account --', '');

    //         if(isset( $request->account_id) && isset( $request->start_date) && isset( $request->end_date)){
    //             $showTable = 'Yes';
    //             $start_date = $request->start_date;
    //             $end_date = $request->end_date;
    //             $chartOfAccount = $request->account_id;

    //             $bankRecs = BankRecEntry::with('related_chart_of_account', 'branch', 'type')
    //                                             ->where('branch_id', \Auth::user()->branchId())
    //                                             ->where('status', 1)
    //                                             ->where('chart_of_account_id', $request->account_id)
    //                                             ->where('date', '>=', $start_date)
    //                                             ->where('date', '<=', $end_date)
    //                                             ->get()
    //                                             ->pluck('acccount_transaction_id');

    //             $transactions = AccountTransaction::with('related_chart_of_account', 'branch', 'type', 'customer')
    //                                                     ->where('branch_id', \Auth::user()->branchId())
    //                                                     ->whereNotIn('id', $bankRecs)
    //                                                     ->where('status', 1)
    //                                                     ->where('chart_of_account_id', $request->account_id)
    //                                                     ->where('date', '>=', $start_date)
    //                                                     ->where('date', '<=', $end_date)
    //                                                     ->get();
    //         }
    //         return view('bankRec.uncleared_entries', compact('showTable','accounts','start_date','end_date','chartOfAccount','bankRecs','transactions'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    // public function bankRecIndex(){
    //     if(\Auth::user()->can('view bank rec'))
    //     {
    //         $showTable = 'No';
    //         $accounts = ChartOfAccount::whereIn('id', [227,228,229])->orderBy('name')->get()->pluck('name', 'id');
    //         $accounts->prepend('-- Select Banck Account --', '');
    //         return view('bankRec.reconciliation', compact('showTable','accounts'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    public function bankRecStatement($recId)
    {
        if (\Auth::user()->can('view bank rec')) {
            $showTable = 'Yes';

            $reconciliation = BankReconciliation::with('cleareRecEntriesDebit', 'cleareRecEntriesCredit', 'unCleareRecEntriesDebit', 'unCleareRecEntriesCredit')->find($recId);

            return view('bankRec.reconciliation', compact('showTable', 'reconciliation'));
        } else {
            return redirect()->route('bank_rec.index')->with('error', __('Permission denied.'));
        }
    }

    public function bankRecStatementUpdate(Request $request)
    {
        $recId = $request->rec_id;
        $statementBalance = $request->statement_balance;
        $cashDifference = $request->cash_difference;

        $reconciliation = BankReconciliation::find($recId);
        $reconciliation->statement_balance = $statementBalance;
        $reconciliation->difference = $cashDifference;
        $reconciliation->update();

        if ($reconciliation) {
            return json_encode('success');
        } else {
            return json_encode('error');
        }
    }

    public function destroyBankRec($id)
    {
        if (\Auth::user()->can('delete bank rec')) {
            BankReconciliation::find($id)->delete();
            // $rec->status = 0;
            // $rec->update();

            BankRecEntry::where('bank_rec_id', $id)->delete();
            return redirect()->back()->with('success', __('Bank Rec Deleted Successful!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bankRecIndexOld(Request $request)
    {

        if (\Auth::user()->can('view bank rec')) {
            $data['month']     = [
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sept',
                'Oct',
                'Nov',
                'Dec',
                'Jan',
                'Feb',
                'Mar',
                'Total',
            ];
            $data['monthList'] = $month = $this->yearMonth();
            $data['yearList']  = $this->yearList();

            if (isset($request->year)) {
                $year = $request->year;
            } else {
                $year = date('Y');
            }
            $data['currentYear'] = $year;
            // $year = ( date('m') > 6) ? date('Y') + 1 : date('Y');
            $endYear = $year + 1;

            // -------------------------------REVENUE INCOME-------------------------------------------------

            $incomes = Revenue::selectRaw('sum(revenues.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id');
            // $incomes->where('created_by', '=', \Auth::user()->creatorId());
            $incomes->where('branch_id', '=', \Auth::user()->branchId());
            $incomes->whereRAW('YEAR(date) =?', [$request->year]);
            $incomes->groupBy('month', 'year', 'category_id');
            $incomes        = $incomes->get();
            // dd($request->year);
            $tmpIncomeArray = [];
            foreach ($incomes as $income) {
                $tmpIncomeArray[$income->category_id][$income->month] = $income->amount;
            }

            $incomeCatAmount_1  = $incomeCatAmount_2 = $incomeCatAmount_3 = $incomeCatAmount_4 = $incomeCatAmount_5 = $incomeCatAmount_6 = $incomeCatAmount_7 = $incomeCatAmount_8 = $incomeCatAmount_9 = $incomeCatAmount_10 = $incomeCatAmount_11 = $incomeCatAmount_12 = 0;
            $revenueIncomeArray = array();
            foreach ($tmpIncomeArray as $cat_id => $record) {

                $tmp             = [];
                $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $sumData         = [];
                for ($i = 1; $i <= 12; $i++) {
                    $sumData[] = array_key_exists($i, $record) ? $record[$i] : 0;
                }

                $month_1 = array_slice($sumData, 3, 1);
                $month_2 = array_slice($sumData, 4, 1);
                $month_3 = array_slice($sumData, 5, 1);
                $month_4 = array_slice($sumData, 6, 1);
                $month_5 = array_slice($sumData, 7, 1);
                $month_6 = array_slice($sumData, 8, 1);
                $month_7 = array_slice($sumData, 9, 1);
                $month_8 = array_slice($sumData, 10, 1);
                $month_9 = array_slice($sumData, 11, 1);
                $month_10 = array_slice($sumData, 12, 1);
                $month_11 = array_slice($sumData, 1, 1);
                $month_12 = array_slice($sumData, 2, 1);


                $incomeData[__('Apr')] = $sum_1 = array_sum($month_1);
                $incomeData[__('May')] = $sum_2 = array_sum($month_2);
                $incomeData[__('Jun')] = $sum_3 = array_sum($month_3);
                $incomeData[__('Jul')] = $sum_4 = array_sum($month_4);
                $incomeData[__('Aug')] = $sum_5 = array_sum($month_5);
                $incomeData[__('Sept')] = $sum_6 = array_sum($month_6);
                $incomeData[__('Oct')] = $sum_7 = array_sum($month_7);
                $incomeData[__('Nov')] = $sum_8 = array_sum($month_8);
                $incomeData[__('Dec')] = $sum_9 = array_sum($month_9);
                $incomeData[__('Jan')] = $sum_10 = array_sum($month_10);
                $incomeData[__('Feb')] = $sum_11 = array_sum($month_11);
                $incomeData[__('Mar')] = $sum_12 = array_sum($month_12);
                $incomeData[__('Total')]   = array_sum(
                    array(
                        $sum_1,
                        $sum_2,
                        $sum_3,
                        $sum_4,
                        $sum_5,
                        $sum_6,
                        $sum_7,
                        $sum_8,
                        $sum_9,
                        $sum_10,
                        $sum_11,
                        $sum_12,
                    )
                );

                $incomeCatAmount_1 += $sum_1;
                $incomeCatAmount_2 += $sum_2;
                $incomeCatAmount_3 += $sum_3;
                $incomeCatAmount_4 += $sum_4;
                $incomeCatAmount_5 += $sum_5;
                $incomeCatAmount_6 += $sum_6;
                $incomeCatAmount_7 += $sum_7;
                $incomeCatAmount_8 += $sum_8;
                $incomeCatAmount_9 += $sum_9;
                $incomeCatAmount_10 += $sum_10;
                $incomeCatAmount_11 += $sum_11;
                $incomeCatAmount_12 += $sum_12;

                $data['month'] = array_keys($incomeData);
                $tmp['amount'] = array_values($incomeData);

                $revenueIncomeArray[] = $tmp;
            }

            $data['incomeCatAmount'] = $incomeCatAmount = [
                $incomeCatAmount_1,
                $incomeCatAmount_2,
                $incomeCatAmount_3,
                $incomeCatAmount_4,
                $incomeCatAmount_5,
                $incomeCatAmount_6,
                $incomeCatAmount_7,
                $incomeCatAmount_8,
                $incomeCatAmount_9,
                $incomeCatAmount_10,
                $incomeCatAmount_11,
                $incomeCatAmount_12,
                array_sum(
                    array(
                        $incomeCatAmount_1,
                        $incomeCatAmount_2,
                        $incomeCatAmount_3,
                        $incomeCatAmount_4,
                        $incomeCatAmount_5,
                        $incomeCatAmount_6,
                        $incomeCatAmount_7,
                        $incomeCatAmount_8,
                        $incomeCatAmount_9,
                        $incomeCatAmount_10,
                        $incomeCatAmount_11,
                        $incomeCatAmount_12,
                    )
                ),
            ];

            $data['revenueIncomeArray'] = $revenueIncomeArray;

            //-----------------------INVOICE INCOME---------------------------------------------

            $invoices = Invoice::selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,invoice_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
            $invoices->whereRAW('YEAR(send_date) =?', [$year]);
            if (!empty($request->customer)) {
                $invoices->where('customer_id', '=', $request->customer);
            }
            $invoices        = $invoices->get();
            $invoiceTmpArray = [];
            foreach ($invoices as $invoice) {
                $invoiceTmpArray[$invoice->category_id][$invoice->month][] = $invoice->getTotal();
            }

            $invoiceCatAmount_1 = $invoiceCatAmount_2 = $invoiceCatAmount_3 = $invoiceCatAmount_4 = $incomeCatAmount_5 = $incomeCatAmount_6 = $incomeCatAmount_7 = $incomeCatAmount_8 = $incomeCatAmount_9 = $incomeCatAmount_10 = $incomeCatAmount_11 = $incomeCatAmount_12 = 0;
            $invoiceIncomeArray = array();
            foreach ($invoiceTmpArray as $cat_id => $record) {

                $invoiceTmp             = [];
                $invoiceTmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $invoiceSumData         = [];
                for ($i = 1; $i <= 12; $i++) {
                    $invoiceSumData[] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
                }

                $month_1                          = array_slice($invoiceSumData, 3, 1);
                $month_2                          = array_slice($invoiceSumData, 4, 1);
                $month_3                          = array_slice($invoiceSumData, 5, 1);
                $month_4                          = array_slice($invoiceSumData, 6, 1);
                $month_5                          = array_slice($invoiceSumData, 7, 1);
                $month_6                          = array_slice($invoiceSumData, 8, 1);
                $month_7                          = array_slice($invoiceSumData, 9, 1);
                $month_8                          = array_slice($invoiceSumData, 10, 1);
                $month_9                          = array_slice($invoiceSumData, 11, 1);
                $month_10                          = array_slice($invoiceSumData, 12, 1);
                $month_11                          = array_slice($invoiceSumData, 1, 1);
                $month_12                          = array_slice($invoiceSumData, 2, 1);
                $invoiceIncomeData[__('Apr')] = $sum_1 = array_sum($month_1);
                $invoiceIncomeData[__('May')] = $sum_2 = array_sum($month_2);
                $invoiceIncomeData[__('Jun')] = $sum_3 = array_sum($month_3);
                $invoiceIncomeData[__('Jul')] = $sum_4 = array_sum($month_4);
                $invoiceIncomeData[__('Aug')] = $sum_5 = array_sum($month_5);
                $invoiceIncomeData[__('Sept')] = $sum_6 = array_sum($month_6);
                $invoiceIncomeData[__('Oct')] = $sum_7 = array_sum($month_7);
                $invoiceIncomeData[__('Nov')] = $sum_8 = array_sum($month_8);
                $invoiceIncomeData[__('Dec')] = $sum_9 = array_sum($month_9);
                $invoiceIncomeData[__('Jan')] = $sum_10 = array_sum($month_10);
                $invoiceIncomeData[__('Feb')] = $sum_11 = array_sum($month_11);
                $invoiceIncomeData[__('Mar')] = $sum_12 = array_sum($month_12);
                $invoiceIncomeData[__('Total')]   = array_sum(
                    array(
                        $sum_1,
                        $sum_2,
                        $sum_3,
                        $sum_4,
                        $sum_5,
                        $sum_6,
                        $sum_7,
                        $sum_8,
                        $sum_9,
                        $sum_10,
                        $sum_11,
                        $sum_12,
                    )
                );
                $invoiceCatAmount_1               += $sum_1;
                $invoiceCatAmount_2               += $sum_2;
                $invoiceCatAmount_3               += $sum_3;
                $invoiceCatAmount_4               += $sum_4;
                $invoiceCatAmount_5               += $sum_5;
                $invoiceCatAmount_6               += $sum_6;
                $invoiceCatAmount_7               += $sum_7;
                $invoiceCatAmount_8               += $sum_8;
                $invoiceCatAmount_9               += $sum_9;
                $invoiceCatAmount_10              += $sum_10;
                $invoiceCatAmount_11              += $sum_11;
                $invoiceCatAmount_12              += $sum_12;

                $invoiceTmp['amount'] = array_values($invoiceIncomeData);

                $invoiceIncomeArray[] = $invoiceTmp;
            }

            $data['invoiceIncomeCatAmount'] = $invoiceIncomeCatAmount = [
                $invoiceCatAmount_1,
                $invoiceCatAmount_2,
                $invoiceCatAmount_3,
                $invoiceCatAmount_4,
                // $invoiceCatAmount_5,
                // $invoiceCatAmount_6,
                // $invoiceCatAmount_7,
                // $invoiceCatAmount_8,
                // $invoiceCatAmount_9,
                // $invoiceCatAmount_10,
                // $invoiceCatAmount_11,
                // $invoiceCatAmount_12,
                array_sum(
                    array(
                        $invoiceCatAmount_1,
                        $invoiceCatAmount_2,
                        $invoiceCatAmount_3,
                        $invoiceCatAmount_4,
                        // $invoiceCatAmount_5,
                        // $invoiceCatAmount_6,
                        // $invoiceCatAmount_7,
                        // $invoiceCatAmount_8,
                        // $invoiceCatAmount_9,
                        // $invoiceCatAmount_10,
                        // $invoiceCatAmount_11,
                        // $invoiceCatAmount_12,
                    )
                ),
            ];


            $data['invoiceIncomeArray'] = $invoiceIncomeArray;

            $data['totalIncome'] = $totalIncome = array_map(
                function () {
                    return array_sum(func_get_args());
                },
                $invoiceIncomeCatAmount,
                $incomeCatAmount
            );

            //---------------------------------PAYMENT EXPENSE-----------------------------------

            $expenses = Payment::selectRaw('sum(payments.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id');
            $expenses->where('created_by', '=', \Auth::user()->creatorId());
            $expenses->whereRAW('YEAR(date) =?', [$year]);
            $expenses->groupBy('month', 'year', 'category_id');
            $expenses = $expenses->get();

            $tmpExpenseArray = [];
            foreach ($expenses as $expense) {
                $tmpExpenseArray[$expense->category_id][$expense->month] = $expense->amount;
            }

            $expenseArray       = [];
            $expenseCatAmount_1 = $expenseCatAmount_2 = $expenseCatAmount_3 = $expenseCatAmount_4 = $expenseCatAmount_5 = $expenseCatAmount_6 = $expenseCatAmount_7 = $expenseCatAmount_8 = $expenseCatAmount_9 = $expenseCatAmount_10 = $expenseCatAmount_11 = $expenseCatAmount_12 = 0;
            foreach ($tmpExpenseArray as $cat_id => $record) {
                $tmp             = [];
                $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $expenseSumData  = [];
                for ($i = 1; $i <= 12; $i++) {
                    $expenseSumData[] = array_key_exists($i, $record) ? $record[$i] : 0;
                }

                $month_1 = array_slice($expenseSumData, 3, 1);
                $month_2 = array_slice($expenseSumData, 4, 1);
                $month_3 = array_slice($expenseSumData, 5, 1);
                $month_4 = array_slice($expenseSumData, 6, 1);
                $month_5 = array_slice($expenseSumData, 7, 1);
                $month_6 = array_slice($expenseSumData, 8, 1);
                $month_7 = array_slice($expenseSumData, 9, 1);
                $month_8 = array_slice($expenseSumData, 10, 1);
                $month_9 = array_slice($expenseSumData, 11, 1);
                $month_10 = array_slice($expenseSumData, 12, 1);
                $month_11 = array_slice($expenseSumData, 1, 1);
                $month_12 = array_slice($expenseSumData, 2, 1);

                $expenseData[__('Apr')] = $sum_1 = array_sum($month_1);
                $expenseData[__('May')] = $sum_2 = array_sum($month_2);
                $expenseData[__('Jun')] = $sum_3 = array_sum($month_3);
                $expenseData[__('Jul')] = $sum_4 = array_sum($month_4);
                $expenseData[__('Aug')] = $sum_5 = array_sum($month_5);
                $expenseData[__('Sept')] = $sum_6 = array_sum($month_6);
                $expenseData[__('Oct')] = $sum_7 = array_sum($month_7);
                $expenseData[__('Nov')] = $sum_8 = array_sum($month_8);
                $expenseData[__('Dec')] = $sum_9 = array_sum($month_9);
                $expenseData[__('Jan')] = $sum_10 = array_sum($month_10);
                $expenseData[__('Feb')] = $sum_11 = array_sum($month_11);
                $expenseData[__('Mar')] = $sum_12 = array_sum($month_12);
                $expenseData[__('Total')]   = array_sum(
                    array(
                        $sum_1,
                        $sum_2,
                        $sum_3,
                        $sum_4,
                        $sum_5,
                        $sum_6,
                        $sum_7,
                        $sum_8,
                        $sum_9,
                        $sum_10,
                        $sum_11,
                        $sum_12,
                    )
                );

                $expenseCatAmount_1 += $sum_1;
                $expenseCatAmount_2 += $sum_2;
                $expenseCatAmount_3 += $sum_3;
                $expenseCatAmount_4 += $sum_4;
                $expenseCatAmount_5 += $sum_5;
                $expenseCatAmount_6 += $sum_6;
                $expenseCatAmount_7 += $sum_7;
                $expenseCatAmount_8 += $sum_8;
                $expenseCatAmount_9 += $sum_9;
                $expenseCatAmount_10 += $sum_10;
                $expenseCatAmount_11 += $sum_11;
                $expenseCatAmount_12 += $sum_12;

                $data['month'] = array_keys($expenseData);
                $tmp['amount'] = array_values($expenseData);

                $expenseArray[] = $tmp;
            }

            $data['expenseCatAmount'] = $expenseCatAmount = [
                $expenseCatAmount_1,
                $expenseCatAmount_2,
                $expenseCatAmount_3,
                $expenseCatAmount_4,
                $expenseCatAmount_5,
                $expenseCatAmount_6,
                $expenseCatAmount_7,
                $expenseCatAmount_8,
                $expenseCatAmount_9,
                $expenseCatAmount_10,
                $expenseCatAmount_11,
                $expenseCatAmount_12,
                array_sum(
                    array(
                        $expenseCatAmount_1,
                        $expenseCatAmount_2,
                        $expenseCatAmount_3,
                        $expenseCatAmount_4,
                        $expenseCatAmount_5,
                        $expenseCatAmount_6,
                        $expenseCatAmount_7,
                        $expenseCatAmount_8,
                        $expenseCatAmount_9,
                        $expenseCatAmount_10,
                        $expenseCatAmount_11,
                        $expenseCatAmount_12,
                    )
                ),
            ];
            $data['expenseArray']     = $expenseArray;

            //----------------------------EXPENSE BILL-----------------------------------------------------------------------

            $bills = Bill::selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,bill_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
            $bills->whereRAW('YEAR(send_date) =?', [$year]);
            if (!empty($request->customer)) {
                $bills->where('vender_id', '=', $request->vender);
            }
            $bills        = $bills->get();
            $billTmpArray = [];
            foreach ($bills as $bill) {
                $billTmpArray[$bill->category_id][$bill->month][] = $bill->getTotal();
            }

            $billExpenseArray       = [];
            $billExpenseCatAmount_1 = $billExpenseCatAmount_2 = $billExpenseCatAmount_3 = $billExpenseCatAmount_4 = $billExpenseCatAmount_5 = $billExpenseCatAmount_6 = $billExpenseCatAmount_7 = $billExpenseCatAmount_8 = $billExpenseCatAmount_9 = $billExpenseCatAmount_10 = $billExpenseCatAmount_11 = $billExpenseCatAmount_12 = 0;
            foreach ($billTmpArray as $cat_id => $record) {
                $billTmp             = [];
                $billTmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $billExpensSumData   = [];
                for ($i = 1; $i <= 12; $i++) {
                    $billExpensSumData[] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
                }

                $month_1 = array_slice($billExpensSumData, 3, 1);
                $month_2 = array_slice($billExpensSumData, 4, 1);
                $month_3 = array_slice($billExpensSumData, 5, 1);
                $month_4 = array_slice($billExpensSumData, 6, 1);
                $month_5 = array_slice($billExpensSumData, 7, 1);
                $month_6 = array_slice($billExpensSumData, 8, 1);
                $month_7 = array_slice($billExpensSumData, 9, 1);
                $month_8 = array_slice($billExpensSumData, 10, 1);
                $month_9 = array_slice($billExpensSumData, 11, 1);
                $month_10 = array_slice($billExpensSumData, 12, 1);
                $month_11 = array_slice($billExpensSumData, 1, 1);
                $month_12 = array_slice($billExpensSumData, 2, 1);

                $billExpenseData[__('Apr')] = $sum_1 = array_sum($month_1);
                $billExpenseData[__('May')] = $sum_2 = array_sum($month_2);
                $billExpenseData[__('Jun')] = $sum_3 = array_sum($month_3);
                $billExpenseData[__('Jul')] = $sum_4 = array_sum($month_4);
                $billExpenseData[__('Aug')] = $sum_5 = array_sum($month_5);
                $billExpenseData[__('Sept')] = $sum_6 = array_sum($month_6);
                $billExpenseData[__('Oct')] = $sum_7 = array_sum($month_7);
                $billExpenseData[__('Nov')] = $sum_8 = array_sum($month_8);
                $billExpenseData[__('Dec')] = $sum_9 = array_sum($month_9);
                $billExpenseData[__('Jan')] = $sum_10 = array_sum($month_10);
                $billExpenseData[__('Feb')] = $sum_11 = array_sum($month_11);
                $billExpenseData[__('Mar')] = $sum_12 = array_sum($month_12);
                $billExpenseData[__('Total')]   = array_sum(
                    array(
                        $sum_1,
                        $sum_2,
                        $sum_3,
                        $sum_4,
                        $sum_5,
                        $sum_6,
                        $sum_7,
                        $sum_8,
                        $sum_9,
                        $sum_10,
                        $sum_11,
                        $sum_12,
                    )
                );

                $billExpenseCatAmount_1 += $sum_1;
                $billExpenseCatAmount_2 += $sum_2;
                $billExpenseCatAmount_3 += $sum_3;
                $billExpenseCatAmount_4 += $sum_4;
                $billExpenseCatAmount_5 += $sum_5;
                $billExpenseCatAmount_6 += $sum_6;
                $billExpenseCatAmount_7 += $sum_7;
                $billExpenseCatAmount_8 += $sum_8;
                $billExpenseCatAmount_9 += $sum_9;
                $billExpenseCatAmount_10 += $sum_10;
                $billExpenseCatAmount_11 += $sum_11;
                $billExpenseCatAmount_12 += $sum_12;

                $data['month']     = array_keys($billExpenseData);
                $billTmp['amount'] = array_values($billExpenseData);

                $billExpenseArray[] = $billTmp;
            }

            $data['billExpenseCatAmount'] = $billExpenseCatAmount = [
                $billExpenseCatAmount_1,
                $billExpenseCatAmount_2,
                $billExpenseCatAmount_3,
                $billExpenseCatAmount_4,
                $billExpenseCatAmount_5,
                $billExpenseCatAmount_6,
                $billExpenseCatAmount_7,
                $billExpenseCatAmount_8,
                $billExpenseCatAmount_9,
                $billExpenseCatAmount_10,
                $billExpenseCatAmount_11,
                $billExpenseCatAmount_12,
                array_sum(
                    array(
                        $billExpenseCatAmount_1,
                        $billExpenseCatAmount_2,
                        $billExpenseCatAmount_3,
                        $billExpenseCatAmount_4,
                        $billExpenseCatAmount_5,
                        $billExpenseCatAmount_6,
                        $billExpenseCatAmount_7,
                        $billExpenseCatAmount_8,
                        $billExpenseCatAmount_9,
                        $billExpenseCatAmount_10,
                        $billExpenseCatAmount_11,
                        $billExpenseCatAmount_12,
                    )
                ),
            ];

            $data['billExpenseArray'] = $billExpenseArray;


            $data['totalExpense'] = $totalExpense = array_map(
                function () {
                    return array_sum(func_get_args());
                },
                $billExpenseCatAmount,
                $expenseCatAmount
            );


            foreach ($totalIncome as $k => $income) {
                $netProfit[] = $income - $totalExpense[$k];
            }
            $data['netProfitArray'] = $netProfit;

            $filter['startDateRange'] = 'April ' . $year;
            $filter['endDateRange']   = 'March ' . $endYear;

            return view('bankRec.index', compact('filter'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //TRIAL BALANCE
    public function trialBalanceIndex()
    {
        if (\Auth::user()->can('view trial balance')) {
            $showTable = 'No';
            $zeroValue = 'no';
            $currencySymbol = $this->currencySymbol;
            return view('trialBalance.index', compact('showTable', 'zeroValue', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function trialBalanceSearch(Request $request)
    {
        if (\Auth::user()->can('view trial balance')) {
            if (isset($request->end_date) && isset($request->zero_value)) {
                $showTable = 'Yes';
                // $startDate = $request->start_date;
                $endDate = $request->end_date;
                $zeroValue = $request->zero_value;


                $currMonth =  date('Y-m', strtotime(date($endDate)));
                $currentMonth = $currMonth . '-01';

                $yearStartDate = "2021-04-01";
                $yearEndDate = "2022-03-31";

                $accounts = GeneralLedgerAccount::get();

                $trialbalances = [];
                foreach ($accounts as $account) {
                    $UptoSelectedVal = 0;
                    $drcrUptoSelectedVal = "";


                    $transactionsUptoSelectedSelectedMonth = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                        ->where('status', 1)
                        ->where('branch_id', \Auth::user()->branchId())
                        // ->where('date', '>=', $yearStartDate)
                        ->where('date', '<=', $endDate)
                        ->where('chart_of_account_id', $account->id)
                        ->get();

                    foreach ($transactionsUptoSelectedSelectedMonth as $transactionUptoSelectMonth) {
                        $debit_amount_upto_selected_month = $transactionUptoSelectMonth->debit_amount;
                        $credit_amount_upto_selected_month = $transactionUptoSelectMonth->credit_amount;
                    }

                    if ($debit_amount_upto_selected_month >= $credit_amount_upto_selected_month) {
                        $UptoSelectedVal = $debit_amount_upto_selected_month - $credit_amount_upto_selected_month;
                        $drcrUptoSelectedVal = "DR";
                    } else if ($credit_amount_upto_selected_month > $debit_amount_upto_selected_month) {
                        $UptoSelectedVal = $credit_amount_upto_selected_month - $debit_amount_upto_selected_month;
                        $drcrUptoSelectedVal = "CR";
                    }


                    $CurrentMonthVal = 0;
                    $drcrCurrentMonthVal = "";


                    $transactionsCurrentMonth = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                        ->where('status', 1)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('date', '>=', $currentMonth)
                        ->where('date', '<=', $endDate)
                        ->where('chart_of_account_id', $account->id)
                        ->get();

                    foreach ($transactionsCurrentMonth as $transactionCurentMonth) {
                        $debit_amount_current_month = $transactionCurentMonth->debit_amount;
                        $credit_amount_current_month = $transactionCurentMonth->credit_amount;
                    }

                    if ($debit_amount_current_month >= $credit_amount_current_month) {
                        $CurrentMonthVal = $debit_amount_current_month - $credit_amount_current_month;
                        $drcrCurrentMonthVal = "DR";
                    } else if ($credit_amount_current_month > $debit_amount_current_month) {
                        $CurrentMonthVal = $credit_amount_current_month - $debit_amount_current_month;
                        $drcrCurrentMonthVal = "CR";
                    }

                    $trialbalances[] = array('name' => $account->account_name, 'id' => $account->id, 'UptoSelectedVal' => $UptoSelectedVal, 'drcrUptoSelectedVal' => $drcrUptoSelectedVal, 'CurrentMonthVal' => $CurrentMonthVal, 'drcrCurrentMonthVal' => $drcrCurrentMonthVal);
                }
                $currencySymbol = $this->currencySymbol;
                return view('trialBalance.index', compact('trialbalances', 'showTable', 'endDate', 'zeroValue', 'currencySymbol'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
