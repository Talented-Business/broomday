jQuery( document ).ready( function() {

	jQuery( '.tribe-ticket-quantity' ).addClass( 'qty' ).wrap( '<div class="quantity"></div>' );

	// Disable the navigation top and bottom lines, when there is no prev and next nav
	if ( ! jQuery.trim( jQuery( '.tribe-events-nav-previous' ).html() ).length && ! jQuery.trim( jQuery( '.tribe-events-nav-next' ).html() ).length ) {
		jQuery( '.tribe-events-sub-nav' ).parent( '#tribe-events-footer' ).hide();
	}

	jQuery( '.fusion-tribe-has-featured-image' ).each( function() {
		var height = jQuery( this ).parent().height();
		jQuery( this ).find( '.tribe-events-event-image' ).css( 'height', height );
	});

	jQuery( window ).on( 'resize', function() {

		var height;

		jQuery( '.fusion-tribe-has-featured-image' ).each( function() {
			jQuery( this ).find( '.tribe-events-event-image' ).css( 'height', 'auto' );
			height = jQuery( this ).parent().height();
			jQuery( this ).find( '.tribe-events-event-image' ).css( 'height', height );
		});
	});
});

jQuery( document ).ajaxComplete( function( event, request, settings ) {
	var $postsContainer,
	    $posts;

	jQuery( '.fusion-tribe-has-featured-image' ).each( function() {
		var height = jQuery( this ).parent().height();
		jQuery( this ).find( '.tribe-events-event-image' ).css( 'height', height );
	});

	jQuery( this ).find( '.post' ).each( function() {
		jQuery( this ).find( '.fusion-post-slideshow' ).flexslider();
		jQuery( this ).find( '.full-video, .video-shortcode, .wooslider .slide-content' ).fitVids();
	});

	// Fade in new posts when all images are loaded, then relayout isotope
	$postsContainer = jQuery( '#tribe-events .fusion-blog-layout-grid' );
	$posts = $postsContainer.find( '.post' );
	$postsContainer.css( 'height', $postsContainer.height() );
	$posts.hide();
	imagesLoaded( $posts, function() {

		$postsContainer.css( 'height', '' );
		$posts.fadeIn();

		// Relayout isotope
		$postsContainer.isotope();
		jQuery( window ).trigger( 'resize' );

		// Refresh the scrollspy script for one page layouts
		jQuery( '[data-spy="scroll"]' ).each( function() {
			  var $spy = jQuery( this ).scrollspy( 'refresh' );
		});
	});

	if ( jQuery( '.fusion-page-title-bar h1' ).length && jQuery( '.tribe-events-page-title' ).length ) {
		jQuery( '.fusion-page-title-bar h1' ).text( jQuery( '.tribe-events-page-title' ).text() );
	}

});

jQuery( window ).load( function() {

	// Events Calendar Reinitialize Scripts
	jQuery( '.tribe_events_filters_close_filters, .tribe_events_filters_show_filters' ).on( 'click', function() {
		var tribeEvents = jQuery( this );

		setTimeout( function() {
			jQuery( tribeEvents ).parents( '#tribe-events-content-wrapper' ).find( '.fusion-blog-layout-grid' ).isotope();
		});
	});
});
