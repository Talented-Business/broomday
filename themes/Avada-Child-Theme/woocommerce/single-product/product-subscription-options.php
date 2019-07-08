<?php
/**
 * Single-Product Subscription Options Template.
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/product-subscription-options.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 2.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wcsatt-options-wrapper" <?php echo count( $options ) === 1 ? 'style="display:none;"' : '' ?>><?php

	
	
	
	 if ( $prompt ) {
		echo $prompt;
	} else {
		?><h4><?php
			_e( 'Choose a subscription plan:', 'woocommerce-subscribe-all-the-things' );
		?></h4>
			<p><?php
			_e( "¡Al suscribirte a un plan de limpieza recurrente obtendrás grandes beneficios! </br>
<strong>Nota:</strong> Podrás cambiar de profesional ó cancelar la suscripción en cualquier momento, totalmente gratis.", 'woocommerce-subscribe-all-the-things' );
		?></p>
		<?php
	}

	?><ul class="wcsatt-options-product">
	<?php
		global $woocommerce;
       $charge = '';
       $items = $woocommerce->cart->get_cart();
	?>
	<?php
	
	$dd = get_post_meta($product_id,'_wcsatt_schemes',true);
       $label = maybe_unserialize($dd);
      // echo "<pre>"; print_r($label); echo "</pre>";
     //  foreach($label as $labels){
         //  $labels['subscription_period_interval'].'_'.$labels['subscription_period']
         //  $myarray[]
     //  }
       //$option[ 'description' ]
       
	  $_product = wc_get_product( $product_id );
	
        $activeschem ='';
	   
       foreach($items as $item){
		   		$activeschem .= $item['wcsatt_data']['active_subscription_scheme'];

                $charge = $item['total_charges']+ $_product->get_regular_price();
			
	   }
       
        $i=-1;
		foreach ( $options as $option ) {
		   // echo "<pre>"; print_r($option); echo "</pre>";
		    //$lbl = '';
		  
		    if($option['class'] == 'one-time-option')
		    { 
		        $lbl = 'Limpieza Única';
		        $disprice ='<span class="discountprice">'.get_woocommerce_currency_symbol(get_option('woocommerce_currency')).''.number_format($charge,2). '</span>';
		        
		    }else
		    {
		        
		        $lbl = $label[$i]['subscription_label'];
		          $discount = $option['data']['subscription_scheme']['discount'];
		          $selling = $charge - ($charge * ($discount / 100));
					
				
		          $dissym = '';
		          if($discount!=''){
		              $dissym .= '%';
		          }
		        $disecho = '<span class="discountsub">Ahorrar '.$discount.''.$dissym.'</span>';
		        $disprice ='<span class="discountprice">'.get_woocommerce_currency_symbol(get_option('woocommerce_currency')).''.number_format($selling,2). '</span>';
		    }
			 $myactive = '';
			if($activeschem == '')
			{
			 	$myactive = 0;
			}
			
			?><li class="<?php echo esc_attr( $option[ 'class' ] ); ?><?php echo $activeschem == $option[ 'value' ]?' myactive':($option[ 'value' ] ==$myactive ? ' myactive':''); ?>">
				<label>
					<input type="radio" name="convert_to_sub_<?php echo absint( $product_id ); ?>" data-custom_data="<?php echo esc_attr( json_encode( $option[ 'data' ] ) ); ?>" value="<?php echo esc_attr( $option[ 'value' ] ); ?>"  <?php echo $activeschem == $option[ 'value' ]?'checked="checked"':($option[ 'value' ] ==$myactive ? 'checked="checked"':''); ?> />
					<?php if(!empty($disecho)){ echo $disecho; } ?>
					<?php echo '<span class="' . esc_attr( $option[ 'class' ] ) . '-details">'. $lbl .'</span>'; ?>
					<?php if(!empty($disprice)){ echo $disprice; } ?>
				</label>
			</li><?php
			$i++;
		}
	?></ul>
	<hr/>
	<?php if ( ! is_user_logged_in()) : ?>
		<p style="display:none;" id="login_msg">
			<label>Para tener una suscripción debe ser un usuario de Broomday. Si no tienes cuenta puedes registrarte gratuitamente en pocos segundos.&emsp;</label>
			<button class="hidden  single_add_to_cart_button">Ingresar / Registro</button>
			<a class=" single_add_to_cart_button  button" href=<?=home_url('registro')?>>Ingresar / Registro</a>
		<p>
	<?php endif; ?>
</div>
