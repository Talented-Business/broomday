<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Segmentation')) {

    /**
     * FP_Segmentation Class.
     */
    class FP_Segmentation {

        /**
         * check if corresponding product is there in selected products.
         */
        public static function check_selected_products_there($product_id, $selected_products) {

            if (is_array($selected_products)) {
                $selected_products = $selected_products;
            } else {
                $selected_products = explode(',', $selected_products);
            }

            $whole_product = fp_rac_get_product($product_id);
            if (is_object($whole_product)) {
                if (fp_rac_get_product_obj_data($whole_product, 'product_type') === 'simple') {
                    if (in_array($product_id, $selected_products)) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (fp_rac_get_product_obj_data($whole_product, 'product_type') === 'variation') {
                    $variation_id = $product_id;
                    $productid = fp_rac_get_product_obj_data($whole_product, 'id');
                    if (in_array($variation_id, $selected_products)) {
                        return true;
                    } else if ((in_array($productid, $selected_products))) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }

        /**
         * check if corresponding category is there in selected category.
         */
        public static function check_selected_category_there($product_id, $selected_category) {
            if (is_array($selected_category)) {
                $selected_category = $selected_category;
            } else {
                $selected_category = explode(',', $selected_category);
            }

            $whole_product = fp_rac_get_product($product_id);
            if (is_object($whole_product)) {
                $terms = get_the_terms($product_id, 'product_cat');
                if (rac_check_is_array($terms)) {
                    foreach ($terms as $key => $term) {
                        if (is_object($term)) {
                            if (in_array($term->term_id, $selected_category)) {
                                return true;
                            }
                        }
                    }
                }
                return false;
            }
        }

        /**
         * check if corresponding user role is there in selected user roles.
         */
        public static function check_user_roles($user_id, $selected_user_roles) {
            if ($user_id != '0') {
                $role = implode(get_userdata($user_id)->roles);
            } else {
                if ($user_id == '0') {
                    $role = 'rac_guest';
                }
            }
            if (in_array($role, $selected_user_roles)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Get No of Orders Placed By User.
         */
        public static function get_no_of_orders_placed($user_id, $email_id) {
            global $wpdb;
            if ($user_id != 0) {
                $count = $wpdb->get_var("SELECT COUNT(*)
                 FROM $wpdb->posts as posts

                 LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

                 WHERE   meta.meta_key       = '_customer_user'
                 AND     posts.post_type     IN ('" . implode("','", self::get_order_type_dependencies('order-count')) . "')
                 AND     posts.post_status IN ( '" . implode("','", self::get_post_status_dependencies()) . " ')
                 AND     meta_value          = $user_id
             ");
            } else {
                $count = $wpdb->get_var("SELECT COUNT(*)
                 FROM $wpdb->posts as posts
                 LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
                 LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id    
                 WHERE   posts.post_type     IN ('" . implode("','", self::get_order_type_dependencies('order-count')) . "')
                 AND     posts.post_status IN ( '" . implode("','", self::get_post_status_dependencies()) . " ' )
                 AND     meta.meta_value   = $user_id
                 AND     meta.meta_key= '_customer_user'
                 AND     meta2.meta_key= '_billing_email'
                 AND     meta2.meta_value   = '$email_id'
             ");
            }
            return (float) $count;
        }

        /**
         * Get overall Amount Placed By User.
         */
        public static function get_amount_spent_by_user($user_id, $email_id) {
            global $wpdb;
            if ($user_id != 0) {
                $spent = $spent = $wpdb->get_var("SELECT SUM(meta2.meta_value)
			FROM $wpdb->posts as posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id

			WHERE   meta.meta_key       = '_customer_user'
			AND     meta.meta_value     = $user_id
			AND     posts.post_type     IN ('" . implode("','", self::get_order_type_dependencies('reports')) . "')
			AND     posts.post_status   IN ( '" . implode("','", self::get_post_status_dependencies()) . " ' )
			AND     meta2.meta_key      = '_order_total'
		");
            } else {
                $spent = $wpdb->get_var("SELECT SUM(meta3.meta_value)
			FROM $wpdb->posts as posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id
                        LEFT JOIN {$wpdb->postmeta} AS meta3 ON posts.ID = meta3.post_id    

			WHERE   meta.meta_key       = '_customer_user'
			AND     meta.meta_value     = $user_id
                        AND     meta2.meta_key      = '_billing_email'
                        AND     meta2.meta_value     = '$email_id'    
			AND     posts.post_type     IN ('" . implode("','", self::get_order_type_dependencies('reports')) . "')
			AND     posts.post_status   IN ( '" . implode("','", self::get_post_status_dependencies()) . " ')
			AND     meta3.meta_key      = '_order_total'
		");
            }
            return (float) $spent;
        }

        /**
         * Check minimum and maximum value
         */
        public static function check_status_of_min_max($total, $minimum, $maximum) {
            if ($minimum == '*' && $maximum != '*') {
                if ($total <= $maximum) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($minimum != '*' && $maximum == '*') {
                if ($total >= $minimum) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($total <= $maximum && $total >= $minimum) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        /**
         * Check minimum and maximum date
         */
        public static function check_status_of_from_to_date($time, $from, $to) {
            if ($from == '' && $to == '') {
                return true;
            } else if ($from != '' && $to == '') {
                if ($time > $from) {
                    return true;
                } else {
                    return false;
                }
            } else if ($from == '' && $to != '') {
                if ($time < $to) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if (($time > $from) && ($time < $to)) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        /**
         * Get Order Status
         */
        public static function get_post_status_dependencies() {
            if (function_exists('wc_get_order_statuses')) {
                $getpoststatus = array('wc-completed', 'wc-processing');
            } else {
                $getpoststatus = array('publish');
            }
            return $getpoststatus;
        }

        /**
         * Get Order Type
         */
        public static function get_order_type_dependencies($parameter) {
            if (function_exists('wc_get_order_types')) {
                $getorderstatus = wc_get_order_types($parameter);
            } else {
                $getorderstatus = array('shop_order');
            }
            return $getorderstatus;
        }

    }

}