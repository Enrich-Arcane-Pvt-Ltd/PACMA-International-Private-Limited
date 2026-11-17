(function ($) {
    'use strict';

    var activeInput = null;
    var activeIndex = -1;
    var results = [];
    var searchInputs = '.product-search, .supplier-search';
    function sourceSelect(input) {
        return input.parentNode.querySelector(input.classList.contains('supplier-search') ? 'select#vender' : 'select.item');
    }
    function validationMessage(input) {
        return input.classList.contains('supplier-search')
            ? 'Please select a supplier from the search results.'
            : 'Please select a product from the search results.';
    }
    var popup = document.createElement('div');
    popup.className = 'product-search-results';
    popup.setAttribute('role', 'listbox');
    popup.style.cssText = 'display:none;position:fixed;z-index:1060;background:#fff;color:#343a40;border:1px solid #ddd;border-radius:4px;box-shadow:0 4px 12px rgba(0,0,0,.12);max-height:240px;overflow-y:auto;';
    document.body.appendChild(popup);

    function closeResults() {
        popup.style.display = 'none';
        if (activeInput) activeInput.setAttribute('aria-expanded', 'false');
        activeInput = null;
    }

    function choose(option) {
        var input = activeInput;
        var select = sourceSelect(input);
        input.value = option.text;
        input.title = option.text;
        if (input.classList.contains('product-search')) {
            input.parentNode.querySelector('.selected-product-name').textContent = option.text;
        }
        input.setCustomValidity('');
        if (select.value !== option.value) $(select).val(option.value).trigger('change');
        closeResults();
    }

    function showResults(input) {
        if (activeInput && activeInput !== input) closeResults();
        activeInput = input;
        activeIndex = -1;
        popup.textContent = '';
        var query = input.value.trim().toLowerCase();
        var select = sourceSelect(input);
        results = Array.prototype.filter.call(select.options, function (option) {
            return option.value && !option.disabled && option.text.toLowerCase().indexOf(query) !== -1;
        });
        results.forEach(function (option, index) {
            var row = document.createElement('div');
            row.textContent = option.text;
            row.setAttribute('role', 'option');
            row.style.cssText = 'padding:9px 12px;cursor:pointer;white-space:normal;background:#fff;color:#343a40;font-size:14px;';
            row.addEventListener('mouseenter', function () { highlight(index); });
            row.addEventListener('mousedown', function (event) {
                event.preventDefault();
                choose(option);
            });
            popup.appendChild(row);
        });
        if (!results.length) {
            popup.textContent = input.classList.contains('supplier-search') ? 'No suppliers found' : 'No products found';
            popup.style.padding = '9px 12px';
        } else popup.style.padding = '0';
        var rect = input.getBoundingClientRect();
        popup.style.left = rect.left + 'px';
        var below = input.classList.contains('supplier-search');
        popup.style.top = below ? rect.bottom + 'px' : 'auto';
        popup.style.bottom = below ? 'auto' : (window.innerHeight - rect.top) + 'px';
        popup.style.width = Math.min(Math.max(rect.width, 300), window.innerWidth - rect.left - 12) + 'px';
        popup.style.maxHeight = Math.max(0, Math.min(240, (below ? window.innerHeight - rect.bottom : rect.top) - 12)) + 'px';
        popup.style.display = 'block';
        input.setAttribute('aria-expanded', 'true');
    }

    function highlight(index) {
        activeIndex = index;
        Array.prototype.forEach.call(popup.children, function (row, i) {
            row.style.background = i === index ? '#eef0ff' : '#fff';
            row.setAttribute('aria-selected', i === index ? 'true' : 'false');
        });
        if (popup.children[index]) popup.children[index].scrollIntoView({block: 'nearest'});
    }

    function prepare(input) {
        var select = sourceSelect(input);
        input.removeAttribute('list');
        input.setAttribute('role', 'combobox');
        input.setAttribute('aria-autocomplete', 'list');
        input.setAttribute('aria-expanded', 'false');
        input.required = true;
        if (input.classList.contains('product-search')) {
            var caption = input.parentNode.querySelector('.selected-product-name');
            if (!caption) {
                caption = document.createElement('small');
                caption.className = 'selected-product-name d-block text-muted mt-1';
                caption.style.cssText = 'white-space:normal;overflow-wrap:anywhere;max-width:30rem;';
                input.parentNode.appendChild(caption);
            }
            caption.textContent = select.value ? input.value : '';
            input.title = caption.textContent;
        }
        input.setCustomValidity(select.value ? '' : validationMessage(input));
    }

    $(document).on('focusin', searchInputs, function () { showResults(this); });
    $(document).on('focusout', searchInputs, closeResults);
    $(document).on('keydown', searchInputs, function (event) {
        if (event.key === 'Escape') { closeResults(); return; }
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            if (!activeInput) showResults(this);
            if (results.length) highlight((activeIndex + (event.key === 'ArrowDown' ? 1 : -1) + results.length) % results.length);
        } else if (event.key === 'Enter' && activeInput && activeIndex >= 0) {
            event.preventDefault();
            choose(results[activeIndex]);
        }
    });
    window.addEventListener('resize', closeResults);
    document.addEventListener('scroll', function (event) {
        if (!popup.contains(event.target)) closeResults();
    }, true);

    $(document).on('input', searchInputs, function () {
        var select = sourceSelect(this);
        var query = this.value.trim().toLowerCase();
        var match = Array.prototype.find.call(select.options, function (option) {
            return option.value && !option.disabled && option.text.toLowerCase() === query;
        });
        var value = match ? match.value : '';
        this.title = match ? match.text : '';
        if (this.classList.contains('product-search')) {
            this.parentNode.querySelector('.selected-product-name').textContent = match ? match.text : '';
        }
        this.setCustomValidity(match ? '' : validationMessage(this));
        if (select.value !== value) $(select).val(value).trigger('change');
        showResults(this);
    });

    $(document).on('input', '.grn-order-product-search', function () {
        var query = this.value.trim().toLowerCase();
        $(this).closest('.card').find('select.item').each(function () {
            var row = $(this).closest('tr');
            var text = $(this).find('option:selected').text() + ' ' + (row.attr('data-product-name') || '');
            row.toggle(text.toLowerCase().indexOf(query) !== -1);
        });
    });

    // Repeater templates include the search field; clear it for each new row.
    window.resetProductSearch = function (row) {
        $(row).find('.product-search').each(function () {
            this.value = '';
            prepare(this);
        });
    };
    $(function () {
        $('.product-search').each(function () { prepare(this); });
        $('select#vender').each(function () {
            var select = $(this);
            if (select.data('selectric')) select.selectric('destroy');
            select.removeClass('selectric').addClass('d-none').prop('required', false)
                .attr({'tabindex': '-1', 'aria-hidden': 'true'});
            var input = document.createElement('input');
            input.type = 'search';
            input.className = 'form-control supplier-search';
            input.placeholder = 'Search supplier';
            input.autocomplete = 'off';
            input.setAttribute('aria-label', 'Search supplier by name');
            input.id = 'supplier-search';
            if (this.value) input.value = select.find('option:selected').text();
            this.parentNode.insertBefore(input, this);
            $('label[for="vender_id"], label[for="vender"]').attr('for', input.id);
            prepare(input);
        });
    });
})(jQuery);
