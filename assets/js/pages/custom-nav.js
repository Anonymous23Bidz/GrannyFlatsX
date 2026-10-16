document.addEventListener('DOMContentLoaded', () => {
    const menuTrigger = document.querySelector('#sub-menu');
    const closeTrigger = document.querySelector('#close-menu');
    const nav = document.querySelector('.gxf-mega-nav');

    if (!menuTrigger || !nav || menuTrigger.dataset.menuInitialized) return;

    menuTrigger.dataset.menuInitialized = 'true';

    const setMenuState = isOpen => {
        nav.classList.toggle('mobile-open', isOpen);

        menuTrigger.style.display = isOpen ? 'none' : '';
        menuTrigger.classList.toggle('menu-icon-hidden', isOpen);

        if (closeTrigger) {
            closeTrigger.classList.toggle('menu-icon-visible', isOpen);
        }

        menuTrigger.setAttribute('aria-expanded', String(isOpen));
        menuTrigger.setAttribute(
            'aria-label',
            isOpen ? 'Close navigation menu' : 'Open navigation menu'
        );

        if (!isOpen) {
            nav.querySelectorAll('.gxf-top-item.is-open').forEach(item => {
                item.classList.remove('is-open');
            });
        }
    };

    const toggleMenu = event => {
        event.preventDefault();
        setMenuState(!nav.classList.contains('mobile-open'));
    };

    const closeMenu = () => {
        if (nav.classList.contains('mobile-open')) {
            setMenuState(false);
        }
    };

    menuTrigger.addEventListener('click', toggleMenu);

    menuTrigger.addEventListener('keydown', event => {
        if (
            (event.key === 'Enter' || event.key === ' ') &&
            menuTrigger.tagName !== 'BUTTON'
        ) {
            toggleMenu(event);
        }
    });

    if (closeTrigger) {
        closeTrigger.addEventListener('click', toggleMenu);

        closeTrigger.addEventListener('keydown', event => {
            if (
                (event.key === 'Enter' || event.key === ' ') &&
                closeTrigger.tagName !== 'BUTTON'
            ) {
                toggleMenu(event);
            }
        });
    }

    document.addEventListener('click', event => {
        if (
            !nav.contains(event.target) &&
            !menuTrigger.contains(event.target) &&
            !(closeTrigger && closeTrigger.contains(event.target))
        ) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    nav.querySelectorAll('.gxf-top-trigger').forEach(trigger => {
        trigger.addEventListener('click', event => {
            const item = trigger.closest('.gxf-top-item');

            if (!item) return;

            if (trigger.getAttribute('href') === '#') {
                event.preventDefault();
            }

            nav.querySelectorAll('.gxf-top-item.is-open').forEach(openItem => {
                if (openItem !== item) {
                    openItem.classList.remove('is-open');
                }
            });

            item.classList.toggle('is-open');
        });
    });

    setMenuState(false);
});