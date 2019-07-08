// Get current height of sticky header
function getStickyHeaderHeight() {
	var $stickyHeaderType   = 1,
	    $stickyHeaderHeight = 0,
	    $mediaQueryIpad     = Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1366px) and (orientation: portrait)' ) ||  Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape)' );

	// Set header type to 2 for headers v4, v5
	if ( jQuery( '.fusion-header-v4' ).length || jQuery( '.fusion-header-v5' ).length ) {
		$stickyHeaderType = 2;
	}

	// Sticky header is enabled
	if ( '1' == avadaHeaderVars.header_sticky && jQuery( '.fusion-header-wrapper' ).length ) {

		// Desktop mode - headers v1, v2, v3
		if ( 1 == $stickyHeaderType ) {
			$stickyHeaderHeight = jQuery( '.fusion-header' ).outerHeight() - 1;

			// For headers v1 - v3 the sticky header min height is always 65px
			if ( $stickyHeaderHeight < 64 ) {
				$stickyHeaderHeight = 64;
			}

		// Desktop mode - headers v4, v5
		} else {
			$stickyHeaderHeight = jQuery( '.fusion-secondary-main-menu' ).outerHeight();

			if ( 'menu_and_logo' === avadaHeaderVars.header_sticky_type2_layout ) {
				$stickyHeaderHeight += jQuery( '.fusion-header' ).outerHeight();
			}
		}

		// Mobile mode
		if ( Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {

			// Sticky header is enabled on mobile
			if ( '1' == avadaHeaderVars.header_sticky_mobile ) {

				// Classic mobile menu
				if ( jQuery( '.fusion-mobile-menu-design-classic' ).length ) {
					$stickyHeaderHeight = jQuery( '.fusion-secondary-main-menu' ).outerHeight();
				}

				// Modern mobile menu
				if ( jQuery( '.fusion-mobile-menu-design-modern' ).length ) {
					$stickyHeaderHeight = jQuery( '.fusion-header' ).outerHeight();
				}

			// Sticky header is disabled on mobile
			} else {
				$stickyHeaderHeight = 0;
			}
		}

		// Tablet mode
		if ( '1' != avadaHeaderVars.header_sticky_tablet && $mediaQueryIpad ) {
			$stickyHeaderHeight = 0;
		}
	}

	return $stickyHeaderHeight;
}

// Calculate height of sticky header on page load
function getWaypointTopOffset() {
	var $stickyHeaderHeight = 0,
	    $mediaQueryIpad     = Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1366px) and (orientation: portrait)' ) ||  Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape)' ),
	    $stickyHeaderType   = 1;

		if ( jQuery( '.fusion-header-v4' ).length || jQuery( '.fusion-header-v5' ).length ) {
		   $stickyHeaderType = 2;
		}

	// Sticky header is enabled
	if ( '1' == avadaHeaderVars.header_sticky && jQuery( '.fusion-header-wrapper' ).length ) {

		// Desktop mode - headers v1, v2, v3
		if ( 1 == $stickyHeaderType ) {
			$stickyHeaderHeight = jQuery( '.fusion-header' ).outerHeight() - 1;

		// Desktop mode - headers v4, v5
		} else {

			// Menu only
			$stickyHeaderHeight = jQuery( '.fusion-secondary-main-menu' ).outerHeight();

			// Menu and logo
			if ( 'menu_and_logo' === avadaHeaderVars.header_sticky_type2_layout ) {
				$stickyHeaderHeight += jQuery( '.fusion-header' ).outerHeight() - 1;
			}
		}

		// Mobile mode
		if ( Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {

			// Sticky header is enabled on mobile
			if ( '1' == avadaHeaderVars.header_sticky_mobile ) {
				$stickyHeaderHeight = jQuery( '.fusion-header' ).outerHeight() - 1;

			// Sticky header is disabled on mobile
			} else {
				$stickyHeaderHeight = 0;
			}
		}

		// Tablet mode
		if ( '1' != avadaHeaderVars.header_sticky_tablet && $mediaQueryIpad ) {
			$stickyHeaderHeight = 0;
		}
	}

	return $stickyHeaderHeight;
}

jQuery( window ).load( function() {

	var $animationDuration,
	    $headerParent,
	    $menuHeight,
	    $menuBorderHeight,
	    $logo,
	    $stickyHeaderScrolled,
	    $logoImage,
	    resizeWidth,
	    resizeHeight,
	    sliderScroll,
	    sliderSticky,
	    marginTop,
	    stickySliderTop;

	jQuery( window ).scroll( function() {
		if ( jQuery( '#sliders-container .tfs-slider' ).data( 'parallax' ) && 'wide' !== avadaHeaderVars.layout_mode && ! cssua.ua.tablet_pc && ! cssua.ua.mobile && Modernizr.mq( 'only screen and (min-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) && 'full' === avadaHeaderVars.offset_scroll ) {
			sliderSticky    = jQuery( '#sliders-container .tfs-slider' );
			sliderScroll    = jQuery( window ).scrollTop();
			stickySliderTop = 0;
			marginTop       = jQuery( 'body' ).css( 'marginTop' );
			marginTop       = parseInt( marginTop );

			if ( '1' == avadaHeaderVars.header_sticky && ( jQuery( '.fusion-header-wrapper' ).length >= 1 || jQuery( '#side-header' ).length >= 1 )  ) {
				$menuHeight = parseInt( jQuery( '.fusion-header' ).height() );
				stickySliderTop = 0;
			} else {
				$menuHeight     = marginTop;
				stickySliderTop = parseInt( avadaHeaderVars.nav_height );
				if ( jQuery( '#side-header' ).length < 1 ) {
					$menuHeight = 0;
				}
			}
			if ( sliderScroll >= jQuery( '#wpadminbar' ).height() + marginTop + stickySliderTop ) {
				sliderSticky.css( 'top', 0 );
				sliderSticky.addClass( 'fusion-fixed-slider' );
			} else {
				sliderSticky.css( 'top', 0 );
				sliderSticky.removeClass( 'fusion-fixed-slider' );
			}
		} else if ( jQuery( '#sliders-container .tfs-slider.fusion-fixed-slider' ).length ) {
			jQuery( '#sliders-container .tfs-slider.fusion-fixed-slider' ).removeClass( 'fusion-fixed-slider' );
		}
	} );

	// Sticky Header
	if ( '1' == avadaHeaderVars.header_sticky && ( jQuery( '.fusion-header-wrapper' ).length >= 1 || jQuery( '#side-header' ).length >= 1 )  ) {
		$animationDuration = 300;
		if ( '0' == avadaHeaderVars.sticky_header_shrinkage ) {
			$animationDuration = 0;
		}
		$headerParent                   = jQuery( '.fusion-header' ).parent();
		window.$headerParentHeight      = $headerParent.outerHeight();
		window.$headerHeight            = jQuery( '.fusion-header' ).outerHeight();
		$menuHeight                     = parseInt( avadaHeaderVars.nav_height );
		window.$menuHeight              = $menuHeight,
		$menuBorderHeight               = parseInt( avadaHeaderVars.nav_highlight_border );
		window.$scrolled_header_height  = 65;
		$logo                           = ( jQuery( '.fusion-logo img:visible' ).length ) ? jQuery( '.fusion-logo img:visible' ) : '';
		$stickyHeaderScrolled           = false;
		window.$stickyTrigger           = jQuery( '.fusion-header' );
		window.$wpadminbarHeight        = ( jQuery( '#wpadminbar' ).length ) ? jQuery( '#wpadminbar' ).height() : 0;
		window.$stickyTrigger_position  = ( window.$stickyTrigger.length ) ? Math.round( window.$stickyTrigger.offset().top ) - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame : 0;
		window.$woo_store_notice        = ( jQuery( '.demo_store' ).length ) ? jQuery( '.demo_store' ).outerHeight() : 0;
		window.$top_frame               = ( jQuery( '.fusion-top-frame' ).length ) ? jQuery( '.fusion-top-frame' ).outerHeight() - window.$woo_store_notice : 0;
		window.$sticky_header_type      = 1;
		window.$logo_height, window.$main_menu_height;
		window.$slider_offset           = 0;
		window.$site_width              = jQuery( '#wrapper' ).outerWidth();
		window.$media_query_test_1      = Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1366px) and (orientation: portrait)' ) ||  Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape)' );
		window.$media_query_test_2      = Modernizr.mq( 'screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' );
		window.$media_query_test_3      = Modernizr.mq( 'screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' );
		window.$media_query_test_4      = Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' );
		window.$standardLogoHeight      = jQuery( '.fusion-standard-logo' ).height() + parseInt( jQuery( '.fusion-logo' ).data( 'margin-top' ) ) + parseInt( jQuery( '.fusion-logo' ).data( 'margin-bottom' ) );

		window.$initial_desktop_header_height   = Math.max( window.$headerHeight, Math.round( Math.max( window.$menuHeight, window.$standardLogoHeight ) + parseFloat( jQuery( '.fusion-header' ).find( '.fusion-row' ).css( 'padding-top' ) ) + parseFloat( jQuery( '.fusion-header' ).find( '.fusion-row' ).css( 'padding-bottom' ) ) ) );
		window.$initial_sticky_header_shrinkage = avadaHeaderVars.sticky_header_shrinkage;
		window.$sticky_can_be_shrinked          = true;

		if ( '0' == avadaHeaderVars.sticky_header_shrinkage ) {
			$animationDuration = 0;
			window.$scrolled_header_height = window.$headerHeight;
		}

		if ( $logo ) {

			// Getting the correct natural height of the visible logo
			if ( $logo.hasClass( 'fusion-logo-2x' ) ) {
				$logoImage = new Image();
				$logoImage.src = $logo.attr( 'src' );
				window.original_logo_height = parseInt( $logo.height() ) + parseInt( avadaHeaderVars.logo_margin_top ) + parseInt( avadaHeaderVars.logo_margin_bottom );
			} else {

				// For normal logo we need to setup the image object to get the natural heights
				$logoImage = new Image();
				$logoImage.src = $logo.attr( 'src' );
				window.original_logo_height = parseInt( $logoImage.naturalHeight ) + parseInt( avadaHeaderVars.logo_margin_top ) + parseInt( avadaHeaderVars.logo_margin_bottom );

			}
		}

		// Different sticky header behavior for header v4/v5
		// Instead of header with logo, secondary menu is made sticky
		if ( jQuery( '.fusion-header-v4' ).length >= 1 || jQuery( '.fusion-header-v5' ).length >= 1 ) {
			window.$sticky_header_type = 2;
			if ( 'menu_and_logo' === avadaHeaderVars.header_sticky_type2_layout || ( Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) && 'modern' === avadaHeaderVars.mobile_menu_design ) ) {
				window.$stickyTrigger = jQuery( '.fusion-sticky-header-wrapper' );
			} else {
				window.$stickyTrigger = jQuery( '.fusion-secondary-main-menu' );
			}
			window.$stickyTrigger_position = Math.round( window.$stickyTrigger.offset().top ) - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame;
		}

		if ( 1 == window.$sticky_header_type ) {
			if ( Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
				window.$scrolled_header_height = window.$headerHeight;
			} else {
				window.$original_sticky_trigger_height = jQuery( window.$stickyTrigger ).outerHeight();
			}
		}

		if ( 2 == window.$sticky_header_type ) {
			if ( 'classic' === avadaHeaderVars.mobile_menu_design ) {
				jQuery( $headerParent ).height( window.$headerParentHeight );
			}

			if ( ! Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
				jQuery( $headerParent ).height( window.$headerParentHeight );
			} else {
				window.$scrolled_header_height = window.$headerParentHeight;
			}
		}

		// Side Header
		if ( jQuery( '#side-header' ).length >= 1 ) {
			window.$sticky_header_type = 3;
		}

		if ( jQuery( document ).height() - ( window.$initial_desktop_header_height - window.$scrolled_header_height ) < jQuery( window ).height() && 1 == avadaHeaderVars.sticky_header_shrinkage ) {
			window.$sticky_can_be_shrinked = false;
			jQuery( '.fusion-header-wrapper' ).removeClass( 'fusion-is-sticky' );
		} else {
			window.$sticky_can_be_shrinked = true;
		}

		resizeWidth = jQuery( window ).width();
		resizeHeight = jQuery( window ).height();

		jQuery( window ).resize( function() {
			var $menuHeight,
			    $menuBorderHeight,
			    $stickyTrigger,
			    $logoHeightWithMargin,
			    $mainMenuWidth,
			    $availableWidth,
			    $positionTop,
			    $scrolledLogoHeight,
			    $scrolledLogoContainerMargin;

			window.$media_query_test_1 = Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1366px) and (orientation: portrait)' ) ||  Modernizr.mq( 'only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape)' );
			window.$media_query_test_2 = Modernizr.mq( 'screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' );
			window.$media_query_test_3 = Modernizr.mq( 'screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' );
			window.$media_query_test_4 = Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' );

			if ( -1 !== avadaHeaderVars.header_padding_top.indexOf( '%' ) || -1 !== avadaHeaderVars.header_padding_bottom.indexOf( '%' ) ) {
				window.$initial_desktop_header_height = Math.max( window.$headerHeight, Math.round( Math.max( window.$menuHeight, window.$standardLogoHeight ) + parseFloat( jQuery( '.fusion-header' ).find( '.fusion-row' ).css( 'padding-top' ) ) + parseFloat( jQuery( '.fusion-header' ).find( '.fusion-row' ).css( 'padding-bottom' ) ) ) );
			}

			if ( '1' != avadaHeaderVars.header_sticky_tablet && ( window.$media_query_test_1 ) ) {
				jQuery( '.fusion-header-wrapper, .fusion-header-sticky-height, .fusion-header, .fusion-logo, .fusion-header-wrapper .fusion-main-menu > li a, .fusion-header-wrapper .fusion-secondary-main-menu' ).attr( 'style', '' );
				jQuery( '.fusion-header-wrapper' ).removeClass( 'fusion-is-sticky' );
			} else if ( '1' == avadaHeaderVars.header_sticky_tablet && ( window.$media_query_test_1 ) ) {
				$animationDuration = 0;
			}

			if ( '1' != avadaHeaderVars.header_sticky_mobile && window.$media_query_test_2 ) {
				jQuery( '.fusion-header-wrapper, .fusion-header-sticky-height, .fusion-header, .fusion-logo, .fusion-header-wrapper .fusion-main-menu > li a, .fusion-header-wrapper .fusion-secondary-main-menu' ).attr( 'style', '' );
				jQuery( '.fusion-header-wrapper' ).removeClass( 'fusion-is-sticky' );
			} else if ( '1' == avadaHeaderVars.header_sticky_mobile && window.$media_query_test_2 ) {
				$animationDuration = 0;
			}

			// Check the variable stored dimensions are not 0.
			if ( ( resizeWidth && resizeHeight ) && ( jQuery( window ).width() != resizeWidth || jQuery( window ).height() != resizeHeight ) ) { // Check for actual resize
				$menuHeight = parseInt( avadaHeaderVars.nav_height );
				$menuBorderHeight = parseInt( avadaHeaderVars.nav_highlight_border );

				if ( jQuery( '#wpadminbar' ).length ) {
					window.$wpadminbarHeight = jQuery( '#wpadminbar' ).height();
				} else {
					window.$wpadminbarHeight = 0;
				}

				window.$woo_store_notice = ( jQuery( '.demo_store' ).length ) ? jQuery( '.demo_store' ).outerHeight() : 0;

				if ( jQuery( '.fusion-is-sticky' ).length ) {
					$stickyTrigger = jQuery( '.fusion-header' );

					if ( 2 == window.$sticky_header_type ) {
						if ( 'menu_only' === avadaHeaderVars.header_sticky_type2_layout && ( 'classic' === avadaHeaderVars.mobile_menu_design || ! window.$media_query_test_4 ) ) {
							$stickyTrigger = jQuery( '.fusion-secondary-main-menu' );
						} else {
							$stickyTrigger = jQuery( '.fusion-sticky-header-wrapper' );
						}
					}

					if ( jQuery( '#wpadminbar' ).length ) {

						// Unset the top value for all candidates
						jQuery( '.fusion-header, .fusion-sticky-header-wrapper, .fusion-secondary-main-menu' ).css( 'top', '' );

						// Set top value for coreect selector
						jQuery( $stickyTrigger ).css( 'top', window.$wpadminbarHeight + window.$woo_store_notice + window.$top_frame );
					}

					if ( 'boxed' === avadaHeaderVars.layout_mode ) {
						jQuery( $stickyTrigger ).css( 'max-width', jQuery( '#wrapper' ).outerWidth() + 'px' );
					}
				}

				// Refresh header v1, v2, v3 and v6
				if ( 1 == window.$sticky_header_type ) {
					avadaHeaderVars.sticky_header_shrinkage = window.$initial_sticky_header_shrinkage;

					if ( jQuery( '.fusion-secondary-header' ).length ) {
						window.$stickyTrigger_position = Math.round( jQuery( '.fusion-secondary-header' ).offset().top )  - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame + jQuery( '.fusion-secondary-header' ).outerHeight();

					// If there is no secondary header, trigger position is 0
					} else {
						window.$stickyTrigger_position = Math.round( jQuery( '.fusion-header' ).offset().top )  - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame;
					}

					// Desktop mode
					if ( ! Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
						$logoHeightWithMargin = jQuery( '.fusion-logo img:visible' ).outerHeight() + parseInt( avadaHeaderVars.logo_margin_top ) + parseInt( avadaHeaderVars.logo_margin_bottom );
						$mainMenuWidth = 0;

						// Calculate actual menu width
						jQuery( '.fusion-main-menu > ul > li' ).each( function() {
							$mainMenuWidth += jQuery( this ).outerWidth();
						});

						if ( jQuery( '.fusion-header-v6' ).length ) {
							$mainMenuWidth = 0;
						}

						// Sticky desktop header
						if ( jQuery( '.fusion-is-sticky' ).length ) {
							if ( $mainMenuWidth > ( jQuery( '.fusion-header .fusion-row' ).width() - jQuery( '.fusion-logo img:visible' ).outerWidth() ) ) {
								window.$headerHeight = jQuery( '.fusion-main-menu' ).outerHeight() + $logoHeightWithMargin;
								if ( jQuery( '.fusion-header-v7' ).length ) {
									window.$headerHeight = jQuery( '.fusion-middle-logo-menu' ).height();
								}

								// Headers v2 and v3 have a 1px bottom border
								if ( jQuery( '.fusion-header-v2' ).length || jQuery( '.fusion-header-v3' ).length ) {
									window.$headerHeight += 1;
								}
							} else {
								if ( '0' == avadaHeaderVars.sticky_header_shrinkage ) {
									if ( window.original_logo_height > $menuHeight ) {
										window.$headerHeight = window.original_logo_height;
									} else {
										window.$headerHeight = $menuHeight;
									}

									window.$headerHeight += parseFloat( jQuery( '.fusion-header > .fusion-row' ).css( 'padding-top' ) ) + parseFloat( jQuery( '.fusion-header > .fusion-row' ).css( 'padding-bottom' ) );
									window.$headerHeight = Math.round( window.$headerHeight );

									// Headers v2 and v3 have a 1px bottom border
									if ( jQuery( '.fusion-header-v2' ).length || jQuery( '.fusion-header-v3' ).length ) {
										window.$headerHeight += 1;
									}
								} else {
									window.$headerHeight = 65;
								}
							}

							window.$scrolled_header_height = window.$headerHeight;

							jQuery( '.fusion-header-sticky-height' ).css( 'height', window.$headerHeight );
							jQuery( '.fusion-header' ).css( 'height', window.$headerHeight );

						// Non sticky desktop header.
						} else {
							$availableWidth =  jQuery( '.fusion-header .fusion-row' ).width() - jQuery( '.fusion-logo img:visible' ).outerWidth();
							if ( jQuery( '.fusion-header-v7' ).length ) {
								$availableWidth =  jQuery( '.fusion-header .fusion-row' ).width();
							}
							if ( $mainMenuWidth > $availableWidth ) {
								window.$headerHeight = jQuery( '.fusion-main-menu' ).outerHeight() + $logoHeightWithMargin;
								if ( jQuery( '.fusion-header-v7' ).length ) {
									window.$headerHeight = jQuery( '.fusion-middle-logo-menu' ).height();
								}
								avadaHeaderVars.sticky_header_shrinkage = '0';
							} else {
								if ( window.original_logo_height > $menuHeight ) {
									window.$headerHeight = window.original_logo_height;
								} else {
									window.$headerHeight = $menuHeight;
								}

								if ( jQuery( '.fusion-header-v7' ).length ) {
									window.$headerHeight = jQuery( '.fusion-main-menu' ).outerHeight();
								}
							}

							window.$headerHeight += parseFloat( jQuery( '.fusion-header > .fusion-row' ).css( 'padding-top' ) ) + parseFloat( jQuery( '.fusion-header > .fusion-row' ).css( 'padding-bottom' ) );
							window.$headerHeight = Math.round( window.$headerHeight );

							// Headers v2 and v3 have a 1px bottom border
							if ( jQuery( '.fusion-header-v2' ).length || jQuery( '.fusion-header-v3' ).length ) {
								window.$headerHeight += 1;
							}

							window.$scrolled_header_height = 65;

							if ( '0' == avadaHeaderVars.sticky_header_shrinkage ) {
								window.$scrolled_header_height = window.$headerHeight;
							}

							jQuery( '.fusion-header-sticky-height' ).css( 'height', window.$headerHeight );
							jQuery( '.fusion-header' ).css( 'height', window.$headerHeight );
						}
					}

					// Mobile mode
					if ( Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
						jQuery( '.fusion-header' ).css( 'height', '' );

						window.$headerHeight = jQuery( '.fusion-header' ).outerHeight();
						window.$scrolled_header_height = window.$headerHeight;

						jQuery( '.fusion-header-sticky-height' ).css( 'height', window.$scrolled_header_height );
					}
				}

				// Refresh header v4 and v5
				if ( 2 == window.$sticky_header_type ) {
					if ( 'modern' === avadaHeaderVars.mobile_menu_design ) {

						// Desktop mode and sticky active
						if ( ! Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) && jQuery( '.fusion-is-sticky' ).length && 'menu_only' === avadaHeaderVars.header_sticky_type2_layout ) {
							window.$headerParentHeight = jQuery( '.fusion-header' ).parent().outerHeight() + jQuery( '.fusion-secondary-main-menu' ).outerHeight();
						} else {
							window.$headerParentHeight = jQuery( '.fusion-header' ).parent().outerHeight();
						}
						window.$scrolled_header_height = window.header_parent_height;

						// Desktop Mode
						if ( ! Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
							window.$headerParentHeight = jQuery( '.fusion-header' ).outerHeight() + jQuery( '.fusion-secondary-main-menu' ).outerHeight();
							window.$stickyTrigger_position = Math.round( jQuery( '.fusion-header' ).offset().top )  - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame + jQuery( '.fusion-header' ).outerHeight();

							jQuery( $headerParent ).height( window.$headerParentHeight );
							jQuery( '.fusion-header-sticky-height' ).css( 'height', '' );
						}

						// Mobile Mode
						if ( Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {

							// Trigger position basis is fusion-secondary-header, if there is a secondary header
							if ( jQuery( '.fusion-secondary-header' ).length ) {
								window.$stickyTrigger_position = Math.round( jQuery( '.fusion-secondary-header' ).offset().top )  - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame + jQuery( '.fusion-secondary-header' ).outerHeight();

							// If there is no secondary header, trigger position is 0
							} else {
								window.$stickyTrigger_position = Math.round( jQuery( '.fusion-header' ).offset().top )  - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame;
							}

							jQuery( $headerParent ).height( '' );
							jQuery( '.fusion-header-sticky-height' ).css( 'height', jQuery( '.fusion-sticky-header-wrapper' ).outerHeight() ).hide();
						}
					}

					if ( 'classic' === avadaHeaderVars.mobile_menu_design ) {
						window.$headerParentHeight = jQuery( '.fusion-header' ).outerHeight() + jQuery( '.fusion-secondary-main-menu' ).outerHeight();
						window.$stickyTrigger_position = Math.round( jQuery( '.fusion-header' ).offset().top ) - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame + jQuery( '.fusion-header' ).outerHeight();

						jQuery( $headerParent ).height( window.$headerParentHeight );
					}
				}

				// Refresh header v3
				if ( 3 == window.$sticky_header_type ) {
					$positionTop = '';

					// Desktop mode
					if ( ! Modernizr.mq( 'only screen and (max-width:' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
						jQuery( '#side-header-sticky' ).css({
							height: '',
							top: ''
						});

						if ( jQuery( '#side-header' ).hasClass( 'fusion-is-sticky' ) ) {
							jQuery( '#side-header' ).css({
								top: '',
								position: ''
							});

							jQuery( '#side-header' ).removeClass( 'fusion-is-sticky' );
						}
					}
				}

				if ( jQuery( document ).height() - ( window.$initial_desktop_header_height - window.$scrolled_header_height ) < jQuery( window ).height() && 1 == avadaHeaderVars.sticky_header_shrinkage ) {
					window.$sticky_can_be_shrinked = false;
					jQuery( '.fusion-header-wrapper' ).removeClass( 'fusion-is-sticky' );
					jQuery( '.fusion-header-sticky-height' ).hide();
					jQuery( '.fusion-header' ).css( 'height', '' );

					jQuery( '.fusion-logo' ).css({
						'margin-top': '',
						'margin-bottom': ''
					});

					jQuery( '.fusion-main-menu > ul > li > a' ).css({
						'height': '',
						'line-height': ''
					});

					jQuery( '.fusion-logo img' ).css( 'height', '' );
				} else {
					window.$sticky_can_be_shrinked = true;

					// Resizing sticky header
					if ( jQuery( '.fusion-is-sticky' ).length >= 1 ) {
						if ( 1 == window.$sticky_header_type && ! Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {

							// Animate Header Height
							if ( ! Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
								if ( window.$headerHeight == window.$initial_desktop_header_height ) {
									jQuery( window.$stickyTrigger ).stop( true, true ).animate({
										height: window.$scrolled_header_height
									}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
										jQuery( this ).css( 'overflow', 'visible' );
									} });
									jQuery( '.fusion-header-sticky-height' ).show();
									jQuery( '.fusion-header-sticky-height' ).stop( true, true ).animate({
										height: window.$scrolled_header_height
									}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
										jQuery( this ).css( 'overflow', 'visible' );
									} });
								} else {
									jQuery( '.fusion-header-sticky-height' ).show();
								}
							} else {
								jQuery( '.fusion-header-sticky-height' ).css( 'height', window.$scrolled_header_height ).show();
							}

							// Animate Logo
							if ( '1' == avadaHeaderVars.sticky_header_shrinkage && window.$headerHeight == window.$initial_desktop_header_height ) {
								if ( $logo ) {
									$scrolledLogoHeight = $logo.height();

									if (  $scrolledLogoHeight < window.$scrolled_header_height - 10 ) {
										$scrolledLogoContainerMargin = ( window.$scrolled_header_height - $scrolledLogoHeight ) / 2;
									} else {
										$scrolledLogoHeight = window.$scrolled_header_height - 10;
										$scrolledLogoContainerMargin = 5;
									}

									$logo.stop( true, true ).animate({
										'height': $scrolledLogoHeight
									}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
										jQuery( this ).css( 'display', '' );
									}, step: function() {
										jQuery( this ).css( 'display', '' );
									} });
								}

								jQuery( '.fusion-logo' ).stop( true, true ).animate({
									'margin-top': $scrolledLogoContainerMargin,
									'margin-bottom': $scrolledLogoContainerMargin
								}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic' });

								// Animate Menu Height
								if ( ! jQuery( '.fusion-header-v6' ).length ) {
									jQuery( '.fusion-main-menu > ul > li' ).not( '.fusion-middle-logo-menu-logo' ).find( '> a' ).stop( true, true ).animate({
										height: window.$scrolled_header_height,
										'line-height': window.$scrolled_header_height - $menuBorderHeight
									}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic' });
								}
							}
						}
					}
				}

				resizeWidth = jQuery( window ).width();
				resizeHeight = jQuery( window ).height();
			}
		}); // End resize event

		jQuery( window ).scroll( function() {

			var $scrolledLogoHeight,
			    $scrolledLogoContainerMargin;

			if ( window.$sticky_can_be_shrinked ) {
				if ( '1' != avadaHeaderVars.header_sticky_tablet && ( window.$media_query_test_1 ) ) {
					return;
				} else if ( '1' == avadaHeaderVars.header_sticky_tablet && ( window.$media_query_test_1 ) ) {
					$animationDuration = 0;
				}

				if ( '1' != avadaHeaderVars.header_sticky_mobile && window.$media_query_test_2 && ! window.$media_query_test_1 ) {
					return;
				} else if ( '1' == avadaHeaderVars.header_sticky_mobile && window.$media_query_test_2 ) {
					$animationDuration = 0;
				}

				if ( 3 == window.$sticky_header_type && '1' != avadaHeaderVars.header_sticky_mobile ) {
					return;
				}

				if ( 3 == window.$sticky_header_type && '1' == avadaHeaderVars.header_sticky_mobile && ! window.$media_query_test_3 ) {
					return;
				}

				// Change the sticky trigger position to the bottom of the mobile menu
				if ( 0 === jQuery( '.fusion-is-sticky' ).length && jQuery( '.fusion-header, .fusion-secondary-main-menu' ).find( '.fusion-mobile-nav-holder > ul' ).is( ':visible' ) ) {
					window.$stickyTrigger_position = Math.round( jQuery( '.fusion-header, .fusion-sticky-header-wrapper' ).find( '.fusion-mobile-nav-holder:visible' ).offset().top ) - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame + jQuery( '.fusion-header, .fusion-sticky-header-wrapper' ).find( '.fusion-mobile-nav-holder:visible' ).height();
				}

				// If sticky header is not active, reassign the triggers
				if ( 3 != window.$sticky_header_type && 0 === jQuery( '.fusion-is-sticky' ).length && ! jQuery( '.fusion-header, .fusion-secondary-main-menu' ).find( '.fusion-mobile-nav-holder > ul' ).is( ':visible' ) ) {
					window.$stickyTrigger = jQuery( '.fusion-header' );
					window.$stickyTrigger_position = Math.round( window.$stickyTrigger.offset().top )  - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame;

					if ( 2 == window.$sticky_header_type ) {
						if ( 'menu_and_logo' === avadaHeaderVars.header_sticky_type2_layout || ( window.$media_query_test_4 && 'modern' === avadaHeaderVars.mobile_menu_design ) ) {
							window.$stickyTrigger = jQuery( '.fusion-sticky-header-wrapper' );
						} else {
							window.$stickyTrigger = jQuery( '.fusion-secondary-main-menu' );
						}
						window.$stickyTrigger_position = Math.round( window.$stickyTrigger.offset().top )  - window.$wpadminbarHeight - window.$woo_store_notice - window.$top_frame;
					}

					// Set sticky header height for header v4 and v5
					if ( 'modern' === avadaHeaderVars.mobile_menu_design && 2 == window.$sticky_header_type && ( window.$media_query_test_4 || 'menu_and_logo' === avadaHeaderVars.header_sticky_type2_layout ) ) {

						// Refresh header height on scroll
						window.$headerHeight = jQuery( window.$stickyTrigger ).outerHeight();
						window.$scrolled_header_height = window.$headerHeight;
						jQuery( '.fusion-header-sticky-height' ).css( 'height', window.$scrolled_header_height ).show();
					}
				}

				if ( jQuery( window ).scrollTop() > window.$stickyTrigger_position ) { // Sticky header mode

					if ( false === $stickyHeaderScrolled ) {
						window.$woo_store_notice = ( jQuery( '.demo_store' ).length ) ? jQuery( '.demo_store' ).outerHeight() : 0;

						jQuery( '.fusion-header-wrapper' ).addClass( 'fusion-is-sticky' );
						jQuery( window.$stickyTrigger ).css( 'top', window.$wpadminbarHeight + window.$woo_store_notice + window.$top_frame );
						$logo = jQuery( '.fusion-logo img:visible' );

						// Hide all mobile menus
						if ( 'modern' === avadaHeaderVars.mobile_menu_design ) {
							jQuery( '.fusion-header, .fusion-secondary-main-menu' ).find( '.fusion-mobile-nav-holder' ).hide();
							jQuery( '.fusion-secondary-main-menu .fusion-main-menu-search .fusion-custom-menu-item-contents' ).hide();
						} else {
							jQuery( '.fusion-header, .fusion-secondary-main-menu' ).find( '.fusion-mobile-nav-holder > ul' ).hide();
						}

						if ( 'modern' === avadaHeaderVars.mobile_menu_design ) {

							// Hide normal mobile menu if sticky menu is set in sticky header
							if ( jQuery( '.fusion-is-sticky' ).length >= 1 && jQuery( '.fusion-mobile-sticky-nav-holder' ).length >= 1 && jQuery( '.fusion-mobile-nav-holder' ).is( ':visible' ) ) {
								jQuery( '.fusion-mobile-nav-holder' ).not( '.fusion-mobile-sticky-nav-holder' ).hide();
							}
						}

						if ( 'boxed' === avadaHeaderVars.layout_mode ) {
							jQuery( window.$stickyTrigger ).css( 'max-width', jQuery( '#wrapper' ).outerWidth() );

						}

						if ( 1 == window.$sticky_header_type ) {

							// Animate Header Height
							if ( ! Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
								if ( window.$headerHeight == window.$initial_desktop_header_height ) {
									jQuery( window.$stickyTrigger ).stop( true, true ).animate({
										height: window.$scrolled_header_height
									}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
										jQuery( this ).css( 'overflow', 'visible' );
									} });

									jQuery( '.fusion-header-sticky-height' ).show();

									jQuery( '.fusion-header-sticky-height' ).stop( true, true ).animate({
										height: window.$scrolled_header_height
									}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
										jQuery( this ).css( 'overflow', 'visible' );
									} });
								} else {
									jQuery( '.fusion-header-sticky-height' ).show();
								}
							} else {
								jQuery( '.fusion-header-sticky-height' ).css( 'height', window.$scrolled_header_height ).show();
							}

							// Add sticky shadow
							setTimeout( function() {
								jQuery( '.fusion-header' ).addClass( 'fusion-sticky-shadow' );
							}, 150 );

							if ( '1' == avadaHeaderVars.sticky_header_shrinkage && window.$headerHeight == window.$initial_desktop_header_height ) {

								// Animate header padding
								jQuery( window.$stickyTrigger ).find( '.fusion-row' ).stop( true, true ).animate({
									'padding-top': 0,
									'padding-bottom': 0
								}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic' });

								// Animate Logo
								if ( $logo ) {
									$scrolledLogoHeight = $logo.height();

									$logo.attr( 'data-logo-height', $logo.height() );
									$logo.attr( 'data-logo-width', $logo.width() );

									if (  $scrolledLogoHeight < window.$scrolled_header_height - 10 ) {
										$scrolledLogoContainerMargin = ( window.$scrolled_header_height - $scrolledLogoHeight ) / 2;
									} else {
										$scrolledLogoHeight = window.$scrolled_header_height - 10;
										$scrolledLogoContainerMargin = 5;
									}

									$logo.stop( true, true ).animate({
										'height': $scrolledLogoHeight
									}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
										jQuery( this ).css( 'display', '' );
									}, step: function() {
										jQuery( this ).css( 'display', '' );
									} });
								}

								jQuery( '.fusion-logo' ).stop( true, true ).animate({
									'margin-top': $scrolledLogoContainerMargin,
									'margin-bottom': $scrolledLogoContainerMargin
								}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic' });

								// Animate Menu Height
								if ( ! jQuery( '.fusion-header-v6' ).length ) {
									jQuery( '.fusion-main-menu > ul > li' ).not( '.fusion-middle-logo-menu-logo' ).find( '> a' ).stop( true, true ).animate({
										height: window.$scrolled_header_height,
										'line-height': window.$scrolled_header_height - $menuBorderHeight
									}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic' });
								}
							}

						}

						if ( 2 == window.$sticky_header_type ) {
							if ( 'menu_and_logo' === avadaHeaderVars.header_sticky_type2_layout ) {
								jQuery( window.$stickyTrigger ).css( 'height', '' );

								// Refresh header height on scroll
								window.$headerHeight = jQuery( window.$stickyTrigger ).outerHeight();
								window.$scrolled_header_height = window.$headerHeight;
								jQuery( window.$stickyTrigger ).css( 'height', window.$scrolled_header_height );
								jQuery( '.fusion-header-sticky-height' ).css( 'height', window.$scrolled_header_height );
							}

							jQuery( '.fusion-header-sticky-height' ).show();
						}

						if ( 3 == window.$sticky_header_type && Modernizr.mq( 'only screen and (max-width:' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
							jQuery( '#side-header-sticky' ).css({
								height: jQuery( '#side-header' ).outerHeight()
							});

							jQuery( '#side-header' ).css({
								position: 'fixed',
								top: window.$wpadminbarHeight + window.$woo_store_notice + window.$top_frame
							}).addClass( 'fusion-is-sticky' );
						}

						$stickyHeaderScrolled = true;
					}
				} else if ( jQuery( window ).scrollTop() <= window.$stickyTrigger_position ) {
					jQuery( '.fusion-header-wrapper' ).removeClass( 'fusion-is-sticky' );
					jQuery( '.fusion-header' ).removeClass( 'fusion-sticky-shadow' );
					$logo = jQuery( '.fusion-logo img:visible' );

					if ( 'modern' === avadaHeaderVars.mobile_menu_design ) {

						// Hide sticky menu if sticky menu is set in normal header
						if ( 0 === jQuery( '.fusion-is-sticky' ).length && jQuery( '.fusion-mobile-sticky-nav-holder' ).length >= 1 && jQuery( '.fusion-mobile-nav-holder' ).is( ':visible' ) ) {
							jQuery( '.fusion-mobile-sticky-nav-holder' ).hide();
						}
					}

					if ( 1 == window.$sticky_header_type ) {

						// Animate Header Height to Original Size
						if ( ! Modernizr.mq( 'only screen and (max-width: ' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {

							// Done to make sure that resize event while sticky is active doesn't lead to no animation on scroll up
							if ( 1 == window.$sticky_header_type && 65 == window.$headerHeight ) {
								window.$headerHeight = window.$initial_desktop_header_height;
							}

							if ( window.$headerHeight == window.$initial_desktop_header_height ) {
								jQuery( window.$stickyTrigger ).stop( true, true ).animate({
									height: window.$headerHeight
								}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
									jQuery( this ).css( 'overflow', 'visible' );
								}, step: function() {
									jQuery( this ).css( 'overflow', 'visible' );
								} });

								jQuery( '.fusion-header-sticky-height' ).stop( true, true ).animate({
									height: window.$headerHeight
								}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
									jQuery( this ).css( 'overflow', 'visible' );
								}, step: function() {
									jQuery( this ).css( 'overflow', 'visible' );
								} });
							} else {
								if ( jQuery( '.fusion-header-v7' ).length ) {
									jQuery( '.fusion-header-sticky-height' ).css( 'height', jQuery( '.fusion-middle-logo-menu' ).height() );
									jQuery( '.fusion-header' ).css( 'height', jQuery( '.fusion-middle-logo-menu' ).height() );
								}
							}
							jQuery( '.fusion-header-sticky-height' ).hide();
						} else {
							jQuery( '.fusion-header-sticky-height' ).hide().css( 'height', window.$headerHeight );
						}

						if ( '1' == avadaHeaderVars.sticky_header_shrinkage && window.$headerHeight == window.$initial_desktop_header_height ) {

							// Animate header padding to Original Size
							jQuery( window.$stickyTrigger ).find( '.fusion-row' ).stop( true, true ).animate({
								'padding-top': avadaHeaderVars.header_padding_top,
								'padding-bottom': avadaHeaderVars.header_padding_bottom
							}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic' });

							// Animate Logo to Original Size
							if ( $logo ) {
								$logo.stop( true, true ).animate({
									'height': $logo.data( 'logo-height' )
								}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic', complete: function() {
									jQuery( this ).css( 'display', '' );
									jQuery( '.fusion-sticky-logo-1x, .fusion-sticky-logo-2x' ).css( 'height', '' );
								} });
							}

							jQuery( '.fusion-logo' ).stop( true, true ).animate({
								'margin-top': jQuery( '.fusion-logo' ).data( 'margin-top' ),
								'margin-bottom': jQuery( '.fusion-logo' ).data( 'margin-bottom' )
							}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic' });

							// Animate Menu Height to Original Size
							if ( ! jQuery( '.fusion-header-v6' ).length ) {
								jQuery( '.fusion-main-menu > ul > li' ).not( '.fusion-middle-logo-menu-logo' ).find( '> a' ).stop( true, true ).animate({
									height: $menuHeight,
									'line-height': $menuHeight - $menuBorderHeight
								}, { queue: false, duration: $animationDuration, easing: 'easeOutCubic' });
							}
						}
					}

					if ( 2 == window.$sticky_header_type ) {
						jQuery( '.fusion-header-sticky-height' ).hide();

						if ( 'menu_and_logo' == avadaHeaderVars.header_sticky_type2_layout ) {
							jQuery( window.$stickyTrigger ).css( 'height', '' );

							// Refresh header height on scroll
							window.$headerHeight = jQuery( window.$stickyTrigger ).outerHeight();
							window.$scrolled_header_height = window.$headerHeight;
							jQuery( window.$stickyTrigger ).css( 'height', window.$scrolled_header_height );
							jQuery( '.fusion-header-sticky-height' ).css( 'height', window.$scrolled_header_height );
						}

					}

					if ( 3 == window.$sticky_header_type && Modernizr.mq( 'only screen and (max-width:' + avadaHeaderVars.side_header_break_point + 'px)' ) ) {
						jQuery( '#side-header-sticky' ).css({
							height: ''
						});

						jQuery( '#side-header' ).css({
							'position': ''
						}).removeClass( 'fusion-is-sticky' );
					}

					$stickyHeaderScrolled = false;
				}

			}
		});

		jQuery( window ).trigger( 'scroll' ); // Trigger scroll for page load
	}

	// Initial resize to set heights.
	setTimeout( function() {
		resizeWidth = resizeHeight = 0;
		jQuery( window ).trigger( 'resize' );
	}, 10 );

	jQuery( window ).on( 'resize', function() {

		// Check for woo demo bar which can take on 2 lines and thus sticky position must be calculated
		if ( jQuery( '.demo_store' ).length && ! jQuery( '.fusion-top-frame' ).length ) {
			jQuery( '#wrapper' ).css( 'margin-top', jQuery( '.demo_store' ).outerHeight() );
			if ( jQuery( '.sticky-header' ).length ) {
				jQuery( '.sticky-header' ).css( 'margin-top', jQuery( '.demo_store' ).outerHeight() );
			}
		}

		if ( jQuery( '.sticky-header' ).length ) {
			if ( 765 > jQuery( window ).width() ) {
				jQuery( 'body.admin-bar #header-sticky.sticky-header' ).css( 'top', '46px' );
			} else {
				jQuery( 'body.admin-bar #header-sticky.sticky-header' ).css( 'top', '32px' );
			}
		}
	});
});

// Reintalize scripts after ajax
jQuery( document ).ajaxComplete( function() {

	var $stickyTrigger,
	    $menuBorderHeight,
	    $menuHeight,
	    $menuLineHeight;

	jQuery( window ).trigger( 'scroll' ); // Trigger scroll for page load

	if ( 1 <= jQuery( '.fusion-is-sticky' ).length && window.$stickyTrigger && 3 != window.$sticky_header_type ) {
		$stickyTrigger    = jQuery( window.$stickyTrigger );
		$menuBorderHeight = parseInt( avadaHeaderVars.nav_highlight_border );
		$menuHeight       = $stickyTrigger.height();
		$menuLineHeight   = $stickyTrigger.height() - $menuBorderHeight;

		if ( 2 == window.$sticky_header_type ) {
			$stickyTrigger  = jQuery( '.fusion-secondary-main-menu' );
			$menuHeight     = $stickyTrigger.find( '.fusion-main-menu > ul > li > a' ).outerHeight();
			$menuLineHeight = $menuHeight - $menuBorderHeight;
		}

		jQuery( '.fusion-main-menu > ul > li' ).not( '.fusion-middle-logo-menu-logo' ).find( '> a' ).css({
			height: $menuHeight + 'px',
			'line-height': $menuLineHeight + 'px'
		});
	}
});
