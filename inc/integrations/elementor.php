<?php
if (! defined('ABSPATH')) {
    exit;
}

add_action('elementor/widget/before_render_content', function ($widget) {
    if ('heading' === $widget->get_name()) {
        $settings = $widget->get_settings_for_display();
        if (! empty($settings['title']) && strpos($settings['title'], '[') !== false) {
            $widget->set_settings('title', do_shortcode($settings['title']));
        }
    }
});

add_action('elementor/query/complete_projects_by_region', function ($query) {

    $current_id = get_queried_object_id();

    if (!$current_id) {
        $query->set('post__in', [0]);
        return;
    }

    $terms = wp_get_post_terms(
        $current_id,
        'broader_lga',
        [
            'fields' => 'ids',
        ]
    );

    if (is_wp_error($terms) || empty($terms)) {
        $query->set('post__in', [0]);
        return;
    }

    $query->set('post_type', 'completed-projects');

    $query->set('post__not_in', [
        $current_id,
    ]);

    $tax_query = (array) $query->get('tax_query');

    $tax_query[] = [
        'taxonomy' => 'broader_lga',
        'field'    => 'term_id',
        'terms'    => $terms,
        'operator' => 'IN',
    ];

    $query->set('tax_query', $tax_query);
}, 10, 2);
