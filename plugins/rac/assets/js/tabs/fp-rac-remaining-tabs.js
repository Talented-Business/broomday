/* global fp_rac_remaining_tabs_obj */

jQuery(function ($) {

    var Remaining_tabs = {
        init: function () {
            this.trigger_on_page_load();
        },
        trigger_on_page_load: function () {
            Remaining_tabs.initialize_select_event();
        },
        initialize_select_event: function () {
            if (fp_rac_remaining_tabs_obj.rac_wc_version <= parseFloat('2.2.0')) {
                $('#custom_user_role').chosen();
                $('#rac_mailcartlist_change').chosen();
                $('#rac_delete_cart_selection').chosen();
                $('#rac_dont_capture_for_option').chosen();
                $('#rac_delete_coupon_after_use_based_on_status').chosen();
                $('#rac_template_seg_selected_user_role').chosen();
                $('#custom_user_role_for_restrict_in_cart_list').chosen();
                $('#rac_template_seg_selected_product_in_cart').chosen();
                $('#rac_select_category_to_enable_redeeming').chosen();
                $('#rac_template_coupon_include_categories').chosen();
                $('#rac_template_coupon_exclude_categories').chosen();
                $('#rac_exclude_category_to_enable_redeeming').chosen();
                $('#rac_template_seg_selected_category_in_cart').chosen();
                $('#rac_template_seg_selected_category_not_in_cart').chosen();
            } else {
                $('#rac_template_seg_selected_user_role').select2();
                $('#custom_user_role').select2();
                $('#rac_delete_cart_selection').select2();
                $('#rac_dont_capture_for_option').select2();
                $('#rac_delete_coupon_after_use_based_on_status').select2();
                $('#custom_user_role_for_restrict_in_cart_list').select2();
                $('#rac_mailcartlist_change').select2();
                $('#rac_template_coupon_include_categories').select2();
                $('#rac_template_coupon_exclude_categories').select2();
                $('#rac_select_category_to_enable_redeeming').select2();
                $('#rac_exclude_category_to_enable_redeeming').select2();
                $('#rac_template_seg_selected_category_in_cart').select2();
                $('#rac_template_seg_selected_category_not_in_cart').select2();
            }
        },
    };
    Remaining_tabs.init();
});