<?php

/**
 * Enqueue the Files
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Enqueue_Scripts')) {

    /**
     * FP_RAC_Enqueue_Scripts Class.
     */
    class FP_RAC_Enqueue_Scripts {

        /**
         * FP_RAC_Enqueue_Scripts Class Initialization.
         */
        public static function init() {
            add_action('wp_enqueue_scripts', array(__CLASS__, 'fp_rac_checkout_script'));
            add_action('admin_enqueue_scripts', array(__CLASS__, 'fp_rac_admin_external_js_files'));
            add_action('admin_enqueue_scripts', array(__CLASS__, 'fp_rac_admin_external_css_files'));

            if (isset($_GET['page']) && $_GET['page'] == 'recover-abandoned-cart-welcome-page') {
                add_action('admin_enqueue_scripts', array(__CLASS__, 'fp_rac_admin_external_welcomepage_css'));
            }
        }

        /**
         * Enqueue Front end required JS files
         */
        public static function fp_rac_checkout_script() {

            $enqueue_array = array(
                'rac-datepicker' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'fp_rac_frontend_checkout_external_js'),
                    'restrict' => true,
                ),
                'rac-alltabs' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'fp_rac_frontend_myaccount_external_js'),
                    'restrict' => true,
                ),
            );

            $enqueue_array = apply_filters('fp_rac_frontend_enqueue_scripts', $enqueue_array);
            if (rac_check_is_array($enqueue_array)) {
                foreach ($enqueue_array as $key => $enqueue) {
                    if (rac_check_is_array($enqueue)) {
                        if ($enqueue['restrict'])
                            call_user_func_array($enqueue['callable'], array());
                    }
                }
            }
            wp_register_script('fp_rac_enqueue_script_for_swal', RAC_PLUGIN_URL . '/assets/sweetalert2/sweetalert2.min.js');
            wp_register_style('fp_rac_enqueue_style_for_swal', RAC_PLUGIN_URL . '/assets/sweetalert2/sweetalert2.min.css');
            wp_register_style('fp_rac_enqueue_css_for_swal', RAC_PLUGIN_URL . '/assets/sweetalert2/sweetalert2.css');
            wp_enqueue_script('fp_rac_enqueue_script_for_swal');
            wp_enqueue_style('fp_rac_enqueue_style_for_swal');
            wp_enqueue_style('fp_rac_enqueue_css_for_swal');
        }

        public static function fp_rac_frontend_checkout_external_js() {
            if (!is_user_logged_in()) {
                if ((get_option('rac_load_script_styles') == 'wp_head') || !get_option('rac_load_script_styles')) {
                    $bool = false;
                } else {
                    $bool = true;
                }
                wp_register_script('rac_guest_handle', RAC_PLUGIN_URL . '/assets/js/fp-rac-guest-checkout.js', array('jquery'), RAC_VERSION);
                $email_name_no = array();
                if(isset($_COOKIE['raccookie_guest_email'])){
                $email_name_no = (unserialize(stripslashes($_COOKIE['raccookie_guest_email'])));
                }
                $email_address = isset($email_name_no['email']) ? $email_name_no['email'] : "";
                $first_name = isset($email_name_no['firstname']) ? $email_name_no['firstname'] : "";
                $last_name = isset($email_name_no['lastname']) ? $email_name_no['lastname'] : "";
                $phone_no = isset($email_name_no['phone_no']) ? $email_name_no['phone_no'] : "";
                wp_localize_script('rac_guest_handle', 'rac_guest_params', array(
                    'console_error' => __('Not a valid e-mail address', 'recoverabandoncart'),
                    'current_lang_code' => fp_rac_get_current_language(),
                    'ajax_url' => RAC_ADMIN_AJAX_URL,
                    'guest_entry' => wp_create_nonce('guest-entry'),
                    'is_checkout' => is_checkout(),
                    'is_shop' => is_shop(),
                    'ajax_add_to_cart' => get_option('woocommerce_enable_ajax_add_to_cart'),
                    'enable_popup' => get_option('rac_enable_guest_add_to_cart_popup'),
                    'form_label' => get_option('rac_guest_add_to_cart_popup_heading'),
                    'first_name' => get_option('rac_guest_first_name'),
                    'email_address_not_valid' => get_option('rac_guest_popup_err_msg_for_invalid_email'),
                    'enter_email_address' => get_option('rac_guest_popup_err_msg_for_empty'),
                    'enter_first_name' => get_option('rac_guest_popup_err_msg_for_empty_fname'),
                    'enter_phone_no' => get_option('rac_guest_popup_err_msg_for_empty_phoneno'),
                    'enter_valid_phone_no' => get_option('rac_guest_popup_err_msg_for_empty_invalid_phoneno'),
                    'enter_last_name' => get_option('rac_guest_popup_err_msg_for_empty_lname'),
                    'cancel_label' => get_option('rac_guest_popup_cancel_text'),
                    'add_to_cart_label' => get_option('rac_guest_popup_add_to_cart_text'),
                    'force_guest' => get_option('rac_force_guest_to_enter_email_address'),
                    'show_guest_name' => get_option('rac_show_hide_name_in_popup') === '2',
                    'show_guest_contactno' => get_option('rac_show_hide_contactno_in_popup') === '2',
                    'force_guest_name' => get_option('rac_force_guest_to_enter_first_last_name') === 'yes',
                    'force_guest_contactno' => get_option('rac_force_guest_to_enter_phoneno') === 'yes',
                    'popup_already_displayed' => isset($_COOKIE['rac_guest_popup_already_displayed']) ? 'yes' : 'no',
                    'is_cookie_already_set' => isset($_COOKIE['raccookie_guest_email']) ? true : false,
                    'fp_rac_popup_email' => $email_address,
                    'fp_rac_first_name' => $first_name,
                    'fp_rac_last_name' => $last_name,
                    'fp_rac_phone_no' => $phone_no
                ));
                wp_enqueue_script('rac_guest_handle', RAC_PLUGIN_URL . '/assets/js/fp-rac-guest-checkout.js', '', '', $bool);
            }
        }

        public static function fp_rac_frontend_myaccount_external_js() {
            //register script
            wp_register_script('fp_unsubscribe', RAC_PLUGIN_URL . '/assets/js/frontend/fp-rac-unsubscribe.js', array('jquery'), RAC_VERSION);
            //localize script
            wp_localize_script('fp_unsubscribe', 'fp_unsubscribe_obj', array(
                'rac_current_userid' => get_current_user_id(),
                'rac_admin_url' => RAC_ADMIN_AJAX_URL,
                'rac_unsubcribe_nonce' => wp_create_nonce('unsubscribe-email'),
                'rac_unsub_message' => __('Successfully Unsubscribed...', 'recoverabandoncart'),
                'rac_sub_message' => __('Successfully Subscribed...', 'recoverabandoncart'),
            ));
        }

        /**
         * Enqueue required CSS files
         */
        public static function fp_rac_admin_external_css_files() {
            wp_register_style('footable_css', RAC_PLUGIN_URL . '/assets/css/footable.core.css');
            wp_register_style('footablestand_css', RAC_PLUGIN_URL . '/assets/css/footable.standalone.css');
            wp_enqueue_style('footable_css');
            wp_enqueue_style('footablestand_css');
            wp_enqueue_style('jquery_smoothness_ui', RAC_PLUGIN_URL . '/assets/css/jquery_smoothness_ui.css');
        }

        /**
         * Enqueue welcome page css file
         */
        public static function fp_rac_admin_external_welcomepage_css() {
            wp_register_style('rac_welcome_page_css', RAC_PLUGIN_URL . '/assets/css/fp-rac-welcome-page-style.css');
            wp_enqueue_style('rac_welcome_page_css');
        }

        /**
         * Enqueue Admin end required JS files
         */
        public static function fp_rac_admin_external_js_files() {
            $enqueue_array = array(
                'rac-datepicker' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'datepicker_enqueue_scripts'),
                    'restrict' => is_admin(),
                ),
                'rac-alltabs' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'remaining_tabs_enqueue_scripts'),
                    'restrict' => is_admin(),
                ),
                'rac-footable' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'footable_table_enqueue_scripts'),
                    'restrict' => isset($_GET['tab']) && $_GET['tab'] == 'fpracshortocode',
                ),
                'rac-validate' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'validate_text_enqueue_scripts'),
                    'restrict' => is_admin(),
                ),
                'rac-template' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'email_template_enqueue_scripts'),
                    'restrict' => is_admin(),
                ),
                'rac-emailtab' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'email_tab_enqueue_scripts'),
                    'restrict' => isset($_GET['tab']) && $_GET['tab'] == 'fpracadvance',
                ),
                'rac-emailtab_ajax' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'email_template_ajax_enqueue_scripts'),
                    'restrict' => is_admin(),
                ),
                'rac-jscolor' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'jscolor_enqueue_scripts'),
                    'restrict' => isset($_GET['tab']) && $_GET['tab'] == 'fpracadvance',
                ),
                'rac-generaltab' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'general_tab_enqueue_scripts'),
                    'restrict' => isset($_GET['page']) && $_GET['page'] == 'fprac_slug',
                ),
                'rac-cartlist' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'cartlist_tab_enqueue_scripts'),
                    'restrict' => isset($_GET['post_type']) && $_GET['post_type'] == 'raccartlist',
                ),
                'rac-updatetab' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'previous_orders_tab_enqueue_scripts'),
                    'restrict' => isset($_GET['tab']) && $_GET['tab'] == 'fpracupdate',
                ),
                'rac-trouble' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'troubleshoot_enqueue_scripts'),
                    'restrict' => isset($_GET['tab']) && $_GET['tab'] == 'fpracdebug',
                ),
                'rac-coupon' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'coupon_tab_enqueue_scripts'),
                    'restrict' => isset($_GET['tab']) && $_GET['tab'] == 'fpraccoupon',
                ),
                'rac-graph' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'graph_enqueue_scripts'),
                    'restrict' => isset($_GET['page']) && $_GET['page'] == 'fprac_reports_slug',
                ),
                'rac-blockui' => array(
                    'callable' => array('FP_RAC_Enqueue_Scripts', 'blockui_enqueue_scripts'),
                    'restrict' => is_admin(),
                ),
            );
            $post_type_array = array('raccartlist', 'racmaillog', 'racemailtemplate', 'racrecoveredorder');
            if ((isset($_GET['post_type']) && in_array($_GET['post_type'], $post_type_array)) || (isset($_GET['post']) && in_array(get_post_type($_GET['post']), $post_type_array)) || (isset($_GET['page']) && $_GET['page'] == 'fprac_slug')) {
                $enqueue_array = apply_filters('fp_rac_admin_enqueue_scripts', $enqueue_array);
                if (rac_check_is_array($enqueue_array)) {
                    foreach ($enqueue_array as $key => $enqueue) {
                        if (rac_check_is_array($enqueue)) {
                            if ($enqueue['restrict'])
                                call_user_func_array($enqueue['callable'], array());
                        }
                    }
                }
            }
        }

        public static function blockui_enqueue_scripts() {
            //register script
            wp_register_script('fp_rac_blockui', RAC_PLUGIN_URL . '/assets/js/jquery.blockUI.js', array('jquery'), RAC_VERSION);
            wp_enqueue_script('fp_rac_blockui');
        }

        public static function validate_text_enqueue_scripts() {
            //register script
            wp_register_script('fp_validate_text_min_max', RAC_PLUGIN_URL . '/assets/js/fp-rac-validate-text-min-max-field.js', array('jquery'), RAC_VERSION);
            //localize script
            wp_localize_script('fp_validate_text_min_max', 'fp_validate_text_params', array(
                'rac_warning_message' => __('Please enter a value greater than ', 'enhanced validate', 'recoverabandoncart'),
            ));
            //enqueue script
            wp_enqueue_script('fp_validate_text_min_max');
        }

        public static function jscolor_enqueue_scripts() {
            wp_enqueue_script('racjscolorpicker', RAC_PLUGIN_URL . '/assets/js/jscolor/jscolor.js', array('jquery'));
        }

        public static function datepicker_enqueue_scripts() {
            $screen = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            $screen_ids = array(
                'raccartlist',
                'racmaillog',
                'racemailtemplate',
                'racrecoveredorder',
                'raccartlist_page_fprac_slug',
                'raccartlist_page_fprac_reports_slug'
            );
            $screen_id = str_replace('edit-', '', $screen_id);
            wp_register_script('date_picker_initialize', RAC_PLUGIN_URL . '/assets/js/rac-datepicker.js', array('jquery', 'jquery-ui-datepicker'), RAC_VERSION);
            if (in_array($screen_id, $screen_ids)) {
                wp_enqueue_script('iris');
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_script('date_picker_initialize');
            }
        }

        public static function footable_table_enqueue_scripts() {
            wp_enqueue_script('footable', RAC_PLUGIN_URL . '/assets/js/footable/footable.js', array('jquery'), RAC_VERSION);
            wp_enqueue_script('footable_sorting', RAC_PLUGIN_URL . '/assets/js/footable/footable.sort.js', array('jquery'), RAC_VERSION);
            wp_enqueue_script('footable_paginate', RAC_PLUGIN_URL . '/assets/js/footable/footable.paginate.js', array('jquery'), RAC_VERSION);
            wp_enqueue_script('footable_filter', RAC_PLUGIN_URL . '/assets/js/footable/footable.filter.js', array('jquery'), RAC_VERSION);
        }

        public static function general_tab_enqueue_scripts() {
            $reset_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'rac_reset' => 'reset'), RAC_ADMIN_URL));

            //register script
            wp_register_script('fp_rac_general_tab', RAC_PLUGIN_URL . '/assets/js/tabs/fp-rac-general-tab.js', array('jquery'), RAC_VERSION);
            //localize script
            wp_localize_script('fp_rac_general_tab', 'fp_rac_general_tab_obj', array(
                'reset_url' => $reset_url,
            ));
            //enqueue script
            wp_enqueue_script('fp_rac_general_tab');
        }

        public static function email_tab_enqueue_scripts() {
            //enqueue script
            wp_enqueue_script('fp_rac_email_tab', RAC_PLUGIN_URL . '/assets/js/tabs/fp-rac-advance-tab.js', array('jquery'), RAC_VERSION);
        }

        public static function email_template_enqueue_scripts() {
            wp_enqueue_media();
            wp_enqueue_script('fp_email_template', RAC_PLUGIN_URL . '/assets/js/fp-rac-email-templates.js', array('jquery'), RAC_VERSION);
        }

        public static function email_template_ajax_enqueue_scripts() {
            $template_id = isset($_GET['rac_edit_email']) ? $_GET['rac_edit_email'] : '';
            $template_listurl = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail'), RAC_ADMIN_URL));

            //register script
            wp_register_script('fp_email_template_ajax', RAC_PLUGIN_URL . '/assets/js/fp-rac-email-template-ajax.js', array('jquery'), RAC_VERSION);
            //localize script
            wp_localize_script('fp_email_template_ajax', 'fp_email_template_ajax_obj', array(
                'template_id' => $template_id,
                'template_listurl' => $template_listurl,
                'rac_valid_email_id_msg' => __('Please enter email id', 'recoverabandoncart'),
                'rac_valid_text_field_msg' => __('Please Enter any Value', 'recoverabandoncart'),
                'rac_valid_search_field_msg' => __('Please select any Product/Category', 'recoverabandoncart'),
                'load_email_template' => wp_create_nonce('load-email-template'),
                'save_email_template' => wp_create_nonce('save-email-template'),
                'manual_send_email_template' => wp_create_nonce('manual-send-email-template'),
                'delete_email_template' => wp_create_nonce('delete-email-template'),
                'copy_email_template' => wp_create_nonce('copy-email-template'),
                'email_template_status' => wp_create_nonce('email-template-status'),
            ));
            //enqueue script
            wp_enqueue_script('fp_email_template_ajax');
        }

        public static function cartlist_tab_enqueue_scripts() {
            //register script
            wp_register_script('fp_rac_cartlist_tab', RAC_PLUGIN_URL . '/assets/js/tabs/fp-rac-cart-list-tab.js', array('jquery'), RAC_VERSION);
            //localize script
            wp_localize_script('fp_rac_cartlist_tab', 'fp_rac_cartlist_tab_obj', array(
                'rac_cart_list_manual_recovered_alert' => __('Do you want to change the status of this cart to Recovered?', 'recoverabandoncart'),
                'rac_save_label' => __('save', 'recoverabandoncart'),
                'update_guest_email' => wp_create_nonce('update-guest-email'),
                'recover_status' => wp_create_nonce('recover-status'),
                'mailstatus_cartlist' => wp_create_nonce('mailstatus-cartlist'),
                'rac_unsubcribe_nonce' => wp_create_nonce('unsubscribe-email'),
            ));
            //enqueue script
            wp_enqueue_script('fp_rac_cartlist_tab');
        }

        public static function previous_orders_tab_enqueue_scripts() {
            //register script
            wp_register_script('fp_rac_previous_order_tab', RAC_PLUGIN_URL . '/assets/js/tabs/fp-rac-previous-orders-tab.js', array('jquery'), RAC_VERSION);
            //localize script
            wp_localize_script('fp_rac_previous_order_tab', 'fp_rac_previous_order_tab_obj', array(
                'rac_updated_count' => __("Orders found and added to Abandon List", "recoverabandoncart"),
                'rac_empty_order_message' => __("No Orders found", "recoverabandoncart"),
                'rac_chunk_count' => get_option('rac_chunk_count_per_ajax', true),
                'oldorder_cartlist' => wp_create_nonce('oldorder-cartlist'),
            ));
            //enqueue script
            wp_enqueue_script('fp_rac_previous_order_tab');
        }

        public static function troubleshoot_enqueue_scripts() {
            //register script
            wp_register_script('fp_rac_troubleshoot_tab', RAC_PLUGIN_URL . '/assets/js/tabs/fp-rac-troubleshoot-tab.js', array('jquery'), RAC_VERSION);
            //localize script
            wp_localize_script('fp_rac_troubleshoot_tab', 'fp_rac_troubleshoot_tab_obj', array(
                'rac_mail_success_message' => __('Mail has been Sent, but this doesn\'t mean mail will be delivered Successfully. Check Wordpress Codex for More info on Mail', 'recoverabandoncart'),
                'rac_mail_failure_message' => __('Mail not Sent.', 'recoverabandoncart'),
                'rac_email_function_msg' => __('For WooCommerce 2.3 or higher version mail() function will not load the woocommerce default template. This option will be deprecated', 'recoverabandoncart'),
                'test_email' => wp_create_nonce('test-email'),
                'update_data' => wp_create_nonce('oldorder-update'),
                'rac_alert_message' => __('Are you sure you want to proceed with the updation? Based on data available, it may take some time to update', 'recoverabandoncart'),
                'rac_chunk_count' => get_option('rac_chunk_count_per_ajax', true),
                'rac_update_success_message' => __('Update Completed', 'recoverabandoncart'),
            ));
            //enqueue script
            wp_enqueue_script('fp_rac_troubleshoot_tab');
        }

        public static function coupon_tab_enqueue_scripts() {
            global $woocommerce;
            //register script
            wp_register_script('fp_rac_coupon_tab', RAC_PLUGIN_URL . '/assets/js/tabs/fp-rac-coupon-tab.js', array('jquery'), RAC_VERSION);
            //enqueue script
            wp_enqueue_script('fp_rac_coupon_tab');
        }

        public static function remaining_tabs_enqueue_scripts() {
            global $woocommerce;
            //register script
            wp_register_script('fp_rac_remaining_tabs', RAC_PLUGIN_URL . '/assets/js/tabs/fp-rac-remaining-tabs.js', array('jquery'), RAC_VERSION);
            //localize script
            wp_localize_script('fp_rac_remaining_tabs', 'fp_rac_remaining_tabs_obj', array(
                'rac_wc_version' => (float) $woocommerce->version,
                'rac_chunk_count' => get_option('rac_chunk_count_per_ajax'),
                'rac_admin_url' => admin_url(),
            ));
            //enqueue script
            wp_enqueue_script('fp_rac_remaining_tabs');
        }

        public static function graph_enqueue_scripts() {
            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            wp_register_script('flot', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot' . $suffix . '.js', array('jquery'), WC_VERSION);
            wp_register_script('flot-resize', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.resize' . $suffix . '.js', array('jquery', 'flot'), WC_VERSION);
            wp_register_script('flot-time', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.time' . $suffix . '.js', array('jquery', 'flot'), WC_VERSION);
            wp_register_script('flot-pie', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.pie' . $suffix . '.js', array('jquery', 'flot'), WC_VERSION);
            wp_register_script('flot-stack', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.stack' . $suffix . '.js', array('jquery', 'flot'), WC_VERSION);
            wp_enqueue_script('flot');
            wp_enqueue_script('flot-resize');
            wp_enqueue_script('flot-time');
            wp_enqueue_script('flot-pie');
            wp_enqueue_script('flot-stack');
        }

    }

    FP_RAC_Enqueue_Scripts::init();
}
