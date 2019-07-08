<?php

/*
 * Common functions for post type
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function fp_rac_insert_emaillog_post($meta_args, $post_args = array()) {
    $post_defaults = array(
        'post_type' => 'racmaillog',
        'post_status' => 'publish'
    );

    $post_args = wp_parse_args($post_args, $post_defaults);
    //Insert Post 
    $post_id = wp_insert_post($post_args);

    $meta_defaults = array(
        'rac_email_id' => false,
        'rac_date_time' => false,
        'rac_template_used' => false,
        'rac_cart_id' => false,
    );

    $postmeta_args = wp_parse_args($meta_args, $meta_defaults);
    //update postmeta
    fp_rac_update_emaillog_post_meta($postmeta_args, $post_id);

    return $post_id;
}

function fp_rac_update_emaillog_post_meta($postmeta_args, $post_id) {
    $meta_defaults = array(
        'rac_email_id' => false,
        'rac_date_time' => false,
        'rac_template_used' => false,
        'rac_cart_id' => false,
    );
    $postmeta_args = wp_parse_args($postmeta_args, $meta_defaults);
    //Update postmeta
    if (rac_check_is_array($postmeta_args)) {
        foreach ($postmeta_args as $meta_name => $value) {
            if ($value)
                update_post_meta($post_id, $meta_name, $value);
        }
    }
}

function fp_rac_insert_cartlist_post($meta_args, $post_args = array()) {

    $post_defaults = array(
        'post_type' => 'raccartlist',
        'post_status' => 'rac-cart-new'
    );


    $post_args = wp_parse_args($post_args, $post_defaults);
    //Insert Post 
    $post_id = wp_insert_post($post_args);

    $meta_defaults = array(
        'rac_cart_old_id' => $post_id,
        'rac_cart_details' => false,
        'rac_user_details' => false,
        'rac_cart_email_id' => false,
        'rac_cart_abandoned_time' => false,
        'rac_cart_email_template_id' => false,
        'rac_cart_email_template_sending_time' => false,
        'rac_cart_link_clicked_time_log' => false,
        'rac_cart_ip_address' => false,
        'rac_cart_link_status' => false,
        'rac_cart_sending_status' => 'SEND',
        'rac_cart_lang_code' => false,
        'rac_cart_currency_code' => false,
        'rac_recovered_order_id' => false,
        'rac_cart_payment_details' => false
    );

    $postmeta_args = wp_parse_args($meta_args, $meta_defaults);
    //update postmeta
    fp_rac_update_cartlist_post_meta($postmeta_args, $post_id);

    return $post_id;
}

function fp_rac_update_cartlist_post_meta($postmeta_args, $post_id) {
    $meta_defaults = array(
        'rac_cart_old_id' => false,
        'rac_cart_details' => false,
        'rac_user_details' => false,
        'rac_cart_email_id' => false,
        'rac_cart_abandoned_time' => false,
        'rac_cart_email_template_id' => false,
        'rac_cart_email_template_sending_time' => false,
        'rac_cart_link_clicked_time_log' => false,
        'rac_cart_ip_address' => false,
        'rac_cart_link_status' => false,
        'rac_cart_sending_status' => false,
        'rac_cart_lang_code' => false,
        'rac_cart_currency_code' => false,
        'rac_recovered_order_id' => false,
        'rac_cart_payment_details' => false,
        'rac_product_details' => false
    );
    $postmeta_args = wp_parse_args($postmeta_args, $meta_defaults);
    //Update postmeta
    if (rac_check_is_array($postmeta_args)) {
        foreach ($postmeta_args as $meta_name => $value) {
            if ($value !== false)
                update_post_meta($post_id, $meta_name, $value);
        }
    }
}

function fp_rac_insert_recovered_order_post($meta_args, $post_args = array()) {
    $post_defaults = array(
        'post_type' => 'racrecoveredorder',
        'post_status' => 'publish'
    );

    $post_args = wp_parse_args($post_args, $post_defaults);
    //Insert Post 
    $post_id = wp_insert_post($post_args);

    $meta_defaults = array(
        'rac_cart_id' => false,
        'rac_order_id' => false,
        'rac_template_id' => false,
        'rac_product_details' => false,
        'rac_recovered_sales_total' => false,
        'rac_recovered_date' => false,
    );

    $postmeta_args = wp_parse_args($meta_args, $meta_defaults);
    //update postmeta
    fp_rac_update_recovered_order_post_meta($postmeta_args, $post_id);

    return $post_id;
}

function fp_rac_update_recovered_order_post_meta($postmeta_args, $post_id) {
    $meta_defaults = array(
        'rac_cart_id' => false,
        'rac_order_id' => false,
        'rac_template_id' => false,
        'rac_product_details' => false,
        'rac_recovered_sales_total' => false,
        'rac_recovered_date' => false,
    );
    $postmeta_args = wp_parse_args($postmeta_args, $meta_defaults);
    //Update postmeta
    if (rac_check_is_array($postmeta_args)) {
        foreach ($postmeta_args as $meta_name => $value) {
            if ($value)
                update_post_meta($post_id, $meta_name, $value);
        }
    }
}

function fp_rac_create_cart_list_obj($post_id, $type = 'object') {
    $cart_array = array(
        'old_id' => 'rac_cart_old_id',
        'cart_details' => 'rac_cart_details',
        'user_id' => 'rac_user_details',
        'email_id' => 'rac_cart_email_id',
        'cart_abandon_time' => 'rac_cart_abandoned_time',
        'ip_address' => 'rac_cart_ip_address',
        'link_status' => 'rac_cart_link_status',
        'sending_status' => 'rac_cart_sending_status',
        'wpml_lang' => 'rac_cart_lang_code',
        'currency_code' => 'rac_cart_currency_code',
        'placed_order' => 'rac_recovered_order_id',
        'completed' => 'rac_cart_payment_details',
        'mail_template_id' => 'rac_cart_email_template_id',
        'extra_cart_content' => 'extra_cart_content',
        'mail_template_sending_time' => 'rac_cart_email_template_sending_time',
        'cart_link_clicked_time_log' => 'rac_cart_link_clicked_time_log',
    );

    $cartlist = fp_rac_create_post_array($post_id, $cart_array);
    $cartlist['id'] = $post_id;
    $cartlist['cart_status'] = get_post_status($post_id);

    $cartlist = apply_filters('fp_rac_cartlist_obj', $cartlist, $post_id, $type);

    return $type == 'object' ? (object) $cartlist : $cartlist;
}

function fp_rac_create_email_template_obj($post_id, $type = 'object') {
    $template_array = array(
        'old_id' => 'rac_old_template_id',
        'mail' => 'rac_template_mail',
        'link' => 'rac_template_link',
        'subject' => 'rac_template_subject',
        'from_name' => 'rac_template_from_name',
        'from_email' => 'rac_template_from_email',
        'sender_opt' => 'rac_template_sender_opt',
        'anchor_text' => 'rac_template_anchor_text',
        'segmentation' => 'rac_template_segmentation',
        'coupon_mode' => 'rac_template_coupon_mode',
        'coupon' => 'rac_template_coupon',
        'sending_type' => 'rac_template_sending_type',
        'sending_duration' => 'rac_template_sending_duration',
        'rac_blind_carbon_copy' => 'rac_template_blind_carbon_copy',
    );
    $template = fp_rac_create_post_array($post_id, $template_array);
    $template['id'] = $post_id;
    $template['rac_template_status'] = get_post_status($post_id);
    $template['message'] = get_post_field('post_content', $post_id);

    $template = apply_filters('fp_rac_template_obj', $template, $post_id, $type);

    return $type == 'object' ? (object) $template : $template;
}

function fp_rac_create_post_array($post_id, $array) {
    $post_array = array();
    if (rac_check_is_array($array)) {
        foreach ($array as $key => $value) {
            $post_array[$key] = get_post_meta($post_id, $value, true);
        }
    }
    return $post_array;
}

function fp_rac_get_cart_status_name($post_status) {
    if ($post_status == 'rac-cart-new') {
        $post_name = 'NEW';
    } elseif ($post_status == 'trash') {
        $post_name = 'trash';
    } elseif ($post_status == 'rac-cart-abandon') {
        $post_name = 'ABANDON';
    } else {
        $post_name = 'RECOVERED';
    }
    return $post_name;
}

function fp_rac_get_post_id_from_old_id($key, $value, $post_status = array('racactive'), $post_type = 'racemailtemplate') {
    $args = array(
        'post_type' => $post_type,
        'post_status' => $post_status,
        'meta_query' => array(
            array(
                'key' => $key,
                'value' => $value,
            ),
        ),
        'fields' => 'ids'
    );
    $query = new WP_Query($args);
    if (isset($query->posts)) {
        if (rac_check_is_array($query->posts)) {
            return $query->posts[0];
        }
    }
    return false;
}

function fp_rac_check_query_having_posts($args) {
    $post = array();
    $query_post = new WP_Query($args);
    if (isset($query_post->posts)) {
        if (rac_check_is_array($query_post->posts)) {
            $post = $query_post->posts;
        }
    }
    return $post;
}

function fp_rac_get_template_ids($args = array()) {
    $default_args = array(
        'posts_per_page' => -1,
        'post_type' => 'racemailtemplate',
        'post_status' => array(
            'racactive', 'racinactive'
        ),
        'order' => 'ASC',
        'orderby' => 'ID',
        'fields' => 'ids'
    );
    $new_args = wp_parse_args($args, $default_args);
    $template_ids = fp_rac_check_query_having_posts($new_args);
    return $template_ids;
}

function fp_rac_get_post_status_from_cart_status($cart_status) {
    if ($cart_status == 'NEW') {
        $post_status = 'rac-cart-new';
    } elseif ($cart_status == 'ABANDON') {
        $post_status = 'rac-cart-abandon';
    } elseif ($cart_status == 'RECOVERED') {
        $post_status = 'rac-cart-recovered';
    } else {
        $post_status = 'trash';
    }
    return $post_status;
}
