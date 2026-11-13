<?php

function gxf_taxonomy_acf_shortcode($atts)
{

    $atts = shortcode_atts([
        'taxonomy'  => '',
        'field'     => '',
        'term_id'   => '',
        'slug'      => '',
        'separator' => ', ',
        'style'     => 'text',
        'post_id'   => '',
    ], $atts);

    $style   = sanitize_key($atts['style']);
    $post_id = $atts['post_id'] !== ''
        ? absint($atts['post_id'])
        : get_the_ID();

    $values = [];

    // TAXONOMY
    if (!empty($atts['taxonomy'])) {

        $taxonomy = sanitize_key($atts['taxonomy']);
        $field    = !empty($atts['field'])
            ? sanitize_key($atts['field'])
            : 'name';

        if (!taxonomy_exists($taxonomy)) {
            return '';
        }

        if ($atts['term_id'] !== '') {

            $term = get_term(absint($atts['term_id']), $taxonomy);
            $terms = ($term && !is_wp_error($term)) ? [$term] : [];
        } elseif ($atts['slug'] !== '') {

            $term = get_term_by('slug', sanitize_title($atts['slug']), $taxonomy);
            $terms = ($term && !is_wp_error($term)) ? [$term] : [];
        } else {

            $terms = get_the_terms($post_id, $taxonomy);

            if (empty($terms) || is_wp_error($terms)) {
                $terms = [];
            }
        }

        foreach ($terms as $term) {

            switch ($field) {
                case 'name':
                    $values[] = $term->name;
                    break;

                case 'slug':
                    $values[] = $term->slug;
                    break;

                case 'description':
                    $values[] = term_description($term->term_id, $taxonomy);
                    break;

                case 'id':
                    $values[] = (string) $term->term_id;
                    break;
            }
        }
    }

    // ACF FIELD
    elseif (!empty($atts['field'])) {

        if (!function_exists('get_field')) {
            return '';
        }

        $value = get_field($atts['field'], $post_id);

        if (empty($value)) {
            return '';
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $item) {

            if (is_array($item)) {
                $item = $item['label'] ?? $item['value'] ?? '';
            }

            if (is_scalar($item) && $item !== '') {
                $values[] = (string) $item;
            }
        }
    } else {
        return '';
    }

    if (empty($values)) {
        return '';
    }

    $output = [];

    foreach ($values as $value) {

        if ($style === 'tab') {

            $output[] = sprintf(
                '<span class="taxonomy-value-tab">%s</span>',
                esc_html(wp_strip_all_tags((string) $value))
            );
        } else {

            $output[] = esc_html(wp_strip_all_tags((string) $value));
        }
    }

    if ($style === 'tab') {
        return '<div class="taxonomy-value-tabs">' . implode('', $output) . '</div>';
    }

    return implode($atts['separator'], $output);
}

// Register both shortcodes
add_shortcode('taxonomy_value', 'gxf_taxonomy_acf_shortcode');
add_shortcode('acf', 'gxf_taxonomy_acf_shortcode');
