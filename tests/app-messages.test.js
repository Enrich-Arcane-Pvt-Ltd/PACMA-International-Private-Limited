const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const calls = [];
let answer = {value: true};
const library = {fire(options) { calls.push(options); return Promise.resolve(answer); }};
const context = {window: {Swal: library, alert() {}, confirm() { return false; }}};
vm.runInNewContext(fs.readFileSync('public/assets/js/app-messages.js', 'utf8'), context);
(async () => {
    const messages = context.window.AppMessages;
    for (const type of ['warning', 'error', 'success', 'info']) {
        await messages.show(type, 'Test message', 'Test title');
        const last = calls[calls.length - 1];
        assert.equal(last.icon, type);
        assert(last.customClass.popup.includes('app-message--' + type));
        assert.equal(last.text, 'Test message');
    }
    const original = {type: 'warning', text: 'Delete?', showCancelButton: true, confirmButtonText: 'Delete', customClass: {popup: 'existing'}};
    await library.fire(original);
    assert.equal(original.type, 'warning', 'caller options must not be mutated');
    assert.equal(calls[calls.length - 1].confirmButtonText, 'Delete');
    assert(calls[calls.length - 1].customClass.popup.includes('existing'));
    await library.fire('Title', 'Body', 'success');
    assert.equal(calls[calls.length - 1].icon, 'success');
    let deleted = 0;
    answer = {dismiss: 'cancel'};
    await messages.confirm('Delete row?', () => { deleted++; });
    assert.equal(deleted, 0, 'cancel must not run deletion');
    answer = {isConfirmed: true};
    await messages.confirm('Delete row?', () => { deleted++; });
    assert.equal(deleted, 1, 'confirmation runs the action exactly once');
    answer = {value: true};
    await messages.confirm('Delete row?', () => { deleted++; });
    assert.equal(deleted, 2, 'legacy SweetAlert results remain supported');
    console.log('PASS: message themes, legacy options, custom classes, confirm and cancel behavior');
})().catch(error => { console.error(error); process.exitCode = 1; });
