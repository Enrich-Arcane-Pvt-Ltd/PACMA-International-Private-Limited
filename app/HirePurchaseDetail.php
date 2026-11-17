<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HirePurchaseDetail extends Model
{
    public function warranty()
    {
        return $this->belongsTo(ProductWarrantySerial::class, 'product_warranty_id');
    }

    // public function warranty()
    // {
    //     return $this->belongsTo(ProductWarranty::class,'product_warranty_id');
    // }


}
