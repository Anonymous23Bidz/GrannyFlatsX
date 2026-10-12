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
                            $input_id = $value;
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
                            $input_id = $value;
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
                            id="property-address"
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
                            $input_id = $value;
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
                            $input_id = $value;
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

    // Helper to read a collected field safely.
    $get_value = static function ($key) use ($data) {
        if (!isset($data[$key])) {
            return '';
        }

        return is_array($data[$key])
            ? implode(', ', $data[$key])
            : (string) $data[$key];
    };

    // Required contact details.
    $name  = $get_value('full_name');
    $phone = $get_value('mobile');
    $email = sanitize_email($get_value('email'));

    if (!$name || !$phone || !is_email($email)) {
        wp_send_json_error([
            'message' => 'Please enter your full name, mobile number and a valid email address.'
        ], 400);
    }

    // Check required project answers.
    $required_fields = [
        'bedrooms'        => 'bedroom selection',
        'price'           => 'budget',
        'property_address' => 'property address',
        'start_timeline'  => 'start timeline',
        'site_details'    => 'site details',
    ];

    foreach ($required_fields as $key => $label) {
        if (!$get_value($key)) {
            wp_send_json_error([
                'message' => 'Please complete the required field: ' . $label . '.'
            ], 400);
        }
    }

    // Optional attachment.
    $attachments  = [];
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
        'file'             => 'Attachment',
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


    foreach ($data as $key => $value) {
        $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $key));

        if (isset($value_labels[$key])) {
            $value = is_array($value) ? $value : [$value];

            $value = array_map(
                static fn($item) => $value_labels[$key][$item] ?? $item,
                $value
            );

            $value = implode(', ', $value);
        } elseif (is_array($value)) {
            $value = implode(', ', $value);
        }

        $message .= $label . ': ';
        $message .= $value !== '' ? $value : 'Not provided';
        $message .= "\n";
    }

    // $to      = 'lead.nextgengrannyflats@gmail.com';
    $to = 'christopher.bj.hidalgo@gmail.com';
    $subject = 'New Get a Quote Enquiry - ' . $name;

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    $sent = wp_mail(
        $to,
        $subject,
        $message,
        $headers,
        $attachments
    );

    if ($uploaded_path && file_exists($uploaded_path)) {
        wp_delete_file($uploaded_path);
    }

    if (!$sent) {
        error_log('Get a Quote: wp_mail() failed for ' . $email);

        wp_send_json_error([
            'message' => 'Your enquiry could not be sent. Please try again.'
        ], 500);
    }

    wp_send_json_success([
        'message' => 'Your quote request was sent successfully.'
    ]);
}

add_action('wp_ajax_get_quote_send', 'get_quote_send_email');
add_action('wp_ajax_nopriv_get_quote_send', 'get_quote_send_email');
