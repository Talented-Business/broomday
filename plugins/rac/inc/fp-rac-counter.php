<?php

/**
 *  RAC Table row can be deleted,
 * to provide exact report we count it and have it options table
 * 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FPRacCounter')) {

    /**
     * FPRacCounter Class.
     */
    class FPRacCounter {

        /**
         * update the recovered counts.
         */
        public static function rac_do_recovered_count() {
            $recovered_count = (int) get_option('rac_recovered_count');
            $recovered_count++;
            update_option('rac_recovered_count', $recovered_count);
        }

        /**
         * update the recovered order details.
         */
        public static function record_order_id_and_cart_id($order_id, $cart_id, $templateid) {
            $product_ids = array();
            $order_object = fp_rac_get_order_obj($order_id);
            $total = fp_rac_get_order_obj_data($order_object, 'order_total');
            $order_date = fp_rac_get_order_obj_data($order_object, 'order_date');
            $get_items = $order_object->get_items();
            if (rac_check_is_array($get_items)) {
                foreach ($get_items as $product) {
                    $product_ids[] = $product['product_id'];
                }
            }

            $args = array(
                'rac_order_id' => $order_id,
                'rac_cart_id' => $cart_id,
                'rac_template_id' => $templateid,
                'rac_product_details' => implode(',', $product_ids),
                'rac_recovered_sales_total' => $total,
                'rac_recovered_date' => strtotime($order_date),
            );
            fp_rac_insert_recovered_order_post($args);
        }

        /**
         * update the abandon counts.
         */
        public static function rac_do_abandoned_count() {
            $abandoned_count = (int) get_option('rac_abandoned_count');
            $abandoned_count++;
            update_option('rac_abandoned_count', $abandoned_count);
        }

        /**
         * update the mail counts.
         */
        public static function rac_do_mail_count() {
            $mail_count = (int) get_option('rac_mail_count');
            $mail_count++;
            update_option('rac_mail_count', $mail_count);
        }

        /**
         * update the email count for each template.
         */
        public static function email_count_by_template($templateid) {
            $mail_count = (int) get_post_meta($templateid, 'rac_template_email_sent', true);
            $mail_count++;
            update_post_meta($templateid, 'rac_template_email_sent', $mail_count);
        }

        /**
         * update the recovered count for each template.
         */
        public static function rac_recovered_count_by_mail_template($templateid) {
            $recovered_count = (int) get_post_meta($templateid, 'rac_template_cart_recovered', true);
            $recovered_count++;
            update_post_meta($templateid, 'rac_template_cart_recovered', $recovered_count);
        }

        /**
         * update the link clicked count for each template.
         */
        public static function rac_do_linkc_count($abandon_cart_id, $email_template_id) {
            $link_count = (int) get_option('rac_link_count');
            $already_clicked_ids = maybe_unserialize(get_post_meta($abandon_cart_id, 'rac_cart_link_status', true));
            if (!in_array($email_template_id, (array) $already_clicked_ids)) {
                $link_count++;
                update_option('rac_link_count', $link_count);
            }
        }

    }

}