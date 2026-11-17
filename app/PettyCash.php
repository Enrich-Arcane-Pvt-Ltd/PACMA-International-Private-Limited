<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    protected $fillable = [
        'date',
        'description',
        'amount',
        'approved_by',
        'received_by',
        'status',
    ];


    public function approvedUser()
    {
        return $this->belongsTo('App\User', 'approved_by', 'id');
    }


    public function receivedUser()
    {
        return $this->belongsTo('App\User', 'received_by', 'id');
    }

    // public function chartOfAccount()
    // {
    //     return $this->belongsTo('App\ChartOfAccount');
    // }
    public function chartOfAccount()
    {
        return $this->belongsTo('App\GeneralLedgerAccount');
    }

}

