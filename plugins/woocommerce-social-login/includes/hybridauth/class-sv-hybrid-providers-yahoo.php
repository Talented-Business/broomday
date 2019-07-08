<?php
/**
 * WooCommerce Social Login
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @package   WC-Social-Login/Providers/HybridAuth
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

// load base class
if ( ! class_exists( 'Hybrid_Providers_Yahoo' ) ) {
	require_once( Hybrid_Auth::$config['path_providers'] . 'Yahoo.php' );
}


/**
 * Yahoo provider for HybridAuth tailored for WP
 *
 * @since 2.0.0
 */
class SV_Hybrid_Providers_Yahoo extends Hybrid_Providers_Yahoo {


	/**
	 * Initialize the provider.
	 *
	 * This method is largely the same as in the original provider with the
	 * exception of using the WP_OAuth2_Client instead of the built-in OAuth2Client class
	 *
	 * @since 2.0.0
	 */
	public function initialize() {

		if ( ! $this->config['keys']['key'] || ! $this->config['keys']['secret'] ) {
			throw new Exception( "Your application key and secret are required in order to connect to {$this->providerId}.", 4 );
		}

		// override requested scope
		if ( isset( $this->config['scope'] ) && ! empty( $this->config['scope'] ) ) {
			$this->scope = $this->config['scope'];
		}

		// include OAuth1 client
		if ( ! class_exists( 'OAuthConsumer' ) ) {
			require_once( Hybrid_Auth::$config['path_libraries'] . 'OAuth/OAuth.php' );
		}

		require_once( Hybrid_Auth::$config['path_libraries'] . 'OAuth/OAuth1Client.php' );
		require_once( wc_social_login()->get_plugin_path() . '/includes/hybridauth/class-wp-oauth1-client.php' );

		// setup access_token if any stored
		if ( $this->token( 'access_token' ) ) {
			$this->api = new WP_OAuth1_Client( $this->config['keys']['key'], $this->config['keys']['secret'], $this->token( 'access_token' ), $this->token( 'access_token_secret' ) );
		}

		// setup request_token if any stored, in order to exchange with an access token
		elseif ( $this->token( 'request_token' ) ) {
			$this->api = new WP_OAuth1_Client( $this->config['keys']['key'], $this->config['keys']['secret'], $this->token( 'request_token' ), $this->token( 'request_token_secret' ) );
		}

		// instanciate OAuth client with client credentials
		else {
			$this->api = new WP_OAuth1_Client( $this->config['keys']['key'], $this->config['keys']['secret'] );
		}

		$this->api->api_base_url      = 'https://social.yahooapis.com/v1/';
		$this->api->authorize_url     = 'https://api.login.yahoo.com/oauth/v2/request_auth';
		$this->api->request_token_url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
		$this->api->access_token_url  = 'https://api.login.yahoo.com/oauth/v2/get_token';
	}


}
