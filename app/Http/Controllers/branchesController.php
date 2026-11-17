<?php

namespace App\Http\Controllers;

use App\Branch;
use App\CustomField;
use Illuminate\Http\Request;

class branchesController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('create bank account'))
        {
            $branches = Branch::get();

            return view('branches.index', ['branches'=>$branches]);

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create bank account'))
        {
            $customFields = CustomField::where('module', '=', 'account')->get();

            return view('branches.create', compact('customFields'));

        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function insert(Request $request)
    {
        if(\Auth::user()->can('create bank account'))
        {


            $branches = new Branch();
            $branches->name     = $request->name;
            $branches->branch_manager     = $request->branch_manager;
            $branches->contact  = $request->contact;
            $branches->address    = $request->address;
            $branches->status = 1;
            $branches->save();


            return redirect()->route('branchesView')->with('success', __('Branch successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(Branch $branch)
    {
        if(\Auth::user()->can('edit bank account'))
        {
            if($branch->created_by == \Auth::user()->creatorId())
            {
                $branch->customField = CustomField::getData($branch, 'account');
                $customFields             = CustomField::where('module', '=', 'account')->get();

                return view('branches.edit', compact('branche','customFields'));
            }

        }

    }


    public function update(Request $request, Branch $branch)
    {
        if(\Auth::user()->can('create bank account'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'branch_manager' => 'required',
                                   'address' => 'required',

                                   'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('branchesView')->with('error', $messages->first());
            }

            $branch = Branch::find($request->branch_id);
            $branch->update($request->all());

            return redirect()->route('branchesView')->with('success', __('Branch successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function unblock($id){
        $branch = Branch::find($id);
        $branch->status = 1;
        $branch->update();

        return redirect()->route('branchesView')->with('success', __('Branch successfully unblocked.'));
    }

    public function unblocktwo($id){

        $branch = Branch::find($id);
        $branch->status = 0;
        $branch->update();

        return redirect()->route('branchesView')->with('success', __('Branch successfully blocked.'));
    }

}
