<?php

/*
 * Common functions for Woocommerce compatibility
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!function_exists('fp_rac_product_addons_compatibility')) {

    function fp_rac_product_addons_compatibility($product, $curreny_code) {
        //product name
        $product_name = get_the_title($product['product_id']);
        //product variation
        if (get_option('rac_email_product_variation_sh') != 'no') {
            if (isset($product['variation_id']) && (!empty($product['variation_id']))) {
                $product_id = $product['product_id'];
                $product_obj = fp_rac_get_product($product['variation_id']);
                $formatted_variation = '';
                $variations = explode(',', wc_get_formatted_variation($product_obj, true));
                $count = 0;
                if (rac_check_is_array($variations)) {
                    foreach ($variations as $key => $each_attribute) {
                        $explode_data = explode(':', $each_attribute);
                        if ($count == 0) {
                            $formatted_variation = ' -' . $explode_data[1];
                            $count++;
                        } else {
                            $formatted_variation .= ',' . $explode_data[1];
                        }
                    }
                }
                $product_name .= $formatted_variation;
            }
        }
        //product sku
        $product_name = FP_RAC_Polish_Product_Info::fp_rac_format_product_name_by_sku($product_name, $product);

        //product Addons
        if (get_option('rac_email_product_variation_sh') != 'no') {
            $addon_name = '';
            if (!empty($product['addons'])) {
                if (rac_check_is_array($product['addons'])) {
                    foreach ($product['addons'] as $addon) {
                        $name = $addon['name'];
                        if ($addon['price'] > 0 && apply_filters('woocommerce_addons_add_price_to_name', '__return_true')) {
                            $name .= ' (' . fp_rac_format_price($addon['price'], $curreny_code) . ')';
                        }
                        $addon_name .= '<br /><br />' . $name . ': ' . $addon['value'];
                    }
                }
            }
            $product_name .= $addon_name;
        }

        return $product_name;
    }

}