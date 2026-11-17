@extends('layouts.admin')
@section('page-title')
    {{ __('Purchase Order Edit') }}
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        var value = {!! json_encode($purchorders->purchaseProducts) !!};
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: true,
                defaultValues: {
                    'status': 1
                },
                show: function() {
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
                        var el = $(this);
                        var id = $(el.find('.id')).val();
                        $.ajax({
                            url: '{{ route('purchase-order.product.destroy') }}',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': jQuery('#token').val()
                            },
                            data: {
                                'id': id
                            },
                            cache: false,
                            success: function(data) {},
                        });

                        $(this).slideUp(deleteElement);
                        $(this).remove();
                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                        $('#totalAmount').html(subTotal.toFixed(2));
                    });
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            // var value = $(selector + " .repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                // value = JSON.parse(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').parent();
                    tr.find('.item').val(value[i].product_id);
                    changeItem(tr.find('.item'));
                }
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
        });

        var purchase_order_id = '{{ $purchorders->id }}';

        function changeItem(element) {
            var iteams_id = element.val();

            var url = element.data('url');
            var el = element;
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'product_id': iteams_id
                },
                cache: false,
                success: function(data) {
                    var item = JSON.parse(data);
                    $.ajax({
                        url: '{{ route('purchase-order.items') }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': jQuery('#token').val()
                        },
                        data: {
                            'purchase_order_id': purchase_order_id,
                            'product_id': iteams_id,
                        },
                        cache: false,
                        success: function(data) {
                            var billItems = JSON.parse(data);
                            if (billItems != null) {
                                var amount = (billItems.unit_cost * billItems.quantity);

                                $(el.parent().parent().find('.quantity')).val(billItems.quantity);
                                $(el.parent().parent().find('.unit_cost')).val(parseFloat(billItems
                                    .unit_cost).toFixed(2));

                            } else {
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
                                        $(el.parent().parent().find('.quantity')).val(
                                            1);

                                        if (data) {
                                            var last_purchase_price = parseFloat(data
                                                .last_purchase_price);
                                            $(el.parent().parent().find('.unit_cost'))
                                                .val(last_purchase_price.toFixed(2));
                                            $(el.parent().parent().find('.amount'))
                                                .html(last_purchase_price.toFixed(2));

                                        } else {
                                            $(el.parent().parent().find('.unit_cost'))
                                                .val(0);
                                            $(el.parent().parent().find('.amount'))
                                                .html('0.00');
                                        }
                                    },
                                    complete: function() {
                                        var inputs = $(".amount");
                                        var subTotal = 0;
                                        for (var i = 0; i < inputs.length; i++) {
                                            subTotal = parseFloat(subTotal) +
                                                parseFloat($(inputs[i]).html());
                                        }

                                        $('.subTotal').html(subTotal.toFixed(2));

                                        var totalItemPrice = 0;
                                        var priceInput = $('.unit_cost');
                                        for (var j = 0; j < priceInput.length; j++) {
                                            totalItemPrice += parseFloat(priceInput[j]
                                                .value);
                                        }

                                        var totalItemTaxPrice = 0;
                                        var itemTaxPriceInput = $('.itemTaxPrice');
                                        for (var j = 0; j < itemTaxPriceInput
                                            .length; j++) {
                                            totalItemTaxPrice += parseFloat(
                                                itemTaxPriceInput[j].value);
                                        }

                                        $('.totalAmount').html((parseFloat(subTotal) +
                                                parseFloat(totalItemTaxPrice))
                                            .toFixed(2));
                                        $('#totalAmount').val((parseFloat(subTotal) +
                                                parseFloat(totalItemTaxPrice))
                                            .toFixed(2));
                                    }
                                });
                            }

                            if (billItems != null) {
                                $(el.parent().parent().find('.amount')).html(amount.toFixed(2));
                                $(el.parent().parent().find('#totalAmount')).val(amount.toFixed(2));

                                var inputs = $(".amount");
                                var subTotal = 0;
                                for (var i = 0; i < inputs.length; i++) {
                                    subTotal = parseFloat(subTotal) + parseFloat($(inputs[i])
                                        .html());
                                }
                                $('.subTotal').html(subTotal.toFixed(2));

                                var totalItemPrice = 0;
                                var priceInput = $('.price');
                                for (var j = 0; j < priceInput.length; j++) {
                                    totalItemPrice += parseFloat(priceInput[j].value);
                                }

                                var totalItemTaxPrice = 0;
                                var itemTaxPriceInput = $('.itemTaxPrice');
                                for (var j = 0; j < itemTaxPriceInput.length; j++) {
                                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                                }

                                $('.totalAmount').html(subTotal.toFixed(2));
                                $('#totalAmount').val(subTotal.toFixed(2));

                            }
                        }
                    });


                },
            });
        }

        $(document).on('change', '.item', function() {
            changeItem($(this));
        });

        $(document).on('input', '.quantity', function() {
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();
            var quantity = $(this).val();
            var price = $(el.find('.unit_cost')).val();
            var discount = $(el.find('.discount')).val();

            var totalItemPrice = (quantity * price);
            var amount = (totalItemPrice);
            $(el.find('.amount')).html(amount);

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
            $(el.find('.amount')).html(amount);


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

        $(document).on('click', '[data-repeater-create]', function() {
            $('.item :selected').each(function() {
                var id = $(this).val();
                $(".item option[value=" + id + "]").prop("disabled", true);
            });
        })
    </script>
@endpush
@section('content')
    <section class="section">
        <div class="section-body mb-5">


            <div class="col-12">
                <div class="bg-white cus_show p-2 br-4 mb-2">
                    <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                        <h4 class="mt-2 ">{{ __('Purchase Order Edit') }}</h3>
                            <div class="section-header-breadcrumb d-flex ">
                                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                                </div>
                                <div class="breadcrumb-item"><a
                                        href="{{ route('purchase-order.index') }}">{{ __('Purchase Order') }}</a></div>
                                <div class="breadcrumb-item active">{{ __('Edit') }}</div>
                            </div>
                    </div>
                </div>
            </div>

            {{ Form::model($purchorders, ['route' => ['purchase-order.update', $purchorders->id], 'method' => 'PUT']) }}
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="row">
                <input type="hidden" name="total_amount" id="totalAmount">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="vender-box">
                                        <div class="input-group">
                                            {{ Form::label('vender_id', __('Supplier')) }}<span class="text-danger">
                                                *</span>
                                            {{ Form::select('vender_id', $venders, null, ['class' => 'form-control customer-sel font-style selectric ', 'id' => 'vender', 'data-url' => route('bill.vender'), 'required' => 'required']) }}
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
                                                    {{ Form::text('order_date', null, ['class' => 'form-control datepicker', 'required' => 'required']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    {{ Form::label('status', __('Status')) }}<span class="text-danger">
                                                        *</span>
                                                    <select name="status" id="status"
                                                        class="form-control customer-sel font-style selectric" required>
                                                        <option value="">Select Order Status</option>
                                                        <option @if ($purchorders->status == 3) selected @endif
                                                            value="3">Approved</option>
                                                        <option @if ($purchorders->status == 2) selected @endif
                                                            value="2">Not yet approved</option>
                                                        <option @if ($purchorders->status == 1) selected @endif
                                                            value="1">Cancelled</option>
                                                        <option @if ($purchorders->status == 0) selected @endif
                                                            value="0">Rejected</option>
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
                                                    {{ Form::text('required_by', null, ['class' => 'form-control datepicker']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('purchase_order_number', __('Purchase Order Number')) }}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('purchase_order_number', $purchase_order_number ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) }}
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
            <div class="col-12">
                <div class="card repeater" data-value='{!! json_encode($purchorders->purchaseProducts) !!}'>
                    <div class="card-header">
                        <h4>{{ __('Product & Services') }}</h4>
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
                                        <th>{{ __('Unit Cost') }} ({{ $currencySymbol }})<span
                                                class="text-danger">*</span></th>
                                        <th class="text-right">{{ __('Amount') }} ({{ $currencySymbol }}) </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="ui-sortable" data-repeater-item>
                                    <tr>
                                        {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                        <td width="30%">
                                            {{ Form::select('item', $product_services, null, ['class' => 'form-control font-style item', 'data-url' => route('proposal.product'), 'style' => 'min-width: 14rem;']) }}
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('quantity', null, ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required']) }}
                                                    <div class="input-group-append">
                                                        <div class="input-group-text unit">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('unit_cost', null, ['class' => 'form-control unit_cost', 'required' => 'required', 'placeholder' => __('Unit Cost'), 'required' => 'required']) }}
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right amount">
                                            0.00
                                        </td>

                                        <td>
                                            @can('delete proposal product')
                                                <a href="#" class="fas fa-trash" data-repeater-delete></a>
                                            @endcan
                                        </td>
                                    </tr>
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
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td><strong>{{ __('Total Amount') }}</strong>
                                        </td>
                                        <td class="text-right totalAmount">0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-right">
                <a href="{{ route('purchase-order.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                {{ Form::submit(__('Update'), ['class' => 'btn btn-primary']) }}
            </div>
            {{ Form::close() }}
        </div>
    </section>
@endsection
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
    var purchOrders = < php echo json_encode($purchorders) ? > ;
    $(document).ready(function () {
        console.log(purchOrders);
    });

</script> --}}
