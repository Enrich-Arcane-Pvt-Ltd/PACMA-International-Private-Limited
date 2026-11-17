(function ($) {
    'use strict';

    function getUsedSerials(currentScope) {
        var used = [];
        var currentItem = currentScope ? currentScope.closest('[data-repeater-item]') : null;
        $('#cash_sale_items [data-repeater-item]').each(function () {
            if (currentItem && currentItem.length && this === currentItem[0]) {
                return;
            }
            var row = $(this);
            var s = row.find('.selected-warranty-unit-serial').val() || row.find('select.serial_select').val() || row.find('#selected_serial_number').val();
            if (s) {
                used.push(String(s));
            }
        });
        return used;
    }

    window.SaleWarranty = {
        getUsedSerials: getUsedSerials,
        refreshAll: function () {
            $('#cash_sale_items [data-repeater-item]').each(function () {
                var row = $(this).find('tr.cash-sale-item-row');
                if (!row.length) row = $(this);
                var units = row.data('warranty-units');
                if (units && units.length) {
                    var serial = row.find('select.serial_select');
                    var card = row.find('select.warranty_select');
                    var used = getUsedSerials(row);
                    var currentSerial = row.find('.selected-warranty-unit-serial').val() || serial.val();

                    serial.find('option').each(function () {
                        var val = $(this).val();
                        if (!val) return;
                        var isUsed = used.indexOf(String(val)) !== -1 && String(val) !== String(currentSerial);
                        var matchUnit = units.find(function (u) { return String(u.serial_number) === String(val); });
                        var baseText = val;
                        if (matchUnit) {
                            baseText = matchUnit.serial_number + (matchUnit.purchase_price != null ? ' (Cost: ' + Number(matchUnit.purchase_price).toFixed(2) + ')' : '');
                        }
                        $(this).text(baseText + (isUsed ? ' (Already Selected)' : '')).prop('disabled', isUsed);
                    });

                    card.find('option').each(function () {
                        var val = $(this).val();
                        if (!val) return;
                        var matchUnit = units.find(function (u) { return String(u.warranty_number) === String(val); });
                        var isUsed = matchUnit && used.indexOf(String(matchUnit.serial_number)) !== -1 && String(matchUnit.serial_number) !== String(currentSerial);
                        var baseText = val || 'No card number';
                        $(this).text(baseText + (isUsed ? ' (Already Selected)' : '')).prop('disabled', isUsed);
                    });

                    if ($.fn.selectpicker) {
                        row.find('.selectpicker').selectpicker('refresh');
                    }
                }
            });
        },
        bind: function (scope, units, options) {
            options = options || {};
            scope.data('warranty-units', units || []);
            var serial = scope.find('select.serial_select, select#serial_select').first();
            var card = scope.find('select.warranty_select, select#warranty_select').first();
            serial.off('change').empty();
            card.off('change').empty();
            $('<option>').val('').text('Select Serial Card No').appendTo(serial);
            $('<option>').val('').text('Select Warranty Card No').appendTo(card);

            var usedSerials = getUsedSerials(scope);
            (units || []).forEach(function (unit) {
                var isUsed = usedSerials.indexOf(String(unit.serial_number)) !== -1;
                var baseSerialText = unit.serial_number + (unit.purchase_price != null ? ' (Cost: ' + Number(unit.purchase_price).toFixed(2) + ')' : '');
                var optText = baseSerialText + (isUsed ? ' (Already Selected)' : '');
                var serialOpt = $('<option>').val(unit.serial_number).attr('data-warranty', unit.warranty_number || '').text(optText);
                if (isUsed) {
                    serialOpt.prop('disabled', true);
                }
                serialOpt.appendTo(serial);

                var baseCardText = unit.warranty_number || 'No card number';
                var cardOptText = baseCardText + (isUsed ? ' (Already Selected)' : '');
                var cardOpt = $('<option>').val(unit.warranty_number || '').text(cardOptText);
                if (isUsed) {
                    cardOpt.prop('disabled', true);
                }
                cardOpt.appendTo(card);
            });

            function apply(unit) {
                scope.find('.grn-warranty-document').remove();
                scope.find('#warranty_document_btn').hide().removeAttr('href');
                if (!unit) {
                    serial.prop('selectedIndex', 0);
                    card.prop('selectedIndex', 0);
                    scope.find('#selected_serial_number, [name="product_warranty_serial_id"], .selected-warranty-unit, .selected-warranty-unit-serial').val('');
                    scope.find('[name="warranty_period"], .warranty_period, [name="additional_warranty_description"], .additional_warranty_description').val('');
                    // Deselected serial: unlock quantity max back to available quantity
                    var curAvail = parseFloat(scope.find('.available_quantity, #available_quantity').val()) || 1;
                    scope.find('.quantity, #quantity').attr({min: 1, max: curAvail});
                    SaleWarranty.refreshAll();
                    return;
                }

                // Check for duplicate serial selection across items
                var used = getUsedSerials(scope);
                if (used.indexOf(String(unit.serial_number)) !== -1) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Already Selected',
                            text: 'Serial Card No (' + unit.serial_number + ') is already selected in another item.'
                        });
                    }
                    apply(null);
                    if ($.fn.selectpicker) scope.find('.selectpicker').selectpicker('refresh');
                    SaleWarranty.refreshAll();
                    return;
                }

                var selectedIndex = units.indexOf(unit) + 1;
                serial.prop('selectedIndex', selectedIndex);
                scope.find('#selected_serial_number').val(unit.serial_number);
                card.prop('selectedIndex', selectedIndex);
                var unitId = scope.find('[name="product_warranty_serial_id"], .selected-warranty-unit');
                if (!unitId.length) {
                    unitId = $('<input type="hidden" class="selected-warranty-unit">');
                    var serialName = serial.attr('name') || 'serial_card_no';
                    unitId.attr('name', serialName.replace('serial_card_no', 'product_warranty_serial_id')).insertAfter(serial);
                }
                unitId.val(unit.id);

                var serialHidden = scope.find('.selected-warranty-unit-serial');
                if (!serialHidden.length) {
                    serialHidden = $('<input type="hidden" class="selected-warranty-unit-serial">').insertAfter(unitId);
                }
                serialHidden.val(unit.serial_number);

                if (options.onSelect) options.onSelect(unit);
                if (unit.inventory_id && !options.preservePrices) {
                    scope.find('#inventory_id, .inventory_id').val(unit.inventory_id);
                    scope.find('#purchase_price, .purchase_price').val(unit.purchase_price);
                    scope.find('#available_quantity, .available_quantity').val(unit.remain_qty);
                    scope.find('#unit_price, .unit_price, #original_unit_price, .original_unit_price').val(Number(unit.sale_price).toFixed(2));
                    var hire = scope.find('#gross_hire_price');
                    if (hire.length) {
                        scope.find('#original_gross_hire_price').val(Number(unit.hire_price).toFixed(2));
                        hire.val(Number(unit.hire_price).toFixed(2));
                        scope.find('#discount').val(0);
                        scope.find('#quantity').trigger('input').trigger('change');
                        hire.trigger('input').trigger('change');
                    } else {
                        scope.find('.hire_price').val(Number(unit.hire_price).toFixed(2)).trigger('input');
                        scope.find('.quantity').trigger('input').trigger('change');
                    }
                }
                scope.find('[name="warranty_period"], .warranty_period').val(unit.warranty_period == null ? '' : unit.warranty_period);
                scope.find('[name="additional_warranty_description"], .additional_warranty_description').val(unit.additional_warranty_description || '');
                scope.find('.grn-warranty-document').remove();
                if (unit.warranty_document && unit.warranty_document !== 'assets/img/products/default.png') {
                    var path = String(unit.warranty_document);
                    if (/^assets\/[a-zA-Z0-9_./ -]+$/.test(path) && path.indexOf('..') === -1) {
                        $('<a>', {text: 'View Document', href: '/' + path, target: '_blank', rel: 'noopener', class: 'btn btn-primary btn-sm grn-warranty-document'})
                            .insertAfter(scope.find('[type="file"][name*="warranty_document"]').first());
                    }
                }

                // Strictly lock quantity to 1 for serialized items in both Cash and Hire sales!
                var qtyField = scope.find('.quantity, #quantity');
                qtyField.val(1).attr({min: 1, max: 1});
                qtyField.trigger('input').trigger('change');
                SaleWarranty.refreshAll();
            }

            serial.off('change.grnWarranty').on('change.grnWarranty', function () {
                apply(units[serial.prop('selectedIndex') - 1]);
                if ($.fn.selectpicker) scope.find('.selectpicker').selectpicker('refresh');
                SaleWarranty.refreshAll();
            });
            card.off('change.grnWarranty').on('change.grnWarranty', function () {
                apply(units[card.prop('selectedIndex') - 1]);
                if ($.fn.selectpicker) scope.find('.selectpicker').selectpicker('refresh');
                SaleWarranty.refreshAll();
            });
            apply(null);
            if ($.fn.selectpicker) scope.find('.selectpicker').selectpicker('refresh');
        }
    };
})(jQuery);