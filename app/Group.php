<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function groupMembers(){
        return $this->hasMany('App\GroupMember')->where('status', 1);
    }

    public function center(){
        return $this->hasOne('App\Center', 'id', 'center_id');
    }
}
