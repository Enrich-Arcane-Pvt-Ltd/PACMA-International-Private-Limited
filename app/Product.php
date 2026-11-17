<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'barcode',
        'sale_price',
        'purchase_price',
        'description',
        'image_1',
        'image_2',
        'image_3',
        'warranty_period',
        'warranty_document',
        'additional_warranty_details',
        'status',
        'product_category_id',
        'product_sub_category_id',
    ];


    public function taxes()
    {
        return $this->hasOne('App\Tax', 'id', 'tax_id')->first();
    }

    public function unit()
    {
        return $this->hasOne('App\ProductServiceUnit', 'id', 'unit_id')->first();
    }

    public function category()
    {
        return $this->hasOne('App\ProductServiceCategory', 'id', 'category_id');
    }

    public function tax($taxes)
    {
        $taxArr = explode(',', $taxes);

        $taxes  = [];
        foreach ($taxArr as $tax) {
            $taxes[] = Tax::find($tax);
        }

        return $taxes;
    }

    public function taxRate($taxes)
    {
        $taxArr  = explode(',', $taxes);
        $taxRate = 0;
        foreach ($taxArr as $tax) {
            $tax     = Tax::find($tax);
            $taxRate += $tax->rate;
        }

        return $taxRate;
    }




    public function categoryDetails()
    {
        return $this->hasOne('App\ProductCategory', 'id', 'product_category_id');
    }

    public function subCategoryDetails()
    {
        return $this->hasOne('App\ProductSubCategory', 'id', 'product_sub_category_id');
    }

    public function warehouseDetails()
    {
        return $this->hasOne('App\Warehouse', 'id', 'ware_house_id');
    }

    //dilan
    public function product_category()
    {
        return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
    }

    public function inventory()
    {
        return $this->hasMany('App\Inventory', 'product_id', 'id');
    }

    public function brands()
    {
        return $this->hasOne('App\ProductBrand', 'id', 'product_brand_id');
    }



    // iresha
    public function productBrand()
    {
        return $this->belongsTo('App\ProductBrand', 'product_brand_id', 'id');
    }

    public function productCategory()
    {
        return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
    }

    public function productSubCategory()
    {
        return $this->belongsTo('App\ProductSubCategory', 'product_sub_category_id', 'id');
    }

    public function warrantySerials()
    {
        return $this->hasMany(ProductWarrantySerial::class, 'product_id');
    }

    public function advancePayments()
    {
        return $this->hasMany('App\AdvancePayment', 'product_id', 'id');
    }
}
