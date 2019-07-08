<?php
/**
 * Admin Mail Log Custom Post Type.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Maillog_Table')) {

    /**
     * FP_RAC_Maillog_Table Class.
     */
    class FP_RAC_Maillog_Table {

        /**
         * FP_RAC_Maillog_Table Class initialization.
         */
        public static function init() {
            add_action('views_edit-racmaillog', array(__CLASS__, 'remove_post_type_views'));
            add_action('posts_orderby', array(__CLASS__, 'fp_rac_post_orderby_functionality'), 10, 2);
            add_action('restrict_manage_posts', array(__CLASS__, 'fp_rac_add_emaillog_filter_option'));
            add_action('posts_join', array(__CLASS__, 'fp_rac_email_log_post_inner_join_wordpress'), 10, 2);
            add_action('posts_where', array(__CLASS__, 'fp_rac_email_log_posts_sorting_functionality'), 10, 2);
            add_action('posts_distinct', array(__CLASS__, 'fp_rac_email_log_post_distinct_functionality'), 10, 2);
            add_action('admin_action_rac-emaillog-delete', array(__CLASS__, 'fp_rac_move_all_emaillog_to_trash'));
            add_action('manage_posts_extra_tablenav', array(__CLASS__, 'fp_rac_email_log_manage_posts_extra_table'));
            add_action('manage_racmaillog_posts_custom_column', array(__CLASS__, 'fp_rac_display_maillog_table_data'), 10, 2);

            add_filter('posts_search', array(__CLASS__, 'fp_rac_email_log_search'));
            add_filter('parse_query', array(__CLASS__, 'fp_rac_email_log_filters_query'));
            add_filter('post_row_actions', array(__CLASS__, 'fp_rac_maillog_post_row_actions'), 10, 2);
            add_filter('bulk_actions-edit-racmaillog', array(__CLASS__, 'fp_rac_maillog_bulk_post_actions'));
            add_filter('manage_racmaillog_posts_columns', array(__CLASS__, 'fp_rac_initialize_maillog_columns'));
            add_filter('manage_edit-racmaillog_sortable_columns', array(__CLASS__, 'fp_rac_maillog_sortable_columns'));
        }

        /**
         * set the table columns
         */
        public static function fp_rac_initialize_maillog_columns($columns) {
            $columns = array(
                'cb' => $columns['cb'],
                'id' => __('ID', 'recoverabandoncart'),
                'rac_email_id' => __('Email ID', 'recoverabandoncart'),
                'rac_date_time' => __('Date Time', 'recoverabandoncart'),
                'rac_template_used' => __('Template Used', 'recoverabandoncart'),
                'rac_cart_id' => __('Abandon Cart ID', 'recoverabandoncart'),
            );
            return $columns;
        }

        /**
         * set the sortable columns
         */
        public static function fp_rac_maillog_sortable_columns($columns) {
            $array = array(
                'id' => 'ID',
                'rac_email_id' => 'rac_email_id',
                'rac_date_time' => 'rac_date_time',
                'rac_cart_id' => 'rac_cart_id',
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
         * Display the Maillog table data
         */
        public static function fp_rac_display_maillog_table_data($column, $postid) {

            switch ($column) {
                case 'id':
                    echo '#' . $postid;
                    break;
                case 'rac_email_id':
                    $email_id = get_post_meta($postid, 'rac_email_id', true);
                    echo $email_id;
                    break;
                case 'rac_date_time':
                    $date_time = get_post_meta($postid, 'rac_date_time', true);
                    echo date(get_option('date_format'), $date_time) . '/' . date(get_option('time_format'), $date_time);
                    break;
                case 'rac_template_used':
                    $template_used = get_post_meta($postid, 'rac_template_used', true);
                    $manual_mail = strpos($template_used, 'Manual');
                    $manual = false;
                    if ($manual_mail !== false) {
                        $template_id = explode("-", $template_used);
                        $template_id = $template_id[0];
                        $manual = true;
                    } else {
                        $template_id = $template_used;
                    }
                    $template_name = get_post_field('post_title', $template_id);
                    if (!empty($template_name)) {
                        if ($manual)
                            $template_id = $template_used;
                        echo $template_name . ' (#' . $template_id . ')';
                    } else {
                        _e('Template Info not Available', 'recoverabandoncart');
                    }
                    break;
                case 'rac_cart_id':
                    $cart_id = get_post_meta($postid, 'rac_cart_id', true);
                    if ($cart_id) {
                        echo $cart_id;
                    } else {
                        _e('Cart List ID not Available', 'recoverabandoncart');
                    }
                    break;
            }
        }

        /**
         * Update the Bulk post actions
         */
        public static function fp_rac_maillog_bulk_post_actions($action) {
            global $current_screen;
            if (isset($current_screen->post_type)) {
                if ($current_screen->post_type == 'racmaillog') {
                    unset($action['edit']);
                }
            }
            return $action;
        }

        /**
         * Adding extra filter in cart list table.
         */
        public static function fp_rac_add_emaillog_filter_option($post_type) {
            if ($post_type == 'racmaillog') {
                //display date filter for cart list table 
                $fromdate = '';
                $todate = '';
                if (isset($_REQUEST['filter_action'])) {
                    $fromdate = isset($_REQUEST['rac_emaillog_fromdate']) ? $_REQUEST['rac_emaillog_fromdate'] : "";
                    $todate = isset($_REQUEST['rac_emaillog_todate']) ? $_REQUEST['rac_emaillog_todate'] : "";
                }
                ?>
                <input id='rac_from_date' placeholder=<?php _e('From Date', 'recoverabandoncart'); ?> type='text' name='rac_emaillog_fromdate' value="<?php echo $fromdate; ?>"/>
                <input id='rac_to_date' type='text' name='rac_emaillog_todate' value="<?php echo $todate; ?>" placeholder=<?php _e('To Date', 'recoverabandoncart'); ?>/>
                <?php
            }
        }

        /**
         * Adding extra table nav
         */
        public static function fp_rac_email_log_manage_posts_extra_table($which) {
            global $post;
            if (($which === 'top' ) && (((is_object($post) && $post->post_type == 'racmaillog')) || (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'racmaillog'))) {
                $button_name = isset($_GET['post_status']) && $_GET['post_status'] == 'trash' ? 'Restore All Email Logs' : 'Move all Email Logs to Trash';
                $query_arg = isset($_GET['post_status']) ? '&post_status=' . $_GET['post_status'] : '';
                $admin_url = admin_url('edit.php?post_type=racmaillog' . $query_arg);
                $delete_url = wp_nonce_url(esc_url_raw(add_query_arg(array('action' => 'rac-emaillog-delete'), $admin_url)), 'rac-delete-emaillog');
                ?> <a href="<?php echo $delete_url; ?>" class="button-primary"><?php _e($button_name, 'recoverabandoncart') ?></a><?php
            }
        }

        /**
         * Update the post row actions
         */
        public static function fp_rac_maillog_post_row_actions($actions, $post) {
            if ($post->post_type == 'racmaillog') {
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);
            }
            return $actions;
        }

        /**
         * Inner Join Functionality
         */
        public static function fp_rac_email_log_post_inner_join_wordpress($join, $wp_query) {
            if ((isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'racmaillog'))
                return $join;

            if ((isset($_REQUEST['filter_action']) && $_REQUEST['post_type'] == 'racmaillog')) {
                if (empty($join)) {
                    global $wpdb;
                    $table_name = $wpdb->prefix . "posts";
                    $another_table = $wpdb->prefix . "postmeta";
                    $join .= " INNER JOIN $another_table ON ($table_name.ID = $another_table.post_id)";
                }
            }
            return $join;
        }

        /**
         * Distinct Functionality.
         */
        public static function fp_rac_email_log_post_distinct_functionality($distinct, $wp_query) {
            if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'racmaillog')
                return $distinct;

            if (isset($_REQUEST['filter_action']) && $_REQUEST['post_type'] == 'racmaillog') {
                if (empty($distinct)) {
                    $distinct .= 'DISTINCT';
                }
            }
            return $distinct;
        }

        /**
         * Order By Functionality
         */
        public static function fp_rac_post_orderby_functionality($order_by, $wp_query) {
            if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'racmaillog')
                return $order_by;

            if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'racmaillog') {
                global $wpdb;
                if (!isset($_REQUEST['order']) && !isset($_REQUEST['orderby'])) {
                    $order = fp_rac_backward_compatibility_for_table_sorting('rac_display_mail_log_basedon_asc_desc');
                    $order_by = "{$wpdb->posts}.ID " . $order;
                } else {
                    $decimal_column = array(
                        'rac_date_time',
                        'rac_cart_id',
                    );
                    if (in_array($_REQUEST['orderby'], $decimal_column)) {
                        $order_by = "CAST({$wpdb->postmeta}.meta_value AS DECIMAL) " . $_REQUEST['order'];
                    }
                }
            }
            return $order_by;
        }

        /**
         * Date Filter action Functionality
         */
        public static function fp_rac_email_log_posts_sorting_functionality($where, $wp_query) {
            if ((isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'racmaillog'))
                return $where;

            global $wpdb;
            if (isset($_REQUEST['filter_action']) && $_REQUEST['post_type'] == 'racmaillog') {
                $fromdate = isset($_REQUEST['rac_emaillog_fromdate']) ? $_REQUEST['rac_emaillog_fromdate'] : null;
                $todate = isset($_REQUEST['rac_emaillog_todate']) ? $_REQUEST['rac_emaillog_todate'] : null;
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
         * Delete and Restore email log functionality
         */
        public static function fp_rac_move_all_emaillog_to_trash() {
            check_admin_referer('rac-delete-emaillog');
            $trash = 0;
            if (isset($_GET['post_status'])) {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'racmaillog',
                    'post_status' => $_GET['post_status'],
                    'fields' => 'ids'
                );
                $move = $_GET['post_status'] == 'trash' ? 1 : 2;
            } else {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'racmaillog',
                    'post_status' => array('publish'),
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
                }
            }
            $url = esc_url_raw($_SERVER['HTTP_REFERER']);
            wp_redirect($url);
            exit();
        }

        /**
         * Searching Functionality
         */
        public static function fp_rac_email_log_search($where) {
            global $pagenow, $wpdb, $wp;

            if ('edit.php' != $pagenow || !is_search() || !isset($wp->query_vars['s']) || 'racmaillog' != $wp->query_vars['post_type'])
                return $where;


            $search_ids = array();
            $terms = explode(',', $wp->query_vars['s']);

            foreach ($terms as $term) {
                $term = $wpdb->esc_like(wc_clean($term));
                $meta_array = array(
                    'rac_email_id',
                    'rac_date_time',
                    'rac_template_used',
                    'rac_template_status',
                    'rac_cart_id',
                );
                $implode_array = implode("','", $meta_array);
                if (isset($_GET['post_status']) && $_GET['post_status'] != 'all') {
                    $post_status = $_GET['post_status'];
                } else {
                    $post_status_array = array('publish');
                    $post_status = implode("','", $post_status_array);
                }

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
            $search_ids = array_filter(array_unique(array_map('absint', $search_ids)));
            if (sizeof($search_ids) > 0) {
                $where = str_replace('AND (((', "AND ( ({$wpdb->posts}.ID IN (" . implode(',', $search_ids) . ")) OR ((", $where);
            }

            return $where;
        }

        /**
         * Sorting Functionality
         */
        public static function fp_rac_email_log_filters_query($query) {
            global $typenow;

            if (isset($query->query['post_type']) && $query->query['post_type'] == 'racmaillog') {
                if ('racmaillog' == $typenow) {
                    if (isset($_GET['orderby'])) {
                        $excerpt_array = array('ID', 'title', 'post_content');
                        if (!in_array($_GET['orderby'], $excerpt_array))
                            $query->query_vars['meta_key'] = $_GET['orderby'];
                    }
                }
            }
        }

    }

    FP_RAC_Maillog_Table::init();
}