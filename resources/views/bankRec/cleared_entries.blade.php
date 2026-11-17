@extends('layouts.admin')

@section('page-title')
{{ __('Bank Reconciliation Cleared Entries') }}
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

</style>
<section class="section">
    <div class="se-pre-con"></div>
    {{-- <div class="section-header">
        <h1>{{ __('Bank Reconciliation Cleared Entries') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Bank Reconciliation') }}</div>
        <div class="breadcrumb-item">{{ __('Cleared Entries') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Bank Reconciliation Cleared Entries') }}</h1>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Bank Reconciliation') }}</div>
                                <div class="breadcrumb-item">{{ __('Cleared Entries') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('bank_rec.cleared_entries_search') }}">
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
                                    <input class="form-control font-style" name="start_date" type="date"
                                        value="{{ $start_date ?? '' }}" id="start_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="end_month">End Date</label>
                                    <input class="form-control font-style" name="end_date" type="date"
                                        value="{{ $end_date ?? '' }}" id="end_date" required>
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
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{ __('View Bank Reconciliation Entries') }}</h4>
                            <div class="card-header-action">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="bank_rec_entries_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{ __('Date') }}</th>
                                                        <th> {{ __('Type') }}</th>
                                                        <th> {{ __('Account') }}</th>
                                                        <th> {{ __('Diposits') }}</th>
                                                        <th> {{ __('Withdrawal') }}</th>
                                                    </tr>
                                                </thead>

                                                @if($showTable == 'Yes')
                                                <tbody>
                                                    @foreach ($bankRecs as $bankRec)
                                                    <tr>
                                                        <td class="trial_balance_td">{{ $bankRec['date'] }}</td>
                                                        <td class="trial_balance_td">{{ $bankRec->type->name}}</td>
                                                        <td class="trial_balance_td">
                                                            {{ $bankRec->related_chart_of_account->name}}</td>
                                                        <td class="trial_balance_td">@if($bankRec->debit_credit == 1)
                                                            {{ $bankRec['amount'] }} @endif</td>
                                                        <td class="trial_balance_td">@if($bankRec->debit_credit == 0)
                                                            {{ $bankRec['amount'] }} @endif</td>
                                                    </tr>
                                                    @endforeach
                                                    {{-- @foreach ($transactions as $transaction)
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" class="rec_check form-control" style="height: 25px;" name="checked_bank_rec[]" value="{{ $transaction->id }}">
                                                    </td>
                                                    <td class="trial_balance_td">{{ $transaction['date'] }}</td>
                                                    <td class="trial_balance_td">{{ $transaction->type->name}}</td>
                                                    <td class="trial_balance_td">
                                                        {{ $transaction->related_chart_of_account->name}}</td>
                                                    <td class="trial_balance_td">@if($transaction->debit_credit == 1)
                                                        {{ $transaction['amount'] }} @endif</td>
                                                    <td class="trial_balance_td">@if($transaction->debit_credit == 0)
                                                        {{ $transaction['amount'] }} @endif</td>
                                                    <td class="trial_balance_td"> <span
                                                            class="rec_status">Uncleared</span></td>
                                                    </tr>
                                                    @endforeach --}}
                                                </tbody>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        if (localStorage.getItem("bank_rec_cleared_entries_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("bank_rec_cleared_entries_no"));
        }
        var bank_rec_entriesTable = $('#bank_rec_entries_table').DataTable({
            order: [
                [0, "asc"]
            ],
            "pageLength": 50,
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: 'Bank Reconciliation Cleared Entries'
            }, {
                extend: 'print',
                title: 'Bank Reconciliation Cleared Entries'
            }, {
                extend: 'csv',
                title: 'Bank Reconciliation Cleared Entries'
            }]
        });
        bank_rec_entriesTable.page(pageNo).draw(false);

        $('#bank_rec_entries_table').on('page.dt', function () {
            var pageInfo = bank_rec_entriesTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("bank_rec_cleared_entries_no", pageNo);
        });
    });

</script>

@endsection
