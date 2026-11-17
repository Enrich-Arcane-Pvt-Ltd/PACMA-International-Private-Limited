<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrnProduct extends Model
{
    use SoftDeletes;
    protected $table = 'grn_products';

    protected $with = [
        'productWarrantySerial',
    ];


    public function products()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function productWarrantySerial()
    {
        return $this->hasMany('App\ProductWarrantySerial', 'grn_product_id', 'id');
    }
}
