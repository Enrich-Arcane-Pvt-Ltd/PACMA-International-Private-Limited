<?php

namespace App\Http\Controllers;

use App\Center;
use App\Branch;
use App\Group;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage center'))
        {
            $centers = Center::with('branch')
                     ->orderBy('name')
                     ->get();
            return view('center.index', compact('centers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create center'))
        {
            $branch = branch::where('status',1)->get()->pluck('name', 'id');
            return view('center.create', compact('branch'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create center'))
        {
            $rules = [
                'name' => 'required',
                'branch_id' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('center.index')->with('error', $messages->first());
            }
                $center = new Center();
                $center->center_id  = $this->centerNumber();
                $center->name       = $request->name;
                $center->branch_id  = $request->branch_id;
                $center->status = 1;
                $center->created_by  = \Auth::user()->id;
                $center->save();

                return redirect()->back()->with('success', __('Center successfully created !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }



    function centerNumber()
    {
        $latest = Center::latest()->first();

        if(!$latest)
        {
            return 1;
        }
        return $latest->center_id + 1;
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit center'))
        {
            $branch = Branch::where('status', 1)->orderBy('name')->get()->pluck('name', 'id');
            $center = Center::find($id);
            return view('center.edit', compact('branch','center'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Center $center)
    {
        if(\Auth::user()->can('edit center'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'branch_id' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('branch.index')->with('error', $messages->first());
            }

            $center->name       = $request->name;
            $center->branch_id  = $request->branch_id;
            $center->save();

            return redirect()->back()->with('success', __('Center successfully updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function block($id){
        if(\Auth::user()->can('block center'))
        {
            $center = Center::find($id);
            $center->status = 0;
            $center->update();

            $groups = Group::where('center_id', $id)->get();
            foreach($groups as$group){
                $group->status = 0;
                $group->update();
            }
            return redirect()->back()->with('success', __('Center successfully blocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block center'))
        {
            $center = Center::find($id);
            $center->status = 1;
            $center->update();
            return redirect()->back()->with('success', __('Center successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


}
