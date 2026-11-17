<?php

namespace App\Http\Controllers;

use App\TimePeriod;
use Auth;
use Illuminate\Http\Request;

class TimePeriodController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage time period'))
        {
            $timePeriods = TimePeriod::get();
            return view('timePeriod.index', compact('timePeriods'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create time period'))
        {
            return view('timePeriod.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create time period'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'time_period' => 'required|numeric',
                                 ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $time_period               = new TimePeriod();
            $time_period->time_period_id  = $this->timePeriodNumber();
            $time_period->time_period  = $request->time_period;
            $time_period->status       = 1;
            $time_period->created_by   = \Auth::user()->id;
            $time_period->save();

            return redirect()->route('time-period.index')->with('success', __('Time period successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    function timePeriodNumber()
    {
    $latest = TimePeriod::latest()->first();

    if(!$latest)
    {
        return 1;
    }
        return $latest->time_period_id + 1;
    }



    public function edit($id)
    {
        if(\Auth::user()->can('edit time period'))
        {
            $timePeriod = TimePeriod::find($id);
            return view('timePeriod.edit', compact('timePeriod'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit time period'))
        {
            $timePeriod = TimePeriod::find($id);
            // if($timePeriod->created_by == \Auth::user()->creatorId())
            // {
                $validator = \Validator::make(
                    $request->all(), [
                                       'time_period' => 'required|numeric',
                                     ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $timePeriod->time_period = $request->time_period;
                $timePeriod->save();
                return redirect()->route('time-period.index')->with('success', __('Time period successfully updated !!!'));
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


    public function block($id){
        if(\Auth::user()->can('block time period'))
        {
            $timePeriod = TimePeriod::find($id);
            $timePeriod->status = 0;
            $timePeriod->update();
            return redirect()->back()->with('success', __('Time Period successfully blocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block time period'))
        {
            $timePeriod = TimePeriod::find($id);
            $timePeriod->status = 1;
            $timePeriod->update();
            return redirect()->back()->with('success', __('Time Period successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(TimePeriod $timeperiod)
    {
        if(\Auth::user()->can('delete time period')){
            $timeperiod->delete();

            return redirect()->route('timeperiods.index')->with(
                'success', 'Time Period successfully deleted.'
            );
        }
        else
        {
            return redirect()->back()->with('error','Permission denied.');
        }


    }

}
