<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    public function products()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function inventory()
    {
        return $this->hasOne('App\Inventory', 'id', 'inventory_id');
    }
}
