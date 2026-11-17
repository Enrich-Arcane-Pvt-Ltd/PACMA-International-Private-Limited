<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FineCenterCommand extends Model
{
    public function center(){
        return $this->belongsTo('App\Center','center_id','id');
    }
}
