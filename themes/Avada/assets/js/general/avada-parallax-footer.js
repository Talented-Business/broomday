( function( jQuery ) {

	'use strict';

	// Initialize parallax footer
	jQuery.fn.fusion_footer_parallax = function() {
		var $footer = jQuery( this ),
		    $sliderHeight,
		    $footerHeight,
		    bodyTopMargin = 0,
		    bodyBottomMargin = 0;

		// Needed timeout for dynamic footer content
		setTimeout( function() {
			var $wrapperHeight = ( 'fixed' === $footer.css( 'position' ) ) ? jQuery( '#wrapper' ).outerHeight() : jQuery( '#wrapper' ).outerHeight() - $footer.outerHeight();

			if ( jQuery( '#boxed-wrapper' ).length ) {
				bodyTopMargin = jQuery( 'body' ).offset().top;
				bodyBottomMargin = jQuery( 'body' ).outerHeight( true ) - jQuery( 'body' ).outerHeight() - bodyTopMargin;
			}

			// On desktops, when the footer is smaller in height than the viewport, enable parallax footer effect
			if ( $footer.outerHeight() + bodyBottomMargin < jQuery( window ).height() && $wrapperHeight > jQuery( window ).height() && ( 'Top' === avadaParallaxFooterVars.header_position || ( 'Top' !== avadaParallaxFooterVars.header_position && jQuery( window ).height() > jQuery( '.side-header-wrapper' ).height() ) ) && ( Modernizr.mq( 'only screen and (min-width:'  + parseInt( avadaParallaxFooterVars.side_header_break_point ) +  'px)' ) && ! Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: portrait)' ) && ! Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape)' ) ) ) {
				$footer.css( {
					'position': '',
					'margin': '',
					'padding': '',
					'max-width': ''
				});

				if ( jQuery( '#boxed-wrapper' ).length ) {
					$footer.css( 'bottom', bodyBottomMargin );
				}

				jQuery( '#main' ).css( 'margin-bottom', $footer.outerHeight() + bodyBottomMargin );

				if ( 1 <= jQuery( '.tfs-slider' ).length && 1 == jQuery( '.tfs-slider' ).data( 'parallax' ) && $footer.hasClass( 'fusion-footer-parallax' ) ) {
					$sliderHeight = jQuery( '.tfs-slider' ).parents( '#sliders-container' ).outerHeight();
					$footerHeight = $footer.outerHeight();
					if ( $sliderHeight > $footerHeight ) {
						jQuery( '#main' ).css( 'min-height', $sliderHeight + 100 );
					} else if ( $footerHeight > $sliderHeight ) {
						jQuery( '#main' ).css( 'min-height', $footerHeight + 100 );
					}
				}

			// On mobiles, or when the footer is larger in height than the viewport, the footer will be static
			} else {
				$footer.css( {
					'position': 'static',
					'margin': '0',
					'padding': '0',
					'bottom': '0',
					'max-width': 'none'
				});
				jQuery( '#main' ).css( 'margin-bottom', '' );
			}
		}, 1 );
	};
})( jQuery );

jQuery( document ).ready( function( $ ) {

	// Handle parallax footer
	jQuery( '.fusion-footer-parallax' ).fusion_footer_parallax();

	jQuery( window ).on( 'resize', function() {
		jQuery( '.fusion-footer-parallax' ).fusion_footer_parallax();
	});
});
