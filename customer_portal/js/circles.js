// OnScreen Detect
jQuery(function() {
    var jQueryappeared = jQuery('.detect');
    jQuery('.detect').appear();
    jQuery(document.body).on('appear', '.detect', function(e, jQueryaffected) {
        // this code is executed for each appeared element
        jQueryaffected.each(function() {
            // Remove Class Detect
            jQuery('.circle').removeClass('detect');
            // Circle
            jQuery('.circle').each(function(){
                var circle_width = jQuery(this).width();
                var data_value = jQuery(this).attr('data-value');
                var data_color = jQuery(this).attr('data-value-color');
                var cal_dv = parseFloat(data_value/100);
                jQuery(this).circleProgress({
                    thickness: 12,
                    size: circle_width,
                    value: cal_dv,
                    fill: {
                        color: [''+data_color+'']
                    }
                });
            });
            // Plain
            //jQuery('.flat-counters').find('.detect').removeClass('detect');
            jQuery('.flat-counters').each(function() {
                var numbr = jQuery(this).find('.detect').text();
                jQuery(this).find('.detect')
                    .prop('number', 0)
                    .animateNumber(
                        {
                            number: numbr
                        },
                        5000
                    );
                jQuery(this).find('.detect').removeClass('detect');

            });
        });
    });
});