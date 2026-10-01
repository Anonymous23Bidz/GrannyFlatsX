document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.custom-location-locator').forEach(function (container) {

        const select = container.querySelector('.location-locator-select');
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
            const matchingItems = [];

            items.forEach(function (item) {

                const lgas = item.dataset.lgas
                    ? item.dataset.lgas.split(',')
                    : [];

                if (!selectedLga || lgas.includes(String(selectedLga))) {
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

        if (searchButton) {
            searchButton.addEventListener('click', function () {
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
