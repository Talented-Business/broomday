<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class FPRacCoupon {

    public static function rac_create_coupon($email, $timestamp, $template_id, $template_coupon, $template_coupon_mode) {
        $getdatas = self::get_all_coupon_array_for_email($email);
        $create_coupon_by = (get_option('rac_coupon_creation_mode') == 'template' && $template_coupon_mode == 'specific') ? $template_id : 'global';
        $coupon_data = self::prepare_coupon_creation_data($email, $timestamp, $template_coupon, $create_coupon_by);

        if (!rac_check_is_array($getdatas))
            return self::create_new_coupon($coupon_data);

        $coupon_code = self::rac_check_coupon_already_exists_for_email($getdatas, $create_coupon_by);
        if ($coupon_code)
            return $coupon_code;

        return self::create_new_coupon($coupon_data);
    }

    /*
     * Prepare Coupon creation data.
     */

    public static function prepare_coupon_creation_data($email, $timestamp, $template_coupon, $create_coupon_by) {
        if ($create_coupon_by != 'global') {
            $template_coupon['email'] = $email;
            $template_coupon['timestamp'] = $timestamp;
            $template_coupon['create_coupon_by'] = $create_coupon_by;

            return $template_coupon;
        } else {
            return array(
                'email' => $email,
                'timestamp' => $timestamp,
                'create_coupon_by' => $create_coupon_by,
                'prefix_type' => get_option('rac_prefix_coupon'),
                'prefix' => get_option('rac_manual_prefix_coupon_code'),
                'discount_type' => get_option('rac_coupon_type'),
                'value' => get_option('rac_coupon_value'),
                'validity' => get_option('rac_coupon_validity'),
                'min_amount' => get_option('rac_minimum_spend'),
                'max_amount' => get_option('rac_maximum_spend'),
                'indivitual_use_only' => get_option('rac_individual_use_only'),
                'exclude_sale_items' => get_option('rac_exclude_sale_items'),
                'allow_free_shipping' => get_option('rac_coupon_allow_free_shipping'),
                'include_products' => get_option('rac_include_products_in_coupon'),
                'exclude_products' => get_option('rac_exclude_products_in_coupon'),
                'ínclude_categories' => get_option('rac_select_category_to_enable_redeeming'),
                'exclude_categories' => get_option('rac_exclude_category_to_enable_redeeming'),
            );
        }
    }

    public static function rac_check_coupon_already_exists_for_email($getdatas, $create_coupon_by) {
        foreach ($getdatas as $coupon_post) {
            $coupon_object = new WC_Coupon($coupon_post->ID);
            $expired_date = strtotime(fp_rac_get_coupon_obj_data($coupon_object, 'expiry_date'));
            $usage_limit = fp_rac_get_coupon_obj_data($coupon_object, 'usage_limit');
            $usage_count = fp_rac_get_coupon_obj_data($coupon_object, 'usage_count');
            if (($expired_date && current_time('timestamp') <= $expired_date) && ($usage_limit > 0 && $usage_count < $usage_limit)) {
                $coupon_created_by = get_post_meta($coupon_post->ID, 'rac_cartlist_coupon_by', true);
                if ($create_coupon_by == $coupon_created_by || ($coupon_created_by == '' && $create_coupon_by == 'global')) {
                    return get_the_title($coupon_post->ID);
                } else {
                    wp_delete_post($coupon_post->ID, true);
                }
            }
        }

        return false;
    }

    public static function get_all_coupon_array_for_email($email) {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'shop_coupon',
            'post_status' => 'publish',
            'meta_query' => array(
                'rac_cartlist_coupon_email' => array(
                    'key' => 'rac_cartlist_coupon_email',
                    'value' => $email,
                    'compare' => 'EXISTS',
                ),
            ),
        );
        $coupon_array = fp_rac_check_query_having_posts($args);

        return $coupon_array;
    }

    //coupon exist check
    public static function coupon_exist_check($coupon_code) {
        //coupon creation pre check
        $coupon_name = '';
        $args = array(
            'posts_per_page' => -1,
            'orderby' => 'ID',
            'order' => 'asc',
            'post_type' => 'shop_coupon',
            'post_status' => 'publish',
            's' => $coupon_code,
        );
        $coupon_array = fp_rac_check_query_having_posts($args);
        if (rac_check_is_array($coupon_array)) {
            $coupon_info = $coupon_array[0];
            $coupon_name = $coupon_info->post_title;
        }

        return $coupon_name;
    }

    /*
     * Create a New Coupon
     */

    public static function create_new_coupon($coupon_data) {
        if ($coupon_data['prefix_type'] == '1') {
            $afterexplode = explode('@', $coupon_data['email']);
            $email_letters = $afterexplode[0];
            $coupon_code = $email_letters . $coupon_data['timestamp'];
        } else {
            $manual_prefix = $coupon_data['prefix'];
            $coupon_code = $manual_prefix . $coupon_data['timestamp'];
        }

        $coupon_pre_check = self::coupon_exist_check($coupon_code);
        if ($coupon_pre_check == '') {
            $time_now = time();
            $validity_time = $coupon_data['validity'] * 24 * 60 * 60;
            $expire_time = $time_now + $validity_time;
            $expire_date = date_i18n("Y-m-d", $expire_time); //formating expire date
            $include_products = isset($coupon_data['include_products']) ? $coupon_data['include_products'] : array();
            $exclude_products = isset($coupon_data['exclude_products']) ? $coupon_data['exclude_products'] : array();
            $ínclude_categories = isset($coupon_data['ínclude_categories']) ? $coupon_data['ínclude_categories'] : array();
            $exclude_categories = isset($coupon_data['exclude_categories']) ? $coupon_data['exclude_categories'] : array();
            $allowproducts = self::fp_rac_prepare_search_values($include_products);
            $excluded_products = self::fp_rac_prepare_search_values($exclude_products);
            $allowcategory = self::fp_rac_prepare_search_values($ínclude_categories);
            $excludecategory = self::fp_rac_prepare_search_values($exclude_categories);

            $coupon = array(
                'post_title' => $coupon_code,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'shop_coupon'
            );

            $new_coupon_id = wp_insert_post($coupon);

            $coupon_meta_args = array(
                'discount_type' => $coupon_data['discount_type'],
                'coupon_amount' => $coupon_data['value'],
                'individual_use' => isset($coupon_data['indivitual_use_only']) ? $coupon_data['indivitual_use_only'] : 'no',
                'free_shipping' => isset($coupon_data['allow_free_shipping']) ? $coupon_data['allow_free_shipping'] : 'no',
                'product_ids' => implode(',', $allowproducts),
                'exclude_product_ids' => implode(',', $excluded_products),
                'product_categories' => $allowcategory,
                'exclude_product_categories' => $excludecategory,
                'usage_limit' => '1',
                'expiry_date' => $expire_date,
                'apply_before_tax' => 'yes',
                'exclude_sale_items' => isset($coupon_data['exclude_sale_items']) ? $coupon_data['exclude_sale_items'] : 'no',
                'minimum_amount' => $coupon_data['min_amount'],
                'maximum_amount' => $coupon_data['max_amount'],
                'rac_cartlist_coupon_email' => $coupon_data['email'],
                'rac_cartlist_coupon_by' => $coupon_data['create_coupon_by']
            );

            foreach ($coupon_meta_args as $meta_key => $meta_value) {
                update_post_meta($new_coupon_id, $meta_key, $meta_value);
            }

            if (update_post_meta($new_coupon_id, 'coupon_by_rac', 'yes')) {
                return $coupon_code;
            }
        }
    }

    public static function fp_rac_prepare_search_values($array) {
        $prepare_array = fp_rac_check_is_array($array);
        $intval_array = array_map('intval', $prepare_array);
        $filter_array = array_filter($intval_array);
        return $filter_array;
    }

}
