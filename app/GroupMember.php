<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    public function customers(){
        return $this->hasOne('App\Customer', 'id', 'customer_id');
    }

    public function loans(){
        return $this->hasMany('App\Loan', 'customer_id', 'customer_id');
    }
}
