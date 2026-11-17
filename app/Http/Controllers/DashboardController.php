<?php

namespace App\Http\Controllers;

use App\BalanceSheet;
use App\BankAccount;
use App\Bill;
use App\Goal;
use App\Invoice;
use App\Order;
use App\Payment;
use App\Plan;
use App\ProductServiceCategory;
use App\ProductServiceUnit;
use App\Projects;
use App\Revenue;
use App\Tax;
use App\PawningInstallment;
use App\LoanInstallment;
use App\Installment;
use App\PaymentVoucher;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {


        if(\Auth::user()->type == 'super admin')
        {
            $user                       = \Auth::user();
            $user['total_user']         = $user->countCompany();
            $user['total_paid_user']    = $user->countPaidCompany();
            $user['total_orders']       = Order::total_orders();
            $user['total_orders_price'] = Order::total_orders_price();
            $user['total_plan']         = Plan::total_plan();
            $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->total : 0);
            $chartData                  = $this->getOrderChart(['duration' => 'week']);

            return view('dashboard.super_admin', compact('user', 'chartData'));
        }
        elseif(\Auth::user()->type == 'Cash Collector' || \Auth::user()->type == 'Branch Manager' || \Auth::user()->type == 'Field Manager' || \Auth::user()->type == 'Center Manager')
        {
            if(\Auth::user()->can('show dashboard'))
            {

                // $data['latestIncome']  = Revenue::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                // $data['latestExpense'] = Payment::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                $data['latestIncome']  = Invoice::with('sales')->where('status', 1)->whereNotNull('inventory_id')->where('branch_id', '=', \Auth::user()->branchId())->orderBy('id', 'desc')->limit(5)->get();
                $data['latestExpense'] = PaymentVoucher::with('supplier', 'cheque')->where('branch_id', \Auth::user()->branchId())->where('status', 2)->orderBy('id', 'DESC')->limit(5)->get();

                $incomeCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 1)->get();
                $inColor        = array();
                $inCategory     = array();
                $inAmount       = array();
                for($i = 0; $i < count($incomeCategory); $i++)
                {
                    $inColor[]    = '#' . $incomeCategory[$i]->color;
                    $inCategory[] = $incomeCategory[$i]->name;
                    $inAmount[]   = $incomeCategory[$i]->incomeCategoryRevenueAmount();
                }


                $data['incomeCategoryColor'] = $inColor;
                $data['incomeCategory']      = $inCategory;
                $data['incomeCatAmount']     = $inAmount;


                $expenseCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 2)->get();
                $exColor         = array();
                $exCategory      = array();
                $exAmount        = array();
                for($i = 0; $i < count($expenseCategory); $i++)
                {
                    $exColor[]    = '#' . $expenseCategory[$i]->color;
                    $exCategory[] = $expenseCategory[$i]->name;
                    $exAmount[]   = $expenseCategory[$i]->expenseCategoryAmount();
                }

                $data['expenseCategoryColor'] = $exColor;
                $data['expenseCategory']      = $exCategory;
                $data['expenseCatAmount']     = $exAmount;

                $data['incExpBarChartData']  = \Auth::user()->getincExpBarChartData();
                $data['incExpLineChartData'] = \Auth::user()->getIncExpLineChartDate();

                $data['currentYear']  = date('Y');
                $data['currentMonth'] = date('M');

                $constant['taxes']         = Tax::where('created_by', \Auth::user()->creatorId())->count();
                $constant['category']      = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->count();
                $constant['units']         = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())->count();
                $constant['bankAccount']   = BankAccount::where('created_by', \Auth::user()->creatorId())->count();
                $data['constant']          = $constant;
                $data['bankAccountDetail'] = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get();
                $data['recentInvoice']     = Invoice::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                $data['weeklyInvoice']     = \Auth::user()->weeklyInvoice();
                $data['monthlyInvoice']    = \Auth::user()->monthlyInvoice();
                $data['recentBill']        = Bill::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                $data['weeklyBill']        = \Auth::user()->weeklyBill();
                $data['monthlyBill']       = \Auth::user()->monthlyBill();
                $data['goals']             = Goal::where('created_by', '=', \Auth::user()->creatorId())->where('is_display', 1)->get();

                //cash callectings
                $today = Carbon::today();

                // pawning
                $pawningDuePaymentCount = PawningInstallment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 0)->count();
                $pawningDuePaymentAmount = PawningInstallment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 0)->sum('installment_amount');
                $pawningOverDuePaymentCount = PawningInstallment::whereDate('installment_date', '<', $today->format('Y-m-d'))->where('status', '=', 2)->count();
                $pawningOverDuePaymentAmount = PawningInstallment::whereDate('installment_date', '<', $today->format('Y-m-d'))->where('status', '=', 2)->sum('installment_amount');

                // loan
                $loanDuePaymentCount = LoanInstallment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 2)->count();
                $loanDuePaymentAmount = LoanInstallment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 2)->sum('amount');
                $loanOverDuePaymentCount = LoanInstallment::whereDate('installment_date', '<', $today->format('Y-m-d'))->where('status', '=', 3)->count();
                $loanOverDuePaymentAmount = LoanInstallment::whereDate('installment_date', '<', $today->format('Y-m-d'))->where('status', '=', 3)->sum('amount');

                // hirepurchase
                $hirePurchaseDuePaymentCount = Installment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 2)->count();
                $hirePurchasenDuePaymentAmount = Installment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 2)->sum('amount');
                $hirePurchaseOverDuePaymentCount = Installment::whereDate('installment_date', '<', $today->format('Y-m-d'))->where('status', '=', 3)->count();
                $hirePurchaseOverDuePaymentAmount = Installment::whereDate('installment_date', '<', $today->format('Y-m-d'))->where('status', '=', 3)->sum('amount');

                // totals
                $data['totalDuePaymentCount'] = $pawningDuePaymentCount + $loanDuePaymentCount + $hirePurchaseDuePaymentCount;
                $data['totalDuePaymentAmount'] = $pawningDuePaymentAmount + $loanDuePaymentAmount + $hirePurchasenDuePaymentAmount;
                $data['totalOverDuePaymentCount'] = $pawningOverDuePaymentCount + $loanOverDuePaymentCount + $hirePurchaseOverDuePaymentCount;
                $data['totalOverDuePaymentAmount'] = $pawningOverDuePaymentAmount + $loanOverDuePaymentAmount + $hirePurchaseOverDuePaymentAmount;

                // installments - Due Today
                $data['pawningTodayInstallments'] = PawningInstallment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 0)->get();
                $data['loanTodayInstallments'] = LoanInstallment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 2)->get();
                $data['hirePurchaseTodayInstallments'] = Installment::whereDate('installment_date', '=', $today->format('Y-m-d'))->where('status', '=', 2)->get();

                // installments - Due Today
                $data['pawningOverDueInstallments'] = PawningInstallment::select('id', 'pawning_id','installment_date',
                                                                        DB::raw('MAX(installment_date) as overdue_date'),
                                                                        DB::raw('COUNT(status) as overdue_installment_count'),
                                                                        DB::raw('SUM(installment_amount) as overdue_amount'))
                                                                        ->with('pawning.customer')
                                                                        ->whereDate('installment_date', '<', $today->format('Y-m-d'))
                                                                        ->where('status', 2)
                                                                        ->where('sent_reminder', 0)
                                                                        ->orderBy('installment_date', 'DESC')
                                                                        ->groupBy('pawning_id')->get();

                $data['loanOverDueInstallments'] = LoanInstallment::select('id', 'loan_id','installment_date',
                                                                    DB::raw('MAX(installment_date) as overdue_date'),
                                                                    DB::raw('COUNT(status) as overdue_installment_count'),
                                                                    DB::raw('SUM(amount) as overdue_amount'))
                                                                    ->with('loans.customer')
                                                                    ->whereDate('installment_date', '<', $today->format('Y-m-d'))
                                                                    ->where('status', 3)
                                                                    ->orderBy('installment_date', 'DESC')
                                                                    ->groupBy('loan_id')->get();

                $data['hirePurchaseOverDueInstallments'] = Installment::select('id', 'sale_id','installment_date',
                                                                        DB::raw('MAX(installment_date) as overdue_date'),
                                                                        DB::raw('COUNT(status) as overdue_installment_count'),
                                                                        DB::raw('SUM(amount) as overdue_amount'))
                                                                        ->with('hirepurchaseDetails.customer')
                                                                        ->whereDate('installment_date', '<', $today->format('Y-m-d'))
                                                                        ->where('status', 3)
                                                                        ->orderBy('installment_date', 'DESC')
                                                                        ->groupBy('sale_id')->get();

            }
            else
            {
                $data = [];
            }

            return view('dashboard.cash_collector', $data);

        }
        else
        {
            if(\Auth::user()->can('show dashboard'))
            {

                // $data['latestIncome']  = Revenue::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                // $data['latestExpense'] = Payment::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();

                $data['latestIncome']  = Invoice::with('sales')->where('status', 1)->whereNotNull('inventory_id')->where('branch_id', '=', \Auth::user()->branchId())->orderBy('id', 'desc')->limit(5)->get();
                $data['latestExpense'] = PaymentVoucher::with('supplier', 'cheque')->where('branch_id', \Auth::user()->branchId())->where('status', 2)->orderBy('id', 'DESC')->limit(5)->get();


                $incomeCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 1)->get();
                $inColor        = array();
                $inCategory     = array();
                $inAmount       = array();
                for($i = 0; $i < count($incomeCategory); $i++)
                {
                    $inColor[]    = '#' . $incomeCategory[$i]->color;
                    $inCategory[] = $incomeCategory[$i]->name;
                    $inAmount[]   = $incomeCategory[$i]->incomeCategoryRevenueAmount();
                }


                $data['incomeCategoryColor'] = $inColor;
                $data['incomeCategory']      = $inCategory;
                $data['incomeCatAmount']     = $inAmount;


                $expenseCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 2)->get();
                $exColor         = array();
                $exCategory      = array();
                $exAmount        = array();
                for($i = 0; $i < count($expenseCategory); $i++)
                {
                    $exColor[]    = '#' . $expenseCategory[$i]->color;
                    $exCategory[] = $expenseCategory[$i]->name;
                    $exAmount[]   = $expenseCategory[$i]->expenseCategoryAmount();
                }

                $data['expenseCategoryColor'] = $exColor;
                $data['expenseCategory']      = $exCategory;
                $data['expenseCatAmount']     = $exAmount;

                $data['incExpBarChartData']  = \Auth::user()->getincExpBarChartData();
                $data['incExpLineChartData'] = \Auth::user()->getIncExpLineChartDate();

                $data['currentYear']  = date('Y');
                $data['currentMonth'] = date('M');

                $constant['taxes']         = Tax::where('created_by', \Auth::user()->creatorId())->count();
                $constant['category']      = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->count();
                $constant['units']         = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())->count();
                $constant['bankAccount']   = BankAccount::where('created_by', \Auth::user()->creatorId())->count();
                $data['constant']          = $constant;
                $data['bankAccountDetail'] = BankAccount::where('branch_id', '=', \Auth::user()->branchId())->get();
                $data['recentInvoice']     = Invoice::with('sales')->where('status', 1)->whereNotNull('inventory_id')->where('branch_id', '=', \Auth::user()->branchId())->orderBy('id', 'desc')->limit(5)->get();
                $data['weeklyInvoice']     = \Auth::user()->weeklyInvoice();
                $data['monthlyInvoice']    = \Auth::user()->monthlyInvoice();
                $data['recentBill']        = PaymentVoucher::where('branch_id', '=', \Auth::user()->branchId())->where('status', '!=', 0)->orderBy('id', 'desc')->limit(5)->get();
                $data['weeklyBill']        = \Auth::user()->weeklyBill();
                $data['monthlyBill']       = \Auth::user()->monthlyBill();
                $data['goals']             = Goal::where('created_by', '=', \Auth::user()->creatorId())->where('is_display', 1)->get();

            }
            else
            {
                $data = [];
            }

            return view('dashboard.index', $data);

        }


    }

    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if($arrParam['duration'])
        {
            if($arrParam['duration'] == 'week')
            {
                $previous_week = strtotime("-2 week +1 day");
                for($i = 0; $i < 14; $i++)
                {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach($arrDuration as $date => $label)
        {

            $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }
}

