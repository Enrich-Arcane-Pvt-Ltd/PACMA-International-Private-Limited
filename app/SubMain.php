<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubMain extends Model
{
    protected $fillable = [
        'id',
        'sub_code',
        'name',
        'maccount_id',
        'maccount_name',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        ];
}
