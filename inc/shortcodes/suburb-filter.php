<?php

function custom_suburb_filter_shortcode($atts)
{
    $atts = shortcode_atts([
        'post_type'      => 'location',
        'taxonomy'       => 'broader_lga',
        'posts_per_page' => 8,
    ], $atts, 'suburb_filter');

    $lga_terms = get_terms([
        'taxonomy'   => $atts['taxonomy'],
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    $query = new WP_Query([
        'post_type'      => $atts['post_type'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    $locations = [];

    if ($query->have_posts()) {

        while ($query->have_posts()) {

            $query->the_post();

            $post_id = get_the_ID();

            $terms = get_the_terms(
                $post_id,
                $atts['taxonomy']
            );

            $location_lgas = [];

            if (!is_wp_error($terms) && !empty($terms)) {

                foreach ($terms as $term) {
                    $location_lgas[] = $term->term_id;
                }
            }

            $locations[] = [
                'title' => get_the_title(),
                'url'   => get_permalink($post_id),
                'image' => get_the_post_thumbnail_url($post_id, 'medium') ?: '',
                'lgas'  => $location_lgas,
            ];
        }

        wp_reset_postdata();
    }

    $instance_id = 'location-locator-' . wp_rand(10000, 999999);

    ob_start();

?>

    <div
        id="<?php echo esc_attr($instance_id); ?>"
        class="custom-location-locator"
        data-items-per-load="<?php echo esc_attr($atts['posts_per_page']); ?>">

        <div class="location-locator-sidebar">

            <div class="suburb-filter-wrapper">

                <div class="location-locator-filter">

                    <!-- SUBURB SEARCH -->
                    <div class="location-locator-field location-locator-suburb-field">

                        <label for="<?php echo esc_attr($instance_id); ?>-suburb">
                            Search
                        </label>

                        <input
                            type="text"
                            id="<?php echo esc_attr($instance_id); ?>-suburb"
                            class="location-locator-input"
                            placeholder="Search suburb..."
                            autocomplete="off">

                        <div class="location-locator-suggestions"></div>
                    </div>

                    <!-- LGA FILTER -->
                    <div class="location-locator-field">

                        <label for="<?php echo esc_attr($instance_id); ?>-lga">
                            Search Region
                        </label>

                        <div class="location-locator-custom-select">

                            <button
                                type="button"
                                class="location-locator-select-trigger">

                                <span class="location-locator-select-value">
                                    All LGAs
                                </span>

                                <span class="location-locator-select-arrow">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>

                            </button>

                            <div class="location-locator-select-options">

                                <div
                                    class="location-locator-select-option is-selected"
                                    data-value="">
                                    All LGAs
                                </div>

                                <?php if (!is_wp_error($lga_terms) && !empty($lga_terms)) : ?>

                                    <?php foreach ($lga_terms as $term) : ?>

                                        <div
                                            class="location-locator-select-option"
                                            data-value="<?php echo esc_attr($term->term_id); ?>">
                                            <?php echo esc_html($term->name); ?>
                                        </div>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </div>

                            <input
                                type="hidden"
                                class="location-locator-select"
                                value="">

                        </div>

                    </div>

                    <button
                        type="button"
                        class="location-locator-search">

                        Search

                    </button>

                </div>

            </div>

            <div class="floor-plan-grid">

                <?php foreach ($locations as $index => $location) : ?>

                    <article
                        class="floor-plan-card location-item"
                        data-title="<?php echo esc_attr(strtolower($location['title'])); ?>"
                        data-lgas="<?php echo esc_attr(implode(',', $location['lgas'])); ?>">

                        <a
                            href="<?php echo esc_url($location['url']); ?>"
                            class="floor-plan-card-link">

                            <?php

                            $fallback_images = [

                                'https://grannyflatsx.com.au/wp-content/uploads/2026/08/Cawarra-scaled.webp',

                                'https://grannyflatsx.com.au/wp-content/uploads/2026/08/Clement-scaled.webp',

                                'https://grannyflatsx.com.au/wp-content/uploads/2026/08/Dowding-scaled.webp',

                                'https://grannyflatsx.com.au/wp-content/uploads/2026/08/Frazer-scaled.webp',

                            ];

                            $fallback_image = $fallback_images[$index % count($fallback_images)];

                            ?>

                            <?php if (!empty($location['image'])) : ?>

                                <div class="floor-plan-image">

                                    <img
                                        class="floor-plan-location-image"
                                        src="<?php echo esc_url($location['image']); ?>"
                                        alt="<?php echo esc_attr($location['title']); ?>"
                                        loading="lazy">

                                </div>

                            <?php else : ?>

                                <div class="floor-plan-image">

                                    <img
                                        class="floor-plan-location-image"
                                        src="<?php echo esc_url($fallback_image); ?>"
                                        alt="<?php echo esc_attr($location['title']); ?>"
                                        loading="lazy">

                                </div>

                            <?php endif; ?>

                            <div class="floor-plan-card-content">

                                <h3 class="floor-plan-title">

                                    <?php echo esc_html($location['title']); ?>

                                </h3>

                                <span class="floor-plan-link">

                                    View suburb

                                    <i class="fa-solid fa-arrow-right"></i>

                                </span>

                            </div>

                        </a>

                    </article>

                <?php endforeach; ?>

                <div class="location-no-results" style="display:none;">

                    No locations found.

                </div>

            </div>

            <div class="location-load-more">

                <button
                    type="button"
                    class="location-load-more-button">

                    Show More

                </button>

            </div>

        </div>

    </div>

<?php

    return ob_get_clean();
}

add_shortcode(
    'suburb_filter',
    'custom_suburb_filter_shortcode'
);
