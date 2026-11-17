<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    protected $fillable = [
    'id',
    'name',
    'type',
    'section_id',
    'section_name',
    'created_by',
    'created_at',
    'updated_by',
    'updated_at',
    ];
   
}
