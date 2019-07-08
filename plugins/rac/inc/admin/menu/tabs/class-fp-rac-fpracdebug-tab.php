<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Troubleshoot_tab')) {

    /**
     * FP_RAC_Troubleshoot_tab Class.
     */
    class FP_RAC_Troubleshoot_tab {

        public static function init() {
            add_action('fp_rac_default_settings_fpracdebug', array(__CLASS__, 'fprac_trouble_default_settings'));
            add_action('fp_rac_display_buttons_fpracdebug', array(__CLASS__, 'fp_rac_admin_setting_buttons'));
            add_action('woocommerce_fprac_settings_tabs_fpracdebug', array(__CLASS__, 'fp_rac_admin_setting_troubleshoot'));
            add_action('woocommerce_update_options_fpracdebug', array(__CLASS__, 'fp_rac_update_options_troubleshoot'));
            add_action('woocommerce_admin_field_rac_troubleshoot_update_data', array(__CLASS__, 'rac_troubleshoot_update_data'));
            add_action('woocommerce_admin_field_rac_troubleshoot_extra_sections', array(__CLASS__, 'rac_troubleshoot_extra_sections'));
        }

        public static function fp_rac_menu_options_troubleshoot() {
            $defaultval = "webmaster@" . $_SERVER['SERVER_NAME'];
            return apply_filters('woocommerce_fpracdebug_settings', array(
                array(
                    'name' => __('Troubleshoot Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_troubleshoot',
                ),
                array(
                    'name' => __('Email Function to Use', 'recoverabandoncart'),
                    'desc' => __('Please Select which mail function to use while sending notification', 'recoverabandoncart'),
                    'id' => 'rac_trouble_mail',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array('mail' => 'mail()', 'wp_mail' => 'wp_mail()'),
                    'std' => 'wp_mail',
                    'default' => 'wp_mail',
                    'clone_id' => 'rac_trouble_mail',
                ),
                array(
                    'name' => __('Use Email Troubleshoot', 'recoverabandoncart'),
                    'desc' => __('Please enable this option if you want to send Emails using Fifth Parameter ', 'recoverabandoncart'),
                    'id' => 'rac_webmaster_mail',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array('webmaster1' => __('Enable', 'recoverabandoncart'), 'webmaster2' => __('Disable', 'recoverabandoncart')),
                    'std' => 'webmaster2',
                    'default' => 'webmaster2',
                    'clone_id' => 'rac_webmaster_mail',
                ),
                array(
                    'name' => __('Use Email as Fifth Parameter', 'recoverabandoncart'),
                    'id' => 'rac_textarea_mail',
                    'std' => $defaultval,
                    'default' => $defaultval,
                    'type' => 'text',
                    'newids' => 'rac_textarea_mail',
                ),
                array(
                    'name' => __('MIME Version 1.0 Parameter', 'recoverabandoncart'),
                    'id' => 'rac_mime_mail_header_ts',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array('block' => __('Include', 'recoverabandoncart'), 'none' => __('Exclude', 'recoverabandoncart')),
                    'std' => 'block',
                    'default' => 'block',
                    'clone_id' => 'rac_mime_mail_header_ts',
                ),
                array(
                    'name' => __('Reply-To Parameter', 'recoverabandoncart'),
                    'id' => 'rac_replyto_mail_header_ts',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => true,
                    'options' => array('block' => __('Include', 'recoverabandoncart'), 'none' => __('Exclude', 'recoverabandoncart')),
                    'std' => 'block',
                    'default' => 'block',
                    'clone_id' => 'rac_replyto_mail_header_ts',
                ),
                array('type' => 'sectionend', 'id' => 'rac_troubleshoot'), //Time Settings END
                array(
                    'name' => __('Troubleshoot Performance Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_troubleshoot_performance',
                ),
                array(
                    'name' => __('Load Recover Abandoned Cart Scripts/Styles in', 'recoverabandoncart'),
                    'desc' => __('"Footer of the Site" option is experimental and if your theme doesn\'t contain wp_footer hook then it won\'t work', 'recoverabandoncart'),
                    'id' => 'rac_load_script_styles',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => false,
                    'options' => array('wp_head' => __('Header of the site', 'recoverabandoncart'), 'wp_footer' => __('Footer of the site(Experimental)', 'recoverabandoncart')),
                    'std' => 'wp_head',
                    'default' => 'wp_head',
                    'clone_id' => 'rac_load_script_styles',
                ),
                array('type' => 'sectionend', 'id' => 'rac_troubleshoot_performance'),
                array(
                    'name' => __('Ajax Chunking Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_troubleshoot_ajax_chunking',
                ),
                array(
                    'name' => __('Chunk Count Per Ajax Call', 'recoverabandoncart'),
                    'desc' => __('Applicable for "Check Previous Orders" tab', 'recoverabandoncart'),
                    'id' => 'rac_chunk_count_per_ajax',
                    'std' => 10,
                    'default' => 10,
                    'type' => 'number',
                    'step' => 1,
                    'newids' => 'rac_chunk_count_per_ajax',
                    'desc_tip' => stripslashes("Don't Change the Value unless you need")
                ),
                array('type' => 'sectionend', 'id' => 'rac_troubleshoot_ajax_chunking'),
                array(
                    'name' => __('Cron Troubleshoot Settings', 'recoverabandoncart'),
                    'desc' => '',
                    'type' => 'title',
                    'id' => 'rac_troubleshoot_cron_format',
                ),
                array(
                    'name' => __('What Cron will be Used', 'recoverabandoncart'),
                    'id' => 'rac_cron_troubleshoot_format',
                    'css' => 'min-width:150px;',
                    'type' => 'select',
                    'desc_tip' => false,
                    'options' => array('wp_cron' => __('wp_cron', 'recoverabandoncart'), 'server_cron' => __('server_cron', 'recoverabandoncart')),
                    'std' => 'wp_cron',
                    'default' => 'wp_cron',
                    'clone_id' => 'rac_cron_troubleshoot_format',
                ),
                array('type' => 'sectionend', 'id' => 'rac_troubleshoot_cron_format'),
                array(
                    'name' => __('Abandon Cart List Status Management Settings', 'recoverabandoncart'),
                    'type' => 'title',
                    'id' => 'rac_troubleshoot_status_management',
                ),
                array(
                    'name' => __('Update Status of Captured Carts when Cart List Table is Accessed', 'recoverabandoncart'),
                    'id' => 'rac_troubleshoot_update_cart_list_status_auto',
                    'type' => 'checkbox',
                    'desc_tip' => false,
                    'std' => 'yes',
                    'default' => 'yes',
                    'newids' => 'rac_troubleshoot_update_cart_list_status_auto',
                    'clone_id' => 'rac_troubleshoot_update_cart_list_status_auto',
                ),
                array(
                    'name' => __('Display Update Status Button in Cart List Table for Updating the Captured Carts Status', 'recoverabandoncart'),
                    'id' => 'rac_troubleshoot_update_cart_list_status_manual',
                    'type' => 'checkbox',
                    'desc_tip' => false,
                    'std' => 'no',
                    'default' => 'no',
                    'newids' => 'rac_troubleshoot_update_cart_list_status_manual',
                    'clone_id' => 'rac_troubleshoot_update_cart_list_status_manual',
                ),
                array('type' => 'sectionend', 'id' => 'rac_troubleshoot_status_management'),
                array(
                    'type' => 'rac_troubleshoot_update_data',
                ),
                array(
                    'type' => 'rac_troubleshoot_extra_sections',
                ),
            ));
        }

        public static function fp_rac_admin_setting_troubleshoot() {
            woocommerce_admin_fields(FP_RAC_Troubleshoot_tab::fp_rac_menu_options_troubleshoot());
        }

        public static function fp_rac_update_options_troubleshoot() {
            woocommerce_update_options(FP_RAC_Troubleshoot_tab::fp_rac_menu_options_troubleshoot());
        }

        public static function fprac_trouble_default_settings() {
            $settings = FP_RAC_Troubleshoot_tab::fp_rac_menu_options_troubleshoot();

            if (rac_check_is_array($settings)) {
                foreach ($settings as $setting)
                    if (isset($setting['id']) && isset($setting['std'])) {
                        if (!get_option($setting['id']))
                            add_option($setting['id'], $setting['std']);
                    }
            }
        }

        public static function fp_rac_admin_setting_buttons() {
            ?>
            <span class = "submit" style = "margin-left: 25px;">
                <?php if (!isset($GLOBALS['hide_save_button'])) :
                    ?>
                    <input name="save" class="button-primary" style="margin-top:15px;" type="submit" value="<?php _e('Save', 'recoverabandoncart'); ?>" />
                <?php endif; ?>
                <input type="hidden" name="subtab" id="last_tab" />
                <?php wp_nonce_field('woocommerce-settings'); ?>
            </span>
            <?php
        }

        public static function rac_troubleshoot_update_data() {
            $img_src = RAC_PLUGIN_URL . '/assets/images/update.gif';
            ?><h3><?php _e('Manual Data Updation', 'recoverabandoncart'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><?php _e('Update User Data for Previous Orders', 'recoverabandoncart'); ?></th>
                    <td>
                        <input type="button" id="rac-update-data" class="button button-primary" value=<?php _e('Update', 'recoverabandoncart'); ?>>
                        <img src='<?php echo $img_src; ?>' alt='' id='rac_update_data_img' style="width:35px;display:none"><br>
                        <span id="rac-update-data-msg" style="display:block;margin-top:10px;"> </span>
                    </td>
                </tr>
            </table>
            <?php
        }

        public static function rac_troubleshoot_extra_sections() {
            ?><h3><?php _e('Test Email Settings', 'recoverabandoncart'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><?php _e('Email Format', 'recoverabandoncart'); ?></th>
                    <td>
                        <select name="rac_test_mail_format" id="rac_test_mail_format">
                            <option value="1"><?php _e('Plain Text', 'recoverabandoncart'); ?></option>
                            <option value="2"><?php _e('HTML', 'recoverabandoncart'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Send Test Email to', 'recoverabandoncart'); ?> </th>
                    <td>
                        <input type="text" id="testemailto" name="testemailto" value="">
                        <input type="button" id="senttestmail" class="button button-primary" value=<?php _e('Send Test Email', 'recoverabandoncart'); ?>>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><p id="test_mail_result" style="display:none;"></p></td>
                </tr>
            </table>
            <?php
        }

    }

    FP_RAC_Troubleshoot_tab::init();
}