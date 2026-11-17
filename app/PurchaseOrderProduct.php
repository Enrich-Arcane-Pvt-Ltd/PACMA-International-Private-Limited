<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderProduct extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_order_products';

    public function products()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }
}
