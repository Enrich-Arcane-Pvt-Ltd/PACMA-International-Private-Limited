<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PawningInstallment extends Model
{
    protected $fillable = [

    ];

    public function pawnings() {
        return $this->belongsTo('App\Pawning', 'pawning_id','id');
    }
    
    
//    iresha
    public function pawning() {
        return $this->belongsTo('App\Pawning', 'pawning_id','id');
    }

    // public function pawnedItem() {
    //     return $this->hasMany('App\PawnedItem', 'pawning_id','id');
    // }

}
