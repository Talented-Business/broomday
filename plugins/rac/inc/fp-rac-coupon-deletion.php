<?php

/**
 * Delete RAC coupons after Expired
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FPRacCouponDelete')) {

    /**
     * FPRacCouponDelete Class.
     */
    class FPRacCouponDelete {

        /**
         * Initialize the FPRacCouponDelete class.
         */
        public static function init() {
            add_action('rac_cron_job', array(__CLASS__, 'delete_expired_rac_coupon'), 999);

            if ('yes' === get_option('rac_delete_coupon_after_use')) {
                if ('2' === get_option('rac_delete_coupon_after_use_based_on')) {
                    $order_list = get_option('rac_delete_coupon_after_use_based_on_status');
                    if (is_array($order_list) && (!empty($order_list))) {
                        foreach ($order_list as $each_list) {
                            add_action('woocommerce_order_status_' . $each_list, array(__CLASS__, 'delete_rac_coupon'), 999, 1);
                        }
                    }
                } else {
                    add_action('woocommerce_checkout_order_processed', array(__CLASS__, 'delete_rac_coupon'), 999, 1);
                }
            }
        }

        /**
         * Delete RAC Coupons Based Cron Job.
         */
        public static function delete_expired_rac_coupon() {

            if ('yes' === get_option('rac_delete_coupon_expired')) {
                $args = array('post_type' => 'shop_coupon', 'posts_per_page' => -1);
                $cus_query = fp_rac_check_query_having_posts($args);

                foreach ($cus_query as $value) {
                    $coupon = new WC_Coupon($value->post_name);

                    if (get_post_meta($value->ID, 'coupon_by_rac', true) == 'yes') {

                        $expiry_date = strtotime(get_post_meta($value->ID, 'expiry_date', true));
                        if (current_time('timestamp') > $expiry_date) {
                            wp_delete_post($value->ID, true);
                        }
                    }
                }
            }
        }

        /**
         * Delete RAC Coupons Based Checkout Order Changed.
         */
        public static function delete_rac_coupon($order_id) {
            if ('yes' === get_option('rac_delete_coupon_after_use')) {
                $order = fp_rac_get_order_obj($order_id);
                $used_coupons = $order->get_used_coupons();
                foreach ($used_coupons as $coupon) {
                    $coupon_ob = new WC_Coupon($coupon);
                    $coupon_id = fp_rac_get_coupon_obj_data($coupon_ob, 'id');
                    if (get_post_meta($coupon_id, 'coupon_by_rac', true) == 'yes') {
                        wp_delete_post($coupon_id, true);
                    }
                }
            }
        }

    }

    FPRacCouponDelete::init();
}

