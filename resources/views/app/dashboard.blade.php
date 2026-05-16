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

        <section class="ah-panel p-5 sm:p-6 lg:p-7">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-300">Quick Access</p>
                    <h2 class="mt-1 text-xl font-semibold text-white">Temukan link kerja Anda dalam hitungan detik</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">Dashboard ini fokus pada hal yang paling penting: cari, buka, copy, edit, dan arsipkan link milik Anda atau link shared yang memang bisa Anda akses.</p>
                </div>
                @can('create', \App\Models\Link::class)
                    <a href="{{ route('app.links.create') }}" class="ah-accent-btn w-full justify-center sm:w-auto">+ Tambah Link</a>
                @endcan
            </div>
        </section>

        @include('app.links.partials.list', ['emptyMessage' => 'Belum ada link tersimpan. Tambahkan link pertama Anda agar lebih mudah ditemukan nanti.'])
    </div>
</x-internal-app-layout>
