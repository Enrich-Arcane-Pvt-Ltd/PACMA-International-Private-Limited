<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    protected $fillable = [
        'name',
        'status',
        'product_category_id',
        'product_sub_category_id',
        'created_by',
    ];


    public function productCategory()
    {
        return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
    }

}
