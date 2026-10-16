
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.get-quote-form').forEach(form => {
        const steps = [...form.querySelectorAll('.gqf-step')];
        const currentLabel = form.querySelector('.gqf-current-step');
        const progressBar = form.querySelector('.gqf-progress-bar');
        const backBtn = form.querySelector('.gqf-back');
        const nextBtn = form.querySelector('.gqf-next');
        const submitBtn = form.querySelector('.gqf-submit');

        if (!steps.length || !currentLabel || !progressBar || !backBtn || !nextBtn || !submitBtn) {
            return;
        }

        let currentStep = 0;
        let isSubmitting = false;

        let message = form.querySelector('.gqf-message');

        if (!message) {
            message = document.createElement('div');
            message.className = 'gqf-message';
            message.setAttribute('role', 'status');
            message.setAttribute('aria-live', 'polite');
            form.appendChild(message);
        }

        const isStepValid = step => {
            const fields = [
                ...step.querySelectorAll('input, select, textarea')
            ].filter(field => !field.disabled && field.willValidate);

            if (!fields.every(field => field.checkValidity())) {
                const invalidField = fields.find(field => !field.checkValidity());
                invalidField?.reportValidity();
                return false;
            }

            const radioNames = new Set(
                fields
                    .filter(field => field.type === 'radio' && field.required)
                    .map(field => field.name)
            );

            for (const name of radioNames) {
                if (!fields.some(field =>
                    field.type === 'radio' &&
                    field.name === name &&
                    field.checked
                )) {
                    return false;
                }
            }

            return true;
        };

        const updateStep = () => {
            const step = steps[currentStep];

            steps.forEach((item, index) => {
                item.classList.toggle('is-active', index === currentStep);
            });

            currentLabel.textContent = currentStep + 1;
            progressBar.style.width =
                `${((currentStep + 1) / steps.length) * 100}%`;

            backBtn.textContent = step.dataset.backLabel || 'Back';
            nextBtn.textContent = step.dataset.nextLabel || 'Next';

            nextBtn.hidden = currentStep === steps.length - 1;
            submitBtn.hidden = currentStep !== steps.length - 1;

            backBtn.disabled = isSubmitting;
            nextBtn.disabled = isSubmitting;
            submitBtn.disabled = isSubmitting;

            updateNextButton();
        };

        const updateNextButton = () => {
            const step = steps[currentStep];

            const fields = [
                ...step.querySelectorAll('input, select, textarea')
            ].filter(field => !field.disabled && field.willValidate);

            const radios = fields.filter(
                field => field.type === 'radio' && field.required
            );

            const radioNames = [...new Set(
                radios.map(field => field.name)
            )];

            const radiosValid = radioNames.every(name =>
                radios.some(field =>
                    field.name === name && field.checked
                )
            );

            const fieldsValid = fields
                .filter(field => field.type !== 'radio')
                .every(field => field.checkValidity());

            nextBtn.disabled =
                isSubmitting || !fieldsValid || !radiosValid;
        };

        form.addEventListener('input', event => {
            if (event.target.matches('input, select, textarea')) {
                updateNextButton();
            }
        });

        form.addEventListener('change', event => {
            if (event.target.matches('input, select, textarea')) {
                updateNextButton();
            }
        });

        nextBtn.addEventListener('click', event => {
            event.preventDefault();

            if (!isStepValid(steps[currentStep])) {
                return;
            }

            if (currentStep < steps.length - 1) {
                currentStep++;
                message.textContent = '';
                updateStep();
            }
        });

        backBtn.addEventListener('click', event => {
            event.preventDefault();

            if (isSubmitting) return;

            if (currentStep === 0) {
                form.dispatchEvent(new CustomEvent('gqf:cancel', {
                    bubbles: true,
                    detail: { form }
                }));

                return;
            }

            currentStep--;
            message.textContent = '';
            updateStep();
        });

        form.addEventListener('submit', async event => {
            event.preventDefault();

            if (isSubmitting || !isStepValid(steps[currentStep])) {
                return;
            }

            const ajaxUrl = form.dataset.ajaxUrl;

            if (!ajaxUrl) {
                message.textContent =
                    'The form is missing its AJAX URL. Please contact us.';
                return;
            }

            const formData = new FormData(form);

            isSubmitting = true;
            message.textContent = 'Sending your quote request.';
            message.style.color = '';
            submitBtn.textContent = 'Sending...';

            updateStep();

            try {
                const response = await fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(
                        result?.data?.message ||
                        'Your request could not be sent. Please try again.'
                    );
                }

                message.textContent =
                    result.data?.message ||
                    'Your quote request was sent successfully!';

                message.style.color = 'green';

                form.dispatchEvent(new CustomEvent('gqf:success', {
                    bubbles: true,
                    detail: { form, response: result }
                }));

                setTimeout(() => {
                    window.location.href = '/thank-you-page-form/';
                }, 2000);

                return;

            } catch (error) {
                message.textContent = error.message ||
                    'Something went wrong. Please try again.';

                message.style.color = '#b42318';

            } finally {
                isSubmitting = false;
                submitBtn.textContent = 'Get My Free Quote';
                updateStep();
            }
        });

        updateStep();
    });

    // Close the Elementor off-canvas from the first Back button.
    document.addEventListener('gqf:cancel', event => {
        const form = event.detail?.form;
        if (!form) return;

        const offCanvas = form.closest('.e-off-canvas');
        if (!offCanvas) return;

        closeQuoteOffCanvas(offCanvas);
    });

    // Close the Elementor off-canvas from the close button.
    document.addEventListener('click', event => {
        const closeBtn = event.target.closest('#close-btn');
        if (!closeBtn) return;

        const offCanvas = closeBtn.closest('.e-off-canvas');
        if (!offCanvas) return;

        event.preventDefault();

        closeQuoteOffCanvas(offCanvas);
    });

    function closeQuoteOffCanvas(offCanvas) {
        const overlay = offCanvas.querySelector('.e-off-canvas__overlay');

        if (overlay) {
            overlay.click();
        }

        // Restore scrolling only after Elementor processes the close.
        setTimeout(() => {
            const anotherCanvasOpen = [
                ...document.querySelectorAll('.e-off-canvas')
            ].some(canvas => {
                return canvas !== offCanvas &&
                    canvas.getClientRects().length > 0 &&
                    canvas.getAttribute('aria-hidden') !== 'true';
            });

            if (anotherCanvasOpen) return;

            const body = document.body;
            const html = document.documentElement;

            body.style.removeProperty('overflow');
            body.style.removeProperty('overflow-y');
            body.style.removeProperty('position');
            body.style.removeProperty('top');
            body.style.removeProperty('width');

            html.style.removeProperty('overflow');
            html.style.removeProperty('overflow-y');
        }, 350);
    }

    // Google Places address autocomplete.
    function initQuoteAddressAutocomplete() {
        document.querySelectorAll(
            '.get-quote-form #property-address'
        ).forEach(addressInput => {
            if (addressInput.dataset.autocompleteReady === 'true') {
                return;
            }

            if (!window.google?.maps?.places) {
                return;
            }

            const autocomplete = new google.maps.places.Autocomplete(
                addressInput,
                {
                    componentRestrictions: { country: 'au' },
                    fields: ['formatted_address', 'geometry', 'place_id'],
                    types: ['address']
                }
            );

            addressInput.dataset.autocompleteReady = 'true';

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();

                if (!place?.geometry?.location) return;

                if (place.formatted_address) {
                    addressInput.value = place.formatted_address;
                }

                addressInput.dispatchEvent(
                    new Event('input', { bubbles: true })
                );

                addressInput.dispatchEvent(
                    new Event('change', { bubbles: true })
                );

                setTimeout(() => {
                    document.querySelectorAll('.pac-container').forEach(
                        container => {
                            container.classList.add('gqf-quote-hidden');
                        }
                    );

                    addressInput.blur();
                }, 100);
            });

            ['focus', 'input'].forEach(eventName => {
                addressInput.addEventListener(eventName, () => {
                    document.querySelectorAll('.pac-container').forEach(
                        container => {
                            container.classList.remove('gqf-quote-hidden');
                        }
                    );
                });
            });
        });
    }

    initQuoteAddressAutocomplete();

    let quotePlacesAttempts = 0;

    const quotePlacesInterval = setInterval(() => {
        initQuoteAddressAutocomplete();

        quotePlacesAttempts++;

        if (
            quotePlacesAttempts >= 100 ||
            document.querySelector(
                '.get-quote-form #property-address[data-autocomplete-ready="true"]'
            )
        ) {
            clearInterval(quotePlacesInterval);
        }
    }, 100);
});
