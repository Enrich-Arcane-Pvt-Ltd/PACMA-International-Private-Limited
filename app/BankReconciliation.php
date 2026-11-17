<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankReconciliation extends Model
{
        use SoftDeletes;

    protected $table = 'bank_reconciliations';

    public function recEntriesDebit() {
        return $this->hasMany('App\BankRecEntry', 'bank_rec_id', 'id')->where('debit_credit', 1);
    }

    public function recEntriesCredit() {
        return $this->hasMany('App\BankRecEntry', 'bank_rec_id', 'id')->where('debit_credit', 0);
    }

    public function unCleareRecEntriesDebit() {
        return $this->hasMany('App\BankRecEntry', 'bank_rec_id', 'id')->where('status', 0)->where('debit_credit', 1);
    }

    public function unCleareRecEntriesCredit() {
        return $this->hasMany('App\BankRecEntry', 'bank_rec_id', 'id')->where('status', 0)->where('debit_credit', 0);
    }

    public function cleareRecEntriesDebit() {
        return $this->hasMany('App\BankRecEntry', 'bank_rec_id', 'id')->where('status', 1)->where('debit_credit', 1);
    }

    public function cleareRecEntriesCredit() {
        return $this->hasMany('App\BankRecEntry', 'bank_rec_id', 'id')->where('status', 1)->where('debit_credit', 0);
    }

    // public function chartOfAccount() {
    //     return $this->belongsTo('App\ChartOfAccount');
    // }

    public function chartOfAccount() {
        return $this->belongsTo('App\GeneralLedgerAccount');
    }
}
