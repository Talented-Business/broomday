jQuery( window ).load( function() {

	// Set heights of select arrows correctly
	calcSelectArrowDimensions();

	setTimeout( function() {
		calcSelectArrowDimensions();
	}, 100 );
});

// Wrap gravity forms select and add arrow
function calcSelectArrowDimensions() {
	jQuery( '.avada-select-parent .select-arrow, .gravity-select-parent .select-arrow, .wpcf7-select-parent .select-arrow' ).filter( ':visible' ).each( function() {
		if ( jQuery( this ).prev().innerHeight() > 0 ) {
			jQuery( this ).css( {
				height: jQuery( this ).prev().innerHeight(),
				width: jQuery( this ).prev().innerHeight(),
				'line-height': jQuery( this ).prev().innerHeight() + 'px'
			});
		}
	});
}
