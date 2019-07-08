<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_RAC_Reports_Tab')) {

    /**
     * FP_RAC_Reports_Tab Class.
     */
    class FP_RAC_Reports_Tab {

        private static $tickSize;
        private static $maillogData;
        private static $recoveredData;
        private static $abandonData;
        private static $minData;
        private static $maxData;

        public static function init() {
            self::fp_rac_display_reports();
        }

        public static function fp_rac_display_reports() {
            self::fp_rac_reports();
            self::rac_get_overall_data();
            self::rac_get_min_max();
            self::rac_add_start_end_data();
            self::fp_rac_display_graph_for_reports();
        }

        public static function fp_rac_reports() {
            global $wpdb;
            if (isset($_POST['rac_clear_reports'])) {
                delete_option('rac_abandoned_count');
                delete_option('rac_mail_count');
                delete_option('rac_link_count');
                delete_option('rac_recovered_count');
                $args = array(
                    'post_type' => 'racrecoveredorder',
                    'post_status' => 'publish',
                    'fields' => 'ids'
                );
                $query = new WP_Query($args);
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        wp_delete_post($query->post, true);
                    }
                }
            }
            add_thickbox();
            ?>
            <form id='mainform' method='post' enctype="multipart/form-data">
                <h2> <?php _e('Abandoned Cart Reports', 'recoverabandoncart') ?></h2>

                <table class="rac_reports form-table">
                    <tr>
                        <th>
                            <?php _e('Number of Abandoned Carts Captured', 'recoverabandoncart'); ?>
                        </th>
                        <td>
                            <?php
                            if (get_option('rac_abandoned_count')) {
                                echo get_option('rac_abandoned_count');
                            } else {// if it is boolean false then there is no value. so give 0
                                echo "0";
                            };
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Number of Total Emails Sent', 'recoverabandoncart'); ?> 
                        </th>
                        <td>
                            <?php
                            if (get_option('rac_mail_count')) {
                                echo get_option('rac_mail_count');
                            } else {
                                echo "0";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Number of Total Email Links Clicked', 'recoverabandoncart'); ?>
                        </th>
                        <td>
                            <?php
                            if (get_option('rac_link_count')) {
                                echo get_option('rac_link_count');
                            } else {
                                echo "0";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Number of Carts Recovered', 'recoverabandoncart'); ?>
                        </th>
                        <td>
                            <?php
                            if (get_option('rac_recovered_count')) {
                                $fpracrecoveredorderids = esc_url_raw(add_query_arg(array('post_type' => 'racrecoveredorder'), admin_url('edit.php')));
                                echo '<a style="text-decoration:none" href="' . $fpracrecoveredorderids . '">' . get_option('rac_recovered_count') . '</a>&nbsp;';
                            } else {
                                echo "0";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Total Sales Amount Recovered', 'recoverabandoncart'); ?>
                        </th>
                        <td>
                            <?php
                            $total_sum = $wpdb->get_var($wpdb->prepare("
                                  SELECT sum(pm.meta_value) 
                                  FROM $wpdb->postmeta as pm 
                                  INNER JOIN $wpdb->posts as p 
                                  ON p.ID=pm.post_id     
                                  WHERE pm.meta_key = %s 
                                  AND p.post_status='publish'
                                  AND p.post_type='racrecoveredorder'", 'rac_recovered_sales_total')
                            );
                            echo fp_rac_format_price($total_sum);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Frequently Abandoned Products', 'recoverabandoncart'); ?>
                        </th>
                        <td>
                            <?php
                            $title = __('Frequently Abandoned Products', 'recoverabandoncart');
                            $url = esc_url(add_query_arg(array('wc-ajax' => 'fp_rac_dislay_top_abandoned_products', 'TB_iframe' => 'true', 'width' => '800', 'height' => '500'), home_url()));
                            echo '<a href="' . $url . '" class="thickbox" title="' . $title . '">' . __("Products which are frequently abandoned by the user", "recoverabandoncart") . '</a>';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Frequently Recovered Products', 'recoverabandoncart'); ?>
                        </th>
                        <td>
                            <?php
                            $title = __('Frequently Recovered Products', 'recoverabandoncart');
                            $url = esc_url(add_query_arg(array('wc-ajax' => 'fp_rac_dislay_top_recovered_products', 'TB_iframe' => 'true', 'width' => '800', 'height' => '500'), home_url()));
                            echo '<a href="' . $url . '" class="thickbox" title="' . $title . '">' . __("Products which are frequently recovered by the user", "recoverabandoncart") . '</a>';
                            ?>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="submit" name="rac_clear_reports" id="rac_clear_reports" class="rac_clear_reports button-primary" value="<?php _e('Clear Reports', 'recoverabandoncart'); ?>" onclick="return confirm('<?php _e('Are you sure to clear the reports ?', 'recoverabandoncart'); ?>')">
            </form>
            <style type="text/css">
                .rac_reports {
                    width:50%;
                    background-color:white;
                    border:2px solid #21759b;
                    border-collapse:unset;
                    border-top: 4px solid #21759b;
                    margin-top: 20px !important;

                }
                .rac_reports th{
                    padding: 20px;
                }
            </style>
            <?php
        }

        // Display Reports Graph
        public static function fp_rac_display_graph_for_reports() {
            $period_formatted_options = '';
            $data_formatted_options = '';
            ?>
            <script type = "text/javascript">
                jQuery(function () {
                    var mail_log = <?php echo json_encode(self::$maillogData) ?>,
                            abandon_cart = <?php echo json_encode(self::$abandonData); ?>,
                            recovered_orders = <?php echo json_encode(self::$recoveredData); ?>;
                    var plot = jQuery.plot("#rac_each_container_details", [
                        {data: mail_log, label: "Email Log", color: "#29774a"},
                        {data: abandon_cart, label: "Abandoned Carts", color: "#f00"},
                        {data: recovered_orders, label: "Recovered Orders", color: "#00f"}
                    ], {
                        series: {
                            lines: {
                                show: true
                            },
                            points: {
                                radius: 3,
                                fill: true,
                                show: true
                            }
                        },
                        xaxis: {
                            mode: "time",
                            tickSize: [1, '<?php echo self::$tickSize; ?>'],
                            minTickSize: [1, "<?php echo self::$tickSize; ?>"],
                        },
                        yaxis: {
                            min: 0,
                            minTickSize: 1,
                            tickDecimals: 0,
                        },
                        grid: {
                            hoverable: true,
                            clickable: true,
                            borderWidth: 2,
                            backgroundColor: {colors: ["#ffffff", "#EDF5FF"]}
                        }

                    });
                    jQuery("<div id='tooltip_commissions'></div>").css({
                        position: "absolute",
                        display: "none",
                        border: "1px solid #fdd",
                        padding: "2px",
                        "background-color": "#fee",
                        opacity: 0.80
                    }).appendTo("body");
                    jQuery("#rac_each_container_details").bind("plothover", function (event, pos, item) {

                        var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
                        jQuery("#hoverdata").text(str);
                        if (item) {
                            var x = new Date(item.datapoint[0]),
                                    y = item.datapoint[1].toFixed(2);
                            var getdate = x.getDate();
                            var getmonth = x.getMonth();
                            getmonth += 1;
                            var getyear = x.getFullYear();
                            var formatted_date = getdate + "-" + getmonth + "-" + getyear;
                            jQuery("#tooltip_commissions").html(item.series.label + "<br />" + formatted_date + " : " + parseInt(y))
                                    .css({top: item.pageY + 5, left: item.pageX + 5, color: item.series.color})
                                    .fadeIn(200);
                        } else {
                            jQuery("#tooltip_commissions").hide();
                        }

                    });
                });
            </script>
            <?php
            $period_options = array('alltime' => 'All Time',
                'last7days' => 'Last 7 Days',
                'thismonth' => 'This Month',
                'lastmonth' => 'Last Month',
                '3months' => '3 Months',
                '6months' => '6 Months',
                'thisyear' => 'This Year',
                'lastyear' => 'Last Year'
            );
            foreach ($period_options as $key => $option) {
                if (isset($_POST['rac_reports_period_selection'])) {
                    if ($_POST['rac_reports_period_selection'] == $key) {
                        $selected = "selected=selected";
                    } else {
                        $selected = "";
                    }
                } else {
                    $selected = "";
                }
                $period_formatted_options .= '<option value=' . $key . ' ' . $selected . '>' . $option . '</option>';
            }
            $data_options = array('alldata' => 'All Data',
                'abandonedcarts' => 'Abandoned Carts',
                'maillog' => 'Email Log',
                'recovceredorder' => 'Recovered Orders',
            );
            foreach ($data_options as $key => $option) {
                if (isset($_POST['rac_reports_data_selection'])) {
                    if ($_POST['rac_reports_data_selection'] == $key) {
                        $selected = "selected=selected";
                    } else {
                        $selected = "";
                    }
                } else {
                    $selected = "";
                }
                $data_formatted_options .= '<option value=' . $key . ' ' . $selected . '>' . $option . '</option>';
            }
            ?>
            <form id='mainform' method='post'>
                <div id="poststuff">
                    <div class="postbox ">
                        <h3><?php _e('Reports Graph', 'recoverabandoncart'); ?></h3>
                        <div class="inside">
                            <div class="rac_selection_area">
                                <p>
                                    <select class="rac_reports_data_selection" id="rac_reports_data_selection" name="rac_reports_data_selection"><?php echo $data_formatted_options; ?></select>
                                    <select class="rac_reports_period_selection" id="rac_reports_period_selection" name="rac_reports_period_selection"><?php echo $period_formatted_options; ?></select>
                                    <input type="submit" value="<?php _e("Filter", 'recoverabandoncart'); ?>" name="rac_submit_view_reports" class="button-secondary"/>
                                </p>
                            </div>
                            <div class="rac_each_container">
                                <div id="rac_each_container_details" class="rac_each_container_details"  style="height:400px;"></div>
                            </div>                     
                        </div>
                    </div>   
                </div>
            </form>
            <?php
        }

        private static function rac_get_overall_data() {
            if (isset($_POST['rac_reports_data_selection'])) {
                if ($_POST['rac_reports_data_selection'] == 'alldata') {
                    $mail_log_format_data = self::rac_get_graph_data('racmaillog', 'publish', 'rac_date_time');
                    $abandon_cart_format_data = self::rac_get_graph_data('raccartlist', 'rac-cart-abandon', 'rac_cart_abandoned_time');
                    $recovered_orders_format_data = self::rac_get_graph_data('racrecoveredorder', 'publish', 'rac_recovered_date');
                } elseif ($_POST['rac_reports_data_selection'] == 'abandonedcarts') {
                    $abandon_cart_format_data = self::rac_get_graph_data('raccartlist', 'rac-cart-abandon', 'rac_cart_abandoned_time');
                    $recovered_orders_format_data = array();
                    $mail_log_format_data = array();
                } elseif ($_POST['rac_reports_data_selection'] == 'maillog') {
                    $mail_log_format_data = self::rac_get_graph_data('racmaillog', 'publish', 'rac_date_time');
                    $recovered_orders_format_data = array();
                    $abandon_cart_format_data = array();
                } else {
                    $recovered_orders_format_data = self::rac_get_graph_data('racrecoveredorder', 'publish', 'rac_recovered_date');
                    $mail_log_format_data = array();
                    $abandon_cart_format_data = array();
                }
            } else {
                $mail_log_format_data = self::rac_get_graph_data('racmaillog', 'publish', 'rac_date_time');
                $abandon_cart_format_data = self::rac_get_graph_data('raccartlist', 'rac-cart-abandon', 'rac_cart_abandoned_time');
                $recovered_orders_format_data = self::rac_get_graph_data('racrecoveredorder', 'publish', 'rac_recovered_date');
            }

            self::$maillogData = $mail_log_format_data;
            self::$abandonData = $abandon_cart_format_data;
            self::$recoveredData = $recovered_orders_format_data;
        }

        private static function rac_get_min_max() {
            $maillog_data = self::$maillogData;
            $abandon_data = self::$abandonData;
            $recovered_data = self::$recoveredData;
            if (is_array(self::$maillogData) && !empty(self::$maillogData)) {
                $maillog_min = $maillog_data[0];
                $maillog_max = end($maillog_data);
            }
            if (is_array(self::$abandonData) && !empty(self::$abandonData)) {
                $abandon_min = $abandon_data[0];
                $abandon_max = end($abandon_data);
            }
            if (is_array(self::$recoveredData) && !empty(self::$recoveredData)) {
                $recovered_min = $recovered_data[0];
                $recovered_max = end($recovered_data);
            }
            if (empty(self::$maillogData) && empty(self::$recoveredData) && empty(self::$abandonData)) {
                self::$minData = array();
                self::$maxData = array();
            } elseif (empty(self::$maillogData) && empty(self::$recoveredData)) {
                self::$minData = $abandon_min[0];
                self::$maxData = $abandon_max[0];
            } elseif (empty(self::$maillogData) && empty(self::$abandonData)) {
                self::$minData = $recovered_min[0];
                self::$maxData = $recovered_max[0];
            } elseif (empty(self::$recoveredData) && empty(self::$abandonData)) {
                self::$minData = $maillog_min[0];
                self::$maxData = $maillog_max[0];
            } elseif (!empty(self::$maillogData) && !empty(self::$recoveredData) && empty(self::$abandonData)) {
                self::$minData = min($maillog_min[0], $recovered_min[0]);
                self::$maxData = max($maillog_max[0], $recovered_max[0]);
            } elseif (!empty(self::$maillogData) && empty(self::$recoveredData) && !empty(self::$abandonData)) {
                self::$minData = min($maillog_min[0], $abandon_min[0]);
                self::$maxData = max($maillog_max[0], $abandon_max[0]);
            } elseif (empty(self::$maillogData) && !empty(self::$recoveredData) && !empty(self::$abandonData)) {
                self::$minData = min($abandon_min[0], $recovered_min[0]);
                self::$maxData = max($abandon_max[0], $recovered_max[0]);
            } else {
                self::$minData = min($abandon_min[0], $recovered_min[0], $maillog_min[0]);
                self::$maxData = max($abandon_max[0], $recovered_max[0], $maillog_max[0]);
            }
        }

        private static function rac_add_start_end_data() {
            if (!empty(self::$minData)) {
                $mindata = date('Y-m-d', self::$minData / 1000);
                $maxdata = date('Y-m-d', self::$maxData / 1000);
                if (self::$tickSize == 'day') {
                    $before_date = strtotime($mindata . '-1 day') * 1000;
                    $after_date = strtotime($maxdata . '+1 day') * 1000;
                } else {
                    $before_date = strtotime($mindata . '-1 month') * 1000;
                    $after_date = strtotime($maxdata . '+1 month') * 1000;
                }

                $first_value = array($before_date, 0);
                $last_value = array($after_date, 0);

                if (is_array(self::$maillogData) && !empty(self::$maillogData)) {
                    array_unshift(self::$maillogData, $first_value);
                    array_push(self::$maillogData, $last_value);
                }
                if (is_array(self::$abandonData) && !empty(self::$abandonData)) {
                    array_unshift(self::$abandonData, $first_value);
                    array_push(self::$abandonData, $last_value);
                }
                if (is_array(self::$recoveredData) && !empty(self::$recoveredData)) {
                    array_unshift(self::$recoveredData, $first_value);
                    array_push(self::$recoveredData, $last_value);
                }
            }
        }

        private static function rac_get_graph_data($post_type, $post_status, $meta_key) {
            global $wpdb;
            $json_format = array();
            $between = self::rac_get_between();
            if (empty($between)) {
                $data = $wpdb->get_results(
                        "SELECT 
                         UNIX_TIMESTAMP(from_unixtime(pm.meta_value,'%Y-%m-%d')) as Date_Time ,
                         count(pm.meta_value) as count 
                         FROM {$wpdb->postmeta} as pm INNER JOIN {$wpdb->posts} as p 
                         ON p.ID=pm.post_id 
                         WHERE p.post_status= '$post_status' 
                         AND p.post_type= '$post_type' AND pm.meta_key= '$meta_key' 
                         GROUP BY UNIX_TIMESTAMP(from_unixtime(pm.meta_value,'%Y-%m-%d')) 
                         ORDER BY pm.meta_value ASC ", ARRAY_A);
            } else {
                $from_date = $between[0];
                $to_date = $between[1];
                $data = $wpdb->get_results(
                        "SELECT 
                        UNIX_TIMESTAMP(from_unixtime(pm.meta_value,'%Y-%m-%d')) as Date_Time ,
                        count(pm.meta_value) as count 
                        FROM {$wpdb->postmeta} as pm INNER JOIN {$wpdb->posts} as p 
                        ON p.ID=pm.post_id 
                        WHERE p.post_status= '$post_status'
                        AND p.post_type= '$post_type' AND pm.meta_key= '$meta_key' 
                        AND pm.meta_value >= $from_date AND pm.meta_value <= $to_date
                        GROUP BY UNIX_TIMESTAMP(from_unixtime(pm.meta_value,'%Y-%m-%d')) 
                        ORDER BY pm.meta_value ASC ", ARRAY_A);
            }
            if (!empty($data)) {
                foreach ($data as $newkey => $newvalue) {
                    $json_format[] = array(($newvalue['Date_Time'] ) * 1000, $newvalue['count']);
                }
            }
            return $json_format;
        }

        private static function rac_get_between() {
            if (isset($_POST['rac_reports_period_selection'])) {
                $period_selection = $_POST['rac_reports_period_selection'];
                if ($period_selection == 'alltime') {
                    $between = array();
                    self::$tickSize = 'month';
                } elseif ($period_selection == 'last7days') {
                    $start_date = strtotime(date("Y-m-d", strtotime('midnight -6 days', current_time('timestamp'))));
                    $end_date = strtotime(date("Y-m-d", strtotime('tomorrow midnight', current_time('timestamp'))));
                    $between = array($start_date, $end_date);
                    self::$tickSize = 'day';
                } elseif ($period_selection == 'thismonth') {
                    $start_date = strtotime(date('Y-m-01', current_time('timestamp')));
                    $end_date = strtotime(date("Y-m-d", strtotime('tomorrow midnight', current_time('timestamp'))));
                    $between = array($start_date, $end_date);
                    self::$tickSize = 'day';
                } elseif ($period_selection == 'lastmonth') {
                    $first_day_current_month = strtotime(date('Y-m-01', current_time('timestamp')));
                    $start_date = strtotime(date('Y-m-01', strtotime('-1 DAY', $first_day_current_month)));
                    $end_date = strtotime('midnight', $first_day_current_month) - 1;
                    $between = array($start_date, $end_date);
                    self::$tickSize = 'day';
                } elseif ($period_selection == '3months') {
                    $first_day_current_month = strtotime(date('Y-m-01', current_time('timestamp')));
                    $start_date = strtotime(date('Y-m-01', strtotime('-2 months', $first_day_current_month)));
                    $end_date = strtotime(date("Y-m-d", strtotime('tomorrow midnight', current_time('timestamp'))));
                    $between = array($start_date, $end_date);
                    self::$tickSize = 'month';
                } elseif ($period_selection == '6months') {
                    $first_day_current_month = strtotime(date('Y-m-01', current_time('timestamp')));
                    $start_date = strtotime(date('Y-m-01', strtotime('-5 months', $first_day_current_month)));
                    $end_date = strtotime(date("Y-m-d", strtotime('tomorrow midnight', current_time('timestamp'))));
                    $between = array($start_date, $end_date);
                    self::$tickSize = 'month';
                } elseif ($period_selection == 'thisyear') {
                    $start_date = strtotime(date('Y-01-01', current_time('timestamp')));
                    $end_date = strtotime(date("Y-m-d", strtotime('tomorrow midnight', current_time('timestamp'))));
                    $between = array($start_date, $end_date);
                    self::$tickSize = 'month';
                } else {
                    $first_day_current_year = strtotime(date('Y-01-01', current_time('timestamp')));
                    $start_date = strtotime(date('Y-01-01', strtotime('-1 year', $first_day_current_year)));
                    $end_date = strtotime('midnight', $first_day_current_year);
                    $between = array($start_date, $end_date);
                    self::$tickSize = 'month';
                }
            } else {
                $between = array();
                self::$tickSize = 'month';
            }
            return $between;
        }

    }

    FP_RAC_Reports_Tab::init();
}