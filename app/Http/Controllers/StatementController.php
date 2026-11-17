<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\BankAccount;
use App\Bill;
use App\BillProduct;
use App\Branch;
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
use App\PawningReceipt;
use App\ProductCategory;
use App\Setting;
use App\User;
use App\Stock;
use App\Statement;
use Carbon\Carbon;
use Illuminate\Http\Request;


class StatementController extends Controller
{
    protected $currencySymbol;


    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }
    // PROFIT & LOSS STATEMENT FORMAT
    // public function profitLossIndex(Request $request)
    // {

    //     if(\Auth::user()->can('view sale'))
    //     // if(\Auth::user()->can('loss & profit report'))
    //     {
    //         $data['month']     = [
    //             'Apr',
    //             'May',
    //             'Jun',
    //             'Jul',
    //             'Aug',
    //             'Sept',
    //             'Oct',
    //             'Nov',
    //             'Dec',
    //             'Jan',
    //             'Feb',
    //             'Mar',
    //             'Total',
    //         ];
    //         $data['monthList'] = $month = $this->yearMonth();
    //         $data['yearList']  = $this->yearList();

    //         if(isset($request->year))
    //         {
    //             $year = $request->year;
    //         }
    //         else
    //         {
    //             $year = date('Y');
    //         }
    //         $data['currentYear'] = $year;
    //         // $year = ( date('m') > 6) ? date('Y') + 1 : date('Y');
    //         $endYear = $year + 1;

    //         // -------------------------------REVENUE INCOME-------------------------------------------------

    //         $incomes = Revenue::selectRaw('sum(revenues.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id');
    //         // $incomes->where('created_by', '=', \Auth::user()->creatorId());
    //         $incomes->where('branch_id', '=', \Auth::user()->branchId());
    //         $incomes->whereRAW('YEAR(date) =?', [$request->year]);
    //         $incomes->groupBy('month', 'year', 'category_id');
    //         $incomes        = $incomes->get();
    //         // dd($request->year);
    //         $tmpIncomeArray = [];
    //         foreach($incomes as $income)
    //         {
    //             $tmpIncomeArray[$income->category_id][$income->month] = $income->amount;
    //         }

    //         $incomeCatAmount_1  = $incomeCatAmount_2 = $incomeCatAmount_3 = $incomeCatAmount_4 = $incomeCatAmount_5 = $incomeCatAmount_6 = $incomeCatAmount_7 = $incomeCatAmount_8 = $incomeCatAmount_9 = $incomeCatAmount_10 = $incomeCatAmount_11 = $incomeCatAmount_12 = 0;
    //         $revenueIncomeArray = array();
    //         foreach($tmpIncomeArray as $cat_id => $record)
    //         {

    //             $tmp             = [];
    //             $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
    //             $sumData         = [];
    //             for($i = 1; $i <= 12; $i++)
    //             {
    //                 $sumData[] = array_key_exists($i, $record) ? $record[$i] : 0;
    //             }

    //             $month_1 = array_slice($sumData, 3, 1);
    //             $month_2 = array_slice($sumData, 4, 1);
    //             $month_3 = array_slice($sumData, 5, 1);
    //             $month_4 = array_slice($sumData, 6, 1);
    //             $month_5 = array_slice($sumData, 7, 1);
    //             $month_6 = array_slice($sumData, 8, 1);
    //             $month_7 = array_slice($sumData, 9, 1);
    //             $month_8 = array_slice($sumData, 10, 1);
    //             $month_9 = array_slice($sumData, 11, 1);
    //             $month_10 = array_slice($sumData, 12, 1);
    //             $month_11 = array_slice($sumData, 1, 1);
    //             $month_12 = array_slice($sumData, 2, 1);


    //             $incomeData[__('Apr')] = $sum_1 = array_sum($month_1);
    //             $incomeData[__('May')] = $sum_2 = array_sum($month_2);
    //             $incomeData[__('Jun')] = $sum_3 = array_sum($month_3);
    //             $incomeData[__('Jul')] = $sum_4 = array_sum($month_4);
    //             $incomeData[__('Aug')] = $sum_5 = array_sum($month_5);
    //             $incomeData[__('Sept')] = $sum_6 = array_sum($month_6);
    //             $incomeData[__('Oct')] = $sum_7 = array_sum($month_7);
    //             $incomeData[__('Nov')] = $sum_8 = array_sum($month_8);
    //             $incomeData[__('Dec')] = $sum_9 = array_sum($month_9);
    //             $incomeData[__('Jan')] = $sum_10 = array_sum($month_10);
    //             $incomeData[__('Feb')] = $sum_11 = array_sum($month_11);
    //             $incomeData[__('Mar')] = $sum_12 = array_sum($month_12);
    //             $incomeData[__('Total')]   = array_sum(
    //                 array(
    //                     $sum_1,
    //                     $sum_2,
    //                     $sum_3,
    //                     $sum_4,
    //                     $sum_5,
    //                     $sum_6,
    //                     $sum_7,
    //                     $sum_8,
    //                     $sum_9,
    //                     $sum_10,
    //                     $sum_11,
    //                     $sum_12,
    //                 )
    //             );

    //             $incomeCatAmount_1 += $sum_1;
    //             $incomeCatAmount_2 += $sum_2;
    //             $incomeCatAmount_3 += $sum_3;
    //             $incomeCatAmount_4 += $sum_4;
    //             $incomeCatAmount_5 += $sum_5;
    //             $incomeCatAmount_6 += $sum_6;
    //             $incomeCatAmount_7 += $sum_7;
    //             $incomeCatAmount_8 += $sum_8;
    //             $incomeCatAmount_9 += $sum_9;
    //             $incomeCatAmount_10 += $sum_10;
    //             $incomeCatAmount_11 += $sum_11;
    //             $incomeCatAmount_12 += $sum_12;

    //             $data['month'] = array_keys($incomeData);
    //             $tmp['amount'] = array_values($incomeData);

    //             $revenueIncomeArray[] = $tmp;

    //         }

    //         $data['incomeCatAmount'] = $incomeCatAmount = [
    //             $incomeCatAmount_1,
    //             $incomeCatAmount_2,
    //             $incomeCatAmount_3,
    //             $incomeCatAmount_4,
    //             $incomeCatAmount_5,
    //             $incomeCatAmount_6,
    //             $incomeCatAmount_7,
    //             $incomeCatAmount_8,
    //             $incomeCatAmount_9,
    //             $incomeCatAmount_10,
    //             $incomeCatAmount_11,
    //             $incomeCatAmount_12,
    //             array_sum(
    //                 array(
    //                     $incomeCatAmount_1,
    //                     $incomeCatAmount_2,
    //                     $incomeCatAmount_3,
    //                     $incomeCatAmount_4,
    //                     $incomeCatAmount_5,
    //                     $incomeCatAmount_6,
    //                     $incomeCatAmount_7,
    //                     $incomeCatAmount_8,
    //                     $incomeCatAmount_9,
    //                     $incomeCatAmount_10,
    //                     $incomeCatAmount_11,
    //                     $incomeCatAmount_12,
    //                 )
    //             ),
    //         ];

    //         $data['revenueIncomeArray'] = $revenueIncomeArray;

    //         //-----------------------INVOICE INCOME---------------------------------------------

    //         $invoices = Invoice:: selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,invoice_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
    //         $invoices->whereRAW('YEAR(send_date) =?', [$year]);
    //         if(!empty($request->customer))
    //         {
    //             $invoices->where('customer_id', '=', $request->customer);
    //         }
    //         $invoices        = $invoices->get();
    //         $invoiceTmpArray = [];
    //         foreach($invoices as $invoice)
    //         {
    //             $invoiceTmpArray[$invoice->category_id][$invoice->month][] = $invoice->getTotal();
    //         }

    //         $invoiceCatAmount_1 = $invoiceCatAmount_2 = $invoiceCatAmount_3 = $invoiceCatAmount_4 = $incomeCatAmount_5 = $incomeCatAmount_6 = $incomeCatAmount_7 = $incomeCatAmount_8 = $incomeCatAmount_9 = $incomeCatAmount_10 = $incomeCatAmount_11 = $incomeCatAmount_12 = 0;
    //         $invoiceIncomeArray = array();
    //         foreach($invoiceTmpArray as $cat_id => $record)
    //         {

    //             $invoiceTmp             = [];
    //             $invoiceTmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
    //             $invoiceSumData         = [];
    //             for($i = 1; $i <= 12; $i++)
    //             {
    //                 $invoiceSumData[] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;

    //             }

    //             $month_1                          = array_slice($invoiceSumData, 3, 1);
    //             $month_2                          = array_slice($invoiceSumData, 4, 1);
    //             $month_3                          = array_slice($invoiceSumData, 5, 1);
    //             $month_4                          = array_slice($invoiceSumData, 6, 1);
    //             $month_5                          = array_slice($invoiceSumData, 7, 1);
    //             $month_6                          = array_slice($invoiceSumData, 8, 1);
    //             $month_7                          = array_slice($invoiceSumData, 9, 1);
    //             $month_8                          = array_slice($invoiceSumData, 10, 1);
    //             $month_9                          = array_slice($invoiceSumData, 11, 1);
    //             $month_10                          = array_slice($invoiceSumData, 12, 1);
    //             $month_11                          = array_slice($invoiceSumData, 1, 1);
    //             $month_12                          = array_slice($invoiceSumData, 2, 1);
    //             $invoiceIncomeData[__('Apr')] = $sum_1 = array_sum($month_1);
    //             $invoiceIncomeData[__('May')] = $sum_2 = array_sum($month_2);
    //             $invoiceIncomeData[__('Jun')] = $sum_3 = array_sum($month_3);
    //             $invoiceIncomeData[__('Jul')] = $sum_4 = array_sum($month_4);
    //             $invoiceIncomeData[__('Aug')] = $sum_5 = array_sum($month_5);
    //             $invoiceIncomeData[__('Sept')] = $sum_6 = array_sum($month_6);
    //             $invoiceIncomeData[__('Oct')] = $sum_7 = array_sum($month_7);
    //             $invoiceIncomeData[__('Nov')] = $sum_8 = array_sum($month_8);
    //             $invoiceIncomeData[__('Dec')] = $sum_9 = array_sum($month_9);
    //             $invoiceIncomeData[__('Jan')] = $sum_10 = array_sum($month_10);
    //             $invoiceIncomeData[__('Feb')] = $sum_11 = array_sum($month_11);
    //             $invoiceIncomeData[__('Mar')] = $sum_12 = array_sum($month_12);
    //             $invoiceIncomeData[__('Total')]   = array_sum(
    //                 array(
    //                     $sum_1,
    //                     $sum_2,
    //                     $sum_3,
    //                     $sum_4,
    //                     $sum_5,
    //                     $sum_6,
    //                     $sum_7,
    //                     $sum_8,
    //                     $sum_9,
    //                     $sum_10,
    //                     $sum_11,
    //                     $sum_12,
    //                 )
    //             );
    //             $invoiceCatAmount_1               += $sum_1;
    //             $invoiceCatAmount_2               += $sum_2;
    //             $invoiceCatAmount_3               += $sum_3;
    //             $invoiceCatAmount_4               += $sum_4;
    //             $invoiceCatAmount_5               += $sum_5;
    //             $invoiceCatAmount_6               += $sum_6;
    //             $invoiceCatAmount_7               += $sum_7;
    //             $invoiceCatAmount_8               += $sum_8;
    //             $invoiceCatAmount_9               += $sum_9;
    //             $invoiceCatAmount_10              += $sum_10;
    //             $invoiceCatAmount_11              += $sum_11;
    //             $invoiceCatAmount_12              += $sum_12;

    //             $invoiceTmp['amount'] = array_values($invoiceIncomeData);

    //             $invoiceIncomeArray[] = $invoiceTmp;

    //         }

    //         $data['invoiceIncomeCatAmount'] = $invoiceIncomeCatAmount = [
    //             $invoiceCatAmount_1,
    //             $invoiceCatAmount_2,
    //             $invoiceCatAmount_3,
    //             $invoiceCatAmount_4,
    //             // $invoiceCatAmount_5,
    //             // $invoiceCatAmount_6,
    //             // $invoiceCatAmount_7,
    //             // $invoiceCatAmount_8,
    //             // $invoiceCatAmount_9,
    //             // $invoiceCatAmount_10,
    //             // $invoiceCatAmount_11,
    //             // $invoiceCatAmount_12,
    //             array_sum(
    //                 array(
    //                     $invoiceCatAmount_1,
    //                     $invoiceCatAmount_2,
    //                     $invoiceCatAmount_3,
    //                     $invoiceCatAmount_4,
    //                     // $invoiceCatAmount_5,
    //                     // $invoiceCatAmount_6,
    //                     // $invoiceCatAmount_7,
    //                     // $invoiceCatAmount_8,
    //                     // $invoiceCatAmount_9,
    //                     // $invoiceCatAmount_10,
    //                     // $invoiceCatAmount_11,
    //                     // $invoiceCatAmount_12,
    //                 )
    //             ),
    //         ];


    //         $data['invoiceIncomeArray'] = $invoiceIncomeArray;

    //         $data['totalIncome'] = $totalIncome = array_map(
    //             function (){
    //                 return array_sum(func_get_args());
    //             }, $invoiceIncomeCatAmount, $incomeCatAmount
    //         );

    //         //---------------------------------PAYMENT EXPENSE-----------------------------------

    //         $expenses = Payment::selectRaw('sum(payments.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id');
    //         $expenses->where('created_by', '=', \Auth::user()->creatorId());
    //         $expenses->whereRAW('YEAR(date) =?', [$year]);
    //         $expenses->groupBy('month', 'year', 'category_id');
    //         $expenses = $expenses->get();

    //         $tmpExpenseArray = [];
    //         foreach($expenses as $expense)
    //         {
    //             $tmpExpenseArray[$expense->category_id][$expense->month] = $expense->amount;
    //         }

    //         $expenseArray       = [];
    //         $expenseCatAmount_1 = $expenseCatAmount_2 = $expenseCatAmount_3 = $expenseCatAmount_4 = $expenseCatAmount_5 = $expenseCatAmount_6 = $expenseCatAmount_7 = $expenseCatAmount_8 = $expenseCatAmount_9 = $expenseCatAmount_10 = $expenseCatAmount_11 = $expenseCatAmount_12 = 0;
    //         foreach($tmpExpenseArray as $cat_id => $record)
    //         {
    //             $tmp             = [];
    //             $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
    //             $expenseSumData  = [];
    //             for($i = 1; $i <= 12; $i++)
    //             {
    //                 $expenseSumData[] = array_key_exists($i, $record) ? $record[$i] : 0;

    //             }

    //             $month_1 = array_slice($expenseSumData, 3, 1);
    //             $month_2 = array_slice($expenseSumData, 4, 1);
    //             $month_3 = array_slice($expenseSumData, 5, 1);
    //             $month_4 = array_slice($expenseSumData, 6, 1);
    //             $month_5 = array_slice($expenseSumData, 7, 1);
    //             $month_6 = array_slice($expenseSumData, 8, 1);
    //             $month_7 = array_slice($expenseSumData, 9, 1);
    //             $month_8 = array_slice($expenseSumData, 10, 1);
    //             $month_9 = array_slice($expenseSumData, 11, 1);
    //             $month_10 = array_slice($expenseSumData, 12, 1);
    //             $month_11 = array_slice($expenseSumData, 1, 1);
    //             $month_12 = array_slice($expenseSumData, 2, 1);

    //             $expenseData[__('Apr')] = $sum_1 = array_sum($month_1);
    //             $expenseData[__('May')] = $sum_2 = array_sum($month_2);
    //             $expenseData[__('Jun')] = $sum_3 = array_sum($month_3);
    //             $expenseData[__('Jul')] = $sum_4 = array_sum($month_4);
    //             $expenseData[__('Aug')] = $sum_5 = array_sum($month_5);
    //             $expenseData[__('Sept')] = $sum_6 = array_sum($month_6);
    //             $expenseData[__('Oct')] = $sum_7 = array_sum($month_7);
    //             $expenseData[__('Nov')] = $sum_8 = array_sum($month_8);
    //             $expenseData[__('Dec')] = $sum_9 = array_sum($month_9);
    //             $expenseData[__('Jan')] = $sum_10 = array_sum($month_10);
    //             $expenseData[__('Feb')] = $sum_11 = array_sum($month_11);
    //             $expenseData[__('Mar')] = $sum_12 = array_sum($month_12);
    //             $expenseData[__('Total')]   = array_sum(
    //                 array(
    //                     $sum_1,
    //                     $sum_2,
    //                     $sum_3,
    //                     $sum_4,
    //                     $sum_5,
    //                     $sum_6,
    //                     $sum_7,
    //                     $sum_8,
    //                     $sum_9,
    //                     $sum_10,
    //                     $sum_11,
    //                     $sum_12,
    //                 )
    //             );

    //             $expenseCatAmount_1 += $sum_1;
    //             $expenseCatAmount_2 += $sum_2;
    //             $expenseCatAmount_3 += $sum_3;
    //             $expenseCatAmount_4 += $sum_4;
    //             $expenseCatAmount_5 += $sum_5;
    //             $expenseCatAmount_6 += $sum_6;
    //             $expenseCatAmount_7 += $sum_7;
    //             $expenseCatAmount_8 += $sum_8;
    //             $expenseCatAmount_9 += $sum_9;
    //             $expenseCatAmount_10 += $sum_10;
    //             $expenseCatAmount_11 += $sum_11;
    //             $expenseCatAmount_12 += $sum_12;

    //             $data['month'] = array_keys($expenseData);
    //             $tmp['amount'] = array_values($expenseData);

    //             $expenseArray[] = $tmp;

    //         }

    //         $data['expenseCatAmount'] = $expenseCatAmount = [
    //             $expenseCatAmount_1,
    //             $expenseCatAmount_2,
    //             $expenseCatAmount_3,
    //             $expenseCatAmount_4,
    //             $expenseCatAmount_5,
    //             $expenseCatAmount_6,
    //             $expenseCatAmount_7,
    //             $expenseCatAmount_8,
    //             $expenseCatAmount_9,
    //             $expenseCatAmount_10,
    //             $expenseCatAmount_11,
    //             $expenseCatAmount_12,
    //             array_sum(
    //                 array(
    //                     $expenseCatAmount_1,
    //                     $expenseCatAmount_2,
    //                     $expenseCatAmount_3,
    //                     $expenseCatAmount_4,
    //                     $expenseCatAmount_5,
    //                     $expenseCatAmount_6,
    //                     $expenseCatAmount_7,
    //                     $expenseCatAmount_8,
    //                     $expenseCatAmount_9,
    //                     $expenseCatAmount_10,
    //                     $expenseCatAmount_11,
    //                     $expenseCatAmount_12,
    //                 )
    //             ),
    //         ];
    //         $data['expenseArray']     = $expenseArray;

    //         //----------------------------EXPENSE BILL-----------------------------------------------------------------------

    //         $bills = Bill:: selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,bill_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
    //         $bills->whereRAW('YEAR(send_date) =?', [$year]);
    //         if(!empty($request->customer))
    //         {
    //             $bills->where('vender_id', '=', $request->vender);
    //         }
    //         $bills        = $bills->get();
    //         $billTmpArray = [];
    //         foreach($bills as $bill)
    //         {
    //             $billTmpArray[$bill->category_id][$bill->month][] = $bill->getTotal();
    //         }

    //         $billExpenseArray       = [];
    //         $billExpenseCatAmount_1 = $billExpenseCatAmount_2 = $billExpenseCatAmount_3 = $billExpenseCatAmount_4 = $billExpenseCatAmount_5 = $billExpenseCatAmount_6 = $billExpenseCatAmount_7 = $billExpenseCatAmount_8 = $billExpenseCatAmount_9 = $billExpenseCatAmount_10 = $billExpenseCatAmount_11 = $billExpenseCatAmount_12 = 0;
    //         foreach($billTmpArray as $cat_id => $record)
    //         {
    //             $billTmp             = [];
    //             $billTmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
    //             $billExpensSumData   = [];
    //             for($i = 1; $i <= 12; $i++)
    //             {
    //                 $billExpensSumData[] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
    //             }

    //             $month_1 = array_slice($billExpensSumData, 3, 1);
    //             $month_2 = array_slice($billExpensSumData, 4, 1);
    //             $month_3 = array_slice($billExpensSumData, 5, 1);
    //             $month_4 = array_slice($billExpensSumData, 6, 1);
    //             $month_5 = array_slice($billExpensSumData, 7, 1);
    //             $month_6 = array_slice($billExpensSumData, 8, 1);
    //             $month_7 = array_slice($billExpensSumData, 9, 1);
    //             $month_8 = array_slice($billExpensSumData, 10, 1);
    //             $month_9 = array_slice($billExpensSumData, 11, 1);
    //             $month_10 = array_slice($billExpensSumData, 12, 1);
    //             $month_11 = array_slice($billExpensSumData, 1, 1);
    //             $month_12 = array_slice($billExpensSumData, 2, 1);

    //             $billExpenseData[__('Apr')] = $sum_1 = array_sum($month_1);
    //             $billExpenseData[__('May')] = $sum_2 = array_sum($month_2);
    //             $billExpenseData[__('Jun')] = $sum_3 = array_sum($month_3);
    //             $billExpenseData[__('Jul')] = $sum_4 = array_sum($month_4);
    //             $billExpenseData[__('Aug')] = $sum_5 = array_sum($month_5);
    //             $billExpenseData[__('Sept')] = $sum_6 = array_sum($month_6);
    //             $billExpenseData[__('Oct')] = $sum_7 = array_sum($month_7);
    //             $billExpenseData[__('Nov')] = $sum_8 = array_sum($month_8);
    //             $billExpenseData[__('Dec')] = $sum_9 = array_sum($month_9);
    //             $billExpenseData[__('Jan')] = $sum_10 = array_sum($month_10);
    //             $billExpenseData[__('Feb')] = $sum_11 = array_sum($month_11);
    //             $billExpenseData[__('Mar')] = $sum_12 = array_sum($month_12);
    //             $billExpenseData[__('Total')]   = array_sum(
    //                 array(
    //                     $sum_1,
    //                     $sum_2,
    //                     $sum_3,
    //                     $sum_4,
    //                     $sum_5,
    //                     $sum_6,
    //                     $sum_7,
    //                     $sum_8,
    //                     $sum_9,
    //                     $sum_10,
    //                     $sum_11,
    //                     $sum_12,
    //                 )
    //             );

    //             $billExpenseCatAmount_1 += $sum_1;
    //             $billExpenseCatAmount_2 += $sum_2;
    //             $billExpenseCatAmount_3 += $sum_3;
    //             $billExpenseCatAmount_4 += $sum_4;
    //             $billExpenseCatAmount_5 += $sum_5;
    //             $billExpenseCatAmount_6 += $sum_6;
    //             $billExpenseCatAmount_7 += $sum_7;
    //             $billExpenseCatAmount_8 += $sum_8;
    //             $billExpenseCatAmount_9 += $sum_9;
    //             $billExpenseCatAmount_10 += $sum_10;
    //             $billExpenseCatAmount_11 += $sum_11;
    //             $billExpenseCatAmount_12 += $sum_12;

    //             $data['month']     = array_keys($billExpenseData);
    //             $billTmp['amount'] = array_values($billExpenseData);

    //             $billExpenseArray[] = $billTmp;

    //         }

    //         $data['billExpenseCatAmount'] = $billExpenseCatAmount = [
    //             $billExpenseCatAmount_1,
    //             $billExpenseCatAmount_2,
    //             $billExpenseCatAmount_3,
    //             $billExpenseCatAmount_4,
    //             $billExpenseCatAmount_5,
    //             $billExpenseCatAmount_6,
    //             $billExpenseCatAmount_7,
    //             $billExpenseCatAmount_8,
    //             $billExpenseCatAmount_9,
    //             $billExpenseCatAmount_10,
    //             $billExpenseCatAmount_11,
    //             $billExpenseCatAmount_12,
    //             array_sum(
    //                 array(
    //                     $billExpenseCatAmount_1,
    //                     $billExpenseCatAmount_2,
    //                     $billExpenseCatAmount_3,
    //                     $billExpenseCatAmount_4,
    //                     $billExpenseCatAmount_5,
    //                     $billExpenseCatAmount_6,
    //                     $billExpenseCatAmount_7,
    //                     $billExpenseCatAmount_8,
    //                     $billExpenseCatAmount_9,
    //                     $billExpenseCatAmount_10,
    //                     $billExpenseCatAmount_11,
    //                     $billExpenseCatAmount_12,
    //                 )
    //             ),
    //         ];

    //         $data['billExpenseArray'] = $billExpenseArray;


    //         $data['totalExpense'] = $totalExpense = array_map(
    //             function (){
    //                 return array_sum(func_get_args());
    //             }, $billExpenseCatAmount, $expenseCatAmount
    //         );


    //         foreach($totalIncome as $k => $income)
    //         {
    //             $netProfit[] = $income - $totalExpense[$k];
    //         }
    //         $data['netProfitArray'] = $netProfit;

    //         $filter['startDateRange'] = 'April ' . $year;
    //         $filter['endDateRange']   = 'March ' . $endYear;


    //         return view('statements.profit_loss_summary', compact('filter'), $data);
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }


    // }
    // public function profitLossIndex(Request $request)
    // {
    //     if(\Auth::user()->can('view loss & profit'))
    //     {
    //         $showTable = 'No';
    //         return view('statements.profit_loss', compact('showTable'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    // public function profitLossSearch(Request $request)
    // {
    //     if(\Auth::user()->can('view loss & profit'))
    //     {
    //         if(isset($request->start_date) && isset($request->end_date)){
    //             $showTable = 'Yes';
    //             $startDate = $request->start_date;
    //             $endDate = $request->end_date;
    //             $branchId = \Auth::user()->branchId();

    //             $accounts = ChartOfAccount::get();
    //             $allAccounts = [];
    //             foreach($accounts as $account){
    //                 $transactions = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
    //                                                         ->where('status', 1)
    //                                                         ->where('branch_id', $branchId)
    //                                                         ->where('date', '>=', $startDate)
    //                                                         ->where('date', '<=', $endDate)
    //                                                         ->where('chart_of_account_id', $account->id)
    //                                                         ->get();
    //                 foreach($transactions as $transaction){
    //                     $debit_amount = $transaction->debit_amount;
    //                     $credit_amount = $transaction->credit_amount;
    //                 }
    //                 $allAccounts[$account->id][] = array('name'=>$account->name, 'debit_amount'=>$debit_amount, 'credit_amount'=>$credit_amount);
    //             }

    //             $data['allAccounts'] = $allAccounts;

    //             $accSalesIncome = [19,20,21];
    //             $data['salesIncomeTransactions'] = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
    //                                                         ->where('status', 1)
    //                                                         ->where('branch_id', $branchId)
    //                                                         ->where('date', '>=', $startDate)
    //                                                         ->where('date', '<=', $endDate)
    //                                                         ->whereIn('chart_of_account_id', $accSalesIncome)
    //                                                         ->get();
    //             $data['accSalesIncome'] = json_encode($accSalesIncome, true);

    //             $accOtherIncome = [12,13,14,15,16,17,18,9,10,11,22,23,323];
    //             $data['otherIncomeTransactions'] = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
    //                                                         ->where('status', 1)
    //                                                         ->where('branch_id', $branchId)
    //                                                         ->where('date', '>=', $startDate)
    //                                                         ->where('date', '<=', $endDate)
    //                                                         ->whereIn('chart_of_account_id', $accOtherIncome)
    //                                                         ->get();
    //             $data['accOtherIncome'] = json_encode($accOtherIncome, true);

    //             // $accCostOfSales = [307];
    //             // $data['costOfSalesTransactions'] = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
    //             //                                             ->where('status', 1)
    //             //                                             ->where('branch_id', $branchId)
    //             //                                             ->where('date', '>=', $startDate)
    //             //                                             ->where('date', '<=', $endDate)
    //             //                                             ->whereIn('chart_of_account_id', $accCostOfSales)
    //             //                                             ->get();
    //             // $data['accCostOfSales'] = json_encode($accCostOfSales, true);

    //             $accInterestIncome = [1,2,3,4,5,6,7,8];
    //             $data['interestIncomeTransactions'] = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
    //                                                         ->where('status', 1)
    //                                                         ->where('branch_id', $branchId)
    //                                                         ->where('date', '>=', $startDate)
    //                                                         ->where('date', '<=', $endDate)
    //                                                         ->whereIn('chart_of_account_id', $accInterestIncome)
    //                                                         ->get();
    //             $data['accInterestIncome'] = json_encode($accInterestIncome, true);

    //             $adminTransactions = [];
    //             for($i = 27; $i <= 100; $i++){
    //                 $account = ChartOfAccount::find($i);
    //                 $transactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
    //                                                         ->where('status', 1)
    //                                                         ->where('branch_id', $branchId)
    //                                                         ->where('date', '>=', $startDate)
    //                                                         ->where('date', '<=', $endDate)
    //                                                         ->where('chart_of_account_id', $i)
    //                                                         ->get();

    //                 $adminTransactions[$i] = array('name'=>$account->name, 'balance_amount'=>$transactionBalance[0]->balance_amount);
    //             }
    //             $data['adminTransactions'] = $adminTransactions;

    //             $sellDistributeTransactions = [];
    //             for($j = 101; $j <= 111; $j++){
    //                 $account = ChartOfAccount::find($j);
    //                 $transactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
    //                                                         ->where('status', 1)
    //                                                         ->where('branch_id', $branchId)
    //                                                         ->where('date', '>=', $startDate)
    //                                                         ->where('date', '<=', $endDate)
    //                                                         ->where('chart_of_account_id', $j)
    //                                                         ->get();

    //                 $sellDistributeTransactions[$j] = array('name'=>$account->name, 'balance_amount'=>$transactionBalance[0]->balance_amount);
    //             }
    //             $data['sellDistributeTransactions'] = $sellDistributeTransactions;

    //             $finOtherTransactions = [];
    //             for($k = 112; $k <= 124; $k++){
    //                 $account = ChartOfAccount::find($k);
    //                 $transactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
    //                                                         ->where('status', 1)
    //                                                         ->where('branch_id', $branchId)
    //                                                         ->where('date', '>=', $startDate)
    //                                                         ->where('date', '<=', $endDate)
    //                                                         ->where('chart_of_account_id', $k)
    //                                                         ->get();

    //                 $finOtherTransactions[$k] = array('name'=>$account->name, 'balance_amount'=>$transactionBalance[0]->balance_amount);
    //             }
    //             $data['finOtherTransactions'] = $finOtherTransactions;

    //             $accStockReturn = [313,314];
    //             $data['stockReturnTransactions'] = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
    //                                                         ->where('status', 1)
    //                                                         ->where('branch_id', $branchId)
    //                                                         ->where('date', '>=', $startDate)
    //                                                         ->where('date', '<=', $endDate)
    //                                                         ->whereIn('chart_of_account_id', $accStockReturn)
    //                                                         ->get();
    //             $data['accStockReturn'] = json_encode($accStockReturn, true);

    //             // dd($data['adminTransactions']);
    //             return view('statements.profit_loss', compact('showTable', 'startDate', 'endDate'), $data);
    //         }
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }


    public function profitLossIndex(Request $request)
    {
        if (\Auth::user()->can('view loss & profit')) {
            $showTable = 'No';
            return view('statements.profit_loss', compact('showTable'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function profitLossSearch(Request $request)
    {
        if (\Auth::user()->can('view loss & profit')) {
            if (isset($request->start_date) && isset($request->end_date)) {
                $startDateArr = Carbon::parse($request->start_date);

                // if($startDateArr->month > 03){
                $yearStartDate = $startDateArr->year . '-01-01';
                // } else {
                //     $yearStartDate = $startDateArr->subYears(1)->year.'-01-01';
                // }

                $showTable = 'Yes';
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                $branchId = \Auth::user()->branchId();

                $accounts = GeneralLedgerAccount::get();
                $allAccounts = [];

                $status = $this->checkMoreThanMonth($startDate, $endDate);

                foreach ($accounts as $account) {
                    $allYearTransactions = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                        ->where('status', 1)
                        ->where('branch_id', $branchId)
                        ->where('date', '>=', $yearStartDate)
                        ->where('date', '<=', $endDate)
                        ->where('chart_of_account_id', $account->id)
                        ->get();
                    foreach ($allYearTransactions as $transaction) {
                        $debit_amount_all_year = $transaction->debit_amount;
                        $credit_amount_all_year = $transaction->credit_amount;
                    }

                    $SelectedMonthtransactions = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                        ->where('status', 1)
                        ->where('branch_id', $branchId)
                        ->where('date', '>=', $startDate)
                        ->where('date', '<=', $endDate)
                        ->where('chart_of_account_id', $account->id)
                        ->get();
                    foreach ($SelectedMonthtransactions as $transaction) {
                        $debit_amount_selected_month = $transaction->debit_amount;
                        $credit_amount_selected_month = $transaction->credit_amount;
                    }


                    if ($status == "In same month") {
                        $debit_amount_current_month = $debit_amount_selected_month;
                        $credit_amount_current_month = $credit_amount_selected_month;
                    } else if ($status == "Range not in same month") {


                        $endDateYear =  date('Y', strtotime(date($endDate)));
                        $endDateMonth =  date('m', strtotime(date($endDate)));
                        $currenttransactions = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                            ->where('status', 1)
                            ->where('branch_id', $branchId)
                            ->where('date', '>=', $endDateYear . '-' . $endDateMonth . '-1')
                            ->where('date', '<=', $endDate)
                            ->where('chart_of_account_id', $account->id)
                            ->get();


                        foreach ($currenttransactions as $current_transaction) {
                            $debit_amount_current_month = $current_transaction->debit_amount;
                            $credit_amount_current_month = $current_transaction->credit_amount;
                        }
                    }

                    $allAccounts[$account->id][] = array('name' => $account->name, 'debit_amount_all_year' => $debit_amount_all_year, 'credit_amount_all_year' => $credit_amount_all_year, 'debit_amount_selected_month' => $debit_amount_selected_month, 'credit_amount_selected_month' => $credit_amount_selected_month, 'debit_amount_current_month' => $debit_amount_current_month, 'credit_amount_current_month' => $credit_amount_current_month);
                }

                $data['allAccounts'] = $allAccounts;

                // 4 - Sales of Product Income - Home Appliance
                // 120 - Billable Expense Income
                // 121 - Diriya Loan Interest
                // 122 - Diriya Loan Sales
                // 123 - Unapplied Cash Payment Income
                // 124 - Uncategorized Income
                $accSalesIncome = GeneralLedgerAccount::whereIn('detail_type_id', [88, 89, 90])->pluck('id');
                $data['salesIncomeAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accSalesIncome)
                    ->get();

                $data['salesIncomeSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accSalesIncome)
                    ->get();

                // 5 - Sales and service tax payable
                $accSalesTaxIncome = GeneralLedgerAccount::whereIn('detail_type_id', [59])->pluck('id');
                $data['salesTaxPayableAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accSalesTaxIncome)
                    ->get();

                $data['salesTaxPayableSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accSalesTaxIncome)
                    ->get();

                // 6 - Discount Allowed
                $accDiscountIncome = [6];
                $data['discountAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accDiscountIncome)
                    ->get();

                $data['discountSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accDiscountIncome)
                    ->get();

                // 14 - Loan Interest Income
                $accLoanInterestIncome = GeneralLedgerAccount::whereIn('detail_type_id', [143])->pluck('id');
                $data['loanInterestAllYearTransactions'] = AccountTransaction::select(\DB::raw('ROUND(SUM(amount), 2) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLoanInterestIncome)
                    ->get();

                $data['loanInterestSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('ROUND(SUM(amount), 2) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLoanInterestIncome)
                    ->get();

                // 28 - Penalty Charges
                $accPenalty = GeneralLedgerAccount::whereIn('detail_type_id', [144])->pluck('id');
                $data['penaltyAllYearTransactions'] = AccountTransaction::select(\DB::raw('ROUND(SUM(amount), 2) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accPenalty)
                    ->get();

                $data['penaltySelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('ROUND(SUM(amount), 2) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accPenalty)
                    ->get();

                // 7 - Cost of Sales
                $accCOS = GeneralLedgerAccount::whereIn('detail_type_id', [103])->pluck('id');
                $data['cosAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accCOS)
                    ->get();

                $data['cosSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accCOS)
                    ->get();

                // 132 - Commissions and fees
                $accCommissions = GeneralLedgerAccount::whereIn('detail_type_id', [110])->pluck('id');
                $data['commissionsAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accCommissions)
                    ->get();

                $data['commissionsSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accCommissions)
                    ->get();

                // 155 - Office Rent
                $accRent = [155];
                $data['rentAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accRent)
                    ->get();

                $data['rentSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accRent)
                    ->get();

                // 192 - Other Expense
                $accOtherExpense = [192];
                $data['otherExpAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accOtherExpense)
                    ->get();

                $data['otherExpSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accOtherExpense)
                    ->get();

                // 192 - Opening Ceremony Expenses
                $accCeremonyExpense = [211];
                $data['ceremonyExpAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accCeremonyExpense)
                    ->get();

                $data['ceremonyExpSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accCeremonyExpense)
                    ->get();

                // 156 - Payroll Expenses
                $accPayrollExpense = [156, 179];
                $data['payrollAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accPayrollExpense)
                    ->get();

                $data['payrollSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accPayrollExpense)
                    ->get();

                // 180 - Printing & Stationary
                $accPrinting = [180];
                $data['printingAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accPrinting)
                    ->get();

                $data['printingSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accPrinting)
                    ->get();

                // 185 - Telephone Charges
                $accPhone = [185];
                $data['phoneAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accPhone)
                    ->get();

                $data['phoneSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accPhone)
                    ->get();

                // 186 - Traveling And Transport
                $accTraveling = [186];
                $data['travelAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accTraveling)
                    ->get();

                $data['travelSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accTraveling)
                    ->get();

                // 136 - Fuel Allowance
                $accFuel = [136];
                $data['fuelAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accFuel)
                    ->get();

                $data['fuelSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accFuel)
                    ->get();

                // 187 - Vehicle Repair And Maintenance
                $accRepair = [187];
                $data['repairAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accRepair)
                    ->get();

                $data['repairSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accRepair)
                    ->get();

                // 11 - D & L Charges
                // 24 - D & L Charges - SME
                // 25 - D & L Charges - Vehicle
                // 26 - D & L Charges - Land
                $accDnLCharges = GeneralLedgerAccount::whereIn('detail_type_id', [147])->pluck('id');
                $data['dnLChargesAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accDnLCharges)
                    ->get();

                $data['dnLChargesSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accDnLCharges)
                    ->get();

                // if($status == "In same month") {
                //     $data['dnLChargesAllYearTransactions'] = $data['salesIncomeSelectedMonthTransactions'];

                // } else if($status == "Range not in same month") {
                //     $endDateYear =  date('Y', strtotime(date($endDate)));
                //     $endDateMonth =  date('m', strtotime(date($endDate)));

                //     $data['salesIncomeCurrentMonthTransactions'] = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                //                                         ->where('status', 1)
                //                                         ->where('branch_id', $branchId)
                //                                         ->where('date', '>=', $endDateYear.'-'.$endDateMonth.'-1')
                //                                         ->where('date', '<=', $endDate)
                //                                         ->whereIn('chart_of_account_id', $accSalesIncome)
                //                                         ->get();
                // }



                $data['accSalesIncome'] = json_encode($accSalesIncome, true);

                $saleDetails = $this->showPnLDetails($data['accSalesIncome'], $startDate, $endDate, 'credit');

                $accOtherIncome = [27, 28, 29, 34, 35, 36, 37, 189, 190, 191];
                $data['otherIncomeAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accOtherIncome)
                    ->get();


                $data['otherIncomeSelectedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accOtherIncome)
                    ->get();

                if ($status == "In same month") {
                    $data['otherIncomeCurrentMonthTransactions'] = $data['otherIncomeSelectedMonthTransactions'];
                } else if ($status == "Range not in same month") {
                    $endDateYear =  date('Y', strtotime(date($endDate)));
                    $endDateMonth =  date('m', strtotime(date($endDate)));

                    $data['otherIncomeCurrentMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                        ->where('status', 1)
                        ->where('branch_id', $branchId)
                        ->where('date', '>=', $endDateYear . '-' . $endDateMonth . '-1')
                        ->where('date', '<=', $endDate)
                        ->whereIn('chart_of_account_id', $accOtherIncome)
                        ->get();
                }


                $data['accOtherIncome'] = json_encode($accOtherIncome, true);
                $otherIncomeDetails = $this->showPnLDetails($data['accOtherIncome'], $startDate, $endDate, 'credit');

                // $accCostOfSales = [307];
                // $data['costOfSalesTransactions'] = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $startDate)
                //                                             ->where('date', '<=', $endDate)
                //                                             ->whereIn('chart_of_account_id', $accCostOfSales)
                //                                             ->get();
                // $data['accCostOfSales'] = json_encode($accCostOfSales, true);

                $accInterestIncome = GeneralLedgerAccount::whereIn('detail_type_id', [143])->pluck('id');
                $data['interestIncomeAllYearTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accInterestIncome)
                    ->get();

                $data['interestIncomeSelecetedMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accInterestIncome)
                    ->get();

                if ($status == "In same month") {
                    $data['interestIncomeCurrentMonthTransactions'] = $data['interestIncomeSelecetedMonthTransactions'];
                } else if ($status == "Range not in same month") {
                    $endDateYear =  date('Y', strtotime(date($endDate)));
                    $endDateMonth =  date('m', strtotime(date($endDate)));
                    $data['interestIncomeCurrentMonthTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                        ->where('status', 1)
                        ->where('branch_id', $branchId)
                        ->where('date', '>=', $endDateYear . '-' . $endDateMonth . '-1')
                        ->where('date', '<=', $endDate)
                        ->whereIn('chart_of_account_id', $accInterestIncome)
                        ->get();
                }





                $data['accInterestIncome'] = json_encode($accInterestIncome, true);

                $interestIncomeDetails = $this->showPnLDetails($data['accInterestIncome'], $startDate, $endDate, 'credit');

                // $adminTransactions = [];
                // for($i = 27; $i <= 100; $i++){
                //     $account = GeneralLedgerAccount::find($i);
                //     $allYearTransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $yearStartDate)
                //                                             ->where('date', '<=', $endDate)
                //                                             ->where('chart_of_account_id', $i)
                //                                             ->get();

                //     $slectedMonthTransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $startDate)
                //                                             ->where('date', '<=', $endDate)
                //                                             ->where('chart_of_account_id', $i)
                //                                             ->get();


                //     if($status == "In same month")
                //     {
                //         $CurrentMonthtransactionBalance = $slectedMonthTransactionBalance;

                //     }
                //     else if($status == "Range not in same month")
                //     {


                //         $endDateYear =  date('Y', strtotime(date($endDate)));
                //         $endDateMonth =  date('m', strtotime(date($endDate)));
                //         // return '1-'.$endDateMonth.'-'.$endDateYear .'    '.$endDate;
                //         $CurrentMonthtransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $endDateYear.'-'.$endDateMonth.'-1')
                //                                             ->where('date', '<=', $endDate)
                //                                             ->where('chart_of_account_id', $i)
                //                                             ->get();
                //     }




                //     $adminTransactions[$i] = array('name'=>$account->name, 'allYearTransactionBalance'=>$allYearTransactionBalance[0]->balance_amount, 'slectedMonthTransactionBalance'=>$slectedMonthTransactionBalance[0]->balance_amount, 'CurrentMonthtransactionBalance'=>$CurrentMonthtransactionBalance[0]->balance_amount);
                // }
                // $data['adminTransactions'] = $adminTransactions;



                // $sellDistributeTransactions = [];
                // for($j = 101; $j <= 111; $j++){
                //     $account = GeneralLedgerAccount::find($j);
                //     $allYearTransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $yearStartDate)
                //                                             ->where('date', '<=', $endDate)
                //                                             ->where('chart_of_account_id', $j)
                //                                             ->get();


                //     $selectedMonthTransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $startDate)
                //                                             ->where('date', '<=', $endDate)
                //                                             ->where('chart_of_account_id', $j)
                //                                             ->get();



                //     if($status == "In same month")
                //     {
                //         $currentMonthTransactionBalance = $selectedMonthTransactionBalance;

                //     }
                //     else if($status == "Range not in same month")
                //     {


                //         $endDateYear =  date('Y', strtotime(date($endDate)));
                //         $endDateMonth =  date('m', strtotime(date($endDate)));
                //         // return '1-'.$endDateMonth.'-'.$endDateYear .'    '.$endDate;
                //         $currentMonthTransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $endDateYear.'-'.$endDateMonth.'-1')
                //                                             ->where('date', '<=', $endDate)
                //                                             ->where('chart_of_account_id', $j)
                //                                             ->get();
                //     }



                //     $sellDistributeTransactions[$j] = array('name'=>$account->name, 'allYearTransactionBalance'=>$allYearTransactionBalance[0]->balance_amount, 'selectedMonthTransactionBalance'=>$selectedMonthTransactionBalance[0]->balance_amount, 'currentMonthTransactionBalance'=>$currentMonthTransactionBalance[0]->balance_amount);
                // }
                // $data['sellDistributeTransactions'] = $sellDistributeTransactions;

                // $finOtherTransactions = [];
                // for($k = 112; $k <= 124; $k++){
                //     $account = GeneralLedgerAccount::find($k);
                //     $allYearTransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $yearStartDate)
                //                                             ->where('date', '<=', $endDate)
                //                                             ->where('chart_of_account_id', $k)
                //                                             ->get();

                //     $selecetdMonthTransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                             ->where('status', 1)
                //                                             ->where('branch_id', $branchId)
                //                                             ->where('date', '>=', $startDate)
                //                                             ->where('date', '<=', $endDate)
                //                                             ->where('chart_of_account_id', $k)
                //                                             ->get();

                //     if($status == "In same month")
                //     {
                //         $currenttransactionBalance = $selecetdMonthTransactionBalance;

                //     }
                //     else if($status == "Range not in same month")
                //     {


                //         $endDateYear =  date('Y', strtotime(date($endDate)));
                //         $endDateMonth =  date('m', strtotime(date($endDate)));
                //         // return '1-'.$endDateMonth.'-'.$endDateYear .'    '.$endDate;
                //         $currenttransactionBalance = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                //                                                 ->where('status', 1)
                //                                                 ->where('branch_id', $branchId)
                //                                                 ->where('date', '>=', $endDateYear.'-'.$endDateMonth.'-1')
                //                                                 ->where('date', '<=', $endDate)
                //                                                 ->where('chart_of_account_id', $k)
                //                                                 ->get();


                //     }

                //     $finOtherTransactions[$k] = array('name'=>$account->name, 'allYearTransactionBalance'=>$allYearTransactionBalance[0]->balance_amount, 'selecetdMonthTransactionBalance'=> $selecetdMonthTransactionBalance[0]->balance_amount, 'currenttransactionBalance'=> $currenttransactionBalance[0]->balance_amount);
                // }
                // $data['finOtherTransactions'] = $finOtherTransactions;

                $accStockReturn = [214];
                $data['allYearStockReturnTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $yearStartDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accStockReturn)
                    ->get();

                $data['selectedMonthStockReturnTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accStockReturn)
                    ->get();

                if ($status == "In same month") {
                    $data['currentMonthStockReturnTransactions'] = $data['selectedMonthStockReturnTransactions'];
                } else if ($status == "Range not in same month") {
                    $endDateYear =  date('Y', strtotime(date($endDate)));
                    $endDateMonth =  date('m', strtotime(date($endDate)));

                    $data['currentMonthStockReturnTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                        ->where('status', 1)
                        ->where('branch_id', $branchId)
                        ->where('date', '>=', $endDateYear . '-' . $endDateMonth . '-1')
                        ->where('date', '<=', $endDate)
                        ->whereIn('chart_of_account_id', $accStockReturn)
                        ->get();
                }


                $data['accStockReturn'] = json_encode($accStockReturn, true);

                $stockReturnDetails = $this->showPnLDetails($data['accStockReturn'], $startDate, $endDate, 'debit');

                return view('statements.profit_loss', compact('showTable', 'startDate', 'endDate', 'saleDetails', 'otherIncomeDetails', 'interestIncomeDetails', 'stockReturnDetails'), $data);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function checkMoreThanMonth($startDate, $endDate)
    {

        $status = "";

        $endDateYearSelected =  date('Y-m', strtotime(date($endDate)));

        $startDateYearSelected =  date('Y-m', strtotime(date($startDate)));

        if ($startDateYearSelected == $endDateYearSelected) {
            $status = "In same month";
        } else {
            $status = "Range not in same month";
        }
        return $status;
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

    //BALANCE SHEET FORMAT
    // public function balanceSheetOldIndex(Request $request)
    // {

    //     // if(\Auth::user()->can('balance sheet'))
    //     if(\Auth::user()->can('view sale'))
    //     {
    //         $data['month']     = [
    //             'Apr',
    //             'May',
    //             'Jun',
    //             'Jul',
    //             'Aug',
    //             'Sept',
    //             'Oct',
    //             'Nov',
    //             'Dec',
    //             'Jan',
    //             'Feb',
    //             'Mar',
    //             'Total',
    //         ];
    //         $data['monthList'] = $month = $this->yearMonth();
    //         $data['yearList']  = $this->yearList();

    //         if(isset($request->year))
    //         {
    //             $year = $request->year;
    //         }
    //         else
    //         {
    //             $year = date('Y');
    //         }
    //         $data['currentYear'] = $year;
    //         // $year = ( date('m') > 6) ? date('Y') + 1 : date('Y');
    //         $endYear = $year + 1;

    //         // -------------------------------REVENUE INCOME-------------------------------------------------

    //         $incomes = Revenue::selectRaw('sum(revenues.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id');
    //         // $incomes->where('created_by', '=', \Auth::user()->creatorId());
    //         $incomes->where('branch_id', '=', \Auth::user()->branchId());
    //         $incomes->whereRAW('YEAR(date) =?', [$request->year]);
    //         $incomes->groupBy('month', 'year', 'category_id');
    //         $incomes        = $incomes->get();
    //         // dd($request->year);
    //         $tmpIncomeArray = [];
    //         foreach($incomes as $income)
    //         {
    //             $tmpIncomeArray[$income->category_id][$income->month] = $income->amount;
    //         }

    //         $incomeCatAmount_1  = $incomeCatAmount_2 = $incomeCatAmount_3 = $incomeCatAmount_4 = $incomeCatAmount_5 = $incomeCatAmount_6 = $incomeCatAmount_7 = $incomeCatAmount_8 = $incomeCatAmount_9 = $incomeCatAmount_10 = $incomeCatAmount_11 = $incomeCatAmount_12 = 0;
    //         $revenueIncomeArray = array();
    //         foreach($tmpIncomeArray as $cat_id => $record)
    //         {

    //             $tmp             = [];
    //             $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
    //             $sumData         = [];
    //             for($i = 1; $i <= 12; $i++)
    //             {
    //                 $sumData[] = array_key_exists($i, $record) ? $record[$i] : 0;
    //             }

    //             $month_1 = array_slice($sumData, 3, 1);
    //             $month_2 = array_slice($sumData, 4, 1);
    //             $month_3 = array_slice($sumData, 5, 1);
    //             $month_4 = array_slice($sumData, 6, 1);
    //             $month_5 = array_slice($sumData, 7, 1);
    //             $month_6 = array_slice($sumData, 8, 1);
    //             $month_7 = array_slice($sumData, 9, 1);
    //             $month_8 = array_slice($sumData, 10, 1);
    //             $month_9 = array_slice($sumData, 11, 1);
    //             $month_10 = array_slice($sumData, 12, 1);
    //             $month_11 = array_slice($sumData, 1, 1);
    //             $month_12 = array_slice($sumData, 2, 1);


    //             $incomeData[__('Apr')] = $sum_1 = array_sum($month_1);
    //             $incomeData[__('May')] = $sum_2 = array_sum($month_2);
    //             $incomeData[__('Jun')] = $sum_3 = array_sum($month_3);
    //             $incomeData[__('Jul')] = $sum_4 = array_sum($month_4);
    //             $incomeData[__('Aug')] = $sum_5 = array_sum($month_5);
    //             $incomeData[__('Sept')] = $sum_6 = array_sum($month_6);
    //             $incomeData[__('Oct')] = $sum_7 = array_sum($month_7);
    //             $incomeData[__('Nov')] = $sum_8 = array_sum($month_8);
    //             $incomeData[__('Dec')] = $sum_9 = array_sum($month_9);
    //             $incomeData[__('Jan')] = $sum_10 = array_sum($month_10);
    //             $incomeData[__('Feb')] = $sum_11 = array_sum($month_11);
    //             $incomeData[__('Mar')] = $sum_12 = array_sum($month_12);
    //             $incomeData[__('Total')]   = array_sum(
    //                 array(
    //                     $sum_1,
    //                     $sum_2,
    //                     $sum_3,
    //                     $sum_4,
    //                     $sum_5,
    //                     $sum_6,
    //                     $sum_7,
    //                     $sum_8,
    //                     $sum_9,
    //                     $sum_10,
    //                     $sum_11,
    //                     $sum_12,
    //                 )
    //             );

    //             $incomeCatAmount_1 += $sum_1;
    //             $incomeCatAmount_2 += $sum_2;
    //             $incomeCatAmount_3 += $sum_3;
    //             $incomeCatAmount_4 += $sum_4;
    //             $incomeCatAmount_5 += $sum_5;
    //             $incomeCatAmount_6 += $sum_6;
    //             $incomeCatAmount_7 += $sum_7;
    //             $incomeCatAmount_8 += $sum_8;
    //             $incomeCatAmount_9 += $sum_9;
    //             $incomeCatAmount_10 += $sum_10;
    //             $incomeCatAmount_11 += $sum_11;
    //             $incomeCatAmount_12 += $sum_12;

    //             $data['month'] = array_keys($incomeData);
    //             $tmp['amount'] = array_values($incomeData);

    //             $revenueIncomeArray[] = $tmp;

    //         }

    //         $data['incomeCatAmount'] = $incomeCatAmount = [
    //             $incomeCatAmount_1,
    //             $incomeCatAmount_2,
    //             $incomeCatAmount_3,
    //             $incomeCatAmount_4,
    //             $incomeCatAmount_5,
    //             $incomeCatAmount_6,
    //             $incomeCatAmount_7,
    //             $incomeCatAmount_8,
    //             $incomeCatAmount_9,
    //             $incomeCatAmount_10,
    //             $incomeCatAmount_11,
    //             $incomeCatAmount_12,
    //             array_sum(
    //                 array(
    //                     $incomeCatAmount_1,
    //                     $incomeCatAmount_2,
    //                     $incomeCatAmount_3,
    //                     $incomeCatAmount_4,
    //                     $incomeCatAmount_5,
    //                     $incomeCatAmount_6,
    //                     $incomeCatAmount_7,
    //                     $incomeCatAmount_8,
    //                     $incomeCatAmount_9,
    //                     $incomeCatAmount_10,
    //                     $incomeCatAmount_11,
    //                     $incomeCatAmount_12,
    //                 )
    //             ),
    //         ];

    //         $data['revenueIncomeArray'] = $revenueIncomeArray;

    //         //-----------------------INVOICE INCOME---------------------------------------------

    //         $invoices = Invoice:: selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,invoice_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
    //         $invoices->whereRAW('YEAR(send_date) =?', [$year]);
    //         if(!empty($request->customer))
    //         {
    //             $invoices->where('customer_id', '=', $request->customer);
    //         }
    //         $invoices        = $invoices->get();
    //         $invoiceTmpArray = [];
    //         foreach($invoices as $invoice)
    //         {
    //             $invoiceTmpArray[$invoice->category_id][$invoice->month][] = $invoice->getTotal();
    //         }

    //         $invoiceCatAmount_1 = $invoiceCatAmount_2 = $invoiceCatAmount_3 = $invoiceCatAmount_4 = $incomeCatAmount_5 = $incomeCatAmount_6 = $incomeCatAmount_7 = $incomeCatAmount_8 = $incomeCatAmount_9 = $incomeCatAmount_10 = $incomeCatAmount_11 = $incomeCatAmount_12 = 0;
    //         $invoiceIncomeArray = array();
    //         foreach($invoiceTmpArray as $cat_id => $record)
    //         {

    //             $invoiceTmp             = [];
    //             $invoiceTmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
    //             $invoiceSumData         = [];
    //             for($i = 1; $i <= 12; $i++)
    //             {
    //                 $invoiceSumData[] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;

    //             }

    //             $month_1                          = array_slice($invoiceSumData, 3, 1);
    //             $month_2                          = array_slice($invoiceSumData, 4, 1);
    //             $month_3                          = array_slice($invoiceSumData, 5, 1);
    //             $month_4                          = array_slice($invoiceSumData, 6, 1);
    //             $month_5                          = array_slice($invoiceSumData, 7, 1);
    //             $month_6                          = array_slice($invoiceSumData, 8, 1);
    //             $month_7                          = array_slice($invoiceSumData, 9, 1);
    //             $month_8                          = array_slice($invoiceSumData, 10, 1);
    //             $month_9                          = array_slice($invoiceSumData, 11, 1);
    //             $month_10                          = array_slice($invoiceSumData, 12, 1);
    //             $month_11                          = array_slice($invoiceSumData, 1, 1);
    //             $month_12                          = array_slice($invoiceSumData, 2, 1);
    //             $invoiceIncomeData[__('Apr')] = $sum_1 = array_sum($month_1);
    //             $invoiceIncomeData[__('May')] = $sum_2 = array_sum($month_2);
    //             $invoiceIncomeData[__('Jun')] = $sum_3 = array_sum($month_3);
    //             $invoiceIncomeData[__('Jul')] = $sum_4 = array_sum($month_4);
    //             $invoiceIncomeData[__('Aug')] = $sum_5 = array_sum($month_5);
    //             $invoiceIncomeData[__('Sept')] = $sum_6 = array_sum($month_6);
    //             $invoiceIncomeData[__('Oct')] = $sum_7 = array_sum($month_7);
    //             $invoiceIncomeData[__('Nov')] = $sum_8 = array_sum($month_8);
    //             $invoiceIncomeData[__('Dec')] = $sum_9 = array_sum($month_9);
    //             $invoiceIncomeData[__('Jan')] = $sum_10 = array_sum($month_10);
    //             $invoiceIncomeData[__('Feb')] = $sum_11 = array_sum($month_11);
    //             $invoiceIncomeData[__('Mar')] = $sum_12 = array_sum($month_12);
    //             $invoiceIncomeData[__('Total')]   = array_sum(
    //                 array(
    //                     $sum_1,
    //                     $sum_2,
    //                     $sum_3,
    //                     $sum_4,
    //                     $sum_5,
    //                     $sum_6,
    //                     $sum_7,
    //                     $sum_8,
    //                     $sum_9,
    //                     $sum_10,
    //                     $sum_11,
    //                     $sum_12,
    //                 )
    //             );
    //             $invoiceCatAmount_1               += $sum_1;
    //             $invoiceCatAmount_2               += $sum_2;
    //             $invoiceCatAmount_3               += $sum_3;
    //             $invoiceCatAmount_4               += $sum_4;
    //             $invoiceCatAmount_5               += $sum_5;
    //             $invoiceCatAmount_6               += $sum_6;
    //             $invoiceCatAmount_7               += $sum_7;
    //             $invoiceCatAmount_8               += $sum_8;
    //             $invoiceCatAmount_9               += $sum_9;
    //             $invoiceCatAmount_10              += $sum_10;
    //             $invoiceCatAmount_11              += $sum_11;
    //             $invoiceCatAmount_12              += $sum_12;

    //             $invoiceTmp['amount'] = array_values($invoiceIncomeData);

    //             $invoiceIncomeArray[] = $invoiceTmp;

    //         }

    //         $data['invoiceIncomeCatAmount'] = $invoiceIncomeCatAmount = [
    //             $invoiceCatAmount_1,
    //             $invoiceCatAmount_2,
    //             $invoiceCatAmount_3,
    //             $invoiceCatAmount_4,
    //             // $invoiceCatAmount_5,
    //             // $invoiceCatAmount_6,
    //             // $invoiceCatAmount_7,
    //             // $invoiceCatAmount_8,
    //             // $invoiceCatAmount_9,
    //             // $invoiceCatAmount_10,
    //             // $invoiceCatAmount_11,
    //             // $invoiceCatAmount_12,
    //             array_sum(
    //                 array(
    //                     $invoiceCatAmount_1,
    //                     $invoiceCatAmount_2,
    //                     $invoiceCatAmount_3,
    //                     $invoiceCatAmount_4,
    //                     // $invoiceCatAmount_5,
    //                     // $invoiceCatAmount_6,
    //                     // $invoiceCatAmount_7,
    //                     // $invoiceCatAmount_8,
    //                     // $invoiceCatAmount_9,
    //                     // $invoiceCatAmount_10,
    //                     // $invoiceCatAmount_11,
    //                     // $invoiceCatAmount_12,
    //                 )
    //             ),
    //         ];


    //         $data['invoiceIncomeArray'] = $invoiceIncomeArray;

    //         $data['totalIncome'] = $totalIncome = array_map(
    //             function (){
    //                 return array_sum(func_get_args());
    //             }, $invoiceIncomeCatAmount, $incomeCatAmount
    //         );

    //         //---------------------------------PAYMENT EXPENSE-----------------------------------

    //             $expenses = Payment::selectRaw('sum(payments.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id');
    //             $expenses->where('created_by', '=', \Auth::user()->creatorId());
    //             $expenses->whereRAW('YEAR(date) =?', [$year]);
    //             $expenses->groupBy('month', 'year', 'category_id');
    //             $expenses = $expenses->get();

    //             $tmpExpenseArray = [];
    //             foreach($expenses as $expense)
    //             {
    //                 $tmpExpenseArray[$expense->category_id][$expense->month] = $expense->amount;
    //             }

    //             $expenseArray       = [];
    //             $expenseCatAmount_1 = $expenseCatAmount_2 = $expenseCatAmount_3 = $expenseCatAmount_4 = $expenseCatAmount_5 = $expenseCatAmount_6 = $expenseCatAmount_7 = $expenseCatAmount_8 = $expenseCatAmount_9 = $expenseCatAmount_10 = $expenseCatAmount_11 = $expenseCatAmount_12 = 0;
    //             foreach($tmpExpenseArray as $cat_id => $record)
    //             {
    //                 $tmp             = [];
    //                 $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
    //                 $expenseSumData  = [];
    //                 for($i = 1; $i <= 12; $i++)
    //                 {
    //                     $expenseSumData[] = array_key_exists($i, $record) ? $record[$i] : 0;

    //                 }

    //                 $month_1 = array_slice($expenseSumData, 3, 1);
    //                 $month_2 = array_slice($expenseSumData, 4, 1);
    //                 $month_3 = array_slice($expenseSumData, 5, 1);
    //                 $month_4 = array_slice($expenseSumData, 6, 1);
    //                 $month_5 = array_slice($expenseSumData, 7, 1);
    //                 $month_6 = array_slice($expenseSumData, 8, 1);
    //                 $month_7 = array_slice($expenseSumData, 9, 1);
    //                 $month_8 = array_slice($expenseSumData, 10, 1);
    //                 $month_9 = array_slice($expenseSumData, 11, 1);
    //                 $month_10 = array_slice($expenseSumData, 12, 1);
    //                 $month_11 = array_slice($expenseSumData, 1, 1);
    //                 $month_12 = array_slice($expenseSumData, 2, 1);

    //                 $expenseData[__('Apr')] = $sum_1 = array_sum($month_1);
    //                 $expenseData[__('May')] = $sum_2 = array_sum($month_2);
    //                 $expenseData[__('Jun')] = $sum_3 = array_sum($month_3);
    //                 $expenseData[__('Jul')] = $sum_4 = array_sum($month_4);
    //                 $expenseData[__('Aug')] = $sum_5 = array_sum($month_5);
    //                 $expenseData[__('Sept')] = $sum_6 = array_sum($month_6);
    //                 $expenseData[__('Oct')] = $sum_7 = array_sum($month_7);
    //                 $expenseData[__('Nov')] = $sum_8 = array_sum($month_8);
    //                 $expenseData[__('Dec')] = $sum_9 = array_sum($month_9);
    //                 $expenseData[__('Jan')] = $sum_10 = array_sum($month_10);
    //                 $expenseData[__('Feb')] = $sum_11 = array_sum($month_11);
    //                 $expenseData[__('Mar')] = $sum_12 = array_sum($month_12);
    //                 $expenseData[__('Total')]   = array_sum(
    //                     array(
    //                         $sum_1,
    //                         $sum_2,
    //                         $sum_3,
    //                         $sum_4,
    //                         $sum_5,
    //                         $sum_6,
    //                         $sum_7,
    //                         $sum_8,
    //                         $sum_9,
    //                         $sum_10,
    //                         $sum_11,
    //                         $sum_12,
    //                     )
    //                 );

    //                 $expenseCatAmount_1 += $sum_1;
    //                 $expenseCatAmount_2 += $sum_2;
    //                 $expenseCatAmount_3 += $sum_3;
    //                 $expenseCatAmount_4 += $sum_4;
    //                 $expenseCatAmount_5 += $sum_5;
    //                 $expenseCatAmount_6 += $sum_6;
    //                 $expenseCatAmount_7 += $sum_7;
    //                 $expenseCatAmount_8 += $sum_8;
    //                 $expenseCatAmount_9 += $sum_9;
    //                 $expenseCatAmount_10 += $sum_10;
    //                 $expenseCatAmount_11 += $sum_11;
    //                 $expenseCatAmount_12 += $sum_12;

    //                 $data['month'] = array_keys($expenseData);
    //                 $tmp['amount'] = array_values($expenseData);

    //                 $expenseArray[] = $tmp;

    //             }

    //             $data['expenseCatAmount'] = $expenseCatAmount = [
    //                 $expenseCatAmount_1,
    //                 $expenseCatAmount_2,
    //                 $expenseCatAmount_3,
    //                 $expenseCatAmount_4,
    //                 $expenseCatAmount_5,
    //                 $expenseCatAmount_6,
    //                 $expenseCatAmount_7,
    //                 $expenseCatAmount_8,
    //                 $expenseCatAmount_9,
    //                 $expenseCatAmount_10,
    //                 $expenseCatAmount_11,
    //                 $expenseCatAmount_12,
    //                 array_sum(
    //                     array(
    //                         $expenseCatAmount_1,
    //                         $expenseCatAmount_2,
    //                         $expenseCatAmount_3,
    //                         $expenseCatAmount_4,
    //                         $expenseCatAmount_5,
    //                         $expenseCatAmount_6,
    //                         $expenseCatAmount_7,
    //                         $expenseCatAmount_8,
    //                         $expenseCatAmount_9,
    //                         $expenseCatAmount_10,
    //                         $expenseCatAmount_11,
    //                         $expenseCatAmount_12,
    //                     )
    //                 ),
    //             ];
    //             $data['expenseArray']     = $expenseArray;

    //             //----------------------------EXPENSE BILL-----------------------------------------------------------------------

    //             $bills = Bill:: selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,bill_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
    //             $bills->whereRAW('YEAR(send_date) =?', [$year]);
    //             if(!empty($request->customer))
    //             {
    //                 $bills->where('vender_id', '=', $request->vender);
    //             }
    //             $bills        = $bills->get();
    //             $billTmpArray = [];
    //             foreach($bills as $bill)
    //             {
    //                 $billTmpArray[$bill->category_id][$bill->month][] = $bill->getTotal();
    //             }

    //             $billExpenseArray       = [];
    //             $billExpenseCatAmount_1 = $billExpenseCatAmount_2 = $billExpenseCatAmount_3 = $billExpenseCatAmount_4 = $billExpenseCatAmount_5 = $billExpenseCatAmount_6 = $billExpenseCatAmount_7 = $billExpenseCatAmount_8 = $billExpenseCatAmount_9 = $billExpenseCatAmount_10 = $billExpenseCatAmount_11 = $billExpenseCatAmount_12 = 0;
    //             foreach($billTmpArray as $cat_id => $record)
    //             {
    //                 $billTmp             = [];
    //                 $billTmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
    //                 $billExpensSumData   = [];
    //                 for($i = 1; $i <= 12; $i++)
    //                 {
    //                     $billExpensSumData[] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
    //                 }

    //                 $month_1 = array_slice($billExpensSumData, 3, 1);
    //                 $month_2 = array_slice($billExpensSumData, 4, 1);
    //                 $month_3 = array_slice($billExpensSumData, 5, 1);
    //                 $month_4 = array_slice($billExpensSumData, 6, 1);
    //                 $month_5 = array_slice($billExpensSumData, 7, 1);
    //                 $month_6 = array_slice($billExpensSumData, 8, 1);
    //                 $month_7 = array_slice($billExpensSumData, 9, 1);
    //                 $month_8 = array_slice($billExpensSumData, 10, 1);
    //                 $month_9 = array_slice($billExpensSumData, 11, 1);
    //                 $month_10 = array_slice($billExpensSumData, 12, 1);
    //                 $month_11 = array_slice($billExpensSumData, 1, 1);
    //                 $month_12 = array_slice($billExpensSumData, 2, 1);

    //                 $billExpenseData[__('Apr')] = $sum_1 = array_sum($month_1);
    //                 $billExpenseData[__('May')] = $sum_2 = array_sum($month_2);
    //                 $billExpenseData[__('Jun')] = $sum_3 = array_sum($month_3);
    //                 $billExpenseData[__('Jul')] = $sum_4 = array_sum($month_4);
    //                 $billExpenseData[__('Aug')] = $sum_5 = array_sum($month_5);
    //                 $billExpenseData[__('Sept')] = $sum_6 = array_sum($month_6);
    //                 $billExpenseData[__('Oct')] = $sum_7 = array_sum($month_7);
    //                 $billExpenseData[__('Nov')] = $sum_8 = array_sum($month_8);
    //                 $billExpenseData[__('Dec')] = $sum_9 = array_sum($month_9);
    //                 $billExpenseData[__('Jan')] = $sum_10 = array_sum($month_10);
    //                 $billExpenseData[__('Feb')] = $sum_11 = array_sum($month_11);
    //                 $billExpenseData[__('Mar')] = $sum_12 = array_sum($month_12);
    //                 $billExpenseData[__('Total')]   = array_sum(
    //                     array(
    //                         $sum_1,
    //                         $sum_2,
    //                         $sum_3,
    //                         $sum_4,
    //                         $sum_5,
    //                         $sum_6,
    //                         $sum_7,
    //                         $sum_8,
    //                         $sum_9,
    //                         $sum_10,
    //                         $sum_11,
    //                         $sum_12,
    //                     )
    //                 );

    //                 $billExpenseCatAmount_1 += $sum_1;
    //                 $billExpenseCatAmount_2 += $sum_2;
    //                 $billExpenseCatAmount_3 += $sum_3;
    //                 $billExpenseCatAmount_4 += $sum_4;
    //                 $billExpenseCatAmount_5 += $sum_5;
    //                 $billExpenseCatAmount_6 += $sum_6;
    //                 $billExpenseCatAmount_7 += $sum_7;
    //                 $billExpenseCatAmount_8 += $sum_8;
    //                 $billExpenseCatAmount_9 += $sum_9;
    //                 $billExpenseCatAmount_10 += $sum_10;
    //                 $billExpenseCatAmount_11 += $sum_11;
    //                 $billExpenseCatAmount_12 += $sum_12;

    //                 $data['month']     = array_keys($billExpenseData);
    //                 $billTmp['amount'] = array_values($billExpenseData);

    //                 $billExpenseArray[] = $billTmp;

    //             }

    //             $data['billExpenseCatAmount'] = $billExpenseCatAmount = [
    //                 $billExpenseCatAmount_1,
    //                 $billExpenseCatAmount_2,
    //                 $billExpenseCatAmount_3,
    //                 $billExpenseCatAmount_4,
    //                 $billExpenseCatAmount_5,
    //                 $billExpenseCatAmount_6,
    //                 $billExpenseCatAmount_7,
    //                 $billExpenseCatAmount_8,
    //                 $billExpenseCatAmount_9,
    //                 $billExpenseCatAmount_10,
    //                 $billExpenseCatAmount_11,
    //                 $billExpenseCatAmount_12,
    //                 array_sum(
    //                     array(
    //                         $billExpenseCatAmount_1,
    //                         $billExpenseCatAmount_2,
    //                         $billExpenseCatAmount_3,
    //                         $billExpenseCatAmount_4,
    //                         $billExpenseCatAmount_5,
    //                         $billExpenseCatAmount_6,
    //                         $billExpenseCatAmount_7,
    //                         $billExpenseCatAmount_8,
    //                         $billExpenseCatAmount_9,
    //                         $billExpenseCatAmount_10,
    //                         $billExpenseCatAmount_11,
    //                         $billExpenseCatAmount_12,
    //                     )
    //                 ),
    //             ];

    //             $data['billExpenseArray'] = $billExpenseArray;


    //             $data['totalExpense'] = $totalExpense = array_map(
    //                 function (){
    //                     return array_sum(func_get_args());
    //                 }, $billExpenseCatAmount, $expenseCatAmount
    //             );


    //             foreach($totalIncome as $k => $income)
    //             {
    //             $netProfit[] = $income - $totalExpense[$k];
    //             }
    //             $data['netProfitArray'] = $netProfit;

    //             $filter['startDateRange'] = 'April ' . $year;
    //             $filter['endDateRange']   = 'March ' . $endYear;

    //             return view('statements.balance_sheet_summary', compact('filter'), $data);
    //         }
    //         else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }


    // }

    public function balanceSheetIndex()
    {
        if (\Auth::user()->can('view balance sheet')) {
            $showTable = 'No';
            $currencySymbol = $this->currencySymbol;
            return view('statements.balance_sheet', compact('showTable', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function balanceSheetSearch(Request $request)
    {
        if (\Auth::user()->can('view balance sheet')) {
            if (isset($request->end_date) && isset($request->start_date)) {
                $showTable = 'Yes';
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                $branchId = \Auth::user()->branchId();

                $dateArray = explode('-', $endDate);
                if ($dateArray[1] >= 4) {
                    $startYear = $dateArray[0];
                } else {
                    $startYear = $dateArray[0] - 1;
                }

                $accounts = GeneralLedgerAccount::get();
                $allAccounts = [];
                foreach ($accounts as $account) {
                    $transactions = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                        ->where('status', 1)
                        ->where('branch_id', $branchId)
                        ->where('date', '>=', $startDate)
                        ->where('date', '<=', $endDate)
                        ->where('chart_of_account_id', $account->id)
                        ->get();
                    foreach ($transactions as $transaction) {
                        $debit_amount = $transaction->debit_amount;
                        $credit_amount = $transaction->credit_amount;
                    }
                    $allAccounts[$account->id][] = array('name' => $account->name, 'debit_amount' => $debit_amount, 'credit_amount' => $credit_amount);
                }

                $data['allAccounts'] = $allAccounts;

                // $accInvestments = [158,159,160,161,162,163,164,165];
                $accInvestments = [70];
                $data['investmentTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accInvestments)
                    ->get();
                $data['accInvestments'] = json_encode($accInvestments, true);


                // $accInvestments = [158,159,160,161,162,163,164,165];
                $accOtherCurrentAsset = [48];
                $data['otherCurrentAsset'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accOtherCurrentAsset)
                    ->get();
                $data['accOtherCurrentAsset'] = json_encode($accOtherCurrentAsset, true);

                // $accInvestments = [158,159,160,161,162,163,164,165];
                $accLoanInventry = [10];
                $data['loanInventry'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLoanInventry)
                    ->get();
                $data['accLoanInventry'] = json_encode($accLoanInventry, true);


                $accStockTransfer = Branch::where('id', $branchId)->pluck('chart_of_account_id');;
                $data['stockTransfer'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accStockTransfer)
                    ->get();
                $data['accStockTransfer'] = json_encode($accStockTransfer, true);

                // Inventory Asset
                $accLoanInventryAsset = [202];
                $data['loanInventryAsset'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLoanInventryAsset)
                    ->get();
                $data['accLoanInventryAsset'] = json_encode($accLoanInventryAsset, true);

                // Welfare Fund
                $accWelfare = [213];
                $data['welfare'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accWelfare)
                    ->get();
                $data['accWelfare'] = json_encode($accWelfare, true);

                $accLoanCtrlInventry = [12];
                $data['loanCtrlInventry'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLoanCtrlInventry)
                    ->get();
                $data['accLoanCtrlInventry'] = json_encode($accLoanCtrlInventry, true);


                // $accLoanStocks = [166,167,168,169,170,171,172,173];
                $accLoanStocks = [19, 20, 21, 22, 23];
                $data['loanStockTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLoanStocks)
                    ->get();
                $data['accLoanStocks'] = json_encode($accLoanStocks, true);

                // $accTradeRecievables = [186,196];
                $accTradeRecievables = [];
                $data['tradeRecievableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accTradeRecievables)
                    ->get();
                $data['accTradeRecievables'] = json_encode($accTradeRecievables, true);



                // $accOtherRecievables = [187,188,189,190,191,192,193,194,195,197,199,200,204,205,206,207];
                $accOtherRecievables = [];
                $data['otherRecievableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accOtherRecievables)
                    ->get();
                $data['accOtherRecievables'] = json_encode($accOtherRecievables, true);

                // $accInstallmentCollections = [177,178,179,180,181,182,183,315];
                $accInstallmentCollections = [13, 30, 31, 32, 33, 205];
                $data['installmentCollectionTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accInstallmentCollections)
                    ->get();

                $data['accInstallmentCollections'] = json_encode($accInstallmentCollections, true);

                // $accAdvancePrepayments = [208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226];
                $accAdvancePrepayments = [212];
                $data['advancePrepaymentTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accAdvancePrepayments)
                    ->get();
                $data['accAdvancePrepayments'] = json_encode($accAdvancePrepayments, true);

                // $accCashBankandHands = [227,228,229,230,309];
                $accCashBankandHands = [2, 196];
                $data['cashBankandHandTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accCashBankandHands)
                    ->get();
                $data['accCashBankandHands'] = json_encode($accCashBankandHands, true);

                // Liabilities
                // $accLeaseCrreditors = [239,240];
                $accLeaseCrreditors = [];
                $data['leaseCrreditorTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLeaseCrreditors)
                    ->get();
                $data['accLeaseCrreditors'] = json_encode($accLeaseCrreditors, true);

                // $accLongTermPayables = [235,236,237,238];
                $accLongTermPayables = [];
                $data['longTermPayableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLongTermPayables)
                    ->get();
                $data['accLongTermPayables'] = json_encode($accLongTermPayables, true);

                // $accTradePayables = [316];
                $accTradePayables = [208];
                $data['tradePayableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accTradePayables)
                    ->get();
                $data['accTradePayables'] = json_encode($accTradePayables, true);

                // $accOtherPayables = [244,245,246,247,248,249,250,263,265,266,267,268,269,276,278,279,280,284,289,290];
                $accOtherPayables = [];
                $data['otherPayableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accOtherPayables)
                    ->get();
                $data['accOtherPayables'] = json_encode($accOtherPayables, true);

                // $accLoanStockControls = [252,253,254,255,256,257,258,259];
                $accLoanStockControls = [12, 15, 16, 17, 18];
                $data['loanStockControlTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLoanStockControls)
                    ->get();
                $data['accLoanStockControls'] = json_encode($accLoanStockControls, true);

                // $accLoanPayables = [261,262];
                $accLoanPayables = [];
                $data['loanPayableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accLoanPayables)
                    ->get();
                $data['accLoanPayables'] = json_encode($accLoanPayables, true);

                // $accSalaryPayables = [251,260];
                $accSalaryPayables = [];
                $data['salaryPayableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accSalaryPayables)
                    ->get();
                $data['accSalaryPayables'] = json_encode($accSalaryPayables, true);

                // $accMemberInsurePayables = [295,296];
                $accMemberInsurePayables = [];
                $data['memberInsurePayableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accMemberInsurePayables)
                    ->get();
                $data['accMemberInsurePayables'] = json_encode($accMemberInsurePayables, true);

                // $accHeadOfficAcs = [298,299,300,301,302,303,304];
                $accHeadOfficAcs = [];
                $data['headOfficAcTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accHeadOfficAcs)
                    ->get();
                $data['accHeadOfficAcs'] = json_encode($accHeadOfficAcs, true);

                // $accBadDebtors = [270,271,272,273,274,312];
                $accBadDebtors = [];
                $data['badDebtorTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accBadDebtors)
                    ->get();
                $data['accBadDebtors'] = json_encode($accBadDebtors, true);

                // $accTaxPayables = [264,317,305];
                $accTaxPayables = [];
                $data['taxPayableTransactions'] = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $accTaxPayables)
                    ->get();
                $data['accTaxPayables'] = json_encode($accTaxPayables, true);

                // Profit & Loss for period
                $pnlIncomesAcc = GeneralLedgerAccount::whereIn('detail_type_id', [88, 89, 90])->pluck('id');
                $pnlIncomes = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $pnlIncomesAcc)
                    ->get();

                $pnlCosAcc = GeneralLedgerAccount::whereIn('detail_type_id', [103])->pluck('id');
                $pnlCos = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $pnlCosAcc)
                    ->get();

                $pnlInterestAcc = GeneralLedgerAccount::whereIn('detail_type_id', [143, 144])->pluck('id');
                $pnlInterest = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $pnlInterestAcc)
                    ->get();

                $pnlDiscountAcc = [6];
                $pnlDiscount = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $pnlDiscountAcc)
                    ->get();

                $pnlExpensesAcc = [132, 136, 155, 192, 156, 179, 180, 185, 186, 187, 211];
                $pnlExpenses = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $pnlExpensesAcc)
                    ->get();

                $dnLChargesAcc = GeneralLedgerAccount::whereIn('detail_type_id', [147])->pluck('id');
                $pnlDnLCharges = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 0 THEN amount ELSE -amount END) AS balance_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->whereIn('chart_of_account_id', $dnLChargesAcc)
                    ->get();

                // dump($pnlIncomes[0]->balance_amount);
                $currencySymbol = $this->currencySymbol;
                $data['profitnLoss'] = $pnlIncomes[0]->balance_amount + $pnlCos[0]->balance_amount + $pnlInterest[0]->balance_amount - $pnlDiscount[0]->balance_amount - $pnlExpenses[0]->balance_amount + $pnlDnLCharges[0]->balance_amount;
                // dd($data['profitnLoss']);
                return view('statements.balance_sheet', compact('showTable', 'startDate', 'endDate', 'currencySymbol'), $data);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function showDetails($accIds, $endDate, $balanceType)
    {
        if (\Auth::user()->can('show balance sheet')) {
            $ids = json_decode($accIds, true);
            $branchId = \Auth::user()->branchId();
            $allAccounts = [];

            $accounts = GeneralLedgerAccount::whereIn('id', $ids)->get();
            foreach ($accounts as $account) {
                $transactions = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '<=', $endDate)
                    ->where('chart_of_account_id', $account->id)
                    ->get();
                foreach ($transactions as $transaction) {
                    $debit_amount = $transaction->debit_amount;
                    $credit_amount = $transaction->credit_amount;

                    if ($balanceType == 'debit') {
                        $balanceAmount = $transaction->debit_amount - $transaction->credit_amount;
                    } else if ($balanceType == 'credit') {
                        $balanceAmount = $transaction->credit_amount - $transaction->debit_amount;
                    }
                }
                $allAccounts[$account->id][] = array('name' => $account->account_name, 'debit_amount' => $debit_amount, 'credit_amount' => $credit_amount, 'balance_amount' => $balanceAmount);
            }

            return view('statements.account_detail', compact('allAccounts'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    // public function showPnLDetails($accIds, $startDate, $endDate, $balanceType){
    //     if(\Auth::user()->can('show balance sheet'))
    //     {
    //         $ids = json_decode($accIds, true);
    //         $branchId = \Auth::user()->branchId();
    //         $allAccounts = [];

    //         $accounts = ChartOfAccount::whereIn('id', $ids)->get();
    //         foreach($accounts as $account){
    //             $transactions = AccountTransaction::select( \DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
    //                                                     ->where('status', 1)
    //                                                     ->where('branch_id', $branchId)
    //                                                     ->where('date', '>=', $startDate)
    //                                                     ->where('date', '<=', $endDate)
    //                                                     ->where('chart_of_account_id', $account->id)
    //                                                     ->get();
    //             foreach($transactions as $transaction){
    //                 $debit_amount = $transaction->debit_amount;
    //                 $credit_amount = $transaction->credit_amount;

    //                 if($balanceType == 'debit'){
    //                     $balanceAmount = $transaction->debit_amount - $transaction->credit_amount;
    //                 } else if($balanceType == 'credit'){
    //                     $balanceAmount = $transaction->credit_amount - $transaction->debit_amount;
    //                 }
    //             }
    //             $allAccounts[$account->id][] = array('name'=>$account->name, 'debit_amount'=>$debit_amount, 'credit_amount'=>$credit_amount, 'balance_amount'=>$balanceAmount);
    //         }

    //         return view('statements.account_detail', compact('allAccounts'));
    //     }
    //     else
    //     {
    //         return response()->json(['error' => __('Permission denied.')], 401);
    //     }
    // }

    public function showPnLDetails($accIds, $startDate, $endDate, $balanceType)
    {
        if (\Auth::user()->can('show balance sheet')) {
            $ids = json_decode($accIds, true);
            $branchId = \Auth::user()->branchId();
            $allAccounts = [];

            $accounts = GeneralLedgerAccount::whereIn('id', $ids)->get();
            foreach ($accounts as $account) {
                $transactions = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount END) AS debit_amount, SUM(CASE WHEN debit_credit = 0 THEN amount END) AS credit_amount'))
                    ->where('status', 1)
                    ->where('branch_id', $branchId)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->where('chart_of_account_id', $account->id)
                    ->get();
                foreach ($transactions as $transaction) {
                    $debit_amount = $transaction->debit_amount;
                    $credit_amount = $transaction->credit_amount;

                    if ($balanceType == 'debit') {
                        $balanceAmount = $transaction->debit_amount - $transaction->credit_amount;
                    } else if ($balanceType == 'credit') {
                        $balanceAmount = $transaction->credit_amount - $transaction->debit_amount;
                    }
                }
                $allAccounts[$account->id][] = array('name' => $account->account_name, 'debit_amount' => $debit_amount, 'credit_amount' => $credit_amount, 'balance_amount' => $balanceAmount);
            }

            return $allAccounts;
        } else {
            return 'Permission denied.';
        }
    }

    //Cashflow Statement
    public function cashFlowIndex(Request $request)
    {

        // if(\Auth::user()->can('cash flow statement'))
        if (\Auth::user()->can('view sale')) {
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

            return view('statements.cash_flow_summary', compact('filter'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //Changes in Equity Statement
    public function changesInEquityIndex(Request $request)
    {

        // if(\Auth::user()->can('equity statement'))
        if (\Auth::user()->can('view sale')) {
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


            return view('statements.changes_in_equity_summary', compact('filter'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
