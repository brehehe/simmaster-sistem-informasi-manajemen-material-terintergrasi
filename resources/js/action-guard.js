// One in-flight action per component, including clicks before Livewire sends its request.
const pending = new Map();

function directive(element, name) {
    return [...element.attributes].find(({ name: attribute }) => attribute === name || attribute.startsWith(`${name}.`));
}

function release(id) {
    const state = pending.get(id);
    if (!state) return;
    state.controls.forEach((disabled, control) => { control.disabled = disabled; });
    state.root.removeAttribute('aria-busy');
    pending.delete(id);
}

function guard(event) {
    const element = event.type === 'submit' ? event.target : event.target.closest('button, a, input[type="submit"]');
    if (!element) return;
    const action = directive(element, event.type === 'submit' ? 'wire:submit' : 'wire:click');
    if (!action || action.value.trim().startsWith('$')) return;
    // Confirmation must happen before acquiring the guard; cancelled dialogs send no request.
    if (directive(element, 'wire:confirm')) return;
    const root = element.closest('[wire\\:id]');
    if (!root) return;
    const id = root.getAttribute('wire:id');
    if (pending.has(id)) {
        event.preventDefault();
        event.stopImmediatePropagation();
        return;
    }
    const controls = new Map();
    pending.set(id, { root, controls });
    root.setAttribute('aria-busy', 'true');
    queueMicrotask(() => {
        if (!pending.has(id)) return;
        root.querySelectorAll('button, input[type="submit"]').forEach(control => {
            if (control.closest('[wire\\:id]') !== root) return;
            controls.set(control, control.disabled);
            control.disabled = true;
        });
    });
}

document.addEventListener('click', guard, true);
document.addEventListener('submit', guard, true);
let hooksInstalled = false;
function installHooks() {
    if (hooksInstalled) return;
    hooksInstalled = true;
    Livewire.hook('commit', ({ component, commit, succeed, fail }) => {
        if (!commit.calls.length) return;
        succeed(({ effects }) => {
            if (!effects.redirect) queueMicrotask(() => release(component.id));
        });
        fail(() => release(component.id));
    });
}
if (window.Livewire) installHooks();
else document.addEventListener('livewire:init', installHooks, { once: true });
document.addEventListener('livewire:navigated', () => {
    [...pending.keys()].forEach(release);
});
window.addEventListener('pageshow', () => { [...pending.keys()].forEach(release); });
