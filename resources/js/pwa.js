const isPwaDev = /localhost|\.test$|\.local$|127\.0\.0\.1/.test(location.hostname);

if ('serviceWorker' in navigator) {
    const selectors = {
        install: '[data-pwa-install]',
        installCard: '[data-pwa-install-card]',
        installDismiss: '[data-pwa-install-dismiss]',
        update: '[data-pwa-update]',
        updateRefresh: '[data-pwa-update-refresh]',
    };

    const keyDismissedAt = 'accesshub-pwa-dismissed-at';
    const keySession = 'accesshub-pwa-dismissed-session';
    const keyInstalled = 'accesshub-pwa-installed';
    const dismissDuration = 1000 * 60 * 60 * 24 * 14;

    let deferredPrompt = null;
    let waitingWorker = null;
    let refreshing = false;

    const store = {
        get: (storage, key) => {
            try {
                return storage.getItem(key);
            } catch {
                return null;
            }
        },
        set: (storage, key, value) => {
            try {
                storage.setItem(key, value);
            } catch {
                // Ignore storage failures.
            }
        },
        remove: (storage, key) => {
            try {
                storage.removeItem(key);
            } catch {
                // Ignore storage failures.
            }
        },
    };

    const query = (selector) => document.querySelector(selector);

    const isStandalone = () =>
        window.matchMedia('(display-mode: standalone)').matches ||
        navigator.standalone === true;

    const isInstalled = () => store.get(localStorage, keyInstalled) === '1';

    const isDismissed = () => {
        if (isInstalled()) {
            return true;
        }

        if (store.get(sessionStorage, keySession) === '1') {
            return true;
        }

        const dismissedAt = Number.parseInt(store.get(localStorage, keyDismissedAt) || '', 10);

        if (!Number.isFinite(dismissedAt)) {
            return false;
        }

        if (Date.now() - dismissedAt < dismissDuration) {
            return true;
        }

        store.remove(localStorage, keyDismissedAt);

        return false;
    };

    const markDismissed = () => {
        store.set(sessionStorage, keySession, '1');
        store.set(localStorage, keyDismissedAt, String(Date.now()));
    };

    const markInstalled = () => {
        store.set(localStorage, keyInstalled, '1');
        store.remove(sessionStorage, keySession);
        store.remove(localStorage, keyDismissedAt);
    };

    const hideInstallCard = () => {
        const element = query(selectors.installCard);

        if (element) {
            element.hidden = true;
        }
    };

    const hideUpdateCard = () => {
        const element = query(selectors.update);

        if (element) {
            element.hidden = true;
        }
    };

    const showUpdateCard = () => {
        const element = query(selectors.update);

        if (element) {
            element.hidden = false;
        }
    };

    const showInstallCard = (devMode = false) => {
        const element = query(selectors.installCard);

        if (!element) {
            return;
        }

        element.hidden = false;

        if (!devMode) {
            return;
        }

        const button = element.querySelector(selectors.install);

        if (button && !deferredPrompt) {
            button.textContent = 'Install (HTTPS diperlukan)';
            button.style.opacity = '0.5';
            button.style.cursor = 'not-allowed';
        }
    };

    const maybeShowInstallCard = () => {
        if (isStandalone() || isDismissed()) {
            return;
        }

        if (deferredPrompt) {
            showInstallCard();
            return;
        }

        if (isPwaDev) {
            showInstallCard(true);
        }
    };

    const registerWorker = async () => {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

            if (isPwaDev) {
                console.info('[PWA] Service worker registered.');
            }

            if (registration.waiting) {
                waitingWorker = registration.waiting;
                showUpdateCard();
            }

            registration.addEventListener('updatefound', () => {
                const worker = registration.installing;

                if (!worker) {
                    return;
                }

                worker.addEventListener('statechange', () => {
                    if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                        waitingWorker = worker;
                        showUpdateCard();
                    }
                });
            });
        } catch (error) {
            if (isPwaDev) {
                console.warn('[PWA] SW registration failed:', error);
            }
        }
    };

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;

        const card = query(selectors.installCard);
        const button = card?.querySelector(selectors.install);

        if (button) {
            button.textContent = 'Install Sekarang';
            button.style.opacity = '';
            button.style.cursor = '';
        }

        maybeShowInstallCard();
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        markInstalled();
        hideInstallCard();

        if (isPwaDev) {
            console.info('[PWA] App installed.');
        }
    });

    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (refreshing) {
            return;
        }

        refreshing = true;
        window.location.reload();
    });

    document.addEventListener('click', async (event) => {
        const installButton = event.target.closest(selectors.install);

        if (installButton) {
            if (!deferredPrompt) {
                return;
            }

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;

            if (outcome === 'accepted') {
                markInstalled();
            }

            hideInstallCard();
            return;
        }

        const closeButton = event.target.closest(selectors.installDismiss);

        if (closeButton) {
            event.preventDefault();
            event.stopPropagation();
            markDismissed();
            hideInstallCard();
            return;
        }

        const refreshButton = event.target.closest(selectors.updateRefresh);

        if (!refreshButton) {
            return;
        }

        hideUpdateCard();

        if (waitingWorker) {
            waitingWorker.postMessage({ type: 'SKIP_WAITING' });
            setTimeout(() => window.location.reload(), 600);
            return;
        }

        window.location.reload();
    });

    document.addEventListener('DOMContentLoaded', () => {
        registerWorker();

        if (!isStandalone() && !isInstalled()) {
            maybeShowInstallCard();
        }
    });
}
