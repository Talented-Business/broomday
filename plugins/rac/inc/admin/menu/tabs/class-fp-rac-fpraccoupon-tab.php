<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Coupon_Tab')) {

    /**
     * FP_RAC_Coupon_Tab Class.
     */
    class FP_RAC_Coupon_Tab {

        public static function init() {
            add_action('fp_rac_default_settings_fpraccoupon', array(__CLASS__, 'fprac_coupon_default_settings'));
            add_action('woocommerce_update_options_fpraccoupon', array(__CLASS__, 'save_product_to_exclude'));
            add_action('woocommerce_update_options_fpraccoupon', array(__CLASS__, 'save_product_to_include'));
            add_action('fp_rac_display_buttons_fpraccoupon', array(__CLASS__, 'fp_rac_admin_setting_buttons'));
            add_action('woocommerce_update_options_fpraccoupon', array(__CLASS__, 'fp_rac_update_options_coupon'));
            add_action('woocommerce_fprac_settings_tabs_fpraccoupon', array(__CLASS__, 'fp_rac_admin_setting_coupon'));
            add_action('woocommerce_admin_field_rac_coupon_exclude_products', array(__CLASS__, 'rac_select_product_to_exclude'));
            add_action('woocommerce_admin_field_rac_coupon_include_products', array(__CLASS__, 'rac_select_product_to_include'));
        }

        public static function fp_rac_menu_options_coupon_gen() {
            $categorylist = fp_rac_get_category();
            $orderlist_combine = fp_rac_get_order_status();
            return apply_filters('woocommerce_fpraccoupon_settings', array(
                array(
                    'name' => __('Coupon Code Creation Global Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_coupon',
                ),
                array(
                    'name' => __('Coupon Creation Mode', 'recoverabandoncart'),
                    'desc' => __('"Default" - same coupon code will be sent in all templates unless the user has used the coupon. "Configure on Each Template" - coupons with different values can be configured for each template', 'recoverabandoncart'),
                    'id' => 'rac_coupon_creation_mode',
                    'desc_tip' => true,
                    'type' => 'select',
                    'options' => array(
                        'default' => __('Default', 'recoverabandoncart'),
                        'template' => __('Configure on Each Template', 'recoverabandoncart'),
                    ),
                    'std' => '1',
                    'default' => '1',
                    'clone_id' => 'rac_coupon_creation_mode',
                ),
                array(
                    'name' => __('Coupon code Prefix Type', 'recoverabandoncart'),
                    'desc' => __('Select Prefix Text in Coupon Code', 'recoverabandoncart'),
                    'id' => 'rac_prefix_coupon',
                    'desc_tip' => true,
                    'type' => 'select',
                    'options' => array(
                        '1' => __('Default', 'recoverabandoncart'),
                        '2' => __('Custom', 'recoverabandoncart'),
                    ),
                    'std' => '1',
                    'default' => '1',
                    'clone_id' => 'rac_prefix_coupon',
                ),
                array(
                    'name' => __('Coupon Code Prefix', 'recoverabandoncart'),
                    'desc' => __('Enter Custom Prefix Text for Coupon Code', 'recoverabandoncart'),
                    'id' => 'rac_manual_prefix_coupon_code',
                    'css' => 'rac_manual_prefix',
                    'desc_tip' => true,
                    'type' => 'text',
                    'std' => '',
                    'default' => '',
                    'clone_id' => 'rac_manual_prefix_coupon_code',
                ),
                array(
                    'name' => __('Discount Type', 'recoverabandoncart'),
                    'desc' => __('Please Select which type of discount should be applied', 'recoverabandoncart'),
                    'id' => 'rac_coupon_type',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array('fixed_cart' => __('Amount', 'recoverabandoncart'), 'percent' => __('Percentage', 'recoverabandoncart')),
                    'std' => 'fixed_cart',
                    'default' => 'fixed_cart',
                    'clone_id' => 'rac_coupon_type',
                ),
                array(
                    'name' => __('Value', 'recoverabandoncart'),
                    'desc' => __('Enter the value to reduce in currency or % based on the Type of Discount Selected without any Symbols', 'recoverabandoncart'),
                    'desc_tip' => true,
                    'id' => 'rac_coupon_value',
                    'std' => "",
                    'default' => "",
                    'type' => 'text',
                    'newids' => 'rac_coupon_value',
                    'class' => ''
                ),
                array(
                    'name' => __('Validity in Days', 'recoverabandoncart'),
                    'desc' => __('Enter a value(days in number) for how long the Coupon should be Active', 'recoverabandoncart'),
                    'desc_tip' => true,
                    'id' => 'rac_coupon_validity',
                    'std' => "7",
                    'default' => "7",
                    'type' => 'text',
                    'newids' => 'rac_coupon_validity',
                    'class' => ''
                ),
                array(
                    'name' => __('Minimum Amount for Coupon Usage', 'recoverabandoncart'),
                    'id' => 'rac_minimum_spend',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rac_minimum_spend',
                ),
                array(
                    'name' => __('Maximum Amount for Coupon Usage', 'recoverabandoncart'),
                    'id' => 'rac_maximum_spend',
                    'std' => '',
                    'default' => '',
                    'type' => 'text',
                    'newids' => 'rac_maximum_spend',
                    'class' => '',
                ),
                array(
                    'name' => __('Individual Use Only', 'recoverabandoncart'),
                    'id' => 'rac_individual_use_only',
                    'desc' => __('Check this box if the coupon cannot be used in conjunction with other coupons.', 'recoverabandoncart'),
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_individual_use_only',
                    'class' => '',
                ),
                array(
                    'name' => __('Exclude sale items', 'recoverabandoncart'),
                    'id' => 'rac_exclude_sale_items',
                    'desc' => __('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale', 'recoverabandoncart'),
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_exclude_sale_items',
                ),
                array(
                    'name' => __('Allow Free Shipping', 'recoverabandoncart'),
                    'id' => 'rac_coupon_allow_free_shipping',
                    'desc' => __('Check this box if the coupon grants free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'recoverabandoncart'),
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_coupon_allow_free_shipping',
                    'class' => '',
                ),
                array(
                    'type' => 'rac_coupon_include_products',
                ),
                array(
                    'type' => 'rac_coupon_exclude_products',
                ),
                array(
                    'name' => __('Include Category', 'recoverabandoncart'),
                    'desc' => __('Select the Categories to which the coupons from abandoned cart emails can be applied', 'recoverabandoncart'),
                    'id' => 'rac_select_category_to_enable_redeeming',
                    'class' => 'rac_select_category_to_enable_redeeming',
                    'css' => 'min-width:350px',
                    'std' => '',
                    'type' => 'multiselect',
                    'newids' => 'rac_select_category_to_enable_redeeming',
                    'options' => $categorylist,
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Exclude Category', 'recoverabandoncart'),
                    'desc' => __('Select the Categories to which the coupons from abandoned cart emails cannot be applied', 'recoverabandoncart'),
                    'id' => 'rac_exclude_category_to_enable_redeeming',
                    'class' => 'rac_exclude_category_to_enable_redeeming',
                    'css' => 'min-width:350px',
                    'std' => '',
                    'type' => 'multiselect',
                    'newids' => 'rac_exclude_category_to_enable_redeeming',
                    'options' => $categorylist,
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rac_coupon'), //Coupon Settings END
                array(
                    'name' => __('Coupon Code Deletion Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_coupon_deletion',
                ),
                array(
                    'name' => __('Delete Coupons once Used', 'recoverabandoncart'),
                    'desc' => __('If enabled, coupons which are automatically created by Recover Abandoned Cart plugin will be deleted once the coupon is used', 'recoverabandoncart'),
                    'id' => 'rac_delete_coupon_after_use',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_delete_coupon_after_use',
                ),
                array(
                    'name' => __('Delete Coupon when', 'recoverabandoncart'),
                    'id' => 'rac_delete_coupon_after_use_based_on',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'options' => array('1' => __('User Place the Order', 'recoverabandoncart'), '2' => __('Placed Order reaches Specific Status', 'recoverabandoncart')),
                    'std' => '1',
                    'default' => '1',
                    'class' => 'rac_delete_coupon_by',
                    'clone_id' => 'rac_delete_coupon_after_use_based_on',
                ),
                array(
                    'name' => __('Delete Coupon when Order Status becomes', 'recoverabandoncart'),
                    'id' => 'rac_delete_coupon_after_use_based_on_status',
                    'css' => 'min-width:350px',
                    'std' => array('completed'),
                    'default' => array('completed'),
                    'type' => 'multiselect',
                    'newids' => 'rac_delete_coupon_after_use_based_on_status',
                    'options' => $orderlist_combine,
                    'custom_attributes' => array('required' => 'required'),
                    'class' => 'rac_delete_coupon_by rac_delete_coupon_by_status',
                ),
                array(
                    'name' => __('Delete Coupons once Expired', 'recoverabandoncart'),
                    'desc' => __('If enabled, coupons which are automatically created by Recover Abandoned Cart plugin will be deleted once the coupon is expired', 'recoverabandoncart'),
                    'id' => 'rac_delete_coupon_expired',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_delete_coupon_expired',
                ),
                array('type' => 'sectionend', 'id' => 'rac_coupon_deletion'), //Coupon Settings END
            ));
        }

        public static function fp_rac_admin_setting_coupon() {
            woocommerce_admin_fields(FP_RAC_Coupon_Tab::fp_rac_menu_options_coupon_gen());
        }

        public static function fp_rac_update_options_coupon() {
            woocommerce_update_options(FP_RAC_Coupon_Tab::fp_rac_menu_options_coupon_gen());
        }

        public static function fprac_coupon_default_settings() {
            $settings = FP_RAC_Coupon_Tab::fp_rac_menu_options_coupon_gen();

            if (rac_check_is_array($settings)) {
                foreach ($settings as $setting)
                    if (isset($setting['id']) && isset($setting['std'])) {
                        if (!get_option($setting['id']))
                            add_option($setting['id'], $setting['std']);
                    }
            }
        }

        public static function fp_rac_admin_setting_buttons() {
            ?>
            <span class = "submit" style = "margin-left: 25px;">
                <?php if (!isset($GLOBALS['hide_save_button'])) :
                    ?>
                    <input name="save" class="button-primary" style="margin-top:15px;" type="submit" value="<?php _e('Save', 'recoverabandoncart'); ?>" />
                <?php endif; ?>
                <input type="hidden" name="subtab" id="last_tab" />
                <?php wp_nonce_field('woocommerce-settings'); ?>
            </span>
            <?php
        }

        /*
         * Function to save the selected products to exclude
         */

        public static function save_product_to_exclude() {
            $value = isset($_POST['rac_exclude_products_in_coupon']) ? $_POST['rac_exclude_products_in_coupon'] : '';
            update_option('rac_exclude_products_in_coupon', $value);
        }

        /*
         * Function to save select products to include
         */

        public static function save_product_to_include() {
            $value = isset($_POST['rac_include_products_in_coupon']) ? $_POST['rac_include_products_in_coupon'] : '';
            update_option('rac_include_products_in_coupon', $value);
        }

        /*
         * Function to select products to exclude
         */

        public static function rac_select_product_to_exclude() {
            echo rac_common_function_to_multi_select_for_product_search('rac_exclude_products_in_coupon', 'Exclude Products');
        }

        /*
         * Function to select products to include
         */

        public static function rac_select_product_to_include() {
            echo rac_common_function_to_multi_select_for_product_search('rac_include_products_in_coupon', 'Include Products');
        }

    }

    FP_RAC_Coupon_Tab::init();
}