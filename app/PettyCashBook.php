<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PettyCashBook extends Model
{
    protected $fillable = [
        'id',
        'date',
        'chart_of_account_id',
        'amount_received',
        'amount_paid',
        'narration',
        'approval_status',
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
