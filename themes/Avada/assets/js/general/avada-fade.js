jQuery( window ).load( function() {
	if ( undefined === cssua.ua.mobile ) {

		// Change opacity of page title bar on scrolling
		if ( '1' == avadaFadeVars.page_title_fading ) {
			if ( 'Left' === avadaFadeVars.header_position || 'Right' === avadaFadeVars.header_position ) {
				jQuery( '.fusion-page-title-wrapper' ).fusionScroller({ type: 'opacity', offset: 0 });
			} else {
				jQuery( '.fusion-page-title-wrapper' ).fusionScroller({ type: 'opacity', offset: 100 });
			}
		}
	}
});
