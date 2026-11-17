<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductWarrantySerial extends Model
{
    protected $fillable = [
        'grn_product_id',
        'serial_number',
        'warranty_number',
        'warranty_period',
        'warranty_document',
        'additional_warranty_description',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo('App\GrnProduct', 'grn_product_id', 'id');
    }
}
