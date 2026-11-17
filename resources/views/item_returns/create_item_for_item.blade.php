@extends('layouts.admin')
@section('page-title')
{{__('Item Return')}}
@endsection
@push('script-page')
<script src="{{asset('assets/js/jquery.repeater.min.js')}}"></script>
<script>
    var selector = "body";
    if ($(selector + " .repeater").length) {
        var $dragAndDrop = $("body .repeater tbody").sortable({
            handle: '.sort-handler'
        });
        var $repeater = $(selector + ' .repeater').repeater({
            initEmpty: false,
            defaultValues: {
                'status': 1
            },
            show: function () {
                $(this).slideDown();
                var file_uploads = $(this).find('input.multi');
                if (file_uploads.length) {
                    $(this).find('input.multi').MultiFile({
                        max: 3,
                        accept: 'png|jpg|jpeg',
                        max_size: 2048
                    });
                }
            },
            hide: function (deleteElement) {
                AppMessages.confirm('Are you sure you want to delete this element?', () => {
                    $(this).slideUp(deleteElement);
                    $(this).remove();

                    calculatePaidAmount();
                    // var inputs = $(".amount");
                    // var subTotal = 0;
                    // for (var i = 0; i < inputs.length; i++) {
                    //     subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                    // }
                    // $('.subTotal').html(subTotal.toFixed(2));
                    // $('.totalAmount').html(subTotal.toFixed(2));
                });
            },
            ready: function (setIndexes) {
                $dragAndDrop.on('drop', setIndexes);
            },
            isFirstItemUndeletable: true
        });
        var value = $(selector + " .repeater").attr('data-value');
        if (typeof value != 'undefined' && value.length != 0) {
            value = JSON.parse(value);
            $repeater.setList(value);
        }

    }

    function calculatePaidAmount() {
        var netAmounts = $('body').find('.new_net_amount');
        var totalNetAmounts = 0;
        for (var i = 0; i < netAmounts.length; i++) {
            if (netAmounts[i].value != '') {
                totalNetAmounts += parseFloat(netAmounts[i].value);
            }
        }
        $('.fullTotalOfPayments').html(totalNetAmounts.toFixed(2));
        $('#fullTotalOfPayments').val(totalNetAmounts);
        // $('#paid_amount').val(totalNetAmounts  - parseFloat($('#advance_amount').val()) );
        // paidAmountInWords( totalNetAmounts  - parseFloat($('#advance_amount').val()) );
    }

    function discount(row, discountVal) {
        var grossAmountVal = row.closest("tr").find(".new_gross_amount").val();
        var discount = 0;
        var grossAmount = 0;
        if (discountVal != '') {
            discount = discountVal;
        } else {
            discount = 0;
        }
        if (grossAmountVal != '') {
            grossAmount = grossAmountVal;
        } else {
            grossAmount = 0;
        }

        var netAmount = parseFloat(grossAmount) - parseFloat(discount);
        row.closest("tr").find(".new_net_amount").val(netAmount);
        // $('#net_amount').val(netAmount);

        calculatePaidAmount();

        // if( $('#sale_type').val() == 0 ){
        // $('#paid_amount').val(netAmount - parseFloat($('#advance_amount').val()) );
        // paidAmountInWords(netAmount - parseFloat($('#advance_amount').val()) );
        // }
    }

    $('body').on('change', '.product', function (event) {
        var product = $(this);
        var productId = product.val();
        $.ajax({
            url: "{{ url('sale/get_details') }}/" + productId,
            method: 'GET',
            dataType: 'json',
            success: function (data, textStatus, jqXHR) {
                if (data.length > 0) {
                    product.closest("tr").find(".new_inventory_id").val(data[0].inventory_id);
                    product.closest("tr").find(".new_unit_price").val(data[0].sale_price);
                    product.closest("tr").find(".new_available_quantity").val(data[0].remain_qty);
                    product.closest("tr").find(".new_quantity").val(1);
                    product.closest("tr").find(".new_gross_amount").val(data[0].sale_price);
                    product.closest("tr").find(".new_discount").val(0);
                    product.closest("tr").find(".new_net_amount").val(data[0].sale_price);

                    calculatePaidAmount();

                    // if(event.isTrigger == 3){
                    //     $('#add_row').trigger('click');
                    // }
                } else {
                    product.val('');
                    product.closest("tr").find(".new_inventory_id").val('');
                    product.closest("tr").find(".new_unit_price").val('');
                    product.closest("tr").find(".new_available_quantity").val('');
                    product.closest("tr").find(".new_quantity").val('');
                    product.closest("tr").find(".new_gross_amount").val('');
                    product.closest("tr").find(".new_discount").val('');
                    product.closest("tr").find(".new_net_amount").val('');
                }
            }
        });
    });

    $('body').on('keyup', '.barcode_input', function (event) {
        if (event.keyCode == 13) {
            var barcode = $(this);
            var barcodeId = barcode.val();
            $.ajax({
                url: "{{ url('sale/product_id_by_barcode') }}/" + barcodeId,
                method: 'GET',
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    barcode.closest('tr').find('.product').val(data[0].id).trigger('change');
                }
            });
        }
    });

    $('body').on('click', '#add_row', function () {
        var barInputs = $('body').find('.barcode_input');
        var lastBarInput = barInputs.length - 1;
        barInputs[lastBarInput].focus();
    });

    $('body').on('input', '.new_unit_price', function () {
        var unitPricetVal = $(this).val();
        var quantVal = $(this).closest("tr").find(".new_quantity").val();
        var discountVal = $(this).closest("tr").find(".new_discount").val();
        discount($(this), discountVal);
    });

    $('body').on('input', '.new_discount', function () {
        var discountVal = $(this).val();
        discount($(this), discountVal);
    });

    $('body').on('input', '.new_quantity', function () {
        var quantityField = $(this);
        var quantity = quantityField.val();
        var availableQuantity = quantityField.closest("tr").find(".new_available_quantity").val();
        if (parseFloat(quantity) > parseFloat(availableQuantity)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'You Have No Available Quantity!'
            });
            quantityField.val(availableQuantity);
        } else {
            var unitPrice = quantityField.closest("tr").find(".new_unit_price").val();
            var gross = parseFloat(unitPrice) * parseFloat(quantity);
            quantityField.closest("tr").find(".new_gross_amount").val(gross);

            var discountVal = quantityField.closest("tr").find(".new_discount").val();
            discount(quantityField, discountVal);
        }
    });

</script>
@endpush
@section('content')
<style>
    #barcode_input_container {
        margin-right: -25px;
        border-radius: 50px;
        display: flex;
        background: #3abaf4;
    }

    #barcode_icon {
        padding: 11px 13px;
        color: #ffffff;
    }

    #barcode_input {
        border-radius: 0px;
        overflow: hidden;
        border-radius: 0px 50px 50px 00px;
        border: 1px solid #3abaf4;
    }

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
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" class="repeater" action='{{route("item_return.store_item_for_item")}}'
                            id="items_form">
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
                                                    <input type="hidden" class="sale_id"
                                                        name="return_item[{{$k}}][sale_id]"
                                                        value="{{ $item->id ?? '' }}">
                                                    {{-- <input type="hidden" class="item_id" name="return_item[{{$k}}][item_id]"
                                                    value="{{ $item->id }}"> --}}
                                                    <input type="hidden" class="invoice_id"
                                                        name="return_item[{{$k}}][invoice_id]"
                                                        value="{{ $item->invoice_id }}">
                                                    <td>
                                                        <input type="hidden" class="inventory_id"
                                                            name="return_item[{{$k}}][inventory_id]"
                                                            value="{{ $item->inventory_id }}">
                                                        <input type="hidden" class="product_id"
                                                            name="return_item[{{$k}}][product_id]"
                                                            value="{{ $item->product_id }}">
                                                        {{ $item->products->name ?? '' }}
                                                    </td>
                                                    <td>
                                                        <input type="hidden" class="item_qty"
                                                            name="return_item[{{$k}}][item_qty]"
                                                            value="{{ $item->quantity }}">
                                                        {{ $item->quantity }}
                                                    </td>
                                                    <td>
                                                        <input type="hidden" class="unit_price"
                                                            name="return_item[{{$k}}][unit_price]"
                                                            value="{{ $item->unit_price }}">
                                                        {{ \Auth::user()->priceFormat($item->unit_price) }}
                                                    </td>
                                                    <td>
                                                        <input type="hidden" class="discount"
                                                            name="return_item[{{$k}}][discount]"
                                                            value="{{ $item->discount }}">
                                                        {{ \Auth::user()->priceFormat($item->discount) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ \Auth::user()->priceFormat($item->net_amount) }}</td>
                                                    <td class="text-right"> @if(isset($item->down_payment))
                                                        {{ \Auth::user()->priceFormat($item->down_payment) }} @else --
                                                        @endif</td>
                                                    <td><input type="number" style="text-align: center;"
                                                            class="form-control return_qty"
                                                            name="return_item[{{$k}}][return_qty]"
                                                            value="{{ $item->quantity }}" min="1"
                                                            max="{{ $item->quantity }}"></td>
                                                    <td class="text-right">
                                                        <input type="hidden" class="return_amount"
                                                            name="return_item[{{$k}}][return_amount]"
                                                            value="{{ $item->net_amount }}">
                                                        <span
                                                            class="return_net_amount">{{ number_format($item->net_amount, 2) }}</span>
                                                    </td>
                                                </tr>
                                                @php
                                                $totalAmount += $item->net_amount;
                                                @endphp
                                                @endforeach
                                                @elseif($modalType == 2)
                                                @foreach ( $items as $k => $item )
                                                <tr>
                                                    <input type="hidden" class="sale_id"
                                                        name="return_item[{{$k}}][sale_id]"
                                                        value="{{ $item->sale_id ?? '' }}">
                                                    <input type="hidden" class="item_id"
                                                        name="return_item[{{$k}}][item_id]" value="{{ $item->id }}">
                                                    <input type="hidden" class="invoice_id"
                                                        name="return_item[{{$k}}][invoice_id]"
                                                        value="{{ $item->invoice_id }}">
                                                    <td>
                                                        <input type="hidden" class="inventory_id"
                                                            name="return_item[{{$k}}][inventory_id]"
                                                            value="{{ $item->inventory_id }}">
                                                        <input type="hidden" class="product_id"
                                                            name="return_item[{{$k}}][product_id]"
                                                            value="{{ $item->product_id }}">
                                                        {{ $item->products->name ?? '' }}
                                                    </td>
                                                    <td>
                                                        <input type="hidden" class="item_qty"
                                                            name="return_item[{{$k}}][item_qty]"
                                                            value="{{ $item->quantity }}">
                                                        {{ $item->quantity }}
                                                    </td>
                                                    <td>
                                                        <input type="hidden" class="unit_price"
                                                            name="return_item[{{$k}}][unit_price]"
                                                            value="{{ $item->unit_price }}">
                                                        {{ \Auth::user()->priceFormat($item->unit_price) }}
                                                    </td>
                                                    <td>
                                                        <input type="hidden" class="discount"
                                                            name="return_item[{{$k}}][discount]"
                                                            value="{{ $item->discount }}">
                                                        {{ \Auth::user()->priceFormat($item->discount) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ \Auth::user()->priceFormat($item->net_amount) }}</td>
                                                    <td><input type="number" style="text-align: center;"
                                                            class="form-control return_qty"
                                                            name="return_item[{{$k}}][return_qty]"
                                                            value="{{ $item->quantity }}" min="1"
                                                            max="{{ $item->quantity }}"></td>
                                                    <td class="text-right">
                                                        <input type="hidden" class="return_amount"
                                                            name="return_item[{{$k}}][return_amount]"
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
                                        <label for="total_amount" style="font-size: 16px; font-weight:bold;">Total
                                            Return Amount</label>
                                        {{ Form::text('total_amount',$totalAmount ?? '', array('class' => 'form-control total_amount', 'style'=>'text-align: center; font-weight: bold;', 'readonly'=>'readonly')) }}
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <div class="col-sm-12">
                                            <div class="section-title">{{__('New Product')}}</div>
                                            @if ($modalType == 2)
                                            <a href="#" data-repeater-create=""
                                                class="btn btn-icon icon-left btn-primary" id="add_row"
                                                data-toggle="modal" data-target="#add-bank">
                                                <i class="fas fa-plus"></i>{{__('Add Item')}}
                                            </a>
                                            @endif
                                            <table class="table table-striped mt-3" data-repeater-list="items"
                                                id="sortable-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{__('Product Code')}}</th>
                                                        <th>{{__('Product')}}</th>
                                                        <th>{{__('Purchasing Price')}} </th>
                                                        <th>{{__('Available Quantity')}} </th>
                                                        <th>{{__('Quantity')}}</th>
                                                        <th>{{__('Gross Amount')}}</th>
                                                        <th>{{__('Discount')}}</th>
                                                        <th>{{__('Net Amount')}}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="ui-sortable" data-repeater-item>
                                                    <tr>
                                                        <td>
                                                            <div id="barcode_input_container">
                                                                <i class="fas fa-barcode" id="barcode_icon"></i>
                                                                {{ Form::text('barcode', '', array('class'=>'form-control barcode_input', 'id'=>'barcode_input')) }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="new_inventory_id"
                                                                id="new_inventory_id" class="new_inventory_id">
                                                            <select class="form-control customer-sel font-style product"
                                                                name="product" id="product" required
                                                                data-empty-message="Please Select Product"
                                                                data-focus="product">
                                                                <option value="">Select Product</option>
                                                                @foreach ($products as $product)
                                                                <option value="{{ $product->products->id }}">
                                                                    {{ $product->products->brands->name.' - '.$product->products->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            {{ Form::number('new_unit_price',null, array('class' => 'form-control new_unit_price', 'required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Purchasing Price", 'data-focus'=>"unit_price")) }}
                                                        </td>
                                                        <td>
                                                            {{ Form::number('new_available_quantity',null, array('class' => 'form-control new_available_quantity', 'readonly' => 'readonly')) }}
                                                        </td>
                                                        <td>
                                                            {{ Form::number('new_quantity',null, array('class' => 'form-control new_quantity','required'=>'required', 'data-empty-message'=>"Please Enter Quantity", 'data-focus'=>"quantity")) }}
                                                        </td>
                                                        <td>
                                                            {{ Form::number('new_gross_amount',null, array('class' => 'form-control new_gross_amount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Gross Amount", 'data-focus'=>"gross_amount")) }}
                                                        </td>
                                                        <td>
                                                            {{ Form::number('new_discount',null, array('class' => 'form-control new_discount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Entert Discount", 'data-focus'=>"discount")) }}
                                                        </td>
                                                        <td>
                                                            {{ Form::number('new_net_amount',null, array('class' => 'form-control new_net_amount','required'=>'required','step'=>'0.01', 'data-empty-message'=>"Please Enter Net Amount", 'data-focus'=>"net_amount")) }}
                                                        </td>
                                                        <td>
                                                            <a href="#" class="fas fa-trash" data-repeater-delete></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4"></td>
                                                        <td colspan="3">
                                                            <input type="hidden" name="fullTotalOfPayments"
                                                                id="fullTotalOfPayments">
                                                            <strong>{{__('Total of Payments')}} </strong>
                                                        </td>
                                                        <td class="text-right fullTotalOfPayments">0.00</td>
                                                        <td></td>
                                                    </tr>

                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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
    });

    $('body').on('input', '.return_qty', function () {
        var returnTr = $(this);
        var returnQty = returnTr.val();
        var unitPirce = returnTr.closest('tr').find('.unit_price').val();
        var discount = returnTr.closest('tr').find('.discount').val();

        var newAmount = parseFloat(returnQty) * parseFloat(unitPirce) - parseFloat(discount);
        returnTr.closest('tr').find('.return_amount').val(newAmount);
        returnTr.closest('tr').find('.return_net_amount').html(newAmount.toFixed(2));
    });

    $('#items_form').submit(function (e) {
        console.log('works');
        e.preventDefault();
        var returnTotalAmount = parseFloat($('.total_amount').val());
        var newNetAmount = parseFloat($('#fullTotalOfPayments').val());

        console.log(returnTotalAmount);
        console.log(newNetAmount);
        if (returnTotalAmount < newNetAmount) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Insufficient Return Balance!'
            });
            return this;
        } else if (returnTotalAmount > newNetAmount) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Still Return Balance Left!'
            });
            return this;
        } else if (returnTotalAmount == newNetAmount) {
            this.submit();
        }
    });
    // function calculatePaidAmount(){
    //     var netAmounts = $('body').find('.new_net_amount');
    //     var totalNetAmounts = 0;
    //     for(var i = 0; i < netAmounts.length; i++){
    //         if(netAmounts[i].value != ''){
    //             totalNetAmounts += parseFloat(netAmounts[i].value);
    //         }
    //     }
    //         $('.fullTotalOfPayments').html(totalNetAmounts.toFixed(2));
    //         $('#fullTotalOfPayments').val(totalNetAmounts);
    //         // $('#paid_amount').val(totalNetAmounts  - parseFloat($('#advance_amount').val()) );
    //         // paidAmountInWords( totalNetAmounts  - parseFloat($('#advance_amount').val()) );
    // }
    // $('body').on('click', '#cash_return', function(){
    //     console.log('works');
    //     $('#items_form').attr('action', '{ route("item_return.items") }}');
    //     $('#items_form').submit();
    // });

</script>
@endsection
