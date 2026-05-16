@php
    $selectedCategory = old('category_id', data_get($link, 'category_id'));
    $selectedVisibility = old('visibility', $selectedVisibility ?? 'private');
    $tagValue = old('tags', $tagString ?? '');
@endphp

<section class="ah-panel p-5 sm:p-6 lg:p-7">
    <form method="POST" action="{{ $action }}" class="space-y-7">
        @csrf
        @if (in_array($method, ['PUT', 'PATCH'], true))
            @method($method)
        @endif

        <div class="grid gap-5 lg:grid-cols-2 lg:gap-6">
            <div class="lg:col-span-2">
                <label for="title" class="mb-2 block text-sm font-semibold text-cyan-200">Judul Link</label>
                <input id="title" name="title" type="text" value="{{ old('title', data_get($link, 'title', '')) }}" class="ah-input-lg" placeholder="Contoh: Dashboard Campaign Q3" required>
            </div>

            <div class="lg:col-span-2">
                <label for="url" class="mb-2 block text-sm font-semibold text-cyan-200">URL</label>
                <input id="url" name="url" type="url" value="{{ old('url', data_get($link, 'url', '')) }}" class="ah-input-lg" placeholder="https://example.com" required>
            </div>

            <div>
                <label for="category_id" class="mb-2 block text-sm font-semibold text-cyan-200">Kategori</label>
                <select id="category_id" name="category_id" class="ah-select" required>
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="visibility" class="mb-2 block text-sm font-semibold text-cyan-200">Visibility</label>
                <select id="visibility" name="visibility" class="ah-select">
                    <option value="private" @selected($selectedVisibility === 'private')>Private</option>
                    <option value="shared" @selected($selectedVisibility === 'shared')>Shared</option>
                </select>
                <p class="mt-2 text-xs text-slate-400">Private hanya untuk Anda. Shared bisa dilihat user login lain, tetapi tetap tidak bisa mereka edit.</p>
            </div>

            <div class="lg:col-span-2">
                <label for="description" class="mb-2 block text-sm font-semibold text-cyan-200">Catatan Singkat</label>
                <textarea id="description" name="description" rows="4" class="ah-textarea" placeholder="Opsional. Tambahkan konteks singkat agar link mudah dikenali.">{{ old('description', data_get($link, 'description', '')) }}</textarea>
            </div>

            <div class="lg:col-span-2">
                <label for="tags" class="mb-2 block text-sm font-semibold text-cyan-200">Tag</label>
                <input id="tags" name="tags" type="text" value="{{ $tagValue }}" class="ah-input" placeholder="Contoh: proposal, marketing, client-a">
                <p class="mt-2 text-xs text-slate-400">Pisahkan beberapa tag dengan koma.</p>
            </div>

            @if ($isCreate ?? false)
            <div class="lg:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-cyan-200">Favorit</label>
                <label class="inline-flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="add_to_favorites"
                        value="1"
                        {{ old('add_to_favorites') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-white/20 bg-white/5 text-cyan-400 focus:ring-cyan-400/30"
                    >
                    <span class="text-sm text-slate-300">Tambahkan ke favorit setelah disimpan</span>
                </label>
            </div>
            @endif
        </div>

        <div class="flex flex-col gap-3 pt-1 sm:flex-row">
            <button type="submit" class="ah-accent-btn justify-center">Simpan Link</button>
            <a href="{{ route('dashboard') }}" class="ah-secondary-btn justify-center">Batal</a>
        </div>
    </form>
</section>
