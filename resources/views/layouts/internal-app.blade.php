<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'AccessHub') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @include('layouts.partials.pwa-head')
        @include('layouts.partials.asset-loader')
    </head>
    @php
        $searchRoute = request()->routeIs('app.access-items.*')
            ? route('app.access-items.index')
            : route('app.links.index');

        $searchPlaceholder = request()->routeIs('app.access-items.*')
            ? 'Cari platform, username, PIC, atau catatan akses...'
            : 'Cari link, kategori, tag, atau platform...';

        $currentSection = request()->routeIs('app.links.*')
            ? 'links'
            : (request()->routeIs('app.access-items.*')
                ? 'access'
                : (request()->routeIs('profile.*') ? 'profile' : 'dashboard'));

        $sectionThemes = [
            'dashboard' => [
                'badge' => 'text-cyan-300',
                'title' => 'Ringkas, cepat, dan siap dipakai.',
                'surface' => 'from-cyan-400/16 via-sky-500/10 to-transparent',
                'glow' => 'bg-cyan-400/30',
                'chip' => 'border-cyan-300/30 bg-cyan-400/15 text-cyan-50',
                'search' => 'Cari akses favorit...',
            ],
            'links' => [
                'badge' => 'text-emerald-300',
                'title' => 'Semua link penting dalam satu alur.',
                'surface' => 'from-emerald-400/16 via-teal-500/10 to-transparent',
                'glow' => 'bg-emerald-400/30',
                'chip' => 'border-emerald-300/30 bg-emerald-400/15 text-emerald-50',
                'search' => 'Cari link kerja...',
            ],
            'access' => [
                'badge' => 'text-amber-300',
                'title' => 'Metadata akses yang aman dan jelas.',
                'surface' => 'from-amber-300/18 via-orange-400/10 to-transparent',
                'glow' => 'bg-amber-300/30',
                'chip' => 'border-amber-300/30 bg-amber-300/15 text-amber-50',
                'search' => 'Cari metadata akses...',
            ],
            'profile' => [
                'badge' => 'text-fuchsia-300',
                'title' => 'Profil dan preferensi kerja.',
                'surface' => 'from-fuchsia-400/16 via-violet-500/10 to-transparent',
                'glow' => 'bg-fuchsia-400/30',
                'chip' => 'border-fuchsia-300/30 bg-fuchsia-400/15 text-fuchsia-50',
                'search' => 'Cari halaman...',
            ],
        ];

        $theme = $sectionThemes[$currentSection];

        $icon = function (string $name, string $classes = 'h-5 w-5') {
            $paths = [
                'dashboard' => '<path d="M4.75 5.75A2.75 2.75 0 0 1 7.5 3h2A2.75 2.75 0 0 1 12.25 5.75v2A2.75 2.75 0 0 1 9.5 10.5h-2A2.75 2.75 0 0 1 4.75 7.75v-2Zm7 0A2.75 2.75 0 0 1 14.5 3h2A2.75 2.75 0 0 1 19.25 5.75v6.5A2.75 2.75 0 0 1 16.5 15h-2a2.75 2.75 0 0 1-2.75-2.75v-6.5Zm-7 8A2.75 2.75 0 0 1 7.5 11h2a2.75 2.75 0 0 1 2.75 2.75v4.5A2.75 2.75 0 0 1 9.5 21h-2a2.75 2.75 0 0 1-2.75-2.75v-4.5Zm7 4.5A2.75 2.75 0 0 1 14.5 15h2a2.75 2.75 0 0 1 2.75 2.75v.5A2.75 2.75 0 0 1 16.5 21h-2a2.75 2.75 0 0 1-2.75-2.75v-.5Z" />',
                'links' => '<path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768m-6.263.627-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0" /><path d="m8.5 15.5 7-7" />',
                'access' => '<path d="M12 3 5 6v5c0 4.25 2.75 8.17 7 9 4.25-.83 7-4.75 7-9V6l-7-3Z" /><path d="M9.75 11.5h4.5" /><path d="M12 8.75v5.5" />',
                'profile' => '<path d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /><path d="M4.5 19.25a7.5 7.5 0 0 1 15 0" />',
                'admin' => '<path d="M10.5 3h3l.75 2.25 2.25.75v3l-2.25.75L13.5 12h-3l-.75-2.25L7.5 9v-3l2.25-.75L10.5 3Z" /><path d="M6 14.5h12" /><path d="M8 18h8" />',
                'search' => '<path d="m20 20-3.5-3.5" /><circle cx="10.5" cy="10.5" r="5.5" />',
                'logout' => '<path d="M10 17.25H6.75A2.75 2.75 0 0 1 4 14.5v-5A2.75 2.75 0 0 1 6.75 6.75H10" /><path d="M14 8.5 18.5 12 14 15.5" /><path d="M9 12h9.5" />',
            ];

            return new \Illuminate\Support\HtmlString(
                sprintf(
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="%s">%s</svg>',
                    $classes,
                    $paths[$name] ?? $paths['dashboard']
                )
            );
        };

        $mobileIcon = function (string $name, string $classes = 'h-5 w-5') {
            $paths = [
                'dashboard' => '<path d="M4 7.25A2.25 2.25 0 0 1 6.25 5h3.5A2.25 2.25 0 0 1 12 7.25v3.5A2.25 2.25 0 0 1 9.75 13h-3.5A2.25 2.25 0 0 1 4 10.75v-3.5Zm8 0A2.25 2.25 0 0 1 14.25 5h3.5A2.25 2.25 0 0 1 20 7.25v3.5A2.25 2.25 0 0 1 17.75 13h-3.5A2.25 2.25 0 0 1 12 10.75v-3.5Zm-8 8A2.25 2.25 0 0 1 6.25 13h3.5A2.25 2.25 0 0 1 12 15.25v3.5A2.25 2.25 0 0 1 9.75 21h-3.5A2.25 2.25 0 0 1 4 18.75v-3.5Zm8 0A2.25 2.25 0 0 1 14.25 13h3.5A2.25 2.25 0 0 1 20 15.25v3.5A2.25 2.25 0 0 1 17.75 21h-3.5A2.25 2.25 0 0 1 12 18.75v-3.5Z" />',
                'links' => '<path d="M10.2 13.8 8.1 15.9a3 3 0 1 1-4.243-4.243l2.829-2.829A3 3 0 0 1 10.929 13" /><path d="m13.8 10.2 2.1-2.1a3 3 0 1 1 4.243 4.243l-2.829 2.829A3 3 0 0 1 13.071 11" /><path d="m9 15 6-6" />',
                'access' => '<path d="M12 3.75 6.75 6v4.592c0 3.506 2.22 6.744 5.25 7.908 3.03-1.164 5.25-4.402 5.25-7.908V6L12 3.75Z" /><path d="M12 8.5v4.75" /><path d="M9.625 10.875h4.75" />',
                'profile' => '<path d="M15.25 8a3.25 3.25 0 1 1-6.5 0 3.25 3.25 0 0 1 6.5 0Z" /><path d="M5.5 18.25a6.9 6.9 0 0 1 13 0" />',
            ];

            return new \Illuminate\Support\HtmlString(
                sprintf(
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" class="%s">%s</svg>',
                    $classes,
                    $paths[$name] ?? $paths['dashboard']
                )
            );
        };

        $navItems = [
            [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'route' => route('dashboard'),
                'active' => request()->routeIs('dashboard', 'app.dashboard'),
                'icon' => 'dashboard',
                'activeClass' => 'border-cyan-300/40 bg-cyan-400/18 text-white shadow-[0_18px_40px_-24px_rgba(34,211,238,0.9)]',
                'iconWrap' => 'from-cyan-300 to-sky-500 text-slate-950',
            ],
            [
                'key' => 'links',
                'label' => 'Links',
                'route' => route('app.links.index'),
                'active' => request()->routeIs('app.links.*'),
                'icon' => 'links',
                'activeClass' => 'border-emerald-300/40 bg-emerald-400/18 text-white shadow-[0_18px_40px_-24px_rgba(52,211,153,0.9)]',
                'iconWrap' => 'from-emerald-300 to-teal-500 text-slate-950',
            ],
            [
                'key' => 'access',
                'label' => 'Access',
                'route' => route('app.access-items.index'),
                'active' => request()->routeIs('app.access-items.*'),
                'icon' => 'access',
                'activeClass' => 'border-amber-300/40 bg-amber-300/18 text-white shadow-[0_18px_40px_-24px_rgba(251,191,36,0.9)]',
                'iconWrap' => 'from-amber-200 to-orange-500 text-slate-950',
            ],
            [
                'key' => 'profile',
                'label' => 'Profile',
                'route' => route('profile.edit'),
                'active' => request()->routeIs('profile.*'),
                'icon' => 'profile',
                'activeClass' => 'border-fuchsia-300/40 bg-fuchsia-400/18 text-white shadow-[0_18px_40px_-24px_rgba(232,121,249,0.9)]',
                'iconWrap' => 'from-fuchsia-300 to-violet-500 text-slate-950',
            ],
        ];
    @endphp
    <body class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.16),transparent_24%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.14),transparent_20%),linear-gradient(180deg,#020617_0%,#08111f_100%)] font-sans text-slate-100 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
            <aside class="hidden border-r border-white/10 bg-slate-950/70 p-6 backdrop-blur-2xl lg:flex lg:flex-col">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-[1.25rem] bg-gradient-to-br from-cyan-300 via-sky-400 to-blue-600 text-lg font-semibold text-slate-950 shadow-[0_18px_45px_-24px_rgba(34,211,238,0.95)]">
                        AH
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-cyan-300">Modern Workspace</p>
                        <h1 class="text-lg font-semibold text-white">AccessHub</h1>
                    </div>
                </div>

                <div class="mt-8 rounded-[1.6rem] border border-white/10 bg-white/5 p-4 shadow-[0_20px_50px_-35px_rgba(34,211,238,0.6)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] {{ $theme['badge'] }}">Quick Search</p>
                    <form method="GET" action="{{ $searchRoute }}" class="mt-3">
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                {!! $icon('search', 'h-4 w-4') !!}
                            </span>
                            <input type="text" name="search" class="ah-input pl-11" placeholder="{{ $searchPlaceholder }}">
                        </div>
                    </form>
                </div>

                <nav class="mt-8 space-y-2">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['route'] }}" class="{{ $item['active'] ? $item['activeClass'] : 'border-transparent text-slate-300 hover:border-white/10 hover:bg-white/6 hover:text-white' }} flex items-center gap-3 rounded-[1.35rem] border px-4 py-3 text-sm font-medium transition">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br {{ $item['iconWrap'] }} shadow-lg">
                                {!! $icon($item['icon']) !!}
                            </span>
                            <span class="flex-1">{{ $item['label'] }}</span>
                            @if ($item['active'])
                                <span class="h-2.5 w-2.5 rounded-full bg-white/80"></span>
                            @endif
                        </a>
                    @endforeach
                    @if (auth()->user()->hasAnyRole(['super_admin', 'admin']))
                        <a href="{{ url('/admin') }}" class="flex items-center gap-3 rounded-[1.35rem] border border-transparent px-4 py-3 text-sm font-medium text-slate-300 transition hover:border-white/10 hover:bg-white/6 hover:text-white">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-300 to-fuchsia-500 text-slate-950 shadow-lg">
                                {!! $icon('admin') !!}
                            </span>
                            <span>Admin Panel</span>
                        </a>
                    @endif
                </nav>

                <div class="mt-auto overflow-hidden rounded-[1.75rem] border border-cyan-300/12 bg-[linear-gradient(180deg,rgba(15,23,42,0.96)_0%,rgba(2,6,23,0.98)_100%)] p-5 text-white shadow-[0_32px_85px_-48px_rgba(34,211,238,0.7)]">
                    <p class="text-xs uppercase tracking-[0.24em] text-cyan-300">Signed In</p>
                    <p class="mt-3 text-lg font-semibold">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-sm text-slate-300">{{ auth()->user()->email }}</p>
                    <div class="mt-4 inline-flex items-center rounded-full border border-cyan-300/20 bg-cyan-400/10 px-3 py-1 text-xs font-medium text-cyan-100">
                        Workspace aktif
                    </div>
                </div>
            </aside>

            <div class="flex min-h-screen flex-col">
                <header class="border-b border-white/10 bg-slate-950/55 px-4 py-4 backdrop-blur-2xl sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-7xl">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-[1.15rem] bg-gradient-to-br from-cyan-300 via-sky-400 to-blue-600 text-slate-950 shadow-[0_18px_45px_-24px_rgba(34,211,238,0.95)] lg:hidden">
                                    {!! $icon($currentSection, 'h-5 w-5') !!}
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.32em] {{ $theme['badge'] }}">{{ $eyebrow ?? 'Workspace' }}</p>
                                    <h1 class="mt-1 text-xl font-semibold text-white sm:text-2xl">{{ $heading ?? 'AccessHub' }}</h1>
                                    @isset($subheading)
                                        <p class="mt-1 max-w-2xl text-sm text-slate-300/85">{{ $subheading }}</p>
                                    @endisset
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <form method="GET" action="{{ $searchRoute }}" class="w-full sm:w-[320px]">
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                            {!! $icon('search', 'h-4 w-4') !!}
                                        </span>
                                        <input type="text" name="search" class="ah-input pl-11" placeholder="{{ $searchPlaceholder }}">
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="ah-secondary-btn w-full gap-2 sm:w-auto">
                                        {!! $icon('logout', 'h-4 w-4') !!}
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-[1.75rem] border border-white/10 bg-[linear-gradient(180deg,rgba(15,23,42,0.9)_0%,rgba(2,6,23,0.95)_100%)] p-5 shadow-[0_28px_65px_-42px_rgba(34,211,238,0.75)]">
                            <div class="relative">
                                <div class="pointer-events-none absolute -left-8 -top-8 h-24 w-24 rounded-full {{ $theme['glow'] }} blur-3xl"></div>
                                <div class="pointer-events-none absolute right-0 top-0 h-full w-2/3 bg-gradient-to-r {{ $theme['surface'] }}"></div>
                                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <div class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] {{ $theme['chip'] }}">
                                            {!! $icon($currentSection, 'h-3.5 w-3.5') !!}
                                            {{ $eyebrow ?? 'Workspace' }}
                                        </div>
                                        <p class="mt-3 text-base font-semibold text-white sm:text-lg">{{ $theme['title'] }}</p>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 sm:flex sm:flex-wrap">
                                        @foreach ($navItems as $item)
                                            <a href="{{ $item['route'] }}" class="{{ $item['active'] ? 'border-white/20 bg-white/12 text-white' : 'border-white/8 bg-white/5 text-slate-300' }} flex items-center gap-2 rounded-2xl border px-3 py-2 text-xs font-semibold transition hover:border-white/18 hover:bg-white/10 hover:text-white">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br {{ $item['iconWrap'] }}">
                                                    {!! $icon($item['icon'], 'h-4 w-4') !!}
                                                </span>
                                                <span class="hidden sm:inline">{{ $item['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    <div class="mx-auto max-w-7xl">
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

                <nav class="sticky bottom-0 border-t border-white/10 bg-slate-950/92 px-4 py-3 backdrop-blur-2xl lg:hidden">
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($navItems as $item)
                            <a href="{{ $item['route'] }}" aria-label="{{ $item['label'] }}" class="{{ $item['active'] ? 'scale-[1.03] border-white/12 bg-white/10 text-white shadow-[0_18px_40px_-28px_rgba(15,23,42,0.9)]' : 'border-transparent text-slate-400' }} flex flex-col items-center justify-center gap-1.5 rounded-[1.35rem] border px-2 py-2 transition">
                                <span class="{{ $item['active'] ? 'ring-2 ring-white/15 ring-offset-2 ring-offset-slate-950' : '' }} flex h-11 w-11 items-center justify-center rounded-[1.15rem] bg-gradient-to-br {{ $item['iconWrap'] }} shadow-[0_18px_45px_-24px_rgba(15,23,42,0.95)]">
                                    {!! $mobileIcon($item['icon']) !!}
                                </span>
                                <span class="{{ $item['active'] ? 'text-white' : 'text-slate-400' }} text-[10px] font-semibold leading-none tracking-[0.04em]">
                                    {{ $item['label'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </nav>
            </div>
        </div>

        @include('layouts.partials.pwa-register')
    </body>
</html>
