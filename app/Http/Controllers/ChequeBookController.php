<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\ChequeBook;
use App\ChequeBookCheque;
use Illuminate\Http\Request;

class ChequeBookController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage cheque book'))
        {
            // $chequeBooks = ChequeBook::get();
            $chequeBanks = BankAccount::with(['chequeBooks'=>function($query){
                                                    $query->whereNotIn('status', [9]);
                                                }
                                            ])->where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            return view('chequeBooks.index', compact('chequeBanks'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create cheque book'))
        {
            // $chequeBooks = ChequeBook::get();
            $chequeBanks = BankAccount::where('status', 1)->where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            // $chequeBanks->prepend('- Select Bank -', '');
            return view('chequeBooks.create', compact('chequeBanks'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create cheque book'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'cheque_bank' => 'required',
                                   'start_from' => 'required',
                                   'cheques_amount' => 'required',
                                   'end_at' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $book = ChequeBook::where('status', 1)->where('cheque_bank_id', $request->cheque_bank)->first();
            if($book){
                return redirect()->back()->with('error', __('A book is already ongoing!'));
            }

            $chequeBook = new ChequeBook();
            $chequeBook->cheque_bank_id = $request->cheque_bank;
            $chequeBook->branch_id = \Auth::user()->branchId();
            $chequeBook->cheques_amount = $request->cheques_amount;
            $chequeBook->start_from = $request->start_from;
            $chequeBook->end_at = $request->end_at;
            $chequeBook->status = 1;
            $chequeBook->created_by = \Auth::user()->id;
            $chequeBook->save();

            $chequeNumber = $request->start_from;
            for($i = 0; $i < $request->cheques_amount; $i++){
                $cheques = new ChequeBookCheque();
                $cheques->cheque_book_id = $chequeBook->id;
                $cheques->cheque_bank_id = $request->cheque_bank;
                $cheques->branch_id = \Auth::user()->branchId();
                $cheques->cheque_number = $chequeNumber;
                $cheques->status = 1;
                $cheques->created_by = \Auth::user()->id;
                $cheques->save();

                $chequeNumber++;
            }

            return redirect()->back()->with('success', __('Cheque Book successfully created !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function view($id)
    {
        if(\Auth::user()->can('view cheque book'))
        {
            // $chequeBooks = ChequeBook::get();
            $cheques = ChequeBookCheque::whereNotIN('status', [9])->where('cheque_book_id', $id)->get();
            // $chequeBanks->prepend('- Select Bank -', '');
            return view('chequeBooks.view', compact('cheques'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
