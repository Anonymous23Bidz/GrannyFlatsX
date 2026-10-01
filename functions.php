<?php

/**
 * Child Theme Functions
 *
 * @package Hello_Elementor_Child
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup
 */
require_once get_stylesheet_directory() . '/inc/setup/theme-setup.php';

/**
 * Enqueue styles and scripts
 */
require_once get_stylesheet_directory() . '/inc/setup/enqueue.php';

/**
 * Hooks
 */
require_once get_stylesheet_directory() . '/inc/hooks/frontend.php';
require_once get_stylesheet_directory() . '/inc/hooks/admin.php';
require_once get_stylesheet_directory() . '/inc/hooks/filters.php';

/**
 * Helpers
 */
require_once get_stylesheet_directory() . '/inc/helpers/general.php';

/**
 * ACF
 */
require_once get_stylesheet_directory() . '/inc/acf/fields.php';

/**
 * Elementor integrations
 */
require_once get_stylesheet_directory() . '/inc/integrations/elementor.php';

/**
 * AJAX
 */
require_once get_stylesheet_directory() . '/inc/ajax/ajax.php';
require_once get_stylesheet_directory() . '/inc/ajax/floor-plan-filter.php';

/**
 * Shortcodes
 */
require_once get_stylesheet_directory() . '/inc/shortcodes/post-title.php';
require_once get_stylesheet_directory() . '/inc/shortcodes/single-locations.php';
require_once get_stylesheet_directory() . '/inc/shortcodes/accordion.php';
require_once get_stylesheet_directory() . '/inc/shortcodes/breadcrumbs.php';
require_once get_stylesheet_directory() . '/inc/shortcodes/property-check.php';
require_once get_stylesheet_directory() . '/inc/shortcodes/suburb-filter.php';
