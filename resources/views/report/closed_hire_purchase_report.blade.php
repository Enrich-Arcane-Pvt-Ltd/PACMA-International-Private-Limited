@extends('layouts.admin')
@section('page-title')
    {{ __('Hire Sales Settlement Report') }}
@endsection
@push('script-page')
    <style>
        .tbl-scrl {
            display: block;
            max-height: 40rem;
            overflow-y: scroll;
        }
    </style>

    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();

        }


        $(document).ready(function() {
            var filename = 'Hire Sales Settlement Report';

            $('#report-dataTable').DataTable({
                order: [
                    [0, "desc"]
                ],
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: filename
                }, {
                    extend: 'print',
                    title: filename
                }, {
                    extend: 'csv',
                    title: filename
                }]
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Hire Sales Settlement Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Hire Sales Settlement Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            {{-- <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                            <form method="post" action="{{ route('closed_hire_purchase_report.search') }}">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h4 class="h4 mb-0">Filter</h4>
                </div>
                <div class="col-md-4">
                </div>
                <div class="col-md-2">
                    <label for="start_month">Start Date</label>
                    <input class="form-control font-style" name="start_date" type="date" value="" id="start_date"
                        required>
                </div>
                <div class="col-md-2">
                    <label for="end_month">End Date</label>
                    <input class="form-control font-style" name="end_date" type="date" value="" id="end_date" required>
                </div>
                <div class="col-auto apply-btn">
                    <input class="btn font-style btn-outline-primary" type="submit" value="Search">
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
    </div> --}}
            <div id="printableArea">
                {{-- <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-wrap">
                                <div class="card-body">
                                    <div class="row">
                                            @if ($showTable == 'Yes')
                                                <div class="col">
                                                    <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}
        </h6>
    </div>

    <div class="col">

    </div>

    <div class="col">

    </div>

    <div class="col">
        <h6>{{__('Duration')}} : {{ $start_date . " TO " .$end_date }} </h6>
    </div>
    @endif

    </div>
    </div>
    </div>
    </div>
    </div>
    </div> --}}
                <div class="row justify-content-center">


                    <div class="col-12">
                        <div class="bg-white cus_show p-2 br-4 mb-2">
                            <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                                <h4 class="mt-2 ">{{ __('Hire Sales Settlement Report') }}</h3>
                                    <div class="section-header-breadcrumb d-flex ">
                                        <div class="breadcrumb-item active"><a
                                                href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                                        </div>
                                        <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                        <div class="breadcrumb-item">{{ __('Reports') }}</div>
                                        <div class="breadcrumb-item">{{ __('Hire Sales Settlement Report') }}</div>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 ">
                        <div class="card br-4 cus_show">
                            <div class="card-header">
                                <div class="d-flex justify-content-between w-100">
                                    <table class="table table-flush tbl-scrl" id="report-dataTable">
                                        <thead class="thead-light">
                                            <tr>
                                                {{-- <th>{{__('Loan Id')}}</th>
                                    <th> {{__('Customer No')}}</th>
                                    <th> {{__('Customer Name')}}</th>
                                    <th> {{__('NIC')}}</th>
                                    <th> {{__('Center')}}</th>
                                    <th> {{__('Group')}}</th>
                                    <th> {{__('Loan Type')}}</th>
                                    <th> {{__('Loan Amount')}}</th>
                                    <th> {{__('Time Period')}}</th>
                                    <th> {{__('Collection Period')}}</th>
                                    <th> {{__('Interest Rate')}}</th>
                                    <th> {{__('Interest Amount')}}</th>
                                    <th> {{__('Last Paid Date')}}</th> --}}

                                                <th> {{ __('Sale #') }}</th>
                                                <th> {{ __('Invoice #') }}</th>
                                                <th> {{ __('Date Of Sale') }}</th>
                                                <th> {{ __('Purchasing Date') }}</th>
                                                <th> {{ __('Item Hold Dates') }}</th>
                                                <th> {{ __('Product Category') }}</th>
                                                <th> {{ __('Item') }}</th>
                                                <th> {{ __('Customer #') }}</th>
                                                <th> {{ __('NIC Number') }}</th>
                                                <th> {{ __('Customer') }}</th>
                                                <th> {{ __('Cash Price') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Selling Price') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Period') }}</th>
                                                <th> {{ __('Interest Rate') }}</th>
                                                <th> {{ __('Interest Amount') }} ({{ $currencySymbol }})</th>
                                                {{-- <th> {{ __('Released Interest Amount') }}</th> --}}
                                                <th> {{ __('Total Paid Amuont') }} ({{ $currencySymbol }})</th>
                                                <th> {{ __('Sale Expiry Date') }}</th>
                                                <th> {{ __('Settled Date') }}</th>
                                                <th> {{ __('Settled Amount') }} ({{ $currencySymbol }})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sales as $sale)
                                                <tr>
                                                    <td class="font-style"> {{ $sale->sale_id ?? '' }} </td>
                                                    <td class="font-style"> {{ $sale->invoice->invoice_code ?? '' }} </td>
                                                    <td class="font-style"> {{ $sale->date_of_sale }}</td>
                                                    <td class="font-style"> {{ $sale->inventory->date ?? '' }}</td>
                                                    <td class="font-style">
                                                        @if (isset($sale->inventory))
                                                            {{ date_diff(new \DateTime($sale->inventory->date), new \DateTime($sale->date_of_sale))->format('%mm %dd') }}
                                                        @endif
                                                    </td>
                                                    <td class="font-style">
                                                        {{ $sale->products->productCategory->name ?? '' }}</td>
                                                    <td class="font-style"> {{ $sale->products->name ?? '' }}</td>
                                                    <td class="font-style">
                                                        {{ \Auth::user()->customerNumberFormat($sale->customer_id) }}
                                                    </td>
                                                    <td class="font-style"> {{ $sale->customer->nic_no ?? '' }} </td>
                                                    <td class="font-style"> {{ $sale->customer->name ?? '' }} </td>
                                                    <td class="font-style">
                                                        {{ number_format($sale->outstanding_amount, 2) }} </td>
                                                    <td class="font-style"> {{ number_format($sale->total_due_amount, 2) }}
                                                    </td>
                                                    <td class="font-style"> {{ $sale->installment }}</td>
                                                    <td class="font-style"> {{ $sale->interest_rate }}</td>
                                                    <td class="font-style"> {{ number_format($sale->interest_amount, 2) }}
                                                    </td>
                                                    {{-- <td class="font-style">
                                                        {{ number_format($sale->released_interest_amount, 2) }}</td> --}}
                                                    <td class="font-style"> {{ number_format($sale->due_paid_amount, 2) }}
                                                    </td>
                                                    <td class="font-style"> {{ $sale->due_date }}</td>
                                                    <td class="font-style">{{ $sale->lastInstallment->paid_date ?? '' }}
                                                    </td>
                                                    <td class="font-style">
                                                        @if (isset($sale->lastInstallment->paid_amount))
                                                            {{ number_format($sale->lastInstallment->paid_amount, 2) }}
                                                        @endif
                                                    </td>
                                                    {{-- @php
                                                        $instCount = count($sale->installments);
                                                        if($instCount > 0){
                                                            $lastInstNo = $instCount - 1;
                                                            $lastInst = $sale->installments[$lastInstNo]->paid_date;
                                                        } else {
                                                            $lastInst = '--';
                                                        }
                                                    @endphp
                                                    <td class="font-style"> {{$lastInst}}</td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
