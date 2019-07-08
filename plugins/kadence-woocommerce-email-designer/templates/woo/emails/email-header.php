<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/** 
 * EDIT NOTES FOR KADENCE WOOMAIL DESIGNER
 *
 * add class to body tag so you can style this specifically 
 * Change width="600" to width 100% for tables
 * Add subtitle option.
 * Add Header image container
 * Add Order Style Class
 */
if ( is_object( $email ) && isset( $email->id ) ) {
	$key = $email->id;
} else {
	$key = '';
}
$email_subtitle = Kadence_Woomail_Customizer::opt( $key . '_subtitle' );
if ( ! empty( $email_subtitle ) ) {
	$email_subtitle = Kadence_Woomail_Designer::filter_subtitle( $email_subtitle, $email );
}
$subtitle_placement = Kadence_Woomail_Customizer::opt( 'subtitle_placement' );
$order_style = Kadence_Woomail_Customizer::opt( 'order_items_style' );
if ( empty( $order_style ) ) {
	$order_style = 'normal';
}
$h2_style = Kadence_Woomail_Customizer::opt( 'h2_style' );
if ( empty( $h2_style ) ) {
	$h2_style = 'none';
}
$header_image_maxwidth = Kadence_Woomail_Customizer::opt( 'header_image_maxwidth' );
if ( empty( $header_image_maxwidth ) ) {
	$header_image_maxwidth = 'auto';
}
$header_image_maxwidth = str_replace( 'px', '', $header_image_maxwidth );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	</head>
	<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" class="kt-woo-wrap order-items-<?php echo esc_attr( $order_style );?> title-style-<?php echo esc_attr( $h2_style );?>">
		<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<tr>
					<td align="center" valign="top">
						<div id="template_header_image_container">
							<div id="template_header_image">
								<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header_image_table">
									<tr>
										<td align="center" valign="middle">
											<?php
												if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
													echo '<p style="margin-top:0;"><img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" width="' . esc_attr( $header_image_maxwidth ) . '" /></p>';
												}
											?>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
							<tr>
								<td align="center" valign="top">
									<!-- Header -->
									<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header">
										<tr>
											<td id="header_wrapper">
												<?php if ( 'above' == $subtitle_placement ) { ?>
													<div class="subtitle"><?php echo $email_subtitle; ?></div>
												<?php } ?>
												<h1><?php echo $email_heading; ?></h1>
												<?php if ( 'below' == $subtitle_placement ) { ?>
													<div class="subtitle"><?php echo $email_subtitle; ?></div>
												<?php } ?>
											</td>
										</tr>
									</table>
									<!-- End Header -->
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Body -->
									<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
										<tr>
											<td valign="top" id="body_content">
												<!-- Content -->
												<table border="0" cellpadding="20" cellspacing="0" width="100%">
													<tr>
														<td valign="top">
															<div id="body_content_inner">
