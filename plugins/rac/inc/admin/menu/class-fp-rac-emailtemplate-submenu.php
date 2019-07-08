<?php
/**
 * Admin Email Template Custom Post Type.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_RAC_Email_Template_Table')) {

    /**
     * FP_RAC_Email_Template_Table Class.
     */
    class FP_RAC_Email_Template_Table {

        private static $already_saved = false;

        /**
         * FP_RAC_Email_Template_Table Class initialization.
         */
        public static function init() {

            add_action('admin_init', array(__CLASS__, 'rac_remove_admin_editor'));
            add_action('save_post', array(__CLASS__, 'fp_rac_save_email_template_post'), 10, 2);
            add_action('add_meta_boxes', array(__CLASS__, 'fp_rac_add_meta_box_email_template'));
            add_action('views_edit-racemailtemplate', array(__CLASS__, 'remove_post_type_views'));
            add_action('do_meta_boxes', array(__CLASS__, 'fp_rac_remove_submit_div_metabox'), 10, 3);
            add_action('manage_posts_extra_tablenav', array(__CLASS__, 'fp_rac_add_extra_filter'), 10, 1);
            add_action('admin_action_rac_move_templates', array(__CLASS__, 'fp_rac_move_all_template_to_trash'));
            add_action('admin_action_rac_duplicate_template', array(__CLASS__, 'fp_rac_duplicate_email_template'));
            add_action('manage_racemailtemplate_posts_custom_column', array(__CLASS__, 'fp_rac_display_email_template_table_data'), 10, 2);

            add_filter('posts_search', array(__CLASS__, 'fp_rac_email_template_search'));
            add_filter('parse_query', array(__CLASS__, 'fp_rac_email_template_filters_query'));
            add_action('posts_orderby', array(__CLASS__, 'fp_rac_post_orderby_functionality'), 10, 2);
            add_filter('enter_title_here', array(__CLASS__, 'fp_rac_edit_email_template_title'), 10, 2);
            add_filter('disable_months_dropdown', array(__CLASS__, 'fp_rac_remove_month_dropdown'), 10, 2);
            add_filter('post_row_actions', array(__CLASS__, 'fp_rac_add_emailtemplate_post_row_actions'), 10, 2);
            add_filter('manage_racemailtemplate_posts_columns', array(__CLASS__, 'fp_rac_initialize_email_template_columns'));
            add_filter('handle_bulk_actions-edit-racemailtemplate', array(__CLASS__, 'fp_rac_bulk_actions_functionality'), 10, 3);
            add_filter('manage_edit-racemailtemplate_sortable_columns', array(__CLASS__, 'fp_rac_email_template_sortable_columns'));
            add_filter('bulk_actions-edit-racemailtemplate', array(__CLASS__, 'fp_rac_add_emailtemplate_bulk_post_actions'), 10, 1);
        }

        /**
         * Initialization of columns
         */
        public static function fp_rac_initialize_email_template_columns($columns) {
            $columns = array(
                'cb' => $columns['cb'],
                'id' => __('ID', 'recoverabandoncart'),
                'rac_title' => __('Template Name', 'recoverabandoncart'),
                'rac_template_from_name' => __('From Name', 'recoverabandoncart'),
                'rac_template_from_email' => __('From Email', 'recoverabandoncart'),
                'rac_template_subject' => __('Subject', 'recoverabandoncart'),
                'post_content' => __('Message', 'recoverabandoncart'),
                'rac_template_status' => __('Status', 'recoverabandoncart'),
                'rac_template_email_sent' => __('Email Sent', 'recoverabandoncart'),
                'rac_template_cart_recovered' => __('Carts Recovered', 'recoverabandoncart'),
            );
            return $columns;
        }

        /**
         * Order By Functionality
         */
        public static function fp_rac_post_orderby_functionality($order_by, $wp_query) {
            if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'racemailtemplate')
                return $order_by;

            if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'racemailtemplate') {
                global $wpdb;
                if (!isset($_REQUEST['order']) && !isset($_REQUEST['orderby'])) {
                    $order = fp_rac_backward_compatibility_for_table_sorting('rac_display_template_basedon_asc_desc');
                    $order_by = "{$wpdb->posts}.ID " . $order;
                } else {
                    $decimal_column = array(
                        'rac_template_email_sent',
                        'rac_template_cart_recovered'
                    );
                    if (in_array($_REQUEST['orderby'], $decimal_column)) {
                        $order_by = "CAST({$wpdb->postmeta}.meta_value AS DECIMAL) " . $_REQUEST['order'];
                    }
                }
            }
            return $order_by;
        }

        public static function rac_remove_admin_editor() {
            remove_post_type_support('racemailtemplate', 'title');
            remove_post_type_support('racemailtemplate', 'editor');
        }

        /**
         * Initialization of Sortable columns 
         */
        public static function fp_rac_email_template_sortable_columns($columns) {
            $array = array(
                'id' => 'ID',
                'rac_title' => 'title',
                'rac_template_from_name' => 'rac_template_from_name',
                'rac_template_from_email' => 'rac_template_from_email',
                'rac_template_subject' => 'rac_template_subject',
                'rac_template_email_sent' => 'rac_template_email_sent',
                'rac_template_cart_recovered' => 'rac_template_cart_recovered',
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
         * Display Table Column Data
         */
        public static function fp_rac_display_email_template_table_data($column, $postid) {
            $post_object = get_post($postid);
            switch ($column) {
                case 'id':
                    echo '#' . $postid;
                    break;
                case 'rac_title':
                    echo $post_object->post_title;
                    break;
                case 'rac_template_from_name':
                    if (get_post_meta($postid, 'rac_template_sender_opt', true) == 'local') {
                        $from_name = get_post_meta($postid, 'rac_template_from_name', true);
                    } else {
                        $from_name = get_option('woocommerce_email_from_name');
                    }
                    echo $from_name;
                    break;
                case 'rac_template_from_email':
                    if (get_post_meta($postid, 'rac_template_sender_opt', true) == 'local') {
                        $from_email = get_post_meta($postid, 'rac_template_from_email', true);
                    } else {
                        $from_email = get_option('woocommerce_email_from_address');
                    }
                    echo $from_email;
                    break;
                case 'rac_template_subject':
                    $subject = get_post_meta($postid, 'rac_template_subject', true);
                    echo $subject;
                    break;
                case 'post_content':
                    $message = $post_object->post_content;
                    $message = strip_tags($message);
                    if (strlen($message) > 80) {
                        echo substr($message, 0, 80);
                        echo '.....';
                    } else {
                        echo $message;
                    }
                    break;
                case 'rac_template_status':
                    if ($post_object->post_status == 'racactive') {
                        echo ' <a href="#" class="button rac_mail_active" data-racmailid="' . $postid . '" data-currentstate="ACTIVE">' . __("Deactivate", "recoverabandoncart") . '</a>';
                    } else {
                        echo ' <a href="#" class="button rac_mail_active" data-racmailid="' . $postid . '" data-currentstate="NOTACTIVE">' . __("Activate", "recoverabandoncart") . '</a>';
                    }
                    $img_src = RAC_PLUGIN_URL . '/assets/images/update.gif';
                    echo '<img src=' . $img_src . ' target="" id="rac_load_image' . $postid . '" style="width:40px;display:none">';
                    break;
                case 'rac_template_email_sent':
                    $email_sent_count = get_post_meta($postid, 'rac_template_email_sent', true);
                    echo (float) $email_sent_count;
                    break;
                case 'rac_template_cart_recovered':
                    $cart_recovered_count = get_post_meta($postid, 'rac_template_cart_recovered', true);
                    echo (float) $cart_recovered_count;
                    break;
            }
        }

        /**
         * Modify the Bulk actions
         */
        public static function fp_rac_add_emailtemplate_bulk_post_actions($actions) {
            global $post;

            if ($post->post_type != 'racemailtemplate')
                return $actions;

            $extra_actions = array();
            if ($post->post_status == 'racactive' && isset($_GET['post_status'])) {
                $extra_actions = array('racdeactivate' => 'Deactivate');
            } elseif ($post->post_status == 'racinactive' && isset($_GET['post_status'])) {
                $extra_actions = array('racactivate' => 'Activate');
            } elseif ($post->post_status != 'trash') {
                $extra_actions = array('racactivate' => 'Activate', 'racdeactivate' => 'Deactivate');
            }
            unset($actions['edit']);
            $actions = array_merge($extra_actions, $actions);

            return $actions;
        }

        /**
         * Modify the row actions
         */
        public static function fp_rac_add_emailtemplate_post_row_actions($actions, $post) {
            if ($post->post_type == 'racemailtemplate') {
                $post_status = get_post_status($post->ID); //check whether it is approve/reject/pause
                $view_url = wp_nonce_url(admin_url('?rac_preview_template=preview&post_id=' . $post->ID), 'rac-preview-template');
                $duplicate_url = wp_nonce_url(admin_url('edit.php?post_type=racemailtemplate&action=rac_duplicate_template&amp;post=' . $post->ID), 'rac-duplicate-template_' . $post->ID);
                $duplicate_link = '<a href="' . $duplicate_url . '" title="' . esc_attr__('Make a copy of this email template', 'recoverabandoncart') . '">' . __('Duplicate', 'recoverabandoncart') . '</a>';
                unset($actions['inline hide-if-no-js']);
                if ($post->post_status != 'trash') {
                    $actions['emailtempateview'] = "<a href='$view_url' target='_blank'>View</a>";
                    $actions['emailtemplateduplicate'] = $duplicate_link;
                }
                return apply_filters('racemailtemplate_post_row_management', $actions, $post_status, $view_url, $duplicate_link);
            }
            return $actions;
        }

        /**
         * Adding Extra Filter
         */
        public static function fp_rac_add_extra_filter($which) {
            global $post;

            if (($which === 'top') && (((is_object($post) && $post->post_type == 'racemailtemplate')) || (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'racemailtemplate'))) {
                $value = isset($_GET['post_status']) && $_GET['post_status'] == 'trash' ? 'Restore Email Templates' : 'Move all Email Templates to Trash';
                $query_arg = isset($_GET['post_status']) ? '&post_status=' . $_GET['post_status'] : '';
                $query = 'edit.php?post_type=racemailtemplate&action=rac_move_templates' . $query_arg;
                $admin_url = wp_nonce_url(admin_url($query), 'rac-delete-all-template');
                ?><a href="<?php echo $admin_url; ?>" class="page-title-action button-primary" style="top:3px !important"><?php _e($value, 'recoverabandoncart'); ?></a><?php
            }
        }

        /**
         * Remove month dropdown 
         */
        public static function fp_rac_remove_month_dropdown($bool, $post_type) {
            return $post_type == 'racemailtemplate' ? true : $bool;
        }

        /**
         * Bulk actions Functionality
         */
        public static function fp_rac_bulk_actions_functionality($url, $action, $post_ids) {
            if ($action == 'racactivate' || $action == 'racdeactivate') {
                $status = ($action == 'racactivate') ? 'racactive' : 'racinactive';
                if (rac_check_is_array($_REQUEST['post'])) {
                    $post_ids = array_map('intval', $_REQUEST['post']);
                    foreach ((array) $post_ids as $post_id) {
                        $args = array(
                            'ID' => $post_id,
                            'post_status' => $status
                        );
                        wp_update_post($args);
                    }
                }
            }
            return $url;
        }

        /**
         * Searching Functionality
         */
        public static function fp_rac_email_template_search($where) {
            global $pagenow, $wpdb, $wp;

            if ('edit.php' != $pagenow || !is_search() || !isset($wp->query_vars['s']) || 'racemailtemplate' != $wp->query_vars['post_type'])
                return $where;


            $search_ids = array();
            $terms = explode(',', $wp->query_vars['s']);

            foreach ($terms as $term) {
                $term = $wpdb->esc_like(wc_clean($term));
                $meta_array = array(
                    'rac_template_from_name',
                    'rac_template_from_email',
                    'rac_template_subject',
                    'rac_template_status',
                    'rac_template_email_sent',
                    'rac_template_cart_recovered'
                );
                $implode_array = implode("','", $meta_array);
                if (isset($_GET['post_status']) && $_GET['post_status'] != 'all') {
                    $post_status = $_GET['post_status'];
                } else {
                    $post_status_array = array('racactive', 'racinactive');
                    $post_status = implode("','", $post_status_array);
                }
                $search_other_table_ids = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT pm.post_id FROM "
                                . "{$wpdb->postmeta} as pm "
                                . "WHERE EXISTS("
                                . "SELECT option_id FROM {$wpdb->options} as opt "
                                . "WHERE ((opt.option_name='woocommerce_email_from_address' OR opt.option_name='woocommerce_email_from_name')"
                                . "AND opt.option_value LIKE %s) AND pm.meta_key='rac_template_sender_opt' AND meta_value='woo')", '%' . $term . '%'));

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
            $search_ids = array_merge($search_ids, $search_other_table_ids);
            $search_ids = array_filter(array_unique(array_map('absint', $search_ids)));
            if (sizeof($search_ids) > 0) {
                $where = str_replace('AND (((', "AND ( ({$wpdb->posts}.ID IN (" . implode(',', $search_ids) . ")) OR ((", $where);
            }

            return $where;
        }

        /**
         * Sorting Functionality
         */
        public static function fp_rac_email_template_filters_query($query) {
            global $typenow;

            if (isset($query->query['post_type']) && $query->query['post_type'] == 'racemailtemplate') {
                if (isset($_GET['orderby'])) {
                    $excerpt_array = array('ID', 'title', 'post_content');
                    if (!in_array($_GET['orderby'], $excerpt_array))
                        $query->query_vars['meta_key'] = $_GET['orderby'];
                }
            }
        }

        /**
         * Delete and Restore the Email template
         */
        public static function fp_rac_move_all_template_to_trash() {
            check_admin_referer('rac-delete-all-template');

            $trash = 0;
            if (isset($_GET['post_status'])) {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'racemailtemplate',
                    'post_status' => $_GET['post_status'],
                    'fields' => 'ids'
                );
                $move = $_GET['post_status'] == 'trash' ? 1 : 2;
            } else {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'racemailtemplate',
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

                    $trash++;
                }
            }
            $url = esc_url_raw($_SERVER['HTTP_REFERER']);
            wp_redirect($url);
            exit();
        }

        /**
         * Duplicate Functionality
         */
        public static function fp_rac_duplicate_email_template() {
            if (empty($_REQUEST['post']))
                wp_die(__('No Email Template to duplicate has been supplied!', 'recoverabandoncart'));

            // Get the original page
            $id = isset($_REQUEST['post']) ? absint($_REQUEST['post']) : '';

            check_admin_referer('rac-duplicate-template_' . $id);

            $id = absint($id);
            if (!$id)
                return false;

            $post = get_post($id);

            if (!empty($post)) {
                $new_id = self::fp_rac_create_duplicate_email_template($post);
                // Redirect to the edit screen for the new page
                wp_redirect(admin_url('post.php?action=edit&post=' . $new_id));
                exit;
            } else {
                wp_die(__('Email Template creation failed, could not find original Email Template:', 'recoverabandoncart') . ' ' . $id);
            }
        }

        /**
         * Create a New Template from Exist Template 
         */
        public static function fp_rac_create_duplicate_email_template($post) {
            $arg = array(
                'post_status' => $post->post_status,
                'post_type' => 'racemailtemplate',
                'post_title' => $post->post_title . '-copy',
                'post_content' => $post->post_content,
            );
            $id = wp_insert_post($arg);
            $post_array = array(
                'rac_template_status' => get_post_meta($post->ID, 'rac_template_status', true),
                'rac_template_mail' => get_post_meta($post->ID, 'rac_template_mail', true),
                'rac_old_template_id' => $id,
                'rac_template_link' => get_post_meta($post->ID, 'rac_template_link', true),
                'rac_template_sender_opt' => get_post_meta($post->ID, 'rac_template_sender_opt', true),
                'rac_template_from_name' => get_post_meta($post->ID, 'rac_template_from_name', true),
                'rac_template_from_email' => get_post_meta($post->ID, 'rac_template_from_email', true),
                'rac_template_blind_carbon_copy' => get_post_meta($post->ID, 'rac_template_blind_carbon_copy', true),
                'rac_template_subject' => get_post_meta($post->ID, 'rac_template_subject', true),
                'rac_template_sending_type' => get_post_meta($post->ID, 'rac_template_sending_type', true),
                'rac_template_sending_duration' => get_post_meta($post->ID, 'rac_template_sending_duration', true),
                'rac_template_anchor_text' => get_post_meta($post->ID, 'rac_template_anchor_text', true),
                'rac_template_segmentation' => get_post_meta($post->ID, 'rac_template_segmentation', true),
                'rac_template_coupon' => get_post_meta($post->ID, 'rac_template_coupon', true),
                'rac_template_coupon_mode' => get_post_meta($post->ID, 'rac_template_coupon_mode', true),
            );

            if (rac_check_is_array($post_array)) {
                foreach ($post_array as $name => $value) {
                    update_post_meta($id, $name, $value);
                }
            }
            return $id;
        }

        /**
         * Adding Meta Box
         */
        public static function fp_rac_add_meta_box_email_template($post_type) {
            global $post;
            if (!isset($_GET['rac_send_email'])) {
                add_meta_box('rac_save_button', __('Save', 'recoverabandoncart'), 'FP_RAC_Email_Template_Table::fp_rac_display_email_template_save_meta_box', 'racemailtemplate', 'side', 'high');
            }
            add_meta_box('rac_test_send_mail', __('Send Test Email', 'recoverabandoncart'), 'FP_RAC_Email_Template_Table::fp_rac_display_email_template_send_mail_meta_box', 'racemailtemplate', 'side', 'low');
            add_meta_box('rac_template_layout', __('Email Template', 'recoverabandoncart'), 'FP_RAC_Email_Template_Table::fp_rac_display_email_template_meta_box', 'racemailtemplate', 'normal', 'high');
            add_meta_box('rac_template_coupon', __('Coupon Creation Settings', 'recoverabandoncart'), 'FP_RAC_Email_Template_Table::display_coupon_meta_box', 'racemailtemplate', 'advanced', 'low');
        }

        /**
         * Display Save Meta box
         */
        public static function fp_rac_display_email_template_save_meta_box($post) {

            wp_nonce_field('rac_email_template_save_data', 'rac_email_template_nonce');

            if ($post->post_status == 'racactive' || $post->post_status == 'racinactive') {
                $text = 'Update Email Template';
            } else {
                $text = 'Save Email Template';
            }
            echo get_submit_button(__($text, 'recoverabandoncart'), 'primary large', 'rac_save_post', '', '');
        }

        /**
         * Remove Some Meta Box 
         */
        public static function fp_rac_remove_submit_div_metabox($type, $position, $object) {
            if ($type == 'racemailtemplate') {
                $array_status = array('racactive', 'auto-draft', 'draft', 'racinactive');
                if (in_array($object->post_status, $array_status)) {
                    remove_meta_box('submitdiv', 'racemailtemplate', 'side');
                }
                if ($object->post_status == 'auto-draft')
                    remove_meta_box('rac_test_send_mail', 'racemailtemplate', 'side');
            }
        }

        /**
         * Display Email Template Coupon meta box 
         */
        public static function display_coupon_meta_box($post) {
            $coupon_default_args = array(
                'prefix_type' => 'default',
                'prefix' => '',
                'discount_type' => 'fixed_cart',
                'value' => '',
                'validity' => '',
                'min_amount' => '',
                'max_amount' => '',
                'indivitual_use_only' => 'no',
                'exclude_sale_items' => 'no',
                'allow_free_shipping' => 'no',
                'include_products' => array(),
                'exclude_products' => array(),
                'ínclude_categories' => array(),
                'exclude_categories' => array(),
            );

            $coupon_array = get_post_meta($post->ID, 'rac_template_coupon', true);
            $coupon_values = wp_parse_args($coupon_array, $coupon_default_args);

            $coupon_meta_data = array('template_coupon' => $coupon_values, 'coupon_mode' => get_post_meta($post->ID, 'rac_template_coupon_mode', true));

            wc_get_template('email-template/coupon.php', $coupon_meta_data, RAC_PLUGIN_FOLDER_NAME, RAC_PLUGIN_PATH . '/templates/');
        }

        /**
         * Display Email Template meta box 
         */
        public static function fp_rac_display_email_template_meta_box($post) {
            if ($post->post_status == 'auto-draft') {
                FP_RAC_Email_Template::fp_rac_prepare_email_template_values($post, 'new');
            } else {
                if (isset($_GET['rac_send_email']) && $_GET['rac_send_email'] == 'yes') {
                    FP_RAC_Email_Template::fp_rac_prepare_email_template_values($post, 'send');
                } else {
                    FP_RAC_Email_Template::fp_rac_prepare_email_template_values($post, 'edit');
                }
            }
        }

        /**
         * Display send Email Template meta box 
         */
        public static function fp_rac_display_email_template_send_mail_meta_box($post) {
            global $wpdb;
            if ($post->post_type = 'racemailtemplate' && $post->post_status != 'auto-draft') {
                if (isset($_GET['rac_send_email']) && $_GET['rac_send_email'] == 'yes') {
                    if (isset($_GET['rac_post_status'])) {
                        $post_status = isset($_GET['rac_post_status']) ? $_GET['rac_post_status'] : array('rac-cart-new', 'rac-cart-abandon', 'rac-cart-recovered');
                        $post_status = is_array($post_status) ? implode("','", $post_status) : $post_status;
                        $ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type=%s AND post_status IN('" . $post_status . "')", 'raccartlist'));
                        $ids = implode(',', $ids);
                    } elseif (isset($_GET['post_ids'])) {
                        $ids = is_array($_GET['post_ids']) ? implode(',', $_GET['post_ids']) : $_GET['post_ids'];
                    }
                    ?>
                    <div id='rac_hide_block'>
                        <label><?php _e('Load Message from existing Template', 'recoverabandoncart'); ?></label>
                        <p>
                            <select id='rac_load_mail'>
                                <?php
                                echo FP_RAC_Email_Template::rac_email_template_list_select_options();
                                ?>
                            </select>
                        </p>
                        <div>
                            <input type="hidden" name="rac_cart_row_ids" id="rac_cart_row_ids" value="<?php echo $ids; ?>">
                            <input type='button' class='button button-primary button-large' data-sendby='manual' name='rac_send_cart_list' id='rac_send_cart_list' value='<?php _e('Send Manual Email', 'recoverabandoncart') ?>'>
                            <span id="rac_mail_result" style="display: none;"><?php _e("Mail Sent Successfully", "recoverabandoncart"); ?></span>
                        </div>
                    </div>

                    <?php
                } else {
                    ?>
                    <div id='rac_hide_block'>
                        <div>
                            <label><?php _e("Test Email", "recoverabandoncart") ?> : </label>
                            <input type="email" class='rac_send_test_email_for_this_template'>
                        </div>
                        <p>
                            <input type='button' class='button button-primary button-large' data-sendby='test_mail' name='rac_send_template_preview' id='rac_send_template_preview' value='<?php _e('Send Test', 'recoverabandoncart') ?>'>
                        </p>
                        <p id="rac_test_mail_sent" style="display:none"> <?php _e("Mail Sent", "recoverabandoncart") ?></p>
                        <p class="rac_hide_this_message"><?php _e('Shortcodes replaced by Sample Data', 'recoverabandoncart') ?></p>
                    </div>
                    <?php
                }
            }
        }

        /**
         * Modify Email template Title.
         */
        public static function fp_rac_edit_email_template_title($text, $post) {
            switch ($post->post_type) {
                case 'racemailtemplate' :
                    $text = __('Enter Template Name', 'recoverabandoncart');
                    break;
            }

            return $text;
        }

        /**
         * Save Email Template.
         */
        public static function fp_rac_save_email_template_post($post_id, $post) {
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if (empty($post_id) || empty($post) || self::$already_saved) {
                return;
            }

            // Dont' save meta boxes for revisions or autosaves
            if (defined('DOING_AUTOSAVE') || is_int(wp_is_post_revision($post)) || is_int(wp_is_post_autosave($post))) {
                return;
            }
            // Check the nonce
            if (empty($_POST['rac_email_template_nonce']) || !wp_verify_nonce($_POST['rac_email_template_nonce'], 'rac_email_template_save_data')) {
                return;
            }
            // Check user has permission to edit
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
            // Check post type is product
            if ('racemailtemplate' != $post->post_type && isset($_POST))
                return $post_id;

            self::$already_saved = true;

            $post_array = array(
                'rac_template_status' => true,
                'rac_template_mail' => true,
                'rac_template_link' => true,
                'rac_template_sender_opt' => true,
                'rac_template_from_name' => true,
                'rac_template_from_email' => true,
                'rac_template_blind_carbon_copy' => true,
                'rac_template_subject' => true,
                'rac_template_sending_type' => true,
                'rac_template_sending_duration' => true,
                'rac_template_anchor_text' => true,
                'rac_template_coupon' => true,
                'rac_template_coupon_mode' => true,
                'rac_template_segmentation' => true
            );

            if (rac_check_is_array($post_array)) {
                foreach ($post_array as $post_name => $restrict) {
                    if (isset($_POST[$post_name]) && $restrict)
                        update_post_meta($post_id, $post_name, $_POST[$post_name]);
                }
            }

            $old_id = get_post_meta($post_id, 'rac_old_template_id', true);
            if ($old_id == $post_id || $old_id == '') {
                update_post_meta($post_id, 'rac_old_template_id', $post_id);
            }

            if (isset($_POST['rac_template_status'])) {
                $status = $_POST['rac_template_status'] == 'ACTIVE' ? 'racactive' : 'racinactive';
                $title = $_POST['rac_template_name'];
                $content = $_POST['rac_email_template_timce']; //remove backslashes when data retrieved from a database or from an HTML form.
                $args = array(
                    'ID' => $post_id,
                    'post_status' => $status,
                    'post_title' => $title,
                    'post_content' => $content
                );

                wp_update_post($args);
            }
        }

    }

    FP_RAC_Email_Template_Table::init();
}
