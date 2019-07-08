<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_General_Tab')) {

    /**
     * FP_RAC_General_Tab Class.
     */
    class FP_RAC_General_Tab {

        public static function init() {
            add_action('woocommerce_admin_field_rac_cnext_cron_job', array(__CLASS__, 'rac_cnext_cron_job'));
            add_action('fp_rac_default_settings_fpracgeneral', array(__CLASS__, 'fprac_general_default_settings'));
            add_action('fp_rac_display_buttons_fpracgeneral', array(__CLASS__, 'fp_rac_admin_setting_buttons'));
            add_action('update_option_rac_abandon_cron_time', array(__CLASS__, 'fp_rac_update_cron_job_setting'));
            add_action('woocommerce_update_options_fpracgeneral', array(__CLASS__, 'fp_rac_update_options_general'));
            add_action('update_option_rac_abandon_cart_cron_type', array(__CLASS__, 'fp_rac_update_cron_job_setting'));
            add_action('woocommerce_fprac_settings_tabs_fpracgeneral', array(__CLASS__, 'fp_rac_admin_setting_general'));
            add_action('fp_rac_reset_default_settings_fpracgeneral', array(__CLASS__, 'fp_rac_admin_reset_setting_general'));
            add_action('woocommerce_admin_field_rac_exclude_users_list', array(__CLASS__, 'rac_selected_users_exclude_option'));
            add_action('woocommerce_admin_field_rac_email_cron_time_option', array(__CLASS__, 'rac_email_cron_time_option'));
            add_action('woocommerce_admin_field_rac_abandoned_cart_time_option', array(__CLASS__, 'rac_abandoned_cart_time_option'));
            add_action('woocommerce_admin_field_rac_exclude_users_list_for_restrict_in_cart_list', array(__CLASS__, 'rac_selected_users_restrict_option'));
        }

        public static function fp_rac_menu_options_general() {
            //without guest user roles
            $user_role = fp_rac_user_roles();
            $guest_role = array('rac_guest' => __('Guest', 'woocommerce'));
            //including guest user roles
            $user_guest_role = fp_rac_user_roles($guest_role);
            $admin_mail = get_option('admin_email');
            global $woocommerce, $product;
            $orderlist_combine = fp_rac_get_order_status();
            $cart_list_status = array(
                'NEW' => __('NEW', 'recoverabandoncart'),
                'ABANDON' => __('ABANDON', 'recoverabandoncart'),
                'RECOVERED' => __('RECOVERED', 'recoverabandoncart')
            );
            $remove_options = array(
                'rac-cart-abandon' => __('ABANDON', 'recoverabandoncart'),
                'rac-cart-recovered' => __('RECOVERED', 'recoverabandoncart')
            );
            return apply_filters('woocommerce_fpracgeneral_settings', array(
                array(
                    'name' => __('Time Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_time_settings',
                ),
                array(
                    'type' => 'rac_abandoned_cart_time_option'
                ),
                array('type' => 'sectionend', 'id' => 'rac_time_settings'), //Time Settings END
                array(
                    'name' => __('Email Cron Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_cron_settings',
                ),
                array(
                    'type' => 'rac_email_cron_time_option'
                ),
                array(
                    'type' => 'rac_cnext_cron_job',
                ),
                array('type' => 'sectionend', 'id' => 'rac_cron_settings'), //Cron Settings END
                array(
                    'name' => __('Cart List Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_cartlist_settings',
                ),
                array(
                    'name' => __('When Multiple Cart Entries are about to be Captured for the Same Email ID then', 'recoverabandoncart'),
                    'type' => 'select',
                    'options' => array(
                        'no' => __('Capture all the carts', 'recoverabandoncart'),
                        'yes' => __('Remove old carts and capture new cart', 'recoverabandoncart'),
                        'pre_cart' => __("Don't capture any new cart", 'recoverabandoncart')
                    ),
                    'default' => 'yes',
                    'std' => 'yes',
                    'id' => 'rac_remove_carts',
                    'clone_id' => 'rac_remove_carts',
                ),
                array(
                    'name' => __('Remove Carts with "NEW" Status', 'recoverabandoncart'),
                    'desc' => __('Enabling this option will remove New Carts by same Users', 'recoverabandoncart'),
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'std' => 'yes',
                    'id' => 'rac_remove_new',
                    'class' => 'rac_remove_hide rac_remove_status_yes',
                    'clone_id' => 'rac_remove_new',
                ),
                array(
                    'name' => __('Remove Carts with "ABANDON" Status', 'recoverabandoncart'),
                    'desc' => __('Enabling this option will remove Abandon Carts by same Users', 'recoverabandoncart'),
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'std' => 'yes',
                    'id' => 'rac_remove_abandon',
                    'class' => 'rac_remove_hide rac_remove_status_yes',
                    'clone_id' => 'rac_remove_abandon',
                ),
                array(
                    'name' => __('Restrict Cart List Capturing when Old Carts of the User is in any One of the Following Status', 'recoverabandoncart'),
                    'id' => 'rac_dont_capture_for_option',
                    'class' => 'rac_dont_capture_for_option rac_remove_hide rac_remove_status_pre_cart',
                    'css' => 'min-width:153px',
                    'type' => 'multiselect',
                    'options' => $cart_list_status,
                    'std' => array('NEW', 'ABANDON'),
                    'default' => array('NEW', 'ABANDON'),
                    'newids' => 'rac_dont_capture_for_option',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Create an Entry in Cart List Table when the Order reaches "Failed" status', 'recoverabandoncart'),
                    'desc' => __('If enabled, an entry will be added in "Cart List" when the order reaches "Failed" status', 'recoverabandoncart'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'std' => 'no',
                    'id' => 'rac_insert_abandon_cart_when_order_failed',
                    'clone_id' => 'rac_insert_abandon_cart_when_order_failed',
                ),
                array(
                    'name' => __('Create an Entry in Cart List Table when the User Cancels the Order from the Third Party Payment Page', 'recoverabandoncart'),
                    'desc' => __('If enabled, an entry will be added in "Cart List" when the user cancels the order from the third party payment pages', 'recoverabandoncart'),
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'std' => 'yes',
                    'id' => 'rac_insert_abandon_cart_when_os_cancelled',
                    'clone_id' => 'rac_insert_abandon_cart_when_os_cancelled',
                ),
                array(
                    'name' => __('Prevent Adding Additional Cart Entry when the Order is Cancelled by the Same User', 'recoverabandoncart'),
                    'desc' => __('Enabling this option will Prevent adding "New" cart when order cancelled in cart page', 'recoverabandoncart'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'std' => 'no',
                    'id' => 'rac_prevent_entry_in_cartlist_while_order_cancelled_in_cart_page',
                    'clone_id' => 'rac_prevent_entry_in_cartlist_while_order_cancelled_in_cart_page',
                ),
                array(
                    'name' => __('Remove Carts after x Days', 'recoverabandoncart'),
                    'desc' => __('If "Yes" is selected, you can remove captured carts with specific status after specific number of days', 'recoverabandoncart'),
                    'id' => 'enable_remove_abandon_after_x_days',
                    'css' => 'min-width:153px',
                    'type' => 'select',
                    'options' => array('yes' => 'Yes', 'no' => 'No'),
                    'std' => 'no',
                    'default' => 'no',
                    'clone_id' => 'enable_remove_abandon_after_x_days',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Remove Carts after', 'recoverabandoncart'),
                    'desc' => __("day(s)", 'recoverabandoncart'),
                    'id' => 'rac_remove_abandon_after_x_days',
                    'clone_id' => 'rac_remove_abandon_after_x_days',
                    'type' => 'number',
                    'std' => '30',
                    'default' => '30',
                    'custom_attributes' => array('min' => '1'),
                ),
                array(
                    'name' => __('Remove Carts with the Following Status', 'recoverabandoncart'),
                    'desc' => __('carts will be removed from cart list table based on selected status', 'recoverabandoncart'),
                    'id' => 'rac_delete_cart_selection',
                    'css' => 'min-width:153px',
                    'type' => 'multiselect',
                    'options' => $remove_options,
                    'std' => array('rac-cart-abandon'),
                    'default' => array('rac-cart-abandon'),
                    'custom_attributes' => array('required' => 'required'),
                    'newids' => 'rac_delete_cart_selection',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Custom Restriction Settings', 'recoverabandoncart'),
                    'desc' => __('Selected User Roles and Names To Restrict Entry in Cart List', 'recoverabandoncart'),
                    'id' => 'custom_restrict',
                    'css' => 'min-width:153px',
                    'type' => 'select',
                    'options' => array(
                        'user_role' => __('User Role', 'recoverabandoncart'),
                        'name' => __('Name', 'recoverabandoncart'),
//                        'mail_id' => __('Mail ID', 'recoverabandoncart'),
//                        'email_provider' => __('Email Provider', 'recoverabandoncart')
                    ),
                    'std' => 'user_role',
                    'default' => 'user_role',
                    'clone_id' => 'custom_restrict',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Cart List Capturing is', 'recoverabandoncart'),
                    'id' => 'custom_include_exclude_entry',
                    'css' => 'min-width:100px',
                    'type' => 'select',
                    'options' => array(
                        'exclude' => __('Not allowed for selected User Role(s)/User Name(s)', 'recoverabandoncart'),
                        'include' => __('Allowed for selected User Role(s)/User Name(s)', 'recoverabandoncart'),
                    ),
                    'std' => 'exclude',
                    'default' => 'exclude',
                    'clone_id' => 'custom_include_exclude_entry',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Select User Role', 'recoverabandoncart'),
                    'desc' => __('Enter the First Three Characters of User Role', 'recoverabandoncart'),
                    'id' => 'custom_user_role_for_restrict_in_cart_list',
                    'css' => 'min-width:150px',
                    'type' => 'multiselect',
                    'std' => '',
                    'default' => '',
                    'options' => $user_role,
                    'clone_id' => 'custom_user_role_for_restrict_in_cart_list',
                    'class' => 'rac_cart_sh_class rac_show_user_role',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Select Users', 'recoverabandoncart'),
                    'desc' => __('Enter the First Three Characters of User Name', 'recoverabandoncart'),
                    'id' => 'custom_user_name_select_for_restrict_in_cart_list',
                    'css' => 'min-width:400px',
                    'std' => '',
                    'default' => '',
                    'type' => 'rac_exclude_users_list_for_restrict_in_cart_list',
                    'clone_id' => 'custom_user_name_select_for_restrict_in_cart_list',
                    'desc_tip' => true,
                ),
                //for feature use
//                array(
//                    'name' => __('Custom Mail ID Selected', 'recoverabandoncart'),
//                    'desc' => __('Enter Mail ID per line which will be restricted to includes an entry in Cart List', 'recoverabandoncart'),
//                    'id' => 'custom_mailid_for_restrict_in_cart_list',
//                    'clone_id' => 'custom_mailid_for_restrict_in_cart_list',
//                    'type' => 'textarea',
//                    'css' => 'min-width:500px;min-height:200px',
//                    'std' => '',
//                    'class' => 'rac_cart_sh_class rac_show_mail_id',
//                    'desc_tip' => true,
//                ),
//                array(
//                    'name' => __('Custom Email Provider Selected', 'recoverabandoncart'),
//                    'desc' => __('Enter Mail ID per line which will be restricted to includes an entry in Cart List', 'recoverabandoncart'),
//                    'id' => 'custom_email_provider_for_restrict_in_cart_list',
//                    'clone_id' => 'custom_email_provider_for_restrict_in_cart_list',
//                    'type' => 'textarea',
//                    'css' => 'min-width:500px;min-height:200px',
//                    'std' => '',
//                    'class' => 'rac_cart_sh_class rac_show_email_provider',
//                    'desc_tip' => true,
//                ),
                array('type' => 'sectionend', 'id' => 'rac_cartlist_settings'), //Carts List Settings END
                array(
                    'name' => __('Email Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_email_gen_settings',
                ),
                array(
                    'name' => __('Send Email to Members', 'recoverabandoncart'),
                    'desc' => __('If enabled, emails will be send to members', 'recoverabandoncart'),
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'std' => 'yes',
                    'id' => 'rac_email_use_members',
                    'clone_id' => 'rac_email_use_members',
                ),
                array(
                    'name' => __('Send Email to Guests', 'recoverabandoncart'),
                    'desc' => __('If enabled, emails will be send to guests', 'recoverabandoncart'),
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'std' => 'yes',
                    'id' => 'rac_email_use_guests',
                    'clone_id' => 'rac_email_use_guests',
                ),
                array(
                    'name' => __('Email Sending Method', 'recoverabandoncart'),
                    'id' => 'rac_mail_template_send_method',
                    'css' => 'min-width:153px',
                    'type' => 'select',
                    'options' => array('abandon_time' => __('Based on abandoned cart time', 'recoverabandoncart'), 'template_time' => __('Based on previous email sent time', 'recoverabandoncart')),
                    'std' => 'abandon_time',
                    'default' => 'abandon_time',
                    'clone_id' => 'rac_mail_template_send_method',
                ),
                array(
                    'name' => __('Email Sending Priority ', 'recoverabandoncart'),
                    'id' => 'rac_mail_template_sending_priority',
                    'css' => 'min-width:153px',
                    'type' => 'select',
                    'options' => array('mailduration' => __('Email duration', 'recoverabandoncart'), 'mailsequence' => __('Email sequence', 'recoverabandoncart')),
                    'std' => 'mailduration',
                    'default' => 'mailduration',
                    'clone_id' => 'rac_mail_template_sending_priority',
                ),
                array(
                    'name' => __("Restrict Automatic Abandoned Cart Emails if Captured Cart Contains a Product which was already Purchased", "recoverabandoncart"),
                    'desc' => __('If enabled, automatic abandoned cart emails will not be sent if captured cart contains a product which was already purchased', 'recoverabandoncart'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'std' => 'no',
                    'id' => 'rac_email_restrict_when_cutomer_already_bought_product',
                    'clone_id' => 'rac_email_restrict_when_cutomer_already_bought_product',
                ),
                array(
                    'name' => __('Custom Restriction Settings', 'recoverabandoncart'),
                    'desc' => __('Select User Roles, Names and Email ID To Stop EMail Sending', 'recoverabandoncart'),
                    'id' => 'custom_exclude',
                    'css' => 'min-width:153px',
                    'type' => 'select',
                    'options' => array(
                        'user_role' => __('User Role', 'recoverabandoncart'),
                        'name' => __('Name', 'recoverabandoncart'),
                        'mail_id' => __('Email ID', 'recoverabandoncart'),
                        'email_provider' => __('Email Provider', 'recoverabandoncart')
                    ),
                    'std' => 'user_role',
                    'default' => 'user_role',
                    'clone_id' => 'custom_exclude',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Abandoned Cart Emails are', 'recoverabandoncart'),
                    'id' => 'custom_include_exclude_email',
                    'css' => 'min-width:153px',
                    'type' => 'select',
                    'options' => array(
                        'exclude' => __('Not sent for the selected option', 'recoverabandoncart'),
                        'include' => __('Sent for the selected option', 'recoverabandoncart'),
                    ),
                    'std' => 'exclude',
                    'default' => 'exclude',
                    'clone_id' => 'custom_include_exclude_email',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Select User Role', 'recoverabandoncart'),
                    'id' => 'custom_user_role',
                    'css' => 'min-width:150px',
                    'type' => 'multiselect',
                    'std' => '',
                    'default' => '',
                    'options' => $user_guest_role,
                    'clone_id' => 'custom_user_role',
                    'class' => 'rac_email_sh_class rac_show_user_role',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('User Name Selected', 'recoverabandoncart'),
                    'desc' => __('Enter the First Three Character of User Name', 'recoverabandoncart'),
                    'id' => 'custom_user_name_select',
                    'css' => 'min-width:400px',
                    'std' => '',
                    'default' => '',
                    'type' => 'rac_exclude_users_list',
                    'clone_id' => 'custom_user_name_select',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Custom Mail ID Selected', 'recoverabandoncart'),
                    'desc' => __('Enter Mail ID per line which will be excluded to receive a mail from Recover Abandon Cart', 'recoverabandoncart'),
                    'id' => 'custom_mailid_edit',
                    'clone_id' => 'custom_mailid_edit',
                    'type' => 'textarea',
                    'css' => 'min-width:300px',
                    'std' => '',
                    'default' => '',
                    'class' => 'rac_email_sh_class rac_show_mail_id',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Restrict Email Providers', 'recoverabandoncart'),
                    'desc' => __('Enter the email providers seperated by comma. Do not enter any special characters', 'recoverabandoncart'),
                    'id' => 'custom_email_provider_edit',
                    'clone_id' => 'custom_email_provider_edit',
                    'type' => 'textarea',
                    'css' => 'min-width:300px',
                    'std' => '',
                    'default' => '',
                    'class' => 'rac_email_sh_class rac_show_email_provider',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rac_email_gen_settings'), //Email Settings END
                array(
                    'name' => __('Recover Status Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_mailcontrol_settings',
                ),
                array(
                    'name' => __('Change All the New/Abandon Cart Lists of a User when they Place on Order in the Site', 'recoverabandoncart'),
                    'desc' => __('Recover Cart List based on Order Status', 'recoverabandoncart'),
                    'tip' => '',
                    'id' => 'rac_cartlist_new_abandon_recover',
                    'class' => 'rac_cartlist_new_abandon_recover',
                    'css' => '',
                    'type' => 'checkbox',
                    'std' => 'yes',
                    'default' => 'yes',
                    'newids' => 'rac_cartlist_new_abandon_recover',
                    'clone_id' => 'rac_cartlist_new_abandon_recover',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Allow Manual Orders to Recover Cart List', 'recoverabandoncart'),
                    'desc' => __('Enable this Option will help to recover cart list based on manually created orders.', 'recoverabandoncart'),
                    'id' => 'rac_cartlist_new_abandon_recover_by_manual_order',
                    'class' => 'rac_cart_depends_parent_new_abandon_option',
                    'type' => 'checkbox',
                    'std' => 'yes',
                    'default' => 'yes',
                    'newids' => 'rac_cartlist_new_abandon_recover_by_manual_order',
                    'clone_id' => 'rac_cartlist_new_abandon_recover_by_manual_order',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('New Status to Recovered Status', 'recoverabandoncart'),
                    'desc' => __('Based on Order Status change New Status to Recovered Status', 'recoverabandoncart'),
                    'id' => 'rac_cartlist_change_from_new_to_recover',
                    'class' => 'rac_cart_depends_parent_new_abandon_option',
                    'type' => 'checkbox',
                    'std' => 'yes',
                    'default' => 'yes',
                    'newids' => 'rac_cartlist_change_from_new_to_recover',
                    'clone_id' => 'rac_cartlist_change_from_new_to_recover',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Abandon Status to Recovered Status', 'recoverabandoncart'),
                    'desc' => __('Based on Order Status change Abandon Status to Recovered Status', 'recoverabandoncart'),
                    'id' => 'rac_cartlist_change_from_abandon_to_recover',
                    'class' => 'rac_cart_depends_parent_new_abandon_option',
                    'type' => 'checkbox',
                    'std' => 'yes',
                    'default' => 'yes',
                    'newids' => 'rac_cartlist_change_from_abandon_to_recover',
                    'clone_id' => 'rac_cartlist_change_from_abandon_to_recover',
                    'desc_tip' => true,
                ),
                array(
                    'name' => __('Change Status of the Captured Cart Entry to Recovered when Order status becomes', 'recoverabandoncart'),
                    'desc' => __('Status of captured cart entry will be changed to "Recovered" if the order status of placed order reaches any one of the selected status', 'recoverabandoncart'),
                    'id' => 'rac_mailcartlist_change',
                    'class' => 'rac_mailcartlist_change',
                    'css' => 'min-width:153px',
                    'type' => 'multiselect',
                    'options' => $orderlist_combine,
                    'std' => array('completed', 'processing'),
                    'default' => array('completed', 'processing'),
                    'newids' => 'rac_mailcartlist_change',
                    'desc_tip' => true,
                ),
                array('type' => 'sectionend', 'id' => 'rac_mailcontrol_settings'), // Settings END
                array(
                    'name' => __('Admin Notification Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_notification_settings',
                ),
                array(
                    'name' => __('Notify Admin by Email when an Order is Recovered', 'recoverabandoncart'),
                    'id' => 'rac_admin_cart_recovered_noti',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_admin_cart_recovered_noti',
                ),
                array(
                    'name' => __('Admin Email ID', 'recoverabandoncart'),
                    'id' => 'rac_admin_email',
                    'std' => $admin_mail,
                    'default' => $admin_mail,
                    'type' => 'text',
                    'newids' => 'rac_admin_email',
                    'class' => 'admin_notification'
                ),
                array(
                    'name' => __('Sender Option', 'recoverabandoncart'),
                    'id' => 'rac_recovered_sender_opt',
                    'std' => "woo",
                    'default' => "woo",
                    'type' => 'radio',
                    'newids' => 'rac_recovered_sender_opt',
                    'class' => 'admin_sender_opt',
                    'options' => array(
                        'woo' => __('WooCommerce', 'recoverabandoncart'),
                        'local' => __('Local', 'recoverabandoncart')
                    ),
                    'class' => 'admin_notifi_sender_opt'
                ),
                array(
                    'name' => __('From Name', 'recoverabandoncart'),
                    'id' => 'rac_recovered_from_name',
                    'std' => "",
                    'default' => "",
                    'type' => 'text',
                    'newids' => 'rac_recovered_from_name',
                    'class' => 'local_senders admin_notification'
                ),
                array(
                    'name' => __('From Email', 'recoverabandoncart'),
                    'id' => 'rac_recovered_from_email',
                    'std' => "",
                    'default' => "",
                    'type' => 'text',
                    'newids' => 'rac_recovered_from_email',
                    'class' => 'local_senders admin_notification'
                ),
                array(
                    'name' => __('Email Subject', 'recoverabandoncart'),
                    'id' => 'rac_recovered_email_subject',
                    'std' => "A cart has been Recovered",
                    'default' => "A cart has been Recovered",
                    'type' => 'text',
                    'newids' => 'rac_recovered_email_subject',
                    'class' => 'admin_notification'
                ),
                array(
                    'name' => __('Email Message', 'recoverabandoncart'),
                    'css' => 'min-height:250px;min-width:400px;',
                    'id' => 'rac_recovered_email_message',
                    'std' => "A cart has been Recovered. Here is the order ID {rac.recovered_order_id} for Reference and Line Items is here {rac.order_line_items}.",
                    'default' => "A cart has been Recovered. Here is the order ID {rac.recovered_order_id} for Reference and Line Items is here {rac.order_line_items}.",
                    'type' => 'textarea',
                    'newids' => 'rac_recovered_email_message',
                    'class' => 'admin_notification'
                ),
                array(
                    'name' => __('Notify Admin by Email when the cart is abandoned', 'recoverabandoncart'),
                    'id' => 'rac_admin_cart_abandoned_noti',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_admin_cart_abandoned_noti',
                ),
                array(
                    'name' => __('Admin Email ID', 'recoverabandoncart'),
                    'id' => 'rac_ca_admin_email',
                    'std' => $admin_mail,
                    'default' => $admin_mail,
                    'type' => 'text',
                    'newids' => 'rac_ca_admin_email',
                    'class' => 'admin_notification_ca'
                ),
                array(
                    'name' => __('Sender Option', 'recoverabandoncart'),
                    'id' => 'rac_abandoned_sender_opt',
                    'std' => "woo",
                    'default' => "woo",
                    'type' => 'radio',
                    'newids' => 'rac_abandoned_sender_opt',
                    'class' => 'admin_sender_opt_ca',
                    'options' => array(
                        'woo' => __('WooCommerce', 'recoverabandoncart'),
                        'local' => __('Local', 'recoverabandoncart')
                    ),
                    'class' => 'admin_notifi_sender_opt_ca'
                ),
                array(
                    'name' => __('From Name', 'recoverabandoncart'),
                    'id' => 'rac_abandoned_from_name',
                    'std' => "",
                    'default' => "",
                    'type' => 'text',
                    'newids' => 'rac_abandoned_from_name',
                    'class' => 'local_senders_ca admin_notification_ca'
                ),
                array(
                    'name' => __('From Email', 'recoverabandoncart'),
                    'id' => 'rac_abandoned_from_email',
                    'std' => "",
                    'default' => "",
                    'type' => 'text',
                    'newids' => 'rac_abandoned_from_email',
                    'class' => 'local_senders_ca admin_notification_ca'
                ),
                array(
                    'name' => __('Email Subject', 'recoverabandoncart'),
                    'id' => 'rac_abandoned_email_subject',
                    'std' => "A cart has been Abandoned",
                    'default' => "A cart has been Abandoned",
                    'type' => 'text',
                    'newids' => 'rac_abandoned_email_subject',
                    'class' => 'admin_notification_ca'
                ),
                array(
                    'name' => __('Email Message', 'recoverabandoncart'),
                    'css' => 'min-height:250px;min-width:400px;',
                    'id' => 'rac_abandoned_email_message',
                    'std' => "A cart has been Abandoned. Here is the details {rac.abandoned_cart}<br>by {rac.abandoned_username}.",
                    'default' => "A cart has been Abandoned. Here is the details {rac.abandoned_cart}<br>by {rac.abandoned_username}.",
                    'type' => 'textarea',
                    'newids' => 'rac_abandoned_email_message',
                    'class' => 'admin_notification_ca'
                ),
                array('type' => 'sectionend', 'id' => 'rac_notification_settings'), //Notification Settings END
                array(
                    'name' => __('Guest Cart Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_guestcart_settings',
                ),
                array(
                    'name' => __('Remove Guests Cart when the Order Status Changes to Pending', 'recoverabandoncart'),
                    'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Pending', 'recoverabandoncart'),
                    'id' => 'rac_guest_abadon_type_pending',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_guest_abadon_type_pending',
                ),
                array(
                    'name' => __('Remove Guests Cart when the Order Status Changes to Failed', 'recoverabandoncart'),
                    'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Failed', 'recoverabandoncart'),
                    'id' => 'rac_guest_abadon_type_failed',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_guest_abadon_type_failed',
                ),
                array(
                    'name' => __('Remove Guests Cart when the Order Status Changes to On-Hold', 'recoverabandoncart'),
                    'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become On-Hold', 'recoverabandoncart'),
                    'id' => 'rac_guest_abadon_type_on-hold',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_guest_abadon_type_on-hold',
                ),
                array(
                    'name' => __('Remove Guests Cart when the Order Status Changes to Processing', 'recoverabandoncart'),
                    'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Processing', 'recoverabandoncart'),
                    'id' => 'rac_guest_abadon_type_processing',
                    'std' => 'yes',
                    'default' => 'yes',
                    'type' => 'checkbox',
                    'newids' => 'rac_guest_abadon_type_processing',
                ),
                array(
                    'name' => __('Remove Guests Cart when the Order Status Changes to Completed', 'recoverabandoncart'),
                    'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Completed', 'recoverabandoncart'),
                    'id' => 'rac_guest_abadon_type_completed',
                    'std' => 'yes',
                    'default' => 'yes',
                    'type' => 'checkbox',
                    'newids' => 'rac_guest_abadon_type_completed',
                ),
                array(
                    'name' => __('Remove Guests Cart when the Order Status Changes to Refunded', 'recoverabandoncart'),
                    'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Refunded', 'recoverabandoncart'),
                    'id' => 'rac_guest_abadon_type_refunded',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_guest_abadon_type_refunded',
                ),
                array(
                    'name' => __('Remove Guests Cart when the Order Status Changes to Cancelled', 'recoverabandoncart'),
                    'desc' => __('Guest Cart Captured on place order will be in cart list, it will be removed when order become Cancelled', 'recoverabandoncart'),
                    'id' => 'rac_guest_abadon_type_cancelled',
                    'std' => 'no',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'newids' => 'rac_guest_abadon_type_cancelled',
                ),
                array('type' => 'sectionend', 'id' => 'rac_guestcart_settings'), //Cart Abadoned Guest Settings END
            ));
        }

        public static function fp_rac_admin_setting_general() {
            woocommerce_admin_fields(FP_RAC_General_Tab::fp_rac_menu_options_general());
        }

        public static function fp_rac_update_options_general() {
            woocommerce_update_options(FP_RAC_General_Tab::fp_rac_menu_options_general());
            $value = isset($_POST['custom_user_name_select_for_restrict_in_cart_list']) ? $_POST['custom_user_name_select_for_restrict_in_cart_list'] : '';
            update_option('custom_user_name_select_for_restrict_in_cart_list', $value);
            update_option('rac_abandon_cart_time', $_POST['rac_abandon_cart_time']);
            update_option('rac_abandon_cart_time_type', $_POST['rac_abandon_cart_time_type']);
            update_option('rac_abandon_cart_time_guest', $_POST['rac_abandon_cart_time_guest']);
            update_option('rac_abandon_cart_time_type_guest', $_POST['rac_abandon_cart_time_type_guest']);
            update_option('rac_abandon_cron_time', $_POST['rac_abandon_cron_time']);
            update_option('rac_abandon_cart_cron_type', $_POST['rac_abandon_cart_cron_type']);
        }

        public static function fprac_general_default_settings() {
            $settings = FP_RAC_General_Tab::fp_rac_menu_options_general();
            if (rac_check_is_array($settings)) {
                foreach ($settings as $setting)
                    if (isset($setting['id']) && isset($setting['std'])) {
                        if (!get_option($setting['id']))
                            add_option($setting['id'], $setting['std']);
                    }
            }
        }

        /*
         * Reset the value of fields to standard value.
         * 
         */

        public static function fp_rac_admin_reset_setting_general() {
            update_option('rac_abandon_cart_time_type', 'hours');
            update_option('rac_abandon_cart_time', '1');
            update_option('rac_abandon_cart_time_type_guest', 'hours');
            update_option('rac_abandon_cart_time_guest', '1');
            update_option('rac_abandon_cart_cron_type', 'hours');
            update_option('rac_abandon_cron_time', '12');
            update_option('rac_admin_cart_recovered_noti', 'no');
            update_option('admin_notifi_sender_opt', 'woo');
            update_option('rac_recovered_email_subject', 'A cart has been Recovered');
            update_option('rac_recovered_email_message', 'A cart has been Recovered. Here is the order ID {rac.recovered_order_id} for Reference.');
            update_option('rac_admin_cart_abandoned_noti', 'no');
            update_option('admin_notifi_sender_opt_ca', 'woo');
            update_option('rac_abandoned_email_subject', 'A cart has been Abandoned');
            update_option('rac_abandoned_email_message', 'A cart has been Abandoned. Here is the details {rac.abandoned_cart}<br>by {rac.abandoned_username}.');
        }

        public static function fp_rac_admin_setting_buttons() {
            ?>
            <input class="button-secondary" id="rac_reset" type="button" name="rac_reset" value="<?php _e("Reset", "recoverabandoncart") ?>">

            <span class="submit" style="margin-left: 25px;">
                <?php if (!isset($GLOBALS['hide_save_button'])) : ?>
                    <input name="save" class="button-primary" type="submit" value="<?php _e('Save', 'recoverabandoncart'); ?>" />
                <?php endif; ?>
                <input type="hidden" name="subtab" id="last_tab" />
                <?php wp_nonce_field('woocommerce-settings'); ?>
            </span><?php
        }

        public static function rac_selected_users_restrict_option() {
            echo rac_common_function_to_multi_select_for_customer_search('custom_user_name_select_for_restrict_in_cart_list', 'User Name Selected');
        }

        public static function rac_cnext_cron_job() {
            ?>
            <table class="widefat" style="width: 35%">
                <thead>
                    <tr>
                        <th><?php _e('Email Job hook', 'recoverabandoncart') ?></th>
                        <th><?php _e('Next Run', 'recoverabandoncart') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php _e('rac_cron_job', 'recoverabandoncart') ?>
                        </td>
                        <td>
                            <?php
                            if ($timestamp = wp_next_scheduled('rac_cron_job')) {
                                $date_format = get_option('date_format');
                                $time_format = get_option('time_format');
                                $date = date($date_format, $timestamp);
                                $time = date($time_format, $timestamp);
                                $date_time_format = gmdate('Y-m-d H:i:s', $timestamp);
                                echo 'UTC time = ' . $date . ' / ' . $time . '</br>';
                                echo 'Local time = ' . get_date_from_gmt($date_time_format, $date_format) . ' / ' . get_date_from_gmt($date_time_format, $time_format) . '</br>';
                            } else {
                                _e('Cron is not set', 'recoverabandoncart');
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h4><?php _e("Note: Please don/'t consider 'Next Run' if you are using server cron on your site", 'recoverabandoncart') ?></h4>
            <?php
        }

        /**
         * Set Cron Event as "rac_cron_job" when update cron values.
         * 
         */
        public static function fp_rac_update_cron_job_setting() {
            wp_clear_scheduled_hook('rac_cron_job');
            if (wp_next_scheduled('rac_cron_job') == false) {
                wp_schedule_event(time(), 'xhourly', 'rac_cron_job');
            }
        }

        public static function rac_abandoned_cart_time_option() {
            $member_cart_time = get_option('rac_abandon_cart_time');
            $member_cart_time = ($member_cart_time) ? $member_cart_time : 1;
            $member_cart_time_type = get_option('rac_abandon_cart_time_type');
            $member_cart_time_type = ($member_cart_time_type) ? $member_cart_time_type : 'hours';
            $guest_cart_time = get_option('rac_abandon_cart_time_guest');
            $guest_cart_time = ($guest_cart_time) ? $guest_cart_time : 1;
            $guest_cart_time_type = get_option('rac_abandon_cart_time_type_guest');
            $guest_cart_time_type = ($guest_cart_time_type) ? $guest_cart_time_type : 'hours';
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rac_abandon_cart_time"><?php _e('Time to consider Cart as Abandoned for Members', 'recoverabandoncart'); ?></label>
                    <?php
                    $tooltip_html = __('This setting controls the minimum waiting time for members after which a cart will be considered as abandoned', 'recoverabandoncart');
                    echo fp_rac_tool_tip($tooltip_html);
                    ?>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" id="rac_abandon_cart_time" style="width:45px;height:28px" data-min = "0.01" name="rac_abandon_cart_time" class="fp_text_min_max" value="<?php echo $member_cart_time; ?>"/>
                    <select id="rac_abandon_cart_time_type" name="rac_abandon_cart_time_type" style="width:100px;margin-top: -2px;">
                        <?php echo FP_RAC_Email_Template::rac_mail_duration_select_options($member_cart_time_type) ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rac_abandon_cart_time_guest"><?php _e('Time to consider Cart as Abandoned for Guests', 'recoverabandoncart'); ?></label>
                    <?php
                    $tooltip_html = __('This setting controls the minimum waiting time for guests after which a cart will be considered as abandoned', 'recoverabandoncart');
                    echo fp_rac_tool_tip($tooltip_html);
                    ?>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" id="rac_abandon_cart_time_guest" style="width:45px;height:28px" data-min = "0.01" name="rac_abandon_cart_time_guest" class="fp_text_min_max" value="<?php echo $guest_cart_time; ?>"/>
                    <select id="rac_abandon_cart_time_type_guest" name="rac_abandon_cart_time_type_guest" style="width:100px;margin-top: -2px;">
                        <?php echo FP_RAC_Email_Template::rac_mail_duration_select_options($guest_cart_time_type) ?>
                    </select>
                </td>
            </tr>
            <?php
        }

        public static function rac_email_cron_time_option() {
            $email_cron_time = get_option('rac_abandon_cron_time');
            $email_cron_time = ($email_cron_time) ? $email_cron_time : 1;
            $email_cron_time_type = get_option('rac_abandon_cart_cron_type');
            $email_cron_time_type = ($email_cron_time_type) ? $email_cron_time_type : 'hours';
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="rac_abandon_cron_time"><?php _e('Email Cron Job Running Time', 'recoverabandoncart'); ?></label>
                    <?php
                    $tooltip_html = __('This setting controls the recurrence duration of cron job to run. Used for sending automatic abandoned cart emails. Note: Set to a lesser duration in order to send emails more frequently', 'recoverabandoncart');
                    echo fp_rac_tool_tip($tooltip_html);
                    ?>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" id="rac_abandon_cron_time" style="width:45px;height:28px" data-min = "0.01" name="rac_abandon_cron_time" class="fp_text_min_max" value="<?php echo $email_cron_time; ?>"/>
                    <select id="rac_abandon_cart_cron_type" name="rac_abandon_cart_cron_type" style="width:100px;margin-top: -2px;">
                        <?php echo FP_RAC_Email_Template::rac_mail_duration_select_options($email_cron_time_type) ?>
                    </select>
                </td>
            </tr>
            <?php
        }

        public static function rac_selected_users_exclude_option() {
            echo rac_common_function_to_multi_select_for_customer_search('custom_user_name_select', 'User Name Selected');
        }

    }

    FP_RAC_General_Tab::init();
}