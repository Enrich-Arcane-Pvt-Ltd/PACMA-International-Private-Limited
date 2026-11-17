(function () {
    'use strict';
    var url = document.currentScript.dataset.lookupUrl;
    var input = document.querySelector('input[name="supplier_invoice_number"]:not([type="hidden"])');
    if (!input) return;
    var version = 0;
    input.addEventListener('input', function () { version++; });
    input.addEventListener('change', function () {
        var number = input.value.trim();
        var current = ++version;
        if (!number) return;
        fetch(url + '?supplier_invoice_number=' + encodeURIComponent(number), {
            credentials: 'same-origin', cache: 'no-store', headers: {'Accept': 'application/json'}
        }).then(function (response) {
            return response.ok ? response.json() : null;
        }).then(function (data) {
            if (current === version && input.value.trim() === number && data && data.redirect) {
                var message = 'Supplier Invoice Number ' + number + ' already exists. Open the existing GRN and use Add Item to add products to this invoice.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        type: 'warning',
                        title: 'Invoice number already exists',
                        text: message,
                        showCancelButton: true,
                        confirmButtonText: 'Open Existing GRN',
                        cancelButtonText: 'Cancel'
                    }).then(function (result) {
                        if ((result.isConfirmed || result.value) && current === version && input.value.trim() === number) {
                            window.location.assign(data.redirect);
                        }
                    });
                } else if (window.confirm(message) && current === version && input.value.trim() === number) {
                    window.location.assign(data.redirect);
                }
            }
        }).catch(function () { /* The submit endpoint also checks and redirects duplicates. */ });
    });
})();
