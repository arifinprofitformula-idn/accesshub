<x-internal-app-layout title="Access Items | AccessHub" eyebrow="Access Metadata" heading="Access Item Manager" subheading="Metadata akses platform yang aman, jelas, dan nyaman dibuka dari HP maupun desktop.">
    <div x-data="{ copied: null, submitting: false }" class="space-y-6">
        <section class="ah-panel p-4 sm:p-6">
            <form method="GET" action="{{ route('app.access-items.index') }}" class="space-y-4" x-on:submit="submitting = true">
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-semibold text-slate-800">Pencarian Cepat</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="Cari platform, login URL, username, PIC, kategori, atau catatan..." class="ah-input-lg">
                    </div>
                    <div class="ah-soft-card">
                        <p class="text-sm font-semibold text-slate-900">Catatan Keamanan</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Halaman ini hanya menyimpan metadata akses platform. Password tetap berada di penyimpanan eksternal seperti Bitwarden atau Google Password Manager.</p>
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
                        <span class="ah-badge bg-slate-100 text-slate-600">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
                        <span @class([
                            'ah-badge',
                            'bg-emerald-50 text-emerald-700' => $item->status === 'active',
                            'bg-amber-50 text-amber-700' => $item->status === 'needs_review',
                            'bg-slate-100 text-slate-600' => $item->status === 'archived',
                        ])>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                    </div>

                    <h2 class="mt-3 text-lg font-semibold text-slate-950">{{ $item->platform_name }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $item->username ?: 'Username belum diisi.' }}</p>
                    <p class="mt-1 text-xs text-slate-500">PIC: {{ $item->pic_name }}</p>

                    <div class="mt-4">
                        <span @class([
                            'ah-badge',
                            'bg-rose-50 text-rose-700' => $item->sensitivity_level === 'high',
                            'bg-amber-50 text-amber-700' => $item->sensitivity_level === 'medium',
                            'bg-emerald-50 text-emerald-700' => $item->sensitivity_level === 'low',
                        ])>{{ match($item->sensitivity_level) { 'high' => 'Tinggi', 'medium' => 'Sedang', default => 'Rendah' } }}</span>
                    </div>

                    <div class="mt-4 ah-soft-card text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">Lokasi password eksternal</p>
                        <p class="mt-1">{{ $item->password_location }}</p>
                        @if ($item->note)
                            <p class="mt-3 font-semibold text-slate-900">Catatan</p>
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
                    <thead class="bg-slate-50/90 text-left text-xs uppercase tracking-[0.18em] text-slate-500">
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
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($accessItems as $item)
                            <tr class="align-top">
                                <td class="px-6 py-5">
                                    <p class="font-semibold text-slate-950">{{ $item->platform_name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $item->login_url ?: 'Login URL belum diisi.' }}</p>
                                    <div class="mt-2">
                                        <span @class([
                                            'ah-badge',
                                            'bg-emerald-50 text-emerald-700' => $item->status === 'active',
                                            'bg-amber-50 text-amber-700' => $item->status === 'needs_review',
                                            'bg-slate-100 text-slate-600' => $item->status === 'archived',
                                        ])>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-slate-600">{{ $item->username ?: '-' }}</td>
                                <td class="px-6 py-5">
                                    <span class="ah-badge bg-slate-100 text-slate-600">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
                                </td>
                                <td class="px-6 py-5 text-slate-600">{{ $item->pic_name }}</td>
                                <td class="px-6 py-5">
                                    <span @class([
                                        'ah-badge',
                                        'bg-rose-50 text-rose-700' => $item->sensitivity_level === 'high',
                                        'bg-amber-50 text-amber-700' => $item->sensitivity_level === 'medium',
                                        'bg-emerald-50 text-emerald-700' => $item->sensitivity_level === 'low',
                                    ])>{{ match($item->sensitivity_level) { 'high' => 'Tinggi', 'medium' => 'Sedang', default => 'Rendah' } }}</span>
                                </td>
                                <td class="px-6 py-5 text-slate-600">
                                    <p>{{ $item->password_location }}</p>
                                    @if ($item->note)
                                        <p class="mt-2 max-w-sm text-xs leading-5 text-slate-500">{{ $item->note }}</p>
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
