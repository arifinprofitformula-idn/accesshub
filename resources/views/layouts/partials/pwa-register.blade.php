<script>
    const _PWA_DEV = /localhost|\.test$|\.local$|127\.0\.0\.1/.test(location.hostname);

    (() => {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        /* ── Config ───────────────────────────────────────────── */

        const SEL = {
            install:        '[data-pwa-install]',
            installCard:    '[data-pwa-install-card]',
            installDismiss: '[data-pwa-install-dismiss]',
            update:         '[data-pwa-update]',
            updateRefresh:  '[data-pwa-update-refresh]',
        };

        const KEY_DISMISSED_AT = 'accesshub-pwa-dismissed-at';
        const KEY_SESSION      = 'accesshub-pwa-dismissed-session';
        const KEY_INSTALLED    = 'accesshub-pwa-installed';
        const DISMISS_DURATION = 1000 * 60 * 60 * 24 * 14; // 14 days

        let deferredPrompt = null;
        let waitingWorker  = null;
        let refreshing     = false;

        /* ── Storage helpers ──────────────────────────────────── */

        const store = {
            get:    (s, k)    => { try { return s.getItem(k); }    catch { return null; } },
            set:    (s, k, v) => { try { s.setItem(k, v); }        catch { /* ignore */ } },
            remove: (s, k)    => { try { s.removeItem(k); }        catch { /* ignore */ } },
        };

        /* ── PWA state checks ─────────────────────────────────── */

        const isStandalone = () =>
            window.matchMedia('(display-mode: standalone)').matches ||
            navigator.standalone === true;

        const isInstalled = () =>
            store.get(localStorage, KEY_INSTALLED) === '1';

        const isDismissed = () => {
            if (isInstalled())                                          return true;
            if (store.get(sessionStorage, KEY_SESSION) === '1')        return true;
            const ts = Number.parseInt(store.get(localStorage, KEY_DISMISSED_AT) || '', 10);
            if (!Number.isFinite(ts))                                   return false;
            if ((Date.now() - ts) < DISMISS_DURATION)                  return true;
            store.remove(localStorage, KEY_DISMISSED_AT);
            return false;
        };

        const markDismissed = () => {
            store.set(sessionStorage, KEY_SESSION, '1');
            store.set(localStorage, KEY_DISMISSED_AT, String(Date.now()));
        };

        const markInstalled = () => {
            store.set(localStorage, KEY_INSTALLED, '1');
            store.remove(sessionStorage, KEY_SESSION);
            store.remove(localStorage, KEY_DISMISSED_AT);
        };

        /* ── Card helpers ─────────────────────────────────────── */

        const q = (sel) => document.querySelector(sel);

        const hideInstallCard = () => {
            const el = q(SEL.installCard);
            if (el) el.hidden = true;
        };

        const showInstallCard = (devMode = false) => {
            const el = q(SEL.installCard);
            if (!el) return;

            if (devMode) {
                // In dev, show card but disable install button (no real prompt)
                el.hidden = false;
                const btn = el.querySelector(SEL.install);
                if (btn && !deferredPrompt) {
                    btn.textContent = 'Install (HTTPS diperlukan)';
                    btn.style.opacity = '0.5';
                    btn.style.cursor  = 'not-allowed';
                }
                return;
            }

            el.hidden = false;
        };

        const maybeShowInstallCard = () => {
            if (isStandalone() || isDismissed()) return;

            if (deferredPrompt) {
                showInstallCard();
                return;
            }

            // DEV fallback: show card without real install prompt for UI testing
            if (_PWA_DEV) {
                showInstallCard(true);
            }
        };

        const showUpdateCard = () => {
            const el = q(SEL.update);
            if (el) el.hidden = false;
        };

        const hideUpdateCard = () => {
            const el = q(SEL.update);
            if (el) el.hidden = true;
        };

        /* ── Service worker ───────────────────────────────────── */

        const registerWorker = async () => {
            try {
                const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

                if (_PWA_DEV) console.info('[PWA] Service worker registered.');

                if (reg.waiting) {
                    waitingWorker = reg.waiting;
                    showUpdateCard();
                }

                reg.addEventListener('updatefound', () => {
                    const w = reg.installing;
                    if (!w) return;
                    w.addEventListener('statechange', () => {
                        if (w.state === 'installed' && navigator.serviceWorker.controller) {
                            waitingWorker = w;
                            showUpdateCard();
                        }
                    });
                });
            } catch (err) {
                if (_PWA_DEV) console.warn('[PWA] SW registration failed:', err);
            }
        };

        /* ── Browser events ───────────────────────────────────── */

        // Capture install prompt (requires HTTPS in production)
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Re-enable install button in case it was disabled by dev fallback
            const card = q(SEL.installCard);
            if (card) {
                const btn = card.querySelector(SEL.install);
                if (btn) {
                    btn.textContent = 'Install Sekarang';
                    btn.style.opacity = '';
                    btn.style.cursor  = '';
                }
            }

            maybeShowInstallCard();
        });

        // App installed by OS/browser
        window.addEventListener('appinstalled', () => {
            deferredPrompt = null;
            markInstalled();
            hideInstallCard();
            if (_PWA_DEV) console.info('[PWA] App installed.');
        });

        // SW took over → reload to use new version
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) return;
            refreshing = true;
            window.location.reload();
        });

        /* ── Click delegation ─────────────────────────────────── */

        document.addEventListener('click', async (e) => {

            // ① Install button
            const installBtn = e.target.closest(SEL.install);
            if (installBtn) {
                if (!deferredPrompt) return; // no-op in dev without real prompt
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                deferredPrompt = null;
                if (outcome === 'accepted') markInstalled();
                hideInstallCard();
                return;
            }

            // ② Close install card
            const closeBtn = e.target.closest(SEL.installDismiss);
            if (closeBtn) {
                e.preventDefault();
                e.stopPropagation();
                markDismissed();
                hideInstallCard();
                return;
            }

            // ③ Refresh / apply update
            const refreshBtn = e.target.closest(SEL.updateRefresh);
            if (refreshBtn) {
                hideUpdateCard();
                if (waitingWorker) {
                    waitingWorker.postMessage({ type: 'SKIP_WAITING' });
                    // Force reload as fallback if controllerchange doesn't fire
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    window.location.reload();
                }
                return;
            }
        });

        /* ── Init ─────────────────────────────────────────────── */

        document.addEventListener('DOMContentLoaded', () => {
            registerWorker();

            // Show install card — DEV: always try; PROD: only if beforeinstallprompt set
            if (!isStandalone() && !isInstalled()) {
                maybeShowInstallCard();
            }
        });

    })();
</script>
