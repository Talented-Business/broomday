<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'FP_RAC_Cartlist_Background_Process' ) ) {

    /**
     * FP_RAC_Cartlist_Background_Process Class.
     */
    class FP_RAC_Cartlist_Background_Process extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rac_cartlist_background_updater' ;

        /**
         * Task
         *
         * Override this method to perform any actions required on each
         * queue item. Return the modified item for further processing
         * in the next pass through. Or, return false to remove the
         * item from the queue.
         *
         * @param mixed $item Queue item to iterate over
         *
         * @return mixed
         */
        protected function task( $item ) {
            $this->import_cartlist_data( $item ) ;
            return false ;
        }

        /**
         * Complete
         *
         * Override if applicable, but ensure that the below actions are
         * performed, or, call parent::complete().
         */
        protected function complete() {
            global $wpdb ;
            parent::complete() ;
            $offset = get_option( 'rac_cartlist_background_updater_offset' ) ;
            $ids    = $wpdb->get_col( "SELECT ID FROM " . $wpdb->prefix . "rac_abandoncart ORDER BY ID ASC LIMIT $offset,500" ) ;
            if ( rac_check_is_array( $ids ) ) {
                FP_RAC_Main_Function_Importing_Part::handle_cartlist( $offset ) ;
            } else {
                FP_RAC_Main_Function_Importing_Part::$progress_bar->fp_increase_progress( 30 ) ;
                FP_WooCommerce_Log::log( 'Cart Lists Upgrade Completed' ) ;
                delete_option( 'rac_cartlist_background_updater_offset' ) ;
                FP_RAC_Main_Function_Importing_Part::handle_maillog() ;
            }
        }

        public function import_cartlist_data( $cart_id ) {
            $id = '' ;
            if ( $cart_id != 'rac_no_data' ) {
                global $wpdb ;
                $data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'rac_abandoncart WHERE ID=%d' , $cart_id ) ) ;
                if ( rac_check_is_array( $data ) ) {
                    foreach ( $data as $key => $value ) {
                        if ( $value && is_object( $value ) ) {
                            $old_cartlist = fp_rac_get_post_id_from_old_id( 'rac_cart_old_id' , $value->id , array ( 'rac-cart-new' , 'rac-cart-abandon' , 'rac-cart-recovered' , 'trash' ) , 'raccartlist' ) ;
                            if ( ! $old_cartlist ) {
                                $post_status = fp_rac_get_post_status_from_cart_status( $value->cart_status ) ;
                                $date        = date( 'Y-m-d H:i:s' , $value->cart_abandon_time ) ;
                                $author      = rac_get_user_id_from_cart_list( $value ) ;
                                $arg         = array (
                                    'post_status' => $post_status ,
                                    'post_date'   => $date ,
                                    'post_author' => $author ,
                                    'post_type'   => 'raccartlist' ,
                                        ) ;

                                $id = wp_insert_post( $arg ) ;
                                update_post_meta( $id , 'rac_cart_old_id' , $value->id ) ;
                                update_post_meta( $id , 'rac_user_details' , $value->user_id ) ;
                                update_post_meta( $id , 'rac_cart_email_id' , $value->email_id ) ;
                                update_post_meta( $id , 'rac_cart_lang_code' , $value->wpml_lang ) ;
                                update_post_meta( $id , 'rac_cart_details' , $value->cart_details ) ;
                                if ( isset( $value->ip_address ) )
                                    update_post_meta( $id , 'rac_cart_ip_address' , $value->ip_address ) ;
                                if ( isset( $value->link_status ) )
                                    update_post_meta( $id , 'rac_cart_link_status' , $value->link_status ) ;
                                if ( isset( $value->completed ) )
                                    update_post_meta( $id , 'rac_cart_payment_details' , $value->completed ) ;
                                if ( isset( $value->placed_order ) )
                                    update_post_meta( $id , 'rac_recovered_order_id' , $value->placed_order ) ;
                                if ( isset( $value->currency_code ) )
                                    update_post_meta( $id , 'rac_cart_currency_code' , $value->currency_code ) ;
                                update_post_meta( $id , 'rac_cart_sending_status' , $value->sending_status ) ;
                                update_post_meta( $id , 'rac_cart_abandoned_time' , $value->cart_abandon_time ) ;

                                if ( isset( $value->mail_template_id ) )
                                    update_post_meta( $id , 'rac_cart_email_template_id' , $value->mail_template_id ) ;
                                if ( isset( $value->cart_link_clicked_time_log ) )
                                    update_post_meta( $id , 'rac_cart_link_clicked_time_log' , $value->cart_link_clicked_time_log ) ;
                                if ( isset( $value->mail_template_sending_time ) )
                                    update_post_meta( $id , 'rac_cart_email_template_sending_time' , $value->mail_template_sending_time ) ;

                                $product_ids = fp_rac_extract_cartlist_content( $value->cart_details , $value , true ) ;
                                $product_ids = implode( ',' , $product_ids ) ;

                                update_post_meta( $id , 'rac_product_details' , $product_ids ) ;

                                $user_name    = fp_rac_get_cartlist_user_details( $value , 'firstname' ) ;
                                $phone_number = fp_rac_get_cartlist_user_details( $value ) ;
                                update_post_meta( $id , 'rac_user_info' , implode( ',' , $user_name ) ) ;
                                update_post_meta( $id , 'rac_phone_number' , $phone_number ) ;

                                fp_rac_update_coupon_code( $id , $value->id ) ;

                                if ( $post_status == 'trash' ) {
                                    $old_status = fp_rac_get_post_status_from_cart_status( $value->old_status ) ;
                                    add_post_meta( $id , '_wp_trash_meta_status' , $old_status ) ;
                                    add_post_meta( $id , '_wp_trash_meta_time' , time() ) ;
                                }
                            }
                        }
                    }
                }
            }

            return $id ;
        }

    }

}