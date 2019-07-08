<?php
/**
 * Subscription details table
 *
 * @author  Prospress
 * @package WooCommerce_Subscription/Templates
 * @since 2.2.19
 * @version 2.2.19
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<table class="shop_table subscription_details">
	
	<tr>
		<td><?php esc_html_e( 'Estado', 'woocommerce-subscriptions' ); ?></td>
		<td><?php echo esc_html( wcs_get_subscription_status_name( $subscription->get_status() ) ); ?></td>
	</tr>
	<!--<tr>
		<td><?php echo esc_html_x( 'Fecha de inicio', 'table heading',  'woocommerce-subscriptions' ); ?></td>
		<td><?php echo esc_html( $subscription->get_date_to_display( 'date_created' ) ); ?></td>
	</tr>-->
	<?php
	
	
	//$date = get_post_meta($subscription->id,"order_date",true);
	
	$date = $subscription->get_date('end');
	
	$next_payment = $subscription->get_date('next_payment');
	
	
	//$next_payment = date('d-m-y h:i a', strtotime($date));
	//$next_payment = date('d-m-y h:i a', strtotime($date));
	//$next_book = date('d-m-y h:i a', strtotime('+3 days', strtotime($date)));
	//echo $date;die; ?>
	<tr>
		<td><?php echo esc_html_x( 'Siguiente fecha de pago', 'table heading',  'woocommerce-subscriptions' ); ?></td>
		<td><?php echo spanish_date(date('F j, Y h:i a', strtotime($next_payment.' +5'))); ?></td>
	</tr>
	<?php foreach ( array(
		//'next_payment'            => _x( 'Siguiente fecha de pago', 'admin subscription table header', 'woocommerce-subscriptions' ),
		//'last_order_date_created' => _x( 'Next Booking Date', 'admin subscription table header', 'woocommerce-subscriptions' ),
		'schedule_next_payment' => _x( '
Fecha / hora de la próxima reserva', 'admin subscription table header', 'woocommerce-subscriptions' ),
		//'next_booking_date'       => _x( 'Next booking date', 'admin subscription table header', 'woocommerce-subscriptions' ),
		//'end'                     => _x( 'Fecha final', 'table heading', 'woocommerce-subscriptions' ),
		//'trial_end'               => _x( 'Trial End Date', 'admin subscription table header', 'woocommerce-subscriptions' ),
		) as $date_type => $date_title ) : ?>
		
		<?php $newDate = spanish_date(date('F j, Y h:i a', strtotime($date.' +5')));?>
		<?php if ( ! empty( $date ) ) : ?>
			<tr>
				<td><?php echo esc_html( $date_title ); ?></td>
				<td><?php echo $newDate; ?></td>
			</tr>
		<?php endif; ?>
	<?php endforeach; ?>
<!--	<tr>
		<td><?php echo esc_html_x( 'Next Booking Date', 'table heading',  'woocommerce-subscriptions' ); ?></td>
		<td><?php echo esc_html( $subscription->get_date_to_display( 'schedule_next_payment' ) ); ?></td>
	</tr>-->
	<?php do_action( 'woocommerce_subscription_before_actions', $subscription ); ?>
	<?php $actions = wcs_get_all_user_actions_for_subscription( $subscription, get_current_user_id() ); ?>
	<?php if ( ! empty( $actions ) ) : ?>
		<tr>
			<td><?php esc_html_e( 'Acción', 'woocommerce-subscriptions' ); ?></td>
			<td class="subcription-edit-1">
				<style>
					.subcription-edit-1 .button{padding: 9px !important; background-color: #40ade0 !important;border-radius: 3px;}
					.edit-button{margin-left: 5px !important; top: 2px !important;}
					</style>
				<?php //foreach ( $actions as $key => $action ) : ?>
					
				
					<?php echo do_action( 'woocommerce_my_subscriptions_actions', $subscription ); ?>
				<?php //endforeach; ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php do_action( 'woocommerce_subscription_after_actions', $subscription ); ?>
</table>
<?php 
$order = method_exists( $subscription, 'get_parent' ) ? $subscription->get_parent() : $subscription->order;
$order = wc_get_order( $order->get_id() );


$extra_service = "";
foreach( $order->get_items('fee') as $item_id => $item ){
								
							

						
							 $extra_service .= $item->get_name().", ";
							
							
						}
							
						


?>
<?php if ( $notes = $subscription->get_customer_order_notes() ) :
	?>
	<h2><?php esc_html_e( 'Subscription Updates', 'woocommerce-subscriptions' ); ?></h2>
	<ol class="commentlist notes">
		<?php foreach ( $notes as $note ) : ?>
		<li class="comment note">
			<div class="comment_container">
				<div class="comment-text">
					<p class="meta"><?php echo esc_html( date_i18n( _x( 'l jS \o\f F Y, h:ia', '
fecha en la lista de actualizaciones de suscripción. Será localizado', 'woocommerce-subscriptions' ), wcs_date_to_time( $note->comment_date ) ) ); ?></p>
					<div class="description">
						<?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
					</div>
	  				<div class="clear"></div>
	  			</div>
				<div class="clear"></div>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>
