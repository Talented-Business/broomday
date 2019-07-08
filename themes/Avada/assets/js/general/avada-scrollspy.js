jQuery( document ).ready( function( $ ) {

	// One page scrolling effect
	var $adminbarHeight     = ( 'function' === typeof getAdminbarHeight ) ? getAdminbarHeight() : 0,
	    $stickyHeaderHeight = ( 'function' === typeof getStickyHeaderHeight ) ? getStickyHeaderHeight() : 0;

	jQuery( window ).on( 'resize scroll', function() {
		var $adminbarHeight     = ( 'function' === typeof getAdminbarHeight ) ? getAdminbarHeight() : 0,
		    $stickyHeaderHeight = ( 'function' === typeof getStickyHeaderHeight ) ? getStickyHeaderHeight() : 0;
	});

	// Ititialize ScrollSpy script
	jQuery( 'body' ).scrollspy({
		target: '.fusion-menu',
		offset: parseInt( $adminbarHeight + $stickyHeaderHeight + 1 )
	});

	// Reset ScrollSpy offset to correct height after page is fully loaded, may be needed for
	jQuery( window ).load( function() {
		var $adminbarHeight = ( 'function' === typeof getAdminbarHeight ) ? getAdminbarHeight() : 0,
		    $stickyHeaderHeight = ( 'function' === typeof getStickyHeaderHeight ) ? getStickyHeaderHeight() : 0;

		jQuery( 'body' ).data()['bs.scrollspy'].options.offset = parseInt( $adminbarHeight + $stickyHeaderHeight + 1 );
	});
});
