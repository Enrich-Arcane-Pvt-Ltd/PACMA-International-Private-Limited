<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountTransactionType extends Model
{
    protected $fillable = [
        'id',               
        'description',        
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

}
