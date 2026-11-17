<?php

namespace App\Http\Controllers;

use App\GoldRate;
use Auth;
use Illuminate\Http\Request;

class GoldRateController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage rate'))
        {
            $goldRates = GoldRate::get();
            return view('goldRate.index', compact('goldRates'));
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
            return view('goldRate.create');
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
                                   'gold_rate_type' => 'required',
                                   'gold_rate' => 'required|numeric',
                                   'payment_rate' => 'required',
                                 ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $goldRate               = new GoldRate();
            $goldRate->gold_rate_id  = $this->goldRateNumber();
            $goldRate->gold_rate_type  = $request->gold_rate_type;
            $goldRate->gold_rate  = $request->gold_rate;
            $goldRate->payment_rate  = $request->payment_rate;
            $goldRate->status       = 1;
            $goldRate->created_by   = \Auth::user()->id;
            $goldRate->save();

            return redirect()->route('goldRate.index')->with('success', __('Gold Rate successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    function goldRateNumber()
    {
    $latest = GoldRate::latest()->first();

    if(!$latest)
    {
        return 1;
    }
        return $latest->gold_rate_id + 1;
    }



    public function edit($id)
    {
        if(\Auth::user()->can('edit rate'))
        {
            $goldRate = GoldRate::find($id);
            return view('goldRate.edit', compact('goldRate'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit rate'))
        {
            $goldRate = GoldRate::find($id);
            if($goldRate->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'gold_rate_type' => 'required',
                                       'gold_rate' => 'required',
                                        'payment_rate' => 'required',
                                     ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $goldRate->gold_rate = $request->gold_rate;
                $goldRate->payment_rate = $request->payment_rate;
                $goldRate->save();
                return redirect()->route('goldRate.index')->with('success', __('Gold Rate successfully updated !!!'));
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


    public function block($id){
        if(\Auth::user()->can('block rate'))
        {
            $goldRate = GoldRate::find($id);
            $goldRate->status = 0;
            $goldRate->update();
            return redirect()->back()->with('success', __('Gold Rate successfully blocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block rate'))
        {
            $goldRate = GoldRate::find($id);
            $goldRate->status = 1;
            $goldRate->update();
            return redirect()->back()->with('success', __('Gold Rate successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(GoldRate $goldRate)
    {
        if(\Auth::user()->can('delete rate')){
            $goldRate->delete();

            return redirect()->route('timeperiods.index')->with(
                'success', 'Gold Rate successfully deleted.'
            );
        }
        else
        {
            return redirect()->back()->with('error','Permission denied.');
        }


    }

}
