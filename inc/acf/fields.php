<?php

add_shortcode('get_acf', function ($atts) {

    $atts = shortcode_atts([
        'field'   => '',
        'post_id' => null,
    ], $atts, 'get_acf');

    if (empty($atts['field'])) {
        return '';
    }

    // Split dot-separated string into keys
    $keys = explode('.', $atts['field']);

    // Fetch top-level field from ACF
    $top_key = array_shift($keys);
    $data    = get_field($top_key, $atts['post_id']);

    // Traverse deeply through nested arrays
    foreach ($keys as $key) {

        if (is_array($data) && isset($data[$key])) {

            $data = $data[$key];
        } elseif (is_array($data) && is_numeric($key) && isset($data[(int) $key])) {

            $data = $data[(int) $key];
        } else {

            return '';
        }
    }

    // Return arrays/objects as JSON
    if (is_array($data) || is_object($data)) {
        return esc_html(wp_json_encode($data));
    }

    // Empty value
    if ($data === null || $data === '') {
        return '';
    }

    /*
     * If the value contains HTML from a WYSIWYG field,
     * allow safe HTML so paragraphs, line breaks, lists, etc. work.
     */
    if (is_string($data) && $data !== strip_tags($data)) {
        return wp_kses_post($data);
    }

    // Normal text fields
    return esc_html($data);
});
