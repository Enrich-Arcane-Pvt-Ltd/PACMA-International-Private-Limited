<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_id',
        'customer_id',
        'issue_date',
        'status',
        'category_id',
        'is_convert',
        'converted_invoice_id',
        'created_by',
    ];

    public static $statues = [
        'Draft',
        //0
        'Open',
        //1
        'Accepted',
        //2
        'Declined',
        //3
        'Close',
        //4
    ];


    public function tax()
    {
        return $this->hasOne('App\Tax', 'id', 'tax_id');
    }

    public function items()
    {
        return $this->hasMany('App\ProposalProduct', 'proposal_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach($this->items as $product)
        {
            $subTotal += ($product->price * $product->quantity);
        }

        return $subTotal;
    }

    public function getTotalTax()
    {
        $totalTax = 0;
        foreach($this->items as $product)
        {
            $taxes = Utility::totalTaxRate($product->tax);

            $totalTax += ($taxes / 100) * ($product->price * $product->quantity);
        }

        return $totalTax;
    }

    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach($this->items as $product)
        {
            $totalDiscount += $product->discount;
        }

        return $totalDiscount;
    }

    public function getTotal()
    {
        return ($this->getSubTotal() + $this->getTotalTax()) - $this->getTotalDiscount();
    }

    public function getDue()
    {
        $due = 0;
        foreach($this->payments as $payment)
        {
            $due += $payment->amount;
        }

        return ($this->getTotal() - $due) - $this->invoiceTotalCreditNote();
    }

    public static function change_status($receipt_id, $status)
    {

        $receipt         = Receipt::find($receipt_id);
        $receipt->status = $status;
        $receipt->update();
    }

    public function category()
    {
        return $this->hasOne('App\ProductServiceCategory', 'id', 'category_id');
    }

    public function creates()
    {
        // return $this->hasOne('App\User', 'id', 'created_by');
        return $this->hasOne('App\User', 'id');
    }

    public function invoice(){
        return $this->hasOne('App\Invoice', 'id', 'invoice_id');
    }

    public function installments()
    {
        return $this->hasOne('App\Installment', 'receipt_id', 'id');
    }

    public function loanInstallments()
    {
        return $this->hasOne('App\LoanInstallment', 'receipt_id', 'id');
    }

    public function sales()
    {
        return $this->hasOne('App\Sale', 'invoice_id', 'invoice_id');
    }

    public function loans()
    {
        return $this->hasOne('App\Loan', 'id', 'loan_id');
    }

    public function advance()
    {
        return $this->hasOne('App\AdvancePayment', 'receipt_id', 'id');
    }

    // public function created()
    // {
    //     return $this->belongsTo('App\User', 'id', 'created_by');
    // }
}
