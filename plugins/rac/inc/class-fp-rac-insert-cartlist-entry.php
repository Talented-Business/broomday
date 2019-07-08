<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Insert_CartList_Entry')) {

    /**
     * FP_RAC_Insert_CartList_Entry Class.
     */
    class FP_RAC_Insert_CartList_Entry {

        public static function init() {
            add_action('wp_login', array(__CLASS__, 'remove_action_hook'), 1);
            add_action('woocommerce_cart_updated', array(__CLASS__, 'fp_rac_insert_entry'), 99);
            add_action('wp_ajax_nopriv_rac_preadd_guest', array(__CLASS__, 'fp_rac_guest_entry_checkout_ajax'));
            add_action('wp_ajax_nopriv_fp_rac_set_guest_email_in_cookie', array(__CLASS__, 'fp_rac_set_guest_email_in_cookie_action'));
            add_action('wp_ajax_nopriv_fp_rac_already_popup_displayed', array(__CLASS__, 'fp_rac_already_popup_displayed_action'));
        }

        public static function fp_rac_insert_entry() {
            if (isset($_GET['email_template']) && (isset($_GET['abandon_cart']) || isset($_GET['guest']) || !isset($_GET['old_order'])))
                return '';

            $insert_entry = isset($_GET['cancel_order']) && $_GET['cancel_order'] && get_option('rac_prevent_entry_in_cartlist_while_order_cancelled_in_cart_page') != 'no' ? false : true;
            $entry_when_unsub = ((isset($_COOKIE['dont_insert_when_un_sub_email']) && $_COOKIE['dont_insert_when_un_sub_email'] == 'yes') || (isset($_GET['action']) && $_GET['action'] == 'unsubscribe'));
            $currentuser_lang = fp_rac_get_current_language();
            if ($insert_entry && !$entry_when_unsub) {
                $current_time = current_time('timestamp');
                $currency_code = fp_rac_get_current_currency_code();
                if (is_user_logged_in() && !is_admin()) {
                    $user_id = get_current_user_id();
                    $user_details = get_userdata($user_id);
                    $user_email = $user_details->user_email;
                    $args = array(
                        'posts_per_page' => 1,
                        'post_type' => 'raccartlist',
                        'post_status' => array('rac-cart-new', 'rac-cart-abandon'),
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'rac_user_details',
                                'value' => $user_id,
                            ),
                            array(
                                'key' => 'rac_recovered_order_id',
                                'compare' => 'NOT EXISTS'
                            )
                        ),
                        'orderby' => 'ID',
                        'order' => 'DESC',
                        'fields' => 'ids'
                    );
                    $last_cart = fp_rac_check_query_having_posts($args);
                    if (!empty($last_cart)) {
                        $last_cart = $last_cart[0];
                    }
                    $shipping_details = self::fp_rac_get_shipping_details();
                    $cart_persistent = fp_rac_get_user_persistent_cart($user_id);
                    if (!empty($cart_persistent[0]['cart'])) {
                        $cart_content = fp_rac_compatible_for_sumo_plugins($cart_persistent);
                        $cart_content['shipping_details'] = $shipping_details;
                        $cart_content = maybe_serialize($cart_content);
                        $cut_off_time = fp_rac_get_interval('rac_abandon_cart_time', 'rac_abandon_cart_time_type');
                        if (!empty($last_cart)) {
                            $cut_off_time = get_post_meta($last_cart, 'rac_cart_abandoned_time', true) + $cut_off_time;
                        }
                        $product_ids = fp_rac_extract_cartlist_content($cart_content);
                        $product_ids = implode(',', $product_ids);
                        if ($current_time > $cut_off_time) {
                            if ((isset($_COOKIE['rac_cart_id'])) || (isset($_GET['abandon_cart']))) {
                                //do nothing. Since this cart is from mail
                            } else {
                                if (!empty($last_cart)) {
                                    $update_arg = array(
                                        'ID' => $last_cart,
                                        'post_type' => 'raccartlist',
                                        'post_status' => 'rac-cart-abandon'
                                    );
                                    wp_update_post($update_arg);
                                    if (get_post_meta($last_cart, 'fp_rac_admin_notification_sent_for_cart_abandoned', true) != 'yes') {
                                        FP_RAC_ADMIN_Notification_Email::fp_rac_mail_admin_cart_abandoned($last_cart);
                                        update_post_meta($last_cart, 'fp_rac_admin_notification_sent_for_cart_abandoned', 'yes');
                                    }
                                    FPRacCounter::rac_do_abandoned_count();
                                }
                                if (fp_rac_restirct_insert_cart_based_on($user_email)) {
                                    if (fp_rac_common_custom_restrict($user_email, 'entry') == 'proceed') {
                                        $args = array(
                                            'rac_cart_details' => $cart_content,
                                            'rac_user_details' => $user_id,
                                            'rac_cart_email_id' => $user_email,
                                            'rac_cart_abandoned_time' => $current_time,
                                            'rac_cart_lang_code' => $currentuser_lang,
                                            'rac_cart_currency_code' => $currency_code,
                                            'rac_product_details' => $product_ids,
                                            'rac_user_info' => $user_id,
                                            'rac_phone_number' => $user_details->billing_phone
                                        );
                                        //insert cartlist post
                                        fp_rac_insert_cartlist_post($args);
                                    }
                                }
                            }
                        } else { //Update the cart details if less than or equal to cut off time
                            if (!empty($last_cart)) {
                                $args = array(
                                    'rac_cart_details' => $cart_content,
                                    'rac_cart_abandoned_time' => $current_time,
                                    'rac_cart_currency_code' => $currency_code,
                                    'rac_product_details' => $product_ids,
                                    'rac_user_info' => $user_id,
                                    'rac_phone_number' => $user_details->billing_phone
                                );
                                fp_rac_update_cartlist_post_meta($args, $last_cart);
                            }
                        }
                    } else {
                        if (!empty($last_cart) && get_post_status($last_cart) == 'rac-cart-new') {
                            wp_delete_post($last_cart, true);
                        }
                    }
                    // FOR ALL USER STATUS - - UPDATE ONLY
                    //Members
                    fp_rac_update_cartlist_status('member');
                    //guest
                    fp_rac_update_cartlist_status('guest');
                    // FOR ALL USER STATUS - UPDATE ONLY END
                } else {

                    // FOR ALL USER STATUS - UPDATE ONLY
                    //Members
                    fp_rac_update_cartlist_status('member');
                    //guest
                    fp_rac_update_cartlist_status('guest');
                    // FOR ALL USER STATUS - UPDATE ONLY END

                    self::fp_rac_update_guest_cart_in_checkout($currentuser_lang);
                    if (!is_user_logged_in()) {
                        // new guest entry
                        $guest_cookie = isset($_COOKIE['raccookie_guest_email']) ? $_COOKIE['raccookie_guest_email'] : '';
                        if ($guest_cookie) {
                            $details_array = maybe_unserialize(stripslashes($guest_cookie));
                            $guest_user_details = (fp_rac_check_is_array($details_array)) ? $details_array : array();
                            $guest_firstname_in_cookie = isset($guest_user_details['firstname']) ? $guest_user_details['firstname'] : '';
                            $guest_lastname_in_cookie = isset($guest_user_details['lastname']) ? $guest_user_details['lastname'] : '';
                            $guest_contactno_in_cookie = isset($guest_user_details['phone_no']) ? $guest_user_details['phone_no'] : '';
                            $guest_email_in_cookie = isset($guest_user_details['email']) ? $guest_user_details['email'] : '';
                            if ($guest_email_in_cookie) {
                                $cart_details_compact = array($guest_firstname_in_cookie, $guest_lastname_in_cookie, $guest_contactno_in_cookie, $guest_email_in_cookie);
                                $only_update = isset($_COOKIE['rac_checkout_entry']) ? 'yes' : '';
                                self::fp_rac_insert_update_cartlist_functionality($cart_details_compact, 'ajax', $only_update, $currentuser_lang);
                            }
                        }
                    }
                }
            }
        }

        public static function fp_rac_guest_entry_checkout_ajax() {

            check_ajax_referer('guest-entry', 'rac_security');

            if (!is_user_logged_in()) {
                $visitor_mail = isset($_POST['rac_email']) ? $_POST['rac_email'] : '';
                $visitor_first_name = isset($_POST['rac_first_name']) ? $_POST['rac_first_name'] : '';
                $visitor_last_name = isset($_POST['rac_last_name']) ? $_POST['rac_last_name'] : '';
                $visitor_phone = isset($_POST['rac_phone']) ? $_POST['rac_phone'] : '';
                $cart_details_compact = array($visitor_first_name, $visitor_last_name, $visitor_phone, $visitor_mail);
                $only_update = isset($_COOKIE['rac_checkout_entry']) || isset($_COOKIE['raccookie_guest_email']) ? 'yes' : '';
                $currentuser_lang = isset($_POST['rac_lang']) ? $_POST['rac_lang'] : fp_rac_get_current_language();
                self::fp_rac_insert_update_cartlist_functionality($cart_details_compact, 'ajax', $only_update, $currentuser_lang);
            }
            exit();
        }

        public static function fp_rac_set_guest_email_in_cookie_action() {
            if (!is_user_logged_in()) {
                $firstname = "";
                $lastname = "";
                $phone_no = "";
                if (isset($_POST['cookie_guest_email'])) {
                    $email = $_POST['cookie_guest_email'];
                    $firstname = isset($_POST['cookie_guest_fname']) ? $_POST['cookie_guest_fname'] : '';
                    $lastname = isset($_POST['cookie_guest_lname']) ? $_POST['cookie_guest_lname'] : '';
                    $phone_no = isset($_POST['cookie_guest_phone_no']) ? $_POST['cookie_guest_phone_no'] : '';
                    $details_array = array('email' => $email, 'firstname' => $firstname, 'lastname' => $lastname, 'phone_no' => $phone_no);
                    $details = serialize($details_array);
                    setcookie("raccookie_guest_email", $details, time() + 3600, '/');
                }

                echo 'success';
            }
            exit();
        }

        public static function fp_rac_already_popup_displayed_action() {
            if (!is_user_logged_in()) {
                if (isset($_POST['already_displayed'])) {
                    setcookie("rac_guest_popup_already_displayed", 'yes', time() + 3600, "/");
                }
            }
        }

        public static function fp_rac_update_guest_cart_in_checkout($currentuser_lang) {
            if (isset($_COOKIE['rac_checkout_entry'])) {
                $cart_id = $_COOKIE['rac_checkout_entry'];
                $last_cart_content = maybe_unserialize(get_post_meta($cart_id, 'rac_cart_details', true));
                if (rac_check_is_array($last_cart_content)) {
                    $last_cart_first_name = $last_cart_content['first_name'];
                    $last_cart_last_name = $last_cart_content['last_name'];
                    $last_cart_phone_no = $last_cart_content['visitor_phone'];
                    $last_cart_email = $last_cart_content['visitor_mail'];
                    $cart_details_compact = array($last_cart_first_name, $last_cart_last_name, $last_cart_phone_no, $last_cart_email);
                    self::fp_rac_insert_update_cartlist_functionality($cart_details_compact, '', '', $currentuser_lang);
                }
            }
        }

        public static function fp_rac_insert_update_cartlist_functionality($compact, $ajax = '', $only_update = '', $currentuser_lang) {
            if (isset($_COOKIE['rac_cart_id']))  //means they didn't come mail
                return '';

            global $woocommerce;
            $shipping_details = self::fp_rac_get_shipping_details();
            $currency_code = fp_rac_get_current_currency_code();
            $ip_address = rac_get_client_ip();
            $visitor_mail = $compact[3];
            $visitor_first_name = $compact[0];
            $visitor_last_name = $compact[1];
            $visitor_phone = $compact[2];

            if (!empty($ajax)) {
                if ((float) $woocommerce->version < (float) ('2.3.0')) { //compatible for below old version 2.3.0 due to calculation problem
                    WC()->cart->calculate_totals();
                }
            }

            if (function_exists('WC')) {
                $visitor_cart = WC()->cart->get_cart();
            } else {
                $visitor_cart = $woocommerce->cart->get_cart();
            }

            if (rac_check_is_array($visitor_cart)) {
                $visitor_details = fp_rac_compatible_for_sumo_plugins($visitor_cart);
                $visitor_details['first_name'] = $visitor_first_name;
                $visitor_details['last_name'] = $visitor_last_name;
                $visitor_details['visitor_phone'] = $visitor_phone;
                $visitor_details['visitor_mail'] = $visitor_mail;
                $visitor_details['shipping_details'] = $shipping_details;
                $cart_content = maybe_serialize($visitor_details);
                $user_id = '0';
                $user_info = $visitor_first_name . ',' . $visitor_last_name;
                $current_time = current_time('timestamp');
                $check_args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'raccartlist',
                    'author' => 0,
                    'post_status' => array('rac-cart-new'),
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'rac_user_details',
                            'value' => $user_id,
                            'compare' => '=',
                        ),
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'rac_cart_ip_address',
                                'value' => $ip_address,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'rac_cart_email_id',
                                'value' => $visitor_mail,
                                'compare' => '=',
                            ),
                        ),
                    ),
                    'fields' => 'ids'
                );
                $check_previous = fp_rac_check_query_having_posts($check_args);
                $product_ids = fp_rac_extract_cartlist_content($cart_content);
                $product_ids = implode(',', $product_ids);
                //check for duplication
                if ((!empty($check_previous) && !empty($check_previous[0])) || $only_update == 'yes') {//update
                    $update_args = array('rac_cart_details' => $cart_content,
                        'rac_user_details' => $user_id,
                        'rac_cart_email_id' => $visitor_mail,
                        'rac_cart_abandoned_time' => $current_time,
                        'rac_cart_currency_code' => $currency_code,
                        'rac_product_details' => $product_ids,
                        'rac_user_info' => $user_info,
                        'rac_phone_number' => $visitor_phone
                    );

                    $details_array = array('email' => $visitor_mail, 'firstname' => $visitor_first_name, 'lastname' => $visitor_last_name, 'phone_no' => $visitor_phone);
                    $details = serialize($details_array);
                    setcookie("raccookie_guest_email", $details, time() + 3600, "/");
                    //update post meta
                    fp_rac_update_cartlist_post_meta($update_args, implode(',',$check_previous));
                } else {//Insert New entry
                    if (fp_rac_restirct_insert_cart_based_on($visitor_mail)) {
                        $new_args = array(
                            'rac_cart_details' => $cart_content,
                            'rac_user_details' => $user_id,
                            'rac_cart_email_id' => $visitor_mail,
                            'rac_cart_ip_address' => $ip_address,
                            'rac_cart_abandoned_time' => $current_time,
                            'rac_cart_lang_code' => $currentuser_lang,
                            'rac_cart_currency_code' => $currency_code,
                            'rac_product_details' => $product_ids,
                            'rac_user_info' => $user_info,
                            'rac_phone_number' => $visitor_phone
                        );

                        //insert cartlist post
                        $insert_id = fp_rac_insert_cartlist_post($new_args, array('post_author' => 0));
                        setcookie("rac_checkout_entry", $insert_id, time() + 3600, "/");
                    }
                }
            }
        }

        public static function fp_rac_get_shipping_details() {

            $chosen_methods = WC()->session->get('chosen_shipping_methods');
            $chosen_methods = isset($chosen_methods) ? $chosen_methods[0] : '';
            $shipping_cost = isset(WC()->cart->shipping_total) ? WC()->cart->shipping_total : '';
            $shipping_tax_cost = isset(WC()->cart->shipping_tax_total) ? WC()->cart->shipping_tax_total : '';
            $shipping_details = array('shipping_cost' => $shipping_cost, 'shipping_tax_cost' => $shipping_tax_cost, 'shipping_method' => $chosen_methods);

            return $shipping_details;
        }

        public static function remove_action_hook() {
            remove_action('woocommerce_cart_updated', array('FP_RAC_Insert_CartList_Entry', 'fp_rac_insert_entry'));
        }

        public static function fp_rac_insert_old_order_entry($order_id) {
            $insert_id = '';
            $user_id = "old_order";
            $order = fp_rac_get_order_obj($order_id);
            $cart_details = maybe_serialize($order);
            $product_ids = fp_rac_extract_cartlist_content($order);
            $product_ids = implode(',', $product_ids);
            $order_user_id = fp_rac_get_order_obj_data($order, 'user_id');
            $author = ($order_user_id != '') ? $order_user_id : 0;
            $user_email = fp_rac_get_order_obj_data($order, 'billing_email');
            $first_name = fp_rac_get_order_obj_data($order, 'billing_first_name');
            $last_name = fp_rac_get_order_obj_data($order, 'billing_last_name');
            $phone_number = fp_rac_get_order_obj_data($order, 'billing_phone');
            $modified_date = fp_rac_get_order_obj_data($order, 'modified_date');
            $order_modified_time = strtotime($modified_date); //convert as unix timestamp, so it can be used in comparing even though it is dead old
            $order_items = $order->get_items();
            $get_items = FP_RAC_Previous_Order_Data::rac_prepare_items($order_items);
            $extra_cart_content = array(
                'get_items' => $get_items,
                'user_id' => $author,
                'billing_first_name' => $first_name,
                'billing_last_name' => $last_name,
                'billing_email' => $user_email,
                'billing_phone' => $phone_number,
                'shipping_tax' => $order->get_shipping_tax(),
                'total_shipping' => $order->get_total_shipping(),
                'shipping_method' => $order->get_shipping_method()
            );
            $new_args = array(
                'rac_cart_details' => $cart_details,
                'rac_user_details' => $user_id,
                'rac_cart_email_id' => $user_email,
                'rac_cart_abandoned_time' => $order_modified_time,
                'rac_product_details' => $product_ids,
                'rac_user_info' => $first_name . ',' . $last_name,
                'rac_phone_number' => $phone_number,
                'extra_cart_content' => (object) $extra_cart_content
            );

            //insert oldorder cartlist as a post
            $insert_id = fp_rac_insert_cartlist_post($new_args, array('post_status' => 'rac-cart-abandon', 'post_author' => $author));

            return $insert_id;
        }

    }

    FP_RAC_Insert_CartList_Entry::init();
}