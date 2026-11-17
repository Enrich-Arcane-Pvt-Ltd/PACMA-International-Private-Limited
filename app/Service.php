<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_id', 
        'name', 
        'acct_no', 
        'service_provider', 
    ];
}
