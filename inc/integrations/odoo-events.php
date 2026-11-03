<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Odoo Events REST API endpoint.
 */
function gxf_register_odoo_event_endpoint()
{
    register_rest_route('gxf/v1', '/odoo-event', [
        'methods'             => 'POST',
        'callback'            => 'gxf_receive_odoo_event',
        'permission_callback' => 'gxf_verify_odoo_request',
    ]);
}
add_action('rest_api_init', 'gxf_register_odoo_event_endpoint');


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

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Odoo event received successfully.',
        'odoo_event_id' => $data['id'],
    ], 200);
}
