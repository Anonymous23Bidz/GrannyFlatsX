<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register dynamic event taxonomy rewrite rules.
 */
function gxf_event_type_rewrite_rules()
{

    if (!post_type_exists('event') || !taxonomy_exists('event_type')) {
        return;
    }

    $terms = get_terms([
        'taxonomy'   => 'event_type',
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return;
    }

    foreach ($terms as $term) {

        $slug = preg_quote($term->slug, '/');

        // Single event: /open-home/event-name/
        add_rewrite_rule(
            '^' . $slug . '/([^/]+)/?$',
            'index.php?post_type=event&name=$matches[1]&event_type=' . $term->slug,
            'top'
        );

        // Taxonomy archive: /open-home/
        add_rewrite_rule(
            '^' . $slug . '/?$',
            'index.php?event_type=' . $term->slug,
            'top'
        );
    }
}
add_action('init', 'gxf_event_type_rewrite_rules', 20);



function gxf_event_type_term_link($url, $term, $taxonomy)
{

    if ($taxonomy !== 'event_type') {
        return $url;
    }

    return home_url('/' . $term->slug . '/');
}
add_filter('term_link', 'gxf_event_type_term_link', 10, 3);


/**
 * Generate dynamic single event URLs.
 */
function gxf_event_type_post_link($permalink, $post, $leavename = false)
{

    if ($post->post_type !== 'event') {
        return $permalink;
    }

    if (strpos($permalink, '%event_type%') === false) {
        return $permalink;
    }

    $terms = get_the_terms($post->ID, 'event_type');

    if (empty($terms) || is_wp_error($terms)) {
        return $permalink;
    }

    $term = reset($terms);

    return str_replace('%event_type%', $term->slug, $permalink);
}
add_filter('post_type_link', 'gxf_event_type_post_link', 99, 3);


function gxf_flush_event_type_rewrite_rules()
{
    flush_rewrite_rules(false);
}

add_action('created_event_type', 'gxf_flush_event_type_rewrite_rules');
add_action('edited_event_type', 'gxf_flush_event_type_rewrite_rules');
add_action('delete_event_type', 'gxf_flush_event_type_rewrite_rules');
