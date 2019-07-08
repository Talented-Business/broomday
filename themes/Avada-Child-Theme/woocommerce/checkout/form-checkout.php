<?php

/**

 * Checkout Form

 *

 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.

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

 * @version     2.3.0

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}





//https://maps.googleapis.com/maps/api/js?key=AIzaSyDc3rlwwWUHNOFL-jOOk9WilLBmhHtNDHU&signed_in=true&callback=initMap

wp_enqueue_script('child_theme_script_handle', "https://maps.googleapis.com/maps/api/js?key=AIzaSyBmlFvgfwzMpTlMMA8AyZQym9YMZRxdUAg&libraries=places", array('jquery'));

wp_enqueue_script('child_theme_mapscript_handle', get_stylesheet_directory_uri() . "/map.js", array('jquery'));





wc_print_notices();



do_action( 'woocommerce_before_checkout_form', $checkout );



// If checkout registration is disabled and not logged in, the user cannot checkout

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {

	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );

	return;

}



?>

<style>

@media only screen and (max-width: 960px){

.products li{

    float: left !important;

    margin-left: 0px !important;

    margin-right: 0px !important;

}

}

.ul.wcsatt-options li, ul.wcsatt-options-cart li, ul.wcsatt-options-product li{

   /* display: inline;
    background-color: #f0f9fe;
    margin-left: 12px;
    padding: 10px;
    cursor: pointer;*/

   display: inline-flex;
    background-color: #fff;
    margin-left: 6px;
    padding: 6px;
    cursor: pointer;
    width: 24%;
    border-radius: 7px;
    text-align: center;

}

ul.wcsatt-options-product {
    padding: 0px;
}

.subscription-option-details{

    cursor: pointer;

}

.one-time-option-details{

    cursor: pointer;

}

.one-time-option{

    /*display:none !important;*/

}

.subscription-option>label>input[type="radio"]{

    display:none;

}

.one-time-option>label>input[type="radio"]{

    display:none;

}

.subscription-details{

    cursor: pointer;

}

.subscription-price>.subscription-details{

    display: none;

}

.myactive{

    background-color: #fff !important;
    color: #333 !important;

}

.woocommerce-checkout-review-order-table>font{

    display:none !important;

}

.woocommerce-checkout-review-order-table>strong{

    display:none !important;

}
.woocommerce-message{
    display:none;
}
	.popup_login{
		background: #3CADE0 !important;


		border-radius: 5px;
		box-shadow: 2px 2px 3px 1px #ccc;
     }
	}

</style>

<style>
    	#wait {
    background: url("http://www.aveva.com/Images/ajax-loader.gif") no-repeat scroll center center #FFF;
    position: fixed;
    height: 100%;
    width: 100%;
    background-color: rgba(255, 255, 255, .7);
	z-index: 99999;
	top:0;
}
.subscription-option label{
    width:100%;
}
.discountprice{
    cursor:pointer;
}
.additional_services
{
	cursor: pointer;
}
</style>
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" style="display:none;">

	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table cart woocommerce-cart-form__contents" cellspacing="0">

		<tbody>

			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );



				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

					?>

					<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-price">

							<?php

								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );

							?>

						</td>

					</tr>

					<?php

				}

			}

			?>

			<tr class="avada-cart-actions">

				<td colspan="6" class="actions">

					<input type="submit" class="button trigger-sub" name="update_cart" value="<?php esc_attr_e( 'Subsription', 'woocommerce' ); ?>" />



					<?php //do_action( 'woocommerce_cart_actions' ); ?>



					<?php wp_nonce_field( 'woocommerce-cart' ); ?>

				</td>

			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>

		</tbody>

	</table>



	<?php do_action( 'woocommerce_after_cart_table' ); ?>



</form>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" >

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="checkout-sidebar col-lg-4 col-sm-4 pull-right">

			<div class="row">

        <div class="col-box">

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

            <div id="order_review" class="woocommerce-checkout-review-order">

				<?php do_action( 'woocommerce_checkout_order_review' ); ?>

            </div>

        </div>
   
		<div class="col-box-new">

			<?php

			$terms = get_terms( 'faq_category', array(

				'hide_empty' => 0

			) );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){

				echo '<ul>';

				foreach ( $terms as $term ) {

					$faq_set = get_field('faq_set', $term);

					if($faq_set == 'Yes'){

						echo '<li>' . $term->name . '</li>';

						$args = array(
							'post_type' => 'avada_faq',
							'post_status' => 'publish',
							'tax_query' => array(
								array(
									'taxonomy' => 'faq_category',
									'field' => 'id',
									'terms' => array(  $term->term_id ),
									'include_children' => true
								)
							)
						);

						$the_query = new WP_Query( $args );

						// The Loop

						if ( $the_query->have_posts() ) :

						while ( $the_query->have_posts() ) : $the_query->the_post();

						// Do Stuff

							?>  
						  <a class="col" data-toggle="collapse" href="#collapse<?php echo get_the_id(); ?>" aria-expanded="false" aria-controls="collapseExample">

							<i class="fa fa-chevron-right" aria-hidden="true"></i> <?php echo get_the_title(); ?>

						  </a>
                          <div class="collapse" id="collapse<?php echo get_the_id(); ?>"> <?php echo get_the_content(); ?></div><?php
						endwhile;
						endif;
						// Reset Post Data
						wp_reset_postdata();
					}
				}
				echo '</ul>';

			} ?>
                </div>
            </div>
		</div>
		<div class="col2-set col-lg-8 pull-left" id="customer_details">

			<div class="col-1">
          		<?php do_action( 'woocommerce_check_extra_services' ); ?>
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
               	<?php do_action( 'woocommerce_show_customer_address_map' ); ?>
            	<?php do_action( 'woocommerce_show_paymentform' ); ?>
			</div>
			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>
		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
	<?php endif; ?>



	<!--<h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>-->



	<!-- revere -->



	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>


<?php
				if(is_user_logged_in())
					{
					//	echo 'nananana';
						echo '<input type="hidden" name="logincheck" id="logincheck" value="0">';
					}
					else
					{

						$url =esc_url( home_url( '/' ) ).'login';
						//wp_redirect( $url );
						//exit;

						echo '<input type="hidden" name="logincheck" id="logincheck" value="1">';
					}
				?>


<input type="hidden" name="baselourl" id="baselourl" value="<?php echo esc_url( home_url( '/' ) ).'login?page=checkout'; ?>" />

</form>



<?php

				?>



<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<script>

jQuery(document).ready(function($) {

    $("#billing_address_1").val("");

    $(".woocommerce-content-box").hide();

    //$("#billing_cell").val("");

    //$("#billing_phone").val("");

    //$("#billing_last_name").val("");

    //$("#billing_first_name").val("");

    if ($(window).width() < 960) {

        $(".extra-products").removeClass("product");

    }

    $("form").keypress(function(e) {

        //Enter key

        if (e.which == 13) {

          return false;

        }

    });


});

(function($) {

		var triggerUpdate = function() {

				$('.subscription-option').one('click', 'input[type=radio]', function(){
					var logincheck = $('#logincheck').val();
					var yty = $(this).val();
					$("[value='"+yty+"']").click();
					$('#final_price_1').attr('value', '0.00');  
					//$( ".products li .product-title" ).first().text('Productos de Limpieza (Gratis)');
					$("[name='update_cart']").removeAttr('disabled');
					setTimeout(function(){
						$("[name='update_cart']").trigger("click");
					}, 200);
					$("[name='update_cart']").attr('disabled');
				});

				$('.one-time-option').one('click', 'input[type=radio]', function(){
					var yty = $(this).val();
					if(yty == 0){
						$('.mysub').html('');
					}
					//$( ".products li .product-title" ).first().text('Productos de Limpieza ($4.00)');
					//$('#final_price_1').attr('value', '$4');
					$("[value='"+yty+"']").click();
					$("[name='update_cart']").removeAttr('disabled');
						setTimeout(function(){
								$("[name='update_cart']").trigger("click");
						}, 200);
					$("[name='update_cart']").attr('disabled');
				});
		}
		triggerUpdate();
		$(document).ajaxComplete(function() {
				triggerUpdate();
		});
})(jQuery);

jQuery(document).ready(function() {
	var block = function( $node ) {
		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};

	jQuery(document).ajaxStart(function(callback){
		jQuery( '.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

	});
	jQuery(document).ajaxStop(function(callback){  
		jQuery( '.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table' ).unblock();
	});

	jQuery('#wc-authnet-new-payment-method').click();
    //jQuery('.one-time-option').addClass("myactive");

	jQuery(".subscription-option").click(function () {
    jQuery(".subscription-option").removeClass("myactive");
    jQuery(this).addClass("myactive");
    jQuery(".one-time-option").removeClass("myactive");
    if(jQuery('#payment_method_bacs').attr('checked')=='checked')jQuery('#payment_method_nmi').attr('checked','checked');
    showhideLoginonCheckout(); 
	});

	jQuery(".one-time-option").click(function () {
    jQuery(".one-time-option").removeClass("myactive");
    jQuery(".subscription-option").removeClass("myactive");
    jQuery(this).addClass("myactive");
    showhideLoginonCheckout(); 
	});
	showhideLoginonCheckout();
});

function showhideLoginonCheckout()
{
	if(jQuery(".one-time-option").hasClass("myactive"))
	{
		jQuery("#login_msg").hide(0);
	}
	else
	{
		jQuery("#login_msg").show(0);
	}
}

</script>
