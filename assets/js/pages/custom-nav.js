function initGxfMenu() {
    const menuTrigger = document.querySelector('#open-menu');
    const closeTrigger = document.querySelector('#close-menu');
    const nav = document.querySelector('.gxf-mega-nav');

    if (!menuTrigger || !nav || nav.dataset.menuInitialized === 'true') {
        return;
    }

    nav.dataset.menuInitialized = 'true';

    const setMenuState = (isOpen) => {
        nav.classList.toggle('mobile-open', isOpen);
        document.body.classList.toggle('gxf-submenu-open', isOpen);

        menuTrigger.style.display = isOpen ? 'none' : '';
        menuTrigger.setAttribute('aria-expanded', String(isOpen));
        menuTrigger.setAttribute('aria-label', 'Open navigation menu');

        if (closeTrigger) {
            closeTrigger.style.display = isOpen ? '' : 'none';
            closeTrigger.setAttribute('aria-label', 'Close navigation menu');
        }

        if (!isOpen) {
            nav.querySelectorAll('.gxf-top-item.is-open').forEach((item) => {
                item.classList.remove('is-open');
            });
        }
    };

    menuTrigger.addEventListener('click', (event) => {
        event.preventDefault();
        setMenuState(true);
    });

    if (closeTrigger) {
        closeTrigger.addEventListener('click', (event) => {
            event.preventDefault();
            setMenuState(false);
        });
    }

    document.addEventListener('click', (event) => {
        if (
            nav.classList.contains('mobile-open') &&
            !nav.contains(event.target) &&
            !menuTrigger.contains(event.target) &&
            !(closeTrigger && closeTrigger.contains(event.target))
        ) {
            setMenuState(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setMenuState(false);
        }
    });

    nav.querySelectorAll('.gxf-top-trigger').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            const item = trigger.closest('.gxf-top-item');

            if (!item) return;

            if (trigger.getAttribute('href') === '#') {
                event.preventDefault();
            }

            nav.querySelectorAll('.gxf-top-item.is-open').forEach((openItem) => {
                if (openItem !== item) {
                    openItem.classList.remove('is-open');
                }
            });

            item.classList.toggle('is-open');
        });
    });

    setMenuState(false);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGxfMenu);
} else {
    initGxfMenu();
}

function hideDesktopQuoteButtons() {
    if (window.innerWidth <= 767) return;

    document.querySelectorAll(
        '.gxf-top-menu .gxf-top-link.form-btn.primary-btn, .gxf-top-menu .gxf-top-link.form-btn.outline-primary'
    ).forEach((link) => {
        const listItem = link.closest('li.gxf-top-item');

        if (listItem) {
            listItem.style.display = 'none';
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', hideDesktopQuoteButtons);
} else {
    hideDesktopQuoteButtons();
}