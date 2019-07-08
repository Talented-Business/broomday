jQuery( document ).ready( function( $ ) {

	// Disable bottom margin on empty footer columns
	jQuery( '.fusion-footer .fusion-footer-widget-area .fusion-column' ).each(
		function() {
			if ( jQuery( this ).is( ':empty' ) ) {
				jQuery( this ).css( 'margin-bottom', '0' );
			}
		}
	);

	// Footer without social icons
	if ( ! jQuery( '.fusion-social-links-footer' ).find( '.fusion-social-networks' ).children().length ) {
		jQuery( '.fusion-social-links-footer' ).hide();
		jQuery( '.fusion-footer-copyright-area .fusion-copyright-notice' ).css( 'padding-bottom', '0' );
	}
});
