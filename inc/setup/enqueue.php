<?php

/**
 * Enqueue child theme assets.
 *
 * @package Hello_Elementor_Child
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue child theme CSS and JS.
 */
function hello_child_enqueue_assets()
{

    $theme_uri  = get_stylesheet_directory_uri();
    $theme_path = get_stylesheet_directory();

    /**
     * ---------------------------------------------------------
     * Google Fonts - Montserrat
     * ---------------------------------------------------------
     */
    wp_enqueue_style(
        'google-fonts-montserrat',
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap',
        array(),
        null
    );

    /**
     * ---------------------------------------------------------
     * Font Awesome
     * ---------------------------------------------------------
     */

    wp_enqueue_style(
        'font-awesome-6',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        array(),
        '6.5.2'
    );


    /**
     * ---------------------------------------------------------
     * Child Theme Stylesheet
     * ---------------------------------------------------------
     */
    $style_css = $theme_path . '/style.css';

    if (file_exists($style_css)) {
        wp_enqueue_style(
            'hello-child-style',
            $theme_uri . '/style.css',
            array(
                'hello-elementor',
                'hello-elementor-theme-style',
                'hello-elementor-header-footer',
            ),
            filemtime($style_css)
        );
    }

    /**
     * ---------------------------------------------------------
     * Main CSS
     *
     * main.css imports the component/page CSS files.
     * ---------------------------------------------------------
     */
    $main_css = $theme_path . '/assets/css/main.css';

    if (file_exists($main_css)) {
        wp_enqueue_style(
            'hello-child-main',
            $theme_uri . '/assets/css/main.css',
            array('hello-child-style'),
            filemtime($main_css)
        );
    }

    /**
     * ---------------------------------------------------------
     * Main JavaScript
     * ---------------------------------------------------------
     */
    $main_js = $theme_path . '/assets/js/main.js';

    if (file_exists($main_js)) {
        wp_enqueue_script(
            'hello-child-main-js',
            $theme_uri . '/assets/js/main.js',
            array('jquery'),
            filemtime($main_js),
            true
        );
    }

    /**
     * ---------------------------------------------------------
     * Suburb Filter JavaScript
     * ---------------------------------------------------------
     */
    $suburb_filter_js = $theme_path . '/assets/js/components/suburb-filter.js';

    if (file_exists($suburb_filter_js)) {
        wp_enqueue_script(
            'hello-child-suburb-filter',
            $theme_uri . '/assets/js/components/suburb-filter.js',
            array('jquery'),
            filemtime($suburb_filter_js),
            true
        );
    }

    /**
     * ---------------------------------------------------------
     * Accordion JavaScript
     * ---------------------------------------------------------
     */
    $accordion_js = $theme_path . '/assets/js/components/accordion.js';

    if (file_exists($accordion_js)) {
        wp_enqueue_script(
            'hello-child-accordion',
            $theme_uri . '/assets/js/components/accordion.js',
            array('jquery'),
            filemtime($accordion_js),
            true
        );
    }

    /**
     * ---------------------------------------------------------
     * Bedroom Filter JavaScript
     * ---------------------------------------------------------
     */
    $bedroom_js = $theme_path . '/assets/js/components/bedroom-filter.js';

    if (file_exists($bedroom_js)) {
        wp_enqueue_script(
            'hello-child-bedroom',
            $theme_uri . '/assets/js/components/bedroom-filter.js',
            array('jquery'),
            filemtime($bedroom_js),
            true
        );
    }

    /**
     * ---------------------------------------------------------
     * Property Check Form JavaScript
     * ---------------------------------------------------------
     */
    $property_check_js = $theme_path . '/assets/js/components/property-check.js';

    if (file_exists($property_check_js)) {
        wp_enqueue_script(
            'property-check-form',
            $theme_uri . '/assets/js/components/property-check.js',
            array('jquery'),
            filemtime($property_check_js),
            true
        );

        wp_localize_script(
            'property-check-form',
            'propertyCheckAjax',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
            )
        );
    }

    /**
     * ---------------------------------------------------------
     * Single Location Page JavaScript
     * ---------------------------------------------------------
     */
    if (is_singular('location')) {

        $single_location_js = $theme_path . '/assets/js/pages/single-location.js';

        if (file_exists($single_location_js)) {
            wp_enqueue_script(
                'hello-child-single-location',
                $theme_uri . '/assets/js/pages/single-location.js',
                array(),
                filemtime($single_location_js),
                true
            );
        }
    }

    /**
     * ---------------------------------------------------------
     * Google Maps Places
     * ---------------------------------------------------------
     */
    wp_enqueue_script(
        'google-maps-places',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyDOgoIk4iUqfBL2SVmVOWe75MooDpMz-AM&libraries=places',
        array(),
        null,
        true
    );

    /**
     * ---------------------------------------------------------
     * Floor Plan AJAX Filter
     * ---------------------------------------------------------
     */
    $floor_plan_js = $theme_path . '/assets/js/components/floor-plan-filter.js';

    if (file_exists($floor_plan_js)) {
        wp_enqueue_script(
            'floor-plan-filter',
            $theme_uri . '/assets/js/components/floor-plan-filter.js',
            array('jquery'),
            filemtime($floor_plan_js),
            true
        );

        wp_localize_script(
            'floor-plan-filter',
            'floorPlanFilter',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('floor_plan_filter_nonce'),
            )
        );
    }
}

add_action('wp_enqueue_scripts', 'hello_child_enqueue_assets');
