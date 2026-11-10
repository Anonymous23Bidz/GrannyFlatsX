<?php

if (!defined('ABSPATH')) {
    exit;
}

function gxf_guest_registration_form_shortcode()
{

    ob_start();
?>

    <form
        class="gxf-guest-form"
        method="post"
        data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">

        <input type="hidden" name="action" value="gxf_guest_registration_send">

        <input type="hidden" name="gxf_guest_nonce" value="<?php echo esc_attr(wp_create_nonce('gxf_guest_registration')); ?>">
        <input type="hidden" name="source_page_title" value="<?php echo esc_attr(get_the_title()); ?>">
        <input type="hidden" name="source_page_url" value="<?php echo esc_url(get_permalink()); ?>">
        <input type="hidden" name="source_post_id" value="<?php echo esc_attr(get_the_ID()); ?>">

        <div class="gxf-guest-field">
            <label for="gxf-guest-name">Full Name</label>
            <input
                type="text"
                id="gxf-guest-name"
                name="full_name"
                placeholder="Your name"
                autocomplete="name"
                required>
        </div>

        <div class="gxf-guest-field">
            <label for="gxf-guest-email">Email Address</label>
            <input
                type="email"
                id="gxf-guest-email"
                name="email"
                placeholder="you@email.com"
                autocomplete="email"
                required>
        </div>

        <div class="gxf-guest-field">
            <label for="gxf-guest-phone">Phone Number</label>
            <input
                type="tel"
                id="gxf-guest-phone"
                name="phone"
                placeholder="04XX XXX XXX"
                autocomplete="tel"
                required>
        </div>

        <div class="gxf-guest-field">
            <label for="gxf-guest-guests">Number of Guests</label>

            <div class="gxf-guest-select-wrap">
                <select id="gxf-guest-guests" name="guests" required>
                    <?php for ($i = 1; $i <= 10; $i++) : ?>
                        <option value="<?php echo esc_attr($i); ?>">
                            <?php echo esc_html($i); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div class="gxf-guest-message" aria-live="polite"></div>

        <button type="submit" class="gxf-guest-submit">
            Register Now
        </button>

    </form>
<?php

    return ob_get_clean();
}

add_shortcode(
    'guest_registration_form',
    'gxf_guest_registration_form_shortcode'
);


/**
 * Process Guest Registration AJAX
 */

function gxf_guest_registration_send()
{
    if (
        !isset($_POST['gxf_guest_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['gxf_guest_nonce'])),
            'gxf_guest_registration'
        )
    ) {
        wp_send_json_error([
            'message' => 'Security check failed. Please refresh the page and try again.'
        ]);
    }

    $event_address = 'Not specified';

    $source_post_id = isset($_POST['source_post_id'])
        ? absint($_POST['source_post_id'])
        : 0;

    if (
        $source_post_id &&
        get_post_type($source_post_id) === 'event' &&
        function_exists('get_field')
    ) {
        $address = get_field('event_address', $source_post_id);

        if (is_string($address) && $address !== '') {
            $event_address = sanitize_text_field($address);
        }
    }

    $source_page_title = isset($_POST['source_page_title'])
        ? sanitize_text_field(wp_unslash($_POST['source_page_title']))
        : '';

    $source_page_url = isset($_POST['source_page_url'])
        ? esc_url_raw(wp_unslash($_POST['source_page_url']))
        : '';

    $name = isset($_POST['full_name'])
        ? sanitize_text_field(wp_unslash($_POST['full_name']))
        : '';

    $email = isset($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email']))
        : '';

    $phone = isset($_POST['phone'])
        ? sanitize_text_field(wp_unslash($_POST['phone']))
        : '';

    $guests = isset($_POST['guests'])
        ? absint($_POST['guests'])
        : 0;

    if (!$name || !$email || !$phone || !$guests) {
        wp_send_json_error([
            'message' => 'Please complete all fields.'
        ]);
    }

    if (!is_email($email)) {
        wp_send_json_error([
            'message' => 'Please enter a valid email address.'
        ]);
    }

    if ($guests < 1 || $guests > 10) {
        wp_send_json_error([
            'message' => 'Please select a valid number of guests.'
        ]);
    }

    $priority = 'Medium';

    $to = 'lead.nextgengrannyflats@gmail.com';

    $subject = 'Guest Registration For ' . $source_page_title . ' - ' . $name;

    $message = "Guest Registration For " . $source_page_title . "\n\n";

    $message .= "CUSTOMER DETAILS\n";
    $message .= "Full Name: " . $name . "\n";
    $message .= "Email Address: " . $email . "\n";
    $message .= "Phone Number: " . $phone . "\n";
    $message .= "Number of Guests: " . $guests . "\n";

    $message .= "\nEVENT DETAILS\n";
    $message .= "Event Title: " . $source_page_title . "\n";
    $message .= "Event Address: " . $event_address . "\n";
    $message .= "Page URL: " . $source_page_url . "\n\n";

    $message .= "Priority: " . strtoupper($priority) . "\n";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>'
    ];

    $sent = wp_mail(
        $to,
        $subject,
        $message,
        $headers
    );

    if (!$sent) {

        error_log('GXF Guest Registration: Notification email failed for ' . $email);

        wp_send_json_error([
            'message' => 'Registration could not be submitted. Please try again.'
        ]);
    }

    // Customer confirmation email

    $customer_subject = 'Registration Received - Granny Flats X';

    $customer_message = '
    <!DOCTYPE html>
    <html>
    <body style="margin:0;padding:24px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#333;">
        <div style="max-width:600px;margin:auto;">
            <p>Hi ' . esc_html($name) . ',</p>

            <p>Thank you for registering with Granny Flats X.</p>

            <p>We have received your registration with the following details:</p>

            <p>
                <strong>Name:</strong> ' . esc_html($name) . '<br>
                <strong>Email:</strong> ' . esc_html($email) . '<br>
                <strong>Phone:</strong> ' . esc_html($phone) . '<br>
                <strong>Number of Guests:</strong> ' . esc_html($guests) . '<br>
                <strong>Event Address:</strong> ' . esc_html($event_address) . '
            </p>

            <p>We look forward to seeing you!</p>

            <p>
                Regards,<br>
                <strong>Granny Flats X Team</strong><br>
                (02) 9191 7415 | 1300 950 764
            </p>
        </div>
    </body>
    </html>';

    $customer_headers = [
        'Content-Type: text/html; charset=UTF-8'
    ];

    $customer_sent = wp_mail(
        $email,
        $customer_subject,
        $customer_message,
        $customer_headers
    );

    if (!$customer_sent) {
        error_log('GXF Guest Registration: Customer confirmation failed for ' . $email);
    }

    wp_send_json_success([
        'message' => 'Thank you! Your registration has been received.'
    ]);
}

add_action(
    'wp_ajax_gxf_guest_registration_send',
    'gxf_guest_registration_send'
);

add_action(
    'wp_ajax_nopriv_gxf_guest_registration_send',
    'gxf_guest_registration_send'
);
