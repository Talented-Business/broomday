<?php
/**
 * The footer template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>

<?php
if(!empty($_GET['key'])){
?>
<style>
    #main .fusion-portfolio h2, #main .post h2, #wrapper #main .post h2, #wrapper #main .post-content .fusion-title h2, #wrapper #main .post-content .title h2, #wrapper .fusion-title h2, #wrapper .post-content h2, #wrapper .title h2, #wrapper .woocommerce .checkout h3, .fusion-modal h2, .fusion-widget-area h2, .woocommerce .checkout h3, h2.entry-title {
    font-size: 30px !important;
}
#main .post h2, #main .reading-box h2, #main h2, .cart-empty, .ei-title h2, .fusion-modal h2, .fusion-title h2, .fusion-widget-area h2, .main-flex .slide-content h2, .post-content h2, .title h2 {
    font-family: 'Lato';
    font-weight: 400;
     line-height: 58px !important;
    letter-spacing: 0px;
}
.fusion-title-size-two, h2 {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
}
.post-content p {
    display: none;
}
.avada-thank-you {
    margin-bottom: -1px !important;
        padding-top: 8px !important;
}
.avada-order-details{
    padding-top: 8px !important;
}
</style>
<?php
}
 if (!is_checkout()) {
//global $wp;
//$str = home_url( $wp->request );
//$pid = explode("/",$str);
//$ss = wcs_get_subscription($pid[6]);
//echo $ss->date_created."<br>";
//$date=date_create($ss->date_created);
//$newdate = date_format($date,"Y/m/d H:i:s");
//$newthree =date('Y/m/d', strtotime('+3 days', strtotime($newdate)));

//strtotime('22-02-2011')
//strtotime(date("Y/m/d"))
//if(strtotime(date("Y/m/d")) > strtotime($newthree)){ 
    ?>
    <script type="text/javascript">
  //     jQuery(document).ready(function(){
    //          jQuery(".cancel").removeAttr("href");
      //        jQuery(".cancel").click(function(){
        //            alert("You can't cancel");
          //      });
            //    jQuery(".change_payment_method").removeAttr("href");
             // jQuery(".change_payment_method").click(function(){
               //     alert("You can't change payment method");
               // });
     //  });
</script>
    <?php
}else{
?>    
     <script type="text/javascript">
       //jQuery(document).ready(function(){
         //     jQuery(".cancel").removeAttr("href");
      // });
</script>
<!---<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <form method="post">
       <div> Reason:<textarea name="reason" style="width:100%"></textarea></div>
        <input type="submit" name="resubmit">
    </form>
  </div>

</div>--->
<?php    
}
    ?>

<?php
if(!empty($_POST['reason'])){
//$admin_email = get_option( 'admin_email' );
//$to = $admin_email;
//$subject = 'Request Cancel Subscription #'.$pid[6];
//$body = $_POST['reason'];
//$headers = array('Content-Type: text/html; charset=UTF-8');
 
//wp_mail( $to, $subject, $body, $headers );
}
?>
<style>
/*.shop_table td{
    padding:0px !important ;
}
    .modal {
    display: none;
    position: fixed;
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%; 
}
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}*/
</style>
<script>
//var modal = document.getElementById('myModal');

// Get the button that opens the modal
//var btn = document.getElementsByClassName("cancel")[0];

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal 
//btn.onclick = function() {
  //  modal.style.display = "block";
//}

// When the user clicks on <span> (x), close the modal
//span.onclick = function() {
 //   modal.style.display = "none";
//}

// When the user clicks anywhere outside of the modal, close it
//window.onclick = function(event) {
  //  if (event.target == modal) {
    //    modal.style.display = "none";
    //}
}
</script>
<?php } ?>

					<?php do_action( 'avada_after_main_content' ); ?>

				</div>  <!-- fusion-row -->
			</div>  <!-- #main -->
			<?php do_action( 'avada_after_main_container' ); ?>

			<?php global $social_icons; ?>

			<?php if ( false !== strpos( Avada()->settings->get( 'footer_special_effects' ), 'footer_sticky' ) ) : ?>
				</div>
			<?php endif; ?>

			<?php
			/**
			 * Get the correct page ID.
			 */
			$c_page_id = Avada()->fusion_library->get_page_id();
			?>

			<?php
			/**
			 * Only include the footer.
			 */
			?>
			<?php if ( ! is_page_template( 'blank.php' ) ) : ?>
				<?php $footer_parallax_class = ( 'footer_parallax_effect' == Avada()->settings->get( 'footer_special_effects' ) ) ? ' fusion-footer-parallax' : ''; ?>

				<div class="fusion-footer<?php echo esc_attr( $footer_parallax_class ); ?>">

					<?php
					/**
					 * Check if the footer widget area should be displayed.
					 */
					$display_footer = get_post_meta( $c_page_id, 'pyre_display_footer', true );
					?>
					<?php if ( ( Avada()->settings->get( 'footer_widgets' ) && 'no' !== $display_footer ) || ( ! Avada()->settings->get( 'footer_widgets' ) && 'yes' === $display_footer ) ) : ?>
						<?php $footer_widget_area_center_class = ( Avada()->settings->get( 'footer_widgets_center_content' ) ) ? ' fusion-footer-widget-area-center' : ''; ?>

						<footer role="contentinfo" class="fusion-footer-widget-area fusion-widget-area<?php echo esc_attr( $footer_widget_area_center_class ); ?>">
							<div class="fusion-row">
								<div class="fusion-columns fusion-columns-<?php echo esc_attr( Avada()->settings->get( 'footer_widgets_columns' ) ); ?> fusion-widget-area">
									<?php
									/**
									 * Check the column width based on the amount of columns chosen in Theme Options.
									 */
									$footer_widget_columns = Avada()->settings->get( 'footer_widgets_columns' );
									$footer_widget_columns = ( ! $footer_widget_columns ) ? 1 : $footer_widget_columns;
									$column_width = ( '5' == Avada()->settings->get( 'footer_widgets_columns' ) ) ? 2 : 12 / $footer_widget_columns;
									?>

									<?php
									/**
									 * Render as many widget columns as have been chosen in Theme Options.
									 */
									?>
									<?php for ( $i = 1; $i < 7; $i++ ) : ?>
										<?php if ( $i <= Avada()->settings->get( 'footer_widgets_columns' ) ) : ?>
											<div class="fusion-column<?php echo ( Avada()->settings->get( 'footer_widgets_columns' ) == $i ) ? ' fusion-column-last' : ''; ?> col-lg-<?php echo esc_attr( $column_width ); ?> col-md-<?php echo esc_attr( $column_width ); ?> col-sm-<?php echo esc_attr( $column_width ); ?>">
												<?php if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'avada-footer-widget-' . $i ) ) : ?>
													<?php
													/**
													 * All is good, dynamic_sidebar() already called the rendering.
													 */
													?>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									<?php endfor; ?>

									<div class="fusion-clearfix"></div>
								</div> <!-- fusion-columns -->
							</div> <!-- fusion-row -->
						</footer> <!-- fusion-footer-widget-area -->
					<?php endif; // End footer wigets check. ?>

					<?php
					/**
					 * Check if the footer copyright area should be displayed.
					 */
					$display_copyright = get_post_meta( $c_page_id, 'pyre_display_copyright', true );
					?>
					<?php if ( ( Avada()->settings->get( 'footer_copyright' ) && 'no' !== $display_copyright ) || ( ! Avada()->settings->get( 'footer_copyright' ) && 'yes' === $display_copyright ) ) : ?>
						<?php $footer_copyright_center_class = ( Avada()->settings->get( 'footer_copyright_center_content' ) ) ? ' fusion-footer-copyright-center' : ''; ?>

						<footer id="footer" class="fusion-footer-copyright-area<?php echo esc_attr( $footer_copyright_center_class ); ?>">
							<div class="fusion-row">
								<div class="fusion-copyright-content">

									<?php
									/**
									 * Footer Content (Copyright area) avada_footer_copyright_content hook.
									 *
									 * @hooked avada_render_footer_copyright_notice - 10 (outputs the HTML for the Theme Options footer copyright text)
									 * @hooked avada_render_footer_social_icons - 15 (outputs the HTML for the footer social icons)..
									 */
									do_action( 'avada_footer_copyright_content' );
									?>

								</div> <!-- fusion-fusion-copyright-content -->
							</div> <!-- fusion-row -->
						</footer> <!-- #footer -->
					<?php endif; // End footer copyright area check. ?>
					<?php
					// Displays WPML language switcher inside footer if parallax effect is used.
					if ( defined( 'ICL_SITEPRESS_VERSION' ) && 'footer_parallax_effect' === Avada()->settings->get( 'footer_special_effects' ) ) {
						global $wpml_language_switcher;
						$slot = $wpml_language_switcher->get_slot( 'statics', 'footer' );
						if ( $slot->is_enabled() ) {
							echo wp_kses_post( $wpml_language_switcher->render( $slot ) );
						}
					}
					?>
				</div> <!-- fusion-footer -->
			<?php endif; // End is not blank page check. ?>
		</div> <!-- wrapper -->

		<?php
		/**
		 * Check if boxed side header layout is used; if so close the #boxed-wrapper container.
		 */
		$page_bg_layout = ( $c_page_id ) ? get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) : 'default';
		?>
		<?php if ( ( ( 'Boxed' === Avada()->settings->get( 'layout' ) && 'default' === $page_bg_layout ) || 'boxed' === $page_bg_layout ) && 'Top' !== Avada()->settings->get( 'header_position' ) ) : ?>
			</div> <!-- #boxed-wrapper -->
		<?php endif; ?>
		<?php if ( ( ( 'Boxed' === Avada()->settings->get( 'layout' ) && 'default' === $page_bg_layout ) || 'boxed' === $page_bg_layout ) && 'framed' === Avada()->settings->get( 'scroll_offset' ) && 0 !== intval( Avada()->settings->get( 'margin_offset', 'top' ) ) ) : ?>
			<div class="fusion-top-frame"></div>
			<div class="fusion-bottom-frame"></div>
			<?php if ( 'None' !== Avada()->settings->get( 'boxed_modal_shadow' ) ) : ?>
				<div class="fusion-boxed-shadow"></div>
			<?php endif; ?>
		<?php endif; ?>
		<a class="fusion-one-page-text-link fusion-page-load-link"></a>

		<?php wp_footer(); ?>

		<?php
		/**
		 * Echo the scripts added to the "before </body>" field in Theme Options.
		 * The 'space_body' setting is not sanitized.
		 * In order to be able to take advantage of this,
		 * a user would have to gain access to the database
		 * in which case this is the least on your worries.
		 */
		// @codingStandardsIgnoreLine
		echo Avada()->settings->get( 'space_body' );
		?>
                <script>
                jQuery(document).ready(function($){
					//alert('hi');
                   $(".header_name").on("click",function(){
                         window.location.href = "<?php echo site_url("mi-cuenta/edit-account");?>";
                   });
				  //	$('#wc-nmi-gateway-woocommerce-credit-card-new-payment-method').prop('checked', true);
                </script>
                
	</body>
</html>
