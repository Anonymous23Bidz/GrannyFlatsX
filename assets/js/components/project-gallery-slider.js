
document.querySelectorAll('.gxf-project-gallery').forEach((slider) => {

    if (slider.dataset.initialized === 'true') return;

    const slides = slider.querySelectorAll('.gxf-gallery-slide');
    const thumbs = slider.querySelectorAll('.gxf-gallery-thumb');
    const prev = slider.querySelector('.gxf-gallery-prev');
    const next = slider.querySelector('.gxf-gallery-next');
    const main = slider.querySelector('.gxf-gallery-main');

    if (!slides.length) return;

    slider.dataset.initialized = 'true';

    let currentIndex = 0;
    let touchStartX = 0;

    // Create Lightbox
    const lightbox = document.createElement('div');
    lightbox.className = 'gxf-gallery-lightbox';

    lightbox.innerHTML = `
            <div class="gxf-lightbox-counter">
                <span class="gxf-lightbox-current">1</span>
                <span>/</span>
                <span class="gxf-lightbox-total">${slides.length}</span>
            </div>
            <button type="button" class="gxf-lightbox-close" aria-label="Close">&times;</button>
            <button type="button" class="gxf-lightbox-prev" aria-label="Previous image">&#10094;</button>
            <div class="gxf-lightbox-content"><img src="" alt=""></div>
            <button type="button" class="gxf-lightbox-next" aria-label="Next image">&#10095;</button>
        `;

    document.body.appendChild(lightbox);

    const counterCurrent = lightbox.querySelector('.gxf-lightbox-current');

    if (counterCurrent) {
        counterCurrent.textContent = currentIndex + 1;
    }

    const lightboxImage = lightbox.querySelector('img');
    const closeButton = lightbox.querySelector('.gxf-lightbox-close');
    const lightboxPrev = lightbox.querySelector('.gxf-lightbox-prev');
    const lightboxNext = lightbox.querySelector('.gxf-lightbox-next');

    // Update Lightbox
    function updateLightbox() {
        const image = slides[currentIndex].querySelector('img');

        lightboxImage.src = image.dataset.fullImage || image.currentSrc || image.src;
        lightboxImage.alt = image.alt;
    }

    // Show Slide
    function showSlide(index) {
        currentIndex = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            slide.classList.toggle('is-active', i === currentIndex);
        });

        thumbs.forEach((thumb, i) => {
            thumb.classList.toggle('is-active', i === currentIndex);
        });

        if (counterCurrent) {
            counterCurrent.textContent = currentIndex + 1;
        }

        thumbs[currentIndex]?.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center'
        });

        if (lightbox.classList.contains('is-active')) {
            updateLightbox();
        }
    }

    // Open Lightbox
    function openLightbox(index) {

        showSlide(index);
        updateLightbox();

        lightbox.classList.add('is-active');
        document.body.classList.add('gxf-lightbox-open');

        closeButton.focus();
    }

    // Close Lightbox
    function closeLightbox() {

        lightbox.classList.remove('is-active');
        document.body.classList.remove('gxf-lightbox-open');

    }

    // Main Image Click
    slides.forEach((slide, index) => {

        const image = slide.querySelector('img');

        if (!image) return;

        image.addEventListener('click', () => {
            openLightbox(index);
        });

        image.addEventListener('keydown', (event) => {

            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openLightbox(index);
            }

        });

    });

    // Main Navigation
    prev?.addEventListener('click', () => showSlide(currentIndex - 1));
    next?.addEventListener('click', () => showSlide(currentIndex + 1));

    // Thumbnail Navigation
    thumbs.forEach((thumb, index) => {
        thumb.addEventListener('click', () => showSlide(index));
    });

    // Lightbox Navigation
    lightboxPrev.addEventListener('click', () => {
        showSlide(currentIndex - 1);
    });

    lightboxNext.addEventListener('click', () => {
        showSlide(currentIndex + 1);
    });

    // Close Button
    closeButton.addEventListener('click', closeLightbox);

    // Close when clicking outside image
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    // Keyboard Navigation
    document.addEventListener('keydown', (event) => {

        if (!lightbox.classList.contains('is-active')) return;

        if (event.key === 'Escape') {
            closeLightbox();
        }

        if (event.key === 'ArrowRight') {
            showSlide(currentIndex + 1);
        }

        if (event.key === 'ArrowLeft') {
            showSlide(currentIndex - 1);
        }

    });

    // Mobile Swipe
    main?.addEventListener('touchstart', (event) => {
        touchStartX = event.changedTouches[0].screenX;
    }, { passive: true });

    main?.addEventListener('touchend', (event) => {

        const difference = touchStartX - event.changedTouches[0].screenX;

        if (Math.abs(difference) > 50) {
            showSlide(currentIndex + (difference > 0 ? 1 : -1));
        }

    }, { passive: true });

    showSlide(0);

});
