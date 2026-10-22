<?php
add_shortcode('taxonomy_value', function ($atts) {
    $atts = shortcode_atts([
        'taxonomy'  => '',
        'field'     => 'name',
        'term_id'   => '',
        'slug'      => '',
        'separator' => ', ',
        'style'     => 'text',
        'post_id'   => '',
    ], $atts, 'taxonomy_value');

    $taxonomy = sanitize_key($atts['taxonomy']);
    $field    = sanitize_key($atts['field']);
    $style    = sanitize_key($atts['style']);
    $post_id  = $atts['post_id'] !== '' ? absint($atts['post_id']) : get_the_ID();

    if (!$taxonomy || !taxonomy_exists($taxonomy)) {
        return '';
    }

    $terms = [];

    if ($atts['term_id'] !== '') {
        $term = get_term(absint($atts['term_id']), $taxonomy);

        if ($term && !is_wp_error($term)) {
            $terms[] = $term;
        }
    } elseif ($atts['slug'] !== '') {
        $term = get_term_by('slug', sanitize_title($atts['slug']), $taxonomy);

        if ($term && !is_wp_error($term)) {
            $terms[] = $term;
        }
    } else {
        $assigned_terms = get_the_terms($post_id, $taxonomy);

        if (!empty($assigned_terms) && !is_wp_error($assigned_terms)) {
            $terms = $assigned_terms;
        }
    }

    if (empty($terms)) {
        return '';
    }

    $output = [];

    foreach ($terms as $term) {
        switch ($field) {
            case 'name':
                $value = $term->name;
                break;
            case 'slug':
                $value = $term->slug;
                break;
            case 'description':
                $value = term_description($term->term_id, $taxonomy);
                break;
            case 'id':
                $value = (string) $term->term_id;
                break;
            default:
                continue 2;
        }

        if ($style === 'tab') {
            $output[] = sprintf(
                '<span class="taxonomy-value-tab">%s</span>',
                esc_html(wp_strip_all_tags($value))
            );
        } else {
            $output[] = $field === 'description'
                ? wp_kses_post($value)
                : esc_html($value);
        }
    }

    if (empty($output)) {
        return '';
    }

    if ($style === 'tab') {
        return '<div class="taxonomy-value-tabs">' . implode('', $output) . '</div>';
    }

    return implode($atts['separator'], $output);
});
