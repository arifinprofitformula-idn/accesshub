@php
    $selectedCategory = old('category_id', data_get($link, 'category_id'));
    $selectedVisibility = old('visibility', $selectedVisibility ?? 'private');
    $tagValue = old('tags', $tagString ?? '');
@endphp

<section class="ah-panel p-4 sm:p-5 lg:p-6">
    <form method="POST" action="{{ $action }}" class="space-y-4 sm:space-y-5">
        @csrf
        @if (in_array($method, ['PUT', 'PATCH'], true))
            @method($method)
        @endif

        <div class="grid gap-3 sm:gap-4 lg:grid-cols-2">
            <div class="lg:col-span-2">
                <label for="title" class="mb-1.5 block text-xs font-semibold text-cyan-200">Judul Link</label>
                <input id="title" name="title" type="text" value="{{ old('title', data_get($link, 'title', '')) }}" class="ah-input" placeholder="Contoh: Dashboard Campaign Q3" required>
            </div>

            <div class="lg:col-span-2">
                <label for="url" class="mb-1.5 block text-xs font-semibold text-cyan-200">URL</label>
                <input id="url" name="url" type="url" value="{{ old('url', data_get($link, 'url', '')) }}" class="ah-input" placeholder="https://example.com" required>
            </div>

            <div
                x-data="categoryQuickAdd({
                    storeUrl: @js(route('app.categories.quick-store')),
                    csrfToken: @js(csrf_token()),
                })"
                data-store-url="{{ route('app.categories.quick-store') }}"
            >
                <label for="category_id" class="mb-1.5 block text-xs font-semibold text-cyan-200">Kategori</label>

                <select
                    id="category_id"
                    name="category_id"
                    class="ah-select"
                    required
                    x-on:change="onSelectChange($event)"
                >
                    <option value="">Pilih kategori</option>
                    <option value="__new__">+ Tambah Kategori Baru</option>
                    <option value="" disabled>─────────────────────</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <p x-show="successMsg" x-cloak x-text="successMsg" class="mt-1.5 text-xs text-emerald-400"></p>

                <div x-show="showNew" x-cloak class="mt-2.5 rounded-xl border border-white/10 bg-white/5 p-3 space-y-2.5">
                    <p class="text-xs font-semibold text-cyan-200">Kategori Baru</p>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            x-model="newName"
                            placeholder="Nama kategori..."
                            maxlength="100"
                            class="ah-input flex-1"
                            x-on:keydown.enter.prevent="save()"
                            x-ref="nameInput"
                        >
                        <button
                            type="button"
                            x-on:click="save()"
                            :disabled="saving || !newName.trim()"
                            class="ah-accent-btn shrink-0 disabled:opacity-50"
                        >
                            <span x-show="!saving">Simpan</span>
                            <span x-show="saving">Menyimpan...</span>
                        </button>
                        <button
                            type="button"
                            x-on:click="cancel()"
                            class="ah-secondary-btn shrink-0"
                        >Batal</button>
                    </div>
                    <p x-show="errorMsg" x-cloak x-text="errorMsg" class="text-xs text-rose-400"></p>
                </div>
            </div>

            <div>
                <label for="visibility" class="mb-1.5 block text-xs font-semibold text-cyan-200">Visibility</label>
                <select id="visibility" name="visibility" class="ah-select">
                    <option value="private" @selected($selectedVisibility === 'private')>Private</option>
                    <option value="shared" @selected($selectedVisibility === 'shared')>Shared</option>
                </select>
                <p class="mt-1.5 text-xs text-slate-500">Private hanya untuk Anda. Shared bisa dilihat user login lain.</p>
            </div>

            <div class="lg:col-span-2">
                <label for="description" class="mb-1.5 block text-xs font-semibold text-cyan-200">Catatan Singkat</label>
                <textarea id="description" name="description" rows="3" class="ah-textarea" placeholder="Opsional. Tambahkan konteks singkat agar link mudah dikenali.">{{ old('description', data_get($link, 'description', '')) }}</textarea>
            </div>

            <div class="lg:col-span-2">
                <label for="tags" class="mb-1.5 block text-xs font-semibold text-cyan-200">Tag</label>
                <input id="tags" name="tags" type="text" value="{{ $tagValue }}" class="ah-input" placeholder="Contoh: proposal, marketing, client-a">
                <p class="mt-1.5 text-xs text-slate-500">Pisahkan beberapa tag dengan koma.</p>
            </div>

            @if ($isCreate ?? false)
            <div class="lg:col-span-2">
                <label class="inline-flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="add_to_favorites"
                        value="1"
                        {{ old('add_to_favorites') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400/30"
                    >
                    <span class="text-xs text-slate-400">Tambahkan ke favorit setelah disimpan</span>
                </label>
            </div>
            @endif
        </div>

        <div class="flex gap-2.5 pt-0.5 sm:gap-3">
            <button type="submit" class="ah-accent-btn justify-center">Simpan</button>
            <a href="{{ route('dashboard') }}" class="ah-secondary-btn justify-center">Batal</a>
        </div>
    </form>
</section>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('categoryQuickAdd', (config = {}) => ({
        showNew: false,
        newName: '',
        saving: false,
        errorMsg: '',
        successMsg: '',
        storeUrl: '',
        csrfToken: '',

        init() {
            this.storeUrl = config.storeUrl || this.$root.dataset.storeUrl || '';
            this.csrfToken = config.csrfToken || '';
        },

        onSelectChange(event) {
            if (event.target.value !== '__new__') return;
            event.target.value = '';
            this.showNew = true;
            this.errorMsg = '';
            this.successMsg = '';
            this.$nextTick(() => this.$refs.nameInput?.focus());
        },

        cancel() {
            this.showNew = false;
            this.newName = '';
            this.errorMsg = '';
        },

        save() {
            if (!this.newName.trim() || this.saving) return;
            if (!this.storeUrl || !this.csrfToken) {
                this.errorMsg = 'Endpoint kategori tidak tersedia. Muat ulang halaman lalu coba lagi.';
                return;
            }

            this.saving = true;
            this.errorMsg = '';
            this.successMsg = '';

            fetch(this.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name: this.newName.trim() }),
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    this.errorMsg = data.errors?.name?.[0] ?? 'Gagal menyimpan kategori.';
                    return;
                }
                const select = document.getElementById('category_id');
                const hasOption = Array.from(select.options).some(option => option.value === String(data.id));

                if (!hasOption) {
                    const opt = new Option(data.name, data.id, true, true);
                    select.add(opt, select.options.length - 2);
                }

                select.value = data.id;
                this.successMsg = data.existing
                    ? 'Kategori "' + data.name + '" sudah ada dan langsung dipilih.'
                    : 'Kategori "' + data.name + '" berhasil ditambahkan dan dipilih.';
                this.newName = '';
                this.showNew = false;
            })
            .catch(() => {
                this.errorMsg = 'Terjadi kesalahan jaringan. Coba lagi.';
            })
            .finally(() => {
                this.saving = false;
            });
        },
    }));
});
</script>
