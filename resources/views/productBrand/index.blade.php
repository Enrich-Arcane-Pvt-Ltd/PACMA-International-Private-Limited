@extends('layouts.admin')
@section('page-title')
{{__('Product Brands')}}
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
        <h1>{{__('Product Brands')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Product Brands')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2">{{__('Product Brands')}}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Product Brands')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Product Brands')}}</h4>
                            @can('create product')
                            <div class="col-auto">
                                <a href="#" data-size="xl" data-url="{{ route('product-brand.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Create New Product Brand')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush" id="product_brands_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{__('Brand #')}}</th>
                                                        <th> {{__('Name')}}</th>
                                                        @if(Gate::check('edit product') || Gate::check('block product'))
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($productBrands as $productBrand)
                                                    <tr>
                                                        <td>
                                                            <a href="#" class="btn btn-outline-primary">
                                                                {{ AUth::user()->productBrandNumberFormat($productBrand['product_brand_id']) }}
                                                            </a>
                                                        </td>
                                                        <td class="font-style">{{ $productBrand->name }}</td>
                                                        @if(Gate::check('edit product') || Gate::check('block product'))
                                                        <td class="action text-center">
                                                            @can('edit product')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-size="xl"
                                                                data-url="{{ route('product-brand.edit',$productBrand->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Edit Product Brand')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan
                                                            @if($productBrand->status==1)
                                                            @can('block product')
                                                            <button class="btn btn-danger btn-action mr-1 block"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip" value="{{$productBrand->id}}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @else
                                                            @can('block product')
                                                            <button class="btn btn-success btn-action mr-1 unblock"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$productBrand->id}}">
                                                                <i class="fas fa-unlock"></i>
                                                            </button>
                                                            @endcan
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        if (localStorage.getItem("product_brand_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("product_brand_page_no"));
        }
        var productBrandsTable = $('#product_brands_table').DataTable({
            order: [
                [1, "asc"]
            ]
        });
        productBrandsTable.page(pageNo).draw(false);

        $('#product_brands_table').on('page.dt', function () {
            var pageInfo = productBrandsTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("product_brand_page_no", pageNo);
        });
    });

    $("body").on("click", ".block", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to block this brand ?',
            text: "Blocking this brand will not allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("product-brand/block/" + value);
            }
        });

    });


    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this brand ?',
            text: "Unblocking this brand will allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Unblock'
        }).then((result) => {
            if (result.value) {
                window.location.replace("product-brand/unblock/" + value);
            }
        });
    });

</script>

@endsection
