<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;


    protected $appends = ['profile'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'avatar',
        'lang',
        'delete_status',
        'plan',
        'plan_expire_date',
        'created_by',
        'branch_id',
        'reference_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $settings;

    public function authId()
    {
        return $this->id;
    }

    public function creatorId()
    {
        if ($this->type == 'company' || $this->type == 'super admin') {
            return $this->id;
        } else {
            return $this->created_by;
        }
    }

    public function canEditGrnCostAndQuantity()
    {
        return in_array(strtolower((string) $this->type), ['admin', 'super admin', 'company'], true);
    }

    public function canDeleteGrn()
    {
        return $this->canEditGrnCostAndQuantity() && $this->can('delete grn');
    }

    public function canDeleteProduct()
    {
        return $this->canEditGrnCostAndQuantity() && (
            $this->can('delete product') || in_array($this->type, ['company', 'super admin'], true)
        );
    }

    public function canDeleteProductCategory($subCategory = false)
    {
        return $this->canEditGrnCostAndQuantity() && (
            in_array($this->type, ['company', 'super admin'], true)
            || $this->can('delete product')
            || $this->can($subCategory ? 'delete product sub category' : 'delete product category')
            || $this->can('edit product')
        );
    }

    public function branchId()
    {
        return $this->branch_id;
    }

    public function branchName()
    {
        $branchId = $this->branch_id;
        $branch = Branch::where('id', $branchId)->pluck('name');
        if (count($branch) > 0) {
            return $branch[0];
        } else {
            return null;
        }
    }

    public function userName()
    {
        return $this->name;
    }

    public function otherUserName($id)
    {
        $user = User::where('id', $id)->pluck('name');
        if (count($user) > 0) {
            return $user[0];
        } else {
            return null;
        }
    }

    public function currentLanguage()
    {
        return $this->lang;
    }

    public function ordinal($number)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }

    public function priceFormat($price)
    {
        $settings = Utility::settings();

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . ' ' . number_format($price, Utility::getValByName('decimal_number')) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public function currencySymbol()
    {
        $settings = Utility::settings();

        return $settings['site_currency_symbol'];
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();
        return date($settings['site_date_format'], strtotime($date));
    }

    public function monthFormat($date)
    {
        $settings = Utility::settings();
        return date($settings['site_month_format'], strtotime($date));
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();

        return date($settings['site_time_format'], strtotime($time));
    }

    public function invoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public function proposalNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public function billNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }

    public function purchaseOrderNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["purchase_order_prefix"] . sprintf("%05d", $number);
    }

    public function getPlan()
    {
        return $this->hasOne('App\Plan', 'id', 'plan');
    }

    public function assignPlan($planID)
    {
        $plan = Plan::find($planID);
        if ($plan) {
            $this->plan = $plan->id;
            if ($plan->duration == 'month') {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->duration == 'year') {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            }
            $this->save();

            $users     = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'super admin')->where('type', '!=', 'company')->get();
            $customers = Customer::where('created_by', '=', \Auth::user()->creatorId())->get();
            $venders   = Vender::where('created_by', '=', \Auth::user()->creatorId())->get();


            if ($plan->max_users == -1) {
                foreach ($users as $user) {
                    $user->is_active = 1;
                    $user->save();
                }
            } else {
                $userCount = 0;
                foreach ($users as $user) {
                    $userCount++;
                    if ($userCount <= $plan->max_users) {
                        $user->is_active = 1;
                        $user->save();
                    } else {
                        $user->is_active = 0;
                        $user->save();
                    }
                }
            }

            if ($plan->max_customers == -1) {
                foreach ($customers as $customer) {
                    $customer->is_active = 1;
                    $customer->save();
                }
            } else {
                $customerCount = 0;
                foreach ($customers as $customer) {
                    $customerCount++;
                    if ($customerCount <= $plan->max_customers) {
                        $customer->is_active = 1;
                        $customer->save();
                    } else {
                        $customer->is_active = 0;
                        $customer->save();
                    }
                }
            }


            if ($plan->max_venders == -1) {
                foreach ($venders as $vender) {
                    $vender->is_active = 1;
                    $vender->save();
                }
            } else {
                $venderCount = 0;
                foreach ($venders as $vender) {
                    $venderCount++;
                    if ($venderCount <= $plan->max_venders) {
                        $vender->is_active = 1;
                        $vender->save();
                    } else {
                        $vender->is_active = 0;
                        $vender->save();
                    }
                }
            }

            return ['is_success' => true];
        } else {
            return [
                'is_success' => false,
                'error' => 'Plan is deleted.',
            ];
        }
    }

    public function customerNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["customer_prefix"] . sprintf("%05d", $number);
    }

    public function accountGroupNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["account_group_prefix"] . sprintf("%05d", $number);
    }

    public function accountTransactionNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["account_transaction_prefix"] . sprintf("%05d", $number);
    }

    public function trialBalanceNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["trial_balance_prefix"] . sprintf("%05d", $number);
    }

    public function cashBookNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["cash_book_prefix"] . sprintf("%05d", $number);
    }

    public function pettyCashBookNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["petty_cash_book_prefix"] . sprintf("%05d", $number);
    }

    public function accountSectionNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["account_section_prefix"] . sprintf("%05d", $number);
    }

    public function chartOfAccountNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["chart_of_account_prefix"] . sprintf("%05d", $number);
    }

    public function journalNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["general_journal_prefix"] . sprintf("%05d", $number);
    }

    public function paymentVoucherNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["payment_voucher_prefix"] . sprintf("%05d", $number);
    }

    public function venderNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["vender_prefix"] . sprintf("%05d", $number);
    }

    public function productBrandNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["product_brand_prefix"] . sprintf("%05d", $number);
    }

    public function productCategoryNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["product_category_prefix"] . sprintf("%05d", $number);
    }

    public function productSubCategoryNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["product_sub_category_prefix"] . sprintf("%05d", $number);
    }

    public function productNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["product_prefix"] . sprintf("%05d", $number);
    }

    public function pawningNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["pawning_prefix"] . sprintf("%05d", $number);
    }

    public function bankAccountNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["bank_account_prefix"] . sprintf("%05d", $number);
    }

    public function bankTransferNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["bank_transfer_prefix"] . sprintf("%05d", $number);
    }

    public function assetNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["asset_prefix"] . sprintf("%05d", $number);
    }

    public function serviceBillNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["service_bill_prefix"] . sprintf("%05d", $number);
    }


    public function branchNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["branch_prefix"] . sprintf("%05d", $number);
    }


    public function centerNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["center_prefix"] . sprintf("%05d", $number);
    }


    public function jewelleryNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["jewellery_prefix"] . sprintf("%05d", $number);
    }


    public function serviceNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["service_prefix"] . sprintf("%05d", $number);
    }

    public function timePeriodNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["time_period_prefix"] . sprintf("%05d", $number);
    }

    public function interestRateNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["interest_rate_prefix"] . sprintf("%05d", $number);
    }

    public function goldRateNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["gold_rate_prefix"] . sprintf("%05d", $number);
    }

    public function GrnNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["grn_order"] . sprintf("%06d", $number);
    }

    public function loanNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["loan"] . sprintf("%06d", $number);
    }

    public function groupNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["group"] . sprintf("%06d", $number);
    }

    public function userNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["user_code"] . sprintf("%05d", $number);
    }

    public function receiptNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["receipt_prefix"] . sprintf("%05d", $number);
    }

    public function advanceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["advance_prefix"] . sprintf("%05d", $number);
    }

    public function pettyCashNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["petty_cash_prefix"] . sprintf("%05d", $number);
    }

    public function chequeNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["cheque_prefix"] . sprintf("%05d", $number);
    }

    public function chequeBookNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["cheque_book_prefix"] . sprintf("%05d", $number);
    }

    public function businessCategoryNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["business_category_prefix"] . sprintf("%05d", $number);
    }

    public function stockTransferNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["stock_transfer_prefix"] . sprintf("%05d", $number);
    }

    public function waittingSaleNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["waitting_sale"] . sprintf("%05d", $number);
    }

    public function waittingSaleInstallmentNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["waitting_sale_inatallment"] . sprintf("%05d", $number);
    }

    public function waittingLoanInstallmentNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["waitting_loan_inatallment"] . sprintf("%05d", $number);
    }

    public function waittingPawningInstallmentNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["waitting_pawning_inatallment"] . sprintf("%05d", $number);
    }

    public function debitNoteNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["debit_note_prefix"] . sprintf("%05d", $number);
    }

    public function creditNoteNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["credit_note_prefix"] . sprintf("%05d", $number);
    }

    public function itemReturnNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["item_return_prefix"] . sprintf("%05d", $number);
    }

    public function bankRecNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["bank_rec_prefix"] . sprintf("%05d", $number);
    }

    public function countUsers()
    {
        // return User::where('type', '!=', 'super admin')->where('type', '!=', 'company')->where('created_by', '=', $this->creatorId())->count();
        return User::where('type', '!=', 'super admin')->where('type', '!=', 'company')->count();
    }

    public function countCompany()
    {
        return User::where('type', '=', 'company')->where('created_by', '=', $this->creatorId())->count();
    }

    public function countOrder()
    {
        return Order::count();
    }

    public function countplan()
    {
        return Plan::count();
    }

    public function countPaidCompany()
    {
        return User::where('type', '=', 'company')->whereNotIn(
            'plan',
            [
                0,
                1,
            ]
        )->where('created_by', '=', \Auth::user()->id)->count();
    }

    public function countCustomers()
    {
        return Customer::where('branch_id', '=', $this->branchId())->count();
    }

    public function countVenders()
    {
        return Vender::count();
    }

    public function countInvoices()
    {
        return Invoice::where('branch_id', '=', $this->branchId())->count();
    }

    public function countBills()
    {
        return PaymentVoucher::where('branch_id', '=', $this->branchId())->where('status', 2)->count();
    }

    public function todayIncome()
    {
        $revenue      = Revenue::where('branch_id', '=', $this->branchId())->whereRaw('Date(date) = CURDATE()')->sum('amount');
        $invoices     = Invoice::select('*')->where('branch_id', $this->branchId())->whereRAW('Date(issue_date) = CURDATE()')->get();
        $sales        = Sale::select('*')->where('branch_id', $this->branchId())->whereRAW('Date(date_of_sale) = CURDATE()')->get();
        $installments = Installment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRAW('Date(paid_date) = CURDATE()')->get();
        $loanInstallments = LoanInstallment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRAW('Date(paid_date) = CURDATE()')->get();
        $pawningInstallments = PawningInstallment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRAW('Date(paid_date) = CURDATE()')->get();
        $invoiceArray = array();
        $saleArray = array();
        $installmentArray = array();
        $loanInstallmentArray = array();
        $PawningInstallmentArray = array();
        foreach ($invoices as $invoice) {
            $invoiceArray[] = $invoice->getTotal();
        }
        foreach ($sales as $sale) {
            $saleArray[] = $sale->paid_amount;
        }
        foreach ($installments as $installment) {
            $installmentArray[] = $installment->paid_amount;
        }
        foreach ($loanInstallments as $loanInstallment) {
            $loanInstallmentArray[] = $loanInstallment->paid_amount;
        }
        foreach ($pawningInstallments as $pawningInstallment) {
            $PawningInstallmentArray[] = $pawningInstallment->paid_amount;
        }
        $totalIncome = (!empty($revenue) ? $revenue : 0) + (!empty($invoiceArray) ? array_sum($invoiceArray) : 0) + (!empty($saleArray) ? array_sum($saleArray) : 0) + (!empty($installmentArray) ? array_sum($installmentArray) : 0) + (!empty($loanInstallmentArray) ? array_sum($loanInstallmentArray) : 0) + (!empty($PawningInstallmentArray) ? array_sum($PawningInstallmentArray) : 0);

        return $totalIncome;
    }

    public function todayExpense()
    {
        $payment = Payment::where('created_by', '=', $this->creatorId())->where('created_by', \Auth::user()->creatorId())->whereRaw('Date(date) = CURDATE()')->sum('amount');

        $bills = PaymentVoucher::select('*')->where('branch_id', $this->branchId())->where('status', 2)->whereRAW('Date(date) = CURDATE()')->get();

        $billArray = array();
        foreach ($bills as $bill) {
            $billArray[] = $bill->amount;
        }

        $totalExpense = (!empty($payment) ? $payment : 0) + (!empty($billArray) ? array_sum($billArray) : 0);

        return $totalExpense;
    }

    public function incomeCurrentMonth()
    {
        $currentMonth = date('m');
        $revenue      = Revenue::where('branch_id', '=', $this->branchId())->whereRaw('MONTH(date) = ?', [$currentMonth])->sum('amount');

        $invoices = Invoice::select('*')->where('branch_id', $this->branchId())->whereRAW('MONTH(issue_date) = ?', [$currentMonth])->get();

        $sales        = Sale::select('*')->where('branch_id', $this->branchId())->whereRAW('MONTH(date_of_sale) = ?', [$currentMonth])->where('status', 1)->get();
        $installments = Installment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRAW('MONTH(paid_date) = ?', [$currentMonth])->get();
        $loanInstallments = LoanInstallment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRAW('MONTH(paid_date) = ?', [$currentMonth])->get();
        $pawningInstallments = PawningInstallment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRAW('MONTH(paid_date) = ?', [$currentMonth])->get();

        $invoiceArray = array();
        $saleArray = array();
        $installmentArray = array();
        $loanInstallmentArray = array();
        $PawningInstallmentArray = array();

        foreach ($invoices as $invoice) {
            $invoiceArray[] = $invoice->getTotal();
        }
        foreach ($sales as $sale) {
            $saleArray[] = $sale->paid_amount;
        }
        foreach ($installments as $installment) {
            $installmentArray[] = $installment->paid_amount;
        }
        foreach ($loanInstallments as $loanInstallment) {
            $loanInstallmentArray[] = $loanInstallment->paid_amount;
        }
        foreach ($pawningInstallments as $pawningInstallment) {
            $PawningInstallmentArray[] = $pawningInstallment->paid_amount;
        }
        $totalIncome = (!empty($revenue) ? $revenue : 0) + (!empty($invoiceArray) ? array_sum($invoiceArray) : 0) + (!empty($saleArray) ? array_sum($saleArray) : 0) + (!empty($installmentArray) ? array_sum($installmentArray) : 0) + (!empty($loanInstallmentArray) ? array_sum($loanInstallmentArray) : 0) + (!empty($PawningInstallmentArray) ? array_sum($PawningInstallmentArray) : 0);

        return $totalIncome;
    }

    public function expenseCurrentMonth()
    {
        $currentMonth = date('m');

        $payment = Payment::where('created_by', '=', $this->creatorId())->whereRaw('MONTH(date) = ?', [$currentMonth])->sum('amount');

        $bills     = PaymentVoucher::select('*')->where('branch_id', $this->branchId())->where('status', 2)->whereRAW('MONTH(date) = ?', [$currentMonth])->get();
        $billArray = array();
        foreach ($bills as $bill) {
            $billArray[] = $bill->amount;
        }

        $totalExpense = (!empty($payment) ? $payment : 0) + (!empty($billArray) ? array_sum($billArray) : 0);

        return $totalExpense;
    }

    public function getincExpBarChartData()
    {
        $month[]       = __('January');
        $month[]       = __('February');
        $month[]       = __('March');
        $month[]       = __('April');
        $month[]       = __('May');
        $month[]       = __('June');
        $month[]       = __('July');
        $month[]       = __('August');
        $month[]       = __('September');
        $month[]       = __('October');
        $month[]       = __('November');
        $month[]       = __('December');
        $data['month'] = $month;


        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome = Revenue::selectRaw('sum(amount) amount')->where('branch_id', '=', $this->branchId())->whereRaw('year(`date`) = ?', array(date('Y')))->whereRaw('month(`date`) = ?', $i)->first();
            $invoices      = Invoice::select('*')->where('branch_id', $this->branchId())->whereRaw('year(`issue_date`) = ?', array(date('Y')))->whereRaw('month(`issue_date`) = ?', $i)->get();

            $sales        = Sale::select('*')->where('branch_id', $this->branchId())->whereRaw('year(`date_of_sale`) = ?', array(date('Y')))->whereRAW('month(date_of_sale) = ?', $i)->where('status', 1)->get();
            $installments = Installment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRaw('year(`paid_date`) = ?', array(date('Y')))->whereRAW('month(paid_date) = ?', $i)->get();
            $loanInstallments = LoanInstallment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRaw('year(`paid_date`) = ?', array(date('Y')))->whereRAW('month(paid_date) = ?', $i)->get();
            $pawningInstallments = PawningInstallment::select('*')->where('branch_id', $this->branchId())->where('status', 1)->whereRaw('year(`paid_date`) = ?', array(date('Y')))->whereRAW('month(paid_date) = ?', $i)->get();

            $invoiceArray = array();
            $saleArray = array();
            $installmentArray = array();
            $loanInstallmentArray = array();
            $PawningInstallmentArray = array();
            foreach ($invoices as $invoice) {
                $invoiceArray[] = $invoice->getTotal();
            }
            foreach ($sales as $sale) {
                $saleArray[] = $sale->paid_amount;
            }
            foreach ($installments as $installment) {
                $installmentArray[] = $installment->paid_amount;
            }
            foreach ($loanInstallments as $loanInstallment) {
                $loanInstallmentArray[] = $loanInstallment->paid_amount;
            }
            foreach ($pawningInstallments as $pawningInstallment) {
                $PawningInstallmentArray[] = $pawningInstallment->paid_amount;
            }
            $totalIncome = (!empty($monthlyIncome) ? $monthlyIncome->amount : 0) + (!empty($invoiceArray) ? array_sum($invoiceArray) : 0) + (!empty($saleArray) ? array_sum($saleArray) : 0) + (!empty($installmentArray) ? array_sum($installmentArray) : 0) + (!empty($loanInstallmentArray) ? array_sum($loanInstallmentArray) : 0) + (!empty($PawningInstallmentArray) ? array_sum($PawningInstallmentArray) : 0);

            $incomeArr['label']           = __('Income');
            $incomeArr['backgroundColor'] = '#6777ef';
            $incomeArr['borderColor']     = '#6777ef';
            $incomeArr['data'][]          = !empty($totalIncome) ? $totalIncome : 0;

            $monthlyExpense = Payment::selectRaw('sum(amount) amount')->where('created_by', '=', $this->creatorId())->whereRaw('year(`date`) = ?', array(date('Y')))->whereRaw('month(`date`) = ?', $i)->first();
            $bills          = PaymentVoucher::select('*')->where('branch_id', $this->branchId())->where('status', 2)->whereRaw('year(`date`) = ?', array(date('Y')))->whereRaw('month(`date`) = ?', $i)->get();
            $billArray      = array();
            foreach ($bills as $bill) {
                $billArray[] = $bill->amount;
            }

            $totalExpense                  = (!empty($monthlyExpense) ? $monthlyExpense->amount : 0) + (!empty($billArray) ? array_sum($billArray) : 0);
            $expenseArr['label']           = __('Expense');
            $expenseArr['backgroundColor'] = '#fc544b';
            $expenseArr['borderColor']     = '#fc544b';
            $expenseArr['data'][]          = !empty($totalExpense) ? $totalExpense : 0;
        }

        $dataArr[] = $incomeArr;
        $dataArr[] = $expenseArr;

        $data['incExpArr'] = $dataArr;
        return $data;
    }

    public function getIncExpLineChartDate()
    {
        $usr           = \Auth::user();
        $m             = date("m");
        $de            = date("d");
        $y             = date("Y");
        $format        = 'Y-m-d';
        $arrDate       = [];
        $arrDateFormat = [];

        for ($i = 0; $i <= 15 - 1; $i++) {
            $date = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));

            $arrDay[]        = date('D', mktime(0, 0, 0, $m, ($de - $i), $y));
            $arrDate[]       = $date;
            $arrDateFormat[] = date("d-M", strtotime($date));;
        }
        $data['day'] = $arrDateFormat;
        for ($i = 0; $i < count($arrDate); $i++) {
            $dayIncome = Revenue::selectRaw('sum(amount) amount')->where('created_by', \Auth::user()->creatorId())->whereRaw('date = ?', $arrDate[$i])->first();

            $invoices     = Invoice::select('*')->where('created_by', \Auth::user()->creatorId())->whereRAW('send_date = ?', $arrDate[$i])->get();
            $invoiceArray = array();
            foreach ($invoices as $invoice) {
                $invoiceArray[] = $invoice->getTotal();
            }

            $incomeArr['label']           = __('Income');
            $incomeArr['borderColor']     = '#6777ef';
            $incomeArr['fill']            = '!0';
            $incomeArr['backgroundColor'] = '#6777ef';
            $incomeArr['data'][]          = (!empty($dayIncome->amount) ? $dayIncome->amount : 0) + (!empty($invoiceArray) ? array_sum($invoiceArray) : 0);

            $dayExpense = Payment::selectRaw('sum(amount) amount')->where('created_by', \Auth::user()->creatorId())->whereRaw('date = ?', $arrDate[$i])->first();

            $bills     = Bill::select('*')->where('created_by', \Auth::user()->creatorId())->whereRAW('send_date = ?', $arrDate[$i])->get();
            $billArray = array();
            foreach ($bills as $bill) {
                $billArray[] = $bill->getTotal();
            }

            $expenseArr['label']           = __('Expense');
            $expenseArr['borderColor']     = '#fc544b';
            $expenseArr['fill']            = '!0';
            $expenseArr['backgroundColor'] = '#fc544b';
            $expenseArr['data'][]          = (!empty($dayExpense->amount) ? $dayExpense->amount : 0) + (!empty($billArray) ? array_sum($billArray) : 0);
        }
        $dataArr[]         = $incomeArr;
        $dataArr[]         = $expenseArr;
        $data['incExpArr'] = $dataArr;

        return $data;
    }

    public function totalCompanyUser($id)
    {
        return User::where('created_by', '=', $id)->count();
    }

    public function totalCompanyCustomer($id)
    {
        return Customer::where('created_by', '=', $id)->count();
    }

    public function totalCompanyVender($id)
    {
        return Vender::where('created_by', '=', $id)->count();
    }



    public function planPrice()
    {
        $user = \Auth::user();
        if ($user->type == 'super admin') {
            $userId = $user->id;
        } else {
            $userId = $user->created_by;
        }

        return DB::table('settings')->where('created_by', '=', $userId)->get()->pluck('value', 'name');
    }

    public function currentPlan()
    {
        return $this->hasOne('App\Plan', 'id', 'plan');
    }

    public function weeklyInvoice()
    {
        $staticstart  = date('Y-m-d', strtotime('last Week'));
        $currentDate  = date('Y-m-d');
        $invoices     = Invoice::with('sales')->where('status', 1)->whereNotNull('inventory_id')->where('branch_id', '=', \Auth::user()->branchId())->where('issue_date', '>=', $staticstart)->where('issue_date', '<=', $currentDate)->get();
        $invoiceTotal = 0;
        $invoicePaid  = 0;
        $invoiceDue   = 0;
        foreach ($invoices as $invoice) {
            $invoiceTotal += $invoice->sales->net_amount ?? 0;
            $invoicePaid  += ($invoice->sales->net_amount ?? 0) - ($invoice->sales->outstanding_amount  ?? 0);
            $invoiceDue   += $invoice->sales->outstanding_amount ?? 0;
        }

        $invoiceDetail['invoiceTotal'] = $invoiceTotal;
        $invoiceDetail['invoicePaid']  = $invoicePaid;
        $invoiceDetail['invoiceDue']   = $invoiceDue;

        return $invoiceDetail;
    }

    public function monthlyInvoice()
    {
        $staticstart  = date('Y-m-d', strtotime('last Month'));
        $currentDate  = date('Y-m-d');
        $invoices     = Invoice::with('sales')->where('status', 1)->whereNotNull('inventory_id')->where('branch_id', '=', \Auth::user()->branchId())->where('issue_date', '>=', $staticstart)->where('issue_date', '<=', $currentDate)->get();
        $invoiceTotal = 0;
        $invoicePaid  = 0;
        $invoiceDue   = 0;
        foreach ($invoices as $invoice) {
            $invoiceTotal += $invoice->sales->net_amount ?? 0;
            $invoicePaid  += ($invoice->sales->net_amount ?? 0) - ($invoice->sales->outstanding_amount  ?? 0);
            $invoiceDue   += $invoice->sales->outstanding_amount  ?? 0;
        }

        $invoiceDetail['invoiceTotal'] = $invoiceTotal;
        $invoiceDetail['invoicePaid']  = $invoicePaid;
        $invoiceDetail['invoiceDue']   = $invoiceDue;

        return $invoiceDetail;
    }

    public function weeklyBill()
    {
        $staticstart = date('Y-m-d', strtotime('last Week'));
        $currentDate = date('Y-m-d');
        $bills       = PaymentVoucher::select('*')->where('branch_id', \Auth::user()->branchId())->where('status', '!=', 0)->where('date', '>=', $staticstart)->where('date', '<=', $currentDate)->get();
        $billTotal   = 0;
        $billPaid    = 0;
        $billDue     = 0;
        foreach ($bills as $bill) {
            $billTotal += $bill->amount;
            if ($bill->status == 2) {
                $billPaid  += $bill->amount;
            }
            if ($bill->status == 1) {
                $billDue   += $bill->amount;
            }
        }

        $billDetail['billTotal'] = $billTotal;
        $billDetail['billPaid']  = $billPaid;
        $billDetail['billDue']   = $billDue;

        return $billDetail;
    }

    public function monthlyBill()
    {
        $staticstart = date('Y-m-d', strtotime('last Month'));
        $currentDate = date('Y-m-d');
        $bills       =  PaymentVoucher::select('*')->where('branch_id', \Auth::user()->branchId())->where('status', '!=', 0)->where('date', '>=', $staticstart)->where('date', '<=', $currentDate)->get();
        $billTotal   = 0;
        $billPaid    = 0;
        $billDue     = 0;
        foreach ($bills as $bill) {
            $billTotal += $bill->amount;
            if ($bill->status == 2) {
                $billPaid  += $bill->amount;
            }
            if ($bill->status == 1) {
                $billDue   += $bill->amount;
            }
        }

        $billDetail['billTotal'] = $billTotal;
        $billDetail['billPaid']  = $billPaid;
        $billDetail['billDue']   = $billDue;

        return $billDetail;
    }
}
