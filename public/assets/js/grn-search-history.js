(function ($) {
    'use strict';
    $(function () {
        var table = $('#grn_table');
        if (!table.length) return;
        var input = $('#grn_table_filter input');
        var api = table.DataTable();
        var key = 'dreamhomes-grn-search-' + table.attr('data-search-user');
        var history = [];
        try { history = JSON.parse(localStorage.getItem(key) || '[]'); } catch (e) {}
        if (!Array.isArray(history)) history = [];
        history = history.filter(function (value) { return typeof value === 'string'; }).slice(0, 15);
        var panel = $('<div>').css({position: 'absolute', background: '#202124', color: '#f1f3f4',
            border: '1px solid #5f6368', borderRadius: '6px', boxShadow: '0 4px 12px rgba(0,0,0,.3)', zIndex: 1060, maxHeight: '220px',
            overflowY: 'auto', textAlign: 'left', display: 'none'}).appendTo('body');
        function remember() {
            var value = input.val().trim();
            if (!value) return;
            history = [value].concat(history.filter(function (item) {
                return item.toLowerCase() !== value.toLowerCase();
            })).slice(0, 15);
            try { localStorage.setItem(key, JSON.stringify(history)); } catch (e) {}
        }
        function suggest() {
            var query = input.val().trim().toLowerCase();
            if (!query) { panel.hide(); return; }
            panel.empty();
            history.filter(function (value) { return value.toLowerCase().indexOf(query) !== -1; })
                .forEach(function (value) {
                    $('<button type="button">').text(value).css({display: 'block', width: '100%',
                        border: 0, padding: '8px 12px', background: '#202124', color: '#f1f3f4', textAlign: 'left', cursor: 'pointer'})
                        .on('mouseenter focus', function () { $(this).css('background', '#3c4043'); })
                        .on('mouseleave blur', function () { $(this).css('background', '#202124'); })
                        .on('mousedown', function (event) { event.preventDefault(); })
                        .on('click', function () {
                            input.val(value); api.search(value).draw(); remember(); panel.hide();
                        }).appendTo(panel);
                });
            var rect = input[0].getBoundingClientRect();
            panel.css({left: rect.left + window.scrollX, top: rect.bottom + window.scrollY,
                minWidth: rect.width}).toggle(panel.children().length > 0);
        }
        var saveTimer;
        input.attr('autocomplete', 'off').on('input', function () {
            clearTimeout(saveTimer);
            suggest();
            saveTimer = setTimeout(remember, 1000);
        })
            .on('blur', function () { remember(); panel.hide(); })
            .on('keydown', function (event) {
                if (event.key === 'Enter') { event.preventDefault(); remember(); panel.hide(); }
                if (event.key === 'Escape') panel.hide();
            });
        $(window).on('resize scroll', function () { panel.hide(); });
    });
})(jQuery);
