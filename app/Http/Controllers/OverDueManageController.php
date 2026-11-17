<?php

namespace App\Http\Controllers;

use App\Center;
use App\FineCenterCommand;
use App\FineChildCommand;
use App\FineCommand;
use App\FineRate;
use App\Installment;
use App\Loan;
use App\LoanInstallment;
use App\Product;
use App\Sale;
use App\Utility;
use Carbon\Carbon;

use Illuminate\Http\Request;

class OverDueManageController extends Controller
{
    public function applyFine(){

        $hireFineRate = FineRate::where('id', 1)->first();
        $loanFineRate = FineRate::where('id', 2)->first();

        $fineCommand = FineCommand::first();
        $fineChildCommands = FineChildCommand::get();
        $fineSub = [];
        foreach($fineChildCommands as $fineChildCommand){
            $fineSub[$fineChildCommand->id] = $fineChildCommand;
        }
        $fineCenterCommands = FineCenterCommand::get();
        $centers = [];
        foreach($fineCenterCommands as $fineCenterCommand){
            $centers[] = $fineCenterCommand->center_id;
        }

        $endDate = Carbon::today()->subDays(5)->format('Y-m-d');
        // $startDate = '2021-12-13';
        // $endDate = '2021-12-15';
        // $endDate = Carbon::today()->format('Y-m-d');
        $loanStartDate = '2021-04-01';

        if($fineCommand->fine_status == 1){

            // ================================== Hire Sales =================================>
            if($fineSub[1]->fine_status == 1){
                $startDate = $fineSub[1]->start_date;

                $sales = Sale::with(['installments'=> function($innerQuery) use($endDate, $startDate){
                                        $innerQuery->whereDate('installment_date', '<', $endDate)
                                                    ->whereDate('installment_date', '>=', $startDate)
                                                    ->where('status', 2 );
                                    }])
                                    ->whereHas('installments', function($query) use($endDate, $startDate) {
                                        $query->whereDate('installment_date', '<', $endDate)
                                                ->whereDate('installment_date', '>=', $startDate)
                                                ->where('status', 2 );
                                    })
                                    ->where('status', 2)
                                    ->where('sale_type', 1)
                                    ->get();
                foreach($sales as $sale){
                    $intallmentCount = count($sale->installments);
                    // if($intallmentCount > 1){
                    if($intallmentCount > 0){
                        // for ($i=0; $i < $intallmentCount-1; $i++) {
                        for ($i=0; $i < $intallmentCount; $i++) {
                            // $insDetail = Installment::find($sale->installments[$i]->id);
                            $insDetails = Installment::where('sale_id', $sale->id)->whereDate('installment_date', '>=', $startDate)->whereDate('installment_date', '<=', $sale->installments[$i]->installment_date)->whereIn('status', [2,3])->get();
                            $instFines = 0;
                            $excesAmounts = 0;
                            $remainAmounts = 0;
                            $instalmentAmount = 0;
                            foreach($insDetails as $insDetail){
                                $instFines += $insDetail->fine;
                                $excesAmounts += $insDetail->released_amount;
                                $remainAmounts += $insDetail->remainder_amount;
                                $instalmentAmount += $insDetail->amount;
                                // $fineAmount = $insDetail->amount * ($hireFineRate->fine_rate / 100);
                                // $totAmountToPay = $insDetail->total_amount_to_pay + $fineAmount;
                                // $insDetail->status = 3;
                                // // $insDetail->fine = $fineAmount;
                                // $insDetail->fine = $insDetail->fine + $fineAmount;
                                // $insDetail->total_amount_to_pay = $totAmountToPay;
                                // $insDetail->update();
                            }

                            $balanceAmount = $instalmentAmount + $remainAmounts - $excesAmounts;
                            $fineAmount = $balanceAmount * ($hireFineRate->fine_rate / 100);

                            $sale->installments[$i]->fine = $sale->installments[$i]->fine + $fineAmount;
                            $sale->installments[$i]->total_amount_to_pay = $sale->installments[$i]->fine + $sale->installments[$i]->amount + $sale->installments[$i]->remainder_amount - $sale->installments[$i]->released_amount;
                            $sale->installments[$i]->status = 3;
                            $sale->installments[$i]->update();
                        }
                        // $productDetail = Product::find($sales->product_id);

                        // $insDetails = Installment::where('sale_id', $sale->id)->whereDate('installment_date', '<', $endDate)->whereIn('status', [2,3])->get();
                        // $instFines = 0;
                        // $excesAmounts = 0;
                        // $remainAmounts = 0;
                        // $instalmentAmount = 0;
                        // foreach($insDetails as $insDetail){
                        //     $instFines += $insDetail->fine;
                        //     $excesAmounts += $insDetail->released_amount;
                        //     $remainAmounts += $insDetail->remainder_amount;
                        //     $instalmentAmount += $insDetail->amount;
                        //     $insDetail->fine_status = 1;
                        //     $insDetail->update();
                        // }
                        // $balanceAmount = $instalmentAmount + $instFines + $remainAmounts - $excesAmounts;
                        // $fineAmount = $balanceAmount * ($hireFineRate->fine_rate / 100);

                        // if($excesAmounts >= $fineAmount){
                        //     $excessRemain = $excesAmounts - $fineAmount;
                        //     $insDetails[0]->fine = null;
                        //     $insDetails[0]->released_amount = $excessRemain;
                        //     $insDetails[0]->total_amount_to_pay = $insDetails[0]->amount + $insDetails[0]->remainder_amount - $excessRemain;
                        //     $insDetails[0]->status = 2;
                        //     $insDetails[0]->update();

                        //     $amountBalanced = $fineAmount;
                        // } elseif($excesAmounts < $fineAmount){
                        //     $fineRemain = $fineAmount - $excesAmounts;
                        //     $insDetails[0]->fine = $fineRemain;
                        //     $insDetails[0]->released_amount = null;
                        //     $insDetails[0]->total_amount_to_pay = $fineRemain + $insDetails[0]->amount + $insDetails[0]->remainder_amount ;
                        //     $insDetails[0]->status = 3;
                        //     $insDetails[0]->update();

                        //     $amountBalanced = $excesAmounts;
                        // }

                        // if($excesAmounts > 0){
                        //     // if($productDetail->product_category_id == 1){
                        //         Utility::accountSalesInstallmentTransaction($endDate, 322, 323, $sale->id, $insDetails[0]->id, $sale->product_id, $sale->invoice_id, $sale->customer_id, 2,
                        //                                                     $amountBalanced, 'debit');
                        //         Utility::accountSalesInstallmentTransaction($endDate, 323, 322, $sale->id, $insDetails[0]->id, $sale->product_id, $sale->invoice_id, $sale->customer_id, 2,
                        //                                                     $amountBalanced, 'credit');
                        //     // } elseif($productDetail->product_category_id == 2){
                        //     //     Utility::accountSalesInstallmentTransaction($endDate, 183, 173, $sale->id, $insDetails[0]->id, $sale->product_id, $sale->invoice_id, $sale->customer_id, 2,
                        //     //                                                 $amountBalanced, 'debit');
                        //     //     Utility::accountSalesInstallmentTransaction($endDate, 173, 183, $sale->id, $insDetails[0]->id, $sale->product_id, $sale->invoice_id, $sale->customer_id, 2,
                        //     //                                                 $amountBalanced, 'credit');
                        //     // }
                        // }
                    }
                }
            }

            // ================================== SME Loans =================================>
            if($fineSub[2]->fine_status == 1){
                $startDate = $fineSub[2]->start_date;

                $loans = Loan::with(['loanInstallments'=> function($innerQuery) use($endDate, $startDate){
                                        $innerQuery->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                                    ->where('status', 2 );
                                    }])
                                ->whereHas('loanInstallments', function($query) use($endDate, $startDate) {
                                    $query->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                            ->where('status', 2 );
                                })
                                ->where('status', 2)
                                ->where('loan_type', 1)
                                ->where('date', '>', $loanStartDate)
                                ->get();

                foreach($loans as $loan){
                    if($loan->loan_type == 1){
                        $transType = 4;
                    } elseif($loan->loan_type == 2){
                        $transType = 5;
                    } elseif($loan->loan_type == 3){
                        $transType = 6;
                    } elseif($loan->loan_type == 4){
                        $transType = 7;
                    } elseif($loan->loan_type == 5){
                        $transType = 8;
                    }
                    $loanIntallmentCount = count($loan->loanInstallments);
                //     // if($loanIntallmentCount > 1){
                    if($loanIntallmentCount > 0){
                //         // for ($i=0; $i < $loanIntallmentCount-1; $i++){
                        for ($i=0; $i < $loanIntallmentCount; $i++){
                            // $insDetail = LoanInstallment::find($loan->loanInstallments[$i]->id);
                            $insDetails = LoanInstallment::where('loan_id', $loan->id)->whereDate('installment_date', '>=', $startDate)->whereDate('installment_date', '<=', $loan->loanInstallments[$i]->installment_date)->whereIn('status', [2,3])->get();
                            $instFines = 0;
                            $excesAmounts = 0;
                            $remainAmounts = 0;
                            $instalmentAmount = 0;
                            foreach($insDetails as $insDetail){
                                $instFines += $insDetail->fine;
                                $excesAmounts += $insDetail->released_amount;
                                $remainAmounts += $insDetail->remainder_amount;
                                $instalmentAmount += $insDetail->amount;

                                // $fineAmount = $insDetail->amount * ($loanFineRate->fine_rate / 100);
                                // $totAmountToPay = $insDetail->total_amount_to_pay + $fineAmount;
                                // $insDetail->status = 3;
                                // // $insDetail->fine = $fineAmount;
                                // $insDetail->fine =  $insDetail->fine + $fineAmount;
                                // $insDetail->total_amount_to_pay = $totAmountToPay;
                                // $insDetail->update();
                            }
                            $balanceAmount = $instalmentAmount + $remainAmounts - $excesAmounts;
                            $fineAmount = $balanceAmount * ($loanFineRate->fine_rate / 100);

                            $loan->loanInstallments[$i]->fine = $loan->loanInstallments[$i]->fine + $fineAmount;
                            $loan->loanInstallments[$i]->total_amount_to_pay = $loan->loanInstallments[$i]->fine + $loan->loanInstallments[$i]->amount + $loan->loanInstallments[$i]->remainder_amount - $loan->loanInstallments[$i]->released_amount;
                            $loan->loanInstallments[$i]->status = 3;
                            $loan->loanInstallments[$i]->update();
                        }

                        // $insDetails = LoanInstallment::where('loan_id', $loan->id)->whereDate('installment_date', '<', $endDate)->whereIn('status', [2,3])->get();
                        // $instFines = 0;
                        // $excesAmounts = 0;
                        // $remainAmounts = 0;
                        // $instalmentAmount = 0;
                        // foreach($insDetails as $insDetail){
                        //     $instFines += $insDetail->fine;
                        //     $excesAmounts += $insDetail->released_amount;
                        //     $remainAmounts += $insDetail->remainder_amount;
                        //     $instalmentAmount += $insDetail->amount;
                        //     $insDetail->fine_status = 1;
                        //     $insDetail->update();
                        // }

                        // $balanceAmount = $instalmentAmount + $instFines + $remainAmounts - $excesAmounts;
                        // $fineAmount = $balanceAmount * ($loanFineRate->fine_rate / 100);

                        // if($excesAmounts >= $fineAmount){
                        //     $excessRemain = $excesAmounts - $fineAmount;
                        //     $insDetails[0]->fine = null;
                        //     $insDetails[0]->released_amount = $excessRemain;
                        //     $insDetails[0]->total_amount_to_pay = $insDetails[0]->amount + $insDetails[0]->remainder_amount - $excessRemain;
                        //     $insDetails[0]->status = 2;
                        //     $insDetails[0]->update();

                        //     $amountBalanced = $fineAmount;
                        // } elseif($excesAmounts < $fineAmount){
                        //     $fineRemain = $fineAmount - $excesAmounts;
                        //     $insDetails[0]->fine = $fineRemain;
                        //     $insDetails[0]->released_amount = null;
                        //     $insDetails[0]->total_amount_to_pay = $fineRemain + $insDetails[0]->amount + $insDetails[0]->remainder_amount ;
                        //     $insDetails[0]->status = 3;
                        //     $insDetails[0]->update();

                        //     $amountBalanced = $excesAmounts;
                        // }

                        // if($excesAmounts > 0){
                        //     Utility::accountLoanInstallmentTransaction($endDate, 322, 323, $loan->id, $insDetails[0]->id, $loan->customer_id, $transType, $amountBalanced, 'debit');
                        //     Utility::accountLoanInstallmentTransaction($endDate, 323, 322, $loan->id, $insDetails[0]->id, $loan->customer_id, $transType, $amountBalanced, 'credit');
                        // }
                    }
                }
            }

            // ================================== Vehicle Loans =================================>
            if($fineSub[4]->fine_status == 1){
                $startDate = $fineSub[4]->start_date;

                $loans = Loan::with(['loanInstallments'=> function($innerQuery) use($endDate, $startDate){
                                        $innerQuery->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                                    ->where('status', 2 );
                                    }])
                                ->whereHas('loanInstallments', function($query) use($endDate, $startDate) {
                                    $query->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                            ->where('status', 2 );
                                })
                                ->where('status', 2)
                                ->where('loan_type', 3)
                                ->where('date', '>', $loanStartDate)
                                ->get();

                foreach($loans as $loan){
                    $loanIntallmentCount = count($loan->loanInstallments);
                    if($loanIntallmentCount > 0){
                        for ($i=0; $i < $loanIntallmentCount; $i++){
                            $insDetails = LoanInstallment::where('loan_id', $loan->id)->whereDate('installment_date', '>=', $startDate)->whereDate('installment_date', '<=', $loan->loanInstallments[$i]->installment_date)->whereIn('status', [2,3])->get();
                            $instFines = 0;
                            $excesAmounts = 0;
                            $remainAmounts = 0;
                            $instalmentAmount = 0;
                            foreach($insDetails as $insDetail){
                                $instFines += $insDetail->fine;
                                $excesAmounts += $insDetail->released_amount;
                                $remainAmounts += $insDetail->remainder_amount;
                                $instalmentAmount += $insDetail->amount;
                            }
                            $balanceAmount = $instalmentAmount + $remainAmounts - $excesAmounts;
                            $fineAmount = $balanceAmount * ($loanFineRate->fine_rate / 100);

                            $loan->loanInstallments[$i]->fine = $loan->loanInstallments[$i]->fine + $fineAmount;
                            $loan->loanInstallments[$i]->total_amount_to_pay = $loan->loanInstallments[$i]->fine + $loan->loanInstallments[$i]->amount + $loan->loanInstallments[$i]->remainder_amount - $loan->loanInstallments[$i]->released_amount;
                            $loan->loanInstallments[$i]->status = 3;
                            $loan->loanInstallments[$i]->update();
                        }
                    }
                }
            }

            // ================================== Land Loans =================================>
            if($fineSub[5]->fine_status == 1){
                $startDate = $fineSub[5]->start_date;

                $loans = Loan::with(['loanInstallments'=> function($innerQuery) use($endDate, $startDate){
                                        $innerQuery->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                                    ->where('status', 2 );
                                    }])
                                ->whereHas('loanInstallments', function($query) use($endDate, $startDate) {
                                    $query->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                            ->where('status', 2 );
                                })
                                ->where('status', 2)
                                ->where('loan_type', 4)
                                ->where('date', '>', $loanStartDate)
                                ->get();

                foreach($loans as $loan){
                    $loanIntallmentCount = count($loan->loanInstallments);
                    if($loanIntallmentCount > 0){
                        for ($i=0; $i < $loanIntallmentCount; $i++){
                            $insDetails = LoanInstallment::where('loan_id', $loan->id)->whereDate('installment_date', '>=', $startDate)->whereDate('installment_date', '<=', $loan->loanInstallments[$i]->installment_date)->whereIn('status', [2,3])->get();
                            $instFines = 0;
                            $excesAmounts = 0;
                            $remainAmounts = 0;
                            $instalmentAmount = 0;
                            foreach($insDetails as $insDetail){
                                $instFines += $insDetail->fine;
                                $excesAmounts += $insDetail->released_amount;
                                $remainAmounts += $insDetail->remainder_amount;
                                $instalmentAmount += $insDetail->amount;
                            }
                            $balanceAmount = $instalmentAmount + $remainAmounts - $excesAmounts;
                            $fineAmount = $balanceAmount * ($loanFineRate->fine_rate / 100);

                            $loan->loanInstallments[$i]->fine = $loan->loanInstallments[$i]->fine + $fineAmount;
                            $loan->loanInstallments[$i]->total_amount_to_pay = $loan->loanInstallments[$i]->fine + $loan->loanInstallments[$i]->amount + $loan->loanInstallments[$i]->remainder_amount - $loan->loanInstallments[$i]->released_amount;
                            $loan->loanInstallments[$i]->status = 3;
                            $loan->loanInstallments[$i]->update();
                        }
                    }
                }
            }

            // ================================== Employment Loans =================================>
            if($fineSub[6]->fine_status == 1){
                $startDate = $fineSub[6]->start_date;

                $loans = Loan::with(['loanInstallments'=> function($innerQuery) use($endDate, $startDate){
                                        $innerQuery->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                                    ->where('status', 2 );
                                    }])
                                ->whereHas('loanInstallments', function($query) use($endDate, $startDate) {
                                    $query->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                            ->where('status', 2 );
                                })
                                ->where('status', 2)
                                ->where('loan_type', 5)
                                ->where('date', '>', $loanStartDate)
                                ->get();

                foreach($loans as $loan){
                    $loanIntallmentCount = count($loan->loanInstallments);
                    if($loanIntallmentCount > 0){
                        for ($i=0; $i < $loanIntallmentCount; $i++){
                            $insDetails = LoanInstallment::where('loan_id', $loan->id)->whereDate('installment_date', '>=', $startDate)->whereDate('installment_date', '<=', $loan->loanInstallments[$i]->installment_date)->whereIn('status', [2,3])->get();
                            $instFines = 0;
                            $excesAmounts = 0;
                            $remainAmounts = 0;
                            $instalmentAmount = 0;
                            foreach($insDetails as $insDetail){
                                $instFines += $insDetail->fine;
                                $excesAmounts += $insDetail->released_amount;
                                $remainAmounts += $insDetail->remainder_amount;
                                $instalmentAmount += $insDetail->amount;
                            }
                            $balanceAmount = $instalmentAmount + $remainAmounts - $excesAmounts;
                            $fineAmount = $balanceAmount * ($loanFineRate->fine_rate / 100);

                            $loan->loanInstallments[$i]->fine = $loan->loanInstallments[$i]->fine + $fineAmount;
                            $loan->loanInstallments[$i]->total_amount_to_pay = $loan->loanInstallments[$i]->fine + $loan->loanInstallments[$i]->amount + $loan->loanInstallments[$i]->remainder_amount - $loan->loanInstallments[$i]->released_amount;
                            $loan->loanInstallments[$i]->status = 3;
                            $loan->loanInstallments[$i]->update();
                        }
                    }
                }
            }

            // ================================== Group Loans =================================>
            if($fineSub[3]->fine_status == 1){
                $startDate = $fineSub[3]->start_date;

                $loans = Loan::with(['loanInstallments'=> function($innerQuery) use($endDate, $startDate){
                                        $innerQuery->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                                    ->where('status', 2 );
                                    }])
                                ->whereHas('loanInstallments', function($query) use($endDate, $startDate) {
                                    $query->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $startDate)
                                            ->where('status', 2 );
                                })
                                ->where('status', 2)
                                ->where('loan_type', 2)
                                ->whereNotIn('center_id', $centers)
                                ->where('date', '>', $loanStartDate)
                                ->get();

                foreach($loans as $loan){
                    $loanIntallmentCount = count($loan->loanInstallments);
                    if($loanIntallmentCount > 0){
                        for ($i=0; $i < $loanIntallmentCount; $i++){
                            $insDetails = LoanInstallment::where('loan_id', $loan->id)->whereDate('installment_date', '>=', $startDate)->whereDate('installment_date', '<=', $loan->loanInstallments[$i]->installment_date)->whereIn('status', [2,3])->get();
                            $instFines = 0;
                            $excesAmounts = 0;
                            $remainAmounts = 0;
                            $instalmentAmount = 0;
                            foreach($insDetails as $insDetail){
                                $instFines += $insDetail->fine;
                                $excesAmounts += $insDetail->released_amount;
                                $remainAmounts += $insDetail->remainder_amount;
                                $instalmentAmount += $insDetail->amount;
                            }
                            $balanceAmount = $instalmentAmount + $remainAmounts - $excesAmounts;
                            $fineAmount = $balanceAmount * ($loanFineRate->fine_rate / 100);

                            $loan->loanInstallments[$i]->fine = $loan->loanInstallments[$i]->fine + $fineAmount;
                            $loan->loanInstallments[$i]->total_amount_to_pay = $loan->loanInstallments[$i]->fine + $loan->loanInstallments[$i]->amount + $loan->loanInstallments[$i]->remainder_amount - $loan->loanInstallments[$i]->released_amount;
                            $loan->loanInstallments[$i]->status = 3;
                            $loan->loanInstallments[$i]->update();
                        }
                    }
                }

                foreach($fineCenterCommands as $fineCenter){
                    if($fineCenter->fine_status == 1){
                        $centerStartDate = $fineCenter->start_date;
                        $centerLoans = Loan::with(['loanInstallments'=> function($innerQuery) use($endDate, $centerStartDate){
                                                $innerQuery->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $centerStartDate)
                                                            ->where('status', 2 );
                                            }])
                                        ->whereHas('loanInstallments', function($query) use($endDate, $centerStartDate) {
                                            $query->whereDate('installment_date', '<', $endDate)->whereDate('installment_date', '>=', $centerStartDate)
                                                    ->where('status', 2 );
                                        })
                                        ->where('status', 2)
                                        ->where('loan_type', 2)
                                        ->where('center_id', $fineCenter->center_id)
                                        ->where('date', '>', $loanStartDate)
                                        ->get();

                        foreach($centerLoans as $loan){
                            $loanIntallmentCount = count($loan->loanInstallments);
                            if($loanIntallmentCount > 0){
                                for ($i=0; $i < $loanIntallmentCount; $i++){
                                    $insDetails = LoanInstallment::where('loan_id', $loan->id)->whereDate('installment_date', '>=', $centerStartDate)->whereDate('installment_date', '<=', $loan->loanInstallments[$i]->installment_date)->whereIn('status', [2,3])->get();
                                    $instFines = 0;
                                    $excesAmounts = 0;
                                    $remainAmounts = 0;
                                    $instalmentAmount = 0;
                                    foreach($insDetails as $insDetail){
                                        $instFines += $insDetail->fine;
                                        $excesAmounts += $insDetail->released_amount;
                                        $remainAmounts += $insDetail->remainder_amount;
                                        $instalmentAmount += $insDetail->amount;
                                    }
                                    $balanceAmount = $instalmentAmount + $remainAmounts - $excesAmounts;
                                    $fineAmount = $balanceAmount * ($loanFineRate->fine_rate / 100);

                                    $loan->loanInstallments[$i]->fine = $loan->loanInstallments[$i]->fine + $fineAmount;
                                    $loan->loanInstallments[$i]->total_amount_to_pay = $loan->loanInstallments[$i]->fine + $loan->loanInstallments[$i]->amount + $loan->loanInstallments[$i]->remainder_amount - $loan->loanInstallments[$i]->released_amount;
                                    $loan->loanInstallments[$i]->status = 3;
                                    $loan->loanInstallments[$i]->update();
                                }
                            }
                        }
                    }
                }
            }

        }
    }

    public function removeFine(){
        $loanStartDate = '2021-04-01';
        $endDate = '2021-11-24';

            $sales = Sale::with(['installments'=> function($innerQuery) use($endDate){
                                    $innerQuery->whereDate('installment_date', '<=', $endDate)
                                                // ->whereDate('installment_date', '>=', $startDate)
                                                ->whereIn('status', [2,3] );
                                }])
                                ->whereHas('installments', function($query) use($endDate) {
                                    $query->whereDate('installment_date', '<=', $endDate)
                                            // ->whereDate('installment_date', '>=', $startDate)
                                            ->whereIn('status', [2,3] );
                                })
                                ->where('status', 2)
                                ->where('sale_type', 1)
                                ->get();

            foreach($sales as $sale){
                $intallmentCount = count($sale->installments);
                if($intallmentCount > 0){
                    foreach($sale->installments as $installment){
                        $installment->total_amount_to_pay = $installment->total_amount_to_pay - $installment->fine;
                        $installment->fine = null;
                        $installment->status = 2;
                        $installment->update();
                    }
                }
            }

            $loans = Loan::with(['loanInstallments'=> function($innerQuery) use($endDate){
                                    $innerQuery->whereDate('installment_date', '<', $endDate)
                                                // ->whereDate('installment_date', '>=', $startDate)
                                                ->whereIn('status', [2,3] );
                                }])
                            ->whereHas('loanInstallments', function($query) use($endDate) {
                                $query->whereDate('installment_date', '<', $endDate)
                                        // ->whereDate('installment_date', '>=', $startDate)
                                        ->whereIn('status', [2,3] );
                            })
                            ->where('status', 2)
                            // ->where('date', '>', $loanStartDate)
                            ->get();

            foreach($loans as $loan){
                $loanIntallmentCount = count($loan->loanInstallments);
                if($loanIntallmentCount > 0){
                    foreach($loan->loanInstallments as $loanInstallment){
                        $loanInstallment->total_amount_to_pay = $loanInstallment->total_amount_to_pay - $loanInstallment->fine;
                        $loanInstallment->fine = null;
                        $loanInstallment->status = 2;
                        $loanInstallment->update();
                    }
                }
            }
    }

    public function index(){
        if(\Auth::user()->can('manage penalty'))
        {
            $fineCommand = FineCommand::first();
            $fineChildCommands = FineChildCommand::get();
            $fineCenterCommands = FineCenterCommand::get();
            return view('fine_control.index', compact('fineCommand', 'fineChildCommands', 'fineCenterCommands'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function addCenter(){
        if(\Auth::user()->can('edit penalty'))
        {
            $query = Center::where('status', 1);
            $fineCenters = FineCenterCommand::get()->pluck('center_id');
            if(count($fineCenters) > 0){
                $query->whereNotIn('id', $fineCenters);
            }
            $centers = $query->get()->pluck('name', 'id');
            return view('fine_control.create_center', compact('centers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function createCenter(Request $request){
        if(\Auth::user()->can('edit penalty'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'center' => 'required',
                                    'start_date_center' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $fineCenters = new FineCenterCommand();
            $fineCenters->center_id = $request->center;
            $fineCenters->start_date = $request->start_date_center;
            if(isset($request->fine_status_center)){
                $fineCenters->fine_status = 1;
            } else {
                $fineCenters->fine_status = 0;
            }
            $fineCenters->created_by = \Auth::user()->id;
            $fineCenters->save();

            return redirect()->back()->with('success', __('Penalty Created For Center Successfully!.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function editCenter($id){
        if(\Auth::user()->can('edit penalty'))
        {
            $centers = Center::where('status', 1)->get()->pluck('name', 'id');
            $fineCenterCommand = FineCenterCommand::find($id);
            return view('fine_control.edit_center', compact('centers', 'fineCenterCommand'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateCenter(Request $request){
        if(\Auth::user()->can('edit penalty'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'start_date_center' => 'required',
                                ]
            );

            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $fineCenters = FineCenterCommand::find($request->fine_center_id);
            $fineCenters->start_date = $request->start_date_center;
            if(isset($request->fine_status_center)){
                $fineCenters->fine_status = 1;
            } else {
                $fineCenters->fine_status = 0;
            }
            $fineCenters->updated_by = \Auth::user()->id;
            $fineCenters->update();

            return redirect()->back()->with('success', __('Penalty Updated For Center Successfully!.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request){
        if(\Auth::user()->can('edit penalty'))
        {
            $fineCommand = FineCommand::first();
            if(isset($request->fine_status)){
                $fineCommand->fine_status = 1;
            } else if(!isset($request->fine_status)){
                $fineCommand->fine_status = 0;
            }
            $fineCommand->updated_by; \Auth::user()->id;
            $fineCommand->update();
            return redirect()->back()->with('success', __('Penalty Control Successfully Updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateChild(Request $request){
        if(\Auth::user()->can('edit penalty'))
        {
            $subFines = $request->fchild;
            foreach($subFines as $subFine){
                $fine = FineChildCommand::find($subFine['id']);
                $fine->start_date = $subFine['start_date'];
                if(isset($subFine['fine_status'])){
                    $fine->fine_status = 1;
                } else {
                    $fine->fine_status = 0;
                }
                $fine->updated_by = \Auth::user()->id;
                $fine->update();
            }
            return redirect()->back()->with('success', __('Penalty Control Successfully Updated !!!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
