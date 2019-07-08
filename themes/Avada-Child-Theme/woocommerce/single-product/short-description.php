<?php
/**
 * Single product short description
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version	 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
//echo 'rahultest-'.$post->ID;
echo '<h2 itemprop="name" class="product_title entry-title">Tell Us About Your Place</h2>';
//echo 'Charges '.get_woocommerce_currency_symbol().get_post_meta($post->ID, '_hourly_charges', true).' per hour<br>';
//echo 'Charges '.get_woocommerce_currency_symbol() .get_post_meta($post->ID, '_bedroom_charges', true).' per bedroom<br>';

if ( ! $post->post_excerpt ) return;
?>
<div class="post-content " itemprop="description">

	<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
</div>

