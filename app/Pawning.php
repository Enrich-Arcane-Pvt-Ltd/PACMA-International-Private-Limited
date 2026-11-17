<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pawning extends Model
{
    protected $fillable = [
        'id',
        'pawning_id',
        'customer_id',
        'branch_id',
        'officer_id',
        'introducer_id',
        'time_range',
        'time_period_id',
        'issue_date',
        'due_date',
        'installment_date',
        'full_amount_financed',
        'full_amount_payout',
        'amount_payout_in_words',
        'full_financed_charge',
        'released_financed_charge',
        'full_total_of_payment',
        'full_prepayment',
        'current_due_amount',
        'is_extended',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    public function customer() {
        return $this->belongsTo('App\Customer', 'customer_id','id');
    }

    public function pawnedItem() {
        return $this->hasMany('App\PawnedItem', 'pawning_id','id');
    }

    public function installments() {
        return $this->hasMany('App\PawningInstallment', 'pawning_id','id');
    }

    public function receipts() {
        return $this->hasMany('App\PawningReceipt', 'pawning_id','id');
    }

    public function officer(){
        return $this->belongsTo('App\User');
    }

    public function introducer(){
        return $this->belongsTo('App\User');
    }
    // public function timePeriod() {
    //     return $this->belongsTo('App\TimePeriod', 'time_period_id','id');
    // }
    public function lastInstallment(){
        return $this->hasOne('App\PawningInstallment')->where('status', 1)->whereNotNull('paid_amount')->orderBy('id','DESC');
    }
}
