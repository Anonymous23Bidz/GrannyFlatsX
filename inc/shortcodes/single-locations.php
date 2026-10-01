<?php

// =====================================================
// 1. Council Approval Shortcode
// Usage: [council_approval]
// =====================================================
function render_acf_council_approval_shortcode()
{
    $post_id = get_the_ID();

    if (function_exists('have_rows') && have_rows('council_approval', $post_id)) {

        // Define your FontAwesome icon classes for cards 1, 2, and 3
        $static_icons = array(
            1 => 'fa-solid fa-file-circle-check',
            2 => 'fa-solid fa-file-lines',
            3 => 'fa-solid fa-map-location-dot',
        );

        ob_start();
?>

        <div class="council-approval-wrapper">

            <?php
            while (have_rows('council_approval', $post_id)) :
                the_row();

                $index      = get_row_index(); // Gets 1-based index (1, 2, 3...)
                $title      = get_sub_field('title');
                $content    = get_sub_field('content');

                // Fallback to card 1 icon if index isn't set
                $icon_class = isset($static_icons[$index]) ? $static_icons[$index] : $static_icons[1];
            ?>

                <div class="council-approval-item">

                    <div class="council-approval-icon">
                        <i class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
                    </div>

                    <?php if ($title) : ?>
                        <h3 class="council-approval-title">
                            <?php echo esc_html($title); ?>
                        </h3>
                    <?php endif; ?>

                    <?php if ($content) : ?>
                        <div class="council-approval-content">
                            <?php echo wp_kses_post(wpautop($content)); ?>
                        </div>
                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        </div>

        <?php

        return ob_get_clean();
    }

    return '';
}

add_shortcode(
    'council_approval',
    'render_acf_council_approval_shortcode'
);


// =====================================================
// 2. ACF Group Image Shortcode
// Usage: [acf_group_image parent="group_name" child="image_field"]
// =====================================================

add_shortcode('acf_group_image', function ($atts) {

    $atts = shortcode_atts(
        array(
            'parent' => '',
            'child'  => '',
            'size'   => 'full'
        ),
        $atts
    );

    if (empty($atts['parent']) || empty($atts['child'])) {
        return '';
    }

    $post_id = get_the_ID();

    $group = get_field($atts['parent'], $post_id);

    if (!empty($group) && !empty($group[$atts['child']])) {

        $image_data = $group[$atts['child']];

        // ACF Image Array
        if (is_array($image_data) && isset($image_data['id'])) {

            return wp_get_attachment_image(
                $image_data['id'],
                $atts['size']
            );
        }

        // ACF Image ID
        if (is_numeric($image_data)) {

            return wp_get_attachment_image(
                $image_data,
                $atts['size']
            );
        }

        // ACF Image URL
        if (is_string($image_data)) {

            return '<img src="' . esc_url($image_data) . '" alt="">';
        }
    }

    return '';
});

function render_why_build_image_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'group'   => 'why_build',
        'field'   => 'image',
        'post_id' => false,
    ), $atts, 'why_build_image');

    $post_id = $atts['post_id'] ? $atts['post_id'] : get_the_ID();

    ob_start();

    if (have_rows($atts['group'], $post_id)) :
        while (have_rows($atts['group'], $post_id)) : the_row();
            $image = get_sub_field($atts['field']);

            if ($image) :
                if (is_array($image)) {
                    $url = $image['url'];
                    $alt = ! empty($image['alt']) ? $image['alt'] : get_the_title($post_id);
                } elseif (is_numeric($image)) {
                    $url = wp_get_attachment_image_url($image, 'full');
                    $alt = get_post_meta($image, '_wp_attachment_image_alt', true);
                } else {
                    $url = $image;
                    $alt = get_the_title($post_id);
                }
        ?>
                <img style="width: 100%;" src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>" class="why-build-image" />
    <?php
            endif;
        endwhile;
    endif;

    return ob_get_clean();
}
add_shortcode('why_build_image', 'render_why_build_image_shortcode');

// =====================================================
// Key Facts Shortcode
// ACF Repeater: key_facts
// Sub-fields: label, value
// Usage: [key_facts]
// =====================================================

function key_facts_shortcode()
{

    $post_id = get_the_ID();

    if (
        !function_exists('have_rows') ||
        !have_rows('key_facts', $post_id)
    ) {
        return '';
    }

    ob_start();
    ?>

    <div class="key-facts">

        <?php while (have_rows('key_facts', $post_id)) : the_row(); ?>

            <?php
            $label = get_sub_field('label');
            $value = get_sub_field('value');
            ?>

            <div class="key-fact">

                <div class="key-fact__label">
                    <span class="key-fact__bullet"></span>
                    <span>
                        <?php echo esc_html($label); ?>
                    </span>
                </div>

                <div class="key-fact__value">
                    <?php echo wp_kses_post($value); ?>
                </div>

            </div>

        <?php endwhile; ?>

    </div>

<?php

    return ob_get_clean();
}
add_shortcode('key_facts', 'key_facts_shortcode');

add_shortcode('acf_group_field', function ($atts) {
    $atts = shortcode_atts([
        'group'   => '',
        'field'   => '',
        'post_id' => false,
    ], $atts, 'acf_group_field');

    if (empty($atts['group']) || empty($atts['field'])) {
        return '';
    }

    $group_data = get_field($atts['group'], $atts['post_id']);

    if (is_array($group_data) && isset($group_data[$atts['field']]) && $group_data[$atts['field']] !== '') {
        $value = $group_data[$atts['field']];

        // Replace dynamic {Location} placeholder with current Post Title if present
        $location_name = get_the_title($atts['post_id'] ?: get_the_ID());
        $value = str_replace('{Location}', $location_name, $value);

        // Allow HTML output for WYSIWYG/Formatted text fields
        return wp_kses_post($value);
    }

    return '';
});

add_action('elementor/query/related_council_posts', function ($query) {
    if (! is_singular('location') && ! is_singular('locations')) {
        return;
    }

    $current_post_id = get_the_ID();

    $terms = wp_get_post_terms($current_post_id, 'council', array('fields' => 'ids'));

    if (! empty($terms) && ! is_wp_error($terms)) {
        $tax_query = array(
            array(
                'taxonomy' => 'council',
                'field'    => 'term_id',
                'terms'    => $terms,
                'operator' => 'IN',
            ),
        );

        $query->set('tax_query', $tax_query);

        $query->set('post__not_in', array($current_post_id));
    }
});

// =====================================================
// 4. Allow Shortcodes in Text/Description
// =====================================================

add_filter(
    'term_description',
    'do_shortcode'
);

add_filter(
    'widget_text_content',
    'do_shortcode'
);
