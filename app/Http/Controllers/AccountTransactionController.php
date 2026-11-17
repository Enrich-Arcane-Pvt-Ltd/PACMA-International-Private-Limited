<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AccountTransaction;
use App\Setting;
use App\User;
use App\Utility;

class AccountTransactionController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }
    public function index()
    {
        if (\Auth::user()->can('manage account transaction')) {
            $accounttransactions = AccountTransaction::with('chart_of_account', 'branch', 'type', 'customer')
                ->where('status', 1)
                ->where('branch_id', \Auth::user()->branchId())
                ->orderBy('date', 'ASC')
                ->orderBy('id', 'ASC')
                ->paginate(100);
            $currencySymbol = $this->currencySymbol;
            return view('accountTransaction.index', compact('accounttransactions', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // public function search(Request $request)
    public function search($dates)
    {
        if (\Auth::user()->can('manage account transaction')) {
            $datArr = explode(',', $dates);
            $start_date = $datArr[0];
            $end_date = $datArr[1];
            $query = AccountTransaction::with('chart_of_account', 'branch', 'type', 'customer')
                ->where('status', 1)
                ->where('branch_id', \Auth::user()->branchId());
            if ($start_date != '') {
                $query->where('date', '>=', $start_date);
            }
            if ($end_date != '') {
                $query->where('date', '<=', $end_date);
            }
            $accounttransactions = $query->orderBy('date', 'ASC')->orderBy('id', 'ASC')->paginate(100);
            $currencySymbol = $this->currencySymbol;
            return view('accountTransaction.index', compact('accounttransactions', 'start_date', 'end_date', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
