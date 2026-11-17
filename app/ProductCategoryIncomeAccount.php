<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryIncomeAccount extends Model
{
    protected $fillable = [
        'chart_of_account_id',
        'product_category_id',
    ];

    public $timestamps = false;
}
