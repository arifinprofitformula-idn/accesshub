<script>
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
        const installDismissKey = 'accesshub-pwa-install-dismissed-at';
        const installDismissSessionKey = 'accesshub-pwa-install-dismissed-session';
        const installDismissDurationMs = 1000 * 60 * 60 * 24 * 14;

        let deferredInstallPrompt = null;
        let waitingWorker = null;
        let refreshing = false;

        const isStandalone = () =>
            window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        const get = (selector) => document.querySelector(selector);

        const updateOnlineStatus = () => {
            const status = get(selectors.status);
            const label = get(selectors.statusLabel);

            if (!status || !label) {
                return;
            }

            const online = navigator.onLine;

            status.dataset.state = online ? 'online' : 'offline';
            label.textContent = online ? 'Online' : 'Offline';
        };

        const safeStorageGet = (storage, key) => {
            try {
                return storage.getItem(key);
            } catch (error) {
                return null;
            }
        };

        const safeStorageSet = (storage, key, value) => {
            try {
                storage.setItem(key, value);
            } catch (error) {
                // Ignore storage failures.
            }
        };

        const safeStorageRemove = (storage, key) => {
            try {
                storage.removeItem(key);
            } catch (error) {
                // Ignore storage failures.
            }
        };

        const isInstallDismissed = () => {
            if (safeStorageGet(sessionStorage, installDismissSessionKey) === '1') {
                return true;
            }

            const dismissedAt = Number.parseInt(safeStorageGet(localStorage, installDismissKey) || '', 10);

            if (!Number.isFinite(dismissedAt)) {
                return false;
            }

            if ((Date.now() - dismissedAt) < installDismissDurationMs) {
                return true;
            }

            safeStorageRemove(localStorage, installDismissKey);
            return false;
        };

        const setInstallDismissed = (value) => {
            if (value) {
                safeStorageSet(sessionStorage, installDismissSessionKey, '1');
                safeStorageSet(localStorage, installDismissKey, String(Date.now()));
                return;
            }

            safeStorageRemove(sessionStorage, installDismissSessionKey);
            safeStorageRemove(localStorage, installDismissKey);
        };

        const hideInstallCard = () => {
            const card = get(selectors.installCard);

            if (card) {
                card.hidden = true;
            }
        };

        const maybeShowInstallCard = () => {
            const card = get(selectors.installCard);

            if (!card) {
                return;
            }

            const shouldShow = deferredInstallPrompt && !isStandalone() && !isInstallDismissed();
            card.hidden = !shouldShow;
        };

        const showUpdateNotice = () => {
            const update = get(selectors.update);

            if (update) {
                update.hidden = false;
            }
        };

        const registerWorker = async () => {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

                if (!@json(app()->environment('production'))) {
                    console.info('[AccessHub PWA] Service worker registered.');
                }

                if (registration.waiting) {
                    waitingWorker = registration.waiting;
                    showUpdateNotice();
                }

                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;

                    if (!newWorker) {
                        return;
                    }

                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            waitingWorker = newWorker;
                            showUpdateNotice();
                        }
                    });
                });
            } catch (error) {
                if (!@json(app()->environment('production'))) {
                    console.warn('[AccessHub PWA] Service worker registration failed.', error);
                }
            }
        };

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredInstallPrompt = event;
            maybeShowInstallCard();
        });

        window.addEventListener('appinstalled', () => {
            deferredInstallPrompt = null;
            setInstallDismissed(true);
            hideInstallCard();
        });

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        document.addEventListener('click', async (event) => {
            const installButton = event.target.closest(selectors.install);

            if (installButton) {
                if (!deferredInstallPrompt) {
                    return;
                }

                deferredInstallPrompt.prompt();
                const choice = await deferredInstallPrompt.userChoice;
                deferredInstallPrompt = null;

                if (choice?.outcome === 'accepted') {
                    setInstallDismissed(true);
                }

                hideInstallCard();
                return;
            }

            const dismissButton = event.target.closest(selectors.installDismiss);

            if (dismissButton) {
                event.preventDefault();
                setInstallDismissed(true);
                hideInstallCard();
                return;
            }

            const updateButton = event.target.closest(selectors.updateRefresh);

            if (updateButton && waitingWorker) {
                waitingWorker.postMessage({ type: 'SKIP_WAITING' });
            }
        });

        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) {
                return;
            }

            refreshing = true;
            window.location.reload();
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateOnlineStatus();
            maybeShowInstallCard();
            registerWorker();
        });
    })();
</script>
