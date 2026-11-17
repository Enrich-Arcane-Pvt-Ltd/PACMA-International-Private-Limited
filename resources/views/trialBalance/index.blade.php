@extends('layouts.admin')

@section('page-title')
    {{ __('Trial Balance') }}
@endsection
@section('content')
    <style>
        table tfoot {
            display: table-row-group;
        }

        .bordersTable {
            border-color: grey !important;
            border-style: solid;
            border-width: 1px;
        }

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
        <h1>{{ __('Trial Balance') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Financial Statements') }}</div>
        <div class="breadcrumb-item">{{ __('Trial Balance') }}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Trial Balance') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                    <div class="breadcrumb-item">{{ __('Financial Statements') }}</div>
                                    <div class="breadcrumb-item">{{ __('Trial Balance') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('trial_balance.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    {{-- <div class="col-md-2">
                                    <label for="start_month">Start Date</label>
                                    <input class="form-control font-style" name="start_date" type="date" value="{{ $startDate ?? '' }}"
                                id="start_date" required>
                            </div> --}}
                                    <div class="col-md-2">
                                        <label for="end_date">Date</label>
                                        <input class="form-control font-style" name="end_date" type="date"
                                            value="{{ $endDate ?? '' }}" id="end_date" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="zero_value">With Zero Value</label>
                                        <select name="zero_value" id="zero_value" class="form-control font-style" required>
                                            <option @if (isset($zeroValue) && $zeroValue == 'yes') selected @endif value="yes">Yes
                                            </option>
                                            <option @if (isset($zeroValue) && $zeroValue == 'no') selected @endif value="no">No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3 mb-3" style="float: right;">
                                    <div class="col-auto">
                                        <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                                    </div>
                                </div>
                                {{-- <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-2">
                                    <label for="account_id">Financial Year</label>
                                    <select name="year" id="year" class="form-control font-style selectric">
                                        @foreach ($yearList as $year)
                                            <option @if (isset($startYear)) @if ($startYear == $year) selected @endif @endif value="{{ $year }}">{{ $year }}
                        / {{ $year + 1 }}</option>
                        @endforeach
                        </select>
                </div>
                <div class="col-auto mt-4">
                    <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                </div>
            </div> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-wrap">
                            <div class="card-body">
                                <div class="row">
                                    @if ($showTable == 'Yes')
                                        <div class="col" id="headding">
                                            <h4>{{ __('Trial Balance') }}</h4>
                                            <h5>{{ __('of ') }} {{ config('app.name') }}</h5>
                                            <h6>{{ __('As at ') }} {{ $endDate ?? '' }}</h6>
                                            @if (isset($endDate))
                                                <input type="hidden" id="duration"
                                                    value="{{ __('As at ') . Auth::user()->dateFormat($endDate) }}">
                                            @endif
                                            {{-- <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}</h6> --}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('View Trial Balance') }}</h4>
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
                                                <table class="table table-flush" id="trial_balances_table">
                                                    <thead class="thead-light">
                                                        <tr style="text-align:center;">
                                                            {{-- <th> {{ __('Date') }}</th> --}}
                                                            <th style="width:40%;" rowspan="2">
                                                                {{ __('Chart of Account') }}
                                                            </th>
                                                            <th colspan="2"> {{ __('Current Period') }}</th>
                                                            {{-- <th colspan="2"> {{ __('Upto Selected Date') }}</th> --}}
                                                            <th colspan="2"> {{ __('Cummelative') }}</th>
                                                        </tr>
                                                        <tr>
                                                            {{-- <th> {{ __('Date') }}</th> --}}
                                                            <th style="text-align: right"> {{ __('Debit') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th style="text-align: right"> {{ __('Credit') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th style="text-align: right"> {{ __('Debit') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th style="text-align: right"> {{ __('Credit') }}
                                                                ({{ $currencySymbol }})</th>
                                                            {{-- <th style="text-align: right"> {{ __('Debit') }}</th>
                                                    <th style="text-align: right"> {{ __('Credit') }}</th> --}}
                                                        </tr>
                                                    </thead>


                                                    @if ($showTable == 'Yes')
                                                        @php
                                                            // $totalDebitcummulative = 0;
                                                            // $totalCreditcummulative = 0;

                                                            $totalDebitUptoSelected = 0;
                                                            $totalCreditUptoSelected = 0;

                                                            $totalDebitCurrentMonth = 0;
                                                            $totalCreditCurrentMonth = 0;
                                                        @endphp
                                                        {{-- @dd($trialbalances) --}}
                                                        <tbody>
                                                            @foreach ($trialbalances as $trialbalance)
                                                                @php
                                                                    // $debitcummulativeVal = 0;
                                                                    // $creditcummulativeVal = 0;

                                                                    $debitUptoSelectedVal = 0;
                                                                    $creditUptoSelectedVal = 0;

                                                                    $debitCurrentMonthVal = 0;
                                                                    $creditCurrentMonthVal = 0;

                                                                    // if($trialbalance['drcrcummulativeVal'] == "DR")
                                                                    // {
                                                                    // $debitcummulativeVal = $trialbalance['cummulativeVal'];
                                                                    // }
                                                                    // else if($trialbalance['drcrcummulativeVal'] == "CR")
                                                                    // {
                                                                    // $creditcummulativeVal = $trialbalance['cummulativeVal'];
                                                                    // }

                                                                    if ($trialbalance['drcrUptoSelectedVal'] == 'DR') {
                                                                        $debitUptoSelectedVal =
                                                                            $trialbalance['UptoSelectedVal'];
                                                                    } elseif (
                                                                        $trialbalance['drcrUptoSelectedVal'] == 'CR'
                                                                    ) {
                                                                        $creditUptoSelectedVal =
                                                                            $trialbalance['UptoSelectedVal'];
                                                                    }

                                                                    if ($trialbalance['drcrCurrentMonthVal'] == 'DR') {
                                                                        $debitCurrentMonthVal =
                                                                            $trialbalance['CurrentMonthVal'];
                                                                    } elseif (
                                                                        $trialbalance['drcrCurrentMonthVal'] == 'CR'
                                                                    ) {
                                                                        $creditCurrentMonthVal =
                                                                            $trialbalance['CurrentMonthVal'];
                                                                    }

                                                                @endphp
                                                                {{-- @if (($zeroValue == 'no' && (int) $trialbalance['cummulativeVal'] > 0) || (int) $trialbalance['UptoSelectedVal'] > 0 || (int) $trialbalance['CurrentMonthVal'] > 0) --}}
                                                                @if (($zeroValue == 'no' && (int) $trialbalance['UptoSelectedVal'] > 0) || (int) $trialbalance['CurrentMonthVal'] > 0)
                                                                    <tr>
                                                                        <td>{{ $trialbalance['name'] }}</td>

                                                                        <td class="text-right">
                                                                            {{ number_format($debitCurrentMonthVal, 2) }}
                                                                        </td>
                                                                        <td class="text-right">
                                                                            {{ number_format($creditCurrentMonthVal, 2) }}
                                                                        </td>

                                                                        <td class="text-right">
                                                                            {{ number_format($debitUptoSelectedVal, 2) }}
                                                                        </td>
                                                                        <td class="text-right">
                                                                            {{ number_format($creditUptoSelectedVal, 2) }}
                                                                        </td>

                                                                        {{-- <td class="text-right">{{ number_format($debitcummulativeVal, 2) }}
                                                    </td>
                                                    <td class="text-right">{{ number_format($creditcummulativeVal, 2) }}
                                                    </td> --}}

                                                                    </tr>
                                                                    @php
                                                                        // $totalDebitcummulative += $debitcummulativeVal;
                                                                        // $totalCreditcummulative += $creditcummulativeVal;

                                                                        $totalDebitUptoSelected += $debitUptoSelectedVal;
                                                                        $totalCreditUptoSelected += $creditUptoSelectedVal;

                                                                        $totalDebitCurrentMonth += $debitCurrentMonthVal;
                                                                        $totalCreditCurrentMonth += $creditCurrentMonthVal;
                                                                    @endphp
                                                                @elseif ($zeroValue == 'yes')
                                                                    <tr>
                                                                        <td>{{ $trialbalance['name'] }}</td>

                                                                        <td class="text-right">
                                                                            {{ number_format($debitCurrentMonthVal, 2) }}
                                                                        </td>
                                                                        <td class="text-right">
                                                                            {{ number_format($creditCurrentMonthVal, 2) }}
                                                                        </td>

                                                                        <td class="text-right">
                                                                            {{ number_format($debitUptoSelectedVal, 2) }}
                                                                        </td>
                                                                        <td class="text-right">
                                                                            {{ number_format($creditUptoSelectedVal, 2) }}
                                                                        </td>
                                                                        {{--
                                                                <td class="text-right">{{ number_format($debitcummulativeVal, 2) }}
                                                    </td>
                                                    <td class="text-right">{{ number_format($creditcummulativeVal, 2) }}
                                                    </td> --}}

                                                                    </tr>
                                                                    @php
                                                                        // $totalDebitcummulative += $debitcummulativeVal;
                                                                        // $totalCreditcummulative += $creditcummulativeVal;

                                                                        $totalDebitUptoSelected += $debitUptoSelectedVal;
                                                                        $totalCreditUptoSelected += $creditUptoSelectedVal;

                                                                        $totalDebitCurrentMonth += $debitCurrentMonthVal;
                                                                        $totalCreditCurrentMonth += $creditCurrentMonthVal;
                                                                    @endphp
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Total</th>
                                                                <th class="text-right">
                                                                    {{ number_format($totalDebitCurrentMonth, 2) }}</th>
                                                                <th class="text-right">
                                                                    {{ number_format($totalCreditCurrentMonth, 2) }}</th>

                                                                <th class="text-right">
                                                                    {{ number_format($totalDebitUptoSelected, 2) }}</th>
                                                                <th class="text-right">
                                                                    {{ number_format($totalCreditUptoSelected, 2) }}</th>

                                                                {{-- <th class="text-right">{{ number_format($totalDebitcummulative, 2) }}
                                                    </th>
                                                    <th class="text-right">
                                                        {{ number_format($totalCreditcummulative, 2) }}</th> --}}
                                                            </tr>
                                                        </tfoot>
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
        $(document).ready(function() {
            $(".se-pre-con").fadeOut('slow');

            var filename = ''
            var duration = ''
            if ($('#headding').length > 0) {
                filename = '<center>' + $('#headding').html() + '<center>';
            }
            if ($('#duration').length > 0) {
                duration = $('#duration').val();
            }
            if (localStorage.getItem("trial_balance_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("trial_balance_no"));
            }
            var trial_balancesTable = $('#trial_balances_table').DataTable({
                order: [
                    [0, "asc"]
                ],
                "pageLength": 25,
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: 'Trial Balance of {{ config('
                                                        app.name ') }} ' +
                        duration,
                    footer: true
                }, {
                    extend: 'print',
                    title: filename,
                    footer: true
                }, {
                    extend: 'csv',
                    title: 'Trial Balance of {{ config('
                                                        app.name ') }} ' +
                        duration,
                    footer: true
                }]
            });
            trial_balancesTable.page(pageNo).draw(false);

            $('#trial_balances_table').on('page.dt', function() {
                var pageInfo = trial_balancesTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("trial_balance_no", pageNo);
            });
        });

        // $('body').on('click', '.trial_balance_td', function(){
        // var trialBalanceId = $(this).closest('tr').attr("data-id");
        // window.location.href = "{{ url('trial_balances') }}/"+trialBalanceId;
        // });

        $('body').on('submit', function() {
            $(".se-pre-con").fadeIn('slow');
        });
    </script>

@endsection
