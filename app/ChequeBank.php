<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChequeBank extends Model
{
    public function chequeBooks()
    {
        return $this->hasMany('App\ChequeBook', 'cheque_bank_id', 'id');
    }
}
