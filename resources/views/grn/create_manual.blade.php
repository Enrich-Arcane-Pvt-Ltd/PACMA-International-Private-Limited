@extends('layouts.admin')
@section('page-title')
    {{ __('Create Manual GRN') }}
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/grn-items.css') }}">
@endpush
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

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.3);
    }

    #confirmationMessageContainer .alert {
        background-color: #93e9a7 !important;
        color: #424242 !important;
        border-color: #93e9a7 !important;
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

    .btn-action {
        min-height: auto !important;
    }

    .tab-view ul {
        list-style-type: none;
        display: flex;
        justify-content: space-between;
        padding-left: 0rem;
    }

    .tab-view ul li {
        display: flex;
        align-items: center;
        justify-content: center;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        border-bottom: 1px solid #F97316;
        padding: 0.5rem 1rem;
        width: 33.33%;
        text-align: center;
    }

    .tab-view ul li a {
        text-decoration: none;
        color: #EA580C;
        width: 100%;
    }

    .bg-primary {
        background-color: #F97316 !important;
    }

    .bg-primary a {
        color: #ffffff !important;
    }
</style>

@push('script-page')
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('assets/js/product-search.js') }}"></script>
    <script src="{{ asset('assets/js/grn-invoice-lookup.js') }}" data-lookup-url="{{ route('grn.invoice.lookup') }}"></script>
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
                    calculateTotals();
                },
                hide: function(deleteElement) {
                    AppMessages.confirm('Are you sure you want to delete this element?', () => {
                        $(this).slideUp(deleteElement);
                        $(this).remove();
                        calculateTotals();
                    });
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: false
            });
        }

        let currentProductId;
        let temporaryData = {};
        let activeSerialRow = null;

        function renderUnitBlock(productKey, index, isFurniture, data) {
            data = data || {};
            var serial = data.serial || '';
            var warranty = data.warranty || '';
            var warrantyPeriod = data.warranty_period !== undefined && data.warranty_period !== null ? data.warranty_period : '';
            var warrantyDesc = data.additional_warranty_description || '';
            var docName = data.doc_name || '';

            var warrantyRequiredTag = isFurniture 
                ? '<span class="text-muted font-weight-normal small">({{ __("Optional") }})</span>' 
                : '<span class="text-danger">*</span>';

            return `
                <div class="unit-serial-block border rounded p-3 mb-3 bg-white shadow-sm" data-unit-index="${index}">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.85rem;">
                            <i class="fas fa-barcode mr-1"></i> {{ __('Unit') }} #${index + 1}
                        </span>
                        <span class="text-muted small font-weight-bold">${isFurniture ? '{{ __("Furniture Item") }}' : '{{ __("Standard Item") }}'}</span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="font-weight-bold text-dark mb-1 d-block">
                                {{ __('Serial Number / Color') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control serial-number-input" 
                                name="serial_numbers[${productKey}][]" 
                                value="${serial}" 
                                placeholder="{{ __('Enter Serial Number / Color') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold text-dark mb-1 d-block">
                                {{ __('Warranty Card Number') }} ${warrantyRequiredTag}
                            </label>
                            <input type="text" class="form-control warranty-number-input" 
                                name="warranty_numbers[${productKey}][]" 
                                value="${warranty}" 
                                placeholder="${isFurniture ? '{{ __("Optional") }}' : '{{ __("Enter Warranty Card Number") }}'}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-5 ${index > 0 ? 'd-none' : ''}">
                            <label class="font-weight-bold text-dark mb-1 d-block">
                                {{ __('Warranty Period') }}
                            </label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control warranty-period-input" 
                                    value="${warrantyPeriod}" 
                                    placeholder="e.g. 12">
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold text-muted">{{ __('Months') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="${index > 0 ? 'col-12' : 'col-md-7'}">
                            <label class="font-weight-bold text-dark mb-1 d-block">
                                <i class="fas fa-camera text-primary mr-1"></i> {{ __('Warranty Card Photo / Document') }}
                            </label>
                            <input type="file" class="form-control warranty-file-input" 
                                id="warranty_doc_${productKey}_${index}" 
                                name="warranty_documents[${productKey}][${index}]" 
                                accept="image/*,.pdf,.doc,.docx"
                                style="height: 38px; padding: 5px 10px;">
                            <div class="selected-file-preview small text-success mt-1 font-weight-bold" style="${docName ? '' : 'display:none;'}">
                                ${docName ? '<i class="fas fa-check-circle text-success mr-1"></i> ' + docName : ''}
                            </div>
                        </div>
                    </div>

                    <div class="row ${index > 0 ? 'd-none' : ''}">
                        <div class="col-12">
                            <label class="font-weight-bold text-dark mb-1 d-block">
                                {{ __('Additional Warranty Description / Terms') }} <span class="text-muted font-weight-normal small">({{ __('Optional') }})</span>
                            </label>
                            <input type="text" class="form-control warranty-desc-input" 
                                value="${warrantyDesc}">
                        </div>
                    </div>
                </div>
            `;
        }

        $(document).on('change', '.warranty-file-input', function() {
            var file = this.files[0];
            var block = $(this).closest('.unit-serial-block');
            if (file) {
                block.find('.selected-file-preview').html('<i class="fas fa-check-circle text-success mr-1"></i> ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)').show();
            } else {
                block.find('.selected-file-preview').hide().text('');
            }
        });

        $(document).on('click', '.add-serial-warranty-btn', function() {
            var row = $(this).closest('tr');
            var quantity = parseInt(row.find('.receive_quantity').val()) || 1;
            currentProductId = row.find('.item').val();

            if (!currentProductId) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("Warning") }}',
                    text: '{{ __("Please select a product first.") }}'
                });
                return;
            }

            activeSerialRow = row;
            var productKey = 'product_' + currentProductId;
            var isFurniture = row.attr('data-is-furniture') === '1';
            var productName = row.attr('data-product-name') || row.find('.item option:selected').text();

            $('#serialWarrantyModal').data('product-id', currentProductId);
            $('#serialWarrantyModal').data('product-key', productKey);
            $('#serialWarrantyModal').data('is-furniture', isFurniture);
            $('#serialWarrantyModal').data('product-name', productName);

            var storage = row.find('.row-serial-storage');
            if (storage.length === 0) {
                storage = $('<div class="row-serial-storage d-none"></div>').appendTo(row.find('td:first'));
            }

            var inputContainer = $('#serialWarrantyFields');
            inputContainer.empty();

            var existingBlocks = storage.find('.unit-serial-block');

            if (existingBlocks.length > 0) {
                if (existingBlocks.length === quantity) {
                    inputContainer.append(existingBlocks);
                } else if (existingBlocks.length < quantity) {
                    inputContainer.append(existingBlocks);
                    for (let i = existingBlocks.length; i < quantity; i++) {
                        inputContainer.append(renderUnitBlock(productKey, i, isFurniture, {}));
                    }
                } else {
                    for (let i = 0; i < quantity; i++) {
                        inputContainer.append(existingBlocks.eq(i));
                    }
                }
            } else {
                for (let i = 0; i < quantity; i++) {
                    var prevData = (temporaryData[currentProductId] && temporaryData[currentProductId][i]) ? temporaryData[currentProductId][i] : {};
                    inputContainer.append(renderUnitBlock(productKey, i, isFurniture, prevData));
                }
            }

            $('#serialWarrantyModal').modal('show');
        });

        $(document).on('click', '.view-serial-warranty-btn', function() {
            var row = $(this).closest('tr');
            var productId = row.find('.item').val();
            var productName = row.attr('data-product-name') || row.find('.item option:selected').text();

            var viewContainer = $('#serialWarrantyViewFields');
            viewContainer.empty();

            if (temporaryData[productId] && temporaryData[productId].length > 0) {
                temporaryData[productId].forEach(function(pair, index) {
                    viewContainer.append(`
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <span class="badge badge-info font-weight-bold">{{ __('Unit') }} #${index + 1}</span>
                                <span class="text-muted small">${productName}</span>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">{{ __('Serial Number / Color') }}</small>
                                    <strong>${pair.serial || '{{ __("N/A") }}'}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">{{ __('Warranty Card Number') }}</small>
                                    <strong>${pair.warranty || '{{ __("N/A") }}'}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">{{ __('Warranty Period') }}</small>
                                    <strong>${pair.warranty_period ? pair.warranty_period + ' {{ __("Months") }}' : '{{ __("N/A") }}'}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">{{ __('Warranty Document') }}</small>
                                    <strong>${pair.doc_name ? '<i class="fas fa-file-image text-primary mr-1"></i> ' + pair.doc_name : '{{ __("No Document Uploaded") }}'}</strong>
                                </div>
                                ${pair.additional_warranty_description ? `
                                <div class="col-12 mt-1">
                                    <small class="text-muted d-block">{{ __('Additional Details') }}</small>
                                    <span>${pair.additional_warranty_description}</span>
                                </div>` : ''}
                            </div>
                        </div>
                    `);
                });
            } else {
                viewContainer.append('<p class="text-muted text-center py-3">{{ __("No serial and warranty numbers available yet.") }}</p>');
            }

            $('#serialWarrantyViewModal').modal('show');
        });

        $('#saveSerialWarranty').on('click', function() {
            let modal = $('#serialWarrantyModal');
            let productID = modal.data('product-id');
            let productKey = modal.data('product-key');
            let furnVal = modal.data('is-furniture');
            let isFurniture = furnVal === true || furnVal === 1 || furnVal === '1' || furnVal === 'true';
            let productName = modal.data('product-name') || 'Item';
            let unitBlocks = modal.find('.unit-serial-block');

            // Validate each unit block
            for (let i = 0; i < unitBlocks.length; i++) {
                let $b = $(unitBlocks[i]);
                let serial = $b.find('.serial-number-input').val().trim();
                let warranty = $b.find('.warranty-number-input').val().trim();

                // 1. Serial Number / Color is required for ALL items
                if (!serial) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("Required Field Missing") }}',
                        text: '{{ __("Serial Number / Color is required for") }} ' + productName + ' #' + (i + 1)
                    }).then(() => {
                        $b.find('.serial-number-input').focus();
                    });
                    return;
                }

                // 2. Warranty Number is required for non-furniture items
                if (!isFurniture && !warranty) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("Required Field Missing") }}',
                        text: '{{ __("Warranty Card Number is required for") }} ' + productName + ' #' + (i + 1)
                    }).then(() => {
                        $b.find('.warranty-number-input').focus();
                    });
                    return;
                }
            }

            var sharedWarrantyPeriod = unitBlocks.first().find('.warranty-period-input').val().trim();
            var sharedWarrantyDescription = unitBlocks.first().find('.warranty-desc-input').val().trim();
            temporaryData[productID] = [];
            unitBlocks.each(function(i, el) {
                let $b = $(el);
                let fileInput = $b.find('.warranty-file-input')[0];
                let fileName = (fileInput && fileInput.files && fileInput.files[0]) ? fileInput.files[0].name : ($b.find('.selected-file-preview').text().trim() || '');
                temporaryData[productID].push({
                    serial: $b.find('.serial-number-input').val().trim(),
                    warranty: $b.find('.warranty-number-input').val().trim(),
                    warranty_period: sharedWarrantyPeriod,
                    additional_warranty_description: sharedWarrantyDescription,
                    doc_name: fileName
                });
            });

            if (activeSerialRow && activeSerialRow.length) {
                var storage = activeSerialRow.find('.row-serial-storage');
                if (storage.length === 0) {
                    storage = $('<div class="row-serial-storage d-none"></div>').appendTo(activeSerialRow.find('td:first'));
                }
                storage.empty().append(unitBlocks);
            }

            Swal.fire({
                icon: 'success',
                title: '{{ __("Success") }}',
                text: '{{ __("Serial Numbers & Warranty details saved successfully!") }}',
                timer: 1500,
                showConfirmButton: false
            });

            modal.modal('hide');
        });

        // If modal is closed without clicking Save, preserve existing blocks in row storage
        $('#serialWarrantyModal').on('hide.bs.modal', function() {
            var inputContainer = $('#serialWarrantyFields');
            var remainingBlocks = inputContainer.find('.unit-serial-block');
            if (remainingBlocks.length > 0 && activeSerialRow && activeSerialRow.length) {
                var storage = activeSerialRow.find('.row-serial-storage');
                if (storage.length === 0) {
                    storage = $('<div class="row-serial-storage d-none"></div>').appendTo(activeSerialRow.find('td:first'));
                }
                storage.empty().append(remainingBlocks);
            }
        });

        $(document).on('submit', 'form', function(e) {
            var rows = $('tbody[data-repeater-item] tr');
            for (var r = 0; r < rows.length; r++) {
                var row = $(rows[r]);
                var pId = row.find('.item').val();
                if (!pId) continue;
                var qty = parseInt(row.find('.receive_quantity').val()) || 1;
                var furnAttr = row.attr('data-is-furniture');
                var isFurn = furnAttr === '1' || furnAttr === 'true' || furnAttr === true;
                var pName = row.attr('data-product-name') || row.find('.item option:selected').text();

                var pairs = temporaryData[pId] || [];
                if (pairs.length < qty) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Required Details Missing") }}',
                        text: '{{ __("Please enter Serial & Warranty details for") }} "' + pName + '" ({{ __("Quantity") }}: ' + qty + ') {{ __("before submitting!") }}'
                    });
                    return false;
                }

                for (var k = 0; k < qty; k++) {
                    if (!pairs[k] || !pairs[k].serial || pairs[k].serial.trim() === '') {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Required Details Missing") }}',
                            text: '{{ __("Serial Number / Color is required for") }} "' + pName + '" #' + (k + 1)
                        });
                        return false;
                    }

                    if (!isFurn && (!pairs[k].warranty || pairs[k].warranty.trim() === '')) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Required Details Missing") }}',
                            text: '{{ __("Warranty Card Number is required for") }} "' + pName + '" #' + (k + 1)
                        });
                        return false;
                    }
                }
            }

            let serialWarrantyData = {};
            for (let productId in temporaryData) {
                serialWarrantyData[`product_${productId}`] = temporaryData[productId];
            }

            $('input[name="serial_warranty_data"]').remove();
            $('<input>').attr({
                type: 'hidden',
                name: 'serial_warranty_data',
                value: JSON.stringify(serialWarrantyData)
            }).appendTo('form');
        });

        $(document).on('change', '#vender', function() {
            $('#vender_detail').removeClass('d-none');
            $('#vender_detail').addClass('d-block');
            $('#vender-box').removeClass('d-block');
            $('#vender-box').addClass('d-none');
            var id = $(this).val();
            var url = $(this).data('url');
            if (id) {
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
                    }
                });
            }
        });

        $(document).on('click', '#remove', function() {
            $('#vender-box').removeClass('d-none');
            $('#vender-box').addClass('d-block');
            $('#vender_detail').removeClass('d-block');
            $('#vender_detail').addClass('d-none');
        });

        function changeItem(element) {
            var iteams_id = element.val();
            var el = element.closest('tr');

            if (!iteams_id) return;

            $.ajax({
                url: '{{ route('proposal.product') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'product_id': iteams_id
                },
                cache: false,
                success: function(data) {
                    var item = (typeof data === 'string') ? JSON.parse(data) : data;
                    var productObj = item.product || item;
                    var catName = item.category_name || (productObj.category_details && productObj.category_details.name) || (productObj.category && productObj.category.name) || (productObj.product_category && productObj.product_category.name) || '';
                    var isFurn = /furniture/i.test(catName) ? '1' : '0';
                    el.attr('data-is-furniture', isFurn);
                    el.attr('data-product-name', productObj.name || item.name || '');

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
                        success: function(lastPriceData) {
                            var unitCost = 0;
                            if (lastPriceData && lastPriceData.last_purchase_price) {
                                unitCost = parseFloat(lastPriceData.last_purchase_price);
                            } else if (item && item.purchase_price) {
                                unitCost = parseFloat(item.purchase_price);
                            }

                            el.find('.unit_cost').val(unitCost.toFixed(2));
                            el.find('.grn_cost').val(unitCost.toFixed(2));
                            var cashPrice = parseFloat(item.sale_price || 0);
                            var retailPrice = cashPrice > 0 ? Math.round(cashPrice * 1.38) : 0;
                            el.find('.sale_price').val(cashPrice.toFixed(2));
                            el.find('.hire_price').val(retailPrice.toFixed(2));

                            var qty = parseFloat(el.find('.receive_quantity').val()) || 1;
                            el.find('.receive_quantity').val(qty);

                            var amount = Math.round(qty * unitCost);
                            el.find('.amount').html(amount.toFixed(2));

                            calculateTotals();
                        }
                    });
                }
            });
        }

        $(document).on('change', '.item', function() {
            changeItem($(this));
        });

        // Unit Cost typed by user -> copy into GRN Cost and recalculate amount
        $(document).on('input', '.unit_cost', function() {
            var el = $(this).closest('tr');
            el.find('.grn_cost').val($(this).val()).trigger('input');
        });

        // Cash Price typed by user -> auto calculate Retail Price (Cash Price + 38%), rounded to nearest integer
        $(document).on('input', '.sale_price', function() {
            var el = $(this).closest('tr');
            var cash = parseFloat($(this).val()) || 0;
            var retail = cash > 0 ? Math.round(cash * 1.38) : 0;
            el.find('.hire_price').val(retail.toFixed(2));
        });

        $(document).on('input', '.receive_quantity, .grn_cost', function() {
            var el = $(this).closest('tr');
            var quantity = parseFloat(el.find('.receive_quantity').val()) || 0;
            var price = parseFloat(el.find('.grn_cost').val()) || 0;
            var unitCost = parseFloat(el.find('.unit_cost').val()) || 0;

            if (price < unitCost) {
                el.find('.grn_cost').removeClass('text-danger').addClass('text-info');
            } else if (price == unitCost) {
                el.find('.grn_cost').removeClass('text-info text-danger');
            } else {
                el.find('.grn_cost').removeClass('text-info').addClass('text-danger');
            }

            var amount = Math.round(quantity * price).toFixed(2);
            el.find('.amount').html(amount);

            calculateTotals();
        });

        function calculateTotals() {
            var subTotal = 0;
            $(".amount").each(function() {
                subTotal += parseFloat($(this).text()) || 0;
            });
            var roundedSubTotal = Math.round(subTotal);
            $('.subTotal').html(roundedSubTotal.toFixed(2));
            $('.totalAmount').html(roundedSubTotal.toFixed(2));
            $('#totalAmount').val(roundedSubTotal.toFixed(2));
        }

        $(document).ready(function() {
            $('.se-pre-con').fadeOut('slow');
            calculateTotals();
        });
    </script>
@endpush

@section('content')
    <section class="section">
        <div class="se-pre-con"></div>

        <div class="section-header">
            <h1>{{ __('Create Manual GRN') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('grn.index') }}">{{ __('GRN') }}</a></div>
                <div class="breadcrumb-item active">{{ __('Create Manual GRN') }}</div>
            </div>
        </div>

        <div class="section-body">
            {{ Form::open(['route' => 'grn.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="total_amount" id="totalAmount">

            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card br-4 cus_show">
                        <div class="card-header">
                            <div class="w-100 tab-view">
                                <ul>
                                    <li>
                                        <a href="{{ route('purchase-orders.create-grn') }}">
                                            {{ __('Create GRN From Purchase Orders') }}
                                        </a>
                                    </li>
                                    <li class="bg-primary">
                                        <a href="{{ route('grn.manual.create') }}">
                                            {{ __('Create Manual GRN') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('grn.index') }}">
                                            {{ __('Manage GRN') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="confirmationMessageContainer"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" id="vender-box">
                                                {{ Form::label('vender_id', __('Supplier')) }}<span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    {{ Form::select('vender_id', $venders, null, ['class' => 'form-control customer-sel font-style selectric', 'id' => 'vender', 'data-url' => route('purchase-order.vender'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                            <div id="vender_detail" class="d-none"></div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {{ Form::label('supplier_invoice_number', __('Supplier Invoice Number')) }}
                                                <span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('supplier_invoice_number', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Supplier Invoice Number')]) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('order_date', __('Received Date')) }}<span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('order_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required' => 'required']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('status', __('Status')) }}<span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <select name="status" id="status" class="form-control customer-sel font-style selectric" required>
                                                        <option value="3" selected>{{ __('Approved') }}</option>
                                                        <option value="2">{{ __('Not yet approved') }}</option>
                                                        <option value="1">{{ __('Cancelled') }}</option>
                                                        <option value="0">{{ __('Rejected') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('required_by', __('Debit Date (Due Date)')) }}<span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('required_by', date('Y-m-d'), ['class' => 'form-control datepicker', 'required' => 'required']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('po_ref', __('Purchase Order')) }}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-tag"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{ __('Manual / Direct GRN') }}" readonly>
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
                    <div class="card repeater">
                        <div class="card-header d-flex justify-content-between">
                            <h4>{{ __('Products & Services') }}</h4>
                            <div class="card-header-action">
                                <a href="javascript:void(0)" data-repeater-create="" class="btn btn-icon icon-left btn-primary">
                                    <i class="fas fa-plus"></i> {{ __('Add Item') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped grn-items-table" data-repeater-list="items" id="sortable-table">
                                    <thead>
                                        <tr>
                                            <th width="25%">{{ __('Product') }} <span class="text-danger">*</span></th>
                                            <th class="text-center" width="10%">{{ __('Unit Cost') }} ({{ $currencySymbol }}) <span class="text-danger">*</span></th>
                                            <th class="text-center" width="10%">{{ __('Received Qty') }} <span class="text-danger">*</span></th>
                                            <th class="text-center" width="10%">{{ __('GRN Cost') }} ({{ $currencySymbol }}) <span class="text-danger">*</span></th>
                                            <th class="text-center" width="10%">{{ __('Cash Price') }} ({{ $currencySymbol }}) <span class="text-danger">*</span></th>
                                            <th class="text-center" width="10%">{{ __('Retail Price') }} ({{ $currencySymbol }}) <span class="text-danger">*</span></th>
                                            <th class="text-center" width="12%">{{ __('Amount') }} ({{ $currencySymbol }})</th>
                                            <th width="8%">{{ __('Serial & Warranty') }}</th>
                                            <th width="5%">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ui-sortable" data-repeater-item>
                                        <tr>
                                            <td>
                                                <input type="search" class="form-control product-search mb-1" placeholder="{{ __('Search name or barcode') }}" aria-label="{{ __('Search products by name or barcode') }}" autocomplete="off">
                                                {{ Form::select('item', $product_services, null, ['class' => 'item d-none', 'tabindex' => '-1', 'aria-hidden' => 'true']) }}
                                                <div class="row-serial-storage d-none"></div>
                                            </td>
                                            <td>
                                                {{ Form::text('unit_cost', '0.00', ['class' => 'text-center form-control unit_cost', 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                            </td>
                                            <td>
                                                {{ Form::number('receive_quantity', '1', ['class' => 'text-center form-control receive_quantity', 'required' => 'required', 'min' => '1', 'style' => 'min-width: 5rem;']) }}
                                            </td>
                                            <td>
                                                {{ Form::text('grn_cost', '0.00', ['class' => 'text-right form-control grn_cost', 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                            </td>
                                            <td>
                                                {{ Form::text('sale_price', '0.00', ['class' => 'text-right form-control sale_price', 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                            </td>
                                            <td>
                                                {{ Form::text('hire_price', '0.00', ['class' => 'text-right form-control hire_price', 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
                                            </td>
                                            <td>
                                                <div class="text-right form-control amount" style="min-width: 5rem;">0.00</div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center flex-nowrap" style="gap: 6px;">
                                                    <a href="javascript:void(0)" class="btn btn-primary btn-action add-serial-warranty-btn" title="{{ __('Add Serial & Warranty') }}">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="btn btn-info btn-action view-serial-warranty-btn" title="{{ __('View Serial & Warranty') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" data-repeater-delete="" class="btn btn-danger btn-action">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td class="text-right"><b>{{ __('Total Amount') }} ({{ $currencySymbol }}):</b></td>
                                            <td class="text-right">
                                                <span class="totalAmount font-weight-bold">0.00</span>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-right mb-4">
                    <a href="{{ route('grn.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
                    <input type="submit" value="{{ __('Create GRN') }}" class="btn btn-primary">
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </section>

    <!-- Modal for adding Serial & Warranty -->
    <div class="modal fade" id="serialWarrantyModal" tabindex="-1" role="dialog" aria-labelledby="serialWarrantyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serialWarrantyModalLabel">{{ __('Add Serial & Warranty Details') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="serialWarrantyFields"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" id="saveSerialWarranty">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing Serial & Warranty -->
    <div class="modal fade" id="serialWarrantyViewModal" tabindex="-1" role="dialog" aria-labelledby="serialWarrantyViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serialWarrantyViewModalLabel">{{ __('View Serial & Warranty Details') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="serialWarrantyViewFields"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
