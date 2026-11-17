<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialEntry extends Model
{
    use SoftDeletes;

    // Relations
    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id')->withDefault();
    }

    public function customReceipt()
    {
        return $this->belongsTo(CustomReceipt::class, 'custom_receipt_id')->withDefault();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function vendor()
    {
        return $this->belongsTo(Vender::class, 'vendor_id')->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id')->withDefault();
    }
}
