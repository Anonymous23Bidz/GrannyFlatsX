<?php

/* ============================================================
 * FLOOR PLAN FILTER SHORTCODE
 * ============================================================ */

function bedrooms_floorplan_filter_shortcode($atts)
{
    $atts = shortcode_atts([
        'post_type' => 'floor-plans',
    ], $atts, 'floor_plan_filter');

    $layout_taxonomy   = 'fp_layout';
    $features_taxonomy = 'feature';

    /* 
     * Layout terms
     **/

    $layout_filter_terms = get_terms([
        'taxonomy'   => $layout_taxonomy,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    /* 
     * Feature parent terms
     *  */

    $features_filter_terms = get_terms([
        'taxonomy'   => $features_taxonomy,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'parent'     => 0,
    ]);

    /* 
     * Get floor area values
     */

    $floorplan_query = new WP_Query([
        'post_type'      => $atts['post_type'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    $floor_area_values = [];

    if ($floorplan_query->have_posts()) {
        foreach ($floorplan_query->posts as $floorplan_id) {
            $floor_area = get_field('floor_area', $floorplan_id);

            if ($floor_area !== '' && $floor_area !== null) {
                $floor_area_values[] = $floor_area;
            }
        }
    }

    wp_reset_postdata();

    $floor_area_values = array_unique($floor_area_values);

    usort($floor_area_values, function ($a, $b) {
        return (float) $a <=> (float) $b;
    });

    /* 
     * Unique filter ID
     *  */

    $instance_id = 'bedrooms-fpf-' . wp_rand(10000, 999999);

    /* 
     * Layout display settings
     *  */

    $layout_settings = [
        'wide' => [
            'order'  => 1,
            'icon'   => 'narrow.svg',
            'height' => '16px',
        ],
        'compact' => [
            'order'  => 2,
            'icon'   => 'compact.svg',
            'height' => '20px',
        ],
        'narrow' => [
            'order'  => 3,
            'icon'   => 'narrow.svg',
            'height' => '12px',
        ],
        'l-shaped' => [
            'order'  => 4,
            'icon'   => 'L-shape.svg',
            'height' => '20px',
        ],
        'step-shaped' => [
            'order'  => 5,
            'icon'   => 'step-shaped.svg',
            'height' => '23px',
        ],
    ];

    ob_start();
?>

    <div
        id="<?php echo esc_attr($instance_id); ?>"
        class="bedrooms-fpf-filter"
        data-filter-group="floor-plans">

        <div class="bedrooms-fpf-card">

            <div class="bedrooms-fpf-grid">

                <!-- Floor Area -->

                <div class="bedrooms-fpf-select-container">

                    <label class="bedrooms-fpf-field-label">
                        Floor Area
                    </label>

                    <select
                        name="floor_area"
                        class="bedrooms-fpf-native-select bedrooms-fpf-floor-area-select">

                        <option value="">
                            Any
                        </option>

                        <?php foreach ($floor_area_values as $floor_area) : ?>

                            <option value="<?php echo esc_attr($floor_area); ?>">
                                <?php echo esc_html($floor_area); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Layout -->

                <div class="bedrooms-fpf-select-container">

                    <label class="bedrooms-fpf-field-label">
                        Layout
                    </label>

                    <button
                        type="button"
                        class="bedrooms-fpf-select-trigger bedrooms-fpf-layout-trigger">

                        <span class="bedrooms-fpf-trigger-text">
                            Any Layout
                        </span>

                        <span class="bedrooms-fpf-trigger-arrow">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>

                    </button>

                    <div class="bedrooms-fpf-layout-popover">

                        <div class="bedrooms-fpf-layout-grid">

                            <!-- Any Layout -->

                            <label
                                class="bedrooms-fpf-layout-option bedrooms-fpf-layout-any"
                                style="order:0;">

                                <input
                                    type="radio"
                                    class="bedrooms-fpf-layout-input"
                                    name="<?php echo esc_attr($instance_id); ?>-layout"
                                    value=""
                                    data-layout-name="Any Layout"
                                    checked>

                                <span class="bedrooms-fpf-layout-choice">

                                    <span class="bedrooms-fpf-layout-name">
                                        Any Layout
                                    </span>

                                </span>

                            </label>

                            <?php if ($layout_filter_terms && !is_wp_error($layout_filter_terms)) : ?>

                                <?php foreach ($layout_filter_terms as $term) : ?>

                                    <?php
                                    $layout_slug = sanitize_title($term->name);

                                    $layout_setting = $layout_settings[$layout_slug] ?? [
                                        'order'  => 99,
                                        'icon'   => '',
                                        'height' => '20px',
                                    ];

                                    $icon_url = '';

                                    if (!empty($layout_setting['icon'])) {
                                        $icon_url = get_stylesheet_directory_uri() . '/assets/images/' . $layout_setting['icon'];
                                    }
                                    ?>

                                    <label
                                        class="bedrooms-fpf-layout-option"
                                        style="order:<?php echo esc_attr($layout_setting['order']); ?>;">

                                        <input
                                            type="radio"
                                            class="bedrooms-fpf-layout-input"
                                            name="<?php echo esc_attr($instance_id); ?>-layout"
                                            value="<?php echo esc_attr($term->term_id); ?>"
                                            data-layout-name="<?php echo esc_attr($term->name); ?>">

                                        <span class="bedrooms-fpf-layout-choice">

                                            <?php if ($icon_url) : ?>

                                                <img
                                                    class="bedrooms-fpf-layout-icon"
                                                    src="<?php echo esc_url($icon_url); ?>"
                                                    alt=""
                                                    style="height:<?php echo esc_attr($layout_setting['height']); ?>;">

                                            <?php endif; ?>

                                            <span class="bedrooms-fpf-layout-name">
                                                <?php echo esc_html($term->name); ?>
                                            </span>

                                        </span>

                                    </label>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

                <!-- Features -->

                <div class="bedrooms-fpf-select-container bedrooms-fpf-features-container">

                    <label class="bedrooms-fpf-field-label">
                        Features
                    </label>

                    <button
                        type="button"
                        class="bedrooms-fpf-select-trigger bedrooms-fpf-features-trigger">

                        <span class="bedrooms-fpf-trigger-text">
                            Any Features
                        </span>

                        <span class="bedrooms-fpf-trigger-arrow">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>

                    </button>

                    <div class="bedrooms-fpf-features-popover">

                        <div class="bedrooms-fpf-features-grid">

                            <?php if ($features_filter_terms && !is_wp_error($features_filter_terms)) : ?>

                                <?php foreach ($features_filter_terms as $parent_term) : ?>

                                    <?php
                                    $child_terms = get_terms([
                                        'taxonomy'   => $features_taxonomy,
                                        'hide_empty' => false,
                                        'orderby'    => 'name',
                                        'order'      => 'ASC',
                                        'parent'     => $parent_term->term_id,
                                    ]);
                                    ?>

                                    <div class="bedrooms-fpf-feature-column">

                                        <div class="bedrooms-fpf-feature-category-title">
                                            <?php echo esc_html($parent_term->name); ?>
                                        </div>

                                        <?php if ($child_terms && !is_wp_error($child_terms)) : ?>

                                            <?php foreach ($child_terms as $child_term) : ?>

                                                <label class="bedrooms-fpf-checkbox-label">

                                                    <input
                                                        type="checkbox"
                                                        class="bedrooms-fpf-checkbox-input"
                                                        value="<?php echo esc_attr($child_term->term_id); ?>"
                                                        data-feature-name="<?php echo esc_attr($child_term->name); ?>">

                                                    <span class="bedrooms-fpf-checkbox-custom"></span>

                                                    <span class="bedrooms-fpf-checkbox-text">
                                                        <?php echo esc_html($child_term->name); ?>
                                                    </span>

                                                </label>

                                            <?php endforeach; ?>

                                        <?php endif; ?>

                                    </div>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

                <!-- Buttons -->

                <div class="bedrooms-fpf-actions">

                    <button
                        type="button"
                        class="bedrooms-fpf-view-designs-btn">
                        View Designs
                    </button>

                    <button
                        type="button"
                        class="bedrooms-fpf-reset-btn">
                        Reset Filters
                    </button>

                </div>

            </div>

        </div>

    </div>

<?php

    return ob_get_clean();
}

add_shortcode(
    'floor_plan_filter',
    'bedrooms_floorplan_filter_shortcode'
);


function bedrooms_floorplan_results_shortcode($atts)
{
    $atts = shortcode_atts([
        'post_type'      => 'floor-plans',
        'posts_per_page' => 4,
    ], $atts, 'floor_plan_results');

    /* 
     * Get current URL path
     *  */

    $request_uri = isset($_SERVER['REQUEST_URI'])
        ? wp_unslash($_SERVER['REQUEST_URI'])
        : '';

    $request_path = wp_parse_url($request_uri, PHP_URL_PATH);

    $request_path = trim($request_path, '/');

    /* 
     * Detect 1-bedroom / 2-bedroom / 3-bedroom
     *  */

    $bedroom_number = '';

    if (preg_match(
        '#/designs/([123])-bedroom/?$#i',
        '/' . $request_path,
        $matches
    )) {
        $bedroom_number = $matches[1];
    }

    /* 
     * Build query
     *  */

    $query_args = [
        'post_type'      => $atts['post_type'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ];

    /* 
     * IMPORTANT:
     *
     * Taxonomy:
     * fp_bedrooms
     *
     * Term NAME:
     * 1
     * 2
     * 3
     *  */

    if ($bedroom_number !== '') {

        $query_args['tax_query'] = [
            [
                'taxonomy'         => 'fp_bedrooms',
                'field'            => 'name',
                'terms'            => $bedroom_number,
                'operator'         => 'IN',
                'include_children' => false,
            ],
        ];
    }

    /* 
     * Query floor plans
     *  */

    $floorplan_query = new WP_Query($query_args);

    $floorplans = [];

    if ($floorplan_query->have_posts()) {

        while ($floorplan_query->have_posts()) {

            $floorplan_query->the_post();

            $floorplan_id = get_the_ID();

            /* ------------------------------------------------
             * Layout
             * ------------------------------------------------ */

            $layout_terms = get_the_terms(
                $floorplan_id,
                'fp_layout'
            );

            $layout = [];
            $layout_ids = [];

            if ($layout_terms && !is_wp_error($layout_terms)) {

                foreach ($layout_terms as $term) {

                    $layout[] = $term->name;
                    $layout_ids[] = $term->term_id;
                }
            }

            /* ------------------------------------------------
             * Features
             * ------------------------------------------------ */

            $feature_terms = get_the_terms(
                $floorplan_id,
                'feature'
            );

            $features = [];
            $feature_ids = [];

            if ($feature_terms && !is_wp_error($feature_terms)) {

                foreach ($feature_terms as $term) {

                    $features[] = $term->name;
                    $feature_ids[] = $term->term_id;
                }
            }

            /* ------------------------------------------------
             * Bedrooms
             * ------------------------------------------------ */

            $bedroom_terms = get_the_terms(
                $floorplan_id,
                'fp_bedrooms'
            );

            $bedrooms = [];
            $bedroom_ids = [];

            if ($bedroom_terms && !is_wp_error($bedroom_terms)) {

                foreach ($bedroom_terms as $term) {

                    $bedrooms[] = $term->name;
                    $bedroom_ids[] = $term->term_id;
                }
            }

            /* ------------------------------------------------
             * Bathrooms
             * ------------------------------------------------ */

            $bathroom_terms = get_the_terms(
                $floorplan_id,
                'fp_bathrooms'
            );

            $bathrooms = [];
            $bathroom_ids = [];

            if ($bathroom_terms && !is_wp_error($bathroom_terms)) {

                foreach ($bathroom_terms as $term) {

                    $bathrooms[] = $term->name;
                    $bathroom_ids[] = $term->term_id;
                }
            }

            /* ------------------------------------------------
             * Floor area
             * ------------------------------------------------ */

            $floor_area = get_field(
                'floor_area',
                $floorplan_id
            );

            /* ------------------------------------------------
             * Living area
             * ------------------------------------------------ */

            $living_area = get_field(
                'living_area',
                $floorplan_id
            );

            /* ------------------------------------------------
             * Bedroom size
             * ------------------------------------------------ */

            $bed_1 = get_field(
                'bed_1_area',
                $floorplan_id
            );

            $bedroom_size = '';

            if (is_numeric($bed_1)) {

                $bedroom_size = ((float) $bed_1 < 10)
                    ? 'queen'
                    : 'king';
            }

            /* ------------------------------------------------
             * Plan dimensions
             * ------------------------------------------------ */

            $plan_length_mm = get_field(
                'plan_length_mm',
                $floorplan_id
            );

            $plan_width_mm = get_field(
                'plan_width_mm',
                $floorplan_id
            );

            $plan_length_mm = is_numeric($plan_length_mm)
                ? (float) $plan_length_mm
                : 0;

            $plan_width_mm = is_numeric($plan_width_mm)
                ? (float) $plan_width_mm
                : 0;

            /* ------------------------------------------------
             * Featured image
             * ------------------------------------------------ */

            $featured_image = get_the_post_thumbnail_url(
                $floorplan_id,
                'small'
            ) ?: '';

            /* ------------------------------------------------
             * Design thumbnail
             * ------------------------------------------------ */

            $design_thumbnail = get_field(
                'design_image_for_thumbnail_----_alt_display',
                $floorplan_id
            );

            $design_thumbnail_url = '';

            if (is_array($design_thumbnail)) {

                if (!empty($design_thumbnail['sizes']['medium'])) {

                    $design_thumbnail_url =
                        $design_thumbnail['sizes']['medium'];
                } elseif (!empty($design_thumbnail['url'])) {

                    $design_thumbnail_url =
                        $design_thumbnail['url'];
                }
            } elseif (is_numeric($design_thumbnail)) {

                $design_thumbnail_url =
                    wp_get_attachment_image_url(
                        (int) $design_thumbnail,
                        'medium'
                    ) ?: '';
            } elseif (is_string($design_thumbnail)) {

                $design_thumbnail_url = $design_thumbnail;
            }

            /* ------------------------------------------------
             * Store floor plan
             * ------------------------------------------------ */

            $floorplans[] = [

                'id' => $floorplan_id,

                'title' => get_the_title(
                    $floorplan_id
                ),

                'url' => get_permalink(
                    $floorplan_id
                ),

                'featured_image' => $featured_image,

                'design_thumbnail' => $design_thumbnail_url,

                'bedrooms' => $bedrooms,

                'bedroom_ids' => $bedroom_ids,

                'bed_1' => $bed_1,

                'bedroom_size' => $bedroom_size,

                'bathrooms' => $bathrooms,

                'bathroom_ids' => $bathroom_ids,

                'layout' => $layout,

                'layout_ids' => $layout_ids,

                'features' => $features,

                'feature_ids' => $feature_ids,

                'floor_area' => $floor_area,

                'living_area' => $living_area,

                'plan_length_mm' => $plan_length_mm,

                'plan_width_mm' => $plan_width_mm,

            ];
        }

        wp_reset_postdata();
    }

    /* 
     * Unique results ID
     *  */

    $results_id = 'bedrooms-fpf-results-' . wp_rand(
        10000,
        999999
    );

    ob_start();

?>

    <div
        id="<?php echo esc_attr($results_id); ?>"
        class="bedrooms-fpf-results"
        data-filter-group="floor-plans"
        data-bedroom="<?php echo esc_attr($bedroom_number); ?>"
        data-items-per-load="<?php echo esc_attr($atts['posts_per_page']); ?>">

        <div class="bedrooms-floor-plan-grid">

            <?php foreach ($floorplans as $index => $floorplan) : ?>

                <?php

                $initial_hidden =
                    $index >= (int) $atts['posts_per_page'];

                $bedroom_ids = implode(
                    ',',
                    $floorplan['bedroom_ids']
                );

                $bathroom_ids = implode(
                    ',',
                    $floorplan['bathroom_ids']
                );

                $layout_ids = implode(
                    ',',
                    $floorplan['layout_ids']
                );

                $feature_ids = implode(
                    ',',
                    $floorplan['feature_ids']
                );

                ?>

                <article
                    class="bedrooms-floor-plan-card"
                    <?php if ($initial_hidden) : ?>hidden<?php endif; ?>
                    data-title="<?php echo esc_attr($floorplan['title']); ?>"
                    data-bedrooms="<?php echo esc_attr($bedroom_ids); ?>"
                    data-bathrooms="<?php echo esc_attr($bathroom_ids); ?>"
                    data-bedroom-size="<?php echo esc_attr($floorplan['bedroom_size']); ?>"
                    data-layout="<?php echo esc_attr($layout_ids); ?>"
                    data-features="<?php echo esc_attr($feature_ids); ?>"
                    data-floor-area="<?php echo esc_attr($floorplan['floor_area']); ?>"
                    data-living-area="<?php echo esc_attr($floorplan['living_area']); ?>"
                    data-plan-length-mm="<?php echo esc_attr($floorplan['plan_length_mm']); ?>"
                    data-plan-width-mm="<?php echo esc_attr($floorplan['plan_width_mm']); ?>">

                    <a
                        href="<?php echo esc_url($floorplan['url']); ?>"
                        class="bedrooms-floor-plan-card-link">

                        <div class="bedrooms-floor-plan-image">

                            <?php if ($floorplan['design_thumbnail']) : ?>

                                <img
                                    class="bedrooms-facade-image"
                                    src="<?php echo esc_url($floorplan['design_thumbnail']); ?>"
                                    alt="<?php echo esc_attr($floorplan['title']); ?>"
                                    loading="lazy">

                            <?php elseif ($floorplan['featured_image']) : ?>

                                <img
                                    class="bedrooms-floorplan-image"
                                    src="<?php echo esc_url($floorplan['featured_image']); ?>"
                                    alt="<?php echo esc_attr($floorplan['title']); ?>"
                                    loading="lazy">

                            <?php endif; ?>

                        </div>

                        <div class="bedrooms-floor-plan-card-content">

                            <h3 class="bedrooms-floor-plan-title">
                                <?php echo esc_html($floorplan['title']); ?>
                            </h3>

                            <?php if (!empty($floorplan['floor_area'])) : ?>

                                <div class="bedrooms-floor-plan-meta">

                                    <span class="bedrooms-floor-plan-icon">
                                        <i class="fa-solid fa-bath"></i>
                                    </span>

                                    <span>
                                        <?php echo esc_html($floorplan['floor_area']); ?>
                                    </span>

                                </div>

                            <?php endif; ?>

                            <?php if (!empty($floorplan['layout'])) : ?>

                                <div class="bedrooms-floor-plan-meta">

                                    <span class="bedrooms-floor-plan-icon">
                                        <i class="fa-solid fa-arrow-up-long"></i>
                                    </span>

                                    <span>
                                        <?php echo esc_html(
                                            implode(', ', $floorplan['layout'])
                                        ); ?>
                                    </span>

                                </div>

                            <?php endif; ?>

                            <span class="bedrooms-floor-plan-link">

                                View floor plan

                                <i class="fa-solid fa-arrow-right"></i>

                            </span>

                        </div>

                    </a>

                </article>

            <?php endforeach; ?>

        </div>

    </div>

    <?php if (count($floorplans) > (int) $atts['posts_per_page']) : ?>

        <div class="bedrooms-fpf-view-all-wrap">

            <button type="button" class="bedrooms-fpf-view-all-btn">

                View All Floor Plans
                (<?php echo esc_html(count($floorplans)); ?>)

                <i class="fa-solid fa-arrow-down"></i>

            </button>

        </div>

    <?php endif; ?>

<?php

    return ob_get_clean();
}

add_shortcode(
    'floor_plan_results',
    'bedrooms_floorplan_results_shortcode'
);
?>