<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AccountGroup;
use App\AccountSection;
use App\BankAccount;
use App\ChartOfAccount;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage chart of account'))
        {
            $chartofaccounts = ChartOfAccount::where('status', 1)->orderBy('id', 'DESC')->get();
            return view('chartOfAccount.index', compact('chartofaccounts'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create chart of account'))
        {
            // $grouptypes = AccountGroup::orderBy('type')->get()->pluck('type', 'id');
            // $grouptypes->prepend('-- Select Group Type --');

            $groups = AccountGroup::orderBy('name')->get()->pluck('name', 'id');
            $groups->prepend('-- Select Group Name --','');

            $sections = AccountSection::orderBy('name')->get()->pluck('name', 'id');
            $sections->prepend('-- Select Account Section --','');

            $bankAccounts = BankAccount::orderBy('bank')->get()->pluck('bank', 'id');
            $bankAccounts->prepend('-- Select Account Bank Account --','');

            return view('chartOfAccount.create', compact('groups', 'sections', 'bankAccounts'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create chart of account'))
        {
            $rules = [
                'account_name' => 'required',
                'account_type' => 'required',
                'group' => 'required',
                'section' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
                $group = AccountGroup::find($request->group);
                $section = AccountSection::find($request->section);

                $chartofaccount                  = new ChartOfAccount();
                $chartofaccount->name            = $request->account_name;
                $chartofaccount->type            = $request->account_type;
                $chartofaccount->group_id        = $request->group;
                $chartofaccount->group_type      = $group->type;
                $chartofaccount->group_name      = $group->name;
                $chartofaccount->section_id      = $request->section;
                $chartofaccount->section_name    = $section->name;
                $chartofaccount->status          = 1;
                $chartofaccount->created_by      = \Auth::user()->id;
                $chartofaccount->save();

                return redirect()->back()->with('success', __('Chart of Account successfully created !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }

    public function show($id)
    {
        $chartofaccount = ChartOfAccount::where('id', $id)->first();
        return view('chartOfAccount.show', compact('chartofaccounts'));
    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit chart of account'))
        {
            $chartofaccount = ChartOfAccount::find($id);

            // $grouptypes = AccountGroup::orderBy('type')->get()->pluck('type', 'id');
            // $grouptypes->prepend('-- Select Group Type --');

            $groups = AccountGroup::orderBy('name')->get()->pluck('name', 'id');
            $groups->prepend('-- Select Group Name --','');

            $sections = AccountSection::orderBy('name')->get()->pluck('name', 'id');
            $sections->prepend('-- Select Account Section --','');

            return view('chartOfAccount.edit', compact('chartofaccount', 'groups', 'sections'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id){
        if(\Auth::user()->can('edit chart of account'))
        {
            $rules = [
                'account_name' => 'required',
                'account_type' => 'required',
                'group' => 'required',
                'section' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
                $group = AccountGroup::find($request->group);
                $section = AccountSection::find($request->section);

                $chartofaccount                  = ChartOfAccount::find($id);
                $chartofaccount->name            = $request->account_name;
                $chartofaccount->type            = $request->account_type;
                $chartofaccount->group_id        = $request->group;
                $chartofaccount->group_type      = $group->type;
                $chartofaccount->group_name      = $group->name;
                $chartofaccount->section_id      = $request->section;
                $chartofaccount->section_name    = $section->name;
                $chartofaccount->updated_by      = \Auth::user()->id;
                $chartofaccount->update();

                return redirect()->back()->with('success', __('Chart of Account successfully updated !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }


}
