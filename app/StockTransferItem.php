<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    public function products(){
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }

    public function stock_transfer_parent(){
        return $this->belongsTo('App\StockTransfer', 'stock_transfer_id', 'id');
    }
}
