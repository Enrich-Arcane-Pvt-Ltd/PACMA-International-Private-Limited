<?php

namespace App\Http\Controllers;

use App\PawningJewellery;
use Auth;
use Illuminate\Http\Request;

class PawningJewelleryController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage pawning jewellery'))
        {
            $pawningJewelleries = PawningJewellery::orderBy('name')->get();
            return view('pawningJewellery.index', compact('pawningJewelleries'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create pawning jewellery'))
        {
            return view('pawningJewellery.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create pawning jewellery'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                 ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $pawningJewellery               = new PawningJewellery();
            $pawningJewellery->jewellery_id  = $this->jewelleryNumber();
            $pawningJewellery->name  = $request->name;
            $pawningJewellery->status       = 1;
            $pawningJewellery->created_by   = \Auth::user()->id;
            $pawningJewellery->save();

            return redirect()->route('pawningJewellery.index')->with('success', __('Pawning Jewellery successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    function jewelleryNumber()
    {
    $latest = PawningJewellery::latest()->first();

    if(!$latest)
    {
        return 1;
    }
        return $latest->jewellery_id + 1;
    }



    public function edit($id)
    {
        if(\Auth::user()->can('edit pawning jewellery'))
        {
            $pawningJewellery = PawningJewellery::find($id);
            return view('pawningJewellery.edit', compact('pawningJewellery'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit pawning jewellery'))
        {
            $pawningJewellerry = PawningJewellery::find($id);
            if($pawningJewellerry->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                     ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $pawningJewellerry->name = $request->name;
                $pawningJewellerry->save();
                return redirect()->route('pawningJewellery.index')->with('success', __('Pawning Jewellery successfully updated !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function block($id){
        if(\Auth::user()->can('block pawning jewellery'))
        {
            $pawningJewellerry = PawningJewellery::find($id);
            $pawningJewellerry->status = 0;
            $pawningJewellerry->update();
            return redirect()->back()->with('success', __('Pawning Jewellery successfully blocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block pawning jewellery'))
        {
            $pawningJewellerry = PawningJewellery::find($id);
            $pawningJewellerry->status = 1;
            $pawningJewellerry->update();
            return redirect()->back()->with('success', __('Pawning Jewellery successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(pawningJewellery $pawningJewellery)
    {
        if(\Auth::user()->can('delete pawning jewellery')){
            $pawningJewellery->delete();

            return redirect()->route('pawningJewellery.index')->with(
                'success', 'Pawning Jewellery successfully deleted.'
            );
        }
        else
        {
            return redirect()->back()->with('error','Permission denied.');
        }


    }

}
