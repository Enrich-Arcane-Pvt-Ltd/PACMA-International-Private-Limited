@extends('layouts.admin')

@section('page-title')
{{ __('Bank Reconciliation Entries') }}
@endsection
@section('content')
<style>
    .se-pre-con {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url("{{ asset('assets') }}/loadings/Spinner-1s-200px_blue.gif") center no-repeat #ffffff99;
    }

    .table:not(.table-sm):not(.table-md):not(.dataTable) td,
    .table:not(.table-sm):not(.table-md):not(.dataTable) th {
        padding: 5px 10px;
    }

    .table:not(.table-sm):not(.table-md):not(.dataTable) td,
    .table:not(.table-sm):not(.table-md):not(.dataTable) th {
        height: 48px;
    }

</style>
<section class="section">
    <div class="se-pre-con"></div>
    {{-- <div class="section-header">
        <h1>{{ __('Bank Reconciliation All Entries') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Bank Reconciliation') }}</div>
        <div class="breadcrumb-item">{{ __('All Entries') }}</div>
    </div>
    </div> --}}
    {{-- <div class="section-body"> --}}
    {{-- <div class="row">
            <div class="col-lg-12">
                <div class="card">
                        <form method="post" action="{{ route('bank_rec.entries_search') }}">
    @csrf
    <div class="card-body">
        <div class="row">
            <div class="col">
                <h4 class="h4 mb-0">Filter</h4>
            </div>
            <div class="col-md-2">
                <label for="account_id">Control Account</label>
                {{Form::select('account_id',$accounts, $chartOfAccount ?? '',array('class'=>'form-control customer-sel font-style','required'=>'required'))}}
            </div>
            <div class="col-md-2">
                <label for="start_month">Start Date</label>
                <input class="form-control font-style" name="start_date" type="date" value="{{ $start_date ?? '' }}"
                    id="start_date" required>
            </div>
            <div class="col-md-2">
                <label for="end_month">End Date</label>
                <input class="form-control font-style" name="end_date" type="date" value="{{ $end_date ?? '' }}"
                    id="end_date" required>
            </div>
        </div>
        <div class="row mt-3 mb-3" style="float: right;">
            <div class="col-auto">
                <input class="btn font-style btn-outline-primary" type="submit" value="Search">
            </div>
        </div>
    </div>
    </form>
    </div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Bank Reconciliation All Entries') }}</h1>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Bank Reconciliation') }}</div>
                                <div class="breadcrumb-item">{{ __('All Entries') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('View Bank Reconciliation Entries') }}</h4>
                            <div class="card-header-action">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <h6 class="col-md-5 text-right">Reconciliation Date Range</h6>
                            <h6 class="col-md-1 text-center">:</h6>
                            <h6 class="col-md-3 text-center">{{ $start_date }} - {{ $end_date }}</h6>
                        </div>
                        <div class="row">
                            <h6 class="col-md-5 text-right">CASH BOOK BALANCE AS AT {{ $start_date }}</h6>
                            <h6 class="col-md-1 text-center">:</h6>
                            <h6 class="col-md-3 text-right">{{ number_format($startDateBalance, 2) }}</h6>
                        </div>
                        <div class="row">
                            <h6 class="col-md-5 text-right">BALANCE AS PER BANK STATEMENT AS AT {{ $end_date }}</h6>
                            <h6 class="col-md-1 text-center">:</h6>
                            <h6 class="col-md-3 text-right">{{ number_format($statementBalance, 2) }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('bank-rec.store_entry') }}" method="post" id="reck_enties_form">
                            @csrf
                            <input type="hidden" name="account_id" value="{{ $chartOfAccount }}">
                            <input type="hidden" name="start_date" value="{{ $start_date }}">
                            <input type="hidden" name="end_date" value="{{ $end_date }}">
                            {{-- <input type="hidden" name="statement_balance" value="{{ $statementBalance }}"> --}}
                            <input type="hidden" name="start_balance" id="start_balance"
                                value="{{ $startDateBalance }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>CREDT</h5>
                                        </div>
                                        <div class="col-md-3"><button
                                                class="btn font-style btn-outline-info form-control" type="button"
                                                id="select_credit">Select All</button></div>
                                        <div class="col-md-3"><button
                                                class="btn font-style btn-outline-info form-control" type="button"
                                                id="deselect_credit">Deselect All</button></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>DEBIT</h5>
                                        </div>
                                        <div class="col-md-3"><button
                                                class="btn font-style btn-outline-info form-control" type="button"
                                                id="select_debit">Select All</button></div>
                                        <div class="col-md-3"><button
                                                class="btn font-style btn-outline-info form-control" type="button"
                                                id="deselect_debit">Deselect All</button></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-6" style="max-height: 40rem; overflow-y: scroll;">
                                    <table class="table table-flush" id="bank_rec_entries_table_credit">
                                        <thead class="thead-light">
                                            <tr>
                                                <th> {{ __('Present') }}</th>
                                                <th> {{ __('Date') }}</th>
                                                {{-- <th> {{ __('Type') }}</th> --}}
                                                <th> {{ __('Account') }}</th>
                                                <th> {{ __('Cheque No.') }}</th>
                                                <th> {{ __('Amount') }}</th>
                                                {{-- <th> {{ __('Status') }}</th> --}}
                                            </tr>
                                        </thead>

                                        @php
                                        $total_credit = 0;
                                        $total_debit = 0;
                                        @endphp

                                        @if($showTable == 'Yes')
                                        <tbody>
                                            {{-- @foreach ($bankRecs as $bankRec)
                                                    @if($bankRec->debit_credit == 0)
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="checked_bank_rec[{{ $bankRec->acccount_transaction_id }}][transaction_id]"
                                            value="{{ $bankRec->acccount_transaction_id }}">
                                            <input type="checkbox" class="rec_check_credit form-control"
                                                style="height: 20px;"
                                                name="checked_bank_rec[{{ $bankRec->acccount_transaction_id }}][rec_check]"
                                                value="{{ $bankRec->amount }}" checked>
                                            </td>
                                            <td class="trial_balance_td">{{ $bankRec['date'] }}</td>
                                            <td class="trial_balance_td">{{ $bankRec->related_chart_of_account->name}}
                                            </td>
                                            <td class="trial_balance_td">
                                                {{ $bankRec->accTransaction->paymentVouchers->cheque->cheque_number ?? 'N/A' }}
                                            </td>
                                            <td class="trial_balance_td text-right">
                                                {{ number_format($bankRec['amount'], 2) }} </td>
                                            </tr>
                                            @php
                                            $total_credit += $bankRec->amount;
                                            @endphp
                                            @endif
                                            @endforeach --}}
                                            @foreach ($transactions as $transaction)
                                            @if($transaction->debit_credit == 0)
                                            <tr>
                                                <td>
                                                    <input type="hidden"
                                                        name="checked_bank_rec[{{ $transaction->id }}][transaction_id]"
                                                        value="{{ $transaction->id }}">
                                                    <input type="checkbox" class="rec_check_credit form-control"
                                                        style="height: 20px;"
                                                        name="checked_bank_rec[{{ $transaction->id }}][rec_check]"
                                                        value="{{ $transaction->amount }}" checked>
                                                </td>
                                                <td class="trial_balance_td">{{ $transaction['date'] }}</td>
                                                {{-- <td class="trial_balance_td">{{ $transaction->type->name}}</td>
                                                --}}
                                                <td class="trial_balance_td">
                                                    {{ $transaction->related_chart_of_account->name ?? ''}}</td>
                                                <td class="trial_balance_td">
                                                    {{ $transaction->paymentVouchers->cheque->cheque_number ?? 'N/A' }}
                                                </td>
                                                <td class="trial_balance_td text-right">
                                                    {{ number_format($transaction['amount'], 2) }} </td>
                                                {{-- <td class="trial_balance_td"> <span class="rec_status">Uncleared</span></td> --}}
                                            </tr>
                                            @php
                                            $total_credit += $transaction->amount;
                                            @endphp
                                            @endif
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-sm-6" style="max-height: 40rem; overflow-y: scroll;">
                                    <table class="table table-flush" id="bank_rec_entries_table_debit">
                                        <thead class="thead-light">
                                            <tr>
                                                <th> {{ __('Present') }}</th>
                                                <th> {{ __('Date') }}</th>
                                                {{-- <th> {{ __('Type') }}</th> --}}
                                                <th> {{ __('Account') }}</th>
                                                <th> {{ __('Cheque No.') }}</th>
                                                <th> {{ __('Amount') }}</th>
                                                {{-- <th> {{ __('Status') }}</th> --}}
                                            </tr>
                                        </thead>

                                        @if($showTable == 'Yes')
                                        <tbody>
                                            {{-- @foreach ($bankRecs as $bankRec)
                                                    @if($bankRec->debit_credit == 1)
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="checked_bank_rec[{{ $bankRec->acccount_transaction_id }}][transaction_id]"
                                            value="{{ $bankRec->acccount_transaction_id }}">
                                            <input type="checkbox" class="rec_check_debit form-control"
                                                style="height: 20px;"
                                                name="checked_bank_rec[{{ $bankRec->acccount_transaction_id }}][rec_check]"
                                                value="{{ $bankRec->amount }}" checked>
                                            </td>
                                            <td class="trial_balance_td">{{ $bankRec['date'] }}</td>
                                            <td class="trial_balance_td">{{ $bankRec->related_chart_of_account->name}}
                                            </td>
                                            <td>
                                                @if (isset($bankRec->accTransaction) &&
                                                isset($bankRec->accTransaction->advancePayments))
                                                {{ $bankRec->accTransaction->advancePayments->cheque_number ?? 'N/A' }}

                                                @elseif (isset($bankRec->accTransaction) &&
                                                isset($bankRec->accTransaction->sales))
                                                {{ $bankRec->accTransaction->sales->cheque_number ?? 'N/A' }}

                                                @elseif (isset($bankRec->accTransaction) &&
                                                isset($bankRec->accTransaction->installments))
                                                {{ $bankRec->accTransaction->installments->cheque_number ?? 'N/A' }}

                                                @elseif (isset($bankRec->accTransaction) &&
                                                isset($bankRec->accTransaction->loanInstallments))
                                                {{ $bankRec->accTransaction->loanInstallments->cheque_number ?? 'N/A' }}

                                                @elseif (isset($bankRec->accTransaction) &&
                                                isset($bankRec->accTransaction->pawningInstallments))
                                                {{ $bankRec->accTransaction->pawningInstallments->cheque_number ?? 'N/A' }}

                                                @elseif (isset($bankRec->accTransaction) &&
                                                isset($bankRec->accTransaction->customReceipts))
                                                {{ $bankRec->accTransaction->customReceipts->cheque_number ?? 'N/A' }}
                                                @else
                                                N/A
                                                @endif
                                            </td>
                                            <td class="trial_balance_td text-right">
                                                {{ number_format($bankRec['amount'], 2) }} </td>
                                            </tr>
                                            @php
                                            $total_debit += $bankRec->amount;
                                            @endphp
                                            @endif
                                            @endforeach --}}
                                            @foreach ($transactions as $transaction)
                                            @if($transaction->debit_credit == 1)
                                            <tr>
                                                <td>
                                                    <input type="hidden"
                                                        name="checked_bank_rec[{{ $transaction->id }}][transaction_id]"
                                                        value="{{ $transaction->id }}">
                                                    <input type="checkbox" class="rec_check_debit form-control"
                                                        style="height: 20px;"
                                                        name="checked_bank_rec[{{ $transaction->id }}][rec_check]"
                                                        value="{{ $transaction->amount }}" checked>
                                                </td>
                                                <td class="trial_balance_td">{{ $transaction['date'] }}</td>
                                                <td class="trial_balance_td">
                                                    {{ $transaction->related_chart_of_account->name}}</td>
                                                <td>
                                                    @if (isset($transaction->advancePayments))
                                                    {{ $transaction->advancePayments->cheque_number ?? 'N/A' }}
                                                    @elseif (isset($transaction->sales))
                                                    {{ $transaction->sales->cheque_number ?? 'N/A' }}
                                                    @elseif (isset($transaction->installments))
                                                    {{ $transaction->installments->cheque_number ?? 'N/A' }}
                                                    @elseif (isset($transaction->loanInstallments))
                                                    {{ $transaction->loanInstallments->cheque_number ?? 'N/A' }}
                                                    @elseif (isset($transaction->pawningInstallments))
                                                    {{ $transaction->pawningInstallments->cheque_number ?? 'N/A' }}
                                                    @elseif (isset($transaction->customReceipts))
                                                    {{ $transaction->customReceipts->cheque_number ?? 'N/A' }}
                                                    @else
                                                    N/A
                                                    @endif
                                                </td>
                                                <td class="trial_balance_td text-right">
                                                    {{ number_format($transaction['amount'], 2) }}</td>
                                            </tr>
                                            @php
                                            $total_debit += $transaction->amount;
                                            @endphp
                                            @endif
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4"></div>
                                        <div class="col-sm-4"> <b>Total Amount</b> </div>
                                        <div class="col-sm-4 text-right" style="padding-right: 44px;"><input
                                                type="number" class="form-control text-right" name="total_credit"
                                                id="total_credit" value="{{ $total_credit }}" step="0.01"
                                                style="font-weight: bold;" readonly></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"></div>
                                        <div class="col-sm-4"> <b>Cleared Amount</b> </div>
                                        <div class="col-sm-4 text-right" style="padding-right: 44px;"><input
                                                type="number" class="form-control text-right" name="cleared_credit"
                                                id="cleared_credit" value="0" step="0.01" style="font-weight: bold;"
                                                readonly></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"></div>
                                        <div class="col-sm-4"> <b>Uncleared Amount</b> </div>
                                        <div class="col-sm-4 text-right" style="padding-right: 44px;"><input
                                                type="number" class="form-control text-right" name="uncleared_credit"
                                                id="uncleared_credit" value="0" step="0.01" style="font-weight: bold;"
                                                readonly></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4"></div>
                                        <div class="col-sm-4"> <b>Total Amount</b> </div>
                                        <div class="col-sm-4 text-right" style="padding-right: 44px;"> <input
                                                type="number" class="form-control text-right" name="total_debit"
                                                id="total_debit" value="{{ $total_debit }}" step="0.01"
                                                style="font-weight: bold;" readonly></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"></div>
                                        <div class="col-sm-4"> <b>Cleared Amount</b> </div>
                                        <div class="col-sm-4 text-right" style="padding-right: 44px;"><input
                                                type="number" class="form-control text-right" name="cleared_debit"
                                                id="cleared_debit" value="0" step="0.01" style="font-weight: bold;"
                                                readonly></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"></div>
                                        <div class="col-sm-4"> <b>Uncleared Amount</b> </div>
                                        <div class="col-sm-4 text-right" style="padding-right: 44px;"><input
                                                type="number" class="form-control text-right" name="uncleared_debit"
                                                id="uncleared_debit" value="0" step="0.01" style="font-weight: bold;"
                                                readonly></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h6>Present</h6>
                                        </div>
                                        <div class="col-sm-1 text-center">
                                            <h6>:</h6>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <h6><span id="credit_presents">0</span></h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h6>Unpresent</h6>
                                        </div>
                                        <div class="col-sm-1 text-center">
                                            <h6>:</h6>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <h6><span id="credit_unpresents">0</span></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h6>Present</h6>
                                        </div>
                                        <div class="col-sm-1 text-center">
                                            <h6>:</h6>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <h6><span id="debit_presents">0</span></h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h6>Unpresent</h6>
                                        </div>
                                        <div class="col-sm-1 text-center">
                                            <h6>:</h6>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <h6><span id="debit_unpresents">0</span></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-md-5 text-right">
                                    <h6> BALANCE AS PER BANK STATEMENT AS AT {{ $end_date }} </h6>
                                </div>
                                <div class="col-md-1 text-center">
                                    <h6> : </h6>
                                </div>
                                <div class="col-md-3 text-right"> <input type="number" class="form-control text-right"
                                        name="statement_balance" id="statement_balance" value="{{ $statementBalance }}"
                                        style="font-weight: bold;" readonly> </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-5 text-right">
                                    <h6> CASH BOOK BALANCE AS AT {{ $end_date }} </h6>
                                </div>
                                <div class="col-md-1 text-center">
                                    <h6> : </h6>
                                </div>
                                <div class="col-md-3 text-right"> <input type="number" class="form-control text-right"
                                        name="end_balance" id="end_balance"
                                        value="{{ $startDateBalance + $total_debit - $total_credit }}"
                                        style="font-weight: bold;" readonly> </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-5 text-right">
                                    <h6> Difference </h6>
                                </div>
                                <div class="col-md-1 text-center">
                                    <h6> : </h6>
                                </div>
                                <div class="col-md-3 text-right"> <input type="number" class="form-control text-right"
                                        name="diference" id="diference"
                                        value="{{ $startDateBalance + $total_debit - $total_credit - $statementBalance}}"
                                        style="font-weight: bold;" readonly> </div>
                            </div>

                            <div class="row mt-3 mb-3 justify-content-center">
                                <div class="col-md-3">
                                    <input class="form-control btn font-style btn-primary" type="submit" value="Save">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(document).ready(function () {
        sortTable_credit();
        sortTable_debit();
        check_cal_credit();
        check_cal_debit();
        $(".se-pre-con").fadeOut('slow');

        $('#reck_enties_form').on('submit', function (ev) {
            ev.preventDefault();
            if ($('.rec_check_credit').length > 0) {
                this.submit();
            }
        });
        // if(localStorage.getItem("bank_rec_entries_no") == null){
        //     var pageNo = 0;
        // } else {
        //     var pageNo = Number(localStorage.getItem("bank_rec_entries_no"));
        // }
        // var bank_rec_entriesTable = $('#bank_rec_entries_table').DataTable({
        //     order:[[1,"asc"]],
        //     "pageLength": 50
        //     });
        // bank_rec_entriesTable.page(pageNo).draw(false);

        // $('#bank_rec_entries_table').on( 'page.dt', function () {
        //     var pageInfo =  bank_rec_entriesTable.page.info();
        //     pageNo = pageInfo.page;
        //     localStorage.setItem("bank_rec_entries_no", pageNo);
        // });
    });

    $('body').on('click', '#select_credit', function () {
        $('.rec_check_credit').prop('checked', true);
        check_cal_credit();
    });
    $('body').on('click', '#deselect_credit', function () {
        $('.rec_check_credit').prop('checked', false);
        check_cal_credit();
    });

    $('body').on('click', '#select_debit', function () {
        $('.rec_check_debit').prop('checked', true);
        check_cal_debit();
    });
    $('body').on('click', '#deselect_debit', function () {
        $('.rec_check_debit').prop('checked', false);
        check_cal_debit();
    });

    $('body').on('click', '.rec_check_credit', function () {
        check_cal_credit();
    });

    $('body').on('click', '.rec_check_debit', function () {
        check_cal_debit();
    });

    function check_cal_credit() {
        var allCount = $('.rec_check_credit').length;
        var checkedEnties = $('.rec_check_credit:checked');
        var unCheckedEnties = $('.rec_check_credit:not(:checked)');
        var checkedCount = checkedEnties.length;
        var uncheckedCount = allCount - checkedCount;
        // var totalCredit = 0;
        var clearedCredit = 0;
        var unClearedCredit = 0;
        for (var i = 0; i < checkedEnties.length; i++) {
            clearedCredit += parseFloat(checkedEnties[i].value);
        }
        for (var j = 0; j < unCheckedEnties.length; j++) {
            unClearedCredit += parseFloat(unCheckedEnties[j].value);
        }
        $('#credit_presents').html(checkedCount);
        $('#credit_unpresents').html(uncheckedCount);
        // $('#total_credit').val(totalCredit.toFixed(2));
        $('#cleared_credit').val(clearedCredit.toFixed(2));
        $('#uncleared_credit').val(unClearedCredit.toFixed(2));
        finalCal();
    }

    function check_cal_debit() {
        var allCount = $('.rec_check_debit').length;
        var checkedEnties = $('.rec_check_debit:checked');
        var unCheckedEnties = $('.rec_check_debit:not(:checked)');
        var checkedCount = checkedEnties.length;
        var uncheckedCount = allCount - checkedCount;
        var totalDebit = 0;
        var clearedDebit = 0;
        var unClearedDebit = 0;
        for (var i = 0; i < checkedEnties.length; i++) {
            clearedDebit += parseFloat(checkedEnties[i].value);
        }
        for (var j = 0; j < unCheckedEnties.length; j++) {
            unClearedDebit += parseFloat(unCheckedEnties[j].value);
        }
        $('#debit_presents').html(checkedCount);
        $('#debit_unpresents').html(uncheckedCount);
        $('#cleared_debit').val(clearedDebit.toFixed(2));
        $('#uncleared_debit').val(unClearedDebit.toFixed(2));
        finalCal();
    }

    function finalCal() {
        var startBalance = $('#start_balance').val();
        var statementBalance = $('#statement_balance').val();
        var totalDebit = $('#total_debit').val();
        var totalCredit = $('#total_credit').val();
        var unClearedDebit = $('#uncleared_debit').val();
        var unClearedCredit = $('#uncleared_credit').val();

        var endBalance = parseFloat(startBalance) + parseFloat(totalDebit) + parseFloat(unClearedCredit) - parseFloat(
            totalCredit) - parseFloat(unClearedDebit);
        // var endBalance = parseFloat(startBalance) + parseFloat(totalDebit) - parseFloat(totalCredit);
        $('#end_balance').val(endBalance.toFixed(2));
        $('#diference').val((endBalance - parseFloat(statementBalance)).toFixed(2));
    }

    function sortTable_credit() {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById("bank_rec_entries_table_credit");
        switching = true;
        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[1];
                y = rows[i + 1].getElementsByTagName("TD")[1];
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }

    function sortTable_debit() {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById("bank_rec_entries_table_debit");
        switching = true;
        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[1];
                y = rows[i + 1].getElementsByTagName("TD")[1];
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }

    // $('body').on('click', '.rec_check', function(){
    //     var recCheck = $(this);
    //     var row = $(this).closest('tr');

    //     if(recCheck.prop('checked') == true){
    //         $.ajax({
    //             url:"{{ url('bank-rec/check_entry') }}/"+recCheck.val(),
    //             method:'GET',
    //             dataType: 'json',
    //             beforeSend:function(){
    //                 $(".se-pre-con").fadeIn();
    //             },
    //             success:function(data, textStatus, jqXHR){
    //                 $(".se-pre-con").fadeOut();
    //                 if(data.response == 'success'){
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: 'Success',
    //                         text: 'The Bank Entry Cleared Successfully!',
    //                         timer: 1300,
    //                         showConfirmButton: false
    //                     });

    //                     recCheck.val(data.id);
    //                     row.find('.rec_status').html('Cleared');

    //                 } else if(data.response == 'error'){
    //                     Swal.fire({
    //                         icon: 'error',
    //                         title: 'Oops...',
    //                         text: 'Something Went Wrong!'
    //                     });
    //                     recCheck.prop('checked', false);
    //                     row.find('.rec_status').html('Uncleared');
    //                 }
    //             }
    //         });
    //     } else if(recCheck.prop('checked') == false){
    //         Swal.fire({
    //             icon: 'warning',
    //             title: 'Are You Sure?',
    //             text: 'Do you need to unclear this entry?',
    //             showCancelButton: true,
    //             showConfirmButton: true,
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 $.ajax({
    //                     url:"{{ url('bank-rec/uncheck_entry') }}/"+recCheck.val(),
    //                     method:'GET',
    //                     dataType: 'json',
    //                     beforeSend:function(){
    //                         $(".se-pre-con").fadeIn();
    //                     },
    //                     success:function(data, textStatus, jqXHR){
    //                         $(".se-pre-con").fadeOut();
    //                         if(data.response == 'success'){
    //                             Swal.fire({
    //                                 icon: 'success',
    //                                 title: 'Success',
    //                                 text: 'The Bank Entry Uncleared Successfully!',
    //                                 timer: 1300,
    //                                 showConfirmButton: false
    //                             });

    //                             recCheck.val(data.id);
    //                             row.find('.rec_status').html('Uncleared');

    //                         } else if(data.response == 'error'){
    //                             Swal.fire({
    //                                 icon: 'error',
    //                                 title: 'Oops...',
    //                                 text: 'Something Went Wrong!'
    //                             });
    //                             recCheck.prop('checked', true);
    //                             row.find('.rec_status').html('Cleared');
    //                         }
    //                     }
    //                 });
    //             } else {
    //                 recCheck.prop('checked', true);
    //             }
    //         });
    //     }
    // });

</script>

@endsection
