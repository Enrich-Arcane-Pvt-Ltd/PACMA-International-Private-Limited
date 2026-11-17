var grnBatchSequence = 0;
window.grnEditBatchKey = function (row) {
    var id = row.find('.id').val();
    if (id) return 'line_' + id;
    var key = row.attr('data-batch-key');
    if (!key) {
        key = 'new_' + (++grnBatchSequence);
        row.attr('data-batch-key', key);
    }
    return key;
};
window.initGrnEditWarranty = function (data, productTypes) {
    'use strict';
    var modal = $('#serialWarrantyModal');
    var container = $('#serialWarrantyFields');
    var activeRow;
    function field(block, label, className, value, type) {
        var group = $('<div class="form-group">').appendTo(block);
        var required = /\s\*$/.test(label);
        var caption = $('<label class="font-weight-bold text-dark mb-1 d-block">')
            .text(required ? label.replace(/\s\*$/, '') : label).appendTo(group);
        if (required) $('<span class="text-danger">').text(' *').appendTo(caption);
        return $('<input>').attr('type', type || 'text').addClass('form-control ' + className).val(value == null ? '' : value).appendTo(group);
    }
    function unitBlock(productId, index, unit, batchKey) {
        var block = $('<div class="unit-serial-block border rounded p-3 mb-3 bg-white shadow-sm">').data('unit', unit);
        var header = $('<div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">').appendTo(block);
        $('<span class="badge badge-primary px-3 py-1 font-weight-bold">').text('Unit #' + (index + 1)).appendTo(header);
        $('<span class="text-muted small font-weight-bold">').text(productTypes[productId] ? 'Furniture Item' : 'Standard Item').appendTo(header);
        var sold = unit.status != null && Number(unit.status) !== 0;
        var serialRow = $('<div class="row">').appendTo(block);
        field($('<div class="col-md-6">').appendTo(serialRow), 'Serial Number / Color *', 'serial-number-input', unit.serial)
            .attr('placeholder', 'Enter Serial Number / Color').prop('readonly', sold);
        field($('<div class="col-md-6">').appendTo(serialRow), 'Warranty Card Number ' + (productTypes[productId] ? '(Optional)' : '*'), 'warranty-number-input', unit.warranty)
            .attr('placeholder', productTypes[productId] ? 'Optional' : 'Enter Warranty Card Number').prop('readonly', sold);
        var documentRow = $('<div class="row">').appendTo(block);
        if (index === 0) {
            var periodColumn = $('<div class="col-md-6">').appendTo(documentRow);
            var period = field(periodColumn, 'Warranty Period', 'warranty-period-input', unit.warranty_period, 'number').attr({min: 0, placeholder: 'e.g. 12'});
            period.wrap('<div class="input-group"></div>');
            $('<div class="input-group-append"><span class="input-group-text font-weight-bold text-muted">Months</span></div>').insertAfter(period);
        }
        var documentColumn = $('<div>').addClass(index === 0 ? 'col-md-6' : 'col-12').appendTo(documentRow);
        if (unit.warranty_document && unit.warranty_document !== 'assets/img/products/default.png') {
            $('<a target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary mb-2">').attr('href', '/' + unit.warranty_document.replace(/^\/+/, '')).text('View Document').appendTo(documentColumn);
        }
        if (!sold) {
            field(documentColumn, 'Warranty Card Photo / Document', 'warranty-file-input', '', 'file')
                .attr({name: 'warranty_documents[' + batchKey + '][' + index + ']', accept: '.jpg,.jpeg,.png,.webp,.pdf,.doc,.docx'});
        } else {
            $('<small class="d-block text-muted">').text('Invoiced unit: serial and document are preserved.').appendTo(documentColumn);
        }
        if (index === 0) field(block, 'Additional Warranty Description / Terms (Optional)', 'warranty-desc-input', unit.additional_warranty_description);
        return block;
    }
    $(document).on('click', '.add-serial-warranty-btn', function () {
        activeRow = $(this).closest('tr');
        var productId = activeRow.find('.item').val();
        var quantity = parseInt(activeRow.find('.receive_quantity').val(), 10) || 0;
        if (!productId || quantity < 1) { AppMessages.warning('Select a product and enter Received Quantity first.'); return; }
        modal.data('product-id', productId);
        var batchKey = window.grnEditBatchKey(activeRow);
        modal.data('batch-key', batchKey);
        container.empty();
        var storage = activeRow.find('.row-serial-storage');
        var blocks = storage.children('.unit-serial-block').detach();
        for (var i = 0; i < quantity; i++) {
            container.append(blocks[i] || unitBlock(productId, i, (data[batchKey] || [])[i] || {}, batchKey));
        }
        modal.find('.modal-title').text('Serial & Warranty / Color - ' + activeRow.find('.item option:selected').text());
        modal.modal('show');
    });
    $('#saveSerialWarranty').on('click', function () {
        var productId = modal.data('product-id');
        var blocks = container.children('.unit-serial-block');
        var first = blocks.first();
        var period = first.find('.warranty-period-input').val();
        var terms = first.find('.warranty-desc-input').val();
        var units = [];
        var valid = true;
        blocks.each(function () {
            var block = $(this);
            var unit = Object.assign({}, block.data('unit') || {});
            unit.serial = block.find('.serial-number-input').val().trim();
            unit.warranty = block.find('.warranty-number-input').val().trim();
            var sold = unit.status != null && Number(unit.status) !== 0;
            if (!sold && (!unit.serial || (!productTypes[productId] && !unit.warranty))) valid = false;
            if (unit.status == null || Number(unit.status) === 0) {
                unit.warranty_period = period === '' ? null : period;
                unit.additional_warranty_description = terms;
            }
            units.push(unit);
        });
        if (!valid) { AppMessages.warning('Serial Number / Color is required for every unit. Warranty Card Number is also required for non-furniture items.'); return; }
        data[modal.data('batch-key')] = units;
        modal.modal('hide');
    });
    modal.on('hidden.bs.modal', function () {
        if (!activeRow) return;
        var storage = activeRow.find('.row-serial-storage');
        if (!storage.length) storage = $('<div class="row-serial-storage d-none">').appendTo(activeRow.find('td:first'));
        storage.empty().append(container.children().detach());
    });
    return function () {
        var missing = null;
        $('#grn-edit-form select.item').each(function () {
            var productId = $(this).val();
            var count = Number($(this).closest('tr').find('.receive_quantity').val());
            var units = data[window.grnEditBatchKey($(this).closest('tr'))] || [];
            var valid = count > 0 && Number.isInteger(count) && units.length === count && units.every(function (unit) {
                if (unit.id && unit.status != null && Number(unit.status) !== 0) return true;
                return String(unit.serial || '').trim() && (productTypes[productId] || String(unit.warranty || '').trim());
            });
            if (!valid && !missing) missing = $(this).find('option:selected').text() || 'Product';
        });
        if (missing) {
            var message = missing + ': Add or update serial and warranty details for every received unit before updating the GRN. Warranty Card Number is optional only for furniture.';
            if (typeof Swal !== 'undefined') Swal.fire({type: 'warning', title: 'Warranty details required', text: message});
            else alert(message);
            return false;
        }
        return true;
    };
};
