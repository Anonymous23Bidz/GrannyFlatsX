<?php

/**
 * 1. Filter Form Shortcode
 * Usage: [floor_plan_filter]
 */
function floor_plan_filter_shortcode()
{
    $features_field     = acf_get_field('features_filter');
    $floor_area_field   = acf_get_field('floor_area');
    $design_style_field = acf_get_field('design_style');

    $features      = ! empty($features_field['choices']) ? $features_field['choices'] : array();
    $floor_areas   = ! empty($floor_area_field['choices']) ? $floor_area_field['choices'] : array();
    $design_styles = ! empty($design_style_field['choices']) ? $design_style_field['choices'] : array();

    ob_start();
?>
    <div class="floor-plan-filter-wrapper">
        <div class="floor-plan-filters">
            <div class="floor-filter-field">
                <label for="floor-plan-area">Floor area</label>
                <select id="floor-plan-area">
                    <option value="">All floor areas</option>
                    <?php foreach ($floor_areas as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="floor-filter-field">
                <label for="floor-plan-features">Features</label>
                <select id="floor-plan-features">
                    <option value="">All features</option>
                    <?php foreach ($features as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="floor-filter-field">
                <label for="floor-plan-style">Design style</label>
                <select id="floor-plan-style">
                    <option value="">All design styles</option>
                    <?php foreach ($design_styles as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="button" class="elementor-button elementor-size-sm floor-plan-view-button" id="floor-plan-apply">
                View Designs
            </button>

            <button type="button" class="floor-plan-reset" id="floor-plan-reset">
                Reset filters
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const areaSelect = document.getElementById('floor-plan-area');
            const featuresSelect = document.getElementById('floor-plan-features');
            const styleSelect = document.getElementById('floor-plan-style');
            const applyButton = document.getElementById('floor-plan-apply');
            const resetButton = document.getElementById('floor-plan-reset');

            if (!areaSelect || !featuresSelect || !styleSelect) {
                return;
            }

            function loadFloorPlans(showAll = false) {
                const results = document.getElementById('floor-plan-results');
                if (!results) return;

                const formData = new FormData();
                formData.append('action', 'filter_floor_plans');
                formData.append('nonce', '<?php echo esc_js(wp_create_nonce('floor_plan_filter_nonce')); ?>');
                formData.append('floor_area', areaSelect.value);
                formData.append('features', featuresSelect.value);
                formData.append('design_style', styleSelect.value);

                if (showAll) {
                    formData.append('show_all', '1');
                }

                results.innerHTML = '<div class="floor-plan-loading">Loading floor plans...</div>';

                fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            results.innerHTML = '<p>Unable to load floor plans.</p>';
                            return;
                        }
                        results.innerHTML = data.data.html;
                    })
                    .catch(error => {
                        console.error(error);
                        results.innerHTML = '<p>Something went wrong. Please try again.</p>';
                    });
            }

            // Event listener delegation for dynamically added "View All" button
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'floor-plan-view-all') {
                    loadFloorPlans(true);
                }
            });

            if (applyButton) {
                applyButton.addEventListener('click', function() {
                    loadFloorPlans(false);
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', function() {
                    areaSelect.value = '';
                    featuresSelect.value = '';
                    styleSelect.value = '';
                    loadFloorPlans(false);
                });
            }

            // Initial load (4 items)
            loadFloorPlans(false);
        });
    </script>
<?php
    return ob_get_clean();
}
add_shortcode('floor_plan_filter', 'floor_plan_filter_shortcode');


/**
 * 2. Results Container Shortcode
 * Usage: [floor_plan_results]
 */
function floor_plan_results_shortcode()
{
    ob_start();
?>
    <div id="floor-plan-results" class="floor-plan-results">
        <div class="floor-plan-loading">Loading floor plans...</div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('floor_plan_results', 'floor_plan_results_shortcode');


/**
 * 3. AJAX Callback Handler
 */
function filter_floor_plans_ajax()
{
    check_ajax_referer('floor_plan_filter_nonce', 'nonce');

    $floor_area   = isset($_POST['floor_area']) ? sanitize_text_field(wp_unslash($_POST['floor_area'])) : '';
    $features     = isset($_POST['features']) ? sanitize_text_field(wp_unslash($_POST['features'])) : '';
    $design_style = isset($_POST['design_style']) ? sanitize_text_field(wp_unslash($_POST['design_style'])) : '';
    $show_all     = isset($_POST['show_all']) && $_POST['show_all'] === '1';

    $meta_query = array('relation' => 'AND');

    if ($floor_area !== '') {
        $meta_query[] = array(
            'key'     => 'floor_area',
            'value'   => $floor_area,
            'compare' => '=',
        );
    }

    if ($features !== '') {
        $meta_query[] = array(
            'key'     => 'features_filter',
            'value'   => $features,
            'compare' => '=',
        );
    }

    if ($design_style !== '') {
        $meta_query[] = array(
            'key'     => 'design_style',
            'value'   => $design_style,
            'compare' => '=',
        );
    }

    $has_filters = (
        $floor_area !== '' ||
        $features !== '' ||
        $design_style !== ''
    );

    $posts_per_page = ($has_filters || $show_all) ? -1 : 4;

    $query = new WP_Query(array(
        'post_type'      => 'floor-plans',
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'meta_query'     => $meta_query,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ));

    ob_start();

    if ($query->have_posts()) : ?>
        <div class="floor-plan-grid">
            <?php while ($query->have_posts()) : $query->the_post();
                $post_id            = get_the_ID();
                $title              = get_the_title();
                $permalink          = get_permalink();
                $image              = get_field('design_image', $post_id);
                $features           = get_field('features_filter', $post_id);
                $floor_area_value   = get_field('floor_area', $post_id);
                $design_style_value = get_field('design_style', $post_id);
            ?>

                <article class="floor-plan-card">

                    <a href="<?php echo esc_url($permalink); ?>" class="floor-plan-card-link">

                        <?php if ($image) : ?>
                            <div class="floor-plan-image">
                                <img
                                    class="bedroom-image-list"
                                    src="<?php echo esc_url($image['sizes']['medium']); ?>"
                                    alt="<?php echo esc_attr($title); ?>"
                                    loading="lazy">
                            </div>
                        <?php endif; ?>

                        <div class="floor-plan-card-content">

                            <h3 class="floor-plan-title">
                                <?php echo esc_html($title); ?>
                            </h3>

                            <?php if ($features) : ?>
                                <div class="floor-plan-meta">
                                    <span class="floor-plan-icon">
                                        <i class="fa-solid fa-bed"></i>
                                    </span>
                                    <span><?php echo esc_html($features); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($floor_area_value) : ?>
                                <div class="floor-plan-meta">
                                    <span class="floor-plan-icon">
                                        <i class="fa-solid fa-bath"></i>
                                    </span>
                                    <span><?php echo esc_html($floor_area_value); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($design_style_value) : ?>
                                <div class="floor-plan-meta">
                                    <span class="floor-plan-icon">
                                        <i class="fa-solid fa-arrow-up-long"></i>
                                    </span>
                                    <span><?php echo esc_html($design_style_value); ?></span>
                                </div>
                            <?php endif; ?>

                            <span class="floor-plan-link">
                                View floor plan
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>

                        </div>

                    </a>

                </article>

            <?php endwhile; ?>
        </div>

        <?php if (! $show_all && $query->found_posts > 4) : ?>
            <div class="floor-plan-view-all-wrapper" style="text-align: center; margin-top: 40px;">
                <button type="button" id="floor-plan-view-all" class="floor-plan-view-all-button">
                    View All Floor Plans (<?php echo esc_html($query->found_posts); ?>)
                </button>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="floor-plan-no-results">
            <h3>No floor plans found</h3>
            <p>Try changing your filters.</p>
        </div>
<?php endif;

    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success(array(
        'html'  => $html,
        'count' => $query->found_posts,
    ));
}

add_action('wp_ajax_filter_floor_plans', 'filter_floor_plans_ajax');
add_action('wp_ajax_nopriv_filter_floor_plans', 'filter_floor_plans_ajax');
