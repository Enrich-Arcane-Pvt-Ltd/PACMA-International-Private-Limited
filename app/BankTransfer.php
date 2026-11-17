<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    protected $fillable = [
        'date',
        'from_account',
        'to_account',
        'amount',
        'reference',
        'description',
        'status',
        'created_by',
    ];

    
    public function fromBankAccount()
    {
        return $this->hasOne('App\BankAccount', 'id', 'from_account')->first();
    }

    
    public function toBankAccount()
    {
        return $this->hasOne('App\BankAccount', 'id', 'to_account')->first();
    }

}
