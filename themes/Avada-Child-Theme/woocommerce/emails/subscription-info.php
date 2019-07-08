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
?>
<?php

if ( ! empty( $subscriptions ) ) : ?>
<h2><?php esc_html_e( 'Información de suscripción:', 'woocommerce-subscriptions' ); ?></h2>
<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:left;"><?php esc_html_e( 'Suscripción', 'woocommerce-subscriptions' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Fecha de Siguiente Limpieza', 'table heading',  'woocommerce-subscriptions' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Precio',  'table heading', 'woocommerce-subscriptions' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ( $subscriptions as $subscription ) : ?>
	 
	   <?php $sub_detail = $subscription->data; 
		
		$schem_active = $sub_detail['billing_period']."_".$sub_detail['billing_interval'];
	  
		?>
		
		<tr>
			<td class="td" scope="row" style="text-align:left;"><a href="<?php echo esc_url( ( $is_admin_email ) ? wcs_get_edit_post_link( $subscription->get_id() ) : $subscription->get_view_order_url() ); ?>"><?php echo sprintf( esc_html_x( '#%s', 'subscription number in email table. (eg: #106)', 'woocommerce-subscriptions' ), esc_html( $subscription->get_order_number() ) ); ?></a></td>
			<?php   
			$subscription_id = $subscription->get_id();
			
			
		
		
	 

				// IMPORTANT HERE: Get an instance of the WC_Subscription Object
				$subscription = new WC_Subscription( $subscription_id );
				$order_id = method_exists( $subscription, 'get_parent_id' ) ? $subscription->get_parent_id() : $subscription->order->id;
				$order = method_exists( $subscription, 'get_parent' ) ? $subscription->get_parent() : $subscription->order;
				$bookindate = get_post_meta( $subscription_id, 'order_date', true );
				$sren = get_post_meta( $order_id, '_subscription_renewal', true );
				
					$sschedule_end = get_post_meta( $subscription_id, '_schedule_end', true );
			
				
				
				wp_update_post(
					array (
						'ID'            => $subscription_id,
						'post_date'     => $bookindate,
						'post_date_gmt' => $bookindate
					)
				);				
				list($payment_date,$booking_date) = generate_next_order_dates($bookindate,$subscription->get_billing_interval(),$subscription->get_billing_period());
				$plusone_ago = spanish_date(date('F j, Y h:i a',(strtotime($sschedule_end) - 18000)));
			?>
			<td class="td" scope="row" style="text-align:left;"><?php echo $plusone_ago; ?></td>
			<!--<td class="td" scope="row" style="text-align:left;">
				<?php
				/*$subscription_id = $subscription->ID;

				// IMPORTANT HERE: Get an instance of the WC_Subscription Object
				$subscription = new WC_Subscription( $subscription_id );
				$order_id = method_exists( $subscription, 'get_parent_id' ) ? $subscription->get_parent_id() : $subscription->order->id;
				$order = method_exists( $subscription, 'get_parent' ) ? $subscription->get_parent() : $subscription->order;
				$bookindate = get_post_meta( $subscription_id, 'order_date', true );
				$sren = get_post_meta( $order_id, '_subscription_renewal', true );
				wp_update_post(
					array (
						'ID'            => $subscription_id,
						'post_date'     => $bookindate,
						'post_date_gmt' => $bookindate
					)
				);

				$bellingpe = get_post_meta( $subscription_id, '_billing_period', true );
				$interval = get_post_meta( $subscription_id, '_billing_interval', true );
				$schem = array();
				foreach($schem as $schemsname => $schems){

					if($schems == $activeschem){

						if($schemsname == 'Mensual'){
							//echo "test1"; die;
							$paiddate = get_post_meta( $order_id, '_paid_date', true );
							if(empty($sren)){
								$threedays_ago = date('Y-m-d', strtotime('+27 days', strtotime($bookindate)));
								update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
								echo $threedays_ago;
							}
						}

						if($schemsname == 'Bisemanal'){
							//echo "test2"; die;
							$paiddate = get_post_meta( $order_id, '_paid_date', true );
							if(empty($sren)){
								$threedays_ago = date('Y-m-d', strtotime('+12 days', strtotime($bookindate)));
								update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
								echo $threedays_ago;
							}

						}

						if($schemsname == 'Semanal'){
							//echo "test3"; die;
							$paiddate = get_post_meta( $order_id, '_paid_date', true );
							if(empty($sren)){
								$threedays_ago = date('Y-m-d', strtotime('+4 days', strtotime($bookindate)));
								update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
								echo $threedays_ago;
							}
						}

					}

				}*/



				/*echo esc_html( ( 0 < $subscription->get_time( 'end' ) ) ? date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) : _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-subscriptions' ) ); */?></td>-->
			<td class="td" scope="row" style="text-align:left;"><?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?></td>
		</tr>
	<?php endforeach; ?>
</tbody>
</table>
<p>Los detalles de suscripción los puedes editar en cualquier momento a través de tu cuenta o si tienes dudas contáctanos.</p>
<?php endif; ?>
