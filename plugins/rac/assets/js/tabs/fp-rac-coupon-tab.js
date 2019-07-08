jQuery(function ($) {

    var Coupon_Tab = {
        init: function () {

            this.trigger_on_page_load();
            
            $(document).on('change', '#rac_prefix_coupon', this.toggle_prefix_coupon_option);
            $(document).on('change', '#rac_delete_coupon_after_use', this.toggle_delete_coupon_option);
            $(document).on('change', '#rac_delete_coupon_after_use_based_on', this.toggle_delete_coupon_status_option);
        },
        trigger_on_page_load: function () {
            Coupon_Tab.get_prefix_coupon_option('#rac_prefix_coupon');
            Coupon_Tab.get_delete_coupon_option('#rac_delete_coupon_after_use');
        },
        toggle_prefix_coupon_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Coupon_Tab.get_prefix_coupon_option($this);
        },
        toggle_delete_coupon_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Coupon_Tab.get_delete_coupon_option($this);
        },
        toggle_delete_coupon_status_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Coupon_Tab.get_delete_coupon_status_option($this);
        },
        get_prefix_coupon_option: function ($this) {
            if ($($this).val() === '1') {
                $('#rac_manual_prefix_coupon_code').closest('tr').hide();
            } else {
                $('#rac_manual_prefix_coupon_code').closest('tr').show();
            }
        },
        get_delete_coupon_option: function ($this) {
            if ($($this).is(':checked')) {
                $('.rac_delete_coupon_by').closest('tr').show();
                Coupon_Tab.get_delete_coupon_status_option('#rac_delete_coupon_after_use_based_on');
            } else {
                $('.rac_delete_coupon_by').closest('tr').hide();
            }
        },
        get_delete_coupon_status_option: function ($this) {
            if ($($this).val() === '2') {
                $('.rac_delete_coupon_by_status').closest('tr').show();
            } else {
                $('.rac_delete_coupon_by_status').closest('tr').hide();
            }
        }
    };
    Coupon_Tab.init();
});
