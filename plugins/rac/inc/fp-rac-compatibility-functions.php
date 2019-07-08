<?php

/*
 * Common functions  for Compatibility
 * 
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/*
 * Get Each format Product Price.
 * 
 */

function fp_rac_get_format_product_price($price, $curreny_code, $eachproduct) {
    $product_id = $eachproduct['product_id'];
    $variation_id = $eachproduct['variation_id'];
    if (!empty($eachproduct['variation_id'])) {
        $common_product_id = $variation_id;
        $meta_key = '_enable_reward_points';
        $meta_value = '1';
    } else {
        $common_product_id = $product_id;
        $meta_key = '_rewardsystemcheckboxvalue';
        $meta_value = 'yes';
    }

    $points = '0';
    if (function_exists('sumo_display_subscription_plan') && get_post_meta($common_product_id, 'sumo_susbcription_status', true) == '1') {//Compatible for SUMO Subscription.
        $price = sumo_display_subscription_plan(0, $product_id);
    } else {
        $price = fp_rac_format_price($price, $curreny_code);
    }

    if (function_exists('check_level_of_enable_reward_point') && get_post_meta($common_product_id, $meta_key, true) == $meta_value) {//Compatible for SUMO Reward Points.
        $points = check_level_of_enable_reward_point($product_id, $variation_id, $eachproduct, 'no', '', 'no', 'no', '');
    }

    if (class_exists('SUMOPaymentPlans')) {
        if (isset($eachproduct['sumo_plugins']['sumo_pp']['plan_details']['under_total_column'])) {
            $price = $eachproduct['sumo_plugins']['sumo_pp']['plan_details']['under_price_column'];
            $price .= $eachproduct['sumo_plugins']['sumo_pp']['plan_details']['under_total_column'];
        }
    }

    return compact('price', 'points');
}

function fp_rac_check_is_subscription($order_id) {
    $bool = false;
    if (function_exists('sumo_is_order_contains_subscriptions')) {
        $bool = sumo_is_order_contains_subscriptions($order_id);
    }
    return $bool;
}
function fp_rac_check_is_payment_plan($order_id) {
    $bool = false;
    if (function_exists('_sumo_pp_is_payment_order'))
        $bool = _sumo_pp_is_payment_order($order_id);

    return $bool;
}

function fp_rac_get_rewards_points($product) {
    $points = '0';
    $product_id = $product['product_id'];
    $variation_id = $product['variation_id'];
    //Compatible for Sumo Reward Points
    if (!empty($product['variation_id'])) {
        $common_product_id = $variation_id;
        $point_meta_key = '_enable_reward_points';
        $meta_value = '1';
    } else {
        $common_product_id = $product_id;
        $point_meta_key = '_rewardsystemcheckboxvalue';
        $meta_value = 'yes';
    }

    if (function_exists('check_level_of_enable_reward_point') && get_post_meta($common_product_id, $point_meta_key, true) == $meta_value) {//Compatible for SUMO Reward Points.
        $points = check_level_of_enable_reward_point($product_id, $variation_id, $product, 'no', '', 'no', 'no', '');
    }
    return $points;
}

function fp_rac_get_membership_plans($product_id) {
    $member_levels = array();
    if (function_exists('sumo_get_membership_levels')) {//Compatible for SUMO Membership
        $plan_levels = sumo_get_membership_levels();
        if (rac_check_is_array($plan_levels)) {
            foreach ($plan_levels as $plan_id => $plan_name) {
                if ($product_id == get_post_meta($plan_id, 'sumomemberships_plan_associated_product', true))
                    $member_levels[$plan_id] = $plan_name;
            }
        }
    }
    return $member_levels;
}

function fp_rac_extra_details_in_product_name($product_name, $product, $lang) {
    //Reward Points Compatible
    $points = fp_rac_get_rewards_points($product);
    if (!empty($points)) {
        $message = fp_get_wpml_text('rs_message_in_shop_page_for_simple', $lang, get_option('rs_message_in_shop_page_for_simple'), 'admin_texts_rs_message_in_shop_page_for_simple');
        $message = str_replace('[rewardpoints]', $points, $message);
        $product_name = $product_name . '<br /> <br />' . $message;
    }
    //Reward Points Compatible
    //Membership Compatible
    $product_id = !empty($product['variation_id']) ? $product['variation_id'] : $product['product_id'];
    $plan_names = fp_rac_get_membership_plans($product_id);
    if (!empty($plan_names)) {
        $plan_name = implode(',', $plan_names);
        if ($product_id == $product['product_id']) {
            $membership_message = __("Purchasing this product will provide access to ", 'sumomemberships');
        } else {
            $membership_message = __("Purchasing this Variation will provide access to ", 'sumomemberships');
        }
        $product_name = $product_name . '<br /> <br />' . $membership_message . $plan_name;
    }

    if (class_exists('SUMOPaymentPlans')) {
        if (isset($product['sumo_plugins']['sumo_pp']['plan_details']['under_product_column'])) {
            $product_name .= $product['sumo_plugins']['sumo_pp']['plan_details']['under_product_column'];
        }
    }

    //Membership Compatible
    return $product_name;
}

/*
 * Compatible to SUMO Plugins and to insert extra data.
 */

function fp_rac_compatible_for_sumo_plugins($cart_content) {
    if (rac_check_is_array($cart_content)) {
        if (class_exists('FP_DonationSystem')) {
            $cart_content = fp_rac_extract_cart_content_to_insert($cart_content, 'fp_donation');
        } elseif (class_exists('PayYourPrice')) {
            $cart_content = fp_rac_extract_cart_content_to_insert($cart_content, 'fp_pyp');
        } elseif (class_exists('SUMOPaymentPlans')) {
            $cart_content = fp_rac_extract_cart_content_to_insert($cart_content, 'sumo_pp');
        }
    }
    return $cart_content;
}

/*
 * insert extra data in cart object
 */

function fp_rac_extract_cart_content_to_insert($cart_content, $plugin_name) {
    $cart_array = $cart_content;
    if (rac_check_is_array($cart_array)) {
        if (isset($cart_array[0]['cart'])) {
            $old_cart_array = $cart_array[0]['cart'];
            if (rac_check_is_array($cart_array)) {
                foreach ($old_cart_array as $key => $product) {
                    $product = fp_rac_insert_extra_data_in_obj($product, $plugin_name, $key);
                    $new_cart_array[$key] = $product;
                }
                $cart_array[0]['cart'] = $new_cart_array;
            }
        } else {
            if (rac_check_is_array($cart_array)) {
                foreach ($cart_array as $key => $product) {
                    $product = fp_rac_insert_extra_data_in_obj($product, $plugin_name, $key);
                    $new_cart_array[$key] = $product;
                }
                $cart_array = $new_cart_array;
            }
        }
    }

    return $cart_array;
}

function fp_rac_insert_extra_data_in_obj($product, $plugin_name, $key) {
    $variation_id = !empty($product['variation_id']) ? $product['variation_id'] : $product['product_id'];
    if ($plugin_name == 'fp_donation') {
        $manual_donation_product = get_option('ds_select_particular_products');
        $auto_donation_product = get_option('ds_select_particular_products_automatic');
        if ($manual_donation_product == $product['product_id'] || $manual_donation_product == $variation_id) {
            $product['sumo_plugins']['fp_donation'] = array('donation' => 'manual', 'product_id' => $variation_id, 'donation_amount' => $product['line_total']);
        } elseif ($auto_donation_product == $product['product_id'] || $auto_donation_product == $variation_id) {
            $product['sumo_plugins']['fp_donation'] = array('donation' => 'auto', 'product_id' => $variation_id, 'donation_amount' => $product['line_total']);
        }
    } elseif ($plugin_name == 'fp_pyp') {
        $meta_key = !empty($product['variation_id']) ? '_selectpayyourprice' : '_checkboxvalue';
        $meta_value = !empty($product['variation_id']) ? 'two' : 'yes';
        if (get_post_meta($variation_id, $meta_key, true) == $meta_value) {
            $product['sumo_plugins']['fp_pyp'] = array('product_id' => $variation_id, 'pyp_amount' => $product['line_total'], 'pyp_key' => $key);
        }
    } elseif ($plugin_name == 'sumo_pp') {
        $product_object = fp_rac_get_product($variation_id);
        $payment_data = _sumo_pp_get_cart_data($product_object);
        $plan_details = _sumo_pp_get_cart_payment_display_string($product_object);
        $product['sumo_plugins']['sumo_pp'] = array('product_id' => $variation_id, 'plan_details' => $plan_details, 'balance_payable' => _sumo_pp_get_cart_balance_payable_amount(), 'sumo_pp_payment_data' => $payment_data);
    }
    return $product;
}

function fp_rac_compatible_for_sumo_plugins_to_recover($cart_content) {

    foreach ($cart_content as $product) {
        if (isset($product['sumo_plugins'])) {
            if (rac_check_is_array($product['sumo_plugins'])) {
                $variation_id = !empty($product['variation_id']) ? $product['variation_id'] : $product['product_id'];
                foreach ($product['sumo_plugins'] as $plugin_name => $plugin_data) {
                    if ($plugin_name == 'fp_donation') {
                        if ($plugin_data['donation'] == 'manual') {
                            if (class_exists('FP_DonationSystem')) {
                                $manual_donation_product = get_option('ds_select_particular_products');
                                WC()->session->set('fp_donation_amount', $plugin_data['donation_amount']);
                                WC()->session->set('fp_donation_product', $manual_donation_product);
                            }
                        }
                    } elseif ($plugin_name == 'fp_pyp') {
                        if (class_exists('PayYourPrice')) {
                            WC()->session->set($plugin_data['pyp_key'] . '_set_payyourprice_contribution', $plugin_data['pyp_amount']);
                        }
                    } elseif ($plugin_name == 'sumo_pp') {
                        if (function_exists('_sumo_pp_set_payment_session')) {
                            if (isset($plugin_data['sumo_pp_payment_data']))
                                _sumo_pp_set_payment_session($plugin_data['sumo_pp_payment_data']);
                        }
                    }
                }
            }
        }
    }
}

function fp_rac_check_sumo_coupon_exists($cart_total) {
    $selected_rule = array();
    if (function_exists('fp_sumocoupon_check_coupon_exists')) {
        $selected_rule = fp_sumocoupon_check_coupon_exists($cart_total);
    }

    return $selected_rule;
}
