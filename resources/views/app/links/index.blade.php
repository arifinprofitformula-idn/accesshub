<x-internal-app-layout title="Link Manager | AccessHub" eyebrow="Link Manager" heading="Semua Link Kerja" subheading="Simpan, cari, dan buka link penting tim dengan pengalaman yang cepat di desktop maupun HP.">
    <div x-data="{ copied: null, submitting: false }" class="space-y-6">
        <section class="ah-panel p-4 sm:p-6">
            <form method="GET" action="{{ route('app.links.index') }}" class="space-y-4" x-on:submit="submitting = true">
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-semibold text-slate-800">Pencarian Cepat</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="Cari judul, URL, kategori, tag, atau platform..." class="ah-input-lg">
                    </div>
                    <div class="ah-soft-card">
                        <p class="text-sm font-semibold text-slate-900">Tips Penggunaan</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan kata kunci seperti nama klien, campaign, landing page, atau platform untuk menemukan link lebih cepat.</p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <select name="category" class="ah-select">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="ah-select">
                        <option value="">Semua status</option>
                        <option value="active" @selected(($filters['status'] ?? null) === 'active')>Aktif</option>
                        <option value="needs_review" @selected(($filters['status'] ?? null) === 'needs_review')>Perlu Dicek</option>
                        <option value="archived" @selected(($filters['status'] ?? null) === 'archived')>Arsip</option>
                    </select>

                    <select name="priority" class="ah-select">
                        <option value="">Semua prioritas</option>
                        <option value="normal" @selected(($filters['priority'] ?? null) === 'normal')>Biasa</option>
                        <option value="important" @selected(($filters['priority'] ?? null) === 'important')>Penting</option>
                        <option value="critical" @selected(($filters['priority'] ?? null) === 'critical')>Sangat Penting</option>
                    </select>

                    <select name="platform" class="ah-select">
                        <option value="">Semua platform</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform }}" @selected(($filters['platform'] ?? null) === $platform)>{{ $platform }}</option>
                        @endforeach
                    </select>

                    <label class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                        <span class="font-medium">Favorit saja</span>
                        <input type="checkbox" name="favorites" value="1" @checked(($filters['favorites'] ?? false)) class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    </label>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="ah-accent-btn" x-bind:disabled="submitting">
                        <span x-show="!submitting">Terapkan Filter</span>
                        <span x-show="submitting">Memuat...</span>
                    </button>
                    <a href="{{ route('app.links.index') }}" class="ah-secondary-btn">Reset</a>
                </div>
            </form>
        </section>

        <section class="grid gap-4 lg:hidden">
            @forelse ($links as $link)
                <article class="ah-panel p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap gap-2">
                                <span class="ah-badge bg-sky-50 text-sky-700">{{ $link->platform }}</span>
                                <span class="ah-badge bg-slate-100 text-slate-600">{{ $link->category?->name ?? 'Tanpa kategori' }}</span>
                            </div>
                            <h2 class="mt-3 text-lg font-semibold text-slate-950">{{ $link->title }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $link->description ?: 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <form method="POST" action="{{ route('app.links.favorite.toggle', $link) }}">
                            @csrf
                            <button type="submit" class="{{ in_array($link->id, $favoriteIds, true) ? 'border-amber-300 bg-amber-50 text-amber-700' : 'border-slate-200 bg-white text-slate-500' }} rounded-2xl border px-3 py-2 text-xs font-semibold">
                                {{ in_array($link->id, $favoriteIds, true) ? 'Pinned' : 'Pin' }}
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span @class([
                            'ah-badge',
                            'bg-emerald-50 text-emerald-700' => $link->status === 'active',
                            'bg-amber-50 text-amber-700' => $link->status === 'needs_review',
                            'bg-slate-100 text-slate-600' => $link->status === 'archived',
                        ])>{{ ucfirst(str_replace('_', ' ', $link->status)) }}</span>
                        <span @class([
                            'ah-badge',
                            'bg-sky-50 text-sky-700' => $link->priority === 'normal',
                            'bg-amber-50 text-amber-700' => $link->priority === 'important',
                            'bg-rose-50 text-rose-700' => $link->priority === 'critical',
                        ])>{{ ucfirst($link->priority) }}</span>
                        <span class="ah-badge bg-violet-50 text-violet-700">{{ ucfirst($link->visibility) }}</span>
                    </div>

                    @if ($link->tags->isNotEmpty())
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($link->tags as $tag)
                                <span class="ah-badge bg-slate-100 text-slate-600">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4 grid gap-2 text-xs text-slate-500">
                        <p>PIC: {{ $link->owner_name }}</p>
                        <p>Favorite: {{ $link->favorites_count }}</p>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <a href="{{ route('app.links.open', $link) }}" target="_blank" rel="noopener noreferrer" class="ah-primary-btn">
                            Open Link
                        </a>
                        <button type="button" x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}" class="ah-secondary-btn">
                            <span x-show="copied !== {{ $link->id }}">Copy URL</span>
                            <span x-show="copied === {{ $link->id }}">Copied</span>
                        </button>
                    </div>
                </article>
            @empty
                <div class="ah-empty-state">
                    Tidak ada link yang cocok dengan filter saat ini.
                </div>
            @endforelse
        </section>

        <section class="ah-table-shell hidden lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/90 text-left text-xs uppercase tracking-[0.18em] text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Link</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Prioritas</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">PIC</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($links as $link)
                            <tr class="align-top">
                                <td class="px-6 py-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap gap-2">
                                                <span class="ah-badge bg-sky-50 text-sky-700">{{ $link->platform }}</span>
                                                @foreach ($link->tags as $tag)
                                                    <span class="ah-badge bg-slate-100 text-slate-600">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                            <p class="mt-3 font-semibold text-slate-950">{{ $link->title }}</p>
                                            <p class="mt-1 line-clamp-2 max-w-xl text-sm text-slate-500">{{ $link->description ?: 'Tidak ada deskripsi.' }}</p>
                                        </div>
                                        <form method="POST" action="{{ route('app.links.favorite.toggle', $link) }}">
                                            @csrf
                                            <button type="submit" class="{{ in_array($link->id, $favoriteIds, true) ? 'border-amber-300 bg-amber-50 text-amber-700' : 'border-slate-200 bg-white text-slate-500' }} rounded-2xl border px-3 py-2 text-xs font-semibold">
                                                {{ in_array($link->id, $favoriteIds, true) ? 'Pinned' : 'Pin' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="ah-badge bg-slate-100 text-slate-600">{{ $link->category?->name ?? 'Tanpa kategori' }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <span @class([
                                        'ah-badge',
                                        'bg-sky-50 text-sky-700' => $link->priority === 'normal',
                                        'bg-amber-50 text-amber-700' => $link->priority === 'important',
                                        'bg-rose-50 text-rose-700' => $link->priority === 'critical',
                                    ])>{{ ucfirst($link->priority) }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        <span @class([
                                            'ah-badge',
                                            'bg-emerald-50 text-emerald-700' => $link->status === 'active',
                                            'bg-amber-50 text-amber-700' => $link->status === 'needs_review',
                                            'bg-slate-100 text-slate-600' => $link->status === 'archived',
                                        ])>{{ ucfirst(str_replace('_', ' ', $link->status)) }}</span>
                                        <span class="ah-badge bg-violet-50 text-violet-700">{{ ucfirst($link->visibility) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-slate-600">{{ $link->owner_name }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('app.links.open', $link) }}" target="_blank" rel="noopener noreferrer" class="ah-primary-btn px-4 py-2 text-xs">
                                            Open
                                        </a>
                                        <button type="button" x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}" class="ah-secondary-btn px-4 py-2 text-xs">
                                            <span x-show="copied !== {{ $link->id }}">Copy URL</span>
                                            <span x-show="copied === {{ $link->id }}">Copied</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12">
                                    <div class="ah-empty-state">
                                        Tidak ada link yang cocok dengan filter saat ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>{{ $links->links() }}</div>
    </div>
</x-internal-app-layout>
