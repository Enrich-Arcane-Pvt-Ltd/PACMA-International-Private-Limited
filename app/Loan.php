<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    public function customer(){
        return $this->hasOne('App\Customer','id','customer_id');
    }

    public function guarantor_1(){
        return $this->hasOne('App\Customer','id','guarantor_1');
    }

    public function guarantor_2(){
        return $this->hasOne('App\Customer','id','guarantor_2');
    }

    public function guarantor_3(){
        return $this->hasOne('App\Customer','id','guarantor_3');
    }

    public function guarantor_4(){
        return $this->hasOne('App\Customer','id','guarantor_4');
    }

    public function guarantor1(){
        return $this->hasOne('App\Customer','id','guarantor_1');
    }

    public function guarantor2(){
        return $this->hasOne('App\Customer','id','guarantor_2');
    }

    public function guarantor3(){
        return $this->hasOne('App\Customer','id','guarantor_3');
    }

    public function guarantor4(){
        return $this->hasOne('App\Customer','id','guarantor_4');
    }

    public function loanInstallments(){
        return $this->hasMany('App\LoanInstallment');
    }

    public function receipts(){
        return $this->hasMany('App\Receipt', 'loan_id', 'id');
    }

    public function center(){
        return $this->hasOne('App\Center','id','center_id');
    }

    public function group(){
        return $this->hasOne('App\Group','id','group_id');
    }

    public function businessCategory(){
        return $this->belongsTo('App\BusinessCategory');
    }

    public function officer(){
        return $this->belongsTo('App\User' , 'officer_id' , 'id');
    }

    public function branch(){
        return $this->belongsTo('App\Branch');
    }

    public function lastInstallment(){
        return $this->hasOne('App\LoanInstallment')->where('status', 1)->whereNotNull('paid_amount')->orderBy('id','DESC');
    }
}
