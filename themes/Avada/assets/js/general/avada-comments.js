jQuery( document ).ready( function( $ ) {

	var $titleSep,
	    $titleSepClassString,
	    $titleMainSepClassString,
	    $styles,
	    i;

	// Comment form title changes
	if ( jQuery( '.comment-respond .comment-reply-title' ).length && ! jQuery( '.comment-respond .comment-reply-title' ).parents( '.woocommerce-tabs' ).length ) {
		$titleSep                = avadaCommentVars.title_style_type.split( ' ' );
		$titleSepClassString     = '';
		$titleMainSepClassString = '';

		for ( i = 0; i < $titleSep.length; i++ ) {
			$titleSepClassString += ' sep-' + $titleSep[i];
		}

		if ( $titleSepClassString.indexOf( 'underline' ) > -1 ) {
			$titleMainSepClassString = $titleSepClassString;
		}

		if ( jQuery( 'body' ).hasClass( 'rtl' ) ) {
			jQuery( '.comment-respond .comment-reply-title' ).addClass( 'title-heading-right' );
		} else {
			jQuery( '.comment-respond .comment-reply-title' ).addClass( 'title-heading-left' );
		}

		$styles = ' style="margin-top:' + avadaCommentVars.title_margin_top + ';margin-bottom:' + avadaCommentVars.title_margin_bottom + ';"';

		jQuery( '.comment-respond .comment-reply-title' ).wrap( '<div class="fusion-title title fusion-title-size-three' + $titleSepClassString + '"' + $styles + '></div>' );

		if ( $titleSepClassString.indexOf( 'underline' ) == -1 ) {
			jQuery( '.comment-respond .comment-reply-title' ).parent().append( '<div class="title-sep-container"><div class="title-sep' + $titleSepClassString + ' "></div></div>' );
		}
	}

	// Text area limit expandability
	jQuery( '.textarea-comment' ).each( function() {
		jQuery( this ).css( 'max-width', jQuery( '#content' ).width() );
	});

	jQuery( window ).on( 'resize', function() {
		jQuery( '.textarea-comment' ).each( function() {
			jQuery( this ).css( 'max-width', jQuery( '#content' ).width() );
		});
	});
});
