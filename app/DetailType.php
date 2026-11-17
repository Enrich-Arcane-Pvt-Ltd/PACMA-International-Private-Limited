<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailType extends Model
{
    public function accountType()
    {
        return $this->belongsTo('App\AccountType');
    }
}
