<?php
add_action('admin_init', 'recover_abandoned_cart_welcome_screen_do_activation_redirect');

function recover_abandoned_cart_welcome_screen_do_activation_redirect() {
    if (!get_transient('_welcome_screen_activation_redirect_recover_abandoned_cart')) {
        return;
    }
    
    delete_transient( '_welcome_screen_activation_redirect_recover_abandoned_cart' );
    
    wp_safe_redirect(add_query_arg( array( 'page' => 'recover-abandoned-cart-welcome-page' ), admin_url( 'admin.php' ) ));
}

add_action('admin_menu', 'recover_abandoned_cart_welcome_screen_pages');

function recover_abandoned_cart_welcome_screen_pages() {
    add_dashboard_page(
            'Welcome To Recover Abandoned Cart', 'Welcome To Recover Abandoned Cart', 'read', 'recover-abandoned-cart-welcome-page', 'recover_abandoned_cart_welcome_screen_content'
    );
}

function recover_abandoned_cart_welcome_screen_content() {

    include 'fp-rac-welcome-page.php';
}

    add_action('admin_head', 'recover_abandoned_cart_welcome_screen_remove_menus');

    function recover_abandoned_cart_welcome_screen_remove_menus() {
        remove_submenu_page('index.php', 'recover-abandoned-cart-welcome-page');
    }
    
