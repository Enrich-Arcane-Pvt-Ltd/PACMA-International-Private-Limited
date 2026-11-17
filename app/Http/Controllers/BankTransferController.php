<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\BankTransfer;
use App\Setting;
use App\Utility;
use Illuminate\Http\Request;

class BankTransferController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage transfer')) {
            // $bankAccount = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('bank_holder', 'id');
            // $bankAccount = BankAccount::get()->pluck('bank_holder', 'id');
            $bankAccount = BankAccount::select('*', \DB::raw("CONCAT(bank,' - ',bank_holder) AS name"))->get()->pluck('name', 'id');
            $bankAccount->prepend('All', '');

            // $query = BankTransfer::where('created_by', '=', \Auth::user()->creatorId());
            $query = BankTransfer::whereIn('status', [1, 0]);

            if (!empty($request->date)) {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if (!empty($request->from_account)) {
                $query->where('from_account', '=', $request->from_account);
            }

            if (!empty($request->to_account)) {
                $query->where('to_account', '=', $request->to_account);
            }

            $bankTransfers = $query->orderBy('id', 'DESC')->get();
            $currencySymbol = $this->currencySymbol;
            return view('bankTransfer.index', compact('bankAccount', 'bankTransfers', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create transfer')) {
            $bankAccount = BankAccount::select('*', \DB::raw("CONCAT(bank,' - ',bank_holder) AS name"))->get()->pluck('name', 'id');
            return view('bankTransfer.create', compact('bankAccount'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create transfer')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'date' => 'required',
                    'from_account' => 'required|numeric',
                    'to_account' => 'required|numeric',
                    'amount' => 'required|numeric',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $bankTransfer                   = new BankTransfer();
            $bankTransfer->bank_transfer_id = $this->bankTransferNumber();
            $bankTransfer->date             = $request->date;
            $bankTransfer->from_account     = $request->from_account;
            $bankTransfer->to_account       = $request->to_account;
            $bankTransfer->amount           = $request->amount;
            $bankTransfer->reference        = $request->reference;
            $bankTransfer->description      = $request->description;
            $bankTransfer->status           = 1;
            $bankTransfer->created_by       = \Auth::user()->authId();
            $bankTransfer->save();

            // Utility::bankAccountBalance($request->from_account, $request->amount, 'debit');
            // Utility::bankAccountBalance($request->to_account, $request->amount, 'credit');

            $backAccount = BankAccount::find($request->from_account);
            $fromChartOfAccount = $backAccount->chart_of_account_id;
            // if($request->from_account == 1){
            //     $fromChartOfAccount = 228;
            // } elseif ($request->from_account == 2){
            //     $fromChartOfAccount = 227;
            // } elseif ($request->from_account == 3){
            //     $fromChartOfAccount = 229;
            // }

            $backAccount = BankAccount::find($request->to_account);
            $toChartOfAccount =  $backAccount->chart_of_account_id;
            // if($request->to_account == 1){
            //     $toChartOfAccount = 228;
            // } elseif ($request->to_account == 2){
            //     $toChartOfAccount = 227;
            // } elseif ($request->to_account == 3){
            //     $toChartOfAccount = 229;
            // }

            Utility::accountBankTransferTransaction($request->date, $toChartOfAccount, $fromChartOfAccount, $request->amount, $bankTransfer->id, 'debit');
            Utility::accountBankTransferTransaction($request->date, $fromChartOfAccount, $toChartOfAccount, $request->amount, $bankTransfer->id, 'credit');

            return redirect()->back()->with('success', __('Bank transfer successfully done !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function bankTransferNumber()
    {
        // $latest = BankTransfer::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        $latest = BankTransfer::latest()->first();

        if (!$latest) {
            return 1;
        }
        return $latest->id + 1;
    }


    public function edit(bankTransfer $bankTransfer)
    {
        if (\Auth::user()->can('edit transfer')) {
            $bankAccount = BankAccount::select('*', \DB::raw("CONCAT(bank,' - ',bank_holder) AS name"))->get()->pluck('name', 'id');
            return view('bankTransfer.edit', compact('bankTransfer', 'bankAccount'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, bankTransfer $bankTransfer)
    {
        if (\Auth::user()->can('edit transfer')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'date' => 'required',
                    'from_account' => 'required|numeric',
                    'to_account' => 'required|numeric',
                    'amount' => 'required|numeric',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // Utility::bankAccountBalance($bankTransfer->from_account, $bankTransfer->amount, 'credit');
            // Utility::bankAccountBalance($bankTransfer->to_account, $bankTransfer->amount, 'debit');

            $bankTransfer->date           = $request->date;
            $bankTransfer->from_account   = $request->from_account;
            $bankTransfer->to_account     = $request->to_account;
            $bankTransfer->amount         = $request->amount;
            $bankTransfer->reference      = $request->reference;
            $bankTransfer->description    = $request->description;
            $bankTransfer->status         = 1;
            $bankTransfer->save();

            $backAccount = BankAccount::find($request->from_account);
            $fromChartOfAccount = $backAccount->chart_of_account_id;
            // if($request->from_account == 1){
            //     $fromChartOfAccount = 228;
            // } elseif ($request->from_account == 2){
            //     $fromChartOfAccount = 227;
            // } elseif ($request->from_account == 3){
            //     $fromChartOfAccount = 229;
            // }

            $backAccount = BankAccount::find($request->to_account);
            $toChartOfAccount =  $backAccount->chart_of_account_id;
            // if($request->to_account == 1){
            //     $toChartOfAccount = 228;
            // } elseif ($request->to_account == 2){
            //     $toChartOfAccount = 227;
            // } elseif ($request->to_account == 3){
            //     $toChartOfAccount = 229;
            // }

            Utility::deleteBankTransferAccountTransation($bankTransfer->id);

            Utility::accountBankTransferTransaction($request->date, $toChartOfAccount, $fromChartOfAccount, $request->amount, $bankTransfer->id, 'debit');
            Utility::accountBankTransferTransaction($request->date, $fromChartOfAccount, $toChartOfAccount, $request->amount, $bankTransfer->id, 'credit');

            // Utility::bankAccountBalance($request->from_account, $request->amount, 'debit');
            // Utility::bankAccountBalance($request->to_account, $request->amount, 'credit');

            return redirect()->route('bank-transfer.index')->with('success', __('Bank transfer successfully updated !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(bankTransfer $bankTransfer)
    {
        if (\Auth::user()->can('delete transfer')) {
            // if($bankTransfer->created_by == \Auth::user()->creatorId())
            // {
            $bankTransfer->delete();

            Utility::bankAccountBalance($bankTransfer->from_account, $bankTransfer->amount, 'credit');
            Utility::bankAccountBalance($bankTransfer->to_account, $bankTransfer->amount, 'debit');
            return redirect()->route('bank-transfer.index')->with('success', __('Bank transfer successfully deleted !!!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getOtherBank($id)
    {
        $bankAccount = BankAccount::select('*', \DB::raw("CONCAT(bank,' - ',bank_holder) AS name"))->whereNotIn('id', [$id])->get();
        return json_encode($bankAccount);
    }
}
