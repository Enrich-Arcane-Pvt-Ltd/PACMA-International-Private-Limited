<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
    'name', 'down_payment', 'status',
    ];


    protected $hidden = [

    ];

    public static function categoryName($id){
        return ProductCategory::where('id', $id)->pluck('name');
    }
}
