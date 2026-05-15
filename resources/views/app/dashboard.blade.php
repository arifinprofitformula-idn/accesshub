<x-internal-app-layout title="Dashboard | AccessHub" eyebrow="Overview" heading="Dashboard Tim" subheading="Akses cepat ke link penting, metadata akses, dan pekerjaan yang perlu dicek.">
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <article class="ah-card">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Link Aktif</p>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ $stats['active_links'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Link yang siap dibuka tim hari ini.</p>
            </article>
            <article class="ah-card">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Perlu Dicek</p>
                <p class="mt-4 text-3xl font-semibold text-amber-600">{{ $stats['needs_review_links'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Link yang sebaiknya ditinjau ulang.</p>
            </article>
            <article class="ah-card">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Access Items</p>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ $stats['access_items'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Metadata akses platform tanpa password.</p>
            </article>
            <article class="ah-card">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Kategori Aktif</p>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ $stats['categories'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Kelompok kerja yang tersedia di sistem.</p>
            </article>
            <article class="ah-card">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">User Aktif</p>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ $stats['active_users'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Akun aktif yang dapat mengakses aplikasi.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
            <div class="ah-panel p-5 sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Shortcut</p>
                        <h2 class="mt-1 text-xl font-semibold text-slate-950">Akses Cepat</h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('app.links.index') }}" class="ah-accent-btn">Buka Link Manager</a>
                        <a href="{{ route('app.access-items.index') }}" class="ah-secondary-btn">Buka Access Items</a>
                        @if (auth()->user()->hasAnyRole(['super_admin', 'admin']))
                            <a href="{{ url('/admin') }}" class="ah-secondary-btn">Admin Panel</a>
                        @endif
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="ah-soft-card">
                        <p class="text-sm font-semibold text-slate-900">Cari link penting</p>
                        <p class="mt-1 text-sm text-slate-500">Buka daftar link dan cari campaign, client, dashboard, atau SOP dalam beberapa detik.</p>
                    </div>
                    <div class="ah-soft-card">
                        <p class="text-sm font-semibold text-slate-900">Cek metadata akses</p>
                        <p class="mt-1 text-sm text-slate-500">Lihat username, login URL, PIC, dan lokasi password eksternal tanpa menyimpan password.</p>
                    </div>
                </div>
            </div>

            <div class="ah-panel p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Favorit</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-950">Link Populer</h2>

                <div class="mt-5 space-y-3">
                    @forelse ($favorite_links as $link)
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $link->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $link->platform }}</p>
                                </div>
                                <span class="ah-badge bg-amber-50 text-amber-700">{{ $link->favorites_count }} pin</span>
                            </div>
                            <a href="{{ route('app.links.open', $link) }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex text-sm font-medium text-sky-600 hover:text-sky-500">
                                Open Link
                            </a>
                        </div>
                    @empty
                        <div class="ah-empty-state">
                            Belum ada link favorit yang muncul di dashboard.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="ah-panel p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Terbaru</p>
                        <h2 class="mt-1 text-xl font-semibold text-slate-950">Link Terakhir Ditambahkan</h2>
                    </div>
                    <a href="{{ route('app.links.index') }}" class="text-sm font-medium text-sky-600 hover:text-sky-500">Lihat semua</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($recent_links as $link)
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $link->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $link->platform }} • {{ $link->owner_name }}</p>
                                </div>
                                <span @class([
                                    'ah-badge',
                                    'bg-emerald-50 text-emerald-700' => $link->status === 'active',
                                    'bg-amber-50 text-amber-700' => $link->status === 'needs_review',
                                    'bg-slate-100 text-slate-600' => $link->status === 'archived',
                                ])>
                                    {{ ucfirst(str_replace('_', ' ', $link->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="ah-empty-state">
                            Belum ada link yang tersedia untuk akun ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="ah-panel p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Metadata Akses</p>
                        <h2 class="mt-1 text-xl font-semibold text-slate-950">Access Items Terbaru</h2>
                    </div>
                    <a href="{{ route('app.access-items.index') }}" class="text-sm font-medium text-sky-600 hover:text-sky-500">Lihat semua</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($recent_access_items as $item)
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $item->platform_name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $item->username ?: 'Username belum diisi' }}</p>
                                    <p class="mt-2 text-xs text-slate-400">{{ $item->password_location }}</p>
                                </div>
                                <span @class([
                                    'ah-badge',
                                    'bg-rose-50 text-rose-700' => $item->sensitivity_level === 'high',
                                    'bg-amber-50 text-amber-700' => $item->sensitivity_level === 'medium',
                                    'bg-emerald-50 text-emerald-700' => $item->sensitivity_level === 'low',
                                ])>
                                    {{ match($item->sensitivity_level) { 'high' => 'Tinggi', 'medium' => 'Sedang', default => 'Rendah' } }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="ah-empty-state">
                            Belum ada access item yang terlihat untuk akun ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-internal-app-layout>
