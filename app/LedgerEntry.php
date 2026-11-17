<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $fillable = [
        'id',
        'transaction_id',
        'account_id',
        'entry_type',
        'date',
        'amount',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        ];
}
