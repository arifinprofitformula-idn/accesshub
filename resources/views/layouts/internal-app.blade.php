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
        $icon = function (string $name, string $classes = 'h-5 w-5') {
            $paths = [
                'dashboard' => '<path d="M4 7.25A2.25 2.25 0 0 1 6.25 5h3.5A2.25 2.25 0 0 1 12 7.25v3.5A2.25 2.25 0 0 1 9.75 13h-3.5A2.25 2.25 0 0 1 4 10.75v-3.5Zm8 0A2.25 2.25 0 0 1 14.25 5h3.5A2.25 2.25 0 0 1 20 7.25v3.5A2.25 2.25 0 0 1 17.75 13h-3.5A2.25 2.25 0 0 1 12 10.75v-3.5Zm-8 8A2.25 2.25 0 0 1 6.25 13h3.5A2.25 2.25 0 0 1 12 15.25v3.5A2.25 2.25 0 0 1 9.75 21h-3.5A2.25 2.25 0 0 1 4 18.75v-3.5Zm8 0A2.25 2.25 0 0 1 14.25 13h3.5A2.25 2.25 0 0 1 20 15.25v3.5A2.25 2.25 0 0 1 17.75 21h-3.5A2.25 2.25 0 0 1 12 18.75v-3.5Z" />',
                'plus' => '<path d="M12 5v14" /><path d="M5 12h14" />',
                'star' => '<path d="m12 3.75 2.625 5.32 5.875.854-4.25 4.143 1.003 5.833L12 17.14l-5.253 2.76 1.003-5.833-4.25-4.143 5.875-.854L12 3.75Z" />',
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
                'visible' => auth()->user()->can('create', \App\Models\Link::class),
            ],
            [
                'label' => 'Favorit',
                'route' => route('app.favorites'),
                'active' => request()->boolean('favorites') || request()->routeIs('app.favorites'),
                'icon' => 'star',
                'visible' => true,
            ],
            [
                'label' => 'Profil',
                'route' => route('profile.edit'),
                'active' => request()->routeIs('profile.*'),
                'icon' => 'profile',
                'visible' => true,
            ],
        ])->filter(fn (array $item): bool => $item['visible']);
    @endphp
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.14),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(139,92,246,0.12),transparent_24%),linear-gradient(180deg,#020617_0%,#081120_100%)] font-sans text-slate-100 antialiased">
        <div class="min-h-screen">
            <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur-2xl">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[1.25rem] bg-gradient-to-br from-cyan-300 via-sky-400 to-blue-600 text-lg font-semibold text-slate-950 shadow-[0_18px_45px_-24px_rgba(34,211,238,0.95)]">
                            AH
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-cyan-300">Personal Link Search</p>
                            <h1 class="text-lg font-semibold text-white">AccessHub</h1>
                        </div>
                    </a>

                    <div class="hidden items-center gap-3 md:flex">
                        @if (auth()->user()->hasAnyRole(['super_admin', 'admin']))
                            <a href="{{ url('/admin') }}" class="ah-secondary-btn gap-2">
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

                <div class="mx-auto max-w-7xl px-4 pb-5 sm:px-6 lg:px-8">
                    <div class="rounded-[1.85rem] border border-white/10 bg-[linear-gradient(135deg,rgba(15,23,42,0.96)_0%,rgba(8,17,32,0.94)_52%,rgba(91,33,182,0.12)_100%)] p-5 shadow-[0_28px_65px_-42px_rgba(34,211,238,0.75)] sm:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-cyan-300">{{ $eyebrow ?? 'Workspace' }}</p>
                                <h2 class="mt-1 text-2xl font-semibold text-white sm:text-3xl">{{ $heading ?? 'AccessHub' }}</h2>
                                @isset($subheading)
                                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">{{ $subheading }}</p>
                                @endisset
                            </div>

                            <div class="flex flex-wrap gap-2">
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

            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
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

            <nav class="sticky bottom-0 border-t border-white/10 bg-slate-950/92 px-4 py-3 backdrop-blur-2xl md:hidden">
                <div class="grid grid-cols-4 gap-2">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['route'] }}" aria-label="{{ $item['label'] }}" class="{{ $item['active'] ? 'border-cyan-300/20 bg-white/10 text-white' : 'border-transparent text-slate-400' }} flex flex-col items-center justify-center gap-1.5 rounded-[1.35rem] border px-2 py-2 transition">
                            <span class="flex h-11 w-11 items-center justify-center rounded-[1.15rem] bg-gradient-to-br from-cyan-300 via-sky-400 to-violet-500 text-slate-950 shadow-[0_18px_45px_-24px_rgba(15,23,42,0.95)]">
                                {!! $icon($item['icon']) !!}
                            </span>
                            <span class="text-[10px] font-semibold leading-none tracking-[0.04em]">
                                {{ $item['label'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </nav>
        </div>

        @include('layouts.partials.pwa-register')
    </body>
</html>
