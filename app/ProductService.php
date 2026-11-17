<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductService extends Model
{
    protected $fillable = [
        'name',
        // 'product_category_id',
        // 'product_sub_category_id',
        'barcode',
        'sale_price',
        'purchase_price',
        'quantity',
        'ware_house_name',
        
        'category_id',
        'unit_id',
        'type',
        'warranty_period',
        'warranty_document',
        'additional_details',
        'status',
        'image_1',
        'created_by',
    ];



    public static $warehouse = [
        'Warehouse 1',
        'Warehouse 2',
    ];

    // public function taxes()
    // {
    //     return $this->hasOne('App\Tax', 'id', 'tax_id')->first();
    // }

    public function unit()
    {
        return $this->hasOne('App\ProductServiceUnit', 'id', 'unit_id')->first();
    }

    public function category()
    {
        return $this->hasOne('App\ProductServiceCategory', 'id', 'category_id');
    }

    // public function tax($taxes)
    // {
    //     $taxArr = explode(',', $taxes);

    //     $taxes  = [];
    //     foreach($taxArr as $tax)
    //     {
    //         $taxes[] = Tax::find($tax);
    //     }

    //     return $taxes;
    // }

    // public function taxRate($taxes)
    // {
    //     $taxArr  = explode(',', $taxes);
    //     $taxRate = 0;
    //     foreach($taxArr as $tax)
    //     {
    //         $tax     = Tax::find($tax);
    //         $taxRate += $tax->rate;
    //     }

    //     return $taxRate;
   // }


    public function categoryDetails() {
        return $this->hasOne('App\ProductCategory', 'id', 'product_category_id');
    }

    public function subCategoryDetails() {
        return $this->hasOne('App\ProductSubCategory', 'id', 'product_sub_category_id');
    }

    public function warehouseDetails() {
        return $this->hasOne('App\Warehouse', 'id', 'ware_house_id');
    }
    
    public function product_category(){
     return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
    }
}