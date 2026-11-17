<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_holder',
        'bank',
        'account_no',
        'opening_balance',
        'address',
        'phone',
        'status',
    ];

    public function chequeBooks()
    {
        return $this->hasMany('App\ChequeBook', 'cheque_bank_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch');
    }
}

