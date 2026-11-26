@extends('layouts.admin')
@section('page-title')
    {{ __('GRN') }}
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/grn-items.css?v=2') }}">
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

    .btn-action {
        min-height: auto !important:
    }
</style>
@push('script-page')
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('assets/js/product-search.js') }}"></script>
    <script src="{{ asset('assets/js/grn-invoice-lookup.js') }}" data-lookup-url="{{ route('grn.invoice.lookup') }}"></script>
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
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').closest('tr');
                    tr.find('.item').val(value[i].product_id);
                    if (value[i].is_furniture !== undefined) {
                        tr.attr('data-is-furniture', value[i].is_furniture ? '1' : '0');
                    }
                    if (value[i].product_name) {
                        tr.attr('data-product-name', value[i].product_name);
                        tr.find('.item_name').val(value[i].product_name);
                    }
                    if (value[i].product_model) {
                        tr.find('.item_model').val(value[i].product_model);
                    }
                    changeItem(tr.find('.item'));
                }
            } else {
                $('.se-pre-con').fadeOut('slow');
            }
        } else {
            $('.se-pre-con').fadeOut('slow');
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
            var quantity = parseInt(row.find('.receive_quantity').val()) || 0;
            currentProductId = row.find('.id').val() || row.find('.item').val();

            if (quantity <= 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Received Quantity Required',
                        text: 'Please enter a valid Received Quantity before adding serial / warranty numbers.'
                    });
                } else {
                    AppMessages.warning('Please enter a valid Received Quantity before adding serial / warranty numbers.');
                }
                return false;
            }

            activeSerialRow = row;
            var productKey = 'product_' + currentProductId;
            var isFurniture = row.attr('data-is-furniture') === '1';
            var productName = row.attr('data-product-name') || row.find('.item option:selected').text() || 'Product';

            $('#serialWarrantyModal').data('product-id', currentProductId);
            $('#serialWarrantyModal').data('product-key', productKey);
            $('#serialWarrantyModal').data('is-furniture', isFurniture ? '1' : '0');
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
            var productId = row.find('.id').val() || row.find('.item').val();
            var productName = row.attr('data-product-name') || row.find('.item option:selected').text() || 'Product';

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

        // Handle Save button in the modal
        $('#saveSerialWarranty').on('click', function() {
            let modal = $('#serialWarrantyModal');
            let productID = modal.data('product-id');
            let productKey = modal.data('product-key');
            let furnVal = modal.data('is-furniture');
            let isFurniture = furnVal === '1' || furnVal === 1 || furnVal === true || furnVal === 'true';
            let productName = modal.data('product-name') || 'This product';
            let unitBlocks = modal.find('.unit-serial-block');

            // Validate each unit block
            for (let i = 0; i < unitBlocks.length; i++) {
                let $b = $(unitBlocks[i]);
                let serial = $b.find('.serial-number-input').val().trim();
                let warranty = $b.find('.warranty-number-input').val().trim();

                // 1. Serial Number / Color is required for ALL items
                if (!serial) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Serial Number / Color Required!") }}',
                            html: `<b>${productName}</b>: {{ __("Please enter Serial Number / Color for item") }} #${i + 1}.`
                        }).then(() => {
                            $b.find('.serial-number-input').focus();
                        });
                    } else {
                        AppMessages.warning(`${productName}: Please enter Serial Number / Color for item #${i + 1}.`);
                    }
                    return false;
                }

                // 2. Warranty Number is required for non-furniture items
                if (!isFurniture && !warranty) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Warranty Card Required!") }}',
                            html: `<b>${productName}</b>: {{ __("Warranty Number is required for item") }} #${i + 1}.`
                        }).then(() => {
                            $b.find('.warranty-number-input').focus();
                        });
                    } else {
                        AppMessages.warning(`${productName}: Warranty Number is required for item #${i + 1}.`);
                    }
                    return false;
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

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: isFurniture 
                        ? 'Serial Numbers / Color saved successfully (Warranty optional for Furniture).'
                        : 'Serial Numbers & Warranty Cards saved successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }

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
            var rows = $('#sortable-table tbody tr');
            var validationError = null;

            rows.each(function(index, elem) {
                if (validationError) return;
                var row = $(elem);
                var qty = parseInt(row.find('.receive_quantity').val()) || 0;
                if (qty <= 0) return;

                var pId = row.find('.id').val() || row.find('.item').val();
                var pName = row.attr('data-product-name') || row.find('.item option:selected').text() || ('Row #' + (index + 1));
                var furnAttr = row.attr('data-is-furniture');
                var isFurn = furnAttr === '1' || furnAttr === 'true' || furnAttr === true;
                var data = temporaryData[pId] || [];

                if (data.length < qty) {
                    validationError = `Please add Serial & Warranty details for <b>${pName}</b> (Required: ${qty}, Added: ${data.length}).`;
                    return;
                }

                for (var i = 0; i < qty; i++) {
                    var s = (data[i] && data[i].serial) ? data[i].serial.trim() : '';
                    var w = (data[i] && data[i].warranty) ? data[i].warranty.trim() : '';

                    if (!s) {
                        validationError = `Serial Number / Color is required for <b>${pName}</b> (item #${i + 1}).`;
                        return;
                    }
                    if (!isFurn && !w) {
                        validationError = `Warranty Card is required for <b>${pName}</b> (item #${i + 1}). Non-furniture items require a Warranty Card.`;
                        return;
                    }
                }
            });

            if (validationError) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Required Information',
                        html: validationError
                    });
                } else {
                    AppMessages.warning(validationError.replace(/<[^>]+>/g, ''));
                }
                return false;
            }

            // Prepare the final data structure for serial and warranty numbers
            let serialWarrantyData = {};
            for (let productId in temporaryData) {
                serialWarrantyData[`product_${productId}`] = temporaryData[productId];
            }

            // Remove previous hidden inputs if any to avoid duplication
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
            var tr = element.closest('tr');
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
                    var productObj = item.product || item;
                    var catName = item.category_name || (productObj.category_details && productObj.category_details.name) || (productObj.category && productObj.category.name) || (productObj.product_category && productObj.product_category.name) || '';
                    var isFurn = /furniture/i.test(catName) ? '1' : '0';
                    tr.attr('data-is-furniture', isFurn);
                    if (productObj.name || item.name) {
                        tr.attr('data-product-name', productObj.name || item.name || '');
                    }
                    tr.find('.item_name').val(item.product_name || productObj.name || item.name || '');
                    tr.find('.item_model').val(item.product_model || (productObj.brands && productObj.brands.name) || '');
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
                                // var amount = (0.00);
                                var unit_cost = parseFloat(billItems.unit_cost ?? 0);
                                var quantity = billItems.quantity ?? 0;
                                var amount = Math.round(quantity * unit_cost);

                                // $(el.parent().parent().find('.unit_cost')).val(billItems.unit_cost);

                            } else {
                                $(el.parent().parent().find('.receive_quantity')).val(
                                    1);
                                // $(el.parent().parent().find('.unit_cost')).val(item.sale_price);

                            }




                            if (billItems != null) {
                                $(el.parent().parent().find('.amount')).html(amount.toFixed(2));
                            } else {
                                $(el.parent().parent().find('.amount')).html(item
                                    .totalAmount);
                            }

                            var inputs = $(".amount");
                            var subTotal = 0;
                            for (var i = 0; i < inputs.length; i++) {
                                subTotal = parseFloat(subTotal) + parseFloat($(inputs[
                                        i])
                                    .html());
                            }
                            $('.subTotal').html(Math.round(subTotal).toFixed(2));



                            var totalItemPrice = 0;
                            var priceInput = $('.price');
                            for (var j = 0; j < priceInput.length; j++) {
                                totalItemPrice += parseFloat(priceInput[j].value);
                            }

                            var totalItemTaxPrice = 0;
                            var itemTaxPriceInput = $('.itemTaxPrice');
                            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j]
                                    .value);
                            }

                            $('.totalAmount').html(Math.round(subTotal).toFixed(2));
                            $('#totalAmount').val(Math.round(subTotal).toFixed(2));

                        },
                        complete: function() {
                            $('.se-pre-con').fadeOut('slow');
                        },
                        error: function() {
                            $('.se-pre-con').fadeOut('slow');
                        }
                    });
                },
                error: function() {
                    $('.se-pre-con').fadeOut('slow');
                }
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

            var totalItemPrice = Math.round(quantity * price);
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
            $('.subTotal').html(Math.round(subTotal).toFixed(2));
            // $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html(Math.round(parseFloat(subTotal) + parseFloat(totalItemTaxPrice)).toFixed(2));
            $('#totalAmount').val(Math.round(parseFloat(subTotal) + parseFloat(totalItemTaxPrice)).toFixed(2));

        })


        $(document).on('input', '.grn_cost', function() {

            var el = $(this).parent().parent().parent().parent();
            var price = $(this).val();
            var unitCost = $(el.find('.unit_cost')).val();
            var quantity = $(el.find('.receive_quantity')).val();
            var discount = $(el.find('.discount')).val();
            var totalItemPrice = Math.round(quantity * price);

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

            $('.subTotal').html(Math.round(subTotal).toFixed(2));
            $('.totalAmount').html(Math.round(parseFloat(subTotal) + parseFloat(totalItemTaxPrice)).toFixed(2));
            $('#totalAmount').val(Math.round(parseFloat(subTotal) + parseFloat(totalItemTaxPrice)).toFixed(2));

        })

        // Selling Price is entered manually without auto-calculation


        $(document).on('click', '[data-repeater-create]', function() {
            $('.item :selected').each(function() {
                var id = $(this).val();
                $(".item option[value=" + id + "]").prop("disabled", true);
            });
        })

        // Initialize all amounts with two decimal places
        $(document).ready(function() {
            setTimeout(function() {
                $('.se-pre-con').fadeOut('slow');
            }, 1000);

            $(".amount").each(function() {
                var value = parseFloat($(this).text()) || 0;
                $(this).text(value.toFixed(2)); // Format on load
            });
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
        <div class="se-pre-con"></div>

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
            {{ Form::open(['url' => 'grn-order', 'enctype' => 'multipart/form-data']) }}
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" id="vender-box">
                                                {{ Form::label('vender_id', __('Supplier')) }}<span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    {{ Form::select('vender_id', $venders, $purchorders->vender_id, ['class' => 'form-control customer-sel font-style selectric ', 'id' => 'vender', 'data-url' => route('purchase-order.vender'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                            <div id="vender_detail" class="d-none"></div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="hidden" name="supplier_invoice_number">
                                                {{ Form::label('supplier_invoice_number', __('Supplier Invoice Number')) }}
                                                <span class="text-danger"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </div>
                                                    </div>
                                                    {{ Form::text('supplier_invoice_number', $supplier_invoice_number ?? '', ['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                        </div>
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
                                                {{ Form::label('status', __('Status')) }}<span class="text-danger"> *</span>
                                                <div class="input-group">
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
                                                {{ Form::label('required_by', __('Debit Date ')) }}<span
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
                            <input type="search" class="form-control grn-order-product-search" placeholder="{{ __('Search name or barcode') }}" aria-label="{{ __('Search purchase order products') }}" autocomplete="off">
                            {{-- <a href="#" data-repeater-create="" class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#add-bank">
                                <i class="fas fa-plus"></i>{{__('Add Item')}}
                            </a> --}}
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped grn-items-table" data-repeater-list="items"
                                id="sortable-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product Code') }}</th>
                                        <th>{{ __('Item Name') }}</th>
                                        <th>{{ __('Model') }}</th>
                                        <th class="text-center">{{ __('Unit Cost') }} ({{ $currencySymbol }})<span
                                                class="text-danger"> *</span></th>
                                        {{-- <th>{{ __('Quantity') }}<span class="text-danger"> *</span></th> --}}
                                        <th style="width: 10%">{{ __('Received Quantity') }}<span class="text-danger">
                                                *</span></th>
                                        <th class="text-center">{{ __('GRN Cost') }} ({{ $currencySymbol }})<span
                                                class="text-danger"> *</span>
                                        </th>
                                        <th class="text-center">{{ __('Cash Price') }} ({{ $currencySymbol }})<span
                                                class="text-danger"> *</span>
                                        </th>
                                        <th class="text-center">{{ __('Selling Price') }} ({{ $currencySymbol }})<span
                                                class="text-danger">
                                                *</span> </th>
                                        <th class="text-center">{{ __('Amount') }} ({{ $currencySymbol }})</th>
                                        <th class="">{{ __('Serial & Warranty / Color') }} </th>
                                        <th>{{ __('Action') }}</th>

                                    </tr>
                                </thead>
                                <tbody class="ui-sortable" data-repeater-item>
                                    <tr>


                                        {{ Form::hidden('id', $item->id ?? null, ['class' => 'form-control id']) }}
                                        {{ Form::hidden('quantity', $item->quantity ?? null, ['class' => 'd-none']) }}
                                        {{-- @dd($item ?? '') --}}
                                        <td width="15%">
                                            {{ Form::select('item', $product_services, null, ['class' => 'form-control font-style item', 'data-url' => route('proposal.product'), 'readonly' => 'readonly', 'disabled' => 'disabled', 'style' => 'min-width: 9rem;']) }}
                                            <div class="row-serial-storage d-none"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item_name" readonly tabindex="-1" style="min-width: 10rem;">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item_model" readonly tabindex="-1" style="min-width: 8rem;">
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput flex-nowrap">
                                                    {{ Form::text('unit_cost', $item->unit_cost ?? null, ['class' => 'text-center form-control unit_cost', 'required' => 'required', 'placeholder' => __('Unit Cost'), 'required' => 'required', 'readonly' => 'readonly', 'style' => 'min-width: 5rem;']) }}
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
                                                    {{ Form::text('quantity', $item->quantity ?? null, ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required', 'readonly' => 'readonly', 'style' => 'min-width: 5rem;']) }}
                                                    <div class="input-group-append">
                                                    <div class="input-group-text unit">
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </td> --}}

                                        <td>
                                            <div class="form-group">
                                                <div class="input-group colorpickerinput">
                                                    {{ Form::text('receive_quantity', $item->receive_quantity ?? null, ['class' => 'text-center form-control receive_quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
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
                                                    {{ Form::text('grn_cost', $item->grn_cost ?? null, ['class' => 'text-right form-control grn_cost', 'required' => 'required', 'placeholder' => __('0.00'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
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
                                                    {{ Form::text('sale_price', $item->sale_price ?? null, ['class' => 'text-right form-control sale_price', 'required' => 'required', 'placeholder' => __('0.00'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
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
                                                    {{ Form::text('hire_price', $item->hire_price ?? null, ['class' => 'text-right form-control hire_price', 'required' => 'required', 'placeholder' => __('0.00'), 'required' => 'required', 'style' => 'min-width: 5rem;']) }}
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
                                                    <div class="text-right form-control amount" placeholder='0.00'
                                                        style ="min-width: 5rem;"> </div>
                                                    {{-- <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            {{ \Auth::user()->currencySymbol() }}
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </td>

                                        {{-- <td class="text-right amount"> </td> --}}

                                        <td>
                                            @if (isset($product))
                                                <div class="d-flex align-items-center justify-content-center flex-nowrap" style="gap: 6px;">
                                                    <a data-toggle="modal" data-target="#serialWarrantyModal"
                                                        class="btn btn-primary btn-action add-serial-warranty-btn"
                                                        data-toggle="tooltip" title="Add Serial & Warranty / Color ">
                                                        <i class="fas fa-plus"></i>
                                                    </a>

                                                    <a data-toggle="modal" data-target="#serialWarrantyViewModal"
                                                        class="btn btn-info btn-action view-serial-warranty-btn"
                                                        data-toggle="tooltip" title="View Serial & Warranty / Color">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>

                                                {{-- <button type="button"
                                                    class="btn btn-primary btn-sm add-serial-warranty-btn"
                                                    data-toggle="modal" data-target="#serialWarrantyModal"
                                                    style="margin-bottom: 5px;">
                                                    {{ __('Add Serial & Warranty / Color ') }}
                                                </button> --}}
                                            @else
                                                <p>Product information is not available.</p>
                                            @endif

                                            <!-- Hidden inputs for serial and warranty numbers -->
                                            <div id="hiddenSerialWarrantyContainer_{{ $product->id }}"></div>
                                        </td>
                                        <td>
                                            @can('delete proposal product')
                                                {{-- <a href="#" class="fas fa-trash" data-repeater-delete></a> --}}
                                                <a href="#" data-repeater-delete
                                                    class="btn btn-danger btn-action deleted d-flex align-items-center justify-content-center"
                                                    data-toggle="tooltip" title="Delete Product">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                </tbody>
                                <div id="confirmationMessageContainer"></div>
                                <!-- Modal -->
                                <div class="modal fade" id="serialWarrantyModal" tabindex="-1" role="dialog"
                                    style="margin-top: 80px; " aria-labelledby="serialWarrantyModalLabel"
                                    aria-hidden="true" data-backdrop="false">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('Add Serial & Warranty / Color Details') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="serialWarrantyFields">

                                                <!-- Dynamic fields for both serial and warranty numbers will be inserted here -->
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

                                <!-- Serial & Warranty View Modal -->
                                <div class="modal fade" id="serialWarrantyViewModal" tabindex="-1" role="dialog"
                                    style="margin-top: 80px;" aria-labelledby="serialWarrantyViewModalLabel"
                                    aria-hidden="true" data-backdrop="false">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('View Serial & Warranty Details') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="serialWarrantyViewFields">
                                                <!-- Dynamic content will be injected here based on selected product -->
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('Close') }}</button>
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
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td colspan="2"><strong>{{ __('Sub Total') }}</strong></td>
                                        <td class="text-right subTotal">0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
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
    </script>
@endsection
