<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register dynamic single event permalink rules.
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
        $term_slug = preg_quote($term->slug, '#');

        add_rewrite_rule(
            '^' . $term_slug . '/([^/]+)/?$',
            'index.php?post_type=event&name=$matches[1]',
            'top'
        );
    }
}
add_action('init', 'gxf_event_type_rewrite_rules', 20);


/**
 * Generate event URLs using their taxonomy slug.
 */
function gxf_event_type_post_link($post_link, $post)
{
    if ($post->post_type !== 'event') {
        return $post_link;
    }

    $terms = get_the_terms($post->ID, 'event_type');

    if (!empty($terms) && !is_wp_error($terms)) {
        return home_url(
            '/' . $terms[0]->slug . '/' . $post->post_name . '/'
        );
    }

    return $post_link;
}
add_filter('post_type_link', 'gxf_event_type_post_link', 99, 2);



function gxf_flush_event_type_rewrite_rules()
{
    flush_rewrite_rules(false);
}

add_action('created_event_type', 'gxf_flush_event_type_rewrite_rules');
add_action('edited_event_type', 'gxf_flush_event_type_rewrite_rules');
add_action('delete_event_type', 'gxf_flush_event_type_rewrite_rules');
