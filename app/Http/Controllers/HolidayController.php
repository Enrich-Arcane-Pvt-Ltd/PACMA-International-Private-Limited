<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Holiday;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::all();
        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required',
                'date' => 'required',
            ]);

            $holiday = new Holiday();
            $holiday->name = $request->name;
            $holiday->date = $request->date;
            $holiday->save();

            return redirect()->route('holidays', ['month'=>$request->month ?? ''])
                ->with('success', 'Holiday created successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('holidays', ['month'=>$request->month ?? ''])
                ->with('error', 'Failed to create holiday.');
        }

    }

    public function edit($id, Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'date' => 'required',
            ]);

            $holiday = Holiday::find($id);
            $holiday->name = $request->name;
            $holiday->date = $request->date;
            $holiday->save();

            return redirect()->route('holidays', ['month'=>$request->month ?? ''])
                ->with('success', 'Holiday updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('holidays', ['month'=>$request->month ?? ''])
                ->with('error', 'Failed to update holiday.');
        }

    }

    public function destroy($id)
    {
        try {
            $holiday = Holiday::find($id);
            $holiday->delete();

            return redirect()->back()
                ->with('success', 'Holiday deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('error', 'Failed to delete holiday.');
        }
    }
}


