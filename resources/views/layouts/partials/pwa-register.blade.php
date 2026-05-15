<script>
    const _PWA_DEV = {{ app()->environment('production') ? 'false' : 'true' }};

    (() => {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        const selectors = {
            install: '[data-pwa-install]',
            installCard: '[data-pwa-install-card]',
            installDismiss: '[data-pwa-install-dismiss]',
            status: '[data-pwa-status]',
            statusLabel: '[data-pwa-status-label]',
            update: '[data-pwa-update]',
            updateRefresh: '[data-pwa-update-refresh]',
        };

        const installDismissKey      = 'accesshub-pwa-install-dismissed-at';
        const installDismissSession  = 'accesshub-pwa-install-dismissed-session';
        const installedKey           = 'accesshub-pwa-installed'; // permanent flag
        const installDismissDuration = 1000 * 60 * 60 * 24 * 14;

        let deferredInstallPrompt = null;
        let waitingWorker         = null;
        let refreshing            = false;

        /* ── Helpers ──────────────────────────────────────────── */

        const isStandalone = () =>
            window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true;

        const get = (sel) => document.querySelector(sel);

        const safeGet = (storage, key) => {
            try { return storage.getItem(key); } catch { return null; }
        };
        const safeSet = (storage, key, value) => {
            try { storage.setItem(key, value); } catch { /* ignore */ }
        };
        const safeDel = (storage, key) => {
            try { storage.removeItem(key); } catch { /* ignore */ }
        };

        /* ── Installed detection ──────────────────────────────── */

        const isInstalled = () =>
            safeGet(localStorage, installedKey) === '1';

        const markInstalled = () =>
            safeSet(localStorage, installedKey, '1');

        /* ── Dismiss logic (for users who clicked X) ──────────── */

        const isInstallDismissed = () => {
            // Already installed permanently → never show again
            if (isInstalled()) return true;

            // Dismissed this session
            if (safeGet(sessionStorage, installDismissSession) === '1') return true;

            // Dismissed within 14-day window
            const ts = Number.parseInt(safeGet(localStorage, installDismissKey) || '', 10);
            if (!Number.isFinite(ts)) return false;
            if ((Date.now() - ts) < installDismissDuration) return true;

            safeDel(localStorage, installDismissKey);
            return false;
        };

        const setInstallDismissed = () => {
            safeSet(sessionStorage, installDismissSession, '1');
            safeSet(localStorage, installDismissKey, String(Date.now()));
        };

        /* ── Card visibility ──────────────────────────────────── */

        const hideInstallCard = () => {
            const card = get(selectors.installCard);
            if (card) card.hidden = true;
        };

        const maybeShowInstallCard = () => {
            const card = get(selectors.installCard);
            if (!card) return;

            const shouldShow =
                deferredInstallPrompt !== null &&
                !isStandalone() &&
                !isInstallDismissed();

            card.hidden = !shouldShow;
        };

        const showUpdateCard = () => {
            const card = get(selectors.update);
            if (card) card.hidden = false;
        };

        const hideUpdateCard = () => {
            const card = get(selectors.update);
            if (card) card.hidden = true;
        };

        /* ── Online / offline status ──────────────────────────── */

        const updateOnlineStatus = () => {
            const chip  = get(selectors.status);
            const label = get(selectors.statusLabel);
            if (!chip || !label) return;

            const online = navigator.onLine;
            chip.dataset.state = online ? 'online' : 'offline';
            label.textContent  = online ? 'Online' : 'Offline';
        };

        /* ── Service worker registration ──────────────────────── */

        const registerWorker = async () => {
            try {
                const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

                if (_PWA_DEV) {
                    console.info('[AccessHub PWA] Service worker registered.');
                }

                if (reg.waiting) {
                    waitingWorker = reg.waiting;
                    showUpdateCard();
                }

                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    if (!newWorker) return;

                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            waitingWorker = newWorker;
                            showUpdateCard();
                        }
                    });
                });
            } catch (err) {
                if (_PWA_DEV) {
                    console.warn('[AccessHub PWA] Service worker registration failed.', err);
                }
            }
        };

        /* ── Event: browser ready to prompt install ───────────── */

        window.addEventListener('beforeinstallprompt', (e) => {
            // Only capture if not already installed
            if (isInstalled() || isStandalone()) return;
            e.preventDefault();
            deferredInstallPrompt = e;
            maybeShowInstallCard();
        });

        /* ── Event: app successfully installed ────────────────── */

        window.addEventListener('appinstalled', () => {
            deferredInstallPrompt = null;
            markInstalled();          // permanent — never show popup again
            hideInstallCard();
        });

        /* ── Event: online / offline ──────────────────────────── */

        window.addEventListener('online',  updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        /* ── Event: SW controller changed → reload ────────────── */

        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) return;
            refreshing = true;
            window.location.reload();
        });

        /* ── Click delegation ─────────────────────────────────── */

        document.addEventListener('click', async (e) => {

            // ① Install button
            const installBtn = e.target.closest(selectors.install);
            if (installBtn) {
                if (!deferredInstallPrompt) return;

                deferredInstallPrompt.prompt();
                const { outcome } = await deferredInstallPrompt.userChoice;
                deferredInstallPrompt = null;

                if (outcome === 'accepted') {
                    markInstalled();
                }

                hideInstallCard();
                return;
            }

            // ② Close / dismiss install card
            const dismissBtn = e.target.closest(selectors.installDismiss);
            if (dismissBtn) {
                e.preventDefault();
                e.stopPropagation();
                setInstallDismissed();
                hideInstallCard();
                return;
            }

            // ③ Refresh / update button
            const refreshBtn = e.target.closest(selectors.updateRefresh);
            if (refreshBtn) {
                hideUpdateCard();

                if (waitingWorker) {
                    // Tell waiting SW to take over; controllerchange will reload
                    waitingWorker.postMessage({ type: 'SKIP_WAITING' });
                    // Fallback: force reload after 600 ms if controllerchange doesn't fire
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    // No waiting worker → just reload
                    window.location.reload();
                }
                return;
            }
        });

        /* ── Init on DOM ready ────────────────────────────────── */

        document.addEventListener('DOMContentLoaded', () => {
            updateOnlineStatus();

            // Don't even try to show install card if PWA is already installed
            if (!isInstalled() && !isStandalone()) {
                maybeShowInstallCard();
            }

            registerWorker();
        });
    })();
</script>
