<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Customer extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    protected $guard_name = 'web';

    protected $fillable = [
        'title',
        'name',
        'address',
        'phone',
        'phone_mobile',
        'email',
        'nic_no',
        'billing_title',
        'billing_name',
        'billing_address',
        'billing_phone',
        'billing_email',
        'type',
        'status',
    ];

    public static $title = [
        'Mr.',
        'Mrs.',
        'Miss.',
    ];

    public static function getTitle($id)
    {
        return self::$title[$id];
    }

    public static $gender = [
        'Male',
        'Female'
    ];

    public static function getGender($id)
    {
        return self::$gender[$id];
    }

    public static $marrage = [
        'Married',
        'Single'
    ];

    public static function getMarrage($id)
    {
        return self::$marrage[$id];
    }

    public static $income_period = [
        'Daily',
        'Weekly',
        'Monthly'
    ];

    public static function getIncomePeriod($id)
    {
        return self::$income_period[$id];
    }

    public static $employment_feild = [
        'Government',
        'Private',
        'Business',
        'Self-employment',
        'Expecting',
    ];

    public static function getEmploymentFeild($id)
    {
        return self::$employment_feild[$id];
    }

    public static $boolSelect = [
        'No',
        'Yes'
    ];

    public static $bankAccountType = ['Savings', 'Mobile'];

    public static function getBankAccountType($id)
    {
        return self::$bankAccountType[$id];
    }





    protected $hidden = [
        'remember_token',
    ];


    public $settings;

    public function details()
    {
        return $this->hasOne('App\CustomerDetail', 'customer_id', 'id');
    }

    public function authId()
    {
        return $this->id;
    }

    public function creatorId()
    {
        if($this->type == 'company' || $this->type == 'super admin')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }

    public function currentLanguage()
    {
        return $this->lang;
    }

    public function priceFormat($price)
    {
        $settings = Utility::settings();
        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, Utility::getValByName('decimal_number')) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
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

    public function invoiceChartData()
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

        $data['currentYear'] = date('M-Y');

        $totalInvoice = Invoice::where('customer_id', \Auth::user()->id)->count();
        $unpaidArr    = array();

        $unpaid['label']           = 'Unpaid';
        $unpaid['fill']            = '!0';
        $unpaid['backgroundColor'] = '#fc544b';
        $unpaid['borderColor']     = '#fc544b';

        $paid['label']           = 'Paid';
        $paid['fill']            = '!0';
        $paid['backgroundColor'] = '#63ed7a';
        $paid['borderColor']     = '#63ed7a';

        $partial['label']           = 'Partial';
        $partial['fill']            = '!0';
        $partial['backgroundColor'] = '#6777ef';
        $partial['borderColor']     = '#6777ef';

        $due['label']           = 'Due';
        $due['fill']            = '!0';
        $due['backgroundColor'] = '#ffa426';
        $due['borderColor']     = '#ffa426';


        for($i = 1; $i <= 12; $i++)
        {
            $unpaidInvoice  = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->whereRaw('month(`send_date`) = ?', $i)->where('status', '1')->where('due_date', '>', date('Y-m-d'))->get();
            $paidInvoice    = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->whereRaw('month(`send_date`) = ?', $i)->where('status', '4')->get();
            $partialInvoice = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->whereRaw('month(`send_date`) = ?', $i)->where('status', '3')->get();
            $dueInvoice     = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->whereRaw('month(`send_date`) = ?', $i)->where('status', '1')->where('due_date', '<', date('Y-m-d'))->get();


            $totalUnpaid = 0;
            for($j = 0; $j < count($unpaidInvoice); $j++)
            {
                $unpaidAmount = $unpaidInvoice[$j]->getDue();
                $totalUnpaid  += $unpaidAmount;

            }

            $totalPaid = 0;
            for($j = 0; $j < count($paidInvoice); $j++)
            {
                $paidAmount = $paidInvoice[$j]->getTotal();
                $totalPaid  += $paidAmount;

            }

            $totalPartial = 0;
            for($j = 0; $j < count($partialInvoice); $j++)
            {
                $partialAmount = $partialInvoice[$j]->getDue();
                $totalPartial  += $partialAmount;

            }

            $totalDue = 0;
            for($j = 0; $j < count($dueInvoice); $j++)
            {
                $dueAmount = $dueInvoice[$j]->getDue();
                $totalDue  += $dueAmount;

            }

            $unpaidData[]    = $totalUnpaid;
            $paidData[]      = $totalPaid;
            $partialData[]   = $totalPartial;
            $dueData[]       = $totalDue;
            $unpaid['data']  = $unpaidData;
            $paid['data']    = $paidData;
            $partial['data'] = $partialData;
            $due['data']     = $dueData;
        }

        $dataStatus[]       = $unpaid;
        $dataStatus[]       = $paid;
        $dataStatus[]       = $partial;
        $dataStatus[]       = $due;
        $data['statusData'] = $dataStatus;


        $unpaidInvoice  = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->where('status', '1')->where('due_date', '>', date('Y-m-d'))->get();
        $paidInvoice    = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->where('status', '4')->get();
        $partialInvoice = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->where('status', '3')->get();
        $dueInvoice     = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->where('status', '1')->where('due_date', '<', date('Y-m-d'))->get();

        $progressData['totalInvoice']        = $totalInvoice = Invoice:: where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->count();
        $progressData['totalUnpaidInvoice']  = $totalUnpaidInvoice = count($unpaidInvoice);
        $progressData['totalPaidInvoice']    = $totalPaidInvoice = count($paidInvoice);
        $progressData['totalPartialInvoice'] = $totalPartialInvoice = count($partialInvoice);
        $progressData['totalDueInvoice']     = $totalDueInvoice = count($dueInvoice);

        $progressData['unpaidPr']  = ($totalInvoice != 0) ? ($totalUnpaidInvoice * 100) / $totalInvoice : 0;
        $progressData['paidPr']    = ($totalInvoice != 0) ? ($totalPaidInvoice * 100) / $totalInvoice : 0;
        $progressData['partialPr'] = ($totalInvoice != 0) ? ($totalPartialInvoice * 100) / $totalInvoice : 0;
        $progressData['duePr']     = ($totalInvoice != 0) ? ($totalDueInvoice * 100) / $totalInvoice : 0;

        $progressData['unpaidColor']  = '#fc544b';
        $progressData['paidColor']    = '#63ed7a';
        $progressData['partialColor'] = '#6777ef';
        $progressData['dueColor']     = '#ffa426';

        $data['progressData'] = $progressData;
        return $data;
    }


    public function customerInvoice($customerId)
    {
        $invoices  = Invoice:: where('customer_id', $customerId)->orderBy('issue_date', 'desc')->get();
        $proposals = Proposal:: where('customer_id', $customerId)->orderBy('issue_date', 'desc')->get()->toArray();
        return $invoices;
    }

    public function customerProposal($customerId)
    {
        $proposals = Proposal:: where('customer_id', $customerId)->orderBy('issue_date', 'desc')->get();
        return $proposals;
    }

    public function customerOverdue($customerId)
    {
        $dueInvoices = Invoice:: where('customer_id', $customerId)->whereNotIn(
            'status', [
                        '0',
                        '4',
                    ]
        )->where('due_date', '<', date('Y-m-d'))->get();
        $due         = 0;
        foreach($dueInvoices as $invoice)
        {
            $due += $invoice->getDue();
        }

        return $due;
    }

    public function customerTotalInvoiceSum($customerId)
    {
        $invoices = Invoice:: where('customer_id', $customerId)->get();
        $total    = 0;
        foreach($invoices as $invoice)
        {
            $total += $invoice->getTotal();
        }

        return $total;
    }

    public function customerTotalInvoice($customerId)
    {
        $invoices = Invoice:: where('customer_id', $customerId)->count();
        return $invoices;
    }

    public function loans(){
        return $this->hasOne('App\Loan','customer_id','id');
    }

}
