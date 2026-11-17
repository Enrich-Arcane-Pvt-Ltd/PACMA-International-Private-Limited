<?php

namespace App\Http\Controllers;

use App\Service;
use App\Utility;
use Auth;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage service'))
        {
            $services = Service::where('branch_id', \Auth::user()->creatorId())->orderBy('name')->get();
            return view('service.index', compact('services'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create service'))
        {
            return view('service.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create service'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'account_no' => 'required|numeric',
                                   'service_provider' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $service                  = new Service();
            $service->service_id      = $this->serviceNumber();
            $service->name            = $request->name;
            $service->account_no      = $request->account_no;
            $service->service_provider= $request->service_provider;
            $service->branch_id       = $request->branch_id;
            $service->status          = 1;
            $service->created_by      = \Auth::user()->id;
            $service->save();

            return redirect()->route('service.index')->with('success', __('Service successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function serviceNumber()
    {
        $latest = Service::latest()->first();

        if(!$latest)
        {
            return 1;
        }
        return $latest->service_id + 1;
    }



    public function edit($id)
    {
        if(\Auth::user()->can('edit service'))
        {
            $service = Service::find($id);
            return view('service.edit', compact('service'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }



    public function update(Request $request, Service $service)
    {
        if(\Auth::user()->can('edit service'))
        {
            // if($service->created_by == \Auth::user()->creatorId())
            // {
                $validator = \Validator::make(
                    $request->all(), [
                                    'name' => 'required',
                                    'account_no' => 'required|numeric',
                                    'service_provider' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $service->name = $request->name;
                $service->account_no = $request->account_no;
                $service->service_provider = $request->service_provider;
                $service->save();

                return redirect()->route('service.index')->with('success', __('Service successfully updated !!!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }




    public function block($id){
        if(\Auth::user()->can('block service'))
        {
            $service = Service::find($id);
            $service->status = 0;
            $service->update();
            return redirect()->back()->with('success', __('Service successfully blocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function unblock($id){
        if(\Auth::user()->can('block service'))
        {
            $service = Service::find($id);
            $service->status = 1;
            $service->update();
            return redirect()->back()->with('success', __('Service successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }




    public function destroy(Service $service)
    {
        if(\Auth::user()->can('delete service')){
            $service->delete();

            return redirect()->route('service.index')->with(
                'success', 'service successfully deleted.'
            );
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }

}
