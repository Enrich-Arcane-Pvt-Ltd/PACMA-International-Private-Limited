<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public function products()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    public function grns()
    {
        return $this->hasOne('App\Grn', 'id', 'grn_id');
    }

    public function sales()
    {
        return $this->hasMany('App\Sale', 'inventory_id', 'id')->whereIn('status', [1,2,3]);
    }

    public function saleItems()
    {
        return $this->hasMany('App\SaleItem', 'inventory_id', 'id')->where('status', 1);
    }

    public function stockTransferItems()
    {
        return $this->hasMany('App\StockTransferItem', 'inventory_id', 'id');
    }

    public function transferFrom()
    {
        return $this->hasOne('App\Branch', 'id', 'tranfer_from');
    }
}
