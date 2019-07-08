<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Admin_Ajax')) {

    /**
     * FP_RAC_Admin_Ajax Class.
     */
    class FP_RAC_Admin_Ajax {

        /**
         * FP_RAC_Admin_Ajax Class Initialization.
         */
        public static function init() {
            add_action('wp_ajax_rac_manual_recovered', array(__CLASS__, 'rac_manual_recovered'));
            add_action('wp_ajax_edit_value_update_now', array(__CLASS__, 'fp_rac_edit_mail_update_data'));
            add_action('wp_ajax_rac_email_template_status', array(__CLASS__, 'set_email_template_status'));
            add_action('wp_ajax_mailstatus_cartlist', array(__CLASS__, 'rac_change_cart_list_mailstatus'));
            add_action('wp_ajax_rac_drag_n_drop_product_info_column', array(__CLASS__, 'fp_rac_admin_request_from_ajax_sortable'));
        }

        /**
         * Changing Cart list Mail Status Table.
         */
        public static function rac_change_cart_list_mailstatus() {
            check_ajax_referer('mailstatus-cartlist', 'rac_security');

            if (isset($_POST['row_id']) && isset($_POST['status'])) {
                $status = $_POST['status'];
                update_post_meta($_POST['row_id'], 'rac_cart_sending_status', $status);
                echo '1';
            }
            exit();
        }

        /**
         * Recovered Cart List by Manually in Cart List Table.
         */
        public static function rac_manual_recovered() {

            check_ajax_referer('recover-status', 'rac_security');

            if (isset($_POST['row_id'])) {
                $args = array('ID' => $_POST['row_id'],
                    'post_status' => 'rac-cart-recovered',
                    'post_type' => 'raccartlist'
                );
                wp_update_post($args);
                echo 1;
            }
            exit();
        }

        /**
         * Update Guest Email manually in Cart List Table
         */
        public static function fp_rac_edit_mail_update_data() {

            check_ajax_referer('update-guest-email', 'rac_security');
            if (isset($_POST['id']) && $_POST['email']) {
                $row_id = $_POST['id'];
                $email_value = $_POST['email'];
                $cart_details = maybe_unserialize(get_post_meta($row_id, 'rac_cart_details', true));
                $cart_details["visitor_mail"] = $email_value;
                $details = maybe_serialize($cart_details);
                update_post_meta($row_id, 'rac_cart_details', $details);
            }
            exit();
        }

        /**
         * Changing Email Template Sending Status in Email Template Table.
         */
        public static function set_email_template_status() {

            check_ajax_referer('email-template-status', 'rac_security');

            if (isset($_POST['row_id']) && isset($_POST['status'])) {
                $requesting_state = $_POST['status'];
                $post_id = $_POST['row_id'];
                $status = $requesting_state != 'ACTIVE' ? 'racactive' : 'racinactive';
                $new_status = $requesting_state != 'ACTIVE' ? 'ACTIVE' : 'NOTACTIVE';
                $args = array(
                    'ID' => $post_id,
                    'post_status' => $status
                );

                wp_update_post($args);
                echo $new_status;
            }
            exit();
        }

        /**
         * Update Sortable column of email settings.
         */
        public static function fp_rac_admin_request_from_ajax_sortable() {
            if (isset($_POST['data'])) {
                update_option('drag_and_drop_product_info_sortable_column', $_POST['data']);
            }
            exit();
        }

    }

    FP_RAC_Admin_Ajax::init();
}