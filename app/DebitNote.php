<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebitNote extends Model
{
    protected $fillable = [
        'bill',
        'vendor',
        'amount',
	'quantity',
        'date',
    ];

    public function debitVendor()
    {
        return $this->belongsTo('App\Vender', 'vendor', 'id');
    }

    public function debitCustomer()
    {
        return $this->belongsTo('App\Customer', 'customer', 'id');
    }
}
