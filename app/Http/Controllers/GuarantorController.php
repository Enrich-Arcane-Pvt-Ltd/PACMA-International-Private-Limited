<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomField;
use App\Mail\UserCreate;
use App\Plan;
use App\Transaction;
use App\User;
use Auth;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class GuarantorController extends Controller
{

    public function dashboard()
    {
        $data['invoiceChartData'] = \Auth::user()->invoiceChartData();

        return view('customer.dashboard', $data);
    }

    public function index()
    {
        if(\Auth::user()->can('manage customer'))
        {
            $gurantors = Customer::where('created_by', \Auth::user()->creatorId())
                        ->whereIN('type', array(1,2))->get();
            return view('guarantor.index', compact('gurantors'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }




    public function store(Request $request)
    {
        if(\Auth::user()->can('create customer'))
        {

            $rules = [
                'name' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                'email' => 'required|email|unique:customers',
                'billing_address' => 'required',
            ];


            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('guarantor.index')->with('error', $messages->first());
            }

                $customer                  = new Customer();
                $customer->customer_id     = $this->customerNumber();
                $customer->title           = $request->title;
                $customer->name            = $request->name;
                $customer->address         = $request->address;
                $customer->phone           = $request->phone;
                $customer->email           = $request->email;
                $customer->nic_no          = $request->nic_no;
                $customer->billing_title   = $request->billing_title;
                $customer->billing_name    = $request->billing_name;
                $customer->billing_address = $request->billing_address	;
                $customer->billing_phone   = $request->billing_phone;
                $customer->billing_email    = $request->billing_email;
                $customer->type = $request->type;
                $customer->status  = 1;
                $customer->created_by      = \Auth::user()->creatorId();

                $customer->save();

                return redirect()->route('guarantor.index')->with('success', __('Customer successfully created.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }

            // $role_r = Role::where('name', '=', 'customer')->firstOrFail();
            // $customer->assignRole($role_r);

            // // $customer->password = $request->password;
            // $customer->type     = 'Customer';
            // try
            // {
            //     Mail::to($customer->email)->send(new UserCreate($customer));
            // }
            // catch(\Exception $e)
            // {
            //     $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            // }

            // return redirect()->route('customer.index')->with('success', __('Customer successfully created.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));

    }
    
    function customerNumber()
    {
    $latest = Customer::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        
    if(!$latest)
    {
    return 1;
    }

    return $latest->customer_id + 1;
    }

    
    
    
    // public function show(Customer $customer)
    // {

    //     return view('guarantor.show', compact('customer'));
    // }


    public function show($id)
    {
        $customer = Customer::where('id', $id)->first();
        return view('guarantor.show', compact('customer'));
    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit customer'))
        {
            $customer              = Customer::find($id);
            $customer->customField = CustomField::getData($customer, 'customer');

            $customFields = CustomField::where('module', '=', 'customer')->get();

            return view('guarantor.edit', compact('customer', 'customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Customer $customer)
    {

        if(\Auth::user()->can('edit customer'))
        {

            $rules = [
                'name' => 'required',
                'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',


            ];



            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('guarantor.index')->with('error', $messages->first());
            }

            $customer->name            = $request->name;
            $customer->last_name       = $request->last_name;
            $customer->contact         = $request->contact;
            $customer->nic             = $request->nic;
            $customer->gender          = $request->gender;
            $customer->acc_number      = $request->acc_number;
            $customer->occupation      = $request->occupation;
            $customer->billing_address = $request->billing_address;
            $customer->marital_status  = $request->marital_status;
            $customer->customer_status  = $request->customer_status;
            $customer->guarantor_status  = $request->guarantor_status;
            $customer->created_by       = \Auth::user()->creatorId();
            $customer->save();

            CustomField::saveData($customer, $request->customField);

            return redirect()->route('customer.index')->with('success', __('Customer successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Customer $customer)
    {
        if(\Auth::user()->can('delete customer'))
        {
            if($customer->created_by == \Auth::user()->creatorId())
            {
                $customer->delete();

                return redirect()->route('customer.index')->with('success', __('Customer successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
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

        if(\Auth::user()->can('manage customer payment'))
        {
            $category = [
                'Invoice' => 'Invoice',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer')->where('type', 'Payment');
            if(!empty($request->date))
            {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if(!empty($request->category))
            {
                $query->where('category', '=', $request->category);
            }
            $payments = $query->get();

            return view('customer.payment', compact('payments', 'category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function transaction(Request $request)
    {

        if(\Auth::user()->can('manage customer payment'))
        {

            $category = [
                'Invoice' => 'Invoice',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer');

            if(!empty($request->date))
            {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if(!empty($request->category))
            {
                $query->where('category', '=', $request->category);
            }
            $transactions = $query->get();

            return view('customer.transaction', compact('transactions', 'category'));
        }
        else
        {
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
            $request, [
                        'name' => 'required|max:120',
                        'contact' => 'required',
                        'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                    ]
        );

        if($request->hasFile('profile'))
        {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir        = storage_path('uploads/avatar/');
            $image_path = $dir . $userDetail['avatar'];

            if(File::exists($image_path))
            {
                File::delete($image_path);
            }

            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }

            $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);

        }

        if(!empty($request->profile))
        {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']    = $request['name'];
        $user['email']   = $request['email'];
        $user['contact'] = $request['contact'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }

    public function editBilling(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
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
            'success', 'Profile successfully updated.'
        );
    }

    public function editShipping(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
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
            'success', 'Profile successfully updated.'
        );
    }

    public function updatePassword(Request $request)
    {
        if(Auth::Check())
        {
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
            if(Hash::check($request_data['current_password'], $current_password))
            {
                $user_id            = Auth::User()->id;
                $obj_user           = Customer::find($user_id);
                $obj_user->password = Hash::make($request_data['new_password']);;
                $obj_user->save();

                return redirect()->back()->with('success', __('Password updated successfully.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Please enter correct current password.'));
            }
        }
        else
        {
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

    // public function unblock($id){


    //     $customer = Customer::find($id);
    //     $customert->active = 1;
    //     $customer->update();

    //     return redirect()->route('customer.index')->with('success', __('Warehouse successfully unblocked.'));
    // }

    // public function unblocktwo($id){

    //     $customer = Customer::find($id);
    //     $customer->active = 0;
    //     $customer->update();

    //     return redirect()->route('customer.index')->with('success', __('Warehouse successfully blocked.'));
    // }


}
