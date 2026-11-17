<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrialBalance extends Model
{   protected $fillable = [
    'id',
    'chart_of_account_id',
    'debit',
    'credit',
    'created_by',
    'created_at',
    'updated_by',
    'updated_at',
];
    // public function chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'chart_of_account_id');
    // }
    public function chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'chart_of_account_id');
    }
}
