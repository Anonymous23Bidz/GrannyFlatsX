
document.addEventListener('DOMContentLoaded', initGxfMenus);

function initGxfMenus() {
    const desktopHover = window.matchMedia('(hover: hover) and (min-width: 1025px)');

    document.querySelectorAll('.gxf-mega-nav').forEach(nav => {
        if (nav.dataset.menuInitialized === 'true') return;
        nav.dataset.menuInitialized = 'true';

        const header = nav.closest('.e-con') || nav.parentElement;
        const menuTrigger = header.querySelector('#open-menu');
        const closeTrigger = header.querySelector('#close-menu');
        const items = nav.querySelectorAll('.gxf-top-item.has-mega');

        function closeSubmenus(except = null) {
            items.forEach(item => {
                if (item !== except) {
                    item.classList.remove('is-open');
                    item.querySelector('.gxf-top-trigger')
                        ?.setAttribute('aria-expanded', 'false');
                }
            });
        }

        function setMenuState(isOpen) {
            nav.classList.toggle('mobile-open', isOpen);

            if (menuTrigger) {
                menuTrigger.classList.toggle('menu-icon-hidden', isOpen);
                menuTrigger.setAttribute('aria-expanded', String(isOpen));
                menuTrigger.setAttribute(
                    'aria-label',
                    isOpen ? 'Close navigation menu' : 'Open navigation menu'
                );
            }

            closeTrigger?.classList.toggle('menu-icon-visible', isOpen);

            if (!isOpen) closeSubmenus();
        }

        items.forEach(item => {
            const trigger = item.querySelector('.gxf-top-trigger');
            if (!trigger) return;

            item.addEventListener('mouseenter', () => {
                if (!desktopHover.matches) return;
                closeSubmenus(item);
                item.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
            });

            item.addEventListener('mouseleave', () => {
                if (!desktopHover.matches) return;
                item.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            });

            trigger.addEventListener('click', event => {
                if (desktopHover.matches) return;

                event.preventDefault();

                const isOpen = item.classList.contains('is-open');
                closeSubmenus();

                if (!isOpen) {
                    item.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                } else {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });

            trigger.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    trigger.click();
                }
            });
        });

        menuTrigger?.addEventListener('click', event => {
            event.preventDefault();
            setMenuState(true);
        });

        closeTrigger?.addEventListener('click', event => {
            event.preventDefault();
            setMenuState(false);
        });

        document.addEventListener('click', event => {
            if (
                !nav.contains(event.target) &&
                !menuTrigger?.contains(event.target) &&
                !closeTrigger?.contains(event.target)
            ) {
                setMenuState(false);
            }
        });

        nav.addEventListener('keydown', event => {
            if (event.key === 'Escape') setMenuState(false);
        });

        setMenuState(false);
    });
}

function toggleAppointmentButton() {
    document.querySelectorAll('.gxf-top-menu').forEach(menu => {
        menu.querySelectorAll(
            '.gxf-top-link.form-btn.primary-btn, .gxf-top-link.form-btn.outline-primary'
        ).forEach(link => {
            const listItem = link.closest('li.gxf-top-item');
            if (listItem) {
                listItem.style.display = window.innerWidth > 767 ? 'none' : '';
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', toggleAppointmentButton);
window.addEventListener('resize', toggleAppointmentButton);
