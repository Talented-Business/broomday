jQuery( document ).ready( function( $ ) {

	// Slidingbar initialization
	var slidingbarState = 0;

	// Open slidingbar on page load if .open_onload class is present
	if ( jQuery( '#slidingbar-area.open_onload' ).length ) {
		jQuery( '#slidingbar' ).slideDown( 240, 'easeOutQuad' );
		jQuery( '.sb-toggle' ).addClass( 'open' );
		slidingbarState = 1;

		// Reinitialize google maps
		if ( jQuery( '#slidingbar .shortcode-map' ).length ) {
			jQuery( '#slidingbar' ).find( '.shortcode-map' ).each( function() {
				jQuery( this ).reinitializeGoogleMap();
			});
		}

		jQuery( '#slidingbar-area' ).removeClass( 'open_onload' );
	}

	// Handle the slidingbar toggle click
	jQuery( '.sb-toggle' ).click( function( e ) {
		var $slidingbar = jQuery( this ).parents( '#slidingbar-area' ).children( '#slidingbar' ),
		    $activeTestimonial;

		e.preventDefault();

		// Expand
		if ( 0 === slidingbarState ) {
			$slidingbar.slideDown( 240, 'easeOutQuad' );
			jQuery( '.sb-toggle' ).addClass( 'open' );
			slidingbarState = 1;

			// Reinitialize google maps
			if ( $slidingbar.find( '.shortcode-map' ).length ) {
				$slidingbar.find( '.shortcode-map' ).each( function() {
					jQuery( this ).reinitializeGoogleMap();
				});
			}

			// Reinitialize carousels
			if ( $slidingbar.find( '.fusion-carousel' ).length && 'function' === typeof generateCarousel ) {
				generateCarousel();
			}
			if ( 'function' === typeof jQuery.fn.fusion_recalculate_carousel ) {
				jQuery( '#slidingbar' ).find( '.fusion-carousel' ).fusion_recalculate_carousel();
			}

			// Reinitialize testimonial height; only needed for hidden wrappers
			if ( $slidingbar.find( '.fusion-testimonials' ).length ) {
				$activeTestimonial = $slidingbar.find( '.fusion-testimonials .reviews' ).children( '.active-testimonial' );

				$slidingbar.find( '.fusion-testimonials .reviews' ).height( $activeTestimonial.height() );
			}

		//Collapse
	} else if ( 1 == slidingbarState ) {
			$slidingbar.slideUp( 240, 'easeOutQuad' );
			jQuery( '.sb-toggle' ).removeClass( 'open' );
			slidingbarState = 0;
		}
	});

});

jQuery( window ).load( function() {
	jQuery( '.fusion-modal' ).bind( 'show.bs.modal', function() {
		var $slidingbar = jQuery( '#slidingbar' ),
		    $activeTestimonial;

		// Reinitialize dynamic content
		setTimeout( function() {

			// Reinitialize testimonial height; only needed for hidden wrappers
			if ( $slidingbar.find( '.fusion-testimonials' ).length ) {
				$activeTestimonial = $slidingbar.find( '.fusion-testimonials .reviews' ).children( '.active-testimonial' );

				$slidingbar.find( '.fusion-testimonials .reviews' ).height( $activeTestimonial.height() );
			}

		}, 350 );
	});
});
