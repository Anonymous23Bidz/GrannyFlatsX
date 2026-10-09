document.addEventListener('DOMContentLoaded', () => {
    const wrappers = [...document.querySelectorAll('.fpf-wrapper')];
    const $ = (el, selector) => el.querySelector(selector);
    const $$ = (el, selector) => [...el.querySelectorAll(selector)];
    const value = (el, fallback = '') => el?.value ?? fallback;
    const split = str => String(str || '').split(',').map(v => v.trim()).filter(Boolean);

    const getChecked = (wrapper, suffix) =>
        $(wrapper, `input[name$="-${suffix}"]:checked`);

    const getCards = wrapper =>
        $$(wrapper, '.floor-plan-grid .floor-plan-card');

    const getLabel = radio =>
        radio?.nextElementSibling?.textContent.trim() || '';

    const closeDropdowns = wrapper =>
        $$(wrapper, '.fpf-select-container.is-open')
            .forEach(el => el.classList.remove('is-open'));

    // Update dropdown labels.
    const updateTrigger = radio => {
        const container = radio?.closest('.fpf-select-container');
        const text = $(container, '.fpf-trigger-text');
        if (!text) return;

        const bedroom = getChecked(container, 'bedrooms');
        const size = getChecked(container, 'bedroom-size');
        const bathroom = getChecked(container, 'bathrooms');

        if (bedroom || size || bathroom) {
            const parts = [];

            if (bedroom && !['', 'all'].includes(bedroom.value)) {
                const name = getLabel(bedroom).replace(/\s*bedrooms?\s*$/i, '');
                parts.push(/^studio$/i.test(name) ? 'Studio' : `${name} ${name === '1' ? 'Bed' : 'Beds'}`);
            }

            if (size?.value) {
                const name = size.value.toLowerCase();
                parts.push(name === 'queen' ? 'Queen: under 10m²'
                    : name === 'king' ? 'King: over 10m²' : getLabel(size));
            }

            if (bathroom?.value) {
                const name = getLabel(bathroom).replace(/\s*bathrooms?\s*$/i, '');
                parts.push(`${name} ${name === '1' ? 'Bath' : 'Baths'}`);
            }

            text.textContent = parts.join(', ') || 'Any';
            return;
        }

        if (!radio.value) {
            text.textContent = $(container, '.fpf-layout-popover')
                ? 'Any Layout' : 'Any';
            return;
        }

        text.textContent =
            $(radio.nextElementSibling, '.fpf-layout-name')?.textContent.trim()
            || getLabel(radio) || 'Any';
    };

    // Update the selected features label.
    const updateFeatureTrigger = wrapper => {
        const text = $(wrapper, '.fpf-features-container .fpf-trigger-text');
        if (!text) return;

        const count = $$(wrapper, '.fpf-checkbox-input:checked').length;
        text.textContent = count ? `${count} Features` : 'Any Features';
    };

    // Filter cards using all selected criteria.
    const filterCards = wrapper => {
        const cards = getCards(wrapper);
        const bedroom = getChecked(wrapper, 'bedrooms');
        const bedroomSize = value(getChecked(wrapper, 'bedroom-size'));
        const bathroom = value(getChecked(wrapper, 'bathrooms'));
        const layout = value(getChecked(wrapper, 'layout'));
        const floorArea = value($(wrapper, 'select[name="floor_area"]'));
        const bestFor = value($(wrapper, 'select[name="best_for"]'));
        const livingArea = value($(wrapper, 'select[name="living_area"]'));
        const features = $$(wrapper, '.fpf-checkbox-input:checked').map(el => el.value);

        const width = parseFloat(value($(wrapper, '.fpf-space-width')));
        const depth = parseFloat(value($(wrapper, '.fpf-space-depth')));
        const hasSpace = Number.isFinite(width) && Number.isFinite(depth) && width > 0 && depth > 0;

        cards.forEach(card => {
            const matches = [
                !bedroom || ['', 'all'].includes(bedroom.value) || split(card.dataset.bedrooms).includes(bedroom.value),
                !bedroomSize || (card.dataset.bedroomSize || '') === bedroomSize,
                !bathroom || split(card.dataset.bathrooms).includes(bathroom),
                !layout || layout === 'all' || split(card.dataset.layout).includes(layout),
                !floorArea || (() => {
                    const area = Number(card.dataset.livingArea);

                    if (!Number.isFinite(area) || area <= 0) return false;

                    switch (floorArea) {
                        case 'under-45':
                            return area < 45;

                        case '45-55':
                            return area >= 45 && area < 55;

                        case '55-60':
                            return area >= 55 && area <= 60;

                        case 'over-60':
                            return area > 60;

                        default:
                            return true;
                    }
                })(),
                !bestFor || split(card.dataset.bestFor).includes(bestFor),
                features.every(feature => split(card.dataset.features).includes(feature)),
                !livingArea || (livingArea === 'small'
                    ? Number(card.dataset.livingArea) < 20
                    : livingArea === 'large'
                        ? Number(card.dataset.livingArea) > 20
                        : true)
            ].every(Boolean);

            const length = parseFloat(card.dataset.planLengthMm);
            const planWidth = parseFloat(card.dataset.planWidthMm);

            const fitsSpace = !hasSpace || (
                Number.isFinite(length) && Number.isFinite(planWidth) &&
                length > 0 && planWidth > 0 &&
                ((length <= width && planWidth <= depth) ||
                    (length <= depth && planWidth <= width))
            );

            card.style.display = matches && fitsSpace ? '' : 'none';
        });
    };

    // Sort cards while preserving their original order.
    const sortCards = wrapper => {
        const grid = $(wrapper, '.floor-plan-grid');
        const sort = value($(wrapper, 'select[name="sort_by"]'), 'default');

        if (!grid) return;

        const cards = getCards(wrapper);

        cards.sort((a, b) => {
            switch (sort) {
                case 'title_asc':
                    return (a.dataset.title || '').localeCompare(
                        b.dataset.title || '',
                        undefined,
                        { numeric: true, sensitivity: 'base' }
                    );

                case 'size_desc':
                    return (
                        (parseFloat(b.dataset.livingArea) || 0) -
                        (parseFloat(a.dataset.livingArea) || 0)
                    );

                default:
                    return (
                        Number(a.dataset.originalOrder) -
                        Number(b.dataset.originalOrder)
                    );
            }
        });

        cards.forEach(card => grid.appendChild(card));
    };

    // Create removable filter tags safely.
    const updateTags = wrapper => {
        const list = $(wrapper, '.fpf-tags-list');
        if (!list) return;

        list.replaceChildren();

        const addTag = (label, reset) => {
            if (!label) return;

            const tag = document.createElement('span');
            const button = document.createElement('button');

            tag.className = 'fpf-tag-pill';
            button.className = 'fpf-tag-remove';
            button.type = 'button';
            button.textContent = '×';
            button.setAttribute('aria-label', `Remove ${label} filter`);
            tag.append(document.createTextNode(`${label} `), button);
            button.addEventListener('click', () => {
                reset();
                refresh(wrapper);
            });

            list.appendChild(tag);
        };

        const resetRadio = suffix => {
            const radios = $$(wrapper, `input[name$="-${suffix}"]`);
            const any = radios.find(r => r.value === '' || r.value === 'all');
            if (any) any.checked = true;
            else radios.forEach(r => { r.checked = false; });

            updateTrigger(any || radios[0]);
        };

        const bedroom = getChecked(wrapper, 'bedrooms');
        if (bedroom && !['', 'all'].includes(bedroom.value)) {
            const name = getLabel(bedroom).replace(/\s*bedrooms?\s*$/i, '');
            addTag(/^studio$/i.test(name) ? 'Studio' : `${name} ${name === '1' ? 'Bedroom' : 'Bedrooms'}`,
                () => resetRadio('bedrooms'));
        }

        const size = getChecked(wrapper, 'bedroom-size');
        if (size?.value) {
            const name = size.value.toLowerCase();
            addTag(name === 'queen' ? 'Queen: under 10m²'
                : name === 'king' ? 'King: over 10m²' : getLabel(size),
                () => resetRadio('bedroom-size'));
        }

        const bathroom = getChecked(wrapper, 'bathrooms');
        if (bathroom?.value) {
            const name = getLabel(bathroom).replace(/\s*bathrooms?\s*$/i, '');
            addTag(`${name} ${name === '1' ? 'Bathroom' : 'Bathrooms'}`,
                () => resetRadio('bathrooms'));
        }

        const layout = getChecked(wrapper, 'layout');
        if (layout?.value) {
            addTag($(layout.nextElementSibling, '.fpf-layout-name')?.textContent.trim() || getLabel(layout),
                () => resetRadio('layout'));
        }

        ['floor_area', 'best_for', 'living_area'].forEach(name => {
            const select = $(wrapper, `select[name="${name}"]`);
            if (!select?.value) return;

            addTag(select.selectedOptions[0]?.textContent.trim(), () => {
                select.value = '';
            });
        });

        $$(wrapper, '.fpf-checkbox-input:checked').forEach(checkbox => {
            const label = checkbox.closest('.fpf-checkbox-label');
            const name = checkbox.dataset.featureName ||
                $('span:last-child', label)?.textContent.trim();

            addTag(name, () => {
                checkbox.checked = false;
                updateFeatureTrigger(wrapper);
            });
        });
    };

    // Refresh dependent UI after changes.
    const refresh = wrapper => {
        filterCards(wrapper);
        updateTags(wrapper);
        updateFeatureTrigger(wrapper);
    };

    // Switch floorplan/facade images.
    const updateView = wrapper => {
        const view = getChecked(wrapper, 'view')?.value;
        if (!view) return;

        getCards(wrapper).forEach(card => {
            const images = $$(card, '.fpf-floorplan-image, .fpf-facade-image');
            const selected = $(card, view === 'facade'
                ? '.fpf-facade-image' : '.fpf-floorplan-image');

            images.forEach(img => { img.style.display = 'none'; });

            if (!selected) {
                card.classList.remove('is-loading');
                return;
            }

            selected.style.display = 'block';
            card.classList.toggle('is-loading', !selected.complete);

            if (!selected.complete) {
                ['load', 'error'].forEach(event => selected.addEventListener(
                    event, () => card.classList.remove('is-loading'), { once: true }
                ));
            }
        });
    };

    wrappers.forEach(wrapper => {
        const cards = getCards(wrapper);
        const selects = $$(wrapper, '.fpf-select-container');
        const features = $(wrapper, '.fpf-features-container');

        cards.forEach((card, index) => {
            if (!('originalOrder' in card.dataset)) card.dataset.originalOrder = index;
        });

        selects.forEach(container => {
            const trigger = $(container, '.fpf-select-trigger');
            if (!trigger) return;

            trigger.addEventListener('click', event => {
                event.preventDefault();
                event.stopPropagation();

                const open = !container.classList.contains('is-open');
                closeDropdowns(wrapper);
                if (open) container.classList.add('is-open');
            });

            $$(container, '.fpf-radio-input').forEach(radio => {
                radio.addEventListener('change', () => {
                    updateTrigger(radio);
                    refresh(wrapper);
                    closeDropdowns(wrapper);
                });

                if (radio.checked) updateTrigger(radio);
            });
        });

        $$(wrapper, '.fpf-checkbox-input').forEach(checkbox => {
            checkbox.addEventListener('change', () => refresh(wrapper));
        });

        ['floor_area', 'best_for', 'living_area'].forEach(name => {
            $(wrapper, `select[name="${name}"]`)?.addEventListener('change', () => refresh(wrapper));
        });

        ['.fpf-space-width', '.fpf-space-depth'].forEach(selector => {
            $(wrapper, selector)?.addEventListener('input', () => refresh(wrapper));
        });

        $(wrapper, 'select[name="sort_by"]')?.addEventListener('change', () => sortCards(wrapper));

        $$(wrapper, 'input[type="radio"][name$="-view"]').forEach(radio => {
            radio.addEventListener('change', () => updateView(wrapper));
        });

        // Initial state.
        updateFeatureTrigger(wrapper);
        sortCards(wrapper);
        refresh(wrapper);
        updateView(wrapper);
    });

    // One outside-click listener for every filter wrapper.
    document.addEventListener('click', event => {
        wrappers.forEach(wrapper => {
            if (!wrapper.contains(event.target)) closeDropdowns(wrapper);
        });
    });

    // View All reveals every card in its own results wrapper.
    document.querySelectorAll('.fpf-wrapper .floor-plan-grid').forEach(grid => {
        const wrapper = grid.closest('.fpf-wrapper');
        const button = $(wrapper, '.fpf-view-all-btn');
        const container = $(wrapper, '.fpf-view-all-wrap');

        button?.addEventListener('click', event => {
            event.preventDefault();
            getCards(wrapper).forEach(card => {
                card.style.display = '';
                card.hidden = false;
            });
            if (container) container.hidden = true;
        });
    });
});