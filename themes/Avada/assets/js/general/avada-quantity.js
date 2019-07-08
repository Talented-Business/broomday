/**
 * Avada Quanity buttons add-back
 */
function avadaAddQuantityBoxes( $quantitySelector ) {

	var $quantityBoxes;

	if ( ! $quantitySelector ) {
		$quantitySelector = '.qty';
	}

	$quantityBoxes = jQuery( 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)' ).find( $quantitySelector );

	if ( $quantityBoxes && 'date' !== $quantityBoxes.prop( 'type' ) && 'hidden' !== $quantityBoxes.prop( 'type' ) ) {

		// Add plus and minus boxes
		$quantityBoxes.parent().addClass( 'buttons_added' ).prepend( '<input type="button" value="-" class="minus" />' );
		$quantityBoxes.addClass( 'input-text' ).after( '<input type="button" value="+" class="plus" />' );

		// Target quantity inputs on product pages
		jQuery( 'input' + $quantitySelector + ':not(.product-quantity input' + $quantitySelector + ')' ).each( function() {
				var $min = parseFloat( jQuery( this ).attr( 'min' ) );

				if ( $min && $min > 0 && parseFloat( jQuery( this ).val() ) < $min ) {
					jQuery( this ).val( $min );
				}
		});

		jQuery( '.plus, .minus' ).unbind( 'click' );

		jQuery( '.plus, .minus' ).on( 'click', function() {

				// Get values
				var $quantityBox     = jQuery( this ).parent().find( $quantitySelector ),
				    $currentQuantity = parseFloat( $quantityBox.val() ),
				    $maxQuantity     = parseFloat( $quantityBox.attr( 'max' ) ),
				    $minQuantity     = parseFloat( $quantityBox.attr( 'min' ) ),
				    $step            = $quantityBox.attr( 'step' );

				// Fallback default values
				if ( ! $currentQuantity || '' === $currentQuantity  || 'NaN' === $currentQuantity ) {
					$currentQuantity = 0;
				}
				if ( '' === $maxQuantity || 'NaN' === $maxQuantity ) {
					$maxQuantity = '';
				}

				if ( '' === $minQuantity || 'NaN' === $minQuantity ) {
					$minQuantity = 0;
				}
				if ( 'any' === $step || '' === $step  || undefined === $step || 'NaN' === parseFloat( $step )  ) {
					$step = 1;
				}

				// Change the value
				if ( jQuery( this ).is( '.plus' ) ) {

					if ( $maxQuantity && ( $maxQuantity == $currentQuantity || $currentQuantity > $maxQuantity ) ) {
						$quantityBox.val( $maxQuantity );
					} else {
						$quantityBox.val( $currentQuantity + parseFloat( $step ) );
					}

				} else {

					if ( $minQuantity && ( $minQuantity == $currentQuantity || $currentQuantity < $minQuantity ) ) {
						$quantityBox.val( $minQuantity );
					} else if ( $currentQuantity > 0 ) {
						$quantityBox.val( $currentQuantity - parseFloat( $step ) );
					}

				}

				// Trigger change event
				$quantityBox.trigger( 'change' );
			}
		);
	}
}
jQuery( window ).on( 'load', function( $ ) {
	avadaAddQuantityBoxes();
});
jQuery( document ).ajaxComplete( function() {
	avadaAddQuantityBoxes();
});
