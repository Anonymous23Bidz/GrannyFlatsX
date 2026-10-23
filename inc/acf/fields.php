<?php

add_shortcode('get_acf', function ($atts) {

    $atts = shortcode_atts([
        'field'   => '',
        'post_id' => null,
    ], $atts, 'get_acf');

    if (empty($atts['field'])) {
        return '';
    }

    $keys = explode('.', $atts['field']);

    // Fetch top-level field from ACF
    $top_key = array_shift($keys);
    $data    = get_field($top_key, $atts['post_id']);

    foreach ($keys as $key) {

        if (is_array($data) && isset($data[$key])) {

            $data = $data[$key];
        } elseif (is_array($data) && is_numeric($key) && isset($data[(int) $key])) {

            $data = $data[(int) $key];
        } else {

            return '';
        }
    }

    if (is_array($data) || is_object($data)) {
        return esc_html(wp_json_encode($data));
    }

    if ($data === null || $data === '') {
        return '';
    }

    if (is_string($data) && $data !== strip_tags($data)) {
        return wp_kses_post($data);
    }

    // Normal text fields
    return esc_html($data);
});


add_shortcode('acf_calculate', function ($atts) {
    $atts = shortcode_atts([
        'width'     => '',
        'length'    => '',
        'operation' => 'multiply',
        'divisor'   => '1',
        'decimals'  => '2',
        'suffix'    => '',
        'post_id'   => get_the_ID(),
    ], $atts, 'acf_calculate');

    if (!$atts['width'] || !$atts['length']) {
        return '';
    }

    $get_value = function ($path) use ($atts) {
        $keys = explode('.', $path);
        $value = get_field(array_shift($keys), $atts['post_id']);

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }

            $value = $value[$key];
        }

        return is_numeric($value) ? (float) $value : null;
    };

    $a = $get_value($atts['width']);
    $b = $get_value($atts['length']);
    $divisor = is_numeric($atts['divisor']) ? (float) $atts['divisor'] : 1;

    if ($a === null || $b === null || $divisor == 0) {
        return '';
    }

    switch ($atts['operation']) {
        case 'add':
            $result = $a + $b;
            break;
        case 'subtract':
            $result = $a - $b;
            break;
        case 'divide':
            if ($b == 0) {
                return '';
            }
            $result = $a / $b;
            break;
        case 'multiply':
        default:
            $result = $a * $b;
            break;
    }

    $result /= $divisor;

    return esc_html(number_format(
        $result,
        max(0, min(6, (int) $atts['decimals']))
    ) . $atts['suffix']);
});


add_shortcode('render_kuula_iframe', function () {
    $url = get_field('kuula_link');

    if (! $url) {
        return '';
    }

    if (strpos($url, '<iframe') !== false) {
        return $url;
    }

    return sprintf(
        '<iframe width="100%%" height="640" frameborder="0" allow="xr-spatial-tracking; gyroscope; accelerometer" allowfullscreen scrolling="no" src="%s"></iframe>',
        esc_url($url)
    );
});
