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
use App\PrimeEntryBook;
use App\TrialBalance;
use App\GeneralJournal;
use App\AccountTransaction;
use App\Utility;
use App\CashBook;
use App\PettyCashBook;
use App\SalesJournal;
use App\SalesReturnJournal;
use App\PurchaseJournal;
use App\PurchaseReturnJournal;
use App\AccountSection;
use App\AccountGroup;
use App\DailyOpeningBalance;
use App\FinancialEntry;
use App\Grn;
use App\Inventory;
use App\SalesReturn;
use App\Product;
use App\Setting;
use App\StockAdjustment;
use App\StockTransaction;

class PrimeEntryBookController extends Controller
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

    //SALES JOURNAL
    public function salesJournalIndex()
    {
        if (\Auth::user()->can('view sales journal')) {
            $showTable = 'No';
            // $salesjournals = SalesJournal::with('chart_of_account')->get();
            return view('primeEntryBook.sales_journal', compact('showTable'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function salesJournalSearch(Request $request)
    {
        if (\Auth::user()->can('view sales journal')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            if (isset($request->start_date) && isset($request->end_date)) {
                $showTable = 'Yes';
                $sales = Sale::where('branch_id', \Auth::user()->branchId())
                    ->whereIn('status', [1, 2, 3])
                    ->where('date_of_sale', '>=', $request->start_date)
                    ->where('date_of_sale', '<=', $request->end_date)
                    ->get();
                // $salesjournals = SalesJournal::with('chart_of_account')->get();
                return view('primeEntryBook.sales_journal', compact('showTable', 'start_date', 'end_date', 'sales'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //SALES RETURN JOURNAL
    public function salesReturnJournalIndex()
    {
        if (\Auth::user()->can('view sales return journal')) {
            $showTable = 'No';
            // $salesreturnjournals = SalesReturnJournal::with('chart_of_account')->get();
            return view('primeEntryBook.sales_return_journal', compact('showTable'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function salesReturnJournalSearch(Request $request)
    {
        if (\Auth::user()->can('view sales return journal')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            if (isset($request->start_date) && isset($request->end_date)) {
                $showTable = 'Yes';
                $salesreturns = SalesReturn::select('branch_id', 'sale_id', 'invoice_id', \DB::raw('DATE(created_at) as date'), \DB::raw('SUM(return_amount) AS amount'))
                    ->where('branch_id', \Auth::user()->branchId())
                    ->where('created_at', '>=', $request->start_date)
                    ->where('created_at', '<=', $request->end_date)
                    ->groupBy('branch_id', 'sale_id', 'invoice_id', 'date')
                    ->get();
                // $salesreturnjournals = SalesReturnJournal::with('chart_of_account')->get();
                return view('primeEntryBook.sales_return_journal', compact('showTable', 'start_date', 'end_date', 'salesreturns'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //PURCHASE JOURNAL
    public function purchaseJournalIndex()
    {
        if (\Auth::user()->can('view purchase journal')) {
            $showTable = 'No';
            // $purchasejournals = PurchaseJournal::with('chart_of_account')->get();
            return view('primeEntryBook.purchase_journal', compact('showTable'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function purchaseJournalSearch(Request $request)
    {
        if (\Auth::user()->can('view purchase journal')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = date("d", strtotime($start_date));
                $endDate = date("d", strtotime($end_date));
                $showTable = 'Yes';
                // $purchasejournals = PurchaseJournal::where('branch_id', \Auth::user()->branchId())
                //                                     ->where('status', 1)
                //                                     ->where('date', '>=', $startDate)
                //                                     ->where('date', '<=', $endDate)
                //                                     ->get();
                // $purchasejournals = PurchaseJournal::with('chart_of_account')->get();
                $grns = Grn::with('vender')->where('branch_id', \Auth::user()->branchId())
                    ->where('order_date', '>=', $request->start_date)
                    ->where('order_date', '<=', $request->end_date)
                    ->orderBy('order_date')
                    ->get();
                return view('primeEntryBook.purchase_journal', compact('showTable', 'start_date', 'end_date', 'grns'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //PURCHASE RETURN JOURNAL
    public function purchaseReturnJournalIndex()
    {
        if (\Auth::user()->can('view purchase return journal')) {
            $showTable = 'No';
            // $purchasereturnjournals = PurchaseReturnJournal::with('chart_of_account')->get();
            return view('primeEntryBook.purchase_return_journal', compact('showTable'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function purchaseReturnJournalSearch(Request $request)
    {
        if (\Auth::user()->can('view purchase return journal')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = date("d", strtotime($start_date));
                $endDate = date("d", strtotime($end_date));
                $showTable = 'Yes';
                $purchasereturnjournals = PurchaseReturnJournal::where('branch_id', \Auth::user()->branchId())
                    ->where('status', 1)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->get();
                // $purchasereturnjournals = PurchaseReturnJournal::with('chart_of_account')->get();
                return view('primeEntryBook.purchase_return_journal', compact('showTable', 'startDate', 'endDate'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //CASH BOOK
    // public function cashBookOldIndex()
    // {
    //     if (\Auth::user()->can('view cash book')) {
    //         $showTable = 'No';
    //         $banks = GeneralLedgerAccount::select('id', 'account_name AS name')->where('status', 1)->whereIn('detail_type_id', [15])->get()->pluck('name', 'id');
    //         $banks->prepend('Select Bank Account', '');
    //         return view('primeEntryBook.cash_book', compact('showTable', 'banks'));
    //     } else {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }
    public function cashBookIndex()
    {
        if (\Auth::user()->can('view cash book')) {
            $showTable = 'Yes';

            // Query the financial entries with related data
            $allAvailableFinancialEntries = FinancialEntry::with([
                'receipt:id,receipt_code',
                'customReceipt:id,receipt_code',
                'customer:id,name',
                'vendor:id,name',
                'product:id,barcode',
                'invoice:id,invoice_code'
            ])
                ->where('status', 0)
                ->where('branch_id', \Auth::user()->branch_id)
                ->orderBy('date')
                ->orderBy('created_at')
                ->get()
                ->map(function ($entry) {
                    $entry->receipt_number = $entry->receipt->receipt_code
                        ?? $entry->customReceipt->receipt_code;
                    $entry->customer_name = $entry->customer->name ?? ($entry->vendor->name ?? '-');
                    $entry->product_name = $entry->product->barcode ??   '-';
                    $entry->invoice_number = $entry->invoice->invoice_code ??   '-';

                    return $entry;
                });

            $records = $allAvailableFinancialEntries;

            $openingBalance = DailyOpeningBalance::where('branch_id', \Auth::user()->branch_id)
                ->where('status', 1)
                ->first();

            $users = User::where('is_active', 1)->orderBy('name')->get();
            // dd($records);
            $currencySymbol = $this->currencySymbol;
            return view('primeEntryBook.cash_book', compact('showTable', 'records', 'openingBalance', 'users', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function cashBookSearch(Request $request)
    {
        if (\Auth::user()->can('view cash book')) {
            $showTable = 'No';
            $banks = GeneralLedgerAccount::select('id', 'account_name AS name')->where('status', 1)->whereIn('detail_type_id', [15])->get()->pluck('name', 'id');
            $banks->prepend('Select Bank Account', '');

            if (isset($request->bank_id) && isset($request->start_date) && isset($request->end_date)) {
                $showTable = 'Yes';
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $bank = $request->bank_id;

                $openingBalance = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                    // ->where('branch_id', \Auth::user()->branchId())
                    ->where('chart_of_account_id', $request->bank_id)
                    ->where('status', 1)
                    ->where('date', '<', $start_date)
                    ->get();

                $transactions = AccountTransaction::with('related_chart_of_account')->where('chart_of_account_id', $request->bank_id)
                    // ->where('branch_id', \Auth::user()->branchId())
                    ->where('status', 1)
                    ->where('date', '>=', $request->start_date)
                    ->where('date', '<=', $request->end_date)
                    ->get();
                $currencySymbol = $this->currencySymbol;
                return view('primeEntryBook.cash_book', compact('showTable', 'start_date', 'end_date', 'banks', 'bank', 'transactions', 'openingBalance', 'currencySymbol'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function dailyCashBookIndex()
    {
        if (\Auth::user()->can('view cash book')) {
            $showTable = 'No';
            $currencySymbol = $this->currencySymbol;
            return view('primeEntryBook.daily_cash_book', compact('showTable', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function dailyCashBookSearch(Request $request)
    {
        if (\Auth::user()->can('view cash book')) {
            $showTable = 'Yes';

            $allAvailableFinancialEntries = FinancialEntry::with([
                'receipt:id,receipt_code',
                'customReceipt:id,receipt_code',
                'customer:id,name',
                'vendor:id,name',
                'product:id,barcode',
                'invoice:id,invoice_code'
            ])
                ->where('branch_id', \Auth::user()->branch_id)
                ->where('date', $request->filter_date)
                ->orderBy('date')
                ->orderBy('created_at')
                ->get()
                ->map(function ($entry) {
                    $entry->receipt_number = $entry->receipt->receipt_code
                        ?? $entry->customReceipt->receipt_code;
                    $entry->customer_name = $entry->customer->name ?? ($entry->vendor->name ?? '-');
                    $entry->product_name = $entry->product->barcode ??   '-';
                    $entry->invoice_number = $entry->invoice->invoice_code ??   '-';

                    return $entry;
                });

            $records = $allAvailableFinancialEntries;

            $filterDate = $request->filter_date;
            $currencySymbol = $this->currencySymbol;
            return view('primeEntryBook.daily_cash_book', compact('showTable', 'records', 'filterDate', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //BANK BOOK
    public function bankBookIndex()
    {
        if (\Auth::user()->can('view bank book')) {
            $showTable = 'No';
            $banks = GeneralLedgerAccount::select('id', 'account_name AS name')->whereIn('detail_type_id', 15)->get()->pluck('name', 'id');
            $banks->prepend('Select Bank Account', '');
            return view('primeEntryBook.bank_book', compact('showTable', 'banks'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bankBookSearch(Request $request)
    {
        if (\Auth::user()->can('view bank book')) {
            $banks = GeneralLedgerAccount::select('id', 'account_name AS name')->whereIn('detail_type_id', 15)->get()->pluck('name', 'id');
            $banks->prepend('Select Bank Account', '');
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            if (isset($request->bank_id) && isset($request->start_date) && isset($request->end_date)) {
                $showTable = 'Yes';
                $bank = $request->bank_id;

                if ($bank == 227) {
                    $bankAcount = BankAccount::find(2);
                    // if($bankAcount){
                    //     $openingBalance = $bankAcount->opening_balance;
                    // } else {
                    //     $openingBalance = 0;
                    // }

                    $preDebitAmount = AccountTransaction::where('chart_of_account_id', 227)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('status', 1)
                        ->where('debit_credit', 1)
                        ->where('date', '<', $request->start_date)->get()->sum('amount');

                    $preCreditAmount = AccountTransaction::where('chart_of_account_id', 227)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('status', 1)
                        ->where('debit_credit', 0)
                        ->where('date', '<', $request->start_date)->get()->sum('amount');
                } elseif ($bank == 228) {
                    $bankAcount = BankAccount::find(1);
                    // if($bankAcount){
                    //     $openingBalance = $bankAcount->opening_balance;
                    // } else {
                    //     $openingBalance = 0;
                    // }

                    $preDebitAmount = AccountTransaction::where('chart_of_account_id', 228)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('status', 1)
                        ->where('debit_credit', 1)
                        ->where('date', '<', $request->start_date)->get()->sum('amount');

                    $preCreditAmount = AccountTransaction::where('chart_of_account_id', 228)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('status', 1)
                        ->where('debit_credit', 0)
                        ->where('date', '<', $request->start_date)->get()->sum('amount');
                } elseif ($bank == 229) {
                    $bankAcount = BankAccount::find(3);
                    // if($bankAcount){
                    //     $openingBalance = $bankAcount->opening_balance;
                    // } else {
                    //     $openingBalance = 0;
                    // }

                    $preDebitAmount = AccountTransaction::where('chart_of_account_id', 229)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('status', 1)
                        ->where('debit_credit', 1)
                        ->where('date', '<', $request->start_date)->get()->sum('amount');

                    $preCreditAmount = AccountTransaction::where('chart_of_account_id', 229)
                        ->where('branch_id', \Auth::user()->branchId())
                        ->where('status', 1)
                        ->where('debit_credit', 0)
                        ->where('date', '<', $request->start_date)->get()->sum('amount');
                }

                // $startDateBalance = $openingBalance - $preCreditAmount + $preDebitAmount;
                $startDateBalance = $preCreditAmount + $preDebitAmount;

                $debitRransactions = AccountTransaction::with('chart_of_account')->where('chart_of_account_id', $request->bank_id)
                    ->where('branch_id', \Auth::user()->branchId())
                    ->where('status', 1)
                    ->where('debit_credit', 1)
                    ->where('date', '>=', $request->start_date)
                    ->where('date', '<=', $request->end_date)
                    ->get();

                $creditRransactions = AccountTransaction::with('chart_of_account')->where('chart_of_account_id', $request->bank_id)
                    ->where('branch_id', \Auth::user()->branchId())
                    ->where('status', 1)
                    ->where('debit_credit', 0)
                    ->where('date', '>=', $request->start_date)
                    ->where('date', '<=', $request->end_date)
                    ->get();

                $debitAmount = $debitRransactions->sum('amount') + $startDateBalance;
                $creditAmount = $creditRransactions->sum('amount');

                if ($debitAmount >= $creditAmount) {
                    $balanceType = 'debit';
                    $balanceAmount = $debitAmount - $creditAmount;
                } elseif ($debitAmount < $creditAmount) {
                    $balanceType = 'credit';
                    $balanceAmount = $creditAmount - $debitAmount;
                }

                return view('primeEntryBook.bank_book', compact('startDateBalance', 'banks', 'bank', 'debitRransactions', 'creditRransactions', 'debitAmount', 'creditAmount', 'balanceType', 'balanceAmount', 'showTable', 'start_date', 'end_date'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //PETTY CASH BOOK
    public function pettyCashBookIndex()
    {
        if (\Auth::user()->can('view petty cash book')) {
            $showTable = 'No';
            $pettycashbooks = PettyCashBook::with('chart_of_account')->get();
            $currencySymbol = $this->currencySymbol;
            return view('primeEntryBook.petty_cash_book', compact('showTable', 'pettycashbooks', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function pettyCashBookSearch(Request $request)
    {
        if (\Auth::user()->can('view petty cash book')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = date("d", strtotime($start_date));
                $endDate = date("d", strtotime($end_date));
                $showTable = 'Yes';
                // $pettycashbooks = PettyCashBook::with('chart_of_account')->where('branch_id', \Auth::user()->branchId())
                //                                     ->where('date', '>=', $start_date)
                //                                     ->where('date', '<=', $end_date)
                //                                     ->get();
                $pettycashbooks = AccountTransaction::with('related_chart_of_account')->where('branch_id', \Auth::user()->branchId())
                    ->where('chart_of_account_id', 230)
                    // ->where('debit_credit', 1)
                    ->where('status', 1)
                    ->where('date', '>=', $start_date)
                    ->where('date', '<=', $end_date)
                    ->get();
                // $pettycashbooks = PettyCash::with('chart_of_account')->get();
                $currencySymbol = $this->currencySymbol;
                return view('primeEntryBook.petty_cash_book', compact('showTable', 'start_date', 'end_date', 'pettycashbooks', 'currencySymbol'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //GENERAL JOURNAL
    public function generalJournalIndex()
    {
        if (\Auth::user()->can('view general journal') || \Auth::user()->can('create stock adjustment')) {
            // $generaljournals = GeneralJournal::orderBy('id', 'DESC')->get();
            $generaljournals = GeneralJournal::with('chart_of_account')->where('branch_id', \Auth::user()->branchId())->get();
            $currencySymbol = $this->currencySymbol;
            return view('generalJournal.index', compact('generaljournals', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function generalJournalCreate()
    {
        if (\Auth::user()->can('create general journal') || \Auth::user()->can('create stock adjustment')) {
            $accounts = GeneralLedgerAccount::select('id', 'account_name AS name')->where('status', 1)->orderBy('name')->get();
            $products = Product::where('status', 1)->orderBy('name')->get();
            // $accounts = ChartOfAccount::orderBy('name')->get()->pluck('name', 'id');
            // $accounts->prepend('-- Select Account Name --');
            return view('generalJournal.create', compact('accounts', 'products'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function inventoryProducts($id)
    {
        // $preProducts = Inventory::select('product_id', 'purchase_price', 'sale_price', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products')
        //                                 ->where('branch_id', \Auth::user()->branchId())
        //                                 ->where('status',0)
        //                                 ->where('product_id', $id)
        //                                 ->groupBy('purchase_price', 'sale_price')
        //                                 ->orderBy('id', 'DESC')
        //                                 ->limit(5)
        //                                 ->get();
        $products = Inventory::select('product_id', 'purchase_price', 'sale_price', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products')
            ->where('branch_id', \Auth::user()->branchId())
            ->where('status', 1)
            ->where('product_id', $id)
            ->groupBy('purchase_price', 'sale_price')
            ->get();

        if (count($products) > 0) {
            return json_encode($products);
        } else {
            $soldProducts = Inventory::select('product_id', 'purchase_price', 'sale_price', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products')
                ->where('branch_id', \Auth::user()->branchId())
                ->where('status', 0)
                ->where('product_id', $id)
                ->groupBy('purchase_price', 'sale_price')
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->get();
            return json_encode($soldProducts);
        }
    }

    public function getInventoryDetail($productId, $purchasePrice, $salePrice)
    {
        // echo json_encode('works');
        $products = Inventory::with('products')
            ->where('branch_id', \Auth::user()->branchId())
            // ->where('status',1)
            ->where('product_id', $productId)
            ->where('purchase_price', $purchasePrice)
            ->where('sale_price', $salePrice)
            // ->where('product_id', $request->product_id)
            // ->where('purchase_price', $request->purchase_price)
            // ->groupBy('purchase_price', 'product_id', 'sale_price')
            ->orderBy('id', 'DESC')
            ->first();
        return json_encode($products);
        // return json_encode($request->all());
    }

    public function adjustStock(Request $request)
    {
        $date = $request->date;
        $inventryId = $request->inventory_id;
        $newQty = $request->new_qty;
        $description = $request->description;
        $command = $request->command;

        $inventory = Inventory::find($inventryId);
        if ($inventory) {
            if ($command == 'deduct' && (float) $inventory->remain_qty < (float) $newQty) {
                return json_encode('error');
            }

            $adjustment = new StockAdjustment();
            $adjustment->date = $date;
            $adjustment->quantity = $newQty;
            $adjustment->description = $description;
            $adjustment->inventory_id = $inventryId;
            $adjustment->action = $command;
            $adjustment->status = 2; // Pending Admin Approval
            $adjustment->branch_id = \Auth::user()->branchId();
            $adjustment->created_by = \Auth::user()->id;
            $adjustment->save();

            return json_encode('success');
        } else {
            return json_encode('error');
        }
    }

    public function approveStockAdjustment($id)
    {
        if (!\Auth::user()->can('approve authorization') && !\Auth::user()->can('manage authorization') && \Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $adjustment = StockAdjustment::findOrFail($id);
        if ((int) $adjustment->status !== 2) {
            return redirect()->back()->with('error', __('This Stock Adjustment has already been processed.'));
        }

        $inventory = Inventory::find($adjustment->inventory_id);
        if (!$inventory) {
            return redirect()->back()->with('error', __('Inventory batch not found.'));
        }

        if ($adjustment->action === 'deduct' && (float) $inventory->remain_qty < (float) $adjustment->quantity) {
            return redirect()->back()->with('error', __('Insufficient stock available to approve this deduction.'));
        }

        \DB::transaction(function () use ($adjustment, $inventory) {
            if ($adjustment->action == 'add') {
                $inventory->remain_qty = $inventory->remain_qty + $adjustment->quantity;
                $inventory->status = 1;
            } elseif ($adjustment->action == 'deduct') {
                $newRemainQty = $inventory->remain_qty - $adjustment->quantity;
                $inventory->remain_qty = $newRemainQty;
                $inventory->status = $newRemainQty > 0 ? 1 : 0;
            }
            $inventory->update();

            $stockTransaction = new StockTransaction();
            $stockTransaction->stock_master_id = $adjustment->action == 'add' ? 6 : 7;
            $stockTransaction->inventory_id = $inventory->id;
            $stockTransaction->product_id = $inventory->product_id;
            $stockTransaction->quantity = $adjustment->quantity;
            $stockTransaction->date = $adjustment->date;
            $stockTransaction->branch_id = $adjustment->branch_id;
            $stockTransaction->created_by = $adjustment->created_by;
            $stockTransaction->status = 1;
            $stockTransaction->save();

            $adjustment->status = 1;
            $adjustment->approved_by = \Auth::id();
            $adjustment->approved_at = \Carbon\Carbon::now();
            $adjustment->save();
        });

        return redirect()->back()->with('success', __('Stock Adjustment Approved Successfully!'));
    }

    public function rejectStockAdjustment(Request $request, $id)
    {
        if (!\Auth::user()->can('approve authorization') && !\Auth::user()->can('manage authorization') && \Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $adjustment = StockAdjustment::findOrFail($id);
        if ((int) $adjustment->status !== 2) {
            return redirect()->back()->with('error', __('This Stock Adjustment has already been processed.'));
        }

        $adjustment->status = 3;
        $adjustment->rejection_reason = $request->rejection_reason ?? null;
        $adjustment->approved_by = \Auth::id();
        $adjustment->approved_at = \Carbon\Carbon::now();
        $adjustment->save();

        return redirect()->back()->with('success', __('Stock Adjustment Rejected.'));
    }

    public function generalJournalStore(Request $request)
    {
        if (\Auth::user()->can('create general journal')) {
            $rules = [
                'date' => 'required',
                // 'account_id' => 'required',
                // 'debit' => 'required',
                // 'credit' => 'required',
                // 'narration' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // dd($request);
            // $account = ChartOfAccount::find($request->account_id);
            foreach ($request->items as $entry) {
                $generaljournal                         = new GeneralJournal();
                $generaljournal->date                   = $request->date;
                $generaljournal->account_id             = $entry['account_id'];
                $generaljournal->debit_credit           = $entry['debit_credit'];
                $generaljournal->related_account_id     = $entry['related_account_id'];
                $generaljournal->related_debit_credit   = $entry['related_debit_credit'];
                $generaljournal->amount                 = $entry['amount'];
                $generaljournal->narration              = $entry['narration'];
                $generaljournal->created_by             = \Auth::user()->id;
                $generaljournal->branch_id              = \Auth::user()->branchId();
                $generaljournal->save();

                Utility::generalJournalAccountTransation($request->date, $entry['account_id'], $entry['debit_credit'],  $entry['related_account_id'], $entry['related_debit_credit'], $entry['amount'], $generaljournal->id);
            }
            return redirect()->back()->with('success', __('Journal successfully created !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function generalJournalEdit($id)
    {
        if (\Auth::user()->can('edit general journal')) {
            $accounts = select('id', 'account_name AS name')->where('status', 1)->orderBy('name')->get();
            $journal = GeneralJournal::find($id);
            // $accounts = ChartOfAccount::orderBy('name')->get()->pluck('name', 'id');
            // $accounts->prepend('-- Select Account Name --');
            return view('generalJournal.edit', compact('accounts', 'journal'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function generalJournalUpdate(Request $request)
    {
        if (\Auth::user()->can('edit general journal')) {
            $rules = [
                'date' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $generaljournal                         = GeneralJournal::find($request->journal_id);
            $generaljournal->date                   = $request->date;
            $generaljournal->account_id             = $request->account_id;
            $generaljournal->debit_credit           = $request->debit_credit;
            $generaljournal->related_account_id     = $request->related_account_id;
            $generaljournal->related_debit_credit   = $request->related_debit_credit;
            $generaljournal->amount                 = $request->amount;
            $generaljournal->narration              = $request->narration;
            $generaljournal->update();

            Utility::deleteGeneralJournalAccountTransation($request->journal_id);
            Utility::generalJournalAccountTransation($request->date, $request->account_id, $request->debit_credit,  $request->related_account_id, $request->related_debit_credit, $request->amount, $generaljournal->id);

            return redirect()->back()->with('success', __('Journal successfully Updated !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
