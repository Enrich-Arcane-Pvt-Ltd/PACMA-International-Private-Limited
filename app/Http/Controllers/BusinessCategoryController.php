<?php

namespace App\Http\Controllers;

use App\BusinessCategory;
use Illuminate\Http\Request;

class BusinessCategoryController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage business category'))
        {
            $categories = BusinessCategory::orderBy('category_name')->get();
            return view('business_categories.index', compact('categories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create business category'))
        {
            return view('business_categories.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create business category'))
        {
            $rules = [
                'category_name' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('business_category.index')->with('error', $messages->first());
            }
                $category = new BusinessCategory();
                $category->category_name     = $request->category_name;
                $category->status = 1;
                $category->created_by  = \Auth::user()->id;
                $category->save();

                return redirect()->back()->with('success', __('Business Category successfully created !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit business category'))
        {
            $categories = BusinessCategory::find($id);
            return view('business_categories.edit', compact('categories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request)
    {
        if(\Auth::user()->can('edit business category'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'category_name' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $categories = BusinessCategory::find($request->id);
            $categories->category_name       = $request->category_name;
            $categories->update();

            return redirect()->back()->with('success', __('Business Category successfully updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function block($id){
        if(\Auth::user()->can('block business category'))
        {
            $categories = BusinessCategory::find($id);
            $categories->status = 0;
            $categories->update();
            return redirect()->back()->with('success', __('Business Category successfully blocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function unblock($id){
        if(\Auth::user()->can('block business category'))
        {
            $categories = BusinessCategory::find($id);
            $categories->status = 1;
            $categories->update();
            return redirect()->back()->with('success', __('Business Category successfully unblocked !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
