<?php

	/**
	* @Description : Plugin main core
	* @Package : Drag & Drop Multiple File Upload - Contact Form 7
	* @Author : Glen Don L. Mongaya
	*/

	if ( ! defined( 'ABSPATH' ) || ! defined('dnd_upload_cf7') ) {
		exit;
	}

	/**
	* Begin : begin plugin hooks
	*/

	add_action( 'wpcf7_init', 'dnd_cf7_upload_add_form_tag_file' );
	add_action( 'wpcf7_enqueue_scripts', 'dnd_cf7_scripts' );

	// Hook language init
	add_action('plugins_loaded','dnd_load_plugin_textdomain');

	// Ajax Upload
	add_action( 'wp_ajax_dnd_codedropz_upload', 'dnd_upload_cf7_upload' );
	add_action( 'wp_ajax_nopriv_dnd_codedropz_upload', 'dnd_upload_cf7_upload' );

	// Hook mail cf7
	add_action('wpcf7_before_send_mail','dnd_cf7_before_send_mail', 30, 1);
	add_action('wpcf7_mail_components','dnd_cf7_mail_components', 50, 2);

	// Add Submenu - Settings
	add_action('admin_menu', 'dnd_admin_settings');

	// Load plugin text-domain
	function dnd_load_plugin_textdomain() {
		load_plugin_textdomain( 'dnd-upload-cf7', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	}

	// Hooks for admin settings
	function dnd_admin_settings() {
		add_submenu_page( 'wpcf7', 'Drag & Drop Uploader - Settings', 'Drag & Drop Upload', 'manage_options', 'drag-n-drop-upload','dnd_upload_admin_settings');
		add_action('admin_init','dnd_upload_register_settings');
	}

	// Default Error Message
	function dnd_cf7_error_msg( $error_key ) {

		// Array of default error message
		$errors = array(
			'server_limit'		=>	__('The uploaded file exceeds the maximum upload size of your server.','dnd-upload-cf7'),
			'failed_upload'		=>	__('Uploading a file fails for any reason','dnd-upload-cf7'),
			'large_file'		=>	__('Uploaded file is too large','dnd-upload-cf7'),
			'invalid_type'		=>	__('Uploaded file is not allowed for file type','dnd-upload-cf7'),
		);

		// return error message based on $error_key request
		if( isset( $errors[ $error_key ] ) ) {
			return $errors[ $error_key ];
		}

		return false;
	}

	// Hooks before sending the email
	function dnd_cf7_before_send_mail( $wpcf7 ){
		global $_mail;

		// Mail Counter
		$_mail = 0;

		// Check If send attachment as link
		if( ! get_option('drag_n_drop_mail_attachment') ) {
			return $wpcf7;
		}

		// cf7 instance
		$submission = WPCF7_Submission::get_instance();

		// Check for submission
		if( $submission ) {

			// Get posted data
			$submitted['posted_data'] = $submission->get_posted_data();

			//Get an array containing the current upload directory’s path and url.
			$upload_dir = wp_upload_dir();

			// Parse fields
			$fields = $wpcf7->scan_form_tags();

			// Prop email
			$mail = $wpcf7->prop('mail');

			// Default upload path
			$simple_path = $upload_dir['baseurl'];

			// Check if media upload oraganized by year and month folders
			if( get_option('uploads_use_yearmonth_folders') ) {
				$simple_path = $upload_dir['baseurl'] . '/wp_dndcf7_uploads' . dirname( $upload_dir['subdir'] );
			}

			// Loop fields and replace mfile code
			foreach( $fields as $field ) {
				if( $field->basetype == 'mfile') {
					if( isset( $submitted['posted_data'][$field->name] ) && ! empty( $submitted['posted_data'][$field->name] ) ) {
						$files = implode( "\n" . $simple_path . '/' , $submitted['posted_data'][$field->name] );
						$mail['body'] = str_replace( "[$field->name]", "\n" . $simple_path .'/'. $files, $mail['body'] );
					}
				}
			}

			// Save the email body
			$wpcf7->set_properties( array("mail" => $mail) );
		}

		return $wpcf7;
	}

	// hooks - Custom cf7 Mail components
	function dnd_cf7_mail_components( $components, $form ) {
		global $_mail;

		// cf7 - Submission Object
		$submission = WPCF7_Submission::get_instance();

		// get all form fields
		$fields = $form->scan_form_tags();

		// Send email link as an attachment.
		if( get_option('drag_n_drop_mail_attachment') == 'yes' ) {
			return $components;
		}

		// If mail_2 is set - Do not send attachment ( unless File Attachment field is not empty )
		if( ( $mail_2 = $form->prop('mail_2') ) && $mail_2['active'] && empty( $mail_2['attachments'] ) && $_mail >= 1 ) {
			return $components;
		}

		// Confirm upload dir
		wpcf7_init_uploads();

		// Get cf7 upload directory
		$uploads_dir = wpcf7_upload_tmp_dir();

		// Loop fields get mfile only.
		foreach( $fields as $field ) {

			// If field type equal to mfile which our default field.
			if( $field->basetype == 'mfile') {

				// Make sure we have files to attach
				if( isset( $_POST[ $field->name ] ) && count( $_POST[ $field->name ] ) > 0 ) {

					// Loop all the files and attach to cf7 components
					foreach( $_POST[ $field->name ] as $_file ) {

						// Join dir and a new file name ( get from <input type="hidden" name="upload-file-333"> )
						$new_file_name = path_join( $uploads_dir, $_file );

						// Check if submitted and file exists then file is ready.
						if ( $submission && file_exists( $new_file_name ) ) {
							$components['attachments'][] = $new_file_name;
						}
					}
				}

			}
		}

		// Increment mail counter
		$_mail = $_mail + 1;

		// Return setup components
		return $components;
	}

	// Load js and css
	function dnd_cf7_scripts() {

		// Get plugin version
		$version = dnd_upload_cf7_version;

		// enque script
		wp_enqueue_script( 'codedropz-uploader', plugins_url ('/assets/js/codedropz-uploader-min.js', dirname(__FILE__) ), array('jquery'), $version, true );
		wp_enqueue_script( 'dnd-upload-cf7', plugins_url ('/assets/js/dnd-upload-cf7.js', dirname(__FILE__) ), array('jquery','codedropz-uploader','contact-form-7'), $version, true );

		//  registered script with data for a JavaScript variable.
		wp_localize_script( 'dnd-upload-cf7', 'dnd_cf7_uploader',
			array(
				'ajax_url' 				=> admin_url( 'admin-ajax.php' ),
				'drag_n_drop_upload' 	=> array(
					'text'				=>	( get_option('drag_n_drop_text') ? get_option('drag_n_drop_text') : __('Drag & Drop Files Here','dnd-upload-cf7') ),
					'or_separator'		=>	( get_option('drag_n_drop_separator') ? get_option('drag_n_drop_separator') : __('or','dnd-upload-cf7') ),
					'browse'			=>	( get_option('drag_n_drop_browse_text') ? get_option('drag_n_drop_browse_text') : __('Browse Files','dnd-upload-cf7') ),
					'server_max_error'	=>	( get_option('drag_n_drop_error_server_limit') ? get_option('drag_n_drop_error_server_limit') : dnd_cf7_error_msg('server_limit') ),
				)
			)
		);

		// enque style
		wp_enqueue_style( 'dnd-upload-cf7', plugins_url ('/assets/css/dnd-upload-cf7.css', dirname(__FILE__) ), '', $version );
	}

	// Generate tag
	function dnd_cf7_upload_add_form_tag_file() {
		wpcf7_add_form_tag(	array( 'mfile ', 'mfile*'), 'dnd_cf7_upload_form_tag_handler', array( 'name-attr' => true ) );
	}

	// Form tag handler from the tag - callback
	function dnd_cf7_upload_form_tag_handler( $tag ) {

		// check and make sure tag name is not empty
		if ( empty( $tag->name ) ) {
			return '';
		}

		// Validate our fields
		$validation_error = wpcf7_get_validation_error( $tag->name );

		// Generate class
		$class = wpcf7_form_controls_class( 'drag-n-drop-file d-none' );

		// Add not-valid class if there's an error.
		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		// Setup element attributes
		$atts = array();

		$atts['size'] = $tag->get_size_option( '40' );
		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		// If file is required
		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		// Set invalid attributes if there's validation error
		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		// Set input type and name
		$atts['type'] = 'file';
		$atts['name'] = $tag->name;
		$atts['multiple'] = 'multiple';
		$atts['data-type'] = $tag->get_option( 'filetypes','', true);
		$atts['data-limit'] = $tag->get_option( 'limit','', true);
		$atts['data-max'] = $tag->get_option( 'max-file','', true);

		// Combine and format attrbiutes
		$atts = wpcf7_format_atts( $atts );

		// Return our element and attributes
		return sprintf('<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',	sanitize_html_class( $tag->name ), $atts, $validation_error );
	}

	// Encode type filter to support multipart since this is input type file
	add_filter( 'wpcf7_form_enctype', 'dnd_upload_cf7_form_enctype_filter' );

	function dnd_upload_cf7_form_enctype_filter( $enctype ) {
		$multipart = (bool) wpcf7_scan_form_tags( array( 'type' => array( 'drag_drop_file', 'drag_drop_file*' ) ) );

		if ( $multipart ) {
			$enctype = 'multipart/form-data';
		}

		return $enctype;
	}

	// Validation + upload handling filter
	add_filter( 'wpcf7_validate_mfile', 'dnd_upload_cf7_validation_filter', 10, 2 );
	add_filter( 'wpcf7_validate_mfile*', 'dnd_upload_cf7_validation_filter', 10, 2 );

	function dnd_upload_cf7_validation_filter( $result, $tag ) {
		$name = $tag->name;
		$id = $tag->get_id_option();

		$file = isset( $_FILES[$name] ) ? $_FILES[$name] : null;
		$multiple_files = ( isset( $_POST[ $name ] ) ? $_POST[ $name ] : null );

		// No file
		if ( $file['error'] && UPLOAD_ERR_NO_FILE != $file['error'] && is_null( $multiple_files ) ) {
			$result->invalidate( $tag, wpcf7_get_message( 'upload_failed_php_error' ) );
			return $result;
		}

		// Check if we have files or if it's empty
		if( ( is_null( $multiple_files ) || count( $multiple_files ) == 0 ) && $tag->is_required() ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			return $result;
		}

		//Empty file should be required.
		if ( empty( $file['tmp_name'] ) && $tag->is_required() && is_null( $multiple_files ) ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			return $result;
		}

		return $result;
	}

	// Generate Admin From Tag
	add_action( 'wpcf7_admin_init', 'dnd_upload_cf7_add_tag_generator', 50 );

	function dnd_upload_cf7_add_tag_generator() {
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'upload-file', __( 'multiple file upload', 'dnd-upload-cf7' ),'dnd_upload_cf7_tag_generator_file' );
	}

	// Display form in admin
	function dnd_upload_cf7_tag_generator_file( $contact_form, $args = '' ) {

		// Parse data and get our options
		$args = wp_parse_args( $args, array() );

		// Our multiple upload field
		$type = 'mfile';

		$description = __( "Generate a form-tag for a file uploading field. For more details, see %s.", 'contact-form-7' );
		$desc_link = wpcf7_link( __( 'https://contactform7.com/file-uploading-and-attachment/', 'contact-form-7' ), __( 'File Uploading and Attachment', 'contact-form-7' ) );

		?>

		<div class="control-box">
			<fieldset>
				<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
									<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"><?php echo esc_html( __( "File size limit (bytes)", 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="limit" class="filesize oneline option" id="<?php echo esc_attr( $args['content'] . '-limit' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>"><?php echo esc_html( __( 'Acceptable file types', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="filetypes" class="filetype oneline option" placeholder="jpeg|png|jpg|gif" id="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-max-file' ); ?>"><?php echo esc_html( __( 'Max file upload', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="max-file" class="filetype oneline option" placeholder="10" id="<?php echo esc_attr( $args['content'] . '-max-file' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>
			<br class="clear" />
			<p class="description mail-tag">
				<label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To attach the file uploaded through this field to mail, you need to insert the corresponding mail-tag (%s) into the File Attachments field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label>
			</p>
		</div>

		<?php
	}

	// Begin process upload
	function dnd_upload_cf7_upload() {

		// input type file 'name'
		$name = 'upload-file';

		$file = isset( $_FILES[$name] ) ? $_FILES[$name] : null;

		// Tells whether the file was uploaded via HTTP POST
		if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
			wp_send_json_error( get_option('drag_n_drop_error_failed_to_upload') ? get_option('drag_n_drop_error_failed_to_upload') : dnd_cf7_error_msg('failed_upload') );
		}

		/* File type validation */
		$file_type_pattern = dnd_upload_cf7_filetypes( $_POST['supported_type'] );

		// validate file type
		if ( ! preg_match( $file_type_pattern, $file['name'] ) ) {
			wp_send_json_error( get_option('drag_n_drop_error_invalid_file') ? get_option('drag_n_drop_error_invalid_file') : dnd_cf7_error_msg('invalid_type') );
		}

		// validate file size limit
		if( $file['size'] > (int)$_POST['size_limit'] ) {
			wp_send_json_error( get_option('drag_n_drop_error_files_too_large') ? get_option('drag_n_drop_error_files_too_large') : dnd_cf7_error_msg('large_file') );
		}

		wpcf7_init_uploads(); // Confirm upload dir from Contact Form 7

		// Manage create directory ( Attach image through email or send as links )
		if( get_option('drag_n_drop_mail_attachment') == 'yes' ) {

			$upload = wp_upload_dir();
			$uploads_dir = apply_filters('dnd_cf7_upload_path', $upload['basedir'] . '/wp_dndcf7_uploads', $upload );

			// Check if upload use year and month folders
			if( get_option('uploads_use_yearmonth_folders') ) {
				$uploads_dir = apply_filters('dnd_cf7_upload_path', $upload['basedir'] . '/wp_dndcf7_uploads'. $upload['subdir'], $upload );
			}

			if ( ! is_dir( $uploads_dir ) ) {
				wp_mkdir_p( $uploads_dir );
			}

		}else {
			$uploads_dir = wpcf7_upload_tmp_dir();
			$uploads_dir = wpcf7_maybe_add_random_dir( $uploads_dir );
		}

		// Create file name
		$filename = $file['name'];
		$filename = wpcf7_canonicalize( $filename, 'as-is' );
		$filename = wpcf7_antiscript_file_name( $filename );

		// Add filter on upload file name
		$filename = apply_filters( 'wpcf7_upload_file_name', $filename,	$file['name'] );

		// Generate new filename
		$filename = wp_unique_filename( $uploads_dir, $filename );
		$new_file = path_join( $uploads_dir, $filename );

		// Upload File
		if ( false === move_uploaded_file( $file['tmp_name'], $new_file ) ) {
			wp_send_json_error( get_option('drag_n_drop_error_failed_to_upload') ? get_option('drag_n_drop_error_failed_to_upload') : dnd_cf7_error_msg('failed_upload') );
		}else{

			$files = array(
				'path'	=>	basename($uploads_dir),
				'file'	=>	str_replace('/','-', $filename )
			);

			// Change file permission to 0400
			chmod( $new_file, 0644 );

			wp_send_json_success( $files );
		}

		die;
	}

	// Setup file type pattern for validation
	function dnd_upload_cf7_filetypes( $types ) {
		$file_type_pattern = '';

		// If contact form 7 5.0 and up
		if( function_exists('wpcf7_acceptable_filetypes') ) {
			$file_type_pattern = wpcf7_acceptable_filetypes( $types, 'regex' );
			$file_type_pattern = '/\.(' . $file_type_pattern . ')$/i';
		}else{
			$allowed_file_types = array();
			$file_types = explode( '|', $types );

			foreach ( $file_types as $file_type ) {
				$file_type = trim( $file_type, '.' );
				$file_type = str_replace( array( '.', '+', '*', '?' ), array( '\.', '\+', '\*', '\?' ), $file_type );
				$allowed_file_types[] = $file_type;
			}

			$allowed_file_types = array_unique( $allowed_file_types );
			$file_type_pattern = implode( '|', $allowed_file_types );

			$file_type_pattern = trim( $file_type_pattern, '|' );
			$file_type_pattern = '(' . $file_type_pattern . ')';
			$file_type_pattern = '/\.' . $file_type_pattern . '$/i';
		}

		return $file_type_pattern;
	}

	// Admin Settings
	function dnd_upload_admin_settings( ) {
		echo '<div class="wrap">';
			echo '<h1>Drag & Drop Uploader - Settings</h1>';
				echo '<form method="post" action="options.php"> ';
					settings_fields( 'drag-n-drop-upload-file-cf7' );
					do_settings_sections( 'drag-n-drop-upload-file-cf7' );
		?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Send Attachment as links?','dnd-upload-cf7'); ?></th>
						<td><input name="drag_n_drop_mail_attachment" type="checkbox" value="yes" <?php checked('yes', get_option('drag_n_drop_mail_attachment')); ?>></td>
					</tr>
				</table>

				<h2><?php _e('Uploader Info','dnd-upload-cf7'); ?></h2>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Drag & Drop Text','dnd-upload-cf7'); ?></th>
						<td><input type="text" name="drag_n_drop_text" class="regular-text" value="<?php echo esc_attr( get_option('drag_n_drop_text') ); ?>" placeholder="Drag & Drop Files Here" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"></th>
						<td><input type="text" name="drag_n_drop_separator" value="<?php echo esc_attr( get_option('drag_n_drop_separator') ); ?>" placeholder="or" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Browse Text','dnd-upload-cf7'); ?></th>
						<td><input type="text" name="drag_n_drop_browse_text" class="regular-text" value="<?php echo esc_attr( get_option('drag_n_drop_browse_text') ); ?>" placeholder="Browse Files" /></td>
					</tr>
				</table>

				<h2><?php _e('Error Message','dnd-upload-cf7'); ?></h2>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('File exceeds server limit','dnd-upload-cf7'); ?></th>
						<td><input type="text" name="drag_n_drop_error_server_limit" class="regular-text" value="<?php echo esc_attr( get_option('drag_n_drop_error_server_limit') ); ?>" placeholder="<?php echo dnd_cf7_error_msg('server_limit'); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Failed to Upload','dnd-upload-cf7'); ?></th>
						<td><input type="text" name="drag_n_drop_error_failed_to_upload" class="regular-text" value="<?php echo esc_attr( get_option('drag_n_drop_error_failed_to_upload') ); ?>" placeholder="<?php echo dnd_cf7_error_msg('failed_upload'); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Files too large','dnd-upload-cf7'); ?></th>
						<td><input type="text" name="drag_n_drop_error_files_too_large" class="regular-text" value="<?php echo esc_attr( get_option('drag_n_drop_error_files_too_large') ); ?>" placeholder="<?php echo dnd_cf7_error_msg('large_file'); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Invalid file Type','dnd-upload-cf7'); ?></th>
						<td><input type="text" name="drag_n_drop_error_invalid_file" class="regular-text" value="<?php echo esc_attr( get_option('drag_n_drop_error_invalid_file') ); ?>" placeholder="<?php echo dnd_cf7_error_msg('invalid_type'); ?>" /></td>
					</tr>
				</table>

				<?php submit_button(); ?>

		<?php
			echo '</form>';
		echo '</div>';
	}

	// Save admin settings
	function dnd_upload_register_settings() {
		register_setting( 'drag-n-drop-upload-file-cf7', 'drag_n_drop_mail_attachment' );
		register_setting( 'drag-n-drop-upload-file-cf7', 'drag_n_drop_text' );
		register_setting( 'drag-n-drop-upload-file-cf7', 'drag_n_drop_separator' );
		register_setting( 'drag-n-drop-upload-file-cf7', 'drag_n_drop_browse_text' );
		register_setting( 'drag-n-drop-upload-file-cf7', 'drag_n_drop_error_server_limit' );
		register_setting( 'drag-n-drop-upload-file-cf7', 'drag_n_drop_error_failed_to_upload' );
		register_setting( 'drag-n-drop-upload-file-cf7', 'drag_n_drop_error_files_too_large' );
		register_setting( 'drag-n-drop-upload-file-cf7', 'drag_n_drop_error_invalid_file' );
	}