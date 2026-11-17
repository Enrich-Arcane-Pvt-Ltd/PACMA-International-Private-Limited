<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FineRate extends Model
{
    protected $fillable = [
      'service-type','fine_rate','created_by'
    ];
}
