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
        <h1>{{__('Item Return')}}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
        <div class="breadcrumb-item">{{__('Item Return')}}</div>
    </div>
    </div> --}}

    <div class="section-body">
        <div class="row justify-content-center">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Item Return') }}</h4>
                        <div class="section-header-breadcrumb d-flex ">
                            <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a>
                            </div>
                            <div class="breadcrumb-item">{{__('Item Return')}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card br-4 cus_show">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Item Return')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
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
                    </div>
                </div>
            </div>
        </div>
        @if ($showData == 'Yes')
        @if ($presentData == 'No')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <center>No Data</center>
                    </div>
                </div>
            </div>
        </div>
        @elseif($presentData == 'Yes')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" id="items_form">
                            @csrf
                            {{-- {{ Form::open(array('url' => 'item_return/items', 'id'=>'items_form')) }} --}}

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="invoice-title">
                                        <h2>{{__('Invoice Details')}}</h2>
                                        <div class="invoice-number">{{ $invoice->invoice_code }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        @if(!empty($invoice->customer))
                                        <div class="col-md-6">
                                            <address class="font-style">
                                                <strong>{{__('Billed To')}}:</strong><br>
                                                @if($invoice->customer->billing_title == 0) Mr.
                                                @elseif($invoice->customer->billing_title == 1) Mrs. @else Miss. @endif
                                                {{!empty($invoice->customer->billing_name)?$invoice->customer->billing_name:''}}<br>
                                                {{!empty($invoice->customer->billing_phone)?$invoice->customer->billing_phone:''}}<br>
                                                {{!empty($invoice->customer->billing_address)?$invoice->customer->billing_address:''}}<br>
                                                {{!empty($invoice->customer->billing_email)?$invoice->customer->billing_email:''}}
                                            </address>
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <address>
                                                <strong>{{__('Current Address')}}:</strong><br>
                                                @if($invoice->customer->title == 0) Mr.
                                                @elseif($invoice->customer->title == 1) Mrs. @else Miss. @endif
                                                {{!empty($invoice->customer->name)?$invoice->customer->name:''}}<br>
                                                {{!empty($invoice->customer->phone)?$invoice->customer->phone:''}}<br>
                                                {{!empty($invoice->customer->address)?$invoice->customer->address:''}}<br>
                                                {{!empty($invoice->customer->email)?$invoice->customer->email:''}}
                                            </address>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <address>
                                                <strong>{{__('Type')}}:</strong><br>
                                                @if($invoice->sales->sale_type == 0)
                                                {{-- <span class="badge badge-primary"></span> --}}
                                                <span class="badge badge-success">{{ __('Cash Sales') }}</span>
                                                @elseif($invoice->sales->sale_type == 1)
                                                <span class="badge badge-info">{{ __('Hire Sales') }}</span>
                                                {{-- @elseif($invoice->sales->status == 3)
                                                            <span class="badge badge-danger">Over Due</span> --}}
                                                @endif
                                            </address>
                                        </div>
                                        <div class="col-md-4 text-md-center">
                                            <address>
                                                <strong>{{__('Issue Date')}} :</strong><br>
                                                {{ $invoice->sales->date_of_sale }}<br><br>
                                            </address>
                                        </div>
                                        <div class="col-md-4 text-md-right">
                                            @if (isset($invoice->sales->due_date))
                                            <address>
                                                <strong>{{__('Due Date')}} :</strong><br>
                                                {{ $invoice->sales->due_date }}<br><br>
                                            </address>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="section-title">{{__('Product Summary')}}</div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-md">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th data-width="40">#</th>
                                                    <th>{{__('Product')}}</th>
                                                    <th>{{__('Quantity')}}</th>
                                                    <th>{{__('Purchasing Price')}}</th>
                                                    <th>{{__('Purchase Type')}}</th>
                                                    <th>{{__('Discount')}}</th>
                                                    <th class="text-right" width="12%">{{__('Net Amount')}}</th>
                                                    @if ($invoice->sales->sale_type == 1)
                                                    <th>{{__('Down Payment')}}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($invoice->sales->modal_type == 1)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="returns_check"
                                                            name="checked_sale[]" value="{{ $invoice->sales->id }}">
                                                        {{-- <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input returns_check" name="checked_sale[]" value="{{ $invoice->sales->id }}">
                                                        <label class="form-check-label" for="exampleCheck1">Check me
                                                            out</label>
                                    </div> --}}
                                    </td>
                                    <td>{{ $invoice->sales->id }}</td>
                                    <td>{{ $invoice->sales->products->name }}</td>
                                    <td>{{ $invoice->sales->quantity }}</td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->sales->unit_price) }}</td>
                                    <td>@if ($invoice->sales->sale_type == 0)
                                        {{__('Cash Sales')}}
                                        @elseif ($invoice->sales->sale_type == 1)
                                        {{__('Hire Sales')}}
                                        @endif
                                    </td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->sales->discount) }}</td>

                                    <td class="text-right">{{ \Auth::user()->priceFormat($invoice->sales->net_amount) }}
                                    </td>
                                    @if ($invoice->sales->sale_type == 1)
                                    <td>{{ $invoice->sales->down_payment }}</td>
                                    @endif
                                    </tr>
                                    @elseif ($invoice->sales->modal_type == 2)
                                    @foreach ($invoice->sales->saleItems as $sale)
                                    <tr>
                                        <td><input type="checkbox" class="returns_check" name="checked_saleItem[]"
                                                value="{{ $sale->id }}"></td>
                                        <td>{{ $invoice->sales->sale_id }}</td>
                                        <td>{{ $sale->products->name }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td>{{__('Rs.')}} {{ number_format($sale->unit_price, 2) }}</td>
                                        <td>@if ($invoice->sales->sale_type == 0)
                                            {{__('Cash Sales')}}
                                            @elseif ($invoice->sales->sale_type == 1)
                                            {{__('Hire Sales')}}
                                            @endif
                                        </td>
                                        <td>{{ $sale->discount }}</td>
                                        <td class="text-right">{{__('Rs.')}} {{ number_format($sale->net_amount, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    </tbody>
                                    {{-- @if ($invoice->sales->sale_type == 1)
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="10"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7"></td>
                                                                <td colspan="2" class="text-right"><b>{{__('Down Payment')}}</b>
                                    </td>
                                    <td class="text-right">
                                        {{ \Auth::user()->priceFormat($invoice->sales->down_payment) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td colspan="2" class="text-right"><b>{{__('Interest Amount')}}</b></td>
                                        <td class="text-right">
                                            {{ \Auth::user()->priceFormat($invoice->sales->interest_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td colspan="2" class="text-right"><b>{{__('Monthly Installment')}}</b></td>
                                        <td class="text-right">
                                            {{ \Auth::user()->priceFormat($invoice->sales->monthly_installment) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td colspan="2" class="text-right">
                                            <b>{{__('Total Investment Schedule for Payment')}}</b></td>
                                        <td class="text-right">
                                            {{ \Auth::user()->priceFormat($invoice->sales->total_due_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td colspan="2" class="text-right"><b>{{__('Balance Due')}}</b></td>
                                        <td class="text-right">
                                            {{ \Auth::user()->priceFormat($invoice->sales->current_due_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td colspan="2" class="text-right"><b>{{__('Document Charges')}}</b></td>
                                        <td class="text-right">
                                            {{ \Auth::user()->priceFormat($invoice->sales->document_charges) }}</td>
                                    </tr>
                                    </tfoot>
                                    @endif --}}
                                    {{-- @if ($invoice->sales->modal_type == 2)
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td colspan="2" class="text-right"><b>{{__('Total Amount')}}</b>
                                    </td>
                                    <td class="text-right">{{__('Rs.')}}
                                        {{ number_format($invoice->sales->net_amount, 2) }}</td>
                                    </tr>
                                    </tfoot>
                                    @endif --}}
                                    </table>
                                </div>
                            </div>
                    </div>

                    <div class="row mt-3">
                        <input type="hidden" name="model_type" value="{{ $invoice->sales->modal_type ?? '' }}">
                        <div class="col-md-6">
                            <center>
                                <button style="width: 15rem; border-radius: 20px; margin-top: 25px;"
                                    class="btn btn-icon icon-left btn-danger return-button" type="button"
                                    id="cash_return">
                                    <span class="btn-inner--icon"><i class="fas fa-undo-alt"></i></span>
                                    <span class="btn-inner--text"> {{__('Cash Return')}}</span>
                                </button>
                            </center>
                        </div>
                        <div class="col-md-6">
                            <center>
                                <button style="width: 15rem; border-radius: 20px; margin-top: 25px;"
                                    class="btn btn-icon icon-left btn-warning return-button" type="button"
                                    id="item_for_item_return">
                                    <span class="btn-inner--icon"><i class="fas fa-undo-alt"></i></span>
                                    <span class="btn-inner--text"> {{__('Item For Item Return')}}</span>
                                </button>
                            </center>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endif
    @endif

    </div>

</section>

<script>
    $(document).ready(function () {
        $(".se-pre-con").fadeOut('slow');

        $('.return-button').prop("disabled", true);
    });

    $('body').on('click', '.returns_check', function () {
        var count = $('.returns_check').filter(':checked').length;
        if (count > 0) {
            $('.return-button').prop("disabled", false);
        } else {
            $('.return-button').prop("disabled", true);
        }
    });

    $('body').on('click', '#cash_return', function () {
        $(".se-pre-con").fadeIn('slow');
        $('#items_form').attr('action', '{{ route("item_return.create") }}');
        $('#items_form').submit();
    });

    $('body').on('click', '#item_for_item_return', function () {
        $(".se-pre-con").fadeIn('slow');
        $('#items_form').attr('action', '{{ route("item_return.create_item_for_item") }}');
        $('#items_form').submit();
    });

    $('body').on('submit', function () {
        $(".se-pre-con").fadeIn('slow');
    });

</script>
@endsection
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/sweetAlert2/dist/sweetalert2.min.js') }}"></script> --}}
