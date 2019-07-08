<?php
/*
 * 
 * Common Functions for multi select
 * 
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function rac_common_function_to_multi_select_for_customer_search($id, $label) {
    global $woocommerce;
    ?>
    <script type="text/javascript">
    <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>
            jQuery(function () {
                jQuery('#<?php echo $id; ?>').chosen();
                jQuery('select#<?php echo $id; ?>').ajaxChosen({
                    method: 'GET',
                    url: '<?php echo RAC_ADMIN_AJAX_URL; ?>',
                    dataType: 'json',
                    afterTypeDelay: 100,
                    data: {
                        action: 'woocommerce_json_search_customers',
                        security: '<?php echo wp_create_nonce("search-customers"); ?>'
                    }
                }, function (data) {
                    var terms = {};
                    jQuery.each(data, function (i, val) {
                        terms[i] = val;
                    });
                    return terms;
                });
            });
    <?php } ?>
    </script>


    <?php if ((float) $woocommerce->version <= (float) ('2.2.0') || (float) $woocommerce->version >= (float) ('3.0.0')) { ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="<?php echo $id; ?>"><?php _e($label, 'recoverabandoncart'); ?></label>
            </th>
            <td>
                <select name="<?php echo $id; ?>[]" multiple="multiple" id="<?php echo $id; ?>" class="wc-customer-search" data-placeholder="<?php _e('Search for a customer&hellip;', 'recoverabandoncart'); ?>" data-minimum_input_length='3' style="width:250px" data-allow_clear="true">
                    <?php
                    $json_ids = array();
                    $getuser = get_option($id);
                    if ($getuser != "") {
                        $listofuser = $getuser;
                        if (!is_array($listofuser)) {
                            $userids = array_filter(array_map('absint', (array) explode(',', $listofuser)));
                        } else {
                            $userids = $listofuser;
                        }

                        foreach ($userids as $userid) {
                            $user = get_user_by('id', $userid);
                            ?>
                            <option value="<?php echo $userid; ?>" selected="selected"><?php echo esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
    <?php } else { ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="<?php echo $id; ?>"><?php _e($label, 'recoverabandoncart'); ?></label>
            </th>
            <td>
                <input type="hidden" class="wc-customer-search" name="<?php echo $id; ?>" id="<?php echo $id; ?>" data-multiple="true" data-placeholder="<?php _e('Search for a customer&hellip;', 'recoverabandoncart'); ?>" data-selected="<?php
                $json_ids = array();
                $getuser = get_option($id);
                if ($getuser != "") {
                    $listofuser = $getuser;
                    if (!is_array($listofuser)) {
                        $userids = array_filter(array_map('absint', (array) explode(',', $listofuser)));
                    } else {
                        $userids = $listofuser;
                    }

                    foreach ($userids as $userid) {
                        $user = get_user_by('id', $userid);
                        if (is_object($user)) {
                            $json_ids[$user->ID] = esc_html($user->display_name) . ' (#' . absint($user->ID) . ' &ndash; ' . esc_html($user->user_email);
                        }
                    }echo esc_attr(json_encode($json_ids));
                }
                ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" data-allow_clear="true" />
            </td>
        </tr>
        <?php
    }
}

function rac_common_function_to_multi_select_for_product_search($id, $label) {
    global $woocommerce;
    ?>
    <script type="text/javascript">
    <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>
            jQuery(function () {
                jQuery('#<?php echo $id; ?>').chosen();
                jQuery("select#<?php echo $id; ?>").ajaxChosen({
                    method: 'GET',
                    url: '<?php echo RAC_ADMIN_AJAX_URL; ?>',
                    dataType: 'json',
                    afterTypeDelay: 100,
                    data: {
                        action: 'woocommerce_json_search_products_and_variations',
                        security: '<?php echo wp_create_nonce("search-products"); ?>'
                    }
                }, function (data) {
                    var terms = {};
                    jQuery.each(data, function (i, val) {
                        terms[i] = val;
                    });
                    return terms;
                });
            });

    <?php } ?>
    </script>
    <?php
    if ((float) $woocommerce->version > (float) ('2.2.0') && (float) $woocommerce->version < (float) ('3.0.0')) {
        ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="<?php echo $id; ?>"><?php _e($label, 'recoverabandoncart'); ?></label>
            </th>
            <td class="forminp forminp-select">
                <input type="hidden" class="wc-product-search" style="width: 350px;" id="<?php echo $id; ?>"  name="<?php echo $id; ?>" data-placeholder="<?php _e('Search for a product&hellip;', 'recoverabandoncart'); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="<?php
                $json_ids = array();
                $list_of_produts = get_option($id);
                if ($list_of_produts != "") {
                    if (!is_array($list_of_produts)) {
                        $product_ids = array_filter(array_map('absint', (array) explode(',', $list_of_produts)));
                    } else {
                        $product_ids = $list_of_produts;
                    }

                    foreach ($product_ids as $product_id) {
                        $product = fp_rac_get_product($product_id);
                        if (is_object($product)) {
                            $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());
                        }
                    } echo esc_attr(json_encode($json_ids));
                }
                ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" />
            </td>
        </tr>
    <?php } else { ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="<?php echo $id; ?>"><?php _e($label, 'recoverabandoncart'); ?></label>
            </th>
            <td class="forminp forminp-select">
                <select multiple name="<?php echo $id; ?>[]" style='width:350px;' id='<?php echo $id; ?>' class="wc-product-search" data-minimum_input_length='3' data-placeholder="<?php _e('Search for a product&hellip;', 'recoverabandoncart'); ?>" data-action="woocommerce_json_search_products_and_variations">
                    <?php
                    $selected_products_include = array_filter((array) get_option($id));
                    if ($selected_products_include != "") {
                        if (!empty($selected_products_include)) {
                            $list_of_produts = (array) get_option($id);
                            if (rac_check_is_array($list_of_produts)) {
                                foreach ($list_of_produts as $rs_free_id) {
                                    echo '<option value="' . $rs_free_id . '" ';
                                    selected(1, 1);
                                    echo '>' . ' #' . $rs_free_id . ' &ndash; ' . get_the_title($rs_free_id);
                                }
                            }
                        }
                    } else {
                        ?>
                        <option value=""></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <?php
    }
}

function fp_rac_common_function_for_search_products($id_name, $label, $options = false) {
    ob_start();
    global $woocommerce;
    ?>
    <script type="text/javascript">
    <?php if ((float) $woocommerce->version <= (float) ('2.2.0')) { ?>
            jQuery(function () {
                jQuery('#<?php echo $id_name; ?>').chosen();
                jQuery('select#<?php echo $id_name; ?>').ajaxChosen({
                    method: 'GET',
                    url: '<?php echo RAC_ADMIN_AJAX_URL; ?>',
                    dataType: 'json',
                    afterTypeDelay: 100,
                    data: {
                        action: 'woocommerce_json_search_products_and_variations',
                        security: '<?php echo wp_create_nonce("search-products"); ?>'
                    }
                }, function (data) {
                    var terms = {};
                    jQuery.each(data, function (i, val) {
                        terms[i] = val;
                    });
                    return terms;
                });
            });
    <?php } ?>
    </script><?php
    if ((float) $woocommerce->version > (float) ('2.2.0') && (float) $woocommerce->version < (float) ('3.0.0')) {
        ?>
        <?php if ($label) { ?>
            <label><?php _e($label, 'recoverabandoncart'); ?>:</label> 
        <?php } ?>
        <input type="hidden" class="wc-product-search" style="width: 300px;" id="<?php echo $id_name; ?>"  name="<?php echo $id_name; ?>[]" data-placeholder="<?php _e('Search for a product&hellip;', 'recoverabandoncart'); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="<?php
        $json_ids = array();
        $list_of_produts = ($options) ? $options : get_option($id_name);
        if ($list_of_produts != "") {
            if (!is_array($list_of_produts)) {
                $product_ids = array_filter(array_map('absint', (array) explode(',', $list_of_produts)));
            } else {
                $product_ids = $list_of_produts;
            }

            foreach ($product_ids as $product_id) {
                $product = fp_rac_get_product($product_id);
                if (is_object($product)) {
                    $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());
                }
            } echo esc_attr(json_encode($json_ids));
        }
        ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" />
           <?php } else { ?>
               <?php if ($label) { ?>
            <label><?php _e($label, 'recoverabandoncart'); ?>:</label> 
        <?php } ?>
        <select multiple name="<?php echo $id_name; ?>[]" style='width:300px;' id='<?php echo $id_name; ?>' class="wc-product-search" data-minimum_input_length='3' data-placeholder="<?php _e('Search for a product&hellip;', 'recoverabandoncart'); ?>" data-action="woocommerce_json_search_products_and_variations">
            <?php
            $list_of_produts = ($options) ? $options : get_option($id_name);
            if (rac_check_is_array($list_of_produts)) {
                foreach ($list_of_produts as $rac_free_id) {
                    echo '<option value="' . $rac_free_id . '" ';
                    echo selected(1, 1);
                    echo '>' . ' #' . $rac_free_id . ' &ndash; ' . get_the_title($rac_free_id) . '</option>';
                }
            } else {
                ?>
                <option value=""></option>
                <?php
            }
            ?>
        </select>                    
        <?php
    }
    return ob_get_clean();
}
