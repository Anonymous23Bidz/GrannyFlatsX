let googleMapsPlacesPromise;

/**
 * Load Google Maps Places only when an address field needs it.
 * The URL is supplied by WordPress so the API key remains managed in wp-config.php.
 */
export function loadGoogleMapsPlaces() {
    if (window.google?.maps?.places) {
        return Promise.resolve(window.google);
    }

    if (googleMapsPlacesPromise) {
        return googleMapsPlacesPromise;
    }

    const url = window.helloChildMaps?.url || window.helloChildGoogleMapsUrl;

    if (!url) {
        return Promise.reject(new Error('Google Maps Places is not configured.'));
    }

    googleMapsPlacesPromise = new Promise((resolve, reject) => {
        const callbackName = '__helloChildGoogleMapsPlacesReady';
        const scriptUrl = new URL(url);
        const script = document.createElement('script');
        let timeout;

        const cleanUp = () => {
            clearTimeout(timeout);
            delete window[callbackName];
        };

        // With `loading=async`, Google's script onload event can happen before
        // the Places library is ready. Its callback is the reliable signal.
        window[callbackName] = () => {
            cleanUp();
            resolve(window.google);
        };

        scriptUrl.searchParams.set('callback', callbackName);

        script.src = scriptUrl.href;
        script.async = true;
        script.onload = () => {
            if (window.google?.maps?.places) {
                cleanUp();
                resolve(window.google);
            }
        };
        script.onerror = () => {
            cleanUp();
            googleMapsPlacesPromise = undefined;
            reject(new Error('Google Maps Places could not be loaded.'));
        };

        timeout = window.setTimeout(() => {
            cleanUp();
            googleMapsPlacesPromise = undefined;
            reject(new Error('Google Maps Places took too long to load.'));
        }, 15000);

        document.head.appendChild(script);
    });

    return googleMapsPlacesPromise;
}
