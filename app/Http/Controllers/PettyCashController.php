<?php

namespace App\Http\Controllers;

use App\GeneralLedgerAccount;
use App\PettyCash;
use App\PettyCashBook;
use App\PettyCashFund;
use App\User;
use App\Utility;
use Illuminate\Http\Request;

class PettyCashController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage petty cash'))
        {
            $pettyCashFund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();

            $pettyCashes = PettyCash::where('branch_id', '=', \Auth::user()->branchId())->where('status', 1)->orderBy('id', 'DESC')->get();
            return view('pettyCash.index', compact('pettyCashes','pettyCashFund'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create petty cash'))
        {
            $maxId = $this->pettyCashNumber();
            $pettyCashId = "PECA".str_pad(($maxId), 5, "0", STR_PAD_LEFT);
            $user = User::where('is_active',1)->get()->pluck('name', 'id');
            $chartofAccounts = GeneralLedgerAccount::orderBy('account_name')->get()->pluck('account_name', 'id');
            $pettyCashFund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();

            return view('pettyCash.create', compact('user','pettyCashId', 'chartofAccounts', 'pettyCashFund'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create petty cash'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'date' => 'required',
                                   'description' => 'required',
                                   'amount' => 'required|numeric',
                                   'approved_by' => 'required',
                                   'received_by' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $fund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
            $floatAmount = $fund ? (float)$fund->fund_amount : 0;
            $balanceAmount = $fund ? (float)$fund->balance_amount : 0;

            if ($floatAmount <= 0) {
                return redirect()->back()->with('error', __('Petty Cash Float is not configured. Please set the Petty Cash Float in Company Settings first.'));
            }

            if ($request->amount > $floatAmount) {
                return redirect()->back()->with('error', __('The Amount cannot be more than Petty Cash Float (' . number_format($floatAmount, 2) . ').'));
            }

            if ($request->amount > $balanceAmount) {
                return redirect()->back()->with('error', __('Insufficient Petty Cash Balance (' . number_format($balanceAmount, 2) . '). Please add funds via Payment Voucher first.'));
            }

            $pettyCash                   = new PettyCash();
            $pettyCash->petty_cash_id    = $this->pettyCashNumber();
            $pettyCash->chart_of_account_id  = $request->chart_of_accounts;
            $pettyCash->date             = $request->date;
            $pettyCash->description      = $request->description;
            $pettyCash->amount           = $request->amount;
            $pettyCash->approved_by      = $request->approved_by;
            $pettyCash->received_by      = $request->received_by;
            $pettyCash->status           = 1;
            $pettyCash->branch_id        = \Auth::user()->branchId();
            $pettyCash->created_by       = \Auth::user()->id;
            $pettyCash->save();

            // $pettyCachBook                      = new PettyCashBook();
            // $pettyCachBook->date                = $request->date;
            // $pettyCachBook->chart_of_account_id = $request->chart_of_accounts;
            // $pettyCachBook->petty_cash_id       = $pettyCash->id;
            // $pettyCachBook->amount_paid         = $request->amount;
            // $pettyCachBook->narration           = $request->description;
            // $pettyCachBook->approval_status     = 1;
            // $pettyCachBook->branch_id           = \Auth::user()->branchId();
            // $pettyCachBook->created_by          = \Auth::user()->id;
            // $pettyCachBook->save();

            $fund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
            if ($fund) {
                $fund->balance_amount = ($fund->balance_amount ?? 0) - $request->amount;
                $fund->save();
            }

            Utility::accountPettyCashTransaction($request->date, $request->chart_of_accounts, 207, $pettyCash->id, $request->amount, $request->received_by, 'debit');
            Utility::accountPettyCashTransaction($request->date, 207, $request->chart_of_accounts, $pettyCash->id, $request->amount, $request->received_by, 'credit');

            return redirect()->back()->with('success', __('Petty cash successfully done !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id){
        if(\Auth::user()->can('edit petty cash'))
        {
            $pettyCash                   = PettyCash::find($id);
            // $maxId = $this->pettyCashNumber();
            $pettyCashId = "PECA".str_pad(($id), 5, "0", STR_PAD_LEFT);
            $user = User::where('is_active',1)->get()->pluck('name', 'id');
            $chartofAccounts = GeneralLedgerAccount::orderBy('account_name')->get()->pluck('account_name', 'id');
            $pettyCashFund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();

            return view('pettyCash.edit', compact('user','pettyCashId', 'chartofAccounts', 'pettyCashFund', 'pettyCash'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function update(pettyCash $pettyCash, Request $request){

        if(\Auth::user()->can('edit petty cash'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'date' => 'required',
                                   'description' => 'required',
                                   'amount' => 'required|numeric',
                                   'approved_by' => 'required',
                                   'received_by' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $fund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
            $floatAmount = $fund ? (float)$fund->fund_amount : 0;
            $currentBalance = $fund ? (float)$fund->balance_amount : 0;
            $newBalance = $currentBalance + $pettyCash->amount - $request->amount;

            if ($request->amount > $floatAmount) {
                return redirect()->back()->with('error', __('The Amount cannot be more than Petty Cash Float (' . number_format($floatAmount, 2) . ').'));
            }

            if ($newBalance < 0) {
                return redirect()->back()->with('error', __('Insufficient Petty Cash Balance. The maximum allowed is ' . number_format($currentBalance + $pettyCash->amount, 2) . '.'));
            }

            if ($fund) {
                $fund->balance_amount = $newBalance;
                $fund->save();
            }

            $pettyCash->chart_of_account_id  = $request->chart_of_accounts;
            $pettyCash->date             = $request->date;
            $pettyCash->description      = $request->description;
            $pettyCash->amount           = $request->amount;
            $pettyCash->approved_by      = $request->approved_by;
            $pettyCash->received_by      = $request->received_by;
            $pettyCash->created_by       = \Auth::user()->id;
            $pettyCash->update();

            Utility::deleteAccountPettyCashTransaction($pettyCash->id);
            Utility::accountPettyCashTransaction($request->date, $request->chart_of_accounts, 207, $pettyCash->id, $request->amount, $request->received_by, 'debit');
            Utility::accountPettyCashTransaction($request->date, 207, $request->chart_of_accounts, $pettyCash->id, $request->amount, $request->received_by, 'credit');

            return redirect()->back()->with('success', __('Petty cash successfully Updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if(\Auth::user()->can('delete petty cash'))
        {
            $pettyCash = PettyCash::find($id);
            $pettyCash->status = 0;
            $pettyCash->update();

            $fund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
            if ($fund) {
                $fund->balance_amount = ($fund->balance_amount ?? 0) + $pettyCash->amount;
                $fund->save();
            }

            Utility::deleteAccountPettyCashTransaction($pettyCash->id);

            return redirect()->back()->with('success', __('Petty cash successfully Deleted !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function pettyCashNumber()
    {
        $latest = PettyCash::latest()->first();

        if(!$latest)
        {
            return 1;
        }
        return $latest->id + 1;
    }

    public function addFundCreate()
    {
        if(\Auth::user()->can('create petty cash fund'))
        {
            $pettyCashFund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();

            return view('pettyCash.add_fund', compact('pettyCashFund'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function addFundUpdate(Request $request)
    {
        if(\Auth::user()->can('create petty cash fund'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // $pettyCachBook                      = new PettyCashBook();
            // $pettyCachBook->date                = $request->date;
            // $pettyCachBook->chart_of_account_id = 0;
            // $pettyCachBook->amount_received     = $request->amount;
            // $pettyCachBook->approval_status     = 1;
            // $pettyCachBook->branch_id           = \Auth::user()->branchId();
            // $pettyCachBook->created_by          = \Auth::user()->id;
            // $pettyCachBook->save();

            $fund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
            if (!$fund) {
                $fund = new PettyCashFund();
                $fund->branch_id = \Auth::user()->branchId();
                $fund->fund_amount = 0;
                $fund->balance_amount = 0;
                $fund->status = 1;
            }
            $fund->balance_amount = ($fund->balance_amount ?? 0) + $request->amount;
            $fund->save();

            return redirect()->back()->with('success', __('Fund has been successfully added !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function pettyCashFundIndex()
    {
        if(\Auth::user()->can('manage petty cash fund'))
        {
            $pettyCashFund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();

            return view('pettyCash.petty_cash_fund', compact('pettyCashFund'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function pettyCashFundEdit(Request $request)
    {
        if(\Auth::user()->can('create petty cash fund'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'petty_cash_fund' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $fund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
            if (!$fund) {
                $fund = new PettyCashFund();
                $fund->branch_id = \Auth::user()->branchId();
                $fund->fund_amount = $request->petty_cash_fund;
                $fund->balance_amount = 0;
                $fund->status = 1;
            } else {
                $fund->fund_amount = $request->petty_cash_fund;
            }
            $fund->save();

            return redirect()->route('petty-cash.index')->with('success', __('Fund has been successfully added !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // public function destroy(bankTransfer $bankTransfer)
    // {
    //     if(\Auth::user()->can('delete transfer'))
    //     {
    //         // if($bankTransfer->created_by == \Auth::user()->creatorId())
    //         // {
    //             $bankTransfer->delete();

    //             Utility::bankAccountBalance($bankTransfer->from_account, $bankTransfer->amount, 'credit');
    //             Utility::bankAccountBalance($bankTransfer->to_account, $bankTransfer->amount, 'debit');
    //             return redirect()->route('bank-transfer.index')->with('success', __('Bank transfer successfully deleted !!!'));
    //         // }
    //         // else
    //         // {
    //         //     return redirect()->back()->with('error', __('Permission denied.'));
    //         // }
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }

    // $fund = $request->fund;
    //             while ($fund > 0) {
    //             $inventory = PettyCashFund::where('id', $request->amount_received)->where('branch_id', \Auth::user()->branchId())->where('status', 1)->first();

    //                     if($pettycash->amount_received > $fund){
    //                         $inventory->remain_qty = $inventory->remain_qty - $quantity;
    //                         $inventory->update();

    //                         $stockTransaction = new StockTransaction();
    //                         $stockTransaction->stock_master_id = 2;
    //                         $stockTransaction->inventory_id = $inventory->id;
    //                         $stockTransaction->product_id = $request->product;
    //                         $stockTransaction->quantity = $quantity;
    //                         $stockTransaction->date = $request->date_of_sale;
    //                         $stockTransaction->branch_id = \Auth::user()->branchId();
    //                         $stockTransaction->created_by     = \Auth::user()->id;
    //                         $stockTransaction->status     = 1;
    //                         $stockTransaction->save();

    //                         $quantity = 0;

    //                     } elseif ($inventory->remain_qty <= $quantity){

    //                         $stockTransaction = new StockTransaction();
    //                         $stockTransaction->stock_master_id = 2;
    //                         $stockTransaction->inventory_id = $inventory->id;
    //                         $stockTransaction->product_id = $request->product;
    //                         $stockTransaction->quantity = $inventory->remain_qty;
    //                         $stockTransaction->date = $request->date_of_sale;
    //                         $stockTransaction->branch_id = \Auth::user()->branchId();
    //                         $stockTransaction->created_by     = \Auth::user()->id;
    //                         $stockTransaction->status     = 1;
    //                         $stockTransaction->save();

    //                         $quantity = $quantity - $inventory->remain_qty;
    //                         $inventory->remain_qty = 0;
    //                         $inventory->status = 0;
    //                         $inventory->update();
    //                     }
    //             }


    // if($request->quantity > $request->available_quantity){
    //     return redirect()->back()->with('error', __('No Available Quantity!'));
    // }
}
