<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControlAccountController extends Controller
{
    //Cash Deposit Control
    public function cashDepositIndex(){
        $showTable = 'No';
        return view('controlAccount.cash_deposit', compact('showTable'));
    }

    public function cashDepositSearch(Request $request){

        return view('controlAccount.cash_deposit');
    }

    //EPF/ETF Control
    public function epfEtfIndex(){
        $showTable = 'No';

        return view('controlAccount.epf_etf', compact('showTable'));
    }

    public function epfEtfSearch(Request $request){
        
        return view('controlAccount.epf_etf');
    }

    //Loan Stock Contol
    public function loanStockIndex(){
        $showTable = 'No';

        return view('controlAccount.loan_stock', compact('showTable'));
    }

    public function loanStockSearch(Request $request){
        
        return view('controlAccount.loan_stock');
    }

    //Salary Control
    public function salaryIndex(){
        $showTable = 'No';

        return view('controlAccount.salary', compact('showTable'));
    }

    public function salarySearch(Request $request){
        
        return view('controlAccount.salary');
    }

    //Stock Return Control
    public function stockReturnIndex(){
        $showTable = 'No';

        return view('controlAccount.stock_return', compact('showTable'));
    }

    public function stockReturnSearch(Request $request){
        
        return view('controlAccount.stock_return');
    }

}
