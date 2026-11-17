<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('gxf_site_check', 'gxf_site_check_shortcode');

function gxf_site_check_shortcode($atts)
{

    $atts = shortcode_atts([
        'height' => '850',
        'source' => 'wordpress',
    ], $atts, 'gxf_site_check');

    $height = absint($atts['height']);
    $height = max(600, min($height, 2000));

    $iframe_id = wp_unique_id('gxf-site-check-');

    $src = add_query_arg(
        'src',
        sanitize_key($atts['source']),
        'https://check.grannyflatsx.com.au/embed'
    );

    ob_start();
?>

    <div class="gxf-site-check">
        <iframe
            id="<?php echo esc_attr($iframe_id); ?>"
            src="<?php echo esc_url($src); ?>"
            title="Granny Flat X Property Site Checker"
            loading="lazy"
            allow="geolocation"
            style="width:100%;height:<?php echo esc_attr($height); ?>px;border:0;display:block;"></iframe>
    </div>

    <script>
        (function() {

            const iframe = document.getElementById(
                <?php echo wp_json_encode($iframe_id); ?>
            );

            if (!iframe) return;

            window.addEventListener('message', function(event) {

                if (event.origin !== 'https://check.grannyflatsx.com.au') {
                    return;
                }

                if (event.source !== iframe.contentWindow) {
                    return;
                }

                const height = Number(event.data?.height);

                if (!Number.isFinite(height) || height < 600 || height > 12000) {
                    return;
                }

                iframe.style.height = height + 'px';

            });

        })();
    </script>

<?php

    return ob_get_clean();
}
