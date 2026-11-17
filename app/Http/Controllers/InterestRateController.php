<?php

namespace App\Http\Controllers;

use App\InterestRate;
use App\BillProduct;
use App\InvoiceProduct;
use App\ProposalProduct;
use Auth;
use Illuminate\Http\Request;

class InterestRateController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage rate'))
        {
            $interestRates = InterestRate::get();
            return view('interestRate.index', compact('interestRates'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create rate'))
        {
            return view('interestRate.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create rate'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'service_type' => 'required|max:20',
                                   'interest_rate' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $interestRates             = new InterestRate();
            $interestRates->service_type       = $request->service_type;
            $interestRates->interest_rate       = $request->interest_rate;
            $interestRates->status       = 1;
            $interestRates->created_by = \Auth::user()->id;
            $interestRates->save();

            return redirect()->route('interest-rate.index')->with('success', __('Interest rate successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(InterestRate $interestRate)
    {
        return redirect()->route('interest-rate.index');
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit rate'))
        {
            $interestRates   = InterestRate::find($id);
            return view('interestRate.edit', compact('interestRates'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request)
    {
        if(\Auth::user()->can('edit rate'))
        {
            $interestRate = InterestRate::find($request->interest_rate_id);

            // if($interestRate->created_by == \Auth::user()->creatorId())
            // {
                $validator = \Validator::make(
                    $request->all(), [
                                       'service_type' => 'required|max:20',
                                       'interest_rate' => 'required|numeric',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $interestRate->service_type = $request->service_type;
                $interestRate->interest_rate = $request->interest_rate;
                $interestRate->update();

                return redirect()->route('interest-rate.index')->with('success', __('Interest rate successfully updated.'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // public function destroy(Tax $tax)
    // {
    //     if(\Auth::user()->can('delete constant tax'))
    //     {
    //         if($tax->created_by == \Auth::user()->creatorId())
    //         {
    //             $proposalData = ProposalProduct::whereRaw("find_in_set('$tax->id',tax)")->first();
    //             $billData     = BillProduct::whereRaw("find_in_set('$tax->id',tax)")->first();
    //             $invoiceData  = InvoiceProduct::whereRaw("find_in_set('$tax->id',tax)")->first();

    //             if(!empty($proposalData) || !empty($billData) || !empty($invoiceData))
    //             {
    //                 return redirect()->back()->with('error', __('this tax is already assign to proposal or bill or invoice so please move or remove this tax related data.'));
    //             }

    //             $tax->delete();

    //             return redirect()->route('taxes.index')->with('success', __('Interest rate successfully deleted.'));
    //         }
    //         else
    //         {
    //             return redirect()->back()->with('error', __('Permission denied.'));
    //         }
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }
}
