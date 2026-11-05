function initGuestRegistrationForm() {
    document.querySelectorAll('.gxf-guest-form').forEach(function (form) {
        if (form.dataset.initialized === 'true') return;

        form.dataset.initialized = 'true';

        const submitBtn = form.querySelector('.gxf-guest-submit');
        const message = form.querySelector('.gxf-guest-message');

        if (!submitBtn || !message) return;

        let isSubmitting = false;

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (isSubmitting) return;

            const ajaxUrl = form.dataset.ajaxUrl;

            if (!ajaxUrl) {
                message.textContent = 'The form is missing its AJAX URL. Please contact us.';
                message.className = 'gxf-guest-message error';
                return;
            }

            const formData = new FormData(form);

            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            message.textContent = 'Submitting your registration...';
            message.className = 'gxf-guest-message';

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
                        'Your registration could not be submitted. Please try again.'
                    );
                }

                message.textContent =
                    result.data?.message ||
                    'Thank you! Your registration has been received.';

                message.className = 'gxf-guest-message success';

                form.dispatchEvent(new CustomEvent('gxf:guest-success', {
                    bubbles: true,
                    detail: {
                        form: form,
                        response: result
                    }
                }));

                submitBtn.textContent = 'Registration Received';

                setTimeout(function () {
                    window.location.href = '/thank-you-page-form/';
                }, 3000);

            } catch (error) {
                message.textContent = error.message ||
                    'Something went wrong. Please try again.';

                message.className = 'gxf-guest-message error';

            } finally {
                if (!message.classList.contains('success')) {
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Registration';
                }
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGuestRegistrationForm, { once: true });
} else {
    initGuestRegistrationForm();
}