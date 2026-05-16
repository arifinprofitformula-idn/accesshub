<x-internal-app-layout
    title="Manage Link Asset | AccessHub"
    eyebrow="Manage Assets"
    heading="Manage Link Asset"
    subheading="Kelola seluruh asset link yang bisa Anda akses dalam tampilan tabel yang lebih fokus untuk tindakan cepat."
>
    <div x-data="{ copied: null }" class="space-y-2.5 sm:space-y-5">
        <section class="ah-panel p-3 sm:p-5 lg:p-6">
            <form method="GET" action="{{ route('app.manage') }}" class="flex flex-col gap-2 sm:gap-3 lg:flex-row lg:items-end lg:gap-4">
                <div class="flex-1">
                    <label for="search" class="mb-1.5 hidden text-xs font-semibold text-cyan-200 sm:block">Cari Link</label>
                    <input
                        id="search"
                        name="search"
                        type="text"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Cari judul, URL, kategori, atau tag..."
                        class="ah-input"
                    >
                </div>

                <div class="grid grid-cols-[1fr_auto_auto_auto] items-center gap-2 sm:contents sm:gap-3">
                    <div class="sm:w-48 lg:w-52">
                        <label for="category" class="mb-1.5 hidden text-xs font-semibold text-cyan-200 sm:block">Kategori</label>
                        <select id="category" name="category" class="ah-select">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="sm:flex sm:items-end">
                        <span class="sr-only">Favorit saja</span>
                        <span class="flex min-h-12 items-center gap-2 rounded-2xl border border-white/10 bg-white/6 px-3 py-2 text-xs text-slate-200 sm:px-4">
                            <span class="hidden font-medium sm:inline">Favorit</span>
                            <input type="checkbox" name="favorites" value="1" @checked(($filters['favorites'] ?? false)) class="rounded border-slate-500 bg-slate-950 text-cyan-400 focus:ring-cyan-500">
                        </span>
                    </label>

                    <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-300 via-sky-400 to-blue-500 px-4 text-sm font-semibold text-slate-950 shadow-[0_14px_32px_-20px_rgba(34,211,238,0.85)] transition hover:brightness-105 sm:px-5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 sm:hidden">
                            <circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <span class="hidden sm:inline">Cari</span>
                    </button>

                    <a href="{{ route('app.manage') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-3 text-sm font-semibold text-slate-200 transition hover:bg-white/20 sm:px-5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 sm:hidden">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                        </svg>
                        <span class="hidden sm:inline">Reset</span>
                    </a>
                </div>

                <div class="flex gap-2 sm:gap-3 lg:shrink-0">
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 flex-1 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-3 text-sm font-semibold text-slate-200 transition hover:bg-white/20 sm:flex-none sm:px-5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 sm:hidden">
                            <path d="M4 7.25A2.25 2.25 0 0 1 6.25 5h3.5A2.25 2.25 0 0 1 12 7.25v3.5A2.25 2.25 0 0 1 9.75 13h-3.5A2.25 2.25 0 0 1 4 10.75v-3.5Zm8 0A2.25 2.25 0 0 1 14.25 5h3.5A2.25 2.25 0 0 1 20 7.25v3.5A2.25 2.25 0 0 1 17.75 13h-3.5A2.25 2.25 0 0 1 12 10.75v-3.5Zm-8 8A2.25 2.25 0 0 1 6.25 13h3.5A2.25 2.25 0 0 1 12 15.25v3.5A2.25 2.25 0 0 1 9.75 21h-3.5A2.25 2.25 0 0 1 4 18.75v-3.5Zm8 0A2.25 2.25 0 0 1 14.25 13h3.5A2.25 2.25 0 0 1 20 15.25v3.5A2.25 2.25 0 0 1 17.75 21h-3.5A2.25 2.25 0 0 1 12 18.75v-3.5Z"/>
                        </svg>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                    @can('create', \App\Models\Link::class)
                        <a href="{{ route('app.links.create') }}" class="inline-flex min-h-12 flex-1 items-center justify-center gap-1.5 rounded-2xl bg-gradient-to-r from-cyan-300 via-sky-400 to-blue-500 px-4 text-sm font-semibold text-slate-950 shadow-[0_14px_32px_-20px_rgba(34,211,238,0.85)] transition hover:brightness-105 sm:flex-none sm:px-5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                            <span class="hidden sm:inline">Tambah Link</span>
                        </a>
                    @endcan
                </div>
            </form>
        </section>

        @include('app.links.partials.list', [
            'emptyMessage' => 'Belum ada asset link yang bisa dikelola saat ini.',
            'manageMode' => true,
        ])
    </div>
</x-internal-app-layout>
