<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Don't resize images.
 * Returns an empty array.
 *
 * @param array $sizes We don't really care in this context...
 * @return array
 */
function avada_filter_image_sizes( $sizes ) {
	return array();
}
// Hook importer into admin init.
add_action( 'wp_ajax_fusion_import_demo_data', 'fusion_importer' );

/**
 * The main importer function.
 */
function fusion_importer() {
	global $wpdb;

	check_ajax_referer( 'avada_demo_ajax', 'security' );

	// Include the remote file getter.
	include_once wp_normalize_path( dirname( __FILE__ ) . '/class-avada-importer-data.php');

	if ( current_user_can( 'manage_options' ) ) {
		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true ); // We are loading importers.
		}

		if ( ! class_exists( 'WP_Importer' ) ) { // If main importer class doesn't exist.
			$wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			include $wp_importer;
		}

		if ( ! class_exists( 'WP_Import' ) ) { // If WP importer doesn't exist.
			$wp_import = Avada::$template_dir_path . '/includes/plugins/importer/wordpress-importer.php';
			include $wp_import;
		}

		if ( class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) { // Check for main import class and wp import class.
			if ( ! isset( $_POST['demo_type'] ) || '' == trim( $_POST['demo_type'] ) ) {
				$demo_type = 'classic';
			} else {
				$demo_type = $_POST['demo_type'];
			}

			if ( function_exists( 'ini_get' ) ) {
				if ( 300 < ini_get( 'max_execution_time' ) ) {
					@ini_set( 'max_execution_time', 300 );
				}
				if ( 512 < intval( ini_get( 'memory_limit' ) ) ) {
					@ini_set( 'memory_limit', '512M' );
				}
			}

			if ( ! class_exists( 'Avada_Importer_Data' ) ) {
				include_once wp_normalize_path( Avada::$template_dir_path . '/includes/plugins/importer/class-avada-importer-data.php' );
			}
			// Get remote files and save locally.
			$importer_files = new Avada_Importer_Data( $demo_type );

			$theme_xml          = $importer_files->get_path( 'avada.xml' );
			$theme_options_file = $importer_files->get_path( 'theme_options.json' );
			$widgets_file       = $importer_files->get_path( 'widget_data.json' );
			$fs_url             = $importer_files->get_path( 'fusion_slider.zip' );

			$shop_demo      = $importer_files->is_shop();
			$sidebars       = $importer_files->get_sidebars();
			$homepage_title = $importer_files->get_homepage_title();
			$woopages       = $importer_files->get_woopages();
			$fs_exists      = true;

			if ( 'landing_product' === $demo_type ) {
				$fs_exists = false;
			}

			add_filter( 'intermediate_image_sizes_advanced', 'avada_filter_image_sizes' );

			/* Import Woocommerce if WooCommerce Exists */
			if ( class_exists( 'WooCommerce' ) && $shop_demo ) {
				$importer = new WP_Import();
				$importer->fetch_attachments = true;
				ob_start();
				$importer->import( $theme_xml );
				ob_end_clean();

				foreach ( $woopages as $woo_page_name => $woo_page_title ) {
					$woopage = get_page_by_title( $woo_page_title );
					if ( isset( $woopage ) && $woopage->ID ) {
						update_option( $woo_page_name, $woopage->ID ); // Front Page.
					}
				}

				// We no longer need to install pages.
				delete_option( '_wc_needs_pages' );
				delete_transient( '_wc_activation_redirect' );

				// Flush rules after install.
				flush_rewrite_rules();
			} else {
				$importer = new WP_Import();
				// Import Posts, Pages, Portfolio Content, FAQ, Images, Menus.
				$importer->fetch_attachments = true;
				$importer->import( $theme_xml );

				flush_rewrite_rules();
			}

			// Set imported menus to registered theme locations
			$locations = get_theme_mod( 'nav_menu_locations' ); // Registered menu locations in theme.
			$menus     = wp_get_nav_menus(); // Registered menus.

			if ( $menus ) {
				if ( 'classic' === $demo_type ) {
					// @codingStandardsIgnoreLine
					$opmenu = get_page_by_title( 'One Page Parallax' );
				} elseif ( 'landing_product' == $demo_type ) {
					// @codingStandardsIgnoreLine
					$opmenu = get_page_by_title( 'Homepage' );
				} elseif ( 'technology' == $demo_type ) {
					// @codingStandardsIgnoreLine
					$opmenu = get_page_by_title( 'Technology' );
				} elseif ( 'wedding' == $demo_type ) {
					// @codingStandardsIgnoreLine
					$opmenu = get_page_by_title( 'Home' );
				} elseif ( 'resume' == $demo_type ) {
					// @codingStandardsIgnoreLine
					$opmenu = get_page_by_title( 'Home' );
				}
				foreach ( $menus as $menu ) { // Assign menus to theme locations.
					if ( 'classic' === $demo_type ) {
						if ( 'Classic Main' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						} elseif ( 'Classic 404' === $menu->name ) {
							$locations['404_pages'] = $menu->term_id;
						} elseif ( 'Classic Top' === $menu->name ) {
							$locations['top_navigation'] = $menu->term_id;
						}

						// Assign One Page Menu.
						if ( isset( $opmenu ) && $opmenu->ID && 'Classic One Page' === $menu->name ) {
							update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
						}
					} elseif ( 'agency' === $demo_type ) {
						if ( 'Agency Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'app' === $demo_type ) {
						if ( 'App Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'travel' === $demo_type ) {
						if ( 'Travel Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'cafe' === $demo_type ) {
						if ( 'Cafe Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'fashion' == $demo_type ) {
						if ( 'Fashion Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'architecture' === $demo_type ) {
						if ( 'Architecture Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'hosting' === $demo_type ) {
						if ( 'Hosting Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'hotel' === $demo_type ) {
						if ( 'Hotel Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'law' === $demo_type ) {
						if ( 'Law Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'lifestyle' === $demo_type ) {
						if ( 'Lifestyle Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'church' === $demo_type ) {
						if ( 'Church Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'gym' === $demo_type ) {
						if ( 'Gym Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'photography' === $demo_type ) {
						if ( 'Photography Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'modern_shop' === $demo_type ) {
						if ( 'Modern Shop Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'classic_shop' === $demo_type ) {
						if ( 'Classic Shop Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						} elseif ( 'Classic Shop Top Secondary Menu' == $menu->name ) {
							$locations['top_navigation'] = $menu->term_id;
						}
					} elseif ( 'landing_product' === $demo_type ) {
						// Assign One Page Menu.
						if ( isset( $opmenu ) && $opmenu->ID && 'Landing Product Landing Page Menu' === $menu->name ) {
							update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
						}

						if ( 'Landing Product Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'forum' === $demo_type ) {
						if ( 'Forum Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'resume' === $demo_type ) {
						// Assign One Page Menu.
						if ( isset( $opmenu ) && $opmenu->ID && 'Resume Homepage Menu' === $menu->name ) {
							update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
						}

						if ( 'Resume Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'wedding' === $demo_type ) {
						// Assign One Page Menu.
						if ( isset( $opmenu ) && $opmenu->ID && 'Wedding Homepage Menu' === $menu->name ) {
							update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
						}

						if ( 'Wedding Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'health' === $demo_type ) {
						if ( 'Health Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'technology' === $demo_type ) {
						// Assign One Page Menu.
						if ( isset( $opmenu ) && $opmenu->ID && 'Technology Front Page Menu' === $menu->name ) {
							update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
						}

						if ( 'Technology Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'charity' === $demo_type ) {
						if ( 'Charity Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'construction' === $demo_type ) {
						if ( 'Construction Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'creative' === $demo_type ) {
						if ( 'Creative Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'daycare' === $demo_type ) {
						if ( 'Daycare Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} elseif ( 'veterinarian' === $demo_type ) {
						if ( 'Veterinarian Main Menu' === $menu->name ) {
							$locations['main_navigation'] = $menu->term_id;
						}
					} // End if().
				} // End foreach().
			} // End if().

			set_theme_mod( 'nav_menu_locations', $locations ); // Set menus to locations.

			// Import Theme Options.
			$theme_options_json = file_get_contents( $theme_options_file );
			$theme_options = json_decode( $theme_options_json, true );
			$theme_options['logo_retina'] = '';
			$theme_options['sticky_header_logo_retina'] = '';
			$theme_options['mobile_logo_retina'] = '';
			$theme_options_db_name = Avada::get_original_option_name();
			update_option( $theme_options_db_name, $theme_options );

			// Add sidebar widget areas.
			if ( false !== $sidebars ) {
				update_option( 'sbg_sidebars', $sidebars );

				foreach ( $sidebars as $sidebar ) {
					$sidebar_class = avada_name_to_class( $sidebar );
					register_sidebar( array(
						'name'          => $sidebar,
						'id'            => 'avada-custom-sidebar-' . strtolower( $sidebar_class ),
						'before_widget' => '<div id="%1$s" class="widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<div class="heading"><h4 class="widget-title">',
						'after_title'   => '</h4></div>',
					) );
				}
			}

			// Add data to widgets.
			if ( isset( $widgets_file ) && $widgets_file ) {
				$widgets_json = $widgets_file; // Widgets data file.
				$widgets_json = file_get_contents( $widgets_json );
				$widget_data = $widgets_json;
				$import_widgets = fusion_import_widget_data( $widget_data );
			}

			// Import Layerslider.
			if ( defined( 'LS_PLUGIN_VERSION' ) && file_exists( WP_PLUGIN_DIR . '/LayerSlider/classes/class.ls.importutil.php' ) && false !== $importer_files->get_layerslider() ) {
				// Get importUtil.
				include WP_PLUGIN_DIR . '/LayerSlider/classes/class.ls.importutil.php';

				foreach ( $importer_files->get_layerslider() as $layer_file ) {
					// Finally import rev slider data files.
					$filepath = $importer_files->get_path( 'layersliders/' . $layer_file );
					$import = new LS_ImportUtil( $filepath );
				}

				// Get all sliders
				// Table name.
				$table_name = $wpdb->prefix . 'layerslider';

				// Get sliders.
				$sliders = $wpdb->get_results( "SELECT * FROM $table_name WHERE flag_hidden = '0' AND flag_deleted = '0' ORDER BY date_c ASC" );
				$slides = array();
				if ( ! empty( $sliders ) ) {
					foreach ( $sliders as $key => $item ) {
						$slides[ $item->id ] = $item->name;
					}
				}

				if ( $slides ) {
					foreach ( $slides as $key => $val ) {
						$slides_array[ $val ] = $key;
					}
				}

				// Assign LayerSlider.
				if ( 'classic' == $demo_type ) {
					$lspage = get_page_by_title( 'Layer Slider' );
					if ( isset( $lspage ) && $lspage->ID && $slides_array['Avada Full Width'] ) {
						update_post_meta( $lspage->ID, 'pyre_slider', $slides_array['Avada Full Width'] );
					}
				}
			}

			// Import Revslider.
			if ( class_exists( 'UniteFunctionsRev' ) && false != $importer_files->get_revslider() ) { // If revslider is activated.

				$slider = new RevSlider();
				foreach ( $importer_files->get_revslider() as $rev_file ) {
					// Finally import rev slider data files.
					$filepath = $importer_files->get_path( 'revsliders/' . $rev_file );
					ob_start();
					$slider->importSliderFromPost( true, false, $filepath );
					ob_clean();
					ob_end_clean();
				}
			}

			// Set reading options.
			$homepage = get_page_by_title( $homepage_title );
			if ( isset( $homepage ) && $homepage->ID ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $homepage->ID ); // Front Page.
			}

			// Import site title.
			$site_title = 'Avada ' . ucwords( str_replace( '_', ' ', $demo_type ) );
			update_option( 'blogname', $site_title );

			// Fusion Sliders Import.
			if ( true == $fs_exists ) {
				@avada_import_fsliders( $fs_url, $demo_type );
			}

			update_option( 'avada_imported_demo', 'true' );

			// Reset all caches.
			avada_reset_all_cache();

			echo 'imported';

			exit;
		}
	}
}

/**
 * Parsing Widgets Function
 * Thanks to http://wordpress.org/plugins/widget-settings-importexport/
 *
 * @param string $widget_data The widget-data, JSON-formatted.
 */
function fusion_import_widget_data( $widget_data ) {
	$json_data = json_decode( $widget_data, true );

	$sidebar_data = $json_data[0];
	$widget_data = $json_data[1];

	foreach ( $widget_data as $widget_data_title => $widget_data_value ) {
		$widgets[ $widget_data_title ] = array();
		foreach ( $widget_data_value as $widget_data_key => $widget_data_array ) {
			if ( is_int( $widget_data_key ) ) {
				$widgets[ $widget_data_title ][ $widget_data_key ] = 'on';
			}
		}
	}
	unset( $widgets[''] );

	foreach ( $sidebar_data as $title => $sidebar ) {
		$count = count( $sidebar );
		for ( $i = 0; $i < $count; $i++ ) {
			$widget = array();
			$widget['type'] = trim( substr( $sidebar[ $i ], 0, strrpos( $sidebar[ $i ], '-' ) ) );
			$widget['type-index'] = trim( substr( $sidebar[ $i ], strrpos( $sidebar[ $i ], '-' ) + 1 ) );
			if ( ! isset( $widgets[ $widget['type'] ][ $widget['type-index'] ] ) ) {
				unset( $sidebar_data[ $title ][ $i ] );
			}
		}
		$sidebar_data[ $title ] = array_values( $sidebar_data[ $title ] );
	}

	foreach ( $widgets as $widget_title => $widget_value ) {
		foreach ( $widget_value as $widget_key => $widget_value ) {
			$widgets[ $widget_title ][ $widget_key ] = $widget_data[ $widget_title ][ $widget_key ];
		}
	}

	$sidebar_data = array( array_filter( $sidebar_data ), $widgets );

	fusion_parse_import_data( $sidebar_data );
}

/**
 * Import data.
 *
 * @param array $import_array The array of data to be imported.
 */
function fusion_parse_import_data( $import_array ) {
	global $wp_registered_sidebars;
	$sidebars_data = $import_array[0];
	$widget_data = $import_array[1];
	$current_sidebars = get_option( 'sidebars_widgets' );
	$new_widgets = array();

	foreach ( $sidebars_data as $import_sidebar => $import_widgets ) {

		foreach ( $import_widgets as $import_widget ) {
			// If the sidebar exists.
			if ( isset( $wp_registered_sidebars[ $import_sidebar ] ) ) {
				$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
				$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
				$current_widget_data = get_option( 'widget_' . $title );
				$new_widget_name = fusion_get_new_widget_name( $title, $index );
				$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

				if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
					while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
						$new_index++;
					}
				}
				$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
				if ( array_key_exists( $title, $new_widgets ) ) {
					if ( 'nav_menu' == $title & ! is_numeric( $index ) ) {
						$menu = wp_get_nav_menu_object( $index );
						$menu_id = $menu->term_id;
						$new_widgets[ $title ][ $new_index ] = $menu_id;
					} else {
						$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
					}
					$multiwidget = $new_widgets[ $title ]['_multiwidget'];
					unset( $new_widgets[ $title ]['_multiwidget'] );
					$new_widgets[ $title ]['_multiwidget'] = $multiwidget;
				} else {
					if ( 'nav_menu' == $title & ! is_numeric( $index ) ) {
						$menu = wp_get_nav_menu_object( $index );
						$menu_id = $menu->term_id;
						$current_widget_data[ $new_index ] = $menu_id;
					} else {
						$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
					}
					$current_multiwidget = isset( $current_widget_data['_multiwidget'] ) ? $current_widget_data['_multiwidget'] : false;
					$new_multiwidget = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
					$multiwidget = ( $current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
					unset( $current_widget_data['_multiwidget'] );
					$current_widget_data['_multiwidget'] = $multiwidget;
					$new_widgets[ $title ] = $current_widget_data;
				}
			}
		}
	}

	if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
		update_option( 'sidebars_widgets', $current_sidebars );

		foreach ( $new_widgets as $title => $content ) {
			update_option( 'widget_' . $title, $content );
		}

		return true;
	}

	return false;
}

/**
 * Get the new widget name.
 *
 * @param string $widget_name  The widget-name.
 * @param int    $widget_index The index of the widget.
 */
function fusion_get_new_widget_name( $widget_name, $widget_index ) {
	$current_sidebars = get_option( 'sidebars_widgets' );
	$all_widget_array = array();
	foreach ( $current_sidebars as $sidebar => $widgets ) {
		if ( ! empty( $widgets ) && is_array( $widgets ) && 'wp_inactive_widgets' != $sidebar ) {
			foreach ( $widgets as $widget ) {
				$all_widget_array[] = $widget;
			}
		}
	}
	while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
		$widget_index++;
	}
	$new_widget_name = $widget_name . '-' . $widget_index;
	return $new_widget_name;
}

if ( function_exists( 'layerslider_import_sample_slider' ) ) {
	/**
	 * Import LayerSlider.
	 *
	 * @param mixed $layerslider_data The data.
	 */
	function avada_import_sample_slider( $layerslider_data ) {
		// Base64 encoded, serialized slider export code.
		$sample_slider = $layerslider_data;

		// Iterate over the sliders.
		foreach ( $sample_slider as $sliderkey => $slider ) {

			// Iterate over the layers.
			foreach ( $sample_slider[ $sliderkey ]['layers'] as $layerkey => $layer ) {

				// Change background images if any.
				if ( ! empty( $sample_slider[ $sliderkey ]['layers'][ $layerkey ]['properties']['background'] ) ) {
					$sample_slider[ $sliderkey ]['layers'][ $layerkey ]['properties']['background'] = LS_ROOT_URL . 'sampleslider/' . basename( $layer['properties']['background'] );
				}

				// Change thumbnail images if any.
				if ( ! empty( $sample_slider[ $sliderkey ]['layers'][ $layerkey ]['properties']['thumbnail'] ) ) {
					$sample_slider[ $sliderkey ]['layers'][ $layerkey ]['properties']['thumbnail'] = LS_ROOT_URL . 'sampleslider/' . basename( $layer['properties']['thumbnail'] );
				}

				// Iterate over the sublayers.
				if ( isset( $layer['sublayers'] ) && ! empty( $layer['sublayers'] ) ) {
					foreach ( $layer['sublayers'] as $sublayerkey => $sublayer ) {

						// Only IMG sublayers.
						if ( 'img' == $sublayer['type'] ) {
							$sample_slider[ $sliderkey ]['layers'][ $layerkey ]['sublayers'][ $sublayerkey ]['image'] = LS_ROOT_URL . 'sampleslider/' . basename( $sublayer['image'] );
						}
					}
				}
			}
		}

		// Get WPDB Object.
		global $wpdb;

		// Table name.
		$table_name = $wpdb->prefix . 'layerslider';

		// Append duplicate.
		foreach ( $sample_slider as $key => $val ) {

			// Insert the duplicate.
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO $table_name (name, data, date_c, date_m) VALUES (%s, %s, %d, %d)",
					$val['properties']['title'],
					wp_json_encode( $val ),
					time(),
					time()
				)
			);
		}
	}
}

/**
 * Rename sidebar.
 *
 * @param string $name The name.
 */
function avada_name_to_class( $name ) {
	$class = str_replace( array( ' ', ',', '.', '"', "'", '/', '\\', '+', '=', ')', '(', '*', '&', '^', '%', '$', '#', '@', '!', '~', '`', '<', '>', '?', '[', ']', '{', '}', '|', ':' ), '', $name );
	return $class;
}

/**
 * Import Fusion Sliders.
 *
 * @param string $zip_file  The path to the zip file.
 * @param string $demo_type The demo name.
 */
function avada_import_fsliders( $zip_file, $demo_type = '' ) {
	$upload_dir = wp_upload_dir();
	$base_dir   = trailingslashit( $upload_dir['basedir'] );
	$fs_dir     = $base_dir . 'fusion_slider_exports/';

	// Init the filesystem.
	$filesystem = Fusion_Helper::init_filesystem();

	// Delete existing folder
	$filesystem->delete( $fs_dir, true, 'd' );

	// Unzip file to folder.
	unzip_file( $zip_file, $fs_dir );

	// Replace remote URLs with local ones.
	$sliders_xml = $filesystem->get_contents( $fs_dir . 'sliders.xml' );
	$sliders_xml = preg_replace_callback( '/(?<=<wp:meta_value><!\[CDATA\[)(https?:\/\/avada.theme-fusion.com)+(.*?)(?=]]><)/', 'fusion_fs_importer_replace_url', $sliders_xml );
	$filesystem->put_contents( $fs_dir . 'sliders.xml', $sliders_xml );

	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', true );
	}

	if ( ! class_exists( 'WP_Importer' ) ) { // If main importer class doesn't exist.
		$wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		include $wp_importer;
	}

	if ( ! class_exists( 'WP_Import' ) ) { // If WP importer doesn't exist.
		$wp_import = plugin_dir_path( __FILE__ ) . 'libs/wordpress-importer.php';
		include $wp_import;
	}

	if ( class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) {
		$loop = new WP_Query( array( 'post_type' => 'slide', 'posts_per_page' => -1, 'meta_key' => '_thumbnail_id' ) );

		while ( $loop->have_posts() ) { $loop->the_post();
			$thumbnail_ids[ get_post_meta( get_the_ID(), '_thumbnail_id', true ) ] = get_the_ID();
		}

		if ( is_dir( $fs_dir ) ) {
			foreach ( new DirectoryIterator( $fs_dir ) as $file ) {
				if ( $file->isDot() || $file->getFilename() == '.DS_Store' ) {
					continue;
				}

				$image_path = pathinfo( $fs_dir . $file->getFilename() );
				if ( 'xml' != $image_path['extension'] && 'json' != $image_path['extension'] ) {
					$filename = $image_path['filename'];
					$new_image_path = $upload_dir['path'] . '/' . $image_path['basename'];
					$new_image_url = $upload_dir['url'] . '/' . $image_path['basename'];
					@copy( $fs_dir . $file->getFilename(), $new_image_path );

					// Check the type of tile. We'll use this as the 'post_mime_type'.
					$filetype = wp_check_filetype( basename( $new_image_path ), null );

					// Prepare an array of post data for the attachment.
					$attachment = array(
						'guid'		   => $new_image_url,
						'post_mime_type' => $filetype['type'],
						'post_title'	 => preg_replace( '/\.[^.]+$/', '', basename( $new_image_path ) ),
						'post_content'   => '',
						'post_status'	=> 'inherit',
					);

					// Insert the attachment.
					$attach_id = wp_insert_attachment( $attachment, $new_image_path, $thumbnail_ids[ $filename ] );

					// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
					require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/image.php' );

					// Generate the metadata for the attachment, and update the database record.
					$attach_data = wp_generate_attachment_metadata( $attach_id, $new_image_path );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					set_post_thumbnail( $thumbnail_ids[ $filename ], $attach_id );
				}
			}
		}

		$url = wp_nonce_url( 'edit.php?post_type=slide&page=fs_export_import' );
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) {
			return; // Stop processing here.
		}

		if ( WP_Filesystem( $creds ) ) {
			global $wp_filesystem;

			$settings = $wp_filesystem->get_contents( $fs_dir . 'settings.json' );

			$decode = json_decode( $settings, true );

			foreach ( $decode as $slug => $settings ) {
				$get_term = get_term_by( 'slug', $slug, 'slide-page' );

				if ( $get_term ) {
					update_option( 'taxonomy_' . $get_term->term_id, $settings );
				}
			}
		}
	}
}

/**
 * Delete a slider directory.
 *
 * @param string $dir_path The absolute path to the directory.
 * @throws InvalidArgumentException The exception.
 */
function fusion_slider_delete_dir( $dir_path ) {
	if ( ! is_dir( $dir_path ) ) {
		$message = sprintf( esc_html__( '%s must be a directory', 'Avada' ), $dir_path );
		throw new InvalidArgumentException( $message );
	}
	if ( '/' != substr( $dir_path, strlen( $dir_path ) - 1, 1 ) ) {
		$dir_path .= '/';
	}
	$files = fusion_get_import_files( $dir_path, '*' );

	foreach ( $files as $file ) {
		if ( is_dir( $file ) ) {
			$this->deleteDir( $file );
		} else {
			unlink( $file );
		}
	}
	rmdir( $dir_path );
}

/**
 * Returns all files in directory with the given filetype. Uses glob() for older
 * php versions and recursive directory iterator otherwise.
 *
 * @param string $directory Directory that should be parsed.
 * @param string $filetype  The file type.
 * @return array $files     File names that match the $filetype.
 */
function fusion_get_import_files( $directory, $filetype ) {
	$phpversion = phpversion();
	$files = array();

	// Check if the php version allows for recursive iterators.
	if ( version_compare( $phpversion, '5.2.11', '>' ) ) {
		if ( '*' != $filetype ) {
			$filetype = '/^.*\.' . $filetype . '$/';
		} else {
			$filetype = '/.+\.[^.]+$/';
		}
		$directory_iterator = new RecursiveDirectoryIterator( $directory );
		$recusive_iterator = new RecursiveIteratorIterator( $directory_iterator );
		$regex_iterator = new RegexIterator( $recusive_iterator, $filetype );

		foreach ( $regex_iterator as $file ) {
			$files[] = $file->getPathname();
		}
		// Fallback to glob() for older php versions.
	} else {
		if ( '*' != $filetype ) {
			$filetype = '*.' . $filetype;
		}

		foreach ( glob( $directory . $filetype ) as $filename ) {
			$filename = basename( $filename );
			$files[] = $directory . $filename;
		}
	}

	return $files;
}

function fusion_fs_importer_replace_url( $matches ) {
	// Get the uploads folder.
	$wp_upload_dir = wp_upload_dir();
	if ( is_array( $matches ) ) {
		foreach ( $matches as $key => $match ) {
			if ( false !== strpos( $match, 'wp-content/uploads/sites/' ) ) {
				$parts = explode( 'wp-content/uploads/sites/', $match );
				if ( isset( $parts[1] ) ) {
					$sub_parts = explode( '/', $parts[1] );
					unset( $sub_parts[0] );
					$parts[1] = implode( '/', $sub_parts );

					// append the url to the uploads url.
					$parts[0] = $wp_upload_dir['baseurl'];
					return implode( '/', $parts );
				}
			}
		}
	}
	return $matches;
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
