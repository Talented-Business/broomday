<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'FP_RAC_Polish_Product_Info' ) ) {

    /**
     * FP_RAC_Polish_Product_Info Class.
     * 
     */
    class FP_RAC_Polish_Product_Info {

        //Polish the Product Info using Cart Details

        public static function fp_rac_extract_cart_details( $each_cart , $fp_rac_mail_purpose ) {
            ob_start() ;
            echo $fp_rac_mail_purpose ? fp_rac_email_css() : '' ;
            $class_name            = $fp_rac_mail_purpose ? self::enable_border() : 'td' ;
            $border                = $fp_rac_mail_purpose ? ' ' : ' border="1"' ;
            $width                 = $fp_rac_mail_purpose ? '100%' : '50%' ;
            $border_top_width      = $fp_rac_mail_purpose ? '4px' : '1px' ;
            $lang                  = is_object( $each_cart ) && isset( $each_cart->wpml_lang ) ? $each_cart->wpml_lang : 'en' ;
            $curreny_code          = is_object( $each_cart ) && isset( $each_cart->currency_code ) ? $each_cart->currency_code : '' ;
            ?>
            <table class="<?php echo $class_name ?>" cellspacing="0" cellpadding="6" style="width: <?php echo $width ?>; font-family: 'Helvetica Neue', 'Helvetica', 'Roboto', 'Arial', 'sans-serif';"<?php echo $border ?> >
                <thead>
                    <?php echo self::fp_rac_get_sortable_column_name( $lang ) ; ?>
                </thead>
                <tbody>
                    <?php
                    $tax                   = '0' ;
                    $subtotal              = '0' ;
                    $shipping              = '0' ;
                    $tax_total             = '0' ;
                    $total_points          = '0' ;
                    $shipping_total        = '0' ;
                    $shipping_tax_cost     = '0' ;
                    $shipping_method_title = '' ;
                    if ( $each_cart ) {
                        $cart_array = maybe_unserialize( $each_cart->cart_details ) ;
                        if ( is_array( $cart_array ) && ( ! empty( $cart_array )) ) {
                            $shipping_total        = ( float ) self::fp_rac_get_shipping_total( $cart_array ) ;
                            $shipping_tax_cost     = ( float ) self::fp_rac_get_shipping_tax_total( $cart_array ) ;
                            $shipping_method_title = self::fp_rac_get_shipping_method_tilte( $cart_array ) ;
                            $shipping              = self::fp_rac_get_shipping_details( $shipping_total , $shipping_method_title , $shipping_tax_cost , $curreny_code ) ;
                            if ( isset( $cart_array[ 'shipping_details' ] ) ) {
                                unset( $cart_array[ 'shipping_details' ] ) ;
                            }
                            if ( isset( $cart_array[ 0 ][ 'cart' ] ) ) {
                                $cart_array = $cart_array[ 0 ][ 'cart' ] ;
                                if ( rac_check_is_array( $cart_array ) ) {
                                    $compact_total = self::fp_rac_add_table_rows( $cart_array , $curreny_code , $lang ) ;
                                    extract( $compact_total ) ;
                                }
                            } elseif ( is_array( $cart_array ) && ( ! empty( $cart_array )) ) {
                                if ( isset( $cart_array[ 'visitor_mail' ] ) ) {
                                    unset( $cart_array[ 'visitor_mail' ] ) ;
                                }
                                if ( isset( $cart_array[ 'first_name' ] ) ) {
                                    unset( $cart_array[ 'first_name' ] ) ;
                                }
                                if ( isset( $cart_array[ 'last_name' ] ) ) {
                                    unset( $cart_array[ 'last_name' ] ) ;
                                }
                                if ( isset( $cart_array[ 'visitor_phone' ] ) ) {
                                    unset( $cart_array[ 'visitor_phone' ] ) ;
                                }
                                $compact_total = self::fp_rac_add_table_rows( $cart_array , $curreny_code , $lang ) ;
                                extract( $compact_total ) ;
                            }
                        } elseif ( is_object( $cart_array ) ) {
                            $old_order_obj = new FP_RAC_Previous_Order_Data( $each_cart ) ;
                            if ( $old_order_obj->get_cart_content() ) {
                                $shipping_tax_cost     = $old_order_obj->get_shipping_tax() ;
                                $shipping_total        = $old_order_obj->get_total_shipping() ;
                                $shipping_method_title = $old_order_obj->get_shipping_method() ;
                                $shipping              = self::fp_rac_get_shipping_details( $shipping_total , $shipping_method_title , $shipping_tax_cost , $curreny_code ) ;
                                $cart_array            = $old_order_obj->get_items() ;
                                if ( rac_check_is_array( $cart_array ) ) {
                                    $compact_total = self::fp_rac_add_table_rows( $cart_array , $curreny_code , $lang ) ;
                                    extract( $compact_total ) ;
                                }
                            }
                        }
                        $shipping_check = $shipping_method_title != '' && get_option( 'rac_hide_shipping_row_product_info_shortcode' ) != 'yes' ;
                        $tax_check      = $tax > 0 && get_option( 'rac_hide_tax_row_product_info_shortcode' ) != 'yes' ;
                        $tax_total      = $tax + $shipping_tax_cost ;
                        $total_coupon   = fp_rac_check_sumo_coupon_exists( $subtotal + $tax_total + $shipping_total ) ;
                    } else {
                        $product_name = "Product A" ;
                        if ( get_option( 'rac_troubleshoot_sku_sh' ) != 'no' ) {
                            $product_name = $product_name . " (#PRODSAMP-SKU)" ;
                        }
                        $shipping_check = get_option( 'rac_hide_shipping_row_product_info_shortcode' ) != 'yes' ;
                        $tax_check      = get_option( 'rac_inc_tax_with_product_price_product_info_shortcode' ) == 'no' && get_option( 'rac_hide_tax_row_product_info_shortcode' ) != 'yes' ;
                        $subtotal       = 10 ;
                        $shipping       = 10 ;
                        $shipping_total = 10 ;
                        $tax_total      = 1 ;
                        $total_points   = 0 ;
                        $total_coupon   = 0 ;
                        echo self::fp_split_rac_items_in_cart( $product_name , fp_rac_placeholder_img() , '1' , fp_rac_format_price( 10 ) ) ;
                    }
                    ?>
                </tbody>
                <?php if ( get_option( 'rac_hide_tax_total_product_info_shortcode' ) != 'yes' ) { ?>
                    <tfoot>
                        <tr>
                            <?php $i = 1 ; ?>
                            <th class="<?php echo $class_name ?>" scope="row" colspan="<?php echo fp_rac_get_column_span_count() ; ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo fp_get_wpml_text( 'rac_product_info_subtotal' , $lang , get_option( 'rac_product_info_subtotal' ) , 'admin_texts_rac_product_info_subtotal' ) ; ?></th>
                            <td class="<?php echo $class_name ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo fp_rac_format_price( $subtotal , $curreny_code ) ; ?></td>
                        </tr>
                        <?php if ( $total_points > 0 ) { ?>
                            <tr>
                                <?php
                                $i               = 1 ;
                                $rewards_mesaage = fp_get_wpml_text( 'rs_total_earned_point_caption' , $lang , get_option( 'rs_total_earned_point_caption' ) , 'admin_texts_rs_total_earned_point_caption' ) ;
                                ?>
                                <th class="<?php echo $class_name ?>" scope="row" colspan="<?php echo fp_rac_get_column_span_count() ; ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo $rewards_mesaage ; ?></th>
                                <td class="<?php echo $class_name ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo $total_points ; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ( ! empty( $total_coupon ) ) { ?>
                            <tr>
                                <?php
                                $i              = 1 ;
                                $coupon_message = get_option( 'sumo_earn_purchase_message_in_cart_page_for_cart_total' ) ;
                                $coupon_message = fp_get_wpml_text( 'sumo_earn_purchase_message_in_cart_page_for_cart_total' , $lang , $coupon_message , 'admin_texts_sumo_earn_purchase_message_in_cart_page_for_cart_total' ) ;
                                $coupon_message = str_replace( '[coupon_value]' , '' , $coupon_message ) ;
                                $coupon_value   = ($total_coupon[ 'coupon_type' ] == 'percent') ? $total_coupon[ 'coupon_value' ] . '%' : fp_rac_format_price( $total_coupon[ 'coupon_value' ] , $curreny_code ) ;
                                ?>
                                <th class="<?php echo $class_name ?>" scope="row" colspan="<?php echo fp_rac_get_column_span_count() ; ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo $coupon_message ; ?></th>
                                <td class="<?php echo $class_name ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo $coupon_value ; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ( $shipping_check ) { ?>
                            <tr>
                                <?php $i = 1 ; ?>
                                <th class="<?php echo $class_name ?>" scope="row" colspan="<?php echo fp_rac_get_column_span_count() ; ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo fp_get_wpml_text( 'rac_product_info_shipping' , $lang , get_option( 'rac_product_info_shipping' ) , 'admin_texts_rac_product_info_shipping' ) ; ?></th>
                                <td class="<?php echo $class_name ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo $shipping ; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ( $tax_check ) { ?>
                            <tr>
                                <?php
                                $i = 1 ;
                                ?>
                                <th class="<?php echo $class_name ?>" scope="row" colspan="<?php echo fp_rac_get_column_span_count() ; ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo fp_get_wpml_text( 'rac_product_info_tax' , $lang , get_option( 'rac_product_info_tax' ) , 'admin_texts_rac_product_info_tax' ) ; ?></th>
                                <td class="<?php echo $class_name ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo fp_rac_format_price( $tax_total , $curreny_code ) ; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <?php $i = 1 ; ?>
                            <th class="<?php echo $class_name ?>" scope="row" colspan="<?php echo fp_rac_get_column_span_count() ; ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo fp_get_wpml_text( 'rac_product_info_total' , $lang , get_option( 'rac_product_info_total' ) , 'admin_texts_rac_product_info_total' ) ; ?></th>
                            <td class="<?php echo $class_name ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo fp_rac_format_price( ($subtotal + $tax_total + $shipping_total ) , $curreny_code ) ; ?></td>
                        </tr>
                        <?php
                        if ( class_exists( 'SUMOPaymentPlans' ) ) {
                            $sumo_pp_balance_payable = '' ;
                            foreach ( $cart_array as $each_cart ) {
                                $saved_array = ($each_cart[ 'sumo_plugins' ][ 'sumo_pp' ]) ;
                                if ( isset( $saved_array[ 'balance_payable' ] ) && ! empty( $saved_array[ 'balance_payable' ] ) ) {
                                    $sumo_pp_balance_payable = $saved_array[ 'balance_payable' ] ;
                                }
                            }
                            if ( $sumo_pp_balance_payable ) {
                                ?>
                                <tr>
                                    <?php $i = 1 ; ?>
                                    <th class="<?php echo $class_name ?>" scope="row" colspan="<?php echo fp_rac_get_column_span_count() ; ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php _e( 'Balance Payable Amount' , 'sumopaymentplans' ) ; ?></th>
                                    <td class="<?php echo $class_name ?>" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: ' . $border_top_width . ';' ; ?>"><?php echo fp_rac_format_price( ($sumo_pp_balance_payable ) , $curreny_code ) ; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tfoot>
                <?php } ?>
            </table>
            <?php
            return ob_get_clean() ;
        }

        public static function fp_split_rac_items_in_cart( $product_name , $image , $quantity , $price ) {
            ob_start() ;
            ?><tr>
                <?php
                $default_column  = array ( 'product_name' , 'product_image' , 'product_quantity' , 'product_price' ) ;
                $sortable_column = get_option( 'drag_and_drop_product_info_sortable_column' ) ;
                $sortable_column = is_array( $sortable_column ) && ! empty( $sortable_column ) ? $sortable_column : $default_column ;
                if ( rac_check_is_array( $sortable_column ) ) {
                    foreach ( $sortable_column as $column_key_name ) {
                        $product_details = $column_key_name == 'product_name' ? $product_name : ($column_key_name == 'product_image' ? $image : ($column_key_name == 'product_quantity' ? $quantity : $price)) ;
                        if ( get_option( 'rac_hide_' . $column_key_name . '_product_info_shortcode' ) != 'yes' ) {
                            ?>
                            <td class="<?php echo self::enable_border() ?>" colspan="<?php echo fp_rac_get_column_span_count( 2 ) ; ?>" style="text-align:left; vertical-align:middle; font-family: 'Helvetica Neue', 'Helvetica', 'Roboto', 'Arial', 'sans-serif'; word-wrap:break-word;">
                                <?php echo $product_details ; ?>
                            </td>
                            <?php
                        }
                    }
                }
                ?> </tr><?php
            return ob_get_clean() ;
        }

        /**
         * Adding table rows and get total and subtotal values.
         * @param  Array $cart_array.
         * @return Array
         */
        public static function fp_rac_add_table_rows( $cart_array , $curreny_code , $lang ) {
            $tax          = '0' ;
            $subtotal     = '0' ;
            $total_points = '0' ;
            foreach ( $cart_array as $eachproduct ) {
                $product_name = fp_rac_get_compatible_product_name( $eachproduct , $curreny_code , $lang ) ;
                $image        = self::get_product_image( $eachproduct ) ;
                $quantity     = isset( $eachproduct[ 'quantity' ] ) ? $eachproduct[ 'quantity' ] : $eachproduct[ 'qty' ] ;

                if ( get_option( 'rac_inc_tax_with_product_price_product_info_shortcode' ) == 'yes' ) {
                    $price    = $eachproduct[ 'line_subtotal' ] + $eachproduct[ 'line_subtotal_tax' ] ;
                    $tax      = 0 ;
                    $subtotal += $eachproduct[ 'line_subtotal' ] + $eachproduct[ 'line_subtotal_tax' ] ;
                } else {
                    $price    = $eachproduct[ 'line_subtotal' ] ;
                    $tax      += $eachproduct[ 'line_subtotal_tax' ] ;
                    $subtotal += $eachproduct[ 'line_subtotal' ] ;
                }

                $price_total  = fp_rac_get_format_product_price( $price , $curreny_code , $eachproduct ) ;
                extract( $price_total ) ;
                $total_points += $points ;
                echo self::fp_split_rac_items_in_cart( $product_name , $image , $quantity , $price ) ;
            }

            return array ( 'tax' => $tax , 'subtotal' => $subtotal , 'total_points' => $total_points ) ;
        }

        /**
         * Get Product Name.
         * @param  Object $product
         * @return string $product_name
         */
        public static function get_product_name( $product ) {
            $product_name = get_the_title( $product[ 'product_id' ] ) ;
            $product_name = self::fp_rac_format_product_name_by_sku( $product_name , $product ) ;
            if ( get_option( 'rac_email_product_variation_sh' ) != 'no' ) {
                if ( isset( $product[ 'variation_id' ] ) && ( ! empty( $product[ 'variation_id' ] )) ) {
                    $product_name = $product_name . '<br />' . self::fp_rac_get_formatted_variation( $product ) ;
                }
            }
            return $product_name ;
        }

        /**
         * Get Product Image.
         * @param  Object $product
         * @return string $image
         */
        public static function get_product_image( $product ) {
            $productid = $product[ 'product_id' ] ;
            $imageurl  = "" ;
            if ( (get_post_thumbnail_id( $product[ 'variation_id' ] ) != "") || (get_post_thumbnail_id( $product[ 'variation_id' ] ) != 0) ) {
                $image_urls = wp_get_attachment_image_src( get_post_thumbnail_id( $product[ 'variation_id' ] ) ) ;
                $imageurl   = $image_urls[ 0 ] ;
            }
            if ( $imageurl == "" ) {
                if ( (get_post_thumbnail_id( $productid ) != "") || (get_post_thumbnail_id( $productid ) != 0) ) {
                    $image_urls = wp_get_attachment_image_src( get_post_thumbnail_id( $productid ) ) ;
                    $imageurl   = $image_urls[ 0 ] ;
                } else {
                    $imageurl = esc_url( wc_placeholder_img_src() ) ;
                }
            }
            $image = '<img src="' . $imageurl . '" alt="' . get_the_title( $productid ) . '" height="90" width="90" />' ;
            return $image ;
        }

        /**
         * Get the formatted Attribute variations.
         * @param  Object Variations.
         * @return String
         */
        public static function fp_rac_get_formatted_variation( $variations ) {
            $formatted_attributes = '' ;
//            $product_id           = $variations[ 'product_id' ] ;
//            $product              = fp_rac_get_product( $variations[ 'variation_id' ] ) ;
//            $html_variations      = wc_get_formatted_variation( $product , false ) ;
//            $formatted_variations = strip_tags( $html_variations , '<dd><dt>' ) ;
//            $attributes           = explode( '</dd>' , $formatted_variations ) ;
//            if ( rac_check_is_array( $attributes ) ) {
//                foreach ( $attributes as $each_attribute ) {
//                    $explode_data = explode( ':</dt>' , $each_attribute ) ;
//                    if ( isset( $explode_data[ 0 ] ) && isset( $explode_data[ 1 ] ) ) {
//                        $variation            = strip_tags( $explode_data[ 1 ] ) ;
//                        $attribute            = strip_tags( $explode_data[ 0 ] ) ;
//                        $formatted_attributes .= wc_attribute_label( $explode_data[ 0 ] , $product ) . ':' . $variation . '<br />' ;
//                    }
//                }
//            }
            if ( rac_check_is_array( $variations[ 'variation' ] ) ) {
                foreach ( $variations[ 'variation' ] as $attribute_name => $attribute_value ) {
                    $name                 = str_replace( 'attribute_' , '' , $attribute_name ) ;
                    $formatted_attributes .= $name . ' : ' . $attribute_value . '<br />' ;
                }
            }
            return $formatted_attributes ;
        }

        /**
         * Get the Shipping Total.
         * @param  array CartContents.
         * @return float
         */
        public static function fp_rac_get_shipping_total( $cart_array ) {
            if ( isset( $cart_array[ 'shipping_details' ][ 'shipping_cost' ] ) ) {
                $shipping_total = $cart_array[ 'shipping_details' ][ 'shipping_cost' ] != '' ? $cart_array[ 'shipping_details' ][ 'shipping_cost' ] : ( float ) 0 ;
                return $shipping_total ;
            }
            return '' ;
        }

        /**
         * Get the Shipping Tax Total.
         * @param  array CartContents.
         * @return float
         */
        public static function fp_rac_get_shipping_tax_total( $cart_array ) {
            if ( isset( $cart_array[ 'shipping_details' ][ 'shipping_tax_cost' ] ) ) {
                $shipping_tax_cost = $cart_array[ 'shipping_details' ][ 'shipping_tax_cost' ] != '' ? $cart_array[ 'shipping_details' ][ 'shipping_tax_cost' ] : ( float ) 0 ;
                return $shipping_tax_cost ;
            }
            return '' ;
        }

        /**
         * Get the Shipping method Title.
         * @param  array CartContents.
         * @return string
         */
        public static function fp_rac_get_shipping_method_tilte( $cart_array ) {
            if ( isset( $cart_array[ 'shipping_details' ][ 'shipping_method' ] ) ) {
                $current_chosen_method = $cart_array[ 'shipping_details' ][ 'shipping_method' ] ;
                $shipping_method_title = self::fp_rac_api_get_shipping_method_title( $current_chosen_method ) ;
                return $shipping_method_title ;
            }
            return '' ;
        }

        /**
         * Get the Shipping method Title.
         * @param  boolean $current_chosen_method
         * @return string
         */
        public static function fp_rac_api_get_shipping_method_title( $current_chosen_method ) {
            if ( $current_chosen_method != '' ) {
                $explode_shipping_method_id = explode( ':' , $current_chosen_method ) ;
                $method_id                  = $explode_shipping_method_id[ 0 ] ;
                $instance_id                = isset( $explode_shipping_method_id[ 1 ] ) ? $explode_shipping_method_id[ 1 ] : '' ;
                $wc_shipping                = WC_Shipping::instance() ;
                if ( method_exists( 'WC_Shipping' , 'load_shipping_methods' ) ) {
                    $allowed_classes = $wc_shipping->load_shipping_methods() ;
                } else {
                    $allowed_classes = $wc_shipping->get_shipping_method_class_names() ;
                }
                if ( ! empty( $method_id ) && in_array( $method_id , array_keys( $allowed_classes ) ) ) {
                    $class_name = $allowed_classes[ $method_id ] ;
                    if ( is_object( $class_name ) ) {
                        $class_name = get_class( $class_name ) ;
                    }
                    $method_object = new $class_name( $instance_id ) ;
                    if ( is_object( $method_object ) ) {
                        $shipping_method_title = $method_object->title ;
                        return $shipping_method_title ;
                    }
                }
            }
            return '' ;
        }

        /**
         * Get the Shipping cost Details.
         * @param  string $total
         * @param  string $method_title
         * @return string
         */
        public static function fp_rac_get_shipping_details( $total , $method_title , $shipping_tax_cost , $curreny_code ) {
            if ( $total > 0 ) {
                if ( get_option( 'rac_inc_tax_with_product_price_product_info_shortcode' ) != 'no' ) {
                    $total = $shipping_tax_cost + $total ;
                }
                return $method_title . ': ' . fp_rac_format_price( $total , $curreny_code ) ;
            } else {
                return $method_title ;
            }
        }

        /**
         * Get sortable coulmn name.
         * @param  string $lang
         * @return string
         */
        public static function fp_rac_get_sortable_column_name( $lang ) {
            ob_start() ;
            $default_column  = array ( 'product_name' , 'product_image' , 'product_quantity' , 'product_price' ) ;
            $sortable_column = get_option( 'drag_and_drop_product_info_sortable_column' ) ;
            $sortable_column = is_array( $sortable_column ) && ! empty( $sortable_column ) ? $sortable_column : $default_column ;
            $new_array       = array ( 'product_name' => 'rac_product_info_product_name' , 'product_image' => 'rac_product_info_product_image' , 'product_quantity' => 'rac_product_info_quantity' , 'product_price' => 'rac_product_info_product_price' ) ;
            if ( rac_check_is_array( $sortable_column ) ) {
                foreach ( $sortable_column as $column_key_name ) {
                    if ( $column_key_name == 'product_name' ) {
                        $product_details = get_option( 'rac_product_info_product_name' ) ;
                    } elseif ( $column_key_name == 'product_image' ) {
                        $product_details = get_option( 'rac_product_info_product_image' ) ;
                    } elseif ( $column_key_name == 'product_quantity' ) {
                        $product_details = get_option( 'rac_product_info_quantity' ) ;
                    } elseif ( $column_key_name == 'product_price' ) {
                        $product_details = get_option( 'rac_product_info_product_price' ) ;
                    }
                    if ( get_option( 'rac_hide_' . $column_key_name . '_product_info_shortcode' ) != 'yes' ) {
                        ?>
                        <th class="<?php echo self::enable_border() ?>" colspan="<?php echo fp_rac_get_column_span_count( 2 ) ; ?>" scope="col" style="text-align:left;">
                            <?php echo fp_get_wpml_text( $new_array[ $column_key_name ] , $lang , $product_details , 'admin_texts_' . $new_array[ $column_key_name ] ) ; ?>
                        </th>
                        <?php
                    }
                }
            }
            return ob_get_clean() ;
        }

        public static function fp_rac_format_product_name_by_sku( $product_name , $product ) {
            if ( get_option( 'rac_troubleshoot_sku_sh' ) != 'no' ) {
                $sku = self::fp_rac_get_product_sku( $product ) ;
                if ( ! empty( $sku ) ) {
                    $product_name = $product_name . ' (#' . $sku . ')' ;
                }
            }
            return $product_name ;
        }

        public static function fp_rac_get_product_sku( $product ) {
            $sku = '' ;
            if ( isset( $product[ 'variation_id' ] ) && ( ! empty( $product[ 'variation_id' ] )) ) {
                $product_object = fp_rac_get_product( $product[ 'variation_id' ] ) ;
                if ( is_object( $product_object ) ) {
                    $sku = $product_object->get_sku() ;
                }
            } else {
                $product_object = fp_rac_get_product( $product[ 'product_id' ] ) ;
                if ( is_object( $product_object ) ) {
                    $sku = $product_object->get_sku() ;
                }
            }
            return $sku ;
        }

        public static function enable_border() {
            $enable_border = get_option( 'rac_enable_border_for_productinfo_in_email' ) ;
            if ( $enable_border != 'no' ) {
                return 'td' ;
            } else {
                return '' ;
            }
        }

    }

}

function fp_rac_get_column_span_count( $value = '' ) {
    $i = 3 ;
    $i = get_option( 'rac_hide_product_name_product_info_shortcode' ) == 'yes' ? $i - 1 : $i ;
    $i = get_option( 'rac_hide_product_image_product_info_shortcode' ) == 'yes' ? $i - 1 : $i ;
    $i = get_option( 'rac_hide_product_quantity_product_info_shortcode' ) == 'yes' ? $i - 1 : $i ;
    $i = get_option( 'rac_hide_product_price_product_info_shortcode' ) == 'yes' ? $i - 1 : $i ;
    if ( $i <= 0 && $value != '' ) {
        return $value ;
    }
    if ( $value == '' ) {
        return $i ;
    } else {
        return '' ;
    }
}

function fp_rac_email_css() {
    global $woocommerce ;
    if ( ( float ) $woocommerce->version < ( float ) ('2.4.0') ) {
        ?>
        <style type="text/css">
            table .td{
                border: 1px solid #e4e4e4 !important;
            }

        </style>
        <?php
    }
}
