<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InterestRate extends Model
{
    protected $fillable = [
        'service_type',
        'interest_rate',
        'status',
        'created_by',
    ];
}
