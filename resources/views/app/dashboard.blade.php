<x-internal-app-layout title="Dashboard | AccessHub" eyebrow="Overview" heading="Dashboard Tim" subheading="Buka, cek, dan lanjut kerja lebih cepat.">
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="ah-card overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="ah-badge bg-cyan-400/15 text-cyan-200">Link</span>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 to-sky-500 text-slate-950 shadow-lg">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768" />
                            <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0" />
                            <path d="m8.5 15.5 7-7" />
                        </svg>
                    </span>
                </div>
                <p class="mt-5 text-3xl font-semibold text-white">{{ $stats['active_links'] }}</p>
                <p class="mt-1 text-sm text-slate-400">Aktif</p>
            </article>

            <article class="ah-card overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="ah-badge bg-amber-300/15 text-amber-200">Review</span>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-200 to-orange-500 text-slate-950 shadow-lg">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M12 8v4l2.5 2.5" />
                            <circle cx="12" cy="12" r="8.25" />
                        </svg>
                    </span>
                </div>
                <p class="mt-5 text-3xl font-semibold text-white">{{ $stats['needs_review_links'] }}</p>
                <p class="mt-1 text-sm text-slate-400">Perlu dicek</p>
            </article>

            <article class="ah-card overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="ah-badge bg-emerald-400/15 text-emerald-200">Access</span>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-300 to-teal-500 text-slate-950 shadow-lg">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M12 3 5 6v5c0 4.25 2.75 8.17 7 9 4.25-.83 7-4.75 7-9V6l-7-3Z" />
                            <path d="M9.75 11.5h4.5" />
                            <path d="M12 8.75v5.5" />
                        </svg>
                    </span>
                </div>
                <p class="mt-5 text-3xl font-semibold text-white">{{ $stats['access_items'] }}</p>
                <p class="mt-1 text-sm text-slate-400">Metadata</p>
            </article>

            <article class="ah-card overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="ah-badge bg-fuchsia-400/15 text-fuchsia-200">User</span>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-fuchsia-300 to-violet-500 text-slate-950 shadow-lg">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            <path d="M4.5 19.25a7.5 7.5 0 0 1 15 0" />
                        </svg>
                    </span>
                </div>
                <p class="mt-5 text-3xl font-semibold text-white">{{ $stats['active_users'] }}</p>
                <p class="mt-1 text-sm text-slate-400">Akun aktif</p>
            </article>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="ah-panel p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-300">Quick Action</p>
                        <h2 class="mt-1 text-xl font-semibold text-white">Masuk ke halaman kerja</h2>
                    </div>
                    <span class="ah-badge bg-white/8 text-slate-300">{{ $stats['categories'] }} kategori</span>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <a href="{{ route('app.links.index') }}" class="group rounded-[1.5rem] border border-cyan-300/15 bg-gradient-to-br from-cyan-400/18 to-sky-500/8 p-4 transition hover:border-cyan-300/30 hover:bg-cyan-400/20">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 to-sky-500 text-slate-950 shadow-lg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768" />
                                <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0" />
                                <path d="m8.5 15.5 7-7" />
                            </svg>
                        </span>
                        <p class="mt-4 text-base font-semibold text-white">Links</p>
                        <p class="mt-1 text-sm text-slate-300">Cari dan buka link.</p>
                    </a>

                    <a href="{{ route('app.access-items.index') }}" class="group rounded-[1.5rem] border border-emerald-300/15 bg-gradient-to-br from-emerald-400/18 to-teal-500/8 p-4 transition hover:border-emerald-300/30 hover:bg-emerald-400/20">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-300 to-teal-500 text-slate-950 shadow-lg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M12 3 5 6v5c0 4.25 2.75 8.17 7 9 4.25-.83 7-4.75 7-9V6l-7-3Z" />
                                <path d="M9.75 11.5h4.5" />
                                <path d="M12 8.75v5.5" />
                            </svg>
                        </span>
                        <p class="mt-4 text-base font-semibold text-white">Access</p>
                        <p class="mt-1 text-sm text-slate-300">Lihat metadata login.</p>
                    </a>

                    @if (auth()->user()->hasAnyRole(['super_admin', 'admin']))
                        <a href="{{ url('/admin') }}" class="group rounded-[1.5rem] border border-fuchsia-300/15 bg-gradient-to-br from-fuchsia-400/18 to-violet-500/8 p-4 transition hover:border-fuchsia-300/30 hover:bg-fuchsia-400/20">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-fuchsia-300 to-violet-500 text-slate-950 shadow-lg">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <path d="M10.5 3h3l.75 2.25 2.25.75v3l-2.25.75L13.5 12h-3l-.75-2.25L7.5 9v-3l2.25-.75L10.5 3Z" />
                                    <path d="M6 14.5h12" />
                                    <path d="M8 18h8" />
                                </svg>
                            </span>
                            <p class="mt-4 text-base font-semibold text-white">Admin</p>
                            <p class="mt-1 text-sm text-slate-300">Kelola data utama.</p>
                        </a>
                    @endif
                </div>
            </div>

            <div class="ah-panel p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-300">Favorit</p>
                <h2 class="mt-1 text-xl font-semibold text-white">Paling sering dibuka</h2>

                <div class="mt-5 space-y-3">
                    @forelse ($favorite_links as $link)
                        <a href="{{ route('app.links.open', $link) }}" target="_blank" rel="noopener noreferrer" class="block rounded-[1.35rem] border border-white/8 bg-white/5 p-4 transition hover:border-amber-300/20 hover:bg-white/8">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">{{ $link->title }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $link->platform }}</p>
                                </div>
                                <span class="ah-badge bg-amber-300/15 text-amber-200">{{ $link->favorites_count }}x</span>
                            </div>
                        </a>
                    @empty
                        <div class="ah-empty-state">
                            Belum ada favorit.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="ah-panel p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-300">Terbaru</p>
                        <h2 class="mt-1 text-xl font-semibold text-white">Link terakhir</h2>
                    </div>
                    <a href="{{ route('app.links.index') }}" class="text-sm font-medium text-cyan-300 hover:text-cyan-200">Lihat</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($recent_links as $link)
                        <div class="rounded-[1.35rem] border border-white/8 bg-white/5 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">{{ $link->title }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $link->platform }} • {{ $link->owner_name }}</p>
                                </div>
                                <span @class([
                                    'ah-badge',
                                    'bg-emerald-400/15 text-emerald-200' => $link->status === 'active',
                                    'bg-amber-300/15 text-amber-200' => $link->status === 'needs_review',
                                    'bg-slate-500/20 text-slate-300' => $link->status === 'archived',
                                ])>
                                    {{ ucfirst(str_replace('_', ' ', $link->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="ah-empty-state">
                            Belum ada link.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="ah-panel p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-300">Access</p>
                        <h2 class="mt-1 text-xl font-semibold text-white">Metadata terbaru</h2>
                    </div>
                    <a href="{{ route('app.access-items.index') }}" class="text-sm font-medium text-emerald-300 hover:text-emerald-200">Lihat</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($recent_access_items as $item)
                        <div class="rounded-[1.35rem] border border-white/8 bg-white/5 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">{{ $item->platform_name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $item->username ?: 'Username kosong' }}</p>
                                    <p class="mt-2 truncate text-xs text-slate-500">{{ $item->password_location }}</p>
                                </div>
                                <span @class([
                                    'ah-badge',
                                    'bg-rose-400/15 text-rose-200' => $item->sensitivity_level === 'high',
                                    'bg-amber-300/15 text-amber-200' => $item->sensitivity_level === 'medium',
                                    'bg-emerald-400/15 text-emerald-200' => $item->sensitivity_level === 'low',
                                ])>
                                    {{ match($item->sensitivity_level) { 'high' => 'Tinggi', 'medium' => 'Sedang', default => 'Rendah' } }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="ah-empty-state">
                            Belum ada metadata akses.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-internal-app-layout>
