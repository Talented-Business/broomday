<?php

/**
 * Admin Custom Post Type.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Register_Post_Type')) {

    /**
     * FP_RAC_Register_Post_Type Class.
     */
    class FP_RAC_Register_Post_Type {

        /**
         * FP_RAC_Register_Post_Type Class initialization.
         */
        public static function init() {
            add_action('init', array(__CLASS__, 'fp_rac_register_post_types'));
        }

        public static function fp_rac_register_post_types() {
            $array = array(
                'raccartlist' => array('FP_RAC_Register_Post_Type', 'fp_rac_cartlist_post_type_args'),
                'racmaillog' => array('FP_RAC_Register_Post_Type', 'fp_rac_maillog_post_type_args'),
                'racemailtemplate' => array('FP_RAC_Register_Post_Type', 'fp_rac_emailtemplate_post_type_args'),
                'racrecoveredorder' => array('FP_RAC_Register_Post_Type', 'fp_rac_racrecoveredorder_post_type_args'),
            );
            $array = apply_filters('fp_rac_add_custom_post_type', $array);

            if (rac_check_is_array($array)) {
                foreach ($array as $post_name => $args_function) {
                    $args = call_user_func_array($args_function, array());
                    register_post_type($post_name, $args);
                }
            }
        }

        public static function fp_rac_cartlist_post_type_args() {
            $args = apply_filters('fp_rac_cartlist_post_type_args', array(
                'labels' => array(
                    'name' => __('Cart List Table', 'recoverabandoncart'),
                    'singular_name' => __('Recovered Abandoned Cart', 'recoverabandoncart'),
                    'menu_name' => _x('Recover Abandoned Cart', 'admin menu', 'recoverabandoncart'),
                    'add_new' => __('Add New Cart List', 'recoverabandoncart'),
                    'add_new_item' => __('Add New Affiliate', 'recoverabandoncart'),
                    'edit' => __('Edit Cart List', 'recoverabandoncart'),
                    'edit_item' => __('Edit Cart List', 'recoverabandoncart'),
                    'all_items' => __('Cart List Table', 'recoverabandoncart'),
                    'new_item' => __('New Cart List', 'recoverabandoncart'),
                    'view' => __('View Cart List', 'recoverabandoncart'),
                    'view_item' => __('View Cart List', 'recoverabandoncart'),
                    'search_items' => __('Search Cart List', 'recoverabandoncart'),
                    'not_found' => __('No Cart List found', 'recoverabandoncart'),
                    'not_found_in_trash' => __('No Cart List found in trash', 'recoverabandoncart'),
                ),
                'description' => __('Here you can able to see list of Cart List', 'recoverabandoncart'),
                'public' => false,
                'show_ui' => true,
//                'menu_icon' => RAC_PLUGIN_URL . '/assets/images/dash-icon.png',
                'capability_type' => 'post',
                'show_in_menu' => true,
                'publicly_queryable' => false,
                'exclude_from_search' => true,
                'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                'show_in_nav_menus' => true,
                'capabilities' => array(
                    'publish_posts' => 'publish_posts',
                    'edit_posts' => 'edit_posts',
                    'edit_others_posts' => 'edit_others_posts',
                    'delete_posts' => 'delete_posts',
                    'delete_others_posts' => 'delete_others_posts',
                    'read_private_posts' => 'read_private_posts',
                    'edit_post' => 'edit_post',
                    'delete_post' => 'delete_post',
                    'read_post' => 'read_post',
                    'create_posts' => 'do_not_allow',
                ),
                'map_meta_cap' => true,
                    )
            );
            return $args;
        }

        public static function fp_rac_maillog_post_type_args() {
            $args = apply_filters('fp_rac_maillog_post_type_args', array(
                'labels' => array(
                    'name' => __('Email Log', 'recoverabandoncart'),
                    'singular_name' => __('Email Log', 'recoverabandoncart'),
                    'menu_name' => _x('Email Log', 'admin menu', 'recoverabandoncart'),
                    'add_new' => __('Email Log', 'recoverabandoncart'),
                    'add_new_item' => __('Add Email Log', 'recoverabandoncart'),
                    'edit' => __('Edit Email Log', 'recoverabandoncart'),
                    'edit_item' => __('Edit Email Log', 'recoverabandoncart'),
                    'new_item' => __('New Email Log', 'recoverabandoncart'),
                    'view' => __('View Email log', 'recoverabandoncart'),
                    'view_item' => __('View Email Log', 'recoverabandoncart'),
                    'search_items' => __('Search Email log', 'recoverabandoncart'),
                    'not_found' => __('No Email Log found', 'recoverabandoncart'),
                    'not_found_in_trash' => __('No Email Log found in trash', 'recoverabandoncart'),
                ),
                'description' => __('Here you can able to see list of Email Log', 'recoverabandoncart'),
                'public' => false,
                'show_ui' => true,
                'capability_type' => 'post',
                'show_in_menu' => 'edit.php?post_type=raccartlist',
                'publicly_queryable' => false,
                'exclude_from_search' => false,
                'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                'show_in_nav_menus' => true,
                'capabilities' => array(
                    'publish_posts' => 'publish_posts',
                    'edit_posts' => 'edit_posts',
                    'edit_others_posts' => 'edit_others_posts',
                    'delete_posts' => 'delete_posts',
                    'delete_others_posts' => 'delete_others_posts',
                    'read_private_posts' => 'read_private_posts',
                    'edit_post' => 'edit_post',
                    'delete_post' => 'delete_post',
                    'read_post' => 'read_post',
                    'create_posts' => 'do_not_allow',
                ),
                'map_meta_cap' => true,
                    )
            );
            return $args;
        }

        public static function fp_rac_emailtemplate_post_type_args() {
            $args = apply_filters('fp_rac_emailtemplate_post_type_args', array(
                'labels' => array(
                    'name' => __('Email Templates', 'recoverabandoncart'),
                    'singular_name' => __('Email Template', 'recoverabandoncart'),
                    'menu_name' => _x('Email Templates', 'admin menu', 'recoverabandoncart'),
                    'add_new' => __('New Email Template', 'recoverabandoncart'),
                    'add_new_item' => __('New Email Template', 'recoverabandoncart'),
                    'edit' => __('Edit Email Template', 'recoverabandoncart'),
                    'edit_item' => __('Edit Email Template', 'recoverabandoncart'),
                    'new_item' => __('New Email Template', 'recoverabandoncart'),
                    'view' => __('View Email Template', 'recoverabandoncart'),
                    'view_item' => __('View Email Template', 'recoverabandoncart'),
                    'search_items' => __('Search Email Template', 'recoverabandoncart'),
                    'not_found' => __('No Email Template(s) found', 'recoverabandoncart'),
                    'not_found_in_trash' => __('No Email Template(s) found in trash', 'recoverabandoncart'),
                ),
                'description' => __('Here you can able to see list of Email Templates', 'recoverabandoncart'),
                'public' => false,
                'show_ui' => true,
                'capability_type' => 'post',
                'show_in_menu' => 'edit.php?post_type=raccartlist',
                'publicly_queryable' => false,
                'exclude_from_search' => true,
                'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                'show_in_nav_menus' => true,
                'capabilities' => array(
                    'publish_posts' => 'publish_posts',
                    'edit_posts' => 'edit_posts',
                    'edit_others_posts' => 'edit_others_posts',
                    'delete_posts' => 'delete_posts',
                    'delete_others_posts' => 'delete_others_posts',
                    'read_private_posts' => 'read_private_posts',
                    'edit_post' => 'edit_post',
                    'delete_post' => 'delete_post',
                    'read_post' => 'read_post',
                ),
                'map_meta_cap' => true,
                    )
            );
            return $args;
        }

        public static function fp_rac_racrecoveredorder_post_type_args() {
            $args = apply_filters('fp_rac_racrecoveredorder_post_type_args', array(
                'labels' => array(
                    'name' => __('Recovered Orders  Log', 'recoverabandoncart'),
                    'singular_name' => __('Recovered Orders', 'recoverabandoncart'),
                    'menu_name' => _x('Recovered Orders', 'admin menu', 'recoverabandoncart'),
                    'add_new' => __('Recovered Order', 'recoverabandoncart'),
                    'add_new_item' => __('Add New Recovered Order', 'recoverabandoncart'),
                    'edit' => __('Edit Recovered Order', 'recoverabandoncart'),
                    'edit_item' => __('Edit Recovered Order', 'recoverabandoncart'),
                    'new_item' => __('New Recovered Order', 'recoverabandoncart'),
                    'view' => __('View Recovered Order', 'recoverabandoncart'),
                    'view_item' => __('View Recovered Order', 'recoverabandoncart'),
                    'search_items' => __('Search Recovered Order', 'recoverabandoncart'),
                    'not_found' => __('No Recovered Orders found', 'recoverabandoncart'),
                    'not_found_in_trash' => __('No Recovered Orders found in trash', 'recoverabandoncart'),
                ),
                'description' => __('Here you can able to see list of Recovered Order', 'recoverabandoncart'),
                'public' => false,
                'show_ui' => true,
                'capability_type' => 'post',
                'show_in_menu' => 'edit.php?post_type=raccartlist',
                'publicly_queryable' => false,
                'exclude_from_search' => false,
                'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                'show_in_nav_menus' => true,
                'capabilities' => array(
                    'publish_posts' => 'publish_posts',
                    'edit_posts' => 'edit_posts',
                    'edit_others_posts' => 'edit_others_posts',
                    'delete_posts' => 'delete_posts',
                    'delete_others_posts' => 'delete_others_posts',
                    'read_private_posts' => 'read_private_posts',
                    'edit_post' => 'edit_post',
                    'delete_post' => 'delete_post',
                    'read_post' => 'read_post',
                    'create_posts' => 'do_not_allow',
                ),
                'map_meta_cap' => true,
                    )
            );
            return $args;
        }

    }

    FP_RAC_Register_Post_Type::init();
}