document.addEventListener('DOMContentLoaded', function () {

    const fpfContainers = document.querySelectorAll('.fpf-wrapper');

    fpfContainers.forEach(function (wrapper) {

        const selectContainers = wrapper.querySelectorAll('.fpf-select-container');
        const cards = wrapper.querySelectorAll('.floor-plan-card');

        cards.forEach(function (card, index) {
            card.dataset.originalOrder = index;
        });

        const viewRadios = wrapper.querySelectorAll('input[type="radio"][name$="-view"]');
        const floorAreaSelect = wrapper.querySelector('select[name="floor_area"]');
        const bestForSelect = wrapper.querySelector('select[name="best_for"]');
        const livingAreaSelect = wrapper.querySelector('select[name="living_area"]');
        const sortBySelect = wrapper.querySelector('select[name="sort_by"]');
        const spaceWidthInput = wrapper.querySelector('.fpf-space-width');
        const spaceDepthInput = wrapper.querySelector('.fpf-space-depth');
        const floorPlanGrid = wrapper.querySelector('.floor-plan-grid');
        const tagsList = wrapper.querySelector('.fpf-tags-list');
        const featuresContainer = wrapper.querySelector('.fpf-features-container');
        const featureCheckboxes = wrapper.querySelectorAll('.fpf-checkbox-input');

        /*
         * Update Select Trigger
         */
        function updateSelectTrigger(radio) {

            const container = radio.closest('.fpf-select-container');

            if (!container) {
                return;
            }

            const triggerText = container.querySelector('.fpf-trigger-text');

            if (!triggerText) {
                return;
            }

            /*
             * Bedroom + Bedroom Size + Bathroom combined trigger
             */
            const bedroomRadio = container.querySelector(
                'input[name$="-bedrooms"]:checked'
            );

            const bedroomSizeRadio = container.querySelector(
                'input[name$="-bedroom-size"]:checked'
            );

            const bathroomRadio = container.querySelector(
                'input[name$="-bathrooms"]:checked'
            );

            if (bedroomRadio || bedroomSizeRadio || bathroomRadio) {

                const selectedParts = [];

                /*
                 * Bedroom
                 */
                if (
                    bedroomRadio &&
                    bedroomRadio.value !== '' &&
                    bedroomRadio.value !== 'all'
                ) {

                    const bedroomLabel = bedroomRadio.nextElementSibling;

                    if (bedroomLabel) {

                        let bedroomText = bedroomLabel.textContent.trim();

                        bedroomText = bedroomText.replace(
                            /\s*bedrooms?\s*$/i,
                            ''
                        );

                        if (bedroomText.toLowerCase() === 'studio') {

                            selectedParts.push('Studio');

                        } else {

                            selectedParts.push(
                                bedroomText + (
                                    bedroomText === '1'
                                        ? ' Bed'
                                        : ' Beds'
                                )
                            );
                        }
                    }
                }

                /*
                 * Bedroom Size
                 */
                if (
                    bedroomSizeRadio &&
                    bedroomSizeRadio.value !== ''
                ) {

                    const bedroomSizeLabel =
                        bedroomSizeRadio.nextElementSibling;

                    if (bedroomSizeLabel) {

                        const sizeValue =
                            bedroomSizeRadio.value.toLowerCase();

                        selectedParts.push(
                            sizeValue === 'queen'
                                ? 'Queen: under 10m²'
                                : sizeValue === 'king'
                                    ? 'King: over 10m²'
                                    : bedroomSizeLabel.textContent.trim()
                        );
                    }
                }

                /*
                 * Bathroom
                 */
                if (
                    bathroomRadio &&
                    bathroomRadio.value !== ''
                ) {

                    const bathroomLabel =
                        bathroomRadio.nextElementSibling;

                    if (bathroomLabel) {

                        let bathroomText =
                            bathroomLabel.textContent.trim();

                        bathroomText = bathroomText.replace(
                            /\s*bathrooms?\s*$/i,
                            ''
                        );

                        selectedParts.push(
                            bathroomText + (
                                bathroomText === '1'
                                    ? ' Bath'
                                    : ' Baths'
                            )
                        );
                    }
                }

                triggerText.textContent =
                    selectedParts.length
                        ? selectedParts.join(', ')
                        : 'Any';

                return;
            }

            /*
             * Other dropdowns
             */

            if (!radio.value) {
                triggerText.textContent =
                    container.querySelector('.fpf-layout-popover')
                        ? 'Any Layout'
                        : 'Any';
                return;
            }

            const label = radio.nextElementSibling;

            if (!label) {
                triggerText.textContent = 'Any';
                return;
            }

            const layoutName =
                label.querySelector('.fpf-layout-name');

            triggerText.textContent =
                layoutName
                    ? layoutName.textContent.trim()
                    : label.textContent.trim();
        }

        /*
         * Sort Floor Plans
         */
        function sortFloorPlans() {

            if (!floorPlanGrid || !sortBySelect) {
                return;
            }

            const sortValue = sortBySelect.value;

            const cardsArray = Array.from(
                floorPlanGrid.querySelectorAll('.floor-plan-card')
            );

            if (sortValue === 'default') {

                cardsArray.sort(function (a, b) {

                    return Number(a.dataset.originalOrder) -
                        Number(b.dataset.originalOrder);

                });
            }

            if (sortValue === 'title_asc') {

                cardsArray.sort(function (a, b) {

                    const titleA = a.dataset.title || '';
                    const titleB = b.dataset.title || '';

                    return titleA.localeCompare(
                        titleB,
                        undefined,
                        {
                            numeric: true,
                            sensitivity: 'base'
                        }
                    );
                });
            }

            if (sortValue === 'size_desc') {

                cardsArray.sort(function (a, b) {

                    const sizeA =
                        parseFloat(a.dataset.floorArea || 0);

                    const sizeB =
                        parseFloat(b.dataset.floorArea || 0);

                    return sizeB - sizeA;
                });
            }

            cardsArray.forEach(function (card) {
                floorPlanGrid.appendChild(card);
            });
        }

        /*
         * Filtering
         */
        /*
 * Filtering
 */

        function filterFloorPlans() {

            const selectedBedroom =
                wrapper.querySelector(
                    'input[name$="-bedrooms"]:checked'
                );

            const selectedBedroomSize =
                wrapper.querySelector(
                    'input[name$="-bedroom-size"]:checked'
                )?.value || '';

            const selectedBathroom =
                wrapper.querySelector(
                    'input[name$="-bathrooms"]:checked'
                )?.value || '';

            const selectedLayout =
                wrapper.querySelector(
                    'input[name$="-layout"]:checked'
                );

            const selectedLivingArea =
                livingAreaSelect
                    ? livingAreaSelect.value
                    : '';

            const bedroomValue =
                selectedBedroom
                    ? selectedBedroom.value
                    : 'all';

            const layoutValue =
                selectedLayout
                    ? selectedLayout.value
                    : '';

            const floorAreaValue =
                floorAreaSelect
                    ? floorAreaSelect.value
                    : '';

            const bestForValue =
                bestForSelect
                    ? bestForSelect.value
                    : '';

            const selectedFeatures = Array.from(
                wrapper.querySelectorAll(
                    '.fpf-checkbox-input:checked'
                )
            ).map(function (checkbox) {

                return String(checkbox.value);

            });

            /*
            * Space Available
            *
            * ACF values are stored in millimetres.
            * User inputs are also millimetres.
            */

            const availableWidth = spaceWidthInput
                ? parseFloat(spaceWidthInput.value)
                : NaN;

            const availableDepth = spaceDepthInput
                ? parseFloat(spaceDepthInput.value)
                : NaN;

            const hasSpaceFilter =
                !isNaN(availableWidth) &&
                !isNaN(availableDepth) &&
                availableWidth > 0 &&
                availableDepth > 0;
            /*
             * Check every floor plan
             */

            cards.forEach(function (card) {

                const cardBedrooms =
                    (card.dataset.bedrooms || '')
                        .split(',')
                        .map(function (id) {
                            return id.trim();
                        })
                        .filter(Boolean);

                const cardBathrooms =
                    (card.dataset.bathrooms || '')
                        .split(',')
                        .map(function (id) {
                            return id.trim();
                        })
                        .filter(Boolean);

                const cardLayouts =
                    (card.dataset.layout || '')
                        .split(',')
                        .map(function (id) {
                            return id.trim();
                        })
                        .filter(Boolean);

                const cardBestFor =
                    (card.dataset.bestFor || '')
                        .split(',')
                        .map(function (id) {
                            return id.trim();
                        })
                        .filter(Boolean);

                const cardFeatures =
                    (card.dataset.features || '')
                        .split(',')
                        .map(function (id) {
                            return id.trim();
                        })
                        .filter(Boolean);

                const cardFloorArea =
                    card.dataset.floorArea || '';

                /*
                 * Bedroom
                 */

                const bedroomMatches =
                    bedroomValue === 'all' ||
                    bedroomValue === '' ||
                    cardBedrooms.includes(
                        String(bedroomValue)
                    );

                /*
                 * Bedroom Size
                 */

                const cardBedroomSize =
                    card.dataset.bedroomSize || '';

                const bedroomSizeMatch =
                    !selectedBedroomSize ||
                    cardBedroomSize === selectedBedroomSize;

                /*
                 * Bathroom
                 */

                const bathroomMatches =
                    selectedBathroom === '' ||
                    cardBathrooms.includes(
                        String(selectedBathroom)
                    );

                /*
                 * Layout
                 */

                const layoutMatches =
                    layoutValue === '' ||
                    layoutValue === 'all' ||
                    cardLayouts.includes(
                        String(layoutValue)
                    );

                /*
                 * Floor Area
                 */

                const floorAreaMatches =
                    floorAreaValue === '' ||
                    cardFloorArea === floorAreaValue;

                /*
                 * Best For
                 */

                const bestForMatches =
                    bestForValue === '' ||
                    cardBestFor.includes(
                        String(bestForValue)
                    );

                /*
                 * Features
                 */

                const featuresMatch =
                    selectedFeatures.length === 0 ||
                    selectedFeatures.every(function (feature) {

                        return cardFeatures.includes(feature);

                    });

                /*
                 * Living Area
                 */

                const cardLivingArea =
                    parseFloat(
                        card.dataset.livingArea || 0
                    );

                let livingAreaMatches = true;

                if (selectedLivingArea === 'small') {

                    livingAreaMatches =
                        cardLivingArea < 20;

                }

                if (selectedLivingArea === 'large') {

                    livingAreaMatches =
                        cardLivingArea > 20;

                }

                /*
                * Space Available
                */
                let spaceAvailableMatches = true;

                if (hasSpaceFilter) {

                    const cardPlanLengthMm = parseFloat(
                        card.dataset.planLengthMm || 0
                    );

                    const cardPlanWidthMm = parseFloat(
                        card.dataset.planWidthMm || 0
                    );

                    if (
                        cardPlanLengthMm > 0 &&
                        cardPlanWidthMm > 0
                    ) {

                        spaceAvailableMatches =
                            (
                                cardPlanLengthMm <= availableWidth &&
                                cardPlanWidthMm <= availableDepth
                            ) ||
                            (
                                cardPlanLengthMm <= availableDepth &&
                                cardPlanWidthMm <= availableWidth
                            );

                    } else {

                        spaceAvailableMatches = false;

                    }
                }

                /*
                 * Show / hide card
                 */

                card.style.display =
                    bedroomMatches &&
                        bedroomSizeMatch &&
                        bathroomMatches &&
                        layoutMatches &&
                        floorAreaMatches &&
                        bestForMatches &&
                        featuresMatch &&
                        livingAreaMatches &&
                        spaceAvailableMatches
                        ? ''
                        : 'none';

            });

        }

        /*
         * Add Filter Pill
         */
        function addFilterTag(labelText, resetFunction) {

            if (!tagsList || !labelText) {
                return;
            }

            const tag = document.createElement('span');
            const removeButton = document.createElement('button');

            tag.className = 'fpf-tag-pill';

            removeButton.type = 'button';
            removeButton.className = 'fpf-tag-remove';
            removeButton.innerHTML = '&times;';

            tag.appendChild(
                document.createTextNode(labelText + ' ')
            );

            tag.appendChild(removeButton);

            removeButton.addEventListener('click', function (e) {

                e.preventDefault();
                e.stopPropagation();

                resetFunction();

            });

            tagsList.appendChild(tag);
        }

        /*
         * Update Filter Pills
         */
        function updateFilterTags() {

            if (!tagsList) {
                return;
            }

            tagsList.innerHTML = '';

            /*
             * Bedroom
             */
            const selectedBedroom =
                wrapper.querySelector(
                    'input[name$="-bedrooms"]:checked'
                );

            if (
                selectedBedroom &&
                selectedBedroom.value !== 'all' &&
                selectedBedroom.value !== ''
            ) {

                const label =
                    selectedBedroom.nextElementSibling;

                if (label) {

                    let labelText =
                        label.textContent.trim();

                    labelText = labelText.replace(
                        /\s*bedrooms?\s*$/i,
                        ''
                    );

                    if (
                        labelText.toLowerCase() === 'studio'
                    ) {

                        labelText = 'Studio';

                    } else {

                        labelText +=
                            labelText === '1'
                                ? ' Bedroom'
                                : ' Bedrooms';
                    }

                    addFilterTag(
                        labelText,
                        function () {

                            const anyRadio =
                                wrapper.querySelector(
                                    'input[name$="-bedrooms"][value="all"]'
                                );

                            if (anyRadio) {

                                anyRadio.checked = true;

                                updateSelectTrigger(anyRadio);
                                filterFloorPlans();
                                updateFilterTags();
                            }
                        }
                    );
                }
            }

            /*
             * Bedroom Size
             */
            const selectedBedroomSize =
                wrapper.querySelector(
                    'input[name$="-bedroom-size"]:checked'
                );

            if (
                selectedBedroomSize &&
                selectedBedroomSize.value !== ''
            ) {

                const label =
                    selectedBedroomSize.nextElementSibling;

                if (label) {

                    let labelText =
                        label.textContent.trim();

                    const sizeValue =
                        selectedBedroomSize.value.toLowerCase();

                    if (sizeValue === 'queen') {
                        labelText = 'Queen: under 10m²';
                    } else if (sizeValue === 'king') {
                        labelText = 'King: over 10m²';
                    }

                    addFilterTag(
                        labelText,
                        function () {

                            const anyRadio =
                                wrapper.querySelector(
                                    'input[name$="-bedroom-size"][value=""]'
                                );

                            if (anyRadio) {

                                anyRadio.checked = true;

                                updateSelectTrigger(anyRadio);
                                filterFloorPlans();
                                updateFilterTags();
                            }
                        }
                    );
                }
            }

            /*
             * Bathroom
             */
            const selectedBathroom =
                wrapper.querySelector(
                    'input[name$="-bathrooms"]:checked'
                );

            if (
                selectedBathroom &&
                selectedBathroom.value !== ''
            ) {

                const label =
                    selectedBathroom.nextElementSibling;

                if (label) {

                    let labelText =
                        label.textContent.trim();

                    labelText = labelText.replace(
                        /\s*bathrooms?\s*$/i,
                        ''
                    );

                    labelText +=
                        labelText === '1'
                            ? ' Bathroom'
                            : ' Bathrooms';

                    addFilterTag(
                        labelText,
                        function () {

                            const anyRadio =
                                wrapper.querySelector(
                                    'input[name$="-bathrooms"][value=""]'
                                );

                            if (anyRadio) {

                                anyRadio.checked = true;

                                updateSelectTrigger(anyRadio);
                                filterFloorPlans();
                                updateFilterTags();
                            }
                        }
                    );
                }
            }

            /*
             * Layout
             */
            const selectedLayout =
                wrapper.querySelector(
                    'input[name$="-layout"]:checked'
                );

            if (
                selectedLayout &&
                selectedLayout.value !== ''
            ) {

                const label =
                    selectedLayout.nextElementSibling;

                if (label) {

                    const layoutName =
                        label.querySelector(
                            '.fpf-layout-name'
                        );

                    const labelText =
                        layoutName
                            ? layoutName.textContent.trim()
                            : label.textContent.trim();

                    if (
                        labelText &&
                        labelText !== 'Any Layout'
                    ) {

                        addFilterTag(
                            labelText,
                            function () {

                                const anyRadio =
                                    wrapper.querySelector(
                                        'input[name$="-layout"][value=""]'
                                    );

                                if (anyRadio) {

                                    anyRadio.checked = true;

                                    updateSelectTrigger(anyRadio);
                                    filterFloorPlans();
                                    updateFilterTags();
                                }
                            }
                        );
                    }
                }
            }

            /*
             * Floor Area
             */
            if (
                floorAreaSelect &&
                floorAreaSelect.value !== ''
            ) {

                const selectedOption =
                    floorAreaSelect.options[
                    floorAreaSelect.selectedIndex
                    ];

                if (selectedOption) {

                    addFilterTag(
                        selectedOption.textContent.trim(),
                        function () {

                            floorAreaSelect.value = '';

                            filterFloorPlans();
                            updateFilterTags();
                        }
                    );
                }
            }

            /*
             * Best For
             */
            if (
                bestForSelect &&
                bestForSelect.value !== ''
            ) {

                const selectedOption =
                    bestForSelect.options[
                    bestForSelect.selectedIndex
                    ];

                if (selectedOption) {

                    addFilterTag(
                        selectedOption.textContent.trim(),
                        function () {

                            bestForSelect.value = '';

                            filterFloorPlans();
                            updateFilterTags();
                        }
                    );
                }
            }

            /*
            * Living Area
            */

            if (
                livingAreaSelect &&
                livingAreaSelect.value !== ''
            ) {

                const selectedOption =
                    livingAreaSelect.options[
                    livingAreaSelect.selectedIndex
                    ];

                if (selectedOption) {

                    addFilterTag(
                        selectedOption.textContent.trim(),
                        function () {

                            livingAreaSelect.value = '';

                            filterFloorPlans();
                            updateFilterTags();

                        }
                    );

                }

            }

            /*
             * Features
             */
            const selectedFeatures =
                wrapper.querySelectorAll(
                    '.fpf-checkbox-input:checked'
                );

            selectedFeatures.forEach(function (checkbox) {

                const label =
                    checkbox.closest(
                        '.fpf-checkbox-label'
                    );

                if (!label) {
                    return;
                }

                const labelText =
                    checkbox.dataset.featureName ||
                    label.querySelector(
                        'span:last-child'
                    )?.textContent.trim();

                if (!labelText) {
                    return;
                }

                addFilterTag(
                    labelText,
                    function () {

                        checkbox.checked = false;

                        updateFeatureTrigger();
                        filterFloorPlans();
                        updateFilterTags();
                    }
                );
            });
        }

        /*
         * Update Features Trigger
         */
        function updateFeatureTrigger() {

            if (!featuresContainer) {
                return;
            }

            const triggerText =
                featuresContainer.querySelector(
                    '.fpf-trigger-text'
                );

            if (!triggerText) {
                return;
            }

            const selectedFeatures =
                featuresContainer.querySelectorAll(
                    '.fpf-checkbox-input:checked'
                );

            triggerText.textContent =
                selectedFeatures.length
                    ? selectedFeatures.length + ' Features'
                    : 'Any Features';
        }

        /*
         * Dropdowns
         */
        selectContainers.forEach(function (container) {

            if (
                container.classList.contains(
                    'fpf-features-container'
                )
            ) {
                return;
            }

            const trigger =
                container.querySelector(
                    '.fpf-select-trigger'
                );

            const radios =
                container.querySelectorAll(
                    '.fpf-radio-input'
                );

            if (!trigger) {
                return;
            }

            trigger.addEventListener(
                'click',
                function (e) {

                    e.preventDefault();
                    e.stopPropagation();

                    selectContainers.forEach(
                        function (otherContainer) {

                            if (
                                otherContainer !== container
                            ) {

                                otherContainer.classList.remove(
                                    'is-open'
                                );
                            }
                        }
                    );

                    container.classList.toggle(
                        'is-open'
                    );
                }
            );

            radios.forEach(function (radio) {

                radio.addEventListener(
                    'change',
                    function () {

                        updateSelectTrigger(radio);
                        filterFloorPlans();
                        updateFilterTags();

                    }
                );
            });

            const checkedRadio =
                container.querySelector(
                    '.fpf-radio-input:checked'
                );

            if (checkedRadio) {
                updateSelectTrigger(checkedRadio);
            }
        });

        /*
         * Features
         */
        if (featuresContainer) {

            const featuresTrigger =
                featuresContainer.querySelector(
                    '.fpf-select-trigger'
                );

            if (featuresTrigger) {

                featuresTrigger.addEventListener(
                    'click',
                    function (e) {

                        e.preventDefault();
                        e.stopPropagation();

                        selectContainers.forEach(
                            function (container) {

                                if (
                                    container !==
                                    featuresContainer
                                ) {

                                    container.classList.remove(
                                        'is-open'
                                    );
                                }
                            }
                        );

                        featuresContainer.classList.toggle(
                            'is-open'
                        );
                    }
                );
            }

            featureCheckboxes.forEach(
                function (checkbox) {

                    checkbox.addEventListener(
                        'change',
                        function () {

                            updateFeatureTrigger();
                            filterFloorPlans();
                            updateFilterTags();

                        }
                    );
                }
            );
        }

        /*
         * Floor Area
         */
        if (floorAreaSelect) {

            floorAreaSelect.addEventListener(
                'change',
                function () {

                    filterFloorPlans();
                    updateFilterTags();

                }
            );
        }

        /*
         * Best For
         */
        if (bestForSelect) {

            bestForSelect.addEventListener(
                'change',
                function () {

                    filterFloorPlans();
                    updateFilterTags();

                }
            );
        }

        /*
         * Sort By
         */
        if (sortBySelect) {

            sortBySelect.addEventListener(
                'change',
                function () {

                    sortFloorPlans();

                }
            );
        }

        /*
        * Living Area
        */

        if (livingAreaSelect) {

            livingAreaSelect.addEventListener(
                'change',
                function () {
                    filterFloorPlans();
                    updateFilterTags();
                }
            );

        }

        /*
         * Space Available
         */

        if (spaceWidthInput && spaceDepthInput) {

            spaceWidthInput.addEventListener(
                'input',
                function () {
                    filterFloorPlans();
                    updateFilterTags();
                }
            );

            spaceDepthInput.addEventListener(
                'input',
                function () {
                    filterFloorPlans();
                    updateFilterTags();
                }
            );

        }

        /*
         * View Toggle
         */
        function updateView() {

            const selectedView = wrapper.querySelector(
                'input[name$="-view"]:checked'
            );

            if (!selectedView) {
                return;
            }

            const cards = wrapper.querySelectorAll(
                '.floor-plan-card'
            );

            const showFacade = selectedView.value === 'facade';

            cards.forEach(function (card) {

                const image = showFacade
                    ? card.querySelector('.fpf-facade-image')
                    : card.querySelector('.fpf-floorplan-image');

                const allImages = card.querySelectorAll(
                    '.fpf-floorplan-image, .fpf-facade-image'
                );

                card.classList.add('is-loading');

                allImages.forEach(function (img) {
                    img.style.display = 'none';
                });

                if (!image) {
                    card.classList.remove('is-loading');
                    return;
                }

                image.style.display = 'block';

                if (image.complete) {
                    card.classList.remove('is-loading');
                } else {
                    image.addEventListener(
                        'load',
                        function () {
                            card.classList.remove('is-loading');
                        },
                        { once: true }
                    );

                    image.addEventListener(
                        'error',
                        function () {
                            card.classList.remove('is-loading');
                        },
                        { once: true }
                    );
                }
            });
        }

        viewRadios.forEach(function (radio) {

            radio.addEventListener(
                'change',
                function () {

                    updateView();

                }
            );
        });

        /*
         * Initial view
         */
        updateView();

        /*
         * Close dropdowns
         */
        document.addEventListener(
            'click',
            function (e) {

                if (
                    !e.target.closest(
                        '.fpf-select-container'
                    )
                ) {

                    wrapper.querySelectorAll(
                        '.fpf-select-container.is-open'
                    ).forEach(
                        function (container) {

                            container.classList.remove(
                                'is-open'
                            );

                        }
                    );
                }
            }
        );

        /*
         * Initial State
         */
        updateFeatureTrigger();
        filterFloorPlans();
        sortFloorPlans();
        updateFilterTags();

    });

});