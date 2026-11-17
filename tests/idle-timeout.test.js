const vm = require('vm');
const fs = require('fs');
const assert = require('assert');
const source = fs.readFileSync('public/assets/js/idle-timeout.js', 'utf8');
let clock = 1000000, serverLast = clock;
const tabs = [], stored = new Map();
function tab() {
    const events = {}, windowEvents = {}, state = {redirected: false};
    const context = {
        Date: {now: () => clock}, JSON, Math, Number,
        document: {
            currentScript: {dataset: {key: 'test', last: serverLast, now: clock,
                status: '/status', activity: '/activity', login: '/login'}},
            visibilityState: 'visible', querySelector: () => ({content: 'csrf'}),
            addEventListener: (name, fn) => { events[name] = fn; }
        },
        window: {
            location: {replace: () => { state.redirected = true; }},
            addEventListener: (name, fn) => { windowEvents[name] = fn; },
            setInterval: fn => { state.tick = fn; }
        },
        localStorage: {
            getItem: key => stored.get(key) || null,
            setItem(key, value) {
                stored.set(key, value);
                tabs.forEach(t => { if (t !== state && t.storage) t.storage({key}); });
            }
        },
        fetch: async (url, options) => {
            if (clock - serverLast >= 300000) return {status: 401, ok: false};
            if (url === '/activity') serverLast = Math.max(serverLast, clock - JSON.parse(options.body).age);
            return {status: 200, ok: true, json: async () => ({lastActivity: serverLast})};
        }
    };
    vm.runInNewContext(source, context);
    state.events = events;
    state.storage = windowEvents.storage;
    tabs.push(state);
    return state;
}
const flush = () => new Promise(resolve => setImmediate(resolve));
(async () => {
    const a = tab(), b = tab();
    await flush();
    clock += 60000;
    a.tick(); b.tick();
    await flush();
    assert.equal(serverLast, 1000000, 'background checks must not renew');
    a.events.input({isTrusted: false});
    await flush();
    assert.equal(serverLast, 1000000, 'synthetic activity ignored');
    a.events.keydown({isTrusted: true});
    await flush();
    assert.equal(serverLast, clock, 'typing renews');
    clock += 100000;
    b.tick();
    await flush();
    assert.equal(b.redirected, false, 'other tab uses shared activity');
    clock += 201000;
    b.tick();
    await flush();
    assert.equal(a.redirected, true);
    assert.equal(b.redirected, true);
    console.log('PASS: background checks, synthetic events, typing, shared activity, expiry, cross-tab logout');
})().catch(error => { console.error(error); process.exitCode = 1; });
