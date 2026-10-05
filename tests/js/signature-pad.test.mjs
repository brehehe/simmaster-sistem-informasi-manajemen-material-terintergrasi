import test from 'node:test';
import assert from 'node:assert/strict';
import { signaturePad } from '../../resources/js/signature-pad.js';

test('drawing maps touch coordinates, syncs on finish and clears deferred state', () => {
    const writes = [], lines = [];
    const context = { beginPath() {}, moveTo(...xy) { lines.push(xy); }, lineTo(...xy) { lines.push(xy); }, stroke() {}, clearRect() {} };
    const pad = signaturePad({ get: () => '', set: (...args) => writes.push(args) }, 'kasiSignatureDrawn');
    pad.$refs = { pad: { width: 1000, height: 300, getBoundingClientRect: () => ({ left: 10, top: 20, width: 500, height: 150 }), setPointerCapture() {}, getContext: () => context, toDataURL: () => 'data:image/png;base64,test' } };
    pad.start({ button: 0, isPrimary: true, pointerId: 1, clientX: 20, clientY: 30 });
    pad.move({ isPrimary: true, clientX: 110, clientY: 70 });
    pad.finish();
    assert.deepEqual(lines, [[20, 20], [200, 100]]);
    assert.deepEqual(writes[0], ['kasiSignatureDrawn', 'data:image/png;base64,test', false]);
    pad.clear();
    assert.equal(pad.dirty, false);
    assert.deepEqual(writes[1], ['kasiSignatureDrawn', '', false]);
    pad.start({ button: 2, isPrimary: true });
    assert.equal(pad.drawing, false);
});
