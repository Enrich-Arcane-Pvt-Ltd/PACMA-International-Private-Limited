(function ($) {
    'use strict';
    window.SaleBatches = {
        install: function (config) {
            var selector = config.cash ? 'select.product' : 'select#product';
            function money(value) { return Number(value || 0).toFixed(2); }

            function getOtherRowsDeductions(targetScope, targetProductId) {
                var allocations = { totalOthers: 0, byBatch: {} };
                if (!config.cash) return allocations;

                var currentItem = targetScope.closest('[data-repeater-item]');
                $('#cash_sale_items [data-repeater-item]').each(function () {
                    if (currentItem.length && this === currentItem[0]) {
                        return;
                    }
                    var other = $(this);
                    var otherProd = other.find('select.product').val() || other.find('.inventory_id').val();
                    if (otherProd && String(otherProd) === String(targetProductId)) {
                        var q = parseFloat(other.find('.quantity').val()) || 0;
                        allocations.totalOthers += q;
                        var otherBatch = other.find('.sale-batch-select').val() || other.find('.inventory_id').val() || other.find('.batch_id').val();
                        if (otherBatch) {
                            allocations.byBatch[String(otherBatch)] = (allocations.byBatch[String(otherBatch)] || 0) + q;
                        }
                    }
                });
                return allocations;
            }

            function syncBatchOptions(rowScope, preferredBatchId) {
                var batchData = rowScope.data('sale-batches-data');
                if (!batchData) return;
                var rawBatches = batchData.batches || [];
                var prodId = rowScope.data('sale-batches-product-id');
                var deductions = getOtherRowsDeductions(rowScope, prodId);

                var rawTotal = rawBatches.reduce(function (sum, b) { return sum + Number(b.available_qty || 0); }, 0);
                var netTotal = Math.max(0, rawTotal - deductions.totalOthers);

                var select = rowScope.find('.sale-batch-select');
                var curVal = preferredBatchId !== undefined ? preferredBatchId : select.val();

                if (rawBatches.length > 1) {
                    select.empty();
                    $('<option>').val('').text('Select stock batch (Total Available: ' + netTotal + ')').appendTo(select);
                    rawBatches.forEach(function (b) {
                        var takenFromThis = deductions.byBatch[String(b.id)] || 0;
                        var availInThis = Math.max(0, Number(b.available_qty || 0) - takenFromThis);
                        var effectiveAvail = Math.min(availInThis, netTotal);

                        var returnPrefix = b.return_label ? '[' + b.return_label + '] ' : '';
                        var optText = returnPrefix + b.code + ' | Invoice ' + (b.invoice_number || '-') + ' | ' + b.date +
                            ' | Cost ' + money(b.purchase_price) + ' | Cash ' + money(b.sale_price) +
                            (b.hire_price ? ' | Retail ' + money(b.hire_price) : '') +
                            ' | Available ' + effectiveAvail + (effectiveAvail <= 0 ? ' (Out of stock)' : '');

                        $('<option>').val(b.id).text(optText).appendTo(select);
                    });
                    select.val(curVal || '');
                }

                var itemEl = config.cash ? rowScope.closest('[data-repeater-item]') : null;
                function setRaw(qty) {
                    rowScope.data('raw_remain_qty', qty);
                    if (itemEl && itemEl.length) itemEl.data('raw_remain_qty', qty);
                }

                var currentBatch = rawBatches.find(function (b) { return String(b.id) === String(curVal); });
                if (currentBatch) {
                    var takenFromCur = deductions.byBatch[String(currentBatch.id)] || 0;
                    var availInCur = Math.max(0, Number(currentBatch.available_qty || 0) - takenFromCur);
                    var effectiveCurAvail = Math.min(availInCur, netTotal);
                    rowScope.find('.available_quantity, #available_quantity').val(effectiveCurAvail);
                    setRaw(currentBatch.available_qty);
                    rowScope.find('.quantity, #quantity').attr('max', effectiveCurAvail);
                } else if (rawBatches.length === 1) {
                    var sole = rawBatches[0];
                    var takenSole = deductions.byBatch[String(sole.id)] || deductions.totalOthers;
                    var availSole = Math.max(0, Number(sole.available_qty || 0) - takenSole);
                    rowScope.find('.available_quantity, #available_quantity').val(availSole);
                    setRaw(sole.available_qty);
                    rowScope.find('.quantity, #quantity').attr('max', availSole);
                } else {
                    rowScope.find('.available_quantity, #available_quantity').val(netTotal);
                    setRaw(rawTotal);
                    rowScope.find('.quantity, #quantity').attr('max', netTotal);
                }
            }

            function syncAllRows() {
                if (!config.cash) return;
                $('#cash_sale_items [data-repeater-item]').each(function () {
                    var row = $(this).find('tr.cash-sale-item-row');
                    if (!row.length) row = $(this);
                    syncBatchOptions(row);
                });
                if (window.SaleWarranty && typeof SaleWarranty.refreshAll === 'function') {
                    SaleWarranty.refreshAll();
                }
            }
            SaleBatches.syncAllRows = syncAllRows;

            function load(product, saved) {
                var scope = config.cash ? product.closest('tr') : product.closest('form');
                var item = config.cash ? product.closest('[data-repeater-item]') : null;
                function setRawStock(qty) {
                    scope.data('raw_remain_qty', qty);
                    if (item && item.length) item.data('raw_remain_qty', qty);
                }
                var generation = (scope.data('batch-generation') || 0) + 1;
                scope.data('batch-generation', generation);
                var inventory = scope.find('.inventory_id, #inventory_id').first();
                var batchIdHidden = scope.find('.batch_id, #batch_id').first();
                var group = scope.find('.sale-batch-group');
                if (!group.length) {
                    group = $('<div class="sale-batch-group mt-2 d-none">');
                    $('<label class="sale-batch-label d-block small font-weight-bold mb-1">').text('Stock Batch *').appendTo(group);
                    var select = $('<select class="form-control sale-batch-select">').appendTo(group);
                    select.attr('name', (product.attr('name') || 'product').replace('product', 'batch_id'));
                    var meta = $('<div class="sale-batch-meta d-flex flex-wrap align-items-center justify-content-between mt-2" style="gap: 8px;">').appendTo(group);
                    $('<span class="sale-batch-return-badge d-none">').appendTo(meta);
                    $('<a class="sale-batch-view sale-batch-view-btn d-none" target="_blank" rel="noopener">')
                        .html('<i class="fas fa-external-link-alt mr-1"></i>View Batch')
                        .appendTo(meta);
                    group.insertAfter(config.cash ? (product.closest('.bootstrap-select').length ? product.closest('.bootstrap-select') : product) : product.closest('.input-group'));
                }
                function updateReturnBadge(batch) {
                    var badge = group.find('.sale-batch-return-badge');
                    if (batch && batch.is_returned) {
                        var icon = batch.is_damaged_return ? 'fa-exclamation-triangle' : 'fa-undo-alt';
                        var label = batch.is_damaged_return ? 'Returned Item (Damaged / Reduced Price)' : 'Contains Returned Item';
                        badge.html('<i class="fas ' + icon + ' mr-1"></i>' + label)
                            .removeClass('d-none is-damaged is-restocked')
                            .addClass(batch.is_damaged_return ? 'is-damaged' : 'is-restocked');
                    } else {
                        badge.addClass('d-none').removeClass('is-damaged is-restocked').empty();
                    }
                }
                var batchSelect = group.find('select');
                group.addClass('d-none');
                batchSelect.empty().append($('<option>').val('').text('Select stock batch'));
                batchSelect.prop('required', false);
                inventory.val('');
                if (batchIdHidden.length) batchIdHidden.val('');
                SaleWarranty.bind(scope, []);
                if (!saved) {
                    scope.find('.purchase_price, #purchase_price, .unit_price, #unit_price, .hire_price, #gross_hire_price, .original_unit_price, #original_unit_price, #original_gross_hire_price, .gross_amount, #gross_amount, .net_amount, #net_amount, .discount, #discount').val('');
                    scope.find('.quantity, #quantity').val(1);
                    scope.find('input[type="file"][name*="warranty_document"]').val('');
                }
                group.find('a').addClass('d-none').removeAttr('href');
                if (!product.val()) {
                    scope.find('.available_quantity, #available_quantity').val('');
                    setRawStock(0);
                    return;
                }

                $.getJSON(config.url + '/' + product.val(), { sale_id: config.saleId || '' }).done(function (response) {
                    if (scope.data('batch-generation') !== generation) return;
                    var batches = response.batches || [];
                    var total = batches.reduce(function (sum, batch) { return sum + Number(batch.available_qty || 0); }, 0);
                    var hasMultipleBatches = batches.length > 1;
                    var deductions = getOtherRowsDeductions(scope, product.val());
                    var netTotal = Math.max(0, total - deductions.totalOthers);

                    if (!saved && (total <= 0 || netTotal <= 0)) {
                        product.val('');
                        if ($.fn.selectpicker) product.selectpicker('refresh');
                        inventory.val('');
                        if (batchIdHidden.length) batchIdHidden.val('');
                        scope.find('.available_quantity, #available_quantity').val('');
                        scope.find('.quantity, #quantity').val('');
                        scope.find('.barcode_description, #barcode_description').val('');
                        scope.find('.purchase_price, #purchase_price, .unit_price, #unit_price, .hire_price, #gross_hire_price, .gross_amount, #gross_amount, .net_amount, #net_amount, .discount, #discount').val('');
                        setRawStock(0);
                        group.addClass('d-none');
                        SaleWarranty.bind(scope, []);
                        syncAllRows();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Out of Stock',
                                text: total <= 0
                                    ? 'The selected product is currently out of stock.'
                                    : 'All available stock for this product has already been allocated in this invoice.'
                            });
                        }
                        return;
                    }

                    scope.data('sale-batches-data', { batches: batches, name: response.name, down_payment_rate: response.down_payment_rate });
                    scope.data('sale-batches-product-id', product.val());

                    scope.find('.barcode_description, #barcode_description').val(response.name || '');
                    if (!saved) scope.find('#down_payment_rate').val(response.down_payment_rate || 0);

                    // When product has 0 or 1 batch: hide batch UI completely and do not require batch selection.
                    // Only show batch dropdown when product has multiple batches (> 1).
                    group.toggleClass('d-none', !hasMultipleBatches);
                    batchSelect.prop('required', hasMultipleBatches);

                    function applyPrices(batch, restore) {
                        if (!restore) {
                            scope.find('input[type="file"][name*="warranty_document"]').val('');
                            var quantity = scope.find('.quantity, #quantity');
                            syncBatchOptions(scope, batch ? batch.id : '');
                            var curAvail = parseFloat(scope.find('.available_quantity, #available_quantity').val());
                            if (isNaN(curAvail)) curAvail = batch ? batch.available_qty : total;
                            quantity.attr({ min: 1, max: curAvail });
                            if (!quantity.val() || Number(quantity.val()) > curAvail) {
                                quantity.val(curAvail > 0 ? 1 : 0);
                            }

                            if (batch) {
                                scope.find('.purchase_price, #purchase_price').val(money(batch.purchase_price));
                                scope.find('.unit_price, #unit_price, .original_unit_price, #original_unit_price').val(money(batch.sale_price));
                                if (config.cash) {
                                    var cashGrossPrice = batch.hire_price == null || batch.hire_price === '' ? batch.sale_price : batch.hire_price;
                                    if (scope.find('.hire_price').length) {
                                        scope.find('.hire_price').val(money(cashGrossPrice)).trigger('input');
                                    } else {
                                        scope.find('.unit_price, .original_unit_price').val(money(cashGrossPrice));
                                        scope.find('.discount').val(money((Number(cashGrossPrice) - Number(batch.sale_price)) * Number(quantity.val())));
                                        scope.find('.unit_price').trigger('input');
                                    }
                                    scope.find('.quantity').trigger('input');
                                    if (typeof refreshAllAvailableStock === 'function') refreshAllAvailableStock();
                                    if (typeof calculatePaidAmount === 'function') calculatePaidAmount();
                                } else {
                                    scope.find('#gross_hire_price, #original_gross_hire_price').val(money(batch.hire_price));
                                    scope.find('#discount').val(0);
                                    scope.find('#document_charges').val(Number(batch.sale_price) <= 20000 ? 200 : 300).trigger('input');
                                    scope.find('#quantity').trigger('input');
                                    var down = Number(scope.find('#down_payment').val()) || 0;
                                    scope.find('#down_payment_fixed').val(down);
                                    scope.find('#paid_amount').val(money(down - (Number(scope.find('#advance_amount').val()) || 0))).trigger('input');
                                    if (typeof outstanding === 'function') outstanding();
                                }
                            }
                        }
                    }

                    function bindBatchWarranty(batch, restore) {
                        SaleWarranty.bind(scope, []);
                        if (!batch) return;
                        var units = (batch.units || []).map(function (unit) {
                            var result = Object.assign({}, unit, { inventory_id: batch.id, batch_available_qty: batch.available_qty });
                            if (restore && saved && Number(saved.warrantyId) === Number(unit.id)) {
                                ['warranty_period', 'additional_warranty_description', 'warranty_document'].forEach(function (field) {
                                    if (saved.snapshot && Object.prototype.hasOwnProperty.call(saved.snapshot, field)) result[field] = saved.snapshot[field];
                                });
                            }
                            return result;
                        });
                        SaleWarranty.bind(scope, units, {
                            preservePrices: true,
                            onSelect: function (unit) {
                                if (!unit) return;
                                if (hasMultipleBatches) {
                                    batchSelect.val(unit.inventory_id);
                                }
                                inventory.val(unit.inventory_id);
                                if (batchIdHidden.length) batchIdHidden.val(unit.inventory_id);
                                syncBatchOptions(scope, unit.inventory_id);
                                if (config.canViewBatch) group.find('a').attr('href', '/stock/batch/' + unit.inventory_id).removeClass('d-none');
                                syncAllRows();
                            }
                        });
                        if (restore && saved && saved.warrantyId) {
                            var index = units.findIndex(function (unit) { return Number(unit.id) === Number(saved.warrantyId); });
                            if (index >= 0) scope.find('select.serial_select, select#serial_select').first().prop('selectedIndex', index + 1).trigger('change');
                        }
                    }

                    function choose(restore) {
                        var batchId = batchSelect.val();
                        var batch = batches.find(function (item) { return String(item.id) === String(batchId); });
                        group.find('a').addClass('d-none').removeAttr('href');

                        if (batch) {
                            var batchDeductions = getOtherRowsDeductions(scope, product.val());
                            var takenFromThis = batchDeductions.byBatch[String(batch.id)] || 0;
                            var availInThis = Math.max(0, Number(batch.available_qty || 0) - takenFromThis);
                            var currentNetTotal = Math.max(0, total - batchDeductions.totalOthers);
                            var effectiveAvail = Math.min(availInThis, currentNetTotal);

                            if (!restore && effectiveAvail <= 0) {
                                batchSelect.val('');
                                inventory.val('');
                                if (batchIdHidden.length) batchIdHidden.val('');
                                scope.find('.available_quantity, #available_quantity').val(currentNetTotal);
                                scope.find('.quantity, #quantity').val('');
                                scope.find('.purchase_price, #purchase_price, .unit_price, #unit_price, .hire_price, #gross_hire_price, .gross_amount, #gross_amount, .net_amount, #net_amount, .discount, #discount').val('');
                                SaleWarranty.bind(scope, []);
                                syncBatchOptions(scope, '');
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Batch Out of Stock',
                                        text: 'The selected stock batch has no available stock remaining in this invoice.'
                                    });
                                }
                                return;
                            }

                            inventory.val(batch.id);
                            if (batchIdHidden.length) batchIdHidden.val(batch.id);
                            if (config.canViewBatch) group.find('a').attr('href', '/stock/batch/' + batch.id).removeClass('d-none');
                            updateReturnBadge(batch);
                            syncBatchOptions(scope, batch.id);
                            applyPrices(batch, restore);
                            bindBatchWarranty(batch, restore);
                            syncAllRows();
                        } else {
                            // No batch selected: display the TOTAL available stock across all batches
                            inventory.val('');
                            if (batchIdHidden.length) batchIdHidden.val('');
                            updateReturnBadge(null);
                            syncBatchOptions(scope, '');
                            SaleWarranty.bind(scope, []);
                            if (!restore) {
                                scope.find('.purchase_price, #purchase_price, .unit_price, #unit_price, .hire_price, #gross_hire_price, .gross_amount, #gross_amount, .net_amount, #net_amount').val('');
                            }
                            syncAllRows();
                        }
                    }

                    batchSelect.off('change.saleBatch').on('change.saleBatch', function () { choose(false); });

                    if (saved) {
                        group.find('.sale-batch-label, .sale-batch-select').toggleClass('d-none', !hasMultipleBatches);
                        syncBatchOptions(scope, saved.inventory_id);
                        if (hasMultipleBatches) {
                            batchSelect.val(saved.inventory_id);
                        }
                        choose(true);
                    } else if (hasMultipleBatches) {
                        // For products with multiple batches: initially do NOT select any batch.
                        // Show the total stock in Available Quantity, and require the user to pick a batch.
                        group.find('.sale-batch-label, .sale-batch-select').removeClass('d-none');
                        batchSelect.val('');
                        inventory.val('');
                        if (batchIdHidden.length) batchIdHidden.val('');
                        syncBatchOptions(scope, '');
                        SaleWarranty.bind(scope, []);
                        syncAllRows();
                    } else if (batches.length === 1) {
                        // Product has only 1 batch (no batch choice needed):
                        // Hide batch select UI, set hidden inventory_id, and display return badge / View Batch if applicable.
                        var soleBatch = batches[0];
                        group.find('.sale-batch-label, .sale-batch-select').addClass('d-none');
                        batchSelect.empty().append($('<option>').val(soleBatch.id).text(soleBatch.code));
                        batchSelect.val(soleBatch.id);
                        inventory.val(soleBatch.id);
                        if (batchIdHidden.length) batchIdHidden.val(soleBatch.id);
                        updateReturnBadge(soleBatch);
                        if (config.canViewBatch) group.find('a').attr('href', '/stock/batch/' + soleBatch.id).removeClass('d-none');
                        if (soleBatch.is_returned) group.removeClass('d-none');
                        syncBatchOptions(scope, soleBatch.id);
                        applyPrices(soleBatch, false);
                        bindBatchWarranty(soleBatch, false);
                        syncAllRows();
                    } else {
                        // Out of stock (0 batches)
                        inventory.val('');
                        if (batchIdHidden.length) batchIdHidden.val('');
                        scope.find('.available_quantity, #available_quantity').val(0);
                        setRawStock(0);
                        scope.find('.quantity, #quantity').val('');
                        SaleWarranty.bind(scope, []);
                        syncAllRows();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Out of Stock',
                                text: 'The selected product is currently out of stock.'
                            });
                        }
                    }
                }).fail(function () {
                    if (typeof AppMessages !== 'undefined' && AppMessages.warning) {
                        AppMessages.warning('Could not load stock batches. Please select the product again.');
                    }
                });
            }

            $('body').off('change', config.cash ? '.product' : '#product');
            $('body').off('change', selector).on('change', selector, function () { load($(this)); });
            if (config.cash) $('body').off('change', '.serial_select').off('change', '.warranty_select');
            else $('body').off('change', '#serial_select').off('change', '#warranty_select');

            if (config.cash) {
                $('body').off('input.saleBatchSync', '.quantity')
                    .on('input.saleBatchSync', '.quantity', function () {
                        syncAllRows();
                    });
            }

            $(selector).each(function () {
                var product = $(this);
                var scope = config.cash ? product.closest('tr') : product.closest('form');
                var id = scope.find('.inventory_id, #inventory_id').val();
                var itemId = scope.find('.sale_item_id').val();
                var saved = config.rows.find(function (row) { return config.cash ? String(row.id) === String(itemId) : true; });
                load(product, saved ? { inventory_id: id || saved.inventory_id, warrantyId: config.cash ? saved.product_warranty_id : config.warrantyId, snapshot: config.cash ? saved : config.snapshot } : null);
            });
        }
    };
})(jQuery);
