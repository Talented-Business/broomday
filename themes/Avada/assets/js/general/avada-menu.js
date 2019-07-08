jQuery( document ).ready( function() {

	'use strict';

	var iframeLoaded;

	// SVG arrow class check.
	if ( jQuery( '.fusion-dropdown-svg' ).length ) {
		jQuery( '.fusion-dropdown-svg' ).each( function() {
			var firstChild = jQuery( this ).parents( 'li' ).find( '> .sub-menu > li:first-child' );
			if ( jQuery( firstChild ).hasClass( 'current-menu-item' ) || jQuery( firstChild ).hasClass( 'current-menu-parent' ) || jQuery( firstChild ).hasClass( 'current_page_item' ) ) {
				jQuery( this ).addClass( 'fusion-svg-active' );
			}
			jQuery( firstChild ).not( '.current-menu-item, .current-menu-parent, .current_page_item' ).find( '> a' ).on( 'hover', function() {
				jQuery( this ).parents( 'li' ).find( '.fusion-dropdown-svg' ).toggleClass( 'fusion-svg-active' );
			});
		});
	}

	// Position dropdown menu correctly
	jQuery.fn.fusion_position_menu_dropdown = function( variables ) {

			if ( ( 'Top' === avadaMenuVars.header_position && ! jQuery( 'body.rtl' ).length ) || 'Left' === avadaMenuVars.header_position  ) {
				return jQuery( this ).children( '.sub-menu' ).each( function() {

					var submenu,
					    submenuPosition,
					    submenuLeft,
					    submenuTop,
					    submenuHeight,
					    submenuWidth,
					    submenuBottomEdge,
					    submenuRightEdge,
					    browserBottomEdge,
					    browserRightEdge,
					    submenuNewTopPos,
					    adminbarHeight,
					    sideHeaderTop,
					    submenuLis,
					    submenuJoined;

					// Reset attributes
					jQuery( this ).removeAttr( 'style' );
					jQuery( this ).show();
					jQuery( this ).removeData( 'shifted' );

					submenu = jQuery( this );

					if ( submenu.length ) {
						submenuPosition   = submenu.offset();
						submenuLeft       = submenuPosition.left;
						submenuTop        = submenuPosition.top;
						submenuHeight     = submenu.height();
						submenuWidth      = submenu.outerWidth();
						submenuBottomEdge = submenuTop + submenuHeight;
						submenuRightEdge  = submenuLeft + submenuWidth;
						browserBottomEdge = jQuery( window ).height();
						browserRightEdge  = jQuery( window ).width();

						if (	jQuery( '#wpadminbar' ).length ) {
							adminbarHeight = jQuery( '#wpadminbar' ).height();
						} else {
							adminbarHeight = 0;
						}

						if ( jQuery( '.side-header-wrapper' ).length ) {
							sideHeaderTop = jQuery( '.side-header-wrapper' ).offset().top - adminbarHeight;
						}

						// Current submenu goes beyond browser's right edge
						if ( submenuRightEdge > browserRightEdge ) {

							submenu.addClass( 'fusion-switched-side' );

							// First level submenu
							if ( ! submenu.parent().parent( '.sub-menu' ).length ) {
								submenu.css( 'left', ( -1 ) * submenuWidth + submenu.parent().width() );

							// Second level submenu
							} else {
								submenu.css({
									'left': ( -1 ) * submenuWidth
								});
							}

							submenu.data( 'shifted', 1 );

						// Parent submenu had to be shifted
						} else if ( submenu.parent().parent( '.sub-menu' ).length ) {
							submenu.removeClass( 'fusion-switched-side' );
							if ( submenu.parent().parent( '.sub-menu' ).data( 'shifted' ) ) {
								submenu.css( 'left', ( -1 ) * submenuWidth );
								submenu.data( 'shifted', 1 );
							}
						}

						// Calculate dropdown vertical position on side header.
						if ( 'Top' !== avadaMenuVars.header_position && submenuBottomEdge > sideHeaderTop + browserBottomEdge && jQuery( window ).height() >= jQuery( '.side-header-wrapper' ).height() ) {
							if ( submenuHeight < browserBottomEdge  ) {
								submenuNewTopPos = ( -1 ) * ( submenuBottomEdge - sideHeaderTop - browserBottomEdge + 20 );
							} else {
								submenuNewTopPos = ( -1 ) * ( submenuTop - adminbarHeight );
							}

							// Arrow can be between items, so disable hover change for svg.
							if ( jQuery( '.fusion-dropdown-svg' ).length ) {
								submenu.find( '> li > a' ).off( 'hover' );
								submenu.parents( 'li' ).find( '.fusion-dropdown-svg' ).removeClass( 'fusion-svg-active' );

								submenuLis = Math.floor( submenuNewTopPos / submenu.find( 'li' ).outerHeight() );
								submenuNewTopPos  = submenuLis * submenu.find( 'li' ).outerHeight();
								submenuJoined = submenu.find( '> li:nth-child( ' + ( Math.abs( submenuLis ) + 1 ) + ')' );

								if ( jQuery( submenuJoined ).hasClass( 'current-menu-item' ) || jQuery( submenuJoined ).hasClass( 'current-menu-parent' ) || jQuery( submenuJoined ).hasClass( 'current_page_item' ) ) {
									submenu.parents( 'li' ).find( '.fusion-dropdown-svg' ).addClass( 'fusion-svg-active' );
								}
								jQuery( submenuJoined ).not( '.current-menu-item, .current-menu-parent, .current_page_item' ).find( '> a' ).on( 'hover', function() {
									submenu.parents( 'li' ).find( '.fusion-dropdown-svg' ).toggleClass( 'fusion-svg-active' );
								});
							}

							submenu.css( 'top', submenuNewTopPos );

						}
					}
				});
			} else {
				return jQuery( this ).children( '.sub-menu' ).each( function() {

					var submenu,
					    submenuPosition,
					    submenuLeftEdge,
					    submenuTop,
					    submenuHeight,
					    submenuWidth,
					    submenuBottomEdge,
					    browserBottomEdge,
					    adminbarHeight,
					    sideHeaderTop,
					    submenuNewTopPos,
					    cssPosition,
					    submenuLis,
					    submenuJoined;

					// Reset attributes
					jQuery( this ).removeAttr( 'style' );
					jQuery( this ).removeData( 'shifted' );

					submenu = jQuery( this );

					if ( submenu.length ) {
						submenuPosition   = submenu.offset();
						submenuLeftEdge   = submenuPosition.left;
						submenuTop        = submenuPosition.top;
						submenuHeight     = submenu.height();
						submenuWidth      = submenu.outerWidth();
						submenuBottomEdge = submenuTop + submenuHeight;
						browserBottomEdge = jQuery( window ).height();

						if ( jQuery( '#wpadminbar' ).length ) {
							adminbarHeight = jQuery( '#wpadminbar' ).height();
						} else {
							adminbarHeight = 0;
						}

						if ( jQuery( '.side-header-wrapper' ).length ) {
							sideHeaderTop = jQuery( '.side-header-wrapper' ).offset().top - adminbarHeight;
						}

						cssPosition = 'right';

						// Current submenu goes beyond browser's left edge
						if ( 0 > submenuLeftEdge ) {

							submenu.addClass( 'fusion-switched-side' );

							// First level submenu
							if ( ! submenu.parent().parent( '.sub-menu' ).length ) {
								submenu.css( cssPosition, ( -1 ) * submenuWidth + submenu.parent().width() );

							// Second level submenu
							} else {
								if ( submenuLeftEdge <  submenuWidth ) {
									submenu.attr( 'style', cssPosition + ':' + ( -1  * submenuWidth ) + 'px !important' );
								} else {
									submenu.css( cssPosition, ( -1 ) * submenuWidth );
								}
							}

							submenu.data( 'shifted', 1 );

						// Parent submenu had to be shifted
						} else if ( submenu.parent().parent( '.sub-menu' ).length ) {
							submenu.removeClass( 'fusion-switched-side' );
							if ( submenu.parent().parent( '.sub-menu' ).data( 'shifted' ) ) {
								submenu.css( cssPosition, ( -1 ) * submenuWidth );
							}
						}

						// Calculate dropdown vertical position on side header
						if ( 'Top' !== avadaMenuVars.header_position && submenuBottomEdge > sideHeaderTop + browserBottomEdge && jQuery( window ).height() >= jQuery( '.side-header-wrapper' ).height() ) {
							if ( submenuHeight < browserBottomEdge  ) {
								submenuNewTopPos = ( -1 ) * ( submenuBottomEdge - sideHeaderTop - browserBottomEdge + 20 );
							} else {
								submenuNewTopPos = ( -1 ) * ( submenuTop - adminbarHeight );
							}

							// Arrow can be between items, so disable hover change for svg.
							if ( jQuery( '.fusion-dropdown-svg' ).length ) {
								submenu.find( '> li > a' ).off( 'hover' );
								submenu.parents( 'li' ).find( '.fusion-dropdown-svg' ).removeClass( 'fusion-svg-active' );

								submenuLis = Math.floor( submenuNewTopPos / submenu.find( 'li' ).outerHeight() );
								submenuNewTopPos  = submenuLis * submenu.find( 'li' ).outerHeight();
								submenuJoined = submenu.find( '> li:nth-child( ' + ( Math.abs( submenuLis ) + 1 ) + ')' );

								if ( jQuery( submenuJoined ).hasClass( 'current-menu-item' ) || jQuery( submenuJoined ).hasClass( 'current-menu-parent' ) || jQuery( submenuJoined ).hasClass( 'current_page_item' ) ) {
									submenu.parents( 'li' ).find( '.fusion-dropdown-svg' ).addClass( 'fusion-svg-active' );
								}
								jQuery( submenuJoined ).not( '.current-menu-item, .current-menu-parent, .current_page_item' ).find( '> a' ).on( 'hover', function() {
									submenu.parents( 'li' ).find( '.fusion-dropdown-svg' ).toggleClass( 'fusion-svg-active' );
								});
							}

							submenu.css( 'top', submenuNewTopPos );
						}
					}
				});
			}
	};

	// Recursive function for positioning menu items correctly on load
	jQuery.fn.walk_through_menu_items = function() {
		jQuery( this ).fusion_position_menu_dropdown();

		if ( jQuery( this ).find( '.sub-menu' ).length ) {
			jQuery( this ).find( '.sub-menu li' ).walk_through_menu_items();
		} else {
			return;
		}
	};

	// Position the cart dropdown vertically on side-header layouts
	jQuery.fn.position_cart_dropdown = function() {
		if ( 'Top' !== avadaMenuVars.header_position ) {
			jQuery( this ).each( function() {

				var cartDropdown,
				    cartDropdownTop,
				    cartDropdownHeight,
				    cartDropdownBottomEdge,
				    adminbarHeight,
				    sideHeaderTop,
				    browserBottomEdge,
				    cartDropdownNewTopPos;

				jQuery( this ).css( 'top', '' );

				cartDropdown           = jQuery( this ),
				cartDropdownTop        = cartDropdown.offset().top,
				cartDropdownHeight     = cartDropdown.height(),
				cartDropdownBottomEdge = cartDropdownTop + cartDropdownHeight,
				adminbarHeight         = ( jQuery( '#wpadminbar' ).length ) ? jQuery( '#wpadminbar' ).height() : 0,
				sideHeaderTop          = jQuery( '.side-header-wrapper' ).offset().top - adminbarHeight,
				browserBottomEdge      = jQuery( window ).height();

				if ( cartDropdownBottomEdge > sideHeaderTop + browserBottomEdge && jQuery( window ).height() >= jQuery( '.side-header-wrapper' ).height() ) {
					if ( cartDropdownHeight < browserBottomEdge ) {
						cartDropdownNewTopPos = ( -1 ) * ( cartDropdownBottomEdge - sideHeaderTop - browserBottomEdge + 20 );
					} else {
						cartDropdownNewTopPos = ( -1 ) * ( cartDropdownTop - adminbarHeight );
					}

					cartDropdown.css( 'top', cartDropdownNewTopPos );
				}
			});
		}
	};

	// Position the search form vertically on side-header layouts
	jQuery.fn.position_menu_search_form = function() {
		if ( 'Top' !== avadaMenuVars.header_position ) {
			jQuery( this ).each( function() {

				var searchForm,
				    searchFormTop,
				    searchFormHeight,
				    searchFormBottomEdge,
				    adminbarHeight,
				    sideHeaderTop,
				    browserBottomEdge,
				    searchFormNewTopPos;

				jQuery( this ).css( 'top', '' );

				searchForm = jQuery( this ),
				searchFormTop        = searchForm.offset().top,
				searchFormHeight     = searchForm.outerHeight(),
				searchFormBottomEdge = searchFormTop + searchFormHeight,
				adminbarHeight       = ( jQuery( '#wpadminbar' ).length ) ? jQuery( '#wpadminbar' ).height() : 0,
				sideHeaderTop        = jQuery( '.side-header-wrapper' ).offset().top - adminbarHeight,
				browserBottomEdge    = jQuery( window ).height();

				if ( searchFormBottomEdge > sideHeaderTop + browserBottomEdge && jQuery( window ).height() >= jQuery( '.side-header-wrapper' ).height() ) {
					searchFormNewTopPos = ( -1 ) * ( searchFormBottomEdge - sideHeaderTop - browserBottomEdge + 20 );

					searchForm.css( 'top', searchFormNewTopPos );
				}
			});
		}
	};

	// Position mega menu correctly
	jQuery.fn.fusion_position_megamenu = function( variables ) {

		var referenceElem,
		    mainNavContainer,
		    mainNavContainerPosition,
		    mainNavContainerWidth,
		    mainNavContainerLeftEdge,
		    mainNavContainerRightEdge;

		// Side header left handling
		if ( jQuery( '.side-header-left' ).length ) {
			return this.each( function() {
				jQuery( this ).children( 'li' ).each( function() {
					var liItem = jQuery( this ),
					    megamenuWrapper = liItem.find( '.fusion-megamenu-wrapper' ),
					    megamenuWrapperLeft,
					    megamenuWrapperTop,
					    megamenuWrapperHeight,
					    megamenuBottomEdge,
					    adminbarHeight,
					    sideHeaderTop,
					    browserBottomEdge,
					    megamenuWrapperNewTopPos;

					if ( megamenuWrapper.length ) {
						megamenuWrapper.removeAttr( 'style' );

						megamenuWrapperLeft   = jQuery( '#side-header' ).outerWidth() - 1;
						megamenuWrapperTop    = megamenuWrapper.offset().top;
						megamenuWrapperHeight = megamenuWrapper.height();
						megamenuBottomEdge    = megamenuWrapperTop + megamenuWrapperHeight;
						adminbarHeight        = ( jQuery( '#wpadminbar' ).length ) ? jQuery( '#wpadminbar' ).height() : 0;
						sideHeaderTop         = jQuery( '.side-header-wrapper' ).offset().top - adminbarHeight;
						browserBottomEdge     = jQuery( window ).height();
						megamenuWrapperNewTopPos;

						if ( ! jQuery( 'body.rtl' ).length ) {
							megamenuWrapper.css( 'left', megamenuWrapperLeft );
						} else {
							megamenuWrapper.css({ 'left': megamenuWrapperLeft, 'right': 'auto' });
						}

						if ( megamenuBottomEdge > sideHeaderTop + browserBottomEdge && jQuery( window ).height() >= jQuery( '.side-header-wrapper' ).height() ) {
							if ( megamenuWrapperHeight < browserBottomEdge ) {
								megamenuWrapperNewTopPos = ( -1 ) * ( megamenuBottomEdge - sideHeaderTop - browserBottomEdge + 20 );
							} else {
								megamenuWrapperNewTopPos = ( -1 ) * ( megamenuWrapperTop - adminbarHeight );
							}

							megamenuWrapper.css( 'top', megamenuWrapperNewTopPos );
						}
					}
				});
			});
		}

		// Side header right handling
		if ( jQuery( '.side-header-right' ).length ) {
			return this.each( function() {
				jQuery( this ).children( 'li' ).each( function() {
					var liItem = jQuery( this ),
					    megamenuWrapper = liItem.find( '.fusion-megamenu-wrapper' ),
					    megamenuWrapperLeft,
					    megamenuWrapperTop,
					    megamenuWrapperHeight,
					    megamenuBottomEdge,
					    adminbarHeight,
					    sideHeaderTop,
					    browserBottomEdge,
					    megamenuWrapperNewTopPos;

					if ( megamenuWrapper.length ) {
						megamenuWrapper.removeAttr( 'style' );

						megamenuWrapperLeft   = ( -1 ) * megamenuWrapper.outerWidth();
						megamenuWrapperTop    = megamenuWrapper.offset().top;
						megamenuWrapperHeight = megamenuWrapper.height();
						megamenuBottomEdge    = megamenuWrapperTop + megamenuWrapperHeight;
						adminbarHeight        = ( jQuery( '#wpadminbar' ).length ) ? jQuery( '#wpadminbar' ).height() : 0;
						sideHeaderTop         = jQuery( '.side-header-wrapper' ).offset().top - adminbarHeight;
						browserBottomEdge     = jQuery( window ).height();

						if ( ! jQuery( 'body.rtl' ).length ) {
							megamenuWrapper.css( 'left', megamenuWrapperLeft );
						} else {
							megamenuWrapper.css({ 'left': megamenuWrapperLeft, 'right': 'auto' });
						}

						if ( megamenuBottomEdge > sideHeaderTop + browserBottomEdge && jQuery( window ).height() >= jQuery( '.side-header-wrapper' ).height() ) {
							if ( megamenuWrapperHeight < browserBottomEdge ) {
								megamenuWrapperNewTopPos = ( -1 ) * ( megamenuBottomEdge - sideHeaderTop - browserBottomEdge + 20 );
							} else {
								megamenuWrapperNewTopPos = ( -1 ) * ( megamenuWrapperTop - adminbarHeight );
							}

							megamenuWrapper.css( 'top', megamenuWrapperNewTopPos );
						}
					}
				});
			});
		}

		// Top header handling
		referenceElem = '';
		if ( jQuery( '.fusion-header-v4' ).length ) {
			referenceElem = jQuery( this ).parent( '.fusion-main-menu' ).parent();
		} else {
			referenceElem = jQuery( this ).parent( '.fusion-main-menu' );
		}

		if ( jQuery( this ).parent( '.fusion-main-menu' ).length ) {

			mainNavContainer          = referenceElem,
			mainNavContainerPosition  = mainNavContainer.offset(),
			mainNavContainerWidth     = mainNavContainer.width(),
			mainNavContainerLeftEdge  = mainNavContainerPosition.left,
			mainNavContainerRightEdge = mainNavContainerLeftEdge + mainNavContainerWidth;

			if ( ! jQuery( 'body.rtl' ).length ) {
				return this.each( function() {

					jQuery( this ).children( 'li' ).each( function() {
						var liItem                  = jQuery( this ),
						    liItemPosition          = liItem.offset(),
						    megamenuWrapper         = liItem.find( '.fusion-megamenu-wrapper' ),
						    megamenuWrapperWidth    = megamenuWrapper.outerWidth(),
						    megamenuWrapperPosition = 0,
						    referenceAvadaRow       = 0;

						// Check if there is a megamenu
						if ( megamenuWrapper.length ) {
							megamenuWrapper.removeAttr( 'style' );

							// Set megamenu max width

							if ( jQuery( '.fusion-secondary-main-menu' ).length ) {
								referenceAvadaRow = jQuery( '.fusion-header-wrapper .fusion-secondary-main-menu .fusion-row' );
							} else {
								referenceAvadaRow = jQuery( '.fusion-header-wrapper .fusion-row' );
							}

							if ( megamenuWrapper.hasClass( 'col-span-12' ) && ( referenceAvadaRow.width() < megamenuWrapper.data( 'maxwidth' ) ) ) {
								megamenuWrapper.css( 'width', referenceAvadaRow.width() );
							} else {
								megamenuWrapper.removeAttr( 'style' );
							}

							// Reset the megmenu width after resizing the menu
							megamenuWrapperWidth = megamenuWrapper.outerWidth();

							if ( liItemPosition.left + megamenuWrapperWidth > mainNavContainerRightEdge ) {
								megamenuWrapperPosition = -1 * ( liItemPosition.left - ( mainNavContainerRightEdge - megamenuWrapperWidth ) );

								if ( 'right' === avadaMenuVars.logo_alignment.toLowerCase() ) {
									if ( liItemPosition.left + megamenuWrapperPosition < mainNavContainerLeftEdge ) {
										megamenuWrapperPosition = -1 * ( liItemPosition.left - mainNavContainerLeftEdge );
									}
								}

								megamenuWrapper.css( 'left', megamenuWrapperPosition );
							}
						}
					});
				});

			} else {
				return this.each( function() {
					jQuery( this ).children( 'li' ).each( function() {
						var liItem                  = jQuery( this ),
						    liItemPosition          = liItem.offset(),
						    liItemRightEdge         = liItemPosition.left + liItem.outerWidth(),
						    megamenuWrapper         = liItem.find( '.fusion-megamenu-wrapper' ),
						    megamenuWrapperWidth    = megamenuWrapper.outerWidth(),
						    megamenuWrapperPosition = 0,
						    referenceAvadaRow;

						// Check if there is a megamenu
						if ( megamenuWrapper.length ) {
							megamenuWrapper.removeAttr( 'style' );

							if ( jQuery( '.fusion-secondary-main-menu' ).length ) {
								referenceAvadaRow = jQuery( '.fusion-header-wrapper .fusion-secondary-main-menu .fusion-row' );
							} else {
								referenceAvadaRow = jQuery( '.fusion-header-wrapper .fusion-row' );
							}

							if ( megamenuWrapper.hasClass( 'col-span-12' ) && ( referenceAvadaRow.width() < megamenuWrapper.data( 'maxwidth' ) ) ) {
								megamenuWrapper.css( 'width', referenceAvadaRow.width() );
							} else {
								megamenuWrapper.removeAttr( 'style' );
							}

							if ( liItemRightEdge - megamenuWrapperWidth < mainNavContainerLeftEdge ) {

								megamenuWrapperPosition = -1 * ( megamenuWrapperWidth - ( liItemRightEdge - mainNavContainerLeftEdge ) );

								if ( 'left' === avadaMenuVars.logo_alignment.toLowerCase() || ( 'center' === avadaMenuVars.logo_alignment.toLowerCase() && ! jQuery( '.header-v5' ).length ) || jQuery( this ).parents( '.sticky-header' ).length ) {
									if ( liItemRightEdge - megamenuWrapperPosition > mainNavContainerRightEdge ) {
										megamenuWrapperPosition = -1 * ( mainNavContainerRightEdge - liItemRightEdge );
									}
								}

								megamenuWrapper.css( 'right', megamenuWrapperPosition );
							}
						}
					});
				});
			}
		}
	};

	jQuery.fn.calc_megamenu_responsive_column_widths = function( variables ) {
		jQuery( this ).find( '.fusion-megamenu-menu' ).each( function() {
			var megamenuHolder          = jQuery( this ).find( '.fusion-megamenu-holder' ),
			    megamenuHolderFullWidth = megamenuHolder.data( 'width' ),
			    referenceFusionRow      = ( jQuery( '.fusion-secondary-main-menu' ).length ) ? jQuery( '.fusion-header-wrapper .fusion-secondary-main-menu .fusion-row' ) : jQuery( '.fusion-header-wrapper .fusion-row' ),
			    availableSpace          = referenceFusionRow.width(),
			    mainPaddingLeft;

			if ( 'Top' !== avadaMenuVars.header_position ) {
				mainPaddingLeft = jQuery( '#main' ).css( 'padding-left' ).replace( 'px', '' );
				availableSpace = jQuery( window ).width() - mainPaddingLeft - jQuery( '#side-header' ).outerWidth();
			}

			if ( availableSpace < megamenuHolderFullWidth ) {
				megamenuHolder.css( 'width', availableSpace );

				if ( ! megamenuHolder.parents( '.fusion-megamenu-wrapper' ).hasClass( 'fusion-megamenu-fullwidth' ) ) {
					megamenuHolder.find( '.fusion-megamenu-submenu' ).each( function() {
						var submenu      = jQuery( this ),
						    submenuWidth = submenu.data( 'width' ) * availableSpace / megamenuHolderFullWidth;

						submenu.css( 'width', submenuWidth );
					});
				}
			} else {
				megamenuHolder.css( 'width', megamenuHolderFullWidth );

				if ( ! megamenuHolder.parents( '.fusion-megamenu-wrapper' ).hasClass( 'fusion-megamenu-fullwidth' ) ) {
					megamenuHolder.find( '.fusion-megamenu-submenu' ).each( function() {
						jQuery( this ).css( 'width', jQuery( this ).data( 'width' ) );
					});
				}
			}
		});
	};

	jQuery.fn.position_last_top_menu_item = function( variables ) {

		var lastItem,
		    lastItemLeftPos,
		    lastItemWidth,
		    lastItemChild,
		    parentContainer,
		    parentContainerLeftPos,
		    parentContainerWidth;

		if ( jQuery( this ).children( 'ul' ).length || jQuery( this ).children( 'div' ).length ) {
			lastItem               = jQuery( this );
			lastItemLeftPos        = lastItem.position().left;
			lastItemWidth          = lastItem.outerWidth();
			parentContainer        = jQuery( '.fusion-secondary-header .fusion-row' );
			parentContainerLeftPos = parentContainer.position().left;
			parentContainerWidth   = parentContainer.outerWidth();

			if ( lastItem.children( 'ul' ).length ) {
				lastItemChild =  lastItem.children( 'ul' );
			} else if ( lastItem.children( 'div' ).length ) {
				lastItemChild =  lastItem.children( 'div' );
			}

			if ( ! jQuery( 'body.rtl' ).length ) {
				if ( lastItemLeftPos + lastItemChild.outerWidth() > parentContainerLeftPos + parentContainerWidth ) {
					lastItemChild.css( 'right', '-1px' ).css( 'left', 'auto' );

					lastItemChild.find( '.sub-menu' ).each( function() {
						jQuery( this ).css( 'right', '100px' ).css( 'left', 'auto' );
					});
				}
			} else {
				if ( lastItemChild.position().left < lastItemLeftPos ) {
					lastItemChild.css( 'left', '-1px' ).css( 'right', 'auto' );

					lastItemChild.find( '.sub-menu' ).each( function() {
						jQuery( this ).css( 'left', '100px' ).css( 'right', 'auto' );
					});
				}
			}
		}
	};

	// IE8 fixes.
	jQuery( '.fusion-main-menu > ul > li:last-child' ).addClass( 'fusion-last-menu-item' );

	// Calculate main menu dropdown submenu position.
	if ( jQuery.fn.fusion_position_menu_dropdown ) {
		jQuery( '.fusion-dropdown-menu, .fusion-dropdown-menu li' ).mouseenter( function() {
			jQuery( this ).fusion_position_menu_dropdown();
		});

		jQuery( '.fusion-dropdown-menu > ul > li' ).each( function() {
			jQuery( this ).walk_through_menu_items();
		});

		jQuery( window ).on( 'resize', function() {
			jQuery( '.fusion-dropdown-menu > ul > li' ).each( function() {
				jQuery( this ).walk_through_menu_items();
			});
		});
	}

	// Set overflow state of main nav items; done to get rid of menu overflow.
	jQuery( '.fusion-dropdown-menu' ).mouseenter( function() {
		jQuery( this ).css( 'overflow', 'visible' );
	});
	jQuery( '.fusion-dropdown-menu' ).mouseleave( function() {
		jQuery( this ).css( 'overflow', '' );
	});

	// Accessibility dropdowns.
	jQuery( 'a' ).on( 'focus', function( e ) {
		jQuery( '.fusion-active-link' ).removeClass( 'fusion-active-link' );
		if ( jQuery( this ).parents( '.fusion-dropdown-menu, .fusion-main-menu-cart, .fusion-megamenu-menu' ).length ) {
			jQuery( this ).parents( 'li' ).addClass( 'fusion-active-link' );
			jQuery( '.fusion-main-menu' ).css( 'overflow', 'visible' );
		}
	});

	// Search icon show/hide.
	jQuery( document ).click( function() {
		jQuery( '.fusion-main-menu-search .fusion-custom-menu-item-contents' ).hide();
		jQuery( '.fusion-main-menu-search' ).removeClass( 'fusion-main-menu-search-open' );
		jQuery( '.fusion-main-menu-search' ).find( 'style' ).remove();
	});

	jQuery( '.fusion-main-menu-search' ).click( function( e ) {
		e.stopPropagation();
	});

	jQuery( '.fusion-main-menu-search .fusion-main-menu-icon' ).click( function( e ) {
		e.stopPropagation();

		if ( 'block' === jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).css( 'display' ) ) {
			jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).hide();
			jQuery( this ).parent().removeClass( 'fusion-main-menu-search-open' );

			jQuery( this ).parent().find( 'style' ).remove();
		} else {
			jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).removeAttr( 'style' );
			jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).show();
			jQuery( this ).parent().addClass( 'fusion-main-menu-search-open' );

			jQuery( this ).parent().append( '<style>.fusion-main-menu{overflow:visible!important;</style>' );
			jQuery( this ).parent().find( '.fusion-custom-menu-item-contents .s' ).focus();

			// Position main menu search box on click positioning
			if ( 'Top' === avadaMenuVars.header_position ) {
				if ( ! jQuery( 'body.rtl' ).length && jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).offset().left < 0 ) {
					jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).css({
						'left': '0',
						'right': 'auto'
					});
				}

				if ( jQuery( 'body.rtl' ).length && jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).offset().left + jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).width()  > jQuery( window ).width() ) {
					jQuery( this ).parent().find( '.fusion-custom-menu-item-contents' ).css({
						'left': 'auto',
						'right': '0'
					});
				}
			}
		}
	});

	// Calculate megamenu position.
	if ( jQuery.fn.fusion_position_megamenu ) {
		jQuery( '.fusion-main-menu > ul' ).fusion_position_megamenu();

		jQuery( '.fusion-main-menu .fusion-megamenu-menu' ).mouseenter( function() {
			jQuery( this ).parent().fusion_position_megamenu();
		});

		jQuery( window ).resize( function() {
			jQuery( '.fusion-main-menu > ul' ).fusion_position_megamenu();
		});
	}

	// Calculate megamenu column widths.
	if ( jQuery.fn.calc_megamenu_responsive_column_widths ) {
		jQuery( '.fusion-main-menu > ul' ).calc_megamenu_responsive_column_widths();

		jQuery( window ).resize( function() {
			jQuery( '.fusion-main-menu > ul' ).calc_megamenu_responsive_column_widths();
		});
	}

	// Top Menu last item positioning.
	jQuery( '.fusion-header-wrapper .fusion-secondary-menu > ul > li:last-child' ).position_last_top_menu_item();

	fusionRepositionMenuItem( '.fusion-main-menu .fusion-main-menu-cart' );
	fusionRepositionMenuItem( '.fusion-secondary-menu .fusion-menu-login-box' );

	function fusionRepositionMenuItem( $menuItem ) {

		// Position main menu cart dropdown correctly
		if ( 'Top' === avadaMenuVars.header_position ) {
			jQuery( $menuItem ).mouseenter( function( e ) {

				if ( jQuery( this ).find( '> div' ).length && jQuery( this ).find( '> div' ).offset().left < 0 ) {
					jQuery( this ).find( '> div' ).css({
						'left': '0',
						'right': 'auto'
					});
				}

				if ( jQuery( this ).find( '> div' ).length && jQuery( this ).find( '> div' ).offset().left + jQuery( this ).find( '> div' ).width()  > jQuery( window ).width() ) {
					jQuery( this ).find( '> div' ).css({
						'left': 'auto',
						'right': '0'
					});
				}
			});

			jQuery( window ).on( 'resize', function() {
				jQuery( $menuItem ).find( '> div' ).each( function() {
					var $menuItemDropdown          = jQuery( this ),
					    $menuItemDropdownWidth     = $menuItemDropdown.outerWidth(),
					    $menuItemDropdownLeftEdge  = $menuItemDropdown.offset().left,
					    $menuItemDropdownRightEdge = $menuItemDropdownLeftEdge + $menuItemDropdownWidth,
					    $menuItemLeftEdge          = $menuItemDropdown.parent().offset().left,
					    windowRightEdge            = jQuery( window ).width();

					if ( ! jQuery( 'body.rtl' ).length ) {
						if ( ( $menuItemDropdownLeftEdge < $menuItemLeftEdge && $menuItemDropdownLeftEdge < 0 ) || ( $menuItemDropdownLeftEdge == $menuItemLeftEdge && $menuItemDropdownLeftEdge - $menuItemDropdownWidth < 0 ) ) {
							$menuItemDropdown.css({
								'left': '0',
								'right': 'auto'
							});
						} else {
							$menuItemDropdown.css({
								'left': 'auto',
								'right': '0'
							});
						}
					} else {
						if ( ( $menuItemDropdownLeftEdge == $menuItemLeftEdge && $menuItemDropdownRightEdge > windowRightEdge ) || ( $menuItemDropdownLeftEdge < $menuItemLeftEdge && $menuItemDropdownRightEdge + $menuItemDropdownWidth > windowRightEdge )  ) {
							$menuItemDropdown.css({
								'left': 'auto',
								'right': '0'
							});
						} else {
							$menuItemDropdown.css({
								'left': '0',
								'right': 'auto'
							});
						}
					}
				});
			});
		}
	}

	// Reinitialize google map on megamenu.
	jQuery( '.fusion-megamenu-menu' ).mouseenter( function() {
		if ( jQuery( this ).find( '.shortcode-map' ).length ) {
			jQuery( this ).find( '.shortcode-map' ).each( function() {
				jQuery( this ).reinitializeGoogleMap();
			});
		}
	});

	// Make iframes in megamenu widget area load correctly in Safari and IE.
	// Safari part - load the iframe correctly.
	iframeLoaded = false;

	jQuery( '.fusion-megamenu-menu' ).mouseover(
		function() {
			jQuery( this ).find( '.fusion-megamenu-widgets-container iframe' ).each( function() {
				if ( ! iframeLoaded ) {
					jQuery( this ).attr( 'src', jQuery( this ).attr( 'src' ) );
				}
				iframeLoaded = true;
			});
		}
	);

	// IE part - making the megamenu stay on hover.
	jQuery( '.fusion-megamenu-wrapper iframe' ).mouseover(
		function() {
			jQuery( this ).parents( '.fusion-megamenu-widgets-container' ).css( 'display', 'block' );
			jQuery( this ).parents( '.fusion-megamenu-wrapper' ).css({ 'opacity': '1', 'visibility': 'visible' });
		}
	);

	jQuery( '.fusion-megamenu-wrapper iframe' ).mouseout(
		function() {
			jQuery( this ).parents( '.fusion-megamenu-widgets-container' ).css( 'display', '' );
			jQuery( this ).parents( '.fusion-megamenu-wrapper' ).css({ 'opacity': '', 'visibility': '' });
		}
	);

	// Position main menu cart dropdown correctly on side-header.
	jQuery( '.fusion-navbar-nav .cart' ).find( '.cart-contents' ).position_cart_dropdown();

	jQuery( window ).on( 'resize', function() {
		jQuery( '.fusion-navbar-nav .cart' ).find( '.cart-contents' ).position_cart_dropdown();
	});

	// Position main menu search form correctly on side-header.
	jQuery( '.fusion-navbar-nav .search-link' ).click( function() {
		setTimeout( function() {
			jQuery( '.fusion-navbar-nav .search-link' ).parent().find( '.main-nav-search-form' ).position_menu_search_form();
		}, 5 );
	});

	jQuery( window ).on( 'resize', function() {
		jQuery( '.fusion-navbar-nav .main-nav-search' ).find( '.main-nav-search-form' ).position_menu_search_form();
	});

	/**
	 * Mobile Navigation.
	 */
	jQuery( '.fusion-mobile-nav-holder' ).not( '.fusion-mobile-sticky-nav-holder' ).each( function() {
		var $mobileNavHolder = jQuery( this ),
		    $mobileNav       = '',
		    $menu            = jQuery( this ).parent().find( '.fusion-main-menu, .fusion-secondary-menu' ).not( '.fusion-sticky-menu' ),
		    $currentMenuId   = '';
		if ( $menu.length ) {
			if ( 'classic' === avadaMenuVars.mobile_menu_design ) {
				$mobileNavHolder.append( '<div class="fusion-mobile-selector"><span>' + avadaMenuVars.dropdown_goto + '</span></div>' );
				jQuery( this ).find( '.fusion-mobile-selector' ).append( '<div class="fusion-selector-down"></div>' );
			}

			jQuery( $mobileNavHolder ).append( jQuery( $menu ).find( '> ul' ).clone() );

			$mobileNav = jQuery( $mobileNavHolder ).find( '> ul' );
			$currentMenuId = $mobileNav.attr( 'id' );
			$mobileNav.attr( 'id', 'mobile-' + $currentMenuId );
			$mobileNav.removeClass( 'fusion-middle-logo-ul' );

			$mobileNav.find( '.fusion-middle-logo-menu-logo, .fusion-caret, .fusion-menu-login-box .fusion-custom-menu-item-contents, .fusion-menu-cart .fusion-custom-menu-item-contents, .fusion-main-menu-search, li> a > span > .button-icon-divider-left, li > a > span > .button-icon-divider-right' ).remove();

			if ( 'classic' === avadaMenuVars.mobile_menu_design ) {
				$mobileNav.find( '.fusion-menu-cart > a' ).html( avadaMenuVars.mobile_nav_cart );
			} else {
				$mobileNav.find( '.fusion-main-menu-cart' ).remove();
			}

			$mobileNav.find( 'li' ).each( function() {

				var classes = 'fusion-mobile-nav-item';
				if ( jQuery( this ).data( 'classes' ) ) {
					classes += ' ' + jQuery( this ).data( 'classes' );
				}

				if ( jQuery( this ).find( 'img' ).hasClass( 'wpml-ls-flag' ) ) {
					classes += ' wpml-ls-item';
				}

				jQuery( this ).find( '> a > .menu-text' ).removeAttr( 'class' ).addClass( 'menu-text' );

				if ( jQuery( this ).hasClass( 'current-menu-item' ) || jQuery( this ).hasClass( 'current-menu-parent' ) || jQuery( this ).hasClass( 'current-menu-ancestor' ) ) {
					classes += ' fusion-mobile-current-nav-item';
				}

				jQuery( this ).attr( 'class', classes );

				if ( jQuery( this ).attr( 'id' ) ) {
					jQuery( this ).attr( 'id', jQuery( this ).attr( 'id' ).replace( 'menu-item', 'mobile-menu-item' ) );
				}

				jQuery( this ).attr( 'style', '' );
			});

			jQuery( this ).find( '.fusion-mobile-selector' ).click( function() {
				if ( $mobileNav.hasClass( 'mobile-menu-expanded' ) ) {
					$mobileNav.removeClass( 'mobile-menu-expanded' );
				} else {
					$mobileNav.addClass( 'mobile-menu-expanded' );
				}

				$mobileNav.slideToggle( 200, 'easeOutQuad' );
			});
		}
	});

	jQuery( '.fusion-mobile-sticky-nav-holder' ).each( function() {
		var $mobileNavHolder = jQuery( this ),
		    $mobileNav       = '',
		    $menu            = jQuery( this ).parent().find( '.fusion-sticky-menu' );

		if ( 'classic' === avadaMenuVars.mobile_menu_design ) {
			$mobileNavHolder.append( '<div class="fusion-mobile-selector"><span>' + avadaMenuVars.dropdown_goto + '</span></div>' );
			jQuery( this ).find( '.fusion-mobile-selector' ).append( '<div class="fusion-selector-down"></div>' );
		}

		jQuery( $mobileNavHolder ).append( jQuery( $menu ).find( '> ul' ).clone() );

		$mobileNav = jQuery( $mobileNavHolder ).find( '> ul' );

		$mobileNav.find( '.fusion-middle-logo-menu-logo, .fusion-menu-cart, .fusion-menu-login-box, .fusion-main-menu-search' ).remove();

		$mobileNav.find( 'li' ).each( function() {
			var classes = 'fusion-mobile-nav-item';
			if ( jQuery( this ).data( 'classes' ) ) {
				classes += ' ' + jQuery( this ).data( 'classes' );
			}

			if ( jQuery( this ).find( 'img' ).hasClass( 'wpml-ls-flag' ) ) {
				classes += ' wpml-ls-item';
			}

			if ( jQuery( this ).hasClass( 'current-menu-item' ) || jQuery( this ).hasClass( 'current-menu-parent' ) || jQuery( this ).hasClass( 'current-menu-ancestor' ) ) {
				classes += ' fusion-mobile-current-nav-item';
			}

			jQuery( this ).attr( 'class', classes );

			if ( jQuery( this ).attr( 'id' ) ) {
				jQuery( this ).attr( 'id', jQuery( this ).attr( 'id' ).replace( 'menu-item', 'mobile-menu-item' ) );
			}

			jQuery( this ).attr( 'style', '' );
		});

		jQuery( this ).find( '.fusion-mobile-selector' ).click( function() {
			if ( $mobileNav.hasClass( 'mobile-menu-expanded' ) ) {
				$mobileNav.removeClass( 'mobile-menu-expanded' );
			} else {
				$mobileNav.addClass( 'mobile-menu-expanded' );
			}

			$mobileNav.slideToggle( 200, 'easeOutQuad' );
		});
	});

	// Make megamenu items mobile ready.
	jQuery( '.fusion-mobile-nav-holder > ul > li' ).each( function() {
		jQuery( this ).find( '.fusion-megamenu-widgets-container' ).remove();

		jQuery( this ).find( '.fusion-megamenu-holder > ul' ).each( function() {
			jQuery( this ).attr( 'class', 'sub-menu' );
			jQuery( this ).attr( 'style', '' );
			jQuery( this ).find( '> li' ).each( function() {

				// Add menu needed menu classes to li elements
				var classes = 'fusion-mobile-nav-item',
				    parentLi;

				if ( jQuery( this ).data( 'classes' ) ) {
					classes += ' ' + jQuery( this ).data( 'classes' );
				}

				if ( jQuery( this ).find( 'img' ).hasClass( 'wpml-ls-flag' ) ) {
					classes += ' wpml-ls-item';
				}

				if ( jQuery( this ).hasClass( 'current-menu-item' ) || jQuery( this ).hasClass( 'current-menu-parent' ) || jQuery( this ).hasClass( 'current-menu-ancestor' ) || jQuery( this ).hasClass( 'fusion-mobile-current-nav-item' ) ) {
					classes += ' fusion-mobile-current-nav-item';
				}
				jQuery( this ).attr( 'class', classes );

				// Append column titles and title links correctly
				if ( ! jQuery( this ).find( '.fusion-megamenu-title a, > a' ).length ) {
					jQuery( this ).find( '.fusion-megamenu-title' ).each( function() {
						if ( ! jQuery( this ).children( 'a' ).length ) {
							jQuery( this ).append( '<a href="#">' + jQuery( this ).text() + '</a>' );
						}
					});

					if ( ! jQuery( this ).find( '.fusion-megamenu-title' ).length ) {

						parentLi = jQuery( this );

						jQuery( this ).find( '.sub-menu' ).each( function() {
							parentLi.after( jQuery( this ) );

						});
						jQuery( this ).remove();
					}
				}
				jQuery( this ).prepend( jQuery( this ).find( '.fusion-megamenu-title a, > a' ) );

				jQuery( this ).find( '.fusion-megamenu-title' ).remove();
			});
			jQuery( this ).closest( '.fusion-mobile-nav-item' ).append( jQuery( this ) );
		});

		jQuery( this ).find( '.fusion-megamenu-wrapper, .caret, .fusion-megamenu-bullet' ).remove();
	});

	// Mobile Modern Menu.
	jQuery( '.fusion-mobile-menu-icons .fusion-icon-bars' ).click( function( e ) {

		var $wrapper;

		e.preventDefault();

		if ( jQuery( '.fusion-header-v4' ).length >= 1 || jQuery( '.fusion-header-v5' ).length >= 1 ) {
			$wrapper = '.fusion-secondary-main-menu';
		} else if ( jQuery( '#side-header' ).length >= 1 ) {
			$wrapper = '#side-header';
		} else {
			$wrapper = '.fusion-header';
		}

		if ( jQuery( '.fusion-is-sticky' ).length >= 1 && jQuery( '.fusion-mobile-sticky-nav-holder' ).length >= 1 ) {
			jQuery( $wrapper ).find( '.fusion-mobile-sticky-nav-holder' ).slideToggle( 200, 'easeOutQuad' );
		} else {
			jQuery( $wrapper ).find( '.fusion-mobile-nav-holder' ).not( '.fusion-mobile-sticky-nav-holder' ).slideToggle( 200, 'easeOutQuad' );
		}
	});

	jQuery( '.fusion-mobile-menu-icons .fusion-icon-search' ).click( function( e ) {
		e.preventDefault();

		jQuery( '.fusion-secondary-main-menu .fusion-secondary-menu-search, .side-header-wrapper .fusion-secondary-menu-search' ).slideToggle( 200, 'easeOutQuad' );
	});

	// Collapse mobile menus when on page anchors are clicked.
	jQuery( '.fusion-mobile-nav-holder .fusion-mobile-nav-item a:not([href="#"])' ).click( function() {
		var $target = jQuery( this.hash );
		if ( '' !== $target.length && this.hash.slice( 1 ) ) {
			if ( jQuery( this ).parents( '.fusion-mobile-menu-design-classic' ).length ) {
				jQuery( this ).parents( '.fusion-menu, .menu' )
					.hide()
					.removeClass( 'mobile-menu-expanded' );
			} else {
				jQuery( this ).parents( '.fusion-mobile-nav-holder' ).hide();
			}
		}
	});

	// Make mobile menu sub-menu toggles.
	if ( 1 == avadaMenuVars.submenu_slideout ) {
		jQuery( '.fusion-mobile-nav-holder > ul li' ).each( function() {
			var classes = 'fusion-mobile-nav-item';

			if ( jQuery( this ).data( 'classes' ) ) {
				classes += ' ' + jQuery( this ).data( 'classes' );
			}

			if ( jQuery( this ).find( 'img' ).hasClass( 'wpml-ls-flag' ) ) {
				classes += ' wpml-ls-item';
			}

			if ( jQuery( this ).hasClass( 'current-menu-item' ) || jQuery( this ).hasClass( 'current-menu-parent' ) || jQuery( this ).hasClass( 'current-menu-ancestor' ) || jQuery( this ).hasClass( 'fusion-mobile-current-nav-item' ) ) {
				classes += ' fusion-mobile-current-nav-item';
			}

			jQuery( this ).attr( 'class', classes );

			if ( jQuery( this ).find( ' > ul' ).length ) {
				jQuery( this ).prepend( '<span href="#" aria-haspopup="true" class="fusion-open-submenu"></span>' );

				jQuery( this ).find( ' > ul' ).hide();
			}
		});

		jQuery( '.fusion-mobile-nav-holder .fusion-open-submenu' ).click( function( e ) {
			e.stopPropagation();

			jQuery( this ).parent().children( '.sub-menu' ).slideToggle( 200, 'easeOutQuad' );
		});

		jQuery( '.fusion-mobile-nav-holder a' ).click( function( e ) {
			if ( '#' === jQuery( this ).attr( 'href' ) ) {
				if ( 'modal' == jQuery( this ).data( 'toggle' ) ) {
					jQuery( this ).trigger( 'show.bs.modal' );
				} else {
					e.preventDefault();
					e.stopPropagation();
				}

				jQuery( this ).prev( '.fusion-open-submenu' ).trigger( 'click' );
			}
		} );
	}

	// Flyout Menu.
	function setFlyoutActiveCSS() {

		var $flyoutMenuTopHeight;

		jQuery( 'body' ).bind( 'touchmove', function( e ) {
			if ( ! jQuery( e.target ).parents( '.fusion-flyout-menu' ).length ) {
				e.preventDefault();
			}
		});

		window.$wpadminbarHeight = ( jQuery( '#wpadminbar' ).length ) ? jQuery( '#wpadminbar' ).height() : 0;
		$flyoutMenuTopHeight = jQuery( '.fusion-header-v6-content' ).height() + window.$wpadminbarHeight;

		// Make usre the menu is opened in a way, that menu items do not collide with the header.
		if ( jQuery( '.fusion-header-v6' ).hasClass( 'fusion-flyout-menu-active' ) ) {
			jQuery( '.fusion-header-v6 .fusion-flyout-menu' ).css({
				'height': 'calc(100% - ' + $flyoutMenuTopHeight + 'px)',
				'margin-top': $flyoutMenuTopHeight
			});

			if ( jQuery( '.fusion-header-v6 .fusion-flyout-menu .fusion-menu' ).height() > jQuery( '.fusion-header-v6 .fusion-flyout-menu' ).height() ) {
				jQuery( '.fusion-header-v6 .fusion-flyout-menu' ).css( 'display', 'block' );
			}
		}

		// Make sure logo and menu stay sticky on flyout opened, even if sticky header is disabled.
		if ( '0' == avadaMenuVars.header_sticky ) {
			jQuery( '.fusion-header-v6 .fusion-header' ).css({
				'position': 'fixed',
				'width': '100%',
				'max-width': '100%',
				'top': window.$wpadminbarHeight,
				'z-index': '210'
			});

			jQuery( '.fusion-header-sticky-height' ).css({
				'display': 'block',
				'height': jQuery( '.fusion-header-v6 .fusion-header' ).height()
			});
		}
	}

	function resetFlyoutActiveCSS() {
		setTimeout( function() {
			jQuery( '.fusion-header-v6 .fusion-flyout-menu' ).css( 'display', '' );

			if ( '0' == avadaMenuVars.header_sticky ) {
				jQuery( '.fusion-header-v6 .fusion-header' ).attr( 'style', '' );
				jQuery( '.fusion-header-sticky-height' ).attr( 'style', '' );
			}
			jQuery( 'body' ).unbind( 'touchmove' );
		}, 250 );
	}

	jQuery( '.fusion-flyout-menu-icons .fusion-flyout-menu-toggle' ).on( 'click', function() {
		var $flyoutContent = jQuery( this ).parents( '.fusion-header-v6' );

		if ( $flyoutContent.hasClass( 'fusion-flyout-active' ) ) {
			if ( $flyoutContent.hasClass( 'fusion-flyout-search-active' ) ) {
				$flyoutContent.addClass( 'fusion-flyout-menu-active' );

				setFlyoutActiveCSS();
			} else {
				$flyoutContent.removeClass( 'fusion-flyout-active' );
				$flyoutContent.removeClass( 'fusion-flyout-menu-active' );

				resetFlyoutActiveCSS();
			}
			$flyoutContent.removeClass( 'fusion-flyout-search-active' );
		} else {
			$flyoutContent.addClass( 'fusion-flyout-active' );
			$flyoutContent.addClass( 'fusion-flyout-menu-active' );

			setFlyoutActiveCSS();
		}
	});

	jQuery( '.fusion-flyout-menu-icons .fusion-flyout-search-toggle' ).on( 'click', function() {
		var $flyoutContent = jQuery( this ).parents( '.fusion-header-v6' );

		if ( $flyoutContent.hasClass( 'fusion-flyout-active' ) ) {
			if ( $flyoutContent.hasClass( 'fusion-flyout-menu-active' ) ) {
				$flyoutContent.addClass( 'fusion-flyout-search-active' );

				// Set focus on search field if not on mobiles
				if ( Modernizr.mq( 'only screen and (min-width:'  + parseInt( avadaMenuVars.side_header_break_point ) +  'px)' ) ) {
					$flyoutContent.find( '.fusion-flyout-search .s' ).focus();
				}
			} else {
				$flyoutContent.removeClass( 'fusion-flyout-active' );
				$flyoutContent.removeClass( 'fusion-flyout-search-active' );

				resetFlyoutActiveCSS();
			}
			$flyoutContent.removeClass( 'fusion-flyout-menu-active' );
		} else {
			$flyoutContent.addClass( 'fusion-flyout-active' );
			$flyoutContent.addClass( 'fusion-flyout-search-active' );

			// Set focus on search field if not on mobiles
			if ( Modernizr.mq( 'only screen and (min-width:'  + parseInt( avadaMenuVars.side_header_break_point ) +  'px)' ) ) {
				$flyoutContent.find( '.fusion-flyout-search .s' ).focus();
			}

			setFlyoutActiveCSS();
		}
	});
});

jQuery( window ).load( function() {

	// Adjust mobile menu when it falls to 2 rows.
	window.mobileMenuSepAdded = false;

	function adjustMobileMenuSettings() {
		var menuWidth = 0;

		if ( Modernizr.mq( 'only screen and (max-width: ' + avadaMenuVars.side_header_break_point + 'px)' ) ) {
			jQuery( '.fusion-secondary-menu > ul' ).children( 'li' ).each( function() {
				menuWidth += jQuery( this ).outerWidth( true ) + 2;
			});

			if ( menuWidth > jQuery( window ).width() && jQuery( window ).width() > 318 ) {
				if ( ! window.mobileMenuSepAdded ) {
					jQuery( '.fusion-secondary-menu > ul' ).append( '<div class="fusion-mobile-menu-sep"></div>' );
					jQuery( '.fusion-secondary-menu > ul' ).css( 'position', 'relative' );
					jQuery( '.fusion-mobile-menu-sep' ).css( {
						'position': 'absolute',
						'top': jQuery( '.fusion-secondary-menu > ul > li' ).height() - 1 + 'px',
						'width': '100%',
						'border-bottom-width': '1px',
						'border-bottom-style': 'solid'
					});
					window.mobileMenuSepAdded = true;
				}
			} else {
				jQuery( '.fusion-secondary-menu > ul' ).css( 'position', '' );
				jQuery( '.fusion-secondary-menu > ul' ).find( '.fusion-mobile-menu-sep' ).remove();
				window.mobileMenuSepAdded = false;
			}
		} else {
			jQuery( '.fusion-secondary-menu > ul' ).css( 'position', '' );
			jQuery( '.fusion-secondary-menu > ul' ).find( '.fusion-mobile-menu-sep' ).remove();
			window.mobileMenuSepAdded = false;
		}
	}

	adjustMobileMenuSettings();

	// Side header main nav
	if ( 'classic' === avadaMenuVars.mobile_menu_design ) {
		jQuery( '.sh-mobile-nav-holder' ).append( '<div class="mobile-selector"><span>' + avadaMenuVars.dropdown_goto + '</span></div>' );
		jQuery( '.sh-mobile-nav-holder .mobile-selector' ).append( '<div class="selector-down"></div>' );
	}
	jQuery( '.sh-mobile-nav-holder' ).append( jQuery( '.nav-holder .fusion-navbar-nav' ).clone() );
	jQuery( '.sh-mobile-nav-holder .fusion-navbar-nav' ).attr( 'id', 'mobile-nav' );
	jQuery( '.sh-mobile-nav-holder ul#mobile-nav' ).removeClass( 'fusion-navbar-nav' );
	jQuery( '.sh-mobile-nav-holder ul#mobile-nav' ).children( '.cart' ).remove();
	jQuery( '.sh-mobile-nav-holder ul#mobile-nav .mobile-nav-item' ).children( '.login-box' ).remove();

	jQuery( '.sh-mobile-nav-holder ul#mobile-nav li' ).children( '#main-nav-search-link' ).each( function() {
		jQuery( this ).parents( 'li' ).remove();
	});
	jQuery( '.sh-mobile-nav-holder ul#mobile-nav' ).find( 'li' ).each( function() {
		var classes = 'mobile-nav-item';

		if ( jQuery( this ).hasClass( 'current-menu-item' ) || jQuery( this ).hasClass( 'current-menu-parent' ) || jQuery( this ).hasClass( 'current-menu-ancestor' ) ) {
			classes += ' mobile-current-nav-item';
		}
		jQuery( this ).attr( 'class', classes );
		if ( jQuery( this ).attr( 'id' ) ) {
			jQuery( this ).attr( 'id', jQuery( this ).attr( 'id' ).replace( 'menu-item', 'mobile-menu-item' ) );
		}
		jQuery( this ).attr( 'style', '' );
	});
	jQuery( '.sh-mobile-nav-holder .mobile-selector' ).click( function() {
		if ( jQuery( '.sh-mobile-nav-holder #mobile-nav' ).hasClass( 'mobile-menu-expanded' ) ) {
			jQuery( '.sh-mobile-nav-holder #mobile-nav' ).removeClass( 'mobile-menu-expanded' );
		} else {
			jQuery( '.sh-mobile-nav-holder #mobile-nav' ).addClass( 'mobile-menu-expanded' );
		}
		jQuery( '.sh-mobile-nav-holder #mobile-nav' ).slideToggle( 200, 'easeOutQuad' );
	});

	// Make mobile menu sub-menu toggles
	if ( 1 == avadaMenuVars.submenu_slideout ) {
		jQuery( '.header-wrapper .mobile-topnav-holder .mobile-topnav li, .header-wrapper .mobile-nav-holder .navigation li, .sticky-header .mobile-nav-holder .navigation li, .sh-mobile-nav-holder .navigation li' ).each( function() {
			var classes = 'mobile-nav-item';

			if ( jQuery( this ).hasClass( 'current-menu-item' ) || jQuery( this ).hasClass( 'current-menu-parent' ) || jQuery( this ).hasClass( 'current-menu-ancestor' ) || jQuery( this ).hasClass( 'mobile-current-nav-item' ) ) {
				classes += ' mobile-current-nav-item';
			}
			jQuery( this ).attr( 'class', classes );

			if ( jQuery( this ).find( ' > ul' ).length ) {
				jQuery( this ).prepend( '<span href="#" aria-haspopup="true" class="open-submenu"></span>' );

				jQuery( this ).find( ' > ul' ).hide();
			}
		});

		jQuery( '.header-wrapper .mobile-topnav-holder .open-submenu, .header-wrapper .mobile-nav-holder .open-submenu, .sticky-header .mobile-nav-holder .open-submenu, .sh-mobile-nav-holder .open-submenu' ).click( function( e ) {
			e.stopPropagation();
			jQuery( this ).parent().children( '.sub-menu' ).slideToggle( 200, 'easeOutQuad' );

		});
	}

	if ( 'ontouchstart' in document.documentElement || navigator.msMaxTouchPoints ) {
		jQuery( '.fusion-main-menu li.menu-item-has-children > a, .fusion-secondary-menu li.menu-item-has-children > a, .order-dropdown > li .current-li' ).on( 'click', function( e ) {
			var link = jQuery( this );
			if ( link.hasClass( 'hover' ) ) {
				link.removeClass( 'hover' );
				return true;
			} else {
				link.addClass( 'hover' );
				jQuery( '.fusion-main-menu li.menu-item-has-children > a, .fusion-secondary-menu li.menu-item-has-children > a, .order-dropdown > li .current-li' ).not( this ).removeClass( 'hover' );
				return false;
			}
		});

		jQuery( '.sub-menu li, .fusion-mobile-nav-item li' ).not( 'li.menu-item-has-children' ).on( 'click', function( e ) {
			var link = jQuery( this ).find( 'a' ).attr( 'href' );
			if ( '_blank' != jQuery( this ).find( 'a' ).attr( 'target' ) ) { // Fix for #1564
				window.location = link;
			}

			return true;
		});
	}

	// Touch support for win phone devices
	jQuery( '.fusion-main-menu li.menu-item-has-children > a, .fusion-secondary-menu li.menu-item-has-children > a, .side-nav li.page_item_has_children > a' ).each( function() {
		jQuery( this ).attr( 'aria-haspopup', 'true' );
	});

	// Ubermenu responsive fix
	if ( 1 <= jQuery( '.megaResponsive' ).length ) {
		jQuery( '.mobile-nav-holder.main-menu' ).addClass( 'set-invisible' );
	}

	// Position main menu search box correctly
	if ( 'Top' ==  avadaMenuVars.header_position ) {
		jQuery( window ).on( 'resize', function() {
			jQuery( '.main-nav-search' ).each( function() {
				var searchForm,
				    searchFormWidth,
				    searchFormLeftEdge,
				    searchFormRightEdge,
				    searchMenuItemLeftEdge,
				    windowRightEdge;

				if ( jQuery( this ).hasClass( 'search-box-open' ) ) {
					searchForm             = jQuery( this ).find( '.main-nav-search-form' );
					searchFormWidth        = searchForm.outerWidth();
					searchFormLeftEdge     = searchForm.offset().left;
					searchFormRightEdge    = searchFormLeftEdge + searchFormWidth;
					searchMenuItemLeftEdge = searchForm.parent().offset().left;
					windowRightEdge        = jQuery( window ).width();

					if ( ! jQuery( 'body.rtl' ).length ) {
						if ( ( searchFormLeftEdge < searchMenuItemLeftEdge && searchFormLeftEdge < 0 ) || ( searchFormLeftEdge == searchMenuItemLeftEdge && searchFormLeftEdge - searchFormWidth < 0 ) ) {
							searchForm.css({
								'left': '0',
								'right': 'auto'
							});
						} else {
							searchForm.css({
								'left': 'auto',
								'right': '0'
							});
						}
					} else {
						if ( ( searchFormLeftEdge == searchMenuItemLeftEdge && searchFormRightEdge > windowRightEdge ) || ( searchFormLeftEdge < searchMenuItemLeftEdge && searchFormRightEdge + searchFormWidth > windowRightEdge )  ) {
							searchForm.css({
								'left': 'auto',
								'right': '0'
							});
						} else {
							searchForm.css({
								'left': '0',
								'right': 'auto'
							});
						}
					}
				}
			});
		});
	}

	jQuery( window ).on( 'resize', function() {
		adjustMobileMenuSettings();
	});
});