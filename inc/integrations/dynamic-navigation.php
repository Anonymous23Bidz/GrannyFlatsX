<?php
function dynamic_guide_toc_shortcode()
{
    return '<div class="in-this-guide-wrapper" id="dynamic-guide-toc">
        <ul class="toc-list"></ul>
    </div>';
}
add_shortcode('guide_toc', 'dynamic_guide_toc_shortcode');
