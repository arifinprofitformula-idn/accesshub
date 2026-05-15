<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'AccessHub') }} - Login</title>
        <style>
            :root {
                color-scheme: dark;
                --bg: #08111f;
                --panel: rgba(8, 17, 31, 0.88);
                --panel-border: rgba(148, 163, 184, 0.22);
                --text: #e2e8f0;
                --muted: #94a3b8;
                --accent: #38bdf8;
                --accent-2: #0ea5e9;
                --danger-bg: rgba(127, 29, 29, 0.45);
                --danger-border: rgba(248, 113, 113, 0.4);
                --danger-text: #fecaca;
                --success-bg: rgba(20, 83, 45, 0.45);
                --success-border: rgba(74, 222, 128, 0.35);
                --success-text: #bbf7d0;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 32%),
                    radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.14), transparent 30%),
                    linear-gradient(180deg, #020617 0%, var(--bg) 100%);
                color: var(--text);
            }

            .page {
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 24px;
            }

            .shell {
                width: 100%;
                max-width: 1080px;
                display: grid;
                gap: 28px;
                grid-template-columns: 1.15fr 0.85fr;
                align-items: center;
            }

            .hero,
            .panel {
                border: 1px solid var(--panel-border);
                background: var(--panel);
                backdrop-filter: blur(14px);
                border-radius: 28px;
                box-shadow: 0 28px 80px rgba(2, 6, 23, 0.45);
            }

            .hero {
                padding: 40px;
            }

            .panel {
                padding: 32px;
            }

            .eyebrow {
                margin: 0 0 14px;
                color: #7dd3fc;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.26em;
                text-transform: uppercase;
            }

            h1,
            h2 {
                margin: 0;
                line-height: 1.1;
            }

            h1 {
                font-size: clamp(34px, 4vw, 56px);
                max-width: 10ch;
            }

            h2 {
                font-size: 30px;
                text-align: center;
            }

            .copy,
            .subcopy {
                color: var(--muted);
                line-height: 1.7;
            }

            .copy {
                margin: 18px 0 0;
                max-width: 60ch;
            }

            .metrics {
                margin-top: 28px;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }

            .metric {
                border-radius: 20px;
                padding: 18px;
                background: rgba(15, 23, 42, 0.76);
                border: 1px solid rgba(148, 163, 184, 0.12);
            }

            .metric strong {
                display: block;
                margin-bottom: 8px;
                font-size: 13px;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: #bae6fd;
            }

            .brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 14px;
                margin-bottom: 26px;
                text-align: center;
            }

            .logo {
                width: 82px;
                height: 82px;
                border-radius: 24px;
                background: linear-gradient(135deg, rgba(56, 189, 248, 0.24), rgba(14, 165, 233, 0.1));
                border: 1px solid rgba(125, 211, 252, 0.24);
                display: grid;
                place-items: center;
                overflow: hidden;
            }

            .logo img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .logo-fallback {
                font-size: 28px;
                font-weight: 700;
                color: #e0f2fe;
            }

            .subcopy {
                margin-top: 10px;
                text-align: center;
            }

            .alert {
                border-radius: 16px;
                padding: 14px 16px;
                margin-bottom: 18px;
                font-size: 14px;
                line-height: 1.6;
            }

            .alert-success {
                background: var(--success-bg);
                border: 1px solid var(--success-border);
                color: var(--success-text);
            }

            .alert-error {
                background: var(--danger-bg);
                border: 1px solid var(--danger-border);
                color: var(--danger-text);
            }

            .alert-error ul {
                margin: 8px 0 0 18px;
                padding: 0;
            }

            form {
                display: grid;
                gap: 18px;
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-size: 14px;
                font-weight: 600;
                color: #cbd5e1;
            }

            input[type="email"],
            input[type="password"] {
                width: 100%;
                border: 1px solid rgba(148, 163, 184, 0.22);
                border-radius: 14px;
                background: rgba(15, 23, 42, 0.88);
                color: var(--text);
                padding: 14px 15px;
                font-size: 15px;
                outline: none;
            }

            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: rgba(56, 189, 248, 0.55);
                box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
            }

            .row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
            }

            .checkbox {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                color: #cbd5e1;
                font-size: 14px;
            }

            .checkbox input {
                accent-color: var(--accent);
            }

            .link {
                color: #7dd3fc;
                text-decoration: none;
                font-weight: 600;
            }

            .link:hover {
                text-decoration: underline;
            }

            .button {
                border: 0;
                border-radius: 14px;
                padding: 14px 18px;
                font-size: 15px;
                font-weight: 700;
                cursor: pointer;
                color: #082f49;
                background: linear-gradient(135deg, #67e8f9 0%, #38bdf8 48%, #0ea5e9 100%);
            }

            .helper {
                margin-top: 18px;
                padding-top: 18px;
                border-top: 1px solid rgba(148, 163, 184, 0.14);
                color: var(--muted);
                font-size: 14px;
                line-height: 1.7;
            }

            .helper strong {
                color: #e2e8f0;
            }

            @media (max-width: 960px) {
                .shell {
                    grid-template-columns: 1fr;
                }

                .hero {
                    display: none;
                }
            }

            @media (max-width: 640px) {
                .page {
                    padding: 16px;
                }

                .panel {
                    padding: 24px 18px;
                    border-radius: 22px;
                }

                .metrics {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <main class="page">
            <div class="shell">
                <section class="hero">
                    <p class="eyebrow">AccessHub Workspace</p>
                    <h1>Satu Dashboard untuk Semua Akses Kerja.</h1>
                    <p class="copy">
                        Link penting, data akses, dan favorit tim tersusun rapi agar kerja harian lebih cepat.
                    </p>

                    <div class="metrics">
                        <article class="metric">
                            <strong>Lebih Cepat</strong>
                            Temukan link dan akses penting tanpa bolak-balik chat.
                        </article>
                        <article class="metric">
                            <strong>Lebih Rapi</strong>
                            Semua kebutuhan kerja tersusun per kategori, role, dan tim.
                        </article>
                        <article class="metric">
                            <strong>Lebih Aman</strong>
                            Akses yang sesuai dengan peran dan izin.
                        </article>
                    </div>
                </section>

                <section class="panel">
                    <div class="brand">
                        <div class="logo">
                            @if (file_exists(public_path('images/accesshub-auth-logo.png')))
                                <img src="{{ asset('images/accesshub-auth-logo.png') }}" alt="AccessHub logo">
                            @else
                                <div class="logo-fallback">AH</div>
                            @endif
                        </div>

                        <div>
                            <p class="eyebrow" style="margin-bottom: 10px;">User Login</p>
                            <h2>Masuk ke AccessHub</h2>
                            <p class="subcopy">
                                Akses Penting dalam Genggaman Anda
                            </p>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error">
                            <strong>Login belum berhasil.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div>
                            <label for="email">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="your@email.com"
                            >
                        </div>

                        <div>
                            <label for="password">Password</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password Anda"
                            >
                        </div>

                        <div class="row">
                            <label for="remember_me" class="checkbox">
                                <input id="remember_me" type="checkbox" name="remember">
                                <span>Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="link" href="{{ route('password.request') }}">Lupa password?</a>
                            @endif
                        </div>

                        <button type="submit" class="button">Masuk ke Dashboard</button>
                    </form>

                </section>
            </div>
        </main>
    </body>
</html>
