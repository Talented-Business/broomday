<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('FP_RAC_Coupon_Code_Background_Process')) {

    /**
     * FP_RAC_Recovered_Order_Background_Process Class.
     */
    class FP_RAC_Coupon_Code_Background_Process extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rac_couponcode_background_updater';

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
            parent::complete();
            FP_RAC_Main_Function_Importing_Part::$progress_bar->fp_increase_progress(85);
            FP_WooCommerce_Log::log('Coupon Code Upgrade Completed');
            FP_RAC_Main_Function_Importing_Part::handle_get_option();
            // Show notice to user or perform some other arbitrary task...
        }

        public function import_couponcode_data($email) {
            if ($email != 'rac_no_data') {
                $data = array_filter(get_option('rac_coupon_for_user'));
                if (rac_check_is_array($data)) {
                    if (isset($data[$email])) {
                        $couponcode = $data[$email]['coupon_code'];
                        $coupon_object = new WC_Coupon($couponcode);
                        if (is_object($coupon_object)) {
                            $coupon_id = fp_rac_get_coupon_obj_data($coupon_object, 'id');
                            if ($coupon_id > 0 && get_post_meta($coupon_id, 'rac_cartlist_coupon_email', true) === '') {
                                update_post_meta($coupon_id, 'rac_cartlist_coupon_email', $email);
                            }
                        }
                    }
                }
            }

            return $email;
        }

    }

}