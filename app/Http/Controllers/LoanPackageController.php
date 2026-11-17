<?php

namespace App\Http\Controllers;

use App\LoanPackage;
use Illuminate\Http\Request;

class LoanPackageController extends Controller
{
    public function index(){
        if(\Auth::user()->can('manage loan package'))
        {
            $packages = LoanPackage::where('branch_id', \Auth::user()->branchId())->get();
            return view('loanPackages.index', compact('packages'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create(){
        if(\Auth::user()->can('create loan package'))
        {
            return view('loanPackages.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request){
        if(\Auth::user()->can('create loan package'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name' => 'required',
                                    'loan_amount' => 'required',
                                    'interest_rate' => 'required',
                                    'time_period' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $package = new LoanPackage();
            $package->branch_id = \Auth::user()->branchId();
            $package->name = $request->name;
            $package->loan_amount = $request->loan_amount;
            $package->interest_rate = $request->interest_rate;
            $package->time_period = $request->time_period;
            $package->status = 1;
            $package->created_by = \Auth::user()->id;
            $package->save();
            return redirect()->back()->with('success', __('Package Created Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id){
        if(\Auth::user()->can('edit loan package'))
        {
            $package = LoanPackage::find($id);
            return view('loanPackages.edit', compact('package'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $package){
        if(\Auth::user()->can('edit loan package'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name' => 'required',
                                    'loan_amount' => 'required',
                                    'interest_rate' => 'required',
                                    'time_period' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $package = LoanPackage::find($package);
            $package->name = $request->name;
            $package->loan_amount = $request->loan_amount;
            $package->interest_rate = $request->interest_rate;
            $package->time_period = $request->time_period;
            $package->update();
            return redirect()->back()->with('success', __('Package Updated Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function block($id){
        if(\Auth::user()->can('block loan package'))
        {
            $package = LoanPackage::find($id);
            $package->status = 0;
            $package->update();
            return redirect()->back()->with('success', __('Package Successfully Blocked!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function unblock($id){
        if(\Auth::user()->can('block loan package'))
        {
            $package = LoanPackage::find($id);
            $package->status = 1;
            $package->update();
            return redirect()->back()->with('success', __('Package Successfully Blocked!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getDetail($id){
        $package = LoanPackage::find($id);
        return json_encode($package);
    }
}
