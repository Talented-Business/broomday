<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('FP_RAC_Emaillog_Background_Process')) {

    /**
     * FP_RAC_Emaillog_Background_Process Class.
     */
    class FP_RAC_Emaillog_Background_Process extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rac_maillog_background_updater';

        /**
         * Task
         *
         * Override this method to perform any actions required on each
         * queue item. Return the modified item for further processing
         * in the next pass through. Or, return false to remove the
         * item from the queue.
         *
         * @param mixed $item Queue item to iterate over
         *
         * @return mixed
         */
        protected function task($item) {
            $this->import_emaillog_data($item);
            return false;
        }

        /**
         * Complete
         *
         * Override if applicable, but ensure that the below actions are
         * performed, or, call parent::complete().
         */
        protected function complete() {
            global $wpdb;
            parent::complete();
            $offset = get_option('rac_maillog_background_updater_offset');
            $ids = $wpdb->get_col("SELECT ID FROM " . $wpdb->prefix . "rac_email_logs ORDER BY ID ASC LIMIT $offset,1000");
            if (rac_check_is_array($ids)) {
                FP_RAC_Main_Function_Importing_Part::handle_maillog($offset);
            } else {
                FP_RAC_Main_Function_Importing_Part::$progress_bar->fp_increase_progress(50);
                FP_WooCommerce_Log::log('Email Log Upgrade Completed');
                delete_option('rac_maillog_background_updater_offset');
                FP_RAC_Main_Function_Importing_Part::handle_recoveredorder();
            }

            // Show notice to user or perform some other arbitrary task...
        }

        public function import_emaillog_data($log_id) {
            $id = '';
            if ($log_id != 'rac_no_data') {
                global $wpdb;
                $data = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'rac_email_logs WHERE ID=%d', $log_id));
                if (rac_check_is_array($data)) {
                    foreach ($data as $key => $value) {
                        $old_maillog = fp_rac_get_post_id_from_old_id('rac_emaillog_old_id', $value->id, array('publish'), 'racmaillog');
                        if (!$old_maillog) {
                            $date = date('Y-m-d H:i:s', $value->date_time);
                            $arg = array(
                                'post_status' => 'publish',
                                'post_type' => 'racmaillog',
                                'post_date' => $date
                            );

                            $id = wp_insert_post($arg);
                            $template_used = $value->template_used;
                            $template_check = strpos($value->template_used, 'Manual');
                            $manual = false;
                            if ($template_check !== false) {
                                $template_id = explode("-", $template_used);
                                $template_id = $template_id[0];
                                $manual = true;
                            } else {
                                $template_id = $template_used;
                            }
                            $post_status = array('racactive', 'racinactive', 'trash');
                            $template_id = fp_rac_get_post_id_from_old_id('rac_old_template_id', $template_id, $post_status, 'racemailtemplate');
                            $template_id = !empty($template_id) ? $template_id : '';
                            if ($manual && !empty($template_id)) {
                                $template_id = $template_id . '- Manual';
                            }

                            $status = array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered', 'trash');
                            $cart_id = fp_rac_get_post_id_from_old_id('rac_cart_old_id', $value->rac_cart_id, $status, 'raccartlist');

                            update_post_meta($id, 'rac_email_id', $value->email_id);
                            update_post_meta($id, 'rac_date_time', $value->date_time);
                            update_post_meta($id, 'rac_cart_id', $cart_id);
                            update_post_meta($id, 'rac_template_used', $template_id);
                            update_post_meta($id, 'rac_emaillog_old_id', $value->id);
                        }
                    }
                }
            }

            return $id;
        }

    }

}