
document.addEventListener('DOMContentLoaded', () => {
    const menuTrigger = document.querySelector('#open-menu');
    const closeTrigger = document.querySelector('#close-menu');
    const nav = document.querySelector('.gxf-mega-nav');

    if (!menuTrigger || !nav || nav.dataset.menuInitialized) return;

    nav.dataset.menuInitialized = 'true';

    const setMenuState = (isOpen) => {
        nav.classList.toggle('mobile-open', isOpen);

        menuTrigger.classList.toggle('menu-icon-hidden', isOpen);
        menuTrigger.setAttribute('aria-expanded', String(isOpen));
        menuTrigger.setAttribute(
            'aria-label',
            isOpen ? 'Close navigation menu' : 'Open navigation menu'
        );

        if (closeTrigger) {
            closeTrigger.classList.toggle('menu-icon-visible', isOpen);
        }

        if (!isOpen) {
            nav.querySelectorAll('.gxf-top-item.is-open').forEach(item => {
                item.classList.remove('is-open');
            });
        }
    };

    const openMenu = (event) => {
        event.preventDefault();
        setMenuState(true);
    };

    const closeMenu = (event) => {
        if (event) event.preventDefault();
        setMenuState(false);
    };

    menuTrigger.addEventListener('click', openMenu);

    if (closeTrigger) {
        closeTrigger.addEventListener('click', closeMenu);
    }

    document.addEventListener('click', (event) => {
        if (
            !nav.contains(event.target) &&
            !menuTrigger.contains(event.target) &&
            !(closeTrigger && closeTrigger.contains(event.target))
        ) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });


    nav.querySelectorAll('.gxf-top-trigger').forEach(trigger => {
        const item = trigger.closest('.gxf-top-item');

        if (!item) return;

        const openSubmenu = () => {
            nav.querySelectorAll('.gxf-top-item.is-open').forEach(openItem => {
                if (openItem !== item) {
                    openItem.classList.remove('is-open');
                }
            });

            item.classList.add('is-open');
        };

        const closeSubmenu = () => {
            item.classList.remove('is-open');
        };

        // Desktop: open on hover.
        trigger.addEventListener('mouseenter', () => {
            if (window.matchMedia('(min-width: 768px)').matches) {
                openSubmenu();
            }
        });

        // Keep the submenu open while hovering over its parent item.
        item.addEventListener('mouseleave', () => {
            if (window.matchMedia('(min-width: 768px)').matches) {
                closeSubmenu();
            }
        });

        // Mobile: open and close on click.
        trigger.addEventListener('click', event => {
            if (trigger.getAttribute('href') === '#') {
                event.preventDefault();
            }

            if (window.matchMedia('(max-width: 767px)').matches) {
                const isOpen = item.classList.contains('is-open');

                nav.querySelectorAll('.gxf-top-item.is-open').forEach(openItem => {
                    openItem.classList.remove('is-open');
                });

                if (!isOpen) {
                    item.classList.add('is-open');
                }
            }
        });
    });

    setMenuState(false);
});

function toggleAppointmentButton() {
    document.querySelectorAll('.gxf-top-menu').forEach(menu => {
        menu.querySelectorAll(
            '.gxf-top-link.form-btn.primary-btn, .gxf-top-link.form-btn.outline-primary'
        ).forEach(link => {
            const listItem = link.closest('li.gxf-top-item');

            if (!listItem) return;

            listItem.style.display = window.innerWidth > 767 ? 'none' : '';
        });
    });
}

document.addEventListener('DOMContentLoaded', toggleAppointmentButton);
window.addEventListener('resize', toggleAppointmentButton);
