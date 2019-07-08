<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.2
 */
 
 // load customer data and user email into email template
 $user_email = $user_login;
 $user       = get_user_by('login', $user_login);
 $current_user = wp_get_current_user();
 if ( $user ) {
 $user_email = $user->user_email;
 }

 if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
 }
 ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s Customer username */ ?>
<p>¡<?php printf( __( 'Hi %s', 'woocommerce' ), $current_user->user_firstname ); ?>!</p>

<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ ?>
<p><?php printf( __( 'Gracias por registrarte en Broomday. Ahora podrás agendar limpiezas para tu hogar en tan sólo unos clicks y guardar el historial de tus pedidos bajo tu cuenta. Además, podrás suscribirte a un plan de limpieza que te brindará beneficios adicionales como:
<ul>
 	<li>Planes de limpieza personalizados, editables y cancelables en cualquier momento, totalmente gratis</li>
 	<li>Pagos automáticos seguros con tarjeta de crédito</li>
 	<li>Seguro de responsabilidad civil tanto para los clientes como los profesionales</li>
 	<li>Servicio al cliente 24/7 en línea y de 8:00 am – 5:00 pm en nuestras oficinas</li>
 	<li>Profesionales de limpieza íntegros y capacitados</li>
 	<li>Garantías de felicidad, (<a href="https://www.broomday.com/ayuda/">más información</a>)</li>
 	<li>Para ver que incluye una limpieza has click aquí (<a href="https://www.broomday.com/ayuda/">más información</a>)</li>
 	<li>Grandes descuentos en una gran variedad de servicios  (<a href="https://www.broomday.com/servicios/">ver servicios</a>)</li>
</ul>
<p>¡Broomday está generando cientos de empleos y brindando felicidad a cientos de hogares!</p>
<p>Gracias por confiar en nosotros, te deseamos un excelente día.</p>
<p>Atentamente,<br>Jose F. de Gracia<br>Gerente General<br>Broomday.com</p>', 'woocommerce' ), esc_html( $blogname ), '<strong>' . esc_html( $user_login ) . '</strong>', make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) ); ?></p><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>

<?php if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated ) : ?>
	<?php /* translators: %s Auto generated password */ ?>
	<p><?php printf( esc_html__( 'Your password has been automatically generated: %s', 'woocommerce' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>
<?php endif; ?>

<?php
do_action( 'woocommerce_email_footer', $email );
