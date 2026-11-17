<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'purchase_date',
        'supported_date',
        'description',
        'status'
    ];

}
