(function () {
    'use strict';
    var config = document.currentScript.dataset;
    var key = 'dreamhomes-idle-' + config.key;
    var offset = Number(config.now) - Date.now();
    var last = Number(config.last);
    var pending = 0;
    var sentAt = 0;
    var checkedAt = 0;
    var busy = false;
    var ended = false;
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    function now() { return Date.now() + offset; }
    function readShared() {
        try {
            var value = JSON.parse(localStorage.getItem(key));
            if (value && value.ended) { finish(false); }
            if (value && value.last) { last = Math.max(last, value.last); }
        } catch (e) { /* Storage can be unavailable; server enforcement remains active. */ }
    }
    function share(value) {
        try { localStorage.setItem(key, JSON.stringify(value)); } catch (e) {}
    }
    function finish(broadcast) {
        if (ended) return;
        ended = true;
        if (broadcast) share({ended: true});
        window.location.replace(config.login);
    }
    function request(activity) {
        if (busy || ended) return;
        busy = true;
        var stamp = pending;
        var options = {credentials: 'same-origin', cache: 'no-store', headers: {
            'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'
        }};
        if (activity) {
            options.method = 'POST';
            options.headers['Content-Type'] = 'application/json';
            options.headers['X-CSRF-TOKEN'] = csrf;
            options.body = JSON.stringify({age: Math.max(0, Math.floor(now() - stamp))});
            sentAt = now();
        }
        checkedAt = now();
        fetch(activity ? config.activity : config.status, options).then(function (response) {
            if (response.status === 401 || response.status === 419) {
                finish(true);
                return null;
            }
            if (!response.ok) throw new Error('Session check failed');
            return response.json();
        }).then(function (data) {
            if (!data || ended) return;
            last = Math.max(last, Number(data.lastActivity));
            if (activity && pending === stamp) pending = 0;
            share({last: last});
        }).catch(function () {
            // Retry without inventing activity when the connection returns.
        }).then(function () { busy = false; });
    }
    function tick() {
        readShared();
        if (ended) return;
        if (pending && now() - pending < 30000 && now() - sentAt >= 10000) {
            request(true);
        } else if (now() - checkedAt >= 15000 || now() - last >= 300000) {
            request(false);
        }
    }
    function activity(event) {
        if (!event.isTrusted || document.visibilityState !== 'visible' || ended) return;
        readShared();
        if (now() - last >= 300000) { request(false); return; }
        pending = now();
        last = pending;
        // Share immediately so another tab does not expire while this one is active.
        share({last: last});
        if (now() - sentAt >= 10000) request(true);
    }
    ['pointerdown', 'keydown', 'input', 'change', 'scroll', 'touchstart'].forEach(function (name) {
        document.addEventListener(name, activity, {capture: true, passive: true});
    });
    window.addEventListener('storage', function (event) { if (event.key === key) readShared(); });
    window.addEventListener('pageshow', function () { request(false); });
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') request(false);
    });
    readShared();
    request(false);
    window.setInterval(tick, 1000);
})();
