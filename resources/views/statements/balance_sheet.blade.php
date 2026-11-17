@extends('layouts.admin')

@section('page-title')
    {{ __('Balance Sheet') }}
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

        .final-total {
            border-bottom-style: double;
            border-top: 1px solid !important;
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

        .dark-th {
            background-color: #cdcdcd !important;
        }

        .divider-title {
            font-weight: bold;
            border-top: 1px solid #eeeeee !important;
            border-bottom: 1px solid #eeeeee !important;
        }

        .table td {
            padding: .75rem !important;
        }
    </style>
    <section class="section">
        <div class="se-pre-con"></div>
        {{-- <div class="section-header">
        <h1>{{ __('Balance Sheet') }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
        <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
        <div class="breadcrumb-item">{{ __('Financial Statement') }}</div>
        <div class="breadcrumb-item">{{ __('Balance Sheet') }}</div>
    </div>
    </div> --}}

        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Balance Sheet') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Accounting System') }}</div>
                                    <div class="breadcrumb-item">{{ __('Financial Statement') }}</div>
                                    <div class="breadcrumb-item">{{ __('Balance Sheet') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('balance_sheet_summary.search') }}">
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
                        @if ($showTable == 'Yes')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <center>
                                            <div class="col" id="headding">
                                                <h4>{{ __('Balance Sheet') }}</h4>
                                                <h5>{{ __('of ') }} {{ config('app.name') }}</h5>
                                                <h6>{{ __('for the Period From ') }} {{ $startDate ?? '' }}
                                                    {{ __(' To ') }}
                                                    {{ $endDate ?? '' }}</h6>
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
                                    @if ($showTable == 'Yes')
                                        <div class="row no-print">
                                            <div class="col-md-12">
                                                <button class="btn font-style btn-primary" type="button"
                                                    onclick="print();"><i class="fa fa-print"></i> Print</button>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row mt-2">
                                        <div class="col-sm-12">
                                            <table class="display nowrap table table-flush" id="balance_sheet_table">
                                                <thead class="thead-light ">
                                                    <tr>
                                                        <th class="text-left dark-th" colspan="3">{{ __('Details') }}
                                                        </th>
                                                        <th class="text-right dark-th">{{ __('Amount') }}
                                                            ({{ $currencySymbol }})</th>
                                                    </tr>
                                                </thead>

                                                @if ($showTable == 'Yes')
                                                    @php
                                                        $ppe = 0;
                                                        $currentAssets = 0;
                                                        $totalAssets = 0;
                                                    @endphp

                                                    <tbody>
                                                        <tr>
                                                            <td class="sub-title">Non Current Assets</td>
                                                            <td class="sub-title text-right">Value</td>
                                                            <td class="sub-title text-right">Provision for Depreciation</td>
                                                            <td class="sub-title"></td>
                                                        </tr>

                                                        {{-- @if ($allAccounts[125][0]['debit_amount'] - $allAccounts[125][0]['credit_amount'] - $allAccounts[136][0]['credit_amount'] + $allAccounts[136][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Land</td>
                                                            <td class="text-right">{{ number_format($allAccounts[125][0]['debit_amount'] - $allAccounts[125][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[136][0]['credit_amount'] - $allAccounts[136][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[125][0]['debit_amount'] - $allAccounts[125][0]['credit_amount'] -  $allAccounts[136][0]['credit_amount'] + $allAccounts[136][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[125][0]['debit_amount'] -
                                                $allAccounts[125][0]['credit_amount'] -
                                                $allAccounts[136][0]['credit_amount'] +
                                                $allAccounts[136][0]['debit_amount'];
                                                $totalAssets += $allAccounts[125][0]['debit_amount'] -
                                                $allAccounts[125][0]['credit_amount'] -
                                                $allAccounts[136][0]['credit_amount'] +
                                                $allAccounts[136][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[126][0]['debit_amount'] - $allAccounts[126][0]['credit_amount'] - $allAccounts[137][0]['credit_amount'] + $allAccounts[137][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Building</td>
                                                            <td class="text-right">{{ number_format($allAccounts[126][0]['debit_amount'] - $allAccounts[126][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[137][0]['credit_amount'] - $allAccounts[137][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[126][0]['debit_amount'] - $allAccounts[126][0]['credit_amount'] -  $allAccounts[137][0]['credit_amount'] + $allAccounts[137][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[126][0]['debit_amount'] -
                                                $allAccounts[126][0]['credit_amount'] -
                                                $allAccounts[137][0]['credit_amount'] +
                                                $allAccounts[137][0]['debit_amount'];
                                                $totalAssets += $allAccounts[126][0]['debit_amount'] -
                                                $allAccounts[126][0]['credit_amount'] -
                                                $allAccounts[137][0]['credit_amount'] +
                                                $allAccounts[137][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[127][0]['debit_amount'] - $allAccounts[127][0]['credit_amount'] - $allAccounts[138][0]['credit_amount'] + $allAccounts[138][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Motor Vehicles</td>
                                                            <td class="text-right">{{ number_format($allAccounts[127][0]['debit_amount'] - $allAccounts[127][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[138][0]['credit_amount'] - $allAccounts[138][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[127][0]['debit_amount'] - $allAccounts[127][0]['credit_amount'] -  $allAccounts[138][0]['credit_amount'] + $allAccounts[138][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[127][0]['debit_amount'] -
                                                $allAccounts[127][0]['credit_amount'] -
                                                $allAccounts[138][0]['credit_amount'] +
                                                $allAccounts[138][0]['debit_amount'];
                                                $totalAssets += $allAccounts[127][0]['debit_amount'] -
                                                $allAccounts[127][0]['credit_amount'] -
                                                $allAccounts[138][0]['credit_amount'] +
                                                $allAccounts[138][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[128][0]['debit_amount'] - $allAccounts[128][0]['credit_amount'] - $allAccounts[139][0]['credit_amount'] + $allAccounts[139][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Furniture & Fittings</td>
                                                            <td class="text-right">{{ number_format($allAccounts[128][0]['debit_amount'] - $allAccounts[128][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[139][0]['credit_amount'] - $allAccounts[139][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[128][0]['debit_amount'] - $allAccounts[128][0]['credit_amount'] -  $allAccounts[139][0]['credit_amount'] + $allAccounts[139][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[128][0]['debit_amount'] -
                                                $allAccounts[128][0]['credit_amount'] -
                                                $allAccounts[139][0]['credit_amount'] +
                                                $allAccounts[139][0]['debit_amount'];
                                                $totalAssets += $allAccounts[128][0]['debit_amount'] -
                                                $allAccounts[128][0]['credit_amount'] -
                                                $allAccounts[139][0]['credit_amount'] +
                                                $allAccounts[139][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[129][0]['debit_amount'] - $allAccounts[129][0]['credit_amount'] - $allAccounts[140][0]['credit_amount'] + $allAccounts[140][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Office Equipment</td>
                                                            <td class="text-right">{{ number_format($allAccounts[129][0]['debit_amount'] - $allAccounts[129][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[140][0]['credit_amount'] - $allAccounts[140][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[129][0]['debit_amount'] - $allAccounts[129][0]['credit_amount'] -  $allAccounts[140][0]['credit_amount'] + $allAccounts[140][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[129][0]['debit_amount'] -
                                                $allAccounts[129][0]['credit_amount'] -
                                                $allAccounts[140][0]['credit_amount'] +
                                                $allAccounts[140][0]['debit_amount'];
                                                $totalAssets += $allAccounts[129][0]['debit_amount'] -
                                                $allAccounts[129][0]['credit_amount'] -
                                                $allAccounts[140][0]['credit_amount'] +
                                                $allAccounts[140][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[130][0]['debit_amount'] - $allAccounts[130][0]['credit_amount'] - $allAccounts[141][0]['credit_amount'] + $allAccounts[141][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Computer</td>
                                                            <td class="text-right">{{ number_format($allAccounts[130][0]['debit_amount'] - $allAccounts[130][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[141][0]['credit_amount'] - $allAccounts[141][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[130][0]['debit_amount'] - $allAccounts[130][0]['credit_amount'] -  $allAccounts[141][0]['credit_amount'] + $allAccounts[141][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[130][0]['debit_amount'] -
                                                $allAccounts[130][0]['credit_amount'] -
                                                $allAccounts[141][0]['credit_amount'] +
                                                $allAccounts[141][0]['debit_amount'];
                                                $totalAssets += $allAccounts[130][0]['debit_amount'] -
                                                $allAccounts[130][0]['credit_amount'] -
                                                $allAccounts[141][0]['credit_amount'] +
                                                $allAccounts[141][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[131][0]['debit_amount'] - $allAccounts[131][0]['credit_amount'] - $allAccounts[142][0]['credit_amount'] + $allAccounts[142][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Mobile Phone</td>
                                                            <td class="text-right">{{ number_format($allAccounts[131][0]['debit_amount'] - $allAccounts[131][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[142][0]['credit_amount'] - $allAccounts[142][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[131][0]['debit_amount'] - $allAccounts[131][0]['credit_amount'] -  $allAccounts[142][0]['credit_amount'] + $allAccounts[142][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[131][0]['debit_amount'] -
                                                $allAccounts[131][0]['credit_amount'] -
                                                $allAccounts[142][0]['credit_amount'] +
                                                $allAccounts[142][0]['debit_amount'];
                                                $totalAssets += $allAccounts[131][0]['debit_amount'] -
                                                $allAccounts[131][0]['credit_amount'] -
                                                $allAccounts[142][0]['credit_amount'] +
                                                $allAccounts[142][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[143][0]['debit_amount'] - $allAccounts[143][0]['credit_amount'] - $allAccounts[150][0]['credit_amount'] + $allAccounts[150][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Land - Lease</td>
                                                            <td class="text-right">{{ number_format($allAccounts[143][0]['debit_amount'] - $allAccounts[143][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[150][0]['credit_amount'] - $allAccounts[150][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[143][0]['debit_amount'] - $allAccounts[143][0]['credit_amount'] -  $allAccounts[150][0]['credit_amount'] + $allAccounts[150][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[143][0]['debit_amount'] -
                                                $allAccounts[143][0]['credit_amount'] -
                                                $allAccounts[150][0]['credit_amount'] +
                                                $allAccounts[150][0]['debit_amount'];
                                                $totalAssets += $allAccounts[143][0]['debit_amount'] -
                                                $allAccounts[143][0]['credit_amount'] -
                                                $allAccounts[150][0]['credit_amount'] +
                                                $allAccounts[150][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[144][0]['debit_amount'] - $allAccounts[144][0]['credit_amount'] - $allAccounts[151][0]['credit_amount'] + $allAccounts[151][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Building - Lease</td>
                                                            <td class="text-right">{{ number_format($allAccounts[144][0]['debit_amount'] - $allAccounts[144][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[151][0]['credit_amount'] - $allAccounts[151][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[144][0]['debit_amount'] - $allAccounts[144][0]['credit_amount'] -  $allAccounts[151][0]['credit_amount'] + $allAccounts[151][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[144][0]['debit_amount'] -
                                                $allAccounts[144][0]['credit_amount'] -
                                                $allAccounts[151][0]['credit_amount'] +
                                                $allAccounts[151][0]['debit_amount'];
                                                $totalAssets += $allAccounts[144][0]['debit_amount'] -
                                                $allAccounts[144][0]['credit_amount'] -
                                                $allAccounts[151][0]['credit_amount'] +
                                                $allAccounts[151][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[145][0]['debit_amount'] - $allAccounts[145][0]['credit_amount'] - $allAccounts[153][0]['credit_amount'] + $allAccounts[153][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Motor Cycles - Lease</td>
                                                            <td class="text-right">{{ number_format($allAccounts[145][0]['debit_amount'] - $allAccounts[145][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[153][0]['credit_amount'] - $allAccounts[153][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[145][0]['debit_amount'] - $allAccounts[145][0]['credit_amount'] -  $allAccounts[153][0]['credit_amount'] + $allAccounts[153][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[145][0]['debit_amount'] -
                                                $allAccounts[145][0]['credit_amount'] -
                                                $allAccounts[153][0]['credit_amount'] +
                                                $allAccounts[153][0]['debit_amount'];
                                                $totalAssets += $allAccounts[145][0]['debit_amount'] -
                                                $allAccounts[145][0]['credit_amount'] -
                                                $allAccounts[153][0]['credit_amount'] +
                                                $allAccounts[153][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[146][0]['debit_amount'] - $allAccounts[146][0]['credit_amount'] - $allAccounts[154][0]['credit_amount'] + $allAccounts[154][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Furniture & Fittings - Lease</td>
                                                            <td class="text-right">{{ number_format($allAccounts[146][0]['debit_amount'] - $allAccounts[146][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[154][0]['credit_amount'] - $allAccounts[154][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[146][0]['debit_amount'] - $allAccounts[146][0]['credit_amount'] -  $allAccounts[154][0]['credit_amount'] + $allAccounts[154][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[146][0]['debit_amount'] -
                                                $allAccounts[146][0]['credit_amount'] -
                                                $allAccounts[154][0]['credit_amount'] +
                                                $allAccounts[154][0]['debit_amount'];
                                                $totalAssets += $allAccounts[146][0]['debit_amount'] -
                                                $allAccounts[146][0]['credit_amount'] -
                                                $allAccounts[154][0]['credit_amount'] +
                                                $allAccounts[154][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[147][0]['debit_amount'] - $allAccounts[147][0]['credit_amount'] - $allAccounts[155][0]['credit_amount'] + $allAccounts[155][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Office Equipment - Lease</td>
                                                            <td class="text-right">{{ number_format($allAccounts[147][0]['debit_amount'] - $allAccounts[147][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[155][0]['credit_amount'] - $allAccounts[155][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[147][0]['debit_amount'] - $allAccounts[147][0]['credit_amount'] -  $allAccounts[155][0]['credit_amount'] + $allAccounts[155][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[147][0]['debit_amount'] -
                                                $allAccounts[147][0]['credit_amount'] -
                                                $allAccounts[155][0]['credit_amount'] +
                                                $allAccounts[155][0]['debit_amount'];
                                                $totalAssets += $allAccounts[147][0]['debit_amount'] -
                                                $allAccounts[147][0]['credit_amount'] -
                                                $allAccounts[155][0]['credit_amount'] +
                                                $allAccounts[155][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[148][0]['debit_amount'] - $allAccounts[148][0]['credit_amount'] - $allAccounts[156][0]['credit_amount'] + $allAccounts[156][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Computer - Lease</td>
                                                            <td class="text-right">{{ number_format($allAccounts[148][0]['debit_amount'] - $allAccounts[148][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[156][0]['credit_amount'] - $allAccounts[156][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[148][0]['debit_amount'] - $allAccounts[148][0]['credit_amount'] -  $allAccounts[156][0]['credit_amount'] + $allAccounts[156][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[148][0]['debit_amount'] -
                                                $allAccounts[148][0]['credit_amount'] -
                                                $allAccounts[156][0]['credit_amount'] +
                                                $allAccounts[156][0]['debit_amount'];
                                                $totalAssets += $allAccounts[148][0]['debit_amount'] -
                                                $allAccounts[148][0]['credit_amount'] -
                                                $allAccounts[156][0]['credit_amount'] +
                                                $allAccounts[156][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[149][0]['debit_amount'] - $allAccounts[149][0]['credit_amount'] - $allAccounts[157][0]['credit_amount'] + $allAccounts[157][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td>Mobile Phone - Lease</td>
                                                            <td class="text-right">{{ number_format($allAccounts[149][0]['debit_amount'] - $allAccounts[149][0]['credit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[157][0]['credit_amount'] - $allAccounts[157][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($allAccounts[149][0]['debit_amount'] - $allAccounts[149][0]['credit_amount'] -  $allAccounts[157][0]['credit_amount'] + $allAccounts[157][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[149][0]['debit_amount'] -
                                                $allAccounts[149][0]['credit_amount'] -
                                                $allAccounts[157][0]['credit_amount'] +
                                                $allAccounts[157][0]['debit_amount'];
                                                $totalAssets += $allAccounts[149][0]['debit_amount'] -
                                                $allAccounts[149][0]['credit_amount'] -
                                                $allAccounts[157][0]['credit_amount'] +
                                                $allAccounts[157][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}

                                                        {{-- <tr>
                                                        <td>Motor Vehicles - Lease</td>
                                                        <td>{{ number_format(0, 2) }}</td>
                                                <td>Pro. for Depreciation - Motor Vehicles - Lease</td>
                                                <td>{{ number_format($allAccounts[152][0]['credit_amount'] - $allAccounts[152][0]['debit_amount'], 2) }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format(0 -  $allAccounts[152][0]['credit_amount'] + $allAccounts[152][0]['debit_amount'], 2)}}
                                                </td>
                                                @php
                                                $ppe += 0 - $allAccounts[152][0]['credit_amount'] +
                                                $allAccounts[152][0]['debit_amount'];
                                                $totalAssets += 0 - $allAccounts[152][0]['credit_amount'] +
                                                $allAccounts[152][0]['debit_amount'];
                                                @endphp
                                                </tr> --}}

                                                        {{-- @if ($allAccounts[132][0]['debit_amount'] - $allAccounts[132][0]['credit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Land Working in Progress</td>
                                                            <td class="text-right">{{ number_format($allAccounts[132][0]['debit_amount'] - $allAccounts[132][0]['credit_amount'], 2) }}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[132][0]['debit_amount'] -
                                                $allAccounts[132][0]['credit_amount'];
                                                $totalAssets += $allAccounts[132][0]['debit_amount'] -
                                                $allAccounts[132][0]['credit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[133][0]['debit_amount'] - $allAccounts[133][0]['credit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Building Working in Progress</td>
                                                            <td class="text-right">{{ number_format($allAccounts[133][0]['debit_amount'] - $allAccounts[133][0]['credit_amount'], 2) }}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[133][0]['debit_amount'] -
                                                $allAccounts[133][0]['credit_amount'];
                                                $totalAssets += $allAccounts[133][0]['debit_amount'] -
                                                $allAccounts[133][0]['credit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[134][0]['debit_amount'] - $allAccounts[134][0]['credit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Transformer Building & Equipment</td>
                                                            <td class="text-right">{{ number_format($allAccounts[134][0]['debit_amount'] - $allAccounts[134][0]['credit_amount'], 2) }}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[134][0]['debit_amount'] -
                                                $allAccounts[134][0]['credit_amount'];
                                                $totalAssets += $allAccounts[134][0]['debit_amount'] -
                                                $allAccounts[134][0]['credit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[135][0]['debit_amount'] - $allAccounts[135][0]['credit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">New Building Grameen Group</td>
                                                            <td class="text-right">{{ number_format($allAccounts[135][0]['debit_amount'] - $allAccounts[135][0]['credit_amount'], 2) }}
                                                </td>
                                                @php
                                                $ppe += $allAccounts[135][0]['debit_amount'] -
                                                $allAccounts[135][0]['credit_amount'];
                                                $totalAssets += $allAccounts[135][0]['debit_amount'] -
                                                $allAccounts[135][0]['credit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}

                                                        <tr>
                                                            <td class="sub-title text-right" colspan="4">
                                                                {{ number_format($ppe, 2) }}</td>
                                                        </tr>

                                                        @if ($investmentTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accInvestments, $endDate, 'debit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Investments') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Investments
                                                                        </a>
                                                                    @else
                                                                        Investments
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($investmentTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalAssets +=
                                                                        $investmentTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        <tr>
                                                            <td colspan="4" class="sub-title">Current Assets</td>
                                                        </tr>

                                                        @if ($allAccounts[8][0]['debit_amount'] - $allAccounts[8][0]['credit_amount'] != 0)
                                                            <tr>
                                                                <td colspan="3">Inventory</td>
                                                                <td class="text-right">
                                                                    {{ number_format($allAccounts[8][0]['debit_amount'] - $allAccounts[8][0]['credit_amount'], 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $allAccounts[8][0]['debit_amount'] -
                                                                        $allAccounts[8][0]['credit_amount'];
                                                                    $totalAssets +=
                                                                        $allAccounts[8][0]['debit_amount'] -
                                                                        $allAccounts[8][0]['credit_amount'];
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($stockTransfer[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    Inter-branch Transfer
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($stockTransfer[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets += $stockTransfer[0]->balance_amount;
                                                                    $totalAssets += $stockTransfer[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($loanStockTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accLoanStocks, $endDate, 'debit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Loan Stock') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Loan Stock
                                                                        </a>
                                                                    @else
                                                                        Loan Stock
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($loanStockTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $loanStockTransactions[0]->balance_amount;
                                                                    $totalAssets +=
                                                                        $loanStockTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($tradeRecievableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accTradeRecievables, $endDate, 'debit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Trade Receivables') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Trade Receivables
                                                                        </a>
                                                                    @else
                                                                        Trade Receivables
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($tradeRecievableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $tradeRecievableTransactions[0]->balance_amount;
                                                                    $totalAssets +=
                                                                        $tradeRecievableTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif
                                                        @if ($otherRecievableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accOtherRecievables, $endDate, 'debit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Other Receivables') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Other Receivables
                                                                        </a>
                                                                    @else
                                                                        Other Receivables
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($otherRecievableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $otherRecievableTransactions[0]->balance_amount;
                                                                    $totalAssets +=
                                                                        $otherRecievableTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($installmentCollectionTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accInstallmentCollections, $endDate, 'debit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Installment Collections') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Installment Collections
                                                                        </a>
                                                                    @else
                                                                        Installment Collections
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($installmentCollectionTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $installmentCollectionTransactions[0]
                                                                            ->balance_amount;
                                                                    $totalAssets +=
                                                                        $installmentCollectionTransactions[0]
                                                                            ->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        {{-- @if ($allAccounts[203][0]['debit_amount'] - $allAccounts[203][0]['credit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Income Tax Withholding Receivables</td>
                                                            <td class="text-right">{{ number_format($allAccounts[203][0]['debit_amount'] - $allAccounts[203][0]['credit_amount'], 2) }}
                                                </td>
                                                @php
                                                $currentAssets += $allAccounts[203][0]['debit_amount'] -
                                                $allAccounts[203][0]['credit_amount'];
                                                $totalAssets += $allAccounts[203][0]['debit_amount'] -
                                                $allAccounts[203][0]['credit_amount'];
                                                @endphp
                                                </tr>
                                                @endif

                                                @if ($allAccounts[202][0]['debit_amount'] - $allAccounts[202][0]['credit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Short Term Loan</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[202][0]['debit_amount'] - $allAccounts[202][0]['credit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $currentAssets += $allAccounts[202][0]['debit_amount'] -
                                                    $allAccounts[202][0]['credit_amount'];
                                                    $totalAssets += $allAccounts[202][0]['debit_amount'] -
                                                    $allAccounts[202][0]['credit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif

                                                @if ($allAccounts[184][0]['debit_amount'] - $allAccounts[184][0]['credit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Sundry Loan - Staff</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[184][0]['debit_amount'] - $allAccounts[184][0]['credit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $currentAssets += $allAccounts[184][0]['debit_amount'] -
                                                    $allAccounts[184][0]['credit_amount'];
                                                    $totalAssets += $allAccounts[184][0]['debit_amount'] -
                                                    $allAccounts[184][0]['credit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif

                                                @if ($allAccounts[185][0]['debit_amount'] - $allAccounts[185][0]['credit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Festival Loan - Staff</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[185][0]['debit_amount'] - $allAccounts[185][0]['credit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $currentAssets += $allAccounts[185][0]['debit_amount'] -
                                                    $allAccounts[185][0]['credit_amount'];
                                                    $totalAssets += $allAccounts[185][0]['debit_amount'] -
                                                    $allAccounts[185][0]['credit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif

                                                @if ($allAccounts[201][0]['debit_amount'] - $allAccounts[201][0]['credit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Branch Loan Recoveries</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[201][0]['debit_amount'] - $allAccounts[201][0]['credit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $currentAssets += $allAccounts[201][0]['debit_amount'] -
                                                    $allAccounts[201][0]['credit_amount'];
                                                    $totalAssets += $allAccounts[201][0]['debit_amount'] -
                                                    $allAccounts[201][0]['credit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif

                                                @if ($allAccounts[198][0]['debit_amount'] - $allAccounts[198][0]['credit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Refundable Interest</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[198][0]['debit_amount'] - $allAccounts[198][0]['credit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $currentAssets += $allAccounts[198][0]['debit_amount'] -
                                                    $allAccounts[198][0]['credit_amount'];
                                                    $totalAssets += $allAccounts[198][0]['debit_amount'] -
                                                    $allAccounts[198][0]['credit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif --}}

                                                        @if ($advancePrepaymentTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accAdvancePrepayments, $endDate, 'debit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Advance and Prepayments') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Advance and Prepayments
                                                                        </a>
                                                                    @else
                                                                        Advance and Prepayments
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($advancePrepaymentTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $advancePrepaymentTransactions[0]
                                                                            ->balance_amount;
                                                                    $totalAssets +=
                                                                        $advancePrepaymentTransactions[0]
                                                                            ->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($cashBankandHandTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a data-size="2xl" style="padding-top: 10px;"
                                                                            class="detail-link"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accCashBankandHands, $endDate, 'debit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Cash and Cash Equivalents') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Cash and Cash Equivalents
                                                                        </a>
                                                                    @else
                                                                        Cash and Cash Equivalents
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($cashBankandHandTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $cashBankandHandTransactions[0]->balance_amount;
                                                                    $totalAssets +=
                                                                        $cashBankandHandTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($loanInventry[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    Loan Inventory
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($loanInventry[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets += $loanInventry[0]->balance_amount;
                                                                    $totalAssets += $loanInventry[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($loanInventryAsset[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    Inventory Asset
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($loanInventryAsset[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $loanInventryAsset[0]->balance_amount;
                                                                    $totalAssets +=
                                                                        $loanInventryAsset[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($welfare[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    Welfare Fund
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($welfare[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets += $welfare[0]->balance_amount;
                                                                    $totalAssets += $welfare[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($loanCtrlInventry[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    Loan Stock Control
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($loanCtrlInventry[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $loanCtrlInventry[0]->balance_amount;
                                                                    $totalAssets +=
                                                                        $loanCtrlInventry[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($otherCurrentAsset[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    Other Current Asset
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($otherCurrentAsset[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $currentAssets +=
                                                                        $otherCurrentAsset[0]->balance_amount;
                                                                    $totalAssets +=
                                                                        $otherCurrentAsset[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        <tr>
                                                            <td class="sub-title text-right" colspan="4">
                                                                {{ number_format($currentAssets, 2) }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td class="main-title">Total Assets</td>
                                                            <td class="main-title"></td>
                                                            <td class="main-title"></td>
                                                            <td class="main-title text-right">
                                                                {{ number_format($totalAssets, 2) }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="4"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4"></td>
                                                        </tr>

                                                        {{-- EQUITY AND LIABILITIES --}}
                                                        @php
                                                            $totalCapReserve = 0;
                                                            $totalNonCurrentLiab = 0;
                                                            $totalCurrentLiab = 0;
                                                            $totalLiab = 0;
                                                        @endphp

                                                        <tr>
                                                            <td class="sub-title" colspan="4">Capital and Reserves</td>
                                                        </tr>

                                                        {{-- @if ($allAccounts[231][0]['credit_amount'] - $allAccounts[231][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Share Capital Paidup</td>
                                                            <td class="text-right">{{ number_format($allAccounts[231][0]['credit_amount'] - $allAccounts[231][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCapReserve += $allAccounts[231][0]['credit_amount'] -
                                                $allAccounts[231][0]['debit_amount'];
                                                $totalLiab += $allAccounts[231][0]['credit_amount'] -
                                                $allAccounts[231][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[232][0]['credit_amount'] - $allAccounts[232][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Group Company Contribution</td>
                                                            <td class="text-right">{{ number_format($allAccounts[232][0]['credit_amount'] - $allAccounts[232][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCapReserve += $allAccounts[232][0]['credit_amount'] -
                                                $allAccounts[232][0]['debit_amount'];
                                                $totalLiab += $allAccounts[232][0]['credit_amount'] -
                                                $allAccounts[232][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[233][0]['credit_amount'] - $allAccounts[233][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Profit & Loss Retained</td>
                                                            <td class="text-right">{{ number_format($allAccounts[233][0]['credit_amount'] - $allAccounts[233][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCapReserve += $allAccounts[233][0]['credit_amount'] -
                                                $allAccounts[233][0]['debit_amount'];
                                                $totalLiab += $allAccounts[233][0]['credit_amount'] -
                                                $allAccounts[233][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}

                                                        <tr>
                                                            <td colspan="3">Profit & Loss for the Period</td>
                                                            <td class="text-right">{{ number_format($profitnLoss, 2) }}
                                                            </td>
                                                            @php
                                                                $totalCapReserve += $profitnLoss;
                                                                $totalLiab += $profitnLoss;
                                                            @endphp
                                                        </tr>

                                                        <tr>
                                                            <td class="sub-title text-right" colspan="4">
                                                                {{ number_format($totalCapReserve, 2) }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="4" class="sub-title">Non Current Liabilities
                                                            </td>
                                                        </tr>

                                                        @if ($longTermPayableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accLongTermPayables, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Long Term Payable') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Long Term Payable
                                                                        </a>
                                                                    @else
                                                                        Long Term Payable
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($longTermPayableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalNonCurrentLiab +=
                                                                        $longTermPayableTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $longTermPayableTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($leaseCrreditorTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accLeaseCrreditors, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Lease Creditors') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Lease Creditors
                                                                        </a>
                                                                    @else
                                                                        Lease Creditors
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($leaseCrreditorTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalNonCurrentLiab +=
                                                                        $leaseCrreditorTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $leaseCrreditorTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif


                                                        {{-- @if ($allAccounts[241][0]['credit_amount'] - $allAccounts[241][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Debentures</td>
                                                            <td class="text-right">{{ number_format($allAccounts[241][0]['credit_amount'] - $allAccounts[241][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalNonCurrentLiab += $allAccounts[241][0]['credit_amount'] -
                                                $allAccounts[241][0]['debit_amount'];
                                                $totalLiab += $allAccounts[241][0]['credit_amount'] -
                                                $allAccounts[241][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}

                                                        <tr>
                                                            <td class="sub-title text-right" colspan="4">
                                                                {{ number_format($totalNonCurrentLiab, 2) }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="4" class="sub-title">Current Liabilities </td>
                                                        </tr>

                                                        @if ($tradePayableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accTradePayables, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Trade Payables') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Trade Payables
                                                                        </a>
                                                                    @else
                                                                        Trade Payables
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($tradePayableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $tradePayableTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $tradePayableTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($otherPayableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accOtherPayables, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Other Payables') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Other Payables
                                                                        </a>
                                                                    @else
                                                                        Other Payables
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($otherPayableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $otherPayableTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $otherPayableTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($loanStockControlTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accLoanStockControls, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Loan Stock Control') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Loan Stock Control
                                                                        </a>
                                                                    @else
                                                                        Loan Stock Control
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($loanStockControlTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $loanStockControlTransactions[0]
                                                                            ->balance_amount;
                                                                    $totalLiab +=
                                                                        $loanStockControlTransactions[0]
                                                                            ->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($loanPayableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accLoanPayables, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Loan Payable') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Loan Payable
                                                                        </a>
                                                                    @else
                                                                        Loan Payable
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($loanPayableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $loanPayableTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $loanPayableTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($salaryPayableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accSalaryPayables, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Salary Payable') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Salary Payable
                                                                        </a>
                                                                    @else
                                                                        Salary Payable
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($salaryPayableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $salaryPayableTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $salaryPayableTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        {{-- @if ($allAccounts[242][0]['credit_amount'] - $allAccounts[242][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">E.P.F. Payable</td>
                                                            <td class="text-right">{{ number_format($allAccounts[242][0]['credit_amount'] - $allAccounts[242][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCurrentLiab += $allAccounts[242][0]['credit_amount'] -
                                                $allAccounts[242][0]['debit_amount'];
                                                $totalLiab += $allAccounts[242][0]['credit_amount'] -
                                                $allAccounts[242][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[243][0]['credit_amount'] - $allAccounts[243][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">E.T.F. Payable</td>
                                                            <td class="text-right">{{ number_format($allAccounts[243][0]['credit_amount'] - $allAccounts[243][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCurrentLiab += $allAccounts[243][0]['credit_amount'] -
                                                $allAccounts[243][0]['debit_amount'];
                                                $totalLiab += $allAccounts[243][0]['credit_amount'] -
                                                $allAccounts[243][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif

                                                --}}
                                                        {{-- @if ($allAccounts[287][0]['credit_amount'] - $allAccounts[287][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Pro. for Gratuity</td>
                                                            <td class="text-right">{{ number_format($allAccounts[287][0]['credit_amount'] - $allAccounts[287][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCurrentLiab += $allAccounts[287][0]['credit_amount'] -
                                                $allAccounts[287][0]['debit_amount'];
                                                $totalLiab += $allAccounts[287][0]['credit_amount'] -
                                                $allAccounts[287][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif --}}


                                                        {{-- @if ($allAccounts[288][0]['credit_amount'] - $allAccounts[288][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">E.P.F./E.T.F. Control</td>
                                                            <td class="text-right">{{ number_format($allAccounts[288][0]['credit_amount'] - $allAccounts[288][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCurrentLiab += $allAccounts[288][0]['credit_amount'] -
                                                $allAccounts[288][0]['debit_amount'];
                                                $totalLiab += $allAccounts[288][0]['credit_amount'] -
                                                $allAccounts[288][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[297][0]['credit_amount'] - $allAccounts[297][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Bank Overdraft</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[297][0]['credit_amount'] - $allAccounts[297][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[297][0]['credit_amount'] -
                                                    $allAccounts[297][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[297][0]['credit_amount'] -
                                                    $allAccounts[297][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif --}}

                                                        @if ($memberInsurePayableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accMemberInsurePayables, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Member Insurance Payable') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Member Insurance Payable
                                                                        </a>
                                                                    @else
                                                                        Member Insurance Payable
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($memberInsurePayableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $memberInsurePayableTransactions[0]
                                                                            ->balance_amount;
                                                                    $totalLiab +=
                                                                        $memberInsurePayableTransactions[0]
                                                                            ->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($headOfficAcTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accHeadOfficAcs, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Head Office C/A') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Head Office C/A
                                                                        </a>
                                                                    @else
                                                                        Head Office C/A
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($headOfficAcTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $headOfficAcTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $headOfficAcTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        {{-- @if ($allAccounts[293][0]['credit_amount'] - $allAccounts[293][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Overdue Interest Payable</td>
                                                            <td class="text-right">{{ number_format($allAccounts[293][0]['credit_amount'] - $allAccounts[293][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCurrentLiab += $allAccounts[293][0]['credit_amount'] -
                                                $allAccounts[293][0]['debit_amount'];
                                                $totalLiab += $allAccounts[293][0]['credit_amount'] -
                                                $allAccounts[293][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[294][0]['credit_amount'] - $allAccounts[294][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Fixed Assets Disposal</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[294][0]['credit_amount'] - $allAccounts[294][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[294][0]['credit_amount'] -
                                                    $allAccounts[294][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[294][0]['credit_amount'] -
                                                    $allAccounts[294][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif --}}

                                                        @if ($badDebtorTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accBadDebtors, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Pro. for Bad Debtors') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Pro. for Bad Debtors
                                                                        </a>
                                                                    @else
                                                                        Pro. for Bad Debtors
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($badDebtorTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $badDebtorTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $badDebtorTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($taxPayableTransactions[0]->balance_amount != 0)
                                                            <tr>
                                                                <td colspan="3">
                                                                    @if (Gate::check('show balance sheet'))
                                                                        <a class="detail-link" data-size="2xl"
                                                                            style="padding-top: 10px;"
                                                                            data-url="{{ route('balance_sheet.show_detail', [$accTaxPayables, $endDate, 'credit']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ __('Tax payable') }}"
                                                                            data-toggle="tooltip"
                                                                            data-original-title="{{ __('Show Details') }}">
                                                                            Tax payable
                                                                        </a>
                                                                    @else
                                                                        Tax payable
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($taxPayableTransactions[0]->balance_amount, 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $taxPayableTransactions[0]->balance_amount;
                                                                    $totalLiab +=
                                                                        $taxPayableTransactions[0]->balance_amount;
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        {{-- @if ($allAccounts[306][0]['credit_amount'] - $allAccounts[306][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Provision for Tax</td>
                                                            <td class="text-right">{{ number_format($allAccounts[306][0]['credit_amount'] - $allAccounts[306][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCurrentLiab += $allAccounts[306][0]['credit_amount'] -
                                                $allAccounts[306][0]['debit_amount'];
                                                $totalLiab += $allAccounts[306][0]['credit_amount'] -
                                                $allAccounts[306][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[292][0]['credit_amount'] - $allAccounts[292][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Stamp Duty Payable</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[292][0]['credit_amount'] - $allAccounts[292][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[292][0]['credit_amount'] -
                                                    $allAccounts[292][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[292][0]['credit_amount'] -
                                                    $allAccounts[292][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[291][0]['credit_amount'] - $allAccounts[291][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Bank Error</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[291][0]['credit_amount'] - $allAccounts[291][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[291][0]['credit_amount'] -
                                                    $allAccounts[291][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[291][0]['credit_amount'] -
                                                    $allAccounts[291][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[293][0]['credit_amount'] - $allAccounts[293][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Overdue Interest Payable</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[293][0]['credit_amount'] - $allAccounts[293][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[293][0]['credit_amount'] -
                                                    $allAccounts[293][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[293][0]['credit_amount'] -
                                                    $allAccounts[293][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif --}}

                                                        @if ($allAccounts[5][0]['credit_amount'] - $allAccounts[5][0]['debit_amount'] != 0)
                                                            <tr>
                                                                <td colspan="3">Advance Received</td>
                                                                <td class="text-right">
                                                                    {{ number_format($allAccounts[5][0]['credit_amount'] - $allAccounts[5][0]['debit_amount'], 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $allAccounts[5][0]['credit_amount'] -
                                                                        $allAccounts[5][0]['debit_amount'];
                                                                    $totalLiab +=
                                                                        $allAccounts[5][0]['credit_amount'] -
                                                                        $allAccounts[5][0]['debit_amount'];
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        @if ($allAccounts[29][0]['credit_amount'] - $allAccounts[29][0]['debit_amount'] != 0)
                                                            <tr>
                                                                <td colspan="3">Excess Collection</td>
                                                                <td class="text-right">
                                                                    {{ number_format($allAccounts[29][0]['credit_amount'] - $allAccounts[29][0]['debit_amount'], 2) }}
                                                                </td>
                                                                @php
                                                                    $totalCurrentLiab +=
                                                                        $allAccounts[29][0]['credit_amount'] -
                                                                        $allAccounts[29][0]['debit_amount'];
                                                                    $totalLiab +=
                                                                        $allAccounts[29][0]['credit_amount'] -
                                                                        $allAccounts[29][0]['debit_amount'];
                                                                @endphp
                                                            </tr>
                                                        @endif

                                                        {{--
                                                    @if ($allAccounts[277][0]['credit_amount'] - $allAccounts[277][0]['debit_amount'] != 0)
                                                        <tr>
                                                            <td colspan="3">Lottery Fund</td>
                                                            <td class="text-right">{{ number_format($allAccounts[277][0]['credit_amount'] - $allAccounts[277][0]['debit_amount'], 2) }}
                                                </td>
                                                @php
                                                $totalCurrentLiab += $allAccounts[277][0]['credit_amount'] -
                                                $allAccounts[277][0]['debit_amount'];
                                                $totalLiab += $allAccounts[277][0]['credit_amount'] -
                                                $allAccounts[277][0]['debit_amount'];
                                                @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[281][0]['credit_amount'] - $allAccounts[281][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Collection in Suspense</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[281][0]['credit_amount'] - $allAccounts[281][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[281][0]['credit_amount'] -
                                                    $allAccounts[281][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[281][0]['credit_amount'] -
                                                    $allAccounts[281][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[282][0]['credit_amount'] - $allAccounts[282][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Welfare Fund</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[282][0]['credit_amount'] - $allAccounts[282][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[282][0]['credit_amount'] -
                                                    $allAccounts[282][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[282][0]['credit_amount'] -
                                                    $allAccounts[282][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[283][0]['credit_amount'] - $allAccounts[283][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Housing Project</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[283][0]['credit_amount'] - $allAccounts[283][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[283][0]['credit_amount'] -
                                                    $allAccounts[283][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[283][0]['credit_amount'] -
                                                    $allAccounts[283][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[285][0]['credit_amount'] - $allAccounts[285][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Cash Deposit Control</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[285][0]['credit_amount'] - $allAccounts[285][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[285][0]['credit_amount'] -
                                                    $allAccounts[285][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[285][0]['credit_amount'] -
                                                    $allAccounts[285][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif


                                                @if ($allAccounts[286][0]['credit_amount'] - $allAccounts[286][0]['debit_amount'] != 0)
                                                <tr>
                                                    <td colspan="3">Fund Transfer in Suspense</td>
                                                    <td class="text-right">
                                                        {{ number_format($allAccounts[286][0]['credit_amount'] - $allAccounts[286][0]['debit_amount'], 2) }}
                                                    </td>
                                                    @php
                                                    $totalCurrentLiab += $allAccounts[286][0]['credit_amount'] -
                                                    $allAccounts[286][0]['debit_amount'];
                                                    $totalLiab += $allAccounts[286][0]['credit_amount'] -
                                                    $allAccounts[286][0]['debit_amount'];
                                                    @endphp
                                                </tr>
                                                @endif --}}

                                                        <tr>
                                                            <td class="sub-title text-right" colspan="4">
                                                                {{ number_format($totalCurrentLiab, 2) }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td class="main-title">Total Equity & Liabilities</td>
                                                            <td class="main-title"></td>
                                                            <td class="main-title"></td>
                                                            <td class="main-title text-right">
                                                                {{ number_format($totalLiab, 2) }}
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
            // if(localStorage.getItem("trial_balance_no") == null){
            //     var pageNo = 0;
            // } else {
            //     var pageNo = Number(localStorage.getItem("trial_balance_no"));
            // }
            // var trial_balancesTable = $('#trial_balances_table').DataTable({
            //                                                             order:[[0,"asc"]],
            //                                                             "pageLength": 25,
            //                                                             dom: 'lBfrtip',
            //                                                             buttons: [
            //                                                                 {
            //                                                                     extend: 'excel',
            //                                                                     title: 'Balance Sheet of {{ config('app.name') }} '+duration,
            //                                                                     footer: true
            //                                                                 }, {
            //                                                                     extend: 'print',
            //                                                                     title:  function() {
            //                                                                             return "<h3>"+filename+"</h3>";
            //                                                                         } ,
            //                                                                     footer: true
            //                                                                 }, {
            //                                                                     extend: 'csv',
            //                                                                     title: 'Balance Sheet of {{ config('app.name') }} '+duration,
            //                                                                     footer: true
            //                                                                 }
            //                                                             ]
            //                                                             });
            // trial_balancesTable.page(pageNo).draw(false);

            // $('#trial_balances_table').on( 'page.dt', function () {
            //     var pageInfo =  trial_balancesTable.page.info();
            //     pageNo = pageInfo.page;
            //     localStorage.setItem("trial_balance_no", pageNo);
            // });
        });

        // $('body').on('click', '.trial_balance_td', function(){
        // var trialBalanceId = $(this).closest('tr').attr("data-id");
        // window.location.href = "{{ url('trial_balances') }}/"+trialBalanceId;
        // });

        $('body').on('submit', function() {
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
