jQuery( window ).load( function() {
	var eislideshowArgs;

	if ( jQuery().eislideshow ) {
		eislideshowArgs = {
			autoplay: Boolean( Number( avadaElasticSliderVars.tfes_autoplay ) )
		};

		if ( avadaElasticSliderVars.tfes_animation ) {
			eislideshowArgs.animation = avadaElasticSliderVars.tfes_animation;
		}
		if ( avadaElasticSliderVars.tfes_interval ) {
			eislideshowArgs.slideshow_interval = avadaElasticSliderVars.tfes_interval;
		}
		if ( avadaElasticSliderVars.tfes_speed ) {
			eislideshowArgs.speed = avadaElasticSliderVars.tfes_speed;
		}
		if ( avadaElasticSliderVars.tfes_width ) {
			eislideshowArgs.thumbMaxWidth = avadaElasticSliderVars.tfes_width;
		}

		jQuery( '#ei-slider' ).eislideshow( eislideshowArgs );
	}
});
