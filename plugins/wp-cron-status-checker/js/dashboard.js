
jQuery(document).ready(function($){
    $("#wcsc-force-check").on( 'click', function(e) {
        e.preventDefault();
        var spinner = $(this).prev(".spinner");
        spinner.addClass( "is-active" );
        $.post(wcsc.ajaxurl, {
            action: 'wcsc-force-check',
            nonce: wcsc.nonce
        }, function(){},'json')
        .always(function(data){
            spinner.removeClass( "is-active" );
            if ( data && data.html ) {
                jQuery("#dashboard_wcsc_widget .inside .wcsc-status-container").html( data.html );
            }
            else {
                jQuery("#dashboard_wcsc_widget .inside .wcsc-status").html( 'There was a problem getting the status of WP Cron.' );
            }
        });
    });
});