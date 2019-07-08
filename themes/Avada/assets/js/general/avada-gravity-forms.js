function wrapGravitySelects() {
	jQuery( '.gform_wrapper select:not([multiple])' ).filter( ':visible' ).each( function() {
		var currentSelect = jQuery( this );

		setTimeout( function() {

			var selectWidth;
			if ( ! currentSelect.siblings( '.chosen-container' ).length && ! currentSelect.parent( '.gravity-select-parent' ).length ) {
				selectWidth = currentSelect.css( 'width' );
				currentSelect.wrap( '<div class="gravity-select-parent"></div>' );
				currentSelect.parent().width( selectWidth );
				currentSelect.css( 'cssText', 'width: 100% !important;' );

				jQuery( '<div class="select-arrow">&#xe61f;</div>' ).appendTo( currentSelect.parent( '.gravity-select-parent' ) );
			}
			calcSelectArrowDimensions();
		}, 50 );
	});
}

// Unwrap gravity selects that get a chzn field appended on the fly
jQuery( document ).bind( 'gform_post_conditional_logic', function() {
	var select = jQuery( '.gform_wrapper select' );
	jQuery( select ).each( function() {
		if ( jQuery( this ).hasClass( 'chzn-done' ) && jQuery( this ).parent().hasClass( 'gravity-select-parent' ) ) {
			jQuery( this ).parent().find( '.select-arrow' ).remove();
			jQuery( this ).unwrap( '<div class="gravity-select-parent"></div>' );
		}
	});
});

// Setup a recursive function to handle gform multipart form selects
function recursiveGFormSubmissionHandler() {
	if ( jQuery( '.gform_wrapper' ).find( 'form' ).attr( 'target' ) && jQuery( '.gform_wrapper' ).find( 'form' ).attr( 'target' ).indexOf( 'gform_ajax_frame' ) > -1 ) {
		jQuery( '.gform_wrapper' ).find( 'form' ).submit( function( event ) {
			setTimeout(
				function() {
					wrapGravitySelects();
					calcSelectArrowDimensions();
					recursiveGFormSubmissionHandler();
				},
			800 );
		});
	}
}
recursiveGFormSubmissionHandler();

jQuery( window ).load( function( $ ) {

	// Remove gravity IE specific class
	jQuery( '.gform_wrapper' ).each( function() {
		jQuery( this ).removeClass( 'gf_browser_ie' );
	});

	// Wrap gravity forms select and add arrow
	wrapGravitySelects();

	// Update dimensions for gravity form elements with conditional logic.
	if ( 'undefined' !== typeof gform && gform ) {
		gform.addAction( 'gform_post_conditional_logic_field_action', function( formId, action, targetId, defaultValues, isInit ) {
			if ( 'show' == action && ! isInit ) {
				setTimeout( function() {
					calcSelectArrowDimensions();
					wrapGravitySelects();
				}, 50 );
			}
		});
	}
});
