<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Shortcode_Tab')) {

    /**
     * FP_RAC_Shortcode_Tab Class.
     */
    class FP_RAC_Shortcode_Tab {

        public static function init() {
            add_action('woocommerce_fprac_settings_tabs_fpracshortocode', array(__CLASS__, 'fp_rac_admin_setting_shortcode'));
            add_action('woocommerce_admin_field_rac_display_shorcode_sections', array(__CLASS__, 'rac_display_shortcode_sections'));
        }

        public static function fp_rac_menu_options_shortcode() {
            return apply_filters('woocommerce_fpracshortocode_settings', array(
                array(
                    'type' => 'rac_display_shorcode_sections'
                ),
            ));
        }

        public static function rac_display_shortcode_sections() {
            $shortcodes_info = array(
                "{rac.cartlink}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Abandoned cart can be loaded using this link from email", "recoverabandoncart")),
                "{rac.date}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Shows abandoned cart date", "recoverabandoncart")),
                "{rac.time}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Shows abandoned cart time", "recoverabandoncart")),
                "{rac.productname}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Displays the product name in the email subject", "recoverabandoncart")),
                "{rac.firstname}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Shows receiver first name", "recoverabandoncart")),
                "{rac.lastname}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Shows receiver last name", "recoverabandoncart")),
                "{rac.recovered_order_id}" => array("mail" => __("Admin order recovered notification email", "recoverabandoncart"),
                    "usage" => __("Order ID can be inserted in the admin notification email for reference", "recoverabandoncart")),
                "{rac.order_line_items}" => array("mail" => __("Admin order line items in recovered notification email", "recoverabandoncart"),
                    "usage" => __("Order line items will be displayed in admin notification email for information", "recoverabandoncart")),
                "{rac.Productinfo}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Shows product info table", "recoverabandoncart")),
                "{rac.coupon}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Coupon code will be generated automatically and included in the email with a coupon options based on the settings from Coupon In Email tab", "recoverabandoncart")),
                "{rac.unsubscribe}" => array("mail" => __("Abandoned cart email", "recoverabandoncart"),
                    "usage" => __("Shows unsubscribe link", "recoverabandoncart")),
                "{rac.unsubscribe_email_manual}" => array("mail" => __("Pages", "recoverabandoncart"),
                    "usage" => __("Manual unsubscription of abandon cart emails done in this page", "recoverabandoncart")
            ));
            ?>
            <table class="rac_shortcodes_info">
                <thead>
                    <tr>
                        <th>
                            <?php _e('Shortcode', 'recoverabandoncart'); ?>
                        </th>
                        <th>
                            <?php _e('Context where Shortcode is valid', 'recoverabandoncart'); ?>
                        </th>
                        <th>
                            <?php _e('Purpose', 'recoverabandoncart'); ?>
                        </th>
                    </tr>
                </thead>
                <?php
                if (rac_check_is_array($shortcodes_info)) {
                    foreach ($shortcodes_info as $shortcode => $s_info) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $shortcode; ?>
                            </td>
                            <td>
                                <?php echo $s_info['mail']; ?>
                            </td>
                            <td>
                                <?php echo $s_info['usage']; ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
            <style type="text/css">
                .rac_shortcodes_info{
                    margin-top:20px;
                }
            </style>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    var count = $('.rac_shortcodes_info tbody tr:not(.footable-filtered)').length;
                    $('.rac_shortcodes_info').footable();
                    $('.rac_shortcodes_info').data('page-size', count);
                    $('.rac_shortcodes_info').trigger('footable_initialized');
                })
            </script>
            <?php
        }

        public static function fp_rac_admin_setting_shortcode() {
            woocommerce_admin_fields(FP_RAC_Shortcode_Tab::fp_rac_menu_options_shortcode());
        }

    }

    FP_RAC_Shortcode_Tab::init();
}