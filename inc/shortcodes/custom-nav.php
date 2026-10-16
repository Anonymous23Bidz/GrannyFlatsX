<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('gxf_mega_menu', function ($atts) {
    $atts = shortcode_atts([
        'menu' => 'Primary Menu',
    ], $atts, 'gxf_mega_menu');

    $menu = wp_get_nav_menu_object($atts['menu']);

    if (!$menu) {
        return '';
    }

    $items = wp_get_nav_menu_items($menu->term_id);

    if (!$items) {
        return '';
    }

    $tree = [];
    $lookup = [];

    foreach ($items as $item) {
        $item->children = [];
        $lookup[$item->ID] = $item;
    }

    foreach ($items as $item) {
        $parent_id = (int) $item->menu_item_parent;

        if ($parent_id && isset($lookup[$parent_id])) {
            $lookup[$parent_id]->children[] = $item;
        } else {
            $tree[] = $item;
        }
    }

    $id = wp_unique_id('gxf-mega-');


    $render_link = function ($item, $class) {
        $target = $item->target ?: '_self';

        $rel = $target === '_blank'
            ? ' rel="noopener noreferrer"'
            : '';

        $classes = array_filter([
            $class,
            !empty($item->classes) ? implode(' ', array_filter($item->classes)) : '',
        ]);

        return sprintf(
            '<a class="%s" href="%s" target="%s"%s>%s</a>',
            esc_attr(implode(' ', $classes)),
            esc_url($item->url),
            esc_attr($target),
            $rel,
            esc_html($item->title)
        );
    };


    ob_start();
?>

    <nav id="<?php echo esc_attr($id); ?>"
        class="gxf-mega-nav"
        aria-label="Main navigation">

        <ul class="gxf-top-menu">

            <?php foreach ($tree as $top) : ?>
                <?php $has_children = !empty($top->children); ?>

                <li class="gxf-top-item <?php echo $has_children ? 'has-mega' : ''; ?>">

                    <?php if ($has_children) : ?>

                        <div class="gxf-top-trigger"
                            role="button"
                            tabindex="0"
                            aria-expanded="false">

                            <span class="gxf-top-title">
                                <?php echo esc_html($top->title); ?>
                            </span>

                            <i class="fa-solid fa-chevron-down gxf-top-arrow"></i>

                        </div>

                        <div class="gxf-mobile-submenu">

                            <?php foreach ($top->children as $parent) : ?>

                                <div class="gxf-mobile-group">

                                    <?php echo $render_link($parent, 'gxf-mobile-parent-link'); ?>

                                    <?php if (!empty($parent->children)) : ?>

                                        <div class="gxf-mobile-child-links">

                                            <?php foreach ($parent->children as $child) : ?>
                                                <?php echo $render_link($child, 'gxf-mobile-child-link'); ?>
                                            <?php endforeach; ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>

                        </div>

                        <?php
                        $has_nested_children = (bool) array_filter(
                            $top->children,
                            function ($item) {
                                return !empty($item->children);
                            }
                        );
                        ?>

                        <div class="gxf-mega-panel <?php echo !$has_nested_children ? 'no-children' : ''; ?>">

                            <div class="gxf-mega-left"
                                style="<?php echo !empty($parent->children) ? 'border-radius: 8px 0 0 8px;' : 'border-radius: 8px 8px;'; ?>">
                                <?php foreach ($top->children as $index => $parent) : ?>

                                    <div class="gxf-parent-row"
                                        data-index="<?php echo esc_attr($index); ?>"
                                        data-has-children="<?php echo !empty($parent->children) ? 'true' : 'false'; ?>">

                                        <?php echo $render_link($parent, 'gxf-parent-link'); ?>
                                        <i class="fa-solid fa-chevron-right gxf-row-arrow"></i>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                            <?php if (!empty($parent->children)) : ?>
                                <div class="gxf-mega-right">
                                    <?php foreach ($top->children as $index => $parent) : ?>
                                        <div class="gxf-child-group"
                                            data-index="<?php echo esc_attr($index); ?>">
                                            <?php foreach ($parent->children as $child) : ?>
                                                <div class="child-menu-wrapper">
                                                    <?php echo $render_link($child, 'gxf-child-link'); ?>
                                                    <i class="fa-solid fa-chevron-right gxf-row-arrow"></i>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>

                    <?php else : ?>

                        <?php echo $render_link($top, 'gxf-top-link'); ?>

                    <?php endif; ?>

                </li>

            <?php endforeach; ?>

        </ul>

    </nav>

<?php
    return ob_get_clean();
});
