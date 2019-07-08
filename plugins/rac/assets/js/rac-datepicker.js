
jQuery(document).ready(function () {
    jQuery('.rac_date').datepicker();
    jQuery('#rac_template_seg_cart_from_date').datepicker({
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            var maxDate = new Date(Date.parse(selectedDate));
            maxDate.setDate(maxDate.getDate() + 1);
            jQuery('#rac_template_seg_cart_to_date').datepicker('option', 'minDate', maxDate);
        }
    });
    jQuery('#rac_template_seg_cart_to_date').datepicker({
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            jQuery('#rac_template_seg_cart_from_date').datepicker('option', 'maxDate', selectedDate);
        }
    });

    jQuery('#rac_from_date').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        onClose: function (selectedDate) {
            var maxDate = new Date(Date.parse(selectedDate));
            maxDate.setDate(maxDate.getDate() + 1);
            jQuery('#rac_to_date').datepicker('option', 'minDate', maxDate);
        }
    });
    jQuery('#rac_to_date').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        onClose: function (selectedDate) {
            jQuery('#rac_from_date').datepicker('option', 'maxDate', selectedDate);
        }
    });
});
