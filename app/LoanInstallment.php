<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    public function loans() {
        return $this->belongsTo('App\Loan', 'loan_id','id');
    }

    public function loan() {
        return $this->hasOne('App\Loan', 'id','loan_id');
    }

    public function loanInstallmentAdvances() {
        return $this->hasMany('App\LoanInstallmentAdvance');
    }
}
