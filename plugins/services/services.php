<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://m-preview.com/booking/
 * @since             1.0.0
 * @package           Services
 *
 * @wordpress-plugin
 * Plugin Name:       services post
 * Plugin URI:        https://m-preview.com/booking/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Swati
 * Author URI:        https://m-preview.com/booking/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       services
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-services-activator.php
 */
function activate_services() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-services-activator.php';
	Services_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-services-deactivator.php
 */
function deactivate_services() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-services-deactivator.php';
	Services_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_services' );
register_deactivation_hook( __FILE__, 'deactivate_services' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-services.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_services() {

	$plugin = new Services();
	$plugin->run();

}

add_action('init', 'extra_services_register');
 
function extra_services_register() {
 
	$labels = array(
		'name' =>_x('Extra Services', 'Services'),
		'singular_name' =>_x('Extra Services Item', 'Service'),
		'add_new' => _x('Add New Extra Service', 'service item'),
		'add_new_item' => __('Add New Extra Service Item'),
		'edit_item' => __('Edit Extra Services Item'),
		'new_item' => __('New  Extra Service Item'),
		'view_item' => __('View Extra Service Item'),
		'search_items' => __('Search Extra Service'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => '',
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail','post-formats','page-attributes','custom-fields')
	  ); 
 
	register_post_type( 'extra_services' , $args );
}
add_action( 'init', 'create_services_taxonomies', 0 );

// create two taxonomies, genres and writers for the post type "book"
function create_services_taxonomies() {
	$labels = array(
		'name'              => _x( 'Extra Services Categories', 'servicese' ),
		'singular_name'     => _x( 'Extra Services Category', 'services' ),
		'search_items'      => __( 'Search Extra Services', 'textdomain' ),
		'all_items'         => __( 'All Extra Services Categories', 'textdomain' ),
		'parent_item'       => __( 'Parent Extra Services', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Extra Services:', 'textdomain' ),
		'edit_item'         => __( 'Edit Extra Services', 'textdomain' ),
		'update_item'       => __( 'Update Extra Services', 'textdomain' ),
		'add_new_item'      => __( 'Add New Extra Services', 'textdomain' ),
		'new_item_name'     => __( 'New Extra Services Name', 'textdomain' ),
		'menu_name'         => __( 'Extra Services Categories', 'textdomain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => true,
	);

	register_taxonomy( 'extra_services_category', array( 'extra_services' ), $args );
}
run_services();
