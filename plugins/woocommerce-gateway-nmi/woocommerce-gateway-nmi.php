<?php
/*
Plugin Name: WooCommerce Network Merchants (NMI) Gateway (Enterprise)
Plugin URI: https://pledgedplugins.com/products/nmi-network-merchants-payment-gateway-woocommerce/
Description: A payment gateway for NMI. An NMI account and a server with cURL, SSL support, and a valid SSL certificate is required (for security reasons) for this gateway to function. Requires WC 3.0.0+
Version: 2.1.1
Author: Pledged Plugins
Author URI: https://pledgedplugins.com
Text Domain: wc-nmi
Domain Path: /languages
WC requires at least: 3.0.0
WC tested up to: 3.5

	Copyright: © Pledged Plugins.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main NMI class which sets the gateway up for us
 */
class WC_NMI {

	/**
	 * Constructor
	 */
	public function __construct() {
		define( 'WC_NMI_VERSION', '2.1.1' );
		define( 'WC_NMI_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
		define( 'WC_NMI_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'WC_NMI_MAIN_FILE', __FILE__ );

		// required files
		require_once( 'includes/class-wc-gateway-nmi-logger.php' );
		require_once( 'updates/updates.php' );

		// Actions
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
		add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'capture_payment' ) );
		add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'capture_payment' ) );
		add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'cancel_payment' ) );
		add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'cancel_payment' ) );
	}

	/**
	 * Add relevant links to plugins page
	 * @param  array $links
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=nmi' ) . '">' . __( 'Settings', 'wc-nmi' ) . '</a>',
			'<a href="https://pledgedplugins.com/support/">' . __( 'Support', 'wc-nmi' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Init localisations and files
	 */
	public function init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		if ( is_admin() ) {
			require_once( 'includes/class-wc-nmi-privacy.php' );
		}

		// Includes
		include_once( 'includes/class-wc-gateway-nmi.php' );
		include_once( 'includes/class-wc-gateway-nmi-echeck.php' );

		if ( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' ) ) {
			include_once( 'includes/class-wc-gateway-nmi-addons.php' );
			include_once( 'includes/class-wc-gateway-nmi-addons-echeck.php' );
		}

		$this->load_plugin_textdomain();
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if
	 * the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/wc-nmi/wc-nmi-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/wc-nmi-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wc-nmi' );
		$dir    = trailingslashit( WP_LANG_DIR );

		load_textdomain( 'wc-nmi', $dir . 'wc-nmi/wc-nmi-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc-nmi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register the gateway for use
	 */
	public function register_gateway( $methods ) {
		if ( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' ) ) {
			$methods[] = 'WC_Gateway_NMI_Addons';
			$methods[] = 'WC_Gateway_NMI_Addons_ECheck';
		} else {
			$methods[] = 'WC_Gateway_NMI';
			$methods[] = 'WC_Gateway_NMI_ECheck';
		}
		return $methods;
	}

	/**
	 * Capture payment when the order is changed from on-hold to complete or processing
	 *
	 * @param  int $order_id
	 */
	public function capture_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order->get_payment_method() == 'nmi' ) {
			$charge   = $order->get_meta( '_nmi_charge_id' );
			$captured = $order->get_meta( '_nmi_charge_captured' );

			if ( $charge && $captured == 'no' ) {
				$gateway = new WC_Gateway_NMI();
				$args = array(
					'amount'		=> $order->get_total(),
					'transactionid'	=> $order->get_transaction_id(),
					'type' 			=> 'capture',
					'email' 		=> $order->get_billing_email(),
					'currency'		=> $gateway->get_payment_currency( $order_id ),
				);
				$response = $gateway->nmi_request( $args );

				if ( $response->error || $response->declined ) {
					$order->add_order_note( __( 'Unable to capture charge!', 'wc-nmi' ) . ' ' . $response->error_message );
				} else {
					$order->add_order_note( sprintf( __( 'NMI charge complete (Charge ID: %s)', 'wc-nmi' ), $response->transactionid ) );
					$order->update_meta_data( '_nmi_charge_captured', 'yes' );

					// Store other data such as fees
					$order->update_meta_data( 'NMI Payment ID', $response->transactionid );
					$order->set_transaction_id( $response->transactionid );
					$order->save();
				}
			}
		}
	}

	/**
	 * Cancel pre-auth on refund/cancellation
	 *
	 * @param  int $order_id
	 */
	public function cancel_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order->get_payment_method() == 'nmi' ) {
			$charge = $order->get_meta( '_nmi_charge_id' );

			if ( $charge ) {
				$gateway = new WC_Gateway_NMI();
				$args = array(
					'amount'			=> $order->get_total(),
					'transactionid'		=> $order->get_transaction_id(),
					'type' 				=> 'cancel',
					'email' 			=> $order->get_billing_email(),
					'currency'			=> $gateway->get_payment_currency( $order_id ),
				);
				$response = $gateway->nmi_request( $args );

				if ( $response->error || $response->declined ) {
					$order->add_order_note( __( 'Unable to refund charge!', 'wc-nmi' ) . ' ' . $response->error_message );
				} else {
					$order->add_order_note( sprintf( __( 'NMI charge refunded (Charge ID: %s)', 'wc-nmi' ), $response->transactionid ) );
					$order->delete_meta_data( '_nmi_charge_captured' );
					$order->delete_meta_data( '_nmi_charge_id' );
					$order->save();
				}
			}
		}
	}

}
new WC_NMI();