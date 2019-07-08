<?php

/**
 * Admin Custom Post Status.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Register_Post_Status')) {

    /**
     * FP_RAC_Register_Post_Status Class.
     */
    class FP_RAC_Register_Post_Status {

        /**
         * FP_RAC_Register_Post_Status Class initialization.
         */
        public static function init() {
            add_action('init', array(__CLASS__, 'fp_rac_register_custom_post_status'));
        }

        public static function fp_rac_register_custom_post_status() {
            $array = array(
                'racactive' => array('fp_rac_register_post_status', 'fp_rac_emailtemplate_active_post_status_args'),
                'racinactive' => array('fp_rac_register_post_status', 'fp_rac_emailtemplate_deactive_post_status_args'),
                'rac-cart-new' => array('fp_rac_register_post_status', 'fp_rac_cartlist_new_post_type_args'),
                'rac-cart-abandon' => array('fp_rac_register_post_status', 'fp_rac_cartlist_abandon_post_type_args'),
                'rac-cart-recovered' => array('fp_rac_register_post_status', 'fp_rac_cartlist_recovered_post_type_args'),
            );
            $array = apply_filters('fp_rac_add_custom_post_status', $array);
            if (rac_check_is_array($array)) {
                foreach ($array as $post_name => $args_function) {
                    $args = call_user_func_array($args_function, array());
                    register_post_status($post_name, $args);
                }
            }
        }

        public static function fp_rac_cartlist_new_post_type_args() {
            $args = apply_filters('fp_rac_cartlist_subcribe_args', array(
                'label' => _x('New', 'sumoaffiliates'),
                'public' => true,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop('New <span class="count">(%s)</span>', 'New <span class="count">(%s)</span>'),
                    )
            );
            return $args;
        }

        public static function fp_rac_cartlist_abandon_post_type_args() {
            $args = apply_filters('fp_rac_cartlist_unsubcribe_args', array(
                'label' => _x('Abandon', 'sumoaffiliates'),
                'public' => true,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop('Abandon <span class="count">(%s)</span>', 'Abandon <span class="count">(%s)</span>'),
                    )
            );
            return $args;
        }

        public static function fp_rac_emailtemplate_active_post_status_args() {
            $args = apply_filters('fp_rac_emailtemplate_active_args', array(
                'label' => _x('Active', 'sumoaffiliates'),
                'public' => true,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop('Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>'),
                    )
            );
            return $args;
        }

        public static function fp_rac_emailtemplate_deactive_post_status_args() {
            $args = apply_filters('fp_rac_emailtemplate_deactive_args', array(
                'label' => _x('Deactive', 'sumoaffiliates'),
                'public' => true,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop('Deactive <span class="count">(%s)</span>', 'Deactive <span class="count">(%s)</span>'),
                    )
            );
            return $args;
        }

        public static function fp_rac_cartlist_recovered_post_type_args() {
            $args = apply_filters('fp_rac_emailtemplate_deactive_args', array(
                'label' => _x('Recovered', 'sumoaffiliates'),
                'public' => true,
                'exclude_from_search' => true,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop('Recovered <span class="count">(%s)</span>', 'Recovered <span class="count">(%s)</span>'),
                    )
            );
            return $args;
        }

    }

    FP_RAC_Register_Post_Status::init();
}