<?php

/**
 * RAF & Woocommerce Subscription 
 *
 * @since    2.0.0
 */
$gens_subs = get_option("gens_raf_subscription");
if(isset($gens_subs) && $gens_subs === "yes") {
	add_filter('wcs_new_order_created','gens_renewal_order_created', 10, 2 );
}

function gens_renewal_order_created($order, $subscription)
{
	$gens_subs_coupons = get_option("gens_raf_subscription_all_coupons");
	$user_id = $order->get_user_id();
	$user_info = get_userdata($user_id);
	$user_email = $user_info->user_email;
	// Prevent empty emails.
	if(empty($user_email)) {
    	return $order;
	}

	$order_total = $order->get_total() - $order->get_total_tax();
	$args = array(
		'posts_per_page'   => 999,
	    'post_type'        => 'shop_coupon',
	    'post_status'      => 'publish',
		'meta_query' => array(
			'relation' => 'AND',
		    array(
			  'key' => 'customer_email',
			  'value' => $user_email,
              'compare' => 'LIKE'
		    ),
		    array(
		    	'relation' => 'OR',
		    	array(
			    	'key' => 'usage_count',
			    	'value' => '0',
			    	'compare' => 'LIKE'
			    ),
		    	array(
			    	'key' => 'usage_count',
			    	'compare' => 'NOT EXISTS'
		    	)
		    )
		),
	);
	$coupons = get_posts( $args );
	
	if(empty($coupons)) {
		return $order;
	}

	// If you want to apply all coupons, up to renewal price.
	if(isset($gens_subs_coupons) && $gens_subs_coupons === "yes") {
		$total_value = 0;
		foreach ($coupons as $coupon) {
			$coupons_obj = new WC_Coupon($coupon->ID);
			$total_value = $total_value + $coupons_obj->get_amount();
			if($total_value <= $order_total) {
				gens_renewal_order_apply_coupon($order, $coupons_obj);					
			}
		}
	} else {
		$coupons_obj = new WC_Coupon($coupons[0]->ID);
		gens_renewal_order_apply_coupon($order, $coupons_obj);
	}

	return $order;
}

function gens_renewal_order_apply_coupon($order, $coupons_obj)
{
	$order_total = $order->get_total() - $order->get_total_tax();
	$amount = $coupons_obj->get_amount();
	$type = $coupons_obj->get_discount_type(); // fixed_cart or percent
	$coupons_obj->set_usage_count(1);
	$coupons_obj->save();

	if( $type == "percent") {
		$discount = $order_total * ( $amount / 100);
	} else {
		$discount = $amount;
	}

	// Check to make sure discount is not bigger than order
	if ( $amount > $order_total ) {
		$discount = $order_total;
	}

	$item = new WC_Order_Item_Fee();
	$item->set_props( array(
	    'name'      => __("Referral applied","gens-raf"),
	    'tax_class' => NULL,
	    'total'     => -$discount,
	    'total_tax' => 0,
	    'tax_status'=> 'none',
	    'order_id'  => $order->get_id(),
	) );
	$item->save();
	$order->add_item( $item );
	$order->update_taxes();
	$order->calculate_totals( true );
}


/**
 * Prevent gens referral code copying during renewals. 
 * https://docs.woocommerce.com/document/subscriptions/develop/filter-reference/
 *
 * @since    2.0.9
 */
function gens_prevent_referral_copying( $order_meta_query, $to_order, $from_order ) {

    $order_meta_query .= " AND `meta_key` NOT IN ('_raf_id', '_wpgens_raf_id', '_raf_meta', '_wpgens_raf_meta')";

    return $order_meta_query;
}
add_filter( 'wcs_renewal_order_meta_query', 'gens_prevent_referral_copying', 10, 3 );
