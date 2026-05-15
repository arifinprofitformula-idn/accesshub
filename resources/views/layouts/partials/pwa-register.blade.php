<script>
    (() => {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        const selectors = {
            install: '[data-pwa-install]',
            status: '[data-pwa-status]',
            statusLabel: '[data-pwa-status-label]',
            update: '[data-pwa-update]',
            updateRefresh: '[data-pwa-update-refresh]',
        };

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

        const hideInstallButton = () => {
            const button = get(selectors.install);

            if (button) {
                button.hidden = true;
            }
        };

        const maybeShowInstallButton = () => {
            const button = get(selectors.install);

            if (!button) {
                return;
            }

            button.hidden = !(deferredInstallPrompt && !isStandalone());
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
            maybeShowInstallButton();
        });

        window.addEventListener('appinstalled', () => {
            deferredInstallPrompt = null;
            hideInstallButton();
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
                await deferredInstallPrompt.userChoice;
                deferredInstallPrompt = null;
                hideInstallButton();
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
            maybeShowInstallButton();
            registerWorker();
        });
    })();
</script>
