<?php
/**
 * WooCommerce Social Login
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @package   WC-Social-Login/Templates
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Renders login buttons for available social login providers
 *
 * @param string $login_text
 * @param array $providers Providers to be rendered
 * @param string $return_url Return URL
 *
 * @version 1.0.0
 * @since 1.0.0
 */
?>

<?php if ( $providers ) : ?>

	<div class="wc-social-login form-row-wide">

		<p style="display:none"><?php echo wp_kses_post( $login_text ); ?></p>

		<?php
			foreach ( $providers as $provider ) :
				printf( '<a href="%1$s" class="button-social-login button-social-login-%2$s"><span class="si si-%2$s"></span>%3$s</a> ', esc_url( $provider->get_auth_url( $return_url ) ), esc_attr( $provider->get_id() ), esc_html( $provider->get_login_button_text() ) );
			endforeach;
		?>

	</div>
	<div class="or-seperator"><i>O</i></div>	
<?php endif; ?>
