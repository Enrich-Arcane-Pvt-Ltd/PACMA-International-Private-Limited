<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'branch_manager',
        'status',
    ];

    public static function branchName($id){
        return Branch::where('id', $id)->pluck('name');
    }

}
