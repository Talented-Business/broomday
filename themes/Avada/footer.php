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
					<?php do_action( 'avada_after_main_content' ); ?>

				</div>  <!-- fusion-row -->
			</main>  <!-- #main -->
			<?php do_action( 'avada_after_main_container' ); ?>

			<?php global $social_icons; ?>

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
				<?php $footer_parallax_class = ( 'footer_parallax_effect' === Avada()->settings->get( 'footer_special_effects' ) ) ? ' fusion-footer-parallax' : ''; ?>

				<div class="fusion-footer<?php echo esc_attr( $footer_parallax_class ); ?>">
					<?php get_template_part( 'templates/footer-content' ); ?>
				</div> <!-- fusion-footer -->
			<?php endif; // End is not blank page check. ?>

			<?php
			/**
			 * Add sliding bar.
			 */
			?>
			<?php if ( Avada()->settings->get( 'slidingbar_widgets' ) && ! is_page_template( 'blank.php' ) ) : ?>
				<?php get_template_part( 'sliding_bar' ); ?>
			<?php endif; ?>
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
		echo Avada()->settings->get( 'space_body' ); // WPCS: XSS ok.
		?>
        <?php if($_GET['customer']){ ?>
			<script type="text/javascript">
                jQuery(document).ready(function(){
                	jQuery(".u-column1.col-1 .woocommerce-Button.button").trigger("click");
                });
            </script>
		<?php } ?>
		<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
                <script>
                    jQuery(document).ready(function() {
                      /*  jQuery('#example12').DataTable();*/
                      //Spanish
jQuery('#example12').DataTable({
    "order": [[ 0, "asc" ]],
    "language": {
        "sProcessing":    "Procesando...",
        "sLengthMenu":    "Mostrar _MENU_ entradas",
        "sZeroRecords":   "No se encontraron resultados",
        "sEmptyTable":    "Ningún dato disponible en esta tabla",
        "sInfo":          "Mostrando  _START_ al _END_ de _TOTAL_ entradas",
        "sInfoEmpty":     "Mostrando  _START_ al _END_ de _TOTAL_ entradas",
        "sInfoFiltered":  "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":   "",
        "sSearch":        "Buscar:",
        "sUrl":           "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst":    "Primero",
            "sLast":    "Último",
            "sNext":    "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    }
});

/*
var actual_prc = jQuery('.one-time-option-details').next().text().replace('$','');
     var a2 = '00.00';
         if(a2)
         {
              a2 = jQuery('#gttt_reg_prc').val();
         }
     var total_prc = parseFloat(actual_prc) + parseFloat(a2);
     var total_prc1 = parseFloat(total_prc) - parseFloat(total_prc * 0.2);
      var total_prc2 = parseFloat(total_prc) - parseFloat(total_prc * 0.25);
       var total_prc3 = parseFloat(total_prc) - parseFloat(total_prc * 0.3);
    jQuery('.one-time-option-details').text('Limpieza Única').next().html('$'+(total_prc).toFixed(2));   
 jQuery( ".wcsatt-options-product li:nth-child(2)" ).find('.discountprice').html('$'+ (total_prc1).toFixed(2));
  jQuery( ".wcsatt-options-product li:nth-child(3)" ).find('.discountprice').html('$'+ (total_prc2).toFixed(2));
      jQuery( ".wcsatt-options-product li:nth-child(4)" ).find('.discountprice').html('$'+ (total_prc3).toFixed(2));*/
  

/*   jQuery('.discountprice').each(function () {
        var a1 = jQuery(this).text().replace('$','');
        var a2 = '00.00';
         if(a2)
         {
              a2 = jQuery('#gttt_reg_prc').val();
         }
        jQuery(this).html('$'+(parseFloat(a1)+parseFloat(a2)).toFixed(2));
    });*/
                    } );
                </script>
	</body>
</html>
