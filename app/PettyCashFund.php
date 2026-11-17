<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PettyCashFund extends Model
{
    protected $fillable = [
        'id',
        'date',
        'payment_id',
        'amount_received',
        'fund_amount',
        'balance_amount',
        'branch_id',
        'status',        
    ];

    public function payment()
    {
        return $this->hasMany('App\PettyCash', 'id', 'payment_id');
    }
    
}