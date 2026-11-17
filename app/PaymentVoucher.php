<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    public function service(){
        return $this->belongsTo('App\Service');
    }

    // public function account(){
    //     return $this->belongsTo('App\ChartOfAccount', 'chart_of_account_id', 'id');
    // }
    public function account(){
        return $this->belongsTo('App\GeneralLedgerAccount', 'chart_of_account_id', 'id');
    }

    public function supplier(){
        return $this->hasOne('App\Vender', 'id', 'vender_id');
    }

    public function cheque(){
        return $this->hasOne('App\Cheque', 'id', 'cheque_id');
    }

    public function chequeBookCheque(){
        return $this->hasOne('App\ChequeBookCheque', 'payment_voucher_id', 'id');
    }

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public function bank()
    {
        return $this->belongsTo('App\BankAccount', 'bank_id', 'id');
    }

    public function debitNotes()
    {
        return $this->hasMany('App\DebitNote', 'bill', 'id')->where('status', 1);
    }

    public function billTotaldebitNote()
    {
        return $this->hasMany('App\DebitNote', 'bill', 'id')->where('status', 1)->sum('amount');
    }

    public function loans(){
        return $this->belongsTo('App\Loan', 'loan_id', 'id');
    }

    public function pawnings(){
        return $this->belongsTo('App\Pawning', 'pawning_id', 'id');
    }

    public function advances(){
        return $this->belongsTo('App\AdvancePayment', 'advance_id', 'id');
    }
}
