jQuery( document ).ready( function( $ ) {
	var sidebar1Float;

	// Sidebar Position
	if ( 1 <= jQuery( '#sidebar-2' ).length ) {
		sidebar1Float = jQuery( '#sidebar' ).css( 'float' );
		jQuery( 'body' ).addClass( 'sidebar-position-' + sidebar1Float );
	}
});
