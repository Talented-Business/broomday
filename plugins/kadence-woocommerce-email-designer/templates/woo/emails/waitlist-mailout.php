<?php
/**
 * Waitlist Mailout email
 *
 * @author         Neil Pie
 * @package        WooCommerce_Waitlist/Templates/Emails
 * @version        1.7.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$eobject         = new stdClass();
	$eobject->id     = 'woocommerce_waitlist_mailout';
	$eobject->object = get_product( $product_id );
do_action( 'woocommerce_email_header', $email_heading, $eobject ); ?>

<?php
/**
 * @hooked Kadence_Woomail_Designer::email_main_text_area
 */
do_action( 'kadence_woomail_designer_email_text', $eobject );
?>

<p>
	<?php echo sprintf( $back_in_stock_text, $product_title, get_bloginfo( 'name' ) ) . ". ";
	echo $you_have_been_sent_text; ?>
</p>
<p>
	<?php echo sprintf( $purchase_text, $product_title, '<a href="' . $product_link . '">' . $product_link . '<a>' ); ?>
</p>

<?php if ( WooCommerce_Waitlist_Plugin::persistent_waitlists_are_disabled( $product_id ) && ! $triggered_manually ) {
	echo '<p>' . $remove_text . '</p>';
}
do_action( 'woocommerce_email_footer', $eobject ); ?>
