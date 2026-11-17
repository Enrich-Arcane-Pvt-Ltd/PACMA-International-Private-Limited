@extends('layouts.admin')

@section('page-title')
{{ __('Purchase Return Journal') }}
@endsection
@section('content')
<style>
    table tfoot {
        display: table-row-group;
    }

</style>
<section class="section">
    {{-- <div class="section-header">
        <h1>{{ __('Purchase Return Journal') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
        <div class="breadcrumb-item">{{ __('Purchase Return Journal') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Purchase Return Journal') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Prime Entry Books') }}</div>
                                <div class="breadcrumb-item">{{ __('Purchase Return Journal') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('purchase_return_journal.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
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
                                {{-- <div class="col-md-3">
                                        <label for="end_month">End Month</label>
                                        <input class="form-control font-style" name="end_month" type="month" value="{{ $end_month ?? '' }}"
                                id="end_month" readonly>
                            </div> --}}
                            {{-- <div class="col-md-3">
                                        <label for="center_id">Center</label>
                                        @if (isset($repaymentCenters))
                                            @if ($repaymentCenters != '')
                                                <input type="hidden" id="center" value="{{ __(' Of ').App\Center::centerName($repaymentCenters)[0] }}">
                            @endif
                            @endif
                            {{ Form::select('center_id', $centers, $repaymentCenters ?? '', array('id' => 'center_id','class' => 'form-control font-style', 'required'=>'required')) }}
                        </div> --}}
                </div>
                <div class="row mt-3" style="float: right;">
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
                        <h4>{{ __('View Purchase Return Journal') }}</h4>
                        <div class="card-header-action">
                            {{-- @can('create account transaction')
                                <a href="#" data-size="xl" data-url="{{ route('accountTransaction.create') }}"
                            data-ajax-popup="true" data-title="{{ __('Create New Account Transaction') }}" class="btn
                            btn-icon icon-left btn-primary commonModal">
                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-inner--text"> {{ __('Create') }}</span>
                            </a>
                            @endcan --}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="purchase_return_journals_table">
                                            <thead class="thead-light">
                                                <tr>
                                                    {{-- <th> {{ __('Cash ID') }}</th> --}}
                                                    <th style="text-align: center"> {{ __('Date') }}</th>
                                                    <th style="text-align: center"> {{ __('Description') }}</th>
                                                    <th style="text-align: center"> {{ __('Debit') }}</th>
                                                    <th style="text-align: center"> {{ __('Credit') }}</th>
                                                </tr>
                                            </thead>

                                            @if($showTable == 'Yes')
                                            <tbody>
                                                @php
                                                $debitAmount = 0;
                                                $creditAmount = 0;
                                                @endphp
                                                @foreach ($transactions as $transaction)
                                                <tr>
                                                    {{-- <td> {{ AUth::user()->cashBookNumberFormat($transaction->id) }}
                                                    </td> --}}
                                                    <td class="purchase_return_journal_td" style="text-align: center">
                                                        {{ $transaction->date }}</td>
                                                    <td class="purchase_return_journal_td">
                                                        {{-- {{ $transaction->transactionTypes->name ?? '' }} --}}
                                                        @if($transaction->transaction_type_id == 1)
                                                        @if ($transaction->debit_credit == 1)
                                                        Sales Income
                                                        @elseif ($transaction->debit_credit == 0)
                                                        Bank
                                                        @endif
                                                        @elseif($transaction->transaction_type_id == 2)
                                                        @if ($transaction->debit_credit == 1)
                                                        Loan Stock
                                                        @elseif ($transaction->debit_credit == 0)
                                                        Bank
                                                        @endif
                                                        @endif
                                                    </td>
                                                    <td class="purchase_return_journal_td" style="text-align: right">
                                                        @if ($transaction->debit_credit == 1)
                                                        {{ $transaction->amount }}
                                                        @php
                                                        $debitAmount += $transaction->amount;
                                                        @endphp
                                                        @endif
                                                    </td>
                                                    <td class="purchase_return_journal_td" style="text-align: right">
                                                        @if ($transaction->debit_credit == 0)
                                                        {{ $transaction->amount }}
                                                        @php
                                                        $creditAmount += $transaction->amount;
                                                        @endphp
                                                        @endif
                                                    </td>
                                                </tr>
                                                {{-- @if ($transaction->debit_credit == 1)
                                                                    <tr>
                                                                        <td>{{ $transaction->date }}</td>
                                                <td>{{ $transaction->transactionTypes->name ?? '' }}</td>
                                                <td>{{ $transaction->amount }}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                </tr>
                                                @php
                                                $debitAmount += $transaction->amount;
                                                @endphp
                                                @elseif ($transaction->debit_credit == 0)
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ $transaction->date }}</td>
                                                    <td>{{ $transaction->transactionTypes->name ?? '' }}</td>
                                                    <td>{{ $transaction->amount }}</td>
                                                </tr>
                                                @php
                                                $creditAmount += $transaction->amount;
                                                @endphp
                                                @endif --}}
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th style="text-align: center">Total</th>
                                                    <th style="text-align: right">{{ number_format($debitAmount, 2) }}
                                                    </th>
                                                    <th style="text-align: right">{{ number_format($creditAmount, 2) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                            @endif
                                            {{-- </tbody> --}}
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

        var filename = 'Purchase Return Journal';
        if ($('#month').length > 0) {
            var duration = $('#month').val();
        } else {
            var duration = "";
        }

        if (localStorage.getItem("purchase_return_journal_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("purchase_return_journal_no"));
        }
        var purchase_return_journalsTable = $('#purchase_return_journals_table').DataTable({
            order: [
                [0, "asc"]
            ],
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excel',
                title: filename + ' Of ' + duration,
                footer: true
            }, {
                extend: 'print',
                title: function () {
                    return "<h3>" + filename + ' Of ' + duration + "</h3>";
                },
                footer: true
            }, {
                extend: 'csv',
                title: filename + ' Of ' + duration,
                footer: true
            }]
        });
        purchase_return_journalsTable.page(pageNo).draw(false);

        $('#purchase_return_journals_table').on('page.dt', function () {
            var pageInfo = purchase_return_journalsTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("purchase_return_journal_no", pageNo);
        });

    });

    $('body').on('click', '.purchase_return_journal_td', function () {
        var purchaseReturnJournalId = $(this).closest('tr').attr("data-id");
        window.location.href = "{{ url('purchase_return_journals') }}/" + purchaseReturnJournalId;
    });

</script>

@endsection
