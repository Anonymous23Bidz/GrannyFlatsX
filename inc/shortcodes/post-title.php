<?php
if (! defined('ABSPATH')) {
    exit;
}

// Custom shortcode to return current Post Title
add_shortcode('post_title', function () {
    return get_the_title();
});

// add_action('admin_init', 'update_media_metadata_from_csv');
// function update_media_metadata_from_csv()
// {
//     // Updated to v1 so it runs fresh
//     if (get_option('media_update_completed_v2')) {
//         return;
//     }

//     $updates = [
//         13287 => ['title' => 'Granny Flat Floor Plan Seven Hills', 'alt' => 'granny flat floor plan seven hills'],
//         13284 => ['title' => 'Granny Flat Floor Plan Knox', 'alt' => 'granny flat floor plan knox']
//     ];

//     foreach ($updates as $attachment_id => $data) {
//         wp_update_post([
//             'ID'         => $attachment_id,
//             'post_title' => $data['title'],
//         ]);
//         update_post_meta($attachment_id, '_wp_attachment_image_alt', $data['alt']);
//     }

//     update_option('media_update_completed_v2', true);
// }
