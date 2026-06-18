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

const bonHasVisibleModal = () => {
    return Boolean(document.querySelector(
        '[aria-modal="true"]:not(.hidden), [data-tool-modal]:not(.hidden), [data-bon-service-modal]:not(.hidden), [data-growth-modal]:not(.hidden)'
    ));
};

const bonUnlockPageScrollIfSafe = () => {
    if (bonHasVisibleModal()) {
        return;
    }

    document.documentElement.classList.remove('bon-modal-open', 'bon-menu-open', 'overflow-hidden');
    document.body.classList.remove('bon-modal-open', 'bon-menu-open', 'overflow-hidden');

    if (document.documentElement.style.overflow === 'hidden') {
        document.documentElement.style.overflow = '';
    }

    if (document.body.style.overflow === 'hidden') {
        document.body.style.overflow = '';
    }
};

const bonCloseMobileMenus = (except = null) => {
    document.querySelectorAll('details[data-mobile-menu][open]').forEach((menu) => {
        if (menu !== except) {
            menu.open = false;
        }
    });
};

const bonSyncMobileMenuScrollLock = () => {
    bonUnlockPageScrollIfSafe();
};

document.addEventListener('DOMContentLoaded', () => {
    const mobileMenus = Array.from(document.querySelectorAll('details[data-mobile-menu]'));

    mobileMenus.forEach((menu) => {
        menu.addEventListener('toggle', () => {
            if (menu.open) {
                bonCloseMobileMenus(menu);
                bonUnlockPageScrollIfSafe();
                return;
            }

            requestAnimationFrame(bonSyncMobileMenuScrollLock);
        });

        menu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                menu.open = false;
                requestAnimationFrame(bonUnlockPageScrollIfSafe);
            });
        });

        const menuOverlay = Array.from(menu.children).find((child) => child.tagName === 'DIV');

        menuOverlay?.addEventListener('click', (event) => {
            if (event.target === menuOverlay) {
                menu.open = false;
                requestAnimationFrame(bonUnlockPageScrollIfSafe);
            }
        });
    });

    document.addEventListener('click', (event) => {
        const clickedMenu = event.target.closest?.('details[data-mobile-menu]');

        if (!clickedMenu) {
            bonCloseMobileMenus();
            requestAnimationFrame(bonUnlockPageScrollIfSafe);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            bonCloseMobileMenus();
            requestAnimationFrame(bonUnlockPageScrollIfSafe);
        }
    });

    window.addEventListener('pageshow', () => {
        bonCloseMobileMenus();
        requestAnimationFrame(bonUnlockPageScrollIfSafe);
    });

    window.addEventListener('pagehide', () => {
        bonCloseMobileMenus();
        requestAnimationFrame(bonUnlockPageScrollIfSafe);
    });
});
