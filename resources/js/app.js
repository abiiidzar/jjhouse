document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-notification-bell]').forEach((bell) => {
        const trigger = bell.querySelector('[data-notification-trigger]');
        const panel = bell.querySelector('[data-notification-panel]');

        if (!trigger || !panel) {
            return;
        }

        const setOpen = (open) => {
            panel.classList.toggle('hidden', !open);
            trigger.setAttribute('aria-expanded', String(open));
        };

        const pollUrl = bell.dataset.notificationPollUrl;
        const badge = bell.querySelector('[data-notification-badge]');
        const unreadLabel = bell.querySelector('[data-notification-unread-label]');
        const readAllForm = bell.querySelector('[data-notification-read-all]');

        const setUnreadCount = (count) => {
            const unreadCount = Math.max(0, Number.parseInt(count, 10) || 0);

            if (badge) {
                badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
                badge.classList.toggle('hidden', unreadCount === 0);
            }

            if (unreadLabel) {
                unreadLabel.textContent = unreadCount > 0
                    ? `${unreadCount} belum dibaca`
                    : 'Semua sudah dibaca';
            }

            if (readAllForm) {
                readAllForm.classList.toggle('hidden', unreadCount === 0);
            }

            trigger.setAttribute(
                'aria-label',
                unreadCount > 0
                    ? `Buka notifikasi, ${unreadCount} belum dibaca`
                    : 'Buka notifikasi'
            );
        };

        const refreshUnreadCount = async () => {
            if (!pollUrl || document.hidden) {
                return;
            }

            try {
                const response = await fetch(pollUrl, {
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                setUnreadCount(payload.count);
            } catch {
                // Polling bersifat tambahan; jangan mengganggu halaman bila koneksi gagal.
            }
        };

        trigger.addEventListener('click', () => {
            setOpen(panel.classList.contains('hidden'));
        });

        document.addEventListener('click', (event) => {
            if (!bell.contains(event.target)) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setOpen(false);
                trigger.focus();
            }
        });

        if (pollUrl) {
            window.setInterval(refreshUnreadCount, 60_000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    refreshUnreadCount();
                }
            });
        }
    });

    document.querySelectorAll('[data-sidebar-layout]').forEach((layout, index) => {
        const sidebar = layout.querySelector('[data-sidebar]');
        const overlay = layout.querySelector('[data-sidebar-overlay]');
        const buttons = layout.querySelectorAll('[data-sidebar-toggle]');
        const closeButtons = layout.querySelectorAll('[data-sidebar-close]');
        const storageKey = layout.dataset.sidebarKey || `sidebar-${index}`;
        const configuredBreakpoint = Number.parseInt(layout.dataset.sidebarDesktopBreakpoint || '', 10);
        const desktopBreakpoint = Number.isFinite(configuredBreakpoint) && configuredBreakpoint > 0
            ? configuredBreakpoint
            : 768;
        const desktopQuery = window.matchMedia(`(min-width: ${desktopBreakpoint}px)`);
        const lockScrollOnMobile = layout.hasAttribute('data-sidebar-lock-scroll');
        let lastToggle = null;

        if (!sidebar) {
            return;
        }

        const savedCollapsed = localStorage.getItem(`${storageKey}-collapsed`) === 'true';
        layout.classList.toggle('sidebar-collapsed', savedCollapsed && desktopQuery.matches);

        const isExpanded = () => desktopQuery.matches
            ? !layout.classList.contains('sidebar-collapsed')
            : layout.classList.contains('sidebar-mobile-open');

        const syncState = () => {
            const expanded = isExpanded();

            buttons.forEach((button) => {
                button.setAttribute('aria-expanded', String(expanded));
            });

            sidebar.setAttribute('aria-hidden', String(!expanded));

            if ('inert' in sidebar) {
                sidebar.inert = !expanded;
            }

            if (lockScrollOnMobile) {
                document.body.classList.toggle(
                    'overflow-hidden',
                    !desktopQuery.matches && expanded
                );
            }
        };

        const closeMobile = ({ restoreFocus = false } = {}) => {
            layout.classList.remove('sidebar-mobile-open');
            syncState();

            if (restoreFocus) {
                lastToggle?.focus();
            }
        };

        const toggleSidebar = (event) => {
            lastToggle = event.currentTarget;

            if (desktopQuery.matches) {
                layout.classList.toggle('sidebar-collapsed');
                localStorage.setItem(
                    `${storageKey}-collapsed`,
                    layout.classList.contains('sidebar-collapsed') ? 'true' : 'false'
                );
            } else {
                layout.classList.toggle('sidebar-mobile-open');
            }

            syncState();

            if (!desktopQuery.matches && isExpanded()) {
                window.requestAnimationFrame(() => {
                    sidebar.querySelector('[data-sidebar-close], [data-sidebar-toggle], a, button')?.focus();
                });
            }
        };

        buttons.forEach((button) => button.addEventListener('click', toggleSidebar));
        closeButtons.forEach((button) => button.addEventListener('click', closeMobile));
        overlay?.addEventListener('click', () => closeMobile({ restoreFocus: true }));

        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (!desktopQuery.matches) {
                    closeMobile();
                }
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !desktopQuery.matches && isExpanded()) {
                closeMobile({ restoreFocus: true });
            }
        });

        syncState();

        desktopQuery.addEventListener('change', (event) => {
            closeMobile();
            layout.classList.toggle(
                'sidebar-collapsed',
                event.matches && localStorage.getItem(`${storageKey}-collapsed`) === 'true'
            );
            syncState();
        });
    });
});
