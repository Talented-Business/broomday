<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Support_tab')) {

    /**
     * FP_RAC_Support_tab Class.
     */
    class FP_RAC_Support_tab {

        public static function init() {
            add_action('woocommerce_fprac_settings_tabs_fpracsupport', array(__CLASS__, 'fp_rac_admin_setting_support'));
        }

        public static function fp_rac_support_admin_fields() {
            $welcomepage_url = add_query_arg(array('page' => 'recover-abandoned-cart-welcome-page'), admin_url('admin.php'));
            return apply_filters('woocommerce_fpracsupport_settings', array(
                array(
                    'name' => __('Welcome Page', 'recoverabandoncart'),
                    'type' => 'title',
                    'desc' => __('For more information on Recover Abadoned Cart please check the <a href="' . $welcomepage_url . '">Welcome Page</a> <br> ', 'recoverabandoncart'),
                    'id' => 'rac_welcome_settings'
                ),
                array(
                    'name' => __('Contact Support', 'recoverabandoncart'),
                    'type' => 'title',
                    'desc' => __('For support, feature request or any help, please <a href="http://support.fantasticplugins.com/">register and open a support ticket on our site.</a> <br> ', 'recoverabandoncart'),
                    'id' => 'rac_support_settings'
                ),
                array(
                    'name' => __('Documentation', 'recoverabandoncart'),
                    'type' => 'title',
                    'desc' => __('Please check the documentation as we have lots of information there. The documentation file can be found inside the documentation folder which you will find when you unzip the downloaded zip file.', 'recoverabandoncart'),
                    'id' => 'rac_support_documentation',
                ),
                array('type' => 'sectionend', 'id' => 'rac_support_settings'),
            ));
        }

        public static function fp_rac_admin_setting_support() {
            woocommerce_admin_fields(FP_RAC_Support_tab::fp_rac_support_admin_fields());
        }

    }

    FP_RAC_Support_tab::init();
}