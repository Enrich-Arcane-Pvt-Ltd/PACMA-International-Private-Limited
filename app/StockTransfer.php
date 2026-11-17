<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    public function branch()
    {
        return $this->belongsTo('App\Branch', 'branch_id', 'id');
    }

    public function branchto()
    {
        return $this->belongsTo('App\Branch', 'transfer_to', 'id');
    }

    public function stockTransItems()
    {
        return $this->hasMany('App\StockTransferItem');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
}
