document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-sidebar-layout]').forEach((layout, index) => {
        const sidebar = layout.querySelector('[data-sidebar]');
        const overlay = layout.querySelector('[data-sidebar-overlay]');
        const buttons = layout.querySelectorAll('[data-sidebar-toggle]');
        const closeButtons = layout.querySelectorAll('[data-sidebar-close]');
        const storageKey = layout.dataset.sidebarKey || `sidebar-${index}`;
        const desktopQuery = window.matchMedia('(min-width: 768px)');

        if (!sidebar) {
            return;
        }

        const savedCollapsed = localStorage.getItem(`${storageKey}-collapsed`) === 'true';
        layout.classList.toggle('sidebar-collapsed', savedCollapsed && desktopQuery.matches);

        const closeMobile = () => {
            layout.classList.remove('sidebar-mobile-open');
        };

        const toggleSidebar = () => {
            if (desktopQuery.matches) {
                layout.classList.toggle('sidebar-collapsed');
                localStorage.setItem(
                    `${storageKey}-collapsed`,
                    layout.classList.contains('sidebar-collapsed') ? 'true' : 'false'
                );
                return;
            }

            layout.classList.toggle('sidebar-mobile-open');
        };

        buttons.forEach((button) => button.addEventListener('click', toggleSidebar));
        closeButtons.forEach((button) => button.addEventListener('click', closeMobile));
        overlay?.addEventListener('click', closeMobile);

        desktopQuery.addEventListener('change', (event) => {
            closeMobile();
            layout.classList.toggle(
                'sidebar-collapsed',
                event.matches && localStorage.getItem(`${storageKey}-collapsed`) === 'true'
            );
        });
    });

    document.querySelectorAll('[data-inline-sidebar]').forEach((sidebar, index) => {
        const storageKey = sidebar.dataset.sidebarKey || `inline-sidebar-${index}`;
        const buttons = sidebar.querySelectorAll('[data-inline-sidebar-toggle]');
        const savedClosed = localStorage.getItem(`${storageKey}-closed`) === 'true';

        sidebar.classList.toggle('inline-sidebar-closed', savedClosed);

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                sidebar.classList.toggle('inline-sidebar-closed');
                localStorage.setItem(
                    `${storageKey}-closed`,
                    sidebar.classList.contains('inline-sidebar-closed') ? 'true' : 'false'
                );
            });
        });
    });
});
