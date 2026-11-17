<?php

namespace App\Http\Controllers;

use App\AdvancePayment;
use App\Customer;
use App\Invoice;
use App\Sale;
use App\CustomField;
use App\CustomReceipt;
use App\Loan;
use App\Mail\UserCreate;
use App\Pawning;
use App\Transaction;
use App\User;
use App\SMS;
use App\CustomerDetail;
use App\Setting;
use Auth;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;



class CustomerController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function dashboard()
    {
        $data['invoiceChartData'] = \Auth::user()->invoiceChartData();
        return view('customer.dashboard', $data);
    }


    public function index()
    {
        if (\Auth::user()->can('manage customer')) {
            $customers = Customer::where('branch_id', '=', \Auth::user()->branchId())->orderBy('id', 'DESC')->get();
            return view('customer.index', compact('customers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create customer')) {
            $title = Customer::$title;
            $marrage = Customer::$marrage;
            $income_period = Customer::$income_period;
            $boolSelect = Customer::$boolSelect;
            $bankAccountType = Customer::$bankAccountType;
            $employment_feild = Customer::$employment_feild;
            return view('customer.create', compact('title', 'marrage', 'income_period', 'boolSelect', 'bankAccountType', 'employment_feild'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        // Log all request data
        Log::info('Request cus data :', $request->all());
        if (\Auth::user()->can('create customer')) {
            $rules = [
                'type' => 'required',
                'title' => 'required',
                'name' => 'required',
                'house_no' => 'required',
                'phone_mobile' => 'required|regex:/^\+?\d{10,}$/',
                'nic_no' => 'required',
                'employment_name' => 'required',
                'employment_number' => 'required',
                'job_title' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('customer.index')->with('error', $messages->first());
            }

            $checkCustomer = Customer::where('nic_no', $request->nic_no)
                ->where('branch_id', \Auth::user()->branchId())
                ->first();
            if ($checkCustomer) {
                return redirect()->route('customer.index')->with('error', 'Customer Already Added!');
            }

            // Initialize the image path variable
            $imagePath = 'assets/img/avatar/user.png'; // Set default image path

            if ($request->hasFile('customer_photo')) {
                $filenameWithExt = $request->file('customer_photo')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('customer_photo')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $path = 'uploads/customer_photo/';
                $dir = storage_path('app/public/uploads/customer_photo/');

                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $imagePath = $path . $fileNameToStore; // Update image path if a file is uploaded
                $request->file('customer_photo')->move($dir, $fileNameToStore);

                $publicDir = public_path('uploads/customer_photo/');
                if (!file_exists($publicDir)) {
                    @mkdir($publicDir, 0777, true);
                }
                @copy($dir . $fileNameToStore, $publicDir . $fileNameToStore);
            }

            $customer = new Customer();
            $customer->customer_id           = $this->customerNumber();
            $customer->title                 = $request->title;
            $customer->name                  = ucwords(strtolower(trim($request->name)));
            $customer->full_name             = ucwords(strtolower(trim($request->full_name)));
            $customer->nic_no                = strtoupper($request->nic_no);
            $customer->email                 = $request->email;
            $customer->dob                   = $request->dob;
            $customer->phone                 = $request->phone;
            $customer->phone_mobile          = $request->phone_mobile;
            $customer->house_no              = $request->house_no ? $request->house_no . ',' : '';
            $customer->address               = ucwords(strtolower(trim($request->address)));
            $customer->address_nic           = ucwords(strtolower(trim($request->address_nic)));
            $customer->resident              = $request->resident;
            $customer->lat_coordinates       = $request->lat_coordinates;
            $customer->lng_coordinates       = $request->lng_coordinates;

            $customer->business_name         = ucwords(strtolower(trim($request->business_name)));
            $customer->employment_name       = ucwords(strtolower(trim($request->employment_name)));

            $customer->employment_number       = $request->employment_number;
            // $customer->employment_place      = $request->employment_place;

            $customer->job_title             = ucwords(strtolower(trim($request->job_title)));

            $customer->monthly_income        = $request->monthly_income;
            $customer->phone_office          = $request->phone_office;
            $customer->relationship_to_hirer = $request->relationship_to_hirer;

            $customer->billing_title         = $request->billing_title;
            $customer->billing_name          = ucwords(strtolower(trim($request->billing_name)));
            $customer->billing_house_no       = $request->billing_house_no ? $this->formatHouseNo($request->billing_house_no) : '';
            $customer->billing_address       = ucwords(strtolower(trim($request->billing_address)));
            $customer->billing_phone         = $request->billing_phone;
            $customer->billing_email         = $request->billing_email;
            $customer->type                  = $request->type;
            $customer->status                = 1;
            $customer->sale_customer_type    = 0;
            $customer->branch_id             = \Auth::user()->branchId();
            $customer->created_by            = \Auth::user()->authId();

            $customer->save();

            // customer detail
            $customerDetail = new CustomerDetail();
            $customerDetail->customer_id = $customer->id;
            $customerDetail->gender = $request->gender;
            $customerDetail->phone_mobile2 = $request->phone_mobile2;
            $customerDetail->business_address = ucwords(strtolower(trim($request->business_address)));
            $customerDetail->business_duration = $this->formatServiceDuration($request);
            $customerDetail->business_duration_on_address = ucwords(strtolower(trim($request->business_duration_on_address)));
            $customerDetail->business_registration = $request->business_registration;
            $customerDetail->business_partners = $request->business_partners;
            $customerDetail->business_employees = $request->business_employees;
            $customerDetail->business_insurance = $request->business_insurance;
            $customerDetail->business_bank_accounts = $request->business_bank_accounts;
            $customerDetail->business_bank_name = $request->business_bank_name;
            $customerDetail->business_bank_account_number = $request->business_bank_account_number;
            $customerDetail->business_bank_type = $request->business_bank_type;
            $customerDetail->any_other_income = $request->any_other_income;
            $customerDetail->any_other_income_details = $request->any_other_income_details;
            $customerDetail->expetation_for_starter_up = $request->expetation_for_starter_up;
            $customerDetail->expetation_for_starter_up_details = $request->expetation_for_starter_up_details;
            $customerDetail->insurance = $request->insurance;
            $customerDetail->group_no = $request->group_no;
            $customerDetail->resident_period = $request->resident_period;
            $customerDetail->spouse_name_address = $request->spouse_name_address;
            $customerDetail->spouse_phone = $request->spouse_phone;
            $customerDetail->spouse_occupation = $request->spouse_occupation;
            $customerDetail->spouse_office_address = $request->spouse_office_address;
            $customerDetail->spouse_income = $request->spouse_income;
            $customerDetail->parents_name = $request->parents_name;
            $customerDetail->parents_address = $request->parents_address;
            $customerDetail->relation_details = $request->relation_details;
            $customerDetail->relation_type = $request->relation_type;
            $customerDetail->route_to_residence = $request->route_to_residence;
            $customerDetail->preferred_payment_date = $request->preferred_payment_date;
            $customerDetail->known_duration = $request->known_duration;
            $customerDetail->vahecle_count = $request->vahecle_count;
            $customerDetail->vehicle_model = ucwords(strtolower(trim($request->vehicle_model)));
            $customerDetail->vehicle_number = strtoupper(trim($request->vehicle_number));

            $customerDetail->vehicle_finance = $request->vehicle_finance;

            $customerDetail->vehicle_finance_amount = $request->vehicle_finance_amount;
            $customerDetail->other_loan = $request->other_loan;
            $customerDetail->other_loan_company = $request->other_loan_company;
            $customerDetail->other_loan_amount = $request->other_loan_amount;
            $customerDetail->other_loan_installment = $request->other_loan_installment;
            $customerDetail->children_names = ucwords(strtolower(trim($request->children_names)));
            $customerDetail->marital_status = $request->marital_status;
            $customerDetail->income_period = $request->income_period;
            $customerDetail->employment_feild = $request->employment_feild;
            $customerDetail->business_profit = $request->business_profit;
            $customerDetail->customer_photo = $imagePath; // Ensure this is set, even if it's the default path

            $customerDetail->save();

            return redirect()->back()->with('success', __('Customer successfully created !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function customerNumber()
    {
        $latest = Customer::latest()->first();

        if (!$latest) {
            return 1;
        }
        return $latest->id + 1;
    }


    public function show($id)
    {
        if (\Auth::user()->can('show customer')) {
            // $customer = Customer::where('id', $id)->first();
            $customer = Customer::with('details')->where('id', $id)->first();

            $sales = Sale::with('installments')->whereIn('status', [0, 1, 2, 3])->where('customer_id', $id)->get();

            $loans = Loan::where('customer_id', $id)->whereIn('status', [0, 1, 2, 3, 4])->get();

            $pawnings = Pawning::where('customer_id', $id)->whereNotIn('status', [0])->get();

            $advances = AdvancePayment::where('customer_id', $id)->get();

            $customReceipts = CustomReceipt::where('customer_id', $id)->where('status', 1)->where('receipt_type', 'customer')->get();

            $guarantorContracts = Sale::with('installments')->whereIn('status', [0, 1, 2, 3])->where('guarantor_id', $id)->orWhere('guarantor_2_id', $id)->get();

            $guarantorLoanContracts = Loan::whereIn('status', [0, 1, 2, 3, 4])->where('guarantor_1', $id)->orWhere('guarantor_2', $id)->orWhere('guarantor_3', $id)->orWhere('guarantor_4', $id)->get();
            // dd($customer);
            $currencySymbol = $this->currencySymbol;
            return view('customer.show', compact('customer', 'sales', 'loans', 'pawnings', 'advances', 'customReceipts', 'guarantorContracts', 'guarantorLoanContracts', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit customer')) {
            $customer = Customer::with('details')->find($id);
            $title = Customer::$title;
            $marrage = Customer::$marrage;
            $income_period = Customer::$income_period;
            $boolSelect = Customer::$boolSelect;
            $bankAccountType = Customer::$bankAccountType;
            $employment_feild = Customer::$employment_feild;

            return view('customer.edit', compact('customer',  'title', 'marrage', 'income_period', 'boolSelect', 'bankAccountType', 'employment_feild'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Customer $customer)
    {
        if (\Auth::user()->can('edit customer')) {
            $rules = [
                'type' => 'required',
                'title' => 'required',
                'name' => 'required',
                'house_no' => 'required',
                'phone_mobile' => 'required|regex:/^\+?\d{10,}$/',
                'nic_no' => 'required',
                'job_title' => 'required',
            ];

            if (\Utility::getValByName('billing_display') == 'on') {
                $rules1     = [
                    'billing_title' => 'required',
                    'billing_name' => 'required',
                    'billing_address' => 'required',
                    'billing_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                    // 'billing_email' => 'required|email',
                ];
                $validator1 = \Validator::make($request->all(), $rules1);
                if ($validator1->fails()) {
                    $messages1 = $validator1->getMessageBag();
                    return redirect()->route('customer.index')->with('error', $messages1->first());
                }
            }

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->route('customer.index')->with('error', $messages->first());
            }

            // Handle customer photo upload
            $imagePath = (!empty($customer->details) && !empty($customer->details->customer_photo))
                ? $customer->details->customer_photo
                : null;

            if ($request->hasFile('customer_photo')) {
                // Delete previous photo if it exists in storage or public
                if ($imagePath && strpos($imagePath, 'uploads/') === 0) {
                    $oldFilePath = storage_path('app/public/' . $imagePath);
                    if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                    $oldPublicPath = public_path($imagePath);
                    if (file_exists($oldPublicPath) && is_file($oldPublicPath)) {
                        @unlink($oldPublicPath);
                    }
                }

                $filenameWithExt = $request->file('customer_photo')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('customer_photo')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $path = 'uploads/customer_photo/';
                $dir = storage_path('app/public/uploads/customer_photo/');

                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $imagePath = $path . $fileNameToStore;
                $request->file('customer_photo')->move($dir, $fileNameToStore);

                $publicDir = public_path('uploads/customer_photo/');
                if (!file_exists($publicDir)) {
                    @mkdir($publicDir, 0777, true);
                }
                @copy($dir . $fileNameToStore, $publicDir . $fileNameToStore);
            }


            // Update customer details
            $customer->title                 = $request->title;
            $customer->name                  = ucwords(strtolower(trim($request->name)));
            $customer->full_name             = ucwords(strtolower(trim($request->full_name)));
            $customer->nic_no                = strtoupper($request->nic_no);
            $customer->email                 = $request->email;
            $customer->dob                   = $request->dob;
            $customer->phone                 = $request->phone;
            $customer->phone_mobile          = $request->phone_mobile;
            $customer->house_no               = $request->house_no ? $this->formatHouseNo($request->house_no) : '';
            $customer->address               = ucwords(strtolower(trim($request->address)));
            $customer->address_nic           = ucwords(strtolower(trim($request->address_nic)));
            $customer->resident              = $request->resident;
            $customer->lat_coordinates       = $request->lat_coordinates;
            $customer->lng_coordinates       = $request->lng_coordinates;

            $customer->business_name         = ucwords(strtolower(trim($request->business_name)));
            $customer->employment_name       = ucwords(strtolower(trim($request->employment_name)));

            $customer->employment_number       = $request->employment_number;
            // $customer->employment_place      = $request->employment_place;

            $customer->job_title             = ucwords(strtolower(trim($request->job_title)));

            $customer->monthly_income        = $request->monthly_income;
            $customer->phone_office          = $request->phone_office;
            $customer->relationship_to_hirer = $request->relationship_to_hirer;

            $customer->billing_title         = $request->billing_title;
            $customer->billing_name          = ucwords(strtolower(trim($request->billing_name)));
            $customer->billing_house_no       = $request->billing_house_no ? $this->formatHouseNo($request->billing_house_no) : '';
            $customer->billing_address       = ucwords(strtolower(trim($request->billing_address)));
            $customer->billing_phone         = $request->billing_phone;
            $customer->billing_email         = $request->billing_email;
            $customer->type                  = $request->type;
            $customer->sale_customer_type    = 0;
            $customer->save();

            // customer detail
            $customerDetail = CustomerDetail::where('customer_id', $customer->id)->first();
            if (empty($customerDetail)) {
                $customerDetail = new CustomerDetail();
                $customerDetail->customer_id = $customer->id;
            }

            $customerDetail->gender = $request->gender;
            $customerDetail->phone_mobile2 = $request->phone_mobile2;
            $customerDetail->business_address = ucwords(strtolower(trim($request->business_address)));
            $customerDetail->business_duration = $this->formatServiceDuration($request);
            $customerDetail->business_duration_on_address = ucwords(strtolower(trim($request->business_duration_on_address)));
            $customerDetail->business_registration = $request->business_registration;
            $customerDetail->business_partners = $request->business_partners;
            $customerDetail->business_employees = $request->business_employees;
            $customerDetail->business_insurance = $request->business_insurance;
            $customerDetail->business_bank_accounts = $request->business_bank_accounts;
            $customerDetail->business_bank_name = $request->business_bank_name;
            $customerDetail->business_bank_account_number = $request->business_bank_account_number;
            $customerDetail->business_bank_type = $request->business_bank_type;
            $customerDetail->any_other_income = $request->any_other_income;
            $customerDetail->any_other_income_details = $request->any_other_income_details;
            $customerDetail->expetation_for_starter_up = $request->expetation_for_starter_up;
            $customerDetail->expetation_for_starter_up_details = $request->expetation_for_starter_up_details;
            $customerDetail->insurance = $request->insurance;
            $customerDetail->group_no = $request->group_no;
            $customerDetail->resident_period = $request->resident_period;
            $customerDetail->spouse_name_address = $request->spouse_name_address;
            $customerDetail->spouse_phone = $request->spouse_phone;
            $customerDetail->spouse_occupation = $request->spouse_occupation;
            $customerDetail->spouse_office_address = $request->spouse_office_address;
            $customerDetail->spouse_income = $request->spouse_income;
            $customerDetail->parents_name = $request->parents_name;
            $customerDetail->parents_address = $request->parents_address;
            $customerDetail->relation_details = $request->relation_details;
            $customerDetail->relation_type = $request->relation_type;
            $customerDetail->route_to_residence = $request->route_to_residence;
            $customerDetail->preferred_payment_date = $request->preferred_payment_date;
            $customerDetail->known_duration = $request->known_duration;
            $customerDetail->vahecle_count = $request->vahecle_count;
            $customerDetail->vehicle_model = ucwords(strtolower(trim($request->vehicle_model)));
            $customerDetail->vehicle_finance = $request->vehicle_finance;
            $customerDetail->vehicle_finance_amount = $request->vehicle_finance_amount;
            $customerDetail->other_loan = $request->other_loan;
            $customerDetail->other_loan_company = $request->other_loan_company;
            $customerDetail->other_loan_amount = $request->other_loan_amount;
            $customerDetail->other_loan_installment = $request->other_loan_installment;
            $customerDetail->children_names = ucwords(strtolower(trim($request->children_names)));
            $customerDetail->marital_status = $request->marital_status;
            $customerDetail->income_period = $request->income_period;
            $customerDetail->employment_feild = $request->employment_feild;
            $customerDetail->business_profit = $request->business_profit;
            $customerDetail->vehicle_number = strtoupper(trim($request->vehicle_number));
            if ($imagePath) {
                $customerDetail->customer_photo = $imagePath;
            }

            $customerDetail->save();

            return redirect()->back()->with('success', __('Customer successfully updated !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    protected function formatServiceDuration(Request $request)
    {
        $years = (int) ($request->service_duration_years ?? 0);
        $months = (int) ($request->service_duration_months ?? 0);
        if ($years > 0 || $months > 0) {
            $parts = [];
            if ($years > 0) {
                $parts[] = $years . ' ' . ($years === 1 ? 'Year' : 'Years');
            }
            if ($months > 0) {
                $parts[] = $months . ' ' . ($months === 1 ? 'Month' : 'Months');
            }
            return implode(' ', $parts);
        }
        return $request->business_duration;
    }

    protected function formatHouseNo($houseNo)
    {
        $trimmedHouseNo = trim($houseNo);
        return $trimmedHouseNo ? rtrim($trimmedHouseNo, ',') . ',' : '';
    }

    public function block($id)
    {
        if (\Auth::user()->can('block customer')) {
            $customer = Customer::find($id);
            $customer->status = 0;
            $customer->update();
            return redirect()->back()->with('success', __('Customer successfully blocked !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id)
    {
        if (\Auth::user()->can('block customer')) {
            $customer = Customer::find($id);
            $customer->status = 1;
            $customer->update();
            return redirect()->back()->with('success', __('Customer successfully unblocked !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function mapView($id)
    {
        if (\Auth::user()->can('show customer')) {
            $customer = Customer::find($id);
            return view('customer.map_view', compact('customer'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }











    public function destroy($id)
    {
        if (\Auth::user()->can('delete customer') || \Auth::user()->type == 'company' || \Auth::user()->type == 'super admin') {
            $customer = Customer::find($id);
            if (!$customer) {
                return redirect()->back()->with('error', __('Customer not found.'));
            }

            // Check if customer has associated transactions
            $hasSales = \DB::table('sales')->where('customer_id', $id)->exists();
            $hasInvoices = \DB::table('invoices')->where('customer_id', $id)->exists();
            $hasLoans = \DB::table('loans')->where('customer_id', $id)
                ->orWhere('guarantor_1', $id)
                ->orWhere('guarantor_2', $id)
                ->orWhere('guarantor_3', $id)
                ->orWhere('guarantor_4', $id)
                ->exists();
            $hasPawnings = \DB::table('pawnings')->where('customer_id', $id)->exists();

            if ($hasSales || $hasInvoices || $hasLoans || $hasPawnings) {
                return redirect()->back()->with('error', __('Cannot delete customer because this customer has associated sales, loans, or pawning records. You can block the customer instead.'));
            }

            // Clean up related customer details if any
            \DB::table('customer_details')->where('customer_id', $id)->delete();
            $customer->delete();

            return redirect()->route('customer.index')->with('success', __('Customer successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function customerLogout(Request $request)
    {
        \Auth::guard('customer')->logout();
        $request->session()->invalidate();
        return redirect()->route('customer.login');
    }


    public function payment(Request $request)
    {
        if (\Auth::user()->can('manage customer payment')) {
            $category = [
                'Invoice' => 'Invoice',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer')->where('type', 'Payment');
            if (!empty($request->date)) {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if (!empty($request->category)) {
                $query->where('category', '=', $request->category);
            }
            $payments = $query->get();

            return view('customer.payment', compact('payments', 'category'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function transaction(Request $request)
    {

        if (\Auth::user()->can('manage customer payment')) {

            $category = [
                'Invoice' => 'Invoice',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer');

            if (!empty($request->date)) {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if (!empty($request->category)) {
                $query->where('category', '=', $request->category);
            }
            $transactions = $query->get();

            return view('customer.transaction', compact('transactions', 'category'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profile()
    {
        $userDetail              = \Auth::user();
        $userDetail->customField = CustomField::getData($userDetail, 'customer');
        $customFields            = CustomField::where('module', '=', 'customer')->get();

        return view('customer.profile', compact('userDetail', 'customFields'));
    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);

        $this->validate(
            $request,
            [
                'name' => 'required|max:120',
                'contact' => 'required',
                'email' => 'required|email|unique:users,email,' . $userDetail['id'],
            ]
        );

        if ($request->hasFile('profile')) {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir        = storage_path('uploads/avatar/');
            $image_path = $dir . $userDetail['avatar'];

            if (File::exists($image_path)) {
                File::delete($image_path);
            }

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);
        }

        if (!empty($request->profile)) {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']    = $request['name'];
        $user['email']   = $request['email'];
        $user['contact'] = $request['contact'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success',
            'Profile successfully updated.'
        );
    }

    public function editBilling(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
                'billing_name' => 'required',
                'billing_country' => 'required',
                'billing_state' => 'required',
                'billing_city' => 'required',
                'billing_phone' => 'required',
                'billing_zip' => 'required',
                'billing_address' => 'required',
            ]
        );
        $input = $request->all();
        $user->fill($input)->save();

        return redirect()->back()->with(
            'success',
            'Profile successfully updated.'
        );
    }

    public function editShipping(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
                'shipping_name' => 'required',
                'shipping_country' => 'required',
                'shipping_state' => 'required',
                'shipping_city' => 'required',
                'shipping_phone' => 'required',
                'shipping_zip' => 'required',
                'shipping_address' => 'required',
            ]
        );
        $input = $request->all();
        $user->fill($input)->save();

        return redirect()->back()->with(
            'success',
            'Profile successfully updated.'
        );
    }

    public function updatePassword(Request $request)
    {
        if (Auth::Check()) {
            $request->validate(
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]
            );
            $objUser          = Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;
            if (Hash::check($request_data['current_password'], $current_password)) {
                $user_id            = Auth::User()->id;
                $obj_user           = Customer::find($user_id);
                $obj_user->password = Hash::make($request_data['new_password']);;
                $obj_user->save();

                return redirect()->back()->with('success', __('Password updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Please enter correct current password.'));
            }
        } else {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }

    public function changeLanquage($lang)
    {

        $user       = Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language Change Successfully!'));
    }


    public function birthDayWish($id)
    {
        $customer = Customer::find($id);

        SMS::sendSMS('0767909709', 'Happy Birthday !!');
    }
}

