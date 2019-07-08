<?php
/*
* Plugin Name: WooCommerce Subscribe All the Things
* Plugin URI: https://github.com/Prospress/woocommerce-subscribe-to-all-the-things
* Description: Mini-extension for WooCommerce Subscriptions that allows you to add subscription options to non-subscription product types.
* Version: 2.1.2-dev
* Author: Prospress Inc.
* Author URI: http://prospress.com/
*
* Text Domain: woocommerce-subscribe-all-the-things
* Domain Path: /languages/
*
* Requires at least: 4.4
* Tested up to: 4.9
*
* WC requires at least: 3.0
* WC tested up to: 3.4
*
* Copyright: © 2009-2018 Prospress, Inc.
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WCS_ATT' ) ) :

// Abstract modules container class.
require_once( 'includes/modules/abstract/class-wcs-att-abstract-module.php' );

class WCS_ATT extends WCS_ATT_Abstract_Module {

	/* Plugin version. */
	const VERSION = '2.1.2-dev';

	/* Required WC version. */
	const REQ_WC_VERSION = '3.0.0';

	/* Required WC version. */
	const REQ_WCS_VERSION = '2.1.0';

	/* Text domain. */
	const TEXT_DOMAIN = 'woocommerce-subscribe-all-the-things';

	/**
	 * @var WCS_ATT - the single instance of the class.
	 *
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WCS_ATT Instance.
	 *
	 * Ensures only one instance of WCS_ATT is loaded or can be loaded.
	 *
	 * @static
	 * @see WCS_ATT()
	 * @return WCS_ATT - Main instance
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-subscribe-all-the-things' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-subscribe-all-the-things' ), '1.0.0' );
	}

	/**
	 * Do some work.
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'init', array( $this, 'init_textdomain' ) );
		add_action( 'admin_init', array( $this, 'activate' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta_links' ), 10, 4 );
	}

	/**
	 * The plugin URL.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
	}

	/**
	 * The plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Bootstrap.
	 */
	public function plugins_loaded() {

		global $woocommerce;

		// Subs 2.1+ check.
		if ( ! class_exists( 'WC_Subscriptions' ) || version_compare( WC_Subscriptions::$version, self::REQ_WCS_VERSION ) < 0 ) {
			add_action( 'admin_notices', array( $this, 'wcs_admin_notice' ) );
			return false;
		}

		// WC 3.0+ check.
		if ( version_compare( $woocommerce->version, self::REQ_WC_VERSION ) < 0 ) {
			add_action( 'admin_notices', array( $this, 'wc_admin_notice' ) );
			return false;
		}

		$this->includes();
	}

	/**
	 * Load plugin files.
	 *
	 * @return void
	 */
	public function includes() {

		// Classes.
		require_once( 'includes/class-wcs-att-core-compatibility.php' );
		require_once( 'includes/class-wcs-att-integrations.php' );
		require_once( 'includes/class-wcs-att-scheme.php' );
		require_once( 'includes/class-wcs-att-product.php' );
		require_once( 'includes/class-wcs-att-cart.php' );
		require_once( 'includes/class-wcs-att-order.php' );
		require_once( 'includes/class-wcs-att-sync.php' );

		// Modules.
		$this->register_modules();
		$this->initialize_modules();

		// Load display components.
		require_once( 'includes/class-wcs-att-display.php' );
		$this->register_component_hooks( 'display' );

		// Load form handling components.
		$this->register_component_hooks( 'form' );

		// Legacy stuff.
		require_once( 'includes/legacy/class-wcs-att-schemes.php' );

		// Admin includes.
		if ( is_admin() ) {
			$this->admin_includes();
		}
	}

	/**
	 * Include submodules.
	 *
	 * @since  2.1.0
	 *
	 * @return void
	 */
	protected function register_modules() {

		require_once( 'includes/modules/class-wcs-att-management.php' );

		$this->modules = apply_filters( 'wcsatt_modules', array(
			'WCS_ATT_Management'
		) );
	}

	/**
	 * Register all module hooks associated with a named SATT component.
	 *
	 * @since  2.1.0
	 *
	 * @param  string  $component
	 */
	protected function register_component_hooks( $component ) {

		foreach ( $this->modules as $module ) {
			$module->register_hooks( $component );
		}
	}

	/**
	 * Loads the Admin & AJAX filters / hooks.
	 *
	 * @return void
	 */
	public function admin_includes() {

		require_once( 'includes/admin/class-wcs-att-admin.php' );
	}

	/**
	 * Display a warning message if Subs version check fails.
	 *
	 * @return void
	 */
	public function wc_admin_notice() {

	    echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Subscribe All the Things requires at least WooCommerce %s in order to function. Please upgrade WooCommerce.', 'woocommerce-subscribe-all-the-things' ), self::REQ_WC_VERSION ) . '</p></div>';
	}

	/**
	 * Display a warning message if WC version check fails.
	 *
	 * @return void
	 */
	public function wcs_admin_notice() {

	    echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Subscribe All the Things requires WooCommerce Subscriptions version %s+.', 'woocommerce-subscribe-all-the-things' ), self::REQ_WCS_VERSION ) . '</p></div>';
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function init_textdomain() {

		load_plugin_textdomain( 'woocommerce-subscribe-all-the-things', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Store plugin version.
	 *
	 * @return void
	 */
	public function activate() {

		global $wpdb;

		$version = get_option( 'wcsatt_version', false );

		if ( $version === false ) {
			add_option( 'wcsatt_version', self::VERSION );
		} elseif ( version_compare( $version, self::VERSION, '<' ) ) {
			update_option( 'wcsatt_version', self::VERSION );
		}
	}

	/**
	 * Product types supported by the plugin.
	 *
	 * @return array
	 */
	public function get_supported_product_types() {

		return apply_filters( 'wcsatt_supported_product_types', array( 'simple', 'variable', 'variation', 'mix-and-match', 'bundle', 'composite' ) );
	}

	/**
	 * Log important stuff.
	 *
	 * @param  string  $message
	 * @param  string  $level
	 * @return void
	 */
	public function log( $message, $level ) {

		$logger = wc_get_logger();
		$logger->log( $level, $message, array( 'source' => 'wcs_att' ) );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public function plugin_meta_links( $links, $file, $data, $status ) {

		if ( $file == plugin_basename( __FILE__ ) ) {
			$author1 = '<a href="' . $data[ 'AuthorURI' ] . '">' . $data[ 'Author' ] . '</a>';
			$author2 = '<a href="http://somewherewarm.gr/">SomewhereWarm</a>';
			$links[ 1 ] = sprintf( __( 'By %s', 'woocommerce-subscribe-all-the-things' ), sprintf( __( '%s and %s', 'woocommerce-subscribe-all-the-things' ), $author1, $author2 ) );
		}

		return $links;
	}
}

// End class_exists check.
endif;

/**
 * Returns the main instance of WCS_ATT to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WooCommerce Subscribe All the Things
 */
function WCS_ATT() {
  return WCS_ATT::instance();
}

// Launch the whole plugin.
$GLOBALS[ 'woocommerce_subscribe_all_the_things' ] = WCS_ATT();
