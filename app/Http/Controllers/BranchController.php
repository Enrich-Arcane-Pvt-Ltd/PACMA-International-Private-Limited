<?php

namespace App\Http\Controllers;

use App\Branch;
use App\GeneralLedgerAccount;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage branch'))
        {
            $branches = Branch::orderBy('name')->get();
            return view('branch.index', compact('branches'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create branch'))
        {
            return view('branch.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create branch'))
        {
            $rules = [
                'name' => 'required',
                'address' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                'branch_manager' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('customer.index')->with('error', $messages->first());
                }

                $generalLedgerAccount = new GeneralLedgerAccount();
                $generalLedgerAccount->account_name = $request->name . ' - Inter-branch Transfer Account';
                $generalLedgerAccount->account_type_id = 2;
                $generalLedgerAccount->detail_type_id = 11;
                $generalLedgerAccount->save();

                $branch = new Branch();
                $branch->branch_id  = $this->branchNumber();
                $branch->name = $request->name;
                $branch->address = $request->address;
                $branch->phone = $request->phone;
                $branch->branch_manager = $request->branch_manager;
                $branch->chart_of_account_id = $generalLedgerAccount->id;
                $branch->status = 1;
                $branch->created_by  = \Auth::user()->id;
                $branch->save();

                return redirect()->back()->with('success', __('Branch successfully created !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }



    function branchNumber()
    {
        $latest = Branch::latest()->first();

        if(!$latest)
        {
            return 1;
        }
        return $latest->branch_id + 1;
    }



    public function edit($id)
    {
        if(\Auth::user()->can('edit branch'))
        {
            $branch = Branch::find($id);
            return view('branch.edit', compact('branch'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Branch $branch)
    {
        if(\Auth::user()->can('create branch'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'address' => 'required',
                                   'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                                   'branch_manager' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('branch.index')->with('error', $messages->first());
            }

            $branch->name            = $request->name;
            $branch->address         = $request->address;
            $branch->phone           = $request->phone;
            $branch->branch_manager  = $request->branch_manager;
            $branch->save();

            return redirect()->back()->with('success', __('Branch successfully updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function block($id){
        if(\Auth::user()->can('block branch'))
        {
            $branch = Branch::find($id);
            $branch->status = 0;
            $branch->update();
            return redirect()->back()->with('success', __('Branch successfully blocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block branch'))
        {
            $branch = Branch::find($id);
            $branch->status = 1;
            $branch->update();
            return redirect()->back()->with('success', __('Branch successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


}
