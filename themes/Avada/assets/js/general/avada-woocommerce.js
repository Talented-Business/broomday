function fusionResizeCrossfadeImages( $parent ) {
	var $parentHeight = $parent.height();

	$parent.find( 'img' ).each( function() {
		var $imgHeight = jQuery( this ).height();

		if ( $imgHeight < $parentHeight ) {
			jQuery( this ).css( 'margin-top', ( ( $parentHeight - $imgHeight ) / 2 )  + 'px' );
		}
	});
}

function fusionResizeCrossfadeImagesContainer( $container ) {
	var $biggestHeight = 0;

	$container.find( 'img' ).each( function() {
		var $imgHeight = jQuery( this ).height();

		if ( $imgHeight > $biggestHeight ) {
			$biggestHeight = $imgHeight;
		}
	});

	$container.css( 'height', $biggestHeight );
}

function fusionCalcWoocommerceTabsLayout( $tabSelector ) {
	jQuery( $tabSelector ).each( function() {
		var $menuWidth     = jQuery( this ).parent().width(),
		    $menuItems     = jQuery( this ).find( 'li' ).length,
		    $mod           = $menuWidth % $menuItems,
		    $itemWidth     = ( $menuWidth - $mod ) / $menuItems,
		    $lastItemWidth = $menuWidth - $itemWidth * ( $menuItems - 1 );

		jQuery( this ).css( 'width', $menuWidth + 'px' );
		jQuery( this ).find( 'li' ).css( 'width', $itemWidth + 'px' );
		jQuery( this ).find( 'li:last' ).css( 'width', $lastItemWidth + 'px' ).addClass( 'no-border-right' );
	});
}

// Resize crossfade images and square to be the largest image and also vertically centered
jQuery( window ).load( function() {
	jQuery( '.variations_form' ).find( '.variations .single_variation_wrap .woocommerce-variation-description' ).remove();

	jQuery( window ).resize(
		function() {
			jQuery( '.crossfade-images' ).each(
				function() {
					fusionResizeCrossfadeImagesContainer( jQuery( this ) );
					fusionResizeCrossfadeImages( jQuery( this ) );
				}
			);
		}
	);

	if ( 'function' === typeof jQuery.fn.equalHeights ) {
		jQuery( '.double-sidebars.woocommerce .social-share > li' ).equalHeights();
	}

	jQuery( '.crossfade-images' ).each( function() {
		fusionResizeCrossfadeImagesContainer( jQuery( this ) );
		fusionResizeCrossfadeImages( jQuery( this ) );
	});

	// Make the onsale badge also work on products without image
	jQuery( '.product-images' ).each(
		function() {
			if ( ! jQuery( this ).find( 'img' ).length && jQuery( this ).find( '.onsale' ).length ) {
				jQuery( this ).css( 'min-height', '45px' );
			}
		}
	);

	jQuery( '.woocommerce .images #carousel a' ).click( function( e ) {
		e.preventDefault();
	});

	// Make sure the variation image is also changed in the thumbs carousel and for lightbox
	jQuery( '.variations_form' ).on( 'change', '.variations select', function( event ) {

		var $variationsForm = jQuery( this ).parents( '.variations_form' );

		// Timeout needed to get updated image src attribute
		setTimeout( function() {
			var $sliderFirstImage           = jQuery( '.images' ).find( '#slider img:eq(0)' ),
			    $sliderFirstImageParentLink = $sliderFirstImage.parent(),
			    $sliderFirstImageSrc        = $sliderFirstImage.attr( 'src' ),
			    $thumbsFirstImage           = jQuery( '.images' ).find( '#carousel img:eq(0)' ),
			    $slider;

			if ( $sliderFirstImageParentLink && $sliderFirstImageParentLink.attr( 'href' ) ) {
				$sliderFirstImageSrc = $sliderFirstImageParentLink.attr( 'href' );
			}

			$sliderFirstImage.parent().attr( 'href', $sliderFirstImageSrc );
			$sliderFirstImage.removeAttr( 'sizes' );
			$sliderFirstImage.removeAttr( 'srcset' );

			// Refresh the lightbox
			window.avadaLightBox.refresh_lightbox();

			$thumbsFirstImage.attr( 'src', $sliderFirstImageSrc );
			$thumbsFirstImage.removeAttr( 'sizes' );
			$thumbsFirstImage.removeAttr( 'srcset' );

			$slider = jQuery( '.images #slider' ).data( 'flexslider' );
			if ( $slider ) {
				$slider.resize();
			}

			$slider = jQuery( '.images #carousel' ).data( 'flexslider' );
			if ( $slider ) {
				$slider.resize();
			}

			//$variationsForm.find( '.variations .single_variation_wrap .woocommerce-variation-description' ).remove();

		}, 1 );

		setTimeout( function() {
			var $slider;

			window.avadaLightBox.refresh_lightbox();

			$slider = jQuery( '.images #slider' ).data( 'flexslider' );
			if ( $slider ) {
				$slider.resize();
			}
		}, 500 );

		setTimeout( function() {
			window.avadaLightBox.refresh_lightbox();
		}, 1500 );
	});
});

jQuery( document ).ready( function() {
	var name,
	    avadaMyAccountActive,
	    $titleSep,
	    $titleSepClassString,
	    $titleMainSepClassString,
	    $headingOrientation,
	    i,
	    wooThumbWidth;

	jQuery( '.fusion-update-cart' ).on( 'click', function( e ) {
		e.preventDefault();
		jQuery( '.cart .actions > .button' ).trigger( 'click' );
	});

	jQuery( '.fusion-apply-coupon' ).on( 'click', function( e ) {
		e.preventDefault();
		jQuery( '.cart .actions .coupon #coupon_code' ).val( jQuery( '#avada_coupon_code' ).val() );
		jQuery( '.cart .actions .coupon .button' ).trigger( 'click' );
	});

    jQuery( '.variations_form' ).on( 'show_variation', function() {
        jQuery( '.product-type-variable .variations_form > .single_variation_wrap .woocommerce-variation-price' ).remove();
        jQuery( '.product-type-variable .variations_form > .single_variation_wrap .woocommerce-variation-availability' ).remove();
    });

	jQuery( 'body' ).on( 'click', '.add_to_cart_button', function( e ) {
		var $addToCartButton = jQuery( this );

		$addToCartButton.closest( '.product, li' ).find( '.cart-loading' ).find( 'i' ).removeClass( 'fusion-icon-check-square-o' ).addClass( 'fusion-icon-spinner' );
		$addToCartButton.closest( '.product, li' ).find( '.cart-loading' ).fadeIn();
		setTimeout( function() {
			$addToCartButton.closest( '.product, li' ).find( '.cart-loading' ).find( 'i' ).hide().removeClass( 'fusion-icon-spinner' ).addClass( 'fusion-icon-check-square-o' ).fadeIn();
			jQuery( $addToCartButton ).parents( '.fusion-clean-product-image-wrapper, li' ).addClass( 'fusion-item-in-cart' );
		}, 2000 );
	});

	jQuery( 'li' ).mouseenter(function() {
		if ( jQuery( this ).find( '.cart-loading' ).find( 'i' ).hasClass( 'fusion-icon-check-square-o' ) ) {
			jQuery( this ).find( '.cart-loading' ).fadeIn();
		}
	}).mouseleave(function() {
		if ( jQuery( this ).find( '.cart-loading' ).find( 'i' ).hasClass( 'fusion-icon-check-square-o' ) ) {
			jQuery( this ).find( '.cart-loading' ).fadeOut();
		}
	});

	if ( jQuery( '.demo_store' ).length && ! jQuery( '.fusion-top-frame' ).length ) {
		jQuery( '#wrapper' ).css( 'margin-top', jQuery( '.demo_store' ).outerHeight() );
		if ( 0 < jQuery( '#slidingbar-area' ).outerHeight() ) {
			jQuery( '.header-wrapper' ).css( 'margin-top', '0' );
		}
		if ( jQuery( '.sticky-header' ).length ) {
			jQuery( '.sticky-header' ).css( 'margin-top', jQuery( '.demo_store' ).outerHeight() );
		}
	}

	jQuery( '.catalog-ordering .orderby .current-li a' ).html( jQuery( '.catalog-ordering .orderby ul li.current a' ).html() );
	jQuery( '.catalog-ordering .sort-count .current-li a' ).html( jQuery( '.catalog-ordering .sort-count ul li.current a' ).html() );
	jQuery( '.woocommerce .shop_table .variation dd' ).after( '<br />' );
	jQuery( '.woocommerce .avada-myaccount-data th.order-actions' ).text( avadaWooCommerceVars.order_actions );

	jQuery( 'body.rtl .avada-myaccount-data .my_account_orders .order-status' ).each( function() {
		jQuery( this ).css( 'text-align', 'right' );
	});

	jQuery( '.woocommerce input' ).each( function() {
		if ( ! jQuery( this ).has( '#coupon_code' ) ) {
			name = jQuery( this ).attr( 'id' );
			jQuery( this ).attr( 'placeholder', jQuery( this ).parent().find( 'label[for=' + name + ']' ).text() );
		}
	});

	if ( jQuery( '.woocommerce #reviews #comments .comment_container .comment-text' ).length ) {
		jQuery( '.woocommerce #reviews #comments .comment_container' ).append( '<div class="clear"></div>' );
	}

	$titleSep                = avadaWooCommerceVars.title_style_type.split( ' ' );
	$titleSepClassString     = '';
	$titleMainSepClassString = '';
	$headingOrientation      = 'title-heading-left';

	for ( i = 0; i < $titleSep.length; i++ ) {
		$titleSepClassString += ' sep-' + $titleSep[ i ];
	}

	if ( $titleSepClassString.indexOf( 'underline' ) > -1 ) {
		$titleMainSepClassString = $titleSepClassString;
	}

	if ( jQuery( 'body' ).hasClass( 'rtl' ) ) {
		$headingOrientation = 'title-heading-right';
	}

	jQuery( '.woocommerce.single-product .related.products > h2' ).each( function() {
		var $relatedHeading = jQuery( this ).replaceWith( function() {
			return '<div class="fusion-title title' + $titleSepClassString + '"><h3 class="' + $headingOrientation + '">' + jQuery( this ).html() + '</h3><div class="title-sep-container"><div class="title-sep' + $titleSepClassString + ' "></div></div></div>';
		});
	});

	jQuery( '.woocommerce.single-product .upsells.products > h2' ).each( function() {
		var $relatedHeading = jQuery( this ).replaceWith( function() {
			return '<div class="fusion-title title' + $titleSepClassString + '"><h3 class="' + $headingOrientation + '">' + jQuery( this ).html() + '</h3><div class="title-sep-container"><div class="title-sep' + $titleSepClassString + ' "></div></div></div>';
		});
	});

	jQuery( '.woocommerce-tabs #comments > h2' ).each( function() {
		var $commentsHeading = jQuery( this ).replaceWith( function() {
			return '<h3>' + jQuery( this ).html() + '</h3>';
		});
	});

	if ( 'block' === jQuery( 'body .sidebar' ).css( 'display' ) ) {
		fusionCalcWoocommerceTabsLayout( '.woocommerce-tabs .tabs-horizontal' );
	}

	jQuery( '.sidebar .products,.fusion-footer-widget-area .products,#slidingbar-area .products' ).each(function() {
		jQuery( this ).removeClass( 'products-4' );
		jQuery( this ).removeClass( 'products-3' );
		jQuery( this ).removeClass( 'products-2' );
		jQuery( this ).addClass( 'products-1' );
	});

	jQuery( '.products-6 li, .products-5 li, .products-4 li, .products-3 li, .products-3 li' ).removeClass( 'last' );

	// Woocommerce nested products plugin support
	jQuery( '.subcategory-products' ).each( function() {
		jQuery( this ).addClass( 'products-' + avadaWooCommerceVars.woocommerce_shop_page_columns );
	});

	jQuery( '.woocommerce-tabs ul.tabs li a' ).unbind( 'click' );
	jQuery( 'body' ).on( 'click', '.woocommerce-tabs > ul.tabs li a', function() {

		var tab         = jQuery( this ),
		    tabsWrapper = tab.closest( '.woocommerce-tabs' );

		jQuery( 'ul.tabs li', tabsWrapper ).removeClass( 'active' );
		jQuery( '> div.panel', tabsWrapper ).hide();
		jQuery( 'div' + tab.attr( 'href' ), tabsWrapper ).show();
		tab.parent().addClass( 'active' );

		return false;
	});

	jQuery( '.woocommerce-checkout-nav a,.continue-checkout' ).on( 'click', function( e ) {
		var $adminBarHeight     = ( jQuery( '#wpadminbar' ).length ) ? jQuery( '#wpadminbar' ).height() : 0,
		    $headerDivChildren  = jQuery( '.fusion-header-wrapper' ).find( 'div' ),
		    $stickyHeaderHeight = 0,
		    $dataName,
		    $name,
		    $scrollAnchor;

		$headerDivChildren.each( function() {
			if ( 'fixed' == jQuery( this ).css( 'position' ) ) {
				$stickyHeaderHeight = jQuery( this ).height();
			}
		});

		e.preventDefault();
		jQuery( '.avada-checkout-error' ).parent().remove();

		if ( ! jQuery( '.woocommerce .avada-checkout' ).find( '.woocommerce-invalid' ).is( ':visible' ) ) {

			$dataName = jQuery( this ).attr( 'data-name' ),
			$name     = $dataName;

			if ( 'order_review' == $dataName ) {
				$name = '#' + $dataName;
			} else {
				$name = '.' + $dataName;
			}

			jQuery( 'form.checkout .col-1, form.checkout .col-2, form.checkout #order_review_heading, form.checkout #order_review' ).hide();

			jQuery( 'form.checkout' ).find( $name ).fadeIn();
			if ( 'order_review' == $name ) {
				jQuery( 'form.checkout' ).find( '#order_review_heading ' ).fadeIn();
			}

			jQuery( '.woocommerce-checkout-nav li' ).removeClass( 'is-active' );
			jQuery( '.woocommerce-checkout-nav' ).find( '[data-name=' + $dataName + ']' ).parent().addClass( 'is-active' );

			if ( jQuery( this ).hasClass( 'continue-checkout' ) && jQuery( window ).scrollTop() > 0 ) {
				if ( jQuery( '.woo-tabs-horizontal' ).length ) {
					$scrollAnchor = jQuery( '.woocommerce-checkout-nav' );
				} else {
					$scrollAnchor = jQuery( '.woocommerce-content-box.avada-checkout' );
				}

				jQuery( 'html, body' ).animate( { scrollTop: $scrollAnchor.offset().top - $adminBarHeight - $stickyHeaderHeight }, 500 );
			}
		} else {
			jQuery( '.woocommerce .avada-checkout .woocommerce-checkout' ).prepend( '<ul class="woocommerce-error"><li class="avada-checkout-error">' + avadaWooCommerceVars.woocommerce_checkout_error + '</li><ul>' );

			jQuery( 'html, body' ).animate( { scrollTop: jQuery( '.woocommerce-error' ).offset().top - $adminBarHeight - $stickyHeaderHeight }, 500 );
		}

		// Set heights of select arrows correctly
		calcSelectArrowDimensions();
	});

	// Ship to a different address toggle
	jQuery( 'body' ).on( 'click', 'input[name=ship_to_different_address]',
		function() {
			if ( jQuery( this ).is( ':checked' ) ) {
				setTimeout( function() {

					// Set heights of select arrows correctly
					calcSelectArrowDimensions();
				}, 1 );
			}
		}
	);

	if ( Modernizr.mq( 'only screen and (max-width: 479px)' ) ) {
		jQuery( '.overlay-full.layout-text-left .slide-excerpt p' ).each( function() {
			var excerpt     = jQuery( this ).html(),
			    wordArray   = excerpt.split( /[\s\.\?]+/ ), // Split based on regular expression for spaces
			    maxWords    = 10, // Max number of words
			    $totalWords = wordArray.length, // Current total of words
			    newString   = '',
			    i;

			// Roll back the textarea value with the words that it had previously before the maximum was reached
			if ( $totalWords > maxWords + 1 ) {
				 for ( i = 0; i < maxWords; i++ ) {
					newString += wordArray[ i ] + ' ';
				}
				jQuery( this ).html( newString );
			}
		});
	}

	/**
	 * WooCommerce pre 2.7 compatibility
	 */
	if ( jQuery().flexslider && jQuery( '.woocommerce .images #carousel' ).length ) {
		avadaWooCommerceVars.woocommerce_single_gallery_size = ( '' === avadaWooCommerceVars.woocommerce_single_gallery_size ) ? jQuery( '.product .images' ).width() : avadaWooCommerceVars.woocommerce_single_gallery_size;

		wooThumbWidth = parseInt( avadaWooCommerceVars.woocommerce_single_gallery_size ) / 4;
		wooThumbWidth = parseInt( wooThumbWidth, 10 );

		if ( ! jQuery( 'body.woocommerce .sidebar' ).is( ':visible' ) ) {
			wooThumbWidth = ( parseInt( avadaWooCommerceVars.woocommerce_single_gallery_size ) - 27 ) / 4;
			wooThumbWidth = parseInt( wooThumbWidth, 10 );
		}

		if ( 'undefined' !== typeof jQuery( '.woocommerce .images #carousel' ).data( 'flexslider' ) ) {
			jQuery( '.woocommerce .images #carousel' ).flexslider( 'destroy' );
			jQuery( '.woocommerce .images #slider' ).flexslider( 'destroy' );
		}

		jQuery( '.woocommerce .images #carousel' ).flexslider({
			animation: 'slide',
			controlNav: false,
			directionNav: false,
			animationLoop: false,
			slideshow: false,
			itemWidth: wooThumbWidth,
			itemMargin: 9,
			touch: false,
			useCSS: false,
			asNavFor: '.woocommerce .images #slider',
			smoothHeight: false,
			prevText: '&#xf104;',
			nextText: '&#xf105;',
			start: function( slider ) {
				jQuery( slider ).removeClass( 'fusion-flexslider-loading' );
			}
		});

		jQuery( '.woocommerce .images #slider' ).flexslider({
			animation: 'slide',
			controlNav: false,
			animationLoop: false,
			slideshow: false,
			smoothHeight: true,
			touch: true,
			useCSS: false,
			sync: '.woocommerce .images #carousel',
			prevText: '&#xf104;',
			nextText: '&#xf105;',
			start: function( slider ) {
				jQuery( slider ).removeClass( 'fusion-flexslider-loading' );
			}
		});
	} else {
        jQuery( '.woocommerce .images #slider' ).find( 'li' ).show();
	}
});

jQuery( window ).load( function() {
	var sources = window.sources,
	    imageThumbs,
	    variation,
	    variationImage;

	if ( jQuery( '.avada-product-gallery' ).length ) {
		imageThumbs = ( jQuery( '.flex-control-nav' ).find( 'img' ).length ) ? jQuery( '.flex-control-nav' ).find( 'img' ) : jQuery( '<img class="fusion-main-image-thumb">' ).attr( 'src', jQuery( '.flex-viewport' ).find( '.flex-active-slide' ).data( 'thumb' ) );

		jQuery( '.flex-viewport' ).find( '.flex-active-slide' ).addClass( 'fusion-main-image' );
		jQuery( '.flex-control-nav' ).find( 'li:eq(0) img' ).addClass( 'fusion-main-image-thumb' );

		// Trigger the variation form change on init, needed if a default variation is set.
		setTimeout( function() {
			jQuery( '.variations select' ).trigger( 'change' );
		}, 100 );

        jQuery( '.variations_form' ).on( 'found_variation', function( event, variationParam ) {
            variation = variationParam;
        });

		// Make sure the variation image is also changed in the thumbs carousel and for lightbox
		jQuery( '.variations_form' ).on( 'change', '.variations select', function( event ) {
			variationsChange( imageThumbs, variation );
        });
	}

	// Make sure correct spacing is created for the absolute positioned product image thumbs.
	if ( jQuery( '.avada-single-product-gallery-wrapper' ).find( '.flex-control-thumbs' ).length ) {
		jQuery( '.avada-single-product-gallery-wrapper' ).css( 'margin-bottom', jQuery( '.avada-single-product-gallery-wrapper' ).find( '.flex-control-thumbs' ).height() + 10 );
	}

	jQuery( '.avada-product-gallery' ).each( function() {
		var thumbsContainer = jQuery( this ).find( '.flex-control-thumbs' ),
		    maxHeight       = Math.max.apply( null, thumbsContainer.find( 'li' ).map( function() {
				return jQuery( this ).height();
		    }).get() );

		// Remove the min height setting from the gallery images.
		jQuery( '.woocommerce-product-gallery__image' ).css( 'min-height', '' );
		jQuery( document ).trigger( 'resize' );

        thumbsContainer.animate({ opacity: 1 }, 500 );

		// Make sure the thumbs container has the height of the largest thumb.
		thumbsContainer.wrap( '<div class="avada-product-gallery-thumbs-wrapper"></div>' );
		thumbsContainer.parent().css( 'height', maxHeight );
	});
});

function getVariationsValues() {
	var variations = 0,
	    chosen = 0;

	jQuery( '.variations_form .variations' ).find( 'select' ).each( function() {
		var value  = jQuery( this ).val() || '';

		if ( value.length > 0 ) {
			chosen++;
		}

		variations++;
	});

	return {
		'variations': variations,
		'chosen': chosen
	};

}

function variationsChange( imageThumbs, variation ) {
	var sources = window.sources,
	    variationImage,
	    variationSelects = getVariationsValues();

	variationImage = ( 'undefined' !== typeof variation && variation.image && variation.image.src && variation.image.src.length > 1 ) ? variation.image.src : sources[0];

	if ( variationSelects.variations !== variationSelects.chosen ) {
		jQuery( '.variations_form' ).trigger( 'update_variation_values' );
		jQuery( '.variations_form' ).trigger( 'reset_data' );

		variationImage = sources[0];
	}

	imageThumbs.each( function() {
		var mainImage,
		    productImg,
		    productLink,
		    zoomImage,
		    lightboxTrigger;

		if ( ! jQuery( this ).hasClass( 'fusion-main-image-thumb' ) ) {
			jQuery( this ).attr( 'src', sources[ jQuery( this ).data( 'index' ) ] );
		} else {
			mainImage        = jQuery( '.flex-viewport' ).find( '.fusion-main-image' );
			productImg       = mainImage.find( '.wp-post-image' );
			productLink      = mainImage.find( 'a' ).eq( 0 );
			zoomImage        = mainImage.find( '> img' );
			lightboxTrigger  = mainImage.find( '.avada-product-gallery-lightbox-trigger' );

			jQuery( this ).attr( 'src', variationImage );

			if ( 'undefined' !== typeof variation && variation.image && variation.image.src && variation.image.src.length > 1 && variationSelects.variations === variationSelects.chosen ) {
				productImg.wc_set_variation_attr( 'src', variation.image.src );
				productImg.wc_set_variation_attr( 'height', variation.image.src_h );
				productImg.wc_set_variation_attr( 'width', variation.image.src_w );
				productImg.wc_set_variation_attr( 'srcset', variation.image.srcset );
				productImg.wc_set_variation_attr( 'sizes', variation.image.sizes );
				productImg.wc_set_variation_attr( 'title', variation.image.title );
				productImg.wc_set_variation_attr( 'alt', variation.image.alt );
				productImg.wc_set_variation_attr( 'data-src', variation.image.full_src );
				productImg.wc_set_variation_attr( 'data-large_image', variation.image.full_src );
				productImg.wc_set_variation_attr( 'data-large_image_width', variation.image.full_src_w );
				productImg.wc_set_variation_attr( 'data-large_image_height', variation.image.full_src_h );
				productLink.wc_set_variation_attr( 'href', variation.image.full_src );
				zoomImage.wc_set_variation_attr( 'src', variation.image.full_src );
				lightboxTrigger.wc_set_variation_attr( 'href', variation.image.src );
			} else {
				productImg.wc_reset_variation_attr( 'src' );
				productImg.wc_reset_variation_attr( 'width' );
				productImg.wc_reset_variation_attr( 'height' );
				productImg.wc_reset_variation_attr( 'srcset' );
				productImg.wc_reset_variation_attr( 'sizes' );
				productImg.wc_reset_variation_attr( 'title' );
				productImg.wc_reset_variation_attr( 'alt' );
				productImg.wc_reset_variation_attr( 'data-src' );
				productImg.wc_reset_variation_attr( 'data-large_image' );
				productImg.wc_reset_variation_attr( 'data-large_image_width' );
				productImg.wc_reset_variation_attr( 'data-large_image_height' );
				productLink.wc_reset_variation_attr( 'href' );
				zoomImage.wc_reset_variation_attr( 'src' );
				lightboxTrigger.wc_reset_variation_attr( 'href' );
			}
		}
	});

	// Refresh the lightbox
	window.avadaLightBox.refresh_lightbox();

	setTimeout( function() {
		window.avadaLightBox.refresh_lightbox();
	}, 500 );

	setTimeout( function() {
		window.avadaLightBox.refresh_lightbox();
	}, 1500 );
}

jQuery( document ).ready( function() {
	var name,
	    avadaMyAccountActive,
	    $titleSep,
	    $titleSepClassString,
	    $titleMainSepClassString,
	    $headingOrientation,
	    i;

	// Remove the Woo native magnifying glass svg.
	setTimeout( function() {
		jQuery( '.woocommerce-product-gallery__trigger' ).empty();
	}, 10 );

	if ( jQuery( '.avada-product-gallery' ).length ) {

	}

	jQuery( '.avada-product-gallery' ).each( function() {
		var imageGallery   = jQuery( this ),
		    imageThumbs = ( jQuery( '.flex-control-nav' ).find( 'img' ).length ) ? jQuery( '.flex-control-nav' ).find( 'img' ) : undefined;
		window.sources = [];

		// Initialize the flexslider thumb sources, needed in on load event.
		if ( 'undefined' !== typeof imageThumbs ) {
			imageThumbs.each( function( index ) {
				jQuery( this ).data( 'index', index );
				window.sources.push( jQuery( this ).attr( 'src' ) );
			});
		} else {
			window.sources.push( jQuery( this ).find( '.flex-viewport .flex-active-slide' ).data( 'thumb' ) );
		}

		// Remove flexslider clones from lightbox.
		jQuery( '.flex-viewport' ).find( '.clone' ).find( '.avada-product-gallery-lightbox-trigger' ).addClass( 'fusion-no-lightbox' ).removeAttr( 'data-rel' );

		// Site the image gallery thumbnails correctly.
		sizeGalleryThumbnails( imageGallery );
		jQuery( window ).resize( function() {
			sizeGalleryThumbnails( imageGallery );
		});

		imageGallery.find( '.flex-control-thumbs li' ).on( 'click touchstart', function() {
			var nextThumb = jQuery( this );

			moveProductImageThumbs( imageGallery, nextThumb, 'next' );
		});

		imageGallery.find( '.flex-direction-nav li a' ).on( 'click touchstart', function() {
			var nextThumb = jQuery( this ).parents( '.avada-product-gallery' ).find( '.flex-control-thumbs img.flex-active' );

			if ( nextThumb.offset().left + nextThumb.outerWidth() > imageGallery.offset().left + imageGallery.outerWidth() ) {

				if ( jQuery( this ).hasClass( 'flex-next' ) ) {
					moveProductImageThumbs( imageGallery, nextThumb, 'next' );
				} else {
					moveProductImageThumbs( imageGallery, nextThumb, 'prev' );
				}
			}
		});
	});

	function sizeGalleryThumbnails( imageGallery ) {
		var galleryWidth   = imageGallery.width(),
		    thumbs         = imageGallery.find( '.flex-control-thumbs li' ),
		    thumbColumns   = imageGallery.data( 'columns' ),
		    numberOfThumbs = thumbs.size(),
		    thumbWidth;

		// Set the width of the thumbs.
		thumbWidth = ( galleryWidth - ( thumbColumns - 1 ) * 8 ) / thumbColumns;
		thumbs.css( 'width', thumbWidth );

		// Set .flex-control-thumbs width.
		imageGallery.find( '.flex-control-thumbs' ).css( 'width', ( numberOfThumbs * thumbWidth + ( numberOfThumbs - 1 ) * 8 ) + 'px' );
	}

	function moveProductImageThumbs( gallery, currentThumb, direction ) {
		var thumbsContainer         = gallery.find( '.flex-control-thumbs' ),
		    thumbs                  = thumbsContainer.find( 'li' ),
		    thumbColumns            = gallery.data( 'columns' ),
		    thumbWidth              = thumbsContainer.find( 'li' ).outerWidth(),
		    galleryLeft             = gallery.offset().left,
		    currentThumbIndex       = Math.floor( ( currentThumb.offset().left - galleryLeft ) / thumbWidth ),
		    leftOffsets             = [],
		    thumbsContainerNewLeft,
		    scrollableElements;

		if ( thumbs.length > thumbColumns ) {

			if ( 'next' === direction ) {

				if ( currentThumbIndex < thumbs.length - ( currentThumbIndex + 1 ) ) {

					// If there are enough thumbs, move the clicked thumb to first pos.
					thumbsContainerNewLeft  = currentThumb.position().left * -1;
				} else {

					// If there is less thumbs than needed to scroll into view, just scroll the amount that is there.
					scrollableElements = thumbs.length - thumbColumns;
					thumbsContainerNewLeft = jQuery( thumbs.get( scrollableElements ) ).position().left * -1;
				}

				thumbsContainer.stop( true, true ).animate({
					'left': thumbsContainerNewLeft
				}, { queue: false, duration: 500, easing: 'easeInOutQuad', complete: function() {

					jQuery( this ).find( 'li' ).each( function() {
						leftOffsets.push( jQuery( this ).offset().left );
					});

					jQuery( this ).find( 'li' ).each( function( index ) {
						if ( leftOffsets[index] < galleryLeft ) {
							jQuery( this ).appendTo( thumbsContainer );
						}
					});

					jQuery( this ).css( 'left', '0' );
				} });

			} else {
				thumbsContainerNewLeft  = ( thumbWidth + 8 ) * -1;

				currentThumb.parent().prependTo( thumbsContainer );
				thumbsContainer.css( 'left', thumbsContainerNewLeft );

				thumbsContainer.stop( true, true ).animate({
					'left': 0
				}, { queue: false, duration: 500, easing: 'easeInOutQuad' });
			}
		}
	}
});

// Reintalize scripts after ajax
jQuery( document ).ajaxComplete( function() {
	jQuery( '.fusion-update-cart' ).unbind( 'click' );
	jQuery( '.fusion-update-cart' ).on( 'click', function( e ) {
		e.preventDefault();
		jQuery( '.cart .actions > .button' ).trigger( 'click' );
	});

	// Make sure cross faded images height is correct.
	setTimeout( function() {
		jQuery( window ).trigger( 'resize' );
	}, 1000 );
});
