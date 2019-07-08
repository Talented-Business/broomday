<?php

/**
 * initialize the plugin.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Install')) {

    /**
     * FP_RAC_Install Class.
     */
    class FP_RAC_Install {

        /**
         * FP_RAC_Install Class initialization.
         */
        public static function init() {
            add_action('init', array(__CLASS__, 'rac_delete_abandon_carts_after_selected_days'), 999);
            add_action('admin_init', array(__CLASS__, 'fp_rac_preview_email_template'));
            add_action('plugins_loaded', array(__CLASS__, 'fp_rac_background_process_redirect'));
            add_filter('plugin_row_meta', array(__CLASS__, 'fp_rac_plugin_row_meta'), 10, 2);
            add_action('rac_cron_job', array('FP_RAC_Automatic_Email', 'fp_rac_cron_job_mailing'));
            add_filter("plugin_action_links_" . RAC_PLUGIN_BASE_NAME, array(__CLASS__, 'fp_rac_settings_link'));
            add_filter('woocommerce_attribute_label', array(__CLASS__, 'decode_labels_for_non_english_sites'), 10, 2);
        }

        /**
         * Initializing the Progress Bar
         * 
         */
        public static function fp_rac_background_process_redirect() {
            $background = get_transient('fp_rac_background_process_transient');
            $coupon = get_transient('fp_rac_coupon_background_process_transient');
            if (!$background && !$coupon) {
                return;
            }
            if ($background) {
                delete_transient('fp_rac_background_process_transient');
                FP_RAC_Main_Function_Importing_Part::handle_emailtemplate();
            }

            if ($coupon) {
                delete_transient('fp_rac_coupon_background_process_transient');
                FP_WooCommerce_Log::log('v' . RAC_VERSION . ' Upgrade Started');
                FP_RAC_Main_Function_Importing_Part::handle_couponcode();
            }

            $admin_url = admin_url('edit.php');
            $redirect_url = esc_url_raw(add_query_arg(array('post_type' => 'raccartlist', 'page' => 'fprac_reports_slug', 'rac_updating_action' => 'rac_updating_process'), $admin_url));

            wp_safe_redirect($redirect_url);
        }

        /**
         * Preview Email Template
         * 
         */
        public static function fp_rac_preview_email_template() {
            if (isset($_GET['rac_preview_template']) && isset($_GET['post_id'])) {
                if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'rac-preview-template')) {
                    die('Security check');
                }
                include_once RAC_PLUGIN_PATH . '/inc/email-template/class-fp-rac-preview-email-template.php';
                FP_RAC_Preview_Email_Template::rac_preview_email_template();
                exit();
            }
        }

        /**
         * Install RAC.
         */
        public static function install() {
            FP_RAC_Register_Post_Type::fp_rac_register_post_types();
            FP_RAC_Register_Post_Status::fp_rac_register_custom_post_status();
            self::fp_rac_set_default_value();
            self::fp_rac_create_default_email_template();
        }

        /**
         * Initializing the Welcome Page
         * 
         */
        public static function fp_rac_welcome_screen_activation() {
            set_transient('_welcome_screen_activation_redirect_recover_abandoned_cart', true, 30);
        }

        /**
         * Initializing the settings link. 
         * 
         */
        public static function fp_rac_settings_link($links) {
            $setting_page_link = '<a href="admin.php?page=fprac_slug">Settings</a>';
            array_unshift($links, $setting_page_link);
            return $links;
        }

        /**
         * Initializing the plugin row  
         * 
         */
        public static function fp_rac_plugin_row_meta($links, $file) {
            if (RAC_PLUGIN_BASE_NAME == $file) {
                $row_meta = array(
                    'about' => '<a href="' . esc_url(admin_url('admin.php?page=recover-abandoned-cart-welcome-page')) . '" aria-label="' . esc_attr__('About', 'recoverabandoncart') . '">' . esc_html__('About', 'recoverabandoncart') . '</a>',
                    'support' => '<a href="' . esc_url('http://fantasticplugins.com/support/') . '" aria-label="' . esc_attr__('Support', 'recoverabandoncart') . '">' . esc_html__('Support', 'recoverabandoncart') . '</a>',
                );

                return array_merge($links, $row_meta);
            }

            return (array) $links;
        }

        /**
         * Initializing set transients. 
         * 
         */
        public static function fp_rac_set_default_value() {
            $tabs = array(
                'fpracgeneral',
                'fpraccoupon',
                'fpracdebug'
            );

            $tabs = apply_filters('fp_rac_set_default_value_tabs', $tabs);
            if (rac_check_is_array($tabs)) {
                foreach ($tabs as $tab) {
                    //include current page functionality.
                    include_once RAC_PLUGIN_PATH . '/inc/admin/menu/tabs/class-fp-rac-' . $tab . '-tab.php';

                    do_action('fp_rac_default_settings_' . $tab);
                }
            }
        }

        /**
         * Initialize the First Email Template.
         * 
         */
        public static function fp_rac_create_default_email_template() {
            global $wpdb;
            $set_cron = true;
            $post_arg = array(
                'posts_per_page' => -1,
                'post_status' => array('racactive', 'racinactive', 'trash'),
                'post_type' => 'racemailtemplate',
                'fields' => 'ids'
            );
            $posts = fp_rac_check_query_having_posts($post_arg);
            $coupon_code_array = get_option('rac_coupon_for_user');
            $table_count = FP_RAC_Main_Function_Importing_Part::fp_rac_get_old_table_count();
            $coupon_get_option = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name LIKE %s", '%abandon_time_of%'));
            if ((get_option('rac_upgrade_success') != 'yes') && $table_count > 0) {
                if (FP_RAC_Main_Function_Importing_Part::fp_rac_upgrade_file_exists()) {
                    FP_RAC_Main_Function_Importing_Part::initiate_to_background_process();
                    $set_cron = false;
                }
            } elseif ((get_option('rac_coupon_upgrade_success') != 'yes') && (!empty($coupon_code_array) || !empty($coupon_get_option))) {
                if (FP_RAC_Main_Function_Importing_Part::fp_rac_upgrade_file_exists()) {
                    FP_RAC_Main_Function_Importing_Part::initiate_to_coupon_background_process();
                    $set_cron = false;
                }
            } else {
                if ((empty($posts))) {
                    $arg = array(
                        'post_status' => 'racactive',
                        'post_type' => 'racemailtemplate',
                        'post_title' => 'Default',
                        'post_content' => "Hi {rac.firstname},<br><br>We noticed you have added the following Products in your Cart, but haven't completed the purchase. {rac.Productinfo}<br><br>We have captured the Cart for your convenience. Please use the following link to complete the purchase {rac.cartlink}<br><br>Thanks.",
                    );
                    $id = wp_insert_post($arg);
                    $post_array = array(
                        'rac_template_status' => 'ACTIVE',
                        'rac_template_mail' => 'HTML',
                        'rac_old_template_id' => $id,
                        'rac_template_link' => '',
                        'rac_template_sender_opt' => 'woo',
                        'rac_template_from_name' => 'Admin',
                        'rac_template_from_email' => get_option('admin_email'),
                        'rac_template_blind_carbon_copy' => '',
                        'rac_template_subject' => 'Recovering Abandon Cart',
                        'rac_template_sending_type' => 'days',
                        'rac_template_sending_duration' => '1',
                        'rac_template_anchor_text' => 'Cart Link',
                    );

                    if (rac_check_is_array($post_array)) {
                        foreach ($post_array as $name => $value) {
                            update_post_meta($id, $name, $value);
                        }
                    }
                }
            }
            if ($set_cron)
                self::fp_rac_welcome_screen_activation(); //welcome page
        }

        /**
         * Delete Cart list after Expired
         * 
         */
        public static function rac_delete_abandon_carts_after_selected_days() {
            if (get_option('enable_remove_abandon_after_x_days') == 'yes') {
                $duration = get_option('rac_remove_abandon_after_x_days');
                $post_status = get_option('rac_delete_cart_selection');
                $post_status = empty($post_status) ? array('rac-cart-abandon') : $post_status;
                $args = array(
                    'post_type' => 'raccartlist',
                    'post_status' => $post_status,
                    'fields' => 'ids'
                );
                $posts = fp_rac_check_query_having_posts($args);
                if (rac_check_is_array($posts)) {
                    foreach ($posts as $post_id) {
                        $cart_abandon_time = get_post_meta($post_id, 'rac_cart_abandoned_time', true);
                        $limit = "+" . $duration . " day";
                        $date = strtotime($limit, $cart_abandon_time);
                        if ($date <= current_time('timestamp')) {
                            wp_delete_post($post_id, true);
                        }
                    }
                }
            }
        }

        /**
         * Decodes labels for non english sites
         * 
         */
        public static function decode_labels_for_non_english_sites($label, $name, $product = null) {
            return rawurldecode($label);
        }

    }

    FP_RAC_Install::init();
}