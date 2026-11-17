<?php

namespace App\Http\Controllers;

use App\AccountType;
use App\DetailType;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage chart of account'))
        {
            $detailTypes = DetailType::with('accountType')->where('status', 1)->get();
            return view('account_type.index', ['detailTypes' => $detailTypes]);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }
}
