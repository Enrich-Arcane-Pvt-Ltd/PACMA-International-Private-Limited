<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    protected $fillable = [
        'phone_mobile2', //
        'business_address', //
        'business_duration',
        'business_duration_on_address', //
        'business_registration', //
        'business_partners', //
        'business_employees', //
        'business_insurance', //
        'business_bank_accounts', //
        'business_bank_name', //
        'business_bank_account_number', //
        'business_bank_type',//
        'any_other_income', //
        'any_other_income_details', //
        'expetation_for_starter_up', //
        'expetation_for_starter_up_details', //
        'insurance', //
        'vahecle_count', //
        'vehicle_model', //
        'vehicle_finance', //
        'vehicle_finance_amount', //
        'other_loan', //
        'other_loan_company', //
        'other_loan_amount', //
        'other_loan_installment', //
        'children_names', //
        'gender', //
        'marital_status', //
        'income_period' //
    ];
}
