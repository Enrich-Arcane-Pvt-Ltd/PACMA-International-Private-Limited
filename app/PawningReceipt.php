<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PawningReceipt extends Model
{
    protected $fillable = [

    ];

    public function invoice(){
        return $this->hasOne('App\Pawning', 'id', 'pawning_id');
    }

    public function customer(){
        return $this->hasOne('App\Customer', 'id', 'customer_id');
    }

    public function pawnings()
    {
        return $this->hasOne('App\Pawning', 'id', 'pawning_id');
    }

    public function installments()
    {
        return $this->hasOne('App\PawningInstallment', 'receipt_id', 'id');
    }
}
