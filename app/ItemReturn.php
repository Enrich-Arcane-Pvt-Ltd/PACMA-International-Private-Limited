<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemReturn extends Model
{
    public function invoice(){
        return $this->belongsTo('App\Invoice');
    }

    public function vender(){
        return $this->belongsTo('App\Vender', 'vender_id');
    }

    public function grn(){
        return $this->belongsTo('App\Grn', 'grn_id');
    }

    public function product(){
        return $this->belongsTo('App\Product');
    }

    // public function chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'account_id');
    // }

    // public function retlated_chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'related_account_id');
    // }
    public function chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'account_id');
    }

    public function retlated_chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'related_account_id');
    }
}
