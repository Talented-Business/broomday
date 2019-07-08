<?php
/*
Plugin Name: WP-Cron Status Checker
Plugin URI: https://webheadcoder.com/wp-cron-status-checker
Description: If WP-Cron runs important things for you, you better make sure WP-Cron always runs!
Version: 0.3
Author: Webhead LLC
*/

define( 'WCSC_VERSION', '0.3' );

/**
 * Setup all the admin stuff.
 */
function wcsc_admin_init() {
    add_action('wp_dashboard_setup', 'wcsc_dashboard_widget' );

    /* Register Settings */
    register_setting(
        'general',             // Options group
        'wcsc-email-flag',      // Option name/database
        'wcsc_settings_sanitize' // Sanitize callback function
    );

    /* Create settings field */
    add_settings_field(
        'wcsc-email-flag-id',       // Field ID
        'When WP-Cron fails',       // Field title 
        'wcsc_settings_email_output', // Field callback function
        'general'                    // Settings page slug
    );
}
add_action( 'admin_init', 'wcsc_admin_init' );


if ( defined( 'DOING_CRON' ) && DOING_CRON ) :

/**
 * Setup functions to record the last run
 */
function wcsc_init() {
    wp_schedule_single_event( time() - 1, 'wcsc_event_now' );
}
add_action( 'init', 'wcsc_init' );

/**
 * Set the time for wcsc_last_run.
 */
function wcsc_set_last_run() {
    update_option( '_wcsc_last_run', time() );
}
add_action( 'wcsc_event_now', 'wcsc_set_last_run' );

endif; 

/**
 * Run the check and update the status.
 */
function wcsc_run( $forced = false ) {
    $cached_status = get_transient( 'wcsc-wp-cron-tested' );
    if ( !$forced && $cached_status ) {
        return;
    }
    set_transient( 'wcsc-wp-cron-tested', current_time( 'mysql' ), 86400 );

    $result = wcsc_test_cron_spawn();

    if ( is_wp_error( $result ) ) {
        if ( $result->get_error_code() === 'wcsc_notice' ) {
            update_option( 'wcsc_status', '<span class="wcsc-status wcsc-notice">' . $result->get_error_message() . '</span>' );
        }
        else {
            $msg = sprintf( __( '<p>While trying to spawn a call to the WP-Cron system, the following error occurred: %s</p>', 'wcsc' ), '<br><strong>' . esc_html( $result->get_error_message() ) . '</strong>' );
            $msg .= __( '<p>This is a problem with your installation.  If you need support, please contact your website host or post to the <a href="https://wordpress.org/support/forum/how-to-and-troubleshooting/">main WordPress support forum</a>.</p>', 'wcsc' );

            update_option( 'wcsc_status', '<span class="wcsc-status wcsc-error">' . $msg . '</span>' );
        }
    }
    else {
        $time_string = wcsc_get_datestring();
        $msg = sprintf( __( '<span class="wcsc-label">WP-Cron is working as of:</span><span class="wcsc-value">%s</span>', 'wcsc' ), $time_string );
        update_option( 'wcsc_status', '<span class="wcsc-status wcsc-success">' . $msg . '</span>', false );
    }

    do_action( 'wcsc_run_status', $result, $forced );
}
add_action( 'init', 'wcsc_run' );

/**
 * Gets the status of WP-Cron functionality on the site by performing a test spawn.
 * Code derived from WP-Crontrol.
 *
 */
function wcsc_test_cron_spawn() {
    global $wp_version;

    if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
        return new WP_Error( 'wcsc_notice', sprintf( __( 'The DISABLE_WP_CRON constant is set to true as of %s. WP-Cron is disabled and will not run on it\'s own.', 'wcsc' ), current_time( 'm/d/Y g:i:s a' ) ) );
    }

    if ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) {
        return new WP_Error( 'wcsc_notice', sprintf( __( 'The ALTERNATE_WP_CRON constant is set to true as of %s.  This plugin cannot determine the status of your WP-Cron system.', 'wcsc' ), current_time( 'm/d/Y g:i:s a' ) ) );
    }

    $sslverify     = version_compare( $wp_version, 4.0, '<' );
    $doing_wp_cron = sprintf( '%.22F', microtime( true ) );

    $cron_request = apply_filters( 'cron_request', array(
        'url'  => site_url( 'wp-cron.php?doing_wp_cron=' . $doing_wp_cron ),
        'key'  => $doing_wp_cron,
        'args' => array(
            'timeout'   => 3,
            'blocking'  => true,
            'sslverify' => apply_filters( 'https_local_ssl_verify', $sslverify ),
        ),
    ) );

    $cron_request['args']['blocking'] = true;
    $result = wp_remote_post( $cron_request['url'], $cron_request['args'] );

    if ( is_wp_error( $result ) ) {
        return $result;
    } else if ( wp_remote_retrieve_response_code( $result ) >= 300 ) {
        return new WP_Error( 'unexpected_http_response_code', sprintf(
            __( 'Unexpected HTTP response code: %s', 'wp-crontrol' ),
            intval( wp_remote_retrieve_response_code( $result ) )
        ) );
    }

}


/**
 * Add dashboard widget
 */
function wcsc_dashboard_widget() {
    $title = 'WP-Cron Status Checker';
    if ( current_user_can( 'manage_options' ) ) {
        $title .= '<span class="wcsc-title-email"><a href="' . admin_url( 'options-general.php' ) . '#wcsc-email-flag">' . __( 'Set up Email', 'wcsc' ) . '</a></span>';   
    }
    wp_add_dashboard_widget('dashboard_wcsc_widget', $title, 'wcsc_dashboard_widget_output');
}

/**
 * Show the status and check button.
 */
function wcsc_dashboard_widget_output() { 
    _e( '<p>The WP-Cron system will be automatically checked once every 24 hours.  You can also check the status now by clicking the button below.</p>', 'wcsc' );
    echo '<div class="wcsc-status-container">' . wcsc_dashboard_get_status() . '</div>';
?>
    <span class="spinner"></span> <button id="wcsc-force-check" class="button-primary">Check Status Now</button>
<?php
}

/**
 * Return the dashboard friendly status.
 */
function wcsc_dashboard_get_status() {
    if ( $status = get_option( 'wcsc_status' ) ) {
        $last_run = get_option( '_wcsc_last_run' );
        if ( !empty( $last_run ) ) {
            $time_string = wcsc_get_datestring( $last_run );
            $msg = __( '<span class="wcsc-label">Last time WP Cron ran:</span><span class="wcsc-value">%s</span>' );
            $threshold = 86400; //24 hours
            if ( (time() - $last_run ) <= $threshold ) {
                $status .= '<span class="wcsc-status wcsc-success">' . sprintf( $msg, $time_string ) . '</span>';
            }
            else {
                $status .= '<span class="wcsc-status wcsc-error">' . sprintf( $msg, $time_string ) . '</span>';   
            }
        }
        return $status;
    }
    else {
        return __( 'WP-Cron Status Checker has not run yet.', 'wcsc' );
    }
}

/**
 * Enqueue the scripts
 */
function wcsc_dashboard_widget_enqueue( $hook ) {
    if( 'index.php' != $hook && 'options-general.php' != $hook ) {
        return;
    }

    wp_enqueue_style( 'wcsc-dashboard-widget', 
        plugins_url( '/css/dashboard.css', __FILE__ ),
        array(),
        WCSC_VERSION );

    wp_enqueue_script( 'wcsc-dashboard-widget', 
        plugins_url( '/js/dashboard.js', __FILE__ ), 
        array( 'jquery' ), 
        WCSC_VERSION,
        true );

    wp_localize_script( 'wcsc-dashboard-widget', 'wcsc', array(
        'ajaxurl'       => admin_url( 'admin-ajax.php' ),
        'nonce'         => wp_create_nonce( 'wcsc-nonce' )
    ) );
}
add_action( 'admin_enqueue_scripts', 'wcsc_dashboard_widget_enqueue' );

/**
 * Force check the status.
 */
function wcsc_ajax_check() {
    if ( !check_ajax_referer('wcsc-nonce', 'nonce', false) ){
        die(); 
    }
    wcsc_run( true );
    $html = wcsc_dashboard_get_status();
    wp_send_json( array( 'html' => $html ) );
}
add_action('wp_ajax_wcsc-force-check', 'wcsc_ajax_check');


/**
 * Return true or false only.
 */
function wcsc_settings_sanitize( $input ){
    return isset( $input ) ? true : false;
}

/**
 * Returns the timestamp in the blog's time and format.
 */
function wcsc_get_datestring( $timestamp = '' ) {
    if ( empty( $timestamp ) ) {
        $timestamp = current_time( 'timestamp', true );
    }
    return get_date_from_gmt( 
        date( 'Y-m-d H:i:s', $timestamp ), 
        get_option( 'date_format' ) . ' ' . get_option( 'time_format' )
    );
}
 
/**
 * Output the email settings
 */
function wcsc_settings_email_output(){
    ?>
    <label for="wcsc-email-flag">
        <a name="wcsc-email-flag"></a>
        <input id="wcsc-email-flag" type="checkbox" value="1" name="wcsc-email-flag" <?php checked( get_option( 'wcsc-email-flag', false ) ); ?>> Email the administrator (<?php echo get_option( 'admin_email' ); ?>). The email will <strong>NOT</strong> be sent if
        <ul class="wcsc-email-flag-list">
            <li>Either the DISABLE_WP_CRON or ALTERNATE_WP_CRON constants are set.</li>
            <li>The status is checked by pressing the "Check Status Now" button.</li>
        </ul>
    </label>
    <?php
}

/**
 * Email the admin if the result is bad
 */
function wcsc_email_admin( $result, $forced ) {
    if ( !$forced && is_wp_error( $result ) && $result->get_error_code() != 'wcsc_notice' ) {
        if ( get_option( 'wcsc-email-flag') ) {
            $msg = get_option( 'wcsc_status' );
            $msg .= sprintf( __( '<p>This message has been sent from %s by the WP-Cron Status Checker plugin.  You can change the email settings in the WordPress Settings -> General page.</p>', 'wcsc' ), site_url() );
            $headers = array(' Content-Type: text/html; charset=UTF-8' );
            wp_mail( get_option( 'admin_email' ), 
                __( 'WP-Cron Failed!', 'wcsc' ),
                $msg,
                $headers );
        }
    }
}
add_action( 'wcsc_run_status', 'wcsc_email_admin', 10, 2 );