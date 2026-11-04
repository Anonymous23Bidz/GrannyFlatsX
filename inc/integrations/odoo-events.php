<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Odoo Events REST API endpoint.
 */
function gxf_register_odoo_event_endpoint()
{
    error_log('GXF ODOO REST ROUTE REGISTERING');

    register_rest_route('gxf/v1', '/odoo-event', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'gxf_receive_odoo_event',
        'permission_callback' => 'gxf_verify_odoo_request',
    ]);
}
add_action('rest_api_init', 'gxf_register_odoo_event_endpoint');


/**
 * Verify Odoo webhook authentication.
 */
function gxf_verify_odoo_request($request)
{
    $token = $request->get_header('x-gxf-token');

    $secret = defined('GXF_ODOO_WEBHOOK_SECRET')
        ? GXF_ODOO_WEBHOOK_SECRET
        : '';

    if (!$secret || !$token || !hash_equals($secret, $token)) {
        return new WP_Error(
            'unauthorized',
            'Invalid authentication token.',
            ['status' => 401]
        );
    }

    return true;
}


/**
 * Receive Odoo event and create/update WordPress event.
 */

function gxf_receive_odoo_event($request)
{
    $data = $request->get_json_params();

    if (empty($data['id']) || empty($data['name'])) {
        return new WP_Error(
            'invalid_event',
            'Event ID and name are required.',
            ['status' => 400]
        );
    }

    if (!post_type_exists('event')) {
        return new WP_Error(
            'missing_post_type',
            'Event custom post type is not registered.',
            ['status' => 500]
        );
    }

    if (!function_exists('update_field')) {
        return new WP_Error(
            'acf_missing',
            'Advanced Custom Fields is not active.',
            ['status' => 500]
        );
    }

    $odoo_id = sanitize_text_field((string) $data['id']);

    // Find existing event using Odoo ID.
    $existing = get_posts([
        'post_type'      => 'event',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'meta_key'       => '_odoo_event_id',
        'meta_value'     => $odoo_id,
        'fields'         => 'ids',
    ]);

    // Create or update WordPress event.
    $post_data = [
        'post_type'    => 'event',
        'post_title'   => sanitize_text_field($data['name']),
        'post_status'  => 'publish',
    ];

    if (!empty($existing)) {
        $post_data['ID'] = $existing[0];
        $post_id = wp_update_post($post_data, true);
        $action = 'updated';
    } else {
        $post_id = wp_insert_post($post_data, true);
        $action = 'created';
    }

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    // Format Odoo dates.
    $start = !empty($data['date_begin'])
        ? date_create_from_format(
            '!Y-m-d H:i:s',
            $data['date_begin'],
            wp_timezone()
        )
        : false;

    $end = !empty($data['date_end'])
        ? date_create_from_format(
            '!Y-m-d H:i:s',
            $data['date_end'],
            wp_timezone()
        )
        : false;

    // Save Odoo ID for duplicate checking.
    update_post_meta($post_id, '_odoo_event_id', $odoo_id);

    // Save Odoo ID to ACF.
    update_field('odoo_event_id', $odoo_id, $post_id);

    // ACF Date Picker expects Ymd storage format.
    if ($start) {
        update_field(
            'event_date',
            $start->format('Ymd'),
            $post_id
        );

        update_field(
            'event_start_time',
            $start->format('H:i:s'),
            $post_id
        );
    }

    // ACF Time Picker.
    if ($end) {
        update_field(
            'event_end_time',
            $end->format('H:i:s'),
            $post_id
        );
    }

    // Save address.
    update_field(
        'event_address',
        sanitize_text_field($data['address'] ?? ''),
        $post_id
    );

    // Save or update featured image.
    if (!empty($data['featured_image'])) {

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $image_url = esc_url_raw($data['featured_image']);

        $image_id = attachment_url_to_postid($image_url);

        if (!$image_id) {
            $image_id = media_sideload_image(
                $image_url,
                $post_id,
                get_the_title($post_id),
                'id'
            );
        }

        if (!is_wp_error($image_id) && $image_id) {
            set_post_thumbnail($post_id, $image_id);
        } else {
            error_log(
                'GXF ODOO: Featured image upload failed: ' .
                    (is_wp_error($image_id) ? $image_id->get_error_message() : 'Unknown error')
            );
        }
    }

    // Save description.
    update_field(
        'event_description',
        wp_kses_post($data['description'] ?? ''),
        $post_id
    );

    // Assign event taxonomy.
    if (!empty($data['event_type']) && taxonomy_exists('event_type')) {

        $event_type = sanitize_title($data['event_type']);

        $term = get_term_by(
            'slug',
            $event_type,
            'event_type'
        );

        if ($term && !is_wp_error($term)) {
            wp_set_object_terms(
                $post_id,
                [(int) $term->term_id],
                'event_type'
            );
        } else {
            error_log('GXF ODOO: Event type not found: ' . $event_type);
        }
    }

    error_log('GXF ODOO: Event ' . $action . '. Post ID: ' . $post_id);

    return new WP_REST_Response([
        'success'       => true,
        'message'       => $action === 'created'
            ? 'Event "' . get_the_title($post_id) . '" has been created and published successfully on WordPress.'
            : 'Event "' . get_the_title($post_id) . '" has been updated successfully on WordPress.',
        'post_id'       => $post_id,
        'post_title'    => get_the_title($post_id),
        'odoo_event_id' => $odoo_id,
        'action'        => $action,
        'status'        => get_post_status($post_id),
    ], 200);
}
