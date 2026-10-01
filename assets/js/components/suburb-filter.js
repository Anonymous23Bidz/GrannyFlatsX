document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.custom-location-locator').forEach(function (container) {

        const select = container.querySelector('.location-locator-select');
        const customSelect = container.querySelector('.location-locator-custom-select');
        const selectTrigger = container.querySelector('.location-locator-select-trigger');
        const selectValue = container.querySelector('.location-locator-select-value');
        const selectOptions = container.querySelectorAll('.location-locator-select-option');

        const searchInput = container.querySelector('.location-locator-input');

        const suggestions = container.querySelector('.location-locator-suggestions');

        const searchButton = container.querySelector('.location-locator-search');

        const loadMoreButton = container.querySelector('.location-load-more-button');

        const items = container.querySelectorAll('.location-item');

        const noResults = container.querySelector('.location-no-results');

        if (!select || !items.length) {

            return;

        }

        const itemsPerLoad = parseInt(container.dataset.itemsPerLoad, 10) || 8;

        let visibleCount = itemsPerLoad;

        function updateLocations() {

            const selectedLga = select.value;

            const searchValue = searchInput
                ? searchInput.value.trim().toLowerCase()
                : '';

            const matchingItems = [];

            items.forEach(function (item) {

                const lgas = item.dataset.lgas
                    ? item.dataset.lgas.split(',')
                    : [];

                const title = item.dataset.title
                    ? item.dataset.title.toLowerCase()
                    : item.querySelector('.floor-plan-title')
                        ? item.querySelector('.floor-plan-title').textContent.toLowerCase()
                        : '';

                const matchesLga =
                    !selectedLga ||
                    lgas.includes(String(selectedLga));

                const matchesSearch =
                    !searchValue ||
                    title.includes(searchValue);

                if (matchesLga && matchesSearch) {

                    matchingItems.push(item);

                }

                item.style.display = 'none';

            });

            matchingItems.forEach(function (item, index) {

                if (index < visibleCount) {

                    item.style.display = '';

                }

            });

            if (noResults) {

                noResults.style.display =
                    matchingItems.length === 0 ? '' : 'none';

            }

            if (loadMoreButton) {

                if (matchingItems.length > visibleCount) {

                    loadMoreButton.style.display = '';

                } else {

                    loadMoreButton.style.display = 'none';

                }

            }

        }

        /*
         * SUBURB AUTOCOMPLETE
         */
        if (searchInput && suggestions) {

            searchInput.addEventListener('input', function () {

                const value = searchInput.value.trim().toLowerCase();

                suggestions.innerHTML = '';

                if (!value) {

                    suggestions.style.display = 'none';

                    return;

                }

                const suburbs = [];

                items.forEach(function (item) {

                    const title = item.dataset.title || '';

                    if (
                        title.includes(value) &&
                        !suburbs.includes(title)
                    ) {

                        suburbs.push(title);

                    }

                });

                if (!suburbs.length) {

                    suggestions.style.display = 'none';

                    return;

                }

                suburbs.forEach(function (suburb) {

                    const suggestion = document.createElement('div');

                    suggestion.className = 'location-locator-suggestion';

                    suggestion.textContent = suburb;

                    suggestion.addEventListener('click', function () {

                        searchInput.value = suburb;

                        suggestions.innerHTML = '';

                        suggestions.style.display = 'none';

                    });

                    suggestions.appendChild(suggestion);

                });

                suggestions.style.display = 'block';

            });

            document.addEventListener('click', function (event) {

                if (
                    searchInput &&
                    suggestions &&
                    !searchInput.contains(event.target) &&
                    !suggestions.contains(event.target)
                ) {
                    suggestions.style.display = 'none';
                }

                if (
                    customSelect &&
                    !customSelect.contains(event.target)
                ) {
                    customSelect.classList.remove('is-open');
                }

            });

        }

        if (searchButton) {
            searchButton.addEventListener('click', function () {
                visibleCount = itemsPerLoad;
                if (suggestions) {
                    suggestions.style.display = 'none';
                }
                updateLocations();
            });

        }

        if (customSelect && selectTrigger) {

            selectTrigger.addEventListener('click', function (event) {

                event.stopPropagation();

                customSelect.classList.toggle('is-open');

            });

            selectOptions.forEach(function (option) {
                option.addEventListener('click', function () {
                    const value = option.dataset.value;
                    const text = option.textContent.trim();
                    select.value = value;
                    selectValue.textContent = text;

                    selectOptions.forEach(function (item) {
                        item.classList.remove('is-selected');
                    });
                    option.classList.add('is-selected');
                    customSelect.classList.remove('is-open');
                    visibleCount = itemsPerLoad;

                });

            });

        }

        /*
         * LGA FILTER
         */
        if (select) {
            select.addEventListener('change', function () {
                visibleCount = itemsPerLoad;
                updateLocations();
            });

        }

        if (loadMoreButton) {

            loadMoreButton.addEventListener('click', function () {

                visibleCount += itemsPerLoad;

                updateLocations();

            });

        }
        updateLocations();

    });

});
