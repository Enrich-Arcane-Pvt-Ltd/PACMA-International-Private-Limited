<?php

namespace App\Http\Controllers;

use App\Group;
use App\GroupMember;
use App\Center;
use App\Customer;
use App\Loan;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage group'))
        {
            $groups = Group::with(['center', 'groupMembers'=>function($mainQuery){ $mainQuery->where('status', 1);}])
                     ->where('branch_id', \Auth::user()->branchId())
                     ->orderBy('id', 'DESC')
                     ->get();
            return view('group.index', compact('groups'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create group'))
        {
            $center = Center::where('status',1)->where('branch_id',\Auth::user()->branchId())->get()->pluck('name', 'id');
            $groupMembers = GroupMember::where('status', 1)->get()->pluck('customer_id')->toArray();
            $customers = Customer::where('status', 1)->where('branch_id',\Auth::user()->branchId())->whereNotIn('id', $groupMembers)->get();
            return view('group.create', compact('center', 'customers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function addMember($id){
        $member = Customer::find($id);
        return json_encode($member);
    }

    public function checkLoan($id){
        $count = Loan::where('customer_id', $id)->whereIn('status', [1,2,4,5])->where('loan_type', 2)->where('branch_id',\Auth::user()->branchId())->get()->count();
        return json_encode($count);
    }

    public function store(Request $request)
    {
        // die(var_dump(count($request->member) > 3));
        if(\Auth::user()->can('create group'))
        {

            if(!isset($request->member))
            {
                return redirect()->back()->with('error', 'Please Add Members!');
            }

            if(count($request->member) <= 2)
            {
                return redirect()->back()->with('error', 'Please add minimun 3 members!');
            }

            $rules = [
                'name' => 'required',
                'center_id' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

                $group = new Group();
                $group->group_id  = $this->groupNumber();
                $group->center_id  = $request->center_id;
                $group->branch_id  = \Auth::user()->branchId();
                $group->name       = $request->name;
                $group->status = 1;
                $group->created_by  = \Auth::user()->id;
                $group->save();

                // $newGroup = Group::latest()->first();
                foreach($request->member as $member){
                    $groupMember = new GroupMember();
                    $groupMember->group_id = $group->id;
                    $groupMember->customer_id = $member['id'];
                    $groupMember->status = 1;
                    $groupMember->created_by = \Auth::user()->id;
                    $groupMember->save();
                }

                return redirect()->back()->with('success', __('Center successfully created !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }



    function groupNumber()
    {
        $latest = Group::latest()->first();

        if(!$latest)
        {
            return 1;
        }
        return $latest->group_id + 1;
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit group'))
        {
            $center = Center::where('status',1)->where('branch_id', \Auth::user()->branchId())->get()->pluck('name', 'id');
            $groupMembers = GroupMember::where('status', 1)->whereNotIn('group_id', [$id])->get()->pluck('customer_id')->toArray();
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereNotIn('id', $groupMembers)->get();

            $group = Group::with(['groupMembers'=>function($query){
                                    $query->with('customers')->where('status', 1);
                                }])->find($id);

            return view('group.edit', compact('center', 'customers', 'group'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request)
    {
        if(\Auth::user()->can('create group'))
        {
            if(!isset($request->member))
            {
                return redirect()->back()->with('error', 'Please Add Members!');
            }

            if(count($request->member) <= 2)
            {
                return redirect()->back()->with('error', 'Please add minimun 3 members!');
            }

            $rules = [
                'name' => 'required',
                'center_id' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

                $group = Group::find($request->group_id);
                $group->center_id  = $request->center_id;
                $group->name       = $request->name;
                $group->created_by  = \Auth::user()->id;
                $group->update();

                $groupMembers = GroupMember::where('group_id', $request->group_id)->get();
                foreach($groupMembers as $groupMember){
                    $groupMember->status = 0;
                    $groupMember->update();
                }

                foreach($request->member as $member){
                    $newGroupMember = new GroupMember();
                    $newGroupMember->group_id = $request->group_id;
                    $newGroupMember->customer_id = $member['id'];
                    $newGroupMember->status = 1;
                    $newGroupMember->created_by = \Auth::user()->id;
                    $newGroupMember->save();
                }

            return redirect()->back()->with('success', __('Group successfully updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function view($id)
    {
        if(\Auth::user()->can('view group'))
        {
            // $branch = Branch::where('created_by', '=', \Auth::user()->creatorId())->where('status', 1)->get()->pluck('name', 'id');
            // $center = Center::where('status',1)->where('branch_id',\Auth::user()->branchId())->get()->pluck('name', 'id');
            // $customers = Customer::where('status', 1)->where('branch_id',\Auth::user()->branchId())->get();
            $groups = Group::with(['center', 'groupMembers'=>function($query){
                $query->with('customers')->where('status', 1);
            }])->find($id);
            return view('group.view', compact('groups'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function block($id){
        if(\Auth::user()->can('block group'))
        {
            $group = Group::find($id);
            $group->status = 0;
            $group->update();
            return redirect()->back()->with('success', __('Group successfully blocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block group'))
        {
            $group = Group::find($id);
            $group->status = 1;
            $group->update();
            return redirect()->back()->with('success', __('Group successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


}
