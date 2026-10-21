// Only request a feature's JavaScript on pages that render its markup.
// Keep the paths as literals so esbuild can create minified code-split chunks.
if (document.querySelector('.gxf-mega-nav')) {
    import('./pages/custom-nav.js');
}

if (document.querySelector('.custom-location-locator')) {
    import('./components/suburb-filter.js');
}

if (document.querySelector('.faq-question')) {
    import('./components/accordion.js');
}

if (document.querySelector('.bedrooms-fpf-filter')) {
    import('./components/bedroom-filter.js');
}

if (document.querySelector('.fpf-wrapper')) {
    import('./components/floor-plan-filter.js');
}

if (document.querySelector('#property-check-form')) {
    import('./components/property-check.js');
}

// The quote form lives in the global off-canvas header, so it exists on every
// page. Load its chunk only when a visitor starts interacting with the form.
let quoteFormModule;

const loadQuoteForm = () => {
    if (!quoteFormModule) {
        quoteFormModule = import('./components/get-quote-form.js');
    }

    return quoteFormModule;
};

const quoteFormInteraction = event => {
    if (
        event.target instanceof Element &&
        event.target.closest('.get-quote-form')
    ) {
        loadQuoteForm();
        document.removeEventListener('pointerdown', quoteFormInteraction, true);
        document.removeEventListener('focusin', quoteFormInteraction, true);
        document.removeEventListener('keydown', quoteFormInteraction, true);
    }
};

document.addEventListener('pointerdown', quoteFormInteraction, true);
document.addEventListener('focusin', quoteFormInteraction, true);
document.addEventListener('keydown', quoteFormInteraction, true);
