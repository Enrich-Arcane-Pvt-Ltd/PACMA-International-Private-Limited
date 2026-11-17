@extends('layouts.admin')
@section('page-title')
{{__('Product Grid View ')}}
@endsection
@section('content')
@push('css-page')
<link href="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet"
    type="text/css" />
@endpush

@push('script-page')
<script>
    $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function () {
        var template = $("select[name='invoice_template']").val();
        var color = $("input[name='invoice_color']:checked").val();
        $('#invoice_frame').attr('src', '{{url(' / invoices / preview ')}}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='proposal_template'], input[name='proposal_color']", function () {
        var template = $("select[name='proposal_template']").val();
        var color = $("input[name='proposal_color']:checked").val();
        $('#proposal_frame').attr('src', '{{url(' / proposal / preview ')}}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='bill_template'], input[name='bill_color']", function () {
        var template = $("select[name='bill_template']").val();
        var color = $("input[name='bill_color']:checked").val();
        $('#bill_frame').attr('src', '{{url(' / bill / preview ')}}/' + template + '/' + color);
    });

</script>
<script src="{{ asset('assets/modules/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@endpush
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Product Details')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Product Details')}}</div>
    </div>
    </div> --}}
    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Product Details') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Product Details')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Product Details')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="setting-tab">
                            <ul class="nav nav-pills nav-fill" id="myTab3" role="tablist">


                                <li class="nav-item">
                                    <a class="nav-link active" id="contact-tab4" data-toggle="tab" href="#item-details"
                                        role="tab" aria-controls="" aria-selected="false">{{__('Item Details')}}</a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link" id="contact-tab4" data-toggle="tab"
                                        href="#hire-purchase-details" role="tab" aria-controls=""
                                        aria-selected="false">{{__('Hire Sales Details')}}</a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#warrent-details"
                                        role="tab" aria-controls="" aria-selected="false">{{__('Warrent Details')}}</a>
                                </li>


                            </ul>

                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade fade show active" id="item-details" role="tabpanel"
                                    aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 column">
                                                        <!-- <img src="{{URL::asset('/')}}/{{$productServices->image_1}}" class="image_1" alt="..." > -->
                                                        <div class="preview-pic tab-content">
                                                            <div class="tab-pane active" id="pic-1"><img
                                                                    src="{{URL::asset('/')}}/{{$productServices->image_1}}" />
                                                            </div>
                                                            <div class="tab-pane" id="pic-2"><img
                                                                    src="{{URL::asset('/')}}/{{$productServices->image_2}}" />
                                                            </div>
                                                            <div class="tab-pane" id="pic-3"><img
                                                                    src="{{URL::asset('/')}}/{{$productServices->image_3}}" />
                                                            </div>
                                                        </div>
                                                        <ul class="preview-thumbnail nav nav-tabs">
                                                            <li class="active"><a data-target="#pic-1"
                                                                    data-toggle="tab"><img
                                                                        src="{{URL::asset('/')}}/{{$productServices->image_1}}" /></a>
                                                            </li>
                                                            <li><a data-target="#pic-2" data-toggle="tab"><img
                                                                        src="{{URL::asset('/')}}/{{$productServices->image_2}}" /></a>
                                                            </li>
                                                            <li><a data-target="#pic-3" data-toggle="tab"><img
                                                                        src="{{URL::asset('/')}}/{{$productServices->image_3}}" /></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-8 column">
                                                        <div class="container ">
                                                            <h5 class="group card-title inner list-group-item-heading">
                                                                {{ $productServices->name}}</h5>
                                                            <p class="price">
                                                                <span>Rs.{{$productServices->sale_price}}.00</span></p>
                                                            <h7 class="product-description">
                                                                {{$productServices->description}}</h7>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade fade" id="hire-purchase-details" role="tabpanel"
                                    aria-labelledby="profile-tab3">
                                    <div class="company-setting-wrap">
                                        <div class="card-body">
                                            <div class="card-body">
                                                To be Developed
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="warrent-details" role="tabpanel"
                                    aria-labelledby="contact-tab4">
                                    <div class="email-setting-wrap">
                                        <div class="card-body">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 column">
                                                        <img src="{{URL::asset('/')}}/{{$productServices->image_1}}"
                                                            class="image_1" alt="...">
                                                    </div>
                                                    <div class="col-md-8 column">
                                                        <div class="container">
                                                            <p class="price">Warranty Period -
                                                                <span>{{$productServices->warranty_period}}</span></p>


                                                            <h6 class="product-description"> Warrenty Description :
                                                                {{$productServices->description}}</h6>
                                                            <a
                                                                href="{{URL::asset('/')}}/{{$productServices->warranty_document}}">Go
                                                                to Document</a>

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
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
<style>
    .image_1 {
        max-width: 260px;
        height: 180px;
    }

    .price {
        font-weight: bold;
    }

    .checked,
    .price span {
        color: #ff9f1a;
    }

    .product-title,
    .rating,
    .product-description,
    .price,
    .vote,
    .sizes {
        margin-bottom: 15px;
    }

    #new_container {
        font-family: 'open sans';
        overflow-x: hidden;
    }

    img {
        max-width: 100%;

    }

    .preview {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
    }

    @media screen and (max-width: 996px) {
        .preview {
            margin-bottom: 50px;
        }
    }

    .preview-pic {
        -webkit-box-flex: 1;
        -webkit-flex-grow: 1;
        -ms-flex-positive: 2;
        flex-grow: 2;
    }

    .preview-thumbnail.nav-tabs {
        border: none;
        margin-top: 15px;
    }

    .preview-thumbnail.nav-tabs li {
        width: 18%;
        margin-right: 2.5%;
    }

    .preview-thumbnail.nav-tabs li img {
        max-width: 100%;
        display: block;
    }

    .preview-thumbnail.nav-tabs li a {
        padding: 0;
        margin: 0;
    }

    .preview-thumbnail.nav-tabs li:last-of-type {
        margin-right: 0;
    }

    .tab-content {
        overflow: hidden;
    }

    .tab-content img {
        width: 70%;
        -webkit-animation-name: opacity;
        animation-name: opacity;
        -webkit-animation-duration: .3s;
        animation-duration: .3s;
    }

    #new_card {
        margin-top: 50px;
        background: #D8DBEE;
        padding: 3em;
        line-height: 1.5em;
    }

    @media screen and (min-width: 997px) {
        .wrapper {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
        }
    }

    .details {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
    }

    .colors {
        -webkit-box-flex: 1;
        -webkit-flex-grow: 1;
        -ms-flex-positive: 1;
        flex-grow: 1;
    }

    .product-title,
    .sizes,
    .colors {
        text-transform: UPPERCASE;
        font-weight: bold;
    }

    .price {

        font-weight: bold;
    }




    .checked,
    .price span {
        color: #ff9f1a;
    }

    .product-title,
    .rating,
    .product-description,
    .price,
    .vote,
    .sizes {
        margin-bottom: 15px;
    }

    .product-title {
        margin-top: 0;
    }

    .size {
        margin-right: 10px;
    }

    .size:first-of-type {
        margin-left: 40px;
    }

    .color {
        display: inline-block;
        vertical-align: middle;
        margin-right: 10px;
        height: 2em;
        width: 2em;
        border-radius: 2px;
    }

    .color:first-of-type {
        margin-left: 20px;
    }

    .add-to-cart,
    .like {
        background: #ff9f1a;
        padding: 1.2em 1.5em;
        border: none;
        text-transform: UPPERCASE;
        font-weight: bold;
        color: #fff;
        -webkit-transition: background .3s ease;
        transition: background .3s ease;
    }

    .add-to-cart:hover,
    .like:hover {
        background: #b36800;
        color: #fff;
    }

    .not-available {
        text-align: center;
        line-height: 2em;
    }

    .not-available:before {
        font-family: fontawesome;
        content: "\f00d";
        color: #fff;
    }

    #details {
        margin-bottom: 15px;
    }

    .orange {
        background: #ff9f1a;
    }

    .green {
        background: #85ad00;
    }

    .blue {
        background: #0076ad;
    }

    .tooltip-inner {
        padding: 1.3em;
    }

    @-webkit-keyframes opacity {
        0% {
            opacity: 0;
            -webkit-transform: scale(3);
            transform: scale(3);
        }

        100% {
            opacity: 1;
            -webkit-transform: scale(1);
            transform: scale(1);
        }
    }

    @keyframes opacity {
        0% {
            opacity: 0;
            -webkit-transform: scale(3);
            transform: scale(3);
        }

        100% {
            opacity: 1;
            -webkit-transform: scale(1);
            transform: scale(1);
        }

</style>
