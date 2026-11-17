@extends('layouts.admin')
@section('page-title')
    {{ __('Daily Sales Profit Report') }}
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

        .cash-sales {
            background-color: #d4edda !important;
        }

        .hire-sales {
            background-color: #e3fbfd !important;
        }

        table {
            --bs-table-bg: #ffffff00 !important;
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
            // var filename = $('#filename').val();
            var filename = 'Daily Sales Profit Report';
            if ($('#duration').length > 0) {
                var duration = $('#duration').val();
            } else {
                var duration = "";
            }
            if ($('#category').length > 0) {
                var category = $('#category').val();
            } else {
                var category = "";
            }
            if ($('#saleType').length > 0) {
                var saleType = $('#saleType').val();
            } else {
                var saleType = '';
            }
            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'excel',
                        title: filename + saleType + category + duration,
                        footer: true
                    },
                    // {
                    //     extend: 'print',
                    //     title:  function() {
                    //             return "<h3>"+filename+saleType+category+duration+"</h3>";
                    //         } ,
                    //     footer: true
                    // },
                    {
                        extend: 'csv',
                        title: filename + saleType + category + duration,
                        footer: true
                    }
                ]
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        {{-- <div class="section-header">
            <h1>{{__('Daily Sales Profit Report')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <<<<<<< HEAD <div class="breadcrumb-item">{{__('Direct Sales & Hire Sales')}}
    </div>
    =======
    <div class="breadcrumb-item">{{__('Cash Sales & Hire Sales')}}</div>
    >>>>>>> 22db3de03e922168233868a4fa2bf6f2fce55afd
    <div class="breadcrumb-item">{{__('Reports')}}</div>
    <div class="breadcrumb-item">{{__('Daily Sales Profit Report')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Daily Sales Profit Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                    <div class="breadcrumb-item">{{ __('Reports') }}</div>
                                    <div class="breadcrumb-item">{{ __('Daily Sales Profit Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('ele_profit.search') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>
                                    {{-- <div class="col-md-2">
                                        </div> --}}
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
                                    <div class="col-md-2">
                                        <label for="loan_type">Category</label>
                                        @if (isset($category_id))
                                            @if ($category_id != '')
                                                <input type="hidden" id="category"
                                                    value="{{ __(' Of ') . App\ProductCategory::categoryName($category_id)[0] }}">
                                            @endif
                                        @endif
                                        {{ Form::select('category_id', $productCategories, $category_id ?? '', ['class' => 'form-control font-style']) }}
                                    </div>
                                    <div class="col-md-2">
                                        <label for="loan_type">Sale Type</label>
                                        @if (isset($saleType))
                                            @if ($saleType != '')
                                                <input type="hidden" id="saleType"
                                                    value="@if ($saleType == 0) Of Cash Sales @elseif($saleType == 1) Of Hire Sales @endif">
                                            @endif
                                        @endif
                                        <select name="sale_type" id="sale_type" class="form-control font-style">
                                            <option value="">-- All --</option>
                                            <option
                                                @if (isset($saleType)) @if ($saleType != '' && $saleType == 0) selected @endif
                                                @endif value="0">Cash Sales</option>
                                            <option
                                                @if (isset($saleType)) @if ($saleType != '' && $saleType == 1) selected @endif
                                                @endif value="1">Hire Sales</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="introducer_id">Salesman</label>
                                        {{ Form::select('introducer_id', $introducers, $saleIntroducer ?? '', ['id' => 'introducer_id', 'class' => 'form-control font-style']) }}
                                    </div>

                                    <div class="col-auto apply-btn">
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
                    <div class="col-lg-12">
                        <div class="card br-4 cus_show">
                            <div class="card-wrap">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($showTable == 'Yes')
                                            <div class="col">
                                                {{-- <h6>{{__('Total due payment')}} : {{number_format($duetotal,2)}}</h6> --}}
                                            </div>

                                            <div class="col">

                                            </div>

                                            <div class="col">

                                            </div>

                                            <div class="col">
                                                <input type="hidden" id="duration"
                                                    value="{{ __(' - From ') . Auth::user()->dateFormat($start_date) . __(' To ') . Auth::user()->dateFormat($end_date) }}">
                                                <h6>{{ __('Duration') }} : {{ $start_date . ' TO ' . $end_date }} </h6>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="bill-container">
                        <div class="card br-4 cus_show">
                            <div class="card-header">
                                <div class="d-flex justify-content-between w-100">
                                    <div class="table-responsive" style="overflow-x: auto;">
                                        <table class="table table-flush tbl-scrl" id="report-dataTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th> {{ __('Selling Date') }}</th>
                                                    <th> {{ __('Selling Time') }}</th>
                                                    <th> {{ __('Purchasing Date') }}</th>
                                                    <th> {{ __('Item Hold Dates') }}</th>
                                                    <th> {{ __('Inv No') }}</th>
                                                    <th> {{ __('Customer NIC') }}</th>
                                                    <th> {{ __('Customer Name') }}</th>
                                                    <th> {{ __('Salesman') }}</th>
                                                    <th> {{ __('Product Category') }}</th>
                                                    <th> {{ __('Product Code') }}</th>
                                                    <th> {{ __('Supplier Name') }}</th>
                                                    <th> {{ __('Sales Type') }}</th>
                                                    <th> {{ __('Item') }}</th>
                                                    <th> {{ __('QTY') }}</th>
                                                    <th> {{ __('Purchasing Price Buying') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Selling Purchasing Price') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Buying Total') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Selling Total') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Gross Profit') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Discount') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Interest') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Document charges') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Net Profit') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Cash / Down Payment') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Sale With Interest') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Sale Without Interest') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Interest Rate') }} ({{ $currencySymbol }})</th>
                                                    <th> {{ __('Sale Period') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($showTable == 'Yes')
                                                    @php
                                                        $total_sale_price = 0;
                                                        $total_purchase_price = 0;
                                                        $total_gross_profit = 0;
                                                        $total_net_profit = 0;
                                                        $total_interest = 0;
                                                        $total_cash = 0;
                                                        $total_docCharge = 0;
                                                        $total_credit = 0;
                                                        $total_discount = 0;

                                                        $total_unit_byuing_price = 0;
                                                        $total_unit_selling_price = 0;
                                                        $total_without_interset = 0;

                                                        $grossProfit = 0;
                                                        $netProfit = 0;
                                                        $grossProfit2 = 0;
                                                        $netProfit2 = 0;
                                                        $credit = 0;
                                                    @endphp
                                                    @foreach ($saleInfoLists as $saleInfoList)
                                                        @if ($saleInfoList->modal_type == 1)
                                                            @if ($category_id != '')
                                                                @if ($saleInfoList->products->product_category_id == $category_id)
                                                                    <tr
                                                                        class="{{ $saleInfoList->sale_type == 0 ? 'cash-sales' : 'hire-sales' }}">
                                                                        <td>{{ $saleInfoList->date_of_sale }}</td>

                                                                        <td>{{ $saleInfoList->created_at->format('h:ia') }}
                                                                        </td>

                                                                        <td>{{ $saleInfoList->inventory->date ?? '' }}</td>

                                                                        <td>
                                                                            @if (isset($saleInfoList->inventory))
                                                                                {{ date_diff(new \DateTime($saleInfoList->inventory->date), new \DateTime($saleInfoList->date_of_sale))->format('%mm %dd') }}
                                                                            @endif
                                                                        </td>

                                                                        <td>{{ $saleInfoList->invoice->invoice_code ?? '' }}
                                                                        </td>

                                                                        <td>
                                                                            {{ $saleInfoList->customer->nic_no ?? '-' }}
                                                                        </td>

                                                                        <td>
                                                                            @if ($saleInfoList->customer_type == 1)
                                                                                {{ $saleInfoList->customer->name ?? '' }}
                                                                            @elseif ($saleInfoList->customer_type == 2)
                                                                                {{ $saleInfoList->customer_name ?? '' }}
                                                                            @endif
                                                                        </td>

                                                                        <td>{{ $saleInfoList->introducer->name ?? '--' }}
                                                                        </td>

                                                                        <td>{{ $saleInfoList->products->productCategory->name ?? '' }}
                                                                        </td>

                                                                        <td>{{ $saleInfoList->products->barcode ?? '' }}
                                                                        </td>

                                                                        <td>
                                                                            @if ($saleInfoList->inventory->grns)
                                                                                {{ $saleInfoList->inventory->grns->vender->name ?? '' }}
                                                                            @endif
                                                                        </td>

                                                                        <td>
                                                                            @if ($saleInfoList->sale_type == 0)
                                                                                {{ 'Cash Sales' }}
                                                                            @else
                                                                                {{ 'Hire Sales' }}
                                                                            @endif
                                                                        </td>

                                                                        <td>{{ $saleInfoList->products->name ?? '' }}</td>

                                                                        <td>{{ $saleInfoList->quantity ?? '' }}</td>

                                                                        <td>{{ number_format($saleInfoList->inventory->purchase_price ?? 0, 2) }}
                                                                        </td>

                                                                        <td>{{ number_format($saleInfoList->inventory->sale_price ?? 0, 2) }}
                                                                        </td>

                                                                        <td>{{ number_format(($saleInfoList->inventory->purchase_price ?? 0) * $saleInfoList->quantity, 2) }}
                                                                        </td>

                                                                        <td>{{ number_format(($saleInfoList->inventory->sale_price ?? 0) * $saleInfoList->quantity, 2) }}
                                                                        </td>

                                                                        @php
                                                                            $total_sale_price +=
                                                                                ($saleInfoList->inventory->sale_price ??
                                                                                    0) *
                                                                                $saleInfoList->quantity;
                                                                            $total_purchase_price +=
                                                                                ($saleInfoList->inventory
                                                                                    ->purchase_price ??
                                                                                    0) *
                                                                                $saleInfoList->quantity;
                                                                            $grossProfit =
                                                                                $saleInfoList->net_amount -
                                                                                ($saleInfoList->inventory
                                                                                    ->purchase_price ??
                                                                                    0) *
                                                                                    $saleInfoList->quantity;
                                                                            $total_gross_profit += $grossProfit;

                                                                            $total_unit_byuing_price +=
                                                                                $saleInfoList->inventory
                                                                                    ->purchase_price ?? 0;
                                                                            $total_unit_selling_price +=
                                                                                $saleInfoList->inventory->sale_price ??
                                                                                0;
                                                                            $total_discount +=
                                                                                $saleInfoList->discount ?? 0;
                                                                        @endphp

                                                                        <td>{{ number_format($grossProfit, 2) }}</td>
                                                                        <td>{{ number_format($saleInfoList->discount, 2) }}
                                                                        </td>

                                                                        <td>
                                                                            @if ($saleInfoList->interest_amount != null)
                                                                                {{ number_format($saleInfoList->interest_amount, 2) }}
                                                                            @else
                                                                                --
                                                                            @endif
                                                                        </td>

                                                                        <td>
                                                                            @if ($saleInfoList->document_charges != '')
                                                                                {{ number_format($saleInfoList->document_charges, 2) }}
                                                                            @else
                                                                                --
                                                                            @endif
                                                                        </td>

                                                                        @php
                                                                            $total_docCharge +=
                                                                                $saleInfoList->document_charges;
                                                                            $total_interest +=
                                                                                $saleInfoList->interest_amount;
                                                                            $netProfit =
                                                                                $saleInfoList->net_amount -
                                                                                ($saleInfoList->inventory
                                                                                    ->purchase_price ??
                                                                                    0) *
                                                                                    $saleInfoList->quantity +
                                                                                $saleInfoList->interest_amount +
                                                                                $saleInfoList->document_charges;
                                                                            $total_net_profit += $netProfit;
                                                                        @endphp

                                                                        <td>
                                                                            @if ($saleInfoList->sale_type == 0)
                                                                                --
                                                                            @elseif($saleInfoList->sale_type == 1)
                                                                                {{ number_format($netProfit, 2) }}
                                                                            @endif
                                                                        </td>

                                                                        <td>
                                                                            @if ($saleInfoList->sale_type == 0)
                                                                                {{ number_format($saleInfoList->net_amount, 2) }}
                                                                            @elseif($saleInfoList->sale_type == 1)
                                                                                {{ number_format($saleInfoList->down_payment, 2) }}
                                                                            @endif
                                                                        </td>
                                                                        @php
                                                                            if ($saleInfoList->sale_type == 0) {
                                                                                $total_cash +=
                                                                                    $saleInfoList->net_amount;
                                                                            } elseif ($saleInfoList->sale_type == 1) {
                                                                                $total_cash +=
                                                                                    $saleInfoList->down_payment;
                                                                                $credit =
                                                                                    $saleInfoList->net_amount -
                                                                                    $saleInfoList->down_payment +
                                                                                    $saleInfoList->interest_amount;
                                                                                $creditWithoutInterest =
                                                                                    $saleInfoList->net_amount -
                                                                                    $saleInfoList->down_payment;
                                                                                $total_credit += $credit;

                                                                                $total_without_interset += $creditWithoutInterest;
                                                                            }
                                                                        @endphp

                                                                        <td>
                                                                            @if ($saleInfoList->sale_type == 0)
                                                                                --
                                                                            @elseif($saleInfoList->sale_type == 1)
                                                                                {{ number_format($credit, 2) }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($saleInfoList->sale_type == 0)
                                                                                --
                                                                            @elseif($saleInfoList->sale_type == 1)
                                                                                {{ number_format($creditWithoutInterest, 2) }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($saleInfoList->sale_type == 0)
                                                                                --
                                                                            @elseif($saleInfoList->sale_type == 1)
                                                                                {{ $saleInfoList->interest_rate }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($saleInfoList->sale_type == 0)
                                                                                --
                                                                            @elseif($saleInfoList->sale_type == 1)
                                                                                {{ $saleInfoList->installment }}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @else
                                                                <tr
                                                                    class="{{ $saleInfoList->sale_type == 0 ? 'cash-sales' : 'hire-sales' }}">
                                                                    <td>{{ $saleInfoList->date_of_sale }}</td>

                                                                    <td>{{ $saleInfoList->created_at->format('h:ia') }}
                                                                    </td>

                                                                    <td>{{ $saleInfoList->inventory->date ?? '' }}</td>

                                                                    <td>
                                                                        @if (isset($saleInfoList->inventory))
                                                                            {{ date_diff(new \DateTime($saleInfoList->inventory->date), new \DateTime($saleInfoList->date_of_sale))->format('%mm %dd') }}
                                                                        @endif
                                                                    </td>

                                                                    <td>{{ $saleInfoList->invoice->invoice_code ?? '' }}
                                                                    </td>

                                                                    <td>
                                                                        {{ $saleInfoList->customer->nic_no ?? '-' }}
                                                                    </td>

                                                                    <td>
                                                                        @if ($saleInfoList->customer_type == 1)
                                                                            {{ $saleInfoList->customer->name ?? '' }}
                                                                        @elseif ($saleInfoList->customer_type == 2)
                                                                            {{ $saleInfoList->customer_name ?? '' }}
                                                                        @endif
                                                                    </td>

                                                                    <td>{{ $saleInfoList->introducer->name ?? '--' }}</td>

                                                                    <td>{{ $saleInfoList->products->productCategory->name ?? '' }}
                                                                    </td>

                                                                    <td>{{ $saleInfoList->products->barcode ?? '' }}</td>

                                                                    <td>
                                                                        @if ($saleInfoList->inventory->grns)
                                                                            {{ $saleInfoList->inventory->grns->vender->name ?? '' }}
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        @if ($saleInfoList->sale_type == 0)
                                                                            {{ 'Cash Sales' }}
                                                                        @else
                                                                            {{ 'Hire Sales' }}
                                                                        @endif
                                                                    </td>

                                                                    <td>{{ $saleInfoList->products->name ?? '' }}</td>

                                                                    <td>{{ $saleInfoList->quantity ?? '' }}</td>

                                                                    <td>{{ number_format($saleInfoList->inventory->purchase_price ?? 0, 2) }}
                                                                    </td>

                                                                    <td>{{ number_format($saleInfoList->inventory->sale_price ?? 0, 2) }}
                                                                    </td>

                                                                    <td>{{ number_format(($saleInfoList->inventory->purchase_price ?? 0) * $saleInfoList->quantity, 2) }}
                                                                    </td>

                                                                    <td>{{ number_format(($saleInfoList->inventory->sale_price ?? 0) * $saleInfoList->quantity, 2) }}
                                                                    </td>

                                                                    @php
                                                                        $total_sale_price +=
                                                                            ($saleInfoList->inventory->sale_price ??
                                                                                0) *
                                                                            $saleInfoList->quantity;
                                                                        $total_purchase_price +=
                                                                            ($saleInfoList->inventory->purchase_price ??
                                                                                0) *
                                                                            $saleInfoList->quantity;
                                                                        $grossProfit =
                                                                            $saleInfoList->net_amount -
                                                                            ($saleInfoList->inventory->purchase_price ??
                                                                                0) *
                                                                                $saleInfoList->quantity;
                                                                        $total_gross_profit += $grossProfit;

                                                                        $total_unit_byuing_price +=
                                                                            $saleInfoList->inventory->purchase_price ??
                                                                            0;
                                                                        $total_unit_selling_price +=
                                                                            $saleInfoList->inventory->sale_price ?? 0;
                                                                        $total_discount += $saleInfoList->discount ?? 0;
                                                                    @endphp

                                                                    <td>{{ number_format($grossProfit, 2) }}</td>
                                                                    <td>{{ number_format($saleInfoList->discount, 2) }}
                                                                    </td>

                                                                    <td>
                                                                        @if ($saleInfoList->interest_amount != null)
                                                                            {{ number_format($saleInfoList->interest_amount, 2) }}
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        @if ($saleInfoList->document_charges != '')
                                                                            {{ number_format($saleInfoList->document_charges, 2) }}
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </td>

                                                                    @php
                                                                        $total_docCharge +=
                                                                            $saleInfoList->document_charges;
                                                                        $total_interest +=
                                                                            $saleInfoList->interest_amount;
                                                                        $netProfit =
                                                                            $saleInfoList->net_amount -
                                                                            ($saleInfoList->inventory->purchase_price ??
                                                                                0) *
                                                                                $saleInfoList->quantity +
                                                                            $saleInfoList->interest_amount +
                                                                            $saleInfoList->document_charges;
                                                                        $total_net_profit += $netProfit;
                                                                    @endphp

                                                                    <td>
                                                                        @if ($saleInfoList->sale_type == 0)
                                                                            {{ number_format($netProfit, 2) }}
                                                                        @elseif($saleInfoList->sale_type == 1)
                                                                            {{ number_format($netProfit, 2) }}
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        @if ($saleInfoList->sale_type == 0)
                                                                            {{ number_format($saleInfoList->net_amount, 2) }}
                                                                        @elseif($saleInfoList->sale_type == 1)
                                                                            {{ number_format($saleInfoList->down_payment, 2) }}
                                                                        @endif
                                                                    </td>

                                                                    @php
                                                                        if ($saleInfoList->sale_type == 0) {
                                                                            $total_cash += $saleInfoList->net_amount;
                                                                        } elseif ($saleInfoList->sale_type == 1) {
                                                                            $total_cash += $saleInfoList->down_payment;
                                                                            $credit =
                                                                                $saleInfoList->net_amount -
                                                                                $saleInfoList->down_payment +
                                                                                $saleInfoList->interest_amount;
                                                                            $creditWithoutInterest =
                                                                                $saleInfoList->net_amount -
                                                                                $saleInfoList->down_payment;
                                                                            $total_credit += $credit;

                                                                            $total_without_interset += $creditWithoutInterest;
                                                                        }
                                                                    @endphp

                                                                    <td>
                                                                        @if ($saleInfoList->sale_type == 0)
                                                                            --
                                                                        @elseif($saleInfoList->sale_type == 1)
                                                                            {{ number_format($credit, 2) }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($saleInfoList->sale_type == 0)
                                                                            --
                                                                        @elseif($saleInfoList->sale_type == 1)
                                                                            {{ number_format($creditWithoutInterest, 2) }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($saleInfoList->sale_type == 0)
                                                                            --
                                                                        @elseif($saleInfoList->sale_type == 1)
                                                                            {{ $saleInfoList->interest_rate }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($saleInfoList->sale_type == 0)
                                                                            --
                                                                        @elseif($saleInfoList->sale_type == 1)
                                                                            {{ $saleInfoList->installment }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @elseif ($saleInfoList->modal_type == 2)
                                                            @foreach ($saleInfoList->saleItems as $saleItem)
                                                                @if ($category_id != '')
                                                                    @if ($saleItem->products->product_category_id == $category_id)
                                                                        <tr
                                                                            class="{{ $saleInfoList->sale_type == 0 ? 'cash-sales' : 'hire-sales' }}">
                                                                            <td>{{ $saleInfoList->date_of_sale }}</td>

                                                                            <td>{{ $saleInfoList->created_at->format('h:ia') }}
                                                                            </td>

                                                                            <td>{{ $saleItem->inventory->date ?? '' }}
                                                                            </td>

                                                                            <td>
                                                                                @if (isset($saleItem->inventory))
                                                                                    {{ date_diff(new \DateTime($saleItem->inventory->date), new \DateTime($saleInfoList->date_of_sale))->format('%mm %dd') }}
                                                                                @endif
                                                                            </td>

                                                                            <td>{{ $saleInfoList->invoice->invoice_code ?? '' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ $saleInfoList->customer->nic_no ?? '-' }}
                                                                            </td>

                                                                            <td>
                                                                                @if ($saleInfoList->customer_type == 1)
                                                                                    {{ $saleInfoList->customer->name ?? '' }}
                                                                                @elseif ($saleInfoList->customer_type == 2)
                                                                                    {{ $saleInfoList->customer_name ?? '' }}
                                                                                @endif
                                                                            </td>

                                                                            <td>{{ $saleInfoList->introducer->name ?? '--' }}
                                                                            </td>

                                                                            <td>{{ $saleItem->products->productCategory->name ?? '' }}
                                                                            </td>

                                                                            <td>{{ $saleItem->products->barcode ?? '' }}
                                                                            </td>

                                                                            <td>
                                                                                @if ($saleItem->inventory->grns)
                                                                                    {{ $saleItem->inventory->grns->vender->name ?? '' }}
                                                                                @endif
                                                                            </td>

                                                                            <td>
                                                                                @if ($saleItem->sale_type == 0)
                                                                                    {{ 'Cash Sales' }}
                                                                                @else
                                                                                    {{ 'Hire Sales' }}
                                                                                @endif
                                                                            </td>

                                                                            <td>{{ $saleItem->products->name ?? '' }}</td>

                                                                            <td>{{ $saleItem->quantity ?? '' }}</td>

                                                                            <td>{{ number_format($saleItem->inventory->purchase_price ?? 0, 2) }}
                                                                            </td>

                                                                            <td>{{ number_format($saleItem->inventory->sale_price ?? 0, 2) }}
                                                                            </td>

                                                                            <td>{{ number_format(($saleItem->inventory->purchase_price ?? 0) * $saleItem->quantity, 2) }}
                                                                            </td>

                                                                            <td>{{ number_format(($saleItem->inventory->sale_price ?? 0) * $saleItem->quantity, 2) }}
                                                                            </td>

                                                                            @php
                                                                                $total_sale_price +=
                                                                                    ($saleItem->inventory->sale_price ??
                                                                                        0) *
                                                                                    $saleItem->quantity;
                                                                                $total_purchase_price +=
                                                                                    ($saleItem->inventory
                                                                                        ->purchase_price ??
                                                                                        0) *
                                                                                    $saleItem->quantity;
                                                                                $grossProfit2 =
                                                                                    $saleItem->net_amount -
                                                                                    ($saleItem->inventory
                                                                                        ->purchase_price ??
                                                                                        0) *
                                                                                        $saleItem->quantity;
                                                                                $total_gross_profit += $grossProfit2;

                                                                                $total_unit_byuing_price +=
                                                                                    $saleItem->inventory
                                                                                        ->purchase_price ?? 0;
                                                                                $total_unit_selling_price +=
                                                                                    $saleItem->inventory->sale_price ??
                                                                                    0;
                                                                                $total_discount +=
                                                                                    $saleItem->discount ?? 0;
                                                                            @endphp

                                                                            <td>{{ number_format($grossProfit2, 2) }}</td>
                                                                            <td>{{ number_format($saleItem->discount, 2) }}
                                                                            </td>

                                                                            <td> -- </td>
                                                                            <td> -- </td>

                                                                            @php
                                                                                $netProfit2 =
                                                                                    $saleItem->net_amount -
                                                                                    ($saleItem->inventory
                                                                                        ->purchase_price ??
                                                                                        0) *
                                                                                        $saleItem->quantity +
                                                                                    $saleInfoList->interest_amount;
                                                                                $total_net_profit += $netProfit2;
                                                                            @endphp

                                                                            <td>
                                                                                @if ($saleInfoList->sale_type == 0)
                                                                                    {{ number_format($grossProfit2, 2) }}
                                                                                @elseif($saleInfoList->sale_type == 1)
                                                                                    {{ number_format($netProfit2, 2) }}
                                                                                @endif
                                                                            </td>

                                                                            <td> {{ number_format($saleItem->net_amount, 2) }}
                                                                            </td>

                                                                            @php
                                                                                $total_cash += $saleItem->net_amount;
                                                                            @endphp

                                                                            <td> -- </td>
                                                                            <td> -- </td>
                                                                            <td> -- </td>
                                                                            <td> -- </td>
                                                                        </tr>
                                                                    @endif
                                                                @else
                                                                    <tr
                                                                        class="{{ $saleInfoList->sale_type == 0 ? 'cash-sales' : 'hire-sales' }}">
                                                                        <td>{{ $saleInfoList->date_of_sale }}</td>

                                                                        <td>{{ $saleInfoList->created_at->format('h:ia') }}
                                                                        </td>

                                                                        <td>{{ $saleItem->inventory->date ?? '' }}</td>

                                                                        <td>
                                                                            @if (isset($saleItem->inventory))
                                                                                {{ date_diff(new \DateTime($saleItem->inventory->date), new \DateTime($saleInfoList->date_of_sale))->format('%mm %dd') }}
                                                                            @endif
                                                                        </td>

                                                                        <td>{{ $saleInfoList->invoice->invoice_code ?? '' }}
                                                                        </td>

                                                                        <td>
                                                                            {{ $saleInfoList->customer->nic_no ?? '-' }}
                                                                        </td>

                                                                        <td>
                                                                            @if ($saleInfoList->customer_type == 1)
                                                                                {{ $saleInfoList->customer->name ?? '' }}
                                                                            @elseif ($saleInfoList->customer_type == 2)
                                                                                {{ $saleInfoList->customer_name ?? '' }}
                                                                            @endif
                                                                        </td>

                                                                        <td>{{ $saleInfoList->introducer->name ?? '--' }}
                                                                        </td>

                                                                        <td>{{ $saleItem->products->productCategory->name ?? '' }}
                                                                        </td>

                                                                        <td>{{ $saleItem->products->barcode ?? '' }}</td>

                                                                        <td>
                                                                            @if ($saleItem->inventory->grns)
                                                                                {{ $saleItem->inventory->grns->vender->name ?? '' }}
                                                                            @endif
                                                                        </td>

                                                                        <td>
                                                                            @if ($saleItem->sale_type == 0)
                                                                                {{ 'Cash Sales' }}
                                                                            @else
                                                                                {{ 'Hire Sales' }}
                                                                            @endif
                                                                        </td>

                                                                        <td>{{ $saleItem->products->name ?? '' }}</td>

                                                                        <td>{{ $saleItem->quantity ?? '' }}</td>

                                                                        <td>{{ number_format($saleItem->inventory->purchase_price ?? 0, 2) }}
                                                                        </td>

                                                                        <td>{{ number_format($saleItem->inventory->sale_price ?? 0, 2) }}
                                                                        </td>

                                                                        <td>{{ number_format(($saleItem->inventory->purchase_price ?? 0) * $saleItem->quantity, 2) }}
                                                                        </td>

                                                                        <td>{{ number_format(($saleItem->inventory->sale_price ?? 0) * $saleItem->quantity, 2) }}
                                                                        </td>

                                                                        @php
                                                                            $total_sale_price +=
                                                                                ($saleItem->inventory->sale_price ??
                                                                                    0) *
                                                                                $saleItem->quantity;
                                                                            $total_purchase_price +=
                                                                                ($saleItem->inventory->purchase_price ??
                                                                                    0) *
                                                                                $saleItem->quantity;
                                                                            $grossProfit2 =
                                                                                $saleItem->net_amount -
                                                                                ($saleItem->inventory->purchase_price ??
                                                                                    0) *
                                                                                    $saleItem->quantity;
                                                                            $total_gross_profit += $grossProfit2;

                                                                            $total_unit_byuing_price +=
                                                                                $saleItem->inventory->purchase_price ??
                                                                                0;
                                                                            $total_unit_selling_price +=
                                                                                $saleItem->inventory->sale_price ?? 0;
                                                                            $total_discount += $saleItem->discount ?? 0;
                                                                        @endphp

                                                                        <td>{{ number_format($grossProfit2, 2) }}</td>
                                                                        <td>{{ number_format($saleItem->discount, 2) }}
                                                                        </td>

                                                                        <td> -- </td>
                                                                        <td> -- </td>

                                                                        @php
                                                                            $netProfit2 =
                                                                                $saleItem->net_amount -
                                                                                ($saleItem->inventory->purchase_price ??
                                                                                    0) *
                                                                                    $saleItem->quantity +
                                                                                $saleInfoList->interest_amount;
                                                                            $total_net_profit += $netProfit2;
                                                                        @endphp

                                                                        <td>
                                                                            @if ($saleInfoList->sale_type == 0)
                                                                                {{ number_format($grossProfit2, 2) }}
                                                                            @elseif($saleInfoList->sale_type == 1)
                                                                                {{ number_format($netProfit2, 2) }}
                                                                            @endif
                                                                        </td>

                                                                        <td> {{ number_format($saleItem->net_amount, 2) }}
                                                                        </td>

                                                                        @php
                                                                            $total_cash += $saleItem->net_amount;
                                                                        @endphp

                                                                        <td> -- </td>
                                                                        <td> -- </td>
                                                                        <td> -- </td>
                                                                        <td> -- </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                @if ($showTable == 'Yes')
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Total</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th>{{ number_format($total_unit_byuing_price, 2) }}</th>
                                                        <th>{{ number_format($total_unit_selling_price, 2) }}</th>
                                                        <th>{{ number_format($total_purchase_price, 2) }}</th>
                                                        <th>{{ number_format($total_sale_price, 2) }}</th>
                                                        <th>{{ number_format($total_gross_profit, 2) }}</th>
                                                        <th>{{ number_format($total_discount, 2) }}</th>
                                                        <th>{{ number_format($total_interest, 2) }}</th>
                                                        <th>{{ number_format($total_docCharge, 2) }}</th>
                                                        <th>{{ number_format($total_net_profit, 2) }}</th>
                                                        <th>{{ number_format($total_cash, 2) }}</th>
                                                        <th>{{ number_format($total_credit, 2) }}</th>
                                                        <th>{{ number_format($total_without_interset, 2) }}</th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                @endif
                                            </tfoot>
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
    <section>
        <div class="modal modelView" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document" style="width: auto !important; max-width: 1000px !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Instalment Summery</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class=" col-md-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Due Amount</th>
                                        {{-- <th>Branch</th> --}}
                                        <th>Paid Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="installmetTableBody">

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('.orderModel').on('click', function() {
            const saleId = $(this).attr("data-id");
            let row = "";
            let status;
            $.ajax({
                type: 'GET',
                url: '{{ 'installment/getProductInstallment/' }}' + saleId,
                beforeSend: function() {},
                success: function(data) {
                    result = data;
                    console.log(result);
                    $('#installmetTableBody').html("");
                    for (let i = 0; i < data.length; i++) {
                        let element = data[i].amount;
                        if (data[i].status == 1) {
                            status = "Paid";
                        } else if (data[i].status == 2) {
                            status = "Not Paid";
                        } else if (data[i].status == 3) {
                            status = "Over Due";
                        }

                        var paidAmount = 0.00;
                        if (data[i].paid_amount != null) {
                            paidAmount = data[i].paid_amount;
                        }

                        var no = i + 1;
                        // row+="<tr><td>"+data[i].id+"</td><td>"+data[i].amount+"</td><td>"+data[i].branch_id+"</td><td>"+data[i].paid_amount+"</td><td>"+data[i].installment_date+"</td><td>"+status+"</td></tr>";
                        row += "<tr><td>" + no + "</td><td>" + data[i].amount +
                            "</td><td>" + paidAmount + "</td><td>" + data[i]
                            .installment_date + "</td><td>" + status + "</td></tr>";

                    }
                },
                complete: function() {
                    $('#installmetTableBody').append(row);
                    $('.modelView').modal();
                }
            });

        });
    });
</script>
