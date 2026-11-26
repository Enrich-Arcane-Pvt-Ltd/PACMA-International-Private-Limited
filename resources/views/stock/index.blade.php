@extends('layouts.admin')
@section('page-title')
    {{ __('Stock') }}
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
                                <h4>{{ __('Manage Stock') }}</h4>
                                <div class="col-auto">
                                    @can('create stock transfer')
                                        <a href="#" data-size="2xl" data-url="{{ route('item.transfer') }}"
                                            data-ajax-popup="true" data-title="{{ __('Transfer Item') }}"
                                            class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{ __('Transfer Item') }}</span>
                                        </a>
                                    @endcan
                                    @can('view stock transfer')
                                        <a href="{{ route('stock_transfers.transfers') }}"
                                            class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--text"> {{ __('View Transfers') }}</span>
                                        </a>
                                    @endcan
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
                                                <table class="table table-flush" id="stock_table">
                                                    <thead class="thead-light">
                                                        <tr role="row">
                                                            <th>{{ __('Product Code') }}</th>
                                                            <th>{{ __('Product Description') }}</th>
                                                            {{-- <th>{{__('Barcode')}}</th> --}}
                                                            <th>{{ __('Stock Qty.') }}</th>
                                                            {{-- <th>{{ __('Description') }}</th> --}}
                                                            <th>{{ __('Category') }}</th>
                                                            <th>{{ __('Sub Category') }}</th>
                                                            <th>{{ __('Model') }}</th>
                                                            <th>{{ __('Status') }}</th>
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
                                                                {{-- <td>
                                                            <a href="{{ route('product.show', $product['id']) }}"
                                                                class="btn btn-outline-primary">
                                                                {{ AUth::user()->productNumberFormat($product['product_id']) }}
                                                            </a>
                                                        </td> --}}
                                                                <td class="stock_td">{{ $product->name }}</td>
                                                                {{-- <td class="stock_td">{{ $product->barcode }}</td> --}}
                                                                <td>
                                                                    @if (count($product->inventory) > 0)
                                                                        @foreach ($product->inventory as $stock)
                                                                            <a href="#" data-size="2xl"
                                                                                data-url="{{ route('stock.quantity', $product['id']) }}"
                                                                                data-ajax-popup="true"
                                                                                data-title="{{ $product->name . ' ' . 'Stock' }}"
                                                                                class="btn btn-outline-primary">
                                                                                {{ $stock->remain_qty }}
                                                                            </a>
                                                                        @endforeach
                                                                    @else
                                                                        <a href="#" data-size="2xl"
                                                                            data-url="{{ route('stock.quantity', $product['id']) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="{{ $product->name . ' ' . 'Stock' }}"
                                                                            class="btn btn-outline-primary">
                                                                            {{ 0 }}
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                                {{-- <td class="stock_td description">
                                                                    {{ $product->description }}
                                                                </td> --}}
                                                                <td class="stock_td">
                                                                    {{ $product->categoryDetails['name'] ?? '--' }}</td>
                                                                <td class="stock_td">
                                                                    {{ $product->subCategoryDetails['name'] ?? '--' }}</td>
                                                                <td class="stock_td">{{ $product->brands['name'] ?? '--' }}
                                                                </td>
                                                                <td class="stock_td">
                                                                    @if (count($product->inventory) > 0)
                                                                        @foreach ($product->inventory as $stock)
                                                                            @if ($stock->remain_qty >= 10)
                                                                                <span class="badge badge-success">In
                                                                                    Stock</span>
                                                                            @elseif ($stock->remain_qty < 10 && $stock->remain_qty > 0)
                                                                                <span class="badge badge-warning">Minimum
                                                                                    Stock</span>
                                                                            @elseif ($stock->remain_qty == 0)
                                                                                <span class="badge badge-danger">Out of
                                                                                    Stock</span>
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        <span class="badge badge-danger">Out of Stock</span>
                                                                    @endif
                                                                </td>

                                                                {{-- <td class="action text-right">
                                                                    @can('edit product & service')
                                                                        <a href="#" class="btn btn-primary btn-action mr-1" data-size="xl" data-url="{{ route('product.edit',$product->id) }}"
                                                        data-ajax-popup="true" data-title="{{__('Edit Product')}}"
                                                        data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        @endcan

                                                        @if ($product->status == 1)
                                                        <button class="btn btn-danger btn-action unblock"
                                                            data-original-title="{{__('Block')}}" data-toggle="tooltip"
                                                            value="{{$product->id}}">
                                                            <i class="fas fa-lock"></i>
                                                        </button>
                                                        @else
                                                        <button class="btn btn-success btn-action delete"
                                                            data-toggle="tooltip"
                                                            data-original-title="{{__('Unblock')}}"
                                                            value="{{$product->id}}">
                                                            <i class="fas fa-unlock"></i>
                                                        </button>
                                                        @endif
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
        // var products = <?php echo json_encode($products); ?>;
        // $(document).ready(function() {
        //     $(".se-pre-con").fadeOut('slow');
        //     if (localStorage.getItem("stock_page_no") == null) {
        //         var pageNo = 0;
        //     } else {
        //         var pageNo = Number(localStorage.getItem("stock_page_no"));
        //     }
        //     var stockTable = $('#stock_table').DataTable({
        //         order: [
        //             [8, "asc"]
        //         ]
        //     });
        //     stockTable.page(pageNo).draw(false);

        //     $('#stock_table').on('page.dt', function() {
        //         var pageInfo = stockTable.page.info();
        //         pageNo = pageInfo.page;
        //         localStorage.setItem("stock_page_no", pageNo);
        //     });
        // });
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

        $('body').on('click', '.stock_td', function() {
            var productId = $(this).closest('tr').attr("data-id");
            window.location.href = "{{ url('product') }}/" + productId;
        });


        $("body").on("click", ".delete", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Continue!'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("product/unblock/" + value);
                }
            });
        });

        $("body").on("click", ".unblock", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Continue!'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("product/unblocktwo/" + value);
                }
            });
        });
    </script>
@endsection
