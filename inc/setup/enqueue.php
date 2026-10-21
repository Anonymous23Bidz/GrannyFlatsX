<?php

/**
 * Enqueue child theme assets.
 *
 * @package Hello_Elementor_Child
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load main.js as a native ES module.
 */
function hello_child_module_script_tag($tag, $handle, $src)
{
    if ($handle !== 'hello-child-main-js') {
        return $tag;
    }

    return '<script type="module" data-nowprocket src="' . esc_url($src) . '" id="hello-child-main-js-js"></script>';
}
add_filter('script_loader_tag', 'hello_child_module_script_tag', 10, 3);

/**
 * Check whether Elementor editor mode is active.
 */
function hello_child_is_elementor_edit_mode()
{
    if (!did_action('elementor/loaded') || !class_exists('\Elementor\Plugin')) {
        return false;
    }

    $elementor = \Elementor\Plugin::$instance;

    return isset($elementor->editor) && $elementor->editor->is_edit_mode();
}

/**
 * Enqueue child theme assets.
 */
function hello_child_enqueue_assets()
{
    $theme_uri  = get_stylesheet_directory_uri();
    $theme_path = get_stylesheet_directory();
    $is_elementor_editor = hello_child_is_elementor_edit_mode();

    // Google Fonts.
    wp_enqueue_style(
        'google-fonts-montserrat',
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap',
        [],
        null
    );

    // Font Awesome.
    wp_enqueue_style(
        'font-awesome-6',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        [],
        '6.5.2'
    );

    // Child theme stylesheet.
    $style_css = $theme_path . '/style.css';

    if (file_exists($style_css)) {
        wp_enqueue_style(
            'hello-child-style',
            $theme_uri . '/style.css',
            [
                'hello-elementor',
                'hello-elementor-theme-style',
                'hello-elementor-header-footer',
            ],
            filemtime($style_css)
        );
    }

    // Main CSS.
    $main_css = $theme_path . '/assets/css/main.css';

    if (file_exists($main_css)) {
        wp_enqueue_style(
            'hello-child-main',
            $theme_uri . '/assets/css/main.css',
            ['hello-child-style'],
            filemtime($main_css)
        );
    }

    // Use the bundled/minified module after `npm run build`; source remains a
    // development fallback so local work does not break before the first build.
    $built_main_js = $theme_path . '/assets/dist/main.js';
    $source_main_js = $theme_path . '/assets/js/main.js';
    $main_js = file_exists($built_main_js) ? $built_main_js : $source_main_js;
    $main_js_uri = file_exists($built_main_js)
        ? $theme_uri . '/assets/dist/main.js'
        : $theme_uri . '/assets/js/main.js';

    if (!$is_elementor_editor && file_exists($main_js)) {
        wp_enqueue_script(
            'hello-child-main-js',
            $main_js_uri,
            [],
            filemtime($main_js),
            true
        );

        // Property check AJAX configuration.
        wp_localize_script(
            'hello-child-main-js',
            'propertyCheckAjax',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
            ]
        );

        // Floor plan AJAX configuration.
        wp_localize_script(
            'hello-child-main-js',
            'floorPlanFilter',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('floor_plan_filter_nonce'),
            ]
        );
    }

    $google_maps_api_key = defined('GOOGLE_MAPS_API_KEY')
        ? GOOGLE_MAPS_API_KEY
        : getenv('GOOGLE_MAPS_API_KEY');

    if (!$is_elementor_editor && !empty($google_maps_api_key)) {
        // Do not download Google Maps on every page. The address-field script
        // loads this URL only after a visitor focuses an address input.
        $google_maps_url = add_query_arg(
            [
                'key'       => $google_maps_api_key,
                'libraries' => 'places',
                'loading'   => 'async',
            ],
            'https://maps.googleapis.com/maps/api/js'
        );

        wp_localize_script(
            'hello-child-main-js',
            'helloChildMaps',
            [
                'url' => $google_maps_url,
            ]
        );
    }
}
add_action('wp_enqueue_scripts', 'hello_child_enqueue_assets');
