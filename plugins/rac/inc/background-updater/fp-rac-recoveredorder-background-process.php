<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('FP_RAC_Recovered_Order_Background_Process')) {

    /**
     * FP_RAC_Recovered_Order_Background_Process Class.
     */
    class FP_RAC_Recovered_Order_Background_Process extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rac_recoveredorder_background_updater';

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
            $this->import_recoveredorder_data($item);
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
            FP_RAC_Main_Function_Importing_Part::$progress_bar->fp_increase_progress(70);
            FP_WooCommerce_Log::log('Recovered Orders Upgrade Completed');
            FP_RAC_Main_Function_Importing_Part::handle_couponcode();
            // Show notice to user or perform some other arbitrary task...
        }

        public function import_recoveredorder_data($recovered_id) {
            $id = '';
            if ($recovered_id != 'rac_no_data') {
                global $wpdb;
                $old_recoveredorder = fp_rac_get_post_id_from_old_id('rac_recoveredorder_old_id', $recovered_id, array('publish'), 'racrecoveredorder');
                if (!$old_recoveredorder) {
                    $data = array_filter(get_option('fp_rac_recovered_order_ids'));
                    $value = $data[$recovered_id];

                    if (rac_check_is_array($value)) {
                        $date = date('Y-m-d H:i:s', strtotime($value['date']));
                        $arg = array(
                            'post_status' => 'publish',
                            'post_type' => 'racrecoveredorder',
                            'post_date' => $date
                        );

                        $id = wp_insert_post($arg);
                        update_post_meta($id, 'rac_order_id', $value['order_id']);
                        update_post_meta($id, 'rac_recovered_date', strtotime($value['date']));
                        update_post_meta($id, 'rac_recovered_sales_total', $value['order_total']);
                        update_post_meta($id, 'rac_recoveredorder_old_id', $recovered_id);
                    }
                }
            }

            return $id;
        }

    }

}