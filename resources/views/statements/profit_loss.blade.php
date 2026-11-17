@extends('layouts.admin')

@section('page-title')
{{ __('Profit & Loss Statement') }}
@endsection
@section('content')
<style>
    table tfoot {
        display: table-row-group;
    }

    table thead {
        display: table-row-group;
    }

    .table td {
        height: 30px !important
    }

    .detail-link {
        color: #5180ea !important;
    }

    .detail-link:hover {
        text-decoration: underline !important;
        cursor: pointer !important;
    }

    #exampletb2 td,
    #example1 td {
        height: 0PX !important;
    }

    .dt-buttons.btn-group {
        margin-top: -50px;
    }

    @media print {
        .no-print {
            display: none;
        }

        .detail-link {
            color: inherit !important;
        }
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

    .dark-th {
        background-color: #cdcdcd !important;
    }

    .main-title {
        background-color: #f5f5f5;
        font-weight: bold;
    }

    .divider-title {
        font-weight: bold;
        border-top: 1px solid #eeeeee !important;
        border-bottom: 1px solid #eeeeee !important;
    }

    .sub-title {
        font-weight: bold;
        margin-top: 1rem;
    }

    .note-table-divider {
        border-bottom: 1px solid #eeeeee !important;
    }

</style>
<section class="section">
    <div class="se-pre-con"></div>
    {{-- <div class="section-header">
        <h1>{{ __('Profit & Loss Statement') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Financial Statement') }}</div>
        <div class="breadcrumb-item">{{ __('Profit & Loss Statement') }}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Profit & Loss Statement') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                <div class="breadcrumb-item">{{ __('Financial Statement') }}</div>
                                <div class="breadcrumb-item">{{ __('Profit & Loss Statement') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <form method="post" action="{{ route('profit_loss_summary.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h4 class="h4 mb-0">Filter</h4>
                                </div>
                                <div class="col-md-2">
                                    <label for="start_month">Start Date</label>
                                    <input class="form-control font-style" name="start_date" type="date"
                                        value="{{ $startDate ?? '' }}" id="start_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date">End Date</label>
                                    <input class="form-control font-style" name="end_date" type="date"
                                        value="{{ $endDate ?? '' }}" id="end_date" required>
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
                <div class="card br-4 cus_show" id="printableArea">

                    @if($showTable == 'Yes')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <center>
                                    <div class="col" id="headding">
                                        <h4>{{__('Profit & Loss Statement')}}</h4>
                                        <h5>{{__('of ')}} {{ config('app.name') }}</h5>
                                        <h6>{{__('for the Period From ')}} {{ $startDate ?? '' }} {{__(' To ')}}
                                            {{ $endDate ?? '' }}</h6>
                                        <input id="reportPeriodRange" type="text"
                                            value="{{__('for the Period From ')}} {{ $startDate ?? '' }} {{__(' To ')}} {{ $endDate ?? '' }}"
                                            hidden />
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">

                                <div class="row mt-2">
                                    <div class="col-sm-12">

                                        <table id="example1" class="display nowrap table table-flush"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-left dark-th"><b> {{ __('Details') }} </b></th>
                                                    <th class="text-center dark-th"><b> {{ __('Notes') }} </b></th>
                                                    <th class="text-right dark-th"><b> {{ __('Current Period') }} </b>
                                                    </th>
                                                    <th class="text-right dark-th"><b> {{ __('Cummelative') }} </b></th>
                                                </tr>
                                            </thead>

                                            @if($showTable == 'Yes')
                                            <tbody>
                                                @php
                                                $totalIncomeCurrentMonth = 0;
                                                $totalAllYearIncome = 0;

                                                $totalExpenseCurrentMonth = 0;
                                                $totalAllYearExpense = 0;
                                                @endphp

                                                <tr>
                                                    <td colspan="4" class="sub-title">Income</td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        Sales of Product Income
                                                    </td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($salesIncomeSelectedMonthTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($salesIncomeAllYearTransactions[0]->balance_amount, 2) }}
                                                    </td>

                                                    @php
                                                    $totalIncomeCurrentMonth +=
                                                    $salesIncomeSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearIncome +=
                                                    $salesIncomeAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>
                                                        Sales and service tax payable
                                                    </td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($salesTaxPayableSelectedMonthTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($salesTaxPayableAllYearTransactions[0]->balance_amount, 2) }}
                                                    </td>

                                                    @php
                                                    $totalIncomeCurrentMonth +=
                                                    $salesTaxPayableSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearIncome +=
                                                    $salesTaxPayableAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Stock Return</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        -{{ number_format($currentMonthStockReturnTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        -{{ number_format($allYearStockReturnTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    @php
                                                    $totalIncomeCurrentMonth -=
                                                    $currentMonthStockReturnTransactions[0]->balance_amount;
                                                    $totalAllYearIncome -=
                                                    $allYearStockReturnTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Loan Interest Income</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($loanInterestSelectedMonthTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($loanInterestAllYearTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    @php
                                                    $totalIncomeCurrentMonth +=
                                                    $loanInterestSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearIncome +=
                                                    $loanInterestAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Penalty Charges</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($penaltySelectedMonthTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($penaltyAllYearTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    @php
                                                    $totalIncomeCurrentMonth +=
                                                    $penaltySelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearIncome +=
                                                    $penaltyAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>D & L Charges</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($dnLChargesSelectedMonthTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($dnLChargesAllYearTransactions[0]->balance_amount, 2) }}
                                                    </td>

                                                    @php
                                                    $totalIncomeCurrentMonth +=
                                                    $dnLChargesSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearIncome +=
                                                    $dnLChargesAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td class="divider-title">Total Income</td>
                                                    <td class="divider-title"></td>
                                                    <td class="divider-title text-right">
                                                        {{ number_format($totalIncomeCurrentMonth, 2) }}</td>
                                                    <td class="divider-title text-right">
                                                        {{ number_format($totalAllYearIncome, 2) }}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4"></td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4" class="sub-title">Selling and Distribution Expenses
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        Discount Allowed
                                                    </td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($discountSelectedMonthTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($discountAllYearTransactions[0]->balance_amount, 2) }}
                                                    </td>

                                                    @php
                                                    $totalIncomeCurrentMonth +=
                                                    $discountSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearIncome +=
                                                    $discountAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>
                                                        Cost of Sales
                                                    </td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($cosSelectedMonthTransactions[0]->balance_amount, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($cosAllYearTransactions[0]->balance_amount, 2) }}
                                                    </td>

                                                    @php
                                                    $totalIncomeCurrentMonth +=
                                                    $cosSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearIncome += $cosAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td class="main-title">Gross Profit</td>
                                                    <td class="main-title"></td>
                                                    <td class="main-title text-right">
                                                        {{ number_format($totalIncomeCurrentMonth, 2) }}</td>
                                                    <td class="main-title text-right">
                                                        {{ number_format($totalAllYearIncome, 2) }}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4"></td>
                                                </tr>

                                                <tr>
                                                    <td class="sub-title" colspan="4">Finance and Other Expense</td>
                                                </tr>

                                                <tr>
                                                    <td>Commissions and fees</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($commissionsSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($commissionsAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $commissionsSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense +=
                                                    $commissionsAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Office Rent</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($rentSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($rentAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $rentSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense += $rentAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Other Expense</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($otherExpSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($otherExpAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $otherExpSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense +=
                                                    $otherExpAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Opening Ceremony Expenses</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($ceremonyExpSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($ceremonyExpAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $ceremonyExpSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense +=
                                                    $ceremonyExpAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Payroll Expenses</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($payrollSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($payrollAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $payrollSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense +=
                                                    $payrollAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Printing & Stationary</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($printingSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($printingAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $printingSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense +=
                                                    $printingAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Telephone Charges</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($phoneSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($phoneAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $phoneSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense +=
                                                    $phoneAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Traveling And Transport</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($travelSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($travelAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $travelSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense +=
                                                    $travelAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Fuel Allowance</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($fuelSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($fuelAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $fuelSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense += $fuelAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td>Vehicle Repair And Maintenance</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right">
                                                        {{ number_format($repairSelectedMonthTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($repairAllYearTransactions[0]->balance_amount*-1, 2) }}
                                                    </td>

                                                    @php
                                                    $totalExpenseCurrentMonth +=
                                                    $repairSelectedMonthTransactions[0]->balance_amount;
                                                    $totalAllYearExpense +=
                                                    $repairAllYearTransactions[0]->balance_amount;
                                                    @endphp
                                                </tr>

                                                <tr>
                                                    <td class="divider-title">Total Expense</td>
                                                    <td class="divider-title"></td>
                                                    <td class="divider-title text-right">
                                                        {{ number_format($totalExpenseCurrentMonth*-1, 2) }}</td>
                                                    <td class="divider-title text-right">
                                                        {{ number_format($totalAllYearExpense*-1, 2) }}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4"></td>
                                                </tr>

                                                <tr>
                                                    <td class="main-title">Net Profit / Lost For The Peroid</td>
                                                    <td class="main-title"></td>
                                                    <td class="main-title text-right">
                                                        {{ number_format($totalIncomeCurrentMonth + $totalExpenseCurrentMonth, 2) }}
                                                    </td>
                                                    <td class="main-title text-right">
                                                        {{ number_format($totalAllYearIncome + $totalAllYearExpense, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                            @endif

                                        </table>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-body mt-5">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">

                                <div class="row mt-2">
                                    <div class="col-sm-12">
                                        <h5>Notes</h5><br><br><br>

                                        <table id="exampletb2" class="display nowrap table table-flush"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-left dark-th"><b> {{ __('Note No.') }} </b></th>
                                                    <th class="text-left dark-th"><b> {{ __('Note & Details') }} </b>
                                                    </th>
                                                    <th class="text-right dark-th"><b> {{ __('Amount') }} </b></th>
                                                </tr>
                                            </thead>

                                            @if($showTable == 'Yes')
                                            <tbody>
                                                {{-- Sales --}}
                                                @if(Gate::check('show loss & profit'))
                                                @php
                                                $totalSalesAmmount = 0;
                                                @endphp

                                                @foreach ($saleDetails as $key => $saleDetail)
                                                <tr>
                                                    <td>@if ($loop->first) Sales @endif</td>
                                                    <td>{{ $saleDetail[0]['name'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($saleDetail[0]['balance_amount'], 2) }}</td>
                                                </tr>
                                                @php
                                                $totalSalesAmmount += $saleDetail[0]['balance_amount'];
                                                @endphp
                                                @endforeach

                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-right" style="font-weight: bold;">
                                                        {{ number_format($totalSalesAmmount, 2) }}</td>
                                                </tr>
                                                @else
                                                @foreach ($saleDetails as $key => $saleDetail)
                                                @php
                                                $totalSalesAmmount += $saleDetail[0]['balance_amount'];
                                                @endphp
                                                @endforeach

                                                <tr>
                                                    <td>Sales</td>
                                                    <td></td>
                                                    <td class="text-right" style="font-weight: bold;">
                                                        {{ number_format($totalSalesAmmount, 2) }}</td>
                                                </tr>
                                                @endif

                                                <tr>
                                                    <td colspan="3" class="note-table-divider"></td>
                                                </tr>

                                                {{-- Stock Returns --}}
                                                @if(Gate::check('show loss & profit'))
                                                @php
                                                $totalStockReturnAmmount = 0;
                                                @endphp

                                                @foreach ($stockReturnDetails as $stockReturnDetail)
                                                <tr>
                                                    <td>@if ($loop->first) Stock Returns @endif</td>
                                                    <td>{{ $stockReturnDetail[0]['name'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($stockReturnDetail[0]['balance_amount'], 2) }}
                                                    </td>
                                                </tr>
                                                @php
                                                $totalStockReturnAmmount += $stockReturnDetail[0]['balance_amount'];
                                                @endphp
                                                @endforeach

                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-right" style="font-weight: bold;">
                                                        {{ number_format($totalStockReturnAmmount, 2) }}</td>
                                                </tr>
                                                @else
                                                @php
                                                $totalStockReturnAmmount = 0;
                                                @endphp

                                                @foreach ($stockReturnDetails as $stockReturnDetail)
                                                @php
                                                $totalStockReturnAmmount += $stockReturnDetail[0]['balance_amount'];
                                                @endphp
                                                @endforeach

                                                <tr>
                                                    <td>Stock Returns</td>
                                                    <td></td>
                                                    <td class="text-right" style="font-weight: bold;">
                                                        {{ number_format($totalStockReturnAmmount, 2) }}</td>
                                                </tr>
                                                @endif

                                                <tr>
                                                    <td colspan="3" class="note-table-divider"></td>
                                                </tr>

                                                {{-- Other Income --}}
                                                @if(Gate::check('show loss & profit'))
                                                @php
                                                $totalOtherIncomeAmmount = 0;
                                                @endphp

                                                @foreach ($otherIncomeDetails as $otherIncomeDetail)
                                                <tr>
                                                    <td>@if ($loop->first) Other Income @endif</td>
                                                    <td>{{ $otherIncomeDetail[0]['name'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($otherIncomeDetail[0]['balance_amount'], 2) }}
                                                    </td>
                                                </tr>

                                                @php
                                                $totalOtherIncomeAmmount += $otherIncomeDetail[0]['balance_amount'];
                                                @endphp
                                                @endforeach

                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-right" style="font-weight: bold;">
                                                        {{ number_format($totalOtherIncomeAmmount, 2) }}</td>
                                                </tr>
                                                @else
                                                @php
                                                $totalOtherIncomeAmmount = 0;
                                                @endphp

                                                @foreach ($otherIncomeDetails as $otherIncomeDetail)
                                                @php
                                                $totalOtherIncomeAmmount += $otherIncomeDetail[0]['balance_amount'];
                                                @endphp
                                                @endforeach

                                                <tr>
                                                    <td>Other Income</td>
                                                    <td></td>
                                                    <td class="text-right" style="font-weight: bold;">
                                                        {{ number_format($totalOtherIncomeAmmount, 2) }}</td>
                                                </tr>
                                                @endif

                                                <tr>
                                                    <td colspan="3" class="note-table-divider"></td>
                                                </tr>

                                                {{-- Interest Income --}}
                                                @if(Gate::check('show loss & profit'))
                                                @php
                                                $totalInterestIncomeAmmount = 0;
                                                @endphp

                                                @foreach ($interestIncomeDetails as $interestIncomeDetail)
                                                <tr>
                                                    <td>@if ($loop->first) Interest Income @endif</td>
                                                    <td>{{ $interestIncomeDetail[0]['name'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($interestIncomeDetail[0]['balance_amount'], 2) }}
                                                    </td>
                                                </tr>
                                                @php
                                                $totalInterestIncomeAmmount +=
                                                $interestIncomeDetail[0]['balance_amount'];
                                                @endphp
                                                @endforeach

                                                <td></td>
                                                <td></td>
                                                <td class="text-right" style="font-weight: bold;">
                                                    {{ number_format($totalInterestIncomeAmmount, 2) }}</td>
                                                @else
                                                @php
                                                $totalInterestIncomeAmmount = 0;
                                                @endphp

                                                @foreach ($interestIncomeDetails as $interestIncomeDetail)
                                                @php
                                                $totalInterestIncomeAmmount +=
                                                $interestIncomeDetail[0]['balance_amount'];
                                                @endphp
                                                @endforeach

                                                <td>Interest Income</td>
                                                <td></td>
                                                <td class="text-right" style="font-weight: bold;">
                                                    {{ number_format($totalInterestIncomeAmmount, 2) }}</td>
                                                @endif
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
</section>

<script>
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');

        var filename = '';
        var duration = '';
        if ($('#headding').length > 0) {
            filename = '<center>' + $('#headding').html() + '<center>';
        }

        if ($('#duration').length > 0) {
            duration = $('#duration').val();
        }
    });

    $(document).ready(function () {
        var periodRange = document.getElementById('reportPeriodRange').value;

        $('#example1').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    title: 'Profit & Loss Statement of {{ config('
                    app.name ') }} ' + periodRange
                },
                {
                    extend: 'print',
                    title: 'Profit & Loss Statement of {{ config('
                    app.name ') }} ' + periodRange,
                    customize: function (win) {
                        $(win.document.body).find('h1').css('text-align', 'center');
                    }
                }
            ],
            paging: false,
            "bInfo": false,
            "order": false,
            "searching": false,
        });

        $('#exampletb2').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    title: 'Profit & Loss Statement of {{ config('
                    app.name ') }} ' + periodRange
                },
                {
                    extend: 'print',
                    title: 'Profit & Loss Statement of {{ config('
                    app.name ') }} ' + periodRange,
                    customize: function (win) {
                        $(win.document.body).find('h1').css('text-align', 'center');
                    }
                }
            ],
            paging: true,
            "bInfo": false,
            "order": false,
            "searching": false
        });
    });

    $('body').on('submit', function () {
        $(".se-pre-con").fadeIn('slow');
    });

    function print() {
        $('#printableArea').printThis({
            importCSS: true,
            importStyle: true,
        });
    }

</script>
@endsection
