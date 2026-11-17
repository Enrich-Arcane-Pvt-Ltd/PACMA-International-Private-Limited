<?php

namespace App\Http\Controllers;

use App\Pawning;
use App\TimePeriod;
use App\Customer;
use App\PawningJewellery;
use App\GoldRate;
use App\InterestRate;
use App\PawnedItem;
use App\PawningInstallment;
use App\PawningReceipt;
use App\User;
use App\Utility;
use App\CardInterestExpense;
use App\BankAccount;
use App\GeneralLedgerAccount;
use App\WaittingPawningInstallment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Auth;
use Illuminate\Http\Request;

class PawningController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage pawning'))
        {
            $pawnings = Pawning::with('customer')
                                ->where('branch_id', '=', \Auth::user()->branchId())
                                ->whereNotIn('status', [0])
                                ->orderBy('id', "DESC")
                                ->get();
            return view('pawning.index', compact('pawnings'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function view($id){
        if(\Auth::user()->can('view pawning'))
        {
            $pawningDetails = Pawning::with('customer')
                                        ->where('id',$id)->first();
            $pawnedItemDetails = PawnedItem::with('jewellery','goldType')
                                            ->where('pawning_id',$id)
                                            ->where('status', 1)
                                            ->get();

            // $pItms = Pawning::with('customer', 'pawnedItem.jewellery', 'pawnedItem.goldType', 'installments','receipts')->where('id',$id)->first();
            // $installments = PawningInstallment::with('pawnings')
            //                                         ->where('pawning_id',$id)
            //                                         ->get();
            // $endDate = Carbon::today()->subDays(5);
            // $installments = PawningInstallment::whereDate('installment_date', '<', $endDate->format('Y-m-d'))
            //                     ->where('status', 0)
            //                     ->where('branch_id', \Auth::user()->branchId())
            //                     ->get();

            // if($installments != null){
            //     foreach ($installments as $installment) {
            //         $insDetail = PawningInstallment::find($installment->id);
            //         $totAmountToPay = $insDetail->installment_amount - $insDetail->released_amount;
            //         $insDetail->status = 2;
            //         $insDetail->total_amount_to_pay = $totAmountToPay;
            //         $insDetail->update();
            //     }
            // }

            $pawnings = Pawning::with([ 'lastInstallment', 'installments'=>function($query1){
                                                                    $query1->whereNotIn('status', [9]);
                                                                },
                                        'receipts'=>function($query2){
                                                                $query2->where('status', 1);
                                                            }
                                        ])->where('id', $id)->first();

            $created = Pawning::select('users.id','users.name')
                ->leftJoin('users', 'users.id', 'pawnings.created_by')
                ->where('pawnings.id', $id)->first();

            $pawningOfficer = Pawning::select('users.name')
                                ->join('users', 'users.id', 'pawnings.officer_id')
                                ->where('pawnings.id', $id)->first();

            $introducer = Pawning::select('users.name')
                                ->join('users', 'users.id', 'pawnings.introducer_id')
                                ->where('pawnings.id', $id)->first();
            return view('pawning.view', compact('pawningDetails','pawnedItemDetails','pawnings','created', 'pawningOfficer', 'introducer'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function showPawningTicket($id)
    {
        if(\Auth::user()->can('view pawning')) {
            $pawningTicket = Pawning::find($id);

            $pawningTicketDetails = Pawning::with('customer')->where('id',$id)->first();

            $pawningItemDetails = PawnedItem::with('jewellery','goldType')
                                            ->where('pawning_id',$id)
                                            ->get();
            // $product = Product::find($id);

            $settings = Utility::settings();

            $preview      = 1;
            $logo         = asset(Storage::url('uploads/logo/'));
            $company_logo = Utility::getValByName('company_logo');
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

            // $pawning = Pawning::where('created_by', '=', \Auth::user()->creatorId())->get();
            // return view('pawning.pawning_ticket', compact('product'));
            return view('pawning.ticket', compact('img', 'settings','pawningTicket','pawningTicketDetails','pawningItemDetails', 'id'));
        }

        else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create pawning'))
        {
            $customers = Customer::whereIn('type',[0,2])->where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $timePeriods = TimePeriod::where('status', 1)->get();
            $pawningJewelleries = PawningJewellery::where('status', 1)->get();
            $goldRates = GoldRate::where('status', 1)->get()->pluck('gold_rate_type', 'id');
            $goldRates->prepend('Select Gold Rate Type', '');
            $goldInterest = InterestRate::where('id', 4)->first();

            $users = User::where('is_active', 1)->get()->pluck('name', 'id');
            // $users->prepend('-- Officer --', '');

            return view('pawning.create' , compact('customers', 'timePeriods', 'pawningJewelleries', 'goldRates', 'goldInterest', 'users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create pawning'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'customer_id' => 'required',
                                   'issue_date' => 'required',
                                   'officer_id' => 'required',
                                   'time_range' => 'required',
                                   'time_period_id' => 'required',
                                   'gold_interest' => 'required',
                                   'items' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            // $status = Pawning::$statues;

            $pawning                        = new Pawning();
            $pawning->pawning_id            = $this->pawningNumber();
            $pawning->customer_id           = $request->customer_id;
            $pawning->branch_id             = \Auth::user()->branchId();
            $pawning->officer_id            = $request->officer_id;
            $pawning->introducer_id         = $request->introducer_id;
            $pawning->time_range            = $request->time_range;
            $pawning->time_period_id        = $request->time_period_id;
            $pawning->issue_date            = $request->issue_date;
            $pawning->due_date              = $request->due_date;
            $pawning->installment_date      = $request->installment_date;
            $pawning->full_amount_financed  = $request->totalAmountFinanced;
            $pawning->full_amount_payout    = $request->totalAmountPayOut;
            $pawning->amount_payout_in_words    = $request->amount_payout_in_words;
            $pawning->interest_rate         = $request->gold_interest;
            $pawning->full_financed_charge  = $request->totalFinanceCharge;
            $pawning->full_total_of_payment = $request->fullTotalOfPayments;
            $pawning->full_prepayment       = $request->fullPrepayment;
            $pawning->current_due_amount    = $request->fullTotalOfPayments;
            $pawning->is_extended           = 0;
            $pawning->status                = 1;
            $pawning->created_by            = \Auth::user()->id;
            $pawning->save();
            // CustomField::saveData($pawning, $request->customField);
            $pawningItems = $request->items;

            $timeRange = $request->time_range;
            $dateofPawn = explode('-',$request->issue_date);
            $pawn_yr = $dateofPawn[0];
            // $pawn_mn = $dateofPawn[1];
            if($timeRange == 2){
                if($dateofPawn[2] > $request->installment_date){
                    $pawn_mn = $dateofPawn[1] + 1;
                } else {
                    $pawn_mn = $dateofPawn[1];
                }
            } else if($timeRange == 1){
                if($dateofPawn[2] > $request->installment_date){
                    $pawn_mn = $dateofPawn[1] + 1;
                } else {
                    $pawn_mn = $dateofPawn[1];
                }
            } else {
                $pawn_mn = $dateofPawn[1];
            }
            $pawn_day = $request->installment_date;

            $installment_date = Carbon::create($pawn_yr, $pawn_mn, $pawn_day);

            // // die(var_dump($request->items));

            // for($i = 0; $i < count($pawningItems); $i++)
            foreach( $request->items as $pawningItems)
            {
                $pawnedItem                   = new PawnedItem();
                $pawnedItem->pawning_id       = $pawning->id;
                $pawnedItem->jewellery_id     = $pawningItems['jewellery_id'];
                $pawnedItem->gold_rate_type   = $pawningItems['gold_rate_type'];
                $pawnedItem->payment_rate_value   = $pawningItems['payment_rate_value'];
                $pawnedItem->weight           = $pawningItems['weight'];
                $pawnedItem->amount_financed  = $pawningItems['amountFinanced'];
                $pawnedItem->amount_payout    = $pawningItems['amountPayout'];
                $pawnedItem->financed_charge  = $pawningItems['financeCharge'];
                $pawnedItem->total_of_payment = $pawningItems['totalOfPayment'];
                $pawnedItem->prepayment       = $pawningItems['prepayment'];
                $pawnedItem->status           = 1;
                $pawnedItem->created_by       = \Auth::user()->id;
                $pawnedItem->save();
            }

            if($timeRange == 3){
                $newInstallmentDate = $installment_date->addMonth(1);
                for ($i=0; $i < $request->time_period_id; $i++)
                {
                    $installment                        = new PawningInstallment();
                    $installment->pawning_id            = $pawning->id;
                    $installment->installment_date      = $newInstallmentDate->format('Y-m-d');
                    $installment->installment_amount    = $request->fullPrepayment;
                    $installment->total_amount_to_pay    = $request->fullPrepayment;
                    if($i == 0){
                        $installment->total_due_amount =$request->fullTotalOfPayments;
                    }
                    $installment->status                = 0;
                    $installment->time_range            = $timeRange;
                    $installment->sent_reminder         = 0;
                    $installment->branch_id             = \Auth::user()->branchId();
                    $installment->created_by            = \Auth::user()->id;
                    $installment->save();

                    $newInstallmentDate = $newInstallmentDate->addMonth(1);
                }
            } elseif($timeRange == 2){
                // $newInstallmentDate = $installment_date->addMonth(1);
                $totWeeks = $request->time_period_id*4;
                $newInstallmentDate = $installment_date;
                for ($i=0; $i < $totWeeks; $i++)
                {
                    $installment                        = new PawningInstallment();
                    $installment->pawning_id            = $pawning->id;
                    $installment->installment_date      = $newInstallmentDate->format('Y-m-d');
                    $installment->installment_amount    = $request->fullPrepayment;
                    $installment->total_amount_to_pay    = $request->fullPrepayment;
                    if($i == 0){
                        $installment->total_due_amount =$request->fullTotalOfPayments;
                    }
                    $installment->status                = 0;
                    $installment->time_range            = $timeRange;
                    $installment->sent_reminder         = 0;
                    $installment->branch_id             = \Auth::user()->branchId();
                    $installment->created_by            = \Auth::user()->id;
                    $installment->save();

                    $newInstallmentDate = $newInstallmentDate->addDay(7);
                }
            } elseif($timeRange == 1){
                // $newInstallmentDate = $installment_date->addMonth(1);
                $totDays = $request->time_period_id*30;
                $newInstallmentDate = $installment_date;
                for ($i=0; $i < $totDays; $i++)
                {
                    $installment                        = new PawningInstallment();
                    $installment->pawning_id            = $pawning->id;
                    $installment->installment_date      = $newInstallmentDate->format('Y-m-d');
                    $installment->installment_amount    = $request->fullPrepayment;
                    $installment->total_amount_to_pay    = $request->fullPrepayment;
                    if($i == 0){
                        $installment->total_due_amount =$request->fullTotalOfPayments;
                    }
                    $installment->status                = 0;
                    $installment->time_range            = $timeRange;
                    $installment->sent_reminder         = 0;
                    $installment->branch_id             = \Auth::user()->branchId();
                    $installment->created_by            = \Auth::user()->id;
                    $installment->save();

                    $newInstallmentDate = $newInstallmentDate->addDay(1);
                }
            }

            return redirect()->route('pawning.index')->with('success', __('Pawning successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payInstallment($id){
        if(\Auth::user()->can('pay pawning'))
        {
            $range = 1;
            $installments = PawningInstallment::where('id', $id)->first();
            if($installments->time_range == 3){
                $nextInstallmentDate = Carbon::parse($installments->installment_date)->addMonth(1)->format('Y-m-d');
                $range = 1;
            } else if($installments->time_range == 2){
                $nextInstallmentDate = Carbon::parse($installments->installment_date)->addDay(7)->format('Y-m-d');
                $range = 4;
            } else if($installments->time_range == 1){
                $nextInstallmentDate = Carbon::parse($installments->installment_date)->addDay(1)->format('Y-m-d');
                $range = 30;
            }
            $nextInstallment = PawningInstallment::where('installment_date', $nextInstallmentDate)->where('pawning_id', $installments->pawning_id)->whereNotIn('status', [9])->first();
            $dueInstallments = PawningInstallment::where('pawning_id', $installments->pawning_id)->whereIn('status', [0,2])->get();
            $pawnings = Pawning::where('id', $installments->pawning_id)->first();

            $instCount = $pawnings->time_period_id * $range;
            $data['amountForInst'] = number_format((float)$pawnings->full_amount_payout / $instCount, 2, '.', '');
            $data['interestForInst'] = number_format((float)$pawnings->full_financed_charge / $instCount, 2, '.', '');
            $data['insts'] = $instCount;
            $data['dueInsts'] = count($dueInstallments) - 1;

            if($installments->new_installment == 1){
                $num1 = $pawnings->full_amount_payout;      // Capital amount
                $num2 = $pawnings->full_financed_charge;        // Intesrest amount
                $paid = $pawnings->current_due_amount;     // Current Due amount
                for($i = $num2; $i > 1; $i--) {
                    if(($num1 % $i) == 0 && ($num2 % $i) == 0) {
                        $num1 = $num1 / $i;
                        $num2 = $num2 / $i;
                    }
                }
                $numTot = $num1 + $num2;
                $principal = round(( $paid / $numTot ) * $num1, 2);
                $interest = $paid - $principal;
                $data['amountForInst'] = $principal;
                $data['interestForInst'] = $interest;
            }

            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');

            return view('pawning.pay_installment', compact('installments','pawnings', 'nextInstallment', 'data', 'banks'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function installmentPayment(Request $request) {


        if(\Auth::user()->can('pay pawning'))
        {
            $userId = \Auth::user()->id;
            $installmentPayment = PawningInstallment::find($request->installment_id);

                $validator = \Validator::make(
                    $request->all(), [
                                        'pawning_id' => 'required',
                                        'installment_id' => 'required',
                                        'paid_date' => 'required',
                                        'installment_date' => 'required',
                                        'customer_id' => 'required',
                                        'installment_amount' => 'required',
                                        'paid_amount' => 'required',
                                        'payment_method' => 'required',
                                        'paid_amount_in_words' => 'required',
                                     ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                if(isset($request->waitting_pawning_installment_id)){
                    $checkForinstallments = PawningInstallment::find($request->installment_id);
                    if($checkForinstallments->status == 1){
                        return redirect()->back()->with('error', 'The Installment Payment has been Already Settled!');
                    }
                    $userId = $request->created_by;
                    $waittingPawningInstallment = WaittingPawningInstallment::find($request->waitting_pawning_installment_id);
                    $waittingPawningInstallment->status = 2;
                    $waittingPawningInstallment->approved_rejected_by = \Auth::user()->id;
                    $waittingPawningInstallment->update();
                }

                $paidAmount = $request->paid_amount;
                $totalAmountToPay = $request->total_amount_tobe_paid;
                $extraPaid = 0;
                $remainder = 0;
                $current_due_amount = 0;

                $pawning = Pawning::find($installmentPayment->pawning_id);
                if($pawning->current_due_amount != null){
                    $current_due_amount = $pawning->current_due_amount;
                }
                if($current_due_amount < $paidAmount){
                    $newDueAmount = 0;
                } else {
                    $newDueAmount = $current_due_amount - $paidAmount;
                }

                    if($paidAmount >= $totalAmountToPay){
                        $extraPaid = $paidAmount - $totalAmountToPay;
                    } elseif ($paidAmount < $totalAmountToPay){
                        // $chInsDate = Carbon::parse($installmentPayment->installment_date)->addMonth(1)->format('Y-m-d');
                        // $nextchInsDate = PawningInstallment::where('installment_date', $chInsDate)->where('pawning_id', $request->pawning_id)->first();
                        // if($nextchInsDate){
                            $remainder = $totalAmountToPay - $paidAmount;
                        // } else {
                        //     return redirect()->back()->with('error', 'Insufficient Paid Amount');
                        // }
                    }

                $maxId = PawningReceipt::max('id');
                $receiptCode = "GIR".str_pad(($maxId+1), 6, "0", STR_PAD_LEFT);

                if($request->payment_method == 'CARD'){
                    if($request->card_charge_amount != ''){
                        $cardCharge = new CardInterestExpense();
                        $cardCharge->amount = $request->card_charge_amount;
                        $cardCharge->pawning_id = $installmentPayment->pawning_id;
                        $cardCharge->save();
                    }
                }

                $receipts = new PawningReceipt();

                // dump($request->card_type);
                // dd($receipts->card_type);
                $receipts->branch_id = \Auth::user()->branchId();
                $receipts->receipt_code = $receiptCode;
                $receipts->customer_id = $request->customer_id;
                $receipts->pawning_id = $request->pawning_id;
                $receipts->issue_date = $request->paid_date;
                $receipts->installment_due_date = $request->installment_date;
                $receipts->status = 1;
                $receipts->installment_amount = $request->installment_amount;
                $receipts->total_amount_to_pay = $request->total_amount_tobe_paid;
                $receipts->paid_amount = $request->paid_amount;

                if(isset($request->pay_total)){
                    $receipts->balance_amount = 0;
                } else {
                    if($newDueAmount < 0){
                        $receipts->balance_amount = 0;
                    } else {
                        $receipts->balance_amount = $newDueAmount;
                    }
                }

                $receipts->payment_method = $request->payment_method;
                $receipts->paid_amount_in_words = $request->paid_amount_in_words;

                if($request->payment_method == 'CARD'){
                    $receipts->card_type            = $request->card_type;
                    $receipts->card_charge_amount   = $request->card_charge_amount;
                    $receipts->total_amount         = $request->total_amount;
                } elseif($request->payment_method == 'CHEQUE') {
                    $receipts->cheque_number        = $request->cheque_number;
                }

                $receipts->description = $request->description;
                $receipts->created_by = $userId;
                $receipts->save();

                $receiptId = PawningReceipt::max('id');
                $installmentPayment->paid_date            = $request->paid_date;
                $installmentPayment->paid_amount          = $request->paid_amount;
                $installmentPayment->receipt_id          = $receipts->id;
                if($extraPaid != 0){
                    $installmentPayment->extra_paid_amount = $extraPaid;
                }
                if($remainder != 0){
                    $installmentPayment->remainder_of_paid_amount = $remainder;
                }
                $installmentPayment->payment_method = $request->payment_method;
                $installmentPayment->paid_amount_in_words = $request->paid_amount_in_words;
                $installmentPayment->bank_id = $request->bank;

                if($request->payment_method == 'CARD'){
                    $installmentPayment->card_type            = $request->card_type;
                    $installmentPayment->card_expence_id      = $cardCharge->id;
                    $installmentPayment->card_charge_amount   = $request->card_charge_amount;
                    $installmentPayment->total_amount         = $request->total_amount;
                } elseif($request->payment_method == 'CHEQUE') {
                    $installmentPayment->cheque_number        = $request->cheque_number;
                    $installmentPayment->cheque_realised_date        = $request->realised_date;
                }

                if(isset($request->pay_total)){
                    $installmentPayment->total_due_amount = 0;
                    $installmentPayment->totaly_paid = 1;
                } else {
                    $installmentPayment->total_due_amount = $newDueAmount;
                }

                $installmentPayment->status               = 1;
                $installmentPayment->update();

                // Getting ratio & productDetail variable ============== start ========================>
                $outstanding = $pawning->full_amount_payout;
                $interestAmount = $pawning->full_financed_charge;
                $num1 = $outstanding;
                $num2 = $interestAmount;
                for($i = $num2; $i > 1; $i--) {
                    if(($num1 % $i) == 0 && ($num2 % $i) == 0) {
                        $num1 = $num1 / $i;
                        $num2 = $num2 / $i;
                    }
                }
                $numTot = $num1 + $num2;

                $principal = $request->this_loan_value;
                $interest = $request->this_interest_value;
                $excess = 0;
                // Getting ratio & productDetail variable ============== end ========================>

                if(!isset($request->pay_total)){
                    if($extraPaid > 0){
                        $installmentDate = $installmentPayment->installment_date;
                        $installmentTimeRange = $installmentPayment->time_range;
                        while ($extraPaid > 0){
                            if($installmentTimeRange == 3){
                                $nextInstallmentDate = Carbon::parse($installmentDate)->addMonth(1);
                            } else if($installmentTimeRange == 2){
                                $nextInstallmentDate = Carbon::parse($installmentDate)->addDay(7);
                            } else if($installmentTimeRange == 1){
                                $nextInstallmentDate = Carbon::parse($installmentDate)->addDay(1);
                            }
                            // $nextInstallmentDate = Carbon::parse($installmentDate)->addMonth(1);
                            $nextInstallment = PawningInstallment::where('installment_date', $nextInstallmentDate->format('Y-m-d'))->where('pawning_id', $request->pawning_id)->whereNotIn('status', [9])->first();
                            if($nextInstallment != null){
                                $nextTotAmountToPay = $nextInstallment->installment_amount - $extraPaid;

                                if($nextTotAmountToPay > 0)
                                {
                                    $nextInstallment->total_amount_to_pay = $nextTotAmountToPay;
                                    $nextInstallment->released_amount = $extraPaid;
                                    $nextInstallment->update();
                                    $excess = $extraPaid;
                                    $extraPaid = 0;
                                }
                                elseif($nextTotAmountToPay == 0)
                                {
                                    $nextInstallment->status = 1;
                                    $nextInstallment->total_amount_to_pay = $nextTotAmountToPay;
                                    $nextInstallment->released_amount = $extraPaid;
                                    $nextInstallment->update();
                                    $excess = $extraPaid;
                                    $extraPaid = 0;

                                    $principal += $request->this_loan_value;
                                    $interest += $request->this_interest_value;
                                }
                                elseif ($nextTotAmountToPay < 0)
                                {
                                    $nextInstallment->status = 1;
                                    $nextInstallment->total_amount_to_pay = 0;
                                    $nextInstallment->released_amount = $nextInstallment->installment_amount;
                                    $nextInstallment->update();

                                    $extraPaid = $extraPaid - $nextInstallment->installment_amount;
                                    $installmentDate = $nextInstallmentDate;

                                    $principal += $request->this_loan_value;
                                    $interest += $request->this_interest_value;
                                }
                            } else {
                                $extraPaid = 0;
                            }
                        }
                        $excess = $request->paid_amount - ($principal + $interest);
                    }

                    if($remainder > 0){
                        if($principal >= $remainder){
                            $principal -= $remainder;
                        } else if($principal < $remainder){
                            // $diff = $remainder - $principal;
                            // $interest -= $diff;
                            // $principal = 0;
                            if($paidAmount >= $interest){
                                $principal = $paidAmount - $interest;
                            } else {
                                $interest = $paidAmount;
                                $principal = 0;
                            }
                        }
                        $remainderInstallmentDate = $installmentPayment->installment_date;
                        $remainderInstallmentTimeRange = $installmentPayment->time_range;
                        while ($remainder > 0){
                            if($remainderInstallmentTimeRange == 3){
                                $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addMonth(1);
                            } else if($remainderInstallmentTimeRange == 2){
                                $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addDay(7);
                            } else if($remainderInstallmentTimeRange == 1){
                                $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addDay(1);
                            }
                            // $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addMonth(1)->format('Y-m-d');
                            $nextInstallment = PawningInstallment::where('installment_date', $nextInstallmentDate->format('Y-m-d'))->where('pawning_id', $request->pawning_id)->whereNotIn('status', [9])->first();
                            if($nextInstallment != null){
                                $nextTotAmountToPay = $nextInstallment->installment_amount + $remainder;

                                $nextInstallment->total_amount_to_pay = $nextTotAmountToPay;
                                $nextInstallment->remainder_amount = $remainder;
                                $nextInstallment->update();
                                $remainder = 0;
                            } elseif($nextInstallment == null){
                                $newInstallmentDate = $nextInstallmentDate;
                                    $installment = new PawningInstallment();
                                    $installment->pawning_id = $request->pawning_id;
                                    $installment->installment_amount = $remainder;
                                    $installment->total_amount_to_pay = $remainder;
                                    $installment->status = 0;
                                    $installment->new_installment = 1;
                                    $installment->installment_date = $newInstallmentDate->format('Y-m-d');
                                    $installment->time_range = $remainderInstallmentTimeRange;
                                    $installment->sent_reminder         = 0;
                                    $installment->branch_id             = \Auth::user()->branchId();
                                    $installment->created_by            = $userId;
                                    $installment->save();

                                    $remainder = 0;
                            }
                        }
                    }

                    if($paidAmount >= $current_due_amount){
                        $pawning->current_due_amount = 0;
                        $pawning->status = 4;

                        $allInstallments = PawningInstallment::where('pawning_id', $request->pawning_id)->whereNotIn('status', [1,9])->get();
                        foreach($allInstallments as $allInstallment){
                            $allInstallment->status = 1;
                            $allInstallment->total_amount_to_pay = 0;
                            $allInstallment->total_due_amount = 0;
                            $allInstallment->update();
                        }
                    } else {
                        $pawning->current_due_amount = $newDueAmount;
                        $pawning->status = 2;
                    }

                    // Getting Principle & interest amounts ============ start =========================>
                    // $principal = ( $paidAmount / $numTot ) * $num1;
                    // $interest = $paidAmount - $principal;

                } elseif(isset($request->pay_total)){

                    // Getting Principle & interest amounts ============ start =========================>
                    $thisAmount = $request->installment_amount - $request->released_amount;
                    $thisValue = ( $thisAmount / $numTot ) * $num1;
                    $thisInterest = $thisAmount - $thisValue;

                    $dueLoanValue = $request->due_loan_value;
                    $dueInterest = $request->due_interest_value;

                    // $principal = $thisValue + $dueLoanValue;
                    // $interest = $thisInterest + $dueInterest;
                    $principal += $dueLoanValue;
                    $interest += $dueInterest;
                    // Getting Principle & interest amounts ============ end =========================>

                    $pawning->current_due_amount = 0;
                    $pawning->status = 4;
                    if(isset($request->release_interest)){
                        $pawning->released_financed_charge = $request->due_interest_value;
                        $interest = $thisInterest;
                    }
                        $allInstallments = PawningInstallment::where('pawning_id', $request->pawning_id)->whereNotIn('status', [1,9])->get();
                        foreach($allInstallments as $allInstallment){
                            $allInstallment->status = 1;
                            $allInstallment->total_amount_to_pay = 0;
                            $allInstallment->total_due_amount = 0;
                            $allInstallment->update();
                        }
                }
                $pawning->update();

                // Acount Transactions ============ start =========================>


                // Calculations for Account Transactions ============ start =========================>
                $preInstExcessAmount = 0;
                if(isset($request->released_amount) && $request->released_amount > 0){
                    $preInstExcessAmount = $request->released_amount;
                }

                // $paidAmount; // paid amount
                // $preInstExcessAmount; // excess from previous
                // $reminderAmount; // remains from previous
                // $totalAmountToPay; // total amount to pay
                $trThisCapital = $request->this_loan_value;
                $trThisInterest = $request->this_interest_value;
                $trDueCapital = $request->due_loan_value;
                $trDueInterest = $request->due_interest_value;

                $totalPaidAmount = $paidAmount + $preInstExcessAmount;
                $totAmountToBePaid = $totalAmountToPay + $preInstExcessAmount;
                $InstAmountToBePaid = $trThisCapital + $trThisInterest;

                    if(!isset($request->pay_total)){
                        if($totAmountToBePaid >= $totalPaidAmount){
                            $principal = ( $totalPaidAmount / $numTot ) * $num1;
                            $interest = $totalPaidAmount - $principal;
                        } else {
                            $principal = $trThisCapital;
                            $interest = $trThisInterest;
                            $trExtraPaid = $totalPaidAmount - $totAmountToBePaid;
                            while ($trExtraPaid > 0){
                                if($trExtraPaid < $InstAmountToBePaid){
                                    $extraRemains = $totalPaidAmount - $principal - $interest - $excess; // check on extra amount remains on payment
                                    if($extraRemains > 0){                                       // check on extra amount remains on payment
                                        $extraPrincipal = ( $extraRemains / $numTot ) * $num1;
                                        $extraInterest = $extraRemains - $extraPrincipal;
                                        $principal += $extraPrincipal;
                                        $interest += $extraInterest;
                                    }
                                    // $excess = $trExtraPaid;
                                    $trExtraPaid = 0;
                                } elseif($trExtraPaid == $InstAmountToBePaid){
                                    $principal += $trThisCapital;
                                    $interest += $trThisInterest;
                                    $trExtraPaid = 0;
                                } elseif($trExtraPaid > $InstAmountToBePaid){
                                    $principal += $trThisCapital;
                                    $interest += $trThisInterest;
                                    $trExtraPaid = $trExtraPaid - $InstAmountToBePaid;
                                }
                            }
                        }
                    } elseif(isset($request->pay_total)){
                        $principal = $trThisCapital + $trDueCapital;
                        $interest = $trThisInterest + $trDueInterest;

                        if(isset($request->release_interest)){
                            $interest = $trThisInterest;
                        }
                    }
            // Calculations for Account Transactions ============ end =========================>

            // if($request->payment_method == 'CASH'){
            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 309, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 309, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');

            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 169, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $principal, 'debit');
            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 169, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $principal, 'credit');

            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 4, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $interest, 'debit');
            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 4, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $interest, 'credit');
            // } elseif($request->payment_method == 'CARD' || $request->payment_method == 'ONLINEPAYMENT'){
                $backAccount = BankAccount::find($request->bank);
                Utility::accountPawningInstallmentTransaction($request->paid_date, $backAccount->chart_of_account_id, 205, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
                Utility::accountPawningInstallmentTransaction($request->paid_date, 205, $backAccount->chart_of_account_id, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');

                // if($request->bank == 1){
                    //     Utility::accountPawningInstallmentTransaction($request->paid_date, 228, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
                    //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 228, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');

                    //     // if(isset($request->released_amount) && $request->released_amount > 0){
                    //     //     Utility::accountPawningInstallmentTransaction($request->paid_date, 322, 228, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountPawningInstallmentTransaction($request->paid_date, 228, 322, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 2){
                    //     Utility::accountPawningInstallmentTransaction($request->paid_date, 227, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
                    //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 227, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');

                    //     // if(isset($request->released_amount) && $request->released_amount > 0){
                    //     //     Utility::accountPawningInstallmentTransaction($request->paid_date, 322, 227, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountPawningInstallmentTransaction($request->paid_date, 227, 322, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $preInstExcessAmount, 'credit');
                    //     // }
                    // } else if($request->bank == 3){
                    //     Utility::accountPawningInstallmentTransaction($request->paid_date, 229, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
                    //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 229, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');

                    //     // if(isset($request->released_amount) && $request->released_amount > 0){
                    //     //     Utility::accountPawningInstallmentTransaction($request->paid_date, 322, 229, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $preInstExcessAmount, 'debit');
                    //     //     Utility::accountPawningInstallmentTransaction($request->paid_date, 229, 322, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $preInstExcessAmount, 'credit');
                    //     // }
                // }

                if(isset($request->released_amount) && $request->released_amount > 0){
                    Utility::accountPawningInstallmentTransaction($request->paid_date, 29, 205, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $preInstExcessAmount, 'debit');
                    Utility::accountPawningInstallmentTransaction($request->paid_date, 205, 29, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $preInstExcessAmount, 'credit');
                }

                Utility::accountPawningInstallmentTransaction($request->paid_date, 205, 23, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $principal, 'debit');
                Utility::accountPawningInstallmentTransaction($request->paid_date, 23, 205, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $principal, 'credit');

                Utility::accountPawningInstallmentTransaction($request->paid_date, 205, 206, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $interest, 'debit');
                Utility::accountPawningInstallmentTransaction($request->paid_date, 206, 205, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $interest, 'credit');

                if($excess > 0){
                    Utility::accountPawningInstallmentTransaction($request->paid_date, 205, 29, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $excess, 'debit');
                    Utility::accountPawningInstallmentTransaction($request->paid_date, 29, 205, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $excess, 'credit');
                }
            // } elseif($request->payment_method == 'CHEQUE'){
            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 309, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 309, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');

            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 169, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $principal, 'debit');
            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 169, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $principal, 'credit');

            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 180, 4, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $interest, 'debit');
            //     Utility::accountPawningInstallmentTransaction($request->paid_date, 4, 180, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $interest, 'credit');

            //     if($request->bank == 1){
            //         Utility::accountPawningInstallmentTransaction($request->realised_date, 228, 309, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
            //         Utility::accountPawningInstallmentTransaction($request->realised_date, 309, 228, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');
            //     } else if($request->bank == 2){
            //         Utility::accountPawningInstallmentTransaction($request->realised_date, 227, 309, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
            //         Utility::accountPawningInstallmentTransaction($request->realised_date, 309, 227, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');
            //     } else if($request->bank == 3){
            //         Utility::accountPawningInstallmentTransaction($request->realised_date, 229, 309, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'debit');
            //         Utility::accountPawningInstallmentTransaction($request->realised_date, 309, 229, $request->pawning_id, $request->installment_id, $request->customer_id, 9, $paidAmount, 'credit');
            //     }
            // }
                return redirect()->back()->with('success', __('Pawning Installment successfully updated !!!'));
        }

    }

    public function cancelInstallment($id){
        if(\Auth::user()->can('cancel installment pawning')){
            $installment = PawningInstallment::find($id);
            $pawning = Pawning::where('id', $installment->pawning_id)->first();
            $receipt = PawningReceipt::where('pawning_id', $installment->pawning_id)
                                    ->where('issue_date', $installment->paid_date)
                                    ->where('installment_due_date', $installment->installment_date)
                                    ->where('status', 1)
                                    ->where('paid_amount', $installment->paid_amount)->first();
            if($receipt){
                $receipt->status = 0;
                $receipt->update();
            }
            $installment->status= 0;

            $paidAmount = $installment->paid_amount;

            $pastInstallment = PawningInstallment::where('pawning_id', $installment->pawning_id)->where('id', '<', $id)->whereNotNull('total_due_amount')->orderBy('id', 'DESC')->first();
            if($pastInstallment){
                $pastInstallmentTotalDueAmount = $pastInstallment->total_due_amount;
            } else {
                $pastInstallmentTotalDueAmount = $pawning->full_total_of_payment;
            }

            // Normal payment
            if($installment->totaly_paid != 1){

                if($pawning){
                    $changingAmount = 0;
                    $currentDueAmountShould = $pastInstallmentTotalDueAmount;

                        if($paidAmount > $currentDueAmountShould){
                            $changingAmount = $currentDueAmountShould;
                        } else {
                            $changingAmount = $paidAmount;
                        }

                    $pawning->current_due_amount = $pawning->current_due_amount + $changingAmount;
                    if($pawning->status == 4){ $pawning->status = 2; }
                    $pawning->update();
                }

                $installmentId = $id;

                if($installment->extra_paid_amount != null){
                    $extraPaid = $installment->extra_paid_amount;
                    while($extraPaid > 0){
                        $nextInstallment = PawningInstallment::where('pawning_id', $installment->pawning_id)->where('id', '>', $installmentId)->first();

                            if($nextInstallment){
                                $extraPaid = $extraPaid - $nextInstallment->released_amount;
                                $installmentId = $nextInstallment->id;
                                    if($nextInstallment->total_amount_to_pay > 0){
                                        $nextInstallment->total_amount_to_pay = $nextInstallment->total_amount_to_pay + $nextInstallment->released_amount;
                                    } else {
                                        $nextInstallment->total_amount_to_pay =  $nextInstallment->installment_amount;
                                    }
                                $nextInstallment->released_amount = null;
                                $nextInstallment->total_due_amount = null;
                                $nextInstallment->status= 0;
                                $nextInstallment->update();
                            } else {
                                $extraPaid = 0;
                            }

                    }
                }

                if($installment->remainder_of_paid_amount != null){
                    $remainderOfPaid = $installment->remainder_of_paid_amount;
                    while($remainderOfPaid > 0){
                        $nextInstallment = PawningInstallment::where('pawning_id', $installment->pawning_id)->where('id', '>', $installmentId)->first();

                        if($nextInstallment){
                            // if($nextInstallment->total_amount_to_pay > 0){
                                $nextInstallment->total_amount_to_pay = $nextInstallment->total_amount_to_pay - $nextInstallment->remainder_amount;
                            // } else {
                            //     $nextInstallment->total_amount_to_pay =  $nextInstallment->installment_amount;
                            // }
                            $nextInstallment->remainder_amount = null;
                            $nextInstallment->total_due_amount = null;
                            $nextInstallment->status= 0;
                            $nextInstallment->update();
                            $remainderOfPaid = 0;
                        } else {
                            $remainderOfPaid = 0;
                        }
                    }
                    $installment->remainder_of_paid_amount = null;
                }
            }
            // Totaly Paid payments
            elseif($installment->totaly_paid == 1){

                if($pawning){
                    $changingAmount = 0;
                    $currentDueAmountShould = $pastInstallmentTotalDueAmount;

                        if($paidAmount > $currentDueAmountShould){
                            $pawning->current_due_amount = $currentDueAmountShould;
                        } else {
                            $pawning->current_due_amount = $pawning->current_due_amount + $paidAmount + $pawning->released_financed_charge;
                        }

                    $pawning->released_financed_charge = null;
                    if($pawning->status == 4){ $pawning->status = 2; }
                    $pawning->update();
                }

                $nextInstallments = PawningInstallment::where('pawning_id', $installment->pawning_id)->where('id', '>', $id)->where('status', 1)->get();
                foreach($nextInstallments as $nextInstallment){
                    $nextInstallment->total_amount_to_pay =  $nextInstallment->installment_amount;
                    $nextInstallment->total_due_amount = null;
                    $nextInstallment->status= 0;
                    $nextInstallment->update();
                }

                $installment->totaly_paid = null;
                // $installment->total_amount_to_pay =  $installment->amount - $installment->released_amount;
            }

            $installment->released_amount = $installment->released_amount;
            $installment->extra_paid_amount = null;
            $installment->total_due_amount= null;
            $installment->paid_amount_in_words= null;
            $installment->paid_amount= null;
            $installment->payment_method= null;
            $installment->card_type= null;
            if($installment->card_expence_id != null){
                $cardCharge = CardInterestExpense::find($installment->card_expence_id);
                $cardCharge->status = 0;
                $cardCharge->update();
            }
            $installment->card_expence_id= null;
            $installment->bank_id= null;
            $installment->card_charge_amount= null;
            $installment->total_amount= null;
            $installment->cheque_realised_date= null;
            $installment->cheque_number= null;
            $installment->paid_date= null;
            $installment->update();

            Utility::deleteAccountPawningInstallmentTransaction($id);

            return redirect()->back()->with('success', __('Installment successfully Cancelled!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function pawningNumber()
    {
    // $latest = Pawning::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
    $latest = Pawning::latest()->first();

    if(!$latest)
    {
        return 1;
    }
        return $latest->pawning_id + 1;
    }


    public function product(Request $request)
    {

        // $data['goldRateId'] = $goldRate = GoldRate::find($request->gold_rate_id);

        // return json_encode($data);
        $data['gold_type'] = $gold_type = GoldRate::find($request->gold_rate_type_id);
        $data['gold_interest_rate'] = $interest = InterestRate::where('id', 4)->get();

        $gold_rate_value   = $gold_type->gold_rate;
        $weight            = 1;
        $poundWeight    =  8;
        $gold_interest = $interest->interest_rate;


        $data['amountFinanced']         = ($gold_rate_value * $weight) / $poundWeight;
        $data['financeCharge'] = ($amountFinanced * $gold_interest) / 100;
        $data['totalOfPayment'] = $amountFinanced + $financeCharge;

        return json_encode($data);
    }

    public function goldRateSearch($id){
        $rate_value = GoldRate::where('gold_rate_id', $id)->get();
        return json_encode($rate_value);
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit pawning'))
        {
            $customers = Customer::whereIn('type',[0,2])->where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $timePeriods = TimePeriod::where('status', 1)->get();
            $pawningJewelleries = PawningJewellery::where('status', 1)->get();
            $goldRates = GoldRate::where('status', 1)->get()->pluck('gold_rate_type', 'id');
            $goldRates->prepend('Select Gold Rate Type', '');
            $goldInterest = InterestRate::where('id', 4)->first();

            $users = User::where('is_active', 1)->get()->pluck('name', 'id');

            $pawning = Pawning::with(['pawnedItem'=>function($query){$query->with('goldType')->where('status', 1); }])->find($id);
            return view('pawning.edit', compact('pawning','customers', 'timePeriods', 'pawningJewelleries', 'goldRates', 'goldInterest', 'users'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request)
    {
        if(\Auth::user()->can('edit pawning'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'customer_id' => 'required',
                                   'issue_date' => 'required',
                                   'officer_id' => 'required',
                                   'time_range' => 'required',
                                   'time_period_id' => 'required',
                                   'gold_interest' => 'required',
                                   'items' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $checkInstallments = PawningInstallment::where('pawning_id', $request->pawning_id)->whereNotIn('status', [9])->get();
                if($checkInstallments != null){
                    foreach($checkInstallments as $checkInstallment){
                        if($checkInstallment->status == 1){
                            return redirect()->back()->with('error', __("Could not Proceed! Some installments have been already settled."));
                        }
                    }
                }

            $pItems = PawnedItem::where('pawning_id', $request->pawning_id)->get();
                foreach($pItems as $pItem){
                    $pItem->status = 0;
                    $pItem->update();
                }
            $pinsts = PawningInstallment::where('pawning_id', $request->pawning_id)->get();
                foreach($pinsts as $pinst){
                    $pinst->status = 9;
                    $pinst->update();
                }

            $pawning                        = Pawning::find($request->pawning_id);
            $pawning->customer_id           = $request->customer_id;
            $pawning->officer_id            = $request->officer_id;
            $pawning->introducer_id         = $request->introducer_id;
            $pawning->time_range            = $request->time_range;
            $pawning->time_period_id        = $request->time_period_id;
            $pawning->issue_date            = $request->issue_date;
            $pawning->due_date              = $request->due_date;
            $pawning->installment_date      = $request->installment_date;
            $pawning->full_amount_financed  = $request->totalAmountFinanced;
            $pawning->full_amount_payout    = $request->totalAmountPayOut;
            $pawning->amount_payout_in_words    = $request->amount_payout_in_words;
            $pawning->interest_rate         = $request->gold_interest;
            $pawning->full_financed_charge  = $request->totalFinanceCharge;
            $pawning->full_total_of_payment = $request->fullTotalOfPayments;
            $pawning->full_prepayment       = $request->fullPrepayment;
            $pawning->current_due_amount    = $request->fullTotalOfPayments;
            $pawning->update();

            $pawningItems = $request->items;

            $timeRange = $request->time_range;
            $dateofPawn = explode('-',$request->issue_date);
            $pawn_yr = $dateofPawn[0];
            // $pawn_mn = $dateofPawn[1];
            if($timeRange == 2){
                if($dateofPawn[2] > $request->installment_date){
                    $pawn_mn = $dateofPawn[1] + 1;
                } else {
                    $pawn_mn = $dateofPawn[1];
                }
            } else if($timeRange == 1){
                if($dateofPawn[2] > $request->installment_date){
                    $pawn_mn = $dateofPawn[1] + 1;
                } else {
                    $pawn_mn = $dateofPawn[1];
                }
            } else {
                $pawn_mn = $dateofPawn[1];
            }
            $pawn_day = $request->installment_date;

            $installment_date = Carbon::create($pawn_yr, $pawn_mn, $pawn_day);

            foreach( $request->items as $pawningItems)
            {
                $pawnedItem                   = new PawnedItem();
                $pawnedItem->pawning_id       = $request->pawning_id;
                $pawnedItem->jewellery_id     = $pawningItems['jewellery_id'];
                $pawnedItem->gold_rate_type   = $pawningItems['gold_rate_type'];
                $pawnedItem->payment_rate_value   = $pawningItems['payment_rate_value'];
                $pawnedItem->weight           = $pawningItems['weight'];
                $pawnedItem->amount_financed  = $pawningItems['amountFinanced'];
                $pawnedItem->amount_payout    = $pawningItems['amountPayout'];
                $pawnedItem->financed_charge  = $pawningItems['financeCharge'];
                $pawnedItem->total_of_payment = $pawningItems['totalOfPayment'];
                $pawnedItem->prepayment       = $pawningItems['prepayment'];
                $pawnedItem->status           = 1;
                $pawnedItem->created_by       = \Auth::user()->id;
                $pawnedItem->save();
            }

            if($timeRange == 3){
                $newInstallmentDate = $installment_date->addMonth(1);
                for ($i=0; $i < $request->time_period_id; $i++)
                {
                    $installment                        = new PawningInstallment();
                    $installment->pawning_id            = $request->pawning_id;
                    $installment->installment_date      = $newInstallmentDate->format('Y-m-d');
                    $installment->installment_amount    = $request->fullPrepayment;
                    $installment->total_amount_to_pay    = $request->fullPrepayment;
                    if($i == 0){
                        $installment->total_due_amount =$request->fullTotalOfPayments;
                    }
                    $installment->status                = 0;
                    $installment->time_range            = $timeRange;
                    $installment->sent_reminder         = 0;
                    $installment->branch_id             = \Auth::user()->branchId();
                    $installment->created_by            = \Auth::user()->id;
                    $installment->save();

                    $newInstallmentDate = $newInstallmentDate->addMonth(1);
                }
            } elseif($timeRange == 2){
                // $newInstallmentDate = $installment_date->addMonth(1);
                $totWeeks = $request->time_period_id*4;
                $newInstallmentDate = $installment_date;
                for ($i=0; $i < $totWeeks; $i++)
                {
                    $installment                        = new PawningInstallment();
                    $installment->pawning_id            = $request->pawning_id;
                    $installment->installment_date      = $newInstallmentDate->format('Y-m-d');
                    $installment->installment_amount    = $request->fullPrepayment;
                    $installment->total_amount_to_pay    = $request->fullPrepayment;
                    if($i == 0){
                        $installment->total_due_amount =$request->fullTotalOfPayments;
                    }
                    $installment->status                = 0;
                    $installment->time_range            = $timeRange;
                    $installment->sent_reminder         = 0;
                    $installment->branch_id             = \Auth::user()->branchId();
                    $installment->created_by            = \Auth::user()->id;
                    $installment->save();

                    $newInstallmentDate = $newInstallmentDate->addDay(7);
                }
            } elseif($timeRange == 1){
                // $newInstallmentDate = $installment_date->addMonth(1);
                $totDays = $request->time_period_id*30;
                $newInstallmentDate = $installment_date;
                for ($i=0; $i < $totDays; $i++)
                {
                    $installment                        = new PawningInstallment();
                    $installment->pawning_id            = $request->pawning_id;
                    $installment->installment_date      = $newInstallmentDate->format('Y-m-d');
                    $installment->installment_amount    = $request->fullPrepayment;
                    $installment->total_amount_to_pay    = $request->fullPrepayment;
                    if($i == 0){
                        $installment->total_due_amount =$request->fullTotalOfPayments;
                    }
                    $installment->status                = 0;
                    $installment->time_range            = $timeRange;
                    $installment->sent_reminder         = 0;
                    $installment->branch_id             = \Auth::user()->branchId();
                    $installment->created_by            = \Auth::user()->id;
                    $installment->save();

                    $newInstallmentDate = $newInstallmentDate->addDay(1);
                }
            }

            // return redirect()->route('pawning.index')->with('success', __('Pawning successfully updated.'));
            return redirect()->back()->with('success', __('Pawning successfully updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function receipt($id){
        $receipt = PawningReceipt::with('invoice', 'pawnings', 'customer')->where('id', $id)->first();
        $user = PawningReceipt::select('users.name')
                                ->join('users', 'users.id', 'pawning_receipts.created_by')
                                ->where('pawning_receipts.id', $id)
                                ->first();
        // $settings = Utility::settings();
        // $receipt = PawningReceipt::with('invoice')->where('id', $id)->first();
        // $logo         = asset(Storage::url('uploads/logo/'));
        // $company_logo = Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        // $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        // return view('pawning.receipt', compact('settings', 'receipt', 'img'));
        return view('pawning.receipt', compact('receipt', 'user'));
    }

    public function receiptPrint($id){
        // $settings = Utility::settings();
        // $receipt = PawningReceipt::with('invoice')->where('id', $id)->first();
        // $logo         = asset(Storage::url('uploads/logo/'));
        // $company_logo = Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        // // $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        // return view('pawning.receipt_print', compact('settings', 'receipt', 'img'));
        $receipt = PawningReceipt::with('invoice', 'pawnings', 'customer')->where('id', $id)->first();
        $user = PawningReceipt::select('users.name')
                                ->join('users', 'users.id', 'pawning_receipts.created_by')
                                ->where('pawning_receipts.id', $id)
                                ->first();
        return view('pawning.receipt_print', compact('receipt', 'user'));
    }

    public function cashCollectorReceiptPrint($id){
        // $settings = Utility::settings();
        // $receipt = PawningReceipt::with('invoice')->where('id', $id)->first();
        // $logo         = asset(Storage::url('uploads/logo/'));
        // $company_logo = Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        // // $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        // return view('pawning.receipt_print', compact('settings', 'receipt', 'img'));
        $receipt = PawningReceipt::with('invoice', 'pawnings', 'customer')->where('id', $id)->first();
        $user = PawningReceipt::select('users.name', 'users.branch_id')
                                ->join('users', 'users.id', 'pawning_receipts.created_by')
                                ->where('pawning_receipts.id', $id)
                                ->first();
        return view('pawning.cash_collector_receipt', compact('receipt', 'user'));
    }


    public function block($id){
        $pawningJewellery = PawningJewellery::find($id);
        $pawningJewellery->status = 0;
        $pawningJewellery->update();
        return redirect()->back()->with('success', __('Pawning Jewellery successfully blocked !!!'));
    }


    public function unblock($id){
        $pawningJewellery = PawningJewellery::find($id);
        $pawningJewellery->status = 1;
        $pawningJewellery->update();
        return redirect()->back()->with('success', __('Pawning Jewellery successfully unblocked !!!'));
    }


    public function destroy($id)
    {
        if(\Auth::user()->can('delete pawning')){
            $pawning = Pawning::find($id);
            $pawning->status = 0;
            $pawning->update();

            $pItems = PawnedItem::where('pawning_id', $id)->get();
                foreach($pItems as $pItem){
                    $pItem->status = 0;
                    $pItem->update();
                }

            $pInsts = PawningInstallment::where('pawning_id', $id)->get();
                foreach($pInsts as $pInst){
                    $pInst->status = 9;
                    $pInst->update();
                }

            $pReceipts = PawningReceipt::where('pawning_id', $id)->get();
                foreach($pReceipts as $pReceipt){
                    $pReceipt->status = 0;
                    $pReceipt->update();
                }

            return redirect()->back()->with('success','Pawning Deleted Successfully!');
        }
        else
        {
            return redirect()->back()->with('error','Permission denied.');
        }


    }

    public function pirintTicket($id){
        $pawningTicket = Pawning::with('customer', 'pawnedItem.jewellery', 'pawnedItem.goldType')
                                        ->where('id',$id)->first();

        return view('pawning.pawning_ticket', compact('pawningTicket'));
    }

    public function showReminderOne($id)
    {
        // if(\Auth::user()->can('view pawning reminder one')) {
            // $pawningReminderOne = Pawning::find($id);
            // return view('pawning.reminder_one', compact('pawningReminderOne'));
            $pawnings = Pawning::with('customer','pawnedItem')->where('id', $id)->first();
            return view('pawning.reminder_one', compact('pawnings'));
        // }

        // else {
        //     return response()->json(['error' => __('Permission denied.')], 401);
        // }
    }

    public function showReminderTwo($id)
    {
        // if(\Auth::user()->can('view pawning reminder two')) {
            // $pawningReminderTwo = Pawning::find($id);
            // return view('pawning.reminder_two', compact('pawningReminderTwo'));
            $pawnings = Pawning::with('customer','pawnedItem')->where('id', $id)->first();
            return view('pawning.reminder_two', compact('pawnings'));
        // }

        // else {
        //     return response()->json(['error' => __('Permission denied.')], 401);
        // }
    }

    public function showReminderThree($id)
    {
        // if(\Auth::user()->can('view pawning reminder three')) {
            // $pawningReminderThree = Pawning::find($id);
            // return view('pawning.reminder_three', compact('pawningReminderThree'));
            // $pawnings = Pawning::with('customer','pawnedItem')->where('id', $id)->first();
            // $pawningItemDetails = PawnedItem::with('jewellery','goldType')
            //                                 ->where('pawning_id',$id)
            //                                 ->get();
            $pawnings = Pawning::with('customer')->where('id', $id)->first();
            $pawningItemDetails = PawnedItem::with('jewellery')
                                            ->where('pawning_id',$id)
                                            ->get();
            // dd($pawnings);
            return view('pawning.reminder_three', compact('pawnings','pawningItemDetails'));
        // }

        // else {
        //     return response()->json(['error' => __('Permission denied.')], 401);
        // }
    }

    public function voucherCreate($id){
        if(\Auth::user()->can('create payment voucher'))
        {
            // $supplier = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customers = Customer::where('status', 1)->where('branch_id', \Auth::user()->branchId())->whereIn('type', [0,2])->get()->pluck('name', 'id');
            $customers->prepend('-- Customer --', '');
            $pawning = Pawning::with('customer')->find($id);
            $chequeBanks = BankAccount::where('branch_id', \Auth::user()->branchId())->where('status', 1)->get();
            $accounts = GeneralLedgerAccount::orderBy('account_name')->get()->pluck('account_name', 'id');
            $banks = BankAccount::where('branch_id', \Auth::user()->branchId())->get()->pluck('bank', 'id');
            $banks->prepend('Select Bank', '');
            $chartAccount = 255;

            return view('pawning.create_loan_payment_voucher', compact('pawning', 'chequeBanks', 'customers', 'accounts', 'banks', 'chartAccount'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function authenticate(Request $request){
        $email = $request->login_details[0];
        $password = $request->login_details[1];

        $user = User::where('email', $email)->first();

        if($user == null){
            return json_encode('email_error');
        } elseif ($user != null){
            if($user->type == 'company' || $user->type == 'Admin' || $user->type == 'Branch Manager' || $user->type == 'Branch Accountant'){
                if(Hash::check($password, $user->password)){
                    return json_encode('success');
                } else {
                    return json_encode('password_error');
                }
            } else {
                return json_encode('user_error');
            }
        }
    }

}
