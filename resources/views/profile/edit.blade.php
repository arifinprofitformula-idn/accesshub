<x-internal-app-layout
    title="Profil | AccessHub"
    eyebrow="Profil"
    heading="Profil Anda"
    subheading="Kelola identitas akun, email, dan keamanan dengan tampilan yang konsisten dengan dashboard AccessHub."
>
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] lg:gap-7">
        <section class="ah-panel p-5 sm:p-6 lg:p-7">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-[1.35rem] bg-gradient-to-br from-cyan-300 via-sky-400 to-violet-500 text-lg font-semibold text-slate-950 shadow-[0_18px_45px_-24px_rgba(34,211,238,0.95)]">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-300">Account Overview</p>
                    <h2 class="mt-1 text-2xl font-semibold text-white">{{ $user->name }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-300">{{ $user->email }}</p>
                    <div class="mt-4 inline-flex items-center rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                        {{ $user->hasVerifiedEmail() ? 'Email terverifikasi' : 'Email belum terverifikasi' }}
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-[1.35rem] border border-cyan-300/15 bg-cyan-400/10 p-4">
                    <p class="text-sm font-medium text-slate-300">Nama Akun</p>
                    <p class="mt-2 text-base font-semibold text-white">{{ $user->name }}</p>
                </div>
                <div class="rounded-[1.35rem] border border-violet-300/15 bg-violet-400/10 p-4">
                    <p class="text-sm font-medium text-slate-300">Status Akun</p>
                    <p class="mt-2 text-base font-semibold text-white">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div class="ah-panel p-5 sm:p-6 lg:p-7">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="ah-panel p-5 sm:p-6 lg:p-7">
                @include('profile.partials.update-password-form')
            </div>

            <div class="ah-panel border-rose-400/15 p-5 sm:p-6 lg:p-7">
                @include('profile.partials.delete-user-form')
            </div>
        </section>
    </div>
</x-internal-app-layout>
