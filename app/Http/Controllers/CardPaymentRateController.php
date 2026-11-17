<?php

namespace App\Http\Controllers;
use App\CardPaymentRate;

use Illuminate\Http\Request;

class CardPaymentRateController extends Controller
{
    public function index(){
        if(\Auth::user()->can('manage rate'))
        {
            $cardRates = CardPaymentRate::get();

            return view('cardPaymentRate.index', compact('cardRates'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(Request $request){
        if(\Auth::user()->can('edit rate'))
        {
            $creditCardRates = CardPaymentRate::find(1);
            $debitCardRates = CardPaymentRate::find(2);
            $creditCardRates->rate = $request->credit_card_rate;
            $debitCardRates->rate = $request->debit_card_rate;
            $creditCardRates->update();
            $debitCardRates->update();

            return redirect()->back()->with('success', __('Card Payment Rates Updated Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getDetail($id){
        $cardRate = CardPaymentRate::find($id);
        return json_encode($cardRate);
    }

}
