<?php

namespace App\Http\Controllers;

use App\OpeningCashBalance;
use Illuminate\Http\Request;

class OpeningCashBalanceController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage opening cash'))
        {
            $openingBalance = OpeningCashBalance::where('branch_id', '=', \Auth::user()->branchId())->first();

            return view('settings.opening_cash_balance', compact('openingBalance'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request)
    {
        if(\Auth::user()->can('manage opening cash'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'opening_cash' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $openingCash = OpeningCashBalance::where('branch_id', '=', \Auth::user()->branchId())->first();

            if($openingCash){
                $openingCash->amount = $request->opening_cash;
                $openingCash->update();
                return redirect()->back()->with('success', __('Opening Balance has been successfully Updated !!!'));
            } else {
                $newOpeningCash = new OpeningCashBalance();
                $newOpeningCash->branch_id = \Auth::user()->branchId();
                $newOpeningCash->amount = $request->opening_cash;
                $newOpeningCash->status = 1;
                $newOpeningCash->save();
                return redirect()->back()->with('success', __('Opening Balance has been successfully Created !!!'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
