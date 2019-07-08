<?php
/*
 * Send Recovered Mail to admin after order placed by clicked link on email 
 * 
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_ADMIN_Notification_Email')) {

    /**
     * FP_RAC_ADMIN_Notification_Email Class.
     */
    class FP_RAC_ADMIN_Notification_Email {

        public static function fp_rac_mail_admin_cart_recovered($order_id) {

            if (get_option('rac_admin_cart_recovered_noti') == "yes") {
                $to = get_option('rac_admin_email');
                if (fp_rac_common_custom_restrict($to, 'email') && fp_rac_check_email_subscribed($to)) {
                    $subject = get_option('rac_recovered_email_subject');
                    $message = get_option('rac_recovered_email_message');
                    $from_name = get_option('rac_recovered_from_name');
                    $from_email = get_option('rac_recovered_from_email');
                    $sender_opt = get_option('rac_recovered_sender_opt');
                    $compact = array($sender_opt, $from_name, $from_email);
                    $headers = rac_format_email_headers($compact);
                    $html_template = ($sender_opt == "woo") ? 'HTML' : 'PLAIN';
                    $message = str_replace('{rac.recovered_order_id}', $order_id, $message); //replacing shortcode for order id
                    ob_start();
                    $order = fp_rac_get_order_obj($order_id);
                    ?>
                    <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Product', 'woocommerce'); ?></th>
                                <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'woocommerce'); ?></th>
                                <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Price', 'woocommerce'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo fp_rac_get_email_order_item_table($order); ?>
                        </tbody>
                        <tfoot>
                            <?php
                            if ($totals = $order->get_order_item_totals()) {
                                $i = 0;
                                foreach ($totals as $total) {
                                    $i++;
                                    ?><tr>
                                        <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
                                        <td style="text-align:left; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
                                    </tr><?php
                                }
                            }
                            ?>
                        </tfoot>
                    </table>

                    <?php
                    $newdata = ob_get_clean();

                    ob_start();
                    $message = str_replace('{rac.order_line_items}', $newdata, $message);
                    $woo_temp_msg = email_woocommerce_html($html_template, $subject, $message);
                    //send email
                    rac_send_mail($to, $subject, $woo_temp_msg, $headers, $html_template, $compact);
                }
            }
        }

        public static function fp_rac_mail_admin_cart_abandoned($cart_id) {
            if (get_option('rac_admin_cart_abandoned_noti') == "yes") {
                $cart_object = fp_rac_create_cart_list_obj($cart_id);
                $tablecheckproduct = FP_RAC_Polish_Product_Info::fp_rac_extract_cart_details($cart_object, true);
                $user_name = self::fp_rac_display_cart_list_user_name($cart_object);
                $user_email = self::fp_rac_display_cart_list_user_name($cart_object, true);
                $to = get_option('rac_ca_admin_email');
                $subject = get_option('rac_abandoned_email_subject');
                $message = get_option('rac_abandoned_email_message');
                $from_name = get_option('rac_abandoned_from_name');
                $from_email = get_option('rac_abandoned_from_email');
                $sender_opt = get_option('rac_abandoned_sender_opt');
                $compact = array($sender_opt, $from_name, $from_email);
                $headers = rac_format_email_headers($compact);
                $html_template = ($sender_opt == "woo") ? 'HTML' : 'PLAIN';
                $message = str_replace('{rac.abandoned_cart}', $tablecheckproduct, $message);
                $message = str_replace('{rac.abandoned_username}', $user_name, $message);
                $message = str_replace('{rac.abandoned_useremail}', $user_email, $message);
                ob_start();
                $woo_temp_msg = email_woocommerce_html($html_template, $subject, $message);
                //send email
                rac_send_mail($to, $subject, $woo_temp_msg, $headers, $html_template, $compact);
            }
        }

        public static function fp_rac_display_cart_list_user_name($each_list, $email = false) {
            $user_info = get_userdata($each_list->user_id);
            $user_name = '';
            if (is_object($user_info)) {
                $user_name = $email ? $user_info->user_email : $user_info->user_login;
            } elseif ($each_list->user_id == '0') {
                $cart_array = maybe_unserialize($each_list->cart_details);
                if (is_array($cart_array)) {
                    //for cart captured at checkout(GUEST)
                    $first_name = $cart_array['first_name'];
                    $last_name = $cart_array['last_name'];
                    $guest_first_last = $first_name . ' ' . $last_name;

                    unset($cart_array['visitor_mail']);
                    unset($cart_array['first_name']);
                    unset($cart_array['last_name']);
                    if (isset($cart_array['visitor_phone'])) {
                        unset($cart_array['visitor_phone']);
                    }
                    if (isset($cart_array['shipping_details'])) {
                        unset($cart_array['shipping_details']);
                    }
                } elseif (is_object($cart_array)) { // For Guest
                    $guest_first_last = $cart_array->billing_first_name . ' ' . $cart_array->billing_last_name;
                }
                $user_name = $guest_first_last;
                $user_name = str_replace(' ', '', $user_name);
                if (!$user_name || $email) {
                    $details = maybe_unserialize($each_list->cart_details);
                    if (is_object($details)) {
                        $user_name = $details->billing_email;
                    } elseif (is_array($details)) {
                        $user_name = $details['visitor_mail'];
                    }
                }
            }
            return $user_name;
        }

    }

}