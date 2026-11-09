<?php

if (!defined('ABSPATH')) exit;

function gxf_project_gallery_slider_shortcode()
{

    if (!function_exists('get_field')) {
        return '';
    }

    $post_id = get_the_ID();

    if (!$post_id) {
        return '';
    }

    $images = [];

    $add_image = function ($image) use (&$images) {

        if (empty($image)) {
            return;
        }

        if (is_numeric($image)) {
            $image_id = (int) $image;
            $url = wp_get_attachment_image_url($image_id, 'full');
            $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        } elseif (is_array($image)) {
            $image_id = !empty($image['ID']) ? (int) $image['ID'] : 0;
            $url = !empty($image['url']) ? $image['url'] : '';
            $alt = !empty($image['alt']) ? $image['alt'] : '';
        } elseif (is_string($image)) {
            $image_id = 0;
            $url = $image;
            $alt = '';
        } else {
            return;
        }

        if (!$url) {
            return;
        }

        $images[] = [
            'url' => esc_url($url),
            'alt' => sanitize_text_field($alt),
        ];
    };

    $images = [];

    // 1. Project Main Image - FIRST
    $main_image = get_field('project_main_image', $post_id);

    if ($main_image) {
        $add_image($main_image);
    }

    // 2. Studio Floor Plan - SECOND
    $floorplan = get_field('project_floorplan_image', $post_id);

    if ($floorplan) {
        $add_image($floorplan);
    }

    // 3. Project Gallery Photos - LAST
    $gallery = get_field('projects_gallery_photos', $post_id);

    if (!empty($gallery) && is_array($gallery)) {
        foreach ($gallery as $gallery_image) {
            $add_image($gallery_image);
        }
    }

    $slider_id = wp_unique_id('gxf-project-gallery-');

    ob_start();

    static $assets_loaded = false;

    if (!$assets_loaded) :
        $assets_loaded = true;
?>

    <?php endif; ?>

    <div class="gxf-project-gallery" id="<?php echo esc_attr($slider_id); ?>">

        <div class="gxf-gallery-main">

            <?php foreach ($images as $index => $image) : ?>

                <div class="gxf-gallery-slide <?php echo $index === 0 ? 'is-active' : ''; ?>">

                    <img
                        src="<?php echo esc_url($image['url']); ?>"
                        alt="<?php echo esc_attr($image['alt']); ?>"
                        loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                        <?php echo $index === 0 ? 'fetchpriority="high"' : ''; ?>>

                </div>

            <?php endforeach; ?>

        </div>

        <?php if (count($images) > 1) : ?>

            <button type="button" class="gxf-gallery-nav gxf-gallery-prev" aria-label="Previous image">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </button>

            <button type="button" class="gxf-gallery-nav gxf-gallery-next" aria-label="Next image">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </button>

        <?php endif; ?>

        <?php if (count($images) > 1) : ?>

            <div class="gxf-gallery-thumbnails">

                <?php foreach ($images as $index => $image) : ?>

                    <button
                        type="button"
                        class="gxf-gallery-thumb <?php echo $index === 0 ? 'is-active' : ''; ?>"
                        data-index="<?php echo esc_attr($index); ?>"
                        aria-label="<?php echo esc_attr('Show image ' . ($index + 1)); ?>">

                        <img
                            src="<?php echo esc_url($image['url']); ?>"
                            alt="<?php echo esc_attr($image['alt']); ?>"
                            loading="lazy">

                    </button>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

<?php

    return ob_get_clean();
}

add_shortcode('project_gallery_slider', 'gxf_project_gallery_slider_shortcode');
