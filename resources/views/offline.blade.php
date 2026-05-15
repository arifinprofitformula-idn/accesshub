<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'AccessHub') }} - Offline</title>
        <style>
            :root {
                color-scheme: dark;
                --bg: #020617;
                --panel: rgba(8, 17, 31, 0.84);
                --panel-border: rgba(56, 189, 248, 0.18);
                --text: #e2e8f0;
                --muted: #94a3b8;
                --accent: #38bdf8;
                --accent-2: #8b5cf6;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                color: var(--text);
                background:
                    radial-gradient(circle at top left, rgba(34, 211, 238, 0.2), transparent 28rem),
                    radial-gradient(circle at bottom right, rgba(139, 92, 246, 0.18), transparent 24rem),
                    linear-gradient(180deg, #020617 0%, #08111f 100%);
            }

            .page {
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 24px;
            }

            .card {
                width: 100%;
                max-width: 560px;
                border-radius: 32px;
                border: 1px solid var(--panel-border);
                background: var(--panel);
                backdrop-filter: blur(16px);
                box-shadow: 0 30px 100px rgba(2, 6, 23, 0.5);
                padding: 32px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .card::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: inherit;
                padding: 1px;
                background: linear-gradient(135deg, rgba(34, 211, 238, 0.35), rgba(139, 92, 246, 0.22), rgba(251, 191, 36, 0.14));
                -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
                -webkit-mask-composite: xor;
                mask-composite: exclude;
                pointer-events: none;
            }

            .logo {
                width: 96px;
                height: 96px;
                margin: 0 auto 20px;
                border-radius: 28px;
                background: linear-gradient(135deg, rgba(34, 211, 238, 0.16), rgba(59, 130, 246, 0.14), rgba(139, 92, 246, 0.14));
                box-shadow: 0 0 35px rgba(34, 211, 238, 0.18);
                display: grid;
                place-items: center;
                overflow: hidden;
            }

            .logo img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .eyebrow {
                margin: 0 0 10px;
                color: #7dd3fc;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.28em;
                text-transform: uppercase;
            }

            h1 {
                margin: 0;
                font-size: clamp(30px, 4vw, 42px);
                line-height: 1.08;
            }

            p {
                margin: 16px auto 0;
                max-width: 34ch;
                color: var(--muted);
                line-height: 1.75;
            }

            .actions {
                margin-top: 28px;
                display: flex;
                justify-content: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .button,
            .button-secondary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 16px;
                padding: 14px 18px;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
                cursor: pointer;
                min-width: 160px;
            }

            .button {
                border: 0;
                color: #082f49;
                background: linear-gradient(135deg, #67e8f9 0%, #38bdf8 48%, #0ea5e9 100%);
            }

            .button-secondary {
                color: #cbd5e1;
                border: 1px solid rgba(148, 163, 184, 0.22);
                background: rgba(15, 23, 42, 0.78);
            }

            .note {
                margin-top: 22px;
                font-size: 13px;
                color: #64748b;
            }

            @media (max-width: 640px) {
                .page {
                    padding: 16px;
                }

                .card {
                    padding: 24px 18px;
                    border-radius: 24px;
                }

                .actions {
                    flex-direction: column;
                }

                .button,
                .button-secondary {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <main class="page">
            <section class="card">
                <div class="logo">
                    @if (file_exists(public_path('images/accesshub-auth-logo.png')))
                        <img src="{{ asset('images/accesshub-auth-logo.png') }}" alt="AccessHub logo">
                    @else
                        <span style="font-size: 28px; font-weight: 700; color: #e0f2fe;">AH</span>
                    @endif
                </div>

                <p class="eyebrow">Offline Mode</p>
                <h1>AccessHub sedang offline.</h1>
                <p>
                    Koneksi internet sedang tidak tersedia. Coba sambungkan kembali lalu muat ulang untuk melanjutkan pekerjaan Anda.
                </p>

                <div class="actions">
                    <button type="button" class="button" onclick="window.location.reload()">Coba Lagi</button>
                    <a href="{{ route('dashboard') }}" class="button-secondary">Kembali ke Dashboard</a>
                </div>

                <div class="note">
                    Beberapa halaman sensitif tidak disimpan offline demi menjaga keamanan akses tim.
                </div>
            </section>
        </main>
    </body>
</html>
