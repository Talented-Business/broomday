jQuery( window ).load( function() {
	if ( Boolean( Number( avadaRevVars.avada_rev_styles ) ) ) {
		jQuery( '.rev_slider_wrapper' ).each( function() {
			var revSliderWrapper = jQuery( this ),
			    revSliderID,
			    newDimension;

			if ( 1 <= revSliderWrapper.length && -1 == revSliderWrapper.attr( 'class' ).indexOf( 'tp-shadow' ) ) {
				jQuery( '<div class="shadow-left">' ).appendTo( this );
				jQuery( '<div class="shadow-right">' ).appendTo( this );

				revSliderWrapper.addClass( 'avada-skin-rev' );
			}

			if ( ! jQuery( this ).find( '.tp-leftarrow' ).hasClass( 'preview1' ) && ! jQuery( this ).find( '.tp-leftarrow' ).hasClass( 'preview2' ) && ! jQuery( this ).find( '.tp-leftarrow' ).hasClass( 'preview3' ) && ! jQuery( this ).find( '.tp-leftarrow' ).hasClass( 'preview4' ) ) {
				jQuery( this ).addClass( 'avada-skin-rev-nav' );

				if ( revSliderWrapper.find( '.tp-leftarrow' ).height() > revSliderWrapper.height() / 4 && revSliderWrapper.find( '.tp-leftarrow' ).height() < revSliderWrapper.height() ) {
					revSliderID = revSliderWrapper.attr( 'id' );
					newDimension = revSliderWrapper.height() / 4;
					if ( revSliderWrapper.children( '.avada-rev-arrows' ).length ) {
						revSliderWrapper.children( '.avada-rev-arrows' ).empty();
						revSliderWrapper.children( '.avada-rev-arrows' ).append( '<style type="text/css">#' + revSliderID + ' .tp-leftarrow, #' + revSliderID + ' .tp-rightarrow{margin-top:-' + newDimension / 2 + 'px !important;width:' + newDimension + 'px !important;height:' + newDimension + 'px !important;}#' + revSliderID + ' .tp-leftarrow:before, #' + revSliderID + ' .tp-rightarrow:before{line-height:' + newDimension  + 'px;font-size:' + newDimension / 2 + 'px;}</style>' );
					} else {
						revSliderWrapper.prepend( '<div class="avada-rev-arrows"><style type="text/css">#' + revSliderID + ' .tp-leftarrow, #' + revSliderID + ' .tp-rightarrow{margin-top:-' + newDimension / 2 + 'px !important;width:' + newDimension + 'px !important;height:' + newDimension + 'px !important;}#' + revSliderID + ' .tp-leftarrow:before, #' + revSliderID + ' .tp-rightarrow:before{line-height:' + newDimension  + 'px;font-size:' + newDimension / 2 + 'px;}</style></div>' );
					}
				}

				jQuery( window ).on( 'resize', function() {
					var revSliderID,
					    newDimension;
					if ( revSliderWrapper.find( '.tp-leftarrow' ).height() > revSliderWrapper.height() / 4 && revSliderWrapper.find( '.tp-leftarrow' ).height() < revSliderWrapper.height() ) {
						revSliderID = revSliderWrapper.attr( 'id' );
						newDimension = revSliderWrapper.height() / 4;
						if ( revSliderWrapper.children( '.avada-rev-arrows' ).length ) {
							revSliderWrapper.children( '.avada-rev-arrows' ).empty();
							revSliderWrapper.children( '.avada-rev-arrows' ).append( '<style type="text/css">#' + revSliderID + ' .tp-leftarrow, #' + revSliderID + ' .tp-rightarrow{margin-top:-' + newDimension / 2 + 'px !important;width:' + newDimension + 'px !important;height:' + newDimension + 'px !important;}#' + revSliderID + ' .tp-leftarrow:before, #' + revSliderID + ' .tp-rightarrow:before{line-height:' + newDimension  + 'px;font-size:' + newDimension / 2 + 'px;}</style>' );
						} else {
							revSliderWrapper.prepend( '<div class="avada-rev-arrows"><style type="text/css">#' + revSliderID + ' .tp-leftarrow, #' + revSliderID + ' .tp-rightarrow{margin-top:-' + newDimension / 2 + 'px !important;width:' + newDimension + 'px !important;height:' + newDimension + 'px !important;}#' + revSliderID + ' .tp-leftarrow:before, #' + revSliderID + ' .tp-rightarrow:before{line-height:' + newDimension  + 'px;font-size:' + newDimension / 2 + 'px;}</style></div>' );
						}
					} else {
						revSliderWrapper.children( '.avada-rev-arrows' ).remove();
					}
				});
			}

		});
	}
});
