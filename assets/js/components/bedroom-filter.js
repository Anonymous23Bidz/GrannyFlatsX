document.addEventListener('DOMContentLoaded', () => {
    const filters = document.querySelectorAll('.bedrooms-fpf-filter');
    const results = document.querySelectorAll('.bedrooms-fpf-results[data-filter-group="floor-plans"]');
    const allCards = [...results].flatMap(wrapper => [...wrapper.querySelectorAll('.bedrooms-floor-plan-card')]);
    const splitValues = value => (value || '').split(',').map(v => v.trim()).filter(Boolean);
    const closePopovers = filter => filter.querySelectorAll('.bedrooms-fpf-layout-popover, .bedrooms-fpf-features-popover').forEach(popover => popover.classList.remove('is-open'));

    filters.forEach(filter => {
        const $ = selector => filter.querySelector(selector);
        const $$ = selector => [...filter.querySelectorAll(selector)];

        const floorArea = $('.bedrooms-fpf-floor-area-select');
        const layoutTrigger = $('.bedrooms-fpf-layout-trigger');
        const layoutPopover = $('.bedrooms-fpf-layout-popover');
        const layoutInputs = $$('.bedrooms-fpf-layout-input');
        const featuresTrigger = $('.bedrooms-fpf-features-trigger');
        const featuresPopover = $('.bedrooms-fpf-features-popover');
        const featureInputs = $$('.bedrooms-fpf-checkbox-input');
        const viewButton = $('.bedrooms-fpf-view-designs-btn');
        const resetButton = $('.bedrooms-fpf-reset-btn');

        const updateLayoutLabel = () => {
            const selected = layoutInputs.find(input => input.checked);
            const text = layoutTrigger?.querySelector('.bedrooms-fpf-trigger-text');
            if (text) text.textContent = selected?.dataset.layoutName || 'Any Layout';
        };

        const updateFeaturesLabel = () => {
            const selected = featureInputs.filter(input => input.checked);
            const text = featuresTrigger?.querySelector('.bedrooms-fpf-trigger-text');
            if (!text) return;

            text.textContent = !selected.length ? 'Any Features'
                : selected.length === 1 ? selected[0].dataset.featureName || '1 Feature'
                    : `${selected.length} Features`;
        };

        layoutTrigger?.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            const open = !layoutPopover?.classList.contains('is-open');
            closePopovers(filter);
            if (open) layoutPopover?.classList.add('is-open');
        });

        featuresTrigger?.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            const open = !featuresPopover?.classList.contains('is-open');
            closePopovers(filter);
            if (open) featuresPopover?.classList.add('is-open');
        });

        layoutInputs.forEach(input => input.addEventListener('change', () => {
            updateLayoutLabel();
            closePopovers(filter);
        }));

        featureInputs.forEach(input => input.addEventListener('change', updateFeaturesLabel));

        viewButton?.addEventListener('click', () => {
            const area = (floorArea?.value || '').trim();
            const layout = layoutInputs.find(input => input.checked)?.value || '';
            const features = featureInputs.filter(input => input.checked).map(input => input.value);

            allCards.forEach(card => {
                const cardLayouts = splitValues(card.dataset.layout);
                const cardFeatures = splitValues(card.dataset.features);

                card.hidden = !(
                    (!area || (card.dataset.floorArea || '').trim() === area) &&
                    (!layout || cardLayouts.includes(layout)) &&
                    features.every(feature => cardFeatures.includes(feature))
                );
            });

            closePopovers(filter);
        });

        resetButton?.addEventListener('click', () => {
            if (floorArea) floorArea.value = '';
            [...layoutInputs, ...featureInputs].forEach(input => { input.checked = false; });

            updateLayoutLabel();
            updateFeaturesLabel();
            closePopovers(filter);
            allCards.forEach(card => { card.hidden = false; });
        });
    });

    document.addEventListener('click', event => {
        filters.forEach(filter => {
            if (!filter.contains(event.target)) closePopovers(filter);
        });
    });

    document.querySelectorAll('.bedrooms-fpf-results').forEach(wrapper => {
        const cards = [...wrapper.querySelectorAll('.bedrooms-floor-plan-card')];
        const viewAllWrap = wrapper.querySelector('.bedrooms-fpf-view-all-wrap');
        const viewAllBtn = wrapper.querySelector('.bedrooms-fpf-view-all-btn');

        viewAllBtn?.addEventListener('click', event => {
            event.preventDefault();
            cards.forEach(card => { card.hidden = false; });
            if (viewAllWrap) viewAllWrap.hidden = true;
        });
    });

});
