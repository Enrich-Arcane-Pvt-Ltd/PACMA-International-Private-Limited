@extends('layouts.admin')
@section('page-title')
    {{ __('Price List') }}
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

        #stock_table .main_row:hover td {
            background-color: #d2e4eb !important;
        }
    </style>

    <section class="section">
        <div class="se-pre-con"></div>
        {{-- <div class="section-header">
        <h1>{{__('Stock')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Stock')}}</div>
    </div>
    </div> --}}

        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Stock') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Stock') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">

                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Product Price List') }}</h4>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="card-body p-0">
                                <div id="table-1_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table class="table table-flush" id="stock_table">
                                                    <thead class="thead-light">
                                                        <tr role="row">
                                                            <th>{{ __('Product Code') }}</th>
                                                            <th>{{ __('Sub Category') }}</th>
                                                            <th class="text-right">{{ __('Selling Price') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th class="text-right">{{ __('Discount') }}
                                                                ({{ $currencySymbol }})</th>
                                                            <th class="text-right">{{ __('Discount Cash Price') }}
                                                                ({{ $currencySymbol }})</th>
                                                            {{-- <th>{{__('Product #')}}</th>
                                                        <th>{{__('Name')}}</th> --}}
                                                            {{-- <th>{{ __('Description') }}</th>
                                                            <th>{{ __('Category') }}</th>
                                                            <th>{{ __('Brand') }}</th> --}}
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($products as $product)
                                                            <tr class="main_row" id="product_detail"
                                                                data-id="{{ $product['id'] }}">
                                                                <td>
                                                                    <a href="{{ route('product.show', $product['id']) }}"
                                                                        class="btn btn-outline-primary">
                                                                        {{ $product['barcode'] }}
                                                                    </a>
                                                                </td>
                                                                {{-- <td class="stock_td">{{ $product->barcode }}</td> --}}
                                                                <td class="stock_td description text-left"
                                                                    style="font-weight: bold;">
                                                                    {{ $product->subCategoryDetails['name'] ?? '--' }}</td>
                                                                <td class="stock_td description text-right"
                                                                    style="font-weight: bold;">
                                                                    {{ $product->hire_price !== null ? number_format($product->hire_price, 2) : '--' }}
                                                                </td>
                                                                <td class="stock_td description text-right"
                                                                    style="font-weight: bold;">
                                                                    {{ $product->discount_price !== null ? number_format($product->discount_price, 2) : '--' }}
                                                                </td>
                                                                <td class="stock_td description text-right"
                                                                    style="font-weight: bold;">
                                                                    {{ $product->sale_price !== null ? number_format($product->sale_price, 2) : '--' }}
                                                                </td>
                                                                {{-- <td class="stock_td">
                                                            {{ AUth::user()->productNumberFormat($product['product_id']) }}
                                                        </td>
                                                        <td class="stock_td">{{ $product->name }}</td> --}}
                                                                {{-- <td class="stock_td description">
                                                                    {{ $product->description }}
                                                                </td>
                                                                <td class="stock_td">
                                                                    {{ $product->categoryDetails['name'] ?? '--' }}</td>
                                                                <td class="stock_td">{{ $product->brands['name'] ?? '--' }}
                                                                </td> --}}
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

    <script>
        $(document).ready(function() {
            $(".se-pre-con").fadeOut('slow');

            if (localStorage.getItem("stock_page_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("stock_page_no"));
            }

            var stockTable = $('#stock_table').DataTable({
                "pageLength": 25,
            });

            stockTable.page(pageNo).draw(false);

            $('#stock_table').on('page.dt', function() {
                var pageInfo = stockTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("stock_page_no", pageNo);
            });
        });
    </script>
@endsection
