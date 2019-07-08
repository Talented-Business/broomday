<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_NMI class.
 *
 * @extends WC_Payment_Gateway
 */

class WC_Gateway_NMI extends WC_Payment_Gateway_CC {

	// Supported currencies
	private $currencies = array(
		'AED', 'AMD', 'ANG', 'ARS', 'AUD', 'AWG', 'AZN', 'BBD', 'BDT', 'BGN', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BWP', 'BYR', 'BZD', 'CAD', 'CHF', 'CLP', 'CNY', 'COP',
		'CRC', 'CVE', 'CYP', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD', 'EEK', 'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GHC', 'GIP', 'GMD', 'GNF', 'GTQ', 'GWP', 'GYD',
		'HKD', 'HNL', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'ISK', 'JMD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KRW', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LTL', 'LVL', 'MAD',
		'MDL', 'MGF', 'MNT', 'MOP', 'MRO', 'MTL', 'MUR', 'MVR', 'MWK', 'MYR', 'MZN', 'MXN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR',
		'PLN', 'PYG', 'QAR', 'RON', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 'SHP', 'SKK', 'SLL', 'SOS', 'STD', 'SVC', 'SZL', 'THB', 'TOP', 'TRY', 'TTD', 'TWD',
		'TZS', 'UAH', 'UGX', 'USD', 'UYU', 'UZS', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMK', 'ZWD',
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                   = 'nmi';
		$this->method_title         = __( 'Network Merchants (NMI)', 'wc-nmi' );
		$this->method_description   = __( 'Network Merchants (NMI) works by adding credit card fields on the checkout and then sending the details to NMI for verification. It fully supports WooCommerce Subscriptions and WooCommerce Pre-Orders plugins.', 'wc-nmi' );
		$this->has_fields           = true;
		$this->supports             = array(
			'subscriptions',
			'products',
			'refunds',
			'subscription_cancellation',
			'subscription_reactivation',
			'subscription_suspension',
			'subscription_amount_changes',
			'subscription_payment_method_change', // Subs 1.n compatibility
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'subscription_date_changes',
			'multiple_subscriptions',
			'pre-orders',
            'add_payment_method',
		);
		$this->live_url = 'https://secure.networkmerchants.com/api/transact.php';
		$this->test_url = '';

		// Load the form fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Get setting values.
		$this->title       		  	= $this->get_option( 'title' );
		$this->description 		  	= $this->get_option( 'description' );
		$this->enabled     		  	= $this->get_option( 'enabled' );
		$this->testmode    		  	= $this->get_option( 'testmode' ) === 'yes' ? true : false;
		$this->capture     		  	= $this->get_option( 'capture', 'yes' ) === 'yes' ? true : false;
		$this->saved_cards 		  	= $this->get_option( 'saved_cards' ) === 'yes' ? true : false;
		$this->add_customer_method	= $this->get_option( 'add_customer_method' );
		$this->username	   		  	= $this->get_option( 'username' );
		$this->password	   		  	= $this->get_option( 'password' );
		$this->logging     		  	= $this->get_option( 'logging' ) === 'yes' ? true : false;
		$this->debugging   		  	= $this->get_option( 'debugging' ) === 'yes' ? true : false;
		$this->line_items  		  	= $this->get_option( 'line_items' ) === 'yes' ? true : false;
		$this->allowed_card_types 	= $this->get_option( 'allowed_card_types' );
		$this->customer_receipt   	= $this->get_option( 'customer_receipt' ) === 'yes' ? true : false;

		if ( $this->testmode ) {
			$this->description .= ' ' . sprintf( __( '<br /><br /><strong>TEST MODE ENABLED</strong><br /> In test mode, you can use the card number 4111111111111111 with any CVC and a valid expiration date or check the documentation "<a href="%s">NMI Direct Post API</a>" for more card numbers.', 'wc-nmi' ), 'https://secure.networkmerchants.com/gw/merchants/resources/integration/download.php?document=directpost' );
			$this->description  = trim( $this->description );
		}

		// Hooks
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

	}

	/**
	 * get_icon function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_icon() {
		$icon = '';
		if( in_array( 'visa', $this->allowed_card_types ) ) {
			$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/visa.png' ) . '" alt="Visa" />';
		}
		if( in_array( 'mastercard', $this->allowed_card_types ) ) {
			$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/mastercard.png' ) . '" alt="Mastercard" />';
		}
		if( in_array( 'amex', $this->allowed_card_types ) ) {
			$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/amex.png' ) . '" alt="Amex" />';
		}
		if( in_array( 'discover', $this->allowed_card_types ) ) {
			$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/discover.png' ) . '" alt="Discover" />';
		}
		if( in_array( 'diners-club', $this->allowed_card_types ) ) {
			$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/diners.png' ) . '" alt="Diners Club" />';
		}
		if( in_array( 'jcb', $this->allowed_card_types ) ) {
			$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/jcb.png' ) . '" alt="JCB" />';
		}
		if( in_array( 'maestro', $this->allowed_card_types ) ) {
			$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/maestro.png' ) . '" alt="Maestro" />';
		}
		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
	}

	/**
	 * Check if SSL is enabled and notify the user
	 */
	public function admin_notices() {
		if ( $this->enabled == 'no' ) {
			return;
		}

		// Check required fields
		if ( ! $this->username ) {
			echo '<div class="error"><p>' . sprintf( __( 'NMI error: Please enter your Username <a href="%s">here</a>', 'wc-nmi' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=nmi' ) ) . '</p></div>';
			return;

		} elseif ( ! $this->password ) {
			echo '<div class="error"><p>' . sprintf( __( 'NMI error: Please enter your Password <a href="%s">here</a>', 'wc-nmi' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=nmi' ) ) . '</p></div>';
			return;
		}

		// Simple check for duplicate keys
		if ( $this->username == $this->password ) {
			echo '<div class="error"><p>' . sprintf( __( 'NMI error: Your Username and Password match. Please check and re-enter.', 'wc-nmi' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=nmi' ) ) . '</p></div>';
			return;
		}

		// Show message if enabled and FORCE SSL is disabled and WordpressHTTPS plugin is not detected
		if ( ! wc_checkout_is_https() ) {
			echo '<div class="notice notice-warning"><p>' . sprintf( __( 'Network Merchants (NMI) is enabled, but a SSL certificate is not detected. Your checkout may not be secure! Please ensure your server has a valid <a href="%1$s" target="_blank">SSL certificate</a>', 'wc-nmi' ), 'https://en.wikipedia.org/wiki/Transport_Layer_Security' ) . '</p></div>';
 		}

		if ( ! $this->currency_is_accepted() ) {
			echo '<div class="error"><p>' . sprintf( __( 'NMI supports only these currencies: %s', 'wc-nmi' ), implode( ', ', $this->currencies ) ) . '</p></div>';
			return;
		}
	}

	/**
	 * Check if this gateway is enabled
	 */
	public function is_available() {
		if ( $this->enabled == "yes" ) {
			if ( is_add_payment_method_page() && ! $this->saved_cards ) {
				return false;
			}
			// Required fields check
			if ( ! $this->username || ! $this->password ) {
				return false;
			}
			if ( ! $this->currency_is_accepted() ) {
				return false;
			}
			return true;
		}
		return parent::is_available();
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = apply_filters( 'wc_nmi_settings', array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'wc-nmi' ),
				'label'       => __( 'Enable NMI', 'wc-nmi' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title' => array(
				'title'       => __( 'Title', 'wc-nmi' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'wc-nmi' ),
				'default'     => __( 'Credit card (NMI)', 'wc-nmi' )
			),
			'description' => array(
				'title'       => __( 'Description', 'wc-nmi' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'wc-nmi' ),
				'default'     => __( 'Pay with your credit card via NMI.', 'wc-nmi' )
			),
			'testmode' => array(
				'title'       => __( 'Test mode', 'wc-nmi' ),
				'label'       => __( 'Enable Test Mode', 'wc-nmi' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in test mode. This will display test information on the checkout page and enable processing in non HTTPS mode.', 'wc-nmi' ),
				'default'     => 'yes'
			),
			'username' => array(
				'title'       => __( 'Gateway Username', 'wc-nmi' ),
				'type'        => 'text',
				'description' => __( 'Get your Gateway Username from your NMI account.', 'wc-nmi' ),
				'default'     => ''
			),
			'password' => array(
				'title'       => __( 'Gateway Password', 'wc-nmi' ),
				'type'        => 'password',
				'description' => __( 'Get your Gateway Password from your NMI account.', 'wc-nmi' ),
				'default'     => ''
			),
			'capture' => array(
				'title'       => __( 'Capture', 'wc-nmi' ),
				'label'       => __( 'Capture charge immediately', 'wc-nmi' ),
				'type'        => 'checkbox',
				'description' => __( 'Whether or not to immediately capture the charge. When unchecked, the charge issues an authorization and will need to be captured later.', 'wc-nmi' ),
				'default'     => 'yes'
			),
			'saved_cards' => array(
				'title'       => __( 'Saved Cards', 'wc-nmi' ),
				'label'       => __( 'Enable Payment via Saved Cards', 'wc-nmi' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on NMI servers, not on your store.', 'wc-nmi' ),
				'default'     => 'no'
			),
			'add_customer_method' => array(
				'title'       => __( 'Add Customer Method', 'wc-nmi' ),
				'type'		  => 'select',
				'description' => sprintf( __( 'Choose the API Method to use when adding customers to the gateway vault. The "auth" method will authorize the customer\'s card for %s 1.00 and will quickly void it when it is added to the vault so you must only select it in case your processor does not support "validate". If you have trouble signing up for subscriptions, adding or saving payment methods using "validate" then give "auth" a try.', 'wc-nmi' ), get_woocommerce_currency() ),
				'options' => array(
					'validate'	=> __( 'validate', 'wc-nmi' ),
					'auth'      => __( 'auth', 'wc-nmi' ),
				),
				'default'	  => 'validate',
				'css'    	  => 'min-width:100px;',
			),
			'logging' => array(
				'title'       => __( 'Logging', 'wc-nmi' ),
				'label'       => __( 'Log debug messages', 'wc-nmi' ),
				'type'        => 'checkbox',
				'description' => sprintf( __( 'Save debug messages to the WooCommerce System Status log file <code>%s</code>.', 'wc-nmi' ), WC_Log_Handler_File::get_log_file_path( 'woocommerce-gateway-nmi' ) ),
				'default'     => 'no'
			),
			'debugging' => array(
				'title'       => __( 'Gateway Debug', 'wc-nmi' ),
				'label'       => __( 'Log gateway requests and response to the WooCommerce System Status log.', 'wc-nmi' ),
				'type'        => 'checkbox',
				'description' => __( '<strong>CAUTION! Enabling this option will write gateway requests including card numbers and CVV to the logs.</strong> Do not turn this on unless you have a problem processing credit cards. You must only ever enable it temporarily for troubleshooting or to send requested information to the plugin author. It must be disabled straight away after the issues are resolved and the plugin logs should be deleted.', 'wc-nmi' ) . ' ' . sprintf( __( '<a href="%s">Click here</a> to check and delete the full log file.', 'wc-nmi' ), admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . WC_Log_Handler_File::get_log_file_name( 'woocommerce-gateway-nmi' ) ) ),
				'default'     => 'no'
			),
			'line_items' => array(
				'title'       => __( 'Line Items', 'wc-nmi' ),
				'label'       => __( 'Enable Line Items', 'wc-nmi' ),
				'type'        => 'checkbox',
				'description' => __( 'Add line item data to description sent to the gateway (eg. Item x qty).', 'wc-nmi' ),
				'default'     => 'no'
			),
			'allowed_card_types' => array(
				'title'       => __( 'Allowed Card types', 'wc-nmi' ),
				'class'       => 'wc-enhanced-select',
				'type'        => 'multiselect',
				'description' => __( 'Select the card types you want to allow payments from.', 'wc-nmi' ),
				'default'     => array( 'visa','mastercard','discover','amex' ),
				'options'	  => array(
					'visa' 			=> __( 'Visa', 'wc-nmi' ),
					'mastercard' 	=> __( 'MasterCard', 'wc-nmi' ),
					'discover' 		=> __( 'Discover', 'wc-nmi' ),
					'amex' 			=> __( 'American Express', 'wc-nmi' ),
					'diners-club' 	=> __( 'Diners Club', 'wc-nmi' ),
					'jcb' 			=> __( 'JCB', 'wc-nmi' ),
					'maestro' 		=> __( 'Maestro', 'wc-nmi' ),
				),
			),
			'customer_receipt' => array(
				'title'       => __( 'Receipt', 'wc-nmi' ),
				'label'       => __( 'Send Gateway Receipt', 'wc-nmi' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, the customer will be sent an email receipt from NMI.', 'wc-nmi' ),
				'default'     => 'no'
			),
		) );
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {
		$display_tokenization = is_checkout() && $this->saved_cards;
		$total                = WC()->cart->total;

		// If paying from order, we need to get total from order not cart.
		if ( isset( $_GET['pay_for_order'] ) && ! empty( $_GET['key'] ) ) {
			$order = wc_get_order( wc_get_order_id_by_order_key( wc_clean( $_GET['key'] ) ) );
			$total = $order->get_total();
		}

        echo '<div class="nmi_new_card"
			id="nmi-payment-data"
			data-description=""
			data-amount="' . esc_attr( $total ) . '"
			data-name="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '"
			data-currency="' . esc_attr( strtolower( get_woocommerce_currency() ) ) . '">';

		if ( $this->description ) {
			echo apply_filters( 'wc_nmi_description', wpautop( wp_kses_post( $this->description ) ) );
		}

		if ( $display_tokenization ) {
            $this->tokenization_script();
			$this->saved_payment_methods();
		}
        $this->form();

        if ( $display_tokenization ) {
            $this->save_payment_method_checkbox();
        }

		echo '</div>';
	}

    /**
	 * Returns a users saved tokens for this gateway.
	 * @since 1.1.0
	 * @return array
	 */
	public function get_tokens() {
		if ( sizeof( $this->tokens ) > 0 ) {
			return $this->tokens;
		}

		if ( is_user_logged_in() ) {
			$this->tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), $this->id );
		}

		return $this->tokens;
	}

	/**
	 * Process the payment
	 */
	public function process_payment( $order_id, $retry = true ) {

		$order       = wc_get_order( $order_id );
        $token_id 	 = isset( $_POST['wc-nmi-payment-token'] ) ? wc_clean( $_POST['wc-nmi-payment-token'] ) : '';
		$customer_id = is_user_logged_in() ? get_user_meta( get_current_user_id(), '_nmi_customer_id', true ) : 0;
		if ( ! $customer_id || ! is_string( $customer_id ) ) {
			$customer_id = 0;
		}

		$this->log( "Info: Beginning processing payment for order $order_id for the amount of {$order->get_total()}" );

		// Use NMI CURL API for payment
		try {
			$post_data = array();
			$payment_args = array();

			if ( $token_id !== 'new' && $token_id && $customer_id ) {
                $token = WC_Payment_Tokens::get( $token_id );

                if ( ! $token || $token->get_user_id() !== get_current_user_id() ) {
                    WC()->session->set( 'refresh_totals', true );
                    throw new Exception( __( 'Invalid payment method. Please input a new card number.', 'wc-nmi' ) );
                }

                $card_id = $token->get_token();
			}
			// Use token
			else {
				$maybe_saved_card = isset( $_POST['wc-nmi-new-payment-method'] ) && ! empty( $_POST['wc-nmi-new-payment-method'] );
				$card_id = 0;

				// Check for CC details filled or not
				if( empty( $_POST['nmi-card-number'] ) || empty( $_POST['nmi-card-expiry'] ) || empty( $_POST['nmi-card-cvc'] ) ) {
					throw new Exception( __( 'Credit card details cannot be left incomplete.', 'wc-nmi' ) );
				}

				// Check for card type supported or not
				if( ! in_array( $this->get_card_type( $_POST['nmi-card-number'], 'pattern', 'name' ), $this->allowed_card_types ) ) {
					$this->log( sprintf( __( 'Card type being used is not one of supported types in plugin settings: %s', 'wc-nmi' ), $this->get_card_type( $_POST['nmi-card-number'] ) ) );
					throw new Exception( __( 'Card Type Not Accepted', 'wc-nmi' ) );
				}

				// Save token if logged in
				if ( is_user_logged_in() && $this->saved_cards && $maybe_saved_card ) {
					$customer_id = $this->add_customer( $order );
					if ( is_wp_error( $customer_id ) ) {
						throw new Exception( $customer_id->get_error_message() );
					} else {
						$this->add_card( $customer_id );
						$card_id = $customer_id;
					}
				} else {
					$expiry = explode( ' / ', $_POST['nmi-card-expiry'] );
					$expiry[1] = substr( $expiry[1], -2 );
					$post_data['ccnumber']	= $_POST['nmi-card-number'];
					$post_data['ccexp']		= $expiry[0] . $expiry[1];
					$post_data['cvv']		= $_POST['nmi-card-cvc'];
					$customer_id = 0;
				}
			}
			// Store the ID in the order
			if ( $customer_id ) {
				$order->update_meta_data( '_nmi_customer_id', $customer_id );
			}
			if ( $card_id ) {
				$order->update_meta_data( '_nmi_card_id', $card_id );
			}

			$description = sprintf( __( '%s - Order %s', 'wc-nmi' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $order->get_order_number() );

			if( $this->line_items ) {
				$description .= ' (' . $this->get_line_items( $order ) . ')';
			}

			$payment_args = array(
				'orderid'	 		=> $order_id,
				'order_description'	=> $description,
				'amount'			=> $order->get_total(),
				'transactionid'		=> $order->get_transaction_id(),
				'type'				=> $this->capture ? 'sale' : 'auth',
				'first_name'		=> isset( $_POST['billing_first_name'] ) ? $_POST['billing_first_name'] : $order->get_billing_first_name(),
				'last_name'			=> isset( $_POST['billing_last_name'] ) ? $_POST['billing_last_name'] : $order->get_billing_last_name(),
				'address1'			=> isset( $_POST['billing_address_1'] ) ? $_POST['billing_address_1'] : $order->get_billing_address_1(),
				'address2'			=> isset( $_POST['billing_address_2'] ) ? $_POST['billing_address_2'] : $order->get_billing_address_2(),
				'city'				=> isset( $_POST['billing_city'] ) ? $_POST['billing_city'] : $order->get_billing_city(),
				'state'				=> isset( $_POST['billing_state'] ) ? $_POST['billing_state'] : $order->get_billing_state(),
				'country'			=> isset( $_POST['billing_country'] ) ? $_POST['billing_country'] : $order->get_billing_country(),
				'zip'				=> isset( $_POST['billing_postcode'] ) ? $_POST['billing_postcode'] : $order->get_billing_postcode(),
				'email' 			=> isset( $_POST['billing_email'] ) ? $_POST['billing_email'] : $order->get_billing_email(),
				'phone'				=> isset( $_POST['billing_phone'] ) ? $_POST['billing_phone'] : $order->get_billing_phone(),
				'company'			=> isset( $_POST['billing_company'] ) ? $_POST['billing_company'] : $order->get_billing_company(),
				'customer_vault_id' => $card_id ? $card_id : $customer_id,
				'currency'			=> $this->get_payment_currency( $order_id ),
			);

			$payment_args = array_merge( $payment_args, $post_data );

			$response = $this->nmi_request( $payment_args );

			if ( $response->error || $response->declined ) {
				throw new Exception( $response->error_message );
			}

			// Store charge ID
			$order->update_meta_data( '_nmi_charge_id', $response->transactionid );

			// Store other data such as fees
			if ( $response->approved ) {
				$order->set_transaction_id( $response->transactionid );

				if( $payment_args['type'] == 'sale' ) {

					// Store captured value
					$order->update_meta_data( '_nmi_charge_captured', 'yes' );
					$order->update_meta_data( 'NMI Payment ID', $response->transactionid );

					// Payment complete
					$order->payment_complete( $response->transactionid );

					// Add order note
					$complete_message = sprintf( __( 'NMI charge complete (Charge ID: %s)', 'wc-nmi' ), $response->transactionid );
					$order->add_order_note( $complete_message );
					$this->log( "Success: $complete_message" );

				} else {

					// Store captured value
					$order->update_meta_data( '_nmi_charge_captured', 'no' );

					if ( $order->has_status( array( 'pending', 'failed' ) ) ) {
						$order->reduce_order_stock();
					}

					// Mark as on-hold
					$authorized_message = sprintf( __( 'NMI charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'wc-nmi' ), $response->transactionid );
					$order->update_status( 'on-hold', $authorized_message );
					$this->log( "Success: $authorized_message" );

				}

				$order->save();

			}

			// Remove cart
			WC()->cart->empty_cart();

			do_action( 'wc_gateway_' . $this->id . '_process_payment', $response, $order );

			// Return thank you page redirect
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order )
			);

		} catch ( Exception $e ) {
			wc_add_notice( sprintf( __( 'Gateway Error: %s', 'wc-nmi' ), $e->getMessage() ), 'error' );
			$this->log( sprintf( __( 'Gateway Error: %s', 'wc-nmi' ), $e->getMessage() ) );

			if ( $order->has_status( array( 'pending', 'failed' ) ) ) {
				$this->send_failed_order_email( $order_id );
			}

			do_action( 'wc_gateway_' . $this->id . '_process_payment_error', $e, $order );

			return array(
				'result'   => 'fail',
				'redirect' => ''
			);

		}
	}

	function get_line_items( $order ) {
		$line_items = array();
		// order line items
		foreach ( $this->get_order_line_items( $order ) as $item ) {
			if ( $item->item_total >= 0 ) {
				$line_items[] = $item->name . ' x ' . $item->quantity;
			}
		}
		return implode( ', ', $line_items );
	}

	function get_order_line_items( $order ) {
		$line_items = array();

		foreach ( $order->get_items() as $id => $item ) {
			$line_item = new stdClass();
			$product = $order->get_product_from_item( $item );

			$line_item->id          = $id;
			$line_item->name        = htmlentities( $item['name'], ENT_QUOTES, 'UTF-8', false );

			$line_item->quantity    = $item['qty'];
			$line_item->item_total  = isset( $item['recurring_line_total'] ) ? $item['recurring_line_total'] : $order->get_item_total( $item );
			$line_item->line_total  = $order->get_line_total( $item );

			$line_item->product     = is_object( $product ) ? $product : null;
			$line_item->item        = $item;

			$line_items[] = $line_item;
		}

		return $line_items;
	}

	/**
	 * Refund a charge
	 * @param  int $order_id
	 * @param  float $amount
	 * @return bool
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );

		if ( ! $order || ! $order->get_transaction_id() || $amount <= 0 ) {
			return false;
		}

		$args = array(
			'amount'  			=> $amount,
			'transactionid'		=> $order->get_transaction_id(),
			'email' 			=> $order->get_billing_email(),
			'type'		 		=> 'refund',
			'order_description' => $reason,
			'currency'			=> $this->get_payment_currency( $order_id ),
		);

		$this->log( "Info: Beginning refund for order $order_id for the amount of {$amount}" );

		$response = $this->nmi_request( $args );

		if ( $response->error || $response->declined ) {
			$this->log( "Gateway Error: " . $response->error_message );
			return new WP_Error( 'nmi', $response->error_message );
		} elseif ( ! empty( $response->transactionid ) ) {
			$refund_message = sprintf( __( 'Refunded %s - Refund ID: %s - Reason: %s', 'wc-nmi' ), $amount, $response->transactionid, $reason );
			$order->add_order_note( $refund_message );
			$order->save();
			$this->log( "Success: " . html_entity_decode( strip_tags( $refund_message ) ) );
			return true;
		}
	}

	/**
	 * Add a customer to NMI via the API.
	 *
	 * @param int $order
	 * @param string $nmi_token
	 * @return int|WP_ERROR
	 */
	public function add_customer( $order ) {
		$expiry = explode( ' / ', $_POST['nmi-card-expiry'] );
		$expiry[1] = substr( $expiry[1], -2 );
		$customer_name = sprintf( __( 'Customer: %s %s', 'wc-nmi' ), ( isset( $_POST['billing_first_name'] ) ? $_POST['billing_first_name'] : $order->get_billing_first_name() ), ( isset( $_POST['billing_last_name'] ) ? $_POST['billing_last_name'] : $order->get_billing_last_name() ) );
		$args = array(
			'order_description' => $customer_name,
			'ccnumber'			=> $_POST['nmi-card-number'],
			'ccexp'				=> $expiry[0] . $expiry[1],
			'cvv'				=> $_POST['nmi-card-cvc'],
			'first_name'		=> ( isset( $_POST['billing_first_name'] ) ? $_POST['billing_first_name'] : $order->get_billing_first_name() ),
			'last_name'			=> ( isset( $_POST['billing_last_name'] ) ? $_POST['billing_last_name'] : $order->get_billing_last_name() ),
			'address1'			=> ( isset( $_POST['billing_address_1'] ) ? $_POST['billing_address_1'] : $order->get_billing_address_1() ),
			'address2'			=> ( isset( $_POST['billing_address_2'] ) ? $_POST['billing_address_2'] : $order->get_billing_address_2() ),
			'city'				=> ( isset( $_POST['billing_city'] ) ? $_POST['billing_city'] : $order->get_billing_city() ),
			'state'				=> ( isset( $_POST['billing_state'] ) ? $_POST['billing_state'] : $order->get_billing_state() ),
			'country'			=> ( isset( $_POST['billing_country'] ) ? $_POST['billing_country'] : $order->get_billing_country() ),
			'zip'				=> ( isset( $_POST['billing_postcode'] ) ? $_POST['billing_postcode'] : $order->get_billing_postcode() ),
			'email' 			=> ( isset( $_POST['billing_email'] ) ? $_POST['billing_email'] : $order->get_billing_email() ),
			'phone'				=> ( isset( $_POST['billing_phone'] ) ? $_POST['billing_phone'] : $order->get_billing_phone() ),
			'company'			=> ( isset( $_POST['billing_company'] ) ? $_POST['billing_company'] : $order->get_billing_company() ),
			'customer_vault' 	=> 'add_customer',
			'customer_vault_id'	=> '',
			'currency'			=> $this->get_payment_currency( $order->get_id() ),
		);

		if ( $this->add_customer_method == 'validate' ) {
			$customer_method = array( 'type' => 'validate' );
		} else {
			$customer_method = array( 'type' => 'auth', 'amount' => 1.00 );
		}

		$args = array_merge( $args, $customer_method );

		$response = $this->nmi_request( $args );

		if ( $response->error || $response->declined ) {
			return new WP_Error( 'nmi', $response->error_message );
		} elseif ( ! empty( $response->customer_vault_id ) ) {

			// Store the ID on the user account if logged in
			if ( is_user_logged_in() ) {
				update_user_meta( get_current_user_id(), '_nmi_customer_id', $response->customer_vault_id );
			}

			// Store the ID in the order
			$order->update_meta_data( '_nmi_customer_id', $response->customer_vault_id );

			do_action( 'wc_nmi_add_customer', $order, $response->customer_vault_id, $response );

			// Voiding add_customer auth transaction
			if ( $this->add_customer_method == 'auth' ) {
				$args = array(
					'amount'			=> 1.00,
					'transactionid'		=> $response->transactionid,
					'type' 				=> 'cancel',
				);
				$this->nmi_request( $args );
			}
			return $response->customer_vault_id;
		}

		$error_message = __( 'Unable to add customer', 'wc-nmi' );
		$this->log( sprintf( __( 'Gateway Error: %s', 'wc-nmi' ), $error_message ) );
		return new WP_Error( 'error', $error_message );
	}

    /**
	 * Add a customer to NMI via the API.
	 *
	 * @param int $user_id
	 * @param string $nmi_token
	 * @return int|WP_ERROR
	 */
	public function add_customer_from_meta( $user_id ) {
        $user_meta = get_user_meta( $user_id );
		$expiry = explode( ' / ', $_POST['nmi-card-expiry'] );
		$expiry[1] = substr( $expiry[1], -2 );
		$customer_name = sprintf( __( 'Customer: %s %s', 'wc-nmi' ), $user_meta['billing_first_name'][0], $user_meta['billing_last_name'][0] );
		$args = array(
			'order_description' => $customer_name,
			'ccnumber'			=> $_POST['nmi-card-number'],
			'ccexp'				=> $expiry[0] . $expiry[1],
			'cvv'				=> $_POST['nmi-card-cvc'],
			'first_name'		=> $user_meta['billing_first_name'][0],
			'last_name'			=> $user_meta['billing_last_name'][0],
			'address1'			=> $user_meta['billing_address_1'][0],
			'address2'			=> $user_meta['billing_address_2'][0],
			'city'				=> $user_meta['billing_city'][0],
			'state'				=> $user_meta['billing_state'][0],
			'country'			=> $user_meta['billing_country'][0],
			'zip'				=> $user_meta['billing_postcode'][0],
			'email' 			=> $user_meta['billing_email'][0],
			'phone'				=> $user_meta['billing_phone'][0],
			'company'			=> $user_meta['billing_company'][0],
			'customer_vault' 	=> 'add_customer',
			'customer_vault_id'	=> '',
			'currency'			=> get_woocommerce_currency(),
		);

		if ( $this->add_customer_method == 'validate' ) {
			$customer_method = array( 'type' => 'validate' );
		} else {
			$customer_method = array( 'type' => 'auth', 'amount' => 1.00 );
		}

		$args = array_merge( $args, $customer_method );

		$response = $this->nmi_request( $args );

		if ( $response->error || $response->declined ) {
			return new WP_Error( 'nmi', $response->error_message );
		} elseif ( ! empty( $response->customer_vault_id ) ) {

			// Store the ID on the user account if logged in
			if ( is_user_logged_in() ) {
				update_user_meta( get_current_user_id(), '_nmi_customer_id', $response->customer_vault_id );
			}

			// Voiding add_customer auth transaction
			if ( $this->add_customer_method == 'auth' ) {
				$args = array(
					'amount'			=> 1.00,
					'transactionid'		=> $response->transactionid,
					'type' 				=> 'cancel',
				);
				$this->nmi_request( $args );
			}

			return $response->customer_vault_id;
		}

		$error_message = __( 'Unable to add customer', 'wc-nmi' );
		$this->log( sprintf( __( 'Gateway Error: %s', 'wc-nmi' ), $error_message ) );
		return new WP_Error( 'error', $error_message );
	}

	/**
	 * Add a card to a customer via the API.
	 *
	 * @param int $order
	 * @param string $nmi_token
	 * @return int|WP_ERROR
	 */
	public function add_card( $customer_id, $skip = false ) {
		$card_no = $_POST['nmi-card-number'];
		$expiry = explode( ' / ', $_POST['nmi-card-expiry'] );
		$expiry[1] = substr( $expiry[1], -2 );
		$card = array(
			'id' => $customer_id,
			'ccexp'	=> $expiry[0] . $expiry[1],
			'last4'	=> substr( $card_no, -4 ),
			'brand'	=> $this->get_card_type( $card_no ),
		);
		$card = (object) $card;

		if( !$skip ) {
            $token = new WC_Payment_Token_CC();
            $token->set_token( $card->id );
			$token->set_gateway_id( 'nmi' );
			$token->set_card_type( strtolower( $card->brand ) );
			$token->set_last4( $card->last4 );
			$token->set_expiry_month( $expiry[0] );
			$token->set_expiry_year( '20' . $expiry[1] );
			$token->set_user_id( get_current_user_id() );
			$token->save();
		}
		return $card;
	}

    /**
	 * Add payment method via account screen.
	 * We don't store the token locally, but to the NMI API.
	 * @since 1.1.0
	 */
	public function add_payment_method() {
		if ( ! is_user_logged_in() ) {
			wc_add_notice( __( 'There was a problem adding the card.', 'wc-nmi' ), 'error' );
			return;
		}

        $customer_id = $this->add_customer_from_meta( get_current_user_id() );
        if ( is_wp_error( $customer_id ) ) {
			wc_add_notice( sprintf( __( 'Gateway Error: %s', 'wc-nmi' ), $customer_id->get_error_message() ), 'error' );
			$this->log( sprintf( __( 'Gateway Error: %s', 'wc-nmi' ), $customer_id->get_error_message() ) );
			return;
        }

        $this->add_card( $customer_id );

		return array(
			'result'   => 'success',
			'redirect' => wc_get_endpoint_url( 'payment-methods' ),
		);
	}

	function nmi_request( $args ) {
		if( !class_exists( 'NMI' ) ) {
			require_once( dirname( __FILE__ ) . '/nmi_sdk/NMI.php' );
		}
		$gateway_debug = ( $this->logging && $this->debugging );
		$transaction = new NMI( $this->username, $this->password, $gateway_debug );
		$transaction->setSandbox( $this->testmode );

		if( isset( $args['customer_vault'] ) ) {
			$transaction->customer_vault = $args['customer_vault'];
		}
		if( isset( $args['customer_vault_id'] ) && !empty( $args['customer_vault_id'] ) ) {
			$transaction->customer_vault_id = $args['customer_vault_id'];
		}
		if( isset( $args['amount'] ) ) {
			$transaction->amount = $args['amount'];
		}
		if( isset( $args['transactionid'] ) && !empty( $args['transactionid'] ) ) {
			$transaction->transactionid = $args['transactionid'];
		}
		if( isset( $args['ccnumber'] ) ) {
			$transaction->ccnumber = $args['ccnumber'];
		}
		if( isset( $args['ccexp'] ) ) {
			$transaction->ccexp = $args['ccexp'];
		}
		if( isset( $args['cvv'] ) ) {
			$transaction->cvv = $args['cvv'];
		}
		if( isset( $args['first_name'] ) ) {
			$transaction->first_name = $args['first_name'];
		}
		if( isset( $args['last_name'] ) ) {
			$transaction->last_name = $args['last_name'];
		}
		if( isset( $args['address1'] ) ) {
			$transaction->address1 = $args['address1'];
		}
		if( isset( $args['address2'] ) ) {
			$transaction->address2 = $args['address2'];
		}
		if( isset( $args['city'] ) ) {
			$transaction->city = $args['city'];
		}
		if( ! in_array( $args['type'], array( 'capture', 'cancel', 'refund' ) ) ) {
			if( isset( $args['state'] ) && !empty( $args['state'] ) ) {
				$transaction->state = $args['state'];
			} else {
				$transaction->state = 'NA';
			}
		}
		if( isset( $args['country'] ) ) {
			$transaction->country = $args['country'];
		}
		if( isset( $args['zip'] ) ) {
			$transaction->zip = $args['zip'];
		}
		if( isset( $args['email'] ) ) {
			$transaction->email = $args['email'];
		}
		if( isset( $args['phone'] ) ) {
			$transaction->phone = $args['phone'];
		}
		if( isset( $args['company'] ) ) {
			$transaction->company = $args['company'];
		}
		if( isset( $args['orderid'] ) ) {
			$transaction->orderid = $args['orderid'];
		}
		if( isset( $args['order_description'] ) ) {
			$transaction->order_description = substr( $args['order_description'], 0, 99 );
		}

		$transaction->currency = isset( $args['currency'] ) ? $args['currency'] : get_woocommerce_currency();
		$transaction->customer_receipt = isset( $args['customer_receipt'] ) ? $args['customer_receipt'] : $this->customer_receipt;
		$transaction->ipaddress = isset( $args['ipaddress'] ) ? $args['ipaddress'] : WC_Geolocation::get_ip_address();

		$response = $transaction->{$args['type']}();

		return $response;
	}

	function get_card_type( $value, $field = 'pattern', $return = 'label' ) {
		$card_types = array(
			array(
				'label' => 'American Express',
				'name' => 'amex',
				'pattern' => '/^3[47]/',
				'valid_length' => '[15]'
			),
			array(
				'label' => 'JCB',
				'name' => 'jcb',
				'pattern' => '/^35(2[89]|[3-8][0-9])/',
				'valid_length' => '[16]'
			),
			array(
				'label' => 'Discover',
				'name' => 'discover',
				'pattern' => '/^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/',
				'valid_length' => '[16]'
			),
			array(
				'label' => 'MasterCard',
				'name' => 'mastercard',
				'pattern' => '/^5[1-5]/',
				'valid_length' => '[16]'
			),
			array(
				'label' => 'Visa',
				'name' => 'visa',
				'pattern' => '/^4/',
				'valid_length' => '[16]'
			),
			array(
				'label' => 'Maestro',
				'name' => 'maestro',
				'pattern' => '/^(5018|5020|5038|6304|6759|676[1-3])/',
				'valid_length' => '[12, 13, 14, 15, 16, 17, 18, 19]'
			),
			array(
				'label' => 'Diners Club',
				'name' => 'diners-club',
				'pattern' => '/^3[0689]/',
				'valid_length' => '[14]'
			),
		);

		foreach( $card_types as $type ) {
			$card_type = $type['name'];
			$compare = $type[$field];
			if ( ( $field == 'pattern' && preg_match( $compare, $value, $match ) ) || $compare == $value ) {
				return $type[$return];
			}
		}

	}

	/**
	 * Get payment currency, either from current order or WC settings
	 *
	 * @since 4.1.0
	 * @return string three-letter currency code
	 */
	function get_payment_currency( $order_id = false ) {
 		$currency = get_woocommerce_currency();
		$order_id = ! $order_id ? $this->get_checkout_pay_page_order_id() : $order_id;

 		// Gets currency for the current order, that is about to be paid for
 		if ( $order_id ) {
 			$order    = wc_get_order( $order_id );
 			$currency = $order->get_currency();
 		}
 		return $currency;
 	}

	/**
	 * Returns true if $currency is accepted by this gateway
	 *
	 * @since 2.1.0
	 * @param string $currency optional three-letter currency code, defaults to
	 *        order currency (if available) or currently configured WooCommerce
	 *        currency
	 * @return boolean true if $currency is accepted, false otherwise
	 */
	public function currency_is_accepted( $currency = null ) {
		// accept all currencies
		if ( ! $this->currencies ) {
			return true;
		}
		// default to order/WC currency
		if ( is_null( $currency ) ) {
			$currency = $this->get_payment_currency();
		}
		return in_array( $currency, $this->currencies );
	}

	/**
	 * Returns the order_id if on the checkout pay page
	 *
	 * @since 3.0.0
	 * @return int order identifier
	 */
	public function get_checkout_pay_page_order_id() {
		global $wp;
		return isset( $wp->query_vars['order-pay'] ) ? absint( $wp->query_vars['order-pay'] ) : 0;
	}

	/**
	 * Send the request to NMI's API
	 *
	 * @since 2.6.10
	 *
	 * @param string $context
	 * @param string $message
	 */
	public function log( $message ) {
		if ( $this->logging ) {
			WC_NMI_Logger::log( $message );
		}
	}

	/**
	 * Sends the failed order email to admin
	 *
	 * @version 1.0.2
	 * @since 1.0.2
	 * @param int $order_id
	 * @return null
	 */
	public function send_failed_order_email( $order_id ) {
		$emails = WC()->mailer()->get_emails();
		if ( ! empty( $emails ) && ! empty( $order_id ) ) {
			$emails['WC_Email_Failed_Order']->trigger( $order_id );
		}
	}
}
