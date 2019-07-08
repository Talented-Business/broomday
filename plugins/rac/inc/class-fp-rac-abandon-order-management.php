<?php

/*
 * Recovered Related Functionality
 * 
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Abandon_Order_Management')) {

    /**
     * FP_RAC_Abandon_Order_Management Class.
     */
    class FP_RAC_Abandon_Order_Management {

        /**
         * FP_RAC_Abandon_Order_Management Class initialization.
         */
        public static function init() {
            add_action('woocommerce_thankyou', array(__CLASS__, 'clear_cookie'));
            add_action('woocommerce_order_status_completed', array(__CLASS__, 'fp_rac_check_order_status'));
            add_action('woocommerce_order_status_processing', array(__CLASS__, 'fp_rac_check_order_status'));
            add_action('woocommerce_checkout_order_processed', array(__CLASS__, 'fp_rac_insert_guest_entry'));
            add_action('woocommerce_order_status_changed', array(__CLASS__, 'fp_rac_order_status_guest'), 10, 3);
            add_action('woocommerce_checkout_order_processed', array(__CLASS__, 'fp_rac_cookies_for_cart_recover'));
            add_action('woocommerce_checkout_order_processed', array(__CLASS__, 'remove_member_acart_on_orderplaced'));

            $order_list = get_option('rac_mailcartlist_change');
            if (is_array($order_list) && (!empty($order_list))) {
                foreach ($order_list as $each_list) {
                    add_action('woocommerce_order_status_' . $each_list, array(__CLASS__, 'fp_rac_check_order_status'));
                }
            }
        }

        /*
         * Updating for recovered cart which placed order
         * 
         */

        public static function fp_rac_cookies_for_cart_recover($order_id) {
            if (isset($_COOKIE['rac_cart_id'])) {
                $cart_id = $_COOKIE['rac_cart_id'];
                update_post_meta($cart_id, 'rac_recovered_order_id', $order_id);
                update_post_meta($order_id, 'rac_order_placed', $cart_id);
                fp_rac_update_coupon_code($cart_id, $order_id);
            } else {
                $order_placed = '1';
                self::fp_rac_check_cart_list_manual_recovery($order_id, $order_placed);
            }
        }

        /*
         * Change Member cartlist status to recovered based on order status.
         * 
         */

        public static function fp_rac_check_order_status($order_id) {
            $cart_id = get_post_meta($order_id, 'rac_order_placed', true);
            if (!empty($cart_id)) {
                $recover_args = array(
                    'posts_per_page' => 1,
                    'post_type' => 'racrecoveredorder',
                    'post_status' => array('publish'),
                    'meta_query' => array(
                        array(
                            'key' => 'rac_order_id',
                            'value' => $order_id,
                        ),
                    ),
                    'fields' => 'ids'
                );
                $recover_ids = fp_rac_check_query_having_posts($recover_args);

                if (empty($recover_ids)) {
                    update_post_meta($cart_id, 'rac_cart_payment_details', 'completed');
                    $update_args = array('ID' => $cart_id, 'post_status' => 'rac-cart-recovered');
                    wp_update_post($update_args);
                    $mail_template_ids = get_post_meta($cart_id, 'rac_cart_link_status', true);
                    $mail_template_ids = maybe_unserialize($mail_template_ids);
                    $mail_template_id = $mail_template_ids[0];
                    $template_args = array(
                        'posts_per_page' => 1,
                        'post_type' => 'racemailtemplate',
                        'post_status' => array('racactive', 'racinactive', 'trash'),
                        'meta_query' => array(
                            array(
                                'key' => 'rac_old_template_id',
                                'value' => $mail_template_id,
                            ),
                        ),
                        'fields' => 'ids'
                    );
                    $email_template_id = fp_rac_check_query_having_posts($template_args);

                    FPRacCounter::rac_do_recovered_count();
                    if (rac_check_is_array($email_template_id)) {
                        FPRacCounter::rac_recovered_count_by_mail_template($email_template_id[0]);
                    }
                    FPRacCounter::record_order_id_and_cart_id($order_id, $cart_id, $email_template_id[0]);

                    FP_RAC_ADMIN_Notification_Email::fp_rac_mail_admin_cart_recovered($order_id); //mailing admin on order recover
                }
            }
            $order_placed = '2';
            self::fp_rac_check_cart_list_manual_recovery($order_id, $order_placed);
        }

        /*
         * Check Additional More Function to cross check whatever order contain cart products
         * 
         */

        public static function fp_rac_check_cart_list_manual_recovery($order_id, $orderplaced) {
            if (get_option('rac_cartlist_new_abandon_recover', true) == 'yes') {

                $allow_manual_order = get_option('rac_cartlist_new_abandon_recover_by_manual_order', true);
                if ($allow_manual_order == 'no') {
                    if (get_post_meta($order_id, '_created_via', true) == 'checkout') {
                        // Run
                    } else {
                        return false;
                    }
                }
                $order = fp_rac_get_order_obj($order_id);
                $billing_email = fp_rac_get_order_obj_data($order, 'billing_email');
                //Gather Results
                $user_id = fp_rac_get_order_obj_data($order, 'user_id'); // Previously it was get_current_user_id(); if admin manually recover the cart by making order completed then admin cart will recover (it is a bug) it should be the person cart.
                $user_details = get_userdata($user_id);
                $user_email = ($user_details) ? $user_details->user_email : $billing_email;
                $newstatus = get_option('rac_cartlist_change_from_new_to_recover') == 'yes' ? "1" : "0";
                $abandonstatus = get_option('rac_cartlist_change_from_abandon_to_recover') == 'yes' ? "1" : "0";

                if ($newstatus == '1' && $abandonstatus == '1') {
                    // If both are true
                    $status = array('rac-cart-new', 'rac-cart-abandon');
                } elseif ($newstatus == '1' && $abandonstatus == '0') {
                    $status = array('rac-cart-new');
                } elseif ($newstatus == '0' && $abandonstatus == '1') {
                    $status = array('rac-cart-abandon');
                } else {
                    $status = array();
                }
                $email = ($user_email != $billing_email) ? $user_email : $billing_email;
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'raccartlist',
                    'post_status' => $status,
                    'meta_query' => array(
                        array(
                            'key' => 'rac_cart_email_id',
                            'value' => $email
                        )
                    ),
                    'fields' => 'ids'
                );
                $post_cart_list_ids = fp_rac_check_query_having_posts($args);
                if (rac_check_is_array($post_cart_list_ids)) {
                    foreach ($post_cart_list_ids as $cart_id) {

                        if ($orderplaced == '1') {
                            update_post_meta($cart_id, 'rac_recovered_order_id', $order_id);
                            fp_rac_update_coupon_code($cart_id, $order_id);
                        }
                        if ($orderplaced == '2') {
                            $order_placed = get_post_meta($cart_id, 'rac_recovered_order_id', true);
                            $order_placed = $order_placed ? $order_placed : $order_id;
                            update_post_meta($cart_id, 'rac_recovered_order_id', $order_placed);
                            update_post_meta($cart_id, 'rac_cart_payment_details', 'completed');
                            $update_args = array('ID' => $cart_id, 'post_status' => 'rac-cart-recovered');
                            wp_update_post($update_args);
                            fp_rac_update_coupon_code($cart_id, $order_placed);
                        }
                    }
                }
            }
        }

        /*
         * clear cookie of cart id after order placed by clicked link on email.
         * 
         */

        public static function clear_cookie($orderid) {
            if (isset($_COOKIE['rac_cart_id'])) {
                unset($_COOKIE['rac_cart_id']);
                setcookie("rac_cart_id", null, -1, "/");
            }
        }

        /*
         * Add manual entry as Guest in cart list table.
         * 
         */

        public static function fp_rac_insert_guest_entry($order_id) {

            if (!is_user_logged_in()) {
                if (!isset($_COOKIE['rac_cart_id']) && !isset($_REQUEST['token']) && !isset($_COOKIE['rac_checkout_entry'])) { // We can remove cookie check if we want
                    $currentuser_lang = fp_rac_get_current_language();
                    $currency_code = fp_rac_get_current_currency_code();
                    $order = fp_rac_get_order_obj($order_id);
                    $user_email = fp_rac_get_order_obj_data($order, 'billing_email');
                    if (fp_rac_restirct_insert_cart_based_on($user_email)) {
                        $args = array(
                            'posts_per_page' => 1,
                            'post_type' => 'raccartlist',
                            'post_status' => array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered'),
                            'meta_query' => array(
                                array(
                                    'key' => 'rac_user_details',
                                    'value' => 0,
                                ),
                            ),
                            'orderby' => 'ID',
                            'order' => 'DESC',
                            'fields' => 'ids'
                        );
                        $last_cart = fp_rac_check_query_having_posts($args);

                        $db_cart_content = maybe_unserialize(get_post_meta($last_cart[0], 'rac_cart_details', true));
                        if (empty($db_cart_content)) {// IF no previous entry make a new
                            if (fp_rac_common_custom_restrict($user_email, 'entry') == 'proceed') {
                                $insert_entry = true;
                            }
                        } else {
                            if (is_object($db_cart_content)) {
                                if ($db_cart_content->id != $order_id) { // don't allow if they refresh again || if already exist
                                    $insert_entry = true;
                                }
                            } else {
                                //create after checkout cart
                                $insert_entry = true;
                            }
                        }
                        $user_id = '0';
                        $current_time = current_time('timestamp');
                        $cart_content = maybe_serialize($order);
                        $first_name = fp_rac_get_order_obj_data($order, 'billing_first_name');
                        $last_name = fp_rac_get_order_obj_data($order, 'billing_last_name');
                        $phone_number = fp_rac_get_order_obj_data($order, 'billing_phone');
                        $product_ids = fp_rac_extract_cartlist_content($order);
                        $product_ids = implode(',', $product_ids);
                        $order_items = $order->get_items();
                        $get_items = FP_RAC_Previous_Order_Data::rac_prepare_items($order_items);
                        $extra_cart_content = array(
                            'get_items' => $get_items,
                            'user_id' => $user_id,
                            'billing_first_name' => $first_name,
                            'billing_last_name' => $last_name,
                            'billing_email' => $user_email,
                            'billing_phone' => $phone_number,
                            'shipping_tax' => $order->get_shipping_tax(),
                            'total_shipping' => $order->get_total_shipping(),
                            'shipping_method' => $order->get_shipping_method()
                        );
                        if ($insert_entry) {
                            $args = array(
                                'rac_cart_details' => $cart_content,
                                'rac_user_details' => $user_id,
                                'rac_cart_email_id' => $user_email,
                                'rac_cart_abandoned_time' => $current_time,
                                'rac_cart_lang_code' => $currentuser_lang,
                                'rac_cart_currency_code' => $currency_code,
                                'rac_product_details' => $product_ids,
                                'rac_user_info' => $first_name . ',' . $last_name,
                                'rac_phone_number' => $phone_number,
                                'extra_cart_content' => (object) $extra_cart_content
                            );
                            //insert cartlist post
                            fp_rac_insert_cartlist_post($args, array('post_author' => 0));
                            update_post_meta($order_id, 'guest_cart', 'yes');
                        }
                    }
                } elseif (isset($_COOKIE['rac_checkout_entry']) && !isset($_COOKIE['rac_cart_id'])) {
                    //Check cookies for deleting cart captured from checkout
                    //Delete only if it is not recoverd from mail
                    $delete_id = $_COOKIE['rac_checkout_entry'];
                    wp_delete_post($delete_id, true);
                    //delete entry
                }
            }
        }

        /*
         * Remove Member cart list after order placed by normal.
         * 
         */

        public static function remove_member_acart_on_orderplaced($order_id) {
            if (is_user_logged_in()) {
                $order = fp_rac_get_order_obj($order_id);
                $user_id = fp_rac_get_order_obj_data($order, 'user_id');
                if (!empty($user_id)) { // order by members
                    $args = array(
                        'posts_per_page' => -1,
                        'post_type' => 'raccartlist',
                        'post_status' => array('rac-cart-new'),
                        'meta_query' => array(
                            array(
                                'key' => 'rac_user_details',
                                'value' => $user_id,
                            ),
                        ),
                        'fields' => 'ids',
                    );
                    $post_cart_list_ids = fp_rac_check_query_having_posts($args);
                    if (!empty($post_cart_list_ids)) {
                        foreach ($post_cart_list_ids as $post_cart_list_id) {
                            $each_entry = fp_rac_create_cart_list_obj($post_cart_list_id);
                            $stored_cart = maybe_unserialize($each_entry->cart_details);
                            if (is_array($stored_cart) && !empty($stored_cart)) {
                                if (isset($stored_cart['shipping_details'])) {
                                    unset($stored_cart['shipping_details']);
                                }
                                foreach ($stored_cart as $cart_details) {
                                    if (isset($cart_details['cart']) && !empty($cart_details['cart'])) {
                                        if (count($cart_details['cart']) <= count($order->get_items())) {
                                            $order_item_product_ids = array();
                                            $rac_cart_product_ids = array();
                                            foreach ($cart_details['cart'] as $product) {
                                                $rac_cart_product_ids[] = $product['product_id'];
                                            }
                                            foreach ($order->get_items() as $items) {
                                                $order_item_product_ids[] = $items['product_id'];
                                            }
                                            $check_array = array_diff($rac_cart_product_ids, $order_item_product_ids);
                                            if (empty($check_array)) {
                                                wp_delete_post($post_cart_list_id, true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        /*
         * Delete Guest cartlist based on order status.
         * 
         */

        public static function fp_rac_order_status_guest($order_id, $old, $new_status) {
            $check_guest_cart = get_post_meta($order_id, 'guest_cart', true);
            if ($check_guest_cart == 'yes') {
                if (get_option('rac_guest_abadon_type_' . $new_status) == 'yes') { //option selected by user
                    $args = array(
                        'posts_per_page' => -1,
                        'post_type' => 'raccartlist',
                        'post_status' => array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered'),
                        'meta_query' => array(
                            array(
                                'key' => 'rac_user_details',
                                'value' => 0,
                            ),
                        ),
                        'fields' => 'ids',
                    );
                    $post_cart_list_ids = fp_rac_check_query_having_posts($args);
                    if (rac_check_is_array($post_cart_list_ids)) {
                        foreach ($post_cart_list_ids as $post_cart_list_id) {
                            $each_entry = fp_rac_create_cart_list_obj($post_cart_list_id);
                            $expected_object = maybe_unserialize($each_entry->cart_details);
                            if (is_object($expected_object)) {
                                if ($expected_object->id == $order_id) {
                                    wp_delete_post($post_cart_list_id, true);
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    FP_RAC_Abandon_Order_Management::init();
}