<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    public function hirepurchaseDetails() {
        return $this->belongsTo('App\Sale', 'sale_id','id');
    }

    public function sales() {
        return $this->belongsTo('App\Sale', 'sale_id','id');
    }

    public function installmentAdvances() {
        return $this->hasMany('App\InstallmentAdvance')->where('status', 1);
    }
}
