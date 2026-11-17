@extends('layouts.admin')

@section('page-title')
    {{ __('Product Items') }}
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

        #products_table thead th {
            vertical-align: middle;
        }

        .nowrap {
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
                            <h4 class="mt-2 ">{{ __('Product Items') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item">{{ __('Product Items') }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">

                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100">
                                <h4>{{ __('Manage Product Items') }}</h4>
                                <div class="card-header-action">
                                    @can('create product')
                                        <a href="#" data-size="2xl" data-url="{{ route('product.create') }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Product') }}"
                                            class="btn btn-icon icon-left btn-primary">
                                            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                            <span class="btn-inner--text"> {{ __('Create') }}</span>
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
                                                <table class="table table-flush" id="products_table">
                                                    <thead class="thead-light">
                                                        <tr role="row">
                                                            <th>{{ __('Product #') }}</th>
                                                            <th>{{ __('Name') }}</th>
                                                            {{-- <th>{{__('Serial Number')}}</th> --}}
                                                            <th>{{ __('Description') }}</th>
                                                            <th>{{ __('Brand') }}</th>
                                                            <th>{{ __('Category') }}</th>
                                                            <th class="nowrap">{{ __('Sub Category') }}</th>
                                                            @if (Gate::check('edit product') || Gate::check('block product') || Gate::check('view product') || Auth::user()->canDeleteProduct())
                                                                <th class="text-center">{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($products as $product)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('product.show', $product['id']) }}"
                                                                        class="btn btn-outline-primary w-100">
                                                                        {{ $product->barcode }}
                                                                    </a>
                                                                </td>
                                                                <td class="product_td align-middle">{{ $product->name }}
                                                                </td>
                                                                {{-- <td class="product_td align-middle nowrap">
                                                            {{ $product->serial_number ?? "" }}</td> --}}
                                                                <td class="product_td align-middle description">
                                                                    {{ $product->description }}</td>
                                                                <td class="product_td align-middle">
                                                                    {{ $product->productBrand->name ?? ($product->productBrand['name'] ?? '-') }}</td>
                                                                <td class="product_td align-middle">
                                                                    {{ $product->productCategory->name ?? ($product->productCategory['name'] ?? '-') }}</td>
                                                                <td class="product_td align-middle">
                                                                    {{ $product->productSubCategory->name ?? ($product->productSubCategory['name'] ?? '-') }}</td>

                                                                @if (Gate::check('edit product') || Gate::check('block product') || Gate::check('view product') || Auth::user()->canDeleteProduct())
                                                                    <td class="action text-center">
                                                                        @can('view product')
                                                                            <a href="{{ route('product.show', $product['id']) }}"
                                                                                class="btn btn-info btn-action mr-1"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('View') }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        @endcan

                                                                        @can('edit product')
                                                                            <a href="#"
                                                                                class="btn btn-primary btn-action mr-1"
                                                                                data-size="2xl"
                                                                                data-url="{{ route('product.edit', $product->id) }}"
                                                                                data-ajax-popup="true"
                                                                                data-title="{{ __('Edit Product') }}"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Edit') }}">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </a>
                                                                        @endcan

                                                                        @if ($product->status == 1)
                                                                            @can('block product')
                                                                                <button
                                                                                    class="btn btn-danger btn-action mr-1 block"
                                                                                    data-original-title="{{ __('Block') }}"
                                                                                    data-toggle="tooltip"
                                                                                    value="{{ $product->id }}">
                                                                                    <i class="fas fa-lock"></i>
                                                                                </button>
                                                                            @endcan
                                                                        @else
                                                                            @can('block product')
                                                                                <button
                                                                                    class="btn btn-success btn-action mr-1 unblock"
                                                                                    data-toggle="tooltip"
                                                                                    data-original-title="{{ __('Unblock') }}"
                                                                                    value="{{ $product->id }}">
                                                                                    <i class="fas fa-unlock"></i>
                                                                                </button>
                                                                            @endcan
                                                                        @endif

                                                                        @if (Auth::user()->canDeleteProduct())
                                                                            <button class="btn btn-danger btn-action mr-1 delete-product"
                                                                                data-id="{{ $product->id }}"
                                                                                data-name="{{ $product->name }}"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="{{ __('Delete') }}">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                            <form id="delete-form-{{ $product->id }}" action="{{ route('product.destroy', $product->id) }}" method="POST" style="display: none;">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                            </form>
                                                                        @endif
                                                                    </td>
                                                                @endif

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

            if (localStorage.getItem("product_page_no") == null) {
                var pageNo = 0;
            } else {
                var pageNo = Number(localStorage.getItem("product_page_no"));
            }

            var productsTable = $('#products_table').DataTable({
                order: [
                    [1, "asc"]
                ],
                columnDefs: [
                    {
                        targets: [6],
                        searchable: false,
                        orderable: false
                    }
                ]
            });

            productsTable.page(pageNo).draw(false);

            $('#products_table').on('page.dt', function() {
                var pageInfo = productsTable.page.info();
                pageNo = pageInfo.page;
                localStorage.setItem("product_page_no", pageNo);
            });
        });

        $("body").on("click", ".block", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure you want to block this product ?',
                text: "Blocking this product will not allow it to use in this company.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#fc544b',
                confirmButtonText: 'Block'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("product/block/" + value);
                }
            });
        });

        $("body").on("click", ".unblock", function() {
            var value = $(this).val();
            event.preventDefault();
            var form = $(this).parents('form');
            Swal.fire({
                title: 'Are you sure you want to unblock this product ?',
                text: "Unblocking this product will allow it to use in this company.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#fc544b',
                confirmButtonText: 'Unblock'
            }).then((result) => {
                if (result.value) {
                    window.location.replace("product/unblock/" + value);
                }
            });
        });

        $("body").on("click", ".delete-product", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name') || 'this product';
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("Are you sure you want to delete") }} "' + name + '"? {{ __("This action cannot be undone.") }}',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fc544b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("Yes, delete it!") }}'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    </script>
@endsection
