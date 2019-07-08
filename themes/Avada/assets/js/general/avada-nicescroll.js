/*jshint -W065 */
jQuery( document ).ready( function() {

	var smoothActive,
	    smoothCache;

	function niceScrollInit() {

		jQuery( 'html' ).niceScroll({
			background: '#555',
			scrollspeed: 60,
			mousescrollstep: 40,
			cursorwidth: 9,
			cursorborder: '0px',
			cursorcolor: '#303030',
			cursorborderradius: 8,
			preservenativescrolling: true,
			cursoropacitymax: 1,
			cursoropacitymin: 1,
			autohidemode: false,
			zindex: 999999,
			horizrailenabled: false
		});

		if ( jQuery( 'html' ).getNiceScroll().length ) {
			jQuery( 'html' ).addClass( 'no-overflow-y' );
		} else {
			jQuery( 'html' ).removeClass( 'no-overflow-y' );
		}

	}

	smoothActive = avadaNiceScrollVars.smooth_scrolling;
	smoothCache  = ( 1 === smoothActive || '1' === smoothActive || true === smoothActive ) ? true : false;

	setTimeout( function() {

		if ( ( 1 === smoothActive || '1' === smoothActive || true === smoothActive ) && ! Modernizr.mq( 'screen and (max-width: ' + ( 800 + parseInt( avadaNiceScrollVars.side_header_width ) ) +  'px)' ) && jQuery( 'body' ).outerHeight( true ) > jQuery( window ).height() && ! navigator.userAgent.match( /(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/ ) ) {
			niceScrollInit();
		} else {
			jQuery( 'html' ).removeClass( 'no-overflow-y' );

			if ( jQuery( 'body' ).outerHeight( true ) < jQuery( window ).height() ) {
				jQuery( 'html' ).css( 'overflow-y', 'hidden' );
			}
		}

	}, 50 );

	jQuery( window ).resize( function() {

		var smoothActive = avadaNiceScrollVars.smooth_scrolling,
		    smoothCache  = ( 1 === smoothActive || '1' === smoothActive || true === smoothActive ) ? true : false;

		if ( ( 1 === smoothActive || '1' === smoothActive || true === smoothActive ) && ! Modernizr.mq( 'screen and (max-width: ' + ( 800 + parseInt( avadaNiceScrollVars.side_header_width ) ) +  'px)' ) && jQuery( 'body' ).outerHeight( true ) > jQuery( window ).height() && ! navigator.userAgent.match( /(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/ ) ) {

			niceScrollInit();

		} else {

			jQuery( 'html' ).getNiceScroll().remove();
			jQuery( 'html' ).removeClass( 'no-overflow-y' );
			if ( jQuery( 'body' ).outerHeight( true ) < jQuery( window ).height() ) {
				jQuery( 'html' ).css( 'overflow-y', 'hidden' );
			} else {
				jQuery( 'html' ).css( 'overflow-y', 'auto' );
			}
			jQuery( '#ascrail2000' ).css( 'opacity', '1' );

		}

	});

});
