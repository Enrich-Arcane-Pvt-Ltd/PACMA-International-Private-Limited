const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
let quantity = 2;
const warnings = [];
const select = {};
const secondSelect = {};
let selects = [select];
function $(value) {
    return {
        on() {},
        each(callback) { if (value === '#grn-edit-form select.item') selects.forEach(select => callback.call(select)); },
        val() { return value === select || value === secondSelect ? '10' : quantity; },
        closest() { return {find(selector) { return {val() { return selector === '.id' ? (value === secondSelect ? 6 : 5) : quantity; }}; }}; },
        find() { return {text() { return 'TEST Product'; }}; }
    };
}
const context = {window: {}, document: {}, $, Swal: {fire: warning => warnings.push(warning)}, alert: message => warnings.push(message)};
vm.runInNewContext(fs.readFileSync('public/assets/js/grn-edit-warranty.js', 'utf8'), context);
const data = {line_5: [{serial: 'ONE', warranty: 'CARD1'}]};
const types = {10: false};
const validate = context.window.initGrnEditWarranty(data, types);
assert.equal(validate(), false, 'added quantity requires another warranty record');
assert.equal(warnings[0].type, 'warning');
data.line_5.push({serial: 'TWO', warranty: 'CARD2'});
assert.equal(validate(), true);
data.line_5[1].warranty = '';
assert.equal(validate(), false, 'non-furniture requires card number');
types[10] = true;
assert.equal(validate(), true, 'furniture card is optional');
data.line_5[1].serial = '';
assert.equal(validate(), false, 'furniture still requires serial/color');
quantity = 1;
assert.equal(validate(), false, 'quantity decrease requires matching saved details');
data.line_5 = [data.line_5[0]];
assert.equal(validate(), true);
data.line_5 = [];
assert.equal(validate(), false, 'new item cannot submit without warranty data');
data.line_5 = [{serial: 'OLD', warranty: 'OLD-CARD'}];
selects = [select, secondSelect];
assert.equal(validate(), false, 'same product in a second batch needs its own warranty details');
data.line_6 = [{serial: 'NEW-BATCH', warranty: 'NEW-CARD'}];
assert.equal(validate(), true, 'same product batches validate independently');
console.log('PASS: quantity changes, missing details warning, furniture rules, and new items');
