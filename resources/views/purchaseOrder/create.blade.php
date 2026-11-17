@extends('layouts.admin')
@section('page-title')
    {{ __('Purchase Order Create') }}
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('assets/js/product-search.js') }}"></script>
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
                show: function() {
                    resetProductSearch(this);
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
                hide: function(deleteElement) {
                    AppMessages.confirm('Are you sure you want to delete this element?', () => {
                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                        $('#totalAmount').val(subTotal.toFixed(2));
                    });
                },
                ready: function(setIndexes) {
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

        $(document).on('change', '#vender', function() {
            $('#vender_detail').removeClass('d-none');
            $('#vender_detail').addClass('d-block');
            $('#vender-box').removeClass('d-block');
            $('#vender-box').addClass('d-none');
            var id = $(this).val();
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function(data) {
                    if (data != '') {
                        $('#vender_detail').html(data);
                    } else {
                        $('#vender-box').removeClass('d-none');
                        $('#vender-box').addClass('d-block');
                        $('#vender_detail').removeClass('d-block');
                        $('#vender_detail').addClass('d-none');
                    }
                },
            });
        });

        $(document).on('click', '#remove', function() {
            $('#vender-box').removeClass('d-none');
            $('#vender-box').addClass('d-block');
            $('#vender_detail').removeClass('d-block');
            $('#vender_detail').addClass('d-none');
        })


        $(document).on('change', '.item', function() {
            var iteams_id = $(this).val();
            var url = $(this).data('url');
            var el = $(this);
            //             $.ajax({
            //                 url: url,
            //                 type: 'POST',
            //                 headers: {
            //                     'X-CSRF-TOKEN': jQuery('#token').val()
            //                 },
            //                 data: {
            //                     'product_id': iteams_id
            //                 },
            //                 cache: false,
            //                 success: function (data) {
            //                     var item = JSON.parse(data);
            // console.log(item);

            $.ajax({
                url: '{{ route('get_last_purchase_price') }}',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'product_id': iteams_id
                },
                cache: false,
                success: function(data) {
                    $(el.parent().parent().find('.quantity')).val(1);

                    if (data) {
                        var last_purchase_price = parseFloat(data.last_purchase_price);
                        $(el.parent().parent().find('.unit_cost')).val(last_purchase_price.toFixed(2));
                        $(el.parent().parent().find('.amount')).html(last_purchase_price.toFixed(2));

                    } else {
                        $(el.parent().parent().find('.unit_cost')).val(0);
                        $(el.parent().parent().find('.amount')).html('0.00');
                    }
                },
                complete: function() {
                    var inputs = $(".amount");
                    var subTotal = 0;
                    for (var i = 0; i < inputs.length; i++) {
                        subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                    }

                    $('.subTotal').html(subTotal.toFixed(2));

                    var totalItemPrice = 0;
                    var priceInput = $('.unit_cost');
                    for (var j = 0; j < priceInput.length; j++) {
                        totalItemPrice += parseFloat(priceInput[j].value);
                    }

                    var totalItemTaxPrice = 0;
                    var itemTaxPriceInput = $('.itemTaxPrice');
                    for (var j = 0; j < itemTaxPriceInput.length; j++) {
                        totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                    }

                    $('.totalAmount').html((parseFloat(subTotal) + parseFloat(totalItemTaxPrice))
                        .toFixed(2));
                    $('#totalAmount').val((parseFloat(subTotal) + parseFloat(totalItemTaxPrice))
                        .toFixed(2));
                }
            });

            // var taxes = '';
            // var tax = [];

            // var totalItemTaxRate = 0;
            // for (var i = 0; i < item.length; i++) {
            //     taxes += '<span class="badge badge-primary mt-1">' + item[i].name + ' ' + '(' + item[i].rate + '%)' + '</span>';
            //     tax.push(item[i].id);
            //     totalItemTaxRate += parseFloat(item[i].rate);
            // }

            // var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (item.sale_price * 1));

            // $(el.parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
            // $(el.parent().parent().find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
            // $(el.parent().parent().find('.taxes')).html(taxes);
            // $(el.parent().parent().find('.tax')).val(tax);
            // $(el.parent().parent().find('.unit')).html(item.unit);
            // $(el.parent().parent().find('.discount')).val(0);
        });

        $(document).on('input', '.quantity', function() {
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();
            // var el = $(this).parent();
            console.log(el);
            var quantity = $(this).val();
            var price = $(el.find('.unit_cost')).val();
            var discount = $(el.find('.discount')).val();

            var totalItemPrice = (quantity * price);
            var amount = (totalItemPrice);
            $(el.find('.amount')).html(amount.toFixed(2));

            // var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            // var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            // $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));


            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }
            $('.subTotal').html(subTotal.toFixed(2));
            // $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal) + parseFloat(totalItemTaxPrice)).toFixed(2));
            $('#totalAmount').val((parseFloat(subTotal) + parseFloat(totalItemTaxPrice)).toFixed(2));

        })

        $(document).on('input', '.unit_cost', function() {
            var el = $(this).parent().parent().parent().parent();
            var price = $(this).val();
            var quantity = $(el.find('.quantity')).val();
            var discount = $(el.find('.discount')).val();
            var totalItemPrice = (quantity * price);

            var amount = (totalItemPrice);
            $(el.find('.amount')).html(amount.toFixed(2));


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));


            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.subTotal').html(subTotal.toFixed(2));
            $('.totalAmount').html((parseFloat(subTotal) + parseFloat(totalItemTaxPrice)).toFixed(2));
            $('#totalAmount').val((parseFloat(subTotal) + parseFloat(totalItemTaxPrice)).toFixed(2));

        })




        // $(document).on('keyup', '.discount', function () {
        //     var el = $(this).parent().parent().parent().parent();
        //     var discount = $(this).val();
        //     var price = $(el.find('.price')).val();

        //     var quantity = $(el.find('.quantity')).val();
        //     var totalItemPrice = (quantity * price);

        //     var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
        //     var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
        //     $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));


        //     var totalItemTaxPrice = 0;
        //     var itemTaxPriceInput = $('.itemTaxPrice');
        //     for (var j = 0; j < itemTaxPriceInput.length; j++) {
        //         totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
        //     }


        //     var totalItemDiscountPrice = 0;
        //     var itemDiscountPriceInput = $('.discount');

        //     for (var k = 0; k < itemDiscountPriceInput.length; k++) {

        //         totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
        //     }

        //     var amount = (totalItemPrice);
        //     $(el.find('.amount')).html(amount);

        //     var inputs = $(".amount");
        //     var subTotal = 0;
        //     for (var i = 0; i < inputs.length; i++) {
        //         subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
        //     }
        //     $('.subTotal').html(subTotal.toFixed(2));
        //     $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
        //     $('.totalTax').html(totalItemTaxPrice.toFixed(2));

        //     $('.totalAmount').html((parseFloat(subTotal) - parseFloat(totalItemDiscountPrice) + parseFloat(totalItemTaxPrice)).toFixed(2));
        // })

        var vendorId = '{{ $vendorId }}';
        if (vendorId > 0) {
            $('#vender').val(vendorId).change();
        }
    </script>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Create Purchase Order') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('purchase-order.index') }}">{{ __('Purchase Order') }}</a>
                </div>
                <div class="breadcrumb-item active">{{ __('create') }}</div>
            </div>
        </div>
        <div class="section-body">
            {{ Form::open(['url' => 'purchase-order']) }}
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="row">
                <input type="hidden" name="total_amount" id="totalAmount">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="vender-box">
                                        {{ Form::label('vender_id', __('Supplier')) }}<span class="text-danger"> *</span>
                                        <div class="input-group">
                                            {{ Form::select('vender_id', $venders, $vendorId, ['class' => 'form-control customer-sel font-style selectric ', 'id' => 'vender', 'data-url' => route('purchase-order.vender'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <div id="vender_detail" class="d-none">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('order_date', __('Order Date')) }}<span class="text-danger">
                                                    *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('order_date', '', ['class' => 'form-control datepicker', 'required' => 'required']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('status', __('Status')) }}<span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <select name="status" id="status"
                                                        class="form-control customer-sel font-style selectric" required>
                                                        <option value="">Select Order Status</option>
                                                        <option value="3">Approved</option>
                                                        <option value="2">Not yet approved</option>
                                                        <option value="1">Cancelled</option>
                                                        <option value="0">Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('required_by', __('Debit date')) }}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('required_by', '', ['class' => 'form-control datepicker']) }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('bill_number', __('Bill Number')) }}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-file"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{$bill_number}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                {{ Form::label('category_id', __('Category')) }}
                                                {{ Form::select('category_id', $category,null, array('class' => 'form-control customer-sel font-style selectric','required'=>'required')) }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('order_number', __('Order Number')) }}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-basketball-ball"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::number('order_number', '', array('class' => 'form-control')) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox mt-4">
                                                <input class="custom-control-input" type="checkbox" name="discount_apply" id="discount_apply">
                                                <label class="custom-control-label" for="discount_apply">{{__('Discount Apply')}}</label>
                                            </div>
                                        </div>
                                        @if (!$customFields->isEmpty())
                                            <div class="col-md-6">
                                                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                                    @include('customFields.formBuilder')
                                                </div>
                                            </div>
                                        @endif --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card repeater">
                    <div class="card-header">
                        <h4>{{ __('Product') }}</h4>
                        <div class="card-header-action">
                            <a href="#" data-repeater-create="" class="btn btn-icon icon-left btn-primary"
                                data-toggle="modal" data-target="#add-bank">
                                <i class="fas fa-plus"></i>{{ __('Add Item') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped" data-repeater-list="items" id="sortable-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}<span class="text-danger"> *</span></th>
                                        <th>{{ __('Quantity') }}<span class="text-danger"> *</span></th>
                                        <th>{{ __('Unit Cost') }} ({{ $currencySymbol }})<span class="text-danger">
                                                *</span></th>
                                        {{-- <th>{{__('Tax')}} (%)</th>
                                    <th>{{__('Discount')}}</th> --}}
                                        <th class="text-right">{{ __('Amount') }} </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="ui-sortable" data-repeater-item>
                                    <tr>
                                        <td width="30%">
                                            <input type="search" class="form-control product-search mb-1" placeholder="{{ __('Search name or barcode') }}" aria-label="{{ __('Search products by name or barcode') }}" autocomplete="off">
                                            {{ Form::select('item', $product_services, '', ['class' => 'item d-none', 'data-url' => route('proposal.product'), 'tabindex' => '-1', 'aria-hidden' => 'true']) }}
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::number('quantity', '', ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty')]) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('unit_cost', '', ['class' => 'form-control unit_cost', 'required' => 'required', 'placeholder' => __('Unit Cost'), 'required' => 'required']) }}
                                                    {{-- <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        {{-- <td>
                                        <div class="form-group">
                                            <div class="input-group colorpickerinput">
                                                <div class="taxes"></div>
                                                {{ Form::hidden('tax','', array('class' => 'form-control tax')) }}
                                                {{ Form::hidden('itemTaxPrice','', array('class' => 'form-control itemTaxPrice')) }}
                                                {{ Form::hidden('itemTaxRate','', array('class' => 'form-control itemTaxRate')) }}
                                            </div>
                                        </div>
                                    </td> --}}
                                        {{-- <td>
                                        <div class="form-group">
                                            <div class="input-group colorpickerinput">
                                                {{ Form::text('discount','', array('class' => 'form-control discount','required'=>'required','placeholder'=>__('Discount'))) }}
                                                <div class="input-group-append">

                                                    <div class="input-group-text">
                                                        {{\Auth::user()->currencySymbol()}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td> --}}
                                        <td class="text-right amount">
                                            0.00
                                        </td>
                                        <td>
                                            <a href="#" class="fas fa-trash" data-repeater-delete></a>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                    <td colspan="2">   {{ Form::textarea('description', null, ['class'=>'form-control','rows'=>'2','placeholder'=>__('Description')]) }}</td>
                                    <td colspan="4"></td>
                                </tr> --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td></td>
                                        <td><strong>{{ __('Sub Total') }}</strong>
                                        </td>
                                        <td class="text-right subTotal">0.00</td>
                                        <td></td>
                                    </tr>
                                    {{-- <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td><strong>{{__('Discount')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                    <td class="text-right totalDiscount">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                    <td><strong>{{__('Tax')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                    <td class="text-right totalTax">0.00</td>
                                    <td></td>
                                </tr> --}}
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td><strong>{{ __('Total Amount') }}</strong></td>
                                        <td class="text-right totalAmount font-weight-bold">0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 text-right mb-5">
                <a href="{{ route('purchase-order.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
            </div>
            {{ Form::close() }}
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });

        $(document).on('focusout', '.unit_cost', function() {
            let value = parseFloat($(this).val()).toFixed(2);
            $(this).val(value);
        });
    </script>
@endsection
