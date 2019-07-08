<?php

/**
 * Coupon Handler
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Coupon_Handler')) {

    /**
     * FP_RAC_Coupon_Handler Class.
     */
    class FP_RAC_Coupon_Handler {

        /**
         * FP_RAC_Coupon_Handler Class initialization.
         */
        public static function init() {

            add_filter('views_edit-shop_coupon', array(__CLASS__, 'admin_views_edit_coupon'));
            add_filter('request', array(__CLASS__, 'admin_request_query'));
            add_action('manage_posts_extra_tablenav', array(__CLASS__, 'hide_search_box'));
            add_filter('disable_months_dropdown', array(__CLASS__, 'disable_filters'), 10, 2);
        }

        /*
         * Disable Post Table filters
         */

        public static function disable_filters($bool, $post_type) {

            if ($post_type != 'shop_coupon')
                return $bool;

            if (isset($_GET['rac_other_coupons']) || isset($_GET['rac_coupons'])) {
                remove_all_actions('restrict_manage_posts');

                return false;
            }


            return $bool;
        }

        /*
         *  Hide Search Box
         */

        public static function hide_search_box() {
            if (isset($_GET['rac_other_coupons']) || isset($_GET['rac_coupons'])) {
                ?>
                <script type="text/javascript">
                    jQuery('p.search-box').hide();
                </script>
                <?php

            }
        }

        /*
         * Add Custom a views
         */

        public static function admin_views_edit_coupon($views) {
            $get_rac_coupon_count = self::get_coupon_count();

            if ($get_rac_coupon_count < 0)
                return $views;

            global $post_type, $wp_query;

            $other_coupon_class = '';
            $rac_coupon_class = '';
            $get_other_coupon_count = self::get_coupon_count(false);
            if (isset($wp_query->query['meta_key']) && $wp_query->query['meta_key'] == 'coupon_by_rac') {
                $other_coupon_class = isset($_GET['rac_other_coupons']) ? 'current' : '';
                $rac_coupon_class = isset($_GET['rac_coupons']) ? 'current' : '';
            }

            $query_string = admin_url('edit.php?post_type=shop_coupon');
            $rac_coupon_query_string = add_query_arg('rac_coupons', 'yes', $query_string);
            $other_coupon_query_string = add_query_arg('rac_other_coupons', 'yes', $query_string);

            $views['rac_other_coupons'] = '<a href="' . esc_url($other_coupon_query_string) . '" class="' . esc_attr($other_coupon_class) . '">' . __('Other Coupons', 'recoverabandoncart') . ' (' . $get_other_coupon_count . ')</a>';
            $views['rac_coupons'] = '<a href="' . esc_url($rac_coupon_query_string) . '" class="' . esc_attr($rac_coupon_class) . '">' . __('Recover Abandoned Cart Coupons', 'recoverabandoncart') . ' (' . $get_rac_coupon_count . ')</a>';

            return $views;
        }

        /**
         * Filters and sorting handler
         */
        public static function admin_request_query($vars) {
            global $typenow, $wp_query, $wp_post_statuses;

            if ('shop_coupon' === $typenow) {
                if (isset($_GET['rac_other_coupons'])) {
                    $vars['meta_key'] = 'coupon_by_rac';
                    $vars['meta_compare'] = 'NOT EXISTS';
                } elseif (isset($_GET['rac_coupons'])) {
                    $vars['meta_key'] = 'coupon_by_rac';
                    $vars['meta_value'] = 'yes';
                }
            }

            return $vars;
        }

        /**
         *  Get Coupon Count
         */
        public static function get_coupon_count($bool = true) {
            $compare = ($bool) ? '=' : 'NOT EXISTS';
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'shop_coupon',
                'post_status' => 'publish',
                'meta_key' => 'coupon_by_rac',
                'meta_value' => 'yes',
                'meta_compare' => $compare,
                'fields' => 'ids'
            );

            $coupon_count = array_filter(fp_rac_check_query_having_posts($args));

            return count($coupon_count);
        }

    }

    FP_RAC_Coupon_Handler::init();
}