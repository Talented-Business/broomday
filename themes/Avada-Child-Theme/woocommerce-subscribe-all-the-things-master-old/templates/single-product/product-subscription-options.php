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
		?><h3><?php
			_e( 'Choose a subscription plan:', 'woocommerce-subscribe-all-the-things' );
		?></h3><?php
	}

	?><ul class="wcsatt-options-product"><?php
	
	$dd = get_post_meta($product_id,'_wcsatt_schemes',true);
       $label = maybe_unserialize($dd);
      // echo "<pre>"; print_r($label); echo "</pre>";
     //  foreach($label as $labels){
         //  $labels['subscription_period_interval'].'_'.$labels['subscription_period']
         //  $myarray[]
     //  }
       //$option[ 'description' ]
        $i=-1;
		foreach ( $options as $option ) {
		   // echo "<pre>"; print_r($option); echo "</pre>";
		    //$lbl = '';
		    if($option['class'] == 'one-time-option')
		    { 
		        $lbl = 'No Subscription';
		        
		    }else
		    {
		        
		        $lbl = $label[$i]['subscription_label'];
		        
		    }
		    
			?><li class="<?php echo esc_attr( $option[ 'class' ] ); ?>">
				<label>
					<input type="radio" name="convert_to_sub_<?php echo absint( $product_id ); ?>" data-custom_data="<?php echo esc_attr( json_encode( $option[ 'data' ] ) ); ?>" value="<?php echo esc_attr( $option[ 'value' ] ); ?>" <?php checked( $option[ 'selected' ], true, true ); ?> />
					<?php echo '<span class="' . esc_attr( $option[ 'class' ] ) . '-details">' . $lbl . '</span>'; ?>
				</label>
			</li><?php
			$i++;
		}
	?></ul>
</div>
