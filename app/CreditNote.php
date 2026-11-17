<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'invoice',
        'customer',
        'amount',
	'quantity',
        'date',
    ];

    public function creditCustomer()
    {
        return $this->belongsTo('App\Customer', 'customer', 'id');
    }
}
