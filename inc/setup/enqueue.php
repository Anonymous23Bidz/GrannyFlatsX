
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
 * Load main.js as an ES module.
 */
function hello_child_module_script_tag($tag, $handle, $src)
{
    if ($handle !== 'hello-child-main-js') {
        return $tag;
    }

    return '<script type="module" src="' . esc_url($src) . '" id="hello-child-main-js-js"></script>';
}
add_filter('script_loader_tag', 'hello_child_module_script_tag', 10, 3);

/**
 * Enqueue child theme assets.
 */
function hello_child_enqueue_assets()
{
    $theme_uri  = get_stylesheet_directory_uri();
    $theme_path = get_stylesheet_directory();

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

    // Main JavaScript ES module.
    $main_js = $theme_path . '/assets/js/main.js';

    if (file_exists($main_js)) {
        wp_enqueue_script(
            'hello-child-main-js',
            $theme_uri . '/assets/js/main.js',
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

    // Google Maps Places.
    wp_enqueue_script(
        'google-maps-places',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyDOgoIk4iUqfBL2SVmVOWe75MooDpMz-AM&libraries=places',
        [],
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'hello_child_enqueue_assets');
