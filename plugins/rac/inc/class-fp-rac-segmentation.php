<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

include_once RAC_PLUGIN_PATH . '/inc/api/class-fp-segmentation.php';
if (!class_exists('FP_RAC_Segmentation')) {

    /**
     * FP_RAC_Segmentation Class.
     */
    class FP_RAC_Segmentation extends FP_Segmentation {

        /**
         * This function is used to check Send Mail Based On 
         * 1. Number of Orders Placed by each User or Guest
         * 2. Total Amount Spent by each User or Guest.
         * 3. Abondon Cart Amount.
         * 4. Abandon Cart Date
         * 5. Abandon Cart Quantity.
         * 6. User Roles.
         * 7. Selected Products.
         */
        public static function check_send_mail_based_on($each_cart, $email_template, $user_id, $email_id) {
            $email_template = maybe_unserialize($email_template->segmentation);
            if (isset($email_template) && !empty($email_template)) {
                $send_mail_option = $email_template['rac_template_seg_type'];
                //Numbers Of orders Placed.
                if ($send_mail_option == 'rac_template_seg_odrer_count') {
                    $order_placed_min = $email_template['rac_template_seg_odrer_count_min'] != '*' ? (float) $email_template['rac_template_seg_odrer_count_min'] : '*';
                    $order_placed_max = $email_template['rac_template_seg_odrer_count_max'] != '*' ? (float) $email_template['rac_template_seg_odrer_count_max'] : '*';
                    if ($order_placed_min === '*' && $order_placed_max === '*') {
                        return true;
                    } else {
                        $total_order_placed = self::get_no_of_orders_placed($user_id, $email_id);
                        return self::check_status_of_min_max($total_order_placed, $order_placed_min, $order_placed_max);
                    }

                    //Total Amount Spent each User
                } elseif ($send_mail_option == 'rac_template_seg_odrer_amount') {
                    $order_placd_total_min = $email_template['rac_template_seg_odrer_amount_min'] != '*' ? (float) $email_template['rac_template_seg_odrer_amount_min'] : '*';
                    $order_placd_total_max = $email_template['rac_template_seg_odrer_amount_max'] != '*' ? (float) $email_template['rac_template_seg_odrer_amount_max'] : '*';
                    if ($order_placd_total_min === '*' && $order_placd_total_max === '*') {
                        return true;
                    } else {
                        $total_amount_spent = self::get_amount_spent_by_user($user_id, $email_id);
                        return self::check_status_of_min_max($total_amount_spent, $order_placd_total_min, $order_placd_total_max);
                    }

                    //Abandon Cart Amount Limit.
                } else if ($send_mail_option == 'rac_template_seg_cart_total') {
                    $cart_total_min = $email_template['rac_template_seg_cart_total_min'] != '*' ? (float) $email_template['rac_template_seg_cart_total_min'] : '*';
                    $cart_total_max = $email_template['rac_template_seg_cart_total_max'] != '*' ? (float) $email_template['rac_template_seg_cart_total_max'] : '*';
                    if ($cart_total_min === '*' && $cart_total_max === '*') {
                        return true;
                    } else {
                        $cart_total = self::rac_check_status_of_segmentation($each_cart, 'cart_total');
                        return self::check_status_of_min_max($cart_total, $cart_total_min, $cart_total_max);
                    }

                    //Abandon Date Range.              
                } else if ($send_mail_option == 'rac_template_seg_cart_date') {
                    $cart_abadon_from_date = $email_template['rac_template_seg_cart_from_date'] != '' ? strtotime($email_template['rac_template_seg_cart_from_date'] . '00:00:00') : '';
                    $cart_abadon_to_date = $email_template['rac_template_seg_cart_to_date'] != '' ? strtotime($email_template['rac_template_seg_cart_to_date'] . '23:59:59') : '';
                    return self::check_status_of_from_to_date($each_cart->cart_abandon_time, $cart_abadon_from_date, $cart_abadon_to_date);

                    //Check Each Cart Quantity Range  
                } else if ($send_mail_option == 'rac_template_seg_cart_quantity') {
                    $cart_total_quantity_min = $email_template['rac_template_seg_cart_quantity_min'] != '*' ? (float) $email_template['rac_template_seg_cart_quantity_min'] : '*';
                    $cart_total_quantity_max = $email_template['rac_template_seg_cart_quantity_max'] != '*' ? (float) $email_template['rac_template_seg_cart_quantity_max'] : '*';
                    if ($cart_total_quantity_min === '*' && $cart_total_quantity_max === '*') {
                        return true;
                    } else {
                        $total_qty = self::rac_check_status_of_segmentation($each_cart, 'qty');
                        return self::check_status_of_min_max($total_qty, $cart_total_quantity_min, $cart_total_quantity_max);
                    }

                    //Check Selected Roles match with Each cart User Role.             
                } elseif ($send_mail_option == 'rac_template_seg_user_role') {
                    $selected_user_roles = $email_template['rac_template_seg_selected_user_role'];
                    if (empty($selected_user_roles)) {
                        return false;
                    } else {
                        return self::check_user_roles($user_id, $selected_user_roles);
                    }

                    //Check Selected Product Match with Each Cart Products. 
                } else {
                    if (!isset($email_template['rac_template_seg_cart_product_category'])) {
                        $select_products = $email_template['rac_template_seg_selected_product_in_cart'];
                        if (empty($select_products)) {
                            return true;
                        } else {
                            return self::rac_check_status_of_segmentation($each_cart, 'product', $select_products);
                        }
                    } else {
                        $product_category_option = $email_template['rac_template_seg_cart_product_category'];
                        if ($product_category_option == 'include_product') {
                            $select_products = $email_template['rac_template_seg_selected_product_in_cart'];
                        } elseif ($product_category_option == 'exclude_product') {
                            $select_products = $email_template['rac_template_seg_selected_product_not_in_cart'];
                        } elseif ($product_category_option == 'include_category') {
                            $select_products = $email_template['rac_template_seg_selected_category_in_cart'];
                        } elseif ($product_category_option == 'exclude_category') {
                            $select_products = $email_template['rac_template_seg_selected_category_not_in_cart'];
                        } else {
                            return true;
                        }

                        if (empty($select_products)) {
                            return true;
                        } else {
                            $post_type = explode('_', $product_category_option);
                            if ($post_type[0] == 'include') {
                                return self::rac_check_status_of_segmentation($each_cart, $post_type[1], $select_products);
                            } else {
                                return !self::rac_check_status_of_segmentation($each_cart, $post_type[1], $select_products);
                            }
                        }
                    }
                }
            } else {
                return true;
            }
        }

        /**
         * Check Rac cart Related Functions.
         */
        public static function rac_check_status_of_segmentation($each_cart, $post_type, $select_products = false) {
            $cart_array = maybe_unserialize($each_cart->cart_details);
            if (is_array($cart_array) && isset($cart_array['shipping_details'])) {
                unset($cart_array['shipping_details']);
            }
            $total = '0';
            if (is_array($cart_array) && empty($each_cart->ip_address)) {
                foreach ($cart_array as $cart) {
                    foreach ($cart as $inside) {
                        foreach ($inside as $product) {
                            if ($post_type == 'product') {
                                $product_id = self::get_product_id($product);
                                if (self::check_selected_products_there($product_id, $select_products)) {
                                    return true;
                                }
                            } elseif ($post_type == 'category') {
                                if (self::check_selected_category_there($product['product_id'], $select_products)) {
                                    return true;
                                }
                            } elseif ($post_type == 'cart_total') {
                                $total += $product['line_subtotal'];
                            } else {
                                $total += $product['quantity'];
                            }
                        }
                    }
                }
            } elseif (is_array($cart_array)) {
                if (isset($cart_array['visitor_mail'])) {
                    unset($cart_array['visitor_mail']);
                }
                if (isset($cart_array['first_name'])) {
                    unset($cart_array['first_name']);
                }
                if (isset($cart_array['last_name'])) {
                    unset($cart_array['last_name']);
                }
                if (isset($cart_array['visitor_phone'])) {
                    unset($cart_array['visitor_phone']);
                }
                foreach ($cart_array as $product) {
                    if ($post_type == 'product') {
                        $product_id = self::get_product_id($product);
                        if (self::check_selected_products_there($product_id, $select_products)) {
                            return true;
                        }
                    } elseif ($post_type == 'category') {
                        if (self::check_selected_category_there($product['product_id'], $select_products)) {
                            return true;
                        }
                    } elseif ($post_type == 'cart_total') {
                        $total += $product['line_subtotal'];
                    } else {
                        $total += $product['quantity'];
                    }
                }
            } elseif (is_object($cart_array)) {
                $old_order_obj = new FP_RAC_Previous_Order_Data($each_cart);
                if ($old_order_obj->get_cart_content()) {
                    $order_items = $old_order_obj->get_items();
                    if (rac_check_is_array($order_items)) {
                        foreach ($order_items as $item) {
                            if ($post_type == 'product') {
                                $product_id = self::get_product_id($item);
                                if (self::check_selected_products_there($product_id, $select_products)) {
                                    return true;
                                }
                            } elseif ($post_type == 'category') {
                                if (self::check_selected_category_there($item['product_id'], $select_products)) {
                                    return true;
                                }
                            } elseif ($post_type == 'cart_total') {
                                $total += $item['line_subtotal'];
                            } else {
                                $quantity = isset($item['quantity']) ? $item['quantity'] : $item['qty'];
                                $total += $quantity;
                            }
                        }
                    }
                } else {
                    return false;
                }
            }

            if ($select_products) {
                return false;
            } else {
                return (float) $total;
            }
        }

        public static function get_product_id($product) {
            $product_id = $product['product_id'];
            $whole_product = fp_rac_get_product($product_id);
            if (is_object($whole_product)) {
                if ($whole_product->is_type('simple')) {
                    $product_id = $product['product_id'];
                } else if ($whole_product->is_type('variable')) {
                    $product_id = $product['variation_id'];
                }
            }
            return $product_id;
        }

    }

}
