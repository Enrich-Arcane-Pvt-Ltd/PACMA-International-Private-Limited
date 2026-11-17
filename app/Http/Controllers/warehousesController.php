<?php

namespace App\Http\Controllers;

use App\Warehouse;
use App\CustomField;
use Illuminate\Http\Request;

class warehousesController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('create bank account'))
        {
            $warehouses = Warehouse::get();

            return view('warehouses.index', ['warehouses'=>$warehouses]);
           
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

            return view('warehouses.create', compact('customFields'));
           
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


            $warehouses = new Warehouse();
            $warehouses->name     = $request->name;
            $warehouses->contact  = $request->contact;
            $warehouses->address    = $request->address;
            $warehouses->status = 1;
            $warehouses->save();
            

            return redirect()->route('warehousesView')->with('success', __('Warehouse successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(Warehouse $warehouse)
    {
        if(\Auth::user()->can('edit bank account'))
        {
            if($warehouse->created_by == \Auth::user()->creatorId())
            {
                $warehouse->customField = CustomField::getData($warehouse, 'account');
                $customFields             = CustomField::where('module', '=', 'account')->get();

                return view('warehouses.edit', compact('warehouse','customFields'));
            }
           
        }
       
    }


    public function update(Request $request, Warehouse $warehouse)
    {
        if(\Auth::user()->can('create bank account'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'address' => 'required',
                                
                                   'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('warehousesView')->with('error', $messages->first());
            }

            $warehouse = Warehouse::find($request->warehouse_id);
            $warehouse->update($request->all());
           
            return redirect()->route('warehousesView')->with('success', __('Warehouse successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function unblock1($id){
        $warehouse = Warehouse::find($id);
        $warehouse->status = 1;
        $warehouse->update();

        return redirect()->route('warehousesView')->with('success', __('Warehouse successfully unblocked.'));   
    }

    public function unblock2($id){
       
        $warehouse = Warehouse::find($id);
        $warehouse->status = 0;
        $warehouse->update();

        return redirect()->route('warehousesView')->with('success', __('Warehouse successfully blocked.')); 
    }

}