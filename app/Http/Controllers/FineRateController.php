<?php

namespace App\Http\Controllers;

use App\BillProduct;
use App\InvoiceProduct;
use App\ProposalProduct;
use App\Tax;
use App\FineRate;
use Auth;
use Illuminate\Http\Request;

class FineRateController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage rate'))
        {
            $fineRates = FineRate::get();

            return view('fineRates.index')->with('fineRates', $fineRates);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create constant tax'))
        {
            // return view('fineRates.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create constant tax'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                //    'name' => 'required|max:20',
                                //    'rate' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $fineRate = new FineRate();
            $fineRate->service_type = $request->service_type;
            $fineRate->fine_rate = $request->fine_rate;
            $fineRate->status       = $request->status;
            $fineRate->created_by = \Auth::user()->creatorId();
            $fineRate->save();

            return redirect()->route('finerateView')->with('success', __('Fine rate successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(FineRate $fineRate)
    {
        return redirect()->route('fineRates.index');
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit rate'))
        {
            $fineRate   = FineRate::find($id);
            return view('fineRates.edit', compact('fineRate'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, FineRate $fineRate)
    {
        if(\Auth::user()->can('edit rate'))
        {
            // if($tax->created_by == \Auth::user()->creatorId())
            // {
                $validator = \Validator::make(
                    $request->all(), [

                                       'fine_rate' => 'required|numeric',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                // $fineRate = FineRate::find($request->fine_rate_id);
                $fineRate->fine_rate     = $request->fine_rate;
                $fineRate->update();

                return redirect()->route('fine_rate.index')->with('success', __('Fine rate successfully updated.'));
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
}
