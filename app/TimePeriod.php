<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimePeriod extends Model
{
    protected $fillable = [
        'time_period', 
        'status', 
    ];
}
