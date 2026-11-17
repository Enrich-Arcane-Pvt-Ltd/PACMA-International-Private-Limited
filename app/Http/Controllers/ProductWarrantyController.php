<?php

namespace App\Http\Controllers;

use App\ProductWarranty;
use Illuminate\Http\Request;

class ProductWarrantyController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage product'))
        {
            $productWarranties = ProductWarranty::orderBy('warranty_card_no')->where('branch_id', \Auth::user()->branchId())->get();
            return view('productWarranty.index', compact('productWarranties'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create product'))
        {
            return view('productWarranty.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create product'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'warranty_card_no' => 'required|max:255',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $productWarranty = new ProductWarranty();
            $productWarranty->warranty_card_no = $request->warranty_card_no;
            $productWarranty->branch_id = \Auth::user()->branchId();
            $productWarranty->created_by = \Auth::user()->id;
            $productWarranty->save();

            return redirect()->route('product-warranty.index')->with('success', __('Warranty card successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit product'))
        {
            $productWarranty = ProductWarranty::find($id);
            return view('productWarranty.edit', compact('productWarranty'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit product'))
        {
            $productWarranty = ProductWarranty::find($id);

            $validator = \Validator::make(
                $request->all(), [
                    'warranty_card_no' => 'required|max:255',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $productWarranty->warranty_card_no = $request->warranty_card_no;
            $productWarranty->save();

            return redirect()->route('product-warranty.index')->with('success', __('Warranty card successfully updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function block($id)
    {
        if(\Auth::user()->can('block product'))
        {
            $productWarranty = ProductWarranty::find($id);
            $productWarranty->status = 0;
            $productWarranty->update();
            return redirect()->back()->with('success', __('Warranty card successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block product'))
        {
            $productWarranty = ProductWarranty::find($id);
            $productWarranty->status = 1;
            $productWarranty->update();
            return redirect()->back()->with('success', __('Warranty card successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
