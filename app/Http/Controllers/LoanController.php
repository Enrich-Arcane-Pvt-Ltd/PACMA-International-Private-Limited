<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\Bill;
use App\Customer;
use App\Invoice;
use App\ProductService;
use App\ProductServiceCategory;
use App\TimePeriod;
use App\InterestRate;
use App\DocumentChargeRate;
use App\Loan;
use App\LoanInstallment;
use App\FineRate;
use App\Receipt;
use App\Center;
use App\Group;
use App\GroupMember;
use App\BusinessCategory;
use App\User;
use App\BankAccount;
use App\CardInterestExpense;
use App\GeneralLedgerAccount;
use App\FineCenterCommand;
use App\FineChildCommand;
use App\FineCommand;
use App\PaymentVoucher;
use App\Utility;
use App\WaittingLoanInstallment;
use App\LoanPackage;
use App\SMS;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Holiday;
use App\LoanInstallmentAdvance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\json_decode;

class LoanController extends Controller
{
    public function index()
    {
        return view('loans.index');
    }

    public function create()
    {
        if(\Auth::user()->can('create loan')){
            $customers = Customer::where('status', 1)->whereIn('type', [0,2])->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->whereIn('type', [1,2])->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');
            return view('loans.create', compact('customers', 'guarantors', 'timeperiods'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function calDueDate(Request $request){
        // currentDate: currentDate,
        // timePeriod: 2 months, 3 weeks, 1 days
        // timeRange - monthly 3, weekly 2, daily 1
        // installmentDate = day of the month

        $installmentDate = date('Y-m', time()) . '-' . $request->installmentDate;
        $dueDate = date('Y-m', time());
        if($request->timeRange == 3){
            $dueDate = date('Y-m-d', strtotime($installmentDate. ' + '.$request->timePeriod.' months'));
            if(Holiday::isHoliday($dueDate)){
                $dueDate = Holiday::getNextWorkingDay($dueDate);
            }
        }

        if($request->timeRange == 2){
            $dueDate =  Holiday::getWeeklyDueDate($installmentDate, $request->timePeriod);
        }


        if($request->timeRange == 1){
            // i need to get due date without holidays related to time period
            $dueDate =  Holiday::getDailyDueDate($installmentDate, $request->timePeriod);
        }

        return response()->json($dueDate, 200);
    }

    // Indexes
    public function smeLoan(){
        if(\Auth::user()->can('manage loan')){
            $pendingLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 1)->where('status', 1)->orderBy('id', 'DESC')->get();
            $checkedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 1)->where('status', 4)->orderBy('id', 'DESC')->get();
            $grantedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 1)->where('status', 5)->orderBy('id', 'DESC')->get();
            $disbursedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 1)->where('status', 2)->orderBy('id', 'DESC')->get();
            $rejectedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 1)->where('status', 0)->orderBy('id', 'DESC')->get();
            $closedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 1)->where('status', 3)->orderBy('id', 'DESC')->get();
            return view('loans.sme_loans', compact('pendingLoan', 'checkedLoan', 'grantedLoan', 'disbursedLoan', 'rejectedLoan', 'closedLoan'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function groupLoan(){
        if(\Auth::user()->can('manage loan')){
            $pendingLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4', 'center.branches', 'group')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 2)->where('status', 1)->orderBy('id', 'DESC')->get();
            $checkedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4', 'center.branches', 'group')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 2)->where('status', 4)->orderBy('id', 'DESC')->get();
            $grantedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4', 'center.branches', 'group')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 2)->where('status', 5)->orderBy('id', 'DESC')->get();
            $disbursedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4', 'center.branches', 'group')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 2)->where('status', 2)->orderBy('id', 'DESC')->paginate(500);
            $rejectedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4', 'center.branches', 'group')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 2)->where('status', 0)->orderBy('id', 'DESC')->get();
            $closedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4', 'center.branches', 'group')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 2)->where('status', 3)->orderBy('id', 'DESC')->get();
            return view('loans.group_loans', compact('pendingLoan', 'checkedLoan', 'grantedLoan', 'disbursedLoan', 'rejectedLoan', 'closedLoan'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vehicleLoan(){
        if(\Auth::user()->can('manage loan')){
            $pendingLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 3)->where('status', 1)->orderBy('id', 'DESC')->get();
            $checkedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 3)->where('status', 4)->orderBy('id', 'DESC')->get();
            $grantedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 3)->where('status', 5)->orderBy('id', 'DESC')->get();
            $disbursedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 3)->where('status', 2)->orderBy('id', 'DESC')->get();
            $rejectedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 3)->where('status', 0)->orderBy('id', 'DESC')->get();
            $closedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 3)->where('status', 3)->orderBy('id', 'DESC')->get();
            return view('loans.vehicle_loans', compact('pendingLoan', 'checkedLoan', 'grantedLoan', 'disbursedLoan', 'rejectedLoan', 'closedLoan'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function landLoan(){
        if(\Auth::user()->can('manage loan')){
            $pendingLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 4)->where('status', 1)->orderBy('id', 'DESC')->get();
            $checkedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 4)->where('status', 4)->orderBy('id', 'DESC')->get();
            $grantedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 4)->where('status', 5)->orderBy('id', 'DESC')->get();
            $disbursedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 4)->where('status', 2)->orderBy('id', 'DESC')->get();
            $rejectedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 4)->where('status', 0)->orderBy('id', 'DESC')->get();
            $closedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 4)->where('status', 3)->orderBy('id', 'DESC')->get();
            return view('loans.land_loans', compact('pendingLoan', 'checkedLoan', 'grantedLoan', 'disbursedLoan', 'rejectedLoan', 'closedLoan'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobLoan(){
        if(\Auth::user()->can('manage loan')){
            $pendingLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 5)->where('status', 1)->orderBy('id', 'DESC')->get();
            $checkedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 5)->where('status', 4)->orderBy('id', 'DESC')->get();
            $grantedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 5)->where('status', 5)->orderBy('id', 'DESC')->get();
            $disbursedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 5)->where('status', 2)->orderBy('id', 'DESC')->get();
            $rejectedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 5)->where('status', 0)->orderBy('id', 'DESC')->get();
            $closedLoan = Loan::with('customer', 'guarantor_1', 'guarantor_2', 'guarantor_3', 'guarantor_4')->where('branch_id', \Auth::user()->branchId())->where('loan_type', 5)->where('status', 3)->orderBy('id', 'DESC')->get();
            return view('loans.job_loans', compact('pendingLoan', 'checkedLoan', 'grantedLoan', 'disbursedLoan', 'rejectedLoan', 'closedLoan'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Create Pages
    public function smeLoanCreate(){
        if(\Auth::user()->can('create loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()
                        ->mapWithKeys(function ($item) {
                            return [$item->id => $item->nic_no . ' - ' . $item->name];
                        });
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()
                        ->mapWithKeys(function ($item) {
                            return [$item->id => $item->nic_no . ' - ' . $item->name];
                        });
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $packages = LoanPackage::where('branch_id', \Auth::user()->branchId())->where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $packages->prepend('-- Loan Package --', '');

            $interestRate = InterestRate::where('id', 2)->first();

            $last_loan_id = Loan::max('id') + 1;

            $years = array('2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010',
                      '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020',
                      '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030');


            return view('loans.sme_loan_create', compact('customers', 'guarantors', 'timeperiods', 'busiCategories', 'interestRate', 'users', 'packages', 'last_loan_id', 'years'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function groupLoanCreate(){
        if(\Auth::user()->can('create loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $centers = Center::where('status', 1)->get()->pluck('name', 'id');
            $centers->prepend('-- Center --', '');

            $groups = Group::where('status', 1)->get()->pluck('name', 'id');
            $groups->prepend('-- Group --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $interestRate = InterestRate::where('id', 3)->first();

            $last_loan_id = Loan::max('id') + 1;

            $my_years = array('2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010',
                      '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020',
                      '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030');

            return view('loans.group_loan_create', compact('customers', 'guarantors', 'timeperiods', 'interestRate', 'centers', 'groups', 'users', 'busiCategories', 'last_loan_id', 'my_years'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vehicleLoanCreate(){
        if(\Auth::user()->can('create loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $interestRate = InterestRate::where('id', 5)->first();
            return view('loans.vehicle_loan_create', compact('customers', 'guarantors', 'timeperiods', 'interestRate', 'users', 'busiCategories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function landLoanCreate(){
        if(\Auth::user()->can('create loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $interestRate = InterestRate::where('id', 6)->first();
            return view('loans.land_loan_create', compact('customers', 'guarantors', 'timeperiods', 'interestRate', 'users', 'busiCategories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobLoanCreate(){
        if(\Auth::user()->can('create loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $interestRate = InterestRate::where('id', 7)->first();
            return view('loans.job_loan_create', compact('customers', 'guarantors', 'timeperiods', 'busiCategories', 'interestRate', 'users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Edit Pages
    public function loanEdit($id){
        if(\Auth::user()->can('edit loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()
                        ->mapWithKeys(function ($item) {
                            return [$item->id => $item->nic_no . ' - ' . $item->name];
                        });
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()
                        ->mapWithKeys(function ($item) {
                            return [$item->id => $item->nic_no . ' - ' . $item->name];
                        });
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $packages = LoanPackage::where('branch_id', \Auth::user()->branchId())->where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $packages->prepend('-- Loan Package --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $loan = Loan::find($id);
            if($loan->loan_type == 1){
                $interestRate = InterestRate::where('id', 2)->first();
            } else if($loan->loan_type == 3){
                $interestRate = InterestRate::where('id', 5)->first();
            } else if($loan->loan_type == 4){
                $interestRate = InterestRate::where('id', 6)->first();
            } else if($loan->loan_type == 5){
                $interestRate = InterestRate::where('id', 7)->first();
            }

            $years = array('2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010',
                      '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020',
                      '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030');

            return view('loans.loan_edit', compact('loan', 'customers', 'guarantors', 'timeperiods', 'interestRate', 'users', 'busiCategories', 'packages', 'years'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function groupLoanEdit($id){
        if(\Auth::user()->can('edit loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $centers = Center::where('status', 1)->get()->pluck('name', 'id');
            $centers->prepend('-- Center --', '');

            $groups = Group::where('status', 1)->get()->pluck('name', 'id');
            $groups->prepend('-- Group --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $interestRate = InterestRate::where('id', 3)->first();
            $loan = Loan::find($id);
            return view('loans.group_loan_edit', compact('loan', 'customers', 'guarantors', 'timeperiods', 'interestRate', 'centers', 'groups', 'users', 'busiCategories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Garanter Searches
    public function guarantor1Search($id){
        $guarantors = Customer::select('id', 'name', 'nic_no')->where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->whereNotIn('id', [$id])->orderBy('name')->get();
        return json_encode($guarantors);
    }
    public function guarantor2Search($id1, $id2){
        $guarantors = Customer::select('id', 'name', 'nic_no')->where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->whereNotIn('id', [$id1, $id2])->orderBy('name')->get();
        return json_encode($guarantors);
    }
    public function guarantor3Search($id1, $id2, $id3){
        $guarantors = Customer::select('id', 'name', 'nic_no')->where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->whereNotIn('id', [$id1, $id2, $id3])->orderBy('name')->get();
        return json_encode($guarantors);
    }

    public function docChargeRate(){
        $docCharge = DocumentChargeRate::first();
        return json_encode($docCharge);
    }

    // Save Functions
    public function smeLoanSave(Request $request){
        if(\Auth::user()->can('create loan')){

            $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                                    'customer' => 'required',
                                    // 'reason' => 'required',
                                    // 'guarantor_1' => 'required',
                                    'business_category' => 'required',
                                    'officer_id' => 'required',
                                    // 'guarantor_2' => 'required',
                                    'loan_amount' => 'required',
                                    'interest_rate' => 'required',
                                    // 'time_period' => 'required',
                                    'interest_amount' => 'required',
                                    'total_amount' => 'required',
                                    'time_range' => 'required',
                                    'installment_date' => 'required',
                                    'due_date' => 'required',
                                    'doc_type' => 'required',
                                    'document_charges' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if( $request->time_range == 1){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'daily_installment' => 'required',
                                        'daily_time_period' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 2){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'weekly_installment' => 'required',
                                        'weekly_time_period' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 3){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'monthly_installment' => 'required',
                                        'time_period' => 'required',
                                    ]
                );
            }

            if($validator2->fails()){
                $messages = $validator2->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // $maxId = Invoice::max('id');

                if($request->has('application_document')){
                    $applicationDocumnet = self::uploadApplicationDocumnet($request);
                }
                if($request->has('other_document')){
                    $otherDocumnet = self::uploadOtherDocumnet($request, 'other_document');
                }
                if($request->has('other_document2')){
                    $otherDocumnet2 = self::uploadOtherDocumnet($request, 'other_document2');
                }
                if($request->has('other_document3')){
                    $otherDocumnet3 = self::uploadOtherDocumnet($request, 'other_document3');
                }

                // $invoiceCode = "SMEL".str_pad(($maxId+1), 6, "0", STR_PAD_LEFT);
                // $invoice = new Invoice();
                // $invoice->branch_id = \Auth::user()->branchId();
                // $invoice->invoice_code = $invoiceCode;
                // $invoice->customer_id = $request->customer;
                // $invoice->guarantor_id = $request->guarantor_1;
                // $invoice->issue_date = $request->date;
                // $invoice->due_date = $request->due_date;
                // $invoice->status = 1;
                // $invoice->created_by = \Auth::user()->id;
                // $invoice->save();

                // $invoiceId = $maxId+1;

            $loan = new Loan();
            $loan->branch_id = \Auth::user()->branchId();
            // $loan->invoice_id = $invoiceId;
            $loan->customer_id = $request->customer;
            $loan->business_category_id = $request->business_category;
            $loan->officer_id = $request->officer_id;
            $loan->date = $request->date;
            $loan->guarantor_1 = $request->guarantor_1;
            if($request->guarantor_2 != ''){
                $loan->guarantor_2 = $request->guarantor_2;
            }
            if($request->guarantor_3 != ''){
                $loan->guarantor_3 = $request->guarantor_3;
            }
            if($request->guarantor_4 != ''){
                $loan->guarantor_4 = $request->guarantor_4;
            }

            $loan->person_1_name = $request->person_1_name;
            $loan->person_1_phone = $request->person_1_phone;
            $loan->person_2_name = $request->person_2_name;
            $loan->person_2_phone = $request->person_2_phone;

            $loan->loan_amount = $request->loan_amount;
            $loan->reason = $request->reason;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_amount = $request->interest_amount;
            $loan->total_amount = $request->total_amount;
            $loan->current_due_amount = $request->total_amount;

            $loan->reg_officer_code = $request->reg_officer_code;
            $loan->reg_year_code = $request->reg_year_code;
            $loan->reg_loan_code = $request->reg_loan_code;

            $loan->time_range = $request->time_range;
            if($request->time_range == 1){
                $loan->daily_time_period = $request->daily_time_period;
                $loan->daily_installment = $request->daily_installment;
                $loan->loan_package_id = $request->loan_package;
            }
            if($request->time_range == 2){
                $loan->time_period = $request->weekly_time_period;
                $loan->weekly_installment = $request->weekly_installment;
            }
            if($request->time_range == 3){
                $loan->time_period = $request->time_period;
                $loan->monthly_installment = $request->monthly_installment;
            }

            $loan->installment_date = $request->installment_date;
            $loan->due_date = $request->due_date;
            $loan->doc_type = $request->doc_type;
            $loan->document_charges = $request->document_charges;
            $loan->document_charges_in_words = $request->document_charges_in_words;
            if($request->has('application_document')){
                $loan->application_document = $applicationDocumnet;
            }
            if($request->has('other_document')){
                $loan->other_document = $otherDocumnet;
            }
            if($request->has('other_document2')){
                $loan->other_document2 = $otherDocumnet2;
            }
            if($request->has('other_document3')){
                $loan->other_document3 = $otherDocumnet3;
            }
            $loan->loan_type = 1;
            $loan->status = 1;
            $loan->created_by = \Auth::user()->id;
            $loan->save();

            // $recMaxId = Receipt::max('id');
            // $receiptCode = "DOC".str_pad(($recMaxId+1), 6, "0", STR_PAD_LEFT);

            // $receipts = new Receipt();
            // $receipts->branch_id = \Auth::user()->branchId();
            // $receipts->receipt_code = $receiptCode;
            // $receipts->customer_id = $request->customer;
            // // $receipts->invoice_id = $invoiceId;
            // $receipts->issue_date = $request->date;
            // $receipts->installment_due_date = $request->date;
            // $receipts->status = 1;
            // $receipts->total_amount_to_pay = $request->document_charges;
            // $receipts->paid_amount = $request->document_charges;
            // $receipts->paid_amount_in_words = $request->document_charges_in_words;
            // $receipts->payment_method = 'ONLINEPAYMENT';
            // $receipts->description = 'Document Charges';
            // $receipts->created_by = \Auth::user()->id;
            // $receipts->save();

            // $loanId = Loan::max('id');

            // $dateofSale = explode('-',$request->date);
            // $sale_yr = $dateofSale[0];
            // $sale_mn = $dateofSale[1];
            // $sale_day = $request->installment_date;

            // $installment_date = Carbon::create($sale_yr, $sale_mn, $sale_day);

            // for ($i=0; $i < $request->time_period ; $i++) {
            //     $newInstallmentDate = $installment_date->addMonth(1)->format('Y-m-d');

            //     $installment = new LoanInstallment();
            //     $installment->branch_id = \Auth::user()->branchId();
            //     $installment->loan_id = $loanId;
            //     $installment->amount = $request->monthly_installment;
            //     $installment->total_amount_to_pay = $request->monthly_installment;
            //     $installment->status = 2;
            //     $installment->installment_date = $newInstallmentDate;
            //     $installment->invoice_id = $invoiceId;
            //     $installment->save();
            // }

            return redirect()->back()->with('success', __('Loan successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function groupLoanSave(Request $request){
        if(\Auth::user()->can('create loan')){

            $validator = \Validator::make(
                $request->all(), [
                    'date' => 'required',
                    'center' => 'required',
                    'group' => 'required',
                    'customer' => 'required',
                    'business_category' => 'required',
                    'officer_id' => 'required',
                    // 'reason' => 'required',
                    // 'guarantor_1' => 'required',
                    // 'guarantor_2' => 'required',
                    'loan_amount' => 'required',
                    'interest_rate' => 'required',
                    'time_period' => 'required',
                    'interest_amount' => 'required',
                    'total_amount' => 'required',
                    // 'monthly_installment' => 'required',
                    'time_range' => 'required',
                    'installment_date' => 'required',
                    'due_date' => 'required',
                    'doc_type' => 'required',
                    'document_charges' => 'required',
                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if( $request->time_range == 1){
                $validator2 = \Validator::make(
                    $request->all(), [
                        'daily_installment' => 'required',
                    ]
                );
            } elseif( $request->time_range == 2){
                $validator2 = \Validator::make(
                    $request->all(), [
                        'weekly_installment' => 'required',
                    ]
                );
            } elseif( $request->time_range == 3){
                $validator2 = \Validator::make(
                    $request->all(), [
                        'monthly_installment' => 'required',
                    ]
                );
            }

            if($validator2->fails()){
                $messages = $validator2->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $checkCustomerLoans = Loan::where('customer_id', $request->customer)->whereIn('status', [1,2,4])->get();
            if(count($checkCustomerLoans) > 0){
                foreach($checkCustomerLoans as $checkCustomerLoan){
                    $loanNumberFormat = \Auth::user()->loanNumberFormat($checkCustomerLoan->id);
                    return redirect()->back()->with('error', 'Customer Has Another Loan AS '.$loanNumberFormat.' !');
                }
                return redirect()->back()->with('error', 'Customer Has Another Loan!');
            }

            if($request->has('application_document')){
                $applicationDocumnet = self::uploadApplicationDocumnet($request);
            }

            if($request->has('other_document')){
                $otherDocumnet = self::uploadOtherDocumnet($request, 'other_document');
            }

            // $invoiceCode = "GRPL".str_pad(($maxId+1), 6, "0", STR_PAD_LEFT);
            // $invoice = new Invoice();
            // $invoice->branch_id = \Auth::user()->branchId();
            // $invoice->invoice_code = $invoiceCode;
            // $invoice->customer_id = $request->customer;
            // $invoice->guarantor_id = $request->guarantor_1;
            // $invoice->issue_date = $request->date;
            // $invoice->due_date = $request->due_date;
            // $invoice->status = 1;
            // $invoice->created_by = \Auth::user()->id;
            // $invoice->save();

            // $invoiceId = $maxId+1;

            $loan = new Loan();
            $loan->branch_id = \Auth::user()->branchId();
            $loan->center_id = $request->center;
            $loan->group_id = $request->group;
            $loan->customer_id = $request->customer;
            $loan->business_category_id = $request->business_category;
            $loan->officer_id = $request->officer_id;
            $loan->date = $request->date;
            $loan->guarantor_1 = $request->guarantor_1;
            $loan->guarantor_2 = $request->guarantor_2;

            $loan->guardian_name = $request->guardian_name;
            $loan->guardian_nic_no = $request->guardian_nic_no;
            $loan->guardian_address = $request->guardian_address;
            $loan->guardian_phone_no = $request->guardian_phone_no;

            $loan->person_1_name = $request->person_1_name;
            $loan->person_1_phone = $request->person_1_phone;
            $loan->person_2_name = $request->person_2_name;
            $loan->person_2_phone = $request->person_2_phone;

            if($request->guarantor_3 != ''){
                $loan->guarantor_3 = $request->guarantor_3;
            }
            if($request->guarantor_4 != ''){
                $loan->guarantor_4 = $request->guarantor_4;
            }
            $loan->loan_amount = $request->loan_amount;
            $loan->reason = $request->reason;
            $loan->time_period = $request->time_period;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_amount = $request->interest_amount;
            $loan->total_amount = $request->total_amount;
            $loan->current_due_amount = $request->total_amount;

            $loan->time_range = $request->time_range;
            if($request->time_range == 1){
                $loan->daily_installment = $request->daily_installment;
            }
            if($request->time_range == 2){
                $loan->weekly_installment = $request->weekly_installment;
            }
            if($request->time_range == 3){
                $loan->monthly_installment = $request->monthly_installment;
            }

            $loan->installment_date = $request->installment_date;
            $loan->due_date = $request->due_date;
            $loan->doc_type = $request->doc_type;
            $loan->document_charges = $request->document_charges;
            $loan->document_charges_in_words = $request->document_charges_in_words;
            if($request->has('application_document')){
                $loan->application_document = $applicationDocumnet;
            }
            if($request->has('other_document')){
                $loan->other_document = $otherDocumnet;
            }
            $loan->loan_type = 2;
            $loan->status = 1;
            $loan->created_by = \Auth::user()->id;
            $loan->save();

            // $recMaxId = Receipt::max('id');
            // $receiptCode = "DOC".str_pad(($recMaxId+1), 6, "0", STR_PAD_LEFT);

            // $receipts = new Receipt();
            // $receipts->branch_id = \Auth::user()->branchId();
            // $receipts->receipt_code = $receiptCode;
            // $receipts->customer_id = $request->customer;
            // $receipts->invoice_id = $invoiceId;
            // $receipts->issue_date = $request->date;
            // $receipts->installment_due_date = $request->date;
            // $receipts->status = 1;
            // $receipts->total_amount_to_pay = $request->document_charges;
            // $receipts->paid_amount = $request->document_charges;
            // $receipts->paid_amount_in_words = $request->document_charges_in_words;
            // $receipts->payment_method = 'ONLINEPAYMENT';
            // $receipts->description = 'Document Charges';
            // $receipts->created_by = \Auth::user()->id;
            // $receipts->save();
            // $loanId = Loan::max('id');

            // $dateofSale = explode('-',$request->date);
            // $sale_yr = $dateofSale[0];
            // $sale_mn = $dateofSale[1];
            // $sale_day = $request->installment_date;

            // $installment_date = Carbon::create($sale_yr, $sale_mn, $sale_day);

            // for ($i=0; $i < $request->time_period ; $i++) {
            //     $newInstallmentDate = $installment_date->addMonth(1)->format('Y-m-d');

            //     $installment = new LoanInstallment();
            //     $installment->branch_id = \Auth::user()->branchId();
            //     $installment->loan_id = $loanId;
            //     $installment->amount = $request->monthly_installment;
            //     $installment->total_amount_to_pay = $request->monthly_installment;
            //     $installment->status = 2;
            //     $installment->installment_date = $newInstallmentDate;
            //     $installment->invoice_id = $invoiceId;
            //     $installment->save();
            // }

            return redirect()->back()->with('success', __('Loan successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vehicleLoanSave(Request $request){
        if(\Auth::user()->can('create loan')){

                $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                                    'customer' => 'required',
                                    // 'reason' => 'required',
                                    // 'guarantor_1' => 'required',
                                    'business_category' => 'required',
                                    'officer_id' => 'required',
                                    // 'guarantor_2' => 'required',
                                    'loan_amount' => 'required',
                                    'interest_rate' => 'required',
                                    'time_period' => 'required',
                                    'interest_amount' => 'required',
                                    'total_amount' => 'required',
                                    // 'monthly_installment' => 'required',
                                    'installment_date' => 'required',
                                    'due_date' => 'required',
                                    'doc_type' => 'required',
                                    'document_charges' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if( $request->time_range == 1){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'daily_installment' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 2){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'weekly_installment' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 3){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'monthly_installment' => 'required',
                                    ]
                );
            }

            if($validator2->fails()){
                $messages = $validator2->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // $maxId = Invoice::max('id');

                if($request->has('application_document')){
                    $applicationDocumnet = self::uploadApplicationDocumnet($request);
                }
                if($request->has('other_document')){
                    $otherDocumnet = self::uploadOtherDocumnet($request);
                }

                // $invoiceCode = "VCLL".str_pad(($maxId+1), 6, "0", STR_PAD_LEFT);
                // $invoice = new Invoice();
                // $invoice->branch_id = \Auth::user()->branchId();
                // $invoice->invoice_code = $invoiceCode;
                // $invoice->customer_id = $request->customer;
                // $invoice->guarantor_id = $request->guarantor_1;
                // $invoice->issue_date = $request->date;
                // $invoice->due_date = $request->due_date;
                // $invoice->status = 1;
                // $invoice->created_by = \Auth::user()->id;
                // $invoice->save();

                // $invoiceId = $maxId+1;

            $loan = new Loan();
            $loan->branch_id = \Auth::user()->branchId();
            // $loan->invoice_id = $invoiceId;
            $loan->customer_id = $request->customer;
            $loan->business_category_id = $request->business_category;
            $loan->officer_id = $request->officer_id;
            $loan->date = $request->date;
            $loan->guarantor_1 = $request->guarantor_1;
            if($request->guarantor_2 != ''){
                $loan->guarantor_2 = $request->guarantor_2;
            }
            if($request->guarantor_3 != ''){
                $loan->guarantor_3 = $request->guarantor_3;
            }
            if($request->guarantor_4 != ''){
                $loan->guarantor_4 = $request->guarantor_4;
            }

            $loan->person_1_name = $request->person_1_name;
            $loan->person_1_phone = $request->person_1_phone;
            $loan->person_2_name = $request->person_2_name;
            $loan->person_2_phone = $request->person_2_phone;

            $loan->loan_amount = $request->loan_amount;
            $loan->reason = $request->reason;
            $loan->time_period = $request->time_period;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_amount = $request->interest_amount;
            $loan->total_amount = $request->total_amount;
            $loan->current_due_amount = $request->total_amount;

            $loan->time_range = $request->time_range;
            if($request->time_range == 1){
                $loan->daily_installment = $request->daily_installment;
            }
            if($request->time_range == 2){
                $loan->weekly_installment = $request->weekly_installment;
            }
            if($request->time_range == 3){
                $loan->monthly_installment = $request->monthly_installment;
            }

            $loan->installment_date = $request->installment_date;
            $loan->due_date = $request->due_date;
            $loan->doc_type = $request->doc_type;
            $loan->document_charges = $request->document_charges;
            $loan->document_charges_in_words = $request->document_charges_in_words;
            if($request->has('application_document')){
                $loan->application_document = $applicationDocumnet;
            }
            if($request->has('other_document')){
                $loan->other_document = $otherDocumnet;
            }
            $loan->loan_type = 3;
            $loan->status = 1;
            $loan->created_by = \Auth::user()->id;
            $loan->save();

            // $recMaxId = Receipt::max('id');
            // $receiptCode = "DOC".str_pad(($recMaxId+1), 6, "0", STR_PAD_LEFT);

            // $receipts = new Receipt();
            // $receipts->branch_id = \Auth::user()->branchId();
            // $receipts->receipt_code = $receiptCode;
            // $receipts->customer_id = $request->customer;
            // $receipts->invoice_id = $invoiceId;
            // $receipts->issue_date = $request->date;
            // $receipts->installment_due_date = $request->date;
            // $receipts->status = 1;
            // $receipts->total_amount_to_pay = $request->document_charges;
            // $receipts->paid_amount = $request->document_charges;
            // $receipts->paid_amount_in_words = $request->document_charges_in_words;
            // $receipts->payment_method = 'ONLINEPAYMENT';
            // $receipts->description = 'Document Charges';
            // $receipts->created_by = \Auth::user()->id;
            // $receipts->save();

            // $loanId = Loan::max('id');

            // $dateofSale = explode('-',$request->date);
            // $sale_yr = $dateofSale[0];
            // $sale_mn = $dateofSale[1];
            // $sale_day = $request->installment_date;

            // $installment_date = Carbon::create($sale_yr, $sale_mn, $sale_day);

            // for ($i=0; $i < $request->time_period ; $i++) {
            //     $newInstallmentDate = $installment_date->addMonth(1)->format('Y-m-d');

            //     $installment = new LoanInstallment();
            //     $installment->branch_id = \Auth::user()->branchId();
            //     $installment->loan_id = $loanId;
            //     $installment->amount = $request->monthly_installment;
            //     $installment->total_amount_to_pay = $request->monthly_installment;
            //     $installment->status = 2;
            //     $installment->installment_date = $newInstallmentDate;
            //     $installment->invoice_id = $invoiceId;
            //     $installment->save();
            // }

            return redirect()->back()->with('success', __('Loan successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function landLoanSave(Request $request){
        if(\Auth::user()->can('create loan')){

            $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                                    'customer' => 'required',
                                    // 'reason' => 'required',
                                    // 'guarantor_1' => 'required',
                                    'business_category' => 'required',
                                    'officer_id' => 'required',
                                    // 'guarantor_2' => 'required',
                                    'loan_amount' => 'required',
                                    'interest_rate' => 'required',
                                    'time_period' => 'required',
                                    'interest_amount' => 'required',
                                    'total_amount' => 'required',
                                    'time_range' => 'required',
                                    // 'monthly_installment' => 'required',
                                    'installment_date' => 'required',
                                    'due_date' => 'required',
                                    'doc_type' => 'required',
                                    'document_charges' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if( $request->time_range == 1){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'daily_installment' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 2){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'weekly_installment' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 3){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'monthly_installment' => 'required',
                                    ]
                );
            }

            if($validator2->fails()){
                $messages = $validator2->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // $maxId = Invoice::max('id');

                if($request->has('application_document')){
                    $applicationDocumnet = self::uploadApplicationDocumnet($request);
                }
                if($request->has('other_document')){
                    $otherDocumnet = self::uploadOtherDocumnet($request);
                }

                // $invoiceCode = "LNDL".str_pad(($maxId+1), 6, "0", STR_PAD_LEFT);
                // $invoice = new Invoice();
                // $invoice->branch_id = \Auth::user()->branchId();
                // $invoice->invoice_code = $invoiceCode;
                // $invoice->customer_id = $request->customer;
                // $invoice->guarantor_id = $request->guarantor_1;
                // $invoice->issue_date = $request->date;
                // $invoice->due_date = $request->due_date;
                // $invoice->status = 1;
                // $invoice->created_by = \Auth::user()->id;
                // $invoice->save();

                // $invoiceId = $maxId+1;

            $loan = new Loan();
            $loan->branch_id = \Auth::user()->branchId();
            // $loan->invoice_id = $invoiceId;
            $loan->customer_id = $request->customer;
            $loan->business_category_id = $request->business_category;
            $loan->officer_id = $request->officer_id;
            $loan->date = $request->date;
            $loan->guarantor_1 = $request->guarantor_1;
            if($request->guarantor_2 != ''){
                $loan->guarantor_2 = $request->guarantor_2;
            }
            if($request->guarantor_3 != ''){
                $loan->guarantor_3 = $request->guarantor_3;
            }
            if($request->guarantor_4 != ''){
                $loan->guarantor_4 = $request->guarantor_4;
            }

            $loan->person_1_name = $request->person_1_name;
            $loan->person_1_phone = $request->person_1_phone;
            $loan->person_2_name = $request->person_2_name;
            $loan->person_2_phone = $request->person_2_phone;

            $loan->loan_amount = $request->loan_amount;
            $loan->reason = $request->reason;
            $loan->time_period = $request->time_period;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_amount = $request->interest_amount;
            $loan->total_amount = $request->total_amount;
            $loan->current_due_amount = $request->total_amount;

            $loan->time_range = $request->time_range;
            if($request->time_range == 1){
                $loan->daily_installment = $request->daily_installment;
            }
            if($request->time_range == 2){
                $loan->weekly_installment = $request->weekly_installment;
            }
            if($request->time_range == 3){
                $loan->monthly_installment = $request->monthly_installment;
            }

            $loan->installment_date = $request->installment_date;
            $loan->due_date = $request->due_date;
            $loan->doc_type = $request->doc_type;
            $loan->document_charges = $request->document_charges;
            $loan->document_charges_in_words = $request->document_charges_in_words;
            if($request->has('application_document')){
                $loan->application_document = $applicationDocumnet;
            }
            if($request->has('other_document')){
                $loan->other_document = $otherDocumnet;
            }
            $loan->loan_type = 4;
            $loan->status = 1;
            $loan->created_by = \Auth::user()->id;
            $loan->save();

            // $recMaxId = Receipt::max('id');
            // $receiptCode = "DOC".str_pad(($recMaxId+1), 6, "0", STR_PAD_LEFT);

            // $receipts = new Receipt();
            // $receipts->branch_id = \Auth::user()->branchId();
            // $receipts->receipt_code = $receiptCode;
            // $receipts->customer_id = $request->customer;
            // $receipts->invoice_id = $invoiceId;
            // $receipts->issue_date = $request->date;
            // $receipts->installment_due_date = $request->date;
            // $receipts->status = 1;
            // $receipts->total_amount_to_pay = $request->document_charges;
            // $receipts->paid_amount = $request->document_charges;
            // $receipts->paid_amount_in_words = $request->document_charges_in_words;
            // $receipts->payment_method = 'ONLINEPAYMENT';
            // $receipts->description = 'Document Charges';
            // $receipts->created_by = \Auth::user()->id;
            // $receipts->save();

            // $loanId = Loan::max('id');

            // $dateofSale = explode('-',$request->date);
            // $sale_yr = $dateofSale[0];
            // $sale_mn = $dateofSale[1];
            // $sale_day = $request->installment_date;

            // $installment_date = Carbon::create($sale_yr, $sale_mn, $sale_day);

            // for ($i=0; $i < $request->time_period ; $i++) {
            //     $newInstallmentDate = $installment_date->addMonth(1)->format('Y-m-d');

            //     $installment = new LoanInstallment();
            //     $installment->branch_id = \Auth::user()->branchId();
            //     $installment->loan_id = $loanId;
            //     $installment->amount = $request->monthly_installment;
            //     $installment->total_amount_to_pay = $request->monthly_installment;
            //     $installment->status = 2;
            //     $installment->installment_date = $newInstallmentDate;
            //     $installment->invoice_id = $invoiceId;
            //     $installment->save();
            // }

            return redirect()->back()->with('success', __('Loan successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobLoanSave(Request $request){
        if(\Auth::user()->can('create loan')){

            $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                                    'customer' => 'required',
                                    // 'reason' => 'required',
                                    // 'guarantor_1' => 'required',
                                    'business_category' => 'required',
                                    'officer_id' => 'required',
                                    // 'guarantor_2' => 'required',
                                    'loan_amount' => 'required',
                                    'interest_rate' => 'required',
                                    'time_period' => 'required',
                                    'interest_amount' => 'required',
                                    'total_amount' => 'required',
                                    'time_range' => 'required',
                                    'installment_date' => 'required',
                                    'due_date' => 'required',
                                    'doc_type' => 'required',
                                    'document_charges' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if( $request->time_range == 1){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'daily_installment' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 2){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'weekly_installment' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 3){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'monthly_installment' => 'required',
                                    ]
                );
            }

            if($validator2->fails()){
                $messages = $validator2->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // $maxId = Invoice::max('id');

                if($request->has('application_document')){
                    $applicationDocumnet = self::uploadApplicationDocumnet($request);
                }
                if($request->has('other_document')){
                    $otherDocumnet = self::uploadOtherDocumnet($request);
                }

                // $invoiceCode = "SMEL".str_pad(($maxId+1), 6, "0", STR_PAD_LEFT);
                // $invoice = new Invoice();
                // $invoice->branch_id = \Auth::user()->branchId();
                // $invoice->invoice_code = $invoiceCode;
                // $invoice->customer_id = $request->customer;
                // $invoice->guarantor_id = $request->guarantor_1;
                // $invoice->issue_date = $request->date;
                // $invoice->due_date = $request->due_date;
                // $invoice->status = 1;
                // $invoice->created_by = \Auth::user()->id;
                // $invoice->save();

                // $invoiceId = $maxId+1;

            $loan = new Loan();
            $loan->branch_id = \Auth::user()->branchId();
            // $loan->invoice_id = $invoiceId;
            $loan->customer_id = $request->customer;
            $loan->business_category_id = $request->business_category;
            $loan->officer_id = $request->officer_id;
            $loan->date = $request->date;
            $loan->guarantor_1 = $request->guarantor_1;
            if($request->guarantor_2 != ''){
                $loan->guarantor_2 = $request->guarantor_2;
            }
            if($request->guarantor_3 != ''){
                $loan->guarantor_3 = $request->guarantor_3;
            }
            if($request->guarantor_4 != ''){
                $loan->guarantor_4 = $request->guarantor_4;
            }

            $loan->person_1_name = $request->person_1_name;
            $loan->person_1_phone = $request->person_1_phone;
            $loan->person_2_name = $request->person_2_name;
            $loan->person_2_phone = $request->person_2_phone;

            $loan->loan_amount = $request->loan_amount;
            $loan->reason = $request->reason;
            $loan->time_period = $request->time_period;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_amount = $request->interest_amount;
            $loan->total_amount = $request->total_amount;
            $loan->current_due_amount = $request->total_amount;

            $loan->time_range = $request->time_range;
            if($request->time_range == 1){
                $loan->daily_installment = $request->daily_installment;
            }
            if($request->time_range == 2){
                $loan->weekly_installment = $request->weekly_installment;
            }
            if($request->time_range == 3){
                $loan->monthly_installment = $request->monthly_installment;
            }

            $loan->installment_date = $request->installment_date;
            $loan->due_date = $request->due_date;
            $loan->doc_type = $request->doc_type;
            $loan->document_charges = $request->document_charges;
            $loan->document_charges_in_words = $request->document_charges_in_words;
            if($request->has('application_document')){
                $loan->application_document = $applicationDocumnet;
            }
            if($request->has('other_document')){
                $loan->other_document = $otherDocumnet;
            }
            $loan->loan_type = 5;
            $loan->status = 1;
            $loan->created_by = \Auth::user()->id;
            $loan->save();

            return redirect()->back()->with('success', __('Loan successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Update
    public function loanUpdate(Request $request){
        if(\Auth::user()->can('edit loan')){

            $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                                    'customer' => 'required',
                                    // 'reason' => 'required',
                                    // 'guarantor_1' => 'required',
                                    'business_category' => 'required',
                                    'officer_id' => 'required',
                                    // 'guarantor_2' => 'required',
                                    'loan_amount' => 'required',
                                    'interest_rate' => 'required',
                                    // 'time_period' => 'required',
                                    'interest_amount' => 'required',
                                    'total_amount' => 'required',
                                    'time_range' => 'required',
                                    // 'monthly_installment' => 'required',
                                    'installment_date' => 'required',
                                    'due_date' => 'required',
                                    'doc_type' => 'required',
                                    'document_charges' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            if( $request->time_range == 1){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'daily_installment' => 'required',
                                        'daily_time_period' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 2){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'weekly_installment' => 'required',
                                        'time_period' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 3){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'monthly_installment' => 'required',
                                        'time_period' => 'required',
                                    ]
                );
            }

            if($validator2->fails()){
                $messages = $validator2->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $loan = Loan::find($request->loan_id);
            // $invoice = Invoice::find($loan->invoice_id);

                if($request->has('application_document')){
                    $applicationDocumnet = self::uploadApplicationDocumnet($request);
                }
                if($request->has('other_document')){
                    $otherDocumnet = self::uploadOtherDocumnet($request,'other_document');
                }
                if($request->has('other_document2')){
                    $otherDocumnet2 = self::uploadOtherDocumnet($request,'other_document2');
                }
                if($request->has('other_document3')){
                    $otherDocumnet3 = self::uploadOtherDocumnet($request,'other_document3');
                }

                // $invoice->customer_id = $request->customer;
                // $invoice->guarantor_id = $request->guarantor_1;
                // $invoice->issue_date = $request->date;
                // $invoice->due_date = $request->due_date;
                // $invoice->created_by = \Auth::user()->id;
                // $invoice->update();

            $loan->customer_id = $request->customer;
            $loan->business_category_id = $request->business_category;
            $loan->officer_id = $request->officer_id;
            $loan->date = $request->date;
            $loan->guarantor_1 = $request->guarantor_1;
            if($request->guarantor_2 != ''){
                $loan->guarantor_2 = $request->guarantor_2;
            }
            if($request->guarantor_3 != ''){
                $loan->guarantor_3 = $request->guarantor_3;
            }
            if($request->guarantor_4 != ''){
                $loan->guarantor_4 = $request->guarantor_4;
            }

            $loan->person_1_name = $request->person_1_name;
            $loan->person_1_phone = $request->person_1_phone;
            $loan->person_2_name = $request->person_2_name;
            $loan->person_2_phone = $request->person_2_phone;

            $loan->loan_amount = $request->loan_amount;
            $loan->reason = $request->reason;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_amount = $request->interest_amount;
            $loan->total_amount = $request->total_amount;
            $loan->current_due_amount = $request->total_amount;

            $loan->reg_officer_code = $request->reg_officer_code;
            $loan->reg_year_code = $request->reg_year_code;
            $loan->reg_loan_code = $request->reg_loan_code;

            $loan->time_range = $request->time_range;
            if($request->time_range == 1){
                $loan->daily_time_period = $request->daily_time_period;
                $loan->daily_installment = $request->daily_installment;
                $loan->loan_package_id = $request->loan_package;
            }
            if($request->time_range == 2){
                $loan->time_period = $request->time_period;
                $loan->weekly_installment = $request->weekly_installment;
            }
            if($request->time_range == 3){
                $loan->time_period = $request->time_period;
                $loan->monthly_installment = $request->monthly_installment;
            }
            // $loan->monthly_installment = $request->monthly_installment;
            $loan->installment_date = $request->installment_date;
            $loan->due_date = $request->due_date;
            $loan->doc_type = $request->doc_type;
            $loan->document_charges = $request->document_charges;
            $loan->document_charges_in_words = $request->document_charges_in_words;
            if($request->has('application_document')){
                $loan->application_document = $applicationDocumnet;
            }
            if($request->has('other_document')){
                $loan->other_document = $otherDocumnet;
            }
            if($request->has('other_document2')){
                $loan->other_document2 = $otherDocumnet2;
            }
            if($request->has('other_document3')){
                $loan->other_document3 = $otherDocumnet3;
            }
            $loan->created_by = \Auth::user()->id;
            $loan->update();

            return redirect()->back()->with('success', __('Loan Successfully Updated.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function groupLoanUpdate(Request $request){
        if(\Auth::user()->can('edit loan')){

            $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                                    'center' => 'required',
                                    'group' => 'required',
                                    'customer' => 'required',
                                    'business_category' => 'required',
                                    'officer_id' => 'required',
                                    // 'reason' => 'required',
                                    // 'guarantor_1' => 'required',
                                    // 'guarantor_2' => 'required',
                                    'loan_amount' => 'required',
                                    'interest_rate' => 'required',
                                    'time_period' => 'required',
                                    'interest_amount' => 'required',
                                    'total_amount' => 'required',
                                    'time_range' => 'required',
                                    // 'monthly_installment' => 'required',
                                    'installment_date' => 'required',
                                    'due_date' => 'required',
                                    'doc_type' => 'required',
                                    'document_charges' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            if( $request->time_range == 1){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'daily_installment' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 2){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'weekly_installment' => 'required',
                                    ]
                );
            } elseif( $request->time_range == 3){
                $validator2 = \Validator::make(
                    $request->all(), [
                                        'monthly_installment' => 'required',
                                    ]
                );
            }

            if($validator2->fails()){
                $messages = $validator2->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $loan = Loan::find($request->loan_id);
            // $invoice = Invoice::find($loan->invoice_id);

                if($request->has('application_document')){
                    $applicationDocumnet = self::uploadApplicationDocumnet($request);
                }
                if($request->has('other_document')){
                    $otherDocumnet = self::uploadOtherDocumnet($request);
                }

                // $invoice->customer_id = $request->customer;
                // $invoice->guarantor_id = $request->guarantor_1;
                // $invoice->issue_date = $request->date;
                // $invoice->due_date = $request->due_date;
                // $invoice->created_by = \Auth::user()->id;
                // $invoice->update();

            $loan->center_id = $request->center;
            $loan->group_id = $request->group;
            $loan->customer_id = $request->customer;
            $loan->business_category_id = $request->business_category;
            $loan->officer_id = $request->officer_id;
            $loan->date = $request->date;
            $loan->guarantor_1 = $request->guarantor_1;
            $loan->guarantor_2 = $request->guarantor_2;

            $loan->guardian_name = $request->guardian_name;
            $loan->guardian_nic_no = $request->guardian_nic_no;
            $loan->guardian_address = $request->guardian_address;
            $loan->guardian_phone_no = $request->guardian_phone_no;

            $loan->person_1_name = $request->person_1_name;
            $loan->person_1_phone = $request->person_1_phone;
            $loan->person_2_name = $request->person_2_name;
            $loan->person_2_phone = $request->person_2_phone;

            if($request->guarantor_3 != ''){
                $loan->guarantor_3 = $request->guarantor_3;
            }
            if($request->guarantor_4 != ''){
                $loan->guarantor_4 = $request->guarantor_4;
            }
            $loan->loan_amount = $request->loan_amount;
            $loan->reason = $request->reason;
            $loan->time_period = $request->time_period;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_amount = $request->interest_amount;
            $loan->total_amount = $request->total_amount;
            $loan->current_due_amount = $request->total_amount;

            $loan->time_range = $request->time_range;
            if($request->time_range == 1){
                $loan->daily_installment = $request->daily_installment;
            }
            if($request->time_range == 2){
                $loan->weekly_installment = $request->weekly_installment;
            }
            if($request->time_range == 3){
                $loan->monthly_installment = $request->monthly_installment;
            }

            $loan->installment_date = $request->installment_date;
            $loan->due_date = $request->due_date;
            $loan->doc_type = $request->doc_type;
            $loan->document_charges = $request->document_charges;
            $loan->document_charges_in_words = $request->document_charges_in_words;
            if($request->has('application_document')){
                $loan->application_document = $applicationDocumnet;
            }
            if($request->has('other_document')){
                $loan->other_document = $otherDocumnet;
            }
            $loan->created_by = \Auth::user()->id;
            $loan->update();

            return redirect()->back()->with('success', __('Loan Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Loan Check Pages
    public function loanCheck($id){
        if(\Auth::user()->can('check loan')){

            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $packages = LoanPackage::where('branch_id', \Auth::user()->branchId())->where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $packages->prepend('-- Loan Package --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $loan = Loan::with('customer')->find($id);
            if($loan->loan_type == 1){
                $interestRate = InterestRate::where('id', 2)->first();
            } else if($loan->loan_type == 3){
                $interestRate = InterestRate::where('id', 5)->first();
            } else if($loan->loan_type == 4){
                $interestRate = InterestRate::where('id', 6)->first();
            } else if($loan->loan_type == 5){
                $interestRate = InterestRate::where('id', 7)->first();
            }

            $years = array('2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010',
                      '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020',
                      '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030');
            return view('loans.loan_check', compact('loan', 'customers', 'guarantors', 'timeperiods', 'interestRate', 'users', 'busiCategories', 'packages', 'years'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function groupLoanCheck($id){
        if(\Auth::user()->can('check loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $centers = Center::where('status', 1)->get();
            // $centers = Center::get()->pluck('name', 'id');
            // $centers->prepend('-- Center --', '');

            $groups = Group::where('status', 1)->get()->pluck('name', 'id');
            $groups->prepend('-- Group --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $interestRate = InterestRate::where('id', 3)->first();
            $loan = Loan::with('customer')->find($id);
            return view('loans.group_loan_check', compact('loan', 'customers', 'guarantors', 'timeperiods', 'interestRate', 'centers', 'groups', 'users', 'busiCategories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Checked
    public function checked(Request $request){
        if(\Auth::user()->can('check loan')){
            $loan = Loan::find($request->loan_id);
            $loan->status = 4;
            $loan->update();
            return redirect()->back()->with('success', __('Loan Successfully Checked.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Approve Pages
    public function loanApproveCheck($id){
        if(\Auth::user()->can('approve loan')){

            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $packages = LoanPackage::where('branch_id', \Auth::user()->branchId())->where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $packages->prepend('-- Loan Package --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $loan = Loan::with('customer')->find($id);
            if($loan->loan_type == 1){
                $interestRate = InterestRate::where('id', 2)->first();
            } else if($loan->loan_type == 3){
                $interestRate = InterestRate::where('id', 5)->first();
            } else if($loan->loan_type == 4){
                $interestRate = InterestRate::where('id', 6)->first();
            } else if($loan->loan_type == 5){
                $interestRate = InterestRate::where('id', 7)->first();
            }

            $years = array('2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010',
                      '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020',
                      '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030');

            return view('loans.loan_approve', compact('loan', 'customers', 'guarantors', 'timeperiods', 'interestRate', 'users', 'busiCategories', 'packages', 'years'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function groupLoanApproveCheck($id){
        if(\Auth::user()->can('approve loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $centers = Center::where('status', 1)->get();
            // $centers = Center::get()->pluck('name', 'id');
            // $centers->prepend('-- Center --', '');

            $groups = Group::where('status', 1)->get()->pluck('name', 'id');
            $groups->prepend('-- Group --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $interestRate = InterestRate::where('id', 3)->first();
            $loan = Loan::with('customer')->find($id);
            return view('loans.group_loan_approve', compact('loan', 'customers', 'guarantors', 'timeperiods', 'interestRate', 'centers', 'groups', 'users', 'busiCategories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Approve
    public function loanApprove(Request $request){
        if(\Auth::user()->can('check loan')){
            $loan = Loan::find($request->loan_id);
            $loan->status = 5;
            $loan->update();
            return redirect()->back()->with('success', __('Loan Successfully Checked.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Disbuse Pages

    public function loanDisburseCheck($id){
        if(\Auth::user()->can('disburse loan')){

            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [1,2])->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $packages = LoanPackage::where('branch_id', \Auth::user()->branchId())->where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $packages->prepend('-- Loan Package --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $loan = Loan::with('customer')->find($id);
            if($loan->loan_type == 1){
                $interestRate = InterestRate::where('id', 2)->first();
            } else if($loan->loan_type == 3){
                $interestRate = InterestRate::where('id', 5)->first();
            } else if($loan->loan_type == 4){
                $interestRate = InterestRate::where('id', 6)->first();
            } else if($loan->loan_type == 5){
                $interestRate = InterestRate::where('id', 7)->first();
            }

            $years = array('2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010',
                      '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020',
                      '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030');
            return view('loans.loan_disburse', compact('loan', 'customers', 'guarantors', 'timeperiods', 'interestRate', 'users', 'busiCategories', 'packages', 'years'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function groupLoanDisburseCheck($id){
        if(\Auth::user()->can('disburse loan')){
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');

            $guarantors = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->orderBy('name')->get()->pluck('name', 'id');
            $guarantors->prepend('-- Guarantor --', '');

            $timeperiods = TimePeriod::get()->pluck('time_period', 'time_period');
            $timeperiods->prepend('-- Months --', '');

            $centers = Center::where('status', 1)->get();
            // $centers = Center::get()->pluck('name', 'id');
            // $centers->prepend('-- Center --', '');

            $groups = Group::where('status', 1)->get()->pluck('name', 'id');
            $groups->prepend('-- Group --', '');

            $busiCategories = BusinessCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $busiCategories->prepend('-- Business Categories --', '');

            $users = User::where('is_active', 1)->orderBy('name')->get()->pluck('name', 'id');
            $users->prepend('-- User --', '');

            $interestRate = InterestRate::where('id', 3)->first();
            $loan = Loan::with('customer')->find($id);
            return view('loans.group_loan_disburse', compact('loan', 'customers', 'guarantors', 'timeperiods', 'interestRate', 'centers', 'groups', 'users', 'busiCategories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Disburse
    public function loanDisburse(Request $request){
        if(\Auth::user()->can('disburse loan')){

            $loan = Loan::find($request->loan_id);
                // $invoice = Invoice::find($loan->invoice_id);

                // $invoice->issue_date = $request->date;
                // $invoice->due_date = $request->due_date;
                // $invoice->created_by = \Auth::user()->id;
                // $invoice->update();

            $loan->officer_id = $request->officer_id;
            $loan->date = $request->date;
                // $loan->time_period = $request->time_period;
                // $loan->interest_amount = $request->interest_amount;
                // $loan->total_amount = $request->total_amount;
                // $loan->current_due_amount = $request->total_amount;
                // $loan->monthly_installment = $request->monthly_installment;
                // $loan->installment_date = $request->installment_date;
            $loan->due_date = $request->due_date;
            $loan->status = 2;
            $loan->approved_by = \Auth::user()->id;
            $loan->update();

            $timeRange = $request->time_range;
            $dateofSale = explode('-',$request->date);
            $sale_yr = $dateofSale[0];
            // if($timeRange == 2){
            //     if($dateofSale[2] > $request->installment_date){
            //         $sale_mn = $dateofSale[1] + 1;
            //     } else {
            //         $sale_mn = $dateofSale[1];
            //     }
            // }
            // else if($timeRange == 1){
            //     if($dateofSale[2] > $request->installment_date){
            //         $sale_mn = $dateofSale[1] + 1;
            //     } else {
            //         $sale_mn = $dateofSale[1];
            //     }
            // } else {
            //     $sale_mn = $dateofSale[1];
            // }
            $sale_mn = $dateofSale[1];

            $sale_day = $request->installment_date;

            $installment_date = Carbon::create($sale_yr, $sale_mn, $sale_day);

            if($timeRange == 3){
                for ($i=0; $i < $request->time_period ; $i++) {
                    // $newInstallmentDate = $installment_date->addMonth(1)->format('Y-m-d');
                    // if holiday set next working day
                    $newInstallmentDate = date('Y-m-d', strtotime($installment_date. ' + '.($i+1).' months'));
                    if(Holiday::isHoliday($newInstallmentDate)){
                        $newInstallmentDate = Holiday::getNextWorkingDay($newInstallmentDate);
                    }

                    $installment = new LoanInstallment();
                    $installment->branch_id = \Auth::user()->branchId();
                    $installment->loan_id = $request->loan_id;
                    $installment->amount = $request->monthly_installment;
                    $installment->total_amount_to_pay = $request->monthly_installment;
                    if($i == 0){
                        $installment->total_due_amount =$request->total_amount;
                    }

                    $installment->status = 2;
                    $installment->installment_date = $newInstallmentDate;
                    // $installment->invoice_id = $loan->invoice_id;
                    $installment->time_range = $request->time_range;
                    $installment->save();
                }
            }
            elseif ($timeRange == 2){
                // $totWeeks = $request->time_period*4;
                // $newInstallmentDate = $installment_date;

                for ($i=0; $i < $request->time_period; $i++) {

                    $newInstallmentDate =  Holiday::getWeeklyDueDate($installment_date, $i+1);
                    // $newInstallmentDate = date('Y-m-d', strtotime($installment_date. ' + '.($i+1).' months'));

                    $installment = new LoanInstallment();
                    $installment->branch_id = \Auth::user()->branchId();
                    $installment->loan_id = $request->loan_id;
                    $installment->amount = $request->weekly_installment;
                    $installment->total_amount_to_pay = $request->weekly_installment;
                    if($i == 0){
                        $installment->total_due_amount =$request->total_amount;
                    }
                    $installment->status = 2;
                    $installment->installment_date = $newInstallmentDate;
                    $installment->time_range = $request->time_range;
                    $installment->save();

                    // $newInstallmentDate = $newInstallmentDate->addDay(7);
                }
            }
            elseif ($timeRange == 1){
                // getDailyDueDate
                $totDays = $request->daily_time_period;
                $newInstallmentDate = $installment_date;
                $i=0;
                while ( $i < $totDays) {
                    // $day = $newInstallmentDate->format('D');
                    if(!(Holiday::isHoliday($newInstallmentDate->format('Y-m-d')))){
                        $installment = new LoanInstallment();
                        $installment->branch_id = \Auth::user()->branchId();
                        $installment->loan_id = $request->loan_id;
                        $installment->amount = $request->daily_installment;
                        $installment->total_amount_to_pay = $request->daily_installment;
                        if($i == 0){
                            $installment->total_due_amount = $request->total_amount;
                        }
                        $installment->status = 2;
                        $installment->installment_date = $newInstallmentDate->format('Y-m-d');
                        $installment->time_range = $request->time_range;
                        $installment->save();

                        $i++;
                    }

                    $newInstallmentDate = $newInstallmentDate->addDay(1);
                }
            }

            return redirect()->back()->with('success', __('Loan Successfully Disbursed.!'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function uploadApplicationDocumnet(Request $request) {
        $file = $request->file('application_document');

        $destinationPath = 'assets/documents/loans/applications';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    public function uploadOtherDocumnet(Request $request, $name) {
        $file = $request->file($name);

        $destinationPath = 'assets/documents/loans/documents';

        $file->move($destinationPath, $file->getClientOriginalName());

        return $destinationPath.'/'.$file->getClientOriginalName();
    }

    // public function loanApprove($id){
    //     $loan = Loan::find($id);
    //     $loan->status = 2;
    //     $loan->update();

    //     return redirect()->back()->with('success', __('Loan Successfully Approved.'));
    // }

    public function loanReject($id){
        if(\Auth::user()->can('reject loan')){

            $loan = Loan::find($id);
            $loan->status = 0;
            $loan->update();

            return redirect()->back()->with('success', __('Loan Successfully Rejected.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id){
        if(\Auth::user()->can('delete loan')){
            $loan = Loan::find($id);
            if($loan->cheque_id != null){
                return redirect()->back()->with('error', __('Cheque has been already issued'));
            }
            $paymentVoucher = PaymentVoucher::where('loan_id', $id)->first();
            if($paymentVoucher){
                if($paymentVoucher->status != 0){
                    return redirect()->back()->with('error', __('Please Remove Payment Voucher First'));
                }
            }

            $loan->status = 9;
            $loan->update();
            $loanInstallmets = LoanInstallment::where('loan_id', $id)->get();
                foreach($loanInstallmets as $loanInstallmet){
                    $loanInstallmet->status = 0;
                    $loanInstallmet->update();
                }
            $receipts = Receipt::where('loan_id', $id)->get();
                foreach($receipts as $receipt){
                    $receipt->status = 0;
                    $receipt->update();
                }

            return redirect()->back()->with('success', __('Loan Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id){
        if(\Auth::user()->can('view loan')){

            $loans = Loan::with(['customer', 'guarantor1', 'guarantor2', 'guarantor3', 'guarantor4', 'center.branches', 'group', 'lastInstallment',
                                'loanInstallments'=>function($query){$query->whereNotIn('status', [0]); },
                                'receipts'=>function($query2){$query2->where('status', 1); }
                                ])->find($id);
            $created = Loan::select('users.id','users.name')
                            ->leftJoin('users', 'users.id', 'loans.created_by')
                            ->where('loans.id', $id)->first();
            $loanOfficer = Loan::select('users.name')
                                ->join('users', 'users.id', 'loans.officer_id')
                                ->where('loans.id', $id)->first();
            return view('loans.view', compact('loans', 'created', 'loanOfficer'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // payInstallment page
    public function payInstallment($id){
        if(\Auth::user()->can('pay loan')){
            $range = 1;
            $installments = LoanInstallment::where('id', $id)->first();
            if($installments->time_range == 3){
                $nextInstallmentDate = Carbon::parse($installments->installment_date)->addMonth(1)->format('Y-m-d');
                $range = 1;
                $nextInstallment = LoanInstallment::where('installment_date', $nextInstallmentDate)->where('loan_id', $installments->loan_id)->whereNotIn('status', [0])->first();
            } else if($installments->time_range == 2){
                $nextInstallmentDate = Carbon::parse($installments->installment_date)->addDay(7)->format('Y-m-d');
                $range = 4;
                $nextInstallment = LoanInstallment::where('installment_date', $nextInstallmentDate)->where('loan_id', $installments->loan_id)->whereNotIn('status', [0])->first();
            } else if($installments->time_range == 1){
                $nextInstallmentDate = Carbon::parse($installments->installment_date)->addDay(1)->format('Y-m-d');
                // $range = 30;
                $nextInstallment = LoanInstallment::where('loan_id', $installments->loan_id)->whereNotIn('status', [0,1])->first();
                $nextInstallmentDate = $nextInstallment->installment_date;
            }
            $dueInstallments = LoanInstallment::where('loan_id', $installments->loan_id)->whereIn('status', [2,3])->get();
            $loan = Loan::where('id', $installments->loan_id)->first();
            if($loan->time_range == 1){
                $instCount = $loan->daily_time_period;
            } else {
                $instCount = $loan->time_period * $range;
            }
            $data['amountForInst'] = number_format((float)$loan->loan_amount / $instCount, 2, '.', '');
            $data['interestForInst'] = number_format((float)$loan->interest_amount / $instCount, 2, '.', '');
            $data['insts'] = $instCount;
            $data['dueInsts'] = count($dueInstallments) - 1;

            if($installments->new_installment == 1){

                $num1 = $loan->loan_amount;      // Capital amount
                $num2 = $loan->interest_amount;        // Intesrest amount
                $paid = $loan->current_due_amount;     // Current Due amount
                for($i = $num2; $i > 1; $i--) {
                    if(($num1 % $i) == 0 && ($num2 % $i) == 0) {
                        $num1 = $num1 / $i;
                        $num2 = $num2 / $i;
                    }
                }
                $numTot = $num1 + $num2;
                $principal = round(( $paid / $numTot ) * $num1, 2);
                $interest = $paid - $principal;
                $data['amountForInst'] = $principal;
                $data['interestForInst'] = $interest;
            }

            $installmentAdvances = LoanInstallmentAdvance::where('loan_installment_id', $id)->where('status', 1)->get();

            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            return view('loans.pay_installment', compact('installments', 'loan', 'nextInstallment', 'banks', 'data', 'installmentAdvances'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // pay loan installment
    public function installmentSave(Request $request){
        DB::beginTransaction();
        if(\Auth::user()->can('pay loan')){

            $userId = \Auth::user()->id;
            $validator = \Validator::make(
                $request->all(), [
                                'loan_id' => 'required',
                                'installment_id' => 'required',
                                //    'invoice_id' => 'required',
                                'current_date' => 'required',
                                'installment_due_date' => 'required',
                                'customer_id' => 'required',
                                'installment_amount' => 'required',
                                'paid_amount' => 'required',
                                'payment_method' => 'required',
                                'paid_amount_in_words' => 'required',
                            ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if(isset($request->waitting_loan_installment_id)){
                $checkForinstallments = LoanInstallment::find($request->installment_id);
                if($checkForinstallments->status == 1){
                    return redirect()->back()->with('error', 'The Installment Payment has been Already Settled!');
                }
                $userId = $request->created_by;
                $waittingLoanInstallment = WaittingLoanInstallment::find($request->waitting_loan_installment_id);
                $waittingLoanInstallment->status = 2;
                $waittingLoanInstallment->approved_rejected_by = \Auth::user()->id;
                $waittingLoanInstallment->update();
            }

            $paidAmount = $request->paid_amount;
            $totalAmountToPay = $request->total_amount_tobe_paid;
            $extraPaid = 0;
            $remainder = 0;

            $relasedAmount = $request->released_amount;

            if(isset($request->remainder_amount)){
                $reminderAmount = $request->remainder_amount;
            } else {
                $reminderAmount = 0;
            }

            // if(isset($request->release_fine)){
            //     $fineAmount = $request->fine_amount;
            //     $checkAmountToBePaid = $totalAmountToPay - $relasedAmount + $fineAmount;
            //     $installmentFineAmount = 0;
            // } else {
            //     $fineAmount = 0;
            //     $checkAmountToBePaid = $totalAmountToPay - $relasedAmount;

            //     if(isset($request->fine_amount)){
            //         $installmentFineAmount = $request->fine_amount;
            //     } else {
            //         $installmentFineAmount = 0;
            //     }
            // }

            if(isset($request->fine_amount) && $request->fine_amount){
                // $installmentFineAmount = $request->fine_amount;
                $fineAmount = $request->fine_amount;
            } else {
                // $installmentFineAmount = 0;
                $fineAmount = 0;
            }

            if($totalAmountToPay >= $relasedAmount){
                $checkAmountToBePaid = $totalAmountToPay - $relasedAmount;
            } else {
                $checkAmountToBePaid = $totalAmountToPay;
            }

            // if(isset($request->release_fine)){
            //     $due_paid_without_fine = $request->paid_amount;
            // } else {
                $due_paid_without_fine = $request->paid_amount + $request->released_amount - $fineAmount;
            // }

            // Query has taken to up
            $installments = LoanInstallment::find($request->installment_id);

                if($paidAmount >= $checkAmountToBePaid){
                    $extraPaid = $paidAmount - $checkAmountToBePaid;
                } elseif ($paidAmount < $checkAmountToBePaid){
                    $remainder = $checkAmountToBePaid - $paidAmount;
                }

            $loans = Loan::find($request->loan_id);
            $current_due_amount = 0;
            $due_paid_amount = 0;
            $totalDueAmount = $loans->total_amount;
                if($loans->current_due_amount != null){
                    $current_due_amount = $loans->current_due_amount;
                }
                if($loans->due_paid_amount != null){
                    $due_paid_amount = $loans->due_paid_amount;
                }

            $newDueAmount = $current_due_amount - $due_paid_without_fine;
            $newDuePaidAmount = $due_paid_amount + $due_paid_without_fine;
                if($newDueAmount < 0){
                    $loans->current_due_amount = 0;
                } else {
                    $loans->current_due_amount = $newDueAmount;
                }
            $loans->due_paid_amount = $newDuePaidAmount;


            $maxId = Receipt::max('id');
            $receiptCode = "LN".str_pad(($maxId+1), 6, "0", STR_PAD_LEFT);

            if($request->payment_method == 'CARD'){
                if($request->card_charge_amount != ''){
                    $cardCharge = new CardInterestExpense();
                    $cardCharge->amount = $request->card_charge_amount;
                    $cardCharge->loan_id = $request->loan_id;
                    $cardCharge->save();
                }
            }

            $receipts = new Receipt();
            $receipts->branch_id = \Auth::user()->branchId();
            $receipts->receipt_code = $receiptCode;
            $receipts->customer_id = $request->customer_id;
            $receipts->loan_id = $request->loan_id;
            $receipts->issue_date = $request->current_date;
            $receipts->installment_due_date = $request->installment_due_date;

            $receipts->status = 1;
            $receipts->installment_amount = $request->installment_amount;
                if($request->fine_amount != null){
                    $receipts->extra_charges = $request->fine_amount;
                }
            $receipts->total_amount_to_pay = $request->total_amount_tobe_paid;
            $receipts->paid_amount = $request->paid_amount;
            if(isset($request->pay_total)){
                $receipts->balance_amount = 0;
            }else {
                if($newDueAmount < 0){
                    $receipts->balance_amount = 0;
                } else {
                    $receipts->balance_amount = $newDueAmount;
                }
            }
            $receipts->payment_method = $request->payment_method;
            $receipts->paid_amount_in_words = $request->paid_amount_in_words;

            if($request->payment_method == 'CARD'){
                $receipts->card_type            = $request->card_type;
                $receipts->card_charge_amount   = $request->card_charge_amount;
                $receipts->total_amount         = $request->total_amount;
            } elseif($request->payment_method == 'CHEQUE') {
                $receipts->cheque_number        = $request->cheque_number;
            }

            $receipts->description = $request->description;
            $receipts->created_by = $userId;
            $receipts->save();

            $receiptId = Receipt::max('id');

            // $installments = LoanInstallment::find($request->installment_id);
            // if(isset($request->release_fine)){
                $installments->total_amount_to_pay = $request->total_amount_tobe_paid;
            // }

            //Query has moved up
            $installments->released_amount = $request->released_amount + $request->excess_amount;
            $installments->manual_released_amount = $request->released_amount;
            $installments->status = 1;
            $installments->paid_date = $request->current_date;
            $installments->paid_amount = $request->paid_amount;
            $installments->paid_amount_in_words = $request->paid_amount_in_words;
            $installments->payment_method = $request->payment_method;
            $installments->bank_id = $request->bank;

                if($request->payment_method == 'CARD'){
                    $installments->card_type            = $request->card_type;
                    $installments->card_expence_id      = $cardCharge->id;
                    $installments->card_charge_amount   = $request->card_charge_amount;
                    $installments->total_amount         = $request->total_amount;
                } elseif($request->payment_method == 'CHEQUE') {
                    $installments->cheque_number        = $request->cheque_number;
                    $installments->cheque_realised_date        = $request->realised_date;
                }

                if($extraPaid != 0){
                    $installments->extra_paid_amount = $extraPaid;
                }
                if($remainder != 0){
                    $installments->remainder_of_paid_amount = $remainder;
                }

                if(isset($request->fine_amount)){
                    $installments->fine = $request->fine_amount;
                }

                if(isset($request->pay_total)){
                    $installments->total_due_amount = 0;
                    $installments->totaly_paid = 1;
                } else {
                    $installments->total_due_amount = $newDueAmount;
                }

            $installments->receipt_id = $receipts->id;
            $installments->update();

            // Getting ratio & productDetail variable ============== start ========================>
            $outstanding = $loans->loan_amount;
            $interestAmount = $loans->interest_amount;
            $num1 = $outstanding;
            $num2 = $interestAmount;
            for($i = $num2; $i > 1; $i--) {
                if(($num1 % $i) == 0 && ($num2 % $i) == 0) {
                    $num1 = $num1 / $i;
                    $num2 = $num2 / $i;
                }
            }
            $numTot = $num1 + $num2;

            // $principal = $request->this_loan_value;
            // $interest = $request->this_interest_value;
            $principal = 0;
            $interest = 0;
            $excess = 0;

            $loanType = $loans->loan_type;
            // Getting ratio & productDetail variable ============== end ========================>

            if(!isset($request->pay_total)){
                if($extraPaid > 0){
                    $installmentDate = $installments->installment_date;
                    $installmentTimeRange = $installments->time_range;

                    while ($extraPaid > 0){
                        if($installmentTimeRange == 3){
                            // $nextInstallmentDate = Carbon::parse($installmentDate)->addMonth(1);
                            // $nextInstallment = LoanInstallment::where('installment_date', $nextInstallmentDate->format('Y-m-d'))->where('loan_id', $request->loan_id)->whereNotIn('status', [0])->first();
                            $nextInstallment = LoanInstallment::where('loan_id', $request->loan_id)->where('status', 2)->first();
                        } else if($installmentTimeRange == 2){
                            // $nextInstallmentDate = Carbon::parse($installmentDate)->addDay(7);
                            // $nextInstallment = LoanInstallment::where('installment_date', $nextInstallmentDate->format('Y-m-d'))->where('loan_id', $request->loan_id)->whereNotIn('status', [0])->first();
                            $nextInstallment = LoanInstallment::where('loan_id', $request->loan_id)->where('status', 2)->first();
                        } else if($installmentTimeRange == 1){
                            // $nextInstallmentDate = Carbon::parse($installmentDate)->addDay(1);
                            // $nextInstallment = LoanInstallment::where('loan_id', $request->loan_id)->whereNotIn('status', [0,1])->first();
                            // $nextInstallmentDate = $nextInstallment->installment_date;
                            $nextInstallment = LoanInstallment::where('loan_id', $request->loan_id)->where('status', 2)->first();
                        }
                        if($nextInstallment != null){
                            $nextTotAmountToPay = $nextInstallment->amount + $nextInstallment->fine - $extraPaid;
                            if($nextTotAmountToPay > 0)
                            {
                                $nextInstallment->total_amount_to_pay = $nextTotAmountToPay;
                                $nextInstallment->released_amount = $extraPaid;
                                $nextInstallment->update();
                                $excess = $extraPaid;
                                $extraPaid = 0;
                            }
                            elseif($nextTotAmountToPay == 0)
                            {
                                $nextInstallment->status = 1;
                                $nextInstallment->total_amount_to_pay = $nextTotAmountToPay;
                                $nextInstallment->released_amount = $extraPaid;
                                $nextInstallment->update();
                                $excess = $extraPaid;
                                $extraPaid = 0;

                                // $principal += $request->this_loan_value;
                                // $interest += $request->this_interest_value;
                            }
                            elseif ($nextTotAmountToPay < 0)
                            {
                                $nextInstallment->status = 1;
                                $nextInstallment->total_amount_to_pay = 0;
                                $nextInstallment->released_amount = $nextInstallment->amount + $nextInstallment->fine;
                                $nextInstallment->update();

                                $extraPaid = $extraPaid - ( $nextInstallment->amount + $nextInstallment->fine );
                                // $installmentDate = $nextInstallmentDate;
                                // $installmentTimeRange = $nextInstallment->time_range;

                                // $principal += $request->this_loan_value;
                                // $interest += $request->this_interest_value;
                            }
                        } else {
                            $extraPaid = 0;
                        }
                    }
                    // $excess = ($request->paid_amount - $fineAmount) - ($principal + $interest);
                }

                // dd($extraPaid);
                // DB::rollback();

                if($remainder > 0){
                    // Getting Principle & interest amounts ============ start =========================>
                    // $principal = ( $due_paid_without_fine / $numTot ) * $num1;
                    // $interest = $due_paid_without_fine - $principal;

                    // if($principal >= $remainder){
                    //     $principal -= $remainder;
                    // } else if($principal < $remainder){
                    //     // $diff = $remainder - $principal;
                    //     // $interest -= $diff;
                    //     if($paidAmount >= $fineAmount){
                    //         $blAmount = $paidAmount - $fineAmount;
                    //         if($blAmount >= $interest){
                    //             $principal = $blAmount - $interest;
                    //         } else {
                    //             $interest = $blAmount;
                    //             $principal = 0;
                    //         }
                    //     } else {
                    //         $interest = 0;
                    //         $principal = 0;
                    //     }
                    //     // $principal = 0;
                    // }


                    $loanFineRate = FineRate::where('id', 2)->first();
                    $remainderFineAmount = $remainder * ($loanFineRate->fine_rate / 100);

                    $remainderInstallmentDate = $installments->installment_date;
                    $remainderInstallmentTimeRange = $installments->time_range;
                    while ($remainder > 0){
                        if($remainderInstallmentTimeRange == 3){
                            $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addMonth(1);
                        } else if($remainderInstallmentTimeRange == 2){
                            $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addDay(7);
                        } else if($remainderInstallmentTimeRange == 1){
                            $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addDay(1);
                        }
                        // $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addMonth(1)->format('Y-m-d');
                        // $nextInstallment = LoanInstallment::where('installment_date', $nextInstallmentDate->format('Y-m-d'))->where('loan_id', $request->loan_id)->whereNotIn('status', [0])->first();
                        $nextInstallment = LoanInstallment::where('loan_id', $request->loan_id)->where('status', 2)->first();
                        if($nextInstallment != null){
                            $nextTotAmountToPay = $nextInstallment->amount + $nextInstallment->fine + $remainder + $remainderFineAmount;

                            $nextInstallment->total_amount_to_pay = $nextTotAmountToPay;
                            $nextInstallment->remainder_amount = $remainder;
                            $nextInstallment->fine = $nextInstallment->fine + $remainderFineAmount;
                            $nextInstallment->update();
                            $remainder = 0;
                        } elseif($nextInstallment == null){
                            $newInstallmentDate = $nextInstallmentDate;
                                $installment = new LoanInstallment();
                                $installment->branch_id = \Auth::user()->branchId();
                                $installment->loan_id = $request->loan_id;
                                $installment->amount = $remainder;
                                $installment->fine = $remainderFineAmount;
                                $installment->total_amount_to_pay = $remainder + $remainderFineAmount;
                                $installment->status = 2;
                                $installment->new_installment = 1;
                                $installment->installment_date = $newInstallmentDate->format('Y-m-d');
                                $installment->time_range = $installments->time_range;
                                $installment->save();

                                $remainder = 0;
                        }
                    }
                }

                if($newDuePaidAmount >= $totalDueAmount){
                    $loans->status = 3;

                    $allInstallments = LoanInstallment::where('loan_id', $request->loan_id)->whereNotIn('status', [1,0])->get();
                    foreach($allInstallments as $allInstallment){
                        $allInstallment->status = 1;
                        $allInstallment->total_amount_to_pay = 0;
                        $allInstallment->total_due_amount = 0;
                        $allInstallment->update();
                    }

                    if($loans->loan_type == 2){
                        $customerGroup = $loans->group_id;
                        // $guaranter1Loan = Loan::where('guarantor_1', $request->customer_id)->where('loan_type', 2)->whereIn('status', [1,2,4,5])->get();
                        // $guaranter2Loan = Loan::where('guarantor_2', $request->customer_id)->where('loan_type', 2)->whereIn('status', [1,2,4,5])->get();
                        // $guaranter3Loan = Loan::where('guarantor_3', $request->customer_id)->where('loan_type', 2)->whereIn('status', [1,2,4,5])->get();
                        // $guaranter4Loan = Loan::where('guarantor_4', $request->customer_id)->where('loan_type', 2)->whereIn('status', [1,2,4,5])->get();

                        // if(count($guaranter1Loan) == 0 && count($guaranter2Loan) == 0 && count($guaranter3Loan) == 0 && count($guaranter4Loan) == 0 ){
                            $allgroupMembers = GroupMember::where('status', 1)->where('group_id', $customerGroup)->get();

                            if(count($allgroupMembers) <= 1){
                                foreach($allgroupMembers as $allgroupMember){
                                    $allgroupMember->status = 0;
                                    $allgroupMember->update();
                                }

                                $closeGroup = Group::find($customerGroup);
                                $closeGroup->status = 0;
                                $closeGroup->update();
                            } else {
                                $groupMembers = GroupMember::where('status', 1)->where('customer_id', $loans->customer_id)->get();
                                foreach($groupMembers as $groupMember){
                                    $groupMember->status = 0;
                                    $groupMember->update();
                                }
                            }
                        // }
                    }

                }

                // Getting Principle & interest amounts ============ start =========================>
                // $principal = ( $due_paid_without_fine / $numTot ) * $num1;
                // $interest = $due_paid_without_fine - $principal;

            } elseif(isset($request->pay_total)){

                // Getting Principle & interest amounts ============ start =========================>
                // $thisAmount = $request->installment_amount - $request->released_amount;
                // $thisValue = ( $thisAmount / $numTot ) * $num1;
                // $thisInterest = $thisAmount - $thisValue;

                // $dueLoanValue = $request->due_loan_value;
                // $dueInterest = $request->due_interest_value;

                // // $principal = $thisValue + $dueLoanValue;
                // // $interest = $thisInterest + $dueInterest;
                // $principal += $dueLoanValue;
                // $interest += $dueInterest;
                // Getting Principle & interest amounts ============ end =========================>

                $loans->current_due_amount = 0;
                $loans->status = 3;
                if(isset($request->release_interest)){
                    $loans->released_interest_amount = $request->due_interest_value;
                    // $interest = $thisInterest;
                }
                    $allInstallments = LoanInstallment::where('loan_id', $request->loan_id)->whereNotIn('status', [1,0])->get();
                    foreach($allInstallments as $allInstallment){
                        $allInstallment->status = 1;
                        $allInstallment->total_amount_to_pay = 0;
                        $allInstallment->total_due_amount = 0;
                        $allInstallment->update();
                    }

                    if($loans->loan_type == 2){
                        $customerGroup = $loans->group_id;
                        // $guaranter1Loan = Loan::where('guarantor_1', $request->customer_id)->where('loan_type', 2)->whereIn('status', [1,2,4,5])->get();
                        // $guaranter2Loan = Loan::where('guarantor_2', $request->customer_id)->where('loan_type', 2)->whereIn('status', [1,2,4,5])->get();
                        // $guaranter3Loan = Loan::where('guarantor_3', $request->customer_id)->where('loan_type', 2)->whereIn('status', [1,2,4,5])->get();
                        // $guaranter4Loan = Loan::where('guarantor_4', $request->customer_id)->where('loan_type', 2)->whereIn('status', [1,2,4,5])->get();

                        // if(count($guaranter1Loan) == 0 && count($guaranter2Loan) == 0 && count($guaranter3Loan) == 0 && count($guaranter4Loan) == 0 ){
                        //     $allgroupMembers = GroupMember::where('status', 1)->where('group_id', $customerGroup)->get();
                        //         foreach($allgroupMembers as $allgroupMember){
                        //             $allgroupMember->status = 0;
                        //             $allgroupMember->update();
                        //         }
                        //     $closeGroup = Group::find($customerGroup);
                        //     $closeGroup->status = 0;
                        //     $closeGroup->update();
                        // }

                        $allgroupMembers = GroupMember::where('status', 1)->where('group_id', $customerGroup)->get();

                        if(count($allgroupMembers) <= 1){
                            foreach($allgroupMembers as $allgroupMember){
                                $allgroupMember->status = 0;
                                $allgroupMember->update();
                            }

                            $closeGroup = Group::find($customerGroup);
                            $closeGroup->status = 0;
                            $closeGroup->update();
                        } else {
                            $groupMembers = GroupMember::where('status', 1)->where('customer_id', $loans->customer_id)->get();
                            foreach($groupMembers as $groupMember){
                                $groupMember->status = 0;
                                $groupMember->update();
                            }
                        }
                    }
            }
            $loans->update();

            // Calculations for Account Transactions ============ start =========================>
            $preInstExcessAmount = 0;
            if(isset($request->excess_amount) && $request->excess_amount > 0){
                $preInstExcessAmount = $request->excess_amount;
            }

            // $paidAmount; // paid amount
            // $fineAmount; // fine amount
            // $preInstExcessAmount; // excess from previous
            // $relasedAmount; // manual releasing amount
            // $reminderAmount; // remains from previous
            // $totalAmountToPay; // total amount to pay
            $trThisCapital = $request->this_loan_value;
            $trThisInterest = $request->this_interest_value;
            $trDueCapital = $request->due_loan_value;
            $trDueInterest = $request->due_interest_value;

            $totalPaidAmount = $paidAmount + $preInstExcessAmount - $fineAmount;
            $totAmountToBePaid = $totalAmountToPay - $relasedAmount + $preInstExcessAmount - $fineAmount;
            $InstAmountToBePaid = $trThisCapital + $trThisInterest;

            $accountPaidAmount = $request->paid_amount - $fineAmount;

                if(!isset($request->pay_total)){
                    if($totAmountToBePaid >= $totalPaidAmount){
                        $principal = ( $totalPaidAmount / $numTot ) * $num1;
                        $interest = $totalPaidAmount - $principal;
                    } else {
                        $principal = $trThisCapital;
                        $interest = $trThisInterest;
                        $trExtraPaid = $totalPaidAmount - $totAmountToBePaid;
                        while ($trExtraPaid > 0){
                            if($trExtraPaid < $InstAmountToBePaid){
                                $extraRemains = $totalPaidAmount - $principal - $interest - $excess; // check on extra amount remains on payment
                                if($extraRemains > 0){                                       // check on extra amount remains on payment
                                    $extraPrincipal = ( $extraRemains / $numTot ) * $num1;
                                    $extraInterest = $extraRemains - $extraPrincipal;
                                    $principal += $extraPrincipal;
                                    $interest += $extraInterest;
                                }
                                // $excess = $trExtraPaid;
                                $trExtraPaid = 0;
                            } elseif($trExtraPaid == $InstAmountToBePaid){
                                $principal += $trThisCapital;
                                $interest += $trThisInterest;
                                $trExtraPaid = 0;
                            } elseif($trExtraPaid > $InstAmountToBePaid){
                                $principal += $trThisCapital;
                                $interest += $trThisInterest;
                                $trExtraPaid = $trExtraPaid - $InstAmountToBePaid;
                            }
                        }
                    }

                    if($accountPaidAmount < 0){
                        $accountPaidAmount = 0;
                        $principal = 0;
                        $interest = 0;
                        if($fineAmount > 0){
                            $fineAmount = $request->paid_amount;
                        }
                    }
                } elseif(isset($request->pay_total)){
                    $principal = $trThisCapital + $trDueCapital;
                    $interest = $trThisInterest + $trDueInterest;

                    if(isset($request->release_interest)){
                        $interest = $trThisInterest;
                    }
                }

                $backAccount = BankAccount::find($request->bank);
                // SME
                if($loanType == 1){
                    Utility::accountLoanInstallmentTransaction($request->current_date, $backAccount->chart_of_account_id, 30, $request->loan_id, $request->installment_id, $request->customer_id, 4, $accountPaidAmount, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 30, $backAccount->chart_of_account_id, $request->loan_id, $request->installment_id, $request->customer_id, 4, $accountPaidAmount, 'credit');

                    if(isset($request->fine_amount) && $request->fine_amount > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, $backAccount->chart_of_account_id, 28, $request->loan_id, $request->installment_id, $request->customer_id, 4, $fineAmount, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 28, $backAccount->chart_of_account_id, $request->loan_id, $request->installment_id, $request->customer_id, 4, $fineAmount, 'credit');
                    }


                    // if($request->bank == 1){
                        //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 178, $request->loan_id, $request->installment_id, $request->customer_id, 4, $accountPaidAmount, 'debit');
                        //     Utility::accountLoanInstallmentTransaction($request->current_date, 178, 228, $request->loan_id, $request->installment_id, $request->customer_id, 4, $accountPaidAmount, 'credit');

                        //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                        //         Utility::accountLoanInstallmentTransaction($request->current_date, 228, 323, $request->loan_id, $request->installment_id, $request->customer_id, 4, $fineAmount, 'debit');
                        //         Utility::accountLoanInstallmentTransaction($request->current_date, 323, 228, $request->loan_id, $request->installment_id, $request->customer_id, 4, $fineAmount, 'credit');
                        //     }

                        //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                        //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 322, 228, $request->loan_id, $request->installment_id, $request->customer_id, 4, $preInstExcessAmount, 'debit');
                        //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 322, $request->loan_id, $request->installment_id, $request->customer_id, 4, $preInstExcessAmount, 'credit');
                        //     // }
                        // } else if($request->bank == 2){
                        //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 178, $request->loan_id, $request->installment_id, $request->customer_id, 4, $accountPaidAmount, 'debit');
                        //     Utility::accountLoanInstallmentTransaction($request->current_date, 178, 227, $request->loan_id, $request->installment_id, $request->customer_id, 4, $accountPaidAmount, 'credit');

                        //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                        //         Utility::accountLoanInstallmentTransaction($request->current_date, 227, 323, $request->loan_id, $request->installment_id, $request->customer_id, 4, $fineAmount, 'debit');
                        //         Utility::accountLoanInstallmentTransaction($request->current_date, 323, 227, $request->loan_id, $request->installment_id, $request->customer_id, 4, $fineAmount, 'credit');
                        //     }

                        //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                        //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 322, 227, $request->loan_id, $request->installment_id, $request->customer_id, 4, $preInstExcessAmount, 'debit');
                        //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 322, $request->loan_id, $request->installment_id, $request->customer_id, 4, $preInstExcessAmount, 'credit');
                        //     // }
                        // } else if($request->bank == 3){
                        //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 178, $request->loan_id, $request->installment_id, $request->customer_id, 4, $accountPaidAmount, 'debit');
                        //     Utility::accountLoanInstallmentTransaction($request->current_date, 178, 229, $request->loan_id, $request->installment_id, $request->customer_id, 4, $accountPaidAmount, 'credit');

                        //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                        //         Utility::accountLoanInstallmentTransaction($request->current_date, 229, 323, $request->loan_id, $request->installment_id, $request->customer_id, 4, $fineAmount, 'debit');
                        //         Utility::accountLoanInstallmentTransaction($request->current_date, 323, 229, $request->loan_id, $request->installment_id, $request->customer_id, 4, $fineAmount, 'credit');
                        //     }

                        //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                        //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 322, 229, $request->loan_id, $request->installment_id, $request->customer_id, 4, $preInstExcessAmount, 'debit');
                        //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 322, $request->loan_id, $request->installment_id, $request->customer_id, 4, $preInstExcessAmount, 'credit');
                        //     // }
                    // }

                    if(isset($request->excess_amount) && $request->excess_amount > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, 29, 30, $request->loan_id, $request->installment_id, $request->customer_id, 4, $preInstExcessAmount, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 30, 29, $request->loan_id, $request->installment_id, $request->customer_id, 4, $preInstExcessAmount, 'credit');
                    }

                    Utility::accountLoanInstallmentTransaction($request->current_date, 30, 19, $request->loan_id, $request->installment_id, $request->customer_id, 4, $principal, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 19, 30, $request->loan_id, $request->installment_id, $request->customer_id, 4, $principal, 'credit');

                    Utility::accountLoanInstallmentTransaction($request->current_date, 30, 34, $request->loan_id, $request->installment_id, $request->customer_id, 4, $interest, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 34, 30, $request->loan_id, $request->installment_id, $request->customer_id, 4, $interest, 'credit');

                    if($excess > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, 30, 29, $request->loan_id, $request->installment_id, $request->customer_id, 4, $excess, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 29, 30, $request->loan_id, $request->installment_id, $request->customer_id, 4, $excess, 'credit');
                    }
                }
                // Abimani
                elseif($loanType == 2){
                    // if($request->bank == 1){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 177, $request->loan_id, $request->installment_id, $request->customer_id, 5, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 177, 228, $request->loan_id, $request->installment_id, $request->customer_id, 5, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 228, 323, $request->loan_id, $request->installment_id, $request->customer_id, 5, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 323, 228, $request->loan_id, $request->installment_id, $request->customer_id, 5, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 228, $request->loan_id, $request->installment_id, $request->customer_id, 5, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 29, $request->loan_id, $request->installment_id, $request->customer_id, 5, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 177, $request->loan_id, $request->installment_id, $request->customer_id, 5, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 177, 227, $request->loan_id, $request->installment_id, $request->customer_id, 5, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 227, 323, $request->loan_id, $request->installment_id, $request->customer_id, 5, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 323, 227, $request->loan_id, $request->installment_id, $request->customer_id, 5, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 227, $request->loan_id, $request->installment_id, $request->customer_id, 5, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 29, $request->loan_id, $request->installment_id, $request->customer_id, 5, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 177, $request->loan_id, $request->installment_id, $request->customer_id, 5, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 177, 229, $request->loan_id, $request->installment_id, $request->customer_id, 5, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 229, 323, $request->loan_id, $request->installment_id, $request->customer_id, 5, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 323, 229, $request->loan_id, $request->installment_id, $request->customer_id, 5, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 229, $request->loan_id, $request->installment_id, $request->customer_id, 5, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 29, $request->loan_id, $request->installment_id, $request->customer_id, 5, $preInstExcessAmount, 'credit');
                    //     // }
                    // }

                    // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 177, $request->loan_id, $request->installment_id, $request->customer_id, 5, $preInstExcessAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 177, 29, $request->loan_id, $request->installment_id, $request->customer_id, 5, $preInstExcessAmount, 'credit');
                    // }

                    // Utility::accountLoanInstallmentTransaction($request->current_date, 177, 166, $request->loan_id, $request->installment_id, $request->customer_id, 5, $principal, 'debit');
                    // Utility::accountLoanInstallmentTransaction($request->current_date, 166, 177, $request->loan_id, $request->installment_id, $request->customer_id, 5, $principal, 'credit');

                    // Utility::accountLoanInstallmentTransaction($request->current_date, 177, 1, $request->loan_id, $request->installment_id, $request->customer_id, 5, $interest, 'debit');
                    // Utility::accountLoanInstallmentTransaction($request->current_date, 1, 177, $request->loan_id, $request->installment_id, $request->customer_id, 5, $interest, 'credit');

                    // if($excess > 0){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 177, 29, $request->loan_id, $request->installment_id, $request->customer_id, 5, $excess, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 177, $request->loan_id, $request->installment_id, $request->customer_id, 5, $excess, 'credit');
                    // }
                }
                // Vehicle
                elseif($loanType == 3){
                    Utility::accountLoanInstallmentTransaction($request->current_date, $backAccount->chart_of_account_id, 31, $request->loan_id, $request->installment_id, $request->customer_id, 6, $accountPaidAmount, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 31, $backAccount->chart_of_account_id, $request->loan_id, $request->installment_id, $request->customer_id, 6, $accountPaidAmount, 'credit');

                    if(isset($request->fine_amount) && $request->fine_amount > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, $backAccount->chart_of_account_id, 28, $request->loan_id, $request->installment_id, $request->customer_id, 6, $fineAmount, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 28, $backAccount->chart_of_account_id, $request->loan_id, $request->installment_id, $request->customer_id, 6, $fineAmount, 'credit');
                    }


                    // if($request->bank == 1){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 179, $request->loan_id, $request->installment_id, $request->customer_id, 6, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 179, 228, $request->loan_id, $request->installment_id, $request->customer_id, 6, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 228, 28, $request->loan_id, $request->installment_id, $request->customer_id, 6, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 228, $request->loan_id, $request->installment_id, $request->customer_id, 6, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 228, $request->loan_id, $request->installment_id, $request->customer_id, 6, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 29, $request->loan_id, $request->installment_id, $request->customer_id, 6, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 179, $request->loan_id, $request->installment_id, $request->customer_id, 6, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 179, 227, $request->loan_id, $request->installment_id, $request->customer_id, 6, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 227, 28, $request->loan_id, $request->installment_id, $request->customer_id, 6, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 227, $request->loan_id, $request->installment_id, $request->customer_id, 6, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 227, $request->loan_id, $request->installment_id, $request->customer_id, 6, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 29, $request->loan_id, $request->installment_id, $request->customer_id, 6, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 179, $request->loan_id, $request->installment_id, $request->customer_id, 6, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 179, 229, $request->loan_id, $request->installment_id, $request->customer_id, 6, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 229, 28, $request->loan_id, $request->installment_id, $request->customer_id, 6, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 229, $request->loan_id, $request->installment_id, $request->customer_id, 6, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 229, $request->loan_id, $request->installment_id, $request->customer_id, 6, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 29, $request->loan_id, $request->installment_id, $request->customer_id, 6, $preInstExcessAmount, 'credit');
                    //     // }
                    // }

                    if(isset($request->excess_amount) && $request->excess_amount > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, 29, 31, $request->loan_id, $request->installment_id, $request->customer_id, 6, $preInstExcessAmount, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 31, 29, $request->loan_id, $request->installment_id, $request->customer_id, 6, $preInstExcessAmount, 'credit');
                    }

                    Utility::accountLoanInstallmentTransaction($request->current_date, 31, 168, $request->loan_id, $request->installment_id, $request->customer_id, 6, $principal, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 168, 31, $request->loan_id, $request->installment_id, $request->customer_id, 6, $principal, 'credit');

                    Utility::accountLoanInstallmentTransaction($request->current_date, 31, 35, $request->loan_id, $request->installment_id, $request->customer_id, 6, $interest, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 35, 31, $request->loan_id, $request->installment_id, $request->customer_id, 6, $interest, 'credit');

                    if($excess > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, 31, 29, $request->loan_id, $request->installment_id, $request->customer_id, 6, $excess, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 29, 31, $request->loan_id, $request->installment_id, $request->customer_id, 6, $excess, 'credit');
                    }
                }
                // Land
                elseif($loanType == 4){
                    Utility::accountLoanInstallmentTransaction($request->current_date, $backAccount->chart_of_account_id, 32, $request->loan_id, $request->installment_id, $request->customer_id, 7, $accountPaidAmount, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 32, $backAccount->chart_of_account_id, $request->loan_id, $request->installment_id, $request->customer_id, 7, $accountPaidAmount, 'credit');

                    if(isset($request->fine_amount) && $request->fine_amount > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, $backAccount->chart_of_account_id, 28, $request->loan_id, $request->installment_id, $request->customer_id, 7, $fineAmount, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 28, $backAccount->chart_of_account_id, $request->loan_id, $request->installment_id, $request->customer_id, 7, $fineAmount, 'credit');
                    }
                    // if($request->bank == 1){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 181, $request->loan_id, $request->installment_id, $request->customer_id, 7, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 181, 228, $request->loan_id, $request->installment_id, $request->customer_id, 7, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 228, 28, $request->loan_id, $request->installment_id, $request->customer_id, 7, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 228, $request->loan_id, $request->installment_id, $request->customer_id, 7, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 228, $request->loan_id, $request->installment_id, $request->customer_id, 7, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 29, $request->loan_id, $request->installment_id, $request->customer_id, 7, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 181, $request->loan_id, $request->installment_id, $request->customer_id, 7, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 181, 227, $request->loan_id, $request->installment_id, $request->customer_id, 7, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 227, 28, $request->loan_id, $request->installment_id, $request->customer_id, 7, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 227, $request->loan_id, $request->installment_id, $request->customer_id, 7, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 227, $request->loan_id, $request->installment_id, $request->customer_id, 7, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 29, $request->loan_id, $request->installment_id, $request->customer_id, 7, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 181, $request->loan_id, $request->installment_id, $request->customer_id, 7, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 181, 229, $request->loan_id, $request->installment_id, $request->customer_id, 7, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 229, 28, $request->loan_id, $request->installment_id, $request->customer_id, 7, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 229, $request->loan_id, $request->installment_id, $request->customer_id, 7, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 229, $request->loan_id, $request->installment_id, $request->customer_id, 7, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 29, $request->loan_id, $request->installment_id, $request->customer_id, 7, $preInstExcessAmount, 'credit');
                    //     // }
                    // }

                    if(isset($request->excess_amount) && $request->excess_amount > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, 29, 32, $request->loan_id, $request->installment_id, $request->customer_id, 7, $preInstExcessAmount, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 32, 29, $request->loan_id, $request->installment_id, $request->customer_id, 7, $preInstExcessAmount, 'credit');
                    }

                    Utility::accountLoanInstallmentTransaction($request->current_date, 32, 21, $request->loan_id, $request->installment_id, $request->customer_id, 7, $principal, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 21, 32, $request->loan_id, $request->installment_id, $request->customer_id, 7, $principal, 'credit');

                    Utility::accountLoanInstallmentTransaction($request->current_date, 32, 36, $request->loan_id, $request->installment_id, $request->customer_id, 7, $interest, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 36, 32, $request->loan_id, $request->installment_id, $request->customer_id, 7, $interest, 'credit');

                    if($excess > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, 32, 29, $request->loan_id, $request->installment_id, $request->customer_id, 7, $excess, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 29, 32, $request->loan_id, $request->installment_id, $request->customer_id, 7, $excess, 'credit');
                    }
                }
                // Employment
                elseif($loanType == 5){
                    Utility::accountLoanInstallmentTransaction($request->current_date, $backAccount->chart_of_account_id, 33, $request->loan_id, $request->installment_id, $request->customer_id, 8, $accountPaidAmount, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 33, $backAccount->chart_of_account_id, $request->loan_id, $request->installment_id, $request->customer_id, 8, $accountPaidAmount, 'credit');

                    if(isset($request->fine_amount) && $request->fine_amount > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, $backAccount->chart_of_account_id, 28, $request->loan_id, $request->installment_id, $request->customer_id, 8, $fineAmount, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 28, $backAccount->chart_of_account_id, $request->loan_id, $request->installment_id, $request->customer_id, 8, $fineAmount, 'credit');
                    }
                    // if($request->bank == 1){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 315, $request->loan_id, $request->installment_id, $request->customer_id, 8, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 315, 228, $request->loan_id, $request->installment_id, $request->customer_id, 8, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 228, 28, $request->loan_id, $request->installment_id, $request->customer_id, 8, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 228, $request->loan_id, $request->installment_id, $request->customer_id, 8, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 228, $request->loan_id, $request->installment_id, $request->customer_id, 8, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 228, 29, $request->loan_id, $request->installment_id, $request->customer_id, 8, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 2){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 315, $request->loan_id, $request->installment_id, $request->customer_id, 8, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 315, 227, $request->loan_id, $request->installment_id, $request->customer_id, 8, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 227, 28, $request->loan_id, $request->installment_id, $request->customer_id, 8, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 227, $request->loan_id, $request->installment_id, $request->customer_id, 8, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 227, $request->loan_id, $request->installment_id, $request->customer_id, 8, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 227, 29, $request->loan_id, $request->installment_id, $request->customer_id, 8, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 3){
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 315, $request->loan_id, $request->installment_id, $request->customer_id, 8, $accountPaidAmount, 'debit');
                    //     Utility::accountLoanInstallmentTransaction($request->current_date, 315, 229, $request->loan_id, $request->installment_id, $request->customer_id, 8, $accountPaidAmount, 'credit');

                    //     if(isset($request->fine_amount) && $request->fine_amount > 0){
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 229, 28, $request->loan_id, $request->installment_id, $request->customer_id, 8, $fineAmount, 'debit');
                    //         Utility::accountLoanInstallmentTransaction($request->current_date, 28, 229, $request->loan_id, $request->installment_id, $request->customer_id, 8, $fineAmount, 'credit');
                    //     }

                    //     // if(isset($request->excess_amount) && $request->excess_amount > 0){
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 29, 229, $request->loan_id, $request->installment_id, $request->customer_id, 8, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountLoanInstallmentTransaction($request->current_date, 229, 29, $request->loan_id, $request->installment_id, $request->customer_id, 8, $preInstExcessAmount, 'credit');
                    //     // }
                    // }

                    if(isset($request->excess_amount) && $request->excess_amount > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, 29, 33, $request->loan_id, $request->installment_id, $request->customer_id, 8, $preInstExcessAmount, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 33, 29, $request->loan_id, $request->installment_id, $request->customer_id, 8, $preInstExcessAmount, 'credit');
                    }

                    Utility::accountLoanInstallmentTransaction($request->current_date, 33, 22, $request->loan_id, $request->installment_id, $request->customer_id, 8, $principal, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 22, 33, $request->loan_id, $request->installment_id, $request->customer_id, 8, $principal, 'credit');

                    Utility::accountLoanInstallmentTransaction($request->current_date, 33, 37, $request->loan_id, $request->installment_id, $request->customer_id, 8, $interest, 'debit');
                    Utility::accountLoanInstallmentTransaction($request->current_date, 37, 33, $request->loan_id, $request->installment_id, $request->customer_id, 8, $interest, 'credit');

                    if($excess > 0){
                        Utility::accountLoanInstallmentTransaction($request->current_date, 33, 29, $request->loan_id, $request->installment_id, $request->customer_id, 8, $excess, 'debit');
                        Utility::accountLoanInstallmentTransaction($request->current_date, 29, 33, $request->loan_id, $request->installment_id, $request->customer_id, 8, $excess, 'credit');
                    }
                }


            // arreas calculation
            // get all installments between the current date and the last paid installment from status 1

            $installmentxs = LoanInstallment::where('loan_id', $request->loan_id)
                            ->where('status', 2)
                            ->where('installment_date', '<=',  date('Y-m-d') )
                            ->get();
            // paid_amount
            $arrears = 0;
            foreach($installmentxs as $installmentx){
                $arrears += $installmentx->total_amount_to_pay;
            }

            Receipt::where('id', $receipts->id)->update(['arrears' => $arrears]);

            $loanx = Loan::find($request->loan_id);
            // SMS::sendSMS($loanx->customer->phone_mobile, "Dear Customer, For your loan $receiptCode, we have received a payment of LKR $request->paid_amount, your Loan  Outstanding bal is LKR $installments->total_due_amount Thank you!");
            SMS::sendSMS('94767909709', "Dear Customer, For your loan $receiptCode, we have received a payment of LKR $request->paid_amount, your Loan  Outstanding bal is LKR $installments->total_due_amount, Arrears is LKR $arrears, Thank you!");

            // DB::rollback();
            DB::commit();
            return redirect()->back()->with('success', __('Installment successfully paid!'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function cancelInstallment($id){
        if(\Auth::user()->can('cancel installment loan')){
            $installment = LoanInstallment::find($id);
            $fine = $installment->fine;
            $loan = Loan::where('id', $installment->loan_id)->first();
            $receipt = Receipt::where('loan_id', $installment->loan_id)
                                    ->where('issue_date', $installment->paid_date)
                                    ->where('installment_due_date', $installment->installment_date)
                                    ->where('status', 1)
                                    ->where('paid_amount', $installment->paid_amount)->first();
            if($receipt){
                $receipt->status = 0;
                $receipt->update();
            }

            if($installment->fine != null || $installment->fine > 0){
                $installment->status= 3;
            } else {
                $installment->status= 2;
            }

            $paidAmount = $installment->paid_amount + $installment->manual_released_amount - $fine;

            $pastInstallment = LoanInstallment::where('loan_id', $installment->loan_id)->where('id', '<', $id)->whereNotNull('total_due_amount')->orderBy('id', 'DESC')->first();
            if($pastInstallment){
                $pastInstallmentTotalDueAmount = $pastInstallment->total_due_amount;
            } else {
                $pastInstallmentTotalDueAmount = $loan->total_amount;
            }

            // Normal payment
            if($installment->totaly_paid != 1){
                if($loan){
                    $changingAmount = 0;
                    $currentDueAmountShould = $pastInstallmentTotalDueAmount;
                    $duePaidAmountShould = $loan->total_amount - $currentDueAmountShould;

                        if($paidAmount > $currentDueAmountShould){
                            $changingAmount = $currentDueAmountShould;
                        } else {
                            $changingAmount = $paidAmount;
                        }

                    $loan->current_due_amount = $loan->current_due_amount + $changingAmount;
                    $loan->due_paid_amount = $loan->due_paid_amount - $changingAmount;
                    if($loan->status == 3){ $loan->status = 2; }
                    $loan->update();
                }

                $installmentId = $id;
                if($installment->extra_paid_amount != null){
                    $extraPaid = $installment->extra_paid_amount;
                    while($extraPaid > 0){
                        $nextInstallment = LoanInstallment::where('loan_id', $installment->loan_id)->where('id', '>', $installmentId)->first();

                            if($nextInstallment){
                                $extraPaid = $extraPaid - $nextInstallment->released_amount;
                                $installmentId = $nextInstallment->id;
                                    if($nextInstallment->total_amount_to_pay > 0){
                                        $nextInstallment->total_amount_to_pay = $nextInstallment->total_amount_to_pay + $nextInstallment->released_amount;
                                    } else {
                                        $nextInstallment->total_amount_to_pay =  $nextInstallment->amount + $nextInstallment->fine;
                                    }
                                $nextInstallment->released_amount = null;
                                $nextInstallment->total_due_amount = null;
                                if($nextInstallment->fine != null){
                                    $nextInstallment->status= 3;
                                } else {
                                    $nextInstallment->status= 2;
                                }
                                $nextInstallment->update();
                            } else {
                                $extraPaid = 0;
                            }

                    }
                    $installment->extra_paid_amount = null;
                }

                if($installment->remainder_of_paid_amount != null){
                    $remainderOfPaid = $installment->remainder_of_paid_amount;
                    while($remainderOfPaid > 0){
                        $nextInstallment = LoanInstallment::where('loan_id', $installment->loan_id)->where('id', '>', $installmentId)->first();

                        if($nextInstallment){
                            if($nextInstallment->total_amount_to_pay > 0){
                                $nextInstallment->total_amount_to_pay = $nextInstallment->total_amount_to_pay - $nextInstallment->remainder_amount;
                            } else {
                                $nextInstallment->total_amount_to_pay =  $nextInstallment->installment_amount + $nextInstallment->fine;
                            }
                            $nextInstallment->remainder_amount = null;
                            $nextInstallment->total_due_amount = null;
                            if($nextInstallment->fine != null){
                                $nextInstallment->status= 3;
                            } else {
                                $nextInstallment->status= 2;
                            }
                            $nextInstallment->update();
                            $remainderOfPaid = 0;
                        } else {
                            $remainderOfPaid = 0;
                        }
                    }
                    $installment->remainder_of_paid_amount = null;
                }
            }
            // Totaly Paid payments
            elseif($installment->totaly_paid == 1){

                if($loan){
                    $changingAmount = 0;
                    $currentDueAmountShould = $pastInstallmentTotalDueAmount;
                    $duePaidAmountShould = $loan->total_amount - $currentDueAmountShould;

                        if($paidAmount > $currentDueAmountShould){
                            $loan->current_due_amount = $currentDueAmountShould;
                            $loan->due_paid_amount = $duePaidAmountShould;
                        } else {
                            $loan->current_due_amount = $loan->current_due_amount + $paidAmount + $loan->released_interest_amount;
                            $loan->due_paid_amount = $loan->due_paid_amount - $paidAmount;
                        }

                    $loan->released_interest_amount = null;
                    if($loan->status == 3){ $loan->status = 2; }
                    $loan->update();
                }
                $nextInstallments = LoanInstallment::where('loan_id', $installment->loan_id)->where('id', '>', $id)->where('status', 1)->get();
                foreach($nextInstallments as $nextInstallment){
                    $nextInstallment->total_amount_to_pay =  $nextInstallment->amount + $nextInstallment->fine;
                    $nextInstallment->total_due_amount = null;
                    if($nextInstallment->fine != null){
                        $nextInstallment->status = 3;
                    } else {
                        $nextInstallment->status = 2;
                    }
                    $nextInstallment->update();
                }

                $installment->totaly_paid = null;
                $installment->total_amount_to_pay =  $installment->amount + $installment->fine - $installment->released_amount + $installment->manual_released_amount;
            }

            $installment->released_amount = $installment->released_amount - $installment->manual_released_amount;
            $installment->extra_paid_amount = null;
            $installment->manual_released_amount = null;
            $installment->total_due_amount= null;
            $installment->paid_amount_in_words= null;
            $installment->paid_amount= null;
            $installment->payment_method= null;
            $installment->card_type= null;
            if($installment->card_expence_id != null){
                $cardCharge = CardInterestExpense::find($installment->card_expence_id);
                $cardCharge->status = 0;
                $cardCharge->update();
            }
            $installment->card_expence_id= null;
            $installment->bank_id= null;
            $installment->card_charge_amount= null;
            $installment->total_amount= null;
            $installment->cheque_realised_date= null;
            $installment->cheque_number= null;
            $installment->paid_date= null;
            $installment->update();

            if($loan->loan_type == 2){
                $customerGroup = $loan->group_id;
                $customerId = $loan->customer_id;
                $groupMember = GroupMember::where('group_id', $customerGroup)->where('customer_id', $customerId)->first();
                $groupMember->status = 1;
                $groupMember->update();

                $closeGroup = Group::find($customerGroup);
                $closeGroup->status = 1;
                $closeGroup->update();
            }

            Utility::deleteAccountLoanInstallmentTransaction($id);

            return redirect()->back()->with('success', __('Installment successfully Cancelled!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function receipt($id){
        // $settings = \Utility::settings();
        // $receipt = Receipt::with('invoice')->where('id', $id)->first();

        // $logo         = asset(\Storage::url('uploads/logo/'));
        // $company_logo = \Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        // $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $receipt = Receipt::with('invoice', 'loans')->where('id', $id)->first();

        $user = Receipt::select('users.name')
                            ->join('users', 'users.id', 'receipts.created_by')
                            ->where('receipts.id', $id)
                            ->first();
        return view('loans.receipt', compact('receipt', 'user'));
    }

    public function receiptPrint($id){
        // $settings = \Utility::settings();
        // $receipt = Receipt::with('invoice')->where('id', $id)->first();
        // $logo         = asset(\Storage::url('uploads/logo/'));
        // $company_logo = \Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        // $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $receipt = Receipt::with('invoice', 'loans')->where('id', $id)->first();

        $user = Receipt::select('users.name')
                            ->join('users', 'users.id', 'receipts.created_by')
                            ->where('receipts.id', $id)
                            ->first();
        return view('loans.receipt_print', compact('receipt', 'user'));
    }

    public function cashCollectorReceiptPrint($id){
        $receipt = Receipt::with('invoice', 'loans')->where('id', $id)->first();

        $user = Receipt::select('users.name', 'users.branch_id')
                            ->join('users', 'users.id', 'receipts.created_by')
                            ->where('receipts.id', $id)
                            ->first();
        return view('loans.cash_collector_receipt', compact('receipt', 'user'));
    }

    public function groupSearch($id){
        $groups = Group::where('status', 1)->where('center_id', $id)->get();
        return json_encode($groups);
    }

    public function customerSearch($id){
        $groupMembers = GroupMember::where('status', 1)->where('group_id', $id)->get()->pluck('customer_id');
        $customers = customer::whereIn('id', $groupMembers)->get();
        return json_encode($customers);
    }

    public function guarantorSearch($cid, $gid){
        $groupMembers = GroupMember::where('group_id', $gid)->whereNotIn('customer_id', [$cid])->where('status', 1)->get()->pluck('customer_id');
        return json_encode($groupMembers);
    }

    public function chequePrint($id){
        if(\Auth::user()->can('create transfer'))
        {
            // $supplier = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');
            $loans = Loan::with('customer')->find($id);
            $chequeBanks = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $accounts = GeneralLedgerAccount::orderBy('account_name')->get()->pluck('account_name', 'id');
            return view('loans.create_cheque', compact('loans', 'chequeBanks', 'customers', 'accounts'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function voucherCreate($id){
        if(\Auth::user()->can('create payment voucher'))
        {
            // $supplier = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');
            $loans = Loan::with('customer')->find($id);
            $chequeBanks = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $accounts = GeneralLedgerAccount::orderBy('account_name')->get()->pluck('account_name', 'id');
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');

            if($loans->loan_type == 1){
                $chartAccount = 253;
            } elseif($loans->loan_type == 2){
                $chartAccount = 252;
            } elseif($loans->loan_type == 3){
                $chartAccount = 254;
            } elseif($loans->loan_type == 4){
                $chartAccount = 256;
            } elseif($loans->loan_type == 5){
                $chartAccount = 257;
            }
            return view('loans.create_loan_payment_voucher', compact('loans', 'chequeBanks', 'customers', 'accounts', 'banks', 'chartAccount'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function authenticate(Request $request){
        $email = $request->login_details[0];
        $password = $request->login_details[1];

        $user = User::where('email', $email)->first();

        if($user == null){
            return json_encode('email_error');
        } elseif ($user != null){
            if($user->type == 'company' || $user->type == 'Admin' || $user->type == 'Branch Manager' || $user->type == 'Branch Accountant' ){
                if(Hash::check($password, $user->password)){
                    return json_encode('success');
                } else {
                    return json_encode('password_error');
                }
            } else {
                return json_encode('user_error');
            }
        }
    }

    public function loanAgreement($id){
        $loan = Loan::with('customer.details',  'guarantor1.details','guarantor2.details','guarantor3.details','guarantor4.details')->where('id', $id)->first();

        $customer = $loan->customer;
        $officer = User::where('id', $loan->officer_id)->first();

        $guarant_count =  Loan::where('guarantor_1', $customer->id)
                                ->orWhere('guarantor_2', $customer->id)
                                ->orWhere('guarantor_3', $customer->id)
                                ->orWhere('guarantor_4', $customer->id)
                                ->count();
        $loan_count =  Loan::where('customer_id', $customer->id)
                                ->count();

        return view('loans.loan_agreement', [
            'loan'=>$loan,
            'customer'=>$customer,
            'guarant_count'=>$guarant_count,
            'loan_count' => $loan_count,
            'officer' => $officer
        ]);
    }

    public function loanGuaranterBond($id, Request $request){
        $loan = Loan::with('customer','guarantor1.details','guarantor2.details','guarantor3.details','guarantor4.details')->where('id', $id)->first();

        $gid = $request->gid ?? 1;
        if($gid == 1){
            $guarantor = $loan->guarantor1;
        }
        if($gid == 2){
            $guarantor = $loan->guarantor2;
        }
        if($gid == 3){
            $guarantor = $loan->guarantor3;
        }
        if($gid == 4){
            $guarantor = $loan->guarantor4;
        }

        $guarant_count =  Loan::where('guarantor_1', $guarantor->id)
                                ->orWhere('guarantor_2', $guarantor->id)
                                ->orWhere('guarantor_3', $guarantor->id)
                                ->orWhere('guarantor_4', $guarantor->id)
                                ->count();
        $loan_count =  Loan::where('customer_id', $guarantor->id)
                                ->count();
        return view('loans.guaranter_bond', compact('loan', 'guarantor','guarant_count', 'loan_count'));
    }

    public function loanPromissaryNote($id){
        $loan = Loan::with('customer','guarantor1','guarantor2','guarantor3','guarantor4')->where('id', $id)->first();
        return view('loans.promissary_note', compact('loan'));
    }

    // daily Collection
    public function dailyCollection()
    {
        $users = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $users->prepend('-- Select Officer --', '');
        $centers = Center::get()->pluck('name', 'id');
        $centers->prepend('-- Selecte Center --', '');
        $showTable = 'No';
        return view('loans.daily_collection', compact('users', 'centers', 'showTable'));
    }

    public function dailyCollectionSearch(Request $request)
    {
        $users = User::whereNotIn('type', ['company', 'super admin'])->get()->pluck('name', 'id');
        $users->prepend('-- Select Officer --', '');
        $centers = Center::get()->pluck('name', 'id');
        $centers->prepend('-- Select Center --', '');

        $showTable = 'No';
        $date = $request->date;
        $officerId = $request->officer_id;
        $centerId = $request->center_id;

        if(isset($request->date) && isset($request->officer_id) && isset($request->center_id)){
            $showTable = 'Yes';

            $loans = Loan::with(['customer', 'loanInstallments' => function($innerQuery) use ($date){
                $innerQuery->where('installment_date', $date)
                            ->whereIn('status', [2,3]);
            }])
            ->whereHas('loanInstallments', function($innerQuery) use ($date){
                $innerQuery->where('installment_date', $date)
                            ->whereIn('status', [2,3]);
            })
            ->where('loan_type', 2)
            ->where('status', 2)
            ->where('officer_id', $officerId)
            ->where('center_id', $centerId)
            ->where('branch_id', \Auth::user()->branchId())
            ->get();

            $loanArr = [];
            foreach($loans as $loan){
                $loanArr[] = $loan->id;
            }
            $loanArray = json_encode($loanArr);

            return view('loans.daily_collection', compact('users', 'centers', 'date', 'officerId', 'centerId', 'showTable', 'loans', 'loanArray'));
        }
    }

    public function dailyCollectionShow($id, $loanArray)
    {
        if(\Auth::user()->can('view loan')){
            $loanArr = json_decode($loanArray);
            $pageIndex = 0;
            $prePageIndex = 0;
            $nextPageIndex = 0;
            $lastIndex = count($loanArr) - 1;
            foreach($loanArr as $key=>$arr){
                if($arr == $id){
                    $pageIndex = $key;

                    if($pageIndex != 0){
                        $prePageIndex = $pageIndex - 1;
                    }

                    if($pageIndex != $lastIndex){
                        $nextPageIndex = $pageIndex + 1;
                    } else if($pageIndex == $lastIndex){
                        $nextPageIndex = $lastIndex;
                    }
                }
            }
            $preLoanId = $loanArr[$prePageIndex];
            $nextLoanId = $loanArr[$nextPageIndex];

            $loans = Loan::with(['customer', 'guarantor1', 'guarantor2', 'guarantor3', 'guarantor4', 'center.branches', 'group', 'lastInstallment',
                                'loanInstallments'=>function($query){$query->whereNotIn('status', [0]); },
                                'receipts'=>function($query2){$query2->where('status', 1); }
                                ])->find($id);
            $created = Loan::select('users.id','users.name')
                            ->leftJoin('users', 'users.id', 'loans.created_by')
                            ->where('loans.id', $id)->first();
            $loanOfficer = Loan::select('users.name')
                                ->join('users', 'users.id', 'loans.officer_id')
                                ->where('loans.id', $id)->first();
            return view('loans.daily_collection_view', compact('loans', 'created', 'loanOfficer', 'loanArray', 'preLoanId', 'nextLoanId'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    // Loan installment advance -------------------------------------------------------
    public function getAdvanceAmount(Request $request)
    {
        $installmentAdvances = LoanInstallmentAdvance::where('loan_installment_id', $request->loan_installment_id)->where('status', 1)->get();
        return json_encode($installmentAdvances);
    }

    public function addAdvanceAmount(Request $request)
    {
        $loan = Loan::find($request->loan_id);

        $maxId = Receipt::max('id');
        $receiptCode = "LN".str_pad(($maxId+1), 6, "0", STR_PAD_LEFT);

        $receipt = new Receipt();
        $receipt->branch_id = \Auth::user()->branchId();
        $receipt->receipt_code = $receiptCode;
        $receipt->issue_date = date('Y-m-d');
        $receipt->loan_id = $request->loan_id;
        $receipt->status = 1;
        $receipt->paid_amount = $request->advance_amount;
        $receipt->payment_method = $request->advance_payment_method;
        $receipt->customer_id = $loan->customer->id;
        $receipt->description = "Advance Payment";
        $receipt->created_by = \Auth::user()->id;
        $receipt->save();

        $installmentAdvance = new LoanInstallmentAdvance();
        $installmentAdvance->loan_installment_id = $request->loan_installment_id;
        $installmentAdvance->advance_amount = $request->advance_amount;
        $installmentAdvance->advance_payment_method = $request->advance_payment_method;
        $installmentAdvance->receipt_id = $receipt->id;
        $installmentAdvance->save();

        // $cus = Customer::find($sales->customer->id);

        SMS::sendSMS($loan->customer->phone_mobile, 'Dear Customer, Advance payment successfully paid!, Receipt Number '.$receiptCode);

        return json_encode($receipt);
    }

    public function editAdvanceAmount(Request $request)
    {
        $installmentAdvance = LoanInstallmentAdvance::find($request->installment_advance_id);
        $installmentAdvance->advance_amount = $request->advance_amount;
        $installmentAdvance->advance_payment_method = $request->advance_payment_method;
        $installmentAdvance->update();
    }

    public function removeAdvanceAmount(Request $request)
    {
        $installmentAdvance = LoanInstallmentAdvance::find($request->installment_advance_id);
        $installmentAdvance->status = 0;
        $installmentAdvance->save();
    }


    // sendFine
    public function sendFine(Request $request){

        SMS::sendSMS($request->phone, $request->fine_message);

        return redirect()->back()->with('success' , 'Message Sent Successfully!');
    }

    public function sendTest(Request $request){

        SMS::sendSMS('94766137531', "test SMS");

        return redirect()->back()->with('success' , 'Message Sent Successfully!');
    }


}
