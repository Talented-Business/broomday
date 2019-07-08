<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Cart_Data_From_Mail')) {

    /**
     * FP_RAC_Cart_Data_From_Mail Class.
     */
    class FP_RAC_Cart_Data_From_Mail {

        public static function init() {
            add_action('wp_head', array(__CLASS__, 'recover_old_order_rac'));
            add_action('wp_head', array(__CLASS__, 'fp_rac_guest_cart_recover'));
            add_action('wp_head', array(__CLASS__, 'fp_rac_add_abandon_cart'));
        }

        /*
         * only perform recover from member mail
         * 
         */

        public static function fp_rac_add_abandon_cart() {
            global $woocommerce;
            //only perform recover from member mail
            if (isset($_GET['abandon_cart']) && !isset($_GET['guest']) && !isset($_GET['checkout']) && !isset($_GET['old_order'])) {
                $abandon_cart_id = $_GET['abandon_cart'];
                $email_template_id = $_GET['email_template'];
                $cart_id = self::fp_rac_get_link_cart_ids($abandon_cart_id);
                if (isset($cart_id)) {
                    $last_cart = fp_rac_create_cart_list_obj($cart_id);
                    $user_details = maybe_unserialize($last_cart->cart_details);
                    unset($user_details['shipping_details']);
                    if (rac_check_is_array($user_details)) {
                        foreach ($user_details as $cart) {
                            $cart_content = $cart['cart'];
                        }
                    }

                    if (get_option('rac_cart_content_when_cart_link_is_clicked') != 'no') {
                        $new_session_cart = $cart_content;
                    } else {
                        $old_session_cart = WC()->session->cart;
                        $cart_content = fp_rac_check_is_array($cart_content);
                        $array_cart_content_filter = array_filter($cart_content);
                        $old_session_cart = fp_rac_check_is_array($old_session_cart);
                        $array_session_cart_filter = array_filter($old_session_cart);
                        $new_session_cart = array_merge($array_cart_content_filter, $array_session_cart_filter);
                    }

                    if (!isset($_COOKIE['rac_cart_id'])) {
                        if (function_exists('WC')) {
                            WC()->session->cart = $new_session_cart;
                        } else {
                            $woocommerce->session->cart = $new_session_cart;
                        }
                        setcookie("rac_cart_id", $cart_id, time() + 3600, "/");
                    } else {
                        $get_cookie_id = $_COOKIE['rac_cart_id'];
                        if ($get_cookie_id != $cart_id) {
                            if (function_exists('WC')) {
                                WC()->session->cart = $new_session_cart;
                            } else {
                                $woocommerce->session->cart = $new_session_cart;
                            }
                            setcookie("rac_cart_id", $cart_id, time() + 3600, "/");
                        }
                    }
                    fp_rac_compatible_for_sumo_plugins_to_recover($new_session_cart);
                    self::fp_rac_update_data_in_cart_list($last_cart, $email_template_id, $cart_id);
                    $redirect_url = fp_rac_url_for_checkout_or_cart_with_lan($last_cart->wpml_lang);
                } else {
                    $currentuser_lang = fp_rac_get_current_language();
                    $redirect_url = fp_rac_url_for_checkout_or_cart_with_lan($currentuser_lang);
                    wc_add_notice(__('Seems your cart has been already Recovered/Order Placed', 'recoverabandoncart'), 'error');
                }
                if (!is_user_logged_in()) {
                    if (function_exists('WC')) {
                        WC()->session->set_customer_session_cookie(true);
                    } else {
                        $woocommerce->session->set_customer_session_cookie(true);
                    }
                }
                wp_safe_redirect($redirect_url);
                exit;
            }
        }

        /*
         * only perform recover from guest mail
         * 
         */

        public static function fp_rac_guest_cart_recover() {
            global $woocommerce;
            if (isset($_GET['guest'])) {
                $email_template_id_final = '';
                $abandon_cart_id = $_GET['abandon_cart'];
                $email_template_id = $_GET['email_template'];
                $cart_id = self::fp_rac_get_link_cart_ids($abandon_cart_id);
                if (class_exists('FP_Donation_Automatic')) {//Compatible for SUMO Donation
                    remove_action('woocommerce_before_calculate_totals', array('FP_Donation_Automatic', 'automatic_main_function_donation_form'));
                }
                if (isset($cart_id)) {
                    $last_cart = fp_rac_create_cart_list_obj($cart_id);
                    $expected_object = maybe_unserialize($last_cart->cart_details);
                    if (is_object($expected_object)) {
                        $old_order_obj = new FP_RAC_Previous_Order_Data($last_cart);
                        if ($old_order_obj->get_cart_content()) {
                            $cart_details = $old_order_obj->get_items();
                            //For Object Recover Abandon Cart
                            if (get_option('rac_cart_content_when_cart_link_is_clicked') == 'yes') {
                                $woocommerce->cart->empty_cart();
                            }
                            if (is_array($cart_details) && !empty($cart_details)) {
                                foreach ($cart_details as $products) {
                                    $product = fp_rac_get_product($products['product_id']);
                                    $quantity = isset($products['quantity']) ? $products['quantity'] : $products['qty'];
                                    if (!empty($products['variation_id'])) {
                                        $variations = array();
                                        if (is_array($products['item_meta']) && !empty($products['item_meta'])) {
                                            foreach ($products['item_meta'] as $meta_name => $meta_value) {
                                                $attributes = $product->get_variation_attributes();
                                                $lower_case = array_change_key_case($attributes, CASE_LOWER);
                                                if (!empty($lower_case[$meta_name])) {
                                                    if (!is_null($lower_case[$meta_name])) {
                                                        $value_true = in_array(strtolower($meta_value[0]), array_map('strtolower', $lower_case[$meta_name]));
                                                    } else {
                                                        $value_true = false;
                                                    }
                                                }

                                                if (in_array(strtolower($meta_name), array_map('strtolower', array_keys($attributes))) && $value_true) {
                                                    $variations[$meta_name] = $meta_value[0];
                                                }
                                            }
                                        }
                                        $cart_item_key = $woocommerce->cart->add_to_cart($products['product_id'], $quantity, $products['variation_id'], array_filter($variations));
                                    } else {
                                        $cart_item_key = $woocommerce->cart->add_to_cart($products['product_id'], $quantity);
                                    }
                                    if (!empty($products['addons']) && $cart_item_key) {
                                        $woocommerce->cart->cart_contents[$cart_item_key]['addons'] = $products['addons'];
                                        $woocommerce->cart->set_session();
                                    }
                                }
                            }
                            setcookie("rac_cart_id", $cart_id, time() + 3600, "/");
                        }
                    } elseif (is_array($expected_object)) {

                        $expected_object = maybe_unserialize($last_cart->cart_details);
                        $cart_details = $expected_object;
                        unset($cart_details['visitor_mail']);
                        unset($cart_details['first_name']);
                        unset($cart_details['last_name']);
                        unset($cart_details['visitor_phone']);
                        unset($cart_details['shipping_details']);
                        if (get_option('rac_cart_content_when_cart_link_is_clicked') == 'yes') {
                            $woocommerce->cart->empty_cart();
                        }
                        if (is_array($cart_details) && !empty($cart_details)) {
                            foreach ($cart_details as $products) {
                                if (!empty($products['variation_id'])) {
                                    $variations = array();
                                    if (rac_check_is_array($products['variation'])) {
                                        foreach ($products['variation'] as $attr_name => $attr_val) {
                                            $var_name = str_replace("attribute_", '', $attr_name);
                                            $variations[$var_name] = $attr_val;
                                        }
                                    }
                                    $cart_item_key = $woocommerce->cart->add_to_cart($products['product_id'], $products['quantity'], $products['variation_id'], $variations, $products);
                                } else {
                                    $cart_item_key = $woocommerce->cart->add_to_cart($products['product_id'], $products['quantity']);
                                }
                                if (!empty($products['addons']) && $cart_item_key) {
                                    $woocommerce->cart->cart_contents[$cart_item_key]['addons'] = $products['addons'];
                                    $woocommerce->cart->set_session();
                                }
                            }
                        }
                        setcookie("rac_cart_id", $cart_id, time() + 3600, "/");
                    }
                    fp_rac_compatible_for_sumo_plugins_to_recover($cart_details);
                    self::fp_rac_update_data_in_cart_list($last_cart, $email_template_id, $cart_id);
                    $redirect_url = fp_rac_url_for_checkout_or_cart_with_lan($last_cart->wpml_lang);
                } else {
                    $currentuser_lang = fp_rac_get_current_language();
                    $redirect_url = fp_rac_url_for_checkout_or_cart_with_lan($currentuser_lang);
                    wc_add_notice(__('Seems your cart has been already Recovered/Order Placed', 'recoverabandoncart'), 'error');
                }
                //Redirect again to cart
                wp_safe_redirect($redirect_url);
                exit;
            }
        }

        /*
         * only perform recover from olderorder mail
         * 
         */

        public static function recover_old_order_rac() {
            // old order made as abandoned by update button
            if (isset($_GET['old_order'])) {
                $abandon_cart_id = $_GET['abandon_cart'];
                $email_template_id = $_GET['email_template'];
                global $woocommerce;
                $cart_id = self::fp_rac_get_link_cart_ids($abandon_cart_id);
                if (isset($cart_id)) {
                    $last_cart = fp_rac_create_cart_list_obj($cart_id);
                    $expected_object = maybe_unserialize($last_cart->cart_details);
                    if (is_object($expected_object)) {
                        $old_order_obj = new FP_RAC_Previous_Order_Data($last_cart);
                        if ($old_order_obj->get_cart_content()) {
                            $cart_details = $old_order_obj->get_items();
                            if (get_option('rac_cart_content_when_cart_link_is_clicked') == 'yes') {
                                $woocommerce->cart->empty_cart();
                            }
                            if (is_array($cart_details) && !empty($cart_details)) {
                                foreach ($cart_details as $products) {
                                    $product = fp_rac_get_product($products['product_id']);
                                    $quantity = isset($products['quantity']) ? $products['quantity'] : $products['qty'];
                                    if (!empty($products['variation_id'])) {
                                        $variations = array();
                                        if (is_array($products['item_meta']) && !empty($products['item_meta'])) {
                                            foreach ($products['item_meta'] as $meta_name => $meta_value) {
                                                $attributes = $product->get_variation_attributes();
                                                $lower_case = array_change_key_case($attributes, CASE_LOWER);
                                                if (!is_null($lower_case[$meta_name])) {
                                                    $value_true = in_array(strtolower($meta_value[0]), array_map('strtolower', $lower_case[$meta_name]));
                                                } else {
                                                    $value_true = false;
                                                }
                                                if (in_array(strtolower($meta_name), array_map('strtolower', array_keys($attributes))) && $value_true) {
                                                    $variations[$meta_name] = $meta_value[0];
                                                }
                                            }
                                        }
                                        $cart_item_key = $woocommerce->cart->add_to_cart($products['product_id'], $quantity, $products['variation_id'], $variations);
                                    } else {
                                        $cart_item_key = $woocommerce->cart->add_to_cart($products['product_id'], $quantity);
                                    }
                                    if (!empty($products['addons']) && $cart_item_key) {
                                        $woocommerce->cart->cart_contents[$cart_item_key]['addons'] = $products['addons'];
                                        $woocommerce->cart->set_session();
                                    }
                                }
                            }
                        }
                    }
                    setcookie("rac_cart_id", $cart_id, time() + 3600, "/");
                    self::fp_rac_update_data_in_cart_list($last_cart, $email_template_id, $cart_id);
                    $redirect_url = fp_rac_url_for_checkout_or_cart_with_lan($last_cart->wpml_lang);
                } else {
                    $currentuser_lang = fp_rac_get_current_language();
                    $redirect_url = fp_rac_url_for_checkout_or_cart_with_lan($currentuser_lang);
                    wc_add_notice(__('Seems your cart has been already Recovered/Order Placed', 'recoverabandoncart'), 'error');
                }
                wp_safe_redirect($redirect_url);
                exit;
            }
        }

        public static function fp_rac_get_link_cart_ids($link_cart_id) {
            $cart_id = NULL;
            $args = array(
                'posts_per_page' => 1,
                'post_type' => 'raccartlist',
                'post_status' => array('rac-cart-new', 'rac-cart-abandon'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'rac_cart_old_id',
                        'value' => $link_cart_id,
                    ),
                    array(
                        'key' => 'rac_recovered_order_id',
                        'compare' => 'NOT EXISTS'
                    )
                ),
                'fields' => 'ids'
            );

            $last_cart = fp_rac_check_query_having_posts($args);
            if (rac_check_is_array($last_cart)) {
                $cart_id = $last_cart[0];
            }

            if (!isset($cart_id)) {
                $post_status = get_post_status($link_cart_id);
                if ($post_status == 'rac-cart-new' || $post_status == 'rac-cart-abandon') {
                    $cart_id = $link_cart_id;
                }
            }

            return $cart_id;
        }

        public static function fp_rac_get_link_template_ids($link_email_id) {
            $template_id = NULL;
            $args = array(
                'posts_per_page' => 1,
                'post_type' => 'racemailtemplate',
                'post_status' => array('racactive', 'racinactive', 'trash'),
                'meta_query' => array(
                    array(
                        'key' => 'rac_old_template_id',
                        'value' => $link_email_id,
                    ),
                ),
                'fields' => 'ids'
            );

            $last_template = fp_rac_check_query_having_posts($args);
            if (rac_check_is_array($last_template)) {
                $template_id = $last_template[0];
            }

            if (!isset($template_id)) {
                $post_status = get_post_status($link_email_id);
                if ($post_status == 'racactive' || $post_status == 'racinactive' || $post_status == 'trash') {
                    $template_id = $link_email_id;
                }
            }

            return $template_id;
        }

        public static function fp_rac_update_data_in_cart_list($cart_lists, $email_template_id, $abandon_cart_id) {
            $current_time = current_time('timestamp');
            $old_cart_id = get_post_meta($abandon_cart_id, 'rac_cart_old_id', true);
            $link_email_template = self::fp_rac_get_link_template_ids($email_template_id);
            if (isset($link_email_template)) {
                $old_template_id = get_post_meta($link_email_template, 'rac_old_template_id', true);
            } else {
                $old_template_id = get_post_meta($email_template_id, 'rac_old_template_id', true);
            }

            //updated email template id
            if (!empty($cart_lists->link_status)) {
                $email_template_ids_db = maybe_unserialize($cart_lists->link_status);
                if (!in_array($old_template_id, (array) $email_template_ids_db)) { //check for id duplication
                    $email_template_ids_db[] = $old_template_id;
                    $email_template_id_final = $email_template_ids_db;
                }
                $email_template_id_final = $email_template_ids_db;
            } else {
                $email_template_id_final = array($old_template_id);
            }

            //updated user visit time
            if (!empty($cart_lists->cart_link_clicked_time_log)) {
                $cart_link_clicked_time_log_db = maybe_unserialize($cart_lists->cart_link_clicked_time_log);
                if (is_array($cart_link_clicked_time_log_db)) {
                    $cart_link_clicked_time_log_db[$old_template_id] = $current_time;
                }
            } else {
                $cart_link_clicked_time_log_db[$old_template_id] = $current_time;
            }

            $cart_link_clicked_time_log_db = maybe_serialize(array_filter($cart_link_clicked_time_log_db));
            $email_template_id_final = maybe_serialize(array_filter($email_template_id_final));
            FPRacCounter::rac_do_linkc_count($abandon_cart_id, $old_template_id);
            update_post_meta($abandon_cart_id, 'rac_cart_link_status', $email_template_id_final);
            update_post_meta($abandon_cart_id, 'rac_cart_link_clicked_time_log', $cart_link_clicked_time_log_db);
        }

    }

    FP_RAC_Cart_Data_From_Mail::init();
}