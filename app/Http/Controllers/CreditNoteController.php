<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\CreditNote;
use App\Customer;
use App\Utility;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage credit note'))
        {
            $invoices = Invoice::where('branch_id', \Auth::user()->branchId())->get();

            return view('creditNote.index', compact('invoices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create($invoice_id)
    {
        if(\Auth::user()->can('create credit note'))
        {
            $invoices = Invoice::where('branch_id', \Auth::user()->branchId())->get()->pluck('invoice_id', 'id');
            $invoices->prepend('-- Select Invoice --');
            return view('creditNote.create', compact('invoices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request, $invoice_id)
    {

        if(\Auth::user()->can('create credit note'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric',
                                   'quantity' =>'required',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $invoiceDue = Invoice::where('id', $invoice_id)->first();

            if($request->amount > $invoiceDue->getDue())
            {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoiceDue->getDue()) . ' credit limit of this invoice.');
            }
            $invoice             = Invoice::where('id', $invoice_id)->first();
            $credit              = new CreditNote();
            $credit->invoice     = $invoice_id;
            $credit->customer    = $invoice->customer_id;
            $credit->date        = $request->date;
            $credit->amount      = $request->amount;
            $credit->quantity    = $request->quantity;
            $credit->description = $request->description;
            $credit->save();

            Utility::userBalance('customer', $invoice->customer_id, $request->amount, 'credit');

            return redirect()->back()->with('success', __('Credit Note successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($invoice_id, $creditNote_id)
    {
        if(\Auth::user()->can('edit credit note'))
        {

            $creditNote = CreditNote::find($creditNote_id);

            return view('creditNote.edit', compact('creditNote'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $invoice_id, $creditNote_id)
    {

        if(\Auth::user()->can('edit credit note'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric',
                                   'quantity'=>'required',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $invoiceDue = Invoice::where('id', $invoice_id)->first();
            // if($request->amount > $invoiceDue->getDue())
            // {
            //     return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoiceDue->getDue()) . ' credit limit of this invoice.');
            // }


            $credit = CreditNote::find($creditNote_id);
            Utility::userBalance('customer', $invoiceDue->customer_id, $credit->amount, 'debit');

            $credit->date        = $request->date;
            $credit->amount      = $request->amount;
            $credit->quantity    = $request->quantity;
            $credit->description = $request->description;
            $credit->save();
            Utility::userBalance('customer', $invoiceDue->customer_id, $request->amount, 'credit');

            return redirect()->back()->with('success', __('Credit Note successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($invoice_id, $creditNote_id)
    {
        if(\Auth::user()->can('delete credit note'))
        {

            $creditNote = CreditNote::find($creditNote_id);
            $creditNote->status = 0;
            $creditNote->update();

            Utility::userBalance('customer', $creditNote->customer, $creditNote->amount, 'debit');

            return redirect()->back()->with('success', __('Credit Note successfully deleted.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customCreate()
    {
        if(\Auth::user()->can('create credit note'))
        {
            $invoices = Invoice::where('branch_id', \Auth::user()->branchId())->get()->pluck('invoice_code', 'id');
            $invoices->prepend('-- Select Invoice --');
            return view('creditNote.custom_create', compact('invoices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customStore(Request $request)
    {
        if(\Auth::user()->can('create credit note'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'invoice' => 'required|numeric',
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
            $invoice_id = $request->invoice;
            $invoiceDue = Invoice::where('id', $invoice_id)->first();

            // if($request->amount > $invoiceDue->getDue())
            // {
            //     return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoiceDue->getDue()) . ' credit limit of this invoice.');
            // }
            $invoice             = Invoice::where('id', $invoice_id)->first();
            $credit              = new CreditNote();
            $credit->invoice     = $invoice_id;
            $credit->customer    = $invoice->customer_id;
            $credit->date        = $request->date;
            $credit->amount      = $request->amount;
            $credit->quantity    = $request->quantity;
            $credit->description = $request->description;
            $credit->status      = 1;
            $credit->branch_id   = \Auth::user()->branchId();
            $credit->created_by  = \Auth::user()->id;
            $credit->save();
            Utility::userBalance('customer', $invoice->customer_id, $request->amount, 'credit');

            return redirect()->back()->with('success', __('Credit Note successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getinvoice(Request $request)
    {
        $invoice = Invoice::where('id', $request->invoice_id)->first();
        echo json_encode($invoice->getDue());
    }

    public function printCreditNote($id)
    {
        $creditNote = CreditNote::find($id);
        return view('creditNote.print', compact('creditNote'));
    }
}
