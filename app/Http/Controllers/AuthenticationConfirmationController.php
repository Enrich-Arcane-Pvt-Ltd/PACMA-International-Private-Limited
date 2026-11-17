<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\Customer;
use App\Inventory;
use App\ProductWarrantySerial;
use App\Sale;
use App\TimePeriod;
use App\User;
use App\WaittingInstallment;
use App\WaittingLoanInstallment;
use App\WaittingPawningInstallment;
use App\WaittingSale;
use Illuminate\Http\Request;

class AuthenticationConfirmationController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage authorization')) {
            $sales = WaittingSale::with('warranty')->where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();
            $installments = WaittingInstallment::where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();
            $loanInstallments = WaittingLoanInstallment::where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();
            $pawningInstallments = WaittingPawningInstallment::where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();
            return view('authenticationRequests.index', compact('sales', 'installments', 'loanInstallments', 'pawningInstallments'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getCount()
    {
        $sales = WaittingSale::where('status', 1)->where('branch_id', \Auth::user()->branchId())->count();
        $installments = WaittingInstallment::where('status', 1)->where('branch_id', \Auth::user()->branchId())->count();
        $loanInstallments = WaittingLoanInstallment::where('status', 1)->where('branch_id', \Auth::user()->branchId())->count();
        $pawningInstallments = WaittingPawningInstallment::where('status', 1)->where('branch_id', \Auth::user()->branchId())->count();
        return json_encode($sales + $installments + $loanInstallments + $pawningInstallments);
    }

    public function waittingSaleCreate(Request $request)
    {

        // new working customer
        if ($request->customer_type == 2) {
            $newCustomer = new customer();
            $newCustomer->customer_id = $this->customerNumber();
            $newCustomer->sale_customer_type = 1; //direct sale customer
            $newCustomer->title = $request->customer_title;
            $newCustomer->name = ucwords(strtolower(trim($request->customer_name)));
            $newCustomer->billing_name = ucwords(strtolower(trim($request->customer_name)));
            $newCustomer->billing_address =  ucwords(strtolower(trim($request->customer_address)));
            $newCustomer->nic_no = $request->customer_nic;
            $newCustomer->address =  ucwords(strtolower(trim($request->customer_address)));
            $newCustomer->phone_mobile = $request->customer_phone_no;
            $newCustomer->branch_id = \Auth::user()->branchId();
            $newCustomer->created_by = \Auth::user()->authId();
            $newCustomer->save();
        }
        // END:new working customer

        // Serial/Warranty :START
        $serialNumber = null;
        if ($request->get('serial_card_no')) {
            $serialNumber = ProductWarrantySerial::where('serial_number', $request->get('serial_card_no'))
                ->first(['id'])->id ?? null;
        }
        // END

        $sale             = new WaittingSale();
        $sale->product_warranty_serial_id    = $serialNumber;
        $sale->date_of_sale       = $request->date_of_sale;
        $sale->customer_type       = $request->customer_type;
        if ($request->customer_type == 1) {
            $sale->customer_type       = $request->customer_type;
            $sale->customer_id       = $request->customer;
        } elseif ($request->customer_type == 2) {
            $sale->customer_type       = 1;
            $sale->customer_id       = $newCustomer->id;
        }
        $sale->guarantor_id       = $request->guarantor;
        $sale->guarantor_2_id       = $request->guarantor_2;
        $sale->officer_id       = $request->officer_id;
        $sale->introducer_id       = $request->introducer_id;
        $sale->sale_type         = $request->sale_type;
        $sale->product_id     = $request->product;
        $sale->branch_id       = \Auth::user()->branchId();
        $sale->purchase_price     = $request->purchase_price;
        $sale->unit_price     = $request->unit_price;
        $sale->available_quantity     = $request->available_quantity;
        $sale->quantity     = $request->quantity;
        $sale->gross_amount     = $request->gross_amount;
        $sale->gross_hire_price = $request->gross_hire_price;
        $sale->discount     = $request->discount;
        $sale->net_amount     = $request->net_amount;
        $sale->advance_amount     = $request->advance_amount;

        $sale->paid_amount     = $request->paid_amount;
        $sale->paid_amount_in_words     = $request->paid_amount_in_words;
        $sale->payment_method     = $request->payment_method;
        if ($request->payment_method == 'CARD') {
            $sale->card_type     = $request->card_type;

            $sale->card_charge_amount     = $request->card_charge_amount;
            $sale->total_amount     = $request->total_amount;
        } elseif ($request->payment_method == 'CHEQUE') {
            $sale->cheque_number     = $request->cheque_number;
            $sale->cheque_realised_date     = $request->realised_date;
        }


        if ($request->sale_type == 1) {
            $status = 2;
            $sale->hire_purchase_index     = $request->hire_purchase_index;
            $sale->down_payment     = $request->down_payment;
            $sale->outstanding_amount  = $request->outstanding_amount;
            $sale->installment     = $request->installment;
            $sale->interest_rate     = $request->interest_rate;
            $sale->interest_amount     = $request->interest_amount;
            $sale->total_due_amount     = $request->total_due_amount;
            $sale->current_due_amount     = $request->total_due_amount;
            $sale->monthly_installment     = $request->monthly_installment;
            $sale->installment_date     = $request->installment_date;
            $sale->due_date     = $request->due_date;
            $sale->document_charges     = $request->document_charges;
        }

        $sale->sale_status     = $status;
        $sale->created_by     = \Auth::user()->id;
        $sale->bank_id     = $request->bank;
        $sale->inventory_id     = $request->inventory_id;
        $sale->traveling_charges     = $request->traveling_charges;
        $sale->modal_type     = $request->modal_type;
        $sale->status     = 1;
        $sale->save();
        return redirect()->back()->with('info', 'Sale has been sent for Autherization Sucessfully!');
    }

    public function waittingSaleApprove($id)
    {
        if (\Auth::user()->can('approve authorization')) {

            $customers = Customer::whereIn('type', [0, 2])->where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();
            $guarantors = Customer::whereIn('type', [1, 2])->where('status', 1)->where('branch_id', \Auth::user()->branchId())->get();
            $saleTypes = Sale::$saleType;
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $users = User::where('is_active', 1)->whereNotIn('type', ['company', 'super admin'])->get();

            if (\Auth::user()->branchId() == 0) {
                $products = Inventory::select('product_id', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products.brands')
                    ->where('status', 1)
                    ->groupBy('product_id')
                    ->get()
                    ->sortBy('products.brands.name');
            } else if (\Auth::user()->branchId() != 0) {
                $products = Inventory::select('product_id', \DB::raw('SUM(remain_qty) as remain_qty'))->with('products.brands')
                    ->where('branch_id', \Auth::user()->branchId())
                    ->where('status', 1)
                    ->groupBy('product_id')
                    ->get()
                    ->sortBy('products.brands.name');
            }


            $timePeriods = TimePeriod::where('status', 1)->get();

            $waittingSales = WaittingSale::with('warranty')->where('status', 1)->where('id', $id)->first();

            $selectedProducts = array();

            return view('authenticationRequests.approve_sale', compact('customers', 'guarantors', 'saleTypes', 'products', 'timePeriods', 'banks', 'users', 'waittingSales'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function waittingSaleReject(Request $request)
    {
        if (\Auth::user()->can('approve authorization')) {
            $waittingSale = WaittingSale::find($request->waitting_sale_id);
            $waittingSale->status = 3;
            $waittingSale->approved_rejected_by = \Auth::user()->id;
            $waittingSale->update();

            return redirect()->back()->with('success', 'Sale Sucessfully Rejected!');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function waittingInstallmentCreate(Request $request)
    {
        $installments = new WaittingInstallment();
        $installments->sale_id = $request->sale_id;
        $installments->installment_id = $request->installment_id;
        $installments->invoice_id = $request->invoice_id;
        $installments->customer_id = $request->customer_id;
        $installments->branch_id = \Auth::user()->branchId();
        $installments->next_installment_date = $request->next_installment_date;
        $installments->date = $request->current_date;
        $installments->installment_due_date = $request->installment_due_date;
        $installments->this_sale_value = $request->this_sale_value;
        $installments->this_interest_value = $request->this_interest_value;
        $installments->installment_amount = $request->installment_amount;
        $installments->penalty_charge = $request->add_penalty_amount;
        if (isset($request->due_sale_value)) {
            $installments->due_sale_value = $request->due_sale_value;
        }
        if (isset($request->due_interest_value)) {
            $installments->due_interest_value = $request->due_interest_value;
        }
        if (isset($request->due_installment_amount)) {
            $installments->due_installment_amount = $request->due_installment_amount;
        }
        if (isset($request->pay_total)) {
            $installments->pay_total = 1;
        }
        if (isset($request->release_interest)) {
            $installments->release_interest = 1;
        }
        if (isset($request->pay_total_amount)) {
            $installments->pay_total_amount = $request->pay_total_amount;
        }
        if (isset($request->pre_fine_amount)) {
            $installments->pre_fine_amount = $request->pre_fine_amount;
        }
        if (isset($request->fine_amount)) {
            $installments->fine_amount = $request->fine_amount;
        }
        if (isset($request->release_fine)) {
            $installments->release_fine = 1;
        }
        if (isset($request->excess_amount)) {
            $installments->excess_amount = $request->excess_amount;
        }
        if (isset($request->remainder_amount)) {
            $installments->remainder_amount = $request->remainder_amount;
        }

        $installments->total_amount_tobe_paid = $request->total_amount_tobe_paid;
        $installments->released_amount = $request->released_amount;
        $installments->paid_amount = $request->paid_amount;
        $installments->paid_amount_in_words = $request->paid_amount_in_words;
        $installments->payment_method = $request->payment_method;
        if ($request->payment_method == 'CARD') {
            $installments->card_type = $request->card_type;
            $installments->card_charge_amount = $request->card_charge_amount;
            $installments->total_amount = $request->total_amount;
        } elseif ($request->payment_method == 'CHEQUE') {
            $installments->cheque_number = $request->cheque_number;
            $installments->cheque_realised_date     = $request->realised_date;
        }
        $installments->description = $request->description;
        $installments->created_by = \Auth::user()->id;
        $installments->installment_status = $request->status;
        $installments->status = 1;
        $installments->save();

        return redirect()->back()->with('info', 'Installment has been sent for Autherization Sucessfully!');
    }

    public function waittingInstallmentApprove($id)
    {
        if (\Auth::user()->can('approve authorization')) {
            $waittingInstallment = WaittingInstallment::find($id);
            return view('authenticationRequests.approve_installment', compact('waittingInstallment'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function waittingInstallmentReject(Request $request)
    {
        if (\Auth::user()->can('approve authorization')) {
            $waittingSale = WaittingInstallment::find($request->waitting_installment_id);
            $waittingSale->status = 3;
            $waittingSale->approved_rejected_by = \Auth::user()->id;
            $waittingSale->update();

            return redirect()->back()->with('success', 'Installment Payment Sucessfully Rejected!');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function waittingLoanInstallmentCreate(Request $request)
    {
        $installments = new WaittingLoanInstallment();
        $installments->loan_id = $request->loan_id;
        $installments->installment_id = $request->installment_id;
        $installments->customer_id = $request->customer_id;
        $installments->branch_id = \Auth::user()->branchId();
        $installments->next_installment_date = $request->next_installment_date;
        $installments->date = $request->current_date;
        $installments->installment_due_date = $request->installment_due_date;
        $installments->this_loan_value = $request->this_loan_value;
        $installments->this_interest_value = $request->this_interest_value;
        $installments->installment_amount = $request->installment_amount;
        if (isset($request->due_loan_value)) {
            $installments->due_loan_value = $request->due_loan_value;
        }
        if (isset($request->due_interest_value)) {
            $installments->due_interest_value = $request->due_interest_value;
        }
        if (isset($request->due_installment_amount)) {
            $installments->due_installment_amount = $request->due_installment_amount;
        }
        if (isset($request->pay_total)) {
            $installments->pay_total = 1;
        }
        if (isset($request->release_interest)) {
            $installments->release_interest = 1;
        }
        if (isset($request->pay_total_amount)) {
            $installments->pay_total_amount = $request->pay_total_amount;
        }
        if (isset($request->pre_fine_amount)) {
            $installments->pre_fine_amount = $request->pre_fine_amount;
        }
        if (isset($request->fine_amount)) {
            $installments->fine_amount = $request->fine_amount;
        }
        if (isset($request->release_fine)) {
            $installments->release_fine = 1;
        }
        if (isset($request->excess_amount)) {
            $installments->excess_amount = $request->excess_amount;
        }
        if (isset($request->remainder_amount)) {
            $installments->remainder_amount = $request->remainder_amount;
        }

        $installments->total_amount_tobe_paid = $request->total_amount_tobe_paid;
        $installments->released_amount = $request->released_amount;
        $installments->paid_amount = $request->paid_amount;
        $installments->paid_amount_in_words = $request->paid_amount_in_words;
        $installments->payment_method = $request->payment_method;
        if ($request->payment_method == 'CARD') {
            $installments->card_type = $request->card_type;
            $installments->card_charge_amount = $request->card_charge_amount;
            $installments->total_amount = $request->total_amount;
        } elseif ($request->payment_method == 'CHEQUE') {
            $installments->cheque_number = $request->cheque_number;
            $installments->cheque_realised_date     = $request->realised_date;
        }
        $installments->description = $request->description;
        $installments->created_by = \Auth::user()->id;
        $installments->installment_status = $request->status;
        $installments->status = 1;
        $installments->save();

        return redirect()->back()->with('info', 'Loan Installment has been sent for Autherization Sucessfully!');
    }

    public function waittingLoanInstallmentApprove($id)
    {
        if (\Auth::user()->can('approve authorization')) {
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $waittingLoanInstallment = WaittingLoanInstallment::find($id);
            return view('authenticationRequests.approve_loan_installment', compact('waittingLoanInstallment', 'banks'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function waittingLoanInstallmentReject(Request $request)
    {
        if (\Auth::user()->can('approve authorization')) {
            $waittingLoanInstallment = WaittingLoanInstallment::find($request->waitting_loan_installment_id);
            $waittingLoanInstallment->status = 3;
            $waittingLoanInstallment->approved_rejected_by = \Auth::user()->id;
            $waittingLoanInstallment->update();

            return redirect()->back()->with('success', 'Loan Installment Payment Sucessfully Rejected!');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function waittingPawningInstallmentCreate(Request $request)
    {
        $installments = new WaittingPawningInstallment();
        $installments->pawning_id = $request->pawning_id;
        $installments->installment_id = $request->installment_id;
        $installments->customer_id = $request->customer_id;
        $installments->branch_id = \Auth::user()->branchId();
        $installments->next_installment_date = $request->next_installment_date;
        $installments->paid_date = $request->paid_date;
        $installments->installment_date = $request->installment_date;
        $installments->this_loan_value = $request->this_loan_value;
        $installments->this_interest_value = $request->this_interest_value;
        $installments->installment_amount = $request->installment_amount;
        if (isset($request->due_loan_value)) {
            $installments->due_loan_value = $request->due_loan_value;
        }
        if (isset($request->due_interest_value)) {
            $installments->due_interest_value = $request->due_interest_value;
        }
        if (isset($request->due_installment_amount)) {
            $installments->due_installment_amount = $request->due_installment_amount;
        }
        if (isset($request->pay_total)) {
            $installments->pay_total = 1;
        }
        if (isset($request->release_interest)) {
            $installments->release_interest = 1;
        }
        if (isset($request->pay_total_amount)) {
            $installments->pay_total_amount = $request->pay_total_amount;
        }
        // if(isset($request->pre_fine_amount)){
        //     $installments->pre_fine_amount = $request->pre_fine_amount;
        // }
        // if(isset($request->fine_amount)){
        //     $installments->fine_amount = $request->fine_amount;
        // }
        // if(isset($request->release_fine)){
        //     $installments->release_fine = 1;
        // }
        // if(isset($request->excess_amount)){
        //     $installments->excess_amount = $request->excess_amount;
        // }
        if (isset($request->remainder_amount)) {
            $installments->remainder_amount = $request->remainder_amount;
        }

        $installments->total_amount_tobe_paid = $request->total_amount_tobe_paid;
        $installments->released_amount = $request->released_amount;
        $installments->paid_amount = $request->paid_amount;
        $installments->paid_amount_in_words = $request->paid_amount_in_words;
        $installments->payment_method = $request->payment_method;
        if ($request->payment_method == 'CARD') {
            $installments->card_type = $request->card_type;
            $installments->card_charge_amount = $request->card_charge_amount;
            $installments->total_amount = $request->total_amount;
        } elseif ($request->payment_method == 'CHEQUE') {
            $installments->cheque_number = $request->cheque_number;
            $installments->cheque_realised_date     = $request->realised_date;
        }
        $installments->description = $request->description;
        $installments->created_by = \Auth::user()->id;
        $installments->installment_status = $request->status;
        $installments->status = 1;
        $installments->save();

        return redirect()->back()->with('info', 'Loan Installment has been sent for Autherization Sucessfully!');
    }

    public function waittingPawningInstallmentApprove($id)
    {
        if (\Auth::user()->can('approve authorization')) {
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $waittingPawningInstallment = WaittingPawningInstallment::find($id);
            return view('authenticationRequests.approve_pawning_installment', compact('waittingPawningInstallment', 'banks'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function waittingPawningInstallmentReject(Request $request)
    {
        if (\Auth::user()->can('approve authorization')) {
            $waittingPawningInstallment = WaittingPawningInstallment::find($request->waitting_pawning_installment_id);
            $waittingPawningInstallment->status = 3;
            $waittingPawningInstallment->approved_rejected_by = \Auth::user()->id;
            $waittingPawningInstallment->update();

            return redirect()->back()->with('success', 'Loan Installment Payment Sucessfully Rejected!');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
