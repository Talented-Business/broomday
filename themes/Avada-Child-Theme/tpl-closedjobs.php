<?php
/* Template Name: Closed Jobs */
ob_start();
if (is_user_logged_in()) {
    
} elseif (!is_user_logged_in()) {
    wp_redirect(site_url());
}
get_header();
?>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/validate-js/2.0.1/validate.js" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/validate-js/2.0.1/validate.min.js" />-->

<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
    tr{
        line-height: 10px !important;
    }
    td{
        line-height: 20px;
    }
    td, th {
        /*        border: 1px solid #dddddd;*/
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        /*        background-color: #dddddd;*/
    }
    .customer-table{
        width: 500px;
        height: 470px;
        background: #fff;
        padding:20px 15px;
        box-shadow: 2px 2px 6px 1px #ddd;
        border-radius: 5px;
        color: #888;
        float:left;
        margin: 20px;
        padding-bottom:20px;
    }
    .customer{
        width: 75%;
        float: left;
        padding-left: 6px;
    }
    .hours{
        width: 25%;
        float: left;
    }
    .customer-table p {
        font-size: 18px;
        font-weight: bold;
        margin: 5px 0;
        color: #6cabdd;
    }
    .table-right {
        padding-left: 10px;
    }

    .common-phone p {
        margin-top: 0;
        margin-bottom: 0;
    }

    .common-phone {
        padding-top: 0;
        padding-bottom: 0;
    }

    .assign{
        background: #6cabdd;
        color: #fff;
        border: 0;
        padding: 10px 80px;
        border-radius: 5px;
        font-size: 18px;
        /*float: right;*/

        box-shadow: 2px 2px 4px 1px #ddd;
    }
    .address_box {
        height: 50px !important;
        overflow: hidden;
        min-height: 50px !important;
        line-height: 20px !important;
    }
    @media only screen and (min-device-width: 769px) and (max-device-width: 1024px) {
        .customer-table {
            width: calc(50% - 5px);
            height: auto;
            background: #fff;
            padding: 20px 10px;
            box-shadow: 2px 2px 6px 1px #ddd;
            border-radius: 5px;
            color: #888;
            float: left;
            margin: 0;
            margin-right: 5px;
            margin-bottom: 15px;
        }
    }
    @media only screen and (max-device-width: 768px) {
        .customer-table {
            width: calc(100%);
            margin: 0;
            margin-bottom: 15px;
        }
        #main {
            padding-top: 30px !important;
            padding-bottom: 30px !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
    }
    @media all and (max-device-width: 320px) {
        .table-right {
            padding-left: 5px;
        }
        td, th {
            padding: 5px;
        }
    }
    li span.page-numbers.current {
        padding-top: 6px;
        padding-bottom: 6px;
        padding-left: 11px;
        padding-right: 11px;
        background-color: rgb(108,171,221);
        color:#ffffff;
    }
    li a.page-numbers {
        background-color: #eee;
        padding-left: 11px;
        padding-right: 11px;
        padding-top: 6px;
        padding-bottom: 6px;
    }
    ul.page-numbers li {
        display: inline-block;
        vertical-align: top;
        float: left;
        padding-left: 1%;
    }
    .assigned_paginattion{
        width: 100% !important;
        float: left !important;
    }
    .jobs_table2{
        width:26%;
    }

    .phone{
        font-size:16px !important;

        font-family: "Cabin";
    }
    .phone_no{
        float: left;
        font-size: 16px !important;
        padding-top: 5px;
        font-family: "Cabin";
    }

.jobs_table2.common-phone {
  padding-top: 10px;
}

.table-right.common-phone {
  padding-top: 5px;
}

@media all and (max-device-width: 800px)  {
.assign{
    margin-top: 10px;
    float:right;
 }
}

@media all and (max-device-width: 980px)  {
.assign{
    margin-top: 10px;
    float:right;
 }
}

@media all and (max-device-width: 768px)  {
.assign{
    margin-top: 10px;
    float:right;
 }
}

@media all and (max-device-width: 320px)  {
.assign{
    margin-top: 10px;
    float:right;
 }
}

</style>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php
        global $wpdb;

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $user_info = get_userdata($user_id);
        $user_info = get_userdata($current_user->ID);
        $tbl1 = $wpdb->prefix . 'posts';
        $tbl2 = $wpdb->prefix . 'postmeta';

        $simple_query = "SELECT $tbl1.*,pm2.meta_value AS assigned_user_id
                    FROM $tbl1
                    RIGHT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='assigned_user_id' and pm2.meta_value='$current_user->ID') 
                    WHERE $tbl1.post_type = 'shop_order' and  $tbl1.post_status ='wc-completed'
                    ORDER BY $tbl1.post_date DESC";
        //echo $simple_query;
        $total_count = count($wpdb->get_results($simple_query));
        $per_page = 12;
        $pid = 0;
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
        $pid = (($paged - 1) * $per_page);

        $consulta = "SELECT $tbl1.*,pm2.meta_value AS assigned_user_id
                    FROM $tbl1
                    RIGHT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='assigned_user_id' and pm2.meta_value='$current_user->ID') 
                    WHERE $tbl1.post_type = 'shop_order' and  $tbl1.post_status ='wc-completed'
                    ORDER BY $tbl1.post_date DESC LIMIT $pid,$per_page";
        $orders = $wpdb->get_results($consulta);
        //echo "<h1>Closed Jobs</h1>";
        ?>
        <div class="assigned_jobs" >
            <?php
            if (!empty($orders)) {
                foreach ($orders as $order) {
                    //echo "Here".$order->ID."<pre>";print_r($order);
                    $Billing = get_post_meta($order->ID, '_billing_address_index', true);
                    $total = get_post_meta($order->ID, '_order_total', true);
                    $assign_user_id = get_post_meta($order->ID, 'assigned_user_id', true);
                    $odr_status = '';
                    $order_status = $order->post_status;
                    if ($order->post_status == 'wc-processing')
                        $odr_status = 'In Process';
                    if ($order->post_status == 'wc-completed')
                        $odr_status = 'Closed';

                    $job = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items " .
                            "WHERE order_id = " . $order->ID . " GROUP BY order_id; ";
                    $job_detail = $wpdb->get_results($job);
                    $order_item_id = $job_detail[0]->order_item_id;
                    $date = wc_get_order_item_meta($order_item_id, 'date', true);
                    $time = wc_get_order_item_meta($order_item_id, 'time', true);
                    $user_info = get_userdata($assign_user_id);
                    ///////////////////////////////////////////////////////////////////////////
                    $current_user = wp_get_current_user();
                    $user_jobs = "SELECT post_id " . "FROM {$wpdb->prefix}postmeta " .
                            "WHERE meta_key = 'assigned_user_id' AND meta_value = " . $current_user->ID . "; ";
                    $user_assign_orderids = $wpdb->get_results(( $user_jobs));
                    $my_order_ids = array();
                    foreach ($user_assign_orderids as $uaoi) {
                        $my_order_ids[] = $uaoi->post_id;
                    }
                    $my_jobs_datetime = array();
                    if (count($my_order_ids) > 0) {
                        $myorder = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items " .
                                "WHERE order_id in (" . implode(',', $my_order_ids) . ") group by order_id;";
                        $myorder_details = $wpdb->get_results(( $myorder));
                        $r = 0;
                        foreach ($myorder_details as $mod) {
                            $mydate = wc_get_order_item_meta($mod->order_item_id, 'date', true);
                            $mytime = wc_get_order_item_meta($mod->order_item_id, 'time', true);
                            $myjob_hours = wc_get_order_item_meta($mod->order_item_id, 'recommended_hour', true);
                            $my_jobs_datetime[$r]['dt'] = $mydate . ' ' . $mytime;
                            $my_jobs_datetime[$r]['hours'] = $myjob_hours;
                            $r++;
                        }
                    }
                    $first_name = get_post_meta($order->ID, '_billing_first_name', true);
                    $last_name = get_post_meta($order->ID, '_billing_last_name', true);
                    $email = get_post_meta($order->ID, 'billing_email', true);
                    $Billing = get_post_meta($order->ID, '_billing_address_index', true);
                    $phone = get_post_meta($order->ID, '_billing_phone', true);
                    $address = str_replace($first_name, '', $Billing);
                    $address = str_replace($last_name, '', $address);
                    $address = get_post_meta($order->ID, '_billing_address_1', true);
					$service_description = get_post_meta( $order->ID, 'service_description', true );
                    $order_query = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items
                                   WHERE order_id = " . $order->ID . " GROUP BY order_id; ";
                    $order_details = $wpdb->get_results($order_query);
                    $assign_user_id = get_post_meta($order->ID, 'assigned_user_id', true);
                    $phn_str = '';
                    if ($assign_user_id > 0) {
                        $phn_str = '<tr>
                                            <td>Phone</td>
                                            <td class="table-right">' . $phone . '</td>
                                        </tr>';
                    }
                    ?>
                    <div class="customer-table">
                        <?php
                        $i = 0;
                        if (!empty($order_details)) {
                            foreach ($order_details as $od) {
                                if ($i == 0) {
                                    $product_id = wc_get_order_item_meta($od->order_item_id, '_product_id', true);
                                    $recommended_hour = wc_get_order_item_meta($od->order_item_id, 'recommended_hour', true);
                                    $service_hour = wc_get_order_item_meta($od->order_item_id, 'service_hour', true);
                                    $date = wc_get_order_item_meta($od->order_item_id, 'date', true);
                                    $time = wc_get_order_item_meta($od->order_item_id, 'time', true);
                                    $bedroom = wc_get_order_item_meta($od->order_item_id, 'bedroom', true);
                                    $bathroom = wc_get_order_item_meta($od->order_item_id, 'bathroom', true);
                                    $extra_services = wc_get_order_item_meta($od->order_item_id, 'extra_services', true);
                                    $extra_hours = wc_get_order_item_meta($od->order_item_id, 'extra_hours', true);
                                    $end_time = get_post_meta($order->ID, '_assigned_end_time', true);
                                    $hours = get_post_meta($order->ID, '_assigned_hours', true);
                                    $combinedDT = date('Y-m-d H:i:s', strtotime("$date $time"));
                                    $close_hour = trim(date('Y-m-d H:i:s', strtotime('+ 1 hours', strtotime($combinedDT))));
                                    $total_hours = $recommended_hour + $extra_hours;
                                    $services_array = array();
                                    if (!empty($extra_services)) {
                                        foreach ($extra_services as $services) {
                                            $services_array[] = $services['title'];
                                        }
                                    }
                                    $service_string = "";
                                    if (!empty($services_array))
                                        $service_string = implode(",", $services_array);
                                    ?>
                                                                                <!--                                <p><?php echo $od->order_item_name; ?></p>-->
                                    <div class="customer">
                                        <p><?php _e("Customer Name", "Avada");?></p>
                                        <span><?php echo $first_name . ' ' . $last_name; ?></span>
                                    </div>
                                    <div class="hours">
                                        <p><?php _e("Hours Hired", "Avada");?></p>
                                        <span><?php echo $service_hour; ?></span>
                                    </div>
                                    <hr>
                                    <p style="padding-left: 6px;"><?php _e("As Scheduled", "Avada");?></p>
                                    <table>
                                        <tr>
                                            <td><?php _e("Date", "Avada");?></td>
                                            <td class="table-right"><?php echo date("d F, Y", strtotime($date)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php _e("Time", "Avada");?></td>
                                            <td class="table-right"><?php echo $time; ?></td>
                                        </tr>
                                        <!--<tr>
                                            <td style="width:20%;">End-Time</td>
                                            <td class="table-right"><?php echo date('h:i A', strtotime($end_time)); ?></td>
                                        </tr>-->
                                        <tr class="address_box">
                                            <td><?php _e("Address", "Avada");?></td>
                                            <td class="table-right "><a href="https://maps.google.com/?q=<?php echo $address; ?>" target="_blank"><?php echo $address; ?></a></td>
                                        </tr>
                                          <tr class="address_box">
                                        <td class="jobs_table2"><?php _e("Descripción del servicion", "Avada");?></td>
                                        <td class="table-right "><?php echo $service_description;?></td>
                                    </tr>
                                    </table>
                                    <hr>
                                    <p style="padding-left: 6px;"><?php _e("Order Details", "Avada");?> - #<?php echo $order->ID; ?></p>
                                    <table>
                                        <tr>
                                            <td>Filters</td>
                                            <td class="table-right"><?php
                                                if (get_post_meta($product_id, '_select_bedroom_filter', true) == 1) {
                                                    echo $bedroom . "Bedrooms | " . $bathroom . "Bathrooms";
                                                } else
                                                    echo "-";
                                                ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php _e("Extras", "Avada");?></td>
                                            <td class="table-right"><?php
                                                if (!empty($extra_services))
                                                    echo $service_string;
                                                else
                                                    echo "Sin Extras"
                                                    ?></td>
                                        </tr>
                                       <!-- <tr>
                                            <td class="jobs_table2 common-phone"><p class="phone">Phone</p></td>
                                            <td class="table-right common-phone">
                                                <p class="phone_no"><?php echo get_post_meta($order->ID, '_billing_phone', true); ?></p></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="jobs_table2 common-phone"><p class="phone">Cell NO</p></td>
                                            <td class="table-right common-phone">
                                                <p class="phone_no"><?php echo get_post_meta($order->ID, '_billing_cell', true); ?></p></div>
                                            </td>
                                        </tr>-->
                                        <?php
                                        //echo $phn_str;
                                        ?>
                                    </table>
                                    <?php
                                }
                                $i++;
                            }
                        }
                        $get_job = get_post($order->ID);
                        ?>
                    </div>
                    <?php
                }
                ?>
            </table>
            <?php
        }
        else {
            ?>
            <h3><?php _e("You have not closed jobs at this moment.", "Avada");?>
</h3>
            <?php
        }
        ?> 
        <br/>
        </div>
        <div class="assigned_paginattion">
            <?php
            $big = 999999999; // need an unlikely integer
            $total = round($total_count / $per_page);
            echo paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $total,
                'mid_size' => 1,
                'prev_text' => '<<',
                'next_text' => '>>',
                'type' => 'list'
            ));
            ?>
        </div>
        <?php
    endwhile;
endif;
?>
<?php //get_footer(); ?>
<script>
    jQuery(document).ready(function ($) {
        $(".close_button").one("click", function () {
            $(".close_form").submit();
        });
        $(".open_model").click(function (event) { // Click to only happen on announce links cancel_model
            //event.preventDefault();
            $("#model_order_id").val($(this).data('id'));
            $('#myModalNorm').modal('show');
        });
        $(".cancel_model").click(function () { // Click to only happen on announce linkscancel_model
            $("#modelcancel_order_id").val($(this).data('id'));
            $('#Cancelmodel').modal('show');
        });
    });
</script>
<?php
wp_footer();
 include( get_template_directory() . '/footer.php'); ?>