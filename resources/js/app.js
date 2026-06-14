import './bootstrap';

const isStandalone = () => window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {
            // PWA support should never block the main app.
        });
    });
}

let bonDeferredInstallPrompt = null;

const installDismissedUntil = () => Number(window.localStorage.getItem('bonPwaInstallDismissedUntil') || 0);

const shouldShowInstallPrompt = () => {
    return bonDeferredInstallPrompt && !isStandalone() && Date.now() > installDismissedUntil();
};

const hideInstallPrompt = () => {
    document.getElementById('bon-pwa-install')?.remove();
};

const showInstallPrompt = () => {
    if (!shouldShowInstallPrompt() || document.getElementById('bon-pwa-install')) {
        return;
    }

    const banner = document.createElement('aside');
    banner.id = 'bon-pwa-install';
    banner.className = 'bon-pwa-install';
    banner.setAttribute('role', 'dialog');
    banner.setAttribute('aria-label', 'Инсталирай BON');
    banner.innerHTML = `
        <div class="bon-pwa-install__mark">B</div>
        <div class="bon-pwa-install__copy">
            <strong>Инсталирай BON</strong>
            <span>Добави BON на началния екран за по-бърз достъп.</span>
        </div>
        <button type="button" class="bon-pwa-install__button">Инсталирай</button>
        <button type="button" class="bon-pwa-install__close" aria-label="Затвори">×</button>
    `;

    document.body.appendChild(banner);

    banner.querySelector('.bon-pwa-install__button')?.addEventListener('click', async () => {
        if (!bonDeferredInstallPrompt) {
            return;
        }

        bonDeferredInstallPrompt.prompt();

        try {
            await bonDeferredInstallPrompt.userChoice;
        } finally {
            bonDeferredInstallPrompt = null;
            hideInstallPrompt();
        }
    });

    banner.querySelector('.bon-pwa-install__close')?.addEventListener('click', () => {
        window.localStorage.setItem('bonPwaInstallDismissedUntil', String(Date.now() + 7 * 24 * 60 * 60 * 1000));
        hideInstallPrompt();
    });
};

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    bonDeferredInstallPrompt = event;
    showInstallPrompt();
});

window.addEventListener('appinstalled', () => {
    bonDeferredInstallPrompt = null;
    hideInstallPrompt();
});
