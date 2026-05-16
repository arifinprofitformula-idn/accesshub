<x-internal-app-layout
    title="AccessHub Dashboard"
    eyebrow="Dashboard"
    heading="AccessHub"
    subheading="Cari dan buka link penting pekerjaan dengan cepat."
>
    <div x-data="{ copied: null }" class="space-y-7 sm:space-y-8">
        <section class="ah-panel p-4 sm:p-6 lg:p-7">
            <form method="GET" action="{{ route('dashboard') }}" class="space-y-5">
                <div class="grid gap-4 lg:grid-cols-[minmax(0,1.8fr)_220px_auto_auto] lg:gap-5">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-semibold text-cyan-200">Cari Link</label>
                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Cari dokumen, spreadsheet, website, campaign..."
                            class="ah-input-lg"
                        >
                    </div>

                    <div>
                        <label for="category" class="mb-2 block text-sm font-semibold text-cyan-200">Kategori</label>
                        <select id="category" name="category" class="ah-select">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="flex items-end">
                        <span class="sr-only">Favorit saja</span>
                        <span class="ah-filter-chip">
                            <span class="font-medium">Favorit saja</span>
                            <input type="checkbox" name="favorites" value="1" @checked(($filters['favorites'] ?? false)) class="rounded border-slate-500 bg-slate-950 text-cyan-400 focus:ring-cyan-500">
                        </span>
                    </label>

                    <div class="grid items-end gap-3 sm:grid-cols-2 lg:flex">
                        <button type="submit" class="ah-accent-btn w-full sm:w-auto">Search</button>
                        <a href="{{ route('dashboard') }}" class="ah-secondary-btn w-full justify-center sm:w-auto">Reset</a>
                    </div>
                </div>
            </form>
        </section>

        <section class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr] lg:gap-5">
            <div class="ah-panel p-5 sm:p-6 lg:p-7">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-300">Quick Access</p>
                        <h2 class="mt-1 text-xl font-semibold text-white">Semua link penting ada di satu tempat</h2>
                        <p class="mt-2 max-w-xl text-sm leading-6 text-slate-300">Gunakan pencarian di atas untuk menemukan link kerja dalam hitungan detik, lalu simpan link baru saat dibutuhkan.</p>
                    </div>
                    @can('create', \App\Models\Link::class)
                        <a href="{{ route('app.links.create') }}" class="ah-accent-btn w-full justify-center sm:w-auto">+ Tambah Link</a>
                    @endcan
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <article class="rounded-[1.4rem] border border-cyan-300/15 bg-cyan-400/10 p-4 sm:p-5">
                        <p class="text-sm font-medium text-slate-300">Total Link</p>
                        <p class="mt-2 text-3xl font-semibold text-white">{{ $stats['total_links'] }}</p>
                    </article>
                    <article class="rounded-[1.4rem] border border-violet-300/15 bg-violet-400/10 p-4 sm:p-5">
                        <p class="text-sm font-medium text-slate-300">Link Favorit</p>
                        <p class="mt-2 text-3xl font-semibold text-white">{{ $stats['favorite_links'] }}</p>
                    </article>
                    <article class="rounded-[1.4rem] border border-amber-300/15 bg-amber-300/10 p-4 sm:p-5">
                        <p class="text-sm font-medium text-slate-300">Kategori</p>
                        <p class="mt-2 text-3xl font-semibold text-white">{{ $stats['categories'] }}</p>
                    </article>
                </div>
            </div>

            <div class="ah-panel p-5 sm:p-6 lg:p-7">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-300">Recently Added</p>
                        <h2 class="mt-1 text-xl font-semibold text-white">Tambahan terbaru</h2>
                    </div>
                    <a href="{{ route('app.links.index') }}" class="text-sm font-medium text-cyan-300 hover:text-cyan-200">Lihat semua</a>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse ($recentLinks as $link)
                        @php
                            $host = parse_url($link->url, PHP_URL_HOST) ?: $link->url;
                            $host = str_replace('www.', '', $host);
                        @endphp
                        <div class="ah-touch-card rounded-[1.35rem] border border-white/8 bg-white/5 p-4 sm:p-4.5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">{{ $link->title }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-400">{{ $host }}</p>
                                </div>
                                <span class="ah-badge bg-white/8 text-slate-300">{{ $link->category?->name ?? 'Tanpa kategori' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="ah-empty-state">
                            <div class="ah-empty-state-spot">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                    <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768" />
                                    <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0" />
                                    <path d="m8.5 15.5 7-7" />
                                </svg>
                            </div>
                            <p class="mt-5 text-base font-semibold text-white">Belum ada link terbaru</p>
                            <p class="mt-2 leading-6 text-slate-400">Belum ada link tersimpan. Tambahkan link pertama Anda agar lebih mudah ditemukan nanti.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        @include('app.links.partials.list', ['emptyMessage' => 'Belum ada link tersimpan. Tambahkan link pertama Anda agar lebih mudah ditemukan nanti.'])
    </div>
</x-internal-app-layout>
