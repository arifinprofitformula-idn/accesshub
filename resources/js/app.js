import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('confirmModal', {
    show: false,
    title: '',
    message: '',
    confirmLabel: 'Hapus',
    _onConfirm: null,

    open(opts) {
        this.title = opts.title ?? '';
        this.message = opts.message ?? '';
        this.confirmLabel = opts.confirmLabel ?? 'Hapus';
        this._onConfirm = opts.onConfirm ?? null;
        this.show = true;
    },

    confirm() {
        this.show = false;
        if (this._onConfirm) this._onConfirm();
        this._onConfirm = null;
    },

    cancel() {
        this.show = false;
        this._onConfirm = null;
    },
});

Alpine.start();
