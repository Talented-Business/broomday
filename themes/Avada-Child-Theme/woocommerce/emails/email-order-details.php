<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text_align = is_rtl() ? 'right' : 'left';

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php if ( ! $sent_to_admin ) : ?>
	<h2><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></h2>       
<?php else : ?>
	<h2><a class="link" href="<?php echo esc_url( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ); ?>"><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ); ?>)</h2>
<?php endif; ?>
        
<h2><?php _e( 'Order details', 'woocommerce' ); 
global $wpdb;
$job = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items " .
"WHERE order_id = " . $order->get_id() . " GROUP BY order_id; ";
$job_detail = $wpdb->get_results($job);
$order_item_id = $job_detail[0]->order_item_id;
?></h2>
<ul>
    <li><strong><?php _e("Fecha de Servicio", "Avada-child");?></strong> <span class="text"><?php echo date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $order->get_id(), 'order_date', true ) ) )."  ".date('h:i a',strtotime(get_post_meta( $order->get_id(), 'order_date', true ))); ?></span></li>
    <li><strong><?php _e("Nombre Completo", "Avada-child");?></strong> <span class="text"><?php echo get_post_meta($order->get_id(),"_billing_first_name",true)." ".get_post_meta($order->get_id(),"_billing_last_name",true);; ?></span></li>
    <li><strong><?php _e("Phone", "woocommerce");?></strong> <span class="text"><?php echo get_post_meta($order->get_id(),"_billing_phone",true)." / ".get_post_meta($order->get_id(),"_billing_cell",true);; ?></span></li>
    <li><strong><?php _e("Email", "woocommerce");?></strong> <span class="text"><?php echo $order->get_billing_email(); ?></span></li>
    <li><strong><?php _e("Address", "woocommerce");?></strong> <span class="text"><?php echo get_post_meta($order->get_id(),"_billing_address_1",true); ?></span></li>
    <li><strong><?php _e("Referencias Adicionales", "Avada-child");?></strong> <span class="text"><?php echo get_post_meta($order->get_id(),"billing_comments",true); ?></span></li>
</ul>
<br/>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Price', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php echo wc_get_email_order_items( $order, array(
			'show_sku'      => $sent_to_admin,
			'show_image'    => false,
			'image_size'    => array( 32, 32 ),
			'plain_text'    => $plain_text,
			'sent_to_admin' => $sent_to_admin,
		) ); ?>
	</tbody>
	<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++;
					?><tr>
						<th class="td" scope="row" style="text-align:<?php echo $text_align; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $total['label']; ?></th>
						<td class="td" style="text-align:<?php echo $text_align; ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $total['value']; ?></td>
					</tr>
				
					<?php
				}
			}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>