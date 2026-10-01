<?php

function url_breadcrumbs_shortcode()
{
    if (is_front_page() || is_home()) {
        return '';
    }

    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $segments = explode('/', $path);

    // Slugs to exclude from breadcrumb generation (e.g., local subdirectory names)
    $ignore_slugs = array('grannyflatsx');

    // Map single post slugs to their proper archive/plural URL slugs
    $slug_overrides = array(
        'location' => 'locations',
    );

    $breadcrumbs = array('<a href="' . esc_url(home_url('/')) . '">Home</a>');
    $current_path = '';

    foreach ($segments as $segment) {
        $segment_lower = strtolower($segment);

        if (empty($segment) || in_array($segment_lower, $ignore_slugs)) {
            continue;
        }

        if (array_key_exists($segment_lower, $slug_overrides)) {
            $real_slug = $slug_overrides[$segment_lower];
            $current_path .= '/' . $real_slug;
            $title = ucwords(str_replace(array('-', '_'), ' ', $real_slug));
        } else {
            $current_path .= '/' . $segment;
            $title = ucwords(str_replace(array('-', '_'), ' ', $segment));
        }

        $breadcrumbs[] = array(
            'title' => $title,
            'url'   => home_url($current_path . '/')
        );
    }

    // Build final HTML breadcrumb list
    $total = count($breadcrumbs);
    $html_output = array();

    foreach ($breadcrumbs as $index => $item) {
        if ($index === 0) {
            $html_output[] = $item; // Home link
        } elseif ($index === $total - 1) {
            $html_output[] = '<span>' . esc_html($item['title']) . '</span>'; // Current single page title
        } else {
            $html_output[] = '<a href="' . esc_url($item['url']) . '">' . esc_html($item['title']) . '</a>'; // Archive link (/locations/)
        }
    }

    return '<nav class="url-breadcrumbs">' . implode(' / ', $html_output) . '</nav>';
}

add_shortcode('url_breadcrumbs', 'url_breadcrumbs_shortcode');
