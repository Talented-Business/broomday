<?php

/**
 * Insert Cart based on Cancel Order 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FPRacCancelledOrder')) {

    /**
     * FPRacCancelledOrder Class.
     */
    class FPRacCancelledOrder {

        /**
         * FPRacCancelledOrder Class initialization.
         */
        public static function init() {
            if (get_option('rac_insert_abandon_cart_when_os_cancelled') == 'yes') {
                add_action('woocommerce_order_status_cancelled', array(__CLASS__, 'add_cancelled_order_immediately_to_cart_list_as_abandoned'));
                add_action('woocommerce_cancelled_order', array(__CLASS__, 'prevent_add_new_cart_while_order_cancelled_in_cart_page'), 10, 1);
            }

            if (get_option('rac_insert_abandon_cart_when_order_failed') == 'yes')
                add_action('woocommerce_order_status_failed', array(__CLASS__, 'add_failed_order_immediately_to_cart_list_as_abandoned'));
        }

        /**
         * Prevent Cancelled Order add as Cart List in Cart Page
         */
        public static function prevent_add_new_cart_while_order_cancelled_in_cart_page($order_id) {
            if (get_option('rac_prevent_entry_in_cartlist_while_order_cancelled_in_cart_page') != 'no') {
                remove_action('woocommerce_cart_updated', array('FP_RAC_Insert_CartList_Entry', 'fp_rac_insert_entry'));
            }
        }

        /**
         * Add Cancelled Order as Cart List
         */
        public static function add_cancelled_order_immediately_to_cart_list_as_abandoned($order_id) {
            self::add_order_immediately_to_cart_list($order_id);
        }

        /**
         * Add Failed Order as Cart List
         */
        public static function add_failed_order_immediately_to_cart_list_as_abandoned($order_id) {
            self::add_order_immediately_to_cart_list($order_id);
        }

        /**
         * Add Order as Cart List Table
         */
        public static function add_order_immediately_to_cart_list($order_id) {
            $guest_cart_order_id = get_post_meta($order_id, 'guest_cart', true);
            $email_clicked_order_id = get_post_meta($order_id, 'rac_order_placed', true);
            $old_order_id_updated = get_post_meta($order_id, 'old_order_updated', true);

            if (empty($guest_cart_order_id) && empty($email_clicked_order_id) && empty($old_order_id_updated))
                FP_RAC_Insert_CartList_Entry::fp_rac_insert_old_order_entry($order_id);
        }

    }

    FPRacCancelledOrder::init();
}