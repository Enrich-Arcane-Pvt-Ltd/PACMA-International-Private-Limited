@extends('layouts.admin')
@push('script-page')
    <script>
        $(document).on('click', '#save-invoice-number', function () {
            var input = document.getElementById('supplier_invoice_number');
            if (!input.reportValidity()) return;
            var button = $(this).prop('disabled', true);
            var message = $('#invoice-save-message').text('');
            $.ajax({
                url: @json(route('grn.invoice-number', $grns->id)),
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json'},
                data: {supplier_invoice_number: input.value},
                success: function (data) {
                    message.removeClass('text-danger').addClass('text-success').text(data.message);
                },
                error: function (xhr) {
                    var data = xhr.responseJSON || {};
                    message.removeClass('text-success').addClass('text-danger').text(data.message || 'Could not save invoice number. Please sign in again or retry.');
                },
                complete: function () { button.prop('disabled', false); }
            });
        });
    </script>
@endpush
@section('page-title')
    {{ __('GRN') }}
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/grn-items.css?v=2') }}">
@endpush
<style>
    #sortable-table tbody td {
        vertical-align: top;
        padding-top: 12px;
    }
    #sortable-table tbody .form-group {
        margin: 0;
    }

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.3);
        /* Set opacity to 0.3 */
    }

    #confirmationMessageContainer .alert {
        background-color: #93e9a7 !important;
        /* Green background */
        color: #424242 !important;
        /* White text */
        border-color: #93e9a7 !important;
        /* Green border */
    }

    .modal-body {
        max-height: 400px;
        overflow-y: auto;
    }

    .modal-dialog {
        margin-top: 80px;
    }

    .modal {
        z-index: 1050;
    }
</style>
@push('script-page')
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('assets/js/product-search.js') }}"></script>
    <script src="{{ asset('assets/js/grn-edit-warranty.js') }}"></script>
    <script>
        var value = {!! json_encode($grns->GrnProducts ?? '') !!};

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
                    resetProductSearch(this);
                    if (!$(this).find('.id').val()) {
                        $(this).find('tr').addBack('tr').removeAttr('data-batch-key');
                        $(this).find('.batch-key').val('');
                        $(this).find('.quantity, .receive_quantity').val(1);
                        $(this).find('.unit_cost, .grn_cost, .sale_price, .hire_price').val('0.00');
                        $(this).find('.amount').text('0.00');
                        $(this).find('.row-serial-storage').empty();
                    }
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
                        $(this).slideUp(deleteElement);
                        $(this).remove();
                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                    });
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
                    var tr = $('#sortable-table .id').filter(function () {
                        return String(this.value) === String(value[i].id);
                    }).closest('tr');
                    tr.find('.item').val(value[i].product_id);
                    tr.find('.product-search').val(tr.find('.item option:selected').text().split(' - ')[0].trim());
                    tr.find('.item_name').val((value[i].products && value[i].products.name) || '');
                    tr.find('.item_model').val((value[i].products && value[i].products.brands && value[i].products.brands.name) || '');
                    tr.find('.item').data('previous-product', String(value[i].product_id));
                    tr.find('.quantity, .receive_quantity').val(parseInt(value[i].received_qty, 10) || 1);
                    tr.find('.amount').text((parseFloat(value[i].amount) || 0).toFixed(2));
                }
                var initialTotal = value.reduce(function (sum, line) { return sum + (parseFloat(line.amount) || 0); }, 0);
                $('.subTotal, .totalAmount').text(initialTotal.toFixed(2));
                $('#totalAmount').val(initialTotal.toFixed(2));
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

        var grn_id = '{{ $grns->id }}';
        var grnProducts = {!! json_encode($grns->GrnProducts ?? []) !!};
        if (Array.isArray(grnProducts) && grnProducts.length > 0) {
            grnProducts.forEach(function(product) {
                console.log('Product ID:', product.product_id);
            });
        } else {
            console.log('No products associated with this GRN');
        }



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
                    var trRow = el.closest('tr');
                    trRow.find('.item_name').val(item.product_name || (item.product && item.product.name) || '');
                    trRow.find('.item_model').val(item.product_model || (item.product && item.product.brands && item.product.brands.name) || '');
                    // console.log('------------------------');
                    // console.log(item);
                    $.ajax({
                        url: '{{ route('grn.items') }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': jQuery('#token').val()
                        },
                        data: {
                            'grn_id': grn_id,
                            'product_id': iteams_id,
                        },
                        cache: false,
                        success: function(data) {
                            var billItems = JSON.parse(data);
                            // console.log(billItems);
                            if (billItems != null) {
                                var amount = (0.00);

                                if (parseFloat(billItems.grn_cost) < parseFloat(billItems
                                        .unit_cost)) {
                                    $(el.parent().parent().find('.grn_cost')).removeClass(
                                        'text-danger');
                                    $(el.parent().parent().find('.grn_cost')).addClass('text-info');
                                } else if (parseFloat(billItems.grn_cost) > parseFloat(billItems
                                        .unit_cost)) {
                                    $(el.parent().parent().find('.grn_cost')).removeClass(
                                        'text-info');
                                    $(el.parent().parent().find('.grn_cost')).addClass(
                                        'text-danger');
                                } else {
                                    $(el.parent().parent().find('.grn_cost')).removeClass(
                                        'text-danger');
                                    $(el.parent().parent().find('.grn_cost')).removeClass(
                                        'text-info');
                                }


                                $(el.parent().parent().find('.unit_cost')).val(billItems.unit_cost);
                                $(el.parent().parent().find('.receive_quantity')).val(billItems
                                    .received_qty);
                                $(el.parent().parent().find('.quantity')).val(billItems.received_qty);
                                $(el.parent().parent().find('.amount')).html(billItems.amount);
                            }
                            // else {
                            //     $(el.parent().parent().find('.receive_quantity')).val(1);
                            //     $(el.parent().parent().find('.unit_cost')).val(item.sale_price);

                            // }




                            // if (billItems != null) {
                            //     $(el.parent().parent().find('.amount')).html(billItems.amount);
                            // }
                            // else {
                            //     $(el.parent().parent().find('.amount')).html(item.sale_price);
                            // }

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
            var select = $(this);
            var previous = select.data('previous-product');
            var productId = select.val();
            if (!productId) { refreshGrnProductOptions(); return; }
            if (previous && String(previous) !== String(productId)) {
                var row = select.closest('tr');
                temporaryData[window.grnEditBatchKey(row)] = [];
                row.find('.row-serial-storage').empty();
                row.find('.unit_cost, .grn_cost, .sale_price, .hire_price').val('0.00');
                row.find('.amount').text('0.00');
                row.find('.grn_cost').trigger('input');
            }
            select.data('previous-product', String(productId));
            refreshGrnProductOptions();
        });

        $(document).on('input', '.unit_cost', function() {
            $(this).closest('tr').find('.grn_cost').val($(this).val()).trigger('input');
        });

        $(document).on('input', '.quantity', function () {
            var row = $(this).closest('tr');
            row.find('.receive_quantity').val($(this).val()).trigger('input');
        });

        $(document).on('input', '.receive_quantity', function() {
            $(this).closest('tr').find('.quantity').val($(this).val());
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();
            var quantity = $(this).val();
            var price = $(el.find('.grn_cost')).val();
            var discount = $(el.find('.discount')).val();

            var totalItemPrice = (quantity * price);
            var amount = totalItemPrice.toFixed(2); // Ensure two decimal places
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
            var quantity = $(el.find('.receive_quantity')).val();
            var discount = $(el.find('.discount')).val();
            var unitCost = $(el.find('.unit_cost')).val();
            var totalItemPrice = (quantity * price);

            if (parseFloat(price) < parseFloat(unitCost)) {
                $(this).removeClass('text-danger');
                $(this).addClass('text-info');
            } else if (parseFloat(price) > parseFloat(unitCost)) {
                $(this).removeClass('text-info');
                $(this).addClass('text-danger');
            } else {
                $(this).removeClass('text-danger');
                $(this).removeClass('text-info');
            }

            var amount = totalItemPrice.toFixed(2); // Ensure two decimal places
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

        function refreshGrnProductOptions() {
            $('#sortable-table select.item option').prop('disabled', false);
        }
        $(document).on('click', '[data-repeater-create]', refreshGrnProductOptions);
        refreshGrnProductOptions();

        let temporaryData = {};
        (value || []).forEach(function (line) {
            temporaryData['line_' + line.id] = (line.product_warranty_serial || []).map(function (unit) {
                return {
                    id: unit.id,
                    serial: unit.serial_number || '',
                    warranty: unit.warranty_number || '',
                    warranty_period: unit.warranty_period,
                    warranty_document: unit.warranty_document,
                    status: unit.status,
                    additional_warranty_description: unit.additional_warranty_description
                };
            }).sort(function (a, b) {
                return (Number(b.status) !== 0 ? 1 : 0) - (Number(a.status) !== 0 ? 1 : 0);
            });
        });
        // Initialize all amounts with two decimal places
        $(document).ready(function() {
            $(".amount").each(function() {
                var value = parseFloat($(this).text()) || 0;
                $(this).text(value.toFixed(2)); // Format on load
            });
        });


        var validateEditWarranty = window.initGrnEditWarranty(temporaryData, @json($warrantyProductTypes));

        // Submit form data including serial and warranty information
        $(document).on('submit', '#grn-edit-form', function(event) {
            if (!validateEditWarranty()) {
                event.preventDefault();
                return false;
            }
            // Prepare the final data structure for serial and warranty numbers
            let serialWarrantyData = {};

            // Merge serial and warranty data under product IDs
            $(this).find('select.item').each(function () {
                var productId = $(this).val();
                var row = $(this).closest('tr');
                var key = window.grnEditBatchKey(row);
                row.find('.batch-key').val(key);
                if (temporaryData[key]) serialWarrantyData[key] = temporaryData[key];
            });

            // Append the final data to a hidden input field
            $(this).find('input[name="serial_warranty_data"]').remove();
            $('<input>').attr({
                type: 'hidden',
                name: 'serial_warranty_data',
                value: JSON.stringify(serialWarrantyData) // Send as JSON string
            }).appendTo(this);
            $(this).find('select.item').prop('disabled', false);
        });

        // Function to format input fields with two decimal places
        function formatToTwoDecimals(input) {
            // Convert the input value to a float and format it to two decimals
            let value = parseFloat(input.value).toFixed(2);

            // If the value is not a number, reset to "0.00"
            if (isNaN(value)) {
                value = "0.00";
            }

            // Update the input field with the formatted value
            input.value = value;
        }

        // Attach event listeners to relevant fields
        document.addEventListener('DOMContentLoaded', function() {
            // Select all input fields that need formatting
            const priceInputs = document.querySelectorAll('.grn_cost, .sale_price, .hire_price');

            // Attach event listeners for blur (when input loses focus)
            priceInputs.forEach(function(input) {
                input.addEventListener('blur', function() {
                    formatToTwoDecimals(input);
                });
            });
        });
    </script>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Edit GRN') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('purchase-order.index') }}">{{ __('Purchase Order') }}</a>
                </div>
                <div class="breadcrumb-item active">{{ __('Edit') }}</div>
            </div>
        </div>
        <div class="section-body">
            {{ Form::open(['route' => ['grn.update', $grns->id], 'id' => 'grn-edit-form', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="grn_id" id="grn_id" value="{{ $grns->id }}">

            <div class="row justify-content-center">
                <input type="hidden" name="total_amount" id="totalAmount">
                <div class="col-12">
                    <div class="bg-white cus_show p-2 br-4 mb-2">
                        <div class="d-flex justify-content-between align-content-center align-items-center px-3">
                            <h4 class="mt-2 ">{{ __('Edit GRN') }}</h3>
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" id="vender-box">
                                                {{ Form::label('vender_id', __('Supplier')) }}<span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    {{ Form::select('vender_id', $venders, $grns->vender_id, ['class' => 'form-control customer-sel font-style selectric ', 'id' => 'vender', 'data-url' => route('purchase-order.vender'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                            <div id="vender_detail" class="d-none">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {{ Form::label('supplier_invoice_number', __('Supplier Invoice Number')) }}<span
                                                    class="text-danger">
                                                    *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('supplier_invoice_number', $grns->supplier_invoice_number, ['class' => 'form-control', 'id' => 'supplier_invoice_number', 'required' => 'required']) }}
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="save-invoice-number">{{ __('Save Invoice Number') }}</button>
                                                    </div>
                                                </div>
                                                <small id="invoice-save-message" role="status"></small>
                                            </div>
                                        </div>
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
                                                    {{ Form::text('order_date', $grns->order_date, ['class' => 'form-control datepicker', 'required' => 'required']) }}
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
                                                        <option @if ((string) $grns->status === '4') selected @endif
                                                            value="4">Issued</option>
                                                        <option @if ((string) $grns->status === '3') selected @endif
                                                            value="3">Approved</option>
                                                        <option @if ((string) $grns->status === '2') selected @endif
                                                            value="2">Not yet approved</option>
                                                        <option @if ((string) $grns->status === '1') selected @endif
                                                            value="1">Cancelled</option>
                                                        <option @if ((string) $grns->status === '0') selected @endif
                                                            value="0">Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('required_by', __('Debit date')) }}<span
                                                    class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('required_by', $grns->required_by, ['class' => 'form-control datepicker', 'required' => 'required']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('grn_number', __('GRN Number')) }}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{-- <input type="text" class="form-control" value="{{$purchase_order_number}}" readonly> --}}
                                                    {{ Form::text('grn_number', $grn_number ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) }}
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
                <div class="card repeater" data-value='{!! json_encode($grns->GrnProducts) !!}'>
                    <div class="card-header">
                        <h4>{{ __('Product & Services') }}</h4>
                        <div class="card-header-action">
                            @if(Auth::user()->canEditGrnCostAndQuantity())
                                <button type="button" data-repeater-create class="btn btn-icon icon-left btn-primary"><i class="fas fa-plus"></i> {{ __('Add Item') }}</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped grn-items-table grn-edit-items" data-repeater-list="items" id="sortable-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product Code') }}</th>
                                        <th>{{ __('Item Name') }}</th>
                                        <th>{{ __('Model') }}</th>
                                        <th>{{ __('Unit Cost') }}<span class="text-danger"> *</span> </th>
                                        <th>{{ __('Quantity') }}<span class="text-danger"> *</span></th>
                                        <th>{{ __('GRN Cost') }} ({{ $currencySymbol }})<span class="text-danger">*</span>
                                        </th>
                                        <th>{{ __('Cash Price') }} ({{ $currencySymbol }})<span
                                                class="text-danger">*</span></th>
                                        <th>{{ __('Selling Price') }} ({{ $currencySymbol }})<span
                                                class="text-danger">*</span></th>
                                        <th>{{ __('Received Quantity') }}<span class="text-danger"> *</span></th>
                                        <th class="text-right">{{ __('Amount') }} ({{ $currencySymbol }})</th>
                                        <th class="">{{ __('Serial & Warranty / Color') }} </th>
                                        <th class="">{{ __('Action') }} </th>
                                    </tr>
                                </thead>
                                <tbody class="ui-sortable" data-repeater-item>
                                    <tr>
                                        {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                        <td width="16%">
                                            <input type="search" class="form-control product-search mb-1" placeholder="{{ __('Search name or barcode') }}" aria-label="{{ __('Search products by name or barcode') }}" autocomplete="off" style="min-width: 12rem;">
                                            <input type="hidden" name="batch_key" class="batch-key">
                                            {{ Form::select('item', $product_services, null, ['class' => 'item d-none', 'data-url' => route('proposal.product'), 'tabindex' => '-1', 'aria-hidden' => 'true']) }}
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item_name" readonly tabindex="-1" style="min-width: 9rem;">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item_model" readonly tabindex="-1" style="min-width: 7rem;">
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput flex-nowrap">
                                                    {{ Form::text('unit_cost', null, ['class' => 'form-control unit_cost', 'required' => 'required', 'placeholder' => __('Unit Cost'), 'readonly' => !Auth::user()->canEditGrnCostAndQuantity(), 'style' => 'min-width: 5rem;']) }}
                                                    {{-- <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput flex-nowrap">
                                                    {{ Form::number('quantity', null, ['class' => 'form-control quantity', 'required' => 'required', 'min' => '1', 'step' => '1', 'placeholder' => __('Qty'), 'readonly' => !Auth::user()->canEditGrnCostAndQuantity(), 'style' => 'min-width: 5rem;']) }}
                                                    {{-- <div class="input-group-append">
                                                    <div class="input-group-text unit">

                                                    </div>
                                                </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput flex-nowrap">
                                                    {{ Form::text('grn_cost', null, ['class' => 'form-control grn_cost', 'required' => 'required', 'placeholder' => __('0.00'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                                    {{-- <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput flex-nowrap">
                                                    {{ Form::text('sale_price', null, ['class' => 'form-control sale_price', 'required' => 'required', 'placeholder' => __('0.00'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                                    {{-- <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput flex-nowrap">
                                                    {{ Form::text('hire_price', null, ['class' => 'form-control hire_price', 'required' => 'required', 'placeholder' => __('0.00'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                                    {{-- <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::number('receive_quantity', null, ['class' => 'form-control receive_quantity', 'required' => 'required', 'min' => '1', 'step' => '1', 'placeholder' => __('Qty'), 'readonly' => !Auth::user()->canEditGrnCostAndQuantity(), 'style' => 'min-width: 5rem;']) }}
                                                    {{-- <div class="input-group-append">
                                                    <div class="input-group-text unit">

                                                    </div>
                                                </div> --}}
                                                </div>
                                            </div>
                                        </td>
                                        {{-- <td class="text-right amount">
                                            0.00
                                        </td>

                                        <td>
                                            @can('delete proposal product')
                                                <a href="#" class="fas fa-trash" data-repeater-delete></a>
                                            @endcan
                                        </td> --}}

                                        <td class="text-right">
                                            <div class="form-group">
                                                <div class="input-group flex-nowrap flex-nowrap">
                                                    <span class="form-control amount">0.00</span>
                                                    {{-- <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}</div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Serial & Warranty Buttons -->
                                        <td class="d-flex flex-nowrap">
                                            <button type="button"
                                                class="btn btn-primary btn-action d-flex align-items-center justify-content-center add-serial-warranty-btn me-1"
                                                data-toggle="tooltip" title="Add Serial & Warranty / Color ">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        </td>

                                        <td>
                                            @can('delete proposal product')
                                                <a href="#" data-repeater-delete
                                                    class="btn btn-danger btn-action deleted  d-flex align-items-center justify-content-center view-serial-warranty-btn"
                                                    data-toggle="tooltip" data-original-title="{{ __('Delete Product') }}">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="7">&nbsp;</td>
                                        <td colspan="2"><strong>{{ __('Sub Total') }}</strong></td>
                                        <td class="text-right subTotal">0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">&nbsp;</td>
                                        <td colspan="2"><strong>{{ __('Total Amount') }}</strong></td>
                                        <td class="text-right totalAmount">0.00</td>
                                        <td></td>
                                    </tr>


                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12 text-right mb-5"><a href="{{ route('grn.index') }}"
                    class="btn btn-secondary">{{ __('Cancel') }}</a>
                {{ Form::submit(__('Update'), ['class' => 'btn btn-primary']) }}
            </div>
            {{ Form::close() }}
        </div>

    </section>
    <!-- Modal for adding Serial and Warranty -->
    <div class="modal fade" id="serialWarrantyModal" tabindex="-1" role="dialog" style="margin-top: 80px;"
        aria-labelledby="serialWarrantyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Serial & Warranty / Color') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="serialWarrantyFields">
                    <!-- Dynamic content will be injected here based on selected product and quantity -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" id="saveSerialWarranty">{{ __('Save') }}</button>

                </div>
            </div>
        </div>
    </div>
    {{-- <script>
        $(document).submit(function() {
            $('body :input').prop("disabled", false);
        });


    </script> --}}


@endsection
