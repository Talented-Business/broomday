jQuery( document ).ready( function( $ ) {

	// Side nav drop downs
	jQuery( '.side-nav-left .side-nav li' ).each( function() {
		if ( jQuery( this ).find( '> .children' ).length ) {
			if ( jQuery( '.rtl' ).length ) {
				jQuery( this ).find( '> a' ).prepend( '<span class="arrow"></span>' );
			} else {
				jQuery( this ).find( '> a' ).append( '<span class="arrow"></span>' );
			}
		}
	});

	jQuery( '.side-nav-right .side-nav li' ).each( function() {
		if ( jQuery( this ).find( '> .children' ).length ) {
			if ( jQuery( 'body.rtl' ).length ) {
				jQuery( this ).find( '> a' ).append( '<span class="arrow"></span>' );
			} else {
				jQuery( this ).find( '> a' ).prepend( '<span class="arrow"></span>' );
			}
		}
	});

	jQuery( '.side-nav .current_page_item' ).each( function() {
		if ( jQuery( this ).find( '.children' ).length ) {
			jQuery( this ).find( '.children' ).show( 'slow' );
		}
	});

	jQuery( '.side-nav .current_page_item' ).each( function() {
		if ( jQuery( this ).parent().hasClass( 'side-nav' ) ) {
			jQuery( this ).find( 'ul' ).show( 'slow' );
		}

		if ( jQuery( this ).parent().hasClass( 'children' ) ) {
			jQuery( this ).parents( 'ul' ).show( 'slow' );
		}
	});
});

jQuery( window ).load( function() {
	if ( 'Click' === avadaSideNavVars.sidenav_behavior ) {
		jQuery( '.side-nav li a' ).on( 'click', function( e ) {
			if ( jQuery( this ).parent( '.page_item_has_children' ).length ) {
				if ( jQuery( this ).parent().find( '> .children' ).length  && ! jQuery( this ).parent().find( '> .children' ).is( ':visible' ) ) {
					jQuery( this ).parent().find( '> .children' ).stop( true, true ).slideDown( 'slow' );
				} else {
					jQuery( this ).parent().find( '> .children' ).stop( true, true ).slideUp( 'slow' );
				}
			}

			if ( jQuery( this ).parent( '.page_item_has_children.current_page_item' ).length ) {
				return false;
			}
		});
	} else {
		jQuery( '.side-nav li' ).hoverIntent({
			over: function() {
				if ( jQuery( this ).find( '> .children' ).length ) {
					jQuery( this ).find( '> .children' ).stop( true, true ).slideDown( 'slow' );
				}
			},
			out: function() {
				if ( 0 === jQuery( this ).find( '.current_page_item' ).length && false === jQuery( this ).hasClass( 'current_page_item' ) ) {
					jQuery( this ).find( '.children' ).stop( true, true ).slideUp( 'slow' );
				}
			},
			timeout: 500
		});
	}
});
