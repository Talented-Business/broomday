<?php

/**
 * Plugin Name: Recover Abandoned Cart
 * Plugin URI:
 * Description: Recover Abandoned Cart is a WooCommerce Extension Plugin which will help you Recover the Abandoned Carts and bring more sales.
 * Version: 20.2
 * Author: Fantastic Plugins
 * Author URI:http://fantasticplugins.com
 */
/*
  Copyright 2014 Fantastic Plugins. All Rights Reserved.
  This Software should not be used or changed without the permission
  of Fantastic Plugins.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('RecoverAbandonCart')) {

    /**
     * Main RecoverAbandonCart Class.
     *
     */
    final class RecoverAbandonCart {

        /**
         * RecoverAbandonCart Version
         *  
         */
        public $version = '20.2';

        /**
         * The single instance of the class.
         *
         */
        protected static $_instance = null;

        /**
         * Load RecoverAbandonCart Class in Single Instance
         */
        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /* Cloning has been forbidden */

        public function __clone() {
            _doing_it_wrong(__FUNCTION__, __('You are not allowed to perform this action!!!', 'recoverabandoncart'), $this->version);
        }

        /**
         * Unserialize the class data has been forbidden
         */
        public function __wakeup() {
            _doing_it_wrong(__FUNCTION__, __('You are not allowed to perform this action!!!', 'recoverabandoncart'), $this->version);
        }

        /**
         * RecoverAbandonCart Class Constructor
         */
        public function __construct() {

            /* Include once will help to avoid fatal error by load the files when you call init hook */
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            $this->fp_rac_header_already_sent_problem();

            if ($this->fp_rac_check_if_woocommerce_is_active()) {
                return false;
            }
            include_once 'inc/admin/fp_rac_privacy.php';

            $this->fp_rac_translate_file();
            $this->fp_rac_define_constants();
            $this->fp_rac_include_files();
            $this->fp_rac_init_hooks();
            add_filter('cron_schedules', array($this, 'fp_rac_add_x_hourly'));
            $this->fp_rac_cron_job_setting();
            $this->fp_rac_trigger_cron_job_setting_event();
        }

        /**
         * Function to Prevent Header Error that says You have already sent the header.
         */
        private function fp_rac_header_already_sent_problem() {
            ob_start();
        }

        /**
         * Function to check wheather Woocommerce is active or not
         */
        private function fp_rac_check_if_woocommerce_is_active() {

            if (is_multisite()) {
                // This Condition is for Multi Site WooCommerce Installation
                if (!is_plugin_active_for_network('woocommerce/woocommerce.php') && (!is_plugin_active('woocommerce/woocommerce.php'))) {
                    if (is_admin()) {
                        add_action('init', array('RecoverAbandonCart', 'fp_rac_display_warning_messages'));
                    }
                    return true;
                }
            } else {
                // This Condition is for Single Site WooCommerce Installation
                if (!is_plugin_active('woocommerce/woocommerce.php')) {
                    if (is_admin()) {
                        add_action('init', array('RecoverAbandonCart', 'fp_rac_display_warning_messages'));
                    }
                    return true;
                }
            }
            return false;
        }

        /**
         * Initialize the Translate Files.
         * 
         */
        private function fp_rac_translate_file() {
            load_plugin_textdomain('recoverabandoncart', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        /**
         * Prepare the Constants value array.
         * 
         */
        private function fp_rac_define_constants() {
            $constant_array = array(
                'RAC_VERSION' => $this->version,
                'RAC_PLUGIN_FILE' => __FILE__,
                'RAC_PLUGIN_FOLDER_NAME' => 'rac/',
                'RAC_PLUGIN_BASE_NAME' => plugin_basename(__FILE__),
                'RAC_PLUGIN_PATH' => untrailingslashit(plugin_dir_path(__FILE__)),
                'RAC_PLUGIN_URL' => untrailingslashit(plugins_url('/', __FILE__)),
                'RAC_ADMIN_URL' => admin_url('admin.php'),
                'RAC_ADMIN_AJAX_URL' => admin_url('admin-ajax.php'),
            );
            $constant_array = apply_filters('fp_rac_define_constants', $constant_array);

            if (is_array($constant_array) && !empty($constant_array)) {
                foreach ($constant_array as $name => $value) {
                    $this->fp_rac_define_constant($name, $value);
                }
            }
        }

        /**
         * Define the Constants value.
         * 
         */
        private function fp_rac_define_constant($name, $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        /**
         * Include required files
         * 
         */
        public function fp_rac_include_files() {

            require_once 'inc/fp-rac-counter.php';
            require_once 'inc/fp-rac-coupon-deletion.php';

            include_once 'inc/class-fp-rac-install.php';
            include_once 'inc/fp-rac-email-functions.php';
            include_once 'inc/fp-rac-common-functions.php';
            include_once 'inc/fp-rac-compatibility-functions.php';
            include_once 'inc/class-fp-rac-register-post-type.php';
            include_once 'inc/class-fp-rac-polish-product-info.php';
            include_once 'inc/fp-rac-custom-post-type-functions.php';
            include_once 'inc/class-fp-rac-register-post-status.php';
            include_once 'inc/fp-rac-custom-post-type-functions.php';
            include_once 'inc/class-fp-rac-enqueue-scripts-file.php';
            include_once 'inc/fp-rac-wc-compatibility-functions.php';
            include_once 'inc/fp-rac-class-previous-order-data.php';
            include_once 'inc/emails/class-fp-rac-automatic-mail.php';
            include_once 'inc/class-fp-rac-insert-cartlist-entry.php';
            include_once 'inc/class-fp-rac-abandon-order-management.php';
            include_once 'inc/fp-rac-add-cancelled-order-immediately.php';
            include_once 'inc/admin/menu/class-fp-rac-coupon-handler.php';
            include_once 'inc/compatibility/fp-rac-wpml-compatibility.php';
            include_once 'inc/compatibility/fp-rac-personal-data-handler.php';
            include_once 'inc/woocommerce-log/class-fp-woocommerce-log.php';
            include_once 'inc/background-updater/fp-rac-main-background-process.php';
            include_once 'inc/compatibility/fp-rac-product-addons-compatibility.php';
            include_once 'inc/compatibility/fp-rac-currency-switcher-compatibility.php';
            include_once 'inc/emails/class-fp-rac-admin-notification-email.php';

            if (is_admin()) {
                $this->fp_rac_include_admin_files();
            }

            if (!is_admin() || defined('DOING_AJAX')) {
                $this->fp_rac_include_frontend_files();
            }
        }

        /**
         * Include required admin files
         */
        public function fp_rac_include_admin_files() {

            include_once 'inc/fp-rac-previous-order.php';
            include_once 'inc/emails/class-fp-rac-test-mail.php';
            include_once 'inc/emails/class-fp-rac-manual-mail.php';
            include_once 'inc/class-fp-rac-admin-ajax-functions.php';
            include_once 'inc/emails/class-fp-rac-email-template-test.php';
            include_once 'inc/welcome-page/fp-rac-welcome-page-functions.php';
            include_once 'inc/email-template/class-fp-rac-email-template.php';
            include_once 'inc/api/rac-common-function-for-multi-select-search.php';
            //Submenu
            include_once 'inc/admin/menu/class-fp-rac-settings-submenu.php';
            include_once 'inc/admin/menu/class-fp-rac-maillog-submenu.php';
            include_once 'inc/admin/menu/class-fp-rac-cartlist-submenu.php';
            include_once 'inc/admin/menu/class-fp-rac-emailtemplate-submenu.php';
            include_once 'inc/admin/menu/class-fp-rac-recovered-order-submenu.php';
        }

        /**
         * Include required frontend files
         * 
         */
        public function fp_rac_include_frontend_files() {

            include_once 'inc/class-fp-rac-unsubscribe-function.php';
            include_once 'inc/class-fp-rac-cart-data-from-mail.php';
            include_once 'inc/class-fp-rac-lightbox-handler.php';
        }

        /**
         * Define the hooks
         * 
         */
        private function fp_rac_init_hooks() {
            register_activation_hook(__FILE__, array('FP_RAC_Install', 'install'));
        }

        /**
         * Set Cron Event as "rac_cron_job".
         * 
         */
        public function fp_rac_cron_job_setting() {
            if (wp_next_scheduled('rac_cron_job') == false) {
                wp_schedule_event(time(), 'xhourly', 'rac_cron_job');
            }
        }

        /**
         * Initializing Cron Schedules for  "rac_cron_job" event
         * 
         */
        public function fp_rac_add_x_hourly($schedules) {
            $interval = fp_rac_get_interval('rac_abandon_cron_time', 'rac_abandon_cart_cron_type');
            $schedules['xhourly'] = array(
                'interval' => $interval,
                'display' => 'X Hourly'
            );
            return $schedules;
        }

        /**
         * Trigger the cron job settings.
         * 
         */
        public function fp_rac_trigger_cron_job_setting_event() {
            $wp_array = array(
                'wp_scheduled_delete',
                'wp_version_check',
                'wp_update_plugins',
                'wp_update_themes',
                'wp_scheduled_auto_draft_delete',
                'woocommerce_scheduled_sales'
            );
            $wp_array = apply_filters('fp_rac_check_is_cron_set', $wp_array);
            if (is_array($wp_array) && !empty($wp_array)) {
                foreach ($wp_array as $cron_name) {
                    add_action($cron_name, array($this, 'fp_rac_cron_job_setting'));
                }
            }
        }

        /**
         * Warning Message When woocommerce is not active.
         * 
         */
        public static function fp_rac_display_warning_messages() {
            $variable = "<div class='error'><p> Recover Abandoned Cart will not work until WooCommerce Plugin is Activated. Please Activate the WooCommerce Plugin. </p></div>";
            echo $variable;
        }

    }

    RecoverAbandonCart::instance();
}
