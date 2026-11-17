@extends('layouts.admin')

@section('page-title')
    {{ __('Stock Report Summery') }}
@endsection

@push('script-page')
    <style>
        table tfoot {
            display: table-row-group;
        }

        .tbl-scrl {
            display: block;
            max-height: 40rem;
            overflow-y: scroll;
        }

        .white-space-nowrap {
            white-space: nowrap;
        }

        thead>tr>th.sorting {
            padding-right: 30px !important;
        }

        .table:not(.table-sm) thead th {
            vertical-align: top !important;
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
            var filename = 'Stock Report Summery';
            if ($('#duration').val() != undefined) {
                var duration = $('#duration').val();
            } else {
                var duration = "";
            }

            if ($('#category').length > 0) {
                var category = $('#category').val();
            } else {
                var category = "";
            }

            if ($('#branch').length > 0) {
                var branch = $('#branch').val();
            } else {
                var branch = "";
            }

            $('#report-dataTable').DataTable({
                "pageLength": 25,
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: filename + category + branch + duration,
                    footer: true
                }, {
                    extend: 'print',
                    title: filename + category + branch + duration,
                    footer: true
                }, {
                    extend: 'csv',
                    title: filename + category + branch + duration,
                    footer: true
                }]
            });
        });
    </script>
@endpush

@section('content')
    <style>
        .badge {
            color: #00bf20;
            font-weight: bold;
            font-size: 12px;
        }

        .card .card-header .btn:not(.note-btn) {
            border-radius: 30px;
            background-color: #00AF6B !important;
        }

        thead>tr>th.sorting {
            padding-right: 0% !important;
        }
    </style>

    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Stock  Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
        <div class="breadcrumb-item">{{__('Reports')}}</div>
        <div class="breadcrumb-item">{{__('Stock  Report')}}</div>
    </div>
    </div> --}}

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Stock  Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                    <div class="breadcrumb-item">{{ __('Reports') }}</div>
                                    <div class="breadcrumb-item">{{ __('Stock  Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('stocks_report.search') }}">
                            @csrf

                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-2">Filter</h4>
                                    </div>
                                    <div class="row">
                                        @php
                                            $totalPurchasingAmount = 0;
                                            $totalSallingAmount = 0;

                                            // Calculate totals only if $inventries is defined and has items
                                            if (isset($inventries) && count($inventries) > 0) {
                                                foreach ($inventries as $inventory) {
                                                    if ($inventory->remain_qty != 0) {
                                                        $totalPurchasingAmount +=
                                                            $inventory->purchase_price * $inventory->remain_qty;
                                                        $totalSallingAmount +=
                                                            $inventory->sale_price * $inventory->remain_qty;
                                                    }
                                                }
                                            }
                                        @endphp

                                        <!-- Conditionally display this section only if $inventries has data -->
                                        @if (isset($inventries) && count($inventries) > 0)
                                            <div class="col-6 justify-content-start d-flex">
                                                <div class="col-md-4 me-4">
                                                    <label
                                                        for="total_purchasing_amount">{{ __('Total Purchasing Amount') }}</label>
                                                    <input id="total_purchasing_amount"
                                                        value="Rs. {{ number_format($totalPurchasingAmount, 2) }}"
                                                        readonly>
                                                </div>

                                                <div class="col-md-4">
                                                    <label
                                                        for="total_salling_amount">{{ __('Total Selling Amount') }}</label>
                                                    <input id="total_salling_amount"
                                                        value="Rs. {{ number_format($totalSallingAmount, 2) }}" readonly>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Hidden fallback when $inventries is empty -->
                                            <div class="col-6 justify-content-start d-flex">
                                                <div class="col-md-4 me-4">
                                                    <label
                                                        for="total_purchasing_amount">{{ __('Total Purchasing Amount') }}</label>
                                                    <input id="total_purchasing_amount" value="Rs. 0.00">
                                                </div>

                                                <div class="col-md-4">
                                                    <label
                                                        for="total_salling_amount">{{ __('Total Selling Amount') }}</label>
                                                    <input id="total_salling_amount" value="Rs. 0.00">
                                                </div>
                                            </div>
                                        @endif


                                        <div class="col-6 justify-content-end d-flex">
                                            <div class="col-md-2 me-4">
                                                <label for="branch_id">Branch</label>
                                                @if (isset($branch))
                                                    @if ($branch != '')
                                                        <input type="hidden" id="branch"
                                                            value="{{ __(' In ') . App\Branch::branchName($branch)[0] }}">
                                                    @endif
                                                @endif
                                                {{ Form::select('branch_id', $branches, $branch ?? '', ['class' => 'form-control font-style', 'required' => 'required']) }}
                                            </div>

                                            <div class="col-md-2">
                                                <label for="category">Category</label>
                                                @if (isset($category))
                                                    @if ($category != '')
                                                        <input type="hidden" id="category"
                                                            value="{{ __(' Of ') . App\ProductCategory::categoryName($category)[0] }}">
                                                    @endif
                                                @endif
                                                {{ Form::select('category_id', $productCategories, $category ?? '', ['class' => 'form-control font-style']) }}
                                            </div>
                                        </div>

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

            <div id="printableArea">
                <div class="row">
                    <div id="bill-container">
                        <div class="card">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="card-body " style="min-width: 100%">
                                        <table class="table table-flush" id="report-dataTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>{{ __('Product Code') }}</th>
                                                    <th>{{ __('Item Name') }}</th>
                                                    <th class="text-right">
                                                        {{ __('Unit Purchasing Price') }} ({{ $currencySymbol }})
                                                    </th>
                                                    <th class="text-right">
                                                        {{ __('Unit Selling Price') }} ({{ $currencySymbol }})
                                                    </th>
                                                    <th class="text-center">{{ __('Status') }}</th>
                                                    <th class="text-center">{{ __('QTY') }}</th>
                                                    <th class="text-right">
                                                        {{ __('Total Purchasing Price') }} ({{ $currencySymbol }})</th>
                                                    <th class="text-right">
                                                        {{ __('Total Selling Price') }} ({{ $currencySymbol }})
                                                    </th>
                                                    <th class="text-center">{{ __('Purchasing Date') }}</th>
                                                    <th class="text-center">{{ __('Branch') }}</th>
                                                    <th class="text-center" style="max-width: 100px;">
                                                        {{ __('Product Category') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($showTable == 'Yes')
                                                    @php
                                                        $prevProductCode = null;
                                                    @endphp
                                                    @foreach ($inventries as $key => $inventory)
                                                        @if ($inventory->remain_qty != 0)
                                                            @php
                                                                $isNewProduct =
                                                                    $inventory->product_code != $prevProductCode;
                                                                $prevProductCode = $inventory->product_code;
                                                            @endphp
                                                            <tr>
                                                                <td cla>{{ $inventory->product_code }}</td>
                                                                <td>{{ $inventory->brand_name }}
                                                                    {{ $inventory->product_name }}</td>
                                                                <td class="text-right white-space-nowrap">
                                                                    <strong>
                                                                        {{ number_format($inventory->purchase_price, 2) }}
                                                                    </strong>
                                                                </td>
                                                                <td class="text-right text-info white-space-nowrap">
                                                                    <strong>
                                                                        {{ number_format($inventory->sale_price, 2) }}
                                                                    </strong>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($isNewProduct)
                                                                        <span class="badge">{{ 'current' }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">{{ $inventory->remain_qty }}</td>
                                                                <td class="text-right white-space-nowrap">
                                                                    <strong>
                                                                        {{ number_format($inventory->purchase_price * $inventory->remain_qty, 2) }}
                                                                    </strong>
                                                                </td>
                                                                <td class="text-right white-space-nowrap">
                                                                    <strong>
                                                                        {{ number_format($inventory->sale_price * $inventory->remain_qty, 2) }}
                                                                    </strong>
                                                                </td>
                                                                <td class="text-center white-space-nowrap">
                                                                    {{ $inventory->date }}</td>
                                                                <td class="text-center">{{ $inventory->branch_name }}</td>
                                                                <td class="text-center">
                                                                    {{ App\ProductCategory::categoryName($inventory->product_category_id)[0] }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
