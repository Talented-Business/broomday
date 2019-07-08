<?php
/**
 * Admin Cart List Custom Post Type.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Cartlist_Table')) {

    /**
     * FP_RAC_Cartlist_Table Class.
     */
    class FP_RAC_Cartlist_Table {

        /**
         * FP_RAC_Cartlist_Table Class initialization.
         */
        public static function init() {

            add_action('posts_join', array(__CLASS__, 'fp_rac_post_inner_join_wordpress'), 10, 2);
            add_action('posts_orderby', array(__CLASS__, 'fp_rac_post_orderby_functionality'), 10, 2);
            add_action('admin_action_rac-export-csv', array(__CLASS__, 'fp_rac_cartlist_export_csv'));
            add_action('restrict_manage_posts', array(__CLASS__, 'fp_rac_add_cartlist_filter_option'));
            add_action('posts_distinct', array(__CLASS__, 'fp_rac_post_distinct_functionality'), 10, 2);
            add_action('admin_action_rac-update-status', array(__CLASS__, 'fp_rac_update_cart_list'));
            add_action('manage_posts_extra_tablenav', array(__CLASS__, 'fp_rac_manage_posts_extra_table'));
            add_action('posts_where', array(__CLASS__, 'fp_rac_pre_get_posts_sorting_functionality'), 10, 2);
            add_action('posts_where', array(__CLASS__, 'fp_rac_subscribe_emails_filter'), 10, 2);
            add_action('views_edit-raccartlist', array(__CLASS__, 'remove_post_type_views'));
            add_action('admin_action_rac-send-email-cartlist', array(__CLASS__, 'fp_rac_send_all_cartlist'));
            add_action('admin_action_rac_display_cart_details', array(__CLASS__, 'fp_rac_display_cart_details'));
            add_action('admin_action_rac-delete-cartlist', array(__CLASS__, 'fp_rac_move_all_cartlist_to_trash'));
            add_action('admin_action_rac_send_single_cart_email', array(__CLASS__, 'fp_rac_send_each_row_cart_email'));
            add_action('manage_raccartlist_posts_custom_column', array(__CLASS__, 'fp_rac_display_cartlist_table_data'), 10, 2);

            add_filter('parse_query', array(__CLASS__, 'fp_rac_cartlist_filters_query'));
            add_filter('posts_search', array(__CLASS__, 'fp_rac_cartlist_search_fields'));
            add_filter('post_row_actions', array(__CLASS__, 'fp_rac_cartlist_post_row_actions'), 10, 2);
            add_filter('bulk_post_updated_messages', array(__CLASS__, 'fp_rac_update_cartlist_status'), 10, 2);
            add_filter('manage_raccartlist_posts_columns', array(__CLASS__, 'fp_rac_initialize_cartlist_columns'));
            add_filter('bulk_actions-edit-raccartlist', array(__CLASS__, 'fp_rac_cartlist_bulk_post_actions'), 10, 1);
            add_filter('manage_edit-raccartlist_sortable_columns', array(__CLASS__, 'fp_rac_cartlist_sortable_columns'));
            add_filter('handle_bulk_actions-edit-raccartlist', array(__CLASS__, 'fp_rac_bulk_actions_functionality'), 10, 3);
        }

        /**
         * Initialization of columns in cart list table
         */
        public static function fp_rac_initialize_cartlist_columns($columns) {
            $columns = array(
                'cb' => $columns['cb'],
                'id' => __('ID', 'recoverabandoncart'),
                'rac_cart_details' => __('Cart Details / Cart Total', 'recoverabandoncart'),
                'rac_user_details' => __('User Name / First Last Name', 'recoverabandoncart'),
                'rac_cart_email_id' => __('Email ID / Phone Number', 'recoverabandoncart'),
                'rac_cart_abandoned_time' => __('Abandoned Date / Time', 'recoverabandoncart'),
                'rac_cart_status' => __('Status', 'recoverabandoncart'),
                'rac_cart_email_template_id' => __('Email Template / Email Status / Cart Link in Email', 'recoverabandoncart'),
                'rac_recovered_order_id' => __('Recovered Order ID', 'recoverabandoncart'),
                'rac_coupon_details' => __('Coupon Used', 'recoverabandoncart'),
                'rac_payment_details' => __('Payment Status', 'recoverabandoncart'),
                'cart_email_status' => __('Email Status', 'recoverabandoncart'),
            );
            return $columns;
        }

        /**
         * Initialization of sortable columns in cart list table
         */
        public static function fp_rac_cartlist_sortable_columns($columns) {
            $array = array(
                'id' => 'ID',
                'rac_cart_email_id' => 'rac_cart_email_id',
                'rac_cart_abandoned_time' => 'rac_cart_abandoned_time',
                'rac_cart_status' => 'post_status',
                'rac_recovered_order_id' => 'rac_recovered_order_id',
                'rac_payment_details' => 'rac_payment_details',
            );
            return wp_parse_args($array, $columns);
        }

        /*
         * Remove Custom Post Type Views
         */

        public static function remove_post_type_views($views) {

            unset($views['mine']);
            return $views;
        }

        /**
         * Display each column data in cart list table
         */
        public static function fp_rac_display_cartlist_table_data($column, $postid) {
            $cart_list = fp_rac_create_cart_list_obj($postid);
            switch ($column) {
                case 'id':
                    echo '#' . $postid;
                    break;
                case 'rac_cart_details':

                    echo self::fp_rac_display_cart_list_details_column($cart_list);
                    break;
                case 'rac_user_details':
                    echo self::fp_rac_display_cart_list_user_details_column($cart_list);
                    break;
                case 'rac_cart_email_id':
                    echo self::fp_rac_display_cart_list_user_email_column($cart_list);
                    break;
                case 'rac_cart_abandoned_time':
                    echo self::fp_rac_display_cart_list_abandon_time_column($cart_list);
                    break;
                case 'rac_cart_status':
                    echo self::fp_rac_display_cart_list_status_column($cart_list);
                    break;
                case 'rac_cart_email_template_id':
                    echo self::fp_rac_display_cart_list_email_template_column($cart_list);
                    break;
                case 'rac_recovered_order_id':
                    echo self::fp_rac_display_cart_list_recovered_orderid_column($cart_list);
                    break;
                case 'rac_coupon_details':
                    echo self::fp_rac_display_cart_list_coupon_status_column($cart_list);
                    break;
                case 'rac_payment_details':
                    echo self::fp_rac_display_cart_list_payment_status_column($cart_list);
                    break;
                case 'cart_email_status':
                    echo self::fp_rac_display_cart_list_mail_sending_column($cart_list);
                    break;
            }
        }

        /**
         * Modify Bulk post actions in cart list table
         */
        public static function fp_rac_cartlist_bulk_post_actions($actions) {
            global $post;

            if ($post->post_type != 'raccartlist')
                return $actions;

            //which is used to table allignment
            ?>
            <style>
                table.fixed{
                    table-layout:auto !important;
                }
            </style>
            <?php
            $extra_actions = array();
            if ($post->post_status != 'trash') {
                $extra_actions = array(
                    'rac-send' => 'Send Manual Email(s)',
                    'rac-start-emailstatus' => 'Start Automatic Email(s)',
                    'rac-stop-emailstatus' => 'Stop Automatic Email(s)'
                );
            }
            unset($actions['edit']);
            $actions = array_merge($extra_actions, $actions);

            return $actions;
        }

        /**
         * Adding extra filter in cart list table.
         */
        public static function fp_rac_add_cartlist_filter_option($post_type) {
            if ($post_type == 'raccartlist') {
                //display tag filter for cart list table 
                $unsubcribe_count = count(self::fp_rac_subcribe_email_count('IN'));
                $subcribe_count = count(self::fp_rac_subcribe_email_count());
                $selected_value = isset($_REQUEST['fprac_cartlist_tag']) ? $_REQUEST['fprac_cartlist_tag'] : '';
                ?><select name="fprac_cartlist_tag">
                    <option value='' <?php selected($selected_value, ''); ?>><?php _e('All', 'recoverabandoncart') ?></option>
                    <option value = 'subscribe' <?php selected($selected_value, 'subscribe'); ?> ><?php echo __('Subscribed', 'recoverabandoncart') . ' (' . $subcribe_count . ')'; ?></option>
                    <option value = 'unsubscribe' <?php selected($selected_value, 'unsubscribe'); ?> ><?php echo __('Unsubscribed', 'recoverabandoncart') . ' (' . $unsubcribe_count . ')'; ?></option>
                </select>
                <?php
                //display date filter for cart list table 
                $fromdate = '';
                $todate = '';
                if (isset($_REQUEST['filter_action'])) {
                    $fromdate = isset($_REQUEST['rac_cartlist_fromdate']) ? $_REQUEST['rac_cartlist_fromdate'] : "";
                    $todate = isset($_REQUEST['rac_cartlist_todate']) ? $_REQUEST['rac_cartlist_todate'] : "";
                }
                ?>
                <input id='rac_from_date' placeholder=<?php _e('From Date', 'recoverabandoncart'); ?> type='text' name='rac_cartlist_fromdate' value="<?php echo $fromdate; ?>"/>
                <input id='rac_to_date' type='text' name='rac_cartlist_todate' value="<?php echo $todate; ?>" placeholder=<?php _e('To Date', 'recoverabandoncart'); ?>/>
                <?php
                //which is used to Filter Allignment
                ?>
                <style>
                    .tablenav .alignleft{
                        clear:both;
                    }
                </style>
                <?php
            }
        }

        private static function fp_rac_subcribe_email_count($in = 'NOT IN') {
            global $wpdb;
            $unsubscribe_emails = self::fp_rac_get_unsub_email_array();
            $cart_list_emails = implode("','", $unsubscribe_emails);
            $unsub_emails = $wpdb->get_results("SELECT p.ID FROM  {$wpdb->posts} AS p "
                    . "INNER JOIN {$wpdb->postmeta} AS meta ON p.ID = meta.post_id "
                    . "WHERE p.post_type='raccartlist' AND "
                    . "p.post_status IN ('rac-cart-new','rac-cart-abandon','rac-cart-recovered') "
                    . "AND meta.meta_key='rac_cart_email_id' "
                    . "AND meta_value $in('$cart_list_emails')", ARRAY_A);

            $unsub_emails = fp_rac_array_column_function($unsub_emails, 'fp_rac_array_map_post_ids', 'ID');

            return $unsub_emails;
        }

        private static function fp_rac_get_unsub_email_array() {
            global $wpdb;
            $guest_unsub_emails = array_filter(array_unique((array) get_option('fp_rac_mail_unsubscribed')));
            $member_unsub_emails = $wpdb->get_results("SELECT users.user_email FROM  {$wpdb->users} AS users "
                    . "LEFT JOIN {$wpdb->usermeta} AS meta ON users.ID = meta.user_id "
                    . "WHERE meta.meta_key='fp_rac_mail_unsubscribed' "
                    . "AND meta_value='yes'", ARRAY_A);

            $member_unsub_emails = fp_rac_array_column_function($member_unsub_emails);
            $unsub_email_array = array_merge($member_unsub_emails, $guest_unsub_emails);
            return $unsub_email_array;
        }

        /**
         * Adding Extra action in cart list table
         */
        public static function fp_rac_manage_posts_extra_table($which) {
            global $post;
            if (($which === 'top' ) && (((is_object($post) && $post->post_type == 'raccartlist')) || (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'raccartlist'))) {
                $button_name = isset($_GET['post_status']) && $_GET['post_status'] == 'trash' ? 'Restore all Cart Lists' : 'Move all Cart Lists to Trash';
                $query_arg = isset($_GET['post_status']) ? '&post_status=' . $_GET['post_status'] : '';
                $admin_url = admin_url('edit.php?post_type=raccartlist' . $query_arg);
                $export_url = wp_nonce_url(esc_url_raw(add_query_arg(array('action' => 'rac-export-csv'), $admin_url)), 'rac-exportcsv');
                $update_url = wp_nonce_url(esc_url_raw(add_query_arg(array('action' => 'rac-update-status'), $admin_url)), 'rac-update-status');
                $delete_url = wp_nonce_url(esc_url_raw(add_query_arg(array('action' => 'rac-delete-cartlist'), $admin_url)), 'rac-delete-cartlist');
                $send_url = wp_nonce_url(esc_url_raw(add_query_arg(array('action' => 'rac-send-email-cartlist'), $admin_url)), 'rac-send-email');

                if (!isset($_GET['post_status']) || $_GET['post_status'] != 'trash') {
                    ?>
                    <a href="<?php echo $send_url; ?>" class="button-primary"><?php _e('Send Manual Email for all Carts', 'recoverabandoncart') ?></a><?php
                }
                ?>
                <a href="<?php echo $delete_url; ?>" class="button-primary"><?php _e($button_name, 'recoverabandoncart') ?></a><?php
                if (!isset($_GET['post_status']) || $_GET['post_status'] != 'trash') {
                    ?>
                    <a href="<?php echo $export_url; ?>" class="button-primary"><?php _e('Export as CSV', 'recoverabandoncart') ?></a><?php
                }
                if (get_option('rac_troubleshoot_update_cart_list_status_manual') == 'yes') {
                    ?>
                    <a href="<?php echo $update_url; ?>" class="button-primary"><?php _e('Update Status', 'recoverabandoncart') ?></a>

                    <?php
                }
            }
        }

        /**
         * Modify Row post actions in cart list table
         */
        public static function fp_rac_cartlist_post_row_actions($actions, $post) {
            if ($post->post_type == 'raccartlist') {
                $post_status = get_post_status($post->ID);
                $send_url = wp_nonce_url(admin_url('post.php?post=raccartlist&action=rac_send_single_cart_email&amp;post=' . $post->ID), 'rac-send-email-' . $post->ID);
                $send_link = '<a href="' . $send_url . '" title="' . esc_attr__('Send Manual Cart Recovery Email', 'recoverabandoncart') . '">' . __('Send Email', 'recoverabandoncart') . '</a>';
                if ($post->post_status != 'trash') {
                    $actions['rac-cartlist-send-email'] = $send_link;
                }
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);
            }
            return $actions;
        }

        /**
         * Update the Cart list status.
         */
        public static function fp_rac_update_cartlist_status($messages, $count) {
            if (isset($_GET['post_type']) && $_GET['post_type'] == 'raccartlist') {
                // FOR ALL USER STATUS - - UPDATE ONLY
                //Members
                fp_rac_update_cartlist_status('member');
                //guest
                fp_rac_update_cartlist_status('guest');
                // FOR ALL USER STATUS - UPDATE ONLY END
            }
            return $messages;
        }

        public static function fp_rac_subscribe_emails_filter($where, $wp_query) {
            global $pagenow, $wpdb;

            if ('edit.php' != $pagenow || !isset($_REQUEST['fprac_cartlist_tag']) || 'raccartlist' != $wp_query->query['post_type'])
                return $where;

            if (!empty($_REQUEST['fprac_cartlist_tag'])) {

                if (($_REQUEST['fprac_cartlist_tag'] == 'unsubscribe')) {
                    $emails = self::fp_rac_subcribe_email_count('IN');
                } else {
                    $emails = self::fp_rac_subcribe_email_count();
                }
                $post_ids = array_filter(array_unique(array_map('absint', $emails)));

                $where .= " AND $wpdb->posts.ID IN (" . implode(',', $post_ids) . ")";
            }

            return $where;
        }

        /**
         *  Searching Functionality
         */
        public static function fp_rac_cartlist_search_fields($where) {
            global $pagenow, $wpdb, $wp;

            if ('edit.php' != $pagenow || !is_search() || !isset($wp->query_vars['s']) || 'raccartlist' != $wp->query_vars['post_type'])
                return $where;

            $search_ids = array();
            $terms = explode(',', $wp->query_vars['s']);

            foreach ($terms as $term) {
                $term = $wpdb->esc_like(wc_clean($term));
                $meta_array = array(
                    'rac_recovered_order_id',
                    'rac_payment_details',
                    'rac_cart_email_id',
                    'rac_user_info',
                    'rac_phone_number',
                    'rac_coupon_details',
                );
                $implode_array = implode("','", $meta_array);
                if (isset($_GET['post_status']) && $_GET['post_status'] != 'all') {
                    $post_status = $_GET['post_status'];
                } else {
                    $post_status_array = array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered');
                    $post_status = implode("','", $post_status_array);
                }
                $user_table = $wpdb->base_prefix . 'users';
                $usermeta_table = $wpdb->base_prefix . 'usermeta';
                $post_meta_table = $wpdb->base_prefix . 'postmeta';
                $product_search = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT pm.post_id 
                                 FROM {$wpdb->postmeta} as pm 
                                 INNER JOIN {$wpdb->posts} as p
                                 ON FIND_IN_SET(p.ID, pm.meta_value) 
                                 WHERE pm.meta_key=%s AND p.post_type=%s 
                                 AND p.post_title LIKE %s", 'rac_product_details', 'product', '%' . $term . '%'));

                $user_displayname_search = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT pm.post_id 
                                 FROM $post_meta_table as pm 
                                 INNER JOIN $user_table as user ON pm.meta_value=user.ID 
                                 WHERE pm.meta_key=%s AND user.user_login LIKE %s", 'rac_user_info', '%' . $term . '%'));

                $user_name_search = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT pm.post_id 
                                 FROM $post_meta_table as pm 
                                 INNER JOIN $usermeta_table as user_meta ON pm.meta_value=user_meta.user_id 
                                 WHERE pm.meta_key=%s AND user_meta.meta_key IN(%s,%s) AND user_meta.meta_value LIKE %s", 'rac_user_info', 'first_name', 'last_name', '%' . $term . '%'));

                $search_ids = $wpdb->get_col($wpdb->prepare(
                                "SELECT DISTINCT ID FROM "
                                . "{$wpdb->posts} as p INNER JOIN {$wpdb->postmeta} as pm "
                                . "ON p.ID = pm.post_id "
                                . "WHERE (p.post_status IN ('$post_status')) AND (p.ID LIKE %s "
                                . "OR p.post_title LIKE %s "
                                . "OR p.post_content LIKE %s "
                                . "OR (pm.meta_key IN ('$implode_array') "
                                . "AND pm.meta_value LIKE %s))", '%' . $term . '%', '%' . $term . '%', '%' . $term . '%', '%' . $term . '%'));
            }
            $search_ids = array_merge($search_ids, $product_search, $user_displayname_search, $user_name_search);
            $search_ids = array_filter(array_unique(array_map('absint', $search_ids)));
            if (sizeof($search_ids) > 0) {
                $where = str_replace('AND (((', "AND ( ({$wpdb->posts}.ID IN (" . implode(',', $search_ids) . ")) OR ((", $where);
            }

            return $where;
        }

        /**
         *  Sorting Functionality
         */
        public static function fp_rac_cartlist_filters_query($query) {
            if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'raccartlist' && 'raccartlist' == $query->query['post_type']) {
                if (isset($_GET['orderby'])) {
                    $excerpt_array = array('ID', 'rac_cart_status');
                    if (!in_array($_GET['orderby'], $excerpt_array))
                        $query->query_vars['meta_key'] = $_GET['orderby'];
                }
            }
        }

        /**
         *  Inner Join Functionality
         */
        public static function fp_rac_post_inner_join_wordpress($join, $wp_query) {
            global $wp;
            if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'raccartlist')
                return $join;

            if ((isset($_REQUEST['filter_action']) && $_REQUEST['post_type'] == 'raccartlist') && empty($join)) {
                global $wpdb;
                $table_name = $wpdb->prefix . "posts";
                $another_table = $wpdb->prefix . "postmeta";
                $join .= " INNER JOIN $another_table ON ($table_name.ID = $another_table.post_id)";
            }
            return $join;
        }

        /**
         *  Distinct Functionality
         */
        public static function fp_rac_post_distinct_functionality($distinct, $wp_query) {
            global $wp;
            if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'raccartlist')
                return $distinct;

            if (isset($_REQUEST['filter_action']) && $_REQUEST['post_type'] == 'raccartlist') {
                if (empty($distinct)) {
                    $distinct .= 'DISTINCT';
                }
            }
            return $distinct;
        }

        /**
         *  Orderby Functionality
         */
        public static function fp_rac_post_orderby_functionality($order_by, $wp_query) {
            if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'raccartlist')
                return $order_by;

            if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'raccartlist') {
                global $wpdb;
                if (!isset($_REQUEST['order']) && !isset($_REQUEST['orderby'])) {
                    $order = fp_rac_backward_compatibility_for_table_sorting('rac_display_cart_list_basedon_asc_desc');
                    $order_by = "{$wpdb->posts}.ID " . $order;
                } else {
                    $decimal_column = array(
                        'rac_cart_abandoned_time',
                        'rac_recovered_order_id',
                    );
                    if (in_array($_REQUEST['orderby'], $decimal_column)) {
                        $order_by = "CAST({$wpdb->postmeta}.meta_value AS DECIMAL) " . $_REQUEST['order'];
                    }
                }
            }
            return $order_by;
        }

        /**
         *  Sorting Functionality
         */
        public static function fp_rac_pre_get_posts_sorting_functionality($where, $wp_query) {
            global $wpdb;
            if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'raccartlist')
                return $where;

            if (isset($_REQUEST['filter_action']) && $_REQUEST['post_type'] == 'raccartlist') {
                $fromdate = isset($_REQUEST['rac_cartlist_fromdate']) ? $_REQUEST['rac_cartlist_fromdate'] : null;
                $todate = isset($_REQUEST['rac_cartlist_todate']) ? $_REQUEST['rac_cartlist_todate'] : null;
                if ($fromdate) {
                    $from_strtotime = strtotime($fromdate);
                    $fromdate = date('Y-m-d', $from_strtotime) . " 00:00:00";
                    $where .= " AND $wpdb->posts.post_date >= '$fromdate'";
                }
                if ($todate) {
                    $to_strtotime = strtotime($todate);
                    $todate = date('Y-m-d', $to_strtotime) . " 23:59:59";
                    $where .= " AND $wpdb->posts.post_date <= '$todate'";
                }
            }
            return $where;
        }

        /**
         *  Bulk actions functionality.
         */
        public static function fp_rac_bulk_actions_functionality($url, $action, $post_ids) {
            global $wpdb;
            if ($action == 'rac-send') {
                self::fp_rac_send_cartlist($post_ids, 'post_ids', true);
            } elseif ($action == 'rac-start-emailstatus' || $action == 'rac-stop-emailstatus') {
                $status = $action == 'rac-start-emailstatus' ? "SEND" : "DON'T";
                $wpdb->query($wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value=%s WHERE meta_key='rac_cart_sending_status' AND post_id IN(" . implode(',', $post_ids) . ")", $status));
            }
            return $url;
        }

        /**
         *  Update Status by Update status button
         */
        public static function fp_rac_update_cart_list() {
            check_admin_referer('rac-update-status');
            // FOR ALL USER STATUS - - UPDATE ONLY
            //Members
            fp_rac_update_cartlist_status('member');
            //guest
            fp_rac_update_cartlist_status('guest');
            // FOR ALL USER STATUS - UPDATE ONLY END

            $url = esc_url_raw($_SERVER['HTTP_REFERER']);
            wp_redirect($url);
            exit();
        }

        /**
         *  Delete and Restore the cart list.
         */
        public static function fp_rac_move_all_cartlist_to_trash() {
            check_admin_referer('rac-delete-cartlist');
            $trash = 0;
            if (isset($_GET['post_status'])) {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'raccartlist',
                    'post_status' => $_GET['post_status'],
                    'fields' => 'ids'
                );
                $move = $_GET['post_status'] == 'trash' ? 1 : 2;
            } else {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'raccartlist',
                    'post_status' => array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered'),
                    'fields' => 'ids'
                );
                $move = 2;
            }

            $posts = fp_rac_check_query_having_posts($args);
            if (rac_check_is_array($posts)) {
                foreach ($posts as $post_id) {
                    if ($move == 1) {
                        if (!wp_untrash_post($post_id))
                            wp_die(__('Error in moving to Trash.'));
                    }else {
                        if (!wp_trash_post($post_id))
                            wp_die(__('Error in moving to Trash.'));
                    }

                    $trash ++;
                }
            }
            $url = esc_url_raw($_SERVER['HTTP_REFERER']);
            wp_redirect($url);
            exit();
        }

        /**
         *  Display Cart Product Details in New Window
         */
        public static function fp_rac_display_cart_details() {
            if (isset($_GET['extend_cart'])) {
                $cart_id = $_GET['extend_cart'];
                check_admin_referer('rac-cartlist-' . $cart_id);
                $cart_list = fp_rac_create_cart_list_obj($cart_id);
                echo '<div>';
                echo FP_RAC_Polish_Product_Info::fp_rac_extract_cart_details($cart_list, false);
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $url = $_SERVER['HTTP_REFERER'];
                } else {
                    $url = esc_url_raw(add_query_arg(array('post_type' => 'raccartlist'), admin_url('edit.php')));
                }
                echo '<br><a href="' . $url . '" style="text-decoration:none"><input class="button-primary" type="button" value="' . __('Back to Cart List', 'recoverabandoncart') . '"</a>';
                echo '</div>';
            }
        }

        /**
         *  Send email to each cart list.
         */
        public static function fp_rac_send_each_row_cart_email() {
            if (empty($_REQUEST['post']))
                wp_die(__('No Cart List to send email!', 'woocommerce'));

            // Get the original page
            $id = isset($_REQUEST['post']) ? absint($_REQUEST['post']) : '';

            check_admin_referer('rac-send-email-' . $id);

            $id = absint($id);
            if (!$id)
                return false;

            self::fp_rac_send_cartlist($id);
        }

        /**
         *  Sending Email to all Cart list manually.
         */
        public static function fp_rac_send_all_cartlist() {
            check_admin_referer('rac-send-email');
            if (isset($_GET['post_status']) && ($_GET['post_status'] != 'trash')) {
                $post_status = $_GET['post_status'];
            } else {
                $post_status = array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered');
            }
            self::fp_rac_send_cartlist($post_status, 'rac_post_status');
        }

        public static function fp_rac_send_cartlist($post_ids, $post_name = 'post_ids', $redirect = false) {
            $templateid = false;
            $template_ids = fp_rac_get_template_ids();
            if (rac_check_is_array($template_ids)) {
                $templateid = (int) $template_ids[0];
            }
            if (!$templateid && !$redirect) {
                wp_redirect($_SERVER['HTTP_REFERER']);
                exit();
            }
            if ($templateid) {
                $query = http_build_query(array($post_name => $post_ids));
                $admin_url = admin_url('post.php?post=' . $templateid . '&action=edit&rac_send_email=yes&' . $query);
                wp_redirect($admin_url);
                exit();
            }
        }

        /**
         *  Export Cart list Data as csv file
         */
        public static function fp_rac_cartlist_export_csv() {
            check_admin_referer('rac-exportcsv');
            if (isset($_GET['post_status']) && ($_GET['post_status'] != 'trash')) {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'raccartlist',
                    'post_status' => $_GET['post_status']
                );
            } else {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'raccartlist',
                    'post_status' => array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered'),
                );
            }


            $posts = fp_rac_check_query_having_posts($args);
            $array = array();
            if (rac_check_is_array($posts)) {
                foreach ($posts as $post) {
                    $obj_cart_lists = fp_rac_create_cart_list_obj($post->ID);
                    $cart_lists = (array) $obj_cart_lists;
                    $export_default = array(
                        'id' => false,
                        'cart_details' => false,
                        'user_id' => false,
                        'email_id' => false,
                        'cart_abandon_time' => false,
                        'cart_status' => false,
                        'mail_template_id' => false,
                        'ip_address' => false,
                        'link_status' => false,
                        'sending_status' => false,
                        'wpml_lang' => false,
                        'placed_order' => false,
                        'completed' => false
                    );
                    $cart_lists = array_merge($export_default, $cart_lists);
                    $new_array = array();
                    if (rac_check_is_array($cart_lists)) {
                        foreach ($cart_lists as $key => $cart_list) {
                            if (isset($export_default[$key])) {
                                if ($key == 'cart_status') {
                                    $new_array[$key] = fp_rac_get_cart_status_name($cart_list);
                                } elseif ($key != 'cart_details') {
                                    if ($key != 'mail_template_sending_time' && $key != 'cart_link_clicked_time_log' && $key != 'currency_code') {
                                        $new_array[$key] = $cart_list;
                                    }
                                } else {
                                    ob_start();
                                    $product_details = fp_rac_cart_details($obj_cart_lists);
                                    $products = ($product_details != 'no data') ? $product_details : _e('Product Details not Available', 'recoverabandoncart');
                                    echo $products;
                                    $string = ob_get_clean();
                                    $string1 = str_replace(' ', '', html_entity_decode(strip_tags($string)));
                                    $new_array[$key] = $string1;
                                }
                            }
                        }
                        $new_array = apply_filters('fp_rac_export_custom_row', $new_array, $cart_lists);
                    }
                    array_push($array, $new_array);
                }
            }
            ob_end_clean();
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=rac_cartlist" . date_i18n("Y-m-d H:i:s") . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $output = fopen("php://output", 'w');
            $delimiter = ',';
            $delimiter = apply_filters('fp_rac_export_delimiter', $delimiter);
            $enclosure = '"';
            $enclosure = apply_filters('fp_rac_export_enclosure', $enclosure);
            $row_heading = array('id', 'cart_details', 'user_id', 'email_id', 'cart_abandon_time', 'cart_status', 'mail_template_id', 'ip_address', 'link_status', 'sending_status', 'wpml_lang', 'placed_order', 'completed');
            $row_heading = apply_filters('fp_rac_export_headings', $row_heading);
            fputcsv($output, $row_heading, $delimiter, $enclosure); // here you can change delimiter/enclosure
            foreach ($array as $row) {
                $row = apply_filters('fp_rac_export_row', $row);
                fputcsv($output, $row, $delimiter, $enclosure); // here you can change delimiter/enclosure
            }
            fclose($output);
            exit();
        }

        /**
         * cart list product details 
         */
        public static function fp_rac_display_cart_list_details_column($each_list) {
            ob_start();
            $product_details = fp_rac_cart_details($each_list);
            if ($product_details == 'no data') {
                _e('Product Details not Available', 'recoverabandoncart');
            } else {
                $new_template_url = wp_nonce_url(esc_url_raw(add_query_arg(array('action' => 'rac_display_cart_details', 'extend_cart' => $each_list->id), RAC_ADMIN_URL)), 'rac-cartlist-' . $each_list->id);
                echo '<a style="text-decoration: none;" target="_blank" href="' . $new_template_url . '">' . fp_rac_cart_details($each_list) . '</a>';
            }

            return ob_get_clean();
        }

        /**
         * Cart list User Details.
         */
        public static function fp_rac_display_cart_list_user_details_column($each_list) {
            ob_start();
            $user_info = get_userdata($each_list->user_id);
            if (is_object($user_info)) {
                echo $user_info->user_login;
                echo " / $user_info->user_firstname $user_info->user_lastname";
            } elseif ($each_list->user_id == '0') {
                $cart_array = maybe_unserialize($each_list->cart_details);
                $guest_first_last="";
                if (is_array($cart_array)) {
                    //for cart captured at checkout(GUEST)
                    $first_name = $cart_array['first_name'];
                    $last_name = $cart_array['last_name'];
                    $guest_first_last = " / $first_name $last_name";

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
                    $guest_first_last = " / $cart_array->billing_first_name $cart_array->billing_last_name";
                }
                _e('Guest', 'recoverabandoncart');
                echo $guest_first_last;
            } elseif ($each_list->user_id == 'old_order') {
                $old_order_obj = new FP_RAC_Previous_Order_Data($each_list);
                if ($old_order_obj->get_cart_content()) {
                    $user_id = $old_order_obj->get_user_id();
                    $user_obj = get_userdata($user_id);
                    if (is_object($user_obj)) {
                        echo $user_obj->user_login;
                        echo " / $user_obj->user_firstname $user_obj->user_lastname";
                    } else {
                        $billing_first_name = $old_order_obj->get_billing_firstname();
                        $billing_last_name = $old_order_obj->get_billing_lastname();
                        _e('Guest', 'recoverabandoncart');
                        echo ' / ' . $billing_first_name . ' ' . $billing_last_name;
                    }
                } else {
                    _e('Order Details not Available', 'recoverabandoncart');
                }
            }
            return ob_get_clean();
        }

        /**
         * Cart list User Email and Phone number
         */
        private static function fp_rac_display_cart_list_user_email_column($each_list) {
            ?><style type="text/css">
                .rac_tool_info .tooltip {
                    background: #1496bb;
                    color: #fff;
                    opacity: 0;
                }
            </style>
            <script type='text/javascript'>
                jQuery(function () {
                    jQuery('.rac_tool_info:not(.rac_content_get)').tipTip({'content': 'Double Click here to Edit Email ID for Guest'});
                });</script>
            <?php
            ob_start();
            $guest_email = '';
            $userid = 0;
            if ('0' == $each_list->user_id) {
                $details = maybe_unserialize($each_list->cart_details);
                if (is_object($details)) {
                    ?> <div class="rac_tool_info"><p class="rac_edit_option" data-id="<?php echo $each_list->id; ?>" >
                        <?php
                        echo $details->billing_email; // Order Object. Works for both old order and rac captured order
                        $guest_email = $details->billing_email;
                        ?></p><div class="tooltip"><?php _e("Double Click to Change an Editable", "recoverabandoncart"); ?></div></div><?php
                            echo '</br>&nbsp' . $details->billing_phone;
                        } elseif (is_array($details)) {
                            ?><div class="rac_tool_info"><p class="rac_edit_option" data-id="<?php echo $each_list->id; ?>">
                            <?php
                            echo $details['visitor_mail']; //checkout order
                            $guest_email = $details['visitor_mail'];
                            ?></p><div class="tooltip"><?php _e("Double Click to Change an Editable", "recoverabandoncart"); ?></div></div><?php
                            echo "</br>&nbsp";
                            if (isset($details['visitor_phone'])) {
                                echo $details['visitor_phone'];
                            } else {
                                echo '-';
                            }
                        }
                    } elseif ($each_list->user_id == 'old_order') {
                        $old_order_obj = new FP_RAC_Previous_Order_Data($each_list);
                        if ($old_order_obj->get_cart_content()) {
                            $user_id = $old_order_obj->get_user_id();
                            $user_obj = get_userdata($user_id);
                            if (is_object($user_obj)) {
                                echo $user_obj->user_email;
                                $userid = $user_id;
                                echo '</br> &nbsp' . $user_obj->billing_phone;
                            } else {
                                $billing_email = $old_order_obj->get_billing_email();
                                $guest_email = $billing_email;
                                $phone_number = $old_order_obj->get_billing_phoneno();
                                if (!empty($billing_email)) {
                                    echo $billing_email;
                                    echo '</br> &nbsp' . $phone_number;
                                } else {
                                    echo '-';
                                }
                            }
                        } else {
                            _e('Order Details not Available', 'recoverabandoncart');
                        }
                    } else {
                        $user_infor = get_userdata($each_list->user_id);
                        $userid = $each_list->user_id;
                        if (is_object($user_infor)) {
                            echo $user_infor->user_email;
                            echo '</br> &nbsp' . $user_infor->billing_phone;
                        }
                    }
                    $param = $guest_email ? $guest_email : $userid;
                    $slug = fp_rac_check_email_subscribed($param);
                    if (!$slug) {
                        echo '</br> </br>' . __('Email ID has been Unsubscribed', 'recoverabandoncart');
                        echo '<br><div class="button rac_customer_email_subscribe" data-value="true" data-email_id="' . $guest_email . '" data-user_id="' . $userid . '">' . __("Subscribe", "recoverabandoncart") . '</div>';
                    } else {
                        echo '<br><div class="button rac_customer_email_subscribe" data-value="false" data-email_id="' . $guest_email . '" data-user_id="' . $userid . '">' . __("Unsubscribe", "recoverabandoncart") . '</div>';
                    }

                    return ob_get_clean();
                }

                /**
                 * Cart list abandon time
                 */
                private static function fp_rac_display_cart_list_abandon_time_column($each_list) {
                    ob_start();
                    echo date(get_option('date_format'), $each_list->cart_abandon_time) . '/' . date(get_option('time_format'), $each_list->cart_abandon_time);
                    return ob_get_clean();
                }

                /**
                 * Cart list Status
                 */
                private static function fp_rac_display_cart_list_status_column($each_list) {
                    ob_start();
                    $post_status = get_post_status($each_list->id);
                    $img_src = RAC_PLUGIN_URL . '/assets/images/update.gif';
                    if ($post_status == 'trash') {
                        echo 'Trashed';
                    } else {
                        $post_name = fp_rac_get_cart_status_name($post_status);
                        echo $post_name;
                    }
                    if ($post_status == 'rac-cart-new' || $post_status == 'rac-cart-abandon') {
                        ?>
                <p>
                    <a href="#" class="button rac_manual_recovered" data-racmrid="<?php echo $each_list->id; ?>"><?php _e('Mark as Recovered', 'recoverabandoncart'); ?></a> 
                    <img src='<?php echo $img_src; ?>' target="" id="rac_load_image<?php echo $each_list->id ?>" style="width:40px;display:none">
                </p>
                <?php
            }
            return ob_get_clean();
        }

        /**
         *  Template status for cart list
         */
        private static function fp_rac_display_cart_list_email_template_column($each_list) {
            ob_start();
            $mail_sent = maybe_unserialize($each_list->mail_template_id);
            $arg = array('posts_per_page' => -1, 'post_status' => array('racactive', 'racinactive'), 'post_type' => 'racemailtemplate', 'order' => 'ASC', 'orderby' => 'ID');
            $email_template_all = fp_rac_check_query_having_posts($arg);
            if (rac_check_is_array($email_template_all)) {
                foreach ($email_template_all as $check_all_email_temp) {

                    echo $check_all_email_temp->post_title;
                    //Mail Sent
                    $old_email_id = get_post_meta($check_all_email_temp->ID, 'rac_old_template_id', true);
                    if (!empty($mail_sent)) {
                        if (in_array($old_email_id, (array) $mail_sent)) {
                            echo ' /' . __("Email Sent", "recoverabandoncart") . '';
                        } else {
                            echo ' /' . __("Email Not Sent", "recoverabandoncart") . '';
                        }
                    } else {
                        echo ' /' . __("Email Not Sent", "recoverabandoncart") . '';
                    }
                    //Mail Sent END
                    //Link Clicked
                    if (!empty($each_list->link_status)) {
                        $mails_clicked = maybe_unserialize($each_list->link_status);
                        if (in_array($old_email_id, (array) $mails_clicked)) {
                            echo ' /' . __("Cart Link Clicked", "recoverabandoncart") . '';
                            echo '<br>';
                        } else {
                            echo ' /' . __("Cart Link Not Clicked", "recoverabandoncart") . '';
                            echo '<br>';
                        }
                    } else {
                        echo ' /' . __("Cart Link Not Clicked", "recoverabandoncart") . '';
                        echo '<br>';
                    }
                    //Link Clicked END
                }
            }

            return ob_get_clean();
        }

        /**
         * Cart list recovered order id
         */
        private static function fp_rac_display_cart_list_recovered_orderid_column($each_list) {
            ob_start();
            echo (!empty($each_list->placed_order) ? ' #' . $each_list->placed_order . '' : __('Not Yet', 'recoverabandoncart'));
            return ob_get_clean();
        }

        /**
         * Cart list coupon Status
         */
        private static function fp_rac_display_cart_list_coupon_status_column($each_list) {
            ob_start();
            if ($each_list->cart_status == 'rac-cart-recovered') {
                $coupon_code = get_post_meta($each_list->id, 'rac_cart_coupon_code', true);
                $order = fp_rac_get_order_obj($each_list->placed_order);
                if (!empty($each_list->placed_order)) {
                    if ($order) {
                        $coupons_used = $order->get_used_coupons();
                        if (!empty($coupons_used)) {
                            if (in_array($coupon_code, $order->get_used_coupons())) {
                                echo $coupon_code . ' - ';
                                _e('Success', 'recoverabandoncart');
                            } else {
                                _e('Not Used', 'recoverabandoncart');
                            }
                        } else {
                            _e('Not Used', 'recoverabandoncart');
                        }
                    } else {
                        _e('Order details not available', 'recoverabandoncart');
                    }
                } else {
                    _e('Not Used', 'recoverabandoncart');
                }
            } else {
                _e('Not Yet', 'recoverabandoncart');
            }
            return ob_get_clean();
        }

        /**
         * Cart list Payment Status
         */
        private static function fp_rac_display_cart_list_payment_status_column($each_list) {
            ob_start();
            echo (!empty($each_list->completed) ? __('Completed', 'recoverabandoncart') : __('Not Yet', 'recoverabandoncart'));
            return ob_get_clean();
        }

        /**
         * Cart list email sending button.
         */
        private static function fp_rac_display_cart_list_mail_sending_column($each_list) {
            ob_start();
            if ($each_list->cart_status != 'trash') {
                if (empty($each_list->completed)) {
                    //check if order completed,if completed don't show mail sending button'
                    $status = $each_list->sending_status == 'SEND' ? 'DONT' : 'SEND';
                    ?>
                    <input type="checkbox" class="rac_mail_status_checkboxes" data-racid="<?php echo $each_list->id; ?>" style="border:1px solid black;margin-left:38px"/>
                    <a href="#" class="button rac_mailstatus_check_indi" data-racmoptid="<?php echo $each_list->id; ?>" data-currentsate="<?php echo $status; ?>" disabled="disabled"><?php
                        if ($each_list->sending_status == 'SEND') {
                            _e('Stop Emailing', 'recoverabandoncart');
                        } else {
                            _e('Start Emailing', 'recoverabandoncart');
                        }
                        ?>
                    </a><?php
                } else {
                    _e('Recovered', 'recoverabandoncart');
                }
            } else {
                _e('Trashed', 'recoverabandoncart');
            }
            return ob_get_clean();
        }

    }

    FP_RAC_Cartlist_Table::init();
}
