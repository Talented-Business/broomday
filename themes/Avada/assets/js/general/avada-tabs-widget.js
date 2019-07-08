jQuery( document ).ready( function( $ ) {

	// Tabs Widget
	jQuery( '.tabs-widget .tabset li a' ).click( function( e ) {
		e.preventDefault();
	});

	// When page loads
	jQuery( '.tabs-widget' ).each( function() {
		jQuery( this ).find( '.tabset li:first' ).addClass( 'active' ).show(); //Activate first tab
		jQuery( this ).find( '.tab_content:first' ).show(); //Show first tab content
	});

	//On Click Event
	jQuery( '.tabs-widget .tabset li' ).click( function( e ) {
		var tabToActivate = jQuery( this ).find( 'a' ).attr( 'href' );

		jQuery( this ).parent().find( ' > li' ).removeClass( 'active' ); //Remove all 'active' classes
		jQuery( this ).addClass( 'active' ); // Add 'active' class to selected tab

		jQuery( this ).parents( '.tabs-widget' ).find( '.tab_content' ).hide(); //Hide all tab content
		jQuery( this ).parents( '.tabs-widget' ).find( tabToActivate ).fadeIn(); //Fade in the new active tab content
	});
});
