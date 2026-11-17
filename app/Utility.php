<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Utility extends Model
{
    public static function settings()
    {
        $data = DB::table('settings');
        if(\Auth::check())
        {
            $userId = \Auth::user()->creatorId();
            $data   = $data->where('created_by', '=', $userId);
        }
        else
        {
            $data = $data->where('created_by', '=', 1);
        }
        $data     = $data->get();
        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "LKR",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_month_format" => "M, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "INVO",
            "invoice_color" => "fffff",
            "proposal_prefix" => "PROP",
            "proposal_color" => "fffff",
            "bill_prefix" => "BILL",
            "bill_color" => "fffff",
            "customer_prefix" => "CUST",
            "vender_prefix" => "VEND",
            "product_brand_prefix" => "PBRA",
            "product_category_prefix" => "PCAT",
            "product_sub_category_prefix" => "PSCA",
            "product_prefix" => "PROD",
            "pawning_prefix" => "PAWN",
            "bank_account_prefix" => "BACC",
            "bank_transfer_prefix" => "BTRA",
            "asset_prefix" => "ASSE",
            "service_bill_prefix"=>"BILL",
            "branch_prefix" => "BRAN",
            "center_prefix" => "CENT",
            "jewellery_prefix" => "JEWE",
            "service_prefix" => "SERV",
            "time_period_prefix" => "TPER",
            "interest_rate_prefix" => "IRAT",
            "gold_rate_prefix" => "GRAT",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "registration_number" => "",
            "vat_number" => "",
            "default_language" => "en",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_type" => "VAT",
            "billing_display" => "on",
            "purchase_order_prefix" => "PO",
            "grn_order"=>"GRN",
            "loan"=>"LN",
            "group"=>"GRP",
            "user_code"=>"USR",
            "receipt_prefix"=>"RPT",
            "advance_prefix" => "ADV",
            "petty_cash_prefix" => "PECA",
            "cheque_prefix" => "CHEQ",
            "cheque_book_prefix" => "CHB",
            "business_category_prefix" => "BCAT",
            "stock_transfer_prefix" => "STRN",
            "account_group_prefix"=>"ACG",
            "payment_voucher_prefix"=>"VCH",
            "chart_of_account_prefix"=>"ACC",
            "account_transaction_prefix"=>"ATR",
            "trial_balance_prefix"=>"TRB",
            "general_journal_prefix"=>"GENJ",
            "cash_book_prefix"=>"CASH",
            "petty_cash_book_prefix"=>"PECA",
            "waitting_sale"=>"WHS",
            "waitting_sale_inatallment"=>"WHI",
            "waitting_loan_inatallment"=>"WLI",
            "waitting_pawning_inatallment"=>"WPI",
            "debit_note_prefix"=>"DN",
            "credit_note_prefix"=>"CN",
            "item_return_prefix"=>"ITR",
            "bank_rec_prefix"=>"BKR",
            "shipping_display" => "on",
        ];

        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function languages()
    {
        $dir     = base_path() . '/resources/lang/';
        $glob    = glob($dir . "*", GLOB_ONLYDIR);
        $arrLang = array_map(
            function ($value) use ($dir){
                return str_replace($dir, '', $value);
            }, $glob
        );
        $arrLang = array_map(
            function ($value) use ($dir){
                return preg_replace('/[0-9]+/', '', $value);
            }, $arrLang
        );
        $arrLang = array_filter($arrLang);

        return $arrLang;
    }

    public static function getValByName($key)
    {
        $setting = Utility::settings();
        if(!isset($setting[$key]) || empty($setting[$key]))
        {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if(!file_put_contents($envFile, $str))
        {
            return false;
        }

        return true;
    }

    public static function templateData()
    {
        $arr              = [];
        $arr['colors']    = [
            '003580',
            '666666',
            '6676ef',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];

        return $arr;
    }

    public static function priceFormat($settings, $price)
    {
        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') .' '. number_format($price, Utility::getValByName('decimal_number')) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public static function currencySymbol($settings)
    {
        return $settings['site_currency_symbol'];
    }

    public static function dateFormat($settings, $date)
    {
        return date($settings['site_date_format'], strtotime($date));
    }

    public static function timeFormat($settings, $time)
    {
        return date($settings['site_time_format'], strtotime($time));
    }

    public static function invoiceNumberFormat($settings, $number)
    {

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public static function proposalNumberFormat($settings, $number)
    {
        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public static function billNumberFormat($settings, $number)
    {
        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }

    public static function purchaseOrderNumberFormat($settings, $number)
    {
        return $settings["purchase_order_prefix"] . sprintf("%05d", $number);
    }

    public static function tax($taxes)
    {

        $taxArr = explode(',', $taxes);
        $taxes  = [];
        foreach($taxArr as $tax)
        {
            $taxes[] = Tax::find($tax);
        }

        return $taxes;
    }

    public static function taxRate($taxRate, $price, $quantity)
    {

        return ($taxRate / 100) * ($price * $quantity);
    }

    public static function totalTaxRate($taxes)
    {

        $taxArr  = explode(',', $taxes);
        $taxRate = 0;

        foreach($taxArr as $tax)
        {

            $tax     = Tax::find($tax);
            $taxRate += !empty($tax->rate) ? $tax->rate : 0;
        }

        return $taxRate;
    }

    public static function userBalance($users, $id, $amount, $type)
    {
        if($users == 'customer')
        {
            $user = Customer::find($id);
        }
        else
        {
            $user = Vender::find($id);
        }

        if(!empty($user))
        {
            if($type == 'credit')
            {
                $oldBalance    = $user->balance;
                $user->balance = $oldBalance + $amount;
                $user->save();
            }
            elseif($type == 'debit')
            {
                $oldBalance    = $user->balance;
                $user->balance = $oldBalance - $amount;
                $user->save();
            }
        }
    }

    public static function bankAccountBalance($id, $amount, $type)
    {
        // $bankAccount = BankAccount::find($id);
        // if($bankAccount)
        // {
        //     if($type == 'credit')
        //     {
        //         $oldBalance                   = $bankAccount->opening_balance;
        //         $bankAccount->opening_balance = $oldBalance + $amount;
        //         $bankAccount->save();
        //     }
        //     elseif($type == 'debit')
        //     {
        //         $oldBalance                   = $bankAccount->opening_balance;
        //         $bankAccount->opening_balance = $oldBalance - $amount;
        //         $bankAccount->save();
        //     }
        // }

    }

    public static function accountSalesTransaction($date, $accountId, $relatedAccountId, $saleId, $productId, $invoiceId, $customerId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->sale_id = $saleId;
        $transaction->product_id = $productId;
        $transaction->invoice_id = $invoiceId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountSalesInstallmentTransaction($date, $accountId, $relatedAccountId, $saleId, $installmentId, $productId, $invoiceId, $customerId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->sale_id = $saleId;
        $transaction->installment_id = $installmentId;
        $transaction->product_id = $productId;
        $transaction->invoice_id = $invoiceId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountAdvancePaymentTransaction($date, $accountId, $relatedAccountId, $advancePaymentId, $customerId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->advance_payment_id = $advancePaymentId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountAdvancePaymentSettleTransaction($date, $accountId, $relatedAccountId, $saleId, $invoiceId, $customerId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->sale_id = $saleId;
        $transaction->invoice_id = $invoiceId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountGrnTransaction($date, $accountId, $relatedAccountId, $grnId, $productId, $venderId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->grn_id = $grnId;
        $transaction->product_id = $productId;
        $transaction->vender_id = $venderId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountStockTransferTransaction($date, $accountId, $relatedAccountId, $stockTransferId, $productId, $branchId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = $branchId;
        $transaction->stock_transfer_id = $stockTransferId;
        $transaction->product_id = $productId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function deleteAccountSalesTransaction($saleId){
        $transactions = AccountTransaction::where('sale_id', $saleId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function deleteAccountSalesInstallmentTransaction($installmentId){
        $transactions = AccountTransaction::where('installment_id', $installmentId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function deleteAccountAdvancePaymentTransaction($advanceId){
        $transactions = AccountTransaction::where('advance_payment_id', $advanceId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function deleteAccountGrnTransaction($grnId){
        $transactions = AccountTransaction::where('grn_id', $grnId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function accountLoanTransaction($date, $accountId, $relatedAccountId, $paymentVoucherId, $loanId, $customerId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->payment_voucher_id = $paymentVoucherId;
        $transaction->loan_id = $loanId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountLoanInstallmentTransaction($date, $accountId, $relatedAccountId, $loanId, $installmentId, $customerId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->loan_id = $loanId;
        $transaction->loan_installment_id = $installmentId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountPawningTransaction($date, $accountId, $relatedAccountId, $paymentVoucherId, $pawningId, $customerId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->payment_voucher_id = $paymentVoucherId;
        $transaction->pawning_id = $pawningId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountPawningInstallmentTransaction($date, $accountId, $relatedAccountId, $pawningId, $installmentId, $customerId, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->pawning_id = $pawningId;
        $transaction->pawning_installment_id = $installmentId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function accountPaymentVoucherTransaction($date, $accountId, $relatedAccountId, $amount, $paymentVoucherId, $payType, $paidTo, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = 14;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->payment_voucher_id = $paymentVoucherId;
        if($payType == 'supplier'){
            $transaction->vender_id = $paidTo;
        }elseif($payType == 'customer'){
            $transaction->customer_id = $paidTo;
        }
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function deleteAccountPaymentVoucherTransaction($voucherId){
        $transactions = AccountTransaction::where('payment_voucher_id', $voucherId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function deleteAccountLoanInstallmentTransaction($installmentId){
        $transactions = AccountTransaction::where('loan_installment_id', $installmentId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function deleteAccountPawningInstallmentTransaction($installmentId){
        $transactions = AccountTransaction::where('pawning_installment_id', $installmentId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function addPettyCashFund($amount){
        $fund = PettyCashFund::where('branch_id', '=', \Auth::user()->branchId())->first();
        if (!$fund) {
            $fund = new PettyCashFund();
            $fund->branch_id = \Auth::user()->branchId();
            $fund->fund_amount = 0;
            $fund->balance_amount = 0;
            $fund->status = 1;
        }
        $fund->balance_amount = ($fund->balance_amount ?? 0) + $amount;
        $fund->save();
    }

    public static function accountPettyCashFundTransaction($date, $accountId, $relatedAccountId, $amount, $paymentVoucherId, $type){

        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = 11;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->payment_voucher_id = $paymentVoucherId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }
    public static function accountPettyCashTransaction($date, $accountId, $relatedAccountId, $pettyChashId, $amount, $employeeId, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = 12;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->petty_cash_id = $pettyChashId;
        $transaction->employee_id = $employeeId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function deleteAccountPettyCashTransaction($pettyCashId){
        $transactions = AccountTransaction::where('petty_cash_id', $pettyCashId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function accountBankTransferTransaction($date, $accountId, $relatedAccountId, $amount, $tranferId, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = 15;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->bank_transfer_id = $tranferId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function deleteBankTransferAccountTransation($transferId){
        $transactions = AccountTransaction::where('bank_transfer_id', $transferId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function generalJournalAccountTransation($date, $accountId, $accountType, $relatedAccountId, $relatedAccountType, $amount, $journalId){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = 16;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->general_journal_id = $journalId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        $transaction->debit_credit = $accountType;
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();

        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $relatedAccountId;
        $transaction->related_chart_of_account_id = $accountId;
        $transaction->transaction_type_id = 16;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->general_journal_id = $journalId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        $transaction->debit_credit = $relatedAccountType;
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function deleteGeneralJournalAccountTransation($journalId){
        $transactions = AccountTransaction::where('general_journal_id', $journalId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function itemReturnAccountTransation($date, $accountId, $accountType, $relatedAccountId, $relatedAccountType, $amount, $returnId, $customerId){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = 18;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->item_return_id = $returnId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        $transaction->debit_credit = $accountType;
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();

        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $relatedAccountId;
        $transaction->related_chart_of_account_id = $accountId;
        $transaction->transaction_type_id = 18;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->item_return_id = $returnId;
        $transaction->customer_id = $customerId;
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        $transaction->debit_credit = $relatedAccountType;
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function deleteItemReturnAccountTransation($returnId){
        $transactions = AccountTransaction::where('item_return_id', $returnId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    public static function accountReceiptTransaction($date, $accountId, $relatedAccountId, $receiptId, $receiptFrom, $receiptType, $transType, $amount, $type){
        $transaction = new AccountTransaction();
        $transaction->chart_of_account_id = $accountId;
        $transaction->related_chart_of_account_id = $relatedAccountId;
        $transaction->transaction_type_id = $transType;
        $transaction->branch_id = \Auth::user()->branchId();
        $transaction->custom_receipt_id = $receiptId;
        if($receiptType == 'supplier'){
            $transaction->vender_id = $receiptFrom;
        }elseif($receiptType == 'customer'){
            $transaction->customer_id = $receiptFrom;
        }
        $transaction->date = $date;
        $transaction->amount = $amount;
        $transaction->status = 1;
        if($type == 'credit'){
            $transaction->debit_credit = 0;
        } else if($type == 'debit') {
            $transaction->debit_credit = 1;
        }
        $transaction->created_by = \Auth::user()->id;
        $transaction->save();
    }

    public static function deleteReceiptTransaction($receiptId){
        $transactions = AccountTransaction::where('custom_receipt_id', $receiptId)->where('status', 1)->get();
        foreach ($transactions as $transaction) {
            $transaction->status = 0;
            $transaction->update();
        }
    }

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3)
        {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else
        {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R   = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for($i = 0; $i < count($C); ++$i)
        {
            if($C[$i] <= 0.03928)
            {
                $C[$i] = $C[$i] / 12.92;
            }
            else
            {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if($L > 0.179)
        {
            $color = 'black';
        }
        else
        {
            $color = 'white';
        }

        return $color;
    }

    public static function delete_directory($dir)
    {
        if(!file_exists($dir))
        {
            return true;
        }
        if(!is_dir($dir))
        {
            return unlink($dir);
        }
        foreach(scandir($dir) as $item)
        {
            if($item == '.' || $item == '..')
            {
                continue;
            }
            if(!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item))
            {
                return false;
            }
        }
        return rmdir($dir);
    }
}
