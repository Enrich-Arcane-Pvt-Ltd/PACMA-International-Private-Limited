<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomReceipt extends Model
{
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function vender()
    {
        return $this->belongsTo('App\Vender');
    }
}
