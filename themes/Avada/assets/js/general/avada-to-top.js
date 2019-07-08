jQuery( document ).ready( function( $ ) {

	// To top
	if ( jQuery().UItoTop ) {
		if ( cssua.ua.mobile && '1' == avadaToTopVars.status_totop_mobile ) {
			jQuery().UItoTop({ easingType: 'easeOutQuart' });
		} else if ( ! cssua.ua.mobile ) {
			jQuery().UItoTop({ easingType: 'easeOutQuart' });
		}
	}
});
