<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Email_Template')) {

    /**
     * FP_RAC_Email_Template Class.
     */
    class FP_RAC_Email_Template {

        public static function fp_rac_prepare_email_template_values($post, $template_type) {
            $editor_id = "rac_email_template_timce";
            $settings = array('textarea_name' => 'rac_email_template_timce');
            $template_list_url = esc_url_raw(add_query_arg(array('page' => 'fprac_slug', 'tab' => 'fpracemail'), RAC_ADMIN_URL));
            $content = "Hi {rac.firstname},<br><br>We noticed you have added the following Products in your Cart, but haven't completed the purchase. {rac.Productinfo}<br><br>We have captured the Cart for your convenience. Please use the following link to complete the purchase {rac.cartlink}<br><br>Thanks.";
            $seg_default_args = array(
                'rac_template_seg_type' => 'rac_template_seg_odrer_count',
                'rac_template_seg_odrer_count_min' => '*',
                'rac_template_seg_odrer_count_max' => '*',
                'rac_template_seg_odrer_amount_min' => '*',
                'rac_template_seg_odrer_amount_max' => '*',
                'rac_template_seg_cart_total_min' => '*',
                'rac_template_seg_cart_total_max' => '*',
                'rac_template_seg_cart_from_date' => '*',
                'rac_template_seg_cart_to_date' => '*',
                'rac_template_seg_cart_quantity_min' => '*',
                'rac_template_seg_cart_quantity_max' => '*',
                'rac_template_seg_selected_user_role' => '',
                'rac_template_seg_cart_product_category' => 'allproduct',
                'rac_template_seg_selected_product_in_cart' => '',
                'rac_template_seg_selected_product_not_in_cart' => '',
                'rac_template_seg_selected_category_not_in_cart' => '',
                'rac_template_seg_selected_category_in_cart' => '',
            );
            if ($template_type != 'new') {
                $id = $post->ID;
                $post_obj = get_post($post->ID);
                $seg_array = get_post_meta($id, 'rac_template_segmentation', true);
                $seg_main_array = wp_parse_args($seg_array, $seg_default_args);
                $template = array(
                    'rac_template_type' => $template_type,
                    'template_name' => $post_obj->post_title,
                    'status' => get_post_meta($id, 'rac_template_status', true),
                    'mail' => get_post_meta($id, 'rac_template_mail', true),
                    'rac_segmentation' => $seg_main_array,
                    'link' => get_post_meta($id, 'rac_template_link', true),
                    'sender_opt' => get_post_meta($id, 'rac_template_sender_opt', true),
                    'from_name' => get_post_meta($id, 'rac_template_from_name', true),
                    'from_email' => get_post_meta($id, 'rac_template_from_email', true),
                    'rac_blind_carbon_copy' => get_post_meta($id, 'rac_template_blind_carbon_copy', true),
                    'subject' => get_post_meta($id, 'rac_template_subject', true),
                    'sending_type' => get_post_meta($id, 'rac_template_sending_type', true),
                    'sending_duration' => get_post_meta($id, 'rac_template_sending_duration', true),
                    'anchor_text' => get_post_meta($id, 'rac_template_anchor_text', true),
                    'message' => $post_obj->post_content,
                    'msg_editorid' => $editor_id,
                    'msg_settings' => $settings,
                    'template_list_url' => $template_list_url,
                );
            } else {
                $template = array();
                $seg_array = array();
            }
            $defalut_args = array(
                'rac_template_type' => $template_type,
                'template_name' => 'Default',
                'status' => 'ACTIVE',
                'mail' => 'HTML',
                'rac_segmentation' => $seg_default_args,
                'link' => RAC_ADMIN_URL,
                'sender_opt' => 'woo',
                'from_name' => 'Admin',
                'from_email' => get_option('admin_email'),
                'rac_blind_carbon_copy' => '',
                'subject' => 'Recover Abandon Cart',
                'sending_type' => 'days',
                'sending_duration' => '1',
                'anchor_text' => 'Cart Link',
                'message' => $content,
                'msg_editorid' => $editor_id,
                'msg_settings' => $settings,
                'template_list_url' => $template_list_url,
            );

            $main_array = wp_parse_args($template, $defalut_args);
            //this is for wc_get_template function extract problem.
            $main_array['rac_template_name'] = $main_array['template_name'];
            unset($main_array['template_name']);

            include_once RAC_PLUGIN_PATH . '/templates/email-template-css.php';

            wc_get_template('email-template/templates.php', $main_array, RAC_PLUGIN_FOLDER_NAME, RAC_PLUGIN_PATH . '/templates/');
        }

        public static function rac_segmentation_select_options($selected_value) {
            $segmentation = array(
                'rac_template_seg_odrer_count' => __("Total No.of Orders", "recoverabandoncart"),
                'rac_template_seg_odrer_amount' => __("Total Amount Spent on Site", "recoverabandoncart"),
                'rac_template_seg_cart_total' => __("Abandoned Cart Total", "recoverabandoncart"),
                'rac_template_seg_cart_date' => __("Cart Abandoned Date", "recoverabandoncart"),
                'rac_template_seg_cart_quantity' => __("Abandoned Cart Quantity", "recoverabandoncart"),
                'rac_template_seg_user_role' => __("User Role(s)", "recoverabandoncart"),
                'rac_template_seg_cart_product' => __("Product(s) in Cart", "recoverabandoncart")
            );

            $seg_option = fp_rac_select_options($segmentation, $selected_value);

            return $seg_option;
        }

        public static function rac_template_type_select_options($selectd_value) {
            $email_template_type = array(
                'HTML' => __("WooCommerce Template", "recoverabandoncart"),
                'PLAIN' => __("HTML Template", "recoverabandoncart")
            );

            $email_template_type = fp_rac_select_options($email_template_type, $selectd_value);

            return $email_template_type;
        }

        public static function rac_seg_product_incart_select_options($selected_value) {
            $email_template_type = array(
                'allproduct' => __("All Products", "recoverabandoncart"),
                'include_product' => __("Include Products", "recoverabandoncart"),
                'exclude_product' => __("Exclude Products", "recoverabandoncart"),
                'allcategory' => __("All Categories", "recoverabandoncart"),
                'include_category' => __("Include Categories", "recoverabandoncart"),
                'exclude_category' => __("Exclude Categories", "recoverabandoncart"),
            );

            $email_template_type = fp_rac_select_options($email_template_type, $selected_value);

            return $email_template_type;
        }

        public static function rac_template_status_select_options($selected_value) {
            $template_status = array(
                'ACTIVE' => __("Activated", "recoverabandoncart"),
                'NOTACTIVE' => __("Deactivated", "recoverabandoncart")
            );
            $template_status_option = fp_rac_select_options($template_status, $selected_value);

            return $template_status_option;
        }

        public static function rac_mail_duration_select_options($selected_value) {
            $send_mail_duration = array(
                'minutes' => __("Minutes", "recoverabandoncart"),
                'hours' => __("Hours", "recoverabandoncart"),
                'days' => __("Days", "recoverabandoncart")
            );
            $duration_type_option = fp_rac_select_options($send_mail_duration, $selected_value);

            return $duration_type_option;
        }

        public static function rac_user_roles_select_options($selected_value) {
            $guest_role = array('rac_guest' => __('Guest', 'woocommerce'));
            $user_roles = fp_rac_user_roles($guest_role);
            $seg_user_select = fp_rac_select_options($user_roles, $selected_value);
            return $seg_user_select;
        }

        public static function rac_category_select_options($selected_value) {
            $category = fp_rac_get_category();
            $seg_category = fp_rac_select_options($category, $selected_value);
            return $seg_category;
        }

        public static function rac_email_template_list_select_options() {
            global $post;
            $option = '';
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'racemailtemplate',
                'post_status' => array('racactive', 'racinactive'),
                'orderby' => 'ID',
                'order' => 'ASC'
            );
            $posts = fp_rac_check_query_having_posts($args);
            if (rac_check_is_array($posts)) {
                foreach ($posts as $key => $each_template) {
                    if ($post->ID == $each_template->ID) {
                        $template_name = $each_template->post_title . '( #' . $each_template->ID . ')';
                        $option .= "<option value=" . $each_template->ID . " selected >" . $template_name . "</option>";
                    } else {
                        $template_name = $each_template->post_title . '( #' . $each_template->ID . ')';
                        $option .= "<option value=" . $each_template->ID . ">" . $template_name . "</option>";
                    }
                }
            }
            return $option;
        }

    }

}