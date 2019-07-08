<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('FP_RAC_Email_Template_Background_Process')) {

    /**
     * FP_RAC_Email_Template_Background_Process Class.
     */
    class FP_RAC_Email_Template_Background_Process extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rac_emailtemplate_background_updater';

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
            $this->import_emailtemplate_data($item);
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
            $offset = get_option('rac_emailtemplate_background_updater_offset');
            $ids = $wpdb->get_col("SELECT ID FROM " . $wpdb->prefix . "rac_templates_email ORDER BY ID ASC LIMIT $offset,1000");
            if (rac_check_is_array($ids)) {
                FP_RAC_Main_Function_Importing_Part::handle_emailtemplate($offset);
            } else {
                FP_RAC_Main_Function_Importing_Part::$progress_bar->fp_increase_progress(10);
                FP_WooCommerce_Log::log('Email Templates Upgrade Completed');
                delete_option('rac_emailtemplate_background_updater_offset');
                FP_RAC_Main_Function_Importing_Part::handle_cartlist();
            }
            // Show notice to user or perform some other arbitrary task...
        }

        public function import_emailtemplate_data($template_id) {
            $id = '';
            if ($template_id != 'rac_no_data') {
                global $wpdb;
                $data = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'rac_templates_email WHERE ID=%d', $template_id));
                if (rac_check_is_array($data)) {
                    foreach ($data as $key => $value) {
                        $old_email_template = fp_rac_get_post_id_from_old_id('rac_old_template_id', $value->id, array('racactive', 'racinactive', 'trash'), 'racemailtemplate');
                        if (!$old_email_template) {
                            $status = ($value->status == 'ACTIVE') ? 'racactive' : 'racinactive';
                            $arg = array(
                                'post_status' => $status,
                                'post_type' => 'racemailtemplate',
                                'post_title' => $value->template_name,
                                'post_content' => $value->message,
                            );
                            $recovered_count = get_option('rac_recovered_count_of_' . $value->id);
                            $emailcount = get_option('email_count_of_' . $value->id);
                            $segmentation = is_array($value->segmentation) ? $value->segmentation : maybe_unserialize($value->segmentation);
                            $id = wp_insert_post($arg);

                            $update_args = array(
                                'rac_old_template_id' => $value->id,
                                'rac_template_mail' => $value->mail,
                                'rac_template_link' => $value->link,
                                'rac_template_status' => $value->status,
                                'rac_template_email_sent' => $emailcount,
                                'rac_template_subject' => $value->subject,
                                'rac_template_from_name' => $value->from_name,
                                'rac_template_sender_opt' => $value->sender_opt,
                                'rac_template_from_email' => $value->from_email,
                                'rac_template_anchor_text' => $value->anchor_text,
                                'rac_template_cart_recovered' => $recovered_count,
                                'rac_template_sending_type' => $value->sending_type,
                                'rac_template_segmentation' => $segmentation,
                                'rac_template_sending_duration' => $value->sending_duration,
                                'rac_template_blind_carbon_copy' => $value->rac_blind_carbon_copy,
                            );
                            if (rac_check_is_array($update_args)) {
                                foreach ($update_args as $meta_name => $value) {
                                    update_post_meta($id, $meta_name, $value);
                                }
                            }
                        }
                    }
                }
            }

            return $id;
        }

    }

}