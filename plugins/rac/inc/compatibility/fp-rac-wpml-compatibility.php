<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}



add_action('admin_init', 'rac_register_template_for_wpml');

function rac_register_template_for_wpml() {

    if (function_exists('icl_register_string')) {
        $context = 'RAC';
        $arg = array('posts_per_page' => -1, 'post_status' => array('racactive', 'racinactive'), 'post_type' => 'racemailtemplate', 'fields' => 'ids');
        $email_templates = fp_rac_check_query_having_posts($arg);
        if (rac_check_is_array($email_templates)) {
            foreach ($email_templates as $email_post) {
                $each_template = fp_rac_create_email_template_obj($email_post);
                $register_array = array(
                    'rac_template_' . $each_template->old_id . '_message' => $each_template->message,
                    'rac_template_' . $each_template->old_id . '_subject' => $each_template->subject,
                    'rac_template_' . $each_template->old_id . '_anchor_text' => $each_template->anchor_text,
                    'rac_template_product_name' => get_option('rac_product_info_product_name'),
                    'rac_template_product_image' => get_option('rac_product_info_product_image'),
                    'rac_template_product_quantity' => get_option('rac_product_info_quantity'),
                    'rac_template_product_price' => get_option('rac_product_info_product_price'),
                    'rac_template_subtotal' => get_option('rac_product_info_subtotal'),
                    'rac_template_shipping' => get_option('rac_product_info_shipping'),
                    'rac_template_tax' => get_option('rac_product_info_tax'),
                    'rac_template_total' => get_option('rac_product_info_total'),
                    'rac_template_subject_customization' => get_option('rac_subject_product_shrotcode_customize'),
                );
                if (rac_check_is_array($register_array)) {
                    foreach ($register_array as $name => $value) {
                        icl_register_string($context, $name, $value); //for registering template String
                    }
                }
            }
        }
    }
}

//For WPML
function fp_get_wpml_text($option_name, $language, $message, $context = 'RAC') {
    $translated = '';
    if (function_exists('icl_register_string')) {
        if ($language == '') {
            return $message;
        } else {
            global $wpdb;
            $res = $wpdb->get_results($wpdb->prepare("
            SELECT s.name, s.value, t.value AS translation_value, t.status
            FROM  {$wpdb->prefix}icl_strings s
            LEFT JOIN {$wpdb->prefix}icl_string_translations t ON s.id = t.string_id
            WHERE s.context = %s
                AND (t.language = %s OR t.language IS NULL)
            ", $context, $language), ARRAY_A);
            if (rac_check_is_array($res)) {
                foreach ($res as $each_entry) {
                    if ($each_entry['name'] == $option_name) {
                        if ($each_entry['translation_value']) {
                            $translated = $each_entry['translation_value'];
                        } else {
                            $translated = $each_entry['value'];
                        }
                    }
                }
            }
            return $translated ? $translated : $message;
        }
    } else {
        return $message;
    }
}

function fp_rac_wpml_convert_url($url, $lan = null) {
    if (class_exists('SitePress')) {
        global $sitepress;
        $lang_change_url = $sitepress->convert_url($url, $lan);
        return $lang_change_url;
    }
    return $url;
}
