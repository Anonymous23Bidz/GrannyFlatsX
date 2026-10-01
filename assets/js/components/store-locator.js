document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('wpsl-search-input');
    const category = document.getElementById('wpsl-category');

    if (!searchInput) {
        return;
    }

    searchInput.value = 'Merrylands, NSW 2160, Australia';
    searchInput.setAttribute('readonly', 'readonly');

    if (category) {

        category.addEventListener('change', function () {

            const searchButton = document.getElementById('wpsl-search-btn');

            if (searchButton) {
                searchButton.click();
            }

        });

    }
});

document.addEventListener('DOMContentLoaded', function () {
    const nativeLoadMoreWrapper = document.querySelector('.e-loop__load-more');

    const customBtnWidget = document.getElementById('view-all-location');

    if (!customBtnWidget) return;

    const btnTextElement = customBtnWidget.querySelector('.elementor-button-text');
    const btnLinkElement = customBtnWidget.querySelector('a');
    const originalText = btnTextElement ? btnTextElement.textContent : 'View all projects';

    const loopContainer = document.querySelector('.elementor-loop-container');

    if (loopContainer) {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.addedNodes.length > 0) {
                    if (btnTextElement) btnTextElement.textContent = originalText;
                    if (btnLinkElement) btnLinkElement.classList.remove('is-loading');
                }
            });
        });

        observer.observe(loopContainer, { childList: true });
    }

    document.body.addEventListener('click', function (e) {
        const customBtn = e.target.closest('#view-all-location');

        if (customBtn) {
            e.preventDefault();

            const nativeLoadMoreBtn = document.querySelector('.e-loop__load-more a, .e-loop__load-more [role="button"]');

            if (nativeLoadMoreBtn) {
                if (btnTextElement) btnTextElement.textContent = 'Loading...';
                if (btnLinkElement) btnLinkElement.classList.add('is-loading');

                nativeLoadMoreBtn.click();
            } else {
                console.warn('Elementor native load more button not found.');
            }
        }
    });
});