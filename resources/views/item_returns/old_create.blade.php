@extends('layouts.admin')
@section('page-title')
{{__('Item Return')}}
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
        <h1>{{__('Returning Items')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item active"><a href="{{ route('item_return.index') }}">{{__('Item Return')}}</a></div>
        <div class="breadcrumb-item">{{__('Items')}}</div>
    </div>
    </div> --}}

    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Returning Items') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item active"><a
                                        href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                                <div class="breadcrumb-item active"><a
                                        href="{{ route('item_return.index') }}">{{__('Item Return')}}</a></div>
                                <div class="breadcrumb-item">{{__('Items')}}</div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Returning Items')}}</h4>
                        </div>
                    </div>
                    {{-- <div class="card-body">
                        {{ Form::open(array('url' => 'item_return/search')) }}
                    <div class="d-flex justify-content-between w-100">
                        <div class="form-group col-md-6">
                            {{ Form::label('invoice_number', __('Invoice Number')) }}
                            {{ Form::text('invoice_number',$invoiceNo ?? '', array('class' => 'form-control invoice_number','required'=>'required')) }}
                        </div>
                        <div class="col-auto">
                            <button style="width: 10rem; border-radius: 20px; margin-top: 25px;"
                                class="btn btn-icon icon-left btn-primary" type="submit">
                                <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-inner--text"> {{__('Submit')}}</span>
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div> --}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" action='{{route("item_return.store")}}' id="items_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="section-title">{{__('Product Summary')}}</div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-md">
                                        <thead>
                                            <tr>
                                                <th>{{__('Product')}}</th>
                                                <th>{{__('Quantity')}}</th>
                                                <th>{{__('Purchasing Price')}}</th>
                                                <th>{{__('Discount')}}</th>
                                                {{-- @if ($invoice->sales->sale_type == 1)
                                                    <th>{{__('Down Payment Rate')}}</th>
                                                <th>{{__('Installment Time Period')}}</th>
                                                <th>{{__('Interest Rate')}}</th>
                                                @endif --}}
                                                <th class="text-right" width="12%">{{__('Net Amount')}}</th>
                                                @if ($modalType == 1)
                                                <th class="text-right">{{__('Down Payment')}}</th>
                                                @endif
                                                <th class="text-center" width="12%">{{__('Return Quantity')}}</th>
                                                <th class="text-right" width="12%">{{__('Return Amount')}}</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $totalAmount = 0;
                                            @endphp

                                            @if($modalType == 1)
                                            @foreach ( $items as $k => $item )
                                            <tr>
                                                <input type="hidden" class="sale_id" name="item[{{$k}}][sale_id]"
                                                    value="{{ $item->id ?? '' }}">
                                                {{-- <input type="hidden" class="item_id" name="item[{{$k}}][item_id]"
                                                value="{{ $item->id }}"> --}}
                                                <input type="hidden" class="invoice_id" name="item[{{$k}}][invoice_id]"
                                                    value="{{ $item->invoice_id }}">
                                                <td>
                                                    <input type="hidden" class="inventory_id"
                                                        name="item[{{$k}}][inventory_id]"
                                                        value="{{ $item->inventory_id }}">
                                                    <input type="hidden" class="product_id"
                                                        name="item[{{$k}}][product_id]" value="{{ $item->product_id }}">
                                                    {{ $item->products->name ?? '' }}
                                                </td>
                                                <td>
                                                    <input type="hidden" class="item_qty" name="item[{{$k}}][item_qty]"
                                                        value="{{ $item->quantity }}">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td>
                                                    <input type="hidden" class="unit_price"
                                                        name="item[{{$k}}][unit_price]" value="{{ $item->unit_price }}">
                                                    {{ \Auth::user()->priceFormat($item->unit_price) }}
                                                </td>
                                                <td>
                                                    <input type="hidden" class="discount" name="item[{{$k}}][discount]"
                                                        value="{{ $item->discount }}">
                                                    {{ \Auth::user()->priceFormat($item->discount) }}
                                                </td>
                                                {{-- @if ($invoice->sales->sale_type == 1)
                                                            <td>{{ $invoice->sales->products->product_category['down_payment'] }}
                                                {{__('%')}}</td>
                                                <td>{{ $invoice->sales->installment }} {{__('Months')}}</td>
                                                <td>{{ $invoice->sales->interest_rate }} {{__('%')}}</td>
                                                @endif --}}
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($item->net_amount) }}</td>
                                                <td class="text-right"> @if(isset($item->down_payment))
                                                    {{ \Auth::user()->priceFormat($item->down_payment) }} @else --
                                                    @endif</td>
                                                <td><input type="number" style="text-align: center;"
                                                        class="form-control return_qty" name="item[{{$k}}][return_qty]"
                                                        value="{{ $item->quantity }}" min="1"
                                                        max="{{ $item->quantity }}"></td>
                                                <td class="text-right">
                                                    @if($item->sale_type == 0)
                                                    <input type="hidden" class="return_amount"
                                                        name="item[{{$k}}][return_amount]"
                                                        value="{{ $item->net_amount }}">
                                                    <span
                                                        class="return_net_amount">{{ number_format($item->net_amount, 2) }}</span>
                                                    @elseif($item->sale_type == 1)
                                                    <input type="hidden" class="return_amount"
                                                        name="item[{{$k}}][return_amount]"
                                                        value="{{ $item->down_payment }}">
                                                    <span
                                                        class="return_net_amount">{{ number_format($item->down_payment, 2) }}</span>
                                                    @endif

                                                </td>
                                            </tr>
                                            @php
                                            if( $item->sale_type == 0 ){
                                            $totalAmount += $item->net_amount;
                                            } else if( $item->sale_type == 1 ){
                                            $totalAmount += $item->down_payment;
                                            }
                                            @endphp
                                            @endforeach
                                            @elseif($modalType == 2)
                                            @foreach ( $items as $k => $item )
                                            <tr>
                                                <input type="hidden" class="sale_id" name="item[{{$k}}][sale_id]"
                                                    value="{{ $item->sale_id ?? '' }}">
                                                <input type="hidden" class="item_id" name="item[{{$k}}][item_id]"
                                                    value="{{ $item->id }}">
                                                <input type="hidden" class="invoice_id" name="item[{{$k}}][invoice_id]"
                                                    value="{{ $item->invoice_id }}">
                                                <td>
                                                    <input type="hidden" class="inventory_id"
                                                        name="item[{{$k}}][inventory_id]"
                                                        value="{{ $item->inventory_id }}">
                                                    <input type="hidden" class="product_id"
                                                        name="item[{{$k}}][product_id]" value="{{ $item->product_id }}">
                                                    {{ $item->products->name ?? '' }}
                                                </td>
                                                <td>
                                                    <input type="hidden" class="item_qty" name="item[{{$k}}][item_qty]"
                                                        value="{{ $item->quantity }}">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td>
                                                    <input type="hidden" class="unit_price"
                                                        name="item[{{$k}}][unit_price]" value="{{ $item->unit_price }}">
                                                    {{ \Auth::user()->priceFormat($item->unit_price) }}
                                                </td>
                                                <td>
                                                    <input type="hidden" class="discount" name="item[{{$k}}][discount]"
                                                        value="{{ $item->discount }}">
                                                    {{ \Auth::user()->priceFormat($item->discount) }}
                                                </td>
                                                {{-- @if ($invoice->sales->sale_type == 1)
                                                        <td>{{ $invoice->sales->products->product_category['down_payment'] }}
                                                {{__('%')}}</td>
                                                <td>{{ $invoice->sales->installment }} {{__('Months')}}</td>
                                                <td>{{ $invoice->sales->interest_rate }} {{__('%')}}</td>
                                                @endif --}}
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($item->net_amount) }}</td>
                                                <td><input type="number" style="text-align: center;"
                                                        class="form-control return_qty" name="item[{{$k}}][return_qty]"
                                                        value="{{ $item->quantity }}" min="1"
                                                        max="{{ $item->quantity }}"></td>
                                                <td class="text-right">
                                                    <input type="hidden" class="return_amount"
                                                        name="item[{{$k}}][return_amount]"
                                                        value="{{ $item->net_amount }}">
                                                    <span
                                                        class="return_net_amount">{{ number_format($item->net_amount, 2) }}</span>
                                                </td>
                                            </tr>
                                            @php
                                            $totalAmount += $item->net_amount;
                                            @endphp
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <input type="hidden" name="model_type" value="{{ $modalType }}">

                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="form-group" style="text-align: center;">
                                    <label for="total_amount" style="font-size: 16px; font-weight:bold;">Total Return
                                        Amount</label>
                                    {{ Form::text('total_amount',$totalAmount ?? '', array('class' => 'form-control total_amount', 'style'=>'text-align: center; font-weight: bold;', 'readonly'=>'readonly')) }}
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <center>
                                    <button style="width: 15rem; border-radius: 20px; margin-top: 25px;"
                                        class="btn btn-icon icon-left btn-danger return-button" type="submit"
                                        id="return">
                                        <span class="btn-inner--icon"><i class="fas fa-undo-alt"></i></span>
                                        <span class="btn-inner--text"> {{__('Return')}}</span>
                                    </button>
                                </center>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

</section>

<script>
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');
        // $('.return-button').prop('disablled', true);
        // $('.return-button').prop("disabled",true);
    });

    $('body').on('input', '.return_qty', function () {
        var returnTr = $(this);
        var returnQty = returnTr.val();
        var unitPirce = returnTr.closest('tr').find('.unit_price').val();
        var discount = returnTr.closest('tr').find('.discount').val();

        var newAmount = parseFloat(returnQty) * parseFloat(unitPirce) - parseFloat(discount);
        returnTr.closest('tr').find('.return_amount').val(newAmount);
        returnTr.closest('tr').find('.return_net_amount').html(newAmount.toFixed(2));
        // if(count > 0){
        //     $('.return-button').prop("disabled",false);
        // } else {
        //     $('.return-button').prop("disabled",true);
        // }
    });

    // $('body').on('click', '#cash_return', function(){
    //     console.log('works');
    //     $('#items_form').attr('action', '{ route("item_return.items") }}');
    //     $('#items_form').submit();
    // });

</script>
@endsection
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/sweetAlert2/dist/sweetalert2.min.js') }}"></script> --}}
