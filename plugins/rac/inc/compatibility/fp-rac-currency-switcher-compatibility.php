<?php

/* Compatibility for Multiple Currency Switcher */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
/*
 * Get Current current code 
 * 
 */
if ( ! function_exists( 'fp_rac_get_current_currency_code' ) ) {

    function fp_rac_get_current_currency_code() {
        $currency_code = get_option( 'woocommerce_currency' ) ;

        if ( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {// Compatible for Alia Currency Switcher.
            $currency_code = isset( $_COOKIE[ 'aelia_cs_selected_currency' ] ) ? $_COOKIE[ 'aelia_cs_selected_currency' ] : $currency_code ;
        } elseif ( class_exists( 'WOOCS' ) ) {// Compatible for Woocommerce Currency Switcher.
            global $WOOCS ;
            if ( is_object( $WOOCS ) ) {
                $currency_code = $WOOCS->current_currency ;
            }
        } elseif ( class_exists( 'WCML_Multi_Currency' ) ) {// Compatible for WPML MultiCurrency Switcher
            global $woocommerce_wpml ;
            if ( is_object( $woocommerce_wpml->multi_currency ) ) {
                $currency_code = $woocommerce_wpml->multi_currency->get_client_currency() ;
            }
        } elseif ( class_exists( 'WOOMULTI_CURRENCY_F_Frontend_Symbol' ) ) { // Compatible for WooCommerce Multi Currency by Villa themes
            $obj           = new WOOMULTI_CURRENCY_F_Frontend_Symbol() ;
            $currency_code = $obj->woocommerce_currency( $currency_code ) ;
        } elseif ( class_exists( 'WOOMULTI_CURRENCY_Frontend_Symbol' ) ) { // Compatible for WooCommerce Multi Currency Premium by Villa themes
            $obj           = new WOOMULTI_CURRENCY_Frontend_Symbol() ;
            $currency_code = $obj->woocommerce_currency( $currency_code ) ;
        }
        return $currency_code ;
    }

}
/*
 * Format Price based on Currrency code.
 * 
 */
if ( ! function_exists( 'fp_rac_format_price' ) ) {

    function fp_rac_format_price( $price , $currency_code = '' , $product = NULL ) {
        $formatted_price = '' ;
        if ( $currency_code != '' ) {
            if ( class_exists( 'WCML_Multi_Currency' ) ) {// Compatible for WPML MultiCurrency Switcher
                global $woocommerce_wpml ;
                if ( is_object( $woocommerce_wpml->multi_currency ) && $woocommerce_wpml->settings[ 'enable_multi_currency' ] == WCML_MULTI_CURRENCIES_INDEPENDENT ) {
                    $formatted_price = fp_rac_wpml_change_currency_symbol( $price , $currency_code ) ;
                }
            }
            if ( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {// Compatible for Alia Currency Switcher.
                $switcherobj = $GLOBALS[ WC_Aelia_CurrencySwitcher::$plugin_slug ] ;
                if ( is_object( $switcherobj ) && ! $formatted_price ) {
                    $formatted_price = $switcherobj->format_price( $price , $currency_code ) ;
                }
            }
            if ( class_exists( 'WOOCS' ) ) {
                global $WOOCS ;
                if ( is_object( $WOOCS ) && ! $formatted_price ) {
                    $WOOCS->current_currency = $currency_code ;
                    $formatted_price         = $WOOCS->wc_price( $price , true , array ( 'currency' => $currency_code ) , $product ) ;
                }
            }
            if ( ! $formatted_price )
                $formatted_price = fp_rac_wc_format_price( $price , array ( 'currency' => $currency_code ) ) ;
        } else {
            $formatted_price = fp_rac_wc_format_price( $price ) ;
        }

        return $formatted_price ;
    }

}

function fp_rac_wpml_change_currency_symbol( $amount , $currency ) {
    global $woocommerce_wpml ;
    $currency_details = $woocommerce_wpml->multi_currency->get_currency_details_by_code( $currency ) ;

    switch ( $currency_details[ 'position' ] ) {
        case 'left' :
            $format = '%1$s%2$s' ;
            break ;
        case 'right' :
            $format = '%2$s%1$s' ;
            break ;
        case 'left_space' :
            $format = '%1$s&nbsp;%2$s' ;
            break ;
        case 'right_space' :
            $format = '%2$s&nbsp;%1$s' ;
            break ;
    }

    $wc_price_args = array (
        'currency'           => $currency ,
        'decimal_separator'  => $currency_details[ 'decimal_sep' ] ,
        'thousand_separator' => $currency_details[ 'thousand_sep' ] ,
        'decimals'           => $currency_details[ 'num_decimals' ] ,
        'price_format'       => $format ,
            ) ;

    $price = wc_price( $amount , $wc_price_args ) ;

    return $price ;
}
