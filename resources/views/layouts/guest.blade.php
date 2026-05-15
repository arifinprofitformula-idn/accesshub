<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @include('layouts.partials.pwa-head')
        @include('layouts.partials.asset-loader')
    </head>
    <body class="auth-grid-bg min-h-screen font-sans text-slate-100 antialiased">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="glow-orb -left-20 top-0 h-72 w-72 bg-cyan-400/20"></div>
            <div class="glow-orb -right-12 bottom-0 h-80 w-80 bg-violet-500/20"></div>
            <div class="glow-orb left-1/2 top-1/3 h-40 w-40 -translate-x-1/2 bg-amber-300/10"></div>

            <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid w-full gap-10 lg:grid-cols-[1.1fr_minmax(0,28rem)] lg:items-center">
                    <section class="relative hidden lg:block">
                        <div class="relative max-w-2xl">
                            <div class="hub-ring h-[19rem] w-[19rem] opacity-70"></div>
                            <div class="hub-ring h-[27rem] w-[27rem] border-violet-400/12"></div>
                            <div class="hub-ring h-[35rem] w-[35rem] border-cyan-300/10"></div>

                            <div class="relative rounded-[2rem] border border-white/8 bg-slate-950/35 p-8 backdrop-blur-sm">
                                <div class="mb-8 flex items-center gap-4">
                                    @if (file_exists(public_path('images/accesshub-auth-logo.png')))
                                        <img
                                            src="{{ asset('images/accesshub-auth-logo.png') }}"
                                            alt="AccessHub logo"
                                            class="h-24 w-24 rounded-[1.5rem] object-cover shadow-[0_0_45px_rgba(34,211,238,0.18)]"
                                        >
                                    @else
                                        <div class="accesshub-logo-fallback">
                                            <svg viewBox="0 0 24 24" class="relative z-10 h-10 w-10 text-cyan-200" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 13.5l3-3m-7 6 3.25-3.25m4 4.75 3.25-3.25M7.5 7.5l2-2a3 3 0 014.243 0l2.757 2.757a3 3 0 010 4.243l-2 2m-5 0-2.757-2.757a3 3 0 010-4.243l2-2" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-cyan-300/80">AccessHub</p>
                                        <h1 class="mt-2 text-4xl font-semibold tracking-tight text-slate-50">Work access, organized like a command center.</h1>
                                    </div>
                                </div>

                                <p class="max-w-xl text-base leading-8 text-slate-300/80">
                                    Securely navigate your team’s links, platforms, and operational access metadata in one premium workspace built for speed, clarity, and control.
                                </p>

                                <div class="mt-10 grid gap-4 sm:grid-cols-3">
                                    <div class="rounded-2xl border border-cyan-400/15 bg-slate-900/60 p-4">
                                        <p class="text-xs uppercase tracking-[0.25em] text-cyan-300/70">Fast Access</p>
                                        <p class="mt-2 text-sm text-slate-300">Quick links, sharp search, and low-friction workflows.</p>
                                    </div>
                                    <div class="rounded-2xl border border-violet-400/15 bg-slate-900/60 p-4">
                                        <p class="text-xs uppercase tracking-[0.25em] text-violet-300/70">Role Aware</p>
                                        <p class="mt-2 text-sm text-slate-300">Visibility stays aligned with role and operational needs.</p>
                                    </div>
                                    <div class="rounded-2xl border border-amber-300/15 bg-slate-900/60 p-4">
                                        <p class="text-xs uppercase tracking-[0.25em] text-amber-200/70">Security First</p>
                                        <p class="mt-2 text-sm text-slate-300">No platform passwords stored inside the application.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="relative">
                        <div class="glass-card neon-border mx-auto w-full max-w-lg p-5 sm:p-8">
                            <div class="mb-8 text-center">
                                <a href="{{ route('login') }}" class="inline-flex flex-col items-center gap-4">
                                    @if (file_exists(public_path('images/accesshub-auth-logo.png')))
                                        <img
                                            src="{{ asset('images/accesshub-auth-logo.png') }}"
                                            alt="AccessHub logo"
                                            class="h-20 w-20 rounded-[1.5rem] object-cover shadow-[0_0_45px_rgba(34,211,238,0.2)] sm:h-24 sm:w-24"
                                        >
                                    @else
                                        <div class="accesshub-logo-fallback">
                                            <svg viewBox="0 0 24 24" class="relative z-10 h-10 w-10 text-cyan-200" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 13.5l3-3m-7 6 3.25-3.25m4 4.75 3.25-3.25M7.5 7.5l2-2a3 3 0 014.243 0l2.757 2.757a3 3 0 010 4.243l-2 2m-5 0-2.757-2.757a3 3 0 010-4.243l2-2" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-2xl font-semibold tracking-tight text-slate-50">AccessHub</p>
                                        <p class="mt-1 text-xs font-medium uppercase tracking-[0.35em] text-slate-400">Your Work Access Command Center</p>
                                    </div>
                                </a>
                            </div>

                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </div>
        </div>

        @include('layouts.partials.pwa-register')
    </body>
</html>
