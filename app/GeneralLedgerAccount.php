<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralLedgerAccount extends Model
{
    public function accountType()
    {
        return $this->belongsTo('App\AccountType');
    }

    public function detailType()
    {
        return $this->belongsTo('App\DetailType');
    }

    public function bankRecs(){
        return $this->hasMany('App\BankReconciliation', 'chart_of_account_id', 'id')->where('status', 1);
    }

    public function getNameAttribute()
    {
        return $this->account_name ?? null;
    }
}

