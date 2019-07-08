<?php
/*
 * Email Commom Functions
 * 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function rac_send_mail($to, $subject, $woo_temp_msg, $headers, $html_template = '', $compact = array()) {
    global $woocommerce;

    include_once RAC_PLUGIN_PATH . '/inc/class-fp-rac-send-email-by-woocommerce-mailer.php';
    //This hook for email return path header
    add_action('phpmailer_init', array('FP_RAC_Send_Email_Woocommerce_Mailer', 'fp_rac_phpmailer_init'), 10, 1);
    FP_RAC_Send_Email_Woocommerce_Mailer::$sending = true;
    if ((float) $woocommerce->version <= (float) ('2.2.0')) {
        if (get_option('rac_webmaster_mail') == 'webmaster1') {
            if ('wp_mail' == get_option('rac_trouble_mail')) {
                return wp_mail($to, $subject, $woo_temp_msg, $headers);
            } else {
                return mail($to, $subject, $woo_temp_msg, $headers, '-f' . get_option('rac_textarea_mail'));
            }
        } else {
            if ('wp_mail' == get_option('rac_trouble_mail')) {
                return wp_mail($to, $subject, $woo_temp_msg, $headers);
            } else {
                return mail($to, $subject, $woo_temp_msg, $headers);
            }
        }
    } else {
        if ($html_template == 'HTML') {
            FP_RAC_Send_Email_Woocommerce_Mailer::send_email_via_woocommerce_mailer($to, $subject, $woo_temp_msg, $headers, $compact);
            return true;
        } else {
            wp_mail($to, $subject, $woo_temp_msg, $headers);
            return true;
        }
    }
}

// format email header
function rac_format_email_headers($compact = array(), $bcc = false) {
    $headers = '';
    if (empty($compact)) {
        $sender_opt = 'woo';
    } else {
        $sender_opt = $compact[0];
        $from_name = $compact[1];
        $from_email = $compact[2];
    }

    //header MIME version
    if (get_option('rac_mime_mail_header_ts') != 'none') {//check for to aviod header duplication
        $headers = "MIME-Version: 1.0\r\n";
    }
    //header charset
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $from_name = $sender_opt == 'local' ? $from_name : get_option('woocommerce_email_from_name');
    $from_email = $sender_opt == 'local' ? $from_email : get_option('woocommerce_email_from_address');

    //header for from 
    if (get_option('rac_webmaster_mail') == 'webmaster1') {
        $headers .= "From: " . $from_name . " <" . $from_email . ">\r\n";
    } else {
        $headers .= "From: " . $from_name . " <" . $from_email . ">\r\n";
    }

    //header for reply to
    if (get_option('rac_replyto_mail_header_ts') != 'none') {//check for to aviod header duplication
        $headers .= "Reply-To: " . $from_name . " <" . $from_email . ">\r\n";
    }

    //header BCC.
    if ($bcc) {
        $headers .= "Bcc: " . $bcc . "\r\n";
    }

    return $headers;
}

function email_woocommerce_html($mail_template_post, $subject, $message, $logo = false) {

    if (($mail_template_post == 'HTML')) {
        ob_start();
        if (function_exists('wc_get_template')) {
            wc_get_template('emails/email-header.php', array('email_heading' => $subject));
            echo $message;
            wc_get_template('emails/email-footer.php');
        } else {

            woocommerce_get_template('emails/email-header.php', array('email_heading' => $subject));
            echo $message;
            woocommerce_get_template('emails/email-footer.php');
        }
        $woo_temp_msg = ob_get_clean();
    } elseif ($mail_template_post == 'PLAIN') {

        $woo_temp_msg = $logo . $message;
    } else {

        $woo_temp_msg = $message;
    }

    return $woo_temp_msg;
}

function rac_footer_email_customization($message) {
    global $fp_rac_lang;
    $trans_message = fp_get_wpml_text('woocommerce_email_footer_text', $fp_rac_lang, $message, 'admin_texts_woocommerce_email_footer_text');
    if (get_option('fp_unsubscription_link_in_email') == 'yes') {
        if (get_option('fp_unsubscription_footer_link_text_option') == '2') {
            $replace_footer_text = rac_replace_shortcode_in_custom_footer_text();
            $replace_footer_text = $trans_message . ' ' . $replace_footer_text;
        } else {
            $replace_footer_text = rac_replace_shortcode_in_custom_footer_text();
        }
        return $replace_footer_text;
    } else {
        return $trans_message;
    }
}

function rac_cart_link_button_mode($cartlink, $cart_text) {
    ob_start();
    ?>
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" bgcolor="#<?php echo get_option('rac_cart_button_bg_color'); ?>" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block; padding:0px 10px 0px 10px;">
                <a href="<?php echo $cartlink; ?>" style="text-decoration: none; width:100%; display:inline-block;line-height:40px;"><span style="color: #<?php echo get_option('rac_cart_button_link_color'); ?>"><?php echo $cart_text; ?></span></a>
            </td>
        </tr>
    </table>
    <?php
    $results = ob_get_clean();
    return $results;
}

function rac_cart_link_image_mode($cartlink, $cart_text) {
    ob_start();
    ?>
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <a href="<?php echo $cartlink; ?>"><img src="<?php echo get_option('fp_rac_email_cartlink_logo_text') ?>" width="<?php echo get_option('rac_cart_link_image_width') ?>px" height="<?php echo get_option('rac_cart_link_image_height') ?>px" alt="<?php echo $cart_text ?>"></a>
            </td>
        </tr>
    </table>
    <?php
    $results = ob_get_clean();
    return $results;
}

function shortcode_in_subject($firstname, $lastname, $subject, $email_template_id = false, $each_cart = false) {
    if ($each_cart) {
        $subject = fp_get_wpml_text('rac_template_' . $email_template_id . '_subject', $each_cart->wpml_lang, $subject);
        $custom_product_name = fp_get_wpml_text('rac_template_subject_customization', $each_cart->wpml_lang, get_option('rac_subject_product_shrotcode_customize'));
        $product_details = fp_rac_get_first_product_title($each_cart);
        extract($product_details);
        $product_name = ($product_count > 1) ? $product_title . ' ' . $custom_product_name : $product_title;
    } else {
        $product_name = 'Product Name';
    }

    $find_array = array('{rac.firstname}', '{rac.lastname}', '{rac.productname}');
    $replace_array = array($firstname, $lastname, html_entity_decode($product_name));
    $subject = str_replace($find_array, $replace_array, $subject);
    return $subject;
}

function fp_rac_common_custom_restrict($to, $by) {
    if ($by == 'email') {
        $restrict_array = array(
            'custom_exclude' => 'custom_exclude',
            'custom_user_role' => 'custom_user_role',
            'custom_user_name_select' => 'custom_user_name_select',
            'custom_mailid_edit' => 'custom_mailid_edit',
            'custom_email_provider' => 'custom_email_provider_edit',
            'custom_include_exclude' => 'custom_include_exclude_email'
        );
    } else {
        $restrict_array = array(
            'custom_exclude' => 'custom_restrict',
            'custom_user_role' => 'custom_user_role_for_restrict_in_cart_list',
            'custom_user_name_select' => 'custom_user_name_select_for_restrict_in_cart_list',
            'custom_mailid_edit' => 'custom_mailid_for_restrict_in_cart_list',
            'custom_email_provider' => 'custom_email_provider_for_restrict_in_cart_list',
            'custom_include_exclude' => 'custom_include_exclude_entry'
        );
    }

    if (rac_check_is_array($restrict_array)) {
        extract($restrict_array);
        $getdesiredoption = get_option($custom_exclude);
        if ($getdesiredoption == 'mail_id') {
            $option_array = get_option($custom_mailid_edit);
            $option_array = explode("\r\n", $option_array);
            if ($by != 'email') {
                return true;
            }
        } elseif ($getdesiredoption == 'name') {
            $option_array = get_option($custom_user_name_select);
            $getuserby = get_user_by('email', $to);
            if ($getuserby) {
                $to = $getuserby->ID;
            }
        } elseif ($getdesiredoption == 'email_provider') {
            $to = substr(strrchr($to, "@"), 1);
            $option_array = get_option($custom_email_provider);
            $option_array = explode(',', $option_array);
        } else {
            $option_array = get_option($custom_user_role);
            $getuserby = get_user_by('email', $to);
            if ($getuserby) {
                $to = implode(',', $getuserby->roles);
            } else {
                $to = 'rac_guest';
            }
        }
        if (!empty($option_array)) {
            $inlude_exclude = get_option($custom_include_exclude);
            if (!in_array($to, $option_array)) {
                if ($inlude_exclude == 'include') {
                    return false;
                } else {
                    return true;
                }
            } else {
                if ($inlude_exclude == 'include') {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return true;
        }
    }
}

function rac_unsubscription_shortcode($to, $message, $lang = '') {
    $footer_message = rac_replace_shortcode_in_custom_footer_text($to, $lang);
    $message = str_replace('{rac.unsubscribe}', $footer_message, $message);

    return $message;
}

function rac_replace_shortcode_in_custom_footer_text($to = '', $fp_rac_lang = '') {
    global $to, $fp_rac_lang;
    if (get_option('rac_unsubscription_type') != '2') {
        $siteurl = get_option('rac_unsubscription_redirect_url');
        if ($siteurl) {
            $site_url = $siteurl;
        } else {
            $site_url = get_permalink(wc_get_page_id('myaccount'));
        }
    } else {
        $siteurl = get_option('rac_manual_unsubscription_redirect_url');
        if ($siteurl) {
            $site_url = $siteurl;
        } else {
            $site_url = get_permalink(wc_get_page_id('myaccount'));
        }
    }
    $site_name = get_bloginfo('name'); // Site Name
    $create_nonce = wp_create_nonce('myemail');
    $unsublink = esc_url(add_query_arg(array('email' => $to, 'action' => 'unsubscribe', '_mynonce' => $create_nonce), $site_url));
    $footer_link_text = get_option('fp_unsubscription_footer_link_text');
    $trans_footer_link_text = fp_get_wpml_text('fp_unsubscription_footer_link_text', $fp_rac_lang, $footer_link_text, 'admin_texts_fp_unsubscription_footer_link_text');
    $footer_message = get_option('fp_unsubscription_footer_message');
    $trans_footer_msg = fp_get_wpml_text('fp_unsubscription_footer_message', $fp_rac_lang, $footer_message, 'admin_texts_fp_unsubscription_footer_message');
    $find_shortcode = array('{rac_unsubscribe}', '{rac_site}');
    $unsublink = '<a style="color:#' . get_option('rac_unsubscribe_link_color') . '" href="' . $unsublink . '">' . $trans_footer_link_text . '</a>';
    $replace_shortcode = array($unsublink, $site_name);
    $trans_footer_msg = str_replace($find_shortcode, $replace_shortcode, $trans_footer_msg);
    return $trans_footer_msg;
}

function fp_rac_extract_cart_list($each_list, $single_product = false, $product_id = false) {
    $product_ids = array();
    $product_names = array();
    $cart_array = maybe_unserialize($each_list->cart_details);
    $total = '0';
    $points = '0';
    $shipping_total = '0';
    $shipping_tax_cost = '0';
    if (is_array($cart_array) && empty($each_list->ip_address) && $each_list->user_id != '0') {
        $shipping_total = (float) FP_RAC_Polish_Product_Info::fp_rac_get_shipping_total($cart_array);
        $shipping_tax_cost = (float) FP_RAC_Polish_Product_Info::fp_rac_get_shipping_tax_total($cart_array);
        if (isset($cart_array['shipping_details'])) {
            unset($cart_array['shipping_details']);
        }
        if (rac_check_is_array($cart_array)) {
            foreach ($cart_array as $cart) {
                foreach ($cart as $inside) {
                    foreach ($inside as $product) {
                        $total += ($product['line_subtotal'] + $product['line_subtotal_tax']);
                        $product_title = get_the_title($product['product_id']);
                        if ($single_product) {
                            $product_count = count($inside);
                            return array('product_title' => $product_title, 'product_count' => $product_count);
                        }
                        $points += fp_rac_get_rewards_points($product);
                        $product_ids[] = $product['variation_id'] ? $product['variation_id'] : $product['product_id'];
                        $product_names[] = FP_RAC_Polish_Product_Info::fp_rac_format_product_name_by_sku($product_title, $product);
                    }
                }
            }
        }
    } elseif (is_array($cart_array)) {
        //for cart captured at checkout(GUEST)
        $shipping_total = (float) FP_RAC_Polish_Product_Info::fp_rac_get_shipping_total($cart_array);
        $shipping_tax_cost = (float) FP_RAC_Polish_Product_Info::fp_rac_get_shipping_tax_total($cart_array);
        unset($cart_array['visitor_mail']);
        unset($cart_array['first_name']);
        unset($cart_array['last_name']);
        if (isset($cart_array['visitor_phone'])) {
            unset($cart_array['visitor_phone']);
        }
        if (isset($cart_array['shipping_details'])) {
            unset($cart_array['shipping_details']);
        }
        if (rac_check_is_array($cart_array)) {
            foreach ($cart_array as $product) {
                $total += ($product['line_subtotal'] + $product['line_subtotal_tax']);
                $product_title = get_the_title($product['product_id']);
                $product_ids[] = $product['variation_id'] ? $product['variation_id'] : $product['product_id'];
                if ($single_product) {
                    $product_count = count($cart_array);
                    return array('product_title' => $product_title, 'product_count' => $product_count);
                }
                $points += fp_rac_get_rewards_points($product);
                $product_names[] = FP_RAC_Polish_Product_Info::fp_rac_format_product_name_by_sku($product_title, $product);
            }
        }
    } elseif (is_object($cart_array)) { // For Guest
        $old_order_obj = new FP_RAC_Previous_Order_Data($each_list);
        if ($old_order_obj->get_cart_content()) {
            $shipping_tax_cost = $old_order_obj->get_shipping_tax();
            $shipping_total = $old_order_obj->get_total_shipping();
            $order_items = $old_order_obj->get_items();
            if (rac_check_is_array($order_items)) {
                foreach ($order_items as $item) {
                    $total += ($item['line_subtotal'] + $item['line_subtotal_tax']);
                    $product_title = get_the_title($item['product_id']);
                    $product_ids[] = $item['variation_id'] ? $item['variation_id'] : $item['product_id'];
                    if ($single_product) {
                        $product_count = count($order_items);
                        return array('product_title' => $product_title, 'product_count' => $product_count);
                    }
                    $points += fp_rac_get_rewards_points($item);
                    $product_names[] = FP_RAC_Polish_Product_Info::fp_rac_format_product_name_by_sku($product_title, $item);
                }
            }
        }
    }

    if ($product_id)
        return $product_ids;

    $total = $total + $shipping_total + $shipping_tax_cost;

    return array('product_names' => $product_names, 'total' => $total, 'earn_points' => $points);
}

function fp_rac_cart_details($each_list) {
    ob_start();
    $product_details = fp_rac_extract_cart_list($each_list);
    extract($product_details);
    if (!empty($product_names)) {
        echo implode(' , ', $product_names);
        echo " / " . fp_rac_format_price($total, $each_list->currency_code);
        if (!empty($earn_points))
            echo " / " . $earn_points . ' Points';
    }else {
        echo 'no data';
    }
    return ob_get_clean();
}

function fp_rac_get_first_product_title($each_list) {
    return fp_rac_extract_cart_list($each_list, true);
}

function fp_rac_get_cart_list_product_ids($each_list) {
    return fp_rac_extract_cart_list($each_list, false, true);
}
