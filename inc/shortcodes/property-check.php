<?php

function property_check_form_shortcode()
{
    ob_start();

?>
    <form id="property-check-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('property_check_form', 'property_check_nonce'); ?>

        <!-- Hidden property data -->
        <input type="hidden" name="property_address" id="property-address">
        <input type="hidden" name="zoning_code" id="zoning-code">
        <input type="hidden" name="zoning_name" id="zoning-name">
        <input type="hidden" name="min_lot_size" id="min-lot-size">
        <input type="hidden" name="actual_lot_size" id="actual-lot-size">
        <input type="hidden" name="lga" id="property-lga">
        <input type="hidden" name="latitude" id="property-latitude">
        <input type="hidden" name="longitude" id="property-longitude">
        <input type="hidden" name="bushfire" id="property-bushfire">
        <input type="hidden" name="flood" id="property-flood">
        <input type="hidden" name="state_heritage" id="property-state-heritage">
        <input type="hidden" name="epi_heritage" id="property-epi-heritage">

        <div class="gfx-wrap">
            <div class="gfx-grid">
                <section class="gfx-panel-form">
                    <div class="gfx-bars" id="gfx-bars" aria-hidden="true">
                        <div class="bar-wrapper">
                            <label class="gfx-label on">1</label>
                            <div class="gfx-bar"></div>
                            <label class="gfx-label">2</label>
                            <div class="gfx-bar"></div>
                            <label class="gfx-label">3</label>
                            <div class="gfx-bar"></div>
                            <label class="gfx-label">4</label>
                        </div>
                    </div>

                    <!-- Step 0: Address -->
                    <div class="gfx-stp active" id="s0">

                        <div class="gfx-label">
                            <label for="suburbInput">
                                Your property address
                            </label>
                        </div>

                        <div class="input-icon-wrapper">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input
                                type="text"
                                id="suburbInput"
                                placeholder="2 Ganton Way, Luddenham NSW"
                                autocomplete="street-address" />
                        </div>

                        <p class="gfx-err" id="e0">
                            Please enter a property address.
                        </p>
                        <button
                            type="button"
                            class="gfx-btn gfx-btn-gold"
                            id="go1">
                            Check my property
                        </button>
                        <p class="gfx-fine">
                            Free &middot; No obligation
                        </p>
                    </div>

                    <!-- Step 1: Options -->
                    <div class="gfx-stp" id="s1">
                        <div class="gfx-label" id="lbl-own">
                            Who owns it?
                        </div>
                        <div class="gfx-chips" id="own" role="group" aria-labelledby="lbl-own"></div>
                        <div class="gfx-label" id="lbl-tim">
                            When would you start?
                        </div>

                        <div class="gfx-chips" id="tim" role="group" aria-labelledby="lbl-tim"></div>
                        <p class="gfx-err" id="e1">
                            Pick one from each
                        </p>

                        <div class="gfx-row">
                            <button type="button" class="gfx-btn gfx-btn-ghost" id="back0">
                                Back
                            </button>
                            <button type="button" class="gfx-btn gfx-btn-gold" id="run">
                                Run the check
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Live API Execution -->
                    <div class="gfx-stp" id="s2">
                        <div class="gfx-label">
                            Checking council records
                        </div>

                        <div id="propertyCheckStatus" aria-live="polite"></div>

                        <p id="suburbOutput" class="gfx-err"></p>

                        <div id="retryRow" style="display:none;">
                            <button type="button" id="retryBtn">Try again</button>
                        </div>


                        <div class="gfx-row" id="retryRow" style="display:none;">
                            <button type="button" class="gfx-btn gfx-btn-ghost" id="backToStart">
                                Start Over
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: API Results & Form -->
                    <div class="gfx-stp" id="s3">
                        <div class="gfx-result">
                            <div class="gfx-result-head">
                                <i class="ti ti-circle-check" aria-hidden="true"></i>
                                Your property may suit a granny flat
                            </div>

                            <div class="gfx-facts">
                                <div>
                                    <div class="gfx-fact-label"> Zoning</div>
                                    <div class="gfx-fact-value" id="res-zoning"> - </div>
                                </div>

                                <!-- <div>
                                    <div class="gfx-fact-label"> Lot size </div>
                                    <div class="gfx-fact-value" id="res-actual-lotsize"> - </div>
                                </div> -->

                                <div>
                                    <div class="gfx-fact-label"> Lot size </div>
                                    <div class="gfx-fact-value" id="res-lotsize"> - </div>
                                </div>
                                <div>
                                    <div class="gfx-fact-label">Bushfire</div>
                                    <div class="gfx-fact-value" id="res-bushfire"> - </div>
                                </div>
                            </div>

                            <div class="gfx-source">
                                Source: NSW Planning Portal
                            </div>

                        </div>

                        <div class="gfx-label">
                            Where do we send the full report?
                        </div>

                        <div class="gfx-field">
                            <input type="text" id="nm" name="name" placeholder="Full name" autocomplete="name">
                        </div>

                        <div class="gfx-field">
                            <input type="tel" id="ph" name="phone" placeholder="Mobile" autocomplete="tel">
                        </div>

                        <div class="gfx-field">
                            <input type="email" id="em" name="email" placeholder="Email" autocomplete="email">
                        </div>
                        <div class="gfx-field mt-3">
                            <div class="gfx-label">
                                Files for a detailed report (optional)
                            </div>
                            <input
                                type="file"
                                id="property-file"
                                name="property_file"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>

                        <p class="gfx-err" id="e3">
                            Fill in all three fields
                        </p>
                        <div class="gfx-row">
                            <button type="button" class="gfx-btn gfx-btn-ghost" id="back2">
                                Back
                            </button>
                            <button type="submit" class="gfx-btn gfx-btn-blue" id="send">
                                Send my report
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Final Confirmation -->
                    <div class="gfx-stp" id="s4">
                        <div class="gfx-done-wrap">
                            <div class="gfx-done-icon">
                                <i class="fa-regular fa-circle-check ti-circle-check"></i>
                            </div>
                            <div class="gfx-done-title">
                                Report on its way
                            </div>
                            <p class="gfx-done-body">
                                We'll get it touch with you.<br>
                                <a href="tel:1300950764"><strong>1300 950 764</strong></a>
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </form>

    <script>
        window.propertyCheckAjaxUrl = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
    </script>

<?php

    return ob_get_clean();
}

add_shortcode(
    'property_check_form',
    'property_check_form_shortcode'
);


/**
 * Send Property Check Report
 */
function property_check_send_report()
{

    $attachments = [];

    if (
        isset($_FILES['property_file']) &&
        !empty($_FILES['property_file']['name']) &&
        $_FILES['property_file']['error'] === UPLOAD_ERR_OK
    ) {
        $uploaded_file = $_FILES['property_file'];

        $allowed_types = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $file_type = wp_check_filetype_and_ext(
            $uploaded_file['tmp_name'],
            $uploaded_file['name']
        );

        if (
            empty($file_type['type']) ||
            !in_array($file_type['type'], $allowed_types, true)
        ) {
            wp_send_json_error([
                'message' => 'Invalid file type. Please upload a PDF, JPG, PNG, DOC or DOCX file.'
            ]);
        }

        // Maximum 10 MB
        if ($uploaded_file['size'] > 10 * 1024 * 1024) {
            wp_send_json_error([
                'message' => 'The file is too large. Maximum file size is 10 MB.'
            ]);
        }

        $upload_dir = wp_upload_dir();

        $filename = wp_unique_filename(
            $upload_dir['path'],
            sanitize_file_name($uploaded_file['name'])
        );

        $destination = trailingslashit($upload_dir['path']) . $filename;

        if (!move_uploaded_file($uploaded_file['tmp_name'], $destination)) {
            wp_send_json_error([
                'message' => 'The file could not be uploaded. Please try again.'
            ]);
        }

        $attachments[] = $destination;
    }
    if (
        !isset($_POST['property_check_nonce']) ||
        !wp_verify_nonce(
            $_POST['property_check_nonce'],
            'property_check_form'
        )
    ) {
        wp_send_json_error([
            'message' => 'Security check failed. Please refresh the page and try again.'
        ]);
    }

    // Customer options
    $owner = isset($_POST['owner'])
        ? sanitize_text_field(wp_unslash($_POST['owner']))
        : '';

    $timing = isset($_POST['timing'])
        ? sanitize_text_field(wp_unslash($_POST['timing']))
        : '';

    // Customer information
    $name = isset($_POST['name'])
        ? sanitize_text_field(wp_unslash($_POST['name']))
        : '';

    $phone = isset($_POST['phone'])
        ? sanitize_text_field(wp_unslash($_POST['phone']))
        : '';

    $email = isset($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email']))
        : '';

    // --Property information ----------------
    $property_address = isset($_POST['property_address'])
        ? sanitize_text_field(wp_unslash($_POST['property_address']))
        : '';

    $zoning_code = isset($_POST['zoning_code'])
        ? sanitize_text_field(wp_unslash($_POST['zoning_code']))
        : '';

    $lot_size = isset($_POST['actual_lot_size'])
        ? sanitize_text_field(wp_unslash($_POST['actual_lot_size']))
        : '';

    $min_lot_size = isset($_POST['min_lot_size'])
        ? sanitize_text_field(wp_unslash($_POST['min_lot_size']))
        : '';

    $lga = isset($_POST['lga'])
        ? sanitize_text_field(wp_unslash($_POST['lga']))
        : '';

    $bushfire = isset($_POST['bushfire'])
        ? sanitize_text_field(wp_unslash($_POST['bushfire']))
        : '';

    $flood = isset($_POST['flood'])
        ? sanitize_text_field(wp_unslash($_POST['flood']))
        : '';

    $state_heritage = isset($_POST['state_heritage'])
        ? sanitize_text_field(wp_unslash($_POST['state_heritage']))
        : '';

    $epi_heritage = isset($_POST['epi_heritage'])
        ? sanitize_text_field(wp_unslash($_POST['epi_heritage']))
        : '';

    if (!$name || !$phone || !$email) {
        wp_send_json_error([
            'message' => 'Please provide your name, phone number and email.'
        ]);
    }

    if (!is_email($email)) {
        wp_send_json_error([
            'message' => 'Please enter a valid email address.'
        ]);
    }

    if (!$property_address) {
        wp_send_json_error([
            'message' => 'Property address is missing. Please run the property check again.'
        ]);
    }

    $to = 'lead.nextgengrannyflats@gmail.com';
    $subject = 'New Property Check Report - ' . $property_address;

    // Build email
    $message = '';

    $message .= "PROPERTY CHECK REPORT\n\n";

    $message .= "CUSTOMER DETAILS\n";
    $message .= "Full Name: " . $name . "\n";
    $message .= "Phone Number: " . $phone . "\n";
    $message .= "Email Id: " . $email . "\n\n";
    $message .= "Ownership: " . ($owner ?: 'N/A') . "\n";
    $message .= "Start Timeline: " . ($timing ?: 'N/A') . "\n\n";

    $message .= "PROPERTY DETAILS\n";
    $message .= "Address: " . $property_address . "\n";
    $message .= "LGA: " . ($lga ?: 'N/A') . "\n\n";

    $message .= "PLANNING INFORMATION\n";
    $message .= "Zoning Code: " . ($zoning_code ?: 'N/A') . "\n";
    $message .= "Lot Size: " . ($min_lot_size ?: 'N/A') . "\n\n";
    // $message .= "Actual Lot Size: " . ($lot_size ?: 'N/A') . "\n";

    $message .= "HAZARDS\n";
    $message .= "Bushfire: " . ($bushfire ?: 'N/A') . "\n";
    $message .= "Flood: " . ($flood ?: 'N/A') . "\n\n";

    $message .= "HERITAGE\n";
    $message .= "State Heritage: " . ($state_heritage ?: 'No') . "\n";
    $message .= "EPI Heritage: " . ($epi_heritage ?: 'No') . "\n\n";

    $message .= "SOURCE\n";
    $message .= "NSW Planning Portal\n";

    $odoo_data = [
        'contact_name' => $name,
        'email_from'   => $email,
        'phone'        => $phone,
    ];

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

    if (!$sent) {
        error_log(
            'Property Check: wp_mail() failed for ' . $email
        );
        wp_send_json_error([
            'message' =>
            'The report could not be sent. Please try again or contact us directly.'
        ]);
    }

    wp_send_json_success([
        'message' =>
        'Your property report has been sent successfully.'
    ]);
}

/** Logged-in users */
add_action(
    'wp_ajax_property_check_send',
    'property_check_send_report'
);

/** Logged-out visitors */
add_action(
    'wp_ajax_nopriv_property_check_send',
    'property_check_send_report'
);
