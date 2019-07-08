<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<?php
global $post, $wpdb;
//echo $post->ID;
if($post->ID == 1457){
    $dis= "block";
}else{
    $dis= "none";
}

if($post->ID == 6093){
    $reg = 'block';
}else{
    $reg = 'none';
}

if(!empty($_GET['editoid'])){
    
      $order = new WC_Order( $_GET['editoid'] );
      $user_id = $order->user_id;
      $user = get_userdata( $user_id );
    
}else{
	if(isset($_GET['customer'])){
		$user_id = $_GET['customer'];
		$user = get_userdata( $user_id );
	}else{
		$user_id = null;
		$user = null;
	}
}
?>
<div class="u-columns col2-set" id="customer_login" >

	<div class="u-column1 col-1" style="display:<?php echo $dis; ?>;">

<?php endif; ?>

		<h2><?php _e( 'Ingresa a tu Cuenta', 'woocommerce' ); ?></h2>

		<form class="woocommerce-form woocommerce-form-login login" method="post"  >

			<?php do_action( 'woocommerce_login_form_start' ); ?>
			<div style="margin:0 auto;width:330px;">
			
            <?php if($user_id){ ?>
             <input type="hidden" name="oid" value="<?php echo $_GET['editoid'] ?>" >
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="username"><?php _e( 'Username or email address', 'woocommerce' ); ?> <span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" value="<?php echo $user->data->user_login; ?>" disabled="disabled"/>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="hidden" name="custom_username" id="custom_username" value="<?php echo $user->data->user_login; ?>"/>
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" value="<?php echo $user->data->user_pass; ?>" disabled="disabled"/>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="hidden" name="custom_pass" id="custom_pass" value="<?php echo $user->data->user_pass; ?>"/>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="hidden" name="custom_id" id="custom_id" value="<?php echo $user_id; ?>"/>
                    
                </p>
            <?php }else{ ?>
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
						<input type="text" class="form-control" name="username" id="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?>" placeholder="<?php _e( 'Correo Electrónico', 'woocommerce' ); ?>"/>
					</div>
				</div>
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-lock"></i></span>
						<input class="form-control" type="password" name="password" id="password" />
					</div>	
				</div>
            <?php } ?>

			<?php do_action( 'woocommerce_login_form' ); ?>
			<div class="woocommerce-LostPassword lost_password form-group">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php _e( 'Remember me', 'woocommerce' ); ?></span>
				</label>
			</div>
			<p class="woocommerce-FormRow form-row" style="width:100%;">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<input type="submit" id="lobtn" class="woocommerce-Button button" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>" />
			</p>
            
            <?php
			if(isset($_REQUEST['page'])&&$_REQUEST['page']=='checkout')
			{
				$redirect =  esc_url( home_url( '/' ) ).'checkout';
				echo '<input type="hidden" name="redirect" value="'.$redirect.'" />';
			}
			
			?>           
			</div>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
			
				<hr>
				<p class="woocommerce-form-row form-row form-row-wide">
				¿Eres nuevo?
				<a href="<?=esc_url( home_url( '/registro/' ) );?>">Regístrate</a>
			</p>	
		</form>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

	<div class="u-column2 col-2" style="display:<?php echo $reg; ?>;">

		<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>

		<form method="post" class="register">

			<?php do_action( 'woocommerce_register_form_start' ); ?>


			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?>" />
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( $_POST['email'] ) : ''; ?>" />
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" />
				</p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="woocommerce-FormRow form-row">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<input type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>" />
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>
			<hr>
			<p class="woocommerce-form-row form-row form-row-wide">
				¿Ya tienes cuenta?
				<a href="<?=esc_url( home_url( '/login/' ) );?>">Inicia Sesión</a>
			</p>	

		</form>

	</div>

</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

<?php
if(!empty($_GET['editoid'])){
    ?>
    <script>
      jQuery(document).ready(function(){
        jQuery('#lobtn').trigger('click');
		 
      });
    </script>
    <?php
}
?>
