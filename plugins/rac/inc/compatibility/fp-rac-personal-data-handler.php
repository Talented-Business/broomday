<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
add_filter( 'wp_privacy_personal_data_exporters' , 'add_cartlist_action' ) ;
add_filter( 'wp_privacy_personal_data_erasers' , 'remove_cartlist_action' ) ;

function add_cartlist_action( $datas ) {
    $datas[ 'fp-rac-cartlist' ] = array ( 'exporter_friendly_name' => get_option( 'rac_personal_data_export_label' ) , 'callback' => 'fp_rac_personal_data_exporter' ) ;
    return $datas ;
}

function remove_cartlist_action( $datas ) {
    $datas[ 'fp-rac-cartlist' ] = array ( 'eraser_friendly_name' => 'Remove Carts' , 'callback' => 'fp_rac_personal_data_eraser' ) ;
    return $datas ;
}

function fp_rac_personal_data_exporter( $email_address ) {
    $email_address_trimmed = trim( $email_address ) ;

    $data_to_export = array () ;

    $user = get_user_by( 'email' , $email_address_trimmed ) ;
    if ( ! $user ) {
        $user_id = 0 ;
    } else {
        $user_id = $user->ID ;
    }
    if ( $user ) {
        $args = array (
            'posts_per_page' => -1 ,
            'post_type'      => 'raccartlist' ,
            'post_status'    => array ( 'rac-cart-new' , 'rac-cart-abandon' , 'rac-cart-recovered' ) ,
            'meta_query'     => array (
                'relation'          => 'OR' ,
                'rac_user_details'  => array (
                    'key'     => 'rac_user_details' ,
                    'value'   => $user_id ,
                    'compare' => '=' ,
                ) ,
                'rac_cart_email_id' => array (
                    'key'     => 'rac_cart_email_id' ,
                    'value'   => $email_address_trimmed ,
                    'compare' => '=' ,
                ) ,
            ) ,
            'fields'         => 'ids' ,
                ) ;
    } else {
        $args = array (
            'posts_per_page' => -1 ,
            'post_type'      => 'raccartlist' ,
            'post_status'    => array ( 'rac-cart-new' , 'rac-cart-abandon' , 'rac-cart-recovered' ) ,
            'meta_query'     => array (
                'relation'          => 'OR' ,
                'rac_cart_email_id' => array (
                    'key'     => 'rac_cart_email_id' ,
                    'value'   => $email_address_trimmed ,
                    'compare' => '=' ,
                ) ,
            ) ,
            'fields'         => 'ids' ,
                ) ;
    }

    $cartlists = get_posts( $args ) ;

    foreach ( $cartlists as $each_cart_id ) {
        $mobile_no  = get_post_meta( $each_cart_id , 'rac_phone_number' , true ) ;
        $ip_address = get_post_meta( $each_cart_id , 'rac_cart_ip_address' , true ) ;
        $user_info  = get_post_meta( $each_cart_id , 'rac_user_info' , true ) ;
        if ( is_numeric( $user_info ) || $user_info == '' ) {
            $first_name = '-' ;
            $last_name  = '-' ;
        } else {
            $name       = explode( ',' , $user_info ) ;
            $first_name = $name[ 0 ] ;
            $last_name  = $name[ 1 ] ;
        }
        if ( $user ) {
            $post_data_to_export = array (
                array ( 'name' => __( 'Cart Id' , 'recoverabandoncart' ) , 'value' => $each_cart_id ) ,
                array ( 'name' => __( 'Captured on' , 'recoverabandoncart' ) , 'value' => get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , $each_cart_id ) ) ,
                array ( 'name' => __( 'Email' , 'recoverabandoncart' ) , 'value' => get_post_meta( $each_cart_id , 'rac_cart_email_id' , true ) ) ,
                array ( 'name' => __( 'Mobile No.' , 'recoverabandoncart' ) , 'value' => $mobile_no ? $mobile_no : '-' ) ,
                array ( 'name' => __( 'User ID' , 'recoverabandoncart' ) , 'value' => $user_id )
                    ) ;
        } else {
            $post_data_to_export = array (
                array ( 'name' => __( 'Cart Id' , 'recoverabandoncart' ) , 'value' => $each_cart_id ) ,
                array ( 'name' => __( 'Captured on' , 'recoverabandoncart' ) , 'value' => get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , $each_cart_id ) ) ,
                array ( 'name' => __( 'Email' , 'recoverabandoncart' ) , 'value' => get_post_meta( $each_cart_id , 'rac_cart_email_id' , true ) ) ,
                array ( 'name' => __( 'Mobile No.' , 'recoverabandoncart' ) , 'value' => $mobile_no ? $mobile_no : '-' ) ,
                array ( 'name' => __( 'IP Address' , 'recoverabandoncart' ) , 'value' => $ip_address ) ,
                array ( 'name' => __( 'First Name' , 'recoverabandoncart' ) , 'value' => $first_name ) ,
                array ( 'name' => __( 'Last Name' , 'recoverabandoncart' ) , 'value' => $last_name ) ,
                    ) ;
        }

        $data_to_export[] = array (
            'group_id'    => 'fp-rac-cartlist' ,
            'group_label' => __( 'Captured Carts' , 'recoverabandoncart' ) ,
            'item_id'     => "post-{$each_cart_id}" ,
            'data'        => $post_data_to_export ,
                ) ;
    }

    return array (
        'data' => $data_to_export ,
        'done' => true ,
            ) ;
}

function fp_rac_personal_data_eraser( $email_address ) {
    $user = get_user_by( 'email' , $email_address ) ; // Check if user has an ID in the DB to load stored personal data.
    if ( ! $user ) {
        $user_id = 0 ;
    } else {
        $user_id = $user->ID ;
    }
    $response = array (
        'items_removed'  => false ,
        'items_retained' => false ,
        'messages'       => array () ,
        'done'           => true ,
            ) ;
    if ( $user ) {
        $args = array (
            'posts_per_page' => -1 ,
            'post_type'      => 'raccartlist' ,
            'post_status'    => array ( 'rac-cart-new' , 'rac-cart-abandon' , 'rac-cart-recovered' ) ,
            'meta_query'     => array (
                'relation'          => 'OR' ,
                'rac_user_details'  => array (
                    'key'     => 'rac_user_details' ,
                    'value'   => $user_id ,
                    'compare' => '=' ,
                ) ,
                'rac_cart_email_id' => array (
                    'key'     => 'rac_cart_email_id' ,
                    'value'   => $email_address ,
                    'compare' => '=' ,
                ) ,
            ) ,
            'fields'         => 'ids' ,
                ) ;
    } else {
        $args = array (
            'posts_per_page' => -1 ,
            'post_type'      => 'raccartlist' ,
            'post_status'    => array ( 'rac-cart-new' , 'rac-cart-abandon' , 'rac-cart-recovered' ) ,
            'meta_query'     => array (
                'relation'          => 'OR' ,
                'rac_cart_email_id' => array (
                    'key'     => 'rac_cart_email_id' ,
                    'value'   => $email_address ,
                    'compare' => '=' ,
                ) ,
            ) ,
            'fields'         => 'ids' ,
                ) ;
    }

    $cartlists = get_posts( $args ) ;

    if ( 0 < count( $cartlists ) ) {
        foreach ( $cartlists as $cart_id ) {
            wp_delete_post( $cart_id , true ) ;
            /* Translators: %s Order number. */
            $response[ 'messages' ][]    = sprintf( __( 'Removed personal data from Cartlist %s.' , 'recoverabandoncart' ) , $cart_id ) ;
            $response[ 'items_removed' ] = true ;
        }
        $response[ 'done' ] = 10 > count( $cartlists ) ;
    } else {
        $response[ 'done' ] = true ;
    }

    return $response ;
}
