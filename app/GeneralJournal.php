<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralJournal extends Model
{
    protected $fillable = [
        'id',
        'date',
        'account_id',
        'amount',
        'status',
        'narration',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        ];

    // public function chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'account_id');
    // }

    // public function retlated_chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'related_account_id');
    // }
    public function chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'account_id');
    }

    public function retlated_chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'related_account_id');
    }

}


