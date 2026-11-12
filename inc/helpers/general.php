<?php

function gxf_blog_reading_time_shortcode()
{
    if (!is_singular('post')) {
        return '';
    }

    $post_id = get_the_ID();

    if (!$post_id) {
        $post_id = get_queried_object_id();
    }

    if (!$post_id) {
        return '';
    }

    return sprintf(
        '<span class="gxf-reading-time" data-post-id="%d">1 min</span>',
        absint($post_id)
    );
}
add_shortcode('blog_reading_time', 'gxf_blog_reading_time_shortcode');
