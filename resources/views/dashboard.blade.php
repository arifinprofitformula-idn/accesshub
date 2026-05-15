<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">
                    AccessHub Dashboard
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Fondasi aplikasi sudah aktif. Dashboard internal lengkap akan dilanjutkan di FASE 4.
                </p>
            </div>
            @if (auth()->user()->hasAnyRole(['super_admin', 'admin']))
                <a href="{{ url('/admin') }}" class="inline-flex items-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-sky-500">
                    Buka Admin Panel
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Status Login</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">Aktif</p>
                <p class="mt-2 text-sm text-slate-500">Proteksi user aktif/nonaktif dan rate limiting login sudah dipasang.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Role</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ auth()->user()->getRoleNames()->implode(', ') ?: 'Belum ada role' }}</p>
                <p class="mt-2 text-sm text-slate-500">Hak akses dikelola lewat Spatie Permission dan policy Laravel.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Langkah Berikutnya</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">FASE 2</p>
                <p class="mt-2 text-sm text-slate-500">Link Manager lengkap dengan category, tag, filter, favorite, dan activity log bisnis.</p>
            </div>
        </div>
    </div>
</x-app-layout>
