<?php
/**
 * Contains functions for Ajax Queries
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      1.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

add_action( 'wp_ajax_fusion_cache_map', 'fusion_cache_map' );
add_action( 'wp_ajax_nopriv_fusion_cache_map', 'fusion_cache_map' );

/**
 * Caches google maps.
 */
function fusion_cache_map() {

	check_ajax_referer( 'avada_admin_ajax', 'security' );

	// Check that the user has the right permissions.
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$addresses_to_cache = get_option( 'fusion_map_addresses' );

	// @codingStandardsIgnoreLine
	$post_addresses = isset( $_POST['addresses'] ) ? wp_unslash( $_POST['addresses'] ) : array();
	foreach ( $post_addresses as $address ) {

		if ( isset( $address['latitude'] ) && isset( $address['longitude'] ) ) {
			$addresses_to_cache[ trim( $address['address'] ) ] = array(
				'address'   => trim( $address['address'] ),
				'latitude'  => esc_attr( $address['latitude'] ),
				'longitude' => esc_attr( $address['longitude'] ),
			);

			if ( isset( $address['geocoded_address'] ) && $address['geocoded_address'] ) {
				$addresses_to_cache[ trim( $address['address'] ) ]['address'] = $address['geocoded_address'];
			}
		}
	}
	update_option( 'fusion_map_addresses', $addresses_to_cache );

	wp_die();

}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
