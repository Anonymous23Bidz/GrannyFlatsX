document.addEventListener('click', function (e) {
    const button = e.target.closest('.faq-question');
    if (!button) return;

    const currentItem = button.closest('.faq-item');
    if (!currentItem) return;

    const isActive = currentItem.classList.contains('is-active');

    // Close all items
    document.querySelectorAll('.faq-item').forEach(function (item) {
        item.classList.remove('is-active');
        const btn = item.querySelector('.faq-question');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    });

    // Open clicked item if it was closed
    if (!isActive) {
        currentItem.classList.add('is-active');
        button.setAttribute('aria-expanded', 'true');
    }
});