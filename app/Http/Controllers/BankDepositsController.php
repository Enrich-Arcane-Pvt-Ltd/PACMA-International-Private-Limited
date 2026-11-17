<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\BankDeposit;
use App\DailyOpeningBalance;
use App\FinancialEntry;
use App\Setting;
use Illuminate\Http\Request;

class BankDepositsController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index()
    {
        if (\Auth::user()->can('view cash book')) {
            $bankDeposits = BankDeposit::with('user')->where('branch_id', \Auth::user()->branchId())->get();

            if (count($bankDeposits) > 0) {
                $showTable = 'Yes';
            } else {
                $showTable = 'No';
            }
            $currencySymbol = $this->currencySymbol;
            return view('bank_deposits.index', compact('showTable', 'bankDeposits', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function search(Request $request)
    {
        if (\Auth::user()->can('view cash book')) {
            $showTable = 'Yes';

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $bankDeposits = BankDeposit::with('user')
                ->where('branch_id', \Auth::user()->branchId())
                ->whereBetween('deposited_date', [$startDate, $endDate])
                ->get();
            $currencySymbol = $this->currencySymbol;
            return view('bank_deposits.index', compact('showTable', 'startDate', 'endDate', 'bankDeposits', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        $bank = BankAccount::where('status', 1)->first();

        $bankDeposit = new BankDeposit();
        $bankDeposit->deposited_date = $request->deposited_date;
        $bankDeposit->deposited_time = $request->deposited_time;
        $bankDeposit->deposited_bank_id = $bank->id;
        $bankDeposit->reference_no = $request->reference_no;
        $bankDeposit->deposited_person_id = $request->deposited_person_id;
        $bankDeposit->deposited_remarks = $request->deposited_remarks;
        $bankDeposit->deposited_amount = $request->deposited_amount;
        $bankDeposit->branch_id = \Auth::user()->branchId();
        $bankDeposit->created_by = \Auth::user()->id;
        $bankDeposit->save();

        $relatedOpeningBalance = DailyOpeningBalance::where('status', 1)
            ->where('branch_id', \Auth::user()->branchId())
            ->first();

        if ($relatedOpeningBalance) {
            $relatedOpeningBalance->related_bank_beposit_id = $bankDeposit->id;
            $relatedOpeningBalance->status = 0;
            $relatedOpeningBalance->update();
        }

        $openingBalance = $request->total_cash_in_hand - $request->deposited_amount;

        $dailyOpeningBalance = new DailyOpeningBalance();
        $dailyOpeningBalance->date = $request->deposited_date;
        $dailyOpeningBalance->opening_balance = $openingBalance;
        $dailyOpeningBalance->bank_beposit_id = $bankDeposit->id;
        $dailyOpeningBalance->branch_id = \Auth::user()->branchId();
        $dailyOpeningBalance->created_by = \Auth::user()->id;
        $dailyOpeningBalance->save();

        $financialEntries = FinancialEntry::where('branch_id', \Auth::user()->branchId())
            ->where('status', 0)
            ->get();

        foreach ($financialEntries as $financialEntry) {
            $financialEntry->bank_deposit_id = $bankDeposit->id;
            $financialEntry->deposited_date = $request->deposited_date;
            $financialEntry->status = 1;
            $financialEntry->update();
        }

        return redirect()->back()->with('success', __('Bank Deposit successfully added.'));
    }

    public function details(Request $request)
    {
        $bankDepositId = $request->bank_deposit_id;

        $allAvailableFinancialEntries = FinancialEntry::with([
            'receipt:id,receipt_code',
            'customReceipt:id,receipt_code',
            'customer:id,name',
            'vendor:id,name',
            'product:id,barcode',
            'invoice:id,invoice_code'
        ])
            ->where('bank_deposit_id', $bankDepositId)
            ->get()
            ->map(function ($entry) {
                $entry->receipt_number = $entry->receipt->receipt_code
                    ?? $entry->customReceipt->receipt_code;
                $entry->customer_name = $entry->customer->name ?? ($entry->vendor->name ?? '-');
                $entry->product_name = $entry->product->barcode ??   '-';
                $entry->invoice_number = $entry->invoice->invoice_code ??   '-';

                return $entry;
            });

        $records = $allAvailableFinancialEntries;

        $openingBalance = DailyOpeningBalance::where('branch_id', \Auth::user()->branch_id)
            ->where('related_bank_beposit_id', $bankDepositId)
            ->first();


        return response()->json([
            'records' => $records,
            'openingBalance' => $openingBalance
        ]);
    }
}
