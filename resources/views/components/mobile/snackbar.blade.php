@php
    $styles = [
        'error' => 'bg-red-500 text-white',
        'success' => 'bg-green-600 text-white',
        'warning' => 'bg-yellow-500 text-black',
        'info' => 'bg-blue-500 text-white',
    ];
    $icons = [
        'error' => '⚠',
        'success' => '✔',
        'warning' => '⚠',
        'info' => 'ℹ',
    ];
@endphp

<div x-data="{
        snackbar: @entangle('snackbar'),
        show: false,
        icon: '',
        style: '',
        _timeout: null,
        icons: {
            error: '⚠',
            success: '✔',
            warning: '⚠',
            info: 'ℹ',
        },
        styles: {
            error: 'bg-red-500 text-white',
            success: 'bg-green-600 text-white',
            warning: 'bg-yellow-500 text-black',
            info: 'bg-blue-500 text-white',
        },
        async showAndAutoHide() {
            if (!this.snackbar || !this.snackbar.message) return;
            this.icon = this.icons[this.snackbar.type] ?? '';
            this.style = this.styles[this.snackbar.type] ?? this.styles.success;
            this.show = true;

            if (this._timeout) clearTimeout(this._timeout);
            this._timeout = setTimeout(async () => {
                this.show = false;
                this._timeout = null;

                // Clear snackbar on server so it doesn't reappear after re-render.
                // Gunakan @this.set (Livewire provides it in blade). Jika versi Livewire Anda
                // tidak mendukung @this.set, fallback ke Livewire.emit('clearSnackbar') bisa ditambahkan.
                try {
                    @this.set('snackbar', { type: '', message: '' });
                } catch (e) {
                    // fallback: emit event (opsional)
                    if (window.Livewire) Livewire.emit('clearSnackbar');
                }
            }, 2500);
        }
    }" x-init="$watch('snackbar', value => { if (value && value.message) showAndAutoHide(); }, { immediate: true })"
    x-show="show" x-cloak x-transition.opacity.duration.300ms
    class="fixed top-0 left-1/2 -translate-x-1/2 w-[390px] z-[9999] flex items-center gap-2 px-4 py-3 text-sm font-medium shadow-lg rounded-b-lg"
    :class="style">
    <span class="text-lg" x-text="icon"></span>
    <span class="truncate" x-text="snackbar?.message ?? ''"></span>
</div>

{{-- Optional fallback listener if server dispatches a browser event somewhere --}}
<script>
    // fallback: jika ada bagian lain yang masih mem-dispatch CustomEvent('snackbar', ...)
    window.addEventListener('snackbar', e => {
        if (!e?.detail) return;
        // set entangled state via @this if possible
        try {
            @this.set('snackbar', e.detail);
        } catch (err) {
            if (window.Livewire) Livewire.emit('snackbar', e.detail);
        }
    });

    // Also optionally listen for clearSnackbar emitted in fallback above
    if (window.Livewire) {
        Livewire.on('clearSnackbar', () => {
            try { @this.set('snackbar', { type: '', message: '' }); } catch (e) { }
        });
    }
</script>