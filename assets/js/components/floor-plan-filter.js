jQuery(document).ready(function ($) {

    const $filterForm = $('#floor-plan-filter');
    const $results = $('#floor-plan-results');
    const $count = $('#design-count');
    const $button = $('#view-designs-btn');
    const $reset = $('#reset-floor-filters');

    if (!$filterForm.length) {
        return;
    }

    /**
     * Run AJAX filter.
     */
    function filterFloorPlans() {

        const floorArea = $('#floor-area').val();
        const features = $('#features').val();
        const designStyle = $('#design-style').val();

        $button.prop('disabled', true);
        $button.addClass('loading');

        $results.addClass('loading');

        $.ajax({

            url: floorPlanFilter.ajax_url,

            type: 'POST',

            data: {
                action: 'filter_floor_plans',
                nonce: floorPlanFilter.nonce,

                floor_area: floorArea,
                features: features,
                design_style: designStyle
            },

            success: function (response) {

                if (response.success) {

                    $results.html(response.data.html);

                    $count.text(response.data.count);

                } else {

                    $results.html(
                        '<p>Something went wrong. Please try again.</p>'
                    );

                    $count.text('0');
                }
            },

            error: function () {

                $results.html(
                    '<p>Unable to load designs. Please try again.</p>'
                );

                $count.text('0');
            },

            complete: function () {

                $button.prop('disabled', false);
                $button.removeClass('loading');

                $results.removeClass('loading');
            }
        });
    }


    /**
     * View Designs button.
     */
    $button.on('click', function (e) {

        e.preventDefault();

        filterFloorPlans();

    });


    /**
     * Reset filters.
     */
    $reset.on('click', function (e) {

        e.preventDefault();

        $filterForm[0].reset();

        filterFloorPlans();

    });

});