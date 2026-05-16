<section class="grid gap-4 lg:hidden">
@php
    $manageMode = $manageMode ?? false;
@endphp

@forelse ($links as $link)
        @php
            $host = parse_url($link->url, PHP_URL_HOST) ?: $link->url;
            $host = str_replace('www.', '', $host);
            $isFavorite = in_array($link->id, $favoriteIds, true);
            $visibilityLabel = $link->visibility === 'private' ? 'Private' : 'Shared';
        @endphp
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

            @if ($link->tags->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($link->tags as $tag)
                        <span class="ah-badge bg-violet-400/15 text-violet-100">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 grid grid-cols-2 gap-3">
                <a href="{{ route('app.links.open', $link) }}" target="_blank" rel="noopener noreferrer" class="ah-primary-btn justify-center">
                    Buka
                </a>
                <button type="button" x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}" class="ah-secondary-btn justify-center">
                    <span x-show="copied !== {{ $link->id }}">Copy</span>
                    <span x-show="copied === {{ $link->id }}">Copied</span>
                </button>
            </div>

            @can('update', $link)
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <a href="{{ route('app.links.edit', $link) }}" class="ah-secondary-btn w-full justify-center">Edit</a>
                    <form method="POST" action="{{ route('app.links.destroy', $link) }}" onsubmit="return confirm('{{ $manageMode ? 'Hapus asset link ini dari daftar kelola? Link akan diarsipkan.' : 'Arsipkan link ini?' }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ah-secondary-btn w-full justify-center text-rose-100">
                            {{ $manageMode ? 'Hapus' : 'Arsip' }}
                        </button>
                    </form>
                </div>
            @endcan
        </article>
    @empty
        <div class="ah-empty-state">
            <div class="ah-empty-state-spot">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                    <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768" />
                    <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0" />
                    <path d="m8.5 15.5 7-7" />
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

<section class="ah-table-shell hidden lg:block">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/8 text-sm">
            <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                <tr>
                    <th class="px-6 py-4">Link</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Tag</th>
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
                        <td class="px-6 py-5">
                            <div class="flex max-w-[220px] flex-wrap gap-2">
                                @forelse ($link->tags as $tag)
                                    <span class="ah-badge bg-violet-400/15 text-violet-100">{{ $tag->name }}</span>
                                @empty
                                    <span class="text-slate-500">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="ah-badge bg-white/8 text-slate-300">{{ $visibilityLabel }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('app.links.open', $link) }}" target="_blank" rel="noopener noreferrer" class="ah-primary-btn px-4 py-2 text-xs">
                                    Buka
                                </a>
                                <button type="button" x-on:click="navigator.clipboard.writeText(@js($link->url)); copied = {{ $link->id }}" class="ah-secondary-btn px-4 py-2 text-xs">
                                    <span x-show="copied !== {{ $link->id }}">Copy</span>
                                    <span x-show="copied === {{ $link->id }}">Copied</span>
                                </button>
                                @can('update', $link)
                                    <a href="{{ route('app.links.edit', $link) }}" class="ah-secondary-btn px-4 py-2 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('app.links.destroy', $link) }}" onsubmit="return confirm('{{ $manageMode ? 'Hapus asset link ini dari daftar kelola? Link akan diarsipkan.' : 'Arsipkan link ini?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ah-secondary-btn px-4 py-2 text-xs text-rose-100">
                                            {{ $manageMode ? 'Hapus' : 'Arsip' }}
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12">
                            <div class="ah-empty-state">
                                <div class="ah-empty-state-spot">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                                        <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768" />
                                        <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0" />
                                        <path d="m8.5 15.5 7-7" />
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

<div>{{ $links->links() }}</div>
