<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaittingSale extends Model
{
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function warranty()
    {
        return $this->belongsTo(ProductWarrantySerial::class, 'product_warranty_serial_id');
    }
}
