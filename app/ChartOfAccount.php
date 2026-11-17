<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'id',
        'type',
        'name',
        'is_bs_id',
        'cash_flow_id',
        'bank_rec_id',
        'group_id',
        'group_type',
        'group_name',
        'section_id',
        'section_name',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        ];

    public static function accountName($id){
        return ChartOfAccount::where('id', $id)->pluck('name');
    }

    public function bankRecs(){
        return $this->hasMany('App\BankReconciliation', 'chart_of_account_id', 'id')->where('status', 1);
    }
}
