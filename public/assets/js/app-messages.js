(function (window) {
    'use strict';
    var library = window.Swal;
    var nativeAlert = window.alert.bind(window);
    var nativeConfirm = window.confirm.bind(window);
    if (library && !library.appMessageTheme) {
        var original = library.fire;
        library.fire = function (options, text, icon) {
            if (typeof options === 'string') options = {title: options, text: text, icon: icon};
            options = Object.assign({}, options || {});
            var type = options.icon || options.type || (options.showCancelButton ? 'warning' : 'info');
            if (type === 'danger') type = 'error';
            delete options.type;
            options.icon = type;
            var classes = typeof options.customClass === 'string'
                ? {popup: options.customClass} : Object.assign({}, options.customClass || {});
            classes.popup = 'app-message app-message--' + type + (classes.popup ? ' ' + classes.popup : '');
            options.customClass = classes;
            return original.call(this, options);
        };
        library.appMessageTheme = true;
    }
    function show(type, message, title) {
        if (!library) { nativeAlert(String(message)); return Promise.resolve({isConfirmed: true}); }
        return library.fire({icon: type, title: title || ({warning: 'Warning', error: 'Error', success: 'Success', info: 'Information'}[type]), text: String(message)});
    }
    window.AppMessages = {
        show: show,
        warning: function (message, title) { return show('warning', message, title); },
        error: function (message, title) { return show('error', message, title); },
        success: function (message, title) { return show('success', message, title); },
        info: function (message, title) { return show('info', message, title); },
        confirm: function (message, onConfirm) {
            var promise = library ? library.fire({icon: 'warning', title: 'Are you sure?', text: String(message),
                showCancelButton: true, confirmButtonText: 'Continue', cancelButtonText: 'Cancel'})
                : Promise.resolve({isConfirmed: nativeConfirm(String(message))});
            return promise.then(function (result) {
                if (result.isConfirmed || result.value) return onConfirm();
            });
        }
    };
})(window);
