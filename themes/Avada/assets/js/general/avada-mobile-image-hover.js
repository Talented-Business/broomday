( function( jQuery ) {

	'use strict';

	jQuery.fn.fusion_deactivate_mobile_image_hovers = function() {
		if ( Modernizr.mq( 'only screen and (max-width:' + avadaMobileImageVars.side_header_break_point + 'px)' ) ) {
			jQuery( this ).removeClass( 'fusion-image-hovers' );
		} else {
			jQuery( this ).addClass( 'fusion-image-hovers' );
		}
	};
})( jQuery );

jQuery( document ).ready( function( $ ) {

	// Deactivate image hover animations on mobiles
	jQuery( 'body' ).fusion_deactivate_mobile_image_hovers();
	jQuery( window ).on( 'resize', function() {
		jQuery( 'body' ).fusion_deactivate_mobile_image_hovers();
	});
});
