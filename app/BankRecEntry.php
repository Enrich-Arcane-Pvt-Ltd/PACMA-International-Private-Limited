<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankRecEntry extends Model
{
    use SoftDeletes;

    protected $table = 'bank_rec_entries';

    // public function chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'chart_of_account_id');
    // }

    // public function related_chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'related_chart_of_account_id');
    // }

    public function chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'chart_of_account_id');
    }

    public function related_chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'related_chart_of_account_id');
    }

    public function branch(){
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    public function type(){
        return $this->hasOne('App\AccountTransactionType', 'id', 'transaction_type_id');
    }

    public function accTransaction(){
        return $this->belongsTo('App\AccountTransaction', 'acccount_transaction_id','id');
    }
}
