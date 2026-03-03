@php
    $badge = \App\Filament\Resources\CuentasPorVencerResource::getNavigationBadge();
    $targetPath = parse_url(\App\Filament\Resources\CuentasPorVencerResource::getUrl(), PHP_URL_PATH) ?: '';
@endphp

<script>
    (() => {
        const badgeValue = @js($badge);
        const targetPath = @js($targetPath);
        const badgeId = 'clientes-por-vencer-floating-badge';

        const paintBadge = () => {
            const navLink = Array.from(document.querySelectorAll('aside a[href]'))
                .find((link) => {
                    try {
                        const href = new URL(link.href, window.location.origin);
                        return href.pathname === targetPath;
                    } catch {
                        return false;
                    }
                });

            if (!navLink) {
                return;
            }

            const wrapper = navLink.closest('li, .fi-sidebar-item') || navLink.parentElement;

            if (!wrapper) {
                return;
            }

            const existing = wrapper.querySelector(`#${badgeId}`);

            if (!badgeValue) {
                if (existing) {
                    existing.remove();
                }
                return;
            }

            if (getComputedStyle(wrapper).position === 'static') {
                wrapper.style.position = 'relative';
            }

            const badge = existing || document.createElement('span');
            badge.id = badgeId;
            badge.textContent = String(badgeValue);
            badge.setAttribute('aria-label', `${badgeValue} clientes por vencer`);
            badge.style.position = 'absolute';
            badge.style.top = '2px';
            badge.style.right = '6px';
            badge.style.minWidth = '18px';
            badge.style.height = '18px';
            badge.style.padding = '0 6px';
            badge.style.borderRadius = '9999px';
            badge.style.display = 'inline-flex';
            badge.style.alignItems = 'center';
            badge.style.justifyContent = 'center';
            badge.style.fontSize = '11px';
            badge.style.fontWeight = '700';
            badge.style.lineHeight = '1';
            badge.style.background = '#ef4444';
            badge.style.color = '#fff';
            badge.style.boxShadow = '0 2px 6px rgba(0,0,0,.35)';
            badge.style.pointerEvents = 'none';
            badge.style.zIndex = '30';

            if (!existing) {
                wrapper.appendChild(badge);
            }
        };

        paintBadge();
        document.addEventListener('livewire:navigated', paintBadge);
        document.addEventListener('DOMContentLoaded', paintBadge);
    })();

    (() => {
        const CLOSE_DELAY_MS = 100;
        const getSidebarElement = () => document.querySelector('.fi-main-sidebar, .fi-sidebar');
        const getOverlayElement = () => document.querySelector('.fi-sidebar-close-overlay');
        let closeTimeoutId = null;

        const getSidebarStore = () => {
            try {
                return window.Alpine?.store('sidebar') ?? null;
            } catch {
                return null;
            }
        };

        const isSidebarOpen = () => {
            const store = getSidebarStore();

            if (store && typeof store.isOpen !== 'undefined') {
                return Boolean(store.isOpen);
            }

            return Boolean(getSidebarElement()?.classList.contains('fi-sidebar-open'));
        };

        const closeSidebar = () => {
            const store = getSidebarStore();

            if (typeof store?.close === 'function') {
                store.close();
            }

            const overlay = getOverlayElement();

            if (overlay) {
                overlay.dispatchEvent(new MouseEvent('click', { bubbles: true }));
            }

            window.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
        };

        const closeSidebarWithDelay = () => {
            if (closeTimeoutId) {
                clearTimeout(closeTimeoutId);
            }

            closeTimeoutId = setTimeout(() => {
                closeSidebar();
            }, CLOSE_DELAY_MS);
        };

        const onOutsideInteraction = (event) => {
            if (!isSidebarOpen()) {
                return;
            }

            const target = event.target;
            const sidebar = getSidebarElement();

            if (!sidebar || !(target instanceof Element)) {
                return;
            }

            const clickedInsideSidebar = sidebar.contains(target);

            if (clickedInsideSidebar) {
                return;
            }

            closeSidebarWithDelay();
        };

        const onSidebarNavigationClick = (event) => {
            if (!isSidebarOpen()) {
                return;
            }

            const target = event.target;

            if (!(target instanceof Element)) {
                return;
            }

            if (target.closest('.fi-main-sidebar a[href]')) {
                closeSidebarWithDelay();
            }
        };

        const collapseSidebarByDefault = () => {
            const store = getSidebarStore();

            if (typeof store?.close === 'function') {
                store.close();
            }
        };

        document.addEventListener('pointerdown', onOutsideInteraction, true);
        document.addEventListener('touchstart', onOutsideInteraction, true);
        document.addEventListener('click', onOutsideInteraction, true);
        document.addEventListener('click', onSidebarNavigationClick, true);
        document.addEventListener('DOMContentLoaded', collapseSidebarByDefault);
        document.addEventListener('livewire:navigated', () => {
            closeSidebarWithDelay();
        });
    })();
</script>
