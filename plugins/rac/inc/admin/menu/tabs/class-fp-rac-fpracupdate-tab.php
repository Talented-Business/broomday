<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Previous_Orders_Tab')) {

    /**
     * FP_RAC_Previous_Orders_Tab Class.
     */
    class FP_RAC_Previous_Orders_Tab {

        public static function init() {
            add_action('woocommerce_fprac_settings_tabs_fpracupdate', array(__CLASS__, 'fp_rac_admin_setting_previous_orders'));
            add_action('woocommerce_admin_field_rac_display_check_previous_orders', array(__CLASS__, 'rac_display_check_previous_orders'));
        }

        public static function fp_rac_menu_options_previous_orders() {
            return apply_filters('woocommerce_fpracemail_settings', array(
                array(
                    'type' => 'rac_display_check_previous_orders',
                )
                    )
            );
        }

        public static function fp_rac_admin_setting_previous_orders() {
            woocommerce_admin_fields(FP_RAC_Previous_Orders_Tab::fp_rac_menu_options_previous_orders());
        }

        public static function rac_display_check_previous_orders() {

            include_once RAC_PLUGIN_PATH . '/inc/fp-rac-previous-order.php';
            ?> 
            <table class="form-table">
                <tr>
                    <th><?php _e('Add Old WooCommerce Orders to Cart List which are in', 'recoverabandoncart'); ?></th>
                    <td>
                        <p><input type = "checkbox" name = "order_status[]" value = "wc-on-hold"><?php _e("On hold status", "recoverabandoncart") ?></p>
                        <p><input type = "checkbox" name = "order_status[]" value = "wc-pending"><?php _e("Pending payment status", "recoverabandoncart"); ?></p>
                        <p><input type = "checkbox" name = "order_status[]" value = "wc-failed" checked><?php _e("Failed status", "recoverabandoncart"); ?></p>
                        <p><input type = "checkbox" name = "order_status[]" value = "wc-cancelled"><?php _e("Cancelled status", "recoverabandoncart"); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Time Duration', 'recoverabandoncart'); ?></th>
                    <td>
                        <select id = "order_time">
                            <option value = "all"><?php _e("All time", "recoverabandoncart"); ?></option>
                            <option value = "specific"><?php _e("Specific", "recoverabandoncart"); ?></option>
                        </select>
                    </td>
                </tr>
                <tr style = "display: none" id = "specific_row">
                    <th><?php _e("Specific Time", "recoverabandoncart"); ?></th>
                    <td>
                        <label><?php _e("From", "recoverabandoncart"); ?></label> 
                        <input type = "text" name = "from_date" id = "from_time" class = "rac_date"> 
                        <label><?php _e("To", "recoverabandoncart"); ?></label> 
                        <input type = "text" id = "to_time" name = "to_date" class = "rac_date">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type = "button" class = "button button-primary" name = "update_order" id = "update_order" value = "<?php _e("Check for Abandoned Cart", "recoverabandoncart"); ?>">
                    </td>
                    <td>
                        <img style = "width: 30px;height: 30px;display: none;" class = "perloader_image" src = "<?php echo RAC_PLUGIN_URL ?>/assets/images/update.gif"/>
                        <p id = "update_response"></p>
                    </td>
                </tr>
            </table>
            <?php
        }

    }

    FP_RAC_Previous_Orders_Tab::init();
}