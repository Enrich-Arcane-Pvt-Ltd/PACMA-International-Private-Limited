<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanInstallmentAdvance extends Model
{
    //
    public function receipts() {
        return $this->belongsTo('App\Receipt', 'receipt_id','id');
    }
}
