<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\HirePurchaseContract;
use App\Sale;
use App\Installment;
use App\Receipt;
use App\FineRate;
use App\Product;
use App\Invoice;
use App\CardInterestExpense;
use App\Customer;
use App\InstallmentAdvance;
use App\SMS;
// use Illuminate\Support\Facades\Storage;
use App\Utility;
use App\WaittingInstallment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Storage;
use App\ProductCategory;
use App\BankAccount;
use App\FinancialEntry;
use App\Setting;

class HirePurchaseContractController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index()
    {
        if (\Auth::user()->can('manage sale')) {
            $query = Sale::with('invoice')
                ->where('sale_type', 1)
                ->whereIn('status', [1, 2, 3]);
            if (\Auth::user()->branchId() != 0) {
                $query->where('branch_id', '=', \Auth::user()->branchId());
            }
            $contracts = $query->orderBy('id', 'DESC')->get();
            $currencySymbol = $this->currencySymbol;
            return view('hire_purchase_contract.index', compact('contracts', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function view($id)
    {
        if (\Auth::user()->can('view sale')) {
            $sales = Sale::with([
                'guarantor1',
                'guarantor2',
                'lastInstallment',
                'installments' => function ($query1) {
                    $query1->whereNotIn('status', [0]);
                },
                'receipts' => function ($query2) {
                    $query2->where('status', 1);
                },
            ])->where('id', $id)->first();

            $created = Sale::select('users.id', 'users.name')
                ->leftJoin('users', 'users.id', 'sales.created_by')
                ->where('sales.id', $id)->first();

            $introducer = Sale::select('users.name')
                ->join('users', 'sales.introducer_id', 'users.id')
                ->where('sales.id', $id)
                ->first();
            $currencySymbol = $this->currencySymbol;
            return view('hire_purchase_contract.view', compact('sales', 'created', 'introducer', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payInstallment($id)
    {
        if (\Auth::user()->can('pay sale')) {
            $installments = Installment::where('id', $id)->first();
            $nextInstallment = Installment::where('sale_id', $installments->sale_id)
                ->whereNotIn('status', [0, 1])
                ->where('id', '!=', $installments->id)
                ->where('installment_date', '>=', $installments->installment_date)
                ->orderBy('installment_date', 'asc')
                ->first();
            $dueInstallments = Installment::where('sale_id', $installments->sale_id)->whereIn('status', [2, 3])->get();

            $installmentAdvances = InstallmentAdvance::where('installment_id', $id)->where('status', 1)->get();

            $sales = Sale::where('id', $installments->sale_id)->first();

            // $data['amountForInst'] = $sales->outstanding_amount / $sales->installment;
            $data['amountForInst'] = number_format((float)$sales->outstanding_amount / $sales->installment, 2, '.', '');
            $data['interestForInst'] = number_format((float)$sales->interest_amount / $sales->installment, 2, '.', '');
            $data['insts'] = $sales->installment;
            $data['dueInsts'] = max(0, count($dueInstallments) - 1);

            if ($installments->new_installment == 1) {
                $num1 = $sales->outstanding_amount;      // Capital amount
                $num2 = $sales->interest_amount;        // Intesrest amount
                $paid = $sales->current_due_amount;     // Current Due amount
                for ($i = $num2; $i > 1; $i--) {
                    if (($num1 % $i) == 0 && ($num2 % $i) == 0) {
                        $num1 = $num1 / $i;
                        $num2 = $num2 / $i;
                    }
                }
                $numTot = $num1 + $num2;
                $principal = round(($paid / $numTot) * $num1, 2);
                $interest = $paid - $principal;
                $data['amountForInst'] = $principal;
                $data['interestForInst'] = $interest;
            }

            return view('hire_purchase_contract.pay_installment', compact('installments', 'sales', 'nextInstallment', 'data', 'installmentAdvances'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('pay sale')) {
            $userId = Auth::user()->id;
            $validator = \Validator::make(
                $request->all(),
                [
                    'sale_id' => 'required',
                    'installment_id' => 'required',
                    'invoice_id' => 'required',
                    'current_date' => 'required',
                    'installment_due_date' => 'required',
                    'customer_id' => 'required',
                    'installment_amount' => 'required',
                    'paid_amount' => 'required',
                    'payment_method' => 'required',
                    'paid_amount_in_words' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if (isset($request->waitting_installment_id)) {
                $checkForinstallments = Installment::find($request->installment_id);
                if ($checkForinstallments->status == 1) {
                    return redirect()->back()->with('error', 'The Installment Payment has been Already Settled!');
                }
                $userId = $request->created_by;
                $waittingSale = WaittingInstallment::find($request->waitting_installment_id);
                $waittingSale->status = 2;
                $waittingSale->approved_rejected_by = \Auth::user()->id;
                $waittingSale->update();
            }

            $paidAmount = $request->paid_amount;
            $totalAmountToPay = $request->total_amount_tobe_paid;
            $extraPaid = 0;
            $remainder = 0;

            $relasedAmount = $request->released_amount;
            if (isset($request->remainder_amount)) {
                $reminderAmount = $request->remainder_amount;
            } else {
                $reminderAmount = 0;
            }

            if (isset($request->fine_amount) && $request->fine_amount) {
                // $fineAmount = $request->fine_amount;
                $fineAmount = 0;
            } else {
                $fineAmount = 0;
            }

            if ($totalAmountToPay >= $relasedAmount) {
                $checkAmountToBePaid = $totalAmountToPay - $relasedAmount;
            } else {
                $checkAmountToBePaid = $totalAmountToPay;
            }

            $due_paid_without_fine = $request->paid_amount + $request->released_amount - $fineAmount;

            $installments = Installment::find($request->installment_id);

            if ($paidAmount >= $checkAmountToBePaid) {
                $extraPaid = $paidAmount - $checkAmountToBePaid;
            } elseif ($paidAmount < $checkAmountToBePaid) {
                $remainder = $checkAmountToBePaid - $paidAmount;
            }

            $sales = Sale::find($request->sale_id);
            $current_due_amount = 0;
            $due_paid_amount = 0;
            $totalDueAmount = $sales->total_due_amount;
            if ($sales->current_due_amount != null) {
                $current_due_amount = $sales->current_due_amount;
            }
            if ($sales->due_paid_amount != null) {
                $due_paid_amount = $sales->due_paid_amount;
            }

            $newDueAmount = $current_due_amount - $due_paid_without_fine;
            $newDuePaidAmount = $due_paid_amount + $due_paid_without_fine;
            if ($newDueAmount < 0) {
                $sales->current_due_amount = 0;
            } else {
                $sales->current_due_amount = $newDueAmount;
            }
            $sales->due_paid_amount = $newDuePaidAmount;

            if ($request->payment_method == 'CARD') {
                if ($request->card_charge_amount != '') {
                    $cardCharge = new CardInterestExpense();
                    $cardCharge->amount = $request->card_charge_amount;
                    $cardCharge->invoice_id = $request->invoice_id;
                    $cardCharge->status = 1;
                    $cardCharge->save();
                }
            }

            $maxId = Receipt::max('id');
            $receiptCode = "INR" . str_pad(($maxId + 1), 6, "0", STR_PAD_LEFT);

            $receipts = new Receipt();
            $receipts->branch_id = \Auth::user()->branchId();
            $receipts->receipt_code = $receiptCode;
            $receipts->customer_id = $request->customer_id;
            $receipts->invoice_id = $request->invoice_id;
            $receipts->issue_date = $request->current_date;
            $receipts->installment_due_date = $request->installment_due_date;
            $receipts->status = 1;
            $receipts->installment_amount = $request->installment_amount;
            $receipts->penalty_charge = $request->add_penalty_amount;
            if ($request->fine_amount != null) {
                if (!isset($request->release_fine)) {
                    // $receipts->extra_charges = $request->fine_amount;
                    $receipts->extra_charges = 0;
                };
            }
            $receipts->total_amount_to_pay = $request->total_amount_tobe_paid;
            $receipts->paid_amount = $request->paid_amount;
            if (isset($request->pay_total)) {
                $receipts->balance_amount = 0;
            } else {
                if ($newDueAmount < 0) {
                    $receipts->balance_amount = 0;
                } else {
                    $receipts->balance_amount = $newDueAmount;
                }
            }
            $receipts->paid_amount_in_words = $request->paid_amount_in_words;
            $receipts->payment_method = $request->payment_method;

            if ($request->payment_method == 'CARD') {
                $receipts->card_type            = $request->card_type;
                $receipts->card_charge_amount   = $request->card_charge_amount;
                $receipts->total_amount         = $request->total_amount;
            } elseif ($request->payment_method == 'CHEQUE') {
                $receipts->cheque_number        = $request->cheque_number;
            }

            $receipts->description = $request->description;
            $receipts->created_by = $userId;
            $receipts->save();

            $receiptId = Receipt::max('id');
            $installments->total_amount_to_pay = $request->total_amount_tobe_paid;
            $installments->released_amount = $request->released_amount + $request->excess_amount;
            $installments->manual_released_amount = $request->released_amount;
            $installments->status = 1;
            $installments->paid_date = $request->current_date;
            $installments->paid_amount = $request->paid_amount;
            $installments->penalty_charge = $request->add_penalty_amount;
            $installments->paid_amount_in_words = $request->paid_amount_in_words;
            $installments->payment_method = $request->payment_method;

            if ($request->payment_method == 'CARD') {
                $installments->card_type            = $request->card_type;
                $installments->card_expence_id      = $cardCharge->id;
                $installments->card_charge_amount   = $request->card_charge_amount;
                $installments->total_amount         = $request->total_amount;
            } elseif ($request->payment_method == 'CHEQUE') {
                $installments->cheque_number        = $request->cheque_number;
                $installments->cheque_realised_date     = $request->realised_date;
            }

            if ($extraPaid != 0) {
                $installments->extra_paid_amount = $extraPaid;
            }
            if ($remainder != 0) {
                $installments->remainder_of_paid_amount = $remainder;
            }

            if (isset($request->fine_amount)) {
                // $installments->fine = $request->fine_amount;
                $installments->fine = 0;
            }

            if (isset($request->pay_total)) {
                $installments->total_due_amount = 0;
                $installments->totaly_paid = 1;
            } else {
                $installments->total_due_amount = max(0, $newDueAmount);
            }

            $installments->receipt_id = $receiptId;
            $installments->update();

            // Getting ratio & productDetail variable ============== start ========================>
            $outstanding = $sales->outstanding_amount;
            $interestAmount = $sales->interest_amount;
            $num1 = $outstanding;
            $num2 = $interestAmount;
            for ($i = $num2; $i > 1; $i--) {
                if (($num1 % $i) == 0 && ($num2 % $i) == 0) {
                    $num1 = $num1 / $i;
                    $num2 = $num2 / $i;
                }
            }
            $numTot = $num1 + $num2;

            // $principal = $request->this_sale_value;
            // $interest = $request->this_interest_value;
            // $excessPaid = $request->paid_amount - $fineAmount;
            $principal = 0;
            $interest = 0;
            $excess = 0;

            $productDetail = Product::find($sales->product_id);
            // Getting ratio & productDetail variable ============== end ========================>

            $installmentCount = 1;

            if (!isset($request->pay_total)) {
                if ($extraPaid > 0) {
                    $installmentDate = $installments->installment_date;
                    while ($extraPaid > 0) {
                        $nextInstallment = Installment::where('sale_id', $request->sale_id)
                            ->whereNotIn('status', [0, 1])
                            ->where('id', '!=', $installments->id)
                            ->where('installment_date', '>=', $installmentDate)
                            ->orderBy('installment_date', 'asc')
                            ->first();

                        if ($nextInstallment != null) {
                            $currentDueOnNext = ($nextInstallment->total_amount_to_pay > 0)
                                ? $nextInstallment->total_amount_to_pay
                                : ($nextInstallment->amount + ($nextInstallment->fine ?? 0) - ($nextInstallment->released_amount ?? 0));

                            $nextTotAmountToPay = $currentDueOnNext - $extraPaid;

                            if ($nextTotAmountToPay > 0) {
                                $nextInstallment->total_amount_to_pay = $nextTotAmountToPay;
                                $nextInstallment->released_amount = ($nextInstallment->released_amount ?? 0) + $extraPaid;
                                $nextInstallment->update();
                                $excess = $extraPaid;
                                $extraPaid = 0;
                            } elseif ($nextTotAmountToPay == 0) {
                                $nextInstallment->status = 1;
                                $nextInstallment->total_amount_to_pay = 0;
                                $nextInstallment->released_amount = ($nextInstallment->released_amount ?? 0) + $extraPaid;
                                $nextInstallment->paid_date = $request->current_date;
                                $nextInstallment->update();
                                $excess = $extraPaid;
                                $extraPaid = 0;
                            } elseif ($nextTotAmountToPay < 0) {
                                $nextInstallment->status = 1;
                                $nextInstallment->paid_date = $request->current_date;
                                $nextInstallment->total_amount_to_pay = 0;
                                $nextInstallment->released_amount = ($nextInstallment->released_amount ?? 0) + $currentDueOnNext;
                                $nextInstallment->update();

                                $extraPaid = $extraPaid - $currentDueOnNext;
                                $installmentDate = $nextInstallment->installment_date;
                                $installmentCount = $installmentCount + 1;
                            }
                        } else {
                            $extraPaid = 0;
                        }
                    }
                }

                if ($remainder > 0) {
                    // if($principal >= $remainder){
                    //     $principal -= $remainder;
                    // } else if($principal < $remainder){
                    //     // $diff = $remainder - $principal;
                    //     // $interest -= $diff;
                    //     // $principal = 0;
                    //     if($paidAmount >= $fineAmount){
                    //         $blAmount = $paidAmount - $fineAmount;
                    //         if($blAmount >= $interest){
                    //             $principal = $blAmount - $interest;
                    //         } else {
                    //             $interest = $blAmount;
                    //             $principal = 0;
                    //         }
                    //     } else {
                    //         $interest = 0;
                    //         $principal = 0;
                    //     }
                    // }
                    $hireFineRate = FineRate::where('id', 1)->first();
                    $remainderFineAmount = 0;
                    // $remainderFineAmount = $remainder * ($hireFineRate->fine_rate / 100);

                    $remainderInstallmentDate = $installments->installment_date;
                    while ($remainder > 0) {
                        $nextInstallmentDate = Carbon::parse($remainderInstallmentDate)->addMonth(1)->format('Y-m-d');
                        $nextInstallment = Installment::where('installment_date', $nextInstallmentDate)->where('sale_id', $request->sale_id)->whereNotIn('status', [0])->first();
                        if ($nextInstallment != null) {
                            $nextTotAmountToPay = $nextInstallment->amount + $nextInstallment->fine + $remainder + $remainderFineAmount;

                            $nextInstallment->total_amount_to_pay = $nextTotAmountToPay;
                            $nextInstallment->remainder_amount = $remainder;
                            $nextInstallment->fine = $nextInstallment->fine + $remainderFineAmount;
                            // $nextInstallment->paid_date = $request->current_date;
                            $nextInstallment->update();
                            $remainder = 0;
                        } elseif ($nextInstallment == null) {
                            $newInstallmentDate = $nextInstallmentDate;
                            $installment = new Installment();
                            $installment->branch_id = \Auth::user()->branchId();
                            $installment->sale_id = $request->sale_id;
                            $installment->amount = $remainder;
                            $installment->fine = $remainderFineAmount;
                            $installment->total_amount_to_pay = $remainder + $remainderFineAmount;
                            $installment->status = 2;
                            $installment->new_installment = 1;
                            $installment->installment_date = $newInstallmentDate;
                            $installment->invoice_id = $installments->invoice_id;
                            $installment->save();

                            $remainder = 0;
                        }
                    }
                }

                if ($newDuePaidAmount >= $totalDueAmount) {
                    $sales->status = 1;

                    $allInstallments = Installment::where('sale_id', $request->sale_id)->whereNotIn('status', [1, 0])->get();
                    foreach ($allInstallments as $allInstallment) {
                        $allInstallment->status = 1;
                        $allInstallment->total_amount_to_pay = 0;
                        $allInstallment->total_due_amount = 0;
                        $allInstallment->update();
                    }
                }

                // Getting Principle & interest amounts ============ start =========================>
                // $principal = ( $due_paid_without_fine / $numTot ) * $num1;
                // $interest = $due_paid_without_fine - $principal;

            } elseif (isset($request->pay_total)) {

                // Getting Principle & interest amounts ============ start =========================>
                // $thisAmount = $request->installment_amount - $request->released_amount;
                // $thisValue = ( $thisAmount / $numTot ) * $num1;
                // $thisInterest = $thisAmount - $thisValue;

                // $dueItemValue = $request->due_sale_value;
                // $dueInterest = $request->due_interest_value;

                // // $principal = $thisValue + $dueItemValue;
                // // $interest = $thisInterest + $dueInterest;
                // $principal += $dueItemValue;
                // $interest += $dueInterest;
                // Getting Principle & interest amounts ============ end =========================>

                $sales->current_due_amount = 0;
                $sales->status = 1;
                if (isset($request->release_interest)) {
                    $sales->released_interest_amount = $request->due_interest_value;
                    // $interest = $thisInterest;
                    // $interest -= $dueInterest;
                }

                $allInstallments = Installment::where('sale_id', $request->sale_id)->whereNotIn('status', [1, 0])->get();
                foreach ($allInstallments as $allInstallment) {
                    $allInstallment->status = 1;
                    $allInstallment->total_amount_to_pay = 0;
                    $allInstallment->total_due_amount = 0;
                    $allInstallment->update();
                }
            }
            $sales->update();

            // Acount Transactions ============ start =========================>

            $preInstExcessAmount = 0;
            if (isset($request->excess_amount) && $request->excess_amount > 0) {
                $preInstExcessAmount = $request->excess_amount;
            }

            // $paidAmount; // paid amount
            // $fineAmount; // fine amount
            // $preInstExcessAmount; // excess from previous
            // $relasedAmount; // manual releasing amount
            // $reminderAmount; // remains from previous
            // $totalAmountToPay; // total amount to pay
            $trThisCapital = ($installments->amount / ($sales->outstanding_amount + $sales->interest_amount)) * $sales->outstanding_amount;
            $trThisInterest = $installments->amount - $trThisCapital;
            $trDueCapital = $request->due_sale_value;
            $trDueInterest = $request->due_interest_value;

            $totalPaidAmount = $paidAmount + $preInstExcessAmount - $fineAmount;
            $totAmountToBePaid = $totalAmountToPay - $relasedAmount + $preInstExcessAmount - $fineAmount;
            $InstAmountToBePaid = $trThisCapital + $trThisInterest;

            $accountPaidAmount = $request->paid_amount - $fineAmount;

            if (!isset($request->pay_total)) {
                if ($totAmountToBePaid > $totalPaidAmount) {
                    $principal = ($totalPaidAmount / $numTot) * $num1;
                    $interest = $totalPaidAmount - $principal;
                } elseif ($totAmountToBePaid == $totalPaidAmount) {
                    $principal += $trThisCapital;
                    $interest += $trThisInterest;
                } else {
                    $principal = $trThisCapital;
                    $interest = $trThisInterest;
                    $trExtraPaid = $totalPaidAmount - $totAmountToBePaid;
                    while ($trExtraPaid > 0) {
                        if ($trExtraPaid < $InstAmountToBePaid) {
                            $extraRemains = $totalPaidAmount - $principal - $interest - $excess; // check on extra amount remains on payment

                            if ($extraRemains > 0) {                                       // check on extra amount remains on payment
                                $extraPrincipal = ($extraRemains / $numTot) * $num1;
                                $extraInterest = $extraRemains - $extraPrincipal;
                                $principal += $extraPrincipal;
                                $interest += $extraInterest;
                            }

                            $trExtraPaid = 0;
                        } elseif ($trExtraPaid == $InstAmountToBePaid) {
                            $principal += $trThisCapital;
                            $interest += $trThisInterest;
                            $trExtraPaid = 0;
                        } elseif ($trExtraPaid > $InstAmountToBePaid) {
                            $principal += $trThisCapital;
                            $interest += $trThisInterest;
                            $trExtraPaid = $trExtraPaid - $InstAmountToBePaid;
                        }
                    }
                }
                // dump($principal);
                // dump($interest);
                // dd('hello');

                if ($accountPaidAmount < 0) {
                    $accountPaidAmount = 0;
                    $principal = 0;
                    $interest = 0;
                    if ($fineAmount > 0) {
                        $fineAmount = $request->paid_amount;
                    }
                }
            } elseif (isset($request->pay_total)) {
                $principal = $trThisCapital + $trDueCapital;
                $interest = $trThisInterest + $trDueInterest;

                if (isset($request->release_interest)) {
                    $interest = $trThisInterest;
                }
            }

            $principal = $principal * $installmentCount;
            $interest = $interest * $installmentCount;

            $productCategory = ProductCategory::find($productDetail->product_category_id);
            $backAccount = BankAccount::find($sales->bank_id);

            Utility::accountSalesInstallmentTransaction(
                $request->current_date,
                $backAccount->chart_of_account_id,
                $productCategory->installment_collection_control_chart_of_account_id,
                $request->sale_id,
                $request->installment_id,
                $sales->product_id,
                $request->invoice_id,
                $request->customer_id,
                2,
                $accountPaidAmount,
                'debit'
            );
            Utility::accountSalesInstallmentTransaction(
                $request->current_date,
                $productCategory->installment_collection_control_chart_of_account_id,
                $backAccount->chart_of_account_id,
                $request->sale_id,
                $request->installment_id,
                $sales->product_id,
                $request->invoice_id,
                $request->customer_id,
                2,
                $accountPaidAmount,
                'credit'
            );

            if (isset($request->fine_amount) && $request->fine_amount > 0) {
                Utility::accountSalesInstallmentTransaction(
                    $request->current_date,
                    $backAccount->chart_of_account_id,
                    28,
                    $request->sale_id,
                    $request->installment_id,
                    $sales->product_id,
                    $request->invoice_id,
                    $request->customer_id,
                    2,
                    $fineAmount,
                    'debit'
                );
                Utility::accountSalesInstallmentTransaction(
                    $request->current_date,
                    28,
                    $backAccount->chart_of_account_id,
                    $request->sale_id,
                    $request->installment_id,
                    $sales->product_id,
                    $request->invoice_id,
                    $request->customer_id,
                    2,
                    $fineAmount,
                    'credit'
                );
            }

            if (isset($request->excess_amount) && $request->excess_amount > 0) {
                Utility::accountSalesInstallmentTransaction(
                    $request->current_date,
                    29,
                    $productCategory->installment_collection_control_chart_of_account_id,
                    $request->sale_id,
                    $request->installment_id,
                    $sales->product_id,
                    $request->invoice_id,
                    $request->customer_id,
                    2,
                    $preInstExcessAmount,
                    'debit'
                );
                Utility::accountSalesInstallmentTransaction(
                    $request->current_date,
                    $productCategory->installment_collection_control_chart_of_account_id,
                    29,
                    $request->sale_id,
                    $request->installment_id,
                    $sales->product_id,
                    $request->invoice_id,
                    $request->customer_id,
                    2,
                    $preInstExcessAmount,
                    'credit'
                );
            }

            Utility::accountSalesInstallmentTransaction(
                $request->current_date,
                $productCategory->installment_collection_control_chart_of_account_id,
                $productCategory->inventory_loan_chart_of_account_id,
                $request->sale_id,
                $request->installment_id,
                $sales->product_id,
                $request->invoice_id,
                $request->customer_id,
                2,
                $principal,
                'debit'
            );
            Utility::accountSalesInstallmentTransaction(
                $request->current_date,
                $productCategory->inventory_loan_chart_of_account_id,
                $productCategory->installment_collection_control_chart_of_account_id,
                $request->sale_id,
                $request->installment_id,
                $sales->product_id,
                $request->invoice_id,
                $request->customer_id,
                2,
                $principal,
                'credit'
            );

            Utility::accountSalesInstallmentTransaction(
                $request->current_date,
                $productCategory->installment_collection_control_chart_of_account_id,
                $productCategory->interest_income_control_chart_of_account_id,
                $request->sale_id,
                $request->installment_id,
                $sales->product_id,
                $request->invoice_id,
                $request->customer_id,
                2,
                $interest,
                'debit'
            );
            Utility::accountSalesInstallmentTransaction(
                $request->current_date,
                $productCategory->interest_income_control_chart_of_account_id,
                $productCategory->installment_collection_control_chart_of_account_id,
                $request->sale_id,
                $request->installment_id,
                $sales->product_id,
                $request->invoice_id,
                $request->customer_id,
                2,
                $interest,
                'credit'
            );
            if ($excess > 0) {
                Utility::accountSalesInstallmentTransaction(
                    $request->current_date,
                    $productCategory->installment_collection_control_chart_of_account_id,
                    29,
                    $request->sale_id,
                    $request->installment_id,
                    $sales->product_id,
                    $request->invoice_id,
                    $request->customer_id,
                    2,
                    $excess,
                    'debit'
                );
                Utility::accountSalesInstallmentTransaction(
                    $request->current_date,
                    29,
                    $productCategory->installment_collection_control_chart_of_account_id,
                    $request->sale_id,
                    $request->installment_id,
                    $sales->product_id,
                    $request->invoice_id,
                    $request->customer_id,
                    2,
                    $excess,
                    'credit'
                );
            }

            $financialEntry = new FinancialEntry();
            $financialEntry->date = $request->current_date;
            $financialEntry->type = 'Collectioon';
            $financialEntry->sale_id = $sales->id;
            $financialEntry->receipt_id = $receipts->id;
            $financialEntry->installment_id = $installments->id;
            $financialEntry->payment_method = $request->payment_method;
            $financialEntry->amount = $request->paid_amount;
            $financialEntry->debit_credit = 'debit';
            $financialEntry->entry_type = 'collectioon';
            $financialEntry->branch_id = \Auth::user()->branchId();
            $financialEntry->created_by = \Auth::user()->id;
            $financialEntry->save();

            $cus = Customer::find($request->customer_id);
            SMS::sendSMS($cus->phone_mobile, 'Dear Customer, Installment successfully paid!, Receipt Number ' . $receiptCode);

            return redirect()->back()->with('success', __('Installment successfully paid!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function cancelInstallment($id)
    {
        if (\Auth::user()->can('cancel installment sale')) {
            $installment = Installment::find($id);
            $fine = $installment->fine;
            $sales = Sale::where('id', $installment->sale_id)->first();
            $receipt = Receipt::where('invoice_id', $installment->invoice_id)
                ->where('issue_date', $installment->paid_date)
                ->where('installment_due_date', $installment->installment_date)
                ->where('status', 1)
                ->where('paid_amount', $installment->paid_amount)->first();
            if ($receipt) {
                $receipt->status = 0;
                $receipt->update();
            }

            if ($installment->fine != null) {
                $installment->status = 3;
            } else {
                $installment->status = 2;
            }

            $paidAmount = $installment->paid_amount + $installment->manual_released_amount - $fine;

            $pastInstallment = Installment::where('sale_id', $installment->sale_id)->where('id', '<', $id)->whereNotNull('total_due_amount')->orderBy('id', 'DESC')->first();
            if ($pastInstallment) {
                $pastInstallmentTotalDueAmount = $pastInstallment->total_due_amount;
            } else {
                $pastInstallmentTotalDueAmount = $sales->total_due_amount;
            }

            // Normal payment
            if ($installment->totaly_paid != 1) {

                if ($sales) {
                    $changingAmount = 0;
                    $currentDueAmountShould = $pastInstallmentTotalDueAmount;
                    $duePaidAmountShould = $sales->total_due_amount - $currentDueAmountShould;

                    if ($paidAmount > $currentDueAmountShould) {
                        $changingAmount = $currentDueAmountShould;
                    } else {
                        $changingAmount = $paidAmount;
                    }

                    $sales->current_due_amount = $sales->current_due_amount + $changingAmount;
                    $sales->due_paid_amount = $sales->due_paid_amount - $changingAmount;
                    if ($sales->status == 1) {
                        $sales->status = 2;
                    }
                    $sales->update();
                }

                $installmentId = $id;
                if ($installment->extra_paid_amount != null) {
                    $extraPaid = $installment->extra_paid_amount;
                    while ($extraPaid > 0) {
                        $nextInstallment = Installment::where('sale_id', $installment->sale_id)->where('id', '>', $installmentId)->first();

                        if ($nextInstallment) {
                            $extraPaid = $extraPaid - $nextInstallment->released_amount;
                            $installmentId = $nextInstallment->id;
                            if ($nextInstallment->total_amount_to_pay > 0) {
                                $nextInstallment->total_amount_to_pay = $nextInstallment->total_amount_to_pay + $nextInstallment->released_amount;
                            } else {
                                $nextInstallment->total_amount_to_pay =  $nextInstallment->amount + $nextInstallment->fine;
                            }
                            $nextInstallment->released_amount = null;
                            $nextInstallment->total_due_amount = null;
                            if ($nextInstallment->fine != null) {
                                $nextInstallment->status = 3;
                            } else {
                                $nextInstallment->status = 2;
                            }
                            $nextInstallment->update();
                        } else {
                            $extraPaid = 0;
                        }
                    }
                    $installment->extra_paid_amount = null;
                }

                if ($installment->remainder_of_paid_amount != null) {
                    $remainderOfPaid = $installment->remainder_of_paid_amount;
                    while ($remainderOfPaid > 0) {
                        $nextInstallment = Installment::where('sale_id', $installment->sale_id)->where('id', '>', $installmentId)->first();

                        if ($nextInstallment) {
                            if ($nextInstallment->total_amount_to_pay > 0) {
                                $nextInstallment->total_amount_to_pay = $nextInstallment->total_amount_to_pay - $nextInstallment->remainder_amount;
                            } else {
                                $nextInstallment->total_amount_to_pay =  $nextInstallment->installment_amount + $nextInstallment->fine;
                            }
                            $nextInstallment->remainder_amount = null;
                            $nextInstallment->total_due_amount = null;
                            if ($nextInstallment->fine != null) {
                                $nextInstallment->status = 3;
                            } else {
                                $nextInstallment->status = 2;
                            }
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
            elseif ($installment->totaly_paid == 1) {

                if ($sales) {
                    $changingAmount = 0;
                    $currentDueAmountShould = $pastInstallmentTotalDueAmount;
                    $duePaidAmountShould = $sales->total_due_amount - $currentDueAmountShould;

                    if ($paidAmount > $currentDueAmountShould) {
                        $sales->current_due_amount = $currentDueAmountShould;
                        $sales->due_paid_amount = $duePaidAmountShould;
                    } else {
                        $sales->current_due_amount = $sales->current_due_amount + $paidAmount + $sales->released_interest_amount;
                        $sales->due_paid_amount = $sales->due_paid_amount - $paidAmount;
                    }

                    $sales->released_interest_amount = null;
                    if ($sales->status == 1) {
                        $sales->status = 2;
                    }
                    $sales->update();
                }

                $nextInstallments = Installment::where('sale_id', $installment->sale_id)->where('id', '>', $id)->where('status', 1)->get();
                foreach ($nextInstallments as $nextInstallment) {
                    $nextInstallment->total_amount_to_pay =  $nextInstallment->amount + $nextInstallment->fine;
                    $nextInstallment->total_due_amount = null;
                    if ($nextInstallment->fine != null) {
                        $nextInstallment->status = 3;
                    } else {
                        $nextInstallment->status = 2;
                    }
                    $nextInstallment->update();
                }

                $installment->totaly_paid = null;
                $installment->total_amount_to_pay =  $installment->amount + $installment->fine - $installment->released_amount + $installment->manual_released_amount;
            }

            $installment->released_amount = $installment->released_amount - $installment->manual_released_amount;
            $installment->extra_paid_amount = null;
            $installment->manual_released_amount = null;
            $installment->total_due_amount = null;
            $installment->paid_amount_in_words = null;
            $installment->paid_amount = null;
            $installment->payment_method = null;
            $installment->card_type = null;
            if ($installment->card_expence_id != null) {
                $cardCharge = CardInterestExpense::find($installment->card_expence_id);
                $cardCharge->status = 0;
                $cardCharge->update();
            }
            $installment->card_expence_id = null;
            $installment->card_charge_amount = null;
            $installment->total_amount = null;
            $installment->cheque_realised_date = null;
            $installment->cheque_number = null;
            $installment->paid_date = null;
            $installment->update();

            $financialEntry = FinancialEntry::where('installment_id', $installment->id)->first();
            if ($financialEntry) {
                $financialEntry->delete();
            }

            Utility::deleteAccountSalesInstallmentTransaction($id);

            return redirect()->back()->with('success', __('Installment successfully Cancelled!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function receipt($id)
    {
        // $settings = Utility::settings();
        $receipt = Receipt::with('invoice', 'sales')->where('id', $id)->first();

        $user = Receipt::select('users.name')
            ->join('users', 'users.id', 'receipts.created_by')
            ->where('receipts.id', $id)
            ->first();
        // $logo         = asset(Storage::url('uploads/logo/'));
        // $company_logo = Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        // $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        // return view('hire_purchase_contract.receipt', compact('settings', 'receipt', 'img'));

        return view('hire_purchase_contract.receipt', compact('receipt', 'user'));
    }

    public function receiptPrint($id)
    {
        $receipt = Receipt::with('invoice', 'sales.products', 'sales.hirePurchaseDetail.warranty')
            ->where('id', $id)
            ->first();

        $user = Receipt::select('users.name')
            ->join('users', 'users.id', 'receipts.created_by')
            ->where('receipts.id', $id)
            ->first();
        $currencySymbol = $this->currencySymbol;
        // dd($receipt);
        return view('hire_purchase_contract.receipt_print', compact('receipt', 'user', 'currencySymbol'));
        // return view('hire_purchase_contract.receipt_print_old_02', compact('receipt', 'user'));
    }

    public function cashCollectorReceiptPrint($id)
    {
        $receipt = Receipt::with('invoice', 'sales')->where('id', $id)->first();

        $user = Receipt::select('users.name', 'users.branch_id')
            ->join('users', 'users.id', 'receipts.created_by')
            ->where('receipts.id', $id)
            ->first();

        return view('hire_purchase_contract.cash_collector_receipt', compact('receipt', 'user'));
    }

    // public function show($id){
    //     $color = '#4b86a2';
    //     $settings = Utility::settings();
    //     $receipt = Receipt::with('invoice')->where('id', $id)->first();
    //     $logo         = asset(Storage::url('uploads/logo/'));
    //     $company_logo = Utility::getValByName('company_logo');
    //     $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
    //     // $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
    //     return view('hire_purchase_contract.receipt', compact('settings', 'receipt', 'img', 'color'));
    // }


    public function getAdvanceAmount(Request $request)
    {
        $installmentAdvances = InstallmentAdvance::where('installment_id', $request->installment_id)->where('status', 1)->get();
        return json_encode($installmentAdvances);
    }

    public function addAdvanceAmount(Request $request)
    {
        $installments = Installment::where('id', $request->installment_id)->first();
        $sales = Sale::where('id', $installments->sale_id)->first();

        $maxId = Receipt::max('id');
        $receiptCode = "INR" . str_pad(($maxId + 1), 6, "0", STR_PAD_LEFT);

        $invoice = new Invoice();
        $invoice->invoice_code = $receiptCode;
        $invoice->branch_id = \Auth::user()->branchId();
        $invoice->customer_id = $sales->customer->id;
        $invoice->issue_date = date('Y-m-d');;
        $invoice->status = 1;
        $invoice->created_by = Auth::user()->id;
        $invoice->save();

        $receipt = new Receipt();
        $receipt->branch_id = \Auth::user()->branchId();
        $receipt->receipt_code = $receiptCode;
        $receipt->customer_id = $sales->customer->id;
        $receipt->invoice_id = $invoice->id;
        $receipt->issue_date = date('Y-m-d');
        $receipt->status = 1;
        $receipt->paid_amount = $request->advance_amount;
        $receipt->payment_method = $request->advance_payment_method;
        $receipt->description = "Advance Payment";
        $receipt->created_by = Auth::user()->id;
        $receipt->save();

        $installmentAdvance = new InstallmentAdvance();
        $installmentAdvance->installment_id = $request->installment_id;
        $installmentAdvance->advance_amount = $request->advance_amount;
        $installmentAdvance->advance_payment_method = $request->advance_payment_method;
        $installmentAdvance->receipt_id = $receipt->id;
        $installmentAdvance->save();

        $cus = Customer::find($sales->customer->id);
        SMS::sendSMS($cus->phone_mobile, 'Dear Customer, Advance payment successfully paid!, Receipt Number ' . $receiptCode);

        return json_encode($receipt);
    }

    public function editAdvanceAmount(Request $request)
    {
        $installmentAdvance = InstallmentAdvance::find($request->installment_advance_id);
        $installmentAdvance->advance_amount = $request->advance_amount;
        $installmentAdvance->advance_payment_method = $request->advance_payment_method;
        $installmentAdvance->update();

        $installmentAdvances = InstallmentAdvance::where('installment_id', $installmentAdvance->installment_id)->where('status', 1)->get();
        return json_encode($installmentAdvances);
    }

    public function removeAdvanceAmount(Request $request)
    {
        $installmentAdvance = InstallmentAdvance::find($request->installment_advance_id);
        $installmentAdvance->status = 0;
        $installmentAdvance->save();
    }
}
