@extends('layouts.admin')
@section('page-title')
    {{ __('GRN') }}
@endsection
<style>
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.3);
        /* Set opacity to 0.3 */
    }
</style>
@push('script-page')
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        var value = {!! json_encode($purchorders->purchaseProducts ?? '') !!};

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
                    if (confirm('Are you sure you want to delete this element?')) {
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
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            // var value = $(selector + " .repeater").attr('data-value');
            // console.log(value);
            if (typeof value != 'undefined' && value.length != 0) {
                // value = JSON.parse(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').parent();
                    tr.find('.item').val(value[i].product_id);
                    changeItem(tr.find('.item'));
                }
            }

            // Get Quantity for the Button
            $(document).on('click', '.add-serial-warranty-btn', function() {
                var row = $(this).closest('tr');
                var quantity = row.find('.quantity').val();
                console.log('Quantity:', quantity);
                $('#serialWarrantyFields').html('Quantity: ' + quantity);
            });

        }
        let currentProductId;
        // Get Quantity for the Button
        $(document).on('click', '.add-serial-warranty-btn', function() {
            var row = $(this).closest('tr');
            currentProductId = $(this).data('product-id');
            var quantity = row.find('.quantity').val();
            console.log('Quantity:', quantity);

            // Clear previous fields
            $('#serialWarrantyFields').html('');

            // Create input fields based on quantity
            for (var i = 0; i < quantity; i++) {
                // $('#serialWarrantyFields').append(`
            //     <div class="form-group">
            //         <label for="serialNumber${i}">{{ __('Serial Number / Color') }} ${i + 1}</label>
            //         <input type="text" class="form-control" id="serialNumber${i}" name="serial_numbers[]" placeholder="{{ __('Enter Serial Number  /  Color ') }}">
            //     </div>
            //     <div class="form-group">
            //         <label for="warrantyNumber${i}">{{ __('Warranty Number') }} ${i + 1}</label>
            //         <input type="text" class="form-control" id="warrantyNumber${i}" name="warranty_numbers[]" placeholder="{{ __('Enter Warranty Number') }}">
            //     </div>
            // `);
                $('#serialWarrantyFields').append(`
                    <div class="form-group">
                        <label for="serialNumber_${currentProductId}_${i}">{{ __('Serial Number / Color') }} ${i + 1}</label>
                        <input type="text" class="form-control" id="serialNumber_${currentProductId}_${i}" placeholder="Enter Serial Number  /  Color " />
                    </div>
                    <div class="form-group">
                        <label for="warrantyNumber_${currentProductId}_${i}">{{ __('Warranty Number') }} ${i + 1}</label>
                        <input type="text" class="form-control" id="warrantyNumber_${currentProductId}_${i}" placeholder="Enter Warranty Number" />
                    </div>
                `);
            }
        });
        // Save Button Handler
        $('#saveSerialWarranty').on('click', function() {

            if (typeof currentProductId === 'undefined') {
                console.error('No product ID set.');
                return;
            }

            let serialNumbers = [];
            let warrantyNumbers = [];
            const quantity = $(`.add-serial-warranty-btn[data-product-id="${currentProductId}"]`).data('quantity');
            console.log('HELLOO', quantity, currentProductId, $(
                `.add-serial-warranty-btn[data-product-id="${currentProductId}"]`).length);

            for (let i = 0; i < quantity; i++) {
                // let serial = $(`#serialNumber_${currentProductId}_${i}`).val();
                // let warranty = $(`#warrantyNumber_${currentProductId}_${i}`).val();
                // serialNumbers.push(serial);
                // warrantyNumbers.push(warranty);
                let serial = $(`#serialNumber_${currentProductId}_${i}`).val();
                let warranty = $(`#warrantyNumber_${currentProductId}_${i}`).val();

                console.log(serial, warranty, "HELOOOO");

                // Push only if there is a value entered
                if (serial) serialNumbers.push(serial);
                if (warranty) warrantyNumbers.push(warranty);
            }

            // Validation to ensure the correct number of entries
            // if (serialNumbers.length !== quantity) {
            //     alert(`Please enter exactly ${quantity} serial numbers.`);
            //     return;
            // }

            // if (warrantyNumbers.length !== quantity) {
            //     alert(`Please enter exactly ${quantity} warranty numbers.`);
            //     return;
            // }

            // Append hidden inputs to the container for the current product
            const hiddenContainer = $(`#hiddenSerialWarrantyContainer_${currentProductId}`);
            hiddenContainer.html(''); // Clear previous inputs

            serialNumbers.forEach((serial) => {
                const serialInput = $('<input>', {
                    type: 'hidden',
                    name: `serial_numbers[${currentProductId}][]`,
                    value: serial
                });
                hiddenContainer.append(serialInput);
            });

            warrantyNumbers.forEach((warranty) => {
                const warrantyInput = $('<input>', {
                    type: 'hidden',
                    name: `warranty_numbers[${currentProductId}][]`,
                    value: warranty
                });
                hiddenContainer.append(warrantyInput);
            });

            // Close the modal
            $('#serialWarrantyModal').modal('hide');
        });
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
                    // console.log(data);
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
                                var amount = (0.00);


                                // $(el.parent().parent().find('.unit_cost')).val(billItems.unit_cost);

                            } else {
                                $(el.parent().parent().find('.receive_quantity')).val(1);
                                // $(el.parent().parent().find('.unit_cost')).val(item.sale_price);

                            }




                            if (billItems != null) {
                                $(el.parent().parent().find('.amount')).html(amount);
                            } else {
                                $(el.parent().parent().find('.amount')).html(item.totalAmount);
                            }

                            var inputs = $(".amount");
                            var subTotal = 0;
                            for (var i = 0; i < inputs.length; i++) {
                                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
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
                    });


                },
            });
        }

        $(document).on('change', '.item', function() {
            changeItem($(this));
        });

        $(document).on('input', '.receive_quantity', function() {
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();
            var quantity = $(this).val();
            var price = $(el.find('.grn_cost')).val();
            var discount = $(el.find('.discount')).val();

            var totalItemPrice = (quantity * price);
            var amount = (totalItemPrice);
            $(el.find('.amount')).html(amount);

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


        $(document).on('input', '.grn_cost', function() {

            var el = $(this).parent().parent().parent().parent();
            var price = $(this).val();
            var unitCost = $(el.find('.unit_cost')).val();
            var quantity = $(el.find('.receive_quantity')).val();
            var discount = $(el.find('.discount')).val();
            var totalItemPrice = (quantity * price);

            if (parseFloat(price) < parseFloat(unitCost)) {
                $(this).removeClass('text-danger');
                $(this).addClass('text-info');
            } else if (parseFloat(price) == parseFloat(unitCost)) {
                $(this).removeClass('text-info');
                $(this).removeClass('text-danger');
            } else if (parseFloat(price) > parseFloat(unitCost)) {
                $(this).removeClass('text-info');
                $(this).addClass('text-danger');
            }

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
        <div class="section-header">
            <h1>{{ __('Create GRN') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('purchase-order.index') }}">{{ __('Purchase Order') }}</a>
                </div>
                <div class="breadcrumb-item active">{{ __('Edit') }}</div>
            </div>
        </div>

        <div class="section-body">
            {{ Form::open(['url' => 'grn-order']) }}
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="row justify-content-center">
                <input type="hidden" name="total_amount" id="totalAmount">

                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Create GRN ') }}</h3>
                                <div class="section-header-breadcrumb d-flex ">
                                    <div class="breadcrumb-item"><a
                                            href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="vender-box">
                                        <div class="input-group">
                                            {{ Form::label('vender_id', __('Supplier')) }}<span class="text-danger">
                                                *</span>
                                            {{ Form::select('vender_id', $venders, $purchorders->vender_id, ['class' => 'form-control customer-sel font-style selectric ', 'id' => 'vender', 'data-url' => route('bill.vender'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <div id="vender_detail" class="d-none">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('order_date', __('Order Date ')) }}<span
                                                    class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('order_date', $purchorders->order_date, ['class' => 'form-control datepicker', 'required' => 'required']) }}
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
                                                {{ Form::label('required_by', __('Required By ')) }}<span
                                                    class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('required_by', $purchorders->required_by, ['class' => 'form-control datepicker', 'required' => 'required']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="hidden" name="order_id" value="{{ $purchorders->id }}">
                                                {{ Form::label('purchase_order_number', __('Purchase Order Number')) }}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{-- <input type="text" class="form-control" value="{{$purchase_order_number}}" readonly> --}}
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
                            {{-- <a href="#" data-repeater-create="" class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#add-bank">
                                <i class="fas fa-plus"></i>{{__('Add Item')}}
                            </a> --}}
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-responsive" data-repeater-list="items"
                                id="sortable-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Items') }}</th>
                                        <th>{{ __('Unit Cost') }} <span class="text-danger"> *</span></th>
                                        <th>{{ __('Quantity') }}<span class="text-danger"> *</span></th>
                                        <th>{{ __('GRN Cost') }} <span class="text-danger"> *</span></th>
                                        <th>{{ __('Cash Price') }}<span class="text-danger"> *</span> </th>
                                        <th>{{ __('Received Quantity') }}<span class="text-danger"> *</span></th>
                                        <th class="text-right">{{ __('Amount') }} </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="ui-sortable" data-repeater-item>
                                    <tr>
                                        {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                        <td width="20%">
                                            {{ Form::select('item', $product_services, null, ['class' => 'form-control font-style item', 'data-url' => route('proposal.product'), 'readonly' => 'readonly', 'disabled' => 'disabled', 'style' => 'min-width: 10rem;']) }}
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('unit_cost', $item->unit_cost ?? null, ['class' => 'form-control unit_cost', 'required' => 'required', 'placeholder' => __('Unit Cost'), 'required' => 'required', 'readonly' => 'readonly', 'style' => 'min-width: 5rem;']) }}
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('quantity', $item->quantity ?? null, ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required', 'readonly' => 'readonly', 'style' => 'min-width: 5rem;']) }}
                                                    {{-- <div class="input-group-append">
                                                    <div class="input-group-text unit">

                                                    </div>
                                                </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('grn_cost', $item->grn_cost ?? null, ['class' => 'form-control grn_cost', 'required' => 'required', 'placeholder' => __('0.00'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('sale_price', $item->sale_price ?? null, ['class' => 'form-control sale_price', 'required' => 'required', 'placeholder' => __('0.00'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('receive_quantity', $item->receive_quantity ?? null, ['class' => 'form-control receive_quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                                    {{-- <div class="input-group-append">
                                                    <div class="input-group-text unit">

                                                    </div>
                                                </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right amount">
                                            {{ number_format($item->amount ?? 0, 2) }}
                                        </td>
                                        <td>
                                            @if (isset($product))
                                                <button type="button"
                                                    class="btn btn-primary btn-sm add-serial-warranty-btn"
                                                    data-toggle="modal" data-target="#serialWarrantyModal"
                                                    data-product-id="{{ $product->id }}"
                                                    data-quantity="{{ $product->quantity }}" style="margin-bottom: 5px;">
                                                    {{ __('Add Serial & Warranty / Color ') }}
                                                </button>
                                                @can('delete proposal product')
                                                    <a href="#" class="fas fa-trash" data-repeater-delete></a>
                                                @endcan
                                            @else
                                                <p>Product information is not available.</p>
                                            @endif

                                            <!-- Hidden inputs for serial and warranty numbers -->
                                            <div id="hiddenSerialWarrantyContainer_{{ $product->id }}"></div>
                                        </td>
                                    </tr>
                                </tbody>
                                <!-- Modal -->
                                <div class="modal fade" id="serialWarrantyModal" tabindex="-1" role="dialog"
                                    aria-labelledby="serialWarrantyModalLabel" aria-hidden="true" data-backdrop="false">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="serialWarrantyModalLabel">
                                                    {{ __('Add Serial & Warranty / Color ') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="serialWarrantyFields">
                                                <!-- Dynamic fields will be inserted here -->
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('Close') }}</button>
                                                <button type="button" class="btn btn-primary"
                                                    id="saveSerialWarranty">{{ __('Save') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <tfoot>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td colspan="2"><strong>{{ __('Sub Total') }}
                                                ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                        <td class="text-right subTotal">0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td colspan="2"><strong>{{ __('Total Amount') }}
                                                ({{ \Auth::user()->currencySymbol() }})</strong></td>
                                        <td class="text-right totalAmount">0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-right mb-5"><a href="{{ route('purchase-order.index') }}"
                    class="btn btn-secondary">{{ __('Cancel') }}</a>
                {{ Form::submit(__('Save'), ['class' => 'btn btn-primary']) }}
            </div>
            {{ Form::close() }}
        </div>
    </section>
    <script>
        $(document).submit(function() {
            $('body :input').prop("disabled", false);
        });

        // Save Button Handler
        // $('#saveSerialWarranty').on('click', function() {
        //     // Gather all the serial numbers and warranty numbers
        //     const quantity = $('.add-serial-warranty-btn[data-product-id="' + currentProductId + '"]')
        //         .data('quantity');
        //     let serialNumbers = [];
        //     let warrantyNumbers = [];

        //     for (let i = 0; i < quantity; i++) {
        //         let serial = document.getElementById(`serial_number_${i}`).value;
        //         let warranty = document.getElementById(`warranty_number_${i}`).value;
        //         serialNumbers.push(serial);
        //         warrantyNumbers.push(warranty);
        //     }

        //     // Append hidden inputs to the container for the current product
        //     const hiddenContainer = document.getElementById(
        //         `hiddenSerialWarrantyContainer_${currentProductId}`);
        //     hiddenContainer.innerHTML = '';

        //     serialNumbers.forEach((serial, index) => {
        //         const serialInput = document.createElement('input');
        //         serialInput.type = 'hidden';
        //         serialInput.name =
        //             `serial_numbers[${currentProductId}][]`; // Adjust name as per your backend logic
        //         serialInput.value = serial;
        //         hiddenContainer.appendChild(serialInput);
        //     });

        //     warrantyNumbers.forEach((warranty, index) => {
        //         const warrantyInput = document.createElement('input');
        //         warrantyInput.type = 'hidden';
        //         warrantyInput.name =
        //             `warranty_numbers[${currentProductId}][]`; // Adjust name as per your backend logic
        //         warrantyInput.value = warranty;
        //         hiddenContainer.appendChild(warrantyInput);
        //     });

        //     // Close the modal
        //     $('#serialWarrantyModal').modal('hide');
        // });
        // });
    </script>
@endsection
