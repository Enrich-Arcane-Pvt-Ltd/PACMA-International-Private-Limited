<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $fillable = [
        'name',
        'branch_id',
        'status',
    ];


    public function branch(){
        return $this->belongsTo('App\Branch', 'branch_id', 'id');
    }

    public function branches(){
        return $this->hasOne('App\Branch', 'branch_id', 'id');
    }

    public function groups(){
        return $this->hasMany('App\Group', 'center_id', 'id')->where('status', 1)->orderBy('name');
    }
    public static function centerName($id)
    {
        return Center::where('id', $id)->pluck('name');
    }

}
