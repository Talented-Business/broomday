<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="woocommerce-order">

	<?php if ( $order ) : ?>

		<?php // ThemeFusion edit for Avada theme: add Avada specific classes avada-thank-you woocommerce-content-box full-width. ?>
		<div class="woocommerce-order avada-thank-you woocommerce-content-box full-width">

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>
			
			
			<?php // ThemeFusion edit for Avada theme: change to h2 tag ?>
			<h2><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></h2>
                        <p><?php _e("Please Review information below is correct,if you want to modify your order details or booking date fell free to contact us.", "Avada");?>
</p>
			<?php
            //echo "<pre>";print_r($order);
            //echo $order->billing_first_name;
            global $wpdb;
            $order_query = "SELECT * " ."FROM {$wpdb->prefix}woocommerce_order_items
                       WHERE order_id = " . $order->ID . " GROUP BY order_id; ";
            $order_details = $wpdb->get_results($order_query);
            $booking_date=wc_get_order_item_meta( $order_details[0]->order_item_id, 'date', true );
            ?>
            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
                <!--<li class="woocommerce-order-overview__order order">-->
                <li class="woocommerce-order-overview__order">
					<?php _e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_order_number(); ?></strong>
				</li>
                                
				<li class="woocommerce-order-overview__total total">
					<?php _e( 'Total de orden:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); ?></strong>
				</li>
			<?php
			foreach ( $order->get_items() as $item_id => $item ) :
				?>
				<li class="woocommerce-order-overview__date date">
					Horas Contratadas
					<strong><?php echo wc_get_order_item_meta( $item_id, 'service_hour', true )." horas"; ?></strong>
				</li>
			<?php endforeach; ?>
				<li class="woocommerce-order-overview__date date">
					Servicios Adicionales:
					<?php
					$names = array();
					foreach( $order->get_items('fee') as $item_id => $item ){
						$names[] = $item->get_name();
					}			
					?>
					<strong><?=implode(',',$names); ?></strong>
                </li>                        
                                
				<li class="woocommerce-order-overview__date date">
					<?php _e( 'Booking Date:', 'Avada' ); ?>
					<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $order->ID, 'order_date', true ) ) )."  ".date('h:i a',strtotime(get_post_meta( $order->ID, 'order_date', true ))); ?></strong>
				</li>
				<li class="woocommerce-order-overview__date date">
					<?php _e( 'Full Name:', 'Avada' ); ?>
					<strong><?php echo  $order->billing_first_name." ".$order->billing_last_name; ?></strong>
				</li>
                                <li class="woocommerce-order-overview__date date">
					<?php _e( 'Phone:', 'Avada' ); ?>
					<strong><?php echo $order->billing_phone."/".$order->billing_cell; ?></strong>
				</li>
                                
                                <li class="woocommerce-order-overview__date date">
					<?php _e( 'Email:', 'woocommerce' ); ?>
					<strong> <?php echo get_post_meta($order->ID,"_billing_email",true); ?></strong>
				</li>
                                <li class="woocommerce-order-overview__date date">
					<?php _e( 'Address:', 'Avada' ); ?>
					<strong><?php echo $order->billing_address_1; ?></strong>
				</li>
                                
                                <li class="woocommerce-order-overview__date date">
					<?php _e( 'Extra Address:', 'Avada' ); ?>
					<strong><?php echo get_post_meta($order->id,'billing_comments',true); ?></strong>
				</li>

            	<?php if ( is_object( $order ) ) : ?>
            		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
            
            	<?php endif; ?>

			
			</ul>
			<div class="clear"></div>

		<?php endif; ?>

		<?php if ( is_object( $order ) ) : ?>
                    
                    <div class="warning-msg">
                         <!--<i class="fa fa-warning"></i>-->
			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
                    </div>
		<?php endif; ?>

	</div>

	<?php else : ?>

		<?php // ThemeFusion edit for Avada theme: change to h2 tag ?>
		<h2><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></h2>
		</div>

	<?php endif; ?>

</div>
