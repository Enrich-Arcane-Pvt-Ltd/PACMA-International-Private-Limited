<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'id',        
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        ];
}
