<?php

namespace App\Http\Controllers;

use App\ServiceBill;
use App\Service;
use App\Utility;
use Auth;
use Illuminate\Http\Request;

class ServiceBillController extends Controller
{


    public function index()
    {
        if(\Auth::user()->can('manage bill'))
        {
            // $serviceBill = ServiceBill::where('created_by', '=', \Auth::user()->creatorId())->get();

            $service = Service::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $service->prepend('All', '');

            $query = ServiceBill::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'DESC');

            if(!empty($request->service))
            {
                $query->where('id', '=', $request->service);
            }
            $serviceBills = $query->get();


            // return view('serviceBill.index')->with('serviceBill', $serviceBill);
            return view('serviceBill.index', compact('serviceBills', 'service'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create bill'))
        {
            // $services   = Service::select('*', \DB::raw("CONCAT(name,' - ',account_no) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $services   = Service::select('*', \DB::raw("CONCAT(name,' - ',account_no) AS name"))->get()->pluck('name', 'id');

            return view('serviceBill.create', compact('services'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create bill'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                //    'bill_date' => 'required|max:20',
                                //    'service_id' => 'required',
                                //    'bill_amount' => 'required',
                                //    'paid_amount' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $servicebill             = new ServiceBill();
            $servicebill->bill_id   = $this->serviceBillNumber();
            $servicebill->bill_date    = $request->bill_date;
            $servicebill->service_id       = $request->service_id;
            $servicebill->bill_amount       = $request->bill_amount;
            $servicebill->paid_amount       = $request->paid_amount;
            $servicebill->created_by = \Auth::user()->id;
            $servicebill->save();

            var_dump(($servicebill));

            return redirect()->route('serviceBill.index')->with('success', __('Bill successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function serviceBillNumber()
    {
        $latest = ServiceBill::latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->bill_id + 1;
    }



    // public function edit($id)
    // {
    //     if(\Auth::user()->can('edit customer'))
    //     {
    //         $servicebill = ServiceBill::find($id);
    //         return view('serviceBill.edit', compact('servicebill'));
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }


    public function edit($id)
    {
        // if(\Auth::user()->can('create bill'))
        // {
            if(\Auth::user()->can('edit bill'))
            {
                //  $service   = Service::select('*', \DB::raw("CONCAT(name,' ',acct_no) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $services   = Service::select('*', \DB::raw("CONCAT(name,' - ',account_no) AS name"))->get()->pluck('name', 'id');
            $servicebill = ServiceBill::find($id);
            return view('serviceBill.edit', compact('services', 'servicebill'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission denied.')], 401);
        // }
    }


    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit bill'))
        {
            $servicebill = ServiceBill::find($id);
            // if($servicebill->created_by == \Auth::user()->creatorId())
            // {
                $validator = \Validator::make(
                    $request->all(), [
                                    'bill_date' => 'required',
                                   'service_id' => 'required',
                                   'bill_amount' => 'required',
                                   'paid_amount' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $servicebill->bill_amount = $request->bill_amount;
                $servicebill->paid_amount = $request->paid_amount;
                $servicebill->update();

                return redirect()->route('serviceBill.index')->with('success', __('Bill successfully updated.'));
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

    // public function unblock($id){


    //     $servicebill = ServiceBill::find($id);
    //     $servicebill->status = 1;
    //     $servicebill->update();

    //     return redirect()->route('servicebill.index')->with('success', __('Bill successfully unblocked.'));
    // }

    // public function block($id){

    //     $servicebill = ServiceBill::find($id);
    //     $servicebill->status = 0;
    //     $servicebill->update();

    //     return redirect()->route('servicebill.index')->with('success', __('Bill successfully blocked.'));
    // }

    public function destroy($id)
    {
        if(\Auth::user()->can('delete bill')){
            $servicebill = ServiceBill::find($id);
            $servicebill->delete();

            return redirect()->route('serviceBill.index')->with(
                'success', 'Bill successfully deleted.'
            );
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }


    }

}
