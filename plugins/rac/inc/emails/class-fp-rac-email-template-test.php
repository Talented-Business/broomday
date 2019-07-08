<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Email_Template_Test')) {

    /**
     * FP_RAC_Email_Template_Test Class.
     */
    class FP_RAC_Email_Template_Test {

        public static function init() {

            add_action('wp_ajax_rac_send_template_preview_email', array(__CLASS__, 'fp_rac_send_email_template_test_mail'));
        }

        public static function fp_rac_send_email_template_test_mail() {

            check_ajax_referer('manual-send-email-template', 'rac_security');

            global $woocommerce, $to;
            if (isset($_POST)) {
                $to = stripslashes($_POST['rac_to']);
                $mail_template_post = stripslashes($_POST['rac_template_mail']);  // mail plain or html
                $mail_logo_added = stripslashes($_POST['rac_template_link']);   // mail logo uploaded
                $sender_option_post = stripslashes($_POST['rac_template_sender_opt']);
                $from_name_post = stripslashes($_POST['rac_template_from_name']);
                $from_email_post = stripslashes($_POST['rac_template_from_email']);
                $bcc_post = stripslashes($_POST['rac_template_blind_carbon_copy']);
                $subject_post = stripslashes($_POST['rac_template_subject']);
                $anchor_text_post = stripslashes($_POST['rac_template_anchor_text']);
                $message_post = stripslashes($_POST['rac_content']); //remove backslashes when data retrieved from a database or from an HTML form.
                $message_post = wpautop($message_post); //add HTML P tag on message for Email to create Empty Sapce.
                $mail_template_id_post = $_POST['post_ID'];
                $date = date_i18n(rac_date_format());
                $time = date_i18n(rac_time_format());
                $cart_url = rac_get_page_permalink_dependencies('cart');
                $email_old_template_id = get_post_meta($mail_template_id_post, 'rac_old_template_id', true);
                $urltoclick = esc_url_raw(add_query_arg(array('abandon_cart' => '00', 'email_template' => $mail_template_id_post), $cart_url));
                $url_to_click = apply_filters( 'fp_rac_redirect_url', $urltoclick );
                $link_options = get_option('rac_cart_link_options');
                if ($link_options == '1') {
                    $url_to_click = '<a style="color:#' . get_option("rac_email_link_color") . '"  href="' . $url_to_click . '">' . fp_get_wpml_text('rac_template_' . $email_old_template_id . '_anchor_text', 'en', $anchor_text_post) . '</a>';
                } elseif ($link_options == '2') {
                    $url_to_click = $url_to_click;
                } elseif ($link_options == '3') {
                    $cart_Text = fp_get_wpml_text('rac_template_' . $email_old_template_id . '_anchor_text', 'en', $anchor_text_post);
                    $url_to_click = rac_cart_link_button_mode($url_to_click, $cart_Text);
                } else {
                    $cart_Text = fp_get_wpml_text('rac_template_' . $email_old_template_id . '_anchor_text', 'en', $anchor_text_post);
                    $url_to_click = rac_cart_link_image_mode($url_to_click, $cart_Text);
                }

                require_once RAC_PLUGIN_PATH . '/templates/email-table-css.php';

                $tablecheckproduct = FP_RAC_Polish_Product_Info::fp_rac_extract_cart_details(false, true);
                $find_array = array('{rac.cartlink}', '{rac.date}', '{rac.time}', '{rac.firstname}', '{rac.lastname}', '{rac.Productinfo}', '{rac.coupon}');
                $replace_array = array($url_to_click, $date, $time, 'First Name', 'Last Name', $tablecheckproduct, 'testcoupo.n1234567890');
                $message_post = str_replace($find_array, $replace_array, $message_post);
                $message_post = rac_unsubscription_shortcode($to, $message_post);
                add_filter('woocommerce_email_footer_text', 'rac_footer_email_customization');
                $message_post = do_shortcode($message_post); //shortcode feature

                if ($mail_logo_added == '') {
                    $logo = '';
                } else {
                    $logo = '<table><tr><td align="center" valign="top"><p style="margin-top:0;"><img style="max-height:600px;max-width:600px;" src="' . esc_url($mail_logo_added) . '" /></p></td></tr></table>';
                }
                // woocommerce template
                $subject_post = shortcode_in_subject('First Name', 'Last Name', $subject_post);
                $compact = array($sender_option_post, $from_name_post, $from_email_post);
                $headers = rac_format_email_headers($compact, $bcc_post);
                $woo_temp_msg = email_woocommerce_html($mail_template_post, $subject_post, $message_post, $logo);
                if (rac_send_mail($to, $subject_post, $woo_temp_msg, $headers, $mail_template_post, $compact)) {
                    echo 1;
                }
                exit();
            }
        }

    }

    FP_RAC_Email_Template_Test::init();
}