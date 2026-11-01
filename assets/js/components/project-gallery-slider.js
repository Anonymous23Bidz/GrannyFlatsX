
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

    function showSlide(index) {

        currentIndex = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            slide.classList.toggle('is-active', i === currentIndex);
        });

        thumbs.forEach((thumb, i) => {
            thumb.classList.toggle('is-active', i === currentIndex);
        });

        thumbs[currentIndex]?.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center'
        });
    }

    prev?.addEventListener('click', () => {
        showSlide(currentIndex - 1);
    });

    next?.addEventListener('click', () => {
        showSlide(currentIndex + 1);
    });

    thumbs.forEach((thumb, index) => {
        thumb.addEventListener('click', () => showSlide(index));
    });

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
