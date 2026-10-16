document.addEventListener('DOMContentLoaded', () => {
    const menuTrigger = document.querySelector('#sub-menu');
    const closeTrigger = document.querySelector('#close-menu');
    const nav = document.querySelector('.gxf-mega-nav');

    if (!menuTrigger || !nav || menuTrigger.dataset.menuInitialized) return;

    menuTrigger.dataset.menuInitialized = 'true';

    let scrollPosition = 0;

    const lockBodyScroll = () => {
        if (document.body.classList.contains('gxf-submenu-open')) return;

        scrollPosition = window.scrollY;
        document.body.classList.add('gxf-submenu-open');
        document.body.style.position = 'fixed';
        document.body.style.top = `- ${scrollPosition} px`;
        document.body.style.width = '100%';
    };

    const unlockBodyScroll = () => {
        document.body.classList.remove('gxf-submenu-open');
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';

        window.scrollTo(0, scrollPosition);
    };

    const updateScrollLock = () => {
        const isMobile = window.matchMedia('(max-width: 767px)').matches;
        const menuOpen = nav.classList.contains('mobile-open');

        if (isMobile && menuOpen) {
            lockBodyScroll();
        } else {
            unlockBodyScroll();
        }
    };

    const setMenuState = (isOpen) => {
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

        updateScrollLock();
    };

    const toggleMenu = (event) => {
        event.preventDefault();

        const isOpen = !nav.classList.contains('mobile-open');
        setMenuState(isOpen);
    };

    const closeMenu = () => {
        setMenuState(false);
    };

    menuTrigger.addEventListener('click', toggleMenu);

    menuTrigger.addEventListener('keydown', event => {
        if (event.key === 'Enter' || event.key === ' ') {
            toggleMenu(event);
        }
    });

    if (closeTrigger) {
        closeTrigger.addEventListener('click', toggleMenu);

        closeTrigger.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
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
        trigger.addEventListener('click', () => {
            const item = trigger.closest('.gxf-top-item');

            if (!item) return;

            nav.querySelectorAll('.gxf-top-item.is-open').forEach(openItem => {
                if (openItem !== item) {
                    openItem.classList.remove('is-open');
                }
            });

            item.classList.toggle('is-open');
            updateScrollLock();
        });
    });

    window.addEventListener('resize', updateScrollLock);

    setMenuState(false);
});