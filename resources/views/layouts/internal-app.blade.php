<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'AccessHub') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $searchRoute = request()->routeIs('app.access-items.*')
            ? route('app.access-items.index')
            : route('app.links.index');

        $searchPlaceholder = request()->routeIs('app.access-items.*')
            ? 'Cari platform, username, PIC, atau catatan akses...'
            : 'Cari link, kategori, tag, atau platform...';
    @endphp
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,#dbeafe,transparent_24%),linear-gradient(180deg,#f8fafc_0%,#eef4ff_100%)] font-sans text-slate-900 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
            <aside class="hidden border-r border-white/70 bg-white/78 p-6 backdrop-blur lg:flex lg:flex-col">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-[1.25rem] bg-sky-600 text-lg font-semibold text-white shadow-lg shadow-sky-200">
                        AH
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-sky-600">Modern Workspace</p>
                        <h1 class="text-lg font-semibold text-slate-900">AccessHub</h1>
                    </div>
                </div>

                <div class="mt-8 ah-soft-card">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Quick Search</p>
                    <form method="GET" action="{{ $searchRoute }}" class="mt-3">
                        <input type="text" name="search" class="ah-input" placeholder="{{ $searchPlaceholder }}">
                    </form>
                </div>

                <nav class="mt-8 space-y-2">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard', 'app.dashboard') ? 'bg-slate-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }} flex items-center rounded-2xl px-4 py-3 text-sm font-medium transition">
                        Dashboard
                    </a>
                    <a href="{{ route('app.links.index') }}" class="{{ request()->routeIs('app.links.*') ? 'bg-sky-600 text-white shadow-lg shadow-sky-200' : 'text-slate-600 hover:bg-slate-100' }} flex items-center rounded-2xl px-4 py-3 text-sm font-medium transition">
                        Link Manager
                    </a>
                    <a href="{{ route('app.access-items.index') }}" class="{{ request()->routeIs('app.access-items.*') ? 'bg-slate-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }} flex items-center rounded-2xl px-4 py-3 text-sm font-medium transition">
                        Access Items
                    </a>
                    @if (auth()->user()->hasAnyRole(['super_admin', 'admin']))
                        <a href="{{ url('/admin') }}" class="flex items-center rounded-2xl px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
                            Admin Panel
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100' }} flex items-center rounded-2xl px-4 py-3 text-sm font-medium transition">
                        Profile
                    </a>
                </nav>

                <div class="mt-auto rounded-[1.75rem] bg-slate-950 p-5 text-white shadow-2xl shadow-slate-300/30">
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-300">Signed In</p>
                    <p class="mt-3 text-lg font-semibold">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-sm text-slate-300">{{ auth()->user()->email }}</p>
                    <p class="mt-4 text-xs text-slate-400">Akses cepat untuk tim bisnis, marketing, operasional, dan admin website.</p>
                </div>
            </aside>

            <div class="flex min-h-screen flex-col">
                <header class="border-b border-white/70 bg-white/72 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-7xl">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-[1.15rem] bg-slate-950 text-sm font-semibold text-white lg:hidden">
                                    AH
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-sky-600">{{ $eyebrow ?? 'Workspace' }}</p>
                                    <h1 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">{{ $heading ?? 'AccessHub' }}</h1>
                                    @isset($subheading)
                                        <p class="mt-1 max-w-2xl text-sm text-slate-500">{{ $subheading }}</p>
                                    @endisset
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <form method="GET" action="{{ $searchRoute }}" class="w-full sm:w-[320px]">
                                    <input type="text" name="search" class="ah-input" placeholder="{{ $searchPlaceholder }}">
                                </form>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="ah-secondary-btn w-full sm:w-auto">
                                        Logout
                                    </button>
                                </form>
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

                <nav class="sticky bottom-0 border-t border-slate-200 bg-white/96 px-4 py-3 backdrop-blur lg:hidden">
                    <div class="grid grid-cols-4 gap-2">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard', 'app.dashboard') ? 'bg-slate-950 text-white' : 'text-slate-500' }} rounded-2xl px-3 py-2 text-center text-[11px] font-semibold">
                            Dashboard
                        </a>
                        <a href="{{ route('app.links.index') }}" class="{{ request()->routeIs('app.links.*') ? 'bg-sky-600 text-white' : 'text-slate-500' }} rounded-2xl px-3 py-2 text-center text-[11px] font-semibold">
                            Links
                        </a>
                        <a href="{{ route('app.access-items.index') }}" class="{{ request()->routeIs('app.access-items.*') ? 'bg-slate-950 text-white' : 'text-slate-500' }} rounded-2xl px-3 py-2 text-center text-[11px] font-semibold">
                            Access
                        </a>
                        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'bg-slate-950 text-white' : 'text-slate-500' }} rounded-2xl px-3 py-2 text-center text-[11px] font-semibold">
                            Profile
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </body>
</html>
