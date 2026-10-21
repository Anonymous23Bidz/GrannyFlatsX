
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.get-quote-form').forEach(function (form) {
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

        const isStepValid = function (step) {
            const fields = [
                ...step.querySelectorAll('input, select, textarea')
            ].filter(function (field) {
                return !field.disabled && field.willValidate;
            });

            if (!fields.every(function (field) {
                return field.checkValidity();
            })) {
                const invalidField = fields.find(function (field) {
                    return !field.checkValidity();
                });

                invalidField?.reportValidity();
                return false;
            }

            const radioNames = new Set(
                fields
                    .filter(function (field) {
                        return field.type === 'radio' && field.required;
                    })
                    .map(function (field) {
                        return field.name;
                    })
            );

            for (const name of radioNames) {
                if (!fields.some(function (field) {
                    return field.type === 'radio' &&
                        field.name === name &&
                        field.checked;
                })) {
                    return false;
                }
            }

            return true;
        };

        const updateStep = function () {
            const step = steps[currentStep];

            steps.forEach(function (item, index) {
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

        const updateNextButton = function () {
            const step = steps[currentStep];

            const fields = [
                ...step.querySelectorAll('input, select, textarea')
            ].filter(function (field) {
                return !field.disabled && field.willValidate;
            });

            const radios = fields.filter(function (field) {
                return field.type === 'radio' && field.required;
            });

            const radioNames = [
                ...new Set(radios.map(function (field) {
                    return field.name;
                }))
            ];

            const radiosValid = radioNames.every(function (name) {
                return radios.some(function (field) {
                    return field.name === name && field.checked;
                });
            });

            const fieldsValid = fields
                .filter(function (field) {
                    return field.type !== 'radio';
                })
                .every(function (field) {
                    return field.checkValidity();
                });

            nextBtn.disabled =
                isSubmitting || !fieldsValid || !radiosValid;
        };

        form.addEventListener('input', function (event) {
            if (event.target.matches('input, select, textarea')) {
                updateNextButton();
            }
        });

        form.addEventListener('change', function (event) {
            if (event.target.matches('input, select, textarea')) {
                updateNextButton();
            }
        });

        nextBtn.addEventListener('click', function () {
            if (!isStepValid(steps[currentStep])) {
                return;
            }

            if (currentStep < steps.length - 1) {
                currentStep++;
                message.textContent = '';
                updateStep();
            }
        });

        backBtn.addEventListener('click', function () {
            if (isSubmitting) return;

            if (currentStep === 0) {
                form.dispatchEvent(new CustomEvent('gqf:cancel', {
                    bubbles: true,
                    detail: { form: form }
                }));
                return;
            }

            currentStep--;
            message.textContent = '';
            updateStep();
        });

        form.addEventListener('submit', async function (event) {
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
                    detail: { form: form, response: result }
                }));

                setTimeout(function () {
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

    document.addEventListener('gqf:cancel', function (event) {
        const form = event.detail?.form;
        if (!form) return;

        const offCanvas = form.closest('.e-off-canvas');
        if (!offCanvas) return;

        const overlay = offCanvas.querySelector('.e-off-canvas__overlay');
        overlay?.click();
    });

    document.addEventListener('click', function (event) {
        const target = event.target;

        if (!(target instanceof Element)) return;

        const closeBtn = target.closest('#close-btn');
        if (!closeBtn) return;

        const offCanvas = closeBtn.closest('.e-off-canvas');
        if (!offCanvas) return;

        const overlay = offCanvas.querySelector('.e-off-canvas__overlay');
        overlay?.click();
    });

    /* Google Places autocomplete */

    let quoteAddressSelected = false;
    let quotePlacesObserver = null;
    let quotePlacesInterval = null;

    function hideQuotePlacesDropdown() {
        document.querySelectorAll('.pac-container').forEach(function (container) {
            container.classList.add('d-none');
        });
    }

    function showQuotePlacesDropdown() {
        document.querySelectorAll('.pac-container.d-none').forEach(function (container) {
            container.classList.remove('d-none');
        });
    }

    function initQuoteAddressAutocomplete() {
        if (!window.google?.maps?.places) return;

        document.querySelectorAll(
            '.get-quote-form input[name="property_address"]'
        ).forEach(function (addressInput) {
            if (addressInput.autocompleteInstance) return;

            const autocomplete = new google.maps.places.Autocomplete(
                addressInput,
                {
                    componentRestrictions: { country: 'au' },
                    bounds: new google.maps.LatLngBounds(
                        new google.maps.LatLng(-34.15, 150.50),
                        new google.maps.LatLng(-33.50, 151.35)
                    ),
                    strictBounds: true,
                    fields: ['formatted_address', 'geometry', 'place_id'],
                    types: ['address']
                }
            );

            addressInput.autocompleteInstance = autocomplete;
            addressInput.dataset.autocompleteReady = 'true';

            addressInput.addEventListener('input', function () {
                if (addressInput.dataset.settingSelectedAddress === 'true') {
                    return;
                }

                quoteAddressSelected = false;
                showQuotePlacesDropdown();
            });

            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();

                if (!place?.geometry?.location) return;

                if (place.formatted_address) {
                    addressInput.dataset.settingSelectedAddress = 'true';
                    addressInput.value = place.formatted_address;

                    addressInput.dispatchEvent(
                        new Event('input', { bubbles: true })
                    );

                    addressInput.dispatchEvent(
                        new Event('change', { bubbles: true })
                    );

                    delete addressInput.dataset.settingSelectedAddress;
                }

                quoteAddressSelected = true;

                addressInput.blur();
                hideQuotePlacesDropdown();

                requestAnimationFrame(hideQuotePlacesDropdown);
                setTimeout(hideQuotePlacesDropdown, 100);
                setTimeout(hideQuotePlacesDropdown, 300);
            });
        });
    }

    function startQuoteAddressAutocomplete() {
        initQuoteAddressAutocomplete();

        let attempts = 0;

        if (quotePlacesInterval) {
            clearInterval(quotePlacesInterval);
        }

        quotePlacesInterval = setInterval(function () {
            initQuoteAddressAutocomplete();
            attempts++;

            const inputs = document.querySelectorAll(
                '.get-quote-form input[name="property_address"]'
            );

            const allInitialized = inputs.length > 0 &&
                [...inputs].every(function (input) {
                    return input.dataset.autocompleteReady === 'true';
                });

            if (allInitialized || attempts >= 100) {
                clearInterval(quotePlacesInterval);
                quotePlacesInterval = null;
            }
        }, 100);

        if (!quotePlacesObserver) {
            quotePlacesObserver = new MutationObserver(function () {
                if (quoteAddressSelected) {
                    hideQuotePlacesDropdown();
                }
            });

            quotePlacesObserver.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    startQuoteAddressAutocomplete();

    document.addEventListener('focusin', function (event) {
        if (
            event.target.matches(
                '.get-quote-form input[name="property_address"]'
            )
        ) {
            initQuoteAddressAutocomplete();
        }
    });
});
