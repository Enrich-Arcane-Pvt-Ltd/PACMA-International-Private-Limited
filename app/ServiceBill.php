<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceBill extends Model
{
    protected $fillable = [
        'id', 'bill_id', 'bill_date', 'service_id', 'bill_amount', 'paid_amount', 'created_by', 'created_at', 'updated_at'
    ];

    public function services()
    {
        return $this->hasOne('App\Service', 'id', 'service_id');
    }
}
