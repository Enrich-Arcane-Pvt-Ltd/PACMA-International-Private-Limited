@extends('layouts.admin')

@section('page-title')
{{ __('Bank Reconciliation Statements') }}
@endsection
@section('content')
<style>
    .print-only {
        display: none;
    }

    .headding {
        text-align: center;
    }

    @media print {
        .print-only {
            display: flex;
        }
    }

</style>
<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Bank Reconciliation Statements') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Bank Reconciliation') }}</div>
        <div class="breadcrumb-item">{{ __('Bank Reconciliation Statements') }}</div>
    </div>
    </div> --}}

    <div class="section-body">
        @if($showTable == 'Yes')
        <input type="hidden" name="rec_id" id="rec_id" value="{{ $reconciliation->id }}">
        <div class="row justify-content-center" id="printableArea">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Bank Reconciliation Statements') }}</h1>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Bank Reconciliation') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-body headding">
                        <h3>{{__('Bank Reconciliation Statement')}}</h3>
                        <h4>{{__('of ')}} {{config('app.name')}}</h4>
                        <h5>{{__('for the period 01st')}} {{ $reconciliation->start_date ?? '' }} {{' to '}}
                            {{ $reconciliation->end_date ?? '' }}</h5>
                        <h5>{{__('Account :')}} {{ $reconciliation->chartOfAccount->name ?? '' }} </h5>
                    </div>
                    <div class="card-body">
                        <div class="row print-only">
                            <div class="col-md-8" style="width: 75%"></div>
                            <div class="col-md-4" style="width: 25%; line-height: 25px; margin-top: -2rem;">
                                <span>Printed By : {{ Auth::user()->name }}</span> <br>
                                <span style="margin-left: 4.7rem;"> {{ Carbon\Carbon::now()->format('h:i a') }} </span>
                                <br>
                                <span style="margin-left: 4.7rem;">
                                    {{ Carbon\Carbon::now()->format('d / m / Y') }}</span>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-1" style="width:15%;"></div>
                            <div class="col-md-5" style="font-weight: bold; width:50%;"> CASH BOOK BALANCE AS AT
                                {{ $reconciliation->start_date ?? '' }}</div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> : </div>
                            <div class="col-md-4 text-right" style="font-weight: bold; width:15%;">
                                {{ number_format($reconciliation->start_balance, 2) }} </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12" style="font-weight: bold;">ADD :</div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-1" style="width:15%;"></div>
                            <div class="col-md-5" style="font-weight: bold; width:50%;"> BANKING IN THE PERIOD</div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> : </div>
                            <div class="col-md-4 text-right" style="font-weight: bold; width:15%;">
                                {{ number_format($reconciliation->add_in_period, 2) }} </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12" style="font-weight: bold;">LESS :</div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-1" style="width:15%;"></div>
                            <div class="col-md-5" style="font-weight: bold; width:50%;"> DRAWING IN THE PERIOD</div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> : </div>
                            <div class="col-md-4 text-right" style="font-weight: bold; width:15%;">
                                {{ number_format($reconciliation->less_in_period, 2) }} </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-1" style="width:15%;"></div>
                            <div class="col-md-5" style="font-weight: bold; width:50%;"> BALANCE AS PER CASH BOOK
                                {{ $reconciliation->end_date ?? '' }}</div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> : </div>
                            <div class="col-md-4 text-right"
                                style="font-weight: bold; width:15%; border-bottom: 4px double;">
                                {{ number_format($reconciliation->start_balance + $reconciliation->add_in_period - $reconciliation->less_in_period, 2) }}
                            </div>
                        </div>

                        {{-- ============================== Withdrawals Not Realised - start ================================ --}}
                        <div class="row mt-5">
                            <div class="col-md-12" style="font-weight: bold;">ADD : WITHDRAWALS NOT REALISED</div>
                        </div>
                        <div class="row mt-3 mb-1">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-2" style="font-weight: bold; width:15%;"> DATE</div>
                            <div class="col-md-2" style="font-weight: bold; width:15%;">TYPE </div>
                            <div class="col-md-2" style="font-weight: bold; width:20%;"> ACCOUNT </div>
                            <div class="col-md-1" style="font-weight: bold; width:10%;"> CHQUE NO. </div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> AMOUNT </div>
                            <div class="col-md-2" style="width:15%;"></div>
                        </div>
                        @foreach ($reconciliation->unCleareRecEntriesCredit as $unClearwithdraw)
                        <div class="row">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-2" style="width:15%;">{{ $unClearwithdraw->date }}</div>
                            <div class="col-md-2" style="width:15%;">{{ $unClearwithdraw->type->name ?? '' }}</div>
                            <div class="col-md-2" style="width:20%;">
                                {{ $unClearwithdraw->related_chart_of_account->name ?? '' }}</div>
                            <div class="col-md-1" style="width:10%;">
                                {{ $unClearwithdraw->paymentVouchers->cheque->cheque_number ?? 'N/A' }}</div>
                            <div class="col-md-2 text-right" style="width:15%;">{{ $unClearwithdraw->amount }}</div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;"></div>
                        </div>
                        @endforeach
                        <div class="row mt-1">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-2" style="width:15%;"></div>
                            <div class="col-md-2" style="width:15%;"></div>
                            <div class="col-md-2" style="width:20%;"></div>
                            <div class="col-md-1" style="width:10%;"></div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;">
                                {{ number_format($reconciliation->unCleareRecEntriesCredit->sum('amount'), 2) }}</div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;">
                                {{ number_format($reconciliation->unCleareRecEntriesCredit->sum('amount'), 2) }} </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-2" style="width:15%;"></div>
                            <div class="col-md-2" style="width:15%;"></div>
                            <div class="col-md-2" style="width:20%;"></div>
                            <div class="col-md-1" style="width:10%;"></div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;"></div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;">(
                                {{ number_format($reconciliation->end_balance + $reconciliation->unCleareRecEntriesDebit->sum('amount'), 2) }}
                                )</div>
                        </div>
                        {{-- ============================== Withdrawals Not Realised - end ================================ --}}

                        {{-- ============================== Deposits Not Realised - start ================================ --}}
                        <div class="row mt-5">
                            <div class="col-md-12" style="font-weight: bold;">LESS : DIPOSITS NOT REALISED</div>
                        </div>
                        <div class="row mt-3 mb-1">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-2" style="font-weight: bold; width:15%;"> DATE</div>
                            <div class="col-md-2" style="font-weight: bold; width:15%;">TYPE </div>
                            <div class="col-md-2" style="font-weight: bold; width:20%;"> ACCOUNT </div>
                            <div class="col-md-1" style="font-weight: bold; width:10%;"> CHQUE NO. </div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> AMOUNT </div>
                            <div class="col-md-2" style="width:15%;"></div>
                        </div>
                        @foreach ($reconciliation->unCleareRecEntriesDebit as $unClearDsit)
                        <div class="row">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-2" style="width:15%;">{{ $unClearDsit->date }}</div>
                            <div class="col-md-2" style="width:15%;">{{ $unClearDsit->type->name ?? '' }}</div>
                            <div class="col-md-2" style="width:20%;">
                                {{ $unClearDsit->related_chart_of_account->name ?? '' }}</div>
                            <div class="col-md-1" style="width:10%;">
                                @if (isset($unClearDsit->advancePayments))
                                {{ $unClearDsit->advancePayments->cheque_number ?? 'N/A' }}
                                @elseif (isset($unClearDsit->sales))
                                {{ $unClearDsit->sales->cheque_number ?? 'N/A' }}
                                @elseif (isset($unClearDsit->installments))
                                {{ $unClearDsit->installments->cheque_number ?? 'N/A' }}
                                @elseif (isset($unClearDsit->loanInstallments))
                                {{ $unClearDsit->loanInstallments->cheque_number ?? 'N/A' }}
                                @elseif (isset($unClearDsit->pawningInstallments))
                                {{ $unClearDsit->pawningInstallments->cheque_number ?? 'N/A' }}
                                @elseif (isset($unClearDsit->customReceipts))
                                {{ $unClearDsit->customReceipts->cheque_number ?? 'N/A' }}
                                @else
                                N/A
                                @endif
                            </div>
                            <div class="col-md-2 text-right" style="width:15%;">{{ $unClearDsit->amount }}</div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;"></div>
                        </div>
                        @endforeach
                        <div class="row mt-1">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-2" style="width:15%;"></div>
                            <div class="col-md-2" style="width:15%;"></div>
                            <div class="col-md-2" style="width:20%;"></div>
                            <div class="col-md-1" style="width:10%;"></div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;">
                                {{ number_format($reconciliation->unCleareRecEntriesDebit->sum('amount'), 2) }}</div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;">
                                {{ number_format($reconciliation->unCleareRecEntriesDebit->sum('amount'), 2) }} </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-2" style="width:15%;"></div>
                            <div class="col-md-2" style="width:15%;"></div>
                            <div class="col-md-2" style="width:20%;"></div>
                            <div class="col-md-1" style="width:10%;"></div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;"></div>
                            <div class="col-md-2 text-right" style="font-weight: bold; width:15%;">(
                                {{ number_format($reconciliation->end_balance, 2) }} )</div>
                        </div>
                        {{-- ============================== Deposits Not Realised - end ================================ --}}

                        <div class="row mt-5">
                            <div class="col-md-1" style="width:15%;"></div>
                            <div class="col-md-5" style="font-weight: bold; width:50%;"> BALANCE AS PER CASH BANK
                                STATEMENT AS AT {{ $end_date ?? '' }}</div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> : </div>
                            <div class="col-md-4 text-right"
                                style="font-weight: bold; width:15%; border-bottom: 4px double;  border-top: 2px solid;">
                                ( {{ number_format($reconciliation->end_balance, 2) }} ) </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-1" style="width:15%;"></div>
                            <div class="col-md-5" style="font-weight: bold; width:50%;"> BANK STATEMENT BALANCE
                                {{ $reconciliation->end_date ?? '' }}</div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> : </div>
                            <div class="col-md-4 text-right" style="font-weight: bold; width:15%;">
                                {{ number_format($reconciliation->statement_balance, 2) }}</div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-1" style="width:15%;"></div>
                            <div class="col-md-5" style="font-weight: bold; width:50%;"> DIFFERENECE BETWEEN CASH BOOK
                                AND BANK BALANCE</div>
                            <div class="col-md-2 text-center" style="font-weight: bold; width:15%;"> : </div>
                            <div class="col-md-4 text-right" style="font-weight: bold; width:15%;" id="difference">
                                {{ $reconciliation->difference }}</div>
                        </div>

                        <div class="row print-only" style="margin-top: 10rem;">
                            <div class="col-md-1" style="width:5%;"></div>
                            <div class="col-md-5" style="font-weight: bold; width:45%;"> PREPARED BY :
                                ............................................................................................
                            </div>
                            <div class="col-md-6" style="font-weight: bold; width:50%;"> ACCOUNTANT :
                                ............................................................................................
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-12">
                <center>
                    <button class="btn font-style btn-primary" type="button" onclick="print();"><i
                            class="fa fa-print"></i> Print</button>
                </center>
            </div>
        </div>
        @endif
    </div>
</section>


<script>
    function print() {
        $('#printableArea').printThis({
            importCSS: true,
            importStyle: true,
        });
    }

</script>

@endsection
