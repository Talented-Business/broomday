<?php

/*
 * Common functions  for cart status and email
 * 
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function fp_rac_check_is_array($array) {
    if (is_array($array)) {
        $array = $array;
    } else {
        $array = explode(',', $array);
    }
    return $array;
}

function rac_check_is_array($array) {
    if (is_array($array) && !empty($array)) {
        return true;
    } else {
        return false;
    }
}

function rac_date_format() {
    $date_format = get_option('rac_date_format');
    if ($date_format == '') {
        $date_format = 'd:m:y';
    }
    return $date_format;
}

function rac_time_format() {
    $time_format = get_option('rac_time_format');
    if ($time_format == '') {
        $time_format = 'h:i:s';
    }
    return $time_format;
}

function rac_get_client_ip() {
    $ipaddress = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (!empty($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];

    return $ipaddress;
}

function fp_rac_url_for_checkout_or_cart_with_lan($lang_code) {
    if (get_option('rac_cartlink_redirect') == '2') {
        $redirect_url = rac_get_page_permalink_dependencies('checkout');
        if ($lang_code != NULL) {
            $redirect_url = $lang_code == 'en' ? $redirect_url : fp_rac_wpml_convert_url($redirect_url, $lang_code);
        }
    } else {
        $redirect_url = rac_get_page_permalink_dependencies('cart');

        if ($lang_code != NULL) {

            $redirect_url = $lang_code == 'en' ? $redirect_url : fp_rac_wpml_convert_url($redirect_url, $lang_code);
        }
    }
    return $redirect_url;
}

function rac_get_page_permalink_dependencies($page) {
    $redirect_url = get_permalink(get_option('woocommerce_' . $page . '_page_id'));
    return $redirect_url;
}

function fp_rac_placeholder_img() {
    $size = array(
        'width' => '90',
        'height' => '90',
        'crop' => 1
    );

    return '<img src="' . wc_placeholder_img_src() . '" alt="Placeholder" width="' . esc_attr($size['width']) . '" height="' . esc_attr($size['height']) . '" />';
}

function fp_rac_get_current_language() {
    if (function_exists('icl_register_string')) {
        $currentuser_lang = isset($_SESSION['wpml_globalcart_language']) ? $_SESSION['wpml_globalcart_language'] : ICL_LANGUAGE_CODE;
    } else {
        $currentuser_lang = 'en';
    }
    return $currentuser_lang;
}

function fp_rac_update_cartlist_status($user) {
    $current_time = current_time('timestamp');
    if ($user == 'member') {
        $check = '!=';
        $cut_off_time = fp_rac_get_interval('rac_abandon_cart_time', 'rac_abandon_cart_time_type');
    } else {//guest
        $check = '=';
        $cut_off_time = fp_rac_get_interval('rac_abandon_cart_time_guest', 'rac_abandon_cart_time_type_guest');
    }
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'raccartlist',
        'post_status' => array('rac-cart-new'),
        'meta_query' => array(
            array(
                'key' => 'rac_user_details',
                'value' => '0',
                'compare' => $check
            ),
        ),
        'fields' => 'ids'
    );
    $status_new_list = fp_rac_check_query_having_posts($args);
    if (rac_check_is_array($status_new_list)) {
        foreach ($status_new_list as $cart_id) {
            $cut_off_time = $cut_off_time + get_post_meta($cart_id, 'rac_cart_abandoned_time', true);
            if ($current_time > $cut_off_time) {
                $args = array('ID' => $cart_id, 'post_type' => 'raccartlist', 'post_status' => 'rac-cart-abandon');
                wp_update_post($args);
                if (get_post_meta($cart_id, 'fp_rac_admin_notification_sent_for_cart_abandoned', true) != 'yes') {
                    FP_RAC_ADMIN_Notification_Email::fp_rac_mail_admin_cart_abandoned($cart_id);
                    update_post_meta($cart_id, 'fp_rac_admin_notification_sent_for_cart_abandoned', 'yes');
                }
                FPRacCounter::rac_do_abandoned_count();
            }
        }
    }
}

function fp_rac_get_interval($interval_time, $interval_type) {
    $interval = (float)get_option($interval_time, 12);
    $type = get_option($interval_type, 'hours');
    if ($type == 'minutes') {
        $interval = $interval * 60;
    } else if ($type == 'hours') {
        $interval = $interval * 3600;
    } else if ($type == 'days') {
        $interval = $interval * 86400;
    }
    return $interval;
}

function fp_rac_array_column_function($array, $function = 'fp_rac_array_map', $id = 'user_email') {
    if (function_exists('array_column')) {
        $array = array_column($array, $id);
    } else {
        $array = array_map($function, $array);
    }
    return $array;
}

function fp_rac_array_map($array) {

    return $array['user_email'];
}

function fp_rac_array_map_post_ids($array) {

    return $array['ID'];
}

function fp_rac_check_user_already_bought($email, $user_id, $cart) {
    if (get_option('rac_email_restrict_when_cutomer_already_bought_product') == 'yes') {
        $product_ids = fp_rac_get_cart_list_product_ids($cart);
        return fp_rac_customer_bought_product($email, $user_id, $product_ids);
    } else {
        return true;
    }
}

function fp_rac_customer_bought_product($customer_email, $user_id, $product_array) {
    global $wpdb;
    $dummy_array = array();
    $customer_data = array($user_id);

    if ($user_id) {
        $user = get_user_by('id', $user_id);

        if (isset($user->user_email)) {
            $customer_data[] = $user->user_email;
        }
    }

    if (is_email($customer_email)) {
        $customer_data[] = $customer_email;
    }
    $customer_data = array_map('esc_sql', array_filter(array_unique($customer_data)));
    $statuses = array_map('esc_sql', fp_rac_get_paid_statuses_of_order());

    if (sizeof($customer_data) == 0) {
        return false;
    }

    $result = $wpdb->get_col("
			SELECT im.meta_value FROM {$wpdb->posts} AS p
			INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
			WHERE p.post_status IN ( 'wc-" . implode("','wc-", $statuses) . "' )
			AND pm.meta_key IN ( '_billing_email', '_customer_user' )
			AND im.meta_key IN ( '_product_id', '_variation_id' )
			AND im.meta_value != 0
			AND pm.meta_value IN ( '" . implode("','", $customer_data) . "' )
		");
    $result = array_map('absint', $result);

    if (empty($result)) {
        return true;
    }
    $dummy_array = array_intersect($product_array, $result);
    if (empty($dummy_array)) {
        return true;
    } else {
        return false;
    }
}

function fp_rac_user_roles($extra_role = false) {
    global $wp_roles;

    if (is_object($wp_roles)) {
        $role_names = $wp_roles->role_names;
        if (rac_check_is_array($role_names)) {
            foreach ($role_names as $key => $value) {
                $userrole[] = $key;
                $username[] = $value;
            }
        }
    }
    $user_role = array_combine((array) $userrole, (array) $username);
    if ($extra_role) {
        $user_role = array_merge($user_role, $extra_role);
    }
    return $user_role;
}

function fp_rac_get_category() {
    $categorylist = array();
    $categoryname = array();
    $categoryid = array();
    $particularcategory = get_terms('product_cat');
    if (!is_wp_error($particularcategory)) {
        if (!empty($particularcategory)) {
            if (is_array($particularcategory)) {
                foreach ($particularcategory as $category) {
                    $categoryname[] = $category->name;
                    $categoryid[] = $category->term_id;
                }
            }
            $categorylist = array_combine((array) $categoryid, (array) $categoryname);
        }
    }
    return $categorylist;
}

function fp_rac_select_options($select_array, $selected_value = false) {
    $option = '';
    if (rac_check_is_array($select_array)) {
        foreach ($select_array as $key => $value) {
            $selected = '';
            if (rac_check_is_array($selected_value)) {
                if (in_array($key, $selected_value)) {
                    $selected = 'selected=selected';
                }
            } else {
                if ($selected_value && $selected_value == $key) {
                    $selected = 'selected=selected';
                }
            }
            $option .= "<option value=" . $key . " " . $selected . ">" . $value . "</option>";
        }
    }
    return $option;
}

function fp_rac_check_email_subscribed($to) {
    $check_user_id = 0;
    $email_id = '';
    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $email_id = $to;
        $check_user_id = check_is_member_or_guest($to, true);
    } else {
        $check_user_id = $to;
    }

    if ($check_user_id) {
        // for member
        $status = get_user_meta($check_user_id, 'fp_rac_mail_unsubscribed', true);
        if ($status != 'yes') {
            return true;
        } else {
            return false;
        }
    } else {
        // for guest
        $needle = $email_id;
        $email_array = (array) get_option('fp_rac_mail_unsubscribed');
        $filtered_email_array = array_filter($email_array);
        if (!in_array($needle, $filtered_email_array)) {
            return true;
        } else {
            return false;
        }
    }
}

function rac_get_user_id_from_cart_list($cart) {
    if ($cart->user_id == 'old_order') {
        $old_order_obj = new FP_RAC_Previous_Order_Data($cart);
        if ($old_order_obj->get_cart_content()) {
            $id = $old_order_obj->get_user_id();
            return $id;
        }
    }
    return $cart->user_id;
}

function check_is_member_or_guest($to, $bool = false) {

    $get_user_by_email = get_user_by('email', $to);

    if ($get_user_by_email) {
        return $bool ? $get_user_by_email->ID : true;
    } else {
        return 0;
    }
}

function rac_return_user_id($memberemail) {

    $get_user_by_email = get_user_by('email', $memberemail);

    return $get_user_by_email->ID;
}

function fp_rac_restirct_insert_cart_based_on($user_email) {
    $insert_cart_based = get_option('rac_remove_carts');
    $status = array();
    if ($insert_cart_based == 'no') {
        return true;
    } elseif ($insert_cart_based == 'pre_cart') {
        $dont_cpature_option = get_option('rac_dont_capture_for_option');
        if (empty($dont_cpature_option)) {
            return true;
        } else {
            $new_status = array('NEW' => 'rac-cart-new',
                'ABANDON' => 'rac-cart-abandon',
                'RECOVERED' => 'rac-cart-recovered');
            foreach ($new_status as $key => $value) {
                if (in_array($key, (array) $dont_cpature_option))
                    $status = $value;
            }
        }
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'raccartlist',
            'post_status' => $status,
            'meta_query' => array(
                array(
                    'key' => 'rac_cart_email_id',
                    'value' => $user_email,
                ),
            ),
            'fields' => 'ids'
        );
        $check = fp_rac_check_query_having_posts($args);
        if (((!is_null($check)) && (!empty($check)))) {
            return false;
        } else {
            return true;
        }
    } else {
        $new_carts = array();
        $abandon_carts = array();
        $overall_carts = array();
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'raccartlist',
            'meta_query' => array(
                array(
                    'key' => 'rac_cart_email_id',
                    'value' => $user_email,
                ),
            ),
            'fields' => 'ids'
        );
        if (get_option('rac_remove_new') == 'yes') {
            $args['post_status'] = 'rac-cart-new';
            $new_carts = fp_rac_check_query_having_posts($args);
        }
        if (get_option('rac_remove_abandon') == 'yes') {
            $args['post_status'] = 'rac-cart-abandon';
            $abandon_carts = fp_rac_check_query_having_posts($args);
        }
        $overall_carts = array_merge($new_carts, $abandon_carts);
        if (rac_check_is_array($overall_carts)) {
            foreach ($overall_carts as $new_cart_id) {
                wp_delete_post($new_cart_id, true);
            }
        }
        return true;
    }
}

function fp_rac_get_cart_list($email, $type = 'email') {
    $user_key = ($type == 'email') ? 'rac_cart_email_id' : 'rac_user_details';

    $args = array(
        'post_type' => 'raccartlist',
        'post_status' => array('rac-cart-new', 'trash', 'rac-cart-abandon', 'rac-cart-recovered'),
        'meta_query' => array(
            array(
                'key' => $user_key,
                'value' => $email,
            ),
        ),
        'fields' => 'ids'
    );

    return fp_rac_check_query_having_posts($args);
}

function fp_rac_extract_cartlist_content($cart_array, $cartlist = array(), $bool = false) {
    $product_ids = array();
    $cart_array = maybe_unserialize($cart_array);
    if (is_array($cart_array) && (!empty($cart_array))) {
        if (isset($cart_array['shipping_details'])) {
            unset($cart_array['shipping_details']);
        }
        if (isset($cart_array[0]['cart'])) {
            $cart_array = $cart_array[0]['cart'];
            if (rac_check_is_array($cart_array)) {
                foreach ($cart_array as $product) {
                    $product_ids[] = $product['product_id'];
                }
            }
        } elseif (is_array($cart_array) && (!empty($cart_array))) {
            if (isset($cart_array['visitor_mail'])) {
                unset($cart_array['visitor_mail']);
            }
            if (isset($cart_array['first_name'])) {
                unset($cart_array['first_name']);
            }
            if (isset($cart_array['last_name'])) {
                unset($cart_array['last_name']);
            }
            if (isset($cart_array['visitor_phone'])) {
                unset($cart_array['visitor_phone']);
            }
            foreach ($cart_array as $product) {
                $product_ids[] = $product['product_id'];
            }
        }
    } else {
        $get_items = array();
        if ($bool) {
            $old_order_obj = new FP_RAC_Previous_Order_Data($cartlist);
            if ($old_order_obj->get_cart_content()) {
                $get_items = $old_order_obj->get_items();
            }
        } else {
            $order = fp_rac_get_order_obj(fp_rac_get_order_obj_data($cart_array, 'id'));
            if (is_object($order)) {
                $get_items = $order->get_items();
            }
        }

        if (rac_check_is_array($get_items)) {
            foreach ($get_items as $product) {
                $product_ids[] = $product['product_id'];
            }
        }
    }

    return $product_ids;
}

function fp_rac_get_cartlist_user_details($cartlist, $type = 'phone') {
    $phone_number = '';
    $first_name = '';
    $last_name = '';
    $cart_content = maybe_unserialize($cartlist->cart_details);
    if ($cartlist->user_id == '0' && !empty($cartlist->ip_address)) {
        $phone_number = isset($cart_content['phone_number']) ? $cart_content['phone_number'] : '';
        $first_name = isset($cart_content['first_name']) ? $cart_content['first_name'] : '';
        $last_name = isset($cart_content['last_name']) ? $cart_content['last_name'] : '';
    } elseif ($cartlist->user_id == '0' && empty($cartlist->ip_address)) {
        $old_order_obj = new FP_RAC_Previous_Order_Data($cartlist);
        $cart_content_exists = $old_order_obj->get_cart_content();
        $phone_number = ($cart_content_exists) ? $old_order_obj->get_billing_phoneno() : '';
        $first_name = ($cart_content_exists) ? $old_order_obj->get_billing_firstname() : '';
        $last_name = ($cart_content_exists) ? $old_order_obj->get_billing_lastname() : '';
    } elseif ($cartlist->user_id == 'old_order') {
        $old_order_obj = new FP_RAC_Previous_Order_Data($cartlist);
        $cart_content_exists = $old_order_obj->get_cart_content();
        $phone_number = ($cart_content_exists) ? $old_order_obj->get_billing_phoneno() : '';
        $first_name = ($cart_content_exists) ? $old_order_obj->get_billing_firstname() : '';
        $last_name = ($cart_content_exists) ? $old_order_obj->get_billing_lastname() : '';
    } else {
        $user_inf = get_userdata($cartlist->user_id);
        $phone_number = is_object($user_inf) ? $user_inf->billing_phone : '';
        $first_name = is_object($user_inf) ? $user_inf->user_firstname : '';
        $last_name = is_object($user_inf) ? $user_inf->user_lastname : '';
    }

    if ($type == 'phone')
        return $phone_number;

    return array($first_name, $last_name);
}

function fp_rac_update_coupon_code($cart_id, $order_id) {
    $coupon_code = '';
    $rac_coupon_code = get_post_meta($cart_id, 'rac_cart_coupon_code', true);
    $order = fp_rac_get_order_obj($order_id);
    if (!empty($order_id)) {
        if ($order) {
            $coupons_used = $order->get_used_coupons();
            if (!empty($coupons_used)) {
                if (in_array($rac_coupon_code, $coupons_used)) {
                    $coupon_code = $rac_coupon_code;
                }
            }
        }
    }

    update_post_meta($cart_id, 'rac_coupon_details', $coupon_code);
}

function fp_rac_get_compatible_product_name($product, $curreny_code, $lang) {
    if (!empty($product['addons'])) {
        $product_name = fp_rac_product_addons_compatibility($product, $curreny_code);
    } else {
        $product_name = FP_RAC_Polish_Product_Info::get_product_name($product);
    }

    $product_name = fp_rac_extra_details_in_product_name($product_name, $product, $lang);
    return $product_name;
}

function fp_rac_backward_compatibility_for_table_sorting($id) {
    //backward compatibility of this plugin version 18.0
    if ($option_value = get_user_option($id)) {
        $order = ($option_value == 'yes') ? 'ASC' : 'DESC';
    } elseif ($option_value = get_option($id)) {
        $order = ($option_value == 'yes') ? 'ASC' : 'DESC';
    } else {
        $order = 'ASC';
    }
    return $order;
}

function fp_rac_get_order_status() {
    if (function_exists('wc_get_order_statuses')) {
        $order_list_keys = array_keys(wc_get_order_statuses());
        $order_list_values = array_values(wc_get_order_statuses());
        $orderlist_replace = str_replace('wc-', '', $order_list_keys);
        $orderlist_combine = array_combine($orderlist_replace, $order_list_values);
    } else {
        $order_status = (array) get_terms('shop_order_status', array('hide_empty' => 0, 'orderby' => 'id'));
        if (rac_check_is_array($order_status)) {
            foreach ($order_status as $value) {
                $status_name[] = $value->name;
                $status_slug[] = $value->slug;
            }
        }
        $orderlist_combine = array_combine($status_slug, $status_name);
    }

    return $orderlist_combine;
}
