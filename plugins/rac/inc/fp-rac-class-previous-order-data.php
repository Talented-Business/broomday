<?php

/**
 * Previous Orders Data
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'FP_RAC_Previous_Order_Data' ) ) {

    /**
     * FP_RAC_Previous_Order_Data Class.
     */
    class FP_RAC_Previous_Order_Data {

        private $cart_content = array () ;
        private $extra_cart_content ;

        /**
         * FP_RAC_Previous_Order_Data Class initialization.
         */
        public function __construct( $cartlist ) {
            $this->cart_content_obj = maybe_unserialize( $cartlist->cart_details ) ;
            $this->cart_data        = $cartlist ;
            $this->set_cart_content() ;
        }

        /**
         * Set Cart content.
         */
        private function set_cart_content() {
            if ( $this->cart_content_obj && is_object( $this->cart_content_obj ) ) {
                $order_id = fp_rac_get_order_obj_data( $this->cart_content_obj , 'id' ) ;
                if ( ! $order_id && isset( $this->cart_data->extra_cart_content ) ) {
                    $cart_array               = maybe_unserialize( $this->cart_data->extra_cart_content ) ;
                    $this->extra_cart_content = true ;
                } else {
                    $cart_array               = fp_rac_get_order_obj( $order_id ) ;
                    $this->extra_cart_content = false ;
                }
                $this->cart_content = $cart_array ;
            }else{
                $this->cart_content = false ;
            }
        }

        /**
         * Get Cart content.
         */
        public function get_cart_content() {
            return $this->cart_content ;
        }

        public static function rac_prepare_items( $items ) {
            $rearrange_array = array () ;
            if ( version_compare( WC_VERSION , '3.0.0' , '>=' ) ) {
                $reflectionClass = new ReflectionClass( 'WC_Order_Item_Product' ) ;
                $reflBar         = $reflectionClass->getProperty( 'data' ) ;
                $reflBar->setAccessible( true ) ;
                if ( rac_check_is_array( $items ) ) {
                    foreach ( $items as $key => $item ) {
                        $get_item                      = $reflBar->getValue( $item ) ;
                        $get_item[ 'variation' ]         = $item[ 'variation' ] ;
                        $get_item[ 'line_subtotal' ]     = $item[ 'line_subtotal' ] ;
                        $get_item[ 'line_subtotal_tax' ] = $item[ 'line_subtotal_tax' ] ;
                        $get_item[ 'item_meta' ]         = $item[ 'item_meta' ] ;
                        $rearrange_array[ $key ]         = $get_item ;
                    }
                }
                $items = $rearrange_array ;
            }

            return $items ;
        }

        /**
         * Get items.
         */
        public function get_items() {
            return ( ! $this->extra_cart_content) ? $this->cart_content->get_items() : $this->cart_content->get_items ;
        }

        /**
         * Get Cart content Billing First Name.
         */
        public function get_billing_firstname() {
            return ( ! $this->extra_cart_content) ? fp_rac_get_order_obj_data( $this->cart_content , 'billing_first_name' ) : $this->cart_content->billing_first_name ;
        }

        /**
         * Get Cart content Billing Last Name.
         */
        public function get_billing_lastname() {
            return ( ! $this->extra_cart_content) ? fp_rac_get_order_obj_data( $this->cart_content , 'billing_last_name' ) : $this->cart_content->billing_last_name ;
        }

        /**
         * Get Cart content Billing Email.
         */
        public function get_billing_email() {
            return ( ! $this->extra_cart_content) ? fp_rac_get_order_obj_data( $this->cart_content , 'billing_email' ) : $this->cart_content->billing_email ;
        }

        /**
         * Get Cart content Billing Phone No.
         */
        public function get_billing_phoneno() {
            return ( ! $this->extra_cart_content) ? fp_rac_get_order_obj_data( $this->cart_content , 'billing_phone' ) : $this->cart_content->billing_phone ;
        }

        /**
         * Get Cart content user Id.
         */
        public function get_user_id() {
            return ( ! $this->extra_cart_content) ? fp_rac_get_order_obj_data( $this->cart_content , 'user_id' ) : $this->cart_content->user_id ;
        }

        /**
         * Get Cart content shipping tax
         */
        public function get_shipping_tax() {
            return ( ! $this->extra_cart_content) ? ( float ) $this->cart_content->get_shipping_tax() : $this->cart_content->shipping_tax ;
        }

        /**
         * Get Cart content total Shipping cost
         */
        public function get_total_shipping() {
            return ( ! $this->extra_cart_content) ? ( float ) $this->cart_content->get_total_shipping() : $this->cart_content->total_shipping ;
        }

        /**
         * Get Cart content shipping Method.
         */
        public function get_shipping_method() {
            return ( ! $this->extra_cart_content) ? $this->cart_content->get_shipping_method() : $this->cart_content->shipping_method ;
        }

    }

}