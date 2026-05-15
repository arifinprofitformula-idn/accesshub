<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'AccessHub') }} - Forgot Password</title>
        <style>
            :root {
                color-scheme: dark;
                --bg: #08111f;
                --panel: rgba(8, 17, 31, 0.88);
                --panel-border: rgba(148, 163, 184, 0.22);
                --text: #e2e8f0;
                --muted: #94a3b8;
                --accent: #38bdf8;
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

            .panel {
                width: 100%;
                max-width: 520px;
                padding: 32px;
                border: 1px solid var(--panel-border);
                background: var(--panel);
                backdrop-filter: blur(14px);
                border-radius: 28px;
                box-shadow: 0 28px 80px rgba(2, 6, 23, 0.45);
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

            .eyebrow {
                margin: 0 0 10px;
                color: #7dd3fc;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.26em;
                text-transform: uppercase;
            }

            h1 {
                margin: 0;
                font-size: clamp(28px, 4vw, 38px);
                line-height: 1.12;
            }

            .copy {
                margin: 12px 0 0;
                color: var(--muted);
                line-height: 1.7;
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
                margin: 0;
                padding-left: 18px;
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

            input[type="email"] {
                width: 100%;
                border: 1px solid rgba(148, 163, 184, 0.22);
                border-radius: 14px;
                background: rgba(15, 23, 42, 0.88);
                color: var(--text);
                padding: 14px 15px;
                font-size: 15px;
                outline: none;
            }

            input[type="email"]:focus {
                border-color: rgba(56, 189, 248, 0.55);
                box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
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
                text-align: center;
            }

            .link {
                color: #7dd3fc;
                text-decoration: none;
                font-weight: 600;
            }

            .link:hover {
                text-decoration: underline;
            }

            @media (max-width: 640px) {
                .page {
                    padding: 16px;
                }

                .panel {
                    padding: 24px 18px;
                    border-radius: 22px;
                }
            }
        </style>
    </head>
    <body>
        <main class="page">
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
                        <p class="eyebrow">Password Reset</p>
                        <h1>Atur ulang akses akun.</h1>
                        <p class="copy">
                            Masukkan email kerja Anda untuk menerima link reset password secara aman.
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
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
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
                            placeholder="you@company.com"
                        >
                    </div>

                    <button type="submit" class="button">Kirim Link Reset</button>
                </form>

                <div class="helper">
                    <a href="{{ route('login') }}" class="link">Kembali ke login</a>
                </div>
            </section>
        </main>
    </body>
</html>
