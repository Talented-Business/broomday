<?php
/**
 * Subscription information template
 *
 * @author  Brent Shepherd / Chuck Mac
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 1.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 * EDIT NOTES FOR KADENCE WOOMAIL DESIGNER
 * ADDED: Separator spans.
 */
?>
<?php if ( ! empty( $subscriptions ) ) : ?>
	<div style="clear:both; height:1px;"></div>
<h2><?php esc_html_e( 'Subscription Information:', 'kadence-woocommerce-email-designer' ); ?></h2>
<div style="margin-bottom: 40px;">
<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:left;"><?php esc_html_e( 'Subscription', 'kadence-woocommerce-email-designer' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Start Date', 'table heading',  'kadence-woocommerce-email-designer' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'End Date', 'table heading',  'kadence-woocommerce-email-designer' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Price',  'table heading', 'kadence-woocommerce-email-designer' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ( $subscriptions as $subscription ) : ?>
		<tr>
			<td class="td" scope="row" style="text-align:left;"><a href="<?php echo esc_url( ( $is_admin_email ) ? wcs_get_edit_post_link( $subscription->get_id() ) : $subscription->get_view_order_url() ); ?>"><?php echo sprintf( esc_html_x( '#%s', 'subscription number in email table. (eg: #106)', 'kadence-woocommerce-email-designer' ), esc_html( $subscription->get_order_number() ) ); ?></a></td>
			<td class="td" scope="row" style="text-align:left;"><?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'date_created', 'site' ) ) ); ?></td>
			<td class="td" scope="row" style="text-align:left;"><?php echo esc_html( ( 0 < $subscription->get_time( 'end' ) ) ? date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) : _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'kadence-woocommerce-email-designer' ) ); ?></td>
			<td class="td" scope="row" style="text-align:left;"><?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?></td>
		</tr>
	<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
