<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldRate extends Model
{
    protected $fillable = [
        'gold_rate',
        'gold_rate_type',
        'status',
    ];
}
