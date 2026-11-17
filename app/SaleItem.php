<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
    protected $with = [
        'warranty',
    ];
    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
    public function products()
    {
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Inventory', 'inventory_id', 'id');
    }
    public function warranty()
    {
        return $this->belongsTo(ProductWarrantySerial::class, 'product_warranty_id', 'id');
    }
}
