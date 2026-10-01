<?php

// =====================================================
// FAQ Shortcode
//
// ACF Repeater: faq
// Sub-fields: question, answer
//
// Usage: [custom_faq_accordion]
// =====================================================

function faq_shortcode()
{
    $post_id = get_the_ID();

    if (
        !function_exists('have_rows') ||
        !have_rows('faq', $post_id)
    ) {
        return '';
    }

    ob_start();
?>

    <div class="faq">

        <?php
        $faq_index = 0;

        while (have_rows('faq', $post_id)) :
            the_row();

            $question = get_sub_field('question');
            $answer   = get_sub_field('answer');

            if (!$question || !$answer) {
                continue;
            }

            // First item (index 0) will be active
            $is_active = ($faq_index === 0);
        ?>

            <div class="faq-item <?php echo $is_active ? 'is-active' : ''; ?>">

                <button
                    type="button"
                    class="faq-question"
                    aria-expanded="<?php echo $is_active ? 'true' : 'false'; ?>">
                    <span class="faq-question__text">
                        <?php echo esc_html($question); ?>
                    </span>

                    <span class="faq-question__icon" aria-hidden="true">+</span>
                </button>

                <div class="faq-answer">
                    <div class="faq-answer__inner">
                        <?php echo wp_kses_post(wpautop($answer)); ?>
                    </div>
                </div>

            </div>

        <?php
            $faq_index++;
        endwhile;
        ?>

    </div>

<?php

    return ob_get_clean();
}
add_shortcode('custom_faq_accordion', 'faq_shortcode');

?>