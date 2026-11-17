@extends('layouts.admin')
@section('page-title')
    {{ __('Stock Transfers') }}
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
            <h1>{{__('Stock Transfers')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Stock Transfers')}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Stock Transfers') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Stock Transfers') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('View Stock Transfers') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="stock_transfers_table">
                                                    <thead class="thead-light">
                                                        <tr>

                                                            <th> {{ __('Transfer #') }}</th>
                                                            <th> {{ __('Branch') }}</th>
                                                            <th> {{ __('Transfer Date') }}</th>
                                                            <th> {{ __('Total Amount') }} ({{ $currencySymbol }})</th>
                                                            <th class="text-right"> {{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($transfers as $transfer)
                                                            <tr class="font-style">
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary view">{{ \Auth::user()->stockTransferNumberFormat($transfer->id) }}
                                                                    </button>
                                                                </td>
                                                                <td> {{ $transfer->branch->name ?? '' }} </td>

                                                                <td>{{ Auth::user()->dateFormat($transfer->date) }}</td>

                                                                <td>
                                                                    {{ number_format($transfer->total_amount ?? 0, 2) }}
                                                                    {{-- {{ \Auth::user()->priceFormat($transfer->total_amount ?? '0.00') }} --}}
                                                                </td>

                                                                <td class="action text-right">
                                                                    <button type="button" class="btn btn-info view-invoice"
                                                                        value="{{ $transfer->id }}"><i
                                                                            class="fas fa-eye"></i></button>
                                                                    <button type="button"
                                                                        class="btn btn-primary print-invoice"
                                                                        value="{{ $transfer->id }}"><i
                                                                            class="fas fa-print"></i></button>
                                                                </td>
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
                </div>
            </div>
        </div>
    </section>
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $(".se-pre-con").fadeOut('slow');
        if (localStorage.getItem("stock_transfers_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("stock_transfers_page_no"));
        }
        var stockTransfersTable = $('#stock_transfers_table').DataTable({
            order: [
                [0, "desc"]
            ]
        });
        stockTransfersTable.page(pageNo).draw(false);

        $('#stock_transfers_table').on('page.dt', function() {
            var pageInfo = stockTransfersTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("stock_transfers_page_no", pageNo);
        });

        $('body').on('click', '.view-invoice', function() {
            var transId = $(this).val();
            window.open("{{ url('stock_transfers/print_preview') }}/" + transId + "/view");
        });

        $('body').on('click', '.print-invoice', function() {
            var transId = $(this).val();
            window.open("{{ url('stock_transfers/print_preview') }}/" + transId + "/print");
        });
    });
</script>
