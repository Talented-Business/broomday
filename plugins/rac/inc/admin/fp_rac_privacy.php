<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

function fp_rac_plugin_get_default_privacy_content() {
    return
            '<p>' . __( 'This includes the basics of what personal data your store may be collecting, storing and sharing. Depending on what settings are enabled and which additional plugins are used, the specific information shared by your store will vary.' , 'recoverabandoncart' ) . '</p>' .
            '<h2>' . __( 'What the Plugin Does' , 'recoverabandoncart' ) . '</h2>' .
            '<p>' . __( "- When a user adds a product to cart and doesn't complete their purchase within a specified time, their cart will be considered as abandoned" , 'recoverabandoncart' ) . '</p>' .
            '<p>' . __( "- An email will be sent to the user who has abandoned the cart. The user can complete the purchase by clicking the purchase link provided in the email" , 'recoverabandoncart' ) . '</p>' .
            '<p>' . __( "- The user can unsubscribe from the abandoned cart emails using the unsubscribe link provided in the emails." , 'recoverabandoncart' ) . '</p>' .
            '<h2>' . __( 'What we collect and store' , 'recoverabandoncart' ) . '</h2>' .
            '<h3>' . __( "- Email ID" , 'recoverabandoncart' ) . '</h3>' .
            '<p>' . __( "For logged in users, their registered email id will be recorded. For guests, email id will be recorded from the email id field on the checkout page. The email id obtained will be used for sending abandoned cart emails." , 'recoverabandoncart' ) . '</p>' .
            '<h3>' . __( "- User First Name and Last Name" , 'recoverabandoncart' ) . '</h3>' .
            '<p>' . __( "For logged in users, their first name and last name will be obtained from their profile. For guests, their first name and last name will be obtained from the first name and last name fields on the checkout page. The name obtained will be used in the abandoned cart emails." , 'recoverabandoncart' ) . '</p>' .
            '<h3>' . __( "- Phone Number" , 'recoverabandoncart' ) . '</h3>' .
            '<p>' . __( "For guests, their phone number will be recorded from the phone number field on the checkout page." , 'recoverabandoncart' ) . '</p>' .
            '<h3>' . __( "- Cookies" , 'recoverabandoncart' ) . '</h3>' .
            '<p>' . __( "We use cookies to" , 'recoverabandoncart' ) . '</p>' .
            '<p>' . __( "- Track the orders which were recovered using the purchase link provided in the abandoned cart emails." , 'recoverabandoncart' ) . '</p>' .
            '<p>' . __( "- Capture the carts abandoned by guests" , 'recoverabandoncart' ) . '</p>' .
            '<h3>' . __( "- IP Address" , 'recoverabandoncart' ) . '</h3>' .
            '<p>' . __( "We record the IP address of guests to differentiate one guest user from an another user" , 'recoverabandoncart' ) . '</p>' ;
}

/**
 * Add the suggested privacy policy text to the policy postbox.
 */
function fp_rac_plugin_add_suggested_privacy_content() {
    if (function_exists('wp_add_privacy_policy_content')) {
        $content = fp_rac_plugin_get_default_privacy_content() ;
        wp_add_privacy_policy_content( __( 'Recover Abandoned Cart' , 'recoverabandoncart' ) , $content ) ;
    }
}

// Not sure why but core registers their default text at priority 15, so to be after them (which I think would be the idea, you need to be 20+.
add_action( 'admin_init' , 'fp_rac_plugin_add_suggested_privacy_content' , 20 ) ;
