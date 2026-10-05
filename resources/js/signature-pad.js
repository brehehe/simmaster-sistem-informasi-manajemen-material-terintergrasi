export function signaturePad(wire, field) {
    return {
        drawing: false,
        dirty: false,
        init() {
            const saved = wire.get(field);
            if (saved) {
                const image = new Image();
                image.onload = () => this.$refs.pad.getContext('2d').drawImage(image, 0, 0);
                image.src = saved;
                this.dirty = true;
            }
        },
        point(event) {
            const canvas = this.$refs.pad;
            const rect = canvas.getBoundingClientRect();
            return [(event.clientX - rect.left) * canvas.width / rect.width,
                (event.clientY - rect.top) * canvas.height / rect.height];
        },
        start(event) {
            if (event.button !== 0 || !event.isPrimary) return;
            this.drawing = true;
            this.$refs.pad.setPointerCapture(event.pointerId);
            const context = this.$refs.pad.getContext('2d');
            const [x, y] = this.point(event);
            context.lineWidth = 4;
            context.lineCap = 'round';
            context.lineJoin = 'round';
            context.strokeStyle = '#162b4d';
            context.beginPath();
            context.moveTo(x, y);
        },
        move(event) {
            if (!this.drawing || !event.isPrimary) return;
            const context = this.$refs.pad.getContext('2d');
            context.lineTo(...this.point(event));
            context.stroke();
            this.dirty = true;
        },
        finish() {
            if (!this.drawing) return;
            this.drawing = false;
            if (this.dirty) wire.set(field, this.$refs.pad.toDataURL('image/png'), false);
        },
        clear() {
            this.$refs.pad.getContext('2d').clearRect(0, 0, 1000, 300);
            this.dirty = false;
            this.drawing = false;
            wire.set(field, '', false);
        },
    };
}

if (typeof window !== 'undefined') window.armasterSignature = signaturePad;
