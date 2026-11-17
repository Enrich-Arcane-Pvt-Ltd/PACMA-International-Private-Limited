<?php

namespace App\Http\Controllers;

use App\Bill;
use App\PaymentVoucher;
use App\DebitNote;
use App\Utility;
use Illuminate\Http\Request;

class DebitNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(\Auth::user()->can('manage debit note'))
        {
            $bills = PaymentVoucher::with('debitNotes')->where('branch_id', \Auth::user()->branchId())->get();

            return view('debitNote.index', compact('bills'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create($bill_id)
    {
        if(\Auth::user()->can('create debit note'))
        {

            // $billDue = Bill::where('id', $bill_id)->first();
            $bills = PaymentVoucher::where('branch_id', \Auth::user()->branchId())->get();
            // $bills->prepend('-- Select Bill --');
            return view('debitNote.create', compact('bills'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request, $bill_id)
    {

        if(\Auth::user()->can('create debit note'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric',
                                   'quantity' => 'required',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $billDue = Bill::where('id', $bill_id)->first();

            if($request->amount > $billDue->getDue())
            {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billDue->getDue()) . ' credit limit of this bill.');
            }
            $bill               = Bill::where('id', $bill_id)->first();
            $debit              = new DebitNote();
            $debit->bill        = $bill_id;
            $debit->vendor      = $bill->vender_id;
            $debit->date        = $request->date;
            $debit->amount      = $request->amount;
            $debit->quantity    = $request->quantity;
            $debit->description = $request->description;
            $debit->save();

            Utility::userBalance('vendor', $bill->vender_id, $request->amount, 'debit');

            return redirect()->back()->with('success', __('Credit Note successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($bill_id, $debitNote_id)
    {
        if(\Auth::user()->can('edit debit note'))
        {

            $debitNote = DebitNote::find($debitNote_id);

            return view('debitNote.edit', compact('debitNote'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $bill_id, $debitNote_id)
    {
        if(\Auth::user()->can('edit debit note'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric',
                                   'quantity' => 'required',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $billDue = PaymentVoucher::where('id', $bill_id)->first();
            // if($request->amount > $billDue->getDue())
            // {
            //     return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billDue->getDue()) . ' credit limit of this bill.');
            // }


            $debit = DebitNote::find($debitNote_id);
            // Utility::userBalance('vendor', $billDue->vender_id, $debit->amount, 'credit');

            if($billDue->pay_type == 'supplier'){
                Utility::userBalance('vendor', $billDue->vender_id, $debit->amount, 'credit');
            } else if($billDue->pay_type == 'customer'){
                Utility::userBalance('customer', $billDue->customer_id, $debit->amount, 'credit');
            }

            $debit->date        = $request->date;
            $debit->amount      = $request->amount;
            $debit->quantity    = $request->quantity;
            $debit->description = $request->description;
            $debit->save();
            // Utility::userBalance('vendor', $billDue->vender_id, $request->amount, 'debit');
            if($billDue->pay_type == 'supplier'){
                Utility::userBalance('vendor', $billDue->vender_id, $request->amount, 'debit');
            } else if($billDue->pay_type == 'customer'){
                Utility::userBalance('customer', $billDue->customer_id, $request->amount, 'debit');
            }

            return redirect()->back()->with('success', __('Debit Note successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($bill_id, $debitNote_id)
    {
        if(\Auth::user()->can('delete debit note'))
        {

            $debitNote = DebitNote::find($debitNote_id);
            $debitNote->status = 0;
            $debitNote->update();

            // Utility::userBalance('vendor', $debitNote->vendor, $debitNote->amount, 'credit');
            if($debitNote->vendor != null){
                Utility::userBalance('vendor', $debitNote->vendor, $debitNote->amount, 'credit');
            } else if($debitNote->customer != null){
                Utility::userBalance('customer', $debitNote->customer, $debitNote->amount, 'credit');
            }

            return redirect()->back()->with('success', __('Debit Note successfully deleted.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customCreate()
    {
        if(\Auth::user()->can('create debit note'))
        {
            $bills = PaymentVoucher::where('branch_id', \Auth::user()->branchId())->get();
            // $bills->prepend('-- Select Bill --');
            return view('debitNote.custom_create', compact('bills'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customStore(Request $request)
    {
        if(\Auth::user()->can('create debit note'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'bill' => 'required|numeric',
                                   'amount' => 'required|numeric',
                                   'quantity' => 'required',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            // $bill_id = $request->bill;
            // $billDue = Bill::where('id', $bill_id)->first();

            // if($request->amount > $billDue->getDue())
            // {
            //     return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billDue->getDue()) . ' credit limit of this bill.');
            // }
            $bill               = PaymentVoucher::where('id', $request->bill)->first();

            $debit              = new DebitNote();
            $debit->bill        = $request->bill;
            if($bill->pay_type == 'supplier'){
                $debit->vendor      = $bill->vender_id;
            } else if($bill->pay_type == 'customer'){
                $debit->customer      = $bill->customer_id;
            }
            $debit->date        = $request->date;
            $debit->amount      = $request->amount;
            $debit->quantity     = $request->quantity;
            $debit->description = $request->description;
            $debit->status      = 1;
            $debit->branch_id   = \Auth::user()->branchId();
            $debit->created_by  = \Auth::user()->id;
            $debit->save();

            if($bill->pay_type == 'supplier'){
                Utility::userBalance('vendor', $bill->vender_id, $request->amount, 'debit');
            } else if($bill->pay_type == 'customer'){
                Utility::userBalance('customer', $bill->customer_id, $request->amount, 'debit');
            }
            // Utility::userBalance('vendor', $bill->vender_id, $request->amount, 'debit');

            return redirect()->back()->with('success', __('Debit Note successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getbill(Request $request)
    {

        $bill = PaymentVoucher::where('id', $request->bill_id)->first();
        // echo json_encode($bill->getDue());
    }

    public function printDebitNote($id)
    {
        $debitNote = DebitNote::find($id);
        return view('debitNote.print', compact('debitNote'));
    }
}
