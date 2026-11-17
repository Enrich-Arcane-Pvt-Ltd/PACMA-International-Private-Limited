<?php

namespace App\Http\Controllers;

use App\DocumentChargeRate;
use Illuminate\Http\Request;

class DocumentChargeRateController extends Controller
{
    public function index(){
        if(\Auth::user()->can('manage rate'))
        {
            $docCharge = DocumentChargeRate::first();

            return view('docChageRate.index', compact('docCharge'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(Request $request){

        if(\Auth::user()->can('edit rate'))
        {
            $docCharge = DocumentChargeRate::first();
            $docCharge->rate = $request->document_charge_rate;
            $docCharge->update();

            return redirect()->back()->with('success', __('Document Charge Rate successfully updated!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
