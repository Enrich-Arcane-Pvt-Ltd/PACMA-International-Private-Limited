<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\BankAccount;
use App\Bill;
use App\BillProduct;
use App\Branch;
use App\Center;
use App\ChartOfAccount;
use App\Group;
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
use App\SaleItem;
use App\Installment;
use App\Loan;
use App\LoanInstallment;
use App\PawningReceipt;
use App\ProductCategory;
use App\StockTransfer;
use App\Grn;
use App\Pawning;
use App\Inventory;
use App\Product;
use App\StockTransaction;
use App\Setting;
use App\User;
use App\PettyCash;
use App\PawnedItem;
use App\CustomReceipt;
use App\ItemReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function incomeSummary(Request $request)
    {

        if (\Auth::user()->can('income report')) {
            $account = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('holder_name', 'id');
            $account->prepend('All', '');
            $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customer->prepend('All', '');
            $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 1)->get()->pluck('name', 'id');
            $category->prepend('All', '');

            $data['monthList']  = $month = $this->yearMonth();
            $data['yearList']   = $this->yearList();
            $filter['category'] = __('All');
            $filter['customer'] = __('All');


            if (isset($request->year)) {
                $year = $request->year;
            } else {
                $year = date('Y');
            }
            $data['currentYear'] = $year;

            // ------------------------------REVENUE INCOME-----------------------------------
            $incomes = Revenue::selectRaw('sum(revenues.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id')->leftjoin('product_service_categories', 'revenues.category_id', '=', 'product_service_categories.id')->where('product_service_categories.type', '=', 1);
            $incomes->where('revenues.created_by', '=', \Auth::user()->creatorId());
            $incomes->whereRAW('YEAR(date) =?', [$year]);

            if (!empty($request->category)) {
                $incomes->where('category_id', '=', $request->category);
                $cat                = ProductServiceCategory::find($request->category);
                $filter['category'] = !empty($cat) ? $cat->name : '';
            }

            if (!empty($request->customer)) {
                $incomes->where('customer_id', '=', $request->customer);
                $cust               = Customer::find($request->customer);
                $filter['customer'] = !empty($cust) ? $cust->name : '';
            }
            $incomes->groupBy('month', 'year', 'category_id');
            $incomes = $incomes->get();

            $tmpArray = [];
            foreach ($incomes as $income) {
                $tmpArray[$income->category_id][$income->month] = $income->amount;
            }
            $array = [];
            foreach ($tmpArray as $cat_id => $record) {
                $tmp             = [];
                $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $tmp['data']     = [];
                for ($i = 1; $i <= 12; $i++) {
                    $tmp['data'][$i] = array_key_exists($i, $record) ? $record[$i] : 0;
                }
                $array[] = $tmp;
            }


            $incomesData = Revenue::selectRaw('sum(revenues.amount) as amount,MONTH(date) as month,YEAR(date) as year');
            $incomesData->where('revenues.created_by', '=', \Auth::user()->creatorId());
            $incomesData->whereRAW('YEAR(date) =?', [$year]);

            if (!empty($request->category)) {
                $incomesData->where('category_id', '=', $request->category);
            }
            if (!empty($request->customer)) {
                $incomesData->where('customer_id', '=', $request->customer);
            }
            $incomesData->groupBy('month', 'year');
            $incomesData = $incomesData->get();
            $incomeArr   = [];
            foreach ($incomesData as $k => $incomeData) {
                $incomeArr[$incomeData->month] = $incomeData->amount;
            }
            for ($i = 1; $i <= 12; $i++) {
                $incomeTotal[] = array_key_exists($i, $incomeArr) ? $incomeArr[$i] : 0;
            }

            //---------------------------INVOICE INCOME-----------------------------------------------

            $invoices = Invoice::selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,invoice_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);

            $invoices->whereRAW('YEAR(send_date) =?', [$year]);

            if (!empty($request->customer)) {
                $invoices->where('customer_id', '=', $request->customer);
            }

            if (!empty($request->category)) {
                $invoices->where('category_id', '=', $request->category);
            }

            $invoices        = $invoices->get();
            $invoiceTmpArray = [];
            foreach ($invoices as $invoice) {
                $invoiceTmpArray[$invoice->category_id][$invoice->month][] = $invoice->getTotal();
            }

            $invoiceArray = [];
            foreach ($invoiceTmpArray as $cat_id => $record) {

                $invoice             = [];
                $invoice['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $invoice['data']     = [];
                for ($i = 1; $i <= 12; $i++) {

                    $invoice['data'][$i] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
                }
                $invoiceArray[] = $invoice;
            }

            $invoiceTotalArray = [];
            foreach ($invoices as $invoice) {
                $invoiceTotalArray[$invoice->month][] = $invoice->getTotal();
            }
            for ($i = 1; $i <= 12; $i++) {
                $invoiceTotal[] = array_key_exists($i, $invoiceTotalArray) ? array_sum($invoiceTotalArray[$i]) : 0;
            }

            $chartIncomeArr = array_map(
                function () {
                    return array_sum(func_get_args());
                },
                $incomeTotal,
                $invoiceTotal
            );

            $data['chartIncomeArr'] = $chartIncomeArr;
            $data['incomeArr']      = $array;
            $data['invoiceArray']   = $invoiceArray;
            $data['account']        = $account;
            $data['customer']       = $customer;
            $data['category']       = $category;

            $filter['startDateRange'] = 'Jan-' . $year;
            $filter['endDateRange']   = 'Dec-' . $year;


            return view('report.income_summary', compact('filter'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function expenseSummary(Request $request)
    {
        if (\Auth::user()->can('expense report')) {
            $account = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('holder_name', 'id');
            $account->prepend('All', '');
            $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $vender->prepend('All', '');
            $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 2)->get()->pluck('name', 'id');
            $category->prepend('All', '');

            $data['monthList']  = $month = $this->yearMonth();
            $data['yearList']   = $this->yearList();
            $filter['category'] = __('All');
            $filter['vender']   = __('All');

            if (isset($request->year)) {
                $year = $request->year;
            } else {
                $year = date('Y');
            }
            $data['currentYear'] = $year;

            //   -----------------------------------------PAYMENT EXPENSE ------------------------------------------------------------
            $expenses = Payment::selectRaw('sum(payments.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id')->leftjoin('product_service_categories', 'payments.category_id', '=', 'product_service_categories.id')->where('product_service_categories.type', '=', 2);
            $expenses->where('payments.created_by', '=', \Auth::user()->creatorId());
            $expenses->whereRAW('YEAR(date) =?', [$year]);

            if (!empty($request->category)) {
                $expenses->where('category_id', '=', $request->category);
                $cat                = ProductServiceCategory::find($request->category);
                $filter['category'] = !empty($cat) ? $cat->name : '';
            }
            if (!empty($request->vender)) {
                $expenses->where('vender_id', '=', $request->vender);

                $vend             = Vender::find($request->vender);
                $filter['vender'] = !empty($vend) ? $vend->name : '';
            }

            $expenses->groupBy('month', 'year', 'category_id');
            $expenses = $expenses->get();
            $tmpArray = [];
            foreach ($expenses as $expense) {
                $tmpArray[$expense->category_id][$expense->month] = $expense->amount;
            }
            $array = [];
            foreach ($tmpArray as $cat_id => $record) {
                $tmp             = [];
                $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $tmp['data']     = [];
                for ($i = 1; $i <= 12; $i++) {
                    $tmp['data'][$i] = array_key_exists($i, $record) ? $record[$i] : 0;
                }
                $array[] = $tmp;
            }
            $expensesData = Payment::selectRaw('sum(payments.amount) as amount,MONTH(date) as month,YEAR(date) as year');
            $expensesData->where('payments.created_by', '=', \Auth::user()->creatorId());
            $expensesData->whereRAW('YEAR(date) =?', [$year]);

            if (!empty($request->category)) {
                $expensesData->where('category_id', '=', $request->category);
            }
            if (!empty($request->vender)) {
                $expensesData->where('vender_id', '=', $request->vender);
            }
            $expensesData->groupBy('month', 'year');
            $expensesData = $expensesData->get();

            $expenseArr = [];
            foreach ($expensesData as $k => $expenseData) {
                $expenseArr[$expenseData->month] = $expenseData->amount;
            }
            for ($i = 1; $i <= 12; $i++) {
                $expenseTotal[] = array_key_exists($i, $expenseArr) ? $expenseArr[$i] : 0;
            }

            //     ------------------------------------BILL EXPENSE----------------------------------------------------

            $bills = Bill::selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,bill_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
            $bills->whereRAW('YEAR(send_date) =?', [$year]);

            if (!empty($request->vender)) {
                $bills->where('vender_id', '=', $request->vender);
            }

            if (!empty($request->category)) {
                $bills->where('category_id', '=', $request->category);
            }
            $bills        = $bills->get();
            $billTmpArray = [];
            foreach ($bills as $bill) {
                $billTmpArray[$bill->category_id][$bill->month][] = $bill->getTotal();
            }

            $billArray = [];
            foreach ($billTmpArray as $cat_id => $record) {

                $bill             = [];
                $bill['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $bill['data']     = [];
                for ($i = 1; $i <= 12; $i++) {

                    $bill['data'][$i] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
                }
                $billArray[] = $bill;
            }

            $billTotalArray = [];
            foreach ($bills as $bill) {
                $billTotalArray[$bill->month][] = $bill->getTotal();
            }
            for ($i = 1; $i <= 12; $i++) {
                $billTotal[] = array_key_exists($i, $billTotalArray) ? array_sum($billTotalArray[$i]) : 0;
            }

            $chartExpenseArr = array_map(
                function () {
                    return array_sum(func_get_args());
                },
                $expenseTotal,
                $billTotal
            );


            $data['chartExpenseArr'] = $chartExpenseArr;
            $data['expenseArr']      = $array;
            $data['billArray']       = $billArray;
            $data['account']         = $account;
            $data['vender']          = $vender;
            $data['category']        = $category;

            $filter['startDateRange'] = 'Jan-' . $year;
            $filter['endDateRange']   = 'Dec-' . $year;

            return view('report.expense_summary', compact('filter'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function incomeVsExpenseSummary(Request $request)
    {
        if (\Auth::user()->can('income vs expense report')) {
            $account = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('holder_name', 'id');
            $account->prepend('All', '');
            $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $vender->prepend('All', '');
            $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customer->prepend('All', '');

            $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->whereIn(
                'type',
                [
                    1,
                    2,
                ]
            )->get()->pluck('name', 'id');
            $category->prepend('All', '');

            $data['monthList'] = $month = $this->yearMonth();
            $data['yearList']  = $this->yearList();

            $filter['category'] = __('All');
            $filter['customer'] = __('All');
            $filter['vender']   = __('All');

            if (isset($request->year)) {
                $year = $request->year;
            } else {
                $year = date('Y');
            }
            $data['currentYear'] = $year;

            // ------------------------------TOTAL PAYMENT EXPENSE-----------------------------------------------------------
            $expensesData = Payment::selectRaw('sum(payments.amount) as amount,MONTH(date) as month,YEAR(date) as year');
            $expensesData->where('payments.created_by', '=', \Auth::user()->creatorId());
            $expensesData->whereRAW('YEAR(date) =?', [$year]);

            if (!empty($request->category)) {
                $expensesData->where('category_id', '=', $request->category);
                $cat                = ProductServiceCategory::find($request->category);
                $filter['category'] = !empty($cat) ? $cat->name : '';
            }
            if (!empty($request->vender)) {
                $expensesData->where('vender_id', '=', $request->vender);

                $vend             = Vender::find($request->vender);
                $filter['vender'] = !empty($vend) ? $vend->name : '';
            }
            $expensesData->groupBy('month', 'year');
            $expensesData = $expensesData->get();

            $expenseArr = [];
            foreach ($expensesData as $k => $expenseData) {
                $expenseArr[$expenseData->month] = $expenseData->amount;
            }

            // ------------------------------TOTAL BILL EXPENSE-----------------------------------------------------------

            $bills = Bill::selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,bill_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
            $bills->whereRAW('YEAR(send_date) =?', [$year]);

            if (!empty($request->vender)) {
                $bills->where('vender_id', '=', $request->vender);
            }

            if (!empty($request->category)) {
                $bills->where('category_id', '=', $request->category);
            }

            $bills        = $bills->get();
            $billTmpArray = [];
            foreach ($bills as $bill) {
                $billTmpArray[$bill->category_id][$bill->month][] = $bill->getTotal();
            }
            $billArray = [];
            foreach ($billTmpArray as $cat_id => $record) {
                $bill             = [];
                $bill['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $bill['data']     = [];
                for ($i = 1; $i <= 12; $i++) {

                    $bill['data'][$i] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
                }
                $billArray[] = $bill;
            }

            $billTotalArray = [];
            foreach ($bills as $bill) {
                $billTotalArray[$bill->month][] = $bill->getTotal();
            }


            // ------------------------------TOTAL REVENUE INCOME-----------------------------------------------------------

            $incomesData = Revenue::selectRaw('sum(revenues.amount) as amount,MONTH(date) as month,YEAR(date) as year');
            $incomesData->where('revenues.created_by', '=', \Auth::user()->creatorId());
            $incomesData->whereRAW('YEAR(date) =?', [$year]);

            if (!empty($request->category)) {
                $incomesData->where('category_id', '=', $request->category);
            }
            if (!empty($request->customer)) {
                $incomesData->where('customer_id', '=', $request->customer);
                $cust               = Customer::find($request->customer);
                $filter['customer'] = !empty($cust) ? $cust->name : '';
            }
            $incomesData->groupBy('month', 'year');
            $incomesData = $incomesData->get();
            $incomeArr   = [];
            foreach ($incomesData as $k => $incomeData) {
                $incomeArr[$incomeData->month] = $incomeData->amount;
            }

            // ------------------------------TOTAL INVOICE INCOME-----------------------------------------------------------
            $invoices = Invoice::selectRaw('MONTH(send_date) as month,YEAR(send_date) as year,category_id,invoice_id,id')->where('created_by', \Auth::user()->creatorId())->where('status', '!=', 0);
            $invoices->whereRAW('YEAR(send_date) =?', [$year]);
            if (!empty($request->customer)) {
                $invoices->where('customer_id', '=', $request->customer);
            }
            if (!empty($request->category)) {
                $invoices->where('category_id', '=', $request->category);
            }
            $invoices        = $invoices->get();
            $invoiceTmpArray = [];
            foreach ($invoices as $invoice) {
                $invoiceTmpArray[$invoice->category_id][$invoice->month][] = $invoice->getTotal();
            }

            $invoiceArray = [];
            foreach ($invoiceTmpArray as $cat_id => $record) {

                $invoice             = [];
                $invoice['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $invoice['data']     = [];
                for ($i = 1; $i <= 12; $i++) {

                    $invoice['data'][$i] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
                }
                $invoiceArray[] = $invoice;
            }

            $invoiceTotalArray = [];
            foreach ($invoices as $invoice) {
                $invoiceTotalArray[$invoice->month][] = $invoice->getTotal();
            }
            //        ----------------------------------------------------------------------------------------------------

            for ($i = 1; $i <= 12; $i++) {
                $paymentExpenseTotal[] = array_key_exists($i, $expenseArr) ? $expenseArr[$i] : 0;
                $billExpenseTotal[]    = array_key_exists($i, $billTotalArray) ? array_sum($billTotalArray[$i]) : 0;

                $RevenueIncomeTotal[] = array_key_exists($i, $incomeArr) ? $incomeArr[$i] : 0;
                $invoiceIncomeTotal[] = array_key_exists($i, $invoiceTotalArray) ? array_sum($invoiceTotalArray[$i]) : 0;
            }

            $totalIncome = array_map(
                function () {
                    return array_sum(func_get_args());
                },
                $RevenueIncomeTotal,
                $invoiceIncomeTotal
            );

            $totalExpense = array_map(
                function () {
                    return array_sum(func_get_args());
                },
                $paymentExpenseTotal,
                $billExpenseTotal
            );

            $profit = [];
            $keys   = array_keys($totalIncome + $totalExpense);
            foreach ($keys as $v) {
                $profit[$v] = (empty($totalIncome[$v]) ? 0 : $totalIncome[$v]) - (empty($totalExpense[$v]) ? 0 : $totalExpense[$v]);
            }


            $data['paymentExpenseTotal'] = $paymentExpenseTotal;
            $data['billExpenseTotal']    = $billExpenseTotal;
            $data['revenueIncomeTotal']  = $RevenueIncomeTotal;
            $data['invoiceIncomeTotal']  = $invoiceIncomeTotal;
            $data['profit']              = $profit;
            $data['account']             = $account;
            $data['vender']              = $vender;
            $data['customer']            = $customer;
            $data['category']            = $category;

            $filter['startDateRange'] = 'Jan-' . $year;
            $filter['endDateRange']   = 'Dec-' . $year;

            return view('report.income_vs_expense_summary', compact('filter'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function taxSummary(Request $request)
    {

        if (\Auth::user()->can('tax report')) {
            $data['monthList'] = $month = $this->yearMonth();
            $data['yearList']  = $this->yearList();
            $data['taxList']   = $taxList = Tax::where('created_by', \Auth::user()->creatorId())->get();

            if (isset($request->year)) {
                $year = $request->year;
            } else {
                $year = date('Y');
            }

            $data['currentYear'] = $year;

            $invoiceProducts = InvoiceProduct::selectRaw('invoice_products.* ,MONTH(invoice_products.created_at) as month,YEAR(invoice_products.created_at) as year')->leftjoin('product_services', 'invoice_products.product_id', '=', 'product_services.id')->whereRaw('YEAR(invoice_products.created_at) =?', [$year])->where('product_services.created_by', '=', \Auth::user()->creatorId())->get();

            $incomeTaxesData = [];
            foreach ($invoiceProducts as $invoiceProduct) {
                $incomeTax   = [];
                $incomeTaxes = \Utility::tax($invoiceProduct->tax);
                foreach ($incomeTaxes as $taxe) {
                    $taxDataPrice           = \Utility::taxRate($taxe->rate, $invoiceProduct->price, $invoiceProduct->quantity);
                    $incomeTax[$taxe->name] = $taxDataPrice;
                }
                $incomeTaxesData[$invoiceProduct->month][] = $incomeTax;
            }

            $income = [];
            foreach ($incomeTaxesData as $month => $incomeTaxx) {
                $incomeTaxRecord = [];
                foreach ($incomeTaxx as $k => $record) {
                    foreach ($record as $incomeTaxName => $incomeTaxAmount) {
                        if (array_key_exists($incomeTaxName, $incomeTaxRecord)) {
                            $incomeTaxRecord[$incomeTaxName] += $incomeTaxAmount;
                        } else {
                            $incomeTaxRecord[$incomeTaxName] = $incomeTaxAmount;
                        }
                    }
                    $income['data'][$month] = $incomeTaxRecord;
                }
            }

            foreach ($income as $incomeMonth => $incomeTaxData) {
                $incomeData = [];
                for ($i = 1; $i <= 12; $i++) {
                    $incomeData[$i] = array_key_exists($i, $incomeTaxData) ? $incomeTaxData[$i] : 0;
                }
            }

            $incomes = [];
            if (isset($incomeData) && !empty($incomeData)) {
                foreach ($taxList as $taxArr) {
                    foreach ($incomeData as $month => $tax) {
                        if ($tax != 0) {
                            if (isset($tax[$taxArr->name])) {
                                $incomes[$taxArr->name][$month] = $tax[$taxArr->name];
                            } else {
                                $incomes[$taxArr->name][$month] = 0;
                            }
                        } else {
                            $incomes[$taxArr->name][$month] = 0;
                        }
                    }
                }
            }


            $billProducts = BillProduct::selectRaw('bill_products.* ,MONTH(bill_products.created_at) as month,YEAR(bill_products.created_at) as year')->leftjoin('product_services', 'bill_products.product_id', '=', 'product_services.id')->whereRaw('YEAR(bill_products.created_at) =?', [$year])->where('product_services.created_by', '=', \Auth::user()->creatorId())->get();

            $expenseTaxesData = [];
            foreach ($billProducts as $billProduct) {
                $billTax   = [];
                $billTaxes = \Utility::tax($billProduct->tax);
                foreach ($billTaxes as $taxe) {
                    $taxDataPrice         = \Utility::taxRate($taxe->rate, $billProduct->price, $billProduct->quantity);
                    $billTax[$taxe->name] = $taxDataPrice;
                }
                $expenseTaxesData[$billProduct->month][] = $billTax;
            }

            $bill = [];
            foreach ($expenseTaxesData as $month => $billTaxx) {
                $billTaxRecord = [];
                foreach ($billTaxx as $k => $record) {
                    foreach ($record as $billTaxName => $billTaxAmount) {
                        if (array_key_exists($billTaxName, $billTaxRecord)) {
                            $billTaxRecord[$billTaxName] += $billTaxAmount;
                        } else {
                            $billTaxRecord[$billTaxName] = $billTaxAmount;
                        }
                    }
                    $bill['data'][$month] = $billTaxRecord;
                }
            }

            foreach ($bill as $billMonth => $billTaxData) {
                $billData = [];
                for ($i = 1; $i <= 12; $i++) {
                    $billData[$i] = array_key_exists($i, $billTaxData) ? $billTaxData[$i] : 0;
                }
            }
            $expenses = [];
            if (isset($billData) && !empty($billData)) {

                foreach ($taxList as $taxArr) {
                    foreach ($billData as $month => $tax) {
                        if ($tax != 0) {
                            if (isset($tax[$taxArr->name])) {
                                $expenses[$taxArr->name][$month] = $tax[$taxArr->name];
                            } else {
                                $expenses[$taxArr->name][$month] = 0;
                            }
                        } else {
                            $expenses[$taxArr->name][$month] = 0;
                        }
                    }
                }
            }

            $data['expenses'] = $expenses;
            $data['incomes']  = $incomes;

            $filter['startDateRange'] = 'Jan-' . $year;
            $filter['endDateRange']   = 'Dec-' . $year;

            return view('report.tax_summary', compact('filter'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profitLossSummary(Request $request)
    {

        if (\Auth::user()->can('loss & profit report')) {
            $data['month']     = [
                'Jan-Mar',
                'Apr-Jun',
                'Jul-Sep',
                'Oct-Dec',
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

            // -------------------------------REVENUE INCOME-------------------------------------------------

            $incomes = Revenue::selectRaw('sum(revenues.amount) as amount,MONTH(date) as month,YEAR(date) as year,category_id');
            $incomes->where('created_by', '=', \Auth::user()->creatorId());
            $incomes->whereRAW('YEAR(date) =?', [$year]);
            $incomes->groupBy('month', 'year', 'category_id');
            $incomes        = $incomes->get();
            $tmpIncomeArray = [];
            foreach ($incomes as $income) {
                $tmpIncomeArray[$income->category_id][$income->month] = $income->amount;
            }

            $incomeCatAmount_1  = $incomeCatAmount_2 = $incomeCatAmount_3 = $incomeCatAmount_4 = 0;
            $revenueIncomeArray = array();
            foreach ($tmpIncomeArray as $cat_id => $record) {

                $tmp             = [];
                $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $sumData         = [];
                for ($i = 1; $i <= 12; $i++) {
                    $sumData[] = array_key_exists($i, $record) ? $record[$i] : 0;
                }

                $month_1 = array_slice($sumData, 0, 3);
                $month_2 = array_slice($sumData, 3, 3);
                $month_3 = array_slice($sumData, 6, 3);
                $month_4 = array_slice($sumData, 9, 3);


                $incomeData[__('Jan-Mar')] = $sum_1 = array_sum($month_1);
                $incomeData[__('Apr-Jun')] = $sum_2 = array_sum($month_2);
                $incomeData[__('Jul-Sep')] = $sum_3 = array_sum($month_3);
                $incomeData[__('Oct-Dec')] = $sum_4 = array_sum($month_4);
                $incomeData[__('Total')]   = array_sum(
                    array(
                        $sum_1,
                        $sum_2,
                        $sum_3,
                        $sum_4,
                    )
                );

                $incomeCatAmount_1 += $sum_1;
                $incomeCatAmount_2 += $sum_2;
                $incomeCatAmount_3 += $sum_3;
                $incomeCatAmount_4 += $sum_4;

                $data['month'] = array_keys($incomeData);
                $tmp['amount'] = array_values($incomeData);

                $revenueIncomeArray[] = $tmp;
            }

            $data['incomeCatAmount'] = $incomeCatAmount = [
                $incomeCatAmount_1,
                $incomeCatAmount_2,
                $incomeCatAmount_3,
                $incomeCatAmount_4,
                array_sum(
                    array(
                        $incomeCatAmount_1,
                        $incomeCatAmount_2,
                        $incomeCatAmount_3,
                        $incomeCatAmount_4,
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

            $invoiceCatAmount_1 = $invoiceCatAmount_2 = $invoiceCatAmount_3 = $invoiceCatAmount_4 = 0;
            $invoiceIncomeArray = array();
            foreach ($invoiceTmpArray as $cat_id => $record) {

                $invoiceTmp             = [];
                $invoiceTmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $invoiceSumData         = [];
                for ($i = 1; $i <= 12; $i++) {
                    $invoiceSumData[] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
                }

                $month_1                          = array_slice($invoiceSumData, 0, 3);
                $month_2                          = array_slice($invoiceSumData, 3, 3);
                $month_3                          = array_slice($invoiceSumData, 6, 3);
                $month_4                          = array_slice($invoiceSumData, 9, 3);
                $invoiceIncomeData[__('Jan-Mar')] = $sum_1 = array_sum($month_1);
                $invoiceIncomeData[__('Apr-Jun')] = $sum_2 = array_sum($month_2);
                $invoiceIncomeData[__('Jul-Sep')] = $sum_3 = array_sum($month_3);
                $invoiceIncomeData[__('Oct-Dec')] = $sum_4 = array_sum($month_4);
                $invoiceIncomeData[__('Total')]   = array_sum(
                    array(
                        $sum_1,
                        $sum_2,
                        $sum_3,
                        $sum_4,
                    )
                );
                $invoiceCatAmount_1               += $sum_1;
                $invoiceCatAmount_2               += $sum_2;
                $invoiceCatAmount_3               += $sum_3;
                $invoiceCatAmount_4               += $sum_4;

                $invoiceTmp['amount'] = array_values($invoiceIncomeData);

                $invoiceIncomeArray[] = $invoiceTmp;
            }

            $data['invoiceIncomeCatAmount'] = $invoiceIncomeCatAmount = [
                $invoiceCatAmount_1,
                $invoiceCatAmount_2,
                $invoiceCatAmount_3,
                $invoiceCatAmount_4,
                array_sum(
                    array(
                        $invoiceCatAmount_1,
                        $invoiceCatAmount_2,
                        $invoiceCatAmount_3,
                        $invoiceCatAmount_4,
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
            $expenseCatAmount_1 = $expenseCatAmount_2 = $expenseCatAmount_3 = $expenseCatAmount_4 = 0;
            foreach ($tmpExpenseArray as $cat_id => $record) {
                $tmp             = [];
                $tmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $expenseSumData  = [];
                for ($i = 1; $i <= 12; $i++) {
                    $expenseSumData[] = array_key_exists($i, $record) ? $record[$i] : 0;
                }

                $month_1 = array_slice($expenseSumData, 0, 3);
                $month_2 = array_slice($expenseSumData, 3, 3);
                $month_3 = array_slice($expenseSumData, 6, 3);
                $month_4 = array_slice($expenseSumData, 9, 3);

                $expenseData[__('Jan-Mar')] = $sum_1 = array_sum($month_1);
                $expenseData[__('Apr-Jun')] = $sum_2 = array_sum($month_2);
                $expenseData[__('Jul-Sep')] = $sum_3 = array_sum($month_3);
                $expenseData[__('Oct-Dec')] = $sum_4 = array_sum($month_4);
                $expenseData[__('Total')]   = array_sum(
                    array(
                        $sum_1,
                        $sum_2,
                        $sum_3,
                        $sum_4,
                    )
                );

                $expenseCatAmount_1 += $sum_1;
                $expenseCatAmount_2 += $sum_2;
                $expenseCatAmount_3 += $sum_3;
                $expenseCatAmount_4 += $sum_4;

                $data['month'] = array_keys($expenseData);
                $tmp['amount'] = array_values($expenseData);

                $expenseArray[] = $tmp;
            }

            $data['expenseCatAmount'] = $expenseCatAmount = [
                $expenseCatAmount_1,
                $expenseCatAmount_2,
                $expenseCatAmount_3,
                $expenseCatAmount_4,
                array_sum(
                    array(
                        $expenseCatAmount_1,
                        $expenseCatAmount_2,
                        $expenseCatAmount_3,
                        $expenseCatAmount_4,
                    )
                ),
            ];
            $data['expenseArray']     = $expenseArray;

            //    ----------------------------EXPENSE BILL-----------------------------------------------------------------------

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
            $billExpenseCatAmount_1 = $billExpenseCatAmount_2 = $billExpenseCatAmount_3 = $billExpenseCatAmount_4 = 0;
            foreach ($billTmpArray as $cat_id => $record) {
                $billTmp             = [];
                $billTmp['category'] = !empty(ProductServiceCategory::where('id', '=', $cat_id)->first()) ? ProductServiceCategory::where('id', '=', $cat_id)->first()->name : '';
                $billExpensSumData   = [];
                for ($i = 1; $i <= 12; $i++) {
                    $billExpensSumData[] = array_key_exists($i, $record) ? array_sum($record[$i]) : 0;
                }

                $month_1 = array_slice($billExpensSumData, 0, 3);
                $month_2 = array_slice($billExpensSumData, 3, 3);
                $month_3 = array_slice($billExpensSumData, 6, 3);
                $month_4 = array_slice($billExpensSumData, 9, 3);

                $billExpenseData[__('Jan-Mar')] = $sum_1 = array_sum($month_1);
                $billExpenseData[__('Apr-Jun')] = $sum_2 = array_sum($month_2);
                $billExpenseData[__('Jul-Sep')] = $sum_3 = array_sum($month_3);
                $billExpenseData[__('Oct-Dec')] = $sum_4 = array_sum($month_4);
                $billExpenseData[__('Total')]   = array_sum(
                    array(
                        $sum_1,
                        $sum_2,
                        $sum_3,
                        $sum_4,
                    )
                );

                $billExpenseCatAmount_1 += $sum_1;
                $billExpenseCatAmount_2 += $sum_2;
                $billExpenseCatAmount_3 += $sum_3;
                $billExpenseCatAmount_4 += $sum_4;

                $data['month']     = array_keys($billExpenseData);
                $billTmp['amount'] = array_values($billExpenseData);

                $billExpenseArray[] = $billTmp;
            }

            $data['billExpenseCatAmount'] = $billExpenseCatAmount = [
                $billExpenseCatAmount_1,
                $billExpenseCatAmount_2,
                $billExpenseCatAmount_3,
                $billExpenseCatAmount_4,
                array_sum(
                    array(
                        $billExpenseCatAmount_1,
                        $billExpenseCatAmount_2,
                        $billExpenseCatAmount_3,
                        $billExpenseCatAmount_4,
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

            $filter['startDateRange'] = 'Jan-' . $year;
            $filter['endDateRange']   = 'Dec-' . $year;

            return view('report.profit_loss_summary', compact('filter'), $data);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function yearMonth()
    {

        $month[] = __('January');
        $month[] = __('February');
        $month[] = __('March');
        $month[] = __('April');
        $month[] = __('May');
        $month[] = __('June');
        $month[] = __('July');
        $month[] = __('August');
        $month[] = __('September');
        $month[] = __('October');
        $month[] = __('November');
        $month[] = __('December');

        return $month;
    }

    public function yearList()
    {
        $starting_year = date('Y', strtotime('-5 year'));
        $ending_year   = date('Y');

        foreach (range($ending_year, $starting_year) as $year) {
            $years[$year] = $year;
        }

        return $years;
    }

    public function invoiceSummary(Request $request)
    {

        if (\Auth::user()->can('invoice report')) {
            $filter['customer'] = __('All');
            $filter['status']   = __('All');


            $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'customer_id');
            $customer->prepend('All', '');
            $status = Invoice::$statues;

            $invoices = Invoice::selectRaw('invoices.*,MONTH(send_date) as month,YEAR(send_date) as year');

            if ($request->status != '') {
                $invoices->where('status', $request->status);

                $filter['status'] = Invoice::$statues[$request->status];
            } else {
                $invoices->where('status', '!=', 0);
            }

            $invoices->where('created_by', '=', \Auth::user()->creatorId());

            if (!empty($request->start_month) && !empty($request->end_month)) {
                $start = strtotime($request->start_month);
                $end   = strtotime($request->end_month);
            } else {
                $start = strtotime(date('Y-01'));
                $end   = strtotime(date('Y-12'));
            }

            $invoices->where('send_date', '>=', date('Y-m-01', $start))->where('send_date', '<=', date('Y-m-t', $end));


            $filter['startDateRange'] = date('M-Y', $start);
            $filter['endDateRange']   = date('M-Y', $end);


            if (!empty($request->customer)) {
                $invoices->where('customer_id', $request->customer);
                $cust = Customer::find($request->customer);

                $filter['customer'] = !empty($cust) ? $cust->name : '';
            }


            $invoices = $invoices->get();


            $totalInvoice      = 0;
            $totalDueInvoice   = 0;
            $invoiceTotalArray = [];
            foreach ($invoices as $invoice) {
                $totalInvoice    += $invoice->getTotal();
                $totalDueInvoice += $invoice->getDue();

                $invoiceTotalArray[$invoice->month][] = $invoice->getTotal();
            }
            $totalPaidInvoice = $totalInvoice - $totalDueInvoice;

            for ($i = 1; $i <= 12; $i++) {
                $invoiceTotal[] = array_key_exists($i, $invoiceTotalArray) ? array_sum($invoiceTotalArray[$i]) : 0;
            }

            $monthList = $month = $this->yearMonth();

            return view('report.invoice_report', compact('invoices', 'customer', 'status', 'totalInvoice', 'totalDueInvoice', 'totalPaidInvoice', 'invoiceTotal', 'monthList', 'filter'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function billSummary(Request $request)
    {
        if (\Auth::user()->can('bill report')) {

            $filter['vender'] = __('All');
            $filter['status'] = __('All');


            $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'vender_id');
            $vender->prepend('All', '');
            $status = Bill::$statues;

            $bills = Bill::selectRaw('bills.*,MONTH(send_date) as month,YEAR(send_date) as year');

            if (!empty($request->start_month) && !empty($request->end_month)) {
                $start = strtotime($request->start_month);
                $end   = strtotime($request->end_month);
            } else {
                $start = strtotime(date('Y-01'));
                $end   = strtotime(date('Y-12'));
            }

            $bills->where('send_date', '>=', date('Y-m-01', $start))->where('send_date', '<=', date('Y-m-t', $end));

            $filter['startDateRange'] = date('M-Y', $start);
            $filter['endDateRange']   = date('M-Y', $end);


            if (!empty($request->vender)) {
                $bills->where('vender_id', $request->vender);
                $vend = Vender::find($request->vender);

                $filter['vender'] = !empty($vend) ? $vend->name : '';
            }

            if ($request->status != '') {
                $bills->where('status', '=', $request->status);

                $filter['status'] = Bill::$statues[$request->status];
            } else {
                $bills->where('status', '!=', 0);
            }

            $bills->where('created_by', '=', \Auth::user()->creatorId());
            $bills = $bills->get();


            $totalBill      = 0;
            $totalDueBill   = 0;
            $billTotalArray = [];
            foreach ($bills as $bill) {
                $totalBill    += $bill->getTotal();
                $totalDueBill += $bill->getDue();

                $billTotalArray[$bill->month][] = $bill->getTotal();
            }
            $totalPaidBill = $totalBill - $totalDueBill;

            for ($i = 1; $i <= 12; $i++) {
                $billTotal[] = array_key_exists($i, $billTotalArray) ? array_sum($billTotalArray[$i]) : 0;
            }

            $monthList = $month = $this->yearMonth();

            return view('report.bill_report', compact('bills', 'vender', 'status', 'totalBill', 'totalDueBill', 'totalPaidBill', 'billTotal', 'monthList', 'filter'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function accountStatement(Request $request)
    {

        if (\Auth::user()->can('statement report')) {

            $filter['account']             = __('All');
            $filter['type']                = __('Revenue');
            $reportData['revenues']        = '';
            $reportData['payments']        = '';
            $reportData['revenueAccounts'] = '';
            $reportData['paymentAccounts'] = '';

            $account = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('holder_name', 'id');
            $account->prepend('All', '');

            $types = [
                'revenue' => __('Revenue'),
                'payment' => __('Payment'),
            ];

            if ($request->type == 'revenue' || !isset($request->type)) {

                $revenueAccounts = Revenue::select('bank_accounts.id', 'bank_accounts.holder_name', 'bank_accounts.bank_name')->leftjoin('bank_accounts', 'revenues.account_id', '=', 'bank_accounts.id')->groupBy('revenues.account_id')->selectRaw('sum(amount) as total');

                $revenues = Revenue::orderBy('id', 'desc');
            }

            if ($request->type == 'payment') {
                $paymentAccounts = Payment::select('bank_accounts.id', 'bank_accounts.holder_name', 'bank_accounts.bank_name')->leftjoin('bank_accounts', 'payments.account_id', '=', 'bank_accounts.id')->groupBy('payments.account_id')->selectRaw('sum(amount) as total');

                $payments = Payment::orderBy('id', 'desc');
            }


            if (!empty($request->start_month) && !empty($request->end_month)) {
                $start = strtotime($request->start_month);
                $end   = strtotime($request->end_month);
            } else {
                $start = strtotime(date('Y-m'));
                $end   = strtotime(date('Y-m', strtotime("-5 month")));
            }


            $currentdate = $start;
            while ($currentdate <= $end) {
                $data['month'] = date('m', $currentdate);
                $data['year']  = date('Y', $currentdate);

                if ($request->type == 'revenue' || !isset($request->type)) {
                    $revenues->Orwhere(
                        function ($query) use ($data) {
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );

                    $revenueAccounts->Orwhere(
                        function ($query) use ($data) {
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );
                }

                if ($request->type == 'payment') {
                    $paymentAccounts->Orwhere(
                        function ($query) use ($data) {
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );
                }


                $currentdate = strtotime('+1 month', $currentdate);
            }

            if (!empty($request->account)) {
                if ($request->type == 'revenue' || !isset($request->type)) {
                    $revenues->where('account_id', $request->account);
                    $revenues->where('created_by', '=', \Auth::user()->creatorId());
                    $revenueAccounts->where('account_id', $request->account);
                }

                if ($request->type == 'payment') {
                    $payments->where('account_id', $request->account);
                    $payments->where('created_by', '=', \Auth::user()->creatorId());

                    $paymentAccounts->where('account_id', $request->account);
                }


                $bankAccount       = BankAccount::find($request->account);
                $filter['account'] = !empty($bankAccount) ? $bankAccount->holder_name . ' - ' . $bankAccount->bank_name : '';
                if ($bankAccount->holder_name == 'Cash') {
                    $filter['account'] = 'Cash';
                }
            }

            if ($request->type == 'revenue' || !isset($request->type)) {
                $reportData['revenues'] = $revenues->get();

                $revenueAccounts->where('revenues.created_by', '=', \Auth::user()->creatorId());
                $reportData['revenueAccounts'] = $revenueAccounts->get();
            }

            if ($request->type == 'payment') {
                $reportData['payments'] = $payments->get();

                $paymentAccounts->where('payments.created_by', '=', \Auth::user()->creatorId());
                $reportData['paymentAccounts'] = $paymentAccounts->get();
                $filter['type']                = __('Payment');
            }


            $filter['startDateRange'] = date('M-Y', $start);
            $filter['endDateRange']   = date('M-Y', $end);


            return view('report.statement_report', compact('reportData', 'account', 'types', 'filter'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function salesReportIndex()
    {
        $showTable = 'No';
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $introducers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $introducers->prepend('-- All --', '');
        $currencySymbol = $this->currencySymbol;
        return view('report.ele_profit', compact('showTable', 'productCategories', 'introducers', 'currencySymbol'));
    }

    public function salesReportSearch(Request $request)
    {
        $introducers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $introducers->prepend('-- All --', '');

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $category_id = $request->category_id;
        $saleType = $request->sale_type;
        $saleIntroducer = '';

        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            $productCategories = ProductCategory::get()->pluck('name', 'id');
            $productCategories->prepend('-- All --', '');

            $query = Sale::with(['saleItems' => function ($innerQuery) {
                $innerQuery->where('status', 1)->with('inventory');
            }])->whereNotIn('status', [0, 4])->whereDate('date_of_sale', '<=', $end_date)->whereDate('date_of_sale', '>=', $start_date)->where('branch_id', \Auth::user()->branchId());
            if ($request->sale_type != '') {
                $query->where('sale_type', $request->sale_type);
            }
            if ($request->introducer_id != "") {
                $saleIntroducer = $request->introducer_id;
                $query->where('introducer_id', $request->introducer_id);
            }
            $saleInfoLists = $query->orderBy('id')->get();

            foreach ($saleInfoLists as $sale) {
                $newInterest = 0;

                if ($sale->installment) {
                    $installments = Installment::where('sale_id', $sale->id)->whereDate('paid_date', '<=', $end_date)->whereDate('paid_date', '>=', $start_date)->get();

                    // $newInterest = 0;
                    // foreach ($installments as $installment) {
                    //     $fineAmount = 0;
                    //     if ($installment->fine) {
                    //         $fineAmount = $installment->fine;
                    //     }

                    //     if ($installment->total_amount_to_pay > $installment->paid_amount) {
                    //         $principal = ($installment->paid_amount / ($sale->outstanding_amount + $sale->interest_amount)) * $sale->outstanding_amount;
                    //         $interest = $installment->paid_amount - $principal;
                    //         $newInterest += $interest;

                    //     } else {
                    //         if ($fineAmount > 0) {
                    //             $principal = (($installment->amount + $installment->remainder_amount) / ($sale->outstanding_amount + $sale->interest_amount)) * $sale->outstanding_amount;
                    //             $interest = ($installment->amount + $installment->remainder_amount) - $principal + $fineAmount;
                    //             $newInterest += $interest;

                    //         } else {
                    //             $principal = ($installment->amount / ($sale->outstanding_amount + $sale->interest_amount)) * $sale->outstanding_amount;
                    //             $interest = $installment->amount - $principal;
                    //             $newInterest += $interest;

                    //             if ($sale->id == 28) {
                    //                 dump($interest);
                    //             }
                    //         }
                    //         // $newInterest = $newInterest + round(($sale->interest_amount / $sale->installment), 2, PHP_ROUND_HALF_UP);
                    //     }
                    // }

                    // $sale->new_interest = round($newInterest, 2, PHP_ROUND_HALF_UP);

                    $productCategory = $sale->products->categoryDetails;

                    foreach ($installments as $installment) {
                        $fineAmount = 0;
                        if ($installment->fine) {
                            $fineAmount = $installment->fine;
                        }

                        $transaction = AccountTransaction::where('installment_id', $installment->id)
                            ->where('chart_of_account_id', 13)
                            ->where('related_chart_of_account_id', $productCategory->interest_income_control_chart_of_account_id)
                            ->where('status', 1)
                            ->first();

                        if (isset($transaction->amount)) {
                            $newInterest += $transaction->amount + $fineAmount;
                        }
                    }
                }

                $sale->new_interest = $newInterest;
                $currencySymbol = $this->currencySymbol;
            }

            return view('report.ele_profit', compact('showTable', 'productCategories', 'saleInfoLists', 'start_date', 'end_date', 'category_id', 'saleType', 'introducers', 'saleIntroducer', 'currencySymbol'));
        }
    }

    public function accessoriesReportIndex()
    {
        $showTable = 'No';
        $branches = Branch::get()->pluck('name', 'id');
        $branches->prepend('Select Branch', '');

        return view('report.accessories_profit', compact('showTable', 'branches'));
    }

    public function accessoriesReportSearch(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $category_id = 3;
        $branch_id = $request->branch_id;
        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            $branches = Branch::get()->pluck('name', 'id');
            $branches->prepend('Select Branch', '');

            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = Sale::with(['saleItems' => function ($innerQuery) {
                $innerQuery->where('status', 1)->with('inventory');
            }])->whereNotIn('status', [0, 4])->whereDate('date_of_sale', '<=', $end_date)->whereDate('date_of_sale', '>=', $start_date)->where('sale_type', 0)
                ->whereHas('products', function ($childQuery) use ($category_id) {
                    $childQuery->where('product_category_id', $category_id);
                })->orWhereHas('saleItems.products', function ($childQuery) use ($category_id) {
                    $childQuery->where('product_category_id', $category_id);
                });
            if ($request->branch_id) {
                $query->where('branch_id', $request->branch_id);
            }
            $saleInfoLists = $query->get();
            // $duetotal= Sale::whereDate('due_date','<=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');

            return view('report.accessories_profit', compact('showTable', 'branches', 'saleInfoLists', 'start_date', 'end_date', 'branch_id'));
        }
    }

    public function duePaymentIndex()
    {
        $customers = Customer::where('branch_id', \Auth::user()->branchId())->get()->pluck('name', 'id');
        $showTable = 'No';
        $currencySymbol = $this->currencySymbol;
        return view('report.due_payment', ['customers' => $customers, 'showTable' => $showTable, 'currencySymbol' => $currencySymbol]);
    }


    public function duePaymentSearch(Request $request)
    {
        if (isset($request->start_date) && isset($request->end_date)) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            // $saleInfoLists= Sale::where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            //                 ->whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->whereNotIn('status', [0,4])->where('branch_id', \Auth::user()->branchId())->get();
            $saleInfoLists = Sale::with(['customer', 'products', 'installments' => function ($innerQuery) use ($start_date, $end_date) {
                $innerQuery->where('installment_date', '>=', $start_date)
                    ->where('installment_date', '<=', $end_date);
            }])
                ->whereHas('installments', function ($innerQuery) use ($start_date, $end_date) {
                    $innerQuery->where('installment_date', '>=', $start_date)
                        ->where('installment_date', '<=', $end_date);
                })
                ->whereNotIn('status', [0, 4])->where('branch_id', \Auth::user()->branchId())->get();
            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->whereNotIn('status', [0,4])->where('branch_id', \Auth::user()->branchId())->sum('total_due_amount');
            $duetotal = $saleInfoLists->sum('total_due_amount');
            $currencySymbol = $this->currencySymbol;
            return view('report.due_payment', ['showTable' => $showTable, 'saleInfoLists' => $saleInfoLists, 'duetotal' => $duetotal, 'start_date' => $start_date, 'end_date' => $end_date, 'currencySymbol' => $currencySymbol]);
        }
    }

    public function installmentBySale($id)
    {
        $installment = Installment::where('sale_id', $id)->get();
        return json_decode($installment);
    }




    //    Nipun
    public function receiptReport()
    {
        $receipts = Receipt::where('status', 1)->where('branch_id', \Auth::user()->branchId())->get()->pluck('name', 'id');
        $showTable = 'No';
        $category = '';
        $currencySymbol = $this->currencySymbol;
        return view('report.receipt_payment', ['customers' => $receipts, 'showTable' => $showTable, 'category' => $category, 'currencySymbol' => $currencySymbol]);
    }

    public function receiptsearch(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $category = $request->category;
        $hireSubCategory = '';
        $loanSubCategory = '';

        if (isset($request->start_date) && isset($request->end_date)) {
            // $startDate = date("d", strtotime($start_date));
            // $endDate = date("d", strtotime($end_date));
            // die(var_dump($end_date));

            $showTable = 'Yes';
            // $saleInfoLists= Receipt::with(['customerInfo','productInfo'])->where('issue_date','>=', $startDate)->where('issue_date','<=', $endDate)
            //             ->whereDate('created_at','>=',$end_date)->whereDate('created_at','>=',$start_date)->get();
            $queryInvoiceLists = Sale::where('sale_type', 0)
                ->where('date_of_sale', '>=', $start_date)
                ->where('date_of_sale', '<=', $end_date)
                ->whereNotIn('status', [0, 4])
                ->where('branch_id', \Auth::user()->branchId());


            $querySaleInfoLists = Receipt::where('issue_date', '>=', $start_date)
                ->where('issue_date', '<=', $end_date)
                ->where('status', 1)
                ->where('branch_id', \Auth::user()->branchId());

            $queryPawningInfoLists = PawningReceipt::where('issue_date', '>=', $start_date)
                ->where('issue_date', '<=', $end_date)
                ->where('status', 1)
                ->where('branch_id', \Auth::user()->branchId());

            $queryCustomInfoLists = CustomReceipt::where('issue_date', '>=', $start_date)
                ->where('issue_date', '<=', $end_date)
                ->where('status', 1)
                ->where('branch_id', \Auth::user()->branchId());

            if ($request->hire_sub_category != '') {
                $hireSubCategory = $request->hire_sub_category;
                $querySaleInfoLists->where('receipt_code', 'LIKE', $request->hire_sub_category . '%');
            }

            if ($request->loan_sub_category != '') {
                $loanSubCategory = $request->loan_sub_category;
                $querySaleInfoLists->where('receipt_code', 'LIKE', $request->loan_sub_category . '%');
            }

            if ($category == 'advance') {
                $invoiceLists = [];
                $saleInfoLists = $querySaleInfoLists->whereNull('invoice_id')->whereNull('loan_id')->get();
                $pawningInfoLists = [];
                $customInfoLists = [];
            } elseif ($category == 'direct') {
                $invoiceLists = $queryInvoiceLists->get();
                $saleInfoLists = [];
                $pawningInfoLists = [];
                $customInfoLists = [];
            } elseif ($category == 'hire') {
                $invoiceLists = [];
                $saleInfoLists = $querySaleInfoLists->whereNotNull('invoice_id')->get();
                $pawningInfoLists = [];
                $customInfoLists = [];
            } elseif ($category == 'loan') {
                $invoiceLists = [];
                $saleInfoLists = $querySaleInfoLists->whereNotNull('loan_id')->get();
                $pawningInfoLists = [];
                $customInfoLists = [];
            } elseif ($category == 'pawning') {
                $invoiceLists = [];
                $saleInfoLists = [];
                $pawningInfoLists = $queryPawningInfoLists->get();
                $customInfoLists = [];
            } elseif ($category == 'custom') {
                $invoiceLists = [];
                $saleInfoLists = [];
                $pawningInfoLists = [];
                $customInfoLists = $queryCustomInfoLists->get();
            } else {
                $invoiceLists = $queryInvoiceLists->get();
                $saleInfoLists = $querySaleInfoLists->get();
                $pawningInfoLists = $queryPawningInfoLists->get();
                $customInfoLists = $queryCustomInfoLists->get();
            }

            $currencySymbol = $this->currencySymbol;

            return view('report.receipt_payment', [
                'showTable' => $showTable,
                'invoiceLists' => $invoiceLists,
                'saleInfoLists' => $saleInfoLists,
                'pawningInfoLists' => $pawningInfoLists,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'category' => $category,
                'hireSubCategory' => $hireSubCategory,
                'loanSubCategory' => $loanSubCategory,
                'customInfoLists' => $customInfoLists,
                'currencySymbol' => $currencySymbol,
            ]);
        }
    }

    // public function receiptDetail($id){
    //     $reciept = Receipt::where('id', $id)->first();
    //     return json_encode($reciept);
    // }

    public function receiptDetail($id)
    {
        $settings = \Utility::settings();
        $receipt = Receipt::with('invoice')->where('id', $id)->first();
        $logo         = asset(\Storage::url('uploads/logo/'));
        $company_logo = \Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        // $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        return view('report.templates.receipt', compact('settings', 'receipt', 'img'));
    }

    public function customerReportIndex()
    {
        // $showTable = 'No';
        $customers = Customer::where('branch_id', \Auth::user()->branchId())->get();
        return view('report.customer_report', compact('customers'));
    }

    public function loanReportIndex()
    {
        $users = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $users->prepend('-- All --', '');
        $showTable = 'No';
        return view('report.loan_report', compact('showTable', 'users'));
    }

    public function loanReportSearch(Request $request)
    {
        $users = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $users->prepend('-- All --', '');

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $officerId = $request->officer_id;

        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = Loan::with('customer', 'loanInstallments')->whereDate('date', '<=', $end_date)->whereDate('date', '>=', $start_date)->where('branch_id', \Auth::user()->branchId());
            if ($request->loan_type != "") {
                $query->where('loan_type', $request->loan_type);
            }
            if ($request->loan_status != "") {
                $query->where('status', $request->loan_status);
            }
            if ($request->officer_id != "") {
                $query->where('officer_id', $request->officer_id);
            }

            $loanInfoLists = $query->get();
            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');

            return view('report.loan_report', compact('showTable', 'loanInfoLists', 'start_date', 'end_date', 'users', 'officerId'));
        }
    }

    public function loanStockReportIndex()
    {
        $users = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $users->prepend('-- All --', '');
        $showTable = 'No';
        return view('report.loan_stock_report', compact('showTable', 'users'));
    }

    public function loanStockReportSearch(Request $request)
    {
        $users = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $users->prepend('-- All --', '');

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $officerId = $request->officer_id;

        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = Loan::with('customer', 'loanInstallments')->whereDate('date', '<=', $end_date)->whereDate('date', '>=', $start_date)->where('branch_id', \Auth::user()->branchId());
            if ($request->loan_type != "") {
                $query->where('loan_type', $request->loan_type);
            }
            if ($request->officer_id != "") {
                $query->where('officer_id', $request->officer_id);
            }

            $loanInfoLists = $query->get();
            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');

            return view('report.loan_stock_report', compact('showTable', 'loanInfoLists', 'start_date', 'end_date', 'users', 'officerId'));
        }
    }

    public function loanOutstandingReportIndex()
    {
        $showTable = 'No';
        $loanType = '';
        $loanCenter = '';
        $loanGroup = '';
        $loanCollectionType = '';
        $centers = Center::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- All --', '');
        $groups = Group::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        // $groups->prepend('Select Group', '');
        return view('report.loan_outstanding_report', compact('showTable', 'loanType', 'centers', 'groups', 'loanCenter', 'loanGroup', 'oficcers', 'loanCollectionType'));
    }

    public function loanOutstandingReportSearch(Request $request)
    {
        // dump($request->loan_type);
        // dump($request->center_id);
        // dump($request->group_id);
        // dd($request->loan_status);
        $centers = Center::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- All --', '');
        $groups = Group::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        // $groups->prepend('Select Group', '');

        // $start_date = $request->start_date;
        $end_date = $request->end_date;
        // if(isset( $request->start_date) && isset( $request->end_date)){
        if (isset($request->end_date)) {
            // $startDate =date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            $loanType = '';
            $loanCenter = '';
            $loanGroup = '';
            $loanOficcer = '';
            $loanCollectionType = '';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = Loan::with([
                'customer',
                'center',
                'group',
                'loanInstallments' => function ($innerQuery) use ($end_date) {
                    // $innerQuery->where('status', 1)->whereNotNull('paid_date');
                    $innerQuery->where('status', 1)->where('installment_date', '<=', $end_date)->where('paid_date', '<=', $end_date);
                },
                'lastInstallment' => function ($inQuery) use ($end_date) {
                    $inQuery->where('installment_date', '<=', $end_date)->where('paid_date', '<=', $end_date);
                }
            ])
                ->whereDate('date', '<=', $end_date)
                // ->whereDate('date','>=',$start_date)
                ->where('status', 2)
                ->where('branch_id', \Auth::user()->branchId());
            if ($request->loan_type != "") {
                $loanType = $request->loan_type;
                $query->where('loan_type', $request->loan_type);
                if ($request->loan_type == 2) {
                    if ($request->center_id != '') {
                        $loanCenter = $request->center_id;
                        $query->where('center_id', $request->center_id);
                    }
                    if ($request->group_id != '') {
                        $loanGroup = $request->group_id;
                        $query->where('group_id', $request->group_id);
                    }
                }
            }
            if ($request->oficcer_id != "") {
                $loanOficcer = $request->oficcer_id;
                $query->where('officer_id', $request->oficcer_id);
            }
            if ($request->collection_type != "") {
                $loanCollectionType = $request->collection_type;
                $query->where('time_range', $request->collection_type);
            }


            $loanInfoLists = $query->paginate(1000);
            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');

            return view('report.loan_outstanding_report', compact('showTable', 'loanInfoLists', 'end_date', 'loanType', 'centers', 'groups', 'loanCenter', 'loanGroup', 'oficcers', 'loanOficcer', 'loanCollectionType'));
        }
    }

    public function hirePurchaseOutstandingReportIndex()
    {
        $showTable = 'No';
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        $introducers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $introducers->prepend('-- All --', '');
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $currencySymbol = $this->currencySymbol;

        return view('report.hire_purchase_outstanding_report', compact('showTable', 'oficcers', 'introducers', 'productCategories', 'currencySymbol'));
    }

    public function hirePurchaseOutstandingReportSearch(Request $request)
    {
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        $introducers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $introducers->prepend('-- All --', '');
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');

        // $start_date = $request->start_date;
        $end_date = $request->end_date;
        // if(isset( $request->start_date) && isset( $request->end_date)){
        if (isset($request->end_date)) {
            // $startDate =date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $loanOficcer = '';
            $loanIntroducer = '';
            $productCategory = '';

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = Sale::with([
                'customer',
                'products',
                'installments' => function ($innerQuery) use ($end_date) {
                    // $innerQuery->where('status', 1)->whereNotNull('paid_date');
                    $innerQuery->where('status', 1)->where('installment_date', '<=', $end_date)->where('paid_date', '<=', $end_date);
                },
                'lastInstallment' => function ($inQuery) use ($end_date) {
                    $inQuery->where('installment_date', '<=', $end_date)->where('paid_date', '<=', $end_date);
                }
            ])
                ->whereDate('date_of_sale', '<=', $end_date)
                // ->whereDate('date_of_sale','>=',$start_date)
                ->where('sale_type', 1)
                ->whereIn('status', [2, 3])
                ->where('branch_id', \Auth::user()->branchId());
            if ($request->category_id != "") {
                $productCategory = $request->category_id;
                $query->whereHas('products', function ($childQuery) use ($productCategory) {
                    $childQuery->where('product_category_id', $productCategory);
                });
            }
            if ($request->oficcer_id != "") {
                $loanOficcer = $request->oficcer_id;
                $query->where('officer_id', $request->oficcer_id);
            }
            if ($request->introducer_id != "") {
                $loanIntroducer = $request->introducer_id;
                $query->where('introducer_id', $request->introducer_id);
            }


            $loanInfoLists = $query->get();
            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');
            $currencySymbol = $this->currencySymbol;

            return view('report.hire_purchase_outstanding_report', compact('showTable', 'loanInfoLists', 'end_date', 'oficcers', 'loanOficcer', 'introducers', 'loanIntroducer', 'productCategories', 'productCategory', 'currencySymbol'));
        }
    }

    public function pawningOutstandingReportIndex()
    {
        $showTable = 'No';
        $pawningCollectionType = '';
        $introducers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $introducers->prepend('-- All --', '');
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        // $groups->prepend('Select Group', '');
        return view('report.pawning_outstanding_report', compact('showTable', 'oficcers', 'introducers', 'pawningCollectionType'));
    }

    public function pawningOutstandingReportSearch(Request $request)
    {
        $introducers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $introducers->prepend('-- All --', '');
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        // $groups->prepend('Select Group', '');

        // $start_date = $request->start_date;
        $end_date = $request->end_date;
        // if(isset( $request->start_date) && isset( $request->end_date)){
        if (isset($request->end_date)) {
            // $startDate =date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            $pawningOficcer = '';
            $pawningIntroducer = '';
            $pawningCollectionType = '';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = Pawning::with([
                'installments' => function ($innerQuery) use ($end_date) {
                    // $innerQuery->where('status', 1)->whereNotNull('paid_date');
                    $innerQuery->where('status', 1)->where('installment_date', '<=', $end_date)->where('paid_date', '<=', $end_date);
                },
                'lastInstallment' => function ($inQuery) use ($end_date) {
                    $inQuery->where('installment_date', '<=', $end_date)->where('paid_date', '<=', $end_date);
                }
            ])
                ->whereDate('issue_date', '<=', $end_date)
                // ->whereDate('issue_date','>=',$start_date)
                ->whereIn('status', [1, 2, 3])
                ->where('branch_id', \Auth::user()->branchId());

            if ($request->oficcer_id != "") {
                $pawningOficcer = $request->oficcer_id;
                $query->where('officer_id', $request->oficcer_id);
            }
            if ($request->introducer_id != "") {
                $pawningIntroducer = $request->introducer_id;
                $query->where('introducer_id', $request->introducer_id);
            }
            if ($request->collection_type != "") {
                $pawningCollectionType = $request->collection_type;
                $query->where('time_range', $request->collection_type);
            }


            $pawningInfoLists = $query->get();
            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');

            return view('report.pawning_outstanding_report', compact('showTable', 'pawningInfoLists', 'end_date', 'oficcers', 'pawningOficcer', 'introducers', 'pawningIntroducer', 'pawningCollectionType'));
        }
    }

    public function salesRepaymentSheetIndex()
    {
        $showTable = 'No';
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $currencySymbol = $this->currencySymbol;
        return view('report.sales_repayment_sheet', compact('showTable', 'oficcers', 'productCategories', 'currencySymbol'));
    }

    public function salesRepaymentSheetSearch(Request $request)
    {
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');

        $month = $request->month;
        if (isset($request->month)) {
            $dateYear = date("Y", strtotime($month));
            $dateMonth = date("m", strtotime($month));

            $loanOficcer = '';
            $productCategory = '';

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = Sale::with(['customer', 'products', 'lastInstallment', 'installments' => function ($innerQuery) use ($dateYear, $dateMonth) {
                $innerQuery->whereYear('installment_date', $dateYear)
                    ->whereMonth('installment_date', $dateMonth);
            }])
                // ->whereHas('installments', function($innerQuery) use ($dateYear, $dateMonth){
                //     $innerQuery->whereYear('installment_date', $dateYear)
                //                 ->whereMonth('installment_date', $dateMonth);
                // })
                // ->whereYear('date_of_sale','>=',$dateYear)
                // ->whereMonth('date_of_sale','<',$dateMonth)
                ->where('sale_type', 1)
                ->whereNotIn('status', [0, 1, 4])
                ->where('branch_id', \Auth::user()->branchId());
            if ($request->category_id != "") {
                $productCategory = $request->category_id;
                $query->whereHas('products', function ($childQuery) use ($productCategory) {
                    $childQuery->where('product_category_id', $productCategory);
                });
            }
            if ($request->oficcer_id != "") {
                $loanOficcer = $request->oficcer_id;
                $query->where('officer_id', $request->oficcer_id);
            }

            $saleInfoLists = $query->get();
            $currencySymbol = $this->currencySymbol;
            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');
            // dd($saleInfoLists[50]->installmentsFine);
            return view('report.sales_repayment_sheet', compact('showTable', 'saleInfoLists', 'month', 'oficcers', 'loanOficcer', 'productCategories', 'productCategory', 'currencySymbol'));
        }
    }

    public function repaymentSheetIndex()
    {
        $showTable = 'No';
        $centers = Center::orderBy('name')->where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- Center --', '');

        return view('report.repayment_sheet', compact('showTable', 'centers'));
    }

    public function repaymentSheetSearch(Request $request)
    {
        $centers = Center::orderBy('name')->where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- Center --', '');

        $month = $request->month;
        $repaymentCenters = $request->center_id;
        if (isset($request->month) && isset($request->center_id)) {
            $dateYear = date("Y", strtotime($month));
            $dateMonth = date("m", strtotime($month));

            $showTable = 'Yes';

            $centerInfoLists = Center::with([
                'groups.groupMembers' => function ($mainQuery) use ($dateYear, $dateMonth) {
                    $mainQuery->where('status', 1)->with([
                        'customers.loans' => function ($queryLoan) use ($dateYear, $dateMonth) {
                            $queryLoan->where('status', 2);
                            $queryLoan->where('loan_type', 2);
                            $queryLoan->with([
                                'loanInstallments' => function ($query2) use ($dateYear, $dateMonth) {
                                    $query2->whereYear('installment_date', $dateYear);
                                    $query2->whereMonth('installment_date', $dateMonth);
                                }
                            ]);
                        }
                    ]);
                }
            ])
                // ->whereHas('groups.groupMembers.customers.loans', function($query){
                //     $query->where('status', 2);
                //     $query->where('loan_type', 2);
                // })
                ->whereHas('groups', function ($queryGroup) {
                    $queryGroup->where('status', 1);
                })
                ->where('id', $request->center_id)->get();

            return view('report.repayment_sheet', compact('showTable', 'centerInfoLists', 'month', 'repaymentCenters', 'centers'));
        }
    }

    public function executiveRepaymentSheetIndex()
    {
        $showTable = 'No';
        $loanType = '';
        $loanCollectionType = '';
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');

        return view('report.executive_repayment_sheet', compact('showTable', 'loanType', 'loanCollectionType', 'oficcers'));
    }

    public function executiveRepaymentSheetSearch(Request $request)
    {
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');

        $month = $request->month;
        $loanType = '';
        $loanCollectionType = '';
        $loanOficcer = '';
        if (isset($request->month)) {
            $dateYear = date("Y", strtotime($month));
            $dateMonth = date("m", strtotime($month));

            $showTable = 'Yes';

            $queryInfo = Loan::with(['customer', 'loanInstallments' => function ($innerQuery) use ($dateYear, $dateMonth) {
                $innerQuery->whereYear('installment_date', $dateYear)
                    ->whereMonth('installment_date', $dateMonth);
            }])
                // ->whereHas('loanInstallments', function($innerQuery) use ($dateYear, $dateMonth){
                //         $innerQuery->whereYear('installment_date',$dateYear)
                //                     ->whereMonth('installment_date',$dateMonth);
                //     })
                ->where('status', 2)
                ->where('branch_id', \Auth::user()->branchId());

            if ($request->loan_type != '') {
                $loanType = $request->loan_type;
                $queryInfo->where('loan_type', $request->loan_type);
            }
            if ($request->oficcer_id != '') {
                $loanOficcer = $request->oficcer_id;
                $queryInfo->where('officer_id', $request->oficcer_id);
            }
            if ($request->collection_type != '') {
                $loanCollectionType = $request->collection_type;
                $queryInfo->where('time_range', $request->collection_type);
            }
            $loanInfoLists = $queryInfo->get();

            return view('report.executive_repayment_sheet', compact('showTable', 'loanInfoLists', 'month', 'loanType', 'loanCollectionType', 'oficcers', 'loanOficcer'));
        }
    }


    public function stocksReportIndex()
    {
        $showTable = 'No';

        $branches = Branch::where('status', 1)->get()->pluck('name', 'id');
        $branches->prepend('-- Select Branch --', '');

        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $currencySymbol = $this->currencySymbol;
        return view('report.stocks_report', compact('showTable', 'branches', 'productCategories', 'currencySymbol'));
    }

    public function stocksReportSearch(Request $request)
    {
        $branches = Branch::where('status', 1)->get()->pluck('name', 'id');
        $branches->prepend('-- Select Branch --', '');
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');

        if (isset($request->branch_id)) {
            // $date = $request->date;
            $branch = $request->branch_id;
            $category = '';

            $showTable = 'Yes';
            // $query = StockTransaction::select('branches.name AS branch_name', 'products.name AS product_name', 'products.product_category_id', 'inventories.purchase_price', 'inventories.sale_price',
            //                             DB::raw('SUM(CASE WHEN stock_transactions.stock_master_id = 1 THEN stock_transactions.quantity WHEN stock_transactions.stock_master_id = 2 THEN -stock_transactions.quantity WHEN stock_transactions.stock_master_id = 3 THEN -stock_transactions.quantity WHEN stock_transactions.stock_master_id = 4 THEN stock_transactions.quantity WHEN stock_transactions.stock_master_id = 5 THEN stock_transactions.quantity END) AS quantity'))
            //                             ->leftjoin('branches', 'branches.id', 'stock_transactions.branch_id')
            //                             ->leftjoin('products', 'products.id', 'stock_transactions.product_id')
            //                             ->leftjoin('inventories', 'inventories.id', 'stock_transactions.inventory_id')
            //                             ->where('stock_transactions.date', '<=', $date)
            //                             ->where('stock_transactions.status', 1)
            //                             ->where('stock_transactions.branch_id', $request->branch_id);

            $inventoryQuery = Inventory::select(
                'products.barcode AS product_code',
                'products.name AS product_name',
                'products.product_category_id',
                'product_brands.name AS brand_name',
                'branches.name AS branch_name',
                'inventories.purchase_price',
                'inventories.date',
                'inventories.sale_price',
                \DB::raw('SUM(inventories.remain_qty) as remain_qty'),
                'inventories.status as status'
            )
                ->join('products', 'products.id', 'inventories.product_id')
                ->join('branches', 'branches.id', 'inventories.branch_id')
                ->join('product_brands', 'products.product_brand_id', 'product_brands.id')
                ->where('inventories.branch_id', $request->branch_id)
                ->where('inventories.status', 1)
                ->orderBy('products.barcode')
                ->orderBy('inventories.created_at', 'asc');

            if ($request->category_id != '') {
                $category = $request->category_id;
                // $query->where('products.product_category_id', $request->category_id);
                $inventoryQuery->where('products.product_category_id', $request->category_id);
            }

            // $inventries = $query->groupBy('branch_name', 'product_name', 'purchase_price', 'sale_price')->get();
            $inventries = $inventoryQuery->groupBy('product_name', 'products.product_category_id', 'branch_name', 'inventories.purchase_price', 'inventories.sale_price')
                ->get();
            $currencySymbol = $this->currencySymbol;
            return view('report.stocks_report', compact('showTable', 'inventries', 'branches', 'branch', 'productCategories', 'category', 'currencySymbol'));
        }
    }

    public function stockItemReportIndex()
    {
        $showTable = 'No';
        $products = Product::with('brands')
            ->where('status', 1)
            ->get()
            ->sortBy('brands.name');
        $currencySymbol = $this->currencySymbol;
        return view('report.stock_item_in_out_report', compact('showTable', 'products', 'currencySymbol'));
    }

    public function stockItemReportSearch(Request $request)
    {
        $products = Product::with('brands')
            ->where('status', 1)
            ->get()
            ->sortBy('brands.name');

        $start_date = '';
        $end_date = '';
        if (isset($request->product)) {
            $productId = $request->product;
            $showTable = 'Yes';

            $productDetail = Product::where('id', $request->product)->first();
            $queryInventory = Inventory::with('grns', 'sales', 'saleItems', 'stockTransferItems')->where('product_id', $productId)
                ->where('branch_id', \Auth::user()->branchId())
                ->whereNotIn('grn_id', [0]);

            $queryTransferInventory = Inventory::with('sales', 'saleItems', 'stockTransferItems', 'transferFrom')->where('product_id', $productId)
                ->where('branch_id', \Auth::user()->branchId())
                ->whereIn('grn_id', [0]);

            if (isset($request->start_date) && $request->start_date != '') {
                $start_date = $request->start_date;
                $queryInventory->where('date', '>=', $start_date);
                $queryTransferInventory->where('date', '>=', $start_date);
            }
            if (isset($request->end_date) && $request->end_date != '') {
                $end_date = $request->end_date;
                $queryInventory->where('date', '<=', $end_date);
                $queryTransferInventory->where('date', '<=', $end_date);
            }

            $invenvtories = $queryInventory->get();
            $transfersInvenvtories = $queryTransferInventory->get();
            $currencySymbol = $this->currencySymbol;

            return view('report.stock_item_in_out_report', compact('showTable', 'products', 'productId', 'invenvtories', 'transfersInvenvtories', 'start_date', 'end_date', 'productDetail', 'currencySymbol'));
        }
    }

    public function stockReportIndex()
    {
        $currencySymbol = $this->currencySymbol;
        return view('report.stock_in_out_report', [
            'showTable' => 'No',
            'currencySymbol' => $currencySymbol,
        ]);
    }

    public function stockReportSearch(Request $request)
    {
        $products = Product::with('brands')
            ->where('status', 1)
            ->get()
            ->sortBy('brands.name');

        $start_date = $request->start_date ?? '';
        $end_date = $request->end_date ?? '';

        $productStocks = [];
        foreach ($products as $product) {
            $productId = $product->id;

            $queryInventory = Inventory::with('grns', 'sales', 'saleItems', 'stockTransferItems')->where('product_id', $productId)
                ->where('branch_id', \Auth::user()->branchId())
                ->whereNotIn('grn_id', [0]);

            $queryTransferInventory = Inventory::with('sales', 'saleItems', 'stockTransferItems', 'transferFrom')->where('product_id', $productId)
                ->where('branch_id', \Auth::user()->branchId())
                ->whereIn('grn_id', [0]);

            if (isset($start_date) && $start_date != '') {
                $queryInventory->where('date', '>=', $start_date);
                $queryTransferInventory->where('date', '>=', $start_date);
            }

            if (isset($end_date) && $end_date != '') {
                $queryInventory->where('date', '<=', $end_date);
                $queryTransferInventory->where('date', '<=', $end_date);
            }

            $invenvtories = $queryInventory->get();
            $transfersInvenvtories = $queryTransferInventory->get();

            if ($invenvtories->isNotEmpty() || $transfersInvenvtories->isNotEmpty()) {
                $productStocks[] = [
                    'product' => $product,
                    'invenvtories' => $invenvtories,
                    'transfersInvenvtories' => $transfersInvenvtories,
                ];
            }
        }
        $currencySymbol = $this->currencySymbol;
        return view('report.stock_in_out_report', [
            'showTable' => 'Yes',
            'productStocks' => $productStocks,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'currencySymbol' => $currencySymbol,
        ]);
    }

    public function stockTransfersReportIndex()
    {
        $showTable = 'No';
        $branches = Branch::get()->pluck('name', 'id');
        $branches->prepend('-- All --', '');
        $currencySymbol = $this->currencySymbol;

        return view('report.stock_transfers_report', compact('showTable', 'branches', 'currencySymbol'));
    }

    public function stockTransfersReportSearch(Request $request)
    {
        $branches = Branch::get()->pluck('name', 'id');
        $branches->prepend('-- All --', '');

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $branch = '';

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = StockTransfer::with('stockTransItems')
                ->whereDate('date', '<=', $end_date)
                ->whereDate('date', '>=', $start_date);
            if ($request->branch_id != "") {
                $branch = $request->branch_id;
                $query->where('branch_id', $request->branch_id);
            }


            $transfers = $query->get();
            $currencySymbol = $this->currencySymbol;
            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');

            return view('report.stock_transfers_report', compact('showTable', 'transfers', 'start_date', 'end_date', 'branches', 'branch', 'currencySymbol'));
        }
    }

    public function attendanceSheetIndex()
    {
        $showTable = 'No';
        $centers = Center::orderBy('name')->where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- Center --', '');

        return view('report.attendance_sheet', compact('showTable', 'centers'));
    }

    public function attendanceSheetSearch(Request $request)
    {
        $centers = Center::orderBy('name')->where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- Center --', '');

        $start_month = $request->start_month;
        $end_month = Carbon::parse($start_month)->addMonth(5)->format('Y-m');
        $attendanceCenters = $request->center_id;

        if (isset($request->start_month) && isset($request->center_id)) {
            $dateYear = date("Y", strtotime($start_month));
            $dateMonth = date("m", strtotime($start_month)) - 1;

            $attendMonths = [];
            for ($i = 0; $i < 6; $i++) {
                $months[] = __('January');
                $months[] = __('February');
                $months[] = __('March');
                $months[] = __('April');
                $months[] = __('May');
                $months[] = __('June');
                $months[] = __('July');
                $months[] = __('August');
                $months[] = __('September');
                $months[] = __('October');
                $months[] = __('November');
                $months[] = __('December');

                foreach ($months as $k => $month) {
                    if ($k == $dateMonth) {
                        $attendMonths[] = $month;
                    }
                }
                $dateMonth++;
            }

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $centerInfoLists = Center::with('groups.groupMembers.customers')->where('id', $request->center_id)->get();

            return view('report.attendance_sheet', compact('showTable', 'centerInfoLists', 'attendMonths', 'start_month', 'end_month', 'centers', 'attendanceCenters'));
        }
    }


    public function cashCollectorReportIndex()
    {
        $showTable = 'No';
        $loanType = '';
        $loanCenter = '';
        $loanGroup = '';
        $loanCollectionType = '';
        $category = '';
        $product_category = '';
        $centers = Center::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- All --', '');
        $groups = Group::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        $currencySymbol = $this->currencySymbol;
        return view('report.cash_collector_report', compact('showTable', 'loanType', 'centers', 'groups', 'loanCenter', 'loanGroup', 'oficcers', 'loanCollectionType', 'category', 'productCategories', 'product_category', 'currencySymbol'));
    }

    public function cashCollectorReportSearch(Request $request)
    {
        $centers = Center::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- All --', '');
        $groups = Group::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $category = $request->category;
        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            $loanType = '';
            $loanCenter = '';
            $loanGroup = '';
            $loanOficcer = '';
            $loanCollectionType = '';
            $product_category = '';

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $saleQuery = Sale::with(['customer', 'products', 'installments' => function ($innerQuery) use ($end_date, $start_date) {
                $innerQuery->whereDate('installment_date', '<=', $end_date)
                    ->whereDate('installment_date', '>=', $start_date);
            }])
                ->whereHas('installments', function ($innerQuery) use ($end_date, $start_date) {
                    $innerQuery->whereDate('installment_date', '<=', $end_date)
                        ->whereDate('installment_date', '>=', $start_date);
                })
                ->where('sale_type', 1)
                ->whereNotIn('status', [0, 1, 4])
                ->where('branch_id', \Auth::user()->branchId());

            $loanQuery = Loan::with(['customer', 'loanInstallments' => function ($innerQuery) use ($end_date, $start_date) {
                $innerQuery->whereDate('installment_date', '<=', $end_date)
                    ->whereDate('installment_date', '>=', $start_date);
            }])
                ->whereHas('loanInstallments', function ($innerQuery) use ($end_date, $start_date) {
                    $innerQuery->whereDate('installment_date', '<=', $end_date)
                        ->whereDate('installment_date', '>=', $start_date);
                })
                ->where('status', 2)
                ->where('branch_id', \Auth::user()->branchId());

            $pawningQuery = Pawning::with(['installments' => function ($innerQuery) use ($end_date, $start_date) {
                $innerQuery->whereDate('installment_date', '<=', $end_date)
                    ->whereDate('installment_date', '>=', $start_date);
            }])
                ->whereHas('installments', function ($innerQuery) use ($end_date, $start_date) {
                    $innerQuery->whereDate('installment_date', '<=', $end_date)
                        ->whereDate('installment_date', '>=', $start_date);
                })
                ->whereNotIn('status', [4])
                ->where('branch_id', \Auth::user()->branchId());

            if ($request->loan_type != "") {
                $loanType = $request->loan_type;
                $loanQuery->where('loan_type', $request->loan_type);

                if ($request->loan_type == 2) {
                    if ($request->center_id != '') {
                        $loanCenter = $request->center_id;
                        $loanQuery->where('center_id', $request->center_id);
                    }
                    if ($request->group_id != '') {
                        $loanGroup = $request->group_id;
                        $loanQuery->where('group_id', $request->group_id);
                    }
                }
            }

            if ($request->product_category_id != '') {
                $product_category = $request->product_category_id;
                $saleQuery->whereHas('products', function ($childQuery) use ($product_category) {
                    $childQuery->where('product_category_id', $product_category);
                });
            }

            if ($request->collection_type != "") {
                $loanCollectionType = $request->collection_type;
                $loanQuery->where('time_range', $request->collection_type);
                $pawningQuery->where('time_range', $request->collection_type);
            }

            if ($request->oficcer_id != "") {
                $loanOficcer = $request->oficcer_id;
                $saleQuery->where('officer_id', $request->oficcer_id);
                $loanQuery->where('officer_id', $request->oficcer_id);
                $pawningQuery->where('officer_id', $request->oficcer_id);
            }

            if ($category == 1) {
                $saleInfoLists = $saleQuery->get();
                $loanInfoLists = [];
                $pawningInfoLists = [];
            } elseif ($category == 2) {
                $saleInfoLists = [];
                $loanInfoLists = $loanQuery->get();
                $pawningInfoLists = [];
            } elseif ($category == 3) {
                $saleInfoLists = [];
                $loanInfoLists = [];
                $pawningInfoLists = $pawningQuery->get();
            } else {
                $saleInfoLists = $saleQuery->get();
                $loanInfoLists = $loanQuery->get();
                $pawningInfoLists = $pawningQuery->get();
            }

            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');
            $currencySymbol = $this->currencySymbol;
            return view('report.cash_collector_report', compact('showTable', 'saleInfoLists', 'loanInfoLists', 'pawningInfoLists', 'oficcers', 'loanOficcer', 'loanType', 'centers', 'groups', 'loanCenter', 'loanGroup', 'loanCollectionType', 'category', 'productCategories', 'product_category', 'start_date', 'end_date', 'currencySymbol'));
        }
    }

    public function loanDisbursementReportIndex()
    {
        $showTable = 'No';
        $allCenters = Center::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $allCenters->prepend('-- All --', '');
        return view('report.loan_disbursement_report', compact('showTable', 'allCenters'));
    }

    public function loanDisbursementReportSearch(Request $request)
    {
        $allCenters = Center::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $allCenters->prepend('-- All --', '');

        if ($request->start_date && $request->end_date) {
            $showTable = 'Yes';
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $centerId = $request->center_id;

            $loans = [];
            $counts = [];
            $centerLoans = [];

            $date = $startDate;
            while ($date <= $endDate) {

                $otherLoans = Loan::with('customer', 'center', 'group')
                    ->where('date', $date)
                    ->where('status', 2)
                    ->whereNotIn('loan_type', [2])
                    ->where('branch_id', \Auth::user()->branchId())
                    ->get();

                $queryCenter = Center::with('groups.groupMembers')
                    ->where('branch_id', \Auth::user()->branchId())
                    ->where('status', 1);

                if ($request->center_id != '') {
                    $queryCenter->where('id', $request->center_id);
                    $loans = [];
                }
                $centers = $queryCenter->get();
                // $counts = [];
                // $centerLoans = [];
                if (count($otherLoans) > 0) {
                    foreach ($otherLoans as $loan) {
                        $ln = Loan::where('customer_id', $loan->customer_id)->where('id', '<=', $loan->id)->count();
                        $counts[] = array('customer_id' => $loan->customer_id, 'count' => $ln);
                        $loans[] = $loan;
                    }
                }
                if (count($centers) > 0) {
                    foreach ($centers as $center) {
                        $groupLoanCounts = [];
                        $groupLoans = [];

                        foreach ($center->groups as $group) {
                            foreach ($group->groupMembers as $customer) {
                                $gln2 = Loan::with('customer')
                                    ->where('customer_id', $customer->customer_id)
                                    ->where('date',  $date)
                                    ->where('status', 2)
                                    ->where('loan_type', 2)
                                    ->where('branch_id', \Auth::user()->branchId())
                                    ->first();
                                if ($gln2 != null) {
                                    $gln = Loan::where('customer_id', $customer->customer_id)->where('id', '<=', $gln2->id)->count();
                                    // $groupLoanCounts [] = array();
                                    $groupLoans[] = array($gln2, 'customer_id' => $customer->customer_id, 'count' => $gln);
                                }
                            }
                        }
                        if (count($groupLoans) > 0) {
                            $centerLoans[] = array('center_id' => $center->id, 'center_name' => $center->name, 'center' => $center, 'loans' => $groupLoans, 'date' => $date);
                        }
                    }
                }

                $date = Carbon::parse($date)->addDays(1)->format('Y-m-d');
            }
            return view('report.loan_disbursement_report', compact('showTable', 'loans', 'counts', 'centerLoans', 'startDate', 'endDate', 'allCenters', 'centerId'));
        }
    }

    public function supplierReportIndex()
    {
        // $showTable = 'No';
        // $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        // $oficcers->prepend('-- All --', '');
        // $productCategories = ProductCategory::get()->pluck('name', 'id');
        // $productCategories->prepend('-- All --', '');
        $venders = Vender::get();

        return view('report.supplier_report', compact('venders'));
    }

    public function supplierReportgetGrns($id)
    {
        // $showTable = 'No';
        // $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        // $oficcers->prepend('-- All --', '');
        // $productCategories = ProductCategory::get()->pluck('name', 'id');
        // $productCategories->prepend('-- All --', '');
        $grns = Grn::where('vender_id', $id)->orderBy('order_date', 'DESC')->get();

        return view('report.models.supplier_report_model', compact('grns'));
    }

    public function getGroups($id)
    {
        $groups = Group::select('id', 'name')->where('center_id', $id)->where('status', 1)->get();
        return json_encode($groups);
    }

    public function installmentByLoan($id)
    {
        $installment = LoanInstallment::where('loan_id', $id)->get();
        return json_decode($installment);
    }

    public function loanInstallmentReportIndex()
    {
        $users = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $users->prepend('-- All --', '');
        $showTable = 'No';
        return view('report.loan_installment_report', compact('showTable', 'users'));
    }

    public function loanInstallmentReportSearch(Request $request)
    {

        $users = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $users->prepend('-- All --', '');

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loanType = $request->loan_type;
        $status = $request->status;
        $officerId = $request->officer_id;
        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $query = LoanInstallment::with('loan.customer')->whereDate('installment_date', '<=', $end_date)->whereDate('installment_date', '>=', $start_date)->where('branch_id', \Auth::user()->branchId());
            $dueTotQuery = LoanInstallment::with('loan')->whereDate('installment_date', '<=', $end_date)->whereDate('installment_date', '>=', $start_date)->whereIn('status', [2, 3])->where('branch_id', \Auth::user()->branchId());

            $query->whereHas('loan', function ($childQuery) {
                $childQuery->whereNotNull('id');
            });
            $dueTotQuery->whereHas('loan', function ($dueChildQuery) {
                $dueChildQuery->whereNotNull('id');
            });
            if ($request->loan_type != "") {
                // $query->where('loan_type', $request->loan_type);
                $query->whereHas('loan', function ($childQuery) use ($loanType) {
                    $childQuery->where('loan_type', $loanType);
                });
                $dueTotQuery->whereHas('loan', function ($dueChildQuery) use ($loanType) {
                    $dueChildQuery->where('loan_type', $loanType);
                });
            }
            if ($request->status != "") {
                if ($request->status == 4) {
                    $query->whereIn('status', [2, 3]);
                    // $dueTotQuery->whereIn('status', [2,3]);
                } else {
                    $query->where('status', $request->status);
                    // $dueTotQuery->where('status', $request->status);
                }
            }
            if ($request->officer_id != "") {
                // $query->where('officer_id', $request->officer_id);
                $query->whereHas('loan', function ($childQuery) use ($officerId) {
                    $childQuery->where('officer_id', $officerId);
                });
                $dueTotQuery->whereHas('loan', function ($dueChildQuery) use ($officerId) {
                    $dueChildQuery->where('officer_id', $officerId);
                });
            }

            $loanInfoLists = $query->get();
            $duetotal = $dueTotQuery->sum('total_amount_to_pay');

            return view('report.loan_installment_report', compact('showTable', 'loanInfoLists', 'start_date', 'end_date', 'users', 'duetotal', 'loanType', 'status', 'officerId'));
        }
    }

    public function closedLoanReportIndex()
    {
        // $showTable = 'No';
        $loanType = '';
        $loans = Loan::with([
            'customer',
            'center',
            'group',
            'guarantor_1',
            'guarantor_2',
            'guarantor_3',
            'guarantor_4',
            'loanInstallments' => function ($innerQuery) {
                $innerQuery->where('status', 1)->whereNotNull('paid_date');
            }
        ])
            ->where('status', 3)
            ->where('branch_id', \Auth::user()->branchId())
            ->orderBy('id', 'DESC')->get();
        return view('report.closed_loan_report', compact('loanType', 'loans'));
    }

    public function closedLoanReportSearch(Request $request)
    {
        // $showTable = 'yes';
        $loanType = '';
        $query = Loan::with([
            'customer',
            'center',
            'group',
            'guarantor_1',
            'guarantor_2',
            'guarantor_3',
            'guarantor_4',
            'loanInstallments' => function ($innerQuery) {
                $innerQuery->where('status', 1)->whereNotNull('paid_date');
            }
        ])
            ->where('status', 3)
            ->where('branch_id', \Auth::user()->branchId());
        if ($request->loan_type != "") {
            $loanType = $request->loan_type;
            $query->where('loan_type', $request->loan_type);
        }

        $loans = $query->orderBy('id', 'DESC')->get();
        return view('report.closed_loan_report', compact('loanType', 'loans'));
    }

    public function closedHirePurchaseReportIndex()
    {
        // $showTable = 'No';
        $sales = Sale::with(['lastInstallment', 'installments' => function ($innerQuery) {
            $innerQuery->where('status', 1)->whereNotNull('paid_date');
        }])
            ->where('status', 1)
            ->where('sale_type', 1)
            ->whereNotIn('status', [0, 4])
            ->where('branch_id', \Auth::user()->branchId())
            ->orderBy('id', 'DESC')->get();
        $currencySymbol = $this->currencySymbol;
        return view('report.closed_hire_purchase_report', compact('sales', 'currencySymbol'));
    }

    // public function collectionReportIndex(){
    //     $showTable = 'No';
    //     return view('report.collection_report', compact('showTable'));
    // }

    // public function collectionReportSearch(Request $request){

    //     $start_date = $request->start_date;
    //     $end_date = $request->end_date;
    //     $collectionType = $request->collection_type;


    //     // $loanQuery = LoanInstallment::with('loan.customer')->whereDate('created_at','<=',$end_date)->whereDate('created_at','>=',$start_date)->where('status', 1);
    //     if(isset( $request->start_date) && isset( $request->end_date)){
    //         // $startDate = date("d", strtotime($start_date));
    //         // $endDate = date("d", strtotime($end_date));
    //         // die(var_dump($end_date));

    //         $showTable = 'Yes';

    //         if($request->collection_type == ''){
    //             $saleInfoLists = Receipt::where('issue_date','>=', $start_date)
    //                                         ->where('issue_date','<=', $end_date)
    //                                         ->where('branch_id', \Auth::user()->branchId())
    //                                         ->get();

    //             $pawningInfoLists = PawningReceipt::with('customer')->where('issue_date','>=', $start_date)
    //                                                 ->where('issue_date','<=', $end_date)
    //                                                 ->where('branch_id', \Auth::user()->branchId())
    //                                                 ->get();
    //         }elseif($request->collection_type != ''){
    //             if($request->collection_type == 'pawning'){
    //                 $saleInfoLists = [];
    //                 $pawningInfoLists = PawningReceipt::with('customer')->where('issue_date','>=', $start_date)
    //                                                     ->where('issue_date','<=', $end_date)
    //                                                     ->where('branch_id', \Auth::user()->branchId())
    //                                                     ->get();
    //             } else {
    //                 if($request->collection_type == 'hire_purchase'){
    //                     $saleInfoLists = Receipt::where('issue_date','>=', $start_date)
    //                                             ->where('issue_date','<=', $end_date)
    //                                             ->where('branch_id', \Auth::user()->branchId())
    //                                             ->whereNotNull('invoice_id')
    //                                             ->get();

    //                     $pawningInfoLists = [];
    //                 }elseif($request->collection_type == 'loan'){
    //                     $saleInfoLists = Receipt::where('issue_date','>=', $start_date)
    //                                             ->where('issue_date','<=', $end_date)
    //                                             ->where('branch_id', \Auth::user()->branchId())
    //                                             ->whereNotNull('loan_id')
    //                                             ->get();

    //                     $pawningInfoLists = [];
    //                 }
    //             }
    //         }


    //         return view('report.collection_report', compact('showTable', 'saleInfoLists', 'pawningInfoLists', 'start_date', 'end_date', 'collectionType'));
    //     }
    // }

    public function collectionReportIndex()
    {
        $showTable = 'No';
        $loanType = '';
        $loanCenter = '';
        $loanGroup = '';
        $loanCollectionType = '';
        $category = '';
        $product_category = '';
        $centers = Center::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- All --', '');
        $groups = Group::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        $currencySymbol = $this->currencySymbol;
        return view('report.collection_report', compact('showTable', 'loanType', 'centers', 'groups', 'loanCenter', 'loanGroup', 'oficcers', 'loanCollectionType', 'category', 'productCategories', 'product_category', 'currencySymbol'));
    }

    public function collectionReportSearch(Request $request)
    {
        $centers = Center::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get()->pluck('name', 'id');
        $centers->prepend('-- All --', '');
        $groups = Group::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $category = $request->category;
        if (isset($request->start_date) && isset($request->end_date)) {
            $startDate = date("d", strtotime($start_date));
            $endDate = date("d", strtotime($end_date));

            $showTable = 'Yes';
            $loanType = '';
            $loanCenter = '';
            $loanGroup = '';
            $loanOficcer = '';
            $loanCollectionType = '';
            $product_category = '';

            $showTable = 'Yes';
            // $saleInfoLists= Sale::with(['customerInfo','productInfo'])->where('installment_date','>=', $startDate)->where('installment_date','<=', $endDate)
            $saleQuery = Sale::with(['customer', 'products', 'installments' => function ($innerQuery) use ($end_date, $start_date) {
                $innerQuery->whereDate('paid_date', '<=', $end_date)
                    ->whereDate('paid_date', '>=', $start_date);
            }])
                ->whereHas('installments', function ($innerQuery) use ($end_date, $start_date) {
                    $innerQuery->whereDate('paid_date', '<=', $end_date)
                        ->whereDate('paid_date', '>=', $start_date);
                })
                ->where('sale_type', 1)
                ->whereNotIn('status', [0, 4])
                ->where('branch_id', \Auth::user()->branchId());

            $loanQuery = Loan::with(['customer', 'loanInstallments' => function ($innerQuery) use ($end_date, $start_date) {
                $innerQuery->whereDate('paid_date', '<=', $end_date)
                    ->whereDate('paid_date', '>=', $start_date);
            }])
                ->whereHas('loanInstallments', function ($innerQuery) use ($end_date, $start_date) {
                    $innerQuery->whereDate('paid_date', '<=', $end_date)
                        ->whereDate('paid_date', '>=', $start_date);
                })
                ->whereIn('status', [2, 3])
                ->where('branch_id', \Auth::user()->branchId());

            $pawningQuery = Pawning::with(['installments' => function ($innerQuery) use ($end_date, $start_date) {
                $innerQuery->whereDate('paid_date', '<=', $end_date)
                    ->whereDate('paid_date', '>=', $start_date);
            }])
                ->whereHas('installments', function ($innerQuery) use ($end_date, $start_date) {
                    $innerQuery->whereDate('paid_date', '<=', $end_date)
                        ->whereDate('paid_date', '>=', $start_date);
                })
                ->whereNotIn('status', [0])
                ->where('branch_id', \Auth::user()->branchId());

            if ($request->loan_type != "") {
                $loanType = $request->loan_type;
                $loanQuery->where('loan_type', $request->loan_type);

                if ($request->loan_type == 2) {
                    if ($request->center_id != '') {
                        $loanCenter = $request->center_id;
                        $loanQuery->where('center_id', $request->center_id);
                    }
                    if ($request->group_id != '') {
                        $loanGroup = $request->group_id;
                        $loanQuery->where('group_id', $request->group_id);
                    }
                }
            }

            if ($request->product_category_id != '') {
                $product_category = $request->product_category_id;
                $saleQuery->whereHas('products', function ($childQuery) use ($product_category) {
                    $childQuery->where('product_category_id', $product_category);
                });
            }

            if ($request->collection_type != "") {
                $loanCollectionType = $request->collection_type;
                $loanQuery->where('time_range', $request->collection_type);
                $pawningQuery->where('time_range', $request->collection_type);
            }

            if ($request->oficcer_id != "") {
                $loanOficcer = $request->oficcer_id;
                $saleQuery->where('officer_id', $request->oficcer_id);
                $loanQuery->where('officer_id', $request->oficcer_id);
                $pawningQuery->where('officer_id', $request->oficcer_id);
            }

            if ($category == 1) {
                $saleInfoLists = $saleQuery->get();
                $loanInfoLists = [];
                $pawningInfoLists = [];
            } elseif ($category == 2) {
                $saleInfoLists = [];
                $loanInfoLists = $loanQuery->get();
                $pawningInfoLists = [];
            } elseif ($category == 3) {
                $saleInfoLists = [];
                $loanInfoLists = [];
                $pawningInfoLists = $pawningQuery->get();
            } else {
                $saleInfoLists = $saleQuery->get();
                $loanInfoLists = $loanQuery->get();
                $pawningInfoLists = $pawningQuery->get();
            }

            // $duetotal= Sale::whereDate('due_date','>=',$end_date)->whereDate('due_date','>=',$start_date)->sum('total_due_amount');
            $currencySymbol = $this->currencySymbol;
            return view('report.collection_report', compact('showTable', 'saleInfoLists', 'loanInfoLists', 'pawningInfoLists', 'oficcers', 'loanOficcer', 'loanType', 'centers', 'groups', 'loanCenter', 'loanGroup', 'loanCollectionType', 'category', 'productCategories', 'product_category', 'start_date', 'end_date', 'currencySymbol'));
        }
    }

    public function pettyCashReportIndex()
    {
        $showTable = 'No';
        $currencySymbol = $this->currencySymbol;
        return view('report.petty_cash_report', compact('showTable', 'currencySymbol'));
    }

    public function pettyCashReportSearch(Request $request)
    {
        if (isset($request->start_date) && isset($request->end_date)) {

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            // dump($start_date);
            // dd($end_date);
            $showTable = 'Yes';

            $pettyCashes = PettyCash::where('date', '<=', $end_date)
                ->where('date', '>=', $start_date)
                ->where('branch_id', \Auth::user()->branchId())
                ->get();
            $currencySymbol = $this->currencySymbol;
            return view('report.petty_cash_report', compact('showTable', 'start_date', 'end_date', 'pettyCashes', 'currencySymbol'));
        }
    }

    public function customerLedgerReportIndex()
    {
        $showTable = 'No';
        $customerId = '';
        $customers = Customer::where('branch_id', \Auth::user()->branchId())->get();
        $currencySymbol = $this->currencySymbol;
        return view('report.customer_ledger_report', compact('showTable', 'customers', 'customerId', 'currencySymbol'));
    }

    public function customerLedgerReportSearch(Request $request)
    {
        $showTable = 'No';
        $customerId = '';
        $customerNic = '';
        $customerInfo = [];
        $saleInfoLists = [];
        $loanInfoLists = [];
        $pawningInfoLists = [];
        $customers = Customer::where('branch_id', \Auth::user()->branchId())->get();

        if ($request->customer_id != '' || $request->nic_no != '') {
            $showTable = 'Yes';
            $query = Customer::where('branch_id', \Auth::user()->branchId());

            if ($request->customer_id != '') {
                $customerId = $request->customer_id;
                $query->where('id', $request->customer_id);
            }

            if ($request->nic_no != '') {
                $customerNic = $request->nic_no;
                $query->where('nic_no', $request->nic_no);
            }
            $customerInfo = $query->first();

            if ($customerInfo) {
                $saleInfoLists = Sale::where('customer_id', $customerInfo->id)->whereNotIn('status', [0])->get();
                $loanInfoLists = Loan::where('customer_id', $customerInfo->id)->whereNotIn('status', [9])->get();
                $pawningInfoLists = Pawning::where('customer_id', $customerInfo->id)->whereNotIn('status', [0])->get();
            }
        }
        $currencySymbol = $this->currencySymbol;
        return view('report.customer_ledger_report', compact('showTable', 'customers', 'customerId', 'customerNic', 'customerInfo', 'saleInfoLists', 'loanInfoLists', 'pawningInfoLists', 'currencySymbol'));
    }

    public function customerLedgerReportSaleDetails($id, $command)
    {
        $sales = Sale::with([
            'guarantor1',
            'guarantor2',
            'saleItems' => function ($query) {
                $query->where('status', 1);
            },
            'installments' => function ($query1) {
                $query1->whereNotIn('status', [0]);
            },
            'receipts' => function ($query2) {
                $query2->where('status', 1)->where('receipt_code', 'NOT LIke', 'DOC%');
            },
        ])->where('id', $id)->first();
        $created = Sale::select('users.id', 'users.name')
            ->leftJoin('users', 'users.id', 'sales.created_by')
            ->where('sales.id', $id)->first();

        $introducer = Sale::select('users.name')
            ->join('users', 'sales.introducer_id', 'users.id')
            ->where('sales.id', $id)
            ->first();
        $currencySymbol = $this->currencySymbol;
        return view('report.models.sales_detail_model', compact('sales', 'created', 'introducer', 'command', 'currencySymbol'));
    }

    public function customerLedgerReportLoanDetails($id, $command)
    {
        $loans = Loan::with([
            'customer',
            'guarantor1',
            'guarantor2',
            'guarantor3',
            'guarantor4',
            'center.branches',
            'group',
            'loanInstallments' => function ($query) {
                $query->whereNotIn('status', [0]);
            },
            'receipts' => function ($query2) {
                $query2->where('status', 1)->where('receipt_code', 'NOT LIke', 'DOC%');
            }
        ])->find($id);
        $created = Loan::select('users.id', 'users.name')
            ->leftJoin('users', 'users.id', 'loans.created_by')
            ->where('loans.id', $id)->first();
        $loanOfficer = Loan::select('users.name')
            ->join('users', 'users.id', 'loans.officer_id')
            ->where('loans.id', $id)->first();

        return view('report.models.loan_detail_model', compact('loans', 'created', 'loanOfficer', 'command'));
    }

    public function customerLedgerReportPawningDetails($id, $command)
    {
        $pawningDetails = Pawning::with('customer')
            ->where('id', $id)->first();
        $pawnedItemDetails = PawnedItem::with('jewellery', 'goldType')
            ->where('pawning_id', $id)
            ->where('status', 1)
            ->get();

        $pawnings = Pawning::with([
            'installments' => function ($query1) {
                $query1->whereNotIn('status', [9]);
            },
            'receipts' => function ($query2) {
                $query2->where('status', 1);
            }
        ])->where('id', $id)->first();

        $created = Pawning::select('users.id', 'users.name')
            ->leftJoin('users', 'users.id', 'pawnings.created_by')
            ->where('pawnings.id', $id)->first();

        $pawningOfficer = Pawning::select('users.name')
            ->join('users', 'users.id', 'pawnings.officer_id')
            ->where('pawnings.id', $id)->first();

        $introducer = Pawning::select('users.name')
            ->join('users', 'users.id', 'pawnings.introducer_id')
            ->where('pawnings.id', $id)->first();
        return view('report.models.pawning_detail_model', compact('pawningDetails', 'pawnedItemDetails', 'pawnings', 'created', 'pawningOfficer', 'introducer', 'command'));
    }

    //item return
    public function itemReturnReportIndex()
    {
        $showTable = 'No';

        // $itemReturns = ItemReturn::where('status', 1)->get();
        return view('report.item_return_report', compact('showTable'));
    }

    public function itemReturnReportSearch(Request $request)
    {

        if (isset($request->start_date) && isset($request->end_date)) {

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            // dump($start_date);
            // dd($end_date);
            $showTable = 'Yes';

            $itemReturns = ItemReturn::where('date', '<=', $end_date)
                ->where('date', '>=', $start_date)
                ->where('branch_id', \Auth::user()->branchId())
                ->where('status', 1)
                ->get();

            return view('report.item_return_report', compact('showTable', 'start_date', 'end_date', 'itemReturns'));
        }
    }


    public function calculateArrearsMonths($lastPaidDate)
    {
        if (!$lastPaidDate) {
            return '--'; // or return the total months since loan started
        }
        $lastPaid = Carbon::parse($lastPaidDate);
        $now = Carbon::now();
        return $lastPaid->diffInMonths($now);
    }

    public function calculateArrearsAmount($lastPaidDate, $monthlyInstallment)
    {
        if (!$lastPaidDate || !$monthlyInstallment) {
            return '--'; // or return the total amount due since loan started
        }
        $arrearsMonths = $this->calculateArrearsMonths($lastPaidDate);
        if ($arrearsMonths === '--') {
            return '--';
        }
        return $arrearsMonths * $monthlyInstallment;
    }

    public function outstandingLoanArrearsReportIndex()
    {
        $showTable = 'No';
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        $introducers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $introducers->prepend('-- All --', '');
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        return view('report.outstanding_loan_arrears_report', compact('showTable', 'oficcers', 'introducers', 'productCategories'));
    }

    public function outstandingLoanArrearsReportSearch(Request $request)
    {
        $oficcers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $oficcers->prepend('-- All --', '');
        $introducers = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $introducers->prepend('-- All --', '');
        $productCategories = ProductCategory::get()->pluck('name', 'id');
        $productCategories->prepend('-- All --', '');
        $end_date = $request->end_date;

        if (isset($request->end_date)) {
            $endDate = date("d", strtotime($end_date));
            $loanOficcer = '';
            $loanIntroducer = '';
            $productCategory = '';
            $showTable = 'Yes';

            $query = Sale::with(['customer', 'products', 'installments', 'lastInstallment' => function ($inQuery) use ($end_date) {
                $inQuery->where('installment_date', '<=', $end_date)->where('paid_date', '<=', $end_date);
            }])
                ->whereDate('date_of_sale', '<=', $end_date)
                ->where('sale_type', 1)
                ->whereIn('status', [2, 3])
                ->where('branch_id', \Auth::user()->branchId());

            if ($request->category_id != "") {
                $productCategory = $request->category_id;
                $query->whereHas('products', function ($childQuery) use ($productCategory) {
                    $childQuery->where('product_category_id', $productCategory);
                });
            }

            if ($request->oficcer_id != "") {
                $loanOficcer = $request->oficcer_id;
                $query->where('officer_id', $request->oficcer_id);
            }

            if ($request->introducer_id != "") {
                $loanIntroducer = $request->introducer_id;
                $query->where('introducer_id', $request->introducer_id);
            }

            $myloanInfoLists = $query->get();

            $loanInfoLists = [];

            foreach ($myloanInfoLists as $loan) {
                $loan->arrears_months = 0;
                $loan->arrears_amount = 0;

                foreach ($loan->installments as $installment) {
                    if ($installment->installment_date < $request->end_date) {
                        if ($installment->status == 2) {
                            $loan->arrears_months += 1;
                            $loan->arrears_amount += $installment->total_amount_to_pay;
                        }
                    }
                }

                if ($loan->arrears_months > 0) {
                    $loanInfoLists[] = $loan;
                }
            }

            return view('report.outstanding_loan_arrears_report', compact('showTable', 'loanInfoLists', 'end_date', 'oficcers', 'loanOficcer', 'introducers', 'loanIntroducer', 'productCategories', 'productCategory'));
        }
    }
}
