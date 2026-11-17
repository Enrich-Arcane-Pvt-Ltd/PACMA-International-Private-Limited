@extends('layouts.admin')

@section('page-title')
    {{ __('In & Out Stock Report') }}
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

        table tfoot {
            display: table-row-group;
        }

        .customer-sel.form-control:not(.form-control-sm):not(.form-control-lg) {
            padding: 0px;
        }

        .btn-light {
            background-color: #fdfdff !important;
            font-size: 13px;
            font-weight: 400;
            color: var(--bs-body-color);
        }

        .table .thead-light th {
            border-right: 2px solid #ccc;
        }

        .table .thead-light th:last-child {
            border-right: none;
        }

        .table .thead-light tr:first-child th,
        .table .thead-light th:first-child,
        .table tbody td:first-child {
            border-right: 2px solid #7e7e7e;
        }

        .table .thead-light tr:first-child th:last-child {
            border-right: none;
        }

        .table .thead-light th:nth-child(6),
        .table .thead-light th:nth-child(10),
        .table .thead-light th:nth-child(12) {
            border-right: 2px solid #7e7e7e;
        }

        .table tbody td {
            border-right: 2px solid #ccc;
        }

        .table tbody td:last-child {
            border-right: none;
        }

        .table tbody td:nth-child(6),
        .table tbody td:nth-child(10),
        .table tbody td:nth-child(12) {
            border-right: 2px solid #7e7e7e;
        }

        .white-space-nowrap {
            white-space: nowrap;
        }
    </style>

    <section class="section">
        <div class="se-pre-con"></div>

        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('All Products In & Out Stock Report') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Cash Sales & Hire Sales') }}</div>
                                    <div class="breadcrumb-item">{{ __('Reports') }}</div>
                                    <div class="breadcrumb-item">{{ __('All Products In & Out Stock Report') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <form method="post" action="{{ route('stock_in_out_report.search') }}">
                            @csrf

                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4 class="h4 mb-0">Filter</h4>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="start_date">Start Purchase Date</label>
                                        <input class="form-control font-style" name="start_date" type="date"
                                            value="{{ $start_date ?? '' }}" id="start_date">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_month">End Purchase Date</label>
                                        <input class="form-control font-style" name="end_date" type="date"
                                            value="{{ $end_date ?? '' }}" id="end_date">
                                    </div>

                                    @php
                                        $duration = '';
                                        $from = '';
                                        $to = '';

                                        if (isset($start_date) && $start_date != '') {
                                            $from = 'From ' . Auth::user()->dateFormat($start_date);
                                        }

                                        if (isset($end_date) && $end_date != '') {
                                            $to = ' To ' . Auth::user()->dateFormat($end_date);
                                        }

                                        $duration = $from . $to;
                                    @endphp

                                    <input type="hidden" id="duration" value="{{ $duration }}">
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
                    <div class="col-12" id="bill-container">
                        <div class="card">

                            @if ($showTable == 'Yes')
                                @if (count($productStocks) > 0)
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between w-100" style="overflow-x: auto;">

                                            <table class="table table-flush" id="report-dataTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th></th>
                                                        <th colspan="5" class="text-center ">In</th>
                                                        <th colspan="4" class="text-center  ">Out</th>
                                                        <th colspan="2" class="text-center  ">Balance</th>
                                                        <th class="text-center  ">Remarks</th>
                                                    </tr>

                                                    <tr>
                                                        <th> {{ __('Product') }}</th>
                                                        <th> {{ __('Purchasing Date') }}</th>
                                                        <th> {{ __('Purchasing Number') }}</th>
                                                        <th> {{ __('Purchasing Price Buying') }} ({{ $currencySymbol }})
                                                        </th>
                                                        <th> {{ __('Total Qty') }}</th>
                                                        <th> {{ __('Total Purchase Price') }} ({{ $currencySymbol }})</th>
                                                        <th> {{ __('Selling Date') }}</th>
                                                        <th> {{ __('Invoice Number') }}</th>
                                                        <th> {{ __('QTY') }}</th>
                                                        <th> {{ __('Total Purchase Price') }} ({{ $currencySymbol }})</th>
                                                        <th> {{ __('Purchase Price') }} ({{ $currencySymbol }})</th>
                                                        <th> {{ __('Remaining Qty') }}</th>
                                                        <th> {{ __('Remarks') }}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($productStocks as $stock)
                                                        @foreach ($stock['invenvtories'] as $inventory)
                                                            <tr>
                                                                <td>{{ $stock['product']->barcode ?? '' }}</td>
                                                                <td class="text-center white-space-nowrap">
                                                                    {{ $inventory->date }}</td>
                                                                <td class="text-center">
                                                                    {{ Auth::user()->purchaseOrderNumberFormat($inventory->grns->order_id) }}
                                                                </td>
                                                                <td class="text-right">
                                                                    {{ number_format($inventory->purchase_price, 2) }}</td>
                                                                <td class="text-center">{{ $inventory->grn_qty }}</td>
                                                                <td class="text-right">
                                                                    {{ number_format($inventory->purchase_price * $inventory->grn_qty, 2) }}
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-right">
                                                                    {{ number_format($inventory->purchase_price * $inventory->grn_qty, 2) }}
                                                                </td>
                                                                <td class="text-center">{{ $inventory->grn_qty }}</td>
                                                                <td></td>
                                                            </tr>

                                                            @php
                                                                $balancePurchasePrice =
                                                                    $inventory->purchase_price * $inventory->grn_qty;
                                                                $balanceQty = $inventory->grn_qty;
                                                            @endphp

                                                            @foreach ($inventory->sales as $sale)
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td class="text-center white-space-nowrap">
                                                                        {{ $sale->date_of_sale }}</td>
                                                                    <td class="text-center">
                                                                        {{ Auth::user()->invoiceNumberFormat($sale->invoice_id) }}
                                                                    </td>
                                                                    <td class="text-center">{{ $sale->quantity }}</td>
                                                                    <td class="text-right">
                                                                        @if ($sale->purchase_price != null)
                                                                            {{ number_format($sale->purchase_price * $sale->quantity, 2) }}
                                                                        @else
                                                                            {{ number_format($inventory->purchase_price * $sale->quantity, 2) }}
                                                                        @endif
                                                                    </td>
                                                                    @php
                                                                        if ($sale->purchase_price != null) {
                                                                            $balancePurchasePrice -=
                                                                                $sale->purchase_price * $sale->quantity;
                                                                        } else {
                                                                            $balancePurchasePrice -=
                                                                                $inventory->purchase_price *
                                                                                $sale->quantity;
                                                                        }
                                                                        $balanceQty -= $sale->quantity;
                                                                    @endphp
                                                                    <td class="text-right">
                                                                        {{ number_format($balancePurchasePrice, 2) }}</td>
                                                                    <td class="text-center">{{ $balanceQty }}</td>
                                                                    <td></td>
                                                                </tr>
                                                            @endforeach

                                                            @foreach ($inventory->saleItems as $saleItem)
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td class="text-center white-space-nowrap">
                                                                        {{ $saleItem->date_of_sale }}</td>
                                                                    <td class="text-center">
                                                                        {{ Auth::user()->invoiceNumberFormat($saleItem->invoice_id) }}
                                                                    </td>
                                                                    <td class="text-center">{{ $saleItem->quantity }}</td>
                                                                    <td class="text-right">
                                                                        @if ($saleItem->purchase_price != null)
                                                                            {{ number_format($saleItem->purchase_price * $saleItem->quantity, 2) }}
                                                                        @else
                                                                            {{ number_format($inventory->purchase_price * $saleItem->quantity, 2) }}
                                                                        @endif
                                                                    </td>
                                                                    @php
                                                                        if ($saleItem->purchase_price != null) {
                                                                            $balancePurchasePrice -=
                                                                                $saleItem->purchase_price *
                                                                                $saleItem->quantity;
                                                                        } else {
                                                                            $balancePurchasePrice -=
                                                                                $inventory->purchase_price *
                                                                                $saleItem->quantity;
                                                                        }
                                                                        $balanceQty -= $saleItem->quantity;
                                                                    @endphp
                                                                    <td class="text-right">
                                                                        {{ number_format($balancePurchasePrice, 2) }}</td>
                                                                    <td class="text-center">{{ $balanceQty }}</td>
                                                                    <td></td>
                                                                </tr>
                                                            @endforeach

                                                            @foreach ($inventory->stockTransferItems as $transferItem)
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td class="text-center white-space-nowrap">
                                                                        {{ $transferItem->stock_transfer_parent->date ?? '' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ Auth::user()->stockTransferNumberFormat($transferItem->stock_transfer_id) }}
                                                                    </td>
                                                                    <td class="text-center">{{ $transferItem->quantity }}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {{ number_format($transferItem->purchase_price * $transferItem->quantity, 2) }}
                                                                    </td>
                                                                    @php
                                                                        $balancePurchasePrice -=
                                                                            $transferItem->purchase_price *
                                                                            $transferItem->quantity;
                                                                        $balanceQty -= $transferItem->quantity;
                                                                    @endphp
                                                                    <td class="text-right">
                                                                        {{ number_format($balancePurchasePrice, 2) }}</td>
                                                                    <td class="text-center">{{ $balanceQty }}</td>
                                                                    <td>{{ _('Transfers ' . $transferItem->stock_transfer_parent->branchto->name ?? '') }}
                                                                        Branch</td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach

                                                        @foreach ($stock['transfersInvenvtories'] as $inventory)
                                                            <tr>
                                                                <td></td>
                                                                <td class="text-center white-space-nowrap">
                                                                    {{ $inventory->date }}</td>
                                                                <td></td>
                                                                <td class="text-right">
                                                                    {{ number_format($inventory->purchase_price, 2) }}</td>
                                                                <td class="text-right">
                                                                    {{ number_format($inventory->purchase_price * $inventory->grn_qty, 2) }}
                                                                </td>
                                                                <td class="text-center">{{ $inventory->grn_qty }}</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-right">
                                                                    {{ number_format($inventory->purchase_price * $inventory->grn_qty, 2) }}
                                                                </td>
                                                                <td class="text-center">{{ $inventory->grn_qty }}</td>
                                                                <td>{{ _('Purchased From ' . $inventory->transferFrom->name ?? '') }}
                                                                    Branch</td>
                                                            </tr>

                                                            @php
                                                                $balancePurchasePrice =
                                                                    $inventory->purchase_price * $inventory->grn_qty;
                                                                $balanceQty = $inventory->grn_qty;
                                                            @endphp

                                                            @foreach ($inventory->sales as $sale)
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td class="text-center white-space-nowrap">
                                                                        {{ $sale->date_of_sale }}</td>
                                                                    <td class="text-center">
                                                                        {{ Auth::user()->invoiceNumberFormat($sale->invoice_id) }}
                                                                    </td>
                                                                    <td class="text-center">{{ $sale->quantity }}</td>
                                                                    <td class="text-right">
                                                                        @if ($sale->purchase_price != null)
                                                                            {{ number_format($sale->purchase_price * $sale->quantity, 2) }}
                                                                        @else
                                                                            {{ number_format($inventory->purchase_price * $sale->quantity, 2) }}
                                                                        @endif
                                                                    </td>
                                                                    @php
                                                                        if ($sale->purchase_price != null) {
                                                                            $balancePurchasePrice -=
                                                                                $sale->purchase_price * $sale->quantity;
                                                                        } else {
                                                                            $balancePurchasePrice -=
                                                                                $inventory->purchase_price *
                                                                                $sale->quantity;
                                                                        }
                                                                        $balanceQty -= $sale->quantity;
                                                                    @endphp
                                                                    <td class="text-right">
                                                                        {{ number_format($balancePurchasePrice, 2) }}</td>
                                                                    <td class="text-center">{{ $balanceQty }}</td>
                                                                    <td></td>
                                                                </tr>
                                                            @endforeach

                                                            @foreach ($inventory->saleItems as $saleItem)
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td class="text-center white-space-nowrap">
                                                                        {{ $saleItem->date_of_sale }}</td>
                                                                    <td class="text-center">
                                                                        {{ Auth::user()->invoiceNumberFormat($saleItem->invoice_id) }}
                                                                    </td>
                                                                    <td class="text-center">{{ $saleItem->quantity }}</td>
                                                                    <td class="text-right">
                                                                        @if ($saleItem->purchase_price != null)
                                                                            {{ number_format($saleItem->purchase_price * $saleItem->quantity, 2) }}
                                                                        @else
                                                                            {{ number_format($inventory->purchase_price * $saleItem->quantity, 2) }}
                                                                        @endif
                                                                    </td>
                                                                    @php
                                                                        if ($saleItem->purchase_price != null) {
                                                                            $balancePurchasePrice -=
                                                                                $saleItem->purchase_price *
                                                                                $saleItem->quantity;
                                                                        } else {
                                                                            $balancePurchasePrice -=
                                                                                $inventory->purchase_price *
                                                                                $saleItem->quantity;
                                                                        }
                                                                        $balanceQty -= $saleItem->quantity;
                                                                    @endphp
                                                                    <td class="text-right">
                                                                        {{ number_format($balancePurchasePrice, 2) }}</td>
                                                                    <td class="text-center">{{ $balanceQty }}</td>
                                                                    <td></td>
                                                                </tr>
                                                            @endforeach

                                                            @foreach ($inventory->stockTransferItems as $transferItem)
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td class="text-center white-space-nowrap">
                                                                        {{ $transferItem->stock_transfer_parent->date ?? '' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ Auth::user()->stockTransferNumberFormat($transferItem->stock_transfer_id) }}
                                                                    </td>
                                                                    <td class="text-center">{{ $transferItem->quantity }}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {{ number_format($transferItem->purchase_price * $transferItem->quantity, 2) }}
                                                                    </td>
                                                                    @php
                                                                        $balancePurchasePrice -=
                                                                            $transferItem->purchase_price *
                                                                            $transferItem->quantity;
                                                                        $balanceQty -= $transferItem->quantity;
                                                                    @endphp
                                                                    <td class="text-right">
                                                                        {{ number_format($balancePurchasePrice, 2) }}</td>
                                                                    <td class="text-center">{{ $balanceQty }}</td>
                                                                    <td>{{ _('Transfers ' . $transferItem->stock_transfer_parent->branchto->name ?? '') }}
                                                                        Branch</td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                @else
                                    <div id="empty-message" style="text-align: center; padding: 5rem; color: #d2d2d2;">
                                        <img src="{{ asset('assets') }}/img/icons/search_icon.webp" class="mb-3"
                                            style="width: 125px; opacity: .5;" alt="No Data">
                                        <p>No data available in this table</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('script-page')
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
            $('.se-pre-con').fadeOut('slow');

            var filename = $('#file_name').html();
            var ecxelFilename = 'All Products IN and Out Stock Report';

            if ($('#duration').val() != undefined) {
                var duration = $('#duration').val();
            } else {
                var duration = "";
            }

            $('#report-dataTable').DataTable({
                dom: 'lBfrtip',
                "ordering": false,
                pageLength: 100,
                buttons: [{
                    extend: 'excel',
                    title: ecxelFilename + duration,
                    footer: true
                }, {
                    extend: 'print',
                    title: function() {
                        return "<h4>" + filename + duration + "</h4>";
                    },
                    footer: true
                }, {
                    extend: 'csv',
                    title: ecxelFilename + duration,
                    footer: true
                }]
            });
        });
    </script>
@endpush
