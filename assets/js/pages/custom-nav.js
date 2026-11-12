
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
                listItem.style.display = window.innerWidth > 1200 ? 'none' : '';
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGxfMenus, { once: true });
    document.addEventListener('DOMContentLoaded', toggleAppointmentButton, { once: true });
} else {
    initGxfMenus();
    toggleAppointmentButton();
}

window.addEventListener('resize', toggleAppointmentButton);


// Blog Post Navigation TOC
(function () {
    function buildToc() {
        const tocList = document.querySelector('#dynamic-guide-toc .toc-list');
        if (!tocList) return false;

        const contentArea = document.querySelector('.elementor-widget-theme-post-content') ||
            document.querySelector('.elementor-post__content') ||
            document.querySelector('.entry-content') ||
            document.body;

        const headings = contentArea.querySelectorAll('h2');
        if (headings.length === 0) return false;

        tocList.innerHTML = '';
        let count = 1;
        const linksMap = [];

        headings.forEach((heading) => {
            const innerSpan = heading.querySelector('[id]');
            let elementId = heading.id || (innerSpan ? innerSpan.id : null);

            if (!elementId) {
                const cleanSlug = heading.textContent
                    .trim()
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');

                elementId = `toc-sec-${cleanSlug}`;
                heading.setAttribute('id', elementId);
            }

            // Truncate to first 6 words
            const fullText = heading.textContent.trim();
            const words = fullText.split(/\s+/);
            const truncatedText = words.slice(0, 4).join(' ');
            const headingText = words.length > 4 ? `${truncatedText}` : truncatedText;

            const itemNumber = String(count).padStart(2, '0');
            count++;

            const listItem = document.createElement('li');
            listItem.innerHTML = `
                <a href="#${elementId}" class="toc-link" data-target="${elementId}">
                    <span class="toc-number">${itemNumber}</span>
                    <span class="toc-title">${headingText}</span>
                </a>
            `;

            const linkElement = listItem.querySelector('a');

            // Smooth scroll click handler
            linkElement.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.getElementById(elementId) || heading;
                if (target) {
                    const headerOffset = 90; // Fixed header height in px
                    const elementPos = target.getBoundingClientRect().top;
                    const offsetPos = elementPos + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPos,
                        behavior: 'smooth'
                    });

                    history.pushState(null, null, `#${elementId}`);
                }
            });

            tocList.appendChild(listItem);
            linksMap.push({ id: elementId, element: linkElement });
        });

        // --- SCROLL SPY (ACTIVE LINK HIGHLIGHTING) ---
        const observerOptions = {
            root: null,
            rootMargin: '-100px 0px -50% 0px', // Triggers when heading reaches upper portion of viewport
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const activeId = entry.target.getAttribute('id');

                    // Remove active class from all links, add to current
                    linksMap.forEach(item => {
                        if (item.id === activeId) {
                            item.element.classList.add('active');
                        } else {
                            item.element.classList.remove('active');
                        }
                    });
                }
            });
        }, observerOptions);

        headings.forEach(heading => observer.observe(heading));

        return true;
    }

    if (!buildToc()) {
        document.addEventListener('DOMContentLoaded', buildToc);
        window.addEventListener('load', buildToc);

        let attempts = 0;
        const interval = setInterval(function () {
            attempts++;
            if (buildToc() || attempts > 15) {
                clearInterval(interval);
            }
        }, 200);
    }
})();