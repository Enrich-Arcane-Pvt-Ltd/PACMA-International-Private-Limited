<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    protected $fillable = [
        'id',
        'chart_of_account_id',
        'transaction_type_id',
        'branch_id',
        'sale_id',
        'advance_payment_id',
        'product_id',
        'loan_id',
        'pawning_id',
        'invoice_id',
        'customer_id',
        'description',
        'date',
        'amount',
        'debit_credit',
        'created_by',
        'status',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public function invoice()
    {
        return $this->hasOne('App\Invoice', 'invoice_id', 'id');
    }

    public function customer()
    {
        return $this->hasOne('App\Customer', 'id', 'customer_id');
    }

    public function sales()
    {
        return $this->hasOne('App\Sale', 'id', 'sale_id');
        // return $this->hasMany('App\Sale', 'net_amount', 'value');
    }

    // public function chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'chart_of_account_id');
    // }

    // public function related_chart_of_account(){
    //     return $this->hasOne('App\ChartOfAccount', 'id', 'related_chart_of_account_id');
    // }

    public function chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'chart_of_account_id');
    }

    public function related_chart_of_account(){
        return $this->hasOne('App\GeneralLedgerAccount', 'id', 'related_chart_of_account_id');
    }



    public function branch(){
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    public function type(){
        return $this->hasOne('App\AccountTransactionType', 'id', 'transaction_type_id');
    }

    public function transactionTypes(){
        return $this->belongsTo('App\AccountTransactionType', 'transaction_type_id', 'id');
    }

    public function vender()
    {
        return $this->belongsTo('App\Vender');
    }

    public function employee()
    {
        return $this->belongsTo('App\User', 'employee_id', 'id');
    }

    public function paymentVouchers()
    {
        return $this->belongsTo('App\PaymentVoucher', 'payment_voucher_id', 'id');
    }

    public function advancePayments()
    {
        return $this->belongsTo('App\AdvancePayment', 'advance_payment_id', 'id');
    }

    public function installments()
    {
        return $this->belongsTo('App\Installment', 'installment_id', 'id');
    }

    public function loanInstallments()
    {
        return $this->belongsTo('App\LoanInstallment', 'loan_installment_id', 'id');
    }

    public function pawningInstallments()
    {
        return $this->belongsTo('App\PawningInstallment', 'pawning_installment_id', 'id');
    }

    public function customReceipts()
    {
        return $this->belongsTo('App\CustomReceipt', 'custom_receipt_id', 'id');
    }

    public function loans()
    {
        return $this->belongsTo('App\Loan', 'loan_id', 'id');
    }

    public function pawnings()
    {
        return $this->belongsTo('App\Pawning', 'pawning_id', 'id');
    }
}
