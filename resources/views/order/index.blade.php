@extends('layouts.admin')
@section('page-title')
{{__('Order')}}
@endsection
@section('content')
<section class="section">
    {{-- <div class="section-header">
            <h1>{{__('Order')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Order')}}</div>
    </div>
    </div> --}}

    <div class="section-body">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Order') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item">{{__('Order')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-body">
                        <div class="card-body p-0">
                            <div id="table-1_wrapper"
                                class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-flush font-style" id="dataTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>{{__('Order Id')}}</th>
                                                        <th>{{__('Date')}}</th>
                                                        <th>{{__('Name')}}</th>
                                                        <th>{{__('Plan Name')}}</th>
                                                        <th>{{__('Price')}}</th>
                                                        <th>{{__('Payment Type')}}</th>
                                                        <th>{{__('Status')}}</th>
                                                        <th>{{__('Coupon')}}</th>
                                                        <th class="text-center">{{__('Invoice')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orders as $order)
                                                    <tr>
                                                        <td>{{$order->order_id}}</td>
                                                        <td>{{$order->created_at->format('d M Y')}}</td>
                                                        <td>{{$order->user_name}}</td>
                                                        <td>{{$order->plan_name}}</td>
                                                        <td>{{env('CURRENCY_SYMBOL').$order->price}}</td>
                                                        <td>{{$order->payment_type}}</td>
                                                        <td>
                                                            @if($order->payment_status == 'succeeded')
                                                            <i class="mdi mdi-circle text-success"></i>
                                                            {{ucfirst($order->payment_status)}}
                                                            @else
                                                            <i class="mdi mdi-circle text-danger"></i>
                                                            {{ucfirst($order->payment_status)}}
                                                            @endif
                                                        </td>

                                                        <td>{{!empty($order->total_coupon_used)? !empty($order->total_coupon_used->coupon_detail)?$order->total_coupon_used->coupon_detail->code:'-':'-'}}
                                                        </td>

                                                        <td class="text-center">
                                                            @if($order->receipt != 'free coupon' && $order->payment_type
                                                            == 'STRIPE')
                                                            <a href="{{$order->receipt}}" title="Invoice"
                                                                target="_blank" class="">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                            @elseif($order->receipt == 'free coupon')
                                                            <p>{{__('Used 100 % discount coupon code.')}}</p>
                                                            @elseif($order->payment_type == 'Manually')
                                                            <p>{{__('Manually plan upgraded by super admin')}}</p>
                                                            @else
                                                            -
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
@endsection
