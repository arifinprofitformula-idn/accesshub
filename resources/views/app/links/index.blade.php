<x-internal-app-layout
    title="Daftar Link | AccessHub"
    eyebrow="Link Library"
    heading="Daftar Link"
    subheading="Lihat seluruh link aktif, cari cepat, lalu buka tanpa banyak langkah."
>
    <div x-data="{ copied: null }" class="space-y-7 sm:space-y-8">
        <section class="ah-panel p-4 sm:p-6 lg:p-7">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <form method="GET" action="{{ route('app.links.index') }}" class="grid flex-1 gap-4 lg:grid-cols-[minmax(0,1.7fr)_220px_auto_auto] lg:gap-5">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-semibold text-cyan-200">Cari Link</label>
                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Cari judul, URL, kategori, atau tag..."
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
                        <button type="submit" class="ah-accent-btn w-full justify-center sm:w-auto">Search</button>
                        <a href="{{ route('app.links.index') }}" class="ah-secondary-btn w-full justify-center sm:w-auto">Reset</a>
                    </div>
                </form>

                @can('create', \App\Models\Link::class)
                    <a href="{{ route('app.links.create') }}" class="ah-accent-btn justify-center">+ Tambah Link</a>
                @endcan
            </div>
        </section>

        @include('app.links.partials.list', ['emptyMessage' => 'Tidak ada link yang cocok dengan pencarian atau filter saat ini.'])
    </div>
</x-internal-app-layout>
