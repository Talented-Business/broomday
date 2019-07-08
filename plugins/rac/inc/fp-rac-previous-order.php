<?php

/**
 * Previous Orders
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Previous_Order')) {

    /**
     * FP_RAC_Previous_Order Class.
     */
    class FP_RAC_Previous_Order {

        /**
         * FP_RAC_Previous_Order Class initialization.
         */
        public static function init() {
            add_action('wp_ajax_rac_add_old_order', array(__CLASS__, 'fp_rac_get_list_of_ids_by_query'));
            add_action('wp_ajax_rac_chunk_previous_order_list', array(__CLASS__, 'fp_rac_add_old_order_byupdate'));
            add_action('wp_ajax_rac_get_old_order_data', array(__CLASS__, 'fp_rac_get_old_older_ids_by_query'));
            add_action('wp_ajax_rac_chunk_old_order_list', array(__CLASS__, 'fp_rac_update_old_order_byupdate'));
        }

        public static function fp_rac_get_old_older_ids_by_query() {
            check_ajax_referer('oldorder-update', 'rac_security');

            if (isset($_POST['rac_order_value'])) {
                $args = array(
                    'post_type' => 'raccartlist',
                    'posts_per_page' => '-1',
                    'post_status' => array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered', 'trash'),
                    'meta_query' => array(
                        array(
                            'key' => 'rac_user_details',
                            'value' => 'old_order',
                        )
                    ),
                    'fields' => 'ids',
                    'cache_results' => false
                );
                $cartlists = fp_rac_check_query_having_posts($args);
                echo json_encode($cartlists);
            }
            exit();
        }

        public static function fp_rac_update_old_order_byupdate() {
            check_ajax_referer('oldorder-update', 'rac_security');

            if (isset($_POST['ids']) && !empty($_POST['ids'])) {
                $cart_ids = $_POST['ids'];
                if (rac_check_is_array($cart_ids)) {
                    foreach ($cart_ids as $cart_id) {
                        $cartlist = fp_rac_create_cart_list_obj($cart_id);
                        $order = maybe_unserialize($cartlist->cart_details);
                        if (is_object($order) && ($cartlist->user_id == 'old_order')) {
                            $order_id = fp_rac_get_order_obj_data($order, 'id');
                            if ($order_id) {
                                $user_email = fp_rac_get_order_obj_data($order, 'billing_email');
                                $first_name = fp_rac_get_order_obj_data($order, 'billing_first_name');
                                $last_name = fp_rac_get_order_obj_data($order, 'billing_last_name');
                                $phone_number = fp_rac_get_order_obj_data($order, 'billing_phone');
                                $product_ids = fp_rac_extract_cartlist_content($order, $cartlist, true);
                                $product_ids = implode(',', $product_ids);
                                $update_meta = array(
                                    'rac_product_details' => $product_ids,
                                    'rac_user_info' => $first_name . ',' . $last_name,
                                    'rac_phone_number' => $phone_number,
                                    'rac_cart_email_id' => $user_email,
                                );
                                fp_rac_update_cartlist_post_meta($update_meta, $cart_id);
                            }
                        }
                    }
                }
            } else {
                echo json_encode('completed');
            }
            exit();
        }

        public static function fp_rac_get_list_of_ids_by_query() {

            check_ajax_referer('oldorder-cartlist', 'rac_security');

            if (isset($_POST['rac_order_time'])) {
                $order_statuses = $_POST['rac_order_status'];
                $args = array(
                    'post_type' => 'shop_order',
                    'posts_per_page' => '-1',
                    "post_status" => $order_statuses,
                    'fields' => 'ids',
                    'cache_results' => false,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'rac_order_placed',
                            'compare' => 'NOT EXISTS'
                        ),
                        array(
                            'key' => 'guest_cart',
                            'compare' => 'NOT EXISTS'
                        ),
                        array(
                            'key' => 'old_order_updated',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                );
                if ("all" != $_POST['rac_order_time']) {
                    if (!empty($_POST['rac_from_time']) || !empty($_POST['rac_to_time'])) {
                        $from_strtotime = strtotime($_POST['rac_from_time']);
                        $to_strtotime = strtotime($_POST['rac_to_time']);
                        //convert std format
                        $convert_from_date = date('Y-m-d', $from_strtotime);
                        $convert_to_date = date('Y-m-d', $to_strtotime);
                        $from_time_array = explode("-", $convert_from_date);
                        $to_time_array = explode("-", $convert_to_date);
                        if (!empty($_POST['rac_to_time'])) {
                            $date_query = array(
                                'before' => array(
                                    'year' => $to_time_array[0],
                                    'month' => $to_time_array[1],
                                    'day' => $to_time_array[2],
                                ),
                                'inclusive' => true,
                            );
                        } elseif (!empty($_POST['rac_from_time'])) {
                            $date_query = array(
                                'after' => array(
                                    'year' => $from_time_array[0],
                                    'month' => $from_time_array[1],
                                    'day' => $from_time_array[2],
                                ),
                                'inclusive' => true,
                            );
                        } else {
                            $date_query = array(
                                'after' => array(
                                    'year' => $from_time_array[0],
                                    'month' => $from_time_array[1],
                                    'day' => $from_time_array[2],
                                ),
                                'before' => array(
                                    'year' => $to_time_array[0],
                                    'month' => $to_time_array[1],
                                    'day' => $to_time_array[2],
                                ),
                                'inclusive' => true,
                            );
                        }
                        $args = array_merge($args, array('date_query' => $date_query));
                    }
                }
                $products = fp_rac_check_query_having_posts($args);
                delete_option('rac_abandon_previous_count');
                echo json_encode($products);
            }
            exit();
        }

        public static function fp_rac_add_old_order_byupdate() {

            check_ajax_referer('oldorder-cartlist', 'rac_security');

            if (isset($_POST['rac_order_status']) && !empty($_POST['ids'])) {
                $updated_count = 0;
                $the_query = $_POST['ids'];
                $looking_order_status = $_POST['rac_order_status'];
                $check_previous_data = get_option('rac_abandon_previous_count');
                if (rac_check_is_array($the_query)) {
                    foreach ($the_query as $each_query) {
                        $order = fp_rac_get_order_obj($each_query);
                        $order_id = fp_rac_get_order_obj_data($order, 'id');
                        $subscription_check = fp_rac_check_is_subscription($order_id);
                        $paymentplan_check = fp_rac_check_is_payment_plan($order_id);
                        if (!$subscription_check && !$paymentplan_check) {
                            //check to, not importing order which are recovered and captured on place order
                            if (in_array(get_post_status($order_id), (array) $looking_order_status)) {
                                $insert_id = FP_RAC_Insert_CartList_Entry::fp_rac_insert_old_order_entry($order_id);
                                if ($insert_id) {
                                    update_post_meta($order_id, "old_order_updated", "yes"); // this makes sure for no duplication
                                    $updated_count++;
                                }
                            }
                        }
                    }
                }
                update_option('rac_abandon_previous_count', $check_previous_data + $updated_count);
            } else {
                echo json_encode(array('count' => get_option('rac_abandon_previous_count')));
            }
            exit();
        }

    }

    FP_RAC_Previous_Order::init();
}