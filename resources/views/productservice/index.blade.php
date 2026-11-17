@extends('layouts.admin')
@section('page-title')
{{__('Product')}}
@endsection
@section('content')
<style>
    .action,
    .status {
        white-space: nowrap;
    }

    .description {
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

</style>

<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Product')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Product')}}</div>
    </div>
    </div> --}}

    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Product') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Product')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">

                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Product & Service')}}</h4>
                            <div class="card-header-action">
                                @can('create product & service')
                                <a href="#" data-url="{{ route('productservice.create') }}" data-ajax-popup="true"
                                    data-title="{{__('Create New Product')}}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    <span class="btn-inner--text"> {{__('Create')}}</span>
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

                                            <table class="table table-flush" id="dataTable">
                                                <thead class="thead-light">
                                                    <tr role="row">
                                                        <th>{{__('Product #')}}</th>
                                                        <th>{{__('Name')}}</th>
                                                        <th>{{__('Sale Price')}}</th>
                                                        <th>{{__('Purchase Price')}}</th>
                                                        <th>{{__('Category')}}</th>
                                                        <th> {{__('Sub Category')}}</th>
                                                        <th>{{__('Stock')}}</th>
                                                        <th>{{__('Warehouse')}}</th>
                                                        <th>{{__('Description')}}</th>
                                                        <th>{{__('Status')}}</th>
                                                        <th class="text-right">{{__('Action')}}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($productServices as $productService)
                                                    <tr class="font-style">
                                                        <td>#{{ $productService->product_id }}</td>
                                                        <td>{{ $productService->name}}</td>
                                                        <td>{{ \Auth::user()->priceFormat($productService->sale_price) }}
                                                        </td>
                                                        <td>{{ \Auth::user()->priceFormat($productService->purchase_price )}}
                                                        </td>
                                                        <td>{{ $productService->categoryDetails['name'] }}</td>
                                                        <td>{{ $productService->subCategoryDetails['name'] }}</td>
                                                        <td>{{ $productService->quantity }}</td>
                                                        <td>{{ $productService->warehouseDetails['name'] }}</td>
                                                        <td class="description">{{ $productService->description }}</td>
                                                        <td class="status">
                                                            @can('edit product & service')
                                                            @if($productService->status == 0 &&
                                                            $productService->quantity == 0)
                                                            <kbd class="bg-danger">Blocked</kbd>
                                                            <kbd class="bg-danger">Out of Stock</kbd>

                                                            @elseif($productService->status == 1 &&
                                                            $productService->quantity == 0)
                                                            <kbd class="bg-success">Unblocked</kbd>
                                                            <kbd class="bg-danger">Out of Stock</kbd>

                                                            @elseif($productService->status == 0 &&
                                                            $productService->quantity > 0)
                                                            <kbd class="bg-danger">Blocked</kbd>
                                                            <kbd class="bg-success">In Stock</kbd>

                                                            @elseif($productService->status == 1 &&
                                                            $productService->quantity > 0)
                                                            <kbd class="bg-success">Unblocked</kbd>
                                                            <kbd class="bg-success">In Stock</kbd>
                                                            @endif
                                                            @endcan
                                                        </td>

                                                        <td class="action text-right">
                                                            <a href="#" class="btn btn-info btn-action mr-1"
                                                                data-url="{{ route('productservice.show',$productService->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Product Details')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('View')}}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>

                                                            @can('edit product & service')
                                                            <a href="#" class="btn btn-primary btn-action mr-1"
                                                                data-url="{{ route('productservice.edit',$productService->id) }}"
                                                                data-ajax-popup="true"
                                                                data-title="{{__('Edit Product Service')}}"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Edit')}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endcan

                                                            @if($productService->status==1)
                                                            <button class="btn btn-danger btn-action unblock"
                                                                data-original-title="{{__('Block')}}"
                                                                data-toggle="tooltip" value="{{$productService->id}}">
                                                                <i class="fas fa-lock"></i>
                                                            </button>

                                                            @else
                                                            <button class="btn btn-success btn-action delete"
                                                                data-toggle="tooltip"
                                                                data-original-title="{{__('Unblock')}}"
                                                                value="{{$productService->id}}">
                                                                <i class="fas fa-unlock"></i>
                                                            </button>

                                                            @endif
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

<script>
    $("body").on("click", ".delete", function () {
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
                window.location.replace("productService/unblock/" + value);
            }
        });
    });

    $("body").on("click", ".unblock", function () {
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
                window.location.replace("productService/unblocktwo/" + value);
            }
        });
    });

</script>
@endsection
