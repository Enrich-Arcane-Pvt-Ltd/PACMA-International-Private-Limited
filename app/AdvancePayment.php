<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    public function customer(){
        return $this->belongsTo('App\Customer');
    }
    public function receipts(){
        return $this->belongsTo('App\Receipt', 'receipt_id', 'id');
    }
    public function sales(){
        return $this->belongsTo('App\Sale', 'sale_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }
}
