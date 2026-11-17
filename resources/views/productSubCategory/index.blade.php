@extends('layouts.admin')
@section('page-title')
{{__('Product Sub Categories')}}
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
        <h1>{{__('Product Sub Categories')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Product Sub Categories')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Product Sub Categories') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Product Sub Categories')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Product Sub Categories')}}</h4>
                            @can('create product')
                            <div class="col-auto">
                                <a href="#" data-size="xl" data-url="{{ route('product-sub-category.create') }}"
                                    data-ajax-popup="true" data-title="{{__('Create New Sub Category')}}"
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
                                            <table class="table table-flush" id="product_sub_category_table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th> {{__('Sub Category #')}}</th>
                                                        <th> {{__('Name')}}</th>
                                                        <th class="text-center"> {{__('Category')}}</th>
                                                        @if(Gate::check('edit product') || Gate::check('block product') || Auth::user()->canDeleteProductCategory(true))
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($productSubCategories as $productSubCategory)
                                                    <tr>
                                                        <td>
                                                             <a href="#" class="btn btn-outline-primary">
                                                                {{ AUth::user()->productSubCategoryNumberFormat($productSubCategory['product_sub_category_id']) }}
                                                            </a>
                                                        </td>
                                                        <td class="font-style">{{ $productSubCategory->name }}</td>
                                                        <td class="text-center">
                                                            {{ $productSubCategory->productCategory->name ?? ($productSubCategory->productCategory['name'] ?? '-') }}</td>
                                                        @if(Gate::check('edit product') || Gate::check('block product') || Auth::user()->canDeleteProductCategory(true))
                                                        <td class="action text-center">

                                                            @can('edit product')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-size="xl"
                                                                data-url="{{ route('product-sub-category.edit',$productSubCategory->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Edit Product Category')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan


                                                            @if($productSubCategory->status==1)
                                                            @can('block product')
                                                            <button class="btn btn-danger btn-action mr-1 block"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip"
                                                                value="{{$productSubCategory->id}}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            @endcan
                                                            @else
                                                            @can('block product')
                                                            <button class="btn btn-success btn-action mr-1 unblock"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$productSubCategory->id}}">
                                                                <i class="fas fa-unlock"></i>
                                                            </button>
                                                            @endcan
                                                            @endif

                                                            @if(Auth::user()->canDeleteProductCategory(true))
                                                            <button class="btn btn-danger btn-action delete-sub-category mr-1"
                                                                data-id="{{$productSubCategory->id}}"
                                                                data-name="{{$productSubCategory->name}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Delete')}}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            <form id="delete-form-{{$productSubCategory->id}}" action="{{ route('product-sub-category.destroy', $productSubCategory->id) }}" method="POST" style="display: none;">
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
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        if (localStorage.getItem("product_sub_category_page_no") == null) {
            var pageNo = 0;
        } else {
            var pageNo = Number(localStorage.getItem("product_sub_category_page_no"));
        }
        var productSubCategoryTable = $('#product_sub_category_table').DataTable({
            order: [
                [1, "asc"]
            ]
        });
        productSubCategoryTable.page(pageNo).draw(false);

        $('#product_sub_category_table').on('page.dt', function () {
            var pageInfo = productSubCategoryTable.page.info();
            pageNo = pageInfo.page;
            localStorage.setItem("product_sub_category_page_no", pageNo);
        });
    });

    $("body").on("click", ".block", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to block this sub category ?',
            text: "Blocking this sub category will not allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Block'
        }).then((result) => {
            if (result.value) {
                window.location.replace("product-sub-category/block/" + value);
            }
        });

    });


    $("body").on("click", ".unblock", function () {
        var value = $(this).val();
        event.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
            title: 'Are you sure you want to unblock this sub category ?',
            text: "Unblocking this sub category will allow it to use in this company.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#fc544b',
            confirmButtonText: 'Unblock'
        }).then((result) => {
            if (result.value) {
                window.location.replace("product-sub-category/unblock/" + value);
            }
        });
    });

    $("body").on("click", ".delete-sub-category", function (e) {
        e.preventDefault();
        if (!@json(Auth::user()->canDeleteProductCategory(true))) {
            Swal.fire({type: 'warning', title: @json(__('Permission denied')),
                text: @json(__('Only administrators can delete sub categories. Please contact an administrator.'))});
            return;
        }
        var id = $(this).data('id');
        var name = $(this).data('name') || 'this sub-category';
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

    $(window).on('load', function () {
        $(".se-pre-con").fadeOut('slow');
    });
</script>

@endsection
