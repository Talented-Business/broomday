<?php

/*
 * Send Email Via Woocommerce Mailer
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Send_Email_Woocommerce_Mailer')) {

    /**
     * FP_RAC_Send_Email_Woocommerce_Mailer Class.
     */
    class FP_RAC_Send_Email_Woocommerce_Mailer {

        private static $from_email_address;
        private static $from_name;
        public static $sending;

        public static function send_email_via_woocommerce_mailer($to, $subject, $message, $headers, $compact) {
            add_filter('wp_mail_content_type', array('FP_RAC_Send_Email_Woocommerce_Mailer', 'alter_content_type_of_email'), 15, 1);
            if (!empty($compact) && $compact[0] == 'local') {
                self::$from_name = $compact[1];
                self::$from_email_address = $compact[2];
                add_filter('woocommerce_email_from_address', array('FP_RAC_Send_Email_Woocommerce_Mailer', 'alter_from_email_of_woocommerce'), 10, 2);
                add_filter('woocommerce_email_from_name', array('FP_RAC_Send_Email_Woocommerce_Mailer', 'alter_from_name_of_woocommerce'), 10, 2);
                $mailer = WC()->mailer();
                $mailer->send($to, $subject, $message, $headers, '');
                remove_filter('woocommerce_email_from_address', array('FP_RAC_Send_Email_Woocommerce_Mailer', 'alter_from_email_of_woocommerce'), 10, 2);
                remove_filter('woocommerce_email_from_name', array('FP_RAC_Send_Email_Woocommerce_Mailer', 'alter_from_name_of_woocommerce'), 10, 2);
                remove_filter('wp_mail_content_type', array('FP_RAC_Send_Email_Woocommerce_Mailer', 'alter_content_type_of_email'), 10, 2);
                self::$from_email_address = false;
                self::$from_name = false;
            } else {
                $mailer = WC()->mailer();
                $mailer->send($to, $subject, $message, $headers, '');
            }
            remove_filter('wp_mail_content_type', array('FP_RAC_Send_Email_Woocommerce_Mailer', 'alter_content_type_of_email'), 15, 1);
        }

        //alter From Name 
        public static function alter_from_email_of_woocommerce($from_email, $object) {
            $get_email_address = self::$from_email_address;
            if ($get_email_address) {
                return '<' . $get_email_address . '>';
            }
            return $from_email;
        }

        //alter From address
        public static function alter_from_name_of_woocommerce($from_name, $object) {
            $get_from_email_name = self::$from_name;
            if ($get_from_email_name) {
                return $get_from_email_name;
            }
            return $from_name;
        }

        // Return path Header
        public static function fp_rac_phpmailer_init($phpmailerobj) {
            if (get_option('rac_webmaster_mail') == 'webmaster1') {
                $phpmailerobj->Sender = get_option('rac_textarea_mail');
                self::$sending = false;
            }
        }

        // Content type
        public static function alter_content_type_of_email($content_type) {
            return 'text/html';
        }

    }

}