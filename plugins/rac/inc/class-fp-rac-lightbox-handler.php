<?php
/**
 * Light Box Handler
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Lightbox_Handler')) {

    /**
     * FP_RAC_Lightbox_Handler Class.
     */
    class FP_RAC_Lightbox_Handler {

        /**
         * FP_RAC_Lightbox_Handler Class initialization.
         */
        public static function init() {
            add_action('wc_ajax_fp_rac_dislay_top_abandoned_products', array(__CLASS__, 'dislay_top_abandoned_products'));
            add_action('wc_ajax_fp_rac_dislay_top_recovered_products', array(__CLASS__, 'dislay_top_recovered_products'));
        }

        /**
         * Display Top Abandoned Product Count
         */
        public static function dislay_top_abandoned_products() {
            global $wpdb;
            $abandoned_products = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT product.ID,count(product.ID) as count FROM {$wpdb->postmeta} as pm 
                                 INNER JOIN {$wpdb->posts} as product ON FIND_IN_SET(product.ID, pm.meta_value) 
                                 INNER JOIN {$wpdb->posts} as cartlist ON cartlist.ID=pm.post_id
                                 WHERE pm.meta_key=%s AND product.post_type=%s AND cartlist.post_type=%s AND cartlist.post_status=%s
                                 GROUP BY product.ID order by count(product.ID) DESC LIMIT 10", 'rac_product_details', 'product', 'raccartlist', 'rac-cart-abandon'), ARRAY_A);
            ?>
            <style>
                .fp_rac_top_abandoned_products{
                    width:100%;
                    border:1px solid #f2f2f2;
                    border-collapse: collapse;
                }

                .fp_rac_top_abandoned_products tr td{
                    padding-top: 10px;
                    padding-bottom: 10px;
                    padding-left:10px;
                    vertical-align: left;
                    border:none !important;
                }
                .fp_rac_top_abandoned_products tr:nth-child(odd){
                    background-color: #f2f2f2;
                }
            </style>
            <table class="fp_rac_top_abandoned_products" cellpadding='2'>
                <tr>
                    <td><?php _e('S.No', 'recoverabandoncart'); ?></td>
                    <td><?php _e('Product Name', 'recoverabandoncart'); ?></td>
                    <td><?php _e('Abandoned Count', 'recoverabandoncart'); ?></td>
                </tr>
                <tbody>
                    <?php foreach ($abandoned_products as $key => $abandoned_product) { ?>
                        <tr>
                            <td>
                                <span> <?php echo $key + 1; ?></span>
                            </td>
                            <td>
                                <span style="font-weight:bold;"> <?php echo get_the_title($abandoned_product['ID']); ?> </span>
                            </td>
                            <td>
                                <span> <?php echo $abandoned_product['count']; ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        }

        /**
         * Display Top Recovered Product Count
         */
        public static function dislay_top_recovered_products() {
            global $wpdb;
            $recovered_products = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT product.ID,count(product.ID) as count FROM {$wpdb->postmeta} as pm 
                                 INNER JOIN {$wpdb->posts} as product ON FIND_IN_SET(product.ID, pm.meta_value) 
                                 INNER JOIN {$wpdb->posts} as cartlist ON cartlist.ID=pm.post_id
                                 WHERE pm.meta_key=%s AND product.post_type=%s AND cartlist.post_type=%s AND cartlist.post_status=%s
                                 GROUP BY product.ID order by count(product.ID) DESC LIMIT 10", 'rac_product_details', 'product', 'racrecoveredorder', 'publish'), ARRAY_A);
            ?>
            <style>
                .fp_rac_top_recovered_products{
                    width:100%;
                    border:1px solid #f2f2f2;
                    border-collapse: collapse;
                }

                .fp_rac_top_recovered_products tr td{
                    padding-top: 10px;
                    padding-bottom: 10px;
                    padding-left:10px;
                    vertical-align: left;
                    border:none !important;
                }
                .fp_rac_top_recovered_products tr:nth-child(odd){
                    background-color: #f2f2f2;
                }
            </style>
            <table class="fp_rac_top_recovered_products" cellpadding='2'>
                <tbody>
                    <tr>
                        <td><?php _e('S.No', 'recoverabandoncart'); ?></td>
                        <td><?php _e('Product Name', 'recoverabandoncart'); ?></td>
                        <td><?php _e('Recovered Count', 'recoverabandoncart'); ?></td>
                    </tr>
                    <?php foreach ($recovered_products as $key => $recovered_product) { ?>
                        <tr>
                            <td>
                                <span> <?php echo $key + 1; ?></span>
                            </td>
                            <td>
                                <span style="font-weight:bold;"> <?php echo get_the_title($recovered_product['ID']); ?> </span>
                            </td>
                            <td>
                                <span> <?php echo $recovered_product['count']; ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        }

    }

    FP_RAC_Lightbox_Handler::init();
}