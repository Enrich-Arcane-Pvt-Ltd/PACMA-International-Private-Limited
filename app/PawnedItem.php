<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PawnedItem extends Model
{
    protected $fillable = [

    ];

    public function jewellery() {
        return $this->belongsTo('App\PawningJewellery', 'jewellery_id', 'id');
    }

    public function goldType() {
        return $this->belongsTo('App\GoldRate', 'gold_rate_type', 'id');
    }

}
