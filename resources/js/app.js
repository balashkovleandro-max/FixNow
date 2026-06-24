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
        '[aria-modal="true"]:not(.hidden):not([aria-hidden="true"]):not([data-bon-menu-portal="active"]), [data-tool-modal]:not(.hidden), [data-bon-service-modal]:not(.hidden), [data-growth-modal]:not(.hidden)'
    ));
};

let bonMenuScrollY = 0;
let bonMenuScrollLocked = false;
let bonActiveMobileMenu = null;
let bonMobileMenuBackdrop = null;
const bonScrollLockStyleProperties = ['overflow', 'height', 'position', 'top', 'left', 'right', 'width', 'touchAction'];

const bonHasOpenMobileMenu = () => Boolean(document.querySelector('details[data-mobile-menu][open]'));

const bonGetMobileMenuPanel = (menu) => {
    return menu._bonPortalPanel || Array.from(menu.children).find((child) => child.tagName === 'DIV');
};

const bonRemoveMobileMenuBackdrop = () => {
    bonMobileMenuBackdrop?.remove();
    bonMobileMenuBackdrop = null;
};

const bonClearPageScrollLockStyles = () => {
    document.documentElement.classList.remove('bon-modal-open', 'bon-menu-open', 'overflow-hidden');
    document.body.classList.remove('bon-modal-open', 'bon-menu-open', 'overflow-hidden');

    bonScrollLockStyleProperties.forEach((property) => {
        document.documentElement.style[property] = '';
        document.body.style[property] = '';
    });
};

const bonEnsureMobileMenuBackdrop = () => {
    if (bonMobileMenuBackdrop?.isConnected) {
        return bonMobileMenuBackdrop;
    }

    bonMobileMenuBackdrop = document.createElement('div');
    bonMobileMenuBackdrop.className = 'bon-mobile-menu-backdrop';
    bonMobileMenuBackdrop.setAttribute('aria-hidden', 'true');
    bonMobileMenuBackdrop.addEventListener('click', () => {
        if (bonActiveMobileMenu) {
            bonActiveMobileMenu.open = false;
            return;
        }

        bonCloseMobileMenus();
    });

    document.body.appendChild(bonMobileMenuBackdrop);

    return bonMobileMenuBackdrop;
};

const bonMountMobileMenu = (menu) => {
    const panel = bonGetMobileMenuPanel(menu);

    if (!panel) {
        return;
    }

    if (!menu._bonPortalPlaceholder && panel.parentNode) {
        menu._bonPortalPlaceholder = document.createComment('bon mobile menu portal');
        panel.parentNode.insertBefore(menu._bonPortalPlaceholder, panel);
    }

    if (!menu._bonPortalCloseButton) {
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'bon-mobile-menu-close';
        closeButton.setAttribute('aria-label', 'Затвори меню');
        closeButton.innerHTML = `
            <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.4">
                <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/>
            </svg>
        `;
        closeButton.addEventListener('click', () => {
            menu.open = false;
        });
        menu._bonPortalCloseButton = closeButton;
    }

    if (!menu._bonPortalCloseButton.isConnected) {
        panel.prepend(menu._bonPortalCloseButton);
    }

    panel.dataset.bonMenuPortal = 'active';
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-modal', 'true');
    panel.setAttribute('aria-label', 'BON мобилно меню');

    menu._bonPortalPanel = panel;
    bonEnsureMobileMenuBackdrop();
    document.body.appendChild(panel);
    bonActiveMobileMenu = menu;
};

const bonRestoreMobileMenu = (menu) => {
    const panel = menu._bonPortalPanel || document.querySelector('body > [data-bon-menu-portal="active"]');
    const placeholder = menu._bonPortalPlaceholder;

    menu._bonPortalCloseButton?.remove();

    if (panel && placeholder?.parentNode) {
        placeholder.parentNode.insertBefore(panel, placeholder);
        placeholder.remove();
    }

    if (panel) {
        panel.removeAttribute('data-bon-menu-portal');
        panel.removeAttribute('role');
        panel.removeAttribute('aria-modal');
        panel.removeAttribute('aria-label');
    }

    menu._bonPortalPanel = null;
    menu._bonPortalPlaceholder = null;

    if (bonActiveMobileMenu === menu) {
        bonActiveMobileMenu = null;
    }

    if (!bonHasOpenMobileMenu()) {
        bonRemoveMobileMenuBackdrop();
    }
};

const bonLockPageScrollForMenu = () => {
    if (bonMenuScrollLocked) {
        return;
    }

    bonMenuScrollY = window.scrollY || document.documentElement.scrollTop || 0;
    bonMenuScrollLocked = true;

    document.documentElement.classList.add('bon-menu-open', 'overflow-hidden');
    document.body.classList.add('bon-menu-open', 'overflow-hidden');

    document.documentElement.style.overflow = 'hidden';
    document.documentElement.style.height = '100%';
    document.documentElement.style.touchAction = 'none';
    document.body.style.overflow = 'hidden';
    document.body.style.position = 'fixed';
    document.body.style.top = `-${bonMenuScrollY}px`;
    document.body.style.left = '0';
    document.body.style.right = '0';
    document.body.style.width = '100%';
    document.body.style.height = '100%';
    document.body.style.touchAction = 'none';
};

const bonUnlockPageScrollIfSafe = () => {
    if (bonHasVisibleModal() || bonHasOpenMobileMenu()) {
        return;
    }

    const shouldRestoreScroll = bonMenuScrollLocked;
    const restoreScrollY = bonMenuScrollY;

    bonClearPageScrollLockStyles();
    bonMenuScrollLocked = false;
    bonMenuScrollY = 0;
    bonRemoveMobileMenuBackdrop();

    if (shouldRestoreScroll) {
        requestAnimationFrame(() => window.scrollTo(0, restoreScrollY));
    }
};

const bonCleanupOrphanedScrollLocks = () => {
    if (bonHasOpenMobileMenu()) {
        return;
    }

    if (bonActiveMobileMenu && !bonActiveMobileMenu.open) {
        bonRestoreMobileMenu(bonActiveMobileMenu);
    }

    document.querySelectorAll('body > [data-bon-menu-portal="active"]').forEach((panel) => {
        panel.remove();
    });

    bonRemoveMobileMenuBackdrop();

    if (bonHasVisibleModal()) {
        return;
    }

    bonClearPageScrollLockStyles();
    bonMenuScrollLocked = false;
    bonMenuScrollY = 0;
};

const bonCloseMobileMenus = (except = null) => {
    document.querySelectorAll('details[data-mobile-menu][open]').forEach((menu) => {
        if (menu !== except) {
            menu.open = false;
        }
    });
};

const bonSyncMobileMenuScrollLock = () => {
    if (bonHasOpenMobileMenu()) {
        bonLockPageScrollForMenu();
        return;
    }

    bonUnlockPageScrollIfSafe();
};

document.addEventListener('DOMContentLoaded', () => {
    const mobileMenus = Array.from(document.querySelectorAll('details[data-mobile-menu]'));

    mobileMenus.forEach((menu) => {
        menu.addEventListener('toggle', () => {
            if (menu.open) {
                bonCloseMobileMenus(menu);
                bonMountMobileMenu(menu);
                requestAnimationFrame(bonLockPageScrollForMenu);
                return;
            }

            bonRestoreMobileMenu(menu);
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
        const clickedPortal = event.target.closest?.('[data-bon-menu-portal="active"], .bon-mobile-menu-backdrop, .bon-mobile-menu-close');

        if (!clickedMenu && !clickedPortal) {
            bonCloseMobileMenus();
            requestAnimationFrame(bonUnlockPageScrollIfSafe);
        }

        window.setTimeout(bonCleanupOrphanedScrollLocks, 360);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            bonCloseMobileMenus();
            requestAnimationFrame(bonUnlockPageScrollIfSafe);
        }
    });

    window.addEventListener('pageshow', () => {
        bonCloseMobileMenus();
        requestAnimationFrame(bonCleanupOrphanedScrollLocks);
    });

    window.addEventListener('pagehide', () => {
        bonCloseMobileMenus();
        requestAnimationFrame(bonCleanupOrphanedScrollLocks);
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            requestAnimationFrame(bonCleanupOrphanedScrollLocks);
        }
    });

    window.addEventListener('resize', () => {
        requestAnimationFrame(bonCleanupOrphanedScrollLocks);
    });

    requestAnimationFrame(bonCleanupOrphanedScrollLocks);
});
