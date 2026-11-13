(function () {
    const elements = document.querySelectorAll('.gxf-reading-time');

    if (!elements.length) return;

    elements.forEach(function (element) {
        const postId = element.dataset.postId;

        if (!postId) return;

        const storageKey = 'gxf_reading_time_' + postId;

        let totalTime = Number(sessionStorage.getItem(storageKey)) || 0;
        let startTime = document.visibilityState === 'visible' ? Date.now() : null;

        function updateReadingTime() {
            const currentTime = totalTime + (
                startTime ? Date.now() - startTime : 0
            );

            const minutes = Math.floor(currentTime / 60000);

            element.textContent = minutes < 1
                ? '1 min'
                : minutes + ' min';
        }

        function saveReadingTime() {
            if (startTime !== null) {
                totalTime += Date.now() - startTime;
                startTime = null;
            }

            sessionStorage.setItem(storageKey, totalTime);
            updateReadingTime();
        }

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                saveReadingTime();
            } else if (startTime === null) {
                startTime = Date.now();
            }
        });

        window.addEventListener('pagehide', saveReadingTime);

        updateReadingTime();
        setInterval(updateReadingTime, 1000);
    });
})();

