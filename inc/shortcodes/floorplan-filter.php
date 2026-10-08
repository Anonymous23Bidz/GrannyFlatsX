<?php

function custom_floorplan_filter_shortcode($atts)
{
    $atts = shortcode_atts([
        'post_type'      => 'floor-plans',
        'posts_per_page' => 8,
    ], $atts, 'floorplan_filter');

    /*
     * Taxonomies
     */
    $bedroom_taxonomy = 'fp_bedrooms';
    $bathroom_taxonomy = 'fp_bathrooms';
    $bestfor_taxonomy = 'fp_best-for';
    $layout_taxonomy  = 'fp_layout';
    $features_taxonomy = 'feature';

    /*
     * Bedroom terms
     */
    $bedroom_filter_terms = get_terms([
        'taxonomy'   => $bedroom_taxonomy,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    $bathroom_filter_terms = get_terms([
        'taxonomy'   => $bathroom_taxonomy,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);


    /*
     * Best For terms
     */
    $bestfor_filter_terms = get_terms([
        'taxonomy'   => $bestfor_taxonomy,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    /*
     * Layout terms
     */
    $layout_filter_terms = get_terms([
        'taxonomy'   => $layout_taxonomy,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    /*
     * Features terms
     */
    $features_filter_terms = get_terms([
        'taxonomy'   => $features_taxonomy,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'parent'     => 0,
    ]);

    /*
     * Get all floor plans
     */
    $floorplan_query = new WP_Query([
        'post_type'      => $atts['post_type'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    $floorplans = [];


    if ($floorplan_query->have_posts()) {

        while ($floorplan_query->have_posts()) {

            $floorplan_query->the_post();

            $floorplan_id = get_the_ID();

            $plan_length_mm = get_field('plan_length_mm', $floorplan_id);
            $plan_width_mm = get_field('plan_width_mm', $floorplan_id);

            $plan_length_mm = is_numeric($plan_length_mm) ? (float) $plan_length_mm : 0;
            $plan_width_mm = is_numeric($plan_width_mm) ? (float) $plan_width_mm : 0;

            /*
             * Bedrooms
             */
            $floorplan_bedroom_terms = get_the_terms(
                $floorplan_id,
                $bedroom_taxonomy
            );

            $floorplan_bedrooms = [];
            $floorplan_bedroom_ids = [];

            if (
                !is_wp_error($floorplan_bedroom_terms) &&
                !empty($floorplan_bedroom_terms)
            ) {
                foreach ($floorplan_bedroom_terms as $bedroom_term) {
                    $floorplan_bedrooms[] = $bedroom_term->name;
                    $floorplan_bedroom_ids[] = $bedroom_term->term_id;
                }
            }

            /*
             * Best For
             */
            $floorplan_bestfor_terms = get_the_terms(
                $floorplan_id,
                $bestfor_taxonomy
            );

            /*
            * Bathrooms
            */
            $floorplan_bathroom_terms = get_the_terms(
                $floorplan_id,
                $bathroom_taxonomy
            );

            $floorplan_bathrooms = [];
            $floorplan_bathroom_ids = [];

            /*
            * Bedroom Size
            */
            $bed_1 = get_field('bed_1_area', $floorplan_id);
            $bed_1_number = is_numeric($bed_1)
                ? (float) $bed_1
                : (float) preg_replace('/[^0-9.]/', '', (string) $bed_1);

            $bedroom_size = '';

            if ($bed_1 !== '' && $bed_1 !== null) {
                $bedroom_size = $bed_1_number < 10 ? 'queen' : 'king';
            }
            if (
                !is_wp_error($floorplan_bathroom_terms) &&
                !empty($floorplan_bathroom_terms)
            ) {
                foreach ($floorplan_bathroom_terms as $bathroom_term) {
                    $floorplan_bathrooms[] = $bathroom_term->name;
                    $floorplan_bathroom_ids[] = $bathroom_term->term_id;
                }
            }

            $floorplan_bestfor = [];

            if (
                !is_wp_error($floorplan_bestfor_terms) &&
                !empty($floorplan_bestfor_terms)
            ) {
                foreach ($floorplan_bestfor_terms as $bestfor_term) {
                    $floorplan_bestfor[] = $bestfor_term->term_id;
                }
            }

            /*
             * Layout
             */
            $floorplan_layout_terms = get_the_terms(
                $floorplan_id,
                $layout_taxonomy
            );

            $floorplan_layout = [];

            if (
                !is_wp_error($floorplan_layout_terms) &&
                !empty($floorplan_layout_terms)
            ) {
                foreach ($floorplan_layout_terms as $layout_term) {
                    $floorplan_layout[] = $layout_term->term_id;
                }
            }

            /*
            * Features
            */

            $floorplan_feature_terms = get_the_terms(
                $floorplan_id,
                $features_taxonomy
            );

            $floorplan_features = [];

            if (
                !is_wp_error($floorplan_feature_terms) &&
                !empty($floorplan_feature_terms)
            ) {
                foreach ($floorplan_feature_terms as $feature_term) {
                    $floorplan_features[] = $feature_term->term_id;
                }
            }

            /*
             * Floor Plan Data
             */
            $floorplans[] = [
                'id' => $floorplan_id,
                'title' => get_the_title($floorplan_id),
                'url' => get_permalink($floorplan_id),
                'featured_image' => get_the_post_thumbnail_url($floorplan_id, 'small') ?: '',
                'design_thumbnail' => get_field('design_image_for_thumbnail_----_alt_display', $floorplan_id),
                'bedrooms' => $floorplan_bedrooms,
                'bedroom_ids' => $floorplan_bedroom_ids,
                'bed_1' => $bed_1,
                'bedroom_size' => $bedroom_size,
                'bathrooms' => $floorplan_bathrooms,
                'bathroom_ids' => $floorplan_bathroom_ids,
                'best_for' => $floorplan_bestfor,
                'layout' => $floorplan_layout,
                'features' => $floorplan_features,
                'floor_area' => get_field('floor_area', $floorplan_id),
                'living_area' => get_field('living_area', $floorplan_id),
                'plan_length_mm' => $plan_length_mm,
                'plan_width_mm' => $plan_width_mm,
            ];
        }

        wp_reset_postdata();
    }

    /*
     * Floor Area values
     */
    $floor_area_values = [];

    foreach ($floorplans as $floorplan) {

        $floor_area = $floorplan['floor_area'] ?? '';

        if (
            $floor_area !== '' &&
            $floor_area !== null
        ) {
            $floor_area_values[] = $floor_area;
        }
    }

    $floor_area_values = array_unique($floor_area_values);

    sort($floor_area_values, SORT_NUMERIC);

    /*
     * Unique wrapper ID
     */
    $instance_id = 'fpf-' . wp_rand(10000, 999999);

    ob_start();

?>

    <div
        id="<?php echo esc_attr($instance_id); ?>"
        class="fpf-wrapper"
        data-items-per-load="<?php echo esc_attr($atts['posts_per_page']); ?>">

        <!-- Main Filter Card -->
        <div class="fpf-card">

            <!-- Row 1 -->
            <div class="fpf-grid fpf-grid-top">

                <!-- Bedroom & Bathroom -->
                <div class="fpf-field">

                    <label class="fpf-label">
                        Bedroom &amp; Bathroom
                    </label>

                    <div class="fpf-select-container">

                        <button
                            type="button"
                            class="fpf-select-trigger"
                            id="<?php echo esc_attr($instance_id); ?>-bed-trigger">

                            <span class="fpf-trigger-text">
                                Any
                            </span>

                            <i class="fa-solid fa-chevron-down fpf-arrow"></i>

                        </button>

                        <!-- Popover -->
                        <div class="fpf-popover">

                            <!-- Bedrooms -->
                            <div class="fpf-popover-section">

                                <span class="fpf-popover-title">
                                    Bedrooms
                                </span>

                                <div class="fpf-pill-group">

                                    <label class="fpf-radio-label">

                                        <input
                                            type="radio"
                                            name="<?php echo esc_attr($instance_id); ?>-bedrooms"
                                            value="all"
                                            class="fpf-radio-input" checked>

                                        <span class="fpf-pill-btn">
                                            Any
                                        </span>

                                    </label>

                                    <?php if (
                                        !is_wp_error($bedroom_filter_terms) &&
                                        !empty($bedroom_filter_terms)
                                    ) : ?>

                                        <?php
                                        usort(
                                            $bedroom_filter_terms,
                                            function ($a, $b) {

                                                if (strtolower($a->name) === 'studio') {
                                                    return -1;
                                                }

                                                if (strtolower($b->name) === 'studio') {
                                                    return 1;
                                                }

                                                return strnatcasecmp(
                                                    $a->name,
                                                    $b->name
                                                );
                                            }
                                        );
                                        ?>

                                        <?php foreach (
                                            $bedroom_filter_terms as $bedroom_term
                                        ) : ?>

                                            <label class="fpf-radio-label">

                                                <input
                                                    type="radio"
                                                    name="<?php echo esc_attr($instance_id); ?>-bedrooms"
                                                    value="<?php echo esc_attr($bedroom_term->term_id); ?>"
                                                    class="fpf-radio-input">

                                                <span class="fpf-pill-btn">
                                                    <?php echo esc_html($bedroom_term->name); ?>
                                                </span>

                                            </label>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <!-- Bedroom Size -->
                            <div class="fpf-popover-section">

                                <span class="fpf-popover-title">
                                    Bedroom Size
                                </span>

                                <div class="fpf-pill-group">

                                    <label class="fpf-radio-label">
                                        <input
                                            type="radio"
                                            name="<?php echo esc_attr($instance_id); ?>-bedroom-size"
                                            value=""
                                            class="fpf-radio-input"
                                            checked>

                                        <span class="fpf-pill-btn">
                                            Any
                                        </span>
                                    </label>

                                    <label class="fpf-radio-label">
                                        <input
                                            type="radio"
                                            name="<?php echo esc_attr($instance_id); ?>-bedroom-size"
                                            value="queen"
                                            class="fpf-radio-input">

                                        <span class="fpf-pill-btn">
                                            Queen: under 10 m²
                                        </span>
                                    </label>

                                    <label class="fpf-radio-label">
                                        <input
                                            type="radio"
                                            name="<?php echo esc_attr($instance_id); ?>-bedroom-size"
                                            value="king"
                                            class="fpf-radio-input">

                                        <span class="fpf-pill-btn">
                                            King: 10 m² and over
                                        </span>
                                    </label>

                                </div>

                            </div>

                            <!-- Bathrooms -->
                            <div class="fpf-popover-section">
                                <span class="fpf-popover-title">
                                    Bathrooms
                                </span>

                                <div class="fpf-pill-group">

                                    <!-- Any -->
                                    <label class="fpf-radio-label">
                                        <input
                                            type="radio"
                                            name="<?php echo esc_attr($instance_id); ?>-bathrooms"
                                            value=""
                                            class="fpf-radio-input"
                                            checked>

                                        <span class="fpf-pill-btn">
                                            Any
                                        </span>
                                    </label>

                                    <?php if (
                                        !is_wp_error($bathroom_filter_terms) &&
                                        !empty($bathroom_filter_terms)
                                    ) : ?>

                                        <?php foreach ($bathroom_filter_terms as $bathroom_term) : ?>

                                            <label class="fpf-radio-label">
                                                <input
                                                    type="radio"
                                                    name="<?php echo esc_attr($instance_id); ?>-bathrooms"
                                                    value="<?php echo esc_attr($bathroom_term->term_id); ?>"
                                                    class="fpf-radio-input">

                                                <span class="fpf-pill-btn">
                                                    <?php echo esc_html($bathroom_term->name); ?>
                                                </span>
                                            </label>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Floor Area -->
                <div class="fpf-field">

                    <label class="fpf-label">
                        Floor Area
                    </label>

                    <div class="fpf-select-container">

                        <select
                            class="fpf-native-select"
                            name="floor_area">

                            <option value="">
                                Any Size
                            </option>

                            <?php foreach ($floor_area_values as $floor_area) : ?>

                                <option value="<?php echo esc_attr($floor_area); ?>">
                                    <?php echo esc_html($floor_area); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <i class="fa-solid fa-chevron-down fpf-arrow"></i>

                    </div>

                </div>

                <!-- Layout -->
                <div class="fpf-field">

                    <label class="fpf-label">
                        Layout
                    </label>

                    <div class="fpf-select-container">

                        <button
                            type="button"
                            class="fpf-select-trigger"
                            id="<?php echo esc_attr($instance_id); ?>-layout-trigger">

                            <span class="fpf-trigger-text">
                                Any Layout
                            </span>

                            <i class="fa-solid fa-chevron-down fpf-arrow"></i>

                        </button>

                        <!-- Popover -->
                        <div class="fpf-popover fpf-layout-popover">

                            <div class="fpf-popover-section">

                                <span class="fpf-popover-title">
                                    Layouts
                                </span>

                                <div class="fpf-pill-group fpf-layout-pill-group">

                                    <label class="fpf-radio-label">

                                        <input
                                            type="radio"
                                            name="<?php echo esc_attr($instance_id); ?>-layout"
                                            value=""
                                            class="fpf-radio-input" checked>

                                        <span class="fpf-pill-btn">
                                            Any <br> Layout
                                        </span>

                                    </label>

                                    <?php if (
                                        !is_wp_error($layout_filter_terms) &&
                                        !empty($layout_filter_terms)
                                    ) : ?>

                                        <?php
                                        $layout_settings = [
                                            'wide' => [
                                                'order'  => 1,
                                                'icon'   => 'narrow.svg',
                                                'height' => '16px',
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
                                            'compact' => [
                                                'order'  => 2,
                                                'icon'   => 'compact.svg',
                                                'height' => '20px',
                                            ],
                                        ];
                                        ?>

                                        <?php foreach ($layout_filter_terms as $layout_term) : ?>

                                            <?php
                                            $layout_name = strtolower(trim($layout_term->name));

                                            $settings = $layout_settings[$layout_name] ?? [
                                                'order'  => 999,
                                                'icon'   => 'compact.svg',
                                                'height' => '120px',
                                            ];
                                            ?>

                                            <label
                                                class="fpf-radio-label"
                                                style="order:<?php echo esc_attr($settings['order']); ?>;">

                                                <input
                                                    type="radio"
                                                    name="<?php echo esc_attr($instance_id); ?>-layout"
                                                    value="<?php echo esc_attr($layout_term->term_id); ?>"
                                                    class="fpf-radio-input">

                                                <span class="fpf-pill-btn fpf-layout-grid">

                                                    <span
                                                        class="fpf-layout-icon"
                                                        style="
                                                            height:<?php echo esc_attr($settings['height']); ?>;
                                                            width:100%;
                                                            border-radius:0px;
                                                            -webkit-mask-image:url('<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/' . $settings['icon']); ?>');
                                                            mask-image:url('<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/' . $settings['icon']); ?>');
                                                        "></span>

                                                    <span class="fpf-layout-name">
                                                        <?php echo esc_html($layout_term->name); ?>
                                                    </span>

                                                </span>

                                            </label>

                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Best For -->
                <div class="fpf-field">

                    <label class="fpf-label">
                        Best for
                    </label>

                    <div class="fpf-select-container">

                        <select
                            class="fpf-native-select"
                            name="best_for">

                            <option value="">
                                Any
                            </option>

                            <?php if (
                                !is_wp_error($bestfor_filter_terms) &&
                                !empty($bestfor_filter_terms)
                            ) : ?>

                                <?php foreach ($bestfor_filter_terms as $bestfor_term) : ?>

                                    <option
                                        value="<?php echo esc_attr($bestfor_term->term_id); ?>">

                                        <?php echo esc_html($bestfor_term->name); ?>

                                    </option>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </select>

                        <i class="fa-solid fa-chevron-down fpf-arrow"></i>

                    </div>

                </div>

            </div>

            <!-- Row 2 -->
            <div class="fpf-grid fpf-grid-bottom">

                <!-- Features -->
                <div class="fpf-field fpf-features-field">
                    <label class="fpf-label">Features</label>

                    <div class="fpf-select-container fpf-features-container">

                        <button type="button" class="fpf-select-trigger">
                            <span class="fpf-trigger-text">Any Features</span>
                            <i class="fa-solid fa-chevron-down fpf-arrow"></i>
                        </button>

                        <div class="fpf-popover fpf-features-popover">

                            <?php if (
                                !is_wp_error($features_filter_terms) &&
                                !empty($features_filter_terms)
                            ) : ?>

                                <?php foreach ($features_filter_terms as $feature_parent) : ?>

                                    <?php
                                    $feature_children = get_terms([
                                        'taxonomy'   => $features_taxonomy,
                                        'hide_empty' => false,
                                        'orderby'    => 'name',
                                        'order'      => 'ASC',
                                        'parent'     => $feature_parent->term_id,
                                    ]);
                                    ?>

                                    <?php if (
                                        !is_wp_error($feature_children) &&
                                        !empty($feature_children)
                                    ) : ?>

                                        <div class="fpf-popover-col">

                                            <span class="fpf-feature-category-title">
                                                <?php echo esc_html($feature_parent->name); ?>
                                            </span>

                                            <?php foreach ($feature_children as $feature_child) : ?>

                                                <label class="fpf-checkbox-label">

                                                    <input
                                                        type="checkbox"
                                                        class="fpf-checkbox-input"
                                                        name="<?php echo esc_attr($instance_id); ?>-features[]"
                                                        value="<?php echo esc_attr($feature_child->term_id); ?>"
                                                        data-feature-name="<?php echo esc_attr($feature_child->name); ?>">

                                                    <span class="fpf-checkbox-custom"></span>

                                                    <span>
                                                        <?php echo esc_html($feature_child->name); ?>
                                                    </span>

                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Living Area -->
                <div class="fpf-field">

                    <label class="fpf-label">
                        Living Area
                    </label>

                    <div class="fpf-select-container">

                        <select
                            class="fpf-native-select"
                            name="living_area">

                            <option value="">
                                Any
                            </option>

                            <option value="small">
                                Small
                            </option>

                            <option value="large">
                                Large
                            </option>

                        </select>

                        <i class="fa-solid fa-chevron-down fpf-arrow"></i>

                    </div>

                </div>

                <!-- Space Available -->

                <!-- Space Available -->

                <div class="fpf-field">

                    <label class="fpf-label">
                        Space available
                    </label>

                    <div>

                        <input
                            type="number"
                            step="1"
                            min="0"
                            class="fpf-dim-input fpf-space-width"
                            placeholder="Width"
                            aria-label="Available width in millimetres">

                        <span class="fpf-dim-multiply">
                            ×
                        </span>

                        <input
                            type="number"
                            step="1"
                            min="0"
                            class="fpf-dim-input fpf-space-depth"
                            placeholder="Length"
                            aria-label="Available length in millimetres">

                        <span class="fpf-dim-unit">
                            mm
                        </span>

                    </div>

                </div>

            </div>

        </div>

        <!-- Secondary Controls -->
        <div class="fpf-subbar">
            <div class="fpf-subbar-left">

                <!-- Floorplan / Facade Toggle -->
                <div class="fpf-toggle-switch">
                    <input
                        type="radio"
                        id="<?php echo esc_attr($instance_id); ?>-floorplan"
                        name="<?php echo esc_attr($instance_id); ?>-view"
                        value="floorplan" checked>

                    <label
                        for="<?php echo esc_attr($instance_id); ?>-floorplan"
                        class="fpf-toggle-btn">
                        Floorplan
                    </label>

                    <input
                        type="radio"
                        id="<?php echo esc_attr($instance_id); ?>-facade"
                        name="<?php echo esc_attr($instance_id); ?>-view"
                        value="facade">

                    <label
                        for="<?php echo esc_attr($instance_id); ?>-facade"
                        class="fpf-toggle-btn">
                        Facade
                    </label>
                </div>

                <!-- Filter Tags -->
                <div class="fpf-tags-list"></div>

            </div>

            <!-- Sort By -->
            <div class="fpf-subbar-right">

                <label class="fpf-sort-label">
                    Sort By
                </label>

                <div class="fpf-select-container fpf-sort-container">

                    <select
                        class="fpf-native-select"
                        name="sort_by">

                        <option value="default">
                            Default
                        </option>

                        <option value="title_asc">
                            A-Z
                        </option>

                        <option value="size_desc">
                            Biggest to Smallest
                        </option>

                    </select>

                    <i class="fa-solid fa-chevron-down fpf-arrow"></i>

                </div>

            </div>

        </div>

        <!-- Cards Grid -->
        <div class="floor-plan-grid">

            <?php foreach ($floorplans as $floorplan) : ?>

                <article
                    class="floor-plan-card"
                    data-title="<?php echo esc_attr(strtolower($floorplan['title'])); ?>"
                    data-bedrooms="<?php echo esc_attr(implode(',', $floorplan['bedroom_ids'])); ?>"
                    data-bathrooms="<?php echo esc_attr(implode(',', $floorplan['bathroom_ids'])); ?>"
                    data-bedroom-size="<?php echo esc_attr($floorplan['bedroom_size']); ?>"
                    data-best-for="<?php echo esc_attr(implode(',', $floorplan['best_for'])); ?>"
                    data-layout="<?php echo esc_attr(implode(',', $floorplan['layout'])); ?>"
                    data-features="<?php echo esc_attr(implode(',', $floorplan['features'])); ?>"
                    data-living-area="<?php echo esc_attr($floorplan['living_area']); ?>"
                    data-floor-area="<?php echo esc_attr($floorplan['floor_area']); ?>"
                    data-plan-length-mm="<?php echo esc_attr($floorplan['plan_length_mm']); ?>"
                    data-plan-width-mm="<?php echo esc_attr($floorplan['plan_width_mm']); ?>">

                    <a
                        href="<?php echo esc_url($floorplan['url']); ?>"
                        class="floor-plan-card-link">

                        <div class="floor-plan-image">

                            <span class="fpf-image-spinner" aria-hidden="true"></span>

                            <?php if ($floorplan['featured_image']) : ?>

                                <img
                                    class="fpf-image fpf-floorplan-image"
                                    src="<?php echo esc_url($floorplan['featured_image']); ?>"
                                    alt="<?php echo esc_attr($floorplan['title']); ?>">

                            <?php endif; ?>

                            <?php if ($floorplan['design_thumbnail']) : ?>

                                <?php

                                $design_thumbnail = $floorplan['design_thumbnail'];

                                if (is_array($design_thumbnail)) {
                                    $design_thumbnail_url = $design_thumbnail['url'];
                                } else {
                                    $design_thumbnail_url = $design_thumbnail;
                                }

                                ?>

                                <img
                                    class="fpf-image fpf-facade-image"
                                    src="<?php echo esc_url($design_thumbnail_url); ?>"
                                    alt="<?php echo esc_attr($floorplan['title']); ?> Facade">

                            <?php endif; ?>

                        </div>

                        <div class="floor-plan-card-content">

                            <h3 class="floor-plan-title">
                                <?php echo esc_html($floorplan['title']); ?>
                            </h3>
                            <div class="floor-plan-detail-wrapper">
                                <?php if (!empty($floorplan['bedrooms'])) : ?>
                                    <p class="floor-plan-details">
                                        <img class="icons" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/bed-icon.svg'); ?>" alt="Bedroom Icon">
                                        <?php echo esc_html(implode(', ', $floorplan['bedrooms'])); ?> Bed
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($floorplan['bathrooms'])) : ?>
                                    <p class="floor-plan-details">
                                        <img class="icons" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/bath-icon.svg'); ?>" alt="Bathroom Icon">
                                        <?php echo esc_html(implode(', ', $floorplan['bathrooms'])); ?> Bath
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($floorplan['living_area'])) : ?>
                                    <p class="floor-plan-details">
                                        <img class="icons" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/ruler-icon.svg'); ?>" alt="Ruler Icon">
                                        <?php echo esc_html($floorplan['living_area']); ?> m²
                                    </p>
                                <?php endif; ?>
                            </div>
                            <span class="floor-plan-link">
                                View floor plan <i class="fa-solid fa-arrow-right"></i>
                            </span>

                        </div>

                    </a>

                </article>

            <?php endforeach; ?>

        </div>

    </div>

<?php

    return ob_get_clean();
}

add_shortcode(
    'floorplan_filter',
    'custom_floorplan_filter_shortcode'
);
