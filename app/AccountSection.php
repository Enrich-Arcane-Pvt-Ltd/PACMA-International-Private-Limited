<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountSection extends Model
{
    protected $fillable = [
        'id',
        'name',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        ];
}
