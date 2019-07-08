<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('FP_RAC_Get_Option_Background_Process')) {

    /**
     * FP_RAC_Recovered_Order_Background_Process Class.
     */
    class FP_RAC_Get_Option_Background_Process extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rac_get_option_background_updater';

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
            $this->import_couponcode_data($item);
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
            $offset = get_option('rac_get_option_background_updater_offset');
            $ids = $wpdb->get_results("SELECT DISTINCT ID FROM {$wpdb->posts} as p INNER JOIN {$wpdb->postmeta} as p1 ON p.ID=p1.post_id WHERE p.post_type = 'raccartlist' AND p.post_status IN('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered', 'trash') LIMIT $offset,1000");
            if (rac_check_is_array($ids)) {
                FP_RAC_Main_Function_Importing_Part::handle_get_option($offset);
            } else {
                FP_RAC_Main_Function_Importing_Part::$progress_bar->fp_increase_progress(100);
                FP_WooCommerce_Log::log('Get Option Upgrade Completed');
                FP_WooCommerce_Log::log('v' . RAC_VERSION . ' Upgrade Completed Successfully');
                delete_option('rac_get_option_background_updater_offset');
                update_option('rac_upgrade_success', 'yes');
                update_option('rac_coupon_upgrade_success', 'yes');
            }
            // Show notice to user or perform some other arbitrary task...
        }

        public function import_couponcode_data($cart_id) {
            if ($cart_id != 'rac_no_data') {
                $old_id = get_post_meta($cart_id, 'rac_cart_old_id', true);
                if (($coupon_code = get_option('abandon_time_of' . $old_id)) && get_post_meta($cart_id, 'rac_cart_coupon_code', true) === '') {
                    update_post_meta($cart_id, 'rac_cart_coupon_code', $coupon_code);
                }
            }

            return $cart_id;
        }

    }

}