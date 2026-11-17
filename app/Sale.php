<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'name',
        'type',
        'from',
        'to',
        'amount',
        'is_display',
        'created_by',
    ];

    public static $saleType = [
        'Hire Sales',
    ];

    public static $goalType = [
        'Invoice',
        'Bill',
        'Revenue',
        'Payment',
    ];

    public function target($type, $from, $to, $amount)
    {
        $total    = 0;
        $fromDate = $from . '-01';
        $toDate   = $to . '-01';
        if (\App\Goal::$goalType[$type] == 'Invoice') {
            $invoices = Invoice::select('*')->where('created_by', \Auth::user()->creatorId())->where('issue_date', '>=', $fromDate)->where('issue_date', '<=', $toDate)->get();
            $total    = 0;
            foreach ($invoices as $invoice) {
                $total += $invoice->getTotal();
            }
        } elseif (\App\Goal::$goalType[$type] == 'Bill') {
            $bills = Bill::select('*')->where('created_by', \Auth::user()->creatorId())->where('bill_date', '>=', $fromDate)->where('bill_date', '<=', $toDate)->get();
            $total = 0;
            foreach ($bills as $bill) {
                $total += $bill->getTotal();
            }
        } elseif (\App\Goal::$goalType[$type] == 'Revenue') {
            $revenues = Revenue::select('*')->where('created_by', \Auth::user()->creatorId())->where('date', '>=', $fromDate)->where('date', '<=', $toDate)->get();
            $total    = 0;

            foreach ($revenues as $revenue) {
                $total += $revenue->amount;
            }
        } elseif (\App\Goal::$goalType[$type] == 'Payment') {
            $payments = Payment::select('*')->where('created_by', \Auth::user()->creatorId())->where('date', '>=', $fromDate)->where('date', '<=', $toDate)->get();
            $total    = 0;

            foreach ($payments as $payment) {
                $total += $payment->amount;
            }
        }

        $data['percentage'] = ($total * 100) / $amount;
        $data['total']      = $total;

        return $data;
    }



    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
    public function products()
    {
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }
    public function installments()
    {
        return $this->hasMany('App\Installment');
    }
    public function receipts()
    {
        return $this->hasMany('App\Receipt', 'invoice_id', 'invoice_id');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Inventory', 'inventory_id', 'id');
    }

    public function guarantor1()
    {
        return $this->hasOne('App\Customer', 'id', 'guarantor_id');
    }

    public function guarantor2()
    {
        return $this->hasOne('App\Customer', 'id', 'guarantor_2_id');
    }

    public function guarantor3()
    {
        return $this->hasOne('App\Customer', 'id', 'guarantor_3_id');
    }

    public function customerInfo()
    {
        return $this->hasOne('App\Customer', 'id', 'customer_id');
    }

    public function productInfo()
    {
        return $this->hasOne('App\ProductService', 'id', 'product_id');
    }

    public function saleItems()
    {
        return $this->hasMany('App\SaleItem', 'sale_id', 'id')->where('status', 1);
    }

    public function officer()
    {
        return $this->belongsTo('App\User');
    }

    public function introducer()
    {
        return $this->belongsTo('App\User');
    }

    public function lastInstallment()
    {
        return $this->hasOne('App\Installment')->where('status', 1)->whereNotNull('paid_amount')->orderBy('id', 'DESC');
    }

    public function firstInstallment()
    {
        return $this->hasOne('App\Installment');
    }

    public static function hirePurchaseIndex($customer, $date)
    {
        return Sale::where('sale_type', 1)->whereIn('status', [1, 2, 3])->where('customer_id', $customer)->where('date_of_sale', '<=', $date)->count();
    }

    public function createdUser()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch');
    }

    // HirePurchaseDetail
    public function hirePurchaseDetails()
    {
        return $this->hasOne('App\HirePurchaseDetail', 'sale_id', 'id');
    }

    public function hirePurchaseDetail()
    {
        return $this->hasOne('App\HirePurchaseDetail', 'sale_id', 'id');
    }
}
