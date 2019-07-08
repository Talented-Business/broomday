<?php
/**
 * Base class for the Network Merchants (NMI) Responses.
 *
 * @package    	Network Merchants (NMI)
 * @subpackage	NMI_Request
 */


/**
 * Parses an Network Merchants (NMI) Response.
 *
 * @package 	Network Merchants (NMI)
 * @subpackage	NMI_Request
 */
class NMI_Response {

    const APPROVED = 1;
    const DECLINED = 2;
    const ERROR = 3;

	public $approved;
    public $declined;
    public $error;

    public $response;
	public $responsetext;
	public $authcode;
	public $transactionid;
    public $avsresponse;
    public $cvvresponse;
    public $orderid;
    public $type;
    public $response_code;
    public $customer_vault_id;

}
