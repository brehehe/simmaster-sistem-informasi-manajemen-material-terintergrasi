import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
import { test } from 'node:test';

function setup() {
    const events = {}, hooks = {}, tasks = [];
    const root = {
        getAttribute: () => 'component-1',
        setAttribute() {}, removeAttribute() {},
        querySelectorAll: () => [button],
    };
    const button = {
        disabled: false, attributes: [{ name: 'wire:click', value: 'save' }],
        closest: (selector) => selector.includes('wire') ? root : button,
    };
    const livewire = { hook: (name, callback) => { hooks[name] = callback; } };
    const document = { addEventListener: (name, callback) => { events[name] = callback; } };
    vm.runInNewContext(readFileSync('resources/js/action-guard.js', 'utf8'), {
        document, window: { Livewire: livewire, addEventListener() {} }, Livewire: livewire,
        queueMicrotask: callback => tasks.push(callback), Map,
    });
    const flush = () => { while (tasks.length) tasks.shift()(); };
    const click = () => {
        const event = { type: 'click', target: button, blocked: false,
            preventDefault() { this.blocked = true; }, stopImmediatePropagation() {},
        };
        events.click(event);
        return event;
    };
    const response = (effects = {}, failed = false) => {
        hooks.commit({ component: { id: 'component-1' }, commit: { calls: [{ method: 'save' }] },
            succeed: callback => { if (!failed) callback({ effects }); },
            fail: callback => { if (failed) callback(); },
        });
        flush();
    };
    return { button, click, flush, response, events };
}

test('blocks a second click immediately, then enables retry after validation response', () => {
    const ui = setup();
    assert.equal(ui.click().blocked, false);
    assert.equal(ui.click().blocked, true);
    ui.flush();
    assert.equal(ui.button.disabled, true);
    ui.response();
    assert.equal(ui.button.disabled, false);
    assert.equal(ui.click().blocked, false);
});

test('keeps save disabled until redirect navigation completes', () => {
    const ui = setup();
    ui.click(); ui.flush(); ui.response({ redirect: '/stocks' });
    assert.equal(ui.button.disabled, true);
    assert.equal(ui.click().blocked, true);
    ui.events['livewire:navigated']();
    assert.equal(ui.button.disabled, false);
});

test('releases the button after a failed request', () => {
    const ui = setup();
    ui.click(); ui.flush(); ui.response({}, true);
    assert.equal(ui.button.disabled, false);
    assert.equal(ui.click().blocked, false);
});

test('does not lock client-side refresh helpers', () => {
    const ui = setup();
    ui.button.attributes[0].value = '$refresh';
    ui.click(); ui.flush();
    assert.equal(ui.button.disabled, false);
});
