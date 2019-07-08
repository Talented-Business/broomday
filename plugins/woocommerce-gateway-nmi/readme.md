=== Network Merchants (NMI) Payment Gateway For WooCommerce (Enteprise) ===  
Contributors: Pledged Plugins  
Tags: woocommerce Network Merchants (NMI), Network Merchants (NMI), payment gateway, woocommerce, woocommerce payment gateway, recurring payments, subscriptions, pre-orders  
Plugin URI: https://pledgedplugins.com/products/nmi-network-merchants-payment-gateway-woocommerce/  
Requires at least: 4.0  
Tested up to: 5.0  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

This Payment Gateway For WooCommerce extends the functionality of WooCommerce to accept payments from credit/debit cards using the Network Merchants (NMI) payment gateway. Since customers will be entering credit cards directly on your store you should sure that your checkout pages are protected by SSL.

== Description ==

`Network Merchants (NMI) Payment Gateway for WooCommerce` allows you to accept credit cards directly on your WooCommerce store by utilizing the Network Merchants (NMI) payment gateway.

= Features =

1. Accept Credit Cards directly on your website by using the Network Merchants (NMI) gateway.
2. No redirecting your customer back and forth.
3. Very easy to install and configure. Ready in Minutes!
4. Supports WooCommerce Subscriptions and WooCommerce Pre-Orders add-on from WooCommerce.com.
5. Safe and secure method to process credit cards using the Network Merchants (NMI) payment gateway.
6. Internally processes credit cards, safer, quicker, and more secure!

If you need any assistance with this or any of our other plugins, please visit our support portal:  
https://pledgedplugins.com/support

== Installation ==

Easy steps to install the plugin:

1. Upload `woocommerce-gateway-nmi` folder/directory to the `/wp-content/plugins/` directory
2. Activate the plugin (WordPress -> Plugins).
3. Go to the WooCommerce settings page (WordPress -> WooCommerce -> Settings) and select the Payments tab.
4. Under the Payments tab, you will find all the available payment methods. Find the 'Network Merchants (NMI)' link in the list and click it.
5. On this page you will find all of the configuration options for this payment gateway.
6. Enable the method by using the checkbox.
7. Enter the Network Merchants (NMI) account details (Username, Password)

That's it! You are ready to accept credit cards with your Network Merchants (NMI) payment gateway now connected to WooCommerce.

`Is SSL Required to use this plugin?`  
A valid SSL certificate is required to ensure your customer credit card details are safe and make your site PCI DSS compliant. This plugin does not store the customer credit card numbers or sensitive information on your website.

`Does the plugin support direct updates from the WP dashboard?`  
Yes. You can navigate to WordPress -> Tools -> WooCommerce NMI License page and activate the license key you received with your order. Once that is done you will be able to directly update the plugin to the latest version from the WordPress dashboard itself.

== Changelog ==

2.1.1  
Fixed conflicts with other echeck gateways  
Fixed log message and changed logging descriptions  

2.1.0  
Implemented full ACH support  
Fixed PHP notices  
Changed logging method  
Updated post meta saving method  
Removed deprecated script code  
Prevented the "state" parameter from being sent in "refund", "capture" or "void" transactions  

2.0.7  
Integrated auto-update API  

2.0.6  
Added GDPR retention setting and logic  
Fixed false negative on SSL warning notice in admin.  

2.0.5  
Added GDPR privacy support  
Added "minimum required" and "tested upto" headers  

2.0.4  
Added JCB, Maestro and Diners Club as options for allowed card types  
Made state field default to "NA" to support countries without a state  
Fixed tokenization issues when customer enters card expiry date in MM/YYYY format  
Removed deprecated code  

2.0.3  
Added option to choose the API method for adding customers to the gateway vault  
Passed billing details to "Pay for Order" page  

2.0.2  
Added option to restrict card types  
Made gateway receipt emails optional  

2.0.1  
Fixed issue with decline error handling for subscriptions and pre-orders  

2.0.0  
Made compatible with WooCommerce 3.0.0  

1.1.0  
Added subscription payment retry callbacks  
Implemented tokenization API, and removed legacy method of saving cards  

1.0.1  
Fixed an issue with PHP7  
Added validation for empty checkout form  
Added "Save to account" option to the checkout form for the customers  

1.0.0  
Initial release version