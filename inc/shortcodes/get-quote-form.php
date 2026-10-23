<?php

function get_quote_form_shortcode()
{
    static $instance = 0;
    $instance++;

    $id = 'get-quote-form-' . $instance;

    ob_start();
?>

    <form
        id="<?php echo esc_attr($id); ?>"
        class="get-quote-form"
        method="post"
        enctype="multipart/form-data"
        data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
        novalidate>

        <input type="hidden" name="action" value="get_quote_send">
        <?php wp_nonce_field('get_quote_form', 'get_quote_nonce'); ?>
        <input type="hidden" name="form_name" value="Get a Quote">

        <!-- Progress -->
        <div class="progress-wrapper">
            <div class="gqf-progress">
                <div class="gqf-progress-bar"></div>
            </div>

            <div class="gqf-progress-label">
                Step <span class="gqf-current-step">1</span> of 5
            </div>
        </div>

        <!-- Step 1 -->
        <section
            class="gqf-step is-active"
            data-step="1"
            data-back-label="Cancel"
            data-next-label="Next">
            <div class="gqf-content">
                <div class="get-quote-form-wrapper">

                    <h5 class="gqf-sub-heading">
                        Lets start with your granny flat
                    </h5>

                    <p class="gqf-question">How many bedrooms?</p>

                    <div class="answer-wrapper">

                        <?php
                        $bedroom_options = [
                            'not_sure'  => 'Not sure yet',
                            '1_bedroom' => '1 Bedroom',
                            '2_bedroom' => '2 Bedroom',
                            '3_bedroom' => '3 Bedroom',
                        ];

                        foreach ($bedroom_options as $value => $label):
                            $input_id = $id . '-' . $value;
                        ?>

                            <div class="gqf-option">
                                <input
                                    type="radio"
                                    id="<?php echo esc_attr($input_id); ?>"
                                    name="bedrooms"
                                    value="<?php echo esc_attr($value); ?>"
                                    class="gqf-radio-input"
                                    required>

                                <label
                                    for="<?php echo esc_attr($input_id); ?>"
                                    class="gqf-radio-label">
                                    <?php echo esc_html($label); ?>
                                </label>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </section>

        <!-- Step 2 -->
        <section
            class="gqf-step"
            data-step="2"
            data-back-label="Back"
            data-next-label="Next Your Property">
            <div class="gqf-content">
                <div class="get-quote-form-wrapper">

                    <h5 class="gqf-sub-heading">
                        What budget are you working with?
                    </h5>

                    <p class="gqf-question">Not sure? Choose the closest range. We'll work through the options with you.</p>

                    <div class="answer-wrapper">

                        <?php
                        $price_range = [
                            'option_1'  => '$120k - $160k',
                            'option_2' => '$160-$225k',
                            'option_3' => '$225k-$250k',
                            'option_4' => '$250k+',
                        ];

                        foreach ($price_range as $value => $label):
                            $input_id = $id . '-' . $value;
                        ?>

                            <div class="gqf-option">
                                <input
                                    type="radio"
                                    id="<?php echo esc_attr($input_id); ?>"
                                    name="price"
                                    value="<?php echo esc_attr($value); ?>"
                                    class="gqf-radio-input"
                                    required>

                                <label
                                    for="<?php echo esc_attr($input_id); ?>"
                                    class="gqf-radio-label">
                                    <?php echo esc_html($label); ?>
                                </label>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </section>

        <!-- Step 3 -->
        <section
            class="gqf-step"
            data-step="3"
            data-back-label="Back"
            data-next-label="Next Your Site">
            <div class="gqf-content">
                <div class="get-quote-form-wrapper">

                    <h5 class="gqf-sub-heading">
                        Tell us about your property
                    </h5>

                    <p class="gqf-question">Property address:</p>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <input
                            type="text"
                            id="<?php echo esc_attr($id . '-property-address'); ?>"
                            name="property_address"
                            placeholder="Start typing your address..."
                            autocomplete="off"
                            class="gqf-input"
                            required>
                    </div>

                    <p class="gqf-question">When are you looking to start?</p>

                    <div class="answer-wrapper">
                        <?php
                        $date_range = [
                            'range_1'  => 'ASAP',
                            'range_2' => '1-3 months',
                            'range_3' => '3-6 months',
                            'range_4' => 'Just researching',
                        ];

                        foreach ($date_range as $value => $label):
                            $input_id = $id . '-' . $value;
                        ?>

                            <div class="gqf-option">
                                <input
                                    type="radio"
                                    id="<?php echo esc_attr($input_id); ?>"
                                    name="start_timeline"
                                    value="<?php echo esc_attr($value); ?>"
                                    class="gqf-radio-input"
                                    required>

                                <label
                                    for="<?php echo esc_attr($input_id); ?>"
                                    class="gqf-radio-label">
                                    <?php echo esc_html($label); ?>
                                </label>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </section>

        <!-- Step 4 -->
        <section
            class="gqf-step"
            data-step="4"
            data-back-label="Back"
            data-next-label="Next Your Details">
            <div class="gqf-content">
                <div class="get-quote-form-wrapper">

                    <h5 class="gqf-sub-heading">
                        Help us understand your site
                    </h5>

                    <p class="gqf-question">Anything we should know?</p>

                    <div class="answer-wrapper">
                        <?php
                        $suggestion = [
                            'suggestion_1'  => 'Not sure',
                            'suggestion_2' => 'Clear backyard',
                            'suggestion_3' => 'Existing structure',
                            'suggestion_4' => 'Sloping block',
                        ];

                        foreach ($suggestion as $value => $label):
                            $input_id = $id . '-' . $value;
                        ?>

                            <div class="gqf-option">
                                <input
                                    type="radio"
                                    id="<?php echo esc_attr($input_id); ?>"
                                    name="site_details"
                                    value="<?php echo esc_attr($value); ?>"
                                    class="gqf-radio-input"
                                    required>

                                <label
                                    for="<?php echo esc_attr($input_id); ?>"
                                    class="gqf-radio-label">
                                    <?php echo esc_html($label); ?>
                                </label>
                            </div>

                        <?php endforeach; ?>

                    </div>
                    <div class="file-input-wrapper">
                        <p class="gqf-question">Files for a detailed quote (Optional) </p>
                        <div class="input-icon-wrapper">
                            <input
                                type="file"
                                name="file"
                                class="gqf-attached-file">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Step 5 -->
        <section
            class="gqf-step"
            data-step="5"
            data-back-label="Back"
            data-next-label="Get My Free Quote">
            <div class="gqf-content">
                <div class="get-quote-form-wrapper">

                    <h5 class="gqf-sub-heading">
                        Where should we send your quote?
                    </h5>

                    <!-- Full Name -->
                    <div class="input-icon-wrapper">
                        <i class="fas fa-user input-icon" aria-hidden="true"></i>
                        <input
                            type="text"
                            id="gqf-full-name"
                            name="full_name"
                            placeholder="Full name"
                            autocomplete="name"
                            class="gqf-input"
                            required>
                    </div>

                    <!-- Mobile -->
                    <div class="input-icon-wrapper">
                        <i class="fas fa-phone input-icon" aria-hidden="true"></i>
                        <input
                            type="tel"
                            id="gqf-mobile"
                            name="mobile"
                            placeholder="Mobile number"
                            autocomplete="tel"
                            inputmode="tel"
                            class="gqf-input"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="input-icon-wrapper">
                        <i class="fas fa-envelope input-icon" aria-hidden="true"></i>
                        <input
                            type="email"
                            id="gqf-email"
                            name="email"
                            placeholder="Email address"
                            autocomplete="email"
                            class="gqf-input"
                            required>
                    </div>

                </div>
            </div>
        </section>

        <!-- Navigation -->
        <div class="gqf-navigation">
            <button type="button" class="btn-outline gqf-back">
                Cancel
            </button>

            <button type="button" class="btn-primary gqf-next">
                Next
            </button>

            <button type="submit" class="btn-primary gqf-submit" hidden>
                Get My Free Quote
            </button>
        </div>

    </form>

<?php
    return ob_get_clean();
}

add_shortcode('get_quote_form', 'get_quote_form_shortcode');


function get_quote_send_email()
{
    $nonce = isset($_POST['get_quote_nonce'])
        ? sanitize_text_field(wp_unslash($_POST['get_quote_nonce']))
        : '';

    if (!$nonce || !wp_verify_nonce($nonce, 'get_quote_form')) {
        wp_send_json_error([
            'message' => 'Your session has expired. Refresh the page and try again.'
        ], 403);
    }

    $excluded_fields = [
        'action',
        'get_quote_nonce',
        '_wp_http_referer',
    ];

    $data = [];

    foreach ($_POST as $key => $value) {
        if (in_array($key, $excluded_fields, true)) {
            continue;
        }

        $key = sanitize_key($key);

        if (is_array($value)) {
            $data[$key] = array_map(
                static function ($item) {
                    return is_scalar($item)
                        ? sanitize_text_field(wp_unslash((string) $item))
                        : '';
                },
                $value
            );
        } elseif (is_scalar($value)) {
            $data[$key] = sanitize_text_field(wp_unslash((string) $value));
        }
    }

    $get_value = static function ($key) use ($data) {
        if (!isset($data[$key])) {
            return '';
        }

        return is_array($data[$key])
            ? implode(', ', $data[$key])
            : (string) $data[$key];
    };

    $name  = $get_value('full_name');
    $phone = $get_value('mobile');
    $email = sanitize_email($get_value('email'));

    if (!$name || !$phone || !is_email($email)) {
        wp_send_json_error([
            'message' => 'Please enter your full name, mobile number and a valid email address.'
        ], 400);
    }

    $required_fields = [
        'bedrooms'         => 'bedroom selection',
        'price'            => 'budget',
        'property_address' => 'property address',
        'start_timeline'   => 'start timeline',
        'site_details'     => 'site details',
    ];

    foreach ($required_fields as $key => $label) {
        if (!$get_value($key)) {
            wp_send_json_error([
                'message' => 'Please complete the required field: ' . $label . '.'
            ], 400);
        }
    }

    // Handle optional attachment.
    $attachments   = [];
    $uploaded_path = '';

    if (
        isset($_FILES['file']) &&
        !empty($_FILES['file']['name']) &&
        (int) $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES['file'];

        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error([
                'message' => 'The attachment could not be uploaded. Please try again.'
            ], 400);
        }

        if ((int) $file['size'] > 10 * 1024 * 1024) {
            wp_send_json_error([
                'message' => 'The attachment must be 10 MB or smaller.'
            ], 400);
        }

        $allowed_mimes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        require_once ABSPATH . 'wp-admin/includes/file.php';

        $check = wp_check_filetype_and_ext(
            $file['tmp_name'],
            $file['name'],
            $allowed_mimes
        );

        if (
            empty($check['ext']) ||
            empty($check['type']) ||
            !isset($allowed_mimes[strtolower($check['ext'])]) ||
            $allowed_mimes[strtolower($check['ext'])] !== $check['type']
        ) {
            wp_send_json_error([
                'message' => 'Please upload a PDF, JPG, PNG, DOC or DOCX file.'
            ], 400);
        }

        $upload = wp_handle_upload($file, [
            'test_form' => false,
            'mimes'     => $allowed_mimes,
        ]);

        if (!empty($upload['error']) || empty($upload['file'])) {
            wp_send_json_error([
                'message' => 'The attachment could not be saved. Please try again.'
            ], 400);
        }

        $uploaded_path = $upload['file'];
        $attachments[] = $uploaded_path;
    }

    // Human-readable field labels.
    $labels = [
        'form_name'        => 'Form',
        'bedrooms'         => 'Bedrooms',
        'price'            => 'Budget Range',
        'property_address' => 'Property Address',
        'start_timeline'   => 'Start Timeline',
        'site_details'     => 'Site Details',
        'full_name'        => 'Full Name',
        'mobile'           => 'Mobile',
        'email'            => 'Email',
    ];

    $value_labels = [
        'bedrooms' => [
            'not_sure'  => 'Not sure yet',
            '1_bedroom' => '1 Bedroom',
            '2_bedroom' => '2 Bedroom',
            '3_bedroom' => '3 Bedroom',
        ],
        'price' => [
            'option_1' => '$120k - $160k',
            'option_2' => '$160k - $225k',
            'option_3' => '$225k - $250k',
            'option_4' => '$250k+',
        ],
        'start_timeline' => [
            'range_1' => 'ASAP',
            'range_2' => '1-3 months',
            'range_3' => '3-6 months',
            'range_4' => 'Just researching',
        ],
        'site_details' => [
            'suggestion_1' => 'Not sure',
            'suggestion_2' => 'Clear backyard',
            'suggestion_3' => 'Existing structure',
            'suggestion_4' => 'Sloping block',
        ],
    ];

    // Build the lead notification email.
    $message  = "NEW GET A QUOTE ENQUIRY\n\n";
    $message .= "CUSTOMER DETAILS\n";
    $message .= "Full Name: " . $name . "\n";
    $message .= "Mobile: " . $phone . "\n";
    $message .= "Email: " . $email . "\n\n";
    $message .= "PROJECT DETAILS\n";

    foreach ($labels as $key => $label) {
        if (in_array($key, ['full_name', 'mobile', 'email'], true)) {
            continue;
        }

        $value = $get_value($key);

        if (isset($value_labels[$key])) {
            $value = $value_labels[$key][$value] ?? $value;
        }

        $message .= $label . ': ' . ($value !== '' ? $value : 'Not provided') . "\n";
    }

    $message .= "\nATTACHMENT\n";
    $message .= $uploaded_path
        ? sanitize_file_name(basename($uploaded_path))
        : 'No attachment provided';

    $to      = 'lead.nextgengrannyflats@gmail.com';
    $subject = 'New Get a Quote Enquiry - ' . $name;

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    // Send the lead notification.
    $sent = wp_mail(
        $to,
        $subject,
        $message,
        $headers,
        $attachments
    );

    // The uploaded file is no longer needed after the lead email attempt.
    if ($uploaded_path && file_exists($uploaded_path)) {
        wp_delete_file($uploaded_path);
    }

    if (!$sent) {
        error_log('Get a Quote: Lead email failed for ' . $email);

        wp_send_json_error([
            'message' => 'Your enquiry could not be sent. Please try again.'
        ], 500);
    }

    // Send a separate HTML confirmation email to the customer.
    $customer_subject = 'Thank You for Your Enquiry - Granny Flats X';

    $customer_message = '
    <!DOCTYPE html>
    <html>
    <body style="margin:0;padding:24px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#333;">
        <div style="max-width:700px;margin:auto;">
            <p>Hi ' . esc_html($name) . ',</p>

            <p>Thank you for reaching out to Granny Flats X!</p>

            <p>We have received your granny flat quote request, and our team will be in touch to discuss your project and property.</p>

            <h3 style="color:#26324b;">Your Enquiry Summary</h3>

            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #eee;"><strong>Bedrooms</strong></td>
                    <td style="padding:8px;border-bottom:1px solid #eee;">' . esc_html($value_labels['bedrooms'][$get_value('bedrooms')] ?? $get_value('bedrooms')) . '</td>
                </tr>
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #eee;"><strong>Budget</strong></td>
                    <td style="padding:8px;border-bottom:1px solid #eee;">' . esc_html($value_labels['price'][$get_value('price')] ?? $get_value('price')) . '</td>
                </tr>
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #eee;"><strong>Property Address</strong></td>
                    <td style="padding:8px;border-bottom:1px solid #eee;">' . esc_html($get_value('property_address')) . '</td>
                </tr>
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #eee;"><strong>Start Timeline</strong></td>
                    <td style="padding:8px;border-bottom:1px solid #eee;">' . esc_html($value_labels['start_timeline'][$get_value('start_timeline')] ?? $get_value('start_timeline')) . '</td>
                </tr>
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #eee;"><strong>Site Details</strong></td>
                    <td style="padding:8px;border-bottom:1px solid #eee;">' . esc_html($value_labels['site_details'][$get_value('site_details')] ?? $get_value('site_details')) . '</td>
                </tr>
            </table>

            <h3 style="color:#26324b;">Why Choose Granny Flats X?</h3>

            <ul>
                <li><strong>Decades of experience.</strong> We are an established granny flat builder with a proven track record.</li>
                <li><strong>Everything handled.</strong> We manage the process from approvals through to completion.</li>
                <li><strong>Quality finishes.</strong> Choose from quality materials, fittings and optional upgrades.</li>
                <li><strong>Customised designs.</strong> We personalise your design and layout to suit your property and lifestyle.</li>
                <li><strong>No hidden costs.</strong> We explain the inclusions and costs before you commit.</li>
            </ul>

            <p>We look forward to helping you explore the options for your granny flat.</p>

            <p><strong>Want to learn more?</strong></p>

            <p><a href="https://grannyflatsx.com.au/wp-content/uploads/2026/07/GFX-Handbook-2026-Final1.pdf" style="color:#b58b25;">Read the Granny Flats X Client Handbook</a></p>

            <p>If you have any questions, simply reply to this email or call us on
            <a href="tel:0291917415">(02) 9191 7415</a> or
            <a href="tel:1300950764">1300 950 764</a>.</p>

            <p>Thank you again for considering Granny Flats X. We look forward to speaking with you soon.</p>

            <p>
                Kind regards,<br>
                <strong>Mark I.</strong><br>
                Business Development Associate<br>
                (02) 9191 7415 | 1300 950 764
            </p>

            <p style="color:#b58b25;font-weight:bold;">Price Beat Guarantee | Highest Quality | No Hidden Costs</p>

            <p><a href="https://www.grannyflatsx.com.au">www.GrannyFlatsX.com.au</a></p>

            <p style="font-size:12px;color:#777;">
                Granny Flats X is the legal business name of Bluewood Homes NSW Pty. Ltd.<br>
                ABN: 54 647 631 043 | Builder Licence #: 366254C
            </p>
        </div>
    </body>
    </html>';

    $customer_headers = [
        'Content-Type: text/html; charset=UTF-8',
    ];

    $customer_sent = wp_mail(
        $email,
        $customer_subject,
        $customer_message,
        $customer_headers
    );

    if (!$customer_sent) {
        error_log('Get a Quote: Customer confirmation email failed for ' . $email);
    }

    wp_send_json_success([
        'message' => 'Your quote request was sent successfully.'
    ]);
}

add_action('wp_ajax_get_quote_send', 'get_quote_send_email');
add_action('wp_ajax_nopriv_get_quote_send', 'get_quote_send_email');
