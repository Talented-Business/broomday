=== Payment Gateway Based Fees and Discounts for WooCommerce ===
Contributors: tychesoftwares
Tags: woocommerce, woo commerce, payment, gateway, fee, discount
Requires at least: 4.4
Tested up to: 5.1.1
Stable tag: 2.5.9
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Set fees and discounts for WooCommerce payment gateways.

== Description ==

**Payment Gateway Based Fees and Discounts for WooCommerce** plugin extends WooCommerce by adding options to set **fees or discounts based on customer selected payment gateway**.

Payment gateway based fees and discounts can be added to **all payment gateways** including:

* standard WooCommerce payment gateways (Direct Bank Transfer (BACS), Cheque Payment, Cash on Delivery and PayPal),
* custom payment gateways added with any other plugin.

Fees and discounts can be set:

* globally for all products, or
* on per product basis.

Plugin requires **minimum setup**: after enabling the fee/discount for selected gateway (in WooCommerce > Settings > Payment Gateway Based Fees and Discounts), you can set:

* fee/discount value,
* fee/discount type: fixed or percent,
* additional fee/discount,
* minimum and maximum fee/discount values,
* minimum and/or maximum cart amount for adding the fee/discount,
* rounding options,
* taxation options,
* shipping options,
* product categories,
* customer countries and more.

= Feedback =
* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Drop us a line at [http://www.tychesoftwares.com](http://www.tychesoftwares.com).

= More =
* Visit the [Payment Gateway Based Fees and Discounts for WooCommerce plugin page](https://www.tychesoftwares.com/store/premium-plugins/payment-gateway-based-fees-and-discounts-for-woocommerce-plugin/?utm_source=wprepo&utm_medium=link&utm_campaign=PaymentGatewayFees).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go to "WooCommerce > Settings > Payment Gateway Based Fees and Discounts".

== Changelog === 2.5.9 - 18/04/2019 =* Fix - Fees are not being carried over from the Checkout page to the order.* Fix - Discounts are not being applied to renewal orders for subscription products.


= 2.5.8 - 11/04/2019 =* Added compatibility with WooCommerce Subscriptions.* The plugin goes into continous loading of the cart at Checkout when precision is left blanks for rounding. Fixed the same.* Added uninstall.php file to ensure the plugin data is deleted when the plugin is uninstalled.= 2.5.7 - 01/02/2019 =
= 2.5.7 - 01/02/2019 =
* Author name in the header has been changed.

= 2.5.6 - 16/11/2018 =
* Author name and URL updated due to handover of the plugins

= 2.5.5 - 31/10/2018 =
* Compatibility with WooCommerce 3.5.0 tested.

= 2.5.4 - 16/10/2018 =
* Dev - Current (i.e. chosen) payment gateway function rewritten (fixes the issue with 100% discount coupons on the checkout page - fees were not reapplied when coupon is removed).

= 2.5.3 - 12/10/2018 =
* Fix - Coupons usage with "WooCommerce Gift Certificates" plugin fixed.
* Fix - Settings hook priority increased, so gateways fees settings are not loaded too early (fixes the issue with "Mollie Payments for WooCommerce" plugin).

= 2.5.2 - 17/09/2018 =
* Dev - Code refactoring.

= 2.5.1 - 04/08/2018 =
* Fix - Product categories - Calculation type - Categories to exclude - Only for selected products - Option fixed.
* Dev - Admin settings descriptions updated.
* Dev - "States to include/exclude" options added.

= 2.5.0 - 04/08/2018 =
* Dev - Major code refactoring and clean up. Main plugin file and POT file renamed.
* Dev - Admin settings restyled and descriptions updated.
* Dev - "Reset section settings" options added.
* Dev - Raw input now allowed in all "Info" section templates.

= 2.4.0 - 30/07/2018 =
* Dev - "Merge All Fees" added to "General" section.
* Dev - "Global Extra Fee" added to "General" section.
* Dev - Current (i.e. chosen) payment gateway function rewritten (fixes the issue with "zipMoney" payment gateway).
* Dev - "Customer Countries" options added to both fees separately.
* Dev - "Europe", "European Union", "Europe excluding EU", "Eurozone", "Africa", "Asia", "Australia and Oceania", "Central America", "North America" and "South America" added as country selection.
* Dev - Eight more countries added to the list.
* Dev - "Info" admin settings section added (options moved from "General" section).
* Dev - Admin settings restyled.
* Dev - Plugin link updated from <a href="https://wpcodefactory.com">https://wpcodefactory.com</a> to <a href="https://wpfactory.com">https://wpfactory.com</a>.
* Dev - Code clean up.

= 2.3.3 - 07/01/2018 =
* Dev - Additional check added in `Alg_WC_Checkout_Fees_Settings_Gateways` (prevents AJAX error on some servers).

= 2.3.2 - 02/01/2018 =
* Dev - WooCommerce 3.2 compatibility - `WC_Tax::get_tax_total()` replaced with `get_cart_contents_taxes()` and `get_shipping_taxes()`.
* Dev - Additional check for `WC()->payment_gateways` to be set, added in `add_gateways_fees()`.
* Dev - Additional checks for tax class to exist added (in Core and Info).
* Dev - Additional checks if product's price is zero added (in Info).
* Dev - Per products settings JS file updated.
* Dev - "WC tested up to" added to the plugin header.

= 2.3.1 - 26/08/2017 =
* Dev - Info - `%product_price_diff_percent%` replaceable value added.
* Dev - Wrapping div (class `alg_checkout_fees`) added to meta box settings.
* Dev - "General" settings restyled.

= 2.3.0 - 16/08/2017 =
* Dev - "Aelia Currency Switcher for WooCommerce" plugin currency conversion filter added to: `get_max_ranges()`, `min_cart_amount`, `max_cart_amount`, `min_fee`, `max_fee`, `min_fee_2`, `max_fee_2`.
* Dev - "Coupons Rule" options added.
* Dev - Code refactoring.
* Dev - Settings restyled.

= 2.2.2 - 27/07/2017 =
* Fix - Removed additional check if `add_gateways_fees()` has already been executed (was added in v2.2.1).
* Dev - "Delete All Plugin Data" option added.
* Dev - Code cleanup etc.

= 2.2.1 - 22/07/2017 =
* Fix - Per Product - `custom_atts` for fields (step etc.) fixed.
* Fix - Additional check if `add_gateways_fees()` has already been executed added (this prevents fees duplicating on some servers).

= 2.2.0 - 27/06/2017 =
* Dev - WooCommerce 3.x.x compatibility - Deprecated `get_price_excluding_tax()`, `get_price_including_tax()`, `get_display_price()` notices fixed.
* Dev - WooCommerce 3.x.x compatibility - Deprecated `get_formatted_variation_attributes()` notice fixed.
* Dev - WooCommerce 3.x.x compatibility - Deprecated `get_country()` notice fixed.
* Dev - "Add Taxes" option added for both global and per product fees.
* Dev - `load_plugin_textdomain()` moved from `init` hook to constructor.
* Dev - Plugin header updated ("Text Domain" and "Domain Path" added).
* Dev - Plugin link updated from <a href="http://coder.fm">http://coder.fm</a> to <a href="https://wpcodefactory.com">https://wpcodefactory.com</a>.

= 2.1.1 - 04/10/2016 =
* Fix - Bug when local and global fees have same title, fixed.
* Fix - Categories restrictions wrongly applied to per product fees, fixed.
* Dev - "Max Range Options" options section added to "General" settings.
* Dev - "Override Global Fee" (main and additional) option added to per product settings.
* Dev - "Minimum Fee Value", "Maximum Fee Value", "Minimum Additional Fee Value" and "Maximum Additional Fee Value" options added to both local and global fees.
* Dev - "Minimum Cart Amount" and "Maximum Cart Amount" options title modified.

= 2.1.0 - 20/08/2016 =
* Fix - Tax bug fixed when "Categories to include - Calculation type" is equal to "Only for selected products".
* Fix - Tax bug fixed in info.
* Fix - "Categories to include - Calculation type" with value "Only for selected products" fixed when displaying info.
* Dev - "Categories to exclude - Calculation type." option added.
* Dev - "Categories to include (additional fee)." and "Categories to exclude (additional fee)." options added.
* Dev - Version system added.
* Dev - Author changed.
* Dev - Plugin renamed.

= 2.0.2 - 06/08/2016 =
* Fix - `sanitize_title()` added to `add_gateway_fees_settings_hook()` and removed from `output_sections()`.
* Dev - Multisite support added.
* Dev - "Variable Products Info" option added.
* Dev - "Categories to include - Calculation type" option added to global fees.
* Dev - Language (POT) file added.
* Dev - Author changed.

= 2.0.1 - 10/03/2016 =
* Fix - Additional checks in `add_gateway_fees_settings_hook()`.

= 2.0.0 - 01/03/2016 =
* Fix - Checked tab in admin per product fees is marked now.
* Fix - Info on Single Product bugs fixed: for variable products; for percent fees.
* Fix - "General" section in admin settings menu is marked bold by default.
* Dev - `%product_title%`, `%product_variation_atts%` added.
* Dev - Info on Single Product - `[alg_show_checkout_fees_full_info]` and `[alg_show_checkout_fees_lowest_price_info]` shortcodes added.
* Dev - Info on Single Product - Lowest Price Info on Single Product Page added.
* Dev - Info on Single Product - `%gateway_fee_title%` and `%gateway_fee_value%` removed from info.
* Dev - "Add Product Title to Fee/Discount Title" option added to "General > Fees/Discounts per Product" settings.
* Dev - "Hide Gateways Fees and Discounts on Cart Page" option added to "General" settings.
* Dev - "Exclude Shipping" option added for both global and per product fees.
* Dev - "Title" option added for optional "Additional fee" (per product and global).
* Dev - "Customer Countries" (include / exclude) options added to global fees.
* Dev - "Product Categories" (include / exclude) options added to global fees.
* Dev - Compatibility with "Aelia Currency Switcher for WooCommerce" plugin added (for fixed fees; for percent fees compatibility was already there).
* Dev - "Fee Calculation (for Fixed Fees)" options (once / by product quantity) added to per product fees.
* Dev - "Fee Calculation (for Percent Fees)" options (for all cart / by product) added to per product fees.

= 1.3.0 - 27/10/2015 =
* Dev - Second optional fee added.

= 1.2.0 - 30/09/2015 =
* Dev - Checkout fees/discounts info on single product frontend page added.

= 1.1.0 - 04/09/2015 =
* Dev - Checkout fees/discounts on per product basis added.

= 1.0.0 - 29/08/2015 =
* Initial Release.

== Upgrade Notice ==

= 2.5.0 =
Main plugin file has been renamed, so plugin must be re-activated after update.

= 1.0.0 =
This is the first release of the plugin.
