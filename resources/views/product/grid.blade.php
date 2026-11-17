@extends('layouts.admin')
@section('page-title')
{{__('Product Grid View ')}}
@endsection
@section('content')
<style>
    .view-group {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        padding-left: 0;
        margin-bottom: 0;
        border-color: red;
    }

    .thumbnail {
        margin-bottom: 30px;
        padding: 0px;
        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        border-radius: 0px;
        border-color: #343a40;
        width: 300px;
    }

    .item.list-group-item {
        float: none;
        width: 100%;
        background-color: #fff;
        margin-bottom: 30px;
        -ms-flex: 0 0 100%;
        flex: 0 0 100%;
        max-width: 100%;
        padding: 0 1rem;
        border: 1;
    }

    .item.list-group-item {
        float: left;
        width: 30%;
    }

    .item.list-group-item .list-group-image {
        margin-right: 10px;
    }

    .item.list-group-item .thumbnail {
        margin-bottom: 0px;
        display: inline-block;
    }

    .item.list-group-item .caption {
        float: left;
        width: 70%;
        margin: 0;
    }

    .item.list-group-item:before,
    .item.list-group-item:after {
        display: table;
        content: " ";
    }

    .item.list-group-item:after {
        clear: both;
    }

    .image_1 {
        max-width: 260px;
        height: 150px;
    }

    .img-event {

        width: 30%;
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        text-align: center;

    }

    .card .card-body {
        padding-top: 20px;
        padding-bottom: 20px;
        text-align: center;
    }

    .lead {
        font-size: 1.25rem;
        font-weight: 300;
        text-align: center;
    }

</style>

</script>
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Grid View of Products')}}</h1>
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
                        <h4 class="mt-2 ">{{ __('Grid View of Products') }}</h3>
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
                                <a href="{{ route('productservice.index') }}"
                                    class="btn btn-icon icon-left btn-primary">
                                    <span class="btn-inner--icon"><i class="fas fa-list"></i></span>
                                    <span class="btn-inner--text"> {{__('List View')}}</span>
                                </a>
                                @endcan
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" class="btn btn-icon icon-left btn-primary">
                                        <i class="fas fa-filter"></i>{{__('Filter')}}
                                    </a>
                                    <div
                                        class="dropdown-menu dropdown-list dropdown-menu-right Filter-dropdown {{isset($_GET['category'])?'show':''}}">
                                        {{ Form::open(array('route' => array('productservice.index'),'method' => 'GET')) }}
                                        <div class="form-group">
                                            {{ Form::label('category', __('Category')) }}
                                            {{ Form::select('category', $category,null, array('class' => 'form-control font-style selectric','required'=>'required')) }}
                                        </div>

                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">{{__('Search')}}</button>
                                            <a href="{{route('productservice.index')}}"
                                                class="btn btn-danger">{{__('Reset')}}</a>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
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

                    <div id="products" class="row view-group">
                        @foreach ($productServices as $productService)

                        <div class="item col-xs-4 col-lg-4">
                            <div class="thumbnail card border">

                                <div class="img-event">
                                    <img class="image_1" src="{{URL::asset('/')}}/{{$productService->image_1}}" />
                                </div>

                                <div class="caption card-body">
                                    <h6 class="group card-title inner list-group-item-heading">
                                        {{ $productService->name}}</h6>
                                </div>

                                <p class="lead">
                                    {{ \Auth::user()->priceFormat($productService->sale_price) }}</p>

                                <a class="btn btn-primary"
                                    href="{{ route('productService.detail', $productService->id) }}">More Details</a>

                            </div>
                        </div>

                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
