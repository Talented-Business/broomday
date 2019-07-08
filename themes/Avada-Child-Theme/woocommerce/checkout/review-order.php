<?php
	/**
	 * Review order table
	 *
	 * @author 		WooThemes
	 * @package 	WooCommerce/Templates
	 * @version     2.3.0
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
?>
<div class="shop_table woocommerce-checkout-review-order-table rahul-does">
    <?php  
    /*$cart_contents = array();
    foreach ( WC()->cart->get_cart_contents() as $cart_item_key => $cart_item ) {
        if($cart_item['product_id'] == $output['product_service']){
            $cart_item['extra_services'] = WC()->session->get('extra_services');
        }
        $cart_contents[ $key ] = $cart_item;
    }
    WC()->cart->set_cart_contents($cart_contents);*/
	
    do_action( 'woocommerce_review_order_before_cart_contents' );
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		
		
		//$item = get_cart_for_session();
		//print_r($item);
		
		//echo "<pre>";
          // print_r(WC()->cart->get_cart());
            $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			  
            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                    $dd = get_post_meta($cart_item["product_id"],'_wcsatt_schemes',true);
                    $label = maybe_unserialize($dd);	
                    $subscription_schemes  = WCS_ATT_Cart::get_subscription_schemes( $cart_item );
                    $index = array_search($cart_item['wcsatt_data']['active_subscription_scheme'],array_keys($subscription_schemes));
                    if($index!==false){
                        $title = "Plan ".$label[$index]['subscription_label']." de ".$_product->get_name();
                    }else{
                        $title = "Orden de ".$_product->get_name();
                    }
                    ?>
                    <h4 class ="order-title"><?=$title; ?></h4>
                    <?php if($cart_item["extra_services"]!=""){
                        foreach($cart_item["extra_services"] as $ss => $services){ 
                        ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <span><?php echo $services['title'];?></span>
                            </div>
                        </div>
                    <?php } } ?>
                    

                    <?php if(get_post_meta($cart_item["product_id"],'_select_bedroom_filter',true)==1){ ?>
                        <div class="row">
                            <div class="col-xs-6">
                                <span><?php _e("Cuartos", "Avada");?></span>
                            </div>
                            <div class="col-xs-6">
                                <label><?php echo $cart_item['bedroom'] ?></label>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if(get_post_meta($cart_item["product_id"],'_select_bedroom_filter',true)==1){ ?>
                        <div class="row">
                            <div class="col-xs-6">
                                <span><?php _e("Baños", "Avada");?></span>
                            </div>
                            <div class="col-xs-6">
                                <label><?php echo $cart_item['bathroom'] ?></label>
                            </div>
                        </div>
                    <?php } ?>

                    <?php
                         if (isset($cart_item['service_hour'])) {
                                $sh = explode(':',$cart_item['service_hour']);
                                $sh_h = $sh[0];
                                $sh_m = $sh[1];
                                if($sh_m == ''){
                                    $sh_m = 0;
                                }
                               $total_extra_minutes = 0;
                               $total_extra_minutes = $total_extra_minutes + $sh_m;
                               if($cart_item["extra_services"]!=""){
                                   foreach($cart_item["extra_services"] as $ss => $services){
                       
                                       if($services['id'] == 1573 && $cart_item['wcsatt_data']['active_subscription_scheme'] != 0)
                                            { 
                                               $cart_item["extra_services"][$ss]['title'] = "Productos de Limpieza (Free)";
                                               $cart_item["extra_services"][$ss]['charges'] = 0;
                                            }
                       
                                       $total_extra_minutes = $total_extra_minutes + $services['extra_time'];
                                   }
                                   if($total_extra_minutes>=60){
                                       $total_extra_hours = intdiv($total_extra_minutes, 60);
                                       $total_extra_minutes = ($total_extra_minutes % 60);
                                   }
                               }
                               $total_hours = 0;
                               $total_minutes = '';
                               if($total_extra_hours > 0){
                                   $total_hours = $sh_h + $total_extra_hours;
                               }
                               else{
                                   $total_hours = $sh_h;
                               }
                               if($total_extra_minutes!=0){
                                   $total_hours = $total_hours.':'.$total_extra_minutes;
                               }
                        }
                    ?>

                    <?php if(isset($cart_item['date'])){ ?>
                        <div class="row">

                            <div class="col-xs-6">
                                <label>Fecha</label>
                            </div>

                            <div class="col-xs-6">
                                <span><?php echo date_i18n('d M, Y',strtotime($cart_item['date'])); ?></span>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if(isset($cart_item['time'])){ ?>
                        <div class="row">

                            <div class="col-xs-6">
                                <label>Hora</label>
                            </div>

                            <div class="col-xs-6">
                                <span><?php echo $cart_item['time'] ?></span>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if(isset($total_hours)){ ?>
                        <div class="row">

                            <div class="col-xs-6">
                                <label>Horas</label>
                            </div>

                            <div class="col-xs-6 text-left">
                                <span><?=$total_hours;?> Horas</span>
                            </div>
                        </div>
                    <?php } ?>
                    
                    <div class="row">
                        <div class="col-xs-6">
                            <label><?php // Avada edit ?>
                                    <?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ); ?>
                                 <?php // echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times; %s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); ?>
                                    <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity"></strong>', $cart_item, $cart_item_key ); ?>
                                    <?php //echo WC()->cart->get_item_data( $cart_item ); ?></label>
                        </div>

                        <div class="col-xs-6">
                            <span><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></span>
                        </div>
                    </div>
        <?php
                }
		
            }   
        do_action( 'woocommerce_review_order_after_cart_contents' );
    ?>
    <div class="col-xs-12">
    	<hr>
    </div>
    <div class="row customcoupon">
        <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
        <div class="msg" style="text-align:center"></div>
        <div class="checkout_coupon" method="post" >

	<p class="form-row form-row-first">
		<input name="coupon_code" class="input-text" placeholder="Ingresa tu Cupón" id="coupon_code" value="" type="text">
	</p>

	<p class="form-row form-row-last">
		<input class="button" name="apply_coupon" value="Aplicar" onclick="myfunc(this.value)" type="button">
	</p>
    <div class="clear"></div>
    
</div>

    </div>
    <div class="col-xs-12">
		<input type="hidden" value="<?php echo $_product->get_regular_price()?>" id="gttt_reg_prc">
    	<hr>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <label><?php _e( 'Subtotal', 'woocommerce' ); ?></label>
        </div>
        <div class="col-xs-6">
            <span><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>
    </div>
    <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<tr class="cart-discount coupon-<?php echo esc_attr( $code ); ?>">
			<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
			<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
		</tr>
    <?php endforeach; ?>
    <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
            <?php wc_cart_totals_shipping_html(); ?>
    <?php endif; ?>
    <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>  
	
    <div class="row <?php if($fee->amount<= 0){ echo 'hidden'; } ?>">
        <div class="col-xs-6 ">
            <label><?php echo esc_html( $fee->name ); ?></label>
        </div>
		<?php // if($fee->name == 'Productos de Limpieza') { ?>
        <!--<div class="col-xs-6">
            <span class="finalprice"><?php //echo $content_vlue; ?></span>
        </div>-->
		<?php //} else {?>
		 <div class="col-xs-6">
            <span><?php wc_cart_totals_fee_html( $fee ); ?></span>
        </div>
		<?php //} ?>
    </div>
    <?php endforeach; ?>
    <?php if ( WC()->cart->tax_display_cart === 'excl' ) : ?>
        <?php if ( get_option( 'woocommerce_tax_total_display' ) === 'itemized' ) : ?>
                <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                    <div class="row">
                        <div class="col-xs-6 tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
                            <label><?php echo esc_html( $tax->label ); ?></label>
                        </div>
                        <div class="col-xs-6">
                            <span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
	
        <?php else : ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <label><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></label>
                        </div>
                        <div class="col-xs-6">
                            <span><?php echo wc_price( WC()->cart->get_taxes_total() ); ?></span>
                        </div>
                    </div>
        <?php endif; ?>
    <?php endif; ?>
     <div class="col-xs-12">
    	<hr>
    </div>
	
    <?php  do_action( 'woocommerce_review_order_before_order_total' ); ?>
            
        <div class="row ">
            <div class="col-xs-6">
                <label class="blue-text"><?php _e( 'Por Limpieza', 'woocommerce' ); ?></label>
            </div>
            <div class="col-xs-6">
				
                <span class="blue-text"><?php wc_cart_totals_order_total_html(); ?></span>
            </div>
        </div> 
	 
    <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
	
</div>
<style>
	.shop_table.woocommerce-checkout-review-order-table.rahul-does .col-xs-6:nth-child(2) {
		text-align: left;
	}
	span.mysub {
    	margin-right: 0;
    	width: calc(100% - 50%) !important;
    	text-align: left !important;
	    padding-left: 15px;
	}
	.next-order-date {
	   clear: both;
	}

</style>