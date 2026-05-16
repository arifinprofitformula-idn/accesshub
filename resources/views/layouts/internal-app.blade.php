<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'AccessHub') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @php
            $viteEntries = ['resources/css/app.css', 'resources/js/app.js', 'resources/js/pwa.js'];
        @endphp
        @include('layouts.partials.pwa-head')
        @include('layouts.partials.asset-loader')
    </head>
    @php
        $currentUser = auth()->user();
        $icon = function (string $name, string $classes = 'h-5 w-5') {
            $paths = [
                'dashboard' => '<path d="M4 7.25A2.25 2.25 0 0 1 6.25 5h3.5A2.25 2.25 0 0 1 12 7.25v3.5A2.25 2.25 0 0 1 9.75 13h-3.5A2.25 2.25 0 0 1 4 10.75v-3.5Zm8 0A2.25 2.25 0 0 1 14.25 5h3.5A2.25 2.25 0 0 1 20 7.25v3.5A2.25 2.25 0 0 1 17.75 13h-3.5A2.25 2.25 0 0 1 12 10.75v-3.5Zm-8 8A2.25 2.25 0 0 1 6.25 13h3.5A2.25 2.25 0 0 1 12 15.25v3.5A2.25 2.25 0 0 1 9.75 21h-3.5A2.25 2.25 0 0 1 4 18.75v-3.5Zm8 0A2.25 2.25 0 0 1 14.25 13h3.5A2.25 2.25 0 0 1 20 15.25v3.5A2.25 2.25 0 0 1 17.75 21h-3.5A2.25 2.25 0 0 1 12 18.75v-3.5Z" />',
                'plus' => '<path d="M12 5v14" /><path d="M5 12h14" />',
                'star' => '<path d="m12 3.75 2.625 5.32 5.875.854-4.25 4.143 1.003 5.833L12 17.14l-5.253 2.76 1.003-5.833-4.25-4.143 5.875-.854L12 3.75Z" />',
                'manage' => '<path d="M4.75 6.75h14.5" /><path d="M4.75 12h14.5" /><path d="M4.75 17.25h9.5" /><path d="M17.25 16.25 19.5 18.5l-2.25 2.25" />',
                'profile' => '<path d="M15.25 8a3.25 3.25 0 1 1-6.5 0 3.25 3.25 0 0 1 6.5 0Z" /><path d="M5.5 18.25a6.9 6.9 0 0 1 13 0" />',
                'admin' => '<path d="M10.5 3h3l.75 2.25 2.25.75v3l-2.25.75L13.5 12h-3l-.75-2.25L7.5 9v-3l2.25-.75L10.5 3Z" /><path d="M6 14.5h12" /><path d="M8 18h8" />',
                'logout' => '<path d="M10 17.25H6.75A2.75 2.75 0 0 1 4 14.5v-5A2.75 2.75 0 0 1 6.75 6.75H10" /><path d="M14 8.5 18.5 12 14 15.5" /><path d="M9 12h9.5" />',
            ];

            return new \Illuminate\Support\HtmlString(
                sprintf(
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="%s">%s</svg>',
                    $classes,
                    $paths[$name] ?? $paths['dashboard']
                )
            );
        };

        $navItems = collect([
            [
                'label' => 'Dashboard',
                'route' => route('dashboard'),
                'active' => request()->routeIs('dashboard', 'app.dashboard'),
                'icon' => 'dashboard',
                'visible' => true,
            ],
            [
                'label' => 'Tambah Link',
                'route' => route('app.links.create'),
                'active' => request()->routeIs('app.links.create'),
                'icon' => 'plus',
                'visible' => $currentUser->can('create', \App\Models\Link::class),
            ],
            [
                'label' => 'Favorit',
                'route' => route('app.favorites'),
                'active' => request()->boolean('favorites') || request()->routeIs('app.favorites'),
                'icon' => 'star',
                'visible' => true,
            ],
            [
                'label' => 'Manage',
                'route' => route('app.manage'),
                'active' => request()->routeIs('app.manage'),
                'icon' => 'manage',
                'visible' => true,
            ],
        ])->filter(fn (array $item): bool => $item['visible']);

        $mobileNavItems = collect([
            [
                'label' => 'Tambah Link',
                'route' => route('app.links.create'),
                'active' => request()->routeIs('app.links.create'),
                'icon' => 'plus',
                'visible' => $currentUser->can('create', \App\Models\Link::class),
                'featured' => false,
            ],
            [
                'label' => 'Favorit',
                'route' => route('app.favorites'),
                'active' => request()->boolean('favorites') || request()->routeIs('app.favorites'),
                'icon' => 'star',
                'visible' => true,
                'featured' => false,
            ],
            [
                'label' => 'HOME',
                'route' => route('dashboard'),
                'active' => request()->routeIs('dashboard', 'app.dashboard'),
                'icon' => 'dashboard',
                'visible' => true,
                'featured' => true,
            ],
            [
                'label' => 'Manage',
                'route' => route('app.manage'),
                'active' => request()->routeIs('app.manage'),
                'icon' => 'manage',
                'visible' => true,
                'featured' => false,
            ],
        ])->filter(fn (array $item): bool => $item['visible']);

        $profileActive = request()->routeIs('profile.edit');
        $adminActive = request()->is('admin') || request()->is('admin/*');
    @endphp
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.14),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(139,92,246,0.12),transparent_24%),linear-gradient(180deg,#020617_0%,#081120_100%)] font-sans text-slate-100 antialiased">
        <div class="min-h-screen">
            <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur-2xl">
                <div class="flex items-center justify-between gap-3 px-3 py-2 sm:px-5 sm:py-3.5 lg:px-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 sm:gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-white/10 bg-white/5 p-1 shadow-[0_14px_36px_-18px_rgba(34,211,238,0.9)] sm:h-11 sm:w-11 sm:rounded-[1.25rem] sm:p-1.5">
                            <img src="{{ asset('icons/icon-192.png') }}" alt="Access Hub logo" class="h-full w-full object-contain">
                        </div>
                        <div>
                            <h1 class="text-sm font-semibold text-white sm:text-lg">Access Hub</h1>
                            <p class="hidden text-[11px] font-semibold uppercase tracking-[0.32em] text-cyan-300 sm:block">Find. Access. Work.</p>
                        </div>
                    </a>

                    <div class="flex items-center gap-2 md:gap-3">
                        <a
                            href="{{ route('profile.edit') }}"
                            aria-label="Profil"
                            class="{{ $profileActive ? 'border-cyan-300/25 bg-cyan-400/12 text-white' : 'border-white/10 bg-white/5 text-slate-200' }} inline-flex h-11 w-11 items-center justify-center rounded-2xl border transition hover:border-cyan-300/20 hover:bg-white/10 hover:text-white md:hidden"
                        >
                            {!! $icon('profile', 'h-5 w-5') !!}
                        </a>

                        <div class="hidden items-center gap-3 md:flex">
                            <a href="{{ route('profile.edit') }}" class="{{ $profileActive ? 'border-cyan-300/25 bg-cyan-400/12 text-white' : '' }} ah-secondary-btn gap-2">
                                {!! $icon('profile', 'h-4 w-4') !!}
                                Profil
                            </a>

                            @if ($currentUser->hasAnyRole(['super_admin', 'admin']))
                                <a href="{{ url('/admin') }}" class="{{ $adminActive ? 'border-cyan-300/25 bg-cyan-400/12 text-white' : '' }} ah-secondary-btn gap-2">
                                    {!! $icon('admin', 'h-4 w-4') !!}
                                    Admin
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="ah-secondary-btn gap-2">
                                    {!! $icon('logout', 'h-4 w-4') !!}
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="px-3 pb-2.5 sm:px-5 sm:pb-4 lg:px-6">
                    <div class="rounded-[1.5rem] border border-white/10 bg-[linear-gradient(135deg,rgba(15,23,42,0.96)_0%,rgba(8,17,32,0.94)_52%,rgba(91,33,182,0.12)_100%)] p-3 shadow-[0_20px_50px_-30px_rgba(34,211,238,0.65)] sm:rounded-[1.85rem] sm:p-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.32em] text-cyan-300 sm:text-[11px]">{{ $eyebrow ?? 'Workspace' }}</p>
                                <h2 class="mt-0.5 text-lg font-semibold text-white sm:mt-1 sm:text-2xl lg:text-3xl">{{ $heading ?? 'AccessHub' }}</h2>
                                @isset($subheading)
                                    <p class="mt-1 hidden max-w-2xl text-sm leading-6 text-slate-300 sm:block sm:mt-2">{{ $subheading }}</p>
                                @endisset
                            </div>

                            <div class="hidden flex-wrap gap-2 md:flex">
                                @foreach ($navItems as $item)
                                    <a href="{{ $item['route'] }}" class="{{ $item['active'] ? 'border-cyan-300/25 bg-cyan-400/12 text-white' : 'border-white/10 bg-white/5 text-slate-300' }} flex items-center gap-2 rounded-2xl border px-4 py-2 text-sm font-medium transition hover:border-cyan-300/20 hover:bg-white/10 hover:text-white">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-300/85 via-sky-400 to-violet-500 text-slate-950">
                                            {!! $icon($item['icon'], 'h-4 w-4') !!}
                                        </span>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="px-3 py-3 pb-20 sm:px-5 sm:py-5 md:pb-6 lg:px-6 lg:py-8">
                <div class="w-full">
                    @if (session('status'))
                        <div class="ah-alert-success mb-6">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="ah-alert-error mb-6">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="ah-alert-error mb-6">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>

            @include('layouts.partials.pwa-shell')

            {{-- Custom Confirm Modal --}}
            <div
                x-data
                x-show="$store.confirmModal.show"
                x-cloak
                class="fixed inset-0 z-[60] flex items-end justify-center p-4 sm:items-center"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="$store.confirmModal.cancel()"
            >
                {{-- Backdrop --}}
                <div
                    class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"
                    @click="$store.confirmModal.cancel()"
                ></div>

                {{-- Dialog --}}
                <div
                    class="relative w-full max-w-sm rounded-[1.85rem] border border-white/10 bg-[linear-gradient(135deg,rgba(15,23,42,0.99)_0%,rgba(8,17,32,0.97)_55%,rgba(127,29,29,0.12)_100%)] p-6 shadow-[0_40px_90px_-30px_rgba(239,68,68,0.4),0_0_0_1px_rgba(255,255,255,0.06)] backdrop-blur-2xl"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                    @click.stop
                >
                    {{-- Icon --}}
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-[1.1rem] border border-rose-300/25 bg-rose-500/12 text-rose-300 shadow-[0_14px_36px_-18px_rgba(239,68,68,0.55)]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                            <path d="M12 9v4"/><path d="M12 17h.01"/>
                        </svg>
                    </div>

                    {{-- Text --}}
                    <h3
                        class="mt-4 text-center text-base font-semibold text-white"
                        x-text="$store.confirmModal.title"
                    ></h3>
                    <p
                        class="mt-2 text-center text-sm leading-6 text-slate-400"
                        x-text="$store.confirmModal.message"
                    ></p>

                    {{-- Actions --}}
                    <div class="mt-6 flex gap-3">
                        <button
                            type="button"
                            @click="$store.confirmModal.cancel()"
                            class="ah-secondary-btn flex-1 justify-center"
                        >Batal</button>
                        <button
                            type="button"
                            @click="$store.confirmModal.confirm()"
                            x-text="$store.confirmModal.confirmLabel"
                            class="inline-flex min-h-12 flex-1 items-center justify-center rounded-2xl bg-gradient-to-r from-rose-500 to-rose-600 px-5 text-sm font-semibold text-white shadow-[0_12px_28px_-16px_rgba(239,68,68,0.7)] transition hover:brightness-110 active:scale-[0.98]"
                        ></button>
                    </div>
                </div>
            </div>

            <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-white/10 bg-slate-950/96 px-1 py-1 backdrop-blur-2xl md:hidden">
                <div class="grid w-full grid-cols-5 items-end gap-1">
                    @foreach ($mobileNavItems as $item)
                        <a
                            href="{{ $item['route'] }}"
                            aria-label="{{ $item['label'] }}"
                            data-active="{{ $item['active'] ? 'true' : 'false' }}"
                            data-inactive="{{ $item['active'] ? 'false' : 'true' }}"
                            data-featured="{{ $item['featured'] ? 'true' : 'false' }}"
                            class="{{ $item['active'] ? 'border-cyan-300/20 bg-white/10 text-white' : 'border-transparent text-slate-400' }} ah-mobile-nav-item"
                        >
                            <span class="ah-mobile-nav-icon bg-gradient-to-br from-cyan-300 via-sky-400 to-violet-500">
                                {!! $icon($item['icon'], 'h-4 w-4') !!}
                            </span>
                            <span class="ah-mobile-nav-label">
                                {{ $item['label'] }}
                            </span>
                        </a>
                    @endforeach
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button
                            type="submit"
                            aria-label="Logout"
                            data-active="false"
                            data-inactive="true"
                            class="ah-mobile-nav-item border-transparent text-slate-400 hover:border-rose-300/15 hover:bg-white/10 hover:text-white"
                        >
                            <span class="ah-mobile-nav-icon bg-gradient-to-br from-amber-200 via-rose-300 to-rose-500">
                                {!! $icon('logout', 'h-4 w-4') !!}
                            </span>
                            <span class="ah-mobile-nav-label">
                                Logout
                            </span>
                        </button>
                    </form>
                </div>
            </nav>
        </div>
    </body>
</html>
