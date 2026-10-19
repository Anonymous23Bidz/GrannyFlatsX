<?php
add_shortcode('taxonomy_value', function ($atts) {
    $atts = shortcode_atts([
        'taxonomy' => '',
        'field'    => 'name',
        'term_id'  => '',
        'slug'     => '',
        'separator' => ', ',
    ], $atts, 'taxonomy_value');

    $taxonomy = sanitize_key($atts['taxonomy']);

    if (!$taxonomy || !taxonomy_exists($taxonomy)) {
        return '';
    }

    $terms = [];

    if ($atts['term_id'] !== '') {
        $term = get_term((int) $atts['term_id'], $taxonomy);
        if ($term && !is_wp_error($term)) {
            $terms[] = $term;
        }
    } elseif ($atts['slug'] !== '') {
        $term = get_term_by('slug', sanitize_title($atts['slug']), $taxonomy);
        if ($term && !is_wp_error($term)) {
            $terms[] = $term;
        }
    } else {
        $assigned_terms = get_the_terms(get_the_ID(), $taxonomy);

        if (!empty($assigned_terms) && !is_wp_error($assigned_terms)) {
            $terms = $assigned_terms;
        }
    }

    if (empty($terms)) {
        return '';
    }

    $output = [];

    foreach ($terms as $term) {
        switch ($atts['field']) {
            case 'name':
                $output[] = esc_html($term->name);
                break;

            case 'slug':
                $output[] = esc_html($term->slug);
                break;

            case 'description':
                $output[] = wp_kses_post(term_description($term->term_id, $taxonomy));
                break;

            case 'id':
                $output[] = (string) $term->term_id;
                break;
        }
    }

    return implode($atts['separator'], $output);
});
