<?php
/**
 * Builds and sends an Network Merchants (NMI) Request.
 *
 * @package    Network Merchants (NMI)
 * @subpackage NMI
 */
class NMI extends NMI_Request {

	private $live_url = 'https://secure.networkmerchants.com/api/transact.php';
    private $test_url = 'https://secure.networkmerchants.com/api/transact.php';

	public function __construct( $username = false, $password = false, $logging = false, $live_url = false, $test_url = false ) {
		if( $live_url ) {
			$this->live_url = $live_url;
			$this->test_url = $test_url ? $test_url : $live_url;
		}
		parent::__construct( $username, $password, $logging );
	}

    /**
     * Checks to make sure a field is actually in the API before setting.
     * Set to false to skip this check.
     */
    public $verify_fields = true;

    /**
     * A list of all fields in the Network Merchants (NMI) API.
     * Used to warn user if they try to set a field not offered in the API.
     */
	private $_all_nmi_fields = array(
		"type","username","password","ccnumber","ccexp","cvv","amount","transactionid","orderid","ipaddress","tax","shipping","first_name","last_name","company","address1","address2",
		"city","state","zip","country","phone","fax","email","customer_receipt","recurring","shipping","order_description","customer_vault","customer_vault_id","currency","dup_seconds",
		"checkaba","checkaccount","account_type","checkname","payment","account_holder_type",
    );

	/**
	 * Product Sale transaction (Capture On)
	 * Transaction do completed/processing
	 */
    public function sale() {
        $this->type = "sale";
		return $this->_sendRequest();
    }

	/**
	 * Product Sale transaction (Capture Off)
	 * Transaction to put on-hold
	 */
	public function auth() {
        $this->type = "auth";
		return $this->_sendRequest();
    }

	/**
	 * Add Customer
	 */
	public function validate() {
        $this->type = "validate";
        return $this->_sendRequest();
    }

	/**
	 * Process Product on-hold to complete/processing transaction (Capture Off)
	 */
	public function capture() {
        $this->type = "capture";
        return $this->_sendRequest();
    }

	/**
	 * Process Product on-hold to cancel/refund transaction (Capture Off)
	 */
	public function cancel() {
        $this->type = "void";
		return $this->_sendRequest();
    }

	/**
	 * Product Sale Refund transaction (Capture On)
	 */
    public function refund() {
        $this->type = "refund";
		return $this->_sendRequest();
    }

    /**
     * Alternative syntax for setting x_ fields.
     *
     * Usage: $sale->method = "echeck";
     *
     * @param string $name
     * @param string $value
     */
    public function __set( $name, $value ) {
        $this->setField( $name, $value );
    }

    /**
     * Quickly set multiple fields.
     *
     * Note: The prefix x_ will be added to all fields. If you want to set a
     * custom field without the x_ prefix, use setCustomField or setCustomFields.
     *
     * @param array $fields Takes an array or object.
     */
    public function setFields( $fields ) {
        $array = (array) $fields;
        foreach( $array as $key => $value ) {
            $this->setField( $key, $value );
        }
    }

    /**
     * Set an individual name/value pair. This will append x_ to the name
     * before posting.
     *
     * @param string $name
     * @param string $value
     */
    public function setField( $name, $value ) {
        if( $this->verify_fields ) {
            if( in_array( $name, $this->_all_nmi_fields ) ) {
                $this->_post_fields[$name] = $value;
            } else {
                throw new NMI_Exception( "Error: no field $name exists in the NMI API.
                To set a custom field use setCustomField('field','value') instead." );
            }
        } else {
            $this->_post_fields[$name] = $value;
        }
    }

    /**
     * Unset an x_ field.
     *
     * @param string $name Field to unset.
     */
    public function unsetField( $name ) {
        unset( $this->_post_fields[$name] );
    }

    /**
     *
     *
     * @param string $response
     *
     * @return NMI_Response
     */
    protected function _handleResponse( $response ) {
        return new NMI_Gateway_Response( $response );
    }

    /**
     * @return string
     */
    protected function _getPostUrl() {
        return ( $this->_sandbox ? $this->test_url : $this->live_url );
    }

    /**
     * Converts the x_post_fields array into a string suitable for posting.
     */
    protected function _setPostString() {
        $this->_post_fields['username'] = $this->_username;
        $this->_post_fields['password'] = $this->_password;
        $this->_post_string = "";
        foreach( $this->_post_fields as $key => $value ) {
            $this->_post_string .= "$key=" . urlencode( $value ) . "&";
        }
        $this->_post_string = rtrim( $this->_post_string, "& " );
    }
}

/**
 * Parses an Network Merchants (NMI) Response.
 *
 * @package    Network Merchants (NMI)
 * @subpackage NMI
 */
class NMI_Gateway_Response extends NMI_Response {
    private $_response_array = array(); // An array with the split response.

    /**
     * Constructor. Parses the Network Merchants (NMI) response string.
     *
     * @param string $response      The response from the NMI server.
     */
    public function __construct( $response ) {

        if( $response ) {

            // Split Array
			parse_str( $response, $response_arr );

            /**
             * If Network Merchants (NMI) doesn't return a delimited response.
			*/
            if( count( $response_arr ) < 8 ) {
                $this->approved = false;
                $this->error = true;
                $this->error_message = sprintf( __( 'Unrecognized response from the gateway: %s', 'wc-nmi' ), $response );
                return;
            }

            // Set all fields
			foreach( $response_arr as $key => $value ) {
				$this->{$key} = $response_arr[$key];
			}

			$this->approved = ( $this->response == 1 );
            $this->declined = ( $this->response == 2 );
			$this->error    = ( $this->response == 3 );

            if( $this->declined ) {
                $this->error_message = __( 'Your card has been declined.', 'wc-nmi' );
            }

			if( $this->error ) {
                $this->error_message = $this->responsetext;
            }

        } else {
            $this->approved = false;
            $this->error = true;
            $this->error_message = __( 'Error connecting to the gateway', 'wc-nmi' );
        }
    }
}