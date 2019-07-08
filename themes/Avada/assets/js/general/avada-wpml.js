jQuery( document ).ready( function( $ ) {

	// WPML search form input add
	if ( '' !== avadaLanguageVars.language_flag ) {
		jQuery( '.search-field, .searchform' ).each( function() {
			if ( ! jQuery( this ).find( 'input[name="lang"]' ).length && ! jQuery( this ).parents( '.searchform' ).find( 'input[name="lang"]' ).length ) {
				jQuery( this ).append( '<input type="hidden" name="lang" value="' + avadaLanguageVars.language_flag + '"/>' );
			}
		});
	}

});
