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

        const sortBySelect = wrapper.querySelector('select[name="sort_by"]');
        const floorPlanGrid = wrapper.querySelector('.floor-plan-grid');

        const tagsList = wrapper.querySelector('.fpf-tags-list');

        const featuresContainer = wrapper.querySelector('.fpf-features-container');
        const featureCheckboxes = wrapper.querySelectorAll('.fpf-checkbox-input');


        function updateSelectTrigger(radio) {

            const container = radio.closest('.fpf-select-container');

            if (!container) {
                return;
            }

            const triggerText = container.querySelector('.fpf-trigger-text');

            if (!triggerText) {
                return;
            }

            if (!radio.value) {

                triggerText.textContent =
                    container.classList.contains('fpf-features-container')
                        ? 'Any Features'
                        : container.querySelector('.fpf-layout-popover')
                            ? 'Any Layout'
                            : 'Any';

                return;
            }

            const label = radio.nextElementSibling;

            if (!label) {
                triggerText.textContent = 'Any';
                return;
            }

            const layoutName = label.querySelector('.fpf-layout-name');

            triggerText.textContent = layoutName
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

                    const sizeA = parseFloat(
                        a.dataset.floorArea || 0
                    );

                    const sizeB = parseFloat(
                        b.dataset.floorArea || 0
                    );

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

        function filterFloorPlans() {

            const selectedBedroom =
                wrapper.querySelector('input[name$="-bedrooms"]:checked');

            const selectedLayout =
                wrapper.querySelector('input[name$="-layout"]:checked');

            const bedroomValue =
                selectedBedroom ? selectedBedroom.value : 'all';

            const layoutValue =
                selectedLayout ? selectedLayout.value : '';

            const floorAreaValue =
                floorAreaSelect ? floorAreaSelect.value : '';

            const bestForValue =
                bestForSelect ? bestForSelect.value : '';


            const selectedFeatures = Array.from(
                wrapper.querySelectorAll('.fpf-checkbox-input:checked')
            ).map(function (checkbox) {

                return String(checkbox.value);

            });


            /*
             * Check every floor plan
             */

            cards.forEach(function (card) {

                const cardBedrooms = (card.dataset.bedrooms || '')
                    .split(',')
                    .map(function (id) {
                        return id.trim();
                    })
                    .filter(Boolean);


                const cardLayouts = (card.dataset.layout || '')
                    .split(',')
                    .map(function (id) {
                        return id.trim();
                    })
                    .filter(Boolean);


                const cardBestFor = (card.dataset.bestFor || '')
                    .split(',')
                    .map(function (id) {
                        return id.trim();
                    })
                    .filter(Boolean);


                const cardFeatures = (card.dataset.features || '')
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
                    cardBedrooms.includes(String(bedroomValue));


                /*
                 * Layout
                 */

                const layoutMatches =
                    layoutValue === '' ||
                    layoutValue === 'all' ||
                    cardLayouts.includes(String(layoutValue));


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
                    cardBestFor.includes(String(bestForValue));


                /*
                 * Features
                 *
                 * ALL selected features must exist
                 * on the floor plan.
                 */

                const featuresMatch =
                    selectedFeatures.length === 0 ||
                    selectedFeatures.every(function (feature) {

                        return cardFeatures.includes(feature);

                    });


                /*
                 * Show / hide card
                 */

                card.style.display =
                    bedroomMatches &&
                        layoutMatches &&
                        floorAreaMatches &&
                        bestForMatches &&
                        featuresMatch
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
                wrapper.querySelector('input[name$="-bedrooms"]:checked');

            if (
                selectedBedroom &&
                selectedBedroom.value !== 'all' &&
                selectedBedroom.value !== ''
            ) {

                const label =
                    selectedBedroom.nextElementSibling;

                if (label) {

                    const labelText =
                        label.textContent.trim();

                    addFilterTag(
                        labelText + ' Bedrooms',
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
             * Layout
             */

            const selectedLayout =
                wrapper.querySelector('input[name$="-layout"]:checked');

            if (
                selectedLayout &&
                selectedLayout.value !== ''
            ) {

                const label =
                    selectedLayout.nextElementSibling;

                if (label) {

                    const layoutName =
                        label.querySelector('.fpf-layout-name');

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
             * Features
             */

            const selectedFeatures =
                wrapper.querySelectorAll(
                    '.fpf-checkbox-input:checked'
                );

            selectedFeatures.forEach(function (checkbox) {

                const label =
                    checkbox.closest('.fpf-checkbox-label');

                if (!label) {
                    return;
                }

                const labelText =
                    checkbox.dataset.featureName ||
                    label.querySelector('span:last-child')?.textContent.trim();

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
         * Update Features trigger text
         */

        function updateFeatureTrigger() {

            if (!featuresContainer) {
                return;
            }

            const triggerText =
                featuresContainer.querySelector('.fpf-trigger-text');

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

            /*
             * Features is handled separately
             */

            if (
                container.classList.contains(
                    'fpf-features-container'
                )
            ) {
                return;
            }

            const trigger =
                container.querySelector('.fpf-select-trigger');

            const radios =
                container.querySelectorAll('.fpf-radio-input');

            if (!trigger) {
                return;
            }


            /*
             * Open / close dropdown
             */

            trigger.addEventListener('click', function (e) {

                e.preventDefault();
                e.stopPropagation();

                selectContainers.forEach(function (otherContainer) {

                    if (otherContainer !== container) {

                        otherContainer.classList.remove(
                            'is-open'
                        );

                    }

                });

                container.classList.toggle('is-open');

            });


            /*
             * Radio changes
             */

            radios.forEach(function (radio) {

                radio.addEventListener('change', function () {

                    updateSelectTrigger(radio);

                    filterFloorPlans();
                    updateFilterTags();

                });

            });


            /*
             * Initial radio
             */

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


            /*
             * Feature checkbox changes
             */

            featureCheckboxes.forEach(function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    function () {

                        updateFeatureTrigger();

                        filterFloorPlans();
                        updateFilterTags();

                    }
                );

            });

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
        * View Toggle
        */
        function updateView() {

            const selectedView = wrapper.querySelector(
                'input[name$="-view"]:checked'
            );

            if (!selectedView) {
                return;
            }

            const facadeImages = wrapper.querySelectorAll(
                '.fpf-facade-image'
            );

            const floorplanImages = wrapper.querySelectorAll(
                '.fpf-floorplan-image'
            );

            const showFacade = selectedView.value === 'facade';

            facadeImages.forEach(function (image) {

                image.style.display = showFacade
                    ? 'block'
                    : 'none';

            });

            floorplanImages.forEach(function (image) {

                image.style.display = showFacade
                    ? 'none'
                    : 'block';

            });

        }

        viewRadios.forEach(function (radio) {

            radio.addEventListener('change', function () {

                updateView();

            });

        });

        /* Initial view */
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
                    ).forEach(function (container) {

                        container.classList.remove(
                            'is-open'
                        );

                    });

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
