<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
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
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ) {
	return;
}

if(is_checkout()){
	$error = [];
	if(! $_POST['billing_first_name']){
		$error[] = __('Please enter your name', 'Avada');
	}
	if(! $_POST['billing_last_name']){
		$error[] = __('Please enter your lastname', 'Avada');
	}
	if(! $_POST['billing_phone']){
		$error[] = __('Please enter your phone', 'Avada');
	}
	if(! $_POST['billing_comments']){
		$error[] = __('Please add extra address', 'Avada');
	}
	if(! $_POST['billing_cell']){
		$error[] = __('Please enter your cell no', 'Avada');
	}
	
	if(isset($_POST['cust_latitude']) && $_POST['cust_latitude'] == "" || isset($_POST['cust_longitude']) && $_POST['cust_longitude'] == ""){
		$error[] = __('Please enter correct location', 'Avada');
	}
	if(! $_POST['terms']){
		$error[] = __('Please accept terms to continue', 'Avada');
	}
	if($error):
	?>
<ul class="woocommerce-error">
	<?php foreach ( $error as $message ) : ?>
		<li><?php echo $message;//wc_add_notice(__($message), 'error'); ?></li>
	<?php endforeach; ?>
</ul>
	<?php
	endif;
}else{
?>
<ul class="woocommerce-error">
	<?php foreach ( $messages as $message ) : ?>
		<li><?php echo wp_kses_post( $message ); ?></li>
	<?php endforeach; ?>
</ul>
<?php } ?>