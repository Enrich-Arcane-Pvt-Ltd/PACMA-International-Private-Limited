@extends('layouts.admin')
@section('page-title')
    {{ __('Product Detail') }}
@endsection

@push('css-page')
    {{-- <link rel="stylesheet" href="{{asset('assets')}}/show/bootstrap.min.css"> --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/show/mdb-pro.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/show/mdb.ecommerce.min.css">
@endpush

@section('content')
    <style>
        .navbar {
            box-shadow: none;
        }

        .no-zoom {
            pointer-events: none !important;
            /* Disable link clicks */
        }

        .no-zoom-img {
            cursor: default !important;
            /* Remove zoom-in cursor */
            transform: none !important;
            /* Disable zoom effect */
            transition: none !important;
            /* Remove zoom transition */
        }
    </style>
    <section class="section">
        {{-- <div class="section-header">
        <h1>{{__('Product Detail')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item active"><a href="{{route('product.index')}}">{{__('Product')}}</a></div>
        <div class="breadcrumb-item">{{$product->name}}</div>
    </div>
    </div> --}}
        <div class="section-body">
            <div class="row justify-content-center">


                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Product Detail') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                    <div class="breadcrumb-item active"><a
                                            href="{{ route('product.index') }}">{{ __('Product') }}</a></div>
                                    <div class="breadcrumb-item">{{ $product->name }}</div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <h4>{{ __('Manage Product Detail') }}</h4>
                                <div class="card-header-action">
                                    @can('edit product')
                                        <a href="#" class="btn btn-primary mr-1" data-size="2xl"
                                            data-url="{{ route('product.edit', $product->id) }}" data-ajax-popup="true"
                                            data-title="{{ __('Edit Product') }}" data-toggle="tooltip"
                                            data-original-title="{{ __('Edit') }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @endcan
                                    @if (Auth::user()->canDeleteProduct())
                                        <button class="btn btn-danger mr-1 delete-product"
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
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <div id="mdb-lightbox-ui"></div>
                                    <div class="mdb-lightbox">
                                        <div class="row product-gallery mx-1">

                                            <div class="col-12 mb-3">
                                                <figure class="view overlay rounded z-depth-1 main-img d-flex align-items-center justify-content-center"
                                                    style="height: 450px; width: 100%; background-color: #f8f9fa; border: 1px solid #e9ecef; overflow: hidden; margin-bottom: 0;">
                                                    <img id="main_product_image"
                                                        src="{{ asset($product->image_1 ?? ($product->image_2 ?? ($product->image_3 ?? 'assets/img/products/default.png'))) }}"
                                                        alt="{{ $product->name }}"
                                                        class="img-fluid no-zoom-img"
                                                        style="max-height: 100%; max-width: 100%; width: auto; height: auto; object-fit: contain; pointer-events: none;">
                                                </figure>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    @foreach ([1 => $product->image_1, 2 => $product->image_2, 3 => $product->image_3] as $imgIndex => $imgPath)
                                                        @if(!empty($imgPath))
                                                            <div class="col-4">
                                                                <div class="view overlay rounded z-depth-1 gallery-item hoverable product-thumb-box d-flex align-items-center justify-content-center"
                                                                    style="height: 100px; background-color: #f8f9fa; border: 2px solid {{ $imgIndex === 1 ? '#007bff' : '#e9ecef' }}; cursor: pointer; overflow: hidden; border-radius: 4px;"
                                                                    onclick="switchMainImage('{{ asset($imgPath) }}', this)">
                                                                    <img src="{{ asset($imgPath) }}"
                                                                        alt="Image {{ $imgIndex }}"
                                                                        class="img-fluid"
                                                                        style="max-height: 100%; max-width: 100%; width: auto; height: auto; object-fit: contain; pointer-events: none;">
                                                                    <div class="mask rgba-white-slight"></div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-md-6">

                                    <h5>{{ $product->name }}</h5>
                                    {{-- <p class="mb-2 text-muted text-uppercase small">Shirts</p>
                            <p><span class="mr-1"><strong>$17.99</strong></span></p> --}}
                                    <p class="pt-1">{{ $product->description }}</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th class="pl-0 w-25" scope="row"><strong>Brand</strong></th>
                                                    <td>{{ $product->productBrand->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="pl-0 w-25" scope="row"><strong>Main Category</strong></th>
                                                    <td>{{ $product->productCategory->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="pl-0 w-25" scope="row"><strong>Sub Category</strong></th>
                                                    <td>{{ $product->productSubCategory->name ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>

                                </div>
                            </div>


                            <div class="classic-tabs">

                                <ul class="nav tabs-primary nav-justified" id="advancedTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" id="product-detail-tab" data-toggle="tab"
                                            href="#product-detail" role="tab" aria-controls="description"
                                            aria-selected="true">Product Detail</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="warranty-detail-tab" data-toggle="tab"
                                            href="#warranty-detail" role="tab" aria-controls="info"
                                            aria-selected="false">Company Warranty Detail</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="hire-purchase-detail-tab" data-toggle="tab"
                                            href="#hire-purchase-detail" role="tab" aria-controls="hire-purchase-detail"
                                            aria-selected="false">Hire Sales Detail</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="advancedTabContent">

                                    <div class="tab-pane fade show active" id="product-detail" role="tabpanel"
                                        aria-labelledby="product-detail-tab">
                                        <table class="table table-striped table-bordered mt-3">
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row" class="w-150 dark-grey-text h6">Name</th>
                                                    <td>{{ $product->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="w-150 dark-grey-text h6">Product Code</th>
                                                    <td>{{ $product->barcode }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="w-150 dark-grey-text h6">Serial Number / Color</th>
                                                    <td>
                                                        @include('product.serial-values')
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="w-150 dark-grey-text h6">Description</th>
                                                    <td>{{ $product->description }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="warranty-detail" role="tabpanel"
                                        aria-labelledby="warranty-detail-tab">
                                        @include('product.warranty-fields')
                                    </div>

                                    <div class="tab-pane fade" id="hire-purchase-detail" role="tabpanel"
                                        aria-labelledby="hire-purchase-detail-tab">
                                        <table class="table table-striped table-bordered mt-3">
                                            <thead></thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row" class="w-150 dark-grey-text h6">Down Payment (%)
                                                    </th>
                                                    <td>{{ $product->ProductCategory->down_payment . '%' }}</td>
                                                </tr>
                                                {{-- <tr>
                                            <th scope="row" class="w-150 dark-grey-text h6">Down Payment (LKR)</th>
                                            <td>{{$product->additional_warranty_description}}</td>
                                            </tr> --}}
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

    <script type="text/javascript" src="{{ asset('assets') }}/show/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets') }}/show/popper.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets') }}/show/bootstrap.js"></script>
    <script type="text/javascript" src="{{ asset('assets') }}/show/mdb.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets') }}/show/mdb.ecommerce.min.js"></script>

    <script>
        function switchMainImage(src, element) {
            var $main = $('#main_product_image');
            $main.fadeOut(150, function() {
                $main.attr('src', src).fadeIn(150);
            });
            $('.product-thumb-box').css('border-color', '#e9ecef');
            if (element) {
                $(element).css('border-color', '#007bff');
            }
        }

        $(document).ready(function() {
            $(function() {
                $("#mdb-lightbox-ui").load("{{ asset('assets') }}/show/mdb-lightbox-ui.html");
            });
        });
        $(document).addEventListener('DOMContentLoaded', function() {
            const zoomLinks = document.querySelectorAll('.no-zoom a');

            zoomLinks.forEach(link => {
                link.addEventListener('click', (e) => e.preventDefault()); // Disable zoom click
            });

            const img = document.querySelector('.no-zoom-img');
            if (img) {
                img.style.pointerEvents = 'none'; // Ensure no interaction with the image
            }
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
