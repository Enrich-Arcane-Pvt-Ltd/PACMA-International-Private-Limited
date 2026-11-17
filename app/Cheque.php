<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    protected $fillable = [
        'cross_type',
        'date',
        'pay_type',
        'pay',
        'amount',
        'amount_in_word',
    ];


    public function supplier()
    {
        return $this->belongsTo('App\Vender', 'pay', 'id');
    }

    public function vender()
    {
        return $this->hasOne('App\Vender', 'id', 'vender_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function bank()
    {
        return $this->belongsTo('App\BankAccount', 'cheque_bank_id', 'id');
    }

    // public function account(){
    //     return $this->belongsTo('App\ChartOfAccount', 'chart_of_account_id', 'id');
    // }
    public function account(){
        return $this->belongsTo('App\GeneralLedgerAccount', 'chart_of_account_id', 'id');
    }

    public function voucher(){
        return $this->belongsTo('App\PaymentVoucher', 'payment_voucher_id', 'id');
    }

    public function paymentVoucher(){
        return $this->hasOne('App\PaymentVoucher', 'cheque_id', 'id');
    }

    public function getChartOfAccountAttribute()
    {
        if ($this->relationLoaded('account') && $this->getRelation('account')) {
            return $this->getRelation('account');
        }
        if ($this->chart_of_account_id && $this->account) {
            return $this->account;
        }
        if ($this->relationLoaded('voucher') && $this->voucher && $this->voucher->account) {
            return $this->voucher->account;
        }
        if ($this->payment_voucher_id && $this->voucher && $this->voucher->account) {
            return $this->voucher->account;
        }
        if ($this->paymentVoucher && $this->paymentVoucher->account) {
            return $this->paymentVoucher->account;
        }
        if (!empty($this->cheque_number)) {
            $voucher = PaymentVoucher::where('cheque_number', $this->cheque_number)
                ->whereNotNull('chart_of_account_id')
                ->first();
            if ($voucher && $voucher->account) {
                return $voucher->account;
            }
        }
        return null;
    }

    public function getChartOfAccountNameAttribute()
    {
        $account = $this->chart_of_account;
        return $account ? ($account->account_name ?? $account->name ?? '') : '';
    }
}

