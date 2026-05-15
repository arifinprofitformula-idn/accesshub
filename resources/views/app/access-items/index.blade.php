<x-internal-app-layout title="Access Items | AccessHub" eyebrow="Access Metadata" heading="Access Item Manager" subheading="Metadata akses platform yang aman, jelas, dan nyaman dibuka dari HP maupun desktop.">
    <div x-data="{ copied: null, submitting: false }" class="space-y-6">
        <section class="ah-panel p-4 sm:p-6">
            <form method="GET" action="{{ route('app.access-items.index') }}" class="space-y-4" x-on:submit="submitting = true">
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-semibold text-amber-200">Pencarian Cepat</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="Cari platform, login URL, username, PIC, kategori, atau catatan..." class="ah-input-lg">
                    </div>
                    <div class="ah-soft-card bg-gradient-to-br from-amber-300/12 to-orange-500/6">
                        <div class="flex items-start gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-200 to-orange-500 text-slate-950 shadow-lg">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <path d="M12 3 5 6v5c0 4.25 2.75 8.17 7 9 4.25-.83 7-4.75 7-9V6l-7-3Z" />
                                    <path d="M9.75 11.5h4.5" />
                                    <path d="M12 8.75v5.5" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-white">Tanpa password</p>
                                <p class="mt-1 text-sm leading-6 text-slate-300">Hanya metadata dan lokasi penyimpanan eksternal.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
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

                    <select name="sensitivity" class="ah-select">
                        <option value="">Semua sensitivitas</option>
                        <option value="low" @selected(($filters['sensitivity'] ?? null) === 'low')>Rendah</option>
                        <option value="medium" @selected(($filters['sensitivity'] ?? null) === 'medium')>Sedang</option>
                        <option value="high" @selected(($filters['sensitivity'] ?? null) === 'high')>Tinggi</option>
                    </select>

                    <select name="pic" class="ah-select">
                        <option value="">Semua PIC</option>
                        @foreach ($pics as $pic)
                            <option value="{{ $pic }}" @selected(($filters['pic'] ?? null) === $pic)>{{ $pic }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="ah-accent-btn" x-bind:disabled="submitting">
                        <span x-show="!submitting">Terapkan Filter</span>
                        <span x-show="submitting">Memuat...</span>
                    </button>
                    <a href="{{ route('app.access-items.index') }}" class="ah-secondary-btn">Reset</a>
                </div>
            </form>
        </section>

        <section class="grid gap-4 lg:hidden">
            @forelse ($accessItems as $item)
                <article class="ah-panel p-5">
                    <div class="flex flex-wrap gap-2">
                        <span class="ah-badge bg-white/8 text-slate-300">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
                        <span @class([
                            'ah-badge',
                            'bg-emerald-400/15 text-emerald-200' => $item->status === 'active',
                            'bg-amber-300/15 text-amber-200' => $item->status === 'needs_review',
                            'bg-slate-500/20 text-slate-300' => $item->status === 'archived',
                        ])>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                    </div>

                    <div class="mt-3 flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-200 to-orange-500 text-slate-950 shadow-lg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
                                <path d="M12 3 5 6v5c0 4.25 2.75 8.17 7 9 4.25-.83 7-4.75 7-9V6l-7-3Z" />
                                <path d="M9.75 11.5h4.5" />
                                <path d="M12 8.75v5.5" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold text-white">{{ $item->platform_name }}</h2>
                            <p class="mt-2 text-sm text-slate-300">{{ $item->username ?: 'Username belum diisi.' }}</p>
                            <p class="mt-1 text-xs text-slate-400">PIC: {{ $item->pic_name }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span @class([
                            'ah-badge',
                            'bg-rose-400/15 text-rose-200' => $item->sensitivity_level === 'high',
                            'bg-amber-300/15 text-amber-200' => $item->sensitivity_level === 'medium',
                            'bg-emerald-400/15 text-emerald-200' => $item->sensitivity_level === 'low',
                        ])>{{ match($item->sensitivity_level) { 'high' => 'Tinggi', 'medium' => 'Sedang', default => 'Rendah' } }}</span>
                    </div>

                    <div class="mt-4 ah-soft-card text-sm text-slate-300">
                        <p class="font-semibold text-white">Lokasi password eksternal</p>
                        <p class="mt-1">{{ $item->password_location }}</p>
                        @if ($item->note)
                            <p class="mt-3 font-semibold text-white">Catatan</p>
                            <p class="mt-1 leading-6">{{ $item->note }}</p>
                        @endif
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        @if ($item->login_url)
                            <a href="{{ route('app.access-items.open', $item) }}" target="_blank" rel="noopener noreferrer" class="ah-primary-btn">
                                Open Login
                            </a>
                        @else
                            <span class="ah-secondary-btn cursor-default text-slate-400">No URL</span>
                        @endif
                        <button type="button" @if($item->username) x-on:click="navigator.clipboard.writeText(@js($item->username)); copied = {{ $item->id }}" @endif class="ah-secondary-btn">
                            @if ($item->username)
                                <span x-show="copied !== {{ $item->id }}">Copy Username</span>
                                <span x-show="copied === {{ $item->id }}">Copied</span>
                            @else
                                <span>No Username</span>
                            @endif
                        </button>
                    </div>
                </article>
            @empty
                <div class="ah-empty-state">
                    Tidak ada access item yang cocok dengan filter saat ini.
                </div>
            @endforelse
        </section>

        <section class="ah-table-shell hidden lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Platform</th>
                            <th class="px-6 py-4">Username</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">PIC</th>
                            <th class="px-6 py-4">Sensitivitas</th>
                            <th class="px-6 py-4">Lokasi Password</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/8 bg-transparent">
                        @forelse ($accessItems as $item)
                            <tr class="align-top">
                                <td class="px-6 py-5">
                                    <p class="font-semibold text-white">{{ $item->platform_name }}</p>
                                    <p class="mt-1 text-sm text-slate-400">{{ $item->login_url ?: 'Login URL belum diisi.' }}</p>
                                    <div class="mt-2">
                                        <span @class([
                                            'ah-badge',
                                            'bg-emerald-400/15 text-emerald-200' => $item->status === 'active',
                                            'bg-amber-300/15 text-amber-200' => $item->status === 'needs_review',
                                            'bg-slate-500/20 text-slate-300' => $item->status === 'archived',
                                        ])>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-slate-300">{{ $item->username ?: '-' }}</td>
                                <td class="px-6 py-5">
                                    <span class="ah-badge bg-white/8 text-slate-300">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
                                </td>
                                <td class="px-6 py-5 text-slate-300">{{ $item->pic_name }}</td>
                                <td class="px-6 py-5">
                                    <span @class([
                                        'ah-badge',
                                        'bg-rose-400/15 text-rose-200' => $item->sensitivity_level === 'high',
                                        'bg-amber-300/15 text-amber-200' => $item->sensitivity_level === 'medium',
                                        'bg-emerald-400/15 text-emerald-200' => $item->sensitivity_level === 'low',
                                    ])>{{ match($item->sensitivity_level) { 'high' => 'Tinggi', 'medium' => 'Sedang', default => 'Rendah' } }}</span>
                                </td>
                                <td class="px-6 py-5 text-slate-300">
                                    <p>{{ $item->password_location }}</p>
                                    @if ($item->note)
                                        <p class="mt-2 max-w-sm text-xs leading-5 text-slate-400">{{ $item->note }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        @if ($item->login_url)
                                            <a href="{{ route('app.access-items.open', $item) }}" target="_blank" rel="noopener noreferrer" class="ah-primary-btn px-4 py-2 text-xs">
                                                Open Login
                                            </a>
                                        @endif
                                        <button type="button" @if($item->username) x-on:click="navigator.clipboard.writeText(@js($item->username)); copied = {{ $item->id }}" @endif class="ah-secondary-btn px-4 py-2 text-xs">
                                            @if ($item->username)
                                                <span x-show="copied !== {{ $item->id }}">Copy Username</span>
                                                <span x-show="copied === {{ $item->id }}">Copied</span>
                                            @else
                                                <span>No Username</span>
                                            @endif
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12">
                                    <div class="ah-empty-state">
                                        Tidak ada access item yang cocok dengan filter saat ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>{{ $accessItems->links() }}</div>
    </div>
</x-internal-app-layout>
