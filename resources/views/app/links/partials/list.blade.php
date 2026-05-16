@php
    $manageMode = $manageMode ?? false;
    $dashboardMode = $dashboardMode ?? false;
    $showTags = $showTags ?? true;
@endphp

{{-- Mobile / small screen cards --}}
<section class="{{ ($dashboardMode || $manageMode) ? 'grid gap-2.5 grid-cols-1 sm:grid-cols-2' : 'grid gap-4' }} lg:hidden">
@forelse ($links as $link)
    @php
        $host = parse_url($link->url, PHP_URL_HOST) ?: $link->url;
        $host = str_replace('www.', '', $host);
        $isFavorite = in_array($link->id, $favoriteIds, true);
        $visibilityLabel = $link->visibility === 'private' ? 'Private' : 'Shared';
    @endphp

    @if ($dashboardMode || $manageMode)
    {{-- Compact card (dashboard & manage) --}}
    <article class="ah-panel rounded-xl p-3">
        <div class="flex items-start gap-2">
            <div class="min-w-0 flex-1">
                <p class="truncate text-[10px] font-semibold uppercase tracking-wider text-cyan-300/70">{{ $link->category?->name ?? 'Tanpa kategori' }}</p>
                <h2 class="mt-0.5 line-clamp-1 text-[13px] font-semibold leading-snug text-white">{{ $link->title }}</h2>
                <p class="mt-0.5 line-clamp-1 text-[11px] leading-snug text-slate-400">{{ $link->description ?: $host }}</p>
            </div>
            <div class="flex shrink-0 items-center gap-1">
                @can('favorite', $link)
                    <form method="POST" action="{{ route('app.links.favorite.toggle', $link) }}">
                        @csrf
                        <button type="submit" title="{{ $isFavorite ? 'Unpin' : 'Pin' }}" class="{{ $isFavorite ? 'border-amber-300/25 bg-amber-300/15 text-amber-200' : 'border-white/10 bg-white/5 text-slate-400' }} inline-flex h-7 w-7 items-center justify-center rounded-lg border transition hover:bg-white/10">
                            <svg viewBox="0 0 24 24" fill="{{ $isFavorite ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </button>
                    </form>
                @endcan
                <a
                    href="{{ route('app.links.open', $link) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="Buka"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-cyan-300/20 bg-cyan-400/12 text-cyan-100 transition hover:bg-cyan-400/20"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                        <path d="M7 17 17 7"/><path d="M8.5 7H17v8.5"/>
                    </svg>
                </a>
                <button
                    type="button"
                    title="Copy"
                    x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/12"
                >
                    <span x-show="copied !== {{ $link->id }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                            <rect x="9" y="9" width="10" height="10" rx="2"/><path d="M6 15V7a2 2 0 0 1 2-2h8"/>
                        </svg>
                    </span>
                    <span x-show="copied === {{ $link->id }}" class="text-cyan-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                            <path d="m5 13 4 4L19 7"/>
                        </svg>
                    </span>
                </button>
                @if ($manageMode)
                    @can('update', $link)
                        <a
                            href="{{ route('app.links.edit', $link) }}"
                            title="Edit"
                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/12"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                <path d="m16.862 5.487 1.65 1.65a1.75 1.75 0 0 1 0 2.475L10 18.125 6 19l.875-4 8.512-8.513a1.75 1.75 0 0 1 2.475 0Z"/>
                            </svg>
                        </a>
                    @endcan
                    @can('delete', $link)
                        <form method="POST" action="{{ route('app.links.destroy', $link) }}" onsubmit="return confirm({{ Js::from('Hapus asset link ini dari daftar kelola? Link akan diarsipkan.') }})">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                title="Hapus"
                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-rose-300/20 bg-rose-400/10 text-rose-300 transition hover:bg-rose-400/18"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                    <path d="M4 7h16"/><path d="m9 7 .75-2h4.5L15 7"/><path d="M7.75 7 8.5 18.25A1.75 1.75 0 0 0 10.246 20h3.508A1.75 1.75 0 0 0 15.5 18.25L16.25 7"/><path d="M10 11v5"/><path d="M14 11v5"/>
                                </svg>
                            </button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>
    </article>

    @else
    {{-- Standard card (non-dashboard) --}}
    <article class="ah-panel ah-touch-card p-5 sm:p-6">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="flex flex-wrap gap-2">
                    <span class="ah-badge bg-cyan-400/15 text-cyan-200">{{ $link->category?->name ?? 'Tanpa kategori' }}</span>
                    <span class="ah-badge bg-white/8 text-slate-300">{{ $visibilityLabel }}</span>
                </div>
                <h2 class="mt-3 text-lg font-semibold text-white">{{ $link->title }}</h2>
                <p class="mt-1 truncate text-sm text-slate-400">{{ $host }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-300">{{ $link->description ?: 'Tidak ada catatan singkat.' }}</p>
            </div>

            @can('favorite', $link)
                <form method="POST" action="{{ route('app.links.favorite.toggle', $link) }}">
                    @csrf
                    <button type="submit" class="{{ $isFavorite ? 'border-amber-300/25 bg-amber-300/15 text-amber-100' : 'border-white/10 bg-white/5 text-slate-300' }} inline-flex min-h-11 min-w-16 items-center justify-center rounded-2xl border px-3 py-2.5 text-xs font-semibold">
                        {{ $isFavorite ? 'Pinned' : 'Pin' }}
                    </button>
                </form>
            @endcan
        </div>

        @if ($showTags && $link->relationLoaded('tags') && $link->tags->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($link->tags as $tag)
                    <span class="ah-badge bg-violet-400/15 text-violet-100">{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        <div class="mt-6 flex items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('app.links.open', $link) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="Buka"
                    aria-label="Buka"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-400/12 text-cyan-100 transition hover:border-cyan-300/35 hover:bg-cyan-400/18"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M7 17 17 7"/><path d="M8.5 7H17v8.5"/>
                    </svg>
                </a>
                <button
                    type="button"
                    title="Copy Link"
                    aria-label="Copy Link"
                    x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:border-white/20 hover:bg-white/10"
                >
                    <span x-show="copied !== {{ $link->id }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <rect x="9" y="9" width="10" height="10" rx="2"/><path d="M6 15V7a2 2 0 0 1 2-2h8"/>
                        </svg>
                    </span>
                    <span x-show="copied === {{ $link->id }}" class="text-cyan-200">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m5 13 4 4L19 7"/>
                        </svg>
                    </span>
                </button>
                @can('update', $link)
                    @unless ($dashboardMode)
                    <a
                        href="{{ route('app.links.edit', $link) }}"
                        title="Edit"
                        aria-label="Edit"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:border-white/20 hover:bg-white/10"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m16.862 5.487 1.65 1.65a1.75 1.75 0 0 1 0 2.475L10 18.125 6 19l.875-4 8.512-8.513a1.75 1.75 0 0 1 2.475 0Z"/>
                        </svg>
                    </a>
                    @endunless
                @endcan
                @can('delete', $link)
                    @unless ($dashboardMode)
                    <form method="POST" action="{{ route('app.links.destroy', $link) }}" onsubmit="return confirm({{ Js::from($manageMode ? 'Hapus asset link ini dari daftar kelola? Link akan diarsipkan.' : 'Arsipkan link ini?') }})">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            title="{{ $manageMode ? 'Hapus' : 'Arsipkan' }}"
                            aria-label="{{ $manageMode ? 'Hapus' : 'Arsipkan' }}"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-rose-300/20 bg-rose-400/10 text-rose-100 transition hover:border-rose-300/35 hover:bg-rose-400/18"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                @if ($manageMode)
                                    <path d="M4 7h16"/><path d="m9 7 .75-2h4.5L15 7"/><path d="M7.75 7 8.5 18.25A1.75 1.75 0 0 0 10.246 20h3.508A1.75 1.75 0 0 0 15.5 18.25L16.25 7"/><path d="M10 11v5"/><path d="M14 11v5"/>
                                @else
                                    <path d="M4.75 7.75h14.5"/><path d="M6.75 7.75v9.5A1.75 1.75 0 0 0 8.5 19h7a1.75 1.75 0 0 0 1.75-1.75v-9.5"/><path d="M9 7.75V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5v2.25"/><path d="M10 11.25h4"/>
                                @endif
                            </svg>
                        </button>
                    </form>
                    @endunless
                @endcan
            </div>
        </div>
    </article>
    @endif

@empty
    <div class="ah-empty-state {{ ($dashboardMode || $manageMode) ? 'sm:col-span-2' : '' }}">
        <div class="ah-empty-state-spot">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768"/>
                <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0"/>
                <path d="m8.5 15.5 7-7"/>
            </svg>
        </div>
        <p class="mt-5 text-base font-semibold text-white">Link belum ditemukan</p>
        <p class="mt-2 max-w-md mx-auto leading-6 text-slate-400">{{ $emptyMessage }}</p>
        @can('create', \App\Models\Link::class)
            <div class="mt-5">
                <a href="{{ route('app.links.create') }}" class="ah-accent-btn justify-center">Tambah Link Pertama</a>
            </div>
        @endcan
    </div>
@endforelse
</section>

{{-- Desktop / large screen view --}}
@if ($dashboardMode)
{{-- Dashboard: compact card grid --}}
<section class="hidden lg:grid lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5">
    @forelse ($links as $link)
        @php
            $host = parse_url($link->url, PHP_URL_HOST) ?: $link->url;
            $host = str_replace('www.', '', $host);
            $isFavorite = in_array($link->id, $favoriteIds, true);
        @endphp
        <article class="ah-panel p-3">
            <div class="flex items-start gap-2">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[10px] font-semibold uppercase tracking-wider text-cyan-300/70">{{ $link->category?->name ?? 'Tanpa kategori' }}</p>
                    <h2 class="mt-0.5 line-clamp-1 text-[13px] font-semibold leading-snug text-white" title="{{ $link->title }}">{{ $link->title }}</h2>
                    <p class="mt-0.5 line-clamp-1 text-[11px] leading-snug text-slate-400" title="{{ $link->description ?: $host }}">{{ $link->description ?: $host }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-1">
                    @can('favorite', $link)
                        <form method="POST" action="{{ route('app.links.favorite.toggle', $link) }}">
                            @csrf
                            <button type="submit" title="{{ $isFavorite ? 'Unpin' : 'Pin' }}" class="{{ $isFavorite ? 'border-amber-300/25 bg-amber-300/15 text-amber-200' : 'border-white/10 bg-white/5 text-slate-400' }} inline-flex h-7 w-7 items-center justify-center rounded-lg border transition hover:bg-white/10">
                                <svg viewBox="0 0 24 24" fill="{{ $isFavorite ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </button>
                        </form>
                    @endcan
                    <a
                        href="{{ route('app.links.open', $link) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="Buka"
                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-cyan-300/20 bg-cyan-400/12 text-cyan-100 transition hover:bg-cyan-400/20"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                            <path d="M7 17 17 7"/><path d="M8.5 7H17v8.5"/>
                        </svg>
                    </a>
                    <button
                        type="button"
                        title="Copy Link"
                        x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}"
                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/12"
                    >
                        <span x-show="copied !== {{ $link->id }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                <rect x="9" y="9" width="10" height="10" rx="2"/><path d="M6 15V7a2 2 0 0 1 2-2h8"/>
                            </svg>
                        </span>
                        <span x-show="copied === {{ $link->id }}" class="text-cyan-300">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                <path d="m5 13 4 4L19 7"/>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </article>
    @empty
        <div class="ah-empty-state lg:col-span-3 xl:col-span-4 2xl:col-span-5">
            <div class="ah-empty-state-spot">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                    <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768"/>
                    <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0"/>
                    <path d="m8.5 15.5 7-7"/>
                </svg>
            </div>
            <p class="mt-5 text-base font-semibold text-white">Belum ada link untuk ditampilkan</p>
            <p class="mt-2 max-w-md mx-auto leading-6 text-slate-400">{{ $emptyMessage }}</p>
            @can('create', \App\Models\Link::class)
                <div class="mt-5">
                    <a href="{{ route('app.links.create') }}" class="ah-accent-btn justify-center">Tambah Link Pertama</a>
                </div>
            @endcan
        </div>
    @endforelse
</section>

@else
{{-- Non-dashboard: standard table --}}
<section class="ah-table-shell hidden lg:block">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/8 text-sm">
            <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                <tr>
                    <th class="px-6 py-4">Link</th>
                    <th class="px-6 py-4">Kategori</th>
                    @if ($showTags)
                        <th class="px-6 py-4">Tag</th>
                    @endif
                    <th class="px-6 py-4">Visibility</th>
                    <th class="px-6 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/8 bg-transparent">
                @forelse ($links as $link)
                    @php
                        $host = parse_url($link->url, PHP_URL_HOST) ?: $link->url;
                        $host = str_replace('www.', '', $host);
                        $isFavorite = in_array($link->id, $favoriteIds, true);
                        $visibilityLabel = $link->visibility === 'private' ? 'Private' : 'Shared';
                    @endphp
                    <tr class="align-top">
                        <td class="px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="font-semibold text-white">{{ $link->title }}</p>
                                    <p class="mt-1 text-sm text-slate-400">{{ $host }}</p>
                                    <p class="mt-2 line-clamp-2 max-w-xl text-sm text-slate-300">{{ $link->description ?: 'Tidak ada catatan singkat.' }}</p>
                                </div>
                                @can('favorite', $link)
                                    <form method="POST" action="{{ route('app.links.favorite.toggle', $link) }}">
                                        @csrf
                                        <button type="submit" class="{{ $isFavorite ? 'border-amber-300/25 bg-amber-300/15 text-amber-100' : 'border-white/10 bg-white/5 text-slate-300' }} inline-flex min-h-11 min-w-16 items-center justify-center rounded-2xl border px-3 py-2.5 text-xs font-semibold">
                                            {{ $isFavorite ? 'Pinned' : 'Pin' }}
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="ah-badge bg-cyan-400/15 text-cyan-200">{{ $link->category?->name ?? 'Tanpa kategori' }}</span>
                        </td>
                        @if ($showTags)
                            <td class="px-6 py-5">
                                <div class="flex max-w-[220px] flex-wrap gap-2">
                                    @forelse ($link->tags as $tag)
                                        <span class="ah-badge bg-violet-400/15 text-violet-100">{{ $tag->name }}</span>
                                    @empty
                                        <span class="text-slate-500">-</span>
                                    @endforelse
                                </div>
                            </td>
                        @endif
                        <td class="px-6 py-5">
                            <span class="ah-badge bg-white/8 text-slate-300">{{ $visibilityLabel }}</span>
                        </td>
                        <td class="px-6 py-5">
                            @if ($manageMode)
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('app.links.open', $link) }}" target="_blank" rel="noopener noreferrer" title="Buka" aria-label="Buka" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-400/12 text-cyan-100 transition hover:border-cyan-300/35 hover:bg-cyan-400/18">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M7 17 17 7"/><path d="M8.5 7H17v8.5"/></svg>
                                    </a>
                                    <button type="button" title="Copy Link" aria-label="Copy Link" x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:border-white/20 hover:bg-white/10">
                                        <span x-show="copied !== {{ $link->id }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect x="9" y="9" width="10" height="10" rx="2"/><path d="M6 15V7a2 2 0 0 1 2-2h8"/></svg></span>
                                        <span x-show="copied === {{ $link->id }}" class="text-cyan-200"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m5 13 4 4L19 7"/></svg></span>
                                    </button>
                                    @can('update', $link)
                                        @unless ($dashboardMode)
                                        <a href="{{ route('app.links.edit', $link) }}" title="Edit" aria-label="Edit" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:border-white/20 hover:bg-white/10">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m16.862 5.487 1.65 1.65a1.75 1.75 0 0 1 0 2.475L10 18.125 6 19l.875-4 8.512-8.513a1.75 1.75 0 0 1 2.475 0Z"/></svg>
                                        </a>
                                        @endunless
                                    @endcan
                                    @can('delete', $link)
                                        @unless ($dashboardMode)
                                        <form method="POST" action="{{ route('app.links.destroy', $link) }}" onsubmit="return confirm('Hapus asset link ini dari daftar kelola? Link akan diarsipkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus" aria-label="Hapus" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-rose-300/20 bg-rose-400/10 text-rose-100 transition hover:border-rose-300/35 hover:bg-rose-400/18">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 7h16"/><path d="m9 7 .75-2h4.5L15 7"/><path d="M7.75 7 8.5 18.25A1.75 1.75 0 0 0 10.246 20h3.508A1.75 1.75 0 0 0 15.5 18.25L16.25 7"/><path d="M10 11v5"/><path d="M14 11v5"/></svg>
                                            </button>
                                        </form>
                                        @endunless
                                    @endcan
                                </div>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('app.links.open', $link) }}" target="_blank" rel="noopener noreferrer" title="Buka" aria-label="Buka" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-400/12 text-cyan-100 transition hover:border-cyan-300/35 hover:bg-cyan-400/18">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M7 17 17 7"/><path d="M8.5 7H17v8.5"/></svg>
                                    </a>
                                    <button type="button" title="Copy Link" aria-label="Copy Link" x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:border-white/20 hover:bg-white/10">
                                        <span x-show="copied !== {{ $link->id }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect x="9" y="9" width="10" height="10" rx="2"/><path d="M6 15V7a2 2 0 0 1 2-2h8"/></svg></span>
                                        <span x-show="copied === {{ $link->id }}" class="text-cyan-200"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m5 13 4 4L19 7"/></svg></span>
                                    </button>
                                    @can('update', $link)
                                        <a href="{{ route('app.links.edit', $link) }}" title="Edit" aria-label="Edit" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:border-white/20 hover:bg-white/10">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m16.862 5.487 1.65 1.65a1.75 1.75 0 0 1 0 2.475L10 18.125 6 19l.875-4 8.512-8.513a1.75 1.75 0 0 1 2.475 0Z"/></svg>
                                        </a>
                                    @endcan
                                    @can('delete', $link)
                                        <form method="POST" action="{{ route('app.links.destroy', $link) }}" onsubmit="return confirm('Arsipkan link ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Arsipkan" aria-label="Arsipkan" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-rose-300/20 bg-rose-400/10 text-rose-100 transition hover:border-rose-300/35 hover:bg-rose-400/18">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4.75 7.75h14.5"/><path d="M6.75 7.75v9.5A1.75 1.75 0 0 0 8.5 19h7a1.75 1.75 0 0 0 1.75-1.75v-9.5"/><path d="M9 7.75V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5v2.25"/><path d="M10 11.25h4"/></svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $showTags ? 5 : 4 }}" class="px-6 py-12">
                            <div class="ah-empty-state">
                                <div class="ah-empty-state-spot">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                        <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768"/>
                                        <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0"/>
                                        <path d="m8.5 15.5 7-7"/>
                                    </svg>
                                </div>
                                <p class="mt-5 text-base font-semibold text-white">Belum ada link untuk ditampilkan</p>
                                <p class="mt-2 max-w-md mx-auto leading-6 text-slate-400">{{ $emptyMessage }}</p>
                                @can('create', \App\Models\Link::class)
                                    <div class="mt-5">
                                        <a href="{{ route('app.links.create') }}" class="ah-accent-btn justify-center">Tambah Link Pertama</a>
                                    </div>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endif

<div>{{ $links->links() }}</div>
