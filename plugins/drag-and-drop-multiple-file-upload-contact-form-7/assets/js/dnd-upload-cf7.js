jQuery(document).ready(function($){

	var Cf7myUploadFile = $('.wpcf7-drag-n-drop-file'),
		TextOJB = dnd_cf7_uploader.drag_n_drop_upload

	$.each( Cf7myUploadFile, function(){
		var dnd_input_file = $(this);
		$(this).CodeDropz_Uploader({
			'color'				:	'#fff',
			'ajax_url'			: 	dnd_cf7_uploader.ajax_url,
			'max_upload_size'	: 	dnd_input_file.data('limit'),
			'supported_type'	:	dnd_input_file.data('type'),
			'max_file'			:	dnd_input_file.data('max'),
			'text'				: 	TextOJB.text,
			'separator'			: 	TextOJB.or_separator,
			'button_text'		:	TextOJB.browse,
			'server_max_error'	: 	TextOJB.server_max_error,
			'on_success'		:	function( progressBar, response ){

				// Progressbar Object
				var progressDetails = $('#' + progressBar, dnd_input_file.parents('.codedropz-upload-wrapper') );

				// If it's complete remove disabled attribute in button				
				if( $('.in-progress', dnd_input_file.parents('form') ).length === 0 ) {
					setTimeout(function(){ $('input[type="submit"]', dnd_input_file.parents('form')).removeAttr('disabled'); }, 1);	
				}

				// Append hidden input field
				progressDetails
					.find('.dnd-upload-details')
						.append('<span><input type="hidden" name="'+ dnd_input_file.attr('name') +'[]" value="'+ response.data.path +'/'+ response.data.file +'"></span>');
			}
		});
	});

	// Fires when an Ajax form submission has completed successfully, and mail has been sent.
	document.addEventListener( 'wpcf7mailsent', function( event ) {
		// Reset upload list
		if( Cf7myUploadFile.length > 0 ) {
			$.each( Cf7myUploadFile, function(){
				$('.dnd-upload-status', $('span.' + $(this).attr('name'))).remove();
			});
		}else {
			$('.dnd-upload-status', $('span.' + Cf7myUploadFile.attr('name'))).remove();
		}
		// Reset count files
		r=0;
	}, false );

});