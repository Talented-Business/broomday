<?php
if (!class_exists('FP_RAC_Submenu')) {

    /**
     * FP_RAC_Install Class.
     */
    class FP_RAC_Submenu {

        /**
         * FP_RAC_Install Class initialization.
         */
        public static function init() {
            add_action('admin_menu', array(__CLASS__, 'fprac_admin_submenu'));
            add_filter('woocommerce_fprac_settings_tabs_array', array(__CLASS__, 'fprac_settings_tabs'));
            add_filter('woocommerce_screen_ids', array(__CLASS__, 'fp_rac_screen_ids'), 9, 1);
            add_filter('screen_settings', array(__CLASS__, 'fp_rac_sorting_table_option'), 10, 2);
            add_filter('init', array(__CLASS__, 'fp_rac_set_screen_option_value'));
        }

        /**
         * Initializing the Submenu from Recovered Abandon Cart.
         */
        public static function fprac_admin_submenu() {
            add_submenu_page('edit.php?post_type=raccartlist', __('Reports', 'recoverabandoncart'), __('Reports', 'recoverabandoncart'), 'manage_woocommerce', 'fprac_reports_slug', array('FP_RAC_Submenu', 'fprac_reports_submenu'));
            add_submenu_page('edit.php?post_type=raccartlist', __('Settings', 'recoverabandoncart'), __('Settings', 'recoverabandoncart'), 'manage_woocommerce', 'fprac_slug', array('FP_RAC_Submenu', 'fprac_admin_settings'));
        }

        public static function fp_rac_sorting_table_option($screen_settings, $screen_object) {

            $post_type_array = array(
                'edit-racemailtemplate' => 'rac_display_template_basedon_asc_desc',
                'edit-raccartlist' => 'rac_display_cart_list_basedon_asc_desc',
                'edit-racmaillog' => 'rac_display_mail_log_basedon_asc_desc',
                'edit-racrecoveredorder' => 'rac_display_recovered_orders_basedon_asc_desc',
            );
            $custom_post_types = array('racemailtemplate', 'raccartlist', 'racmaillog', 'racrecoveredorder');
            if (isset($_GET['post_type']) && in_array($_GET['post_type'], $custom_post_types)) {
                $post_type = 'edit-' . $_GET['post_type'];
                $option_name = $post_type_array[$post_type];
                $option_value = get_user_option($option_name);
                //backward compatibility of this plugin
                ob_start();
                ?><fieldset>
                    <legend><?php _e('Sorting', 'recoverabandoncart'); ?></legend>
                    <label for=""><?php _e('Display Table in', 'recoverabandoncart'); ?></label>
                    <select id="<?php echo $option_name; ?>" name="fp_rac_screen_options[value]">
                        <option value="yes" <?php selected($option_value, 'yes') ?>><?php _e('Ascending Order', 'recoverabandoncart'); ?></option>
                        <option value="no" <?php selected($option_value, 'no') ?>><?php _e('Descending Order', 'recoverabandoncart'); ?></option>
                    </select>
                    <input type="hidden" name="fp_rac_screen_options[option]" value="<?php echo $option_name; ?>" />
                    <input type="hidden" name="fp_rac_screen_options_for[option]" value="<?php echo $_GET['post_type']; ?>" />
                </fieldset><?php
                $extra_settings = ob_get_clean();
                $screen_settings .= $extra_settings;
            }
            return $screen_settings;
        }

        /*
         * Update the Values to page screen option.
         * 
         */

        public static function fp_rac_set_screen_option_value() {
            if (isset($_POST['fp_rac_screen_options']) && is_array($_POST['fp_rac_screen_options']) && isset($_POST['fp_rac_screen_options_for']) && is_array($_POST['fp_rac_screen_options_for'])) {

                if (!$user = wp_get_current_user())
                    return;
                $post_type_array = array(
                    'racemailtemplate' => 'rac_display_template_basedon_asc_desc',
                    'raccartlist' => 'rac_display_cart_list_basedon_asc_desc',
                    'racmaillog' => 'rac_display_mail_log_basedon_asc_desc',
                    'racrecoveredorder' => 'rac_display_recovered_orders_basedon_asc_desc',
                );
                $post_type = $_POST['fp_rac_screen_options_for']['option'];
                $option_name = $post_type_array[$post_type];
                $option_value = $_POST['fp_rac_screen_options']['value'];
                update_user_meta($user->ID, $option_name, $option_value);
            }
        }

        /*
         * Set Rac page Screen ID.
         */

        public static function fp_rac_screen_ids($array_screens) {
            $post_type_array = array('raccartlist', 'racmaillog', 'racemailtemplate', 'racrecoveredorder');
            if ((isset($_GET['post_type']) && in_array($_GET['post_type'], $post_type_array)) || (isset($_GET['post']) && in_array(get_post_type($_GET['post']), $post_type_array)) || (isset($_GET['page']) && $_GET['page'] == 'fprac_slug')) {
                $newscreenids = get_current_screen();
                $array_screens[] = $newscreenids->id;
            }
            return $array_screens;
        }

        /*
         * Initializing the Tabs.
         */

        public static function fprac_settings_tabs($tabs) {
            if (!is_array($tabs)) {
                $tabs = (array) $tabs;
            }

            $tabs['fpracgeneral'] = __('General Settings', 'recoverabandoncart');
            $tabs['fpracadvance'] = __('Advanced Settings', 'recoverabandoncart');
            $tabs['fpracupdate'] = __('Check Previous Orders', 'recoverabandoncart');
            $tabs['fpraccoupon'] = __('Coupon in Email', 'recoverabandoncart');
            $tabs['fpracdebug'] = __('Troubleshoot', 'recoverabandoncart');
            $tabs['fpracshortocode'] = __('Shortcodes', 'recoverabandoncart');
            $tabs['fpracsupport'] = __('Support', 'recoverabandoncart');

            return array_filter($tabs);
        }

        /*
         * Initializing the all settings submenu .
         */

        public static function fprac_admin_settings() {
            global $woocommerce, $woocommerce_settings, $current_section, $current_tab;
            $tabs = array();
            do_action('woocommerce_fprac_settings_start');
            $current_tab = ( empty($_GET['tab']) ) ? 'fpracgeneral' : sanitize_text_field(urldecode($_GET['tab']));
            $current_section = ( empty($_REQUEST['section']) ) ? '' : sanitize_text_field(urldecode($_REQUEST['section']));

            require_once RAC_PLUGIN_PATH . '/inc/compatibility/rac-settings-backward-compatibility.php';
            //include current page functionality.
            include_once RAC_PLUGIN_PATH . '/inc/admin/menu/tabs/class-fp-rac-' . $current_tab . '-tab.php';

            if (!empty($_POST['save'])) {
                if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'woocommerce-settings'))
                    die(__('Action failed. Please refresh the page and retry.', 'recoverabandoncart'));

                if (!$current_section) {
                    switch ($current_tab) {
                        default :
                            if (isset($woocommerce_settings[$current_tab]))
                                woocommerce_update_options($woocommerce_settings[$current_tab]);
                            // Trigger action for tab
                            do_action('woocommerce_update_options_' . $current_tab);
                            break;
                    }

                    do_action('woocommerce_update_options');

                    // Handle Colour Settings
                    if ($current_tab == 'general' && get_option('woocommerce_frontend_css') == 'yes') {
                        
                    }
                } else {
                    // Save section onlys
                    do_action('woocommerce_update_options_' . $current_tab . '_' . $current_section);
                }

                // Clear any unwanted data
                delete_transient('woocommerce_cache_excluded_uris');
                // Redirect back to the settings page
                $redirect = esc_url_raw(add_query_arg(array('saved' => 'true')));
                if (isset($_POST['subtab'])) {
                    wp_safe_redirect($redirect);
                    exit;
                }
            }

            if (isset($_GET['rac_reset'])) {
                do_action('fp_rac_reset_default_settings_' . $current_tab);
                $reset_true_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => $current_tab, 'resetted' => 'true'), RAC_ADMIN_URL));
                wp_redirect($reset_true_url);
                exit;
            }

            //display any warning, success or error message.
            echo self::fp_rac_display_tab_message();

            //check cron job define and enabled or disabled.
            echo self::fp_rac_display_cron_format_message();
            ?>

            <div class="wrap woocommerce">
                <form method="post" id="mainform" action="" enctype="multipart/form-data">
                    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br />
                    </div>

                    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
                        <?php
                        $tabs = apply_filters('woocommerce_fprac_settings_tabs_array', $tabs);
                        if (rac_check_is_array($tabs)) {
                            foreach ($tabs as $name => $label)
                                echo '<a href="' . admin_url('admin.php?page=fprac_slug&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
                        }
                        do_action('woocommerce_fprac_settings_tabs');
                        ?>
                    </h2>

                    <?php
                    if (!isset($_GET['rac_new_email']) && !isset($_GET['rac_edit_email']) && !isset($_GET['rac_send_email'])) {
                        //Initialize the Default settings.
                        do_action('fp_rac_default_settings_' . $current_tab);
                        //current tab execute function.
                        do_action('woocommerce_fprac_settings_tabs_' . $current_tab); // @deprecated hook
                        do_action('woocommerce_fprac_settings_' . $current_tab);
                        //display buttons
                        do_action('fp_rac_display_buttons_' . $current_tab);
                    }
                    if ($current_tab == 'fpracemail') {
                        include_once RAC_PLUGIN_PATH . '/inc/email-template/class-fp-rac-preview-email-template.php';
                        include_once RAC_PLUGIN_PATH . '/inc/email-template/class-fp-rac-email-template.php';
                    }
                    ?>
                </form>
            </div>
            <?php
        }

        /*
         * Display Messgaes About current tab when needed.
         */

        public static function fp_rac_display_tab_message() {
            // Get any returned messages
            $error = ( empty($_GET['wc_error']) ) ? '' : urldecode(stripslashes($_GET['wc_error']));
            $message = ( empty($_GET['wc_message']) ) ? '' : urldecode(stripslashes($_GET['wc_message']));

            if ($error || $message) {

                if ($error) {
                    return '<div id="message" class="error fade"><p><strong>' . esc_html($error) . '</strong></p></div>';
                } else {
                    return '<div id="message" class="updated fade"><p><strong>' . esc_html($message) . '</strong></p></div>';
                }
            } elseif (!empty($_GET['saved'])) {

                return '<div id="message" class="updated fade"><p><strong>' . __('Your settings have been saved.', 'recoverabandoncart') . '</strong></p></div>';
            } elseif (!empty($_GET['resetted'])) {
                return '<div id="message" class="updated fade"><p><strong>' . __('Your settings have been Restored.', 'recoverabandoncart') . '</strong></p></div>';
            }
        }

        /*
         * Display Warning Message When WP_Cron Disabled 
         */

        public static function fp_rac_display_cron_format_message() {
            ob_start();
            if (get_option('rac_cron_troubleshoot_format') != 'server_cron') {
                if (defined('DISABLE_WP_CRON')) {
                    if (DISABLE_WP_CRON == 'true') {
                        ?>
                        <div id='message' class='error'>
                            <p>
                                <strong><?php _e("wp_cron is disabled in your Site <br />Recover Abandoned Cart will not be able to send Cart Abandoned Emails to Users Automatically<br />To enable wp_cron, edit the config.php of your Wordpress installation and search for 'define('DISABLE_WP_CRON', 'true');' and set the value as false", 'recoverabandoncart') ?></strong>
                            </p>
                        </div>
                        <?php
                    } else {
                        echo self::fp_rac_check_automatic_mail_issue();
                    }
                } else {
                    echo self::fp_rac_check_automatic_mail_issue();
                }
            } else {
                echo self::fp_rac_check_automatic_mail_issue('false');
            }
            return ob_get_clean();
        }

        /*
         * Display Warning Message When Automatic Related Functionality problem
         */

        public static function fp_rac_check_automatic_mail_issue($bool = true) {
            ob_start();
            if (get_option('rac_email_use_members') == 'no' || get_option('rac_email_use_guests') == 'no') {
                ?>  <div id='message' class='error'>
                    <p>
                        <strong><?php _e('Automatic emailing has been disabled for Members/Guests!!!', 'recoverabandoncart'); ?></strong>
                    </p>
                </div><?php
            }if (!wp_next_scheduled('rac_cron_job') && $bool) {
                ?>  <div id='message' class='error'>
                    <p>
                        <strong><?php _e('Cron has not been set for sending Automatic Emails!!!', 'recoverabandoncart'); ?> </br>
                            <?php _e('Try Deactivating the plugin and activating it again.', 'recoverabandoncart'); ?></strong>
                    </p>
                </div><?php
            }
            return ob_get_clean();
        }

        /*
         * Display Reports Submenu 
         */

        public static function fprac_reports_submenu() {
            if (!isset($_GET['post_type']) && $_GET['post_type'] != 'raccartlist')
                return;
            if (!isset($_GET['page']) && $_GET['post_type'] != 'fprac_reports_slug')
                return;

            if (isset($_GET['rac_updating_action']) && $_GET['rac_updating_action'] == 'rac_updating_process') {
                $obj = new FP_Updating_Process();
                $obj->fp_display_progress_bar();
            } else {
                include_once RAC_PLUGIN_PATH . '/inc/admin/menu/class-fp-rac-fpracreport-submenu.php';
            }
        }

    }

    FP_RAC_Submenu::init();
}