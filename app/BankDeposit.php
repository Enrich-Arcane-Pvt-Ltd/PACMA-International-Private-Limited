<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankDeposit extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'deposited_person_id');
    }
}
