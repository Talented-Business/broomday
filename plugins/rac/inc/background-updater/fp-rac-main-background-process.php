<?php
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('FP_RAC_Main_Function_Importing_Part')) {

    /**
     * FP_RAC_Main_Function_Importing_Part Class.
     */
    class FP_RAC_Main_Function_Importing_Part {

        public static $process_cartlist;
        public static $process_emailtemplate;
        public static $process_maillog;
        public static $process_recoveredorder;
        public static $progress_bar;
        public static $process_couponcode;
        public static $process_getoption;

        public static function init() {

            if (self::fp_rac_upgrade_file_exists()) {
                $background_files = array(
                    'WP_Async_Request' => untrailingslashit(WP_PLUGIN_DIR) . '/woocommerce/includes/libraries/wp-async-request.php',
                    'WP_Background_Process' => untrailingslashit(WP_PLUGIN_DIR) . '/woocommerce/includes/libraries/wp-background-process.php',
                    'FP_RAC_Email_Template_Background_Process' => RAC_PLUGIN_PATH . '/inc/background-updater/fp-rac-emailtemplate-background-process.php',
                    'FP_RAC_Cartlist_Background_Process' => RAC_PLUGIN_PATH . '/inc/background-updater/fp-rac-cartlist-background-process.php',
                    'FP_RAC_Emaillog_Background_Process' => RAC_PLUGIN_PATH . '/inc/background-updater/fp-rac-maillog-background-process.php',
                    'FP_RAC_Recovered_Order_Background_Process' => RAC_PLUGIN_PATH . '/inc/background-updater/fp-rac-recoveredorder-background-process.php',
                    'FP_RAC_Coupon_Code_Background_Process' => RAC_PLUGIN_PATH . '/inc/background-updater/fp-rac-couponcode-background-process.php',
                    'FP_RAC_Get_Option_Background_Process' => RAC_PLUGIN_PATH . '/inc/background-updater/fp-rac-get-option-background-process.php',
                    'FP_RAC_Updating_Process' => RAC_PLUGIN_PATH . '/inc/background-updater/class-fp-rac-updating-process.php',
                );
                if (rac_check_is_array($background_files)) {
                    foreach ($background_files as $classname => $file_path) {
                        if (!class_exists($classname))
                            include_once($file_path);
                    }
                }
                add_action('wp_ajax_rac_database_upgrade_process', array(__CLASS__, 'initiate_to_background_process'));
                add_action('wp_ajax_rac_database_coupon_upgrade_process', array(__CLASS__, 'initiate_to_coupon_background_process'));

                self::$process_cartlist = new FP_RAC_Cartlist_Background_Process();
                self::$process_emailtemplate = new FP_RAC_Email_Template_Background_Process();
                self::$process_maillog = new FP_RAC_Emaillog_Background_Process();
                self::$process_recoveredorder = new FP_RAC_Recovered_Order_Background_Process();
                self::$process_couponcode = new FP_RAC_Coupon_Code_Background_Process();
                self::$process_getoption = new FP_RAC_Get_Option_Background_Process();
                self::$progress_bar = new FP_Updating_Process();
            }
            add_action('admin_head', array(__CLASS__, 'display_notice_in_top'));
        }

        /*
         * get Overal Count of Old table data.
         */

        public static function fp_rac_get_old_table_count() {
            global $wpdb;
            $table_count = 0;
            $tablename_array = array(
                'templates_email',
                'email_logs',
                'abandoncart',
            );
            foreach ($tablename_array as $value) {
                $tablename = $wpdb->prefix . 'rac_' . $value;
                $table_exists = $wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", $tablename));
                $template_data = !empty($table_exists) ? $wpdb->get_results("SELECT * FROM $tablename") : array();

                $table_count += count($template_data);
            }
            $recovered_data = get_option('fp_rac_recovered_order_ids');
            $recovered_ids = array_filter($recovered_data ? $recovered_data : array());

            $table_count += count($recovered_ids);

            return $table_count;
        }

        /*
         * Check if Background Related Files exists
         */

        public static function fp_rac_upgrade_file_exists() {
            $async_file = file_exists(untrailingslashit(WP_PLUGIN_DIR) . '/woocommerce/includes/libraries/wp-async-request.php');
            $background_file = file_exists(untrailingslashit(WP_PLUGIN_DIR) . '/woocommerce/includes/libraries/wp-background-process.php');

            if ($async_file && $background_file)
                return true;

            return false;
        }

        /*
         * Display when required some updates for this plugin
         */

        public static function display_notice_in_top() {
            global $wpdb;
            $link = "<a id='rac_display_notice' data-methd='cron' href='#'>" . __('Click here', 'recoverabandoncart') . "</a>";
            $table_count = self::fp_rac_get_old_table_count();
            $coupon_code_array = get_option('rac_coupon_for_user');
            $coupon_get_option = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name LIKE %s", '%abandon_time_of%'));
            $coupon_code_check = (get_option('rac_coupon_upgrade_success') != 'yes') && (!empty($coupon_code_array) || !empty($coupon_get_option));
            $tables_check = ((get_option('rac_upgrade_success') != 'yes') && $table_count > 0);
            if ($tables_check || $coupon_code_check) {
                if (self::fp_rac_upgrade_file_exists()) {
                    $action = ($tables_check) ? 'rac_database_upgrade_process' : 'rac_database_coupon_upgrade_process';
                    $redirect_url = esc_url_raw(add_query_arg(array('post_type' => 'raccartlist', 'page' => 'fprac_reports_slug', 'rac_updating_action' => 'rac_updating_process'), admin_url('edit.php')));
                    ?>
                    <div id="message" class="notice notice-warning"><p><strong> <?php _e("Recover Abandoned Cart requires Database Upgrade, $link to proceed with the Upgrade", 'recoverabandoncart'); ?></strong></p></div>
                    <div id="updating_message" class="updated notice-warning" style="display:none"><p><strong> <?php _e("Recover Abandoned Cart Data Update - Your database is being updated in the background.", 'recoverabandoncart'); ?></strong></p></div>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery(document).on('click', '#rac_display_notice', function () {
                                var data = {action: "<?php echo $action; ?>"};
                                var rsconfirm = confirm("It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?");
                                if (rsconfirm === true) {
                                    jQuery.ajax({
                                        type: "POST",
                                        url: ajaxurl,
                                        data: data,
                                    }).done(function (response) {
                                        window.location.href = '<?php echo $redirect_url; ?>';
                                    });
                                }
                                return false;
                            });
                        });
                    </script>
                    <?php
                } else {
                    $support_link = '<a href="http://fantasticplugins.com/support">' . __('Support', 'recoverabandoncart') . '</a>';
                    ?><div id="message" class="notice notice-warning"><p><strong> <?php _e("Upgrade to v" . RAC_VERSION . " has failed. Please contact our $support_link", 'recoverabandoncart'); ?></strong></p></div><?php
                }
            }
        }

        /**
         * Handle Cart List 
         */
        public static function handle_cartlist($offset = 0, $limit = 500) {
            global $wpdb;
            $ids = $wpdb->get_col("SELECT ID FROM " . $wpdb->prefix . "rac_abandoncart ORDER BY ID ASC LIMIT $offset,$limit");
            if (rac_check_is_array($ids)) {
                foreach ($ids as $id) {
                    self::$process_cartlist->push_to_queue($id);
                }
            } else {
                self::$process_cartlist->push_to_queue('rac_no_data');
            }
            //update offset 
            update_option('rac_cartlist_background_updater_offset', $limit + $offset);
            if ($offset == 0) {
                FP_WooCommerce_Log::log('Cart Lists Upgrade Started');
                self::$progress_bar->fp_increase_progress(15);
            }
            self::$process_cartlist->save()->dispatch();
        }

        /**
         * Handle Email Template 
         */
        public static function handle_emailtemplate($offset = 0, $limit = 1000) {
            global $wpdb;
            $ids = $wpdb->get_col("SELECT ID FROM " . $wpdb->prefix . "rac_templates_email ORDER BY ID ASC LIMIT $offset,$limit");
            if (rac_check_is_array($ids)) {
                foreach ($ids as $id) {
                    self::$process_emailtemplate->push_to_queue($id);
                }
            } else {
                self::$process_emailtemplate->push_to_queue('rac_no_data');
            }
            //update offset 
            update_option('rac_emailtemplate_background_updater_offset', $limit + $offset);
            if ($offset == 0) {
                FP_WooCommerce_Log::log('v' . RAC_VERSION . ' Upgrade Started');
                FP_WooCommerce_Log::log('Email Templates Upgrade Started');
                self::$progress_bar->fp_increase_progress(5);
            }
            self::$process_emailtemplate->save()->dispatch();
        }

        /**
         * Handle Mail Log 
         */
        public static function handle_maillog($offset = 0, $limit = 1000) {
            global $wpdb;
            $ids = $wpdb->get_col("SELECT ID FROM " . $wpdb->prefix . "rac_email_logs ORDER BY ID ASC LIMIT $offset,$limit");
            if (rac_check_is_array($ids)) {
                foreach ($ids as $id) {
                    self::$process_maillog->push_to_queue($id);
                }
            } else {
                self::$process_maillog->push_to_queue('rac_no_data');
            }
            //update offset 
            update_option('rac_maillog_background_updater_offset', $limit + $offset);
            if ($offset == 0) {
                FP_WooCommerce_Log::log('Email Log Upgrade Started');
                self::$progress_bar->fp_increase_progress(35);
            }
            self::$process_maillog->save()->dispatch();
        }

        /**
         * Handle Recovered Order 
         */
        public static function handle_recoveredorder() {
            $ids = array_filter(get_option('fp_rac_recovered_order_ids') ? get_option('fp_rac_recovered_order_ids') : array());
            if (rac_check_is_array($ids)) {
                foreach ($ids as $id => $value) {
                    self::$process_recoveredorder->push_to_queue($id);
                }
            } else {
                self::$process_recoveredorder->push_to_queue('rac_no_data');
            }

            FP_WooCommerce_Log::log('Recovered Orders Upgrade Started');
            self::$progress_bar->fp_increase_progress(55);
            self::$process_recoveredorder->save()->dispatch();
        }

        /**
         * Handle Coupon Code 
         */
        public static function handle_couponcode() {
            $get_datas = array_filter(get_option('rac_coupon_for_user') ? get_option('rac_coupon_for_user') : array());
            if (rac_check_is_array($get_datas)) {
                foreach ($get_datas as $email => $value) {
                    self::$process_couponcode->push_to_queue($email);
                }
            } else {
                self::$process_couponcode->push_to_queue('rac_no_data');
            }
            FP_WooCommerce_Log::log('Coupon Code Upgrade Started');
            self::$progress_bar->fp_increase_progress(75);
            self::$process_couponcode->save()->dispatch();
        }

        /**
         * Handle Coupon Code 
         */
        public static function handle_get_option($offset = 0, $limit = 1000) {
            global $wpdb;
            $get_datas = $wpdb->get_col("SELECT DISTINCT ID FROM {$wpdb->posts} as p INNER JOIN {$wpdb->postmeta} as p1 ON p.ID=p1.post_id WHERE p.post_type = 'raccartlist' AND p.post_status IN('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered', 'trash') LIMIT $offset,$limit");
            if (rac_check_is_array($get_datas)) {
                foreach ($get_datas as $cart_id) {
                    self::$process_getoption->push_to_queue($cart_id);
                }
            } else {
                self::$process_getoption->push_to_queue('rac_no_data');
            }
            //update offset 
            update_option('rac_get_option_background_updater_offset', $limit + $offset);
            if ($offset == 0) {
                FP_WooCommerce_Log::log('Get Option Upgrade Started');
                self::$progress_bar->fp_increase_progress(90);
            }
            self::$process_getoption->save()->dispatch();
        }

        /**
         * push to queue coupon details.
         */
        public static function initiate_to_coupon_background_process() {
            self::fp_rac_reset_option();
            self::$progress_bar->fp_increase_progress(30);
            set_transient('fp_rac_coupon_background_process_transient', true, 30);
        }

        /**
         * push to queue all ids
         */
        public static function initiate_to_background_process() {
            $total = self::fp_rac_overall_batch_count();
            if (!empty($total)) {
                self::fp_rac_reset_option();
                set_transient('fp_rac_background_process_transient', true, 30);
            }
        }

        public static function fp_rac_reset_option() {
            self::$progress_bar->fp_delete_option();
            delete_option('rac_emailtemplate_background_updater_offset');
            delete_option('rac_cartlist_background_updater_offset');
            delete_option('rac_maillog_background_updater_offset');
            delete_option('rac_get_option_background_updater_offset');
        }

        public static function fp_rac_overall_batch_count() {
            global $wpdb;
            $cartlist_ids = $wpdb->get_col('SELECT ID FROM ' . $wpdb->prefix . 'rac_abandoncart ORDER BY ID ASC');
            $template_ids = $wpdb->get_col('SELECT ID FROM ' . $wpdb->prefix . 'rac_templates_email ORDER BY ID ASC');
            $emaillog_ids = $wpdb->get_col('SELECT ID FROM ' . $wpdb->prefix . 'rac_email_logs ORDER BY ID ASC');
            $recover_ids = get_option('fp_rac_recovered_order_ids');
            $recovered_order = is_array($recover_ids) ? array_filter($recover_ids) : array();

            $total = count($cartlist_ids) + count($template_ids) + count($emaillog_ids) + count($recovered_order);
            return $total;
        }

    }

    FP_RAC_Main_Function_Importing_Part::init();
}