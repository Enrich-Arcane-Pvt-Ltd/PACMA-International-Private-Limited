<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AccountGroup;
use App\AccountSection;

class AccountGroupController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage account group'))
        {
            $accountgroups = AccountGroup::orderBy('id', 'DESC')->get();
            return view('accountGroup.index', compact('accountgroups'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create account group'))
        {
            $sections = AccountSection::get()->pluck('name', 'id');
            $sections->prepend('-- Select Account Section --');
            return view('accountGroup.create', compact('sections'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create account group'))
        {
            $rules = [
                'group_name' => 'required',
                'section' => 'required',
                'group_type' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('customer.index')->with('error', $messages->first());
            }
                $section = AccountSection::find($request->section);

                $accountgroup                  = new AccountGroup();
                $accountgroup->name            = $request->group_name;
                $accountgroup->type            = $request->group_type;
                $accountgroup->section_id      = $request->section;
                $accountgroup->section_name    = $section->name;
                $accountgroup->created_by      = \Auth::user()->id;
                $accountgroup->save();

                return redirect()->back()->with('success', __('Account Group successfully created !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }

    public function show($id)
    {
        $accountgroup = AccountGroup::where('id', $id)->first();
        return view('accountGroup.show', compact('accountgroups'));
    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit account group'))
        {
            $accountgroup = AccountGroup::find($id);
            $sections = AccountSection::get()->pluck('name', 'id');
            $sections->prepend('-- Select Account Section --');
            return view('accountGroup.edit', compact('accountgroup', 'sections'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id){
        if(\Auth::user()->can('edit account group'))
        {
            $rules = [
                'group_name' => 'required',
                'section' => 'required',
                'group_type' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('customer.index')->with('error', $messages->first());
            }
                $section = AccountSection::find($request->section);

                $accountgroup                  = AccountGroup::find($id);
                $accountgroup->name            = $request->group_name;
                $accountgroup->type            = $request->group_type;
                $accountgroup->section_id      = $request->section;
                $accountgroup->section_name    = $section->name;
                $accountgroup->updated_by      = \Auth::user()->id;
                $accountgroup->update();

                return redirect()->back()->with('success', __('Account Group successfully created !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }


}
