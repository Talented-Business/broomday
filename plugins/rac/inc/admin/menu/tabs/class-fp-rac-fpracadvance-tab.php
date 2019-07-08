<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'FP_RAC_Email_Tab' ) ) {

    /**
     * FP_RAC_Email_Tab Class.
     */
    class FP_RAC_Email_Tab {

        public static function init() {
            add_action( 'fp_rac_display_buttons_fpracadvance' , array ( __CLASS__ , 'fp_rac_admin_setting_buttons' ) ) ;
            add_action( 'fp_rac_default_settings_fpracadvance' , array ( __CLASS__ , 'fprac_email_default_settings' ) ) ;
            add_action( 'woocommerce_update_options_fpracadvance' , array ( __CLASS__ , 'fp_rac_update_options_email' ) ) ;
            add_action( 'woocommerce_fprac_settings_tabs_fpracadvance' , array ( __CLASS__ , 'fp_rac_admin_setting_email' ) ) ;
            add_action( 'woocommerce_admin_field_fp_rac_cartlink_logo' , array ( __CLASS__ , 'fp_rac_cartlink_logo_action' ) ) ;
            add_action( 'woocommerce_admin_field_rac_drag_drop_product_info' , array ( __CLASS__ , 'fp_rac_drag_drop_product_info_column_alignment' ) ) ;
        }

        public static function fp_rac_menu_options_email() {

            return apply_filters( 'woocommerce_fpracadvance_settings' , array (
                array (
                    'type' => 'rac_drag_drop_product_info' ,
                ) ,
                array (
                    'name' => __( 'Product Info Table Settings' , 'recoverabandoncart' ) ,
                    'type' => 'title' ,
                    'desc' => 'Following Customization options works with the shortcode {rac.Productinfo} in Email Template' ,
                    'id'   => 'rac_customize_caption_in_product_info' ,
                ) ,
                array (
                    'name'     => __( 'Border for Table' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_enable_border_for_productinfo_in_email' ,
                    'clone_id' => 'rac_enable_border_for_productinfo_in_email' ,
                    'type'     => 'checkbox' ,
                    'default'  => 'yes' ,
                    'std'      => 'yes' ,
                ) ,
                array (
                    'name'     => __( 'Display Variations for Variable Product' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'If "Show" is selected, variation name will be displayed if the product is a variable product)' , 'recoverabandoncart' ) ,
                    'type'     => 'select' ,
                    'default'  => 'yes' ,
                    'options'  => array ( 'yes' => __( 'Show' , 'recoverabandoncart' ) , 'no' => __( 'Hide' , 'recoverabandoncart' ) ) ,
                    'std'      => 'yes' ,
                    'id'       => 'rac_email_product_variation_sh' ,
                    'clone_id' => 'rac_email_product_variation_sh' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( 'SKU' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_troubleshoot_sku_sh' ,
                    'type'     => 'select' ,
                    'desc'     => __( 'If enabled, SKU will be displayed next to product name in abandoned cart emails and cart list' , 'recoverabandoncart' ) ,
                    'desc_tip' => true ,
                    'options'  => array ( 'yes' => __( 'Show' , 'recoverabandoncart' ) , 'no' => __( 'Hide' , 'recoverabandoncart' ) ) ,
                    'std'      => 'yes' ,
                    'default'  => 'yes' ,
                    'clone_id' => 'rac_troubleshoot_sku_sh' ,
                ) ,
                array (
                    'name'     => __( 'Product Name Column' , 'recoverabandoncart' ) ,
                    'type'     => 'select' ,
                    'options'  => array ( 'no' => 'Show' , 'yes' => 'Hide' ) ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_hide_product_name_product_info_shortcode' ,
                    'clone_id' => 'rac_hide_product_name_product_info_shortcode' ,
                ) ,
                array (
                    'name'     => __( 'Product Name Label' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'Product Name' ,
                    'std'      => 'Product Name' ,
                    'id'       => 'rac_product_info_product_name' ,
                    'clone_id' => 'rac_product_info_product_name' ,
                ) ,
                array (
                    'name'     => __( 'Product Image Column' , 'recoverabandoncart' ) ,
                    'type'     => 'select' ,
                    'options'  => array ( 'no' => 'Show' , 'yes' => 'Hide' ) ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_hide_product_image_product_info_shortcode' ,
                    'clone_id' => 'rac_hide_product_image_product_info_shortcode' ,
                ) ,
                array (
                    'name'     => __( 'Product Image Label' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'Product Image' ,
                    'std'      => 'Product Image' ,
                    'id'       => 'rac_product_info_product_image' ,
                    'clone_id' => 'rac_product_info_product_image' ,
                ) ,
                array (
                    'name'     => __( 'Product Quantity Column' , 'recoverabandoncart' ) ,
                    'type'     => 'select' ,
                    'options'  => array ( 'no' => 'Show' , 'yes' => 'Hide' ) ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_hide_product_quantity_product_info_shortcode' ,
                    'clone_id' => 'rac_hide_product_quantity_product_info_shortcode' ,
                ) ,
                array (
                    'name'     => __( 'Product Quantity Label' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'Quantity' ,
                    'std'      => 'Quantity' ,
                    'id'       => 'rac_product_info_quantity' ,
                    'clone_id' => 'rac_product_info_quantity' ,
                ) ,
                array (
                    'name'     => __( 'Product Price Column' , 'recoverabandoncart' ) ,
                    'type'     => 'select' ,
                    'options'  => array ( 'no' => 'Show' , 'yes' => 'Hide' ) ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_hide_product_price_product_info_shortcode' ,
                    'clone_id' => 'rac_hide_product_price_product_info_shortcode' ,
                ) ,
                array (
                    'name'     => __( 'Product Price Label' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'Product Price' ,
                    'std'      => 'Product Price' ,
                    'id'       => 'rac_product_info_product_price' ,
                    'clone_id' => 'rac_product_info_product_price' ,
                ) ,
                array (
                    'name'     => __( 'Subtotal, Shipping, Tax, Total Rows' , 'recoverabandoncart' ) ,
                    'type'     => 'select' ,
                    'options'  => array ( 'no' => 'Show' , 'yes' => 'Hide' ) ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_hide_tax_total_product_info_shortcode' ,
                    'clone_id' => 'rac_hide_tax_total_product_info_shortcode' ,
                ) ,
                array (
                    'name'     => __( 'Subtotal Label' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'Subtotal' ,
                    'std'      => 'Subtotal' ,
                    'id'       => 'rac_product_info_subtotal' ,
                    'clone_id' => 'rac_product_info_subtotal' ,
                    'class'    => 'rac_hide_total_info'
                ) ,
                array (
                    'name'     => __( 'Total Label' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'Total' ,
                    'std'      => 'Total' ,
                    'id'       => 'rac_product_info_total' ,
                    'clone_id' => 'rac_product_info_total' ,
                    'class'    => 'rac_hide_total_info'
                ) ,
                array (
                    'name'     => __( 'Shipping Row' , 'recoverabandoncart' ) ,
                    'type'     => 'select' ,
                    'options'  => array ( 'no' => 'Show' , 'yes' => 'Hide' ) ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_hide_shipping_row_product_info_shortcode' ,
                    'clone_id' => 'rac_hide_shipping_row_product_info_shortcode' ,
                    'class'    => 'rac_hide_total_info'
                ) ,
                array (
                    'name'     => __( 'Shipping Label' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'Shipping' ,
                    'std'      => 'Shipping' ,
                    'id'       => 'rac_product_info_shipping' ,
                    'clone_id' => 'rac_product_info_shipping' ,
                    'class'    => 'rac_hide_total_info'
                ) ,
                array (
                    'name'     => __( 'Tax Row' , 'recoverabandoncart' ) ,
                    'type'     => 'select' ,
                    'options'  => array ( 'no' => 'Show' , 'yes' => 'Hide' ) ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_hide_tax_row_product_info_shortcode' ,
                    'clone_id' => 'rac_hide_tax_row_product_info_shortcode' ,
                    'class'    => 'rac_hide_total_info'
                ) ,
                array (
                    'name'     => __( 'Tax Label' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'Tax' ,
                    'std'      => 'Tax' ,
                    'id'       => 'rac_product_info_tax' ,
                    'clone_id' => 'rac_product_info_tax' ,
                    'class'    => 'rac_hide_total_info'
                ) ,
                array (
                    'name'     => __( 'Display Product Price Including Tax in Emails' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'If enabled, product price will be displayed including tax and if disabled, product price will be displayed excluding tax' , 'recoverabandoncart' ) ,
                    'type'     => 'checkbox' ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_inc_tax_with_product_price_product_info_shortcode' ,
                    'clone_id' => 'rac_inc_tax_with_product_price_product_info_shortcode' ,
                ) ,
                array ( 'type' => 'sectionend' , 'id' => 'rac_email_gen_settings' ) ,
                array (
                    'name' => __( 'Email Template Cart Link Settings' , 'recoverabandoncart' ) ,
                    'type' => 'title' ,
                    'id'   => 'rac_cart_link_customization' ,
                ) ,
                array (
                    'name'     => __( 'Cart Link Type' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Customize the cart link in email template' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_cart_link_options' ,
                    'type'     => 'select' ,
                    'desc_tip' => true ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'options'  => array (
                        '1' => __( 'Hyperlink' , 'recoverabandoncart' ) ,
                        '2' => __( 'URL' , 'recoverabandoncart' ) ,
                        '3' => __( 'Button' , 'recoverabandoncart' ) ,
                        '4' => __( 'Image' , 'recoverabandoncart' )
                    ) ,
                    'clone_id' => 'rac_cart_link_options' ,
                ) ,
                array (
                    'name'     => __( 'Button Background Color' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_cart_button_bg_color' ,
                    'class'    => 'color racbutton' ,
                    'type'     => 'text' ,
                    'desc_tip' => true ,
                    'std'      => '000091' ,
                    'default'  => '000091' ,
                    'clone_id' => 'rac_cart_button_bg_color' ,
                ) ,
                array (
                    'name'     => __( 'Button Text Color' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_cart_button_link_color' ,
                    'class'    => 'color racbutton' ,
                    'type'     => 'text' ,
                    'desc_tip' => true ,
                    'std'      => 'ffffff' ,
                    'default'  => 'ffffff' ,
                    'clone_id' => 'rac_cart_button_link_color' ,
                ) ,
                array (
                    'name'     => __( 'Cart Link Color' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_email_link_color' ,
                    'class'    => 'color raclink' ,
                    'type'     => 'text' ,
                    'desc_tip' => true ,
                    'std'      => '1919FF' ,
                    'default'  => '1919FF' ,
                    'clone_id' => 'rac_email_link_color' ,
                ) ,
                array (
                    'type' => 'fp_rac_cartlink_logo'
                ) ,
                array (
                    'name'     => __( 'Image Height' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_cart_link_image_height' ,
                    'desc'     => 'px' ,
                    'class'    => 'fp_rac_class_cartlink_image' ,
                    'type'     => 'number' ,
                    'step'     => 'any' ,
                    'std'      => '15' ,
                    'default'  => '15' ,
                    'clone_id' => 'rac_cart_link_image_height' ,
                ) ,
                array (
                    'name'     => __( 'Image Width' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_cart_link_image_width' ,
                    'desc'     => 'px' ,
                    'class'    => 'fp_rac_class_cartlink_image' ,
                    'type'     => 'number' ,
                    'step'     => 'any' ,
                    'std'      => '100' ,
                    'default'  => '100' ,
                    'clone_id' => 'rac_cart_link_image_width' ,
                ) ,
                array (
                    'name'     => __( 'Page to be Redirected on Clicking the Cart Link in Abandoned Cart Emails' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Please Select the page that you want to redirect after clicking the Cart Link in email' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_cartlink_redirect' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array ( '1' => __( 'Cart page' , 'recoverabandoncart' ) , '2' => __( 'Checkout page' , 'recoverabandoncart' ) ) ,
                    'newids'   => 'rac_cartlink_redirect' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( 'Clear the Cart Content when Cart Link is Clicked' , 'recoverabandoncart' ) ,
                    'type'     => 'checkbox' ,
                    'default'  => 'yes' ,
                    'std'      => 'yes' ,
                    'id'       => 'rac_cart_content_when_cart_link_is_clicked' ,
                    'clone_id' => 'rac_cart_content_when_cart_link_is_clicked' ,
                ) ,
                array ( 'type' => 'sectionend' , 'id' => 'rac_date_time_format_customization' ) ,
                array (
                    'name' => __( 'Shortcode Customization in Email Template' , 'recoverabandoncart' ) ,
                    'type' => 'title' ,
                    'id'   => 'rac_date_time_format_customization' ,
                ) ,
                array (
                    'name'     => __( 'Date Format' , 'recoverabandoncart' ) ,
                    'desc'     => 'Customize date format for {rac.date}' ,
                    'id'       => 'rac_date_format' ,
                    'type'     => 'text' ,
                    'desc_tip' => true ,
                    'std'      => 'd:m:y' ,
                    'default'  => 'd:m:y' ,
                    'clone_id' => 'rac_date_format' ,
                ) ,
                array (
                    'name'     => __( 'Time Format' , 'recoverabandoncart' ) ,
                    'desc'     => 'Customize time format for {rac.time}' ,
                    'id'       => 'rac_time_format' ,
                    'type'     => 'text' ,
                    'desc_tip' => true ,
                    'std'      => 'h:i:s' ,
                    'default'  => 'h:i:s' ,
                    'clone_id' => 'rac_time_format' ,
                ) ,
                array (
                    'name'     => __( 'Shortcode in Email Subject Label' , 'recoverabandoncart' ) ,
                    'desc_tip' => __( 'If the cart list contains more than one product, the label entered here will be displayed along with the name of the first product when using the shortcode {rac.productname} in abandoned cart email subject' , 'recoverabandoncart' ) ,
                    'type'     => 'text' ,
                    'default'  => 'and more' ,
                    'std'      => 'and more' ,
                    'id'       => 'rac_subject_product_shrotcode_customize' ,
                    'clone_id' => 'rac_subject_product_shrotcode_customize' ,
                ) ,
                array ( 'type' => 'sectionend' , 'id' => 'rac_cart_link_customization' ) ,
                array (
                    'name'     => __( 'Unsubscription Settings' , 'recoverabandoncart' ) ,
                    'type'     => 'title' ,
                    'id'       => 'rac_email_unsubscription' ,
                    'clone_id' => '' ,
                ) ,
                array (
                    'name'     => __( 'Display Unsubscription option on My Account Page' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'If enabled, unsubscribe option will be displayed in "My Account Page"' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsub_myaccount_option' ,
                    'std'      => 'no' ,
                    'default'  => 'no' ,
                    'type'     => 'checkbox' ,
                    'clone_id' => 'rac_unsub_myaccount_option' ,
                    'newids'   => 'rac_unsub_myaccount_option' ,
                ) ,
                array (
                    'name'     => __( 'Customize Unsubscription Heading' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Customize the heading appeared in My Account Page' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsub_myaccount_heading' ,
                    'std'      => 'Unsubscription Settings' ,
                    'default'  => 'Unsubscription Settings' ,
                    'type'     => 'text' ,
                    'clone_id' => 'rac_unsub_myaccount_heading' ,
                    'newids'   => 'rac_unsub_myaccount_heading' ,
                    'class'    => 'rac_unsubscribe_hide'
                ) ,
                array (
                    'name'     => __( 'Customize Unsubscription Text' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Customize the Message appeared in My Account Page' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsub_myaccount_text' ,
                    'std'      => 'Unsubscribe Here to stop Receiving Emails from Recovered Abandoned Cart' ,
                    'default'  => 'Unsubscribe Here to stop Receiving Emails from Recovered Abandoned Cart' ,
                    'type'     => 'textarea' ,
                    'clone_id' => 'rac_unsub_myaccount_text' ,
                    'newids'   => 'rac_unsub_myaccount_text' ,
                    'class'    => 'rac_unsubscribe_hide' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( 'Link in Email Footer' , 'recoverabandoncart' ) ,
                    'desc_tip' => '</ br> <b> ' . __( 'Note: ' , 'recoverabandoncart' ) . '</b>' . __( 'If unsubscription link is not visible in footer of email, then consider using the shortcode <b>{rac.unsubscribe}</b> in text editor for each email template' , 'recoverabandoncart' ) ,
                    'id'       => 'fp_unsubscription_link_in_email' ,
                    'clone_id' => 'fp_unsubscription_link_in_email' ,
                    'type'     => 'checkbox' ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'desc'     => __( 'If enabled, unsubscription link will be appended to the email footer' , 'recoverabandoncart' ) ,
                ) ,
                array (
                    'name'     => __( 'Message' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enter Unsubscription Message which is visible in Email Footer' , 'recoverabandoncart' ) ,
                    'id'       => 'fp_unsubscription_footer_message' ,
                    'clone_id' => 'fp_unsubscription_footer_message' ,
                    'type'     => 'textarea' ,
                    'css'      => 'height: 60px; width: 320px' ,
                    'default'  => 'You can {rac_unsubscribe} to stop Receiving Abandon Cart Mail from {rac_site}' ,
                    'std'      => 'You can {rac_unsubscribe} to stop Receiving Abandon Cart Mail from {rac_site}' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( "Unsubscription Link Text in Email Template will" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Choose how the Unsubscription Link from Recovered Abandon Cart will be displayed in emails ' , 'recoverabandoncart' ) ,
                    'id'       => 'fp_unsubscription_footer_link_text_option' ,
                    'clone_id' => 'fp_unsubscription_footer_link_text_option' ,
                    'type'     => 'select' ,
                    'options'  => array (
                        '1' => __( "Replace WooCommerce footer text" , "recoverabandoncart" ) ,
                        '2' => __( "Append to WooCommerce footer text" , "recoverabandoncart" ) ,
                    ) ,
                    'default'  => '1' ,
                    'std'      => '1' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( 'Link Anchor Text' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enter the text to be replaced for {rac_unsubscribe} shortcode' , 'recoverabandoncart' ) ,
                    'id'       => 'fp_unsubscription_footer_link_text' ,
                    'clone_id' => 'fp_unsubscription_footer_link_text' ,
                    'type'     => 'text' ,
                    'default'  => 'Unsubscribe' ,
                    'std'      => 'Unsubscribe' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( 'Unsubscribe Anchor Color' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsubscribe_link_color' ,
                    'class'    => 'color' ,
                    'type'     => 'text' ,
                    'desc_tip' => true ,
                    'std'      => '1919FF' ,
                    'default'  => '1919FF' ,
                    'clone_id' => 'rac_unsubscribe_link_color' ,
                ) ,
                array (
                    'name'     => __( 'Type' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Please Select the Unsubscription Type' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsubscription_type' ,
                    'class'    => 'rac_unsubscription_type' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'options'  => array ( '1' => 'Automatic Unsubscription' , '2' => 'Manual Unsubscription' ) ,
                    'newids'   => 'rac_unsubscription_type' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( "Redirect URL for Automatic Unsubscription" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enter Redirect Url to redirect when click the Automatic unsubscription link' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsubscription_redirect_url' ,
                    'clone_id' => 'rac_unsubscription_redirect_url' ,
                    'type'     => 'text' ,
                    'default'  => get_permalink( wc_get_page_id( 'myaccount' ) ) ,
                    'std'      => get_permalink( wc_get_page_id( 'myaccount' ) ) ,
                    'desc_tip' => true ,
                    'class'    => 'rac_unsub_auto'
                ) ,
                array (
                    'name'     => __( "Redirect URL for Manual Unsubscription" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enter Redirect Url to redirect when click the Manual unsubscription link' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_manual_unsubscription_redirect_url' ,
                    'clone_id' => 'rac_manual_unsubscription_redirect_url' ,
                    'type'     => 'text' ,
                    'default'  => get_permalink( wc_get_page_id( 'myaccount' ) ) ,
                    'std'      => get_permalink( wc_get_page_id( 'myaccount' ) ) ,
                    'desc_tip' => true ,
                    'class'    => 'rac_unsub_manual'
                ) ,
                array (
                    'name'     => __( "Already Unsubscribed Text" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enter Already Unsubscribed Text' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_already_unsubscribed_text' ,
                    'clone_id' => 'rac_already_unsubscribed_text' ,
                    'type'     => 'text' ,
                    'default'  => 'You have already unsubscribed.' ,
                    'std'      => 'You have already unsubscribed.' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( "Unsubscribed Successfully Text" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enter Unsubscribed Successfully Text' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsubscribed_successfully_text' ,
                    'clone_id' => 'rac_unsubscribed_successfully_text' ,
                    'type'     => 'text' ,
                    'default'  => 'You have successfully unsubscribed from Abandoned cart Emails.' ,
                    'std'      => 'You have successfully unsubscribed from Abandoned cart Emails.' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( "Confirm Unsubscription Text" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enter Confirm Unsubscription Text' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_confirm_unsubscription_text' ,
                    'clone_id' => 'rac_confirm_unsubscription_text' ,
                    'type'     => 'text' ,
                    'default'  => 'To stop receiving Abandoned Cart Emails, Click the Unsubscribe button below' ,
                    'std'      => 'To stop receiving Abandoned Cart Emails, Click the Unsubscribe button below' ,
                    'desc_tip' => true ,
                    'class'    => 'rac_unsub_manual'
                ) ,
                array (
                    'name'     => __( "Message Text Color" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Choose Unsubscription Message Text color' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsubscription_message_text_color' ,
                    'clone_id' => 'rac_unsubscription_message_text_color' ,
                    'type'     => 'text' ,
                    'default'  => 'fff' ,
                    'std'      => 'fff' ,
                    'class'    => 'color rac_unsub_auto' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( "Message Background color" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Choose Background color for Unsubscription Message' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsubscription_message_background_color' ,
                    'clone_id' => 'rac_unsubscription_message_background_color' ,
                    'type'     => 'text' ,
                    'default'  => 'a46497' ,
                    'std'      => 'a46497' ,
                    'class'    => 'color rac_unsub_auto' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( "Unsubscription Email Text color" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Choose Unsubscription Email Text color' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_unsubscription_email_text_color' ,
                    'clone_id' => 'rac_unsubscription_email_text_color' ,
                    'type'     => 'text' ,
                    'default'  => '000000' ,
                    'std'      => '000000' ,
                    'class'    => 'color rac_unsub_manual' ,
                    'desc_tip' => true ,
                ) ,
                array (
                    'name'     => __( "Confirm Unsubscription Text color" , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Choose Confirm Unsubscription Text color' , 'recoverabandoncart' ) ,
                    'id'       => 'rac_confirm_unsubscription_text_color' ,
                    'clone_id' => 'rac_confirm_unsubscription_text_color' ,
                    'type'     => 'text' ,
                    'default'  => 'ff3f12' ,
                    'std'      => 'ff3f12' ,
                    'class'    => 'color rac_unsub_manual' ,
                    'desc_tip' => true ,
                ) ,
                array ( 'type' => 'sectionend' , 'id' => 'rac_email_unsubscription' ) ,
                array (
                    'name'     => __( 'Add to cart popup settings for Guest' , 'recoverabandoncart' ) ,
                    'type'     => 'title' ,
                    'id'       => 'rac_guest_popup_settings' ,
                    'clone_id' => '' ,
                ) ,
                array (
                    'name'     => __( 'Enable Add to cart popup' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enabling this option will display popup to get email address when click Add to cart button' , 'recoverabandoncart' ) ,
                    'type'     => 'checkbox' ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_enable_guest_add_to_cart_popup' ,
                    'clone_id' => 'rac_enable_guest_add_to_cart_popup' ,
                ) ,
                array (
                    'name'     => __( 'Is Email Address Mandatory for Add to Cart' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enabling this option will force guest to enter email address' , 'recoverabandoncart' ) ,
                    'type'     => 'checkbox' ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_force_guest_to_enter_email_address' ,
                    'clone_id' => 'rac_force_guest_to_enter_email_address' ,
                    'class'    => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array(
                    'name'    => __( 'To Show or Hide First name and last name in Guest add to cart Pop Up' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_show_hide_name_in_popup' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rac_show_hide_name_in_popup' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Hide' , 'recoverabandoncart' ) ,
                        '2' => __( 'Show' , 'recoverabandoncart' ) ,
                    ) ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup' 
                ) ,
                array (
                    'name'     => __( 'Is First/Last Name Mandatory for Add to Cart' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enabling this option will Force Guest Users to Enter their First Name and Last Name' , 'recoverabandoncart' ) ,
                    'type'     => 'checkbox' ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_force_guest_to_enter_first_last_name' ,
                    'clone_id' => 'rac_force_guest_to_enter_first_last_name' ,
                    'class'    => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array(
                    'name'    => __( 'To Show or Hide Contact number in Guest add to cart Pop Up' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_show_hide_contactno_in_popup' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rac_show_hide_contactno_in_popup' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Hide' , 'recoverabandoncart' ) ,
                        '2' => __( 'Show' , 'recoverabandoncart' ) ,
                    ) ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup' 
                ) ,
                array (
                    'name'     => __( 'Is Phone Number Mandatory for Add to Cart' , 'recoverabandoncart' ) ,
                    'desc'     => __( 'Enabling this option will Force Guest Users to Enter their Contact Number' , 'recoverabandoncart' ) ,
                    'type'     => 'checkbox' ,
                    'default'  => 'no' ,
                    'std'      => 'no' ,
                    'id'       => 'rac_force_guest_to_enter_phoneno' ,
                    'clone_id' => 'rac_force_guest_to_enter_phoneno' ,
                    'class'    => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Popup Heading' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_add_to_cart_popup_heading' ,
                    'std'     => __( 'Please enter your Details' , 'recoverabandoncart' ) ,
                    'default' => __( 'Please enter your Details' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_add_to_cart_popup_heading' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'First Name Label' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_add_to_cart_popup_fname' ,
                    'std'     => __( 'Enter your First Name' , 'recoverabandoncart' ) ,
                    'default' => __( 'Enter your First Name' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_add_to_cart_popup_fname' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Last Name Label' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_add_to_cart_popup_lname' ,
                    'std'     => __( 'Enter your Last Name' , 'recoverabandoncart' ) ,
                    'default' => __( 'Enter your Last Name' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_add_to_cart_popup_lname' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Phone Number Label' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_add_to_cart_popup_phoneno' ,
                    'std'     => __( 'Enter Your Contact Number' , 'recoverabandoncart' ) ,
                    'default' => __( 'Enter Your Contact Number' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_add_to_cart_popup_phoneno' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Email Address Label' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_add_to_cart_popup_email' ,
                    'std'     => __( 'Enter your Email Address' , 'recoverabandoncart' ) ,
                    'default' => __( 'Enter your Email Address' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_add_to_cart_popup_email' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Add to cart button Text' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_popup_add_to_cart_text' ,
                    'std'     => __( 'Add to cart' , 'recoverabandoncart' ) ,
                    'default' => __( 'Add to cart' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_popup_add_to_cart_text' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Cancel button Text' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_popup_cancel_text' ,
                    'std'     => __( 'Cancel' , 'recoverabandoncart' ) ,
                    'default' => __( 'Cancel' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_popup_cancel_text' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array ( 'type' => 'sectionend' , 'id' => 'rac_guest_popup_settings' ),
                array (
                    'name' => __( 'Error Message Localization' , 'recoverabandoncart' ) ,
                    'type' => 'title' ,
                    'id'   => 'rac_error_message_localization' ,
                ) ,
                array (
                    'name'    => __( 'Error Message for Empty First Name field' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_popup_err_msg_for_empty_fname' ,
                    'std'     => __( 'Please Enter your First Name' , 'recoverabandoncart' ) ,
                    'default' => __( 'Please Enter your First Name' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_popup_err_msg_for_empty_fname' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Error Message for Empty Last Name field' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_popup_err_msg_for_empty_lname' ,
                    'std'     => __( 'Please Enter your Last Name' , 'recoverabandoncart' ) ,
                    'default' => __( 'Please Enter your Last Name' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_popup_err_msg_for_empty_lname' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Error Message for Empty Contact Number field' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_popup_err_msg_for_empty_phoneno' ,
                    'std'     => __( 'Please Enter your Contact Number' , 'recoverabandoncart' ) ,
                    'default' => __( 'Please Enter your Contact Number' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_popup_err_msg_for_empty_phoneno' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Error Message for Invalid Contact Number' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_popup_err_msg_for_empty_invalid_phoneno' ,
                    'std'     => __( 'Please Enter valid Contact Number' , 'recoverabandoncart' ) ,
                    'default' => __( 'Please Enter valid Contact Number' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_popup_err_msg_for_empty_invalid_phoneno' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Error Message for Empty Email field' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_popup_err_msg_for_empty' ,
                    'std'     => __( 'Please Enter your Email Address' , 'recoverabandoncart' ) ,
                    'default' => __( 'Please Enter your Email Address' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_popup_err_msg_for_empty' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array (
                    'name'    => __( 'Error Message for Invalid Email Address' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_guest_popup_err_msg_for_invalid_email' ,
                    'std'     => __( 'Please Enter your Valid Email Address' , 'recoverabandoncart' ) ,
                    'default' => __( 'Please Enter your Valid Email Address' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_guest_popup_err_msg_for_invalid_email' ,
                    'class'   => 'rac_show_hide_settings_for_guest_popup'
                ) ,
                array ( 'type' => 'sectionend' , 'id' => 'rac_error_message_localization' ) ,
                array (
                    'name' => __( 'Personal Data Export Settings for GDPR Compliance' , 'recoverabandoncart' ) ,
                    'type' => 'title' ,
                    'id'   => 'rac_export_personal_data_settings' ,
                ) ,
                array (
                    'name'    => __( 'Personal Data Export Label' , 'recoverabandoncart' ) ,
                    'id'      => 'rac_personal_data_export_label' ,
                    'std'     => __( 'Cart Captured for Recovery' , 'recoverabandoncart' ) ,
                    'default' => __( 'Cart Captured for Recovery' , 'recoverabandoncart' ) ,
                    'type'    => 'text' ,
                    'newids'  => 'rac_personal_data_export_label' ,
                ) ,
                array ( 'type' => 'sectionend' , 'id' => 'rac_export_personal_data_settings' ) ,
                    ) ) ;
        }

        public static function fp_rac_admin_setting_email() {
            woocommerce_admin_fields( FP_RAC_Email_Tab::fp_rac_menu_options_email() ) ;
        }

        public static function fp_rac_update_options_email() {
            woocommerce_update_options( FP_RAC_Email_Tab::fp_rac_menu_options_email() ) ;
            $value = isset( $_POST[ 'custom_user_name_select' ] ) ? $_POST[ 'custom_user_name_select' ] : '' ;
            update_option( 'custom_user_name_select' , $value ) ;

            $logo_url = $_POST[ 'fp_rac_email_cartlink_logo_text' ] ;
            update_option( 'fp_rac_email_cartlink_logo_text' , $logo_url ) ;
        }

        public static function fprac_email_default_settings() {
            $settings = FP_RAC_Email_Tab::fp_rac_menu_options_email() ;
            if ( rac_check_is_array( $settings ) ) {
                foreach ( $settings as $setting )
                    if ( isset( $setting[ 'id' ] ) && isset( $setting[ 'std' ] ) ) {
                        if ( ! get_option( $setting[ 'id' ] ) )
                            add_option( $setting[ 'id' ] , $setting[ 'std' ] ) ;
                    }
            }
        }

        public static function fp_rac_admin_setting_buttons() {
            ?>
            <span class = "submit" style = "margin-left: 25px;">
                <?php if ( ! isset( $GLOBALS[ 'hide_save_button' ] ) ) :
                    ?>
                    <input name="save" class="button-primary" style="margin-top:15px;" type="submit" value="<?php _e( 'Save' , 'recoverabandoncart' ) ; ?>" />
                <?php endif ; ?>
                <input type="hidden" name="subtab" id="last_tab" />
                <?php wp_nonce_field( 'woocommerce-settings' ) ; ?>
            </span>
            <?php
        }

        public static function fp_rac_drag_drop_product_info_column_alignment() {

            include_once RAC_PLUGIN_PATH . '/templates/email-template-css.php' ;
            ?>
            <h3>
                <label><?php _e( 'Product Info Table Column Positioning' , 'recoverabandoncart' ) ?></label>
            </h3>
            <table class="form-table" id="rac_drag_n_drop_product_info">
                <?php
                $sortable_column = array ( 'product_name' => __( 'Product Name' , 'recoverabandoncart' ) , 'product_image' => __( 'Product Image' , 'recoverabandoncart' ) , 'product_quantity' => __( 'Quantity' , 'recoverabandoncart' ) , 'product_price' => __( 'Total' , 'recoverabandoncart' ) ) ;
                $priority_array  = get_option( 'drag_and_drop_product_info_sortable_column' , true ) ;
                $priority_array  = is_array( $priority_array ) && ! empty( $priority_array ) ? $priority_array : array_keys( $sortable_column ) ;
                if ( rac_check_is_array( $priority_array ) ) {
                    foreach ( $priority_array as $key ) {
                        ?><tbody id="<?php echo $key ; ?>">
                            <tr class="rac_product_info_drag_n_drop" id="<?php echo $key ; ?>">
                                <td style="width:400px;"><?php echo $sortable_column[ $key ] ; ?></td>
                            <tr>
                        </tbody>
                        <?php
                    }
                }
                ?>
            </table>

            <?php
        }

        public static function fp_rac_cartlink_logo_action() {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="fp_rac_email_cartlink_logo" id="fp_rac_email_cartlink_logo"><?php _e( 'Cart Link Image' , 'recoverabandoncart' ) ; ?></label>
                </th>
                <td class="forminp forminp-select">
                    <?php
                    $url     = get_option( 'fp_rac_email_cartlink_logo_text' ) ;
                    $img_val = __( $url , 'recoverabandoncart' ) ;
                    ?>
                    <input placeholder="<?php echo __( 'Choose Image' , 'recoverabandoncart' ) ; ?>" type="text" id="fp_rac_email_cartlink_logo_text" name="fp_rac_email_cartlink_logo_text" value="<?php echo $img_val ; ?>"/>
                    <input type="button" id="fp_rac_email_cartlink_logo_button" class="button-secondary fp_rac_class_cartlink_image" name="fp_rac_email_cartlink_logo_button" value="<?php echo __( 'Upload Image' , 'recoverabandoncart' ) ?>"/>
                </td>
            </tr>
            <?php
        }

    }

    FP_RAC_Email_Tab::init() ;
}