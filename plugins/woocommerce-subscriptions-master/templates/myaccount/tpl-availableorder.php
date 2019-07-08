<?php
/* Template Name: Available Jobs */
ob_start();

if (is_user_logged_in()) {

} elseif (!is_user_logged_in()) {
    wp_redirect(site_url());
}
get_header();
?>
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
        height: auto;
        background: #fff;
        /*        padding:20px 15px;*/
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
        margin-top: -55px;
        float: right;
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
<?php
if (have_posts()) : while (have_posts()) : the_post();
        global $wpdb;
  
        //if (!isset($_REQUEST['order_id'])) {
        $area_radius = get_option('area_radius');
        if ($area_radius != "")
            $area_radius = $area_radius;
        else
            $area_radius = '10';
        $user_id = get_current_user_id();
        $user_info = get_userdata($user_id);
        $user_services = unserialize(get_user_meta($user_id, "user_services", true));
        
        if (!empty($user_services)) {
          
            $service = implode(",", $user_services);
        }
        $latitude = get_user_meta($user_id, "latitude", true);
        $longitude = get_user_meta($user_id, "longitude", true);
        $tbl1 = $wpdb->prefix . 'posts';
        $tbl2 = $wpdb->prefix . 'postmeta';


        if (isset($_POST["job_assign"])) {
            extract($_POST);
            $currdate = date("Y-m-d");
            $t = EXPLODE(".", $hours);
            $h = $t[0];
            IF (ISSET($t[1])) {
                $m = $t[1];
            } ELSE {
                $m = "00";
            }
            if ($m != '00')
                $mm = ($h * 60) + 30;
            else
                $mm = ($h * 60);

            $end_time = trim(date('H:i:s', strtotime('+' . $mm . ' minutes', strtotime($start_time))));
            $timeframe_end_time = trim(date('H:i:s', strtotime('+2 hours', strtotime($end_time))));

            
            $check_data=$wpdb->get_results("SELECT * FROM $tbl2 WHERE post_id='$order_id' and meta_key='assigned_user_id' and meta_value!=0");
            
            if(!empty($check_data)){
                   echo '<div class="alert alert-danger">
                        <strong>Oops!</strong>Esta oportunidad ya se ha asignado a otro usuario.
                      </div>';//exit;
            }
            else{
                update_post_meta($order_id, 'assigned_user_id', $current_user->ID);
                
	            update_post_meta($order_id, '_assigned_hours', $hours);
	            update_post_meta($order_id, '_assigned_start_time', $start_time);
	            update_post_meta($order_id, '_assigned_end_time', $end_time);
	            update_post_meta($order_id, '_assigned_date', $date);
	            update_post_meta($order_id, '_assigned_on_date', $currdate);

	            $order = new WC_Order($_REQUEST['order_id']);
	            $order->update_status("processing");
	            $order_id=$_REQUEST['order_id'];
	            $data_array = array('user_id' => $user_id, 'order_id' =>$order_id, 'hours' => $hours, 'status' => 'processing',
                	'start_time' => $start_time, 'end_time' => $end_time, 'timeframe_end_time' => $timeframe_end_time, 'date' => $date);

	            $select_data=$wpdb->get_results("select * from tblassignjobs where order_id=$order_id");
	            if(empty($select_data)){
		        $insert=$wpdb->insert("tblassignjobs",$data_array);
		    }
		    else{
		         $data_array = array('user_id' => $user_id, 'hours' => $hours, 'status' => 'processing',
	                'start_time' => $start_time, 'end_time' => $end_time, 'timeframe_end_time' => $timeframe_end_time, 'date' => $date);
		       $update=$wpdb->update("tblassignjobs",$data_array,array('order_id'=>$order_id));
		    }
		    wp_redirect(site_url("nuevas-oportunidades"));
	    }
        }
		$query_1 = "SELECT $tbl1.*,  
                                pm2.meta_value AS cust_latitude, 
                                pm3.meta_value AS cust_longitude, 
                                pm4.meta_value AS product_service, 
                                pm5.meta_value AS assigned_user_id, 
                                pm6.meta_value AS order_date,
                                ( 6371 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( pm2.meta_value ) ) * COS( RADIANS( pm3.meta_value ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( pm2.meta_value ) ) ) ) AS distance   
                         FROM $tbl1 
                         LEFT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='cust_latitude') 
                         LEFT JOIN $tbl2 AS pm3 ON ($tbl1.ID = pm3.post_id AND pm3.meta_key='cust_longitude')
                         LEFT JOIN $tbl2 AS pm4 ON ($tbl1.ID = pm4.post_id)
                         LEFT JOIN $tbl2 AS pm5 ON ($tbl1.ID = pm5.post_id)
                         LEFT JOIN $tbl2 AS pm6 ON ($tbl1.ID = pm6.post_id)
                         Where $tbl1.post_type = 'shop_order' and $tbl1.post_status= 'wc-processing'
                         AND pm4.meta_key='product_service' and pm4.meta_value IN($service) 
                         AND pm5.meta_key='assigned_user_id' and pm5.meta_value='0'
                         AND pm6.meta_key='order_date'
                         HAVING distance < $area_radius
                         ORDER BY pm6.meta_value ASC";
     
        $job_data = $wpdb->get_results($query_1);
                         
             

        $orders_ids = "";
        $orders_array = array();
        if (!empty($job_data)) {
            foreach ($job_data as $jobDetails) {
				
                $job = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items " .
                        "WHERE order_id = " . $jobDetails->ID . " GROUP BY order_id; ";
                $job_detail = $wpdb->get_results($job);
                $order_item_id = $job_detail[0]->order_item_id;
                $date = wc_get_order_item_meta($order_item_id, 'date', true);
                $time = wc_get_order_item_meta($order_item_id, 'time', true);
                $recommended_hour = wc_get_order_item_meta($order_item_id, 'recommended_hour', true);
                $extra_hours = wc_get_order_item_meta($order_item_id, 'extra_hours', true);
                $total_hours = $recommended_hour + $extra_hours;
                $start_time = date("H:i:s", strtotime($time));
                $new_hours = ($total_hours + 2);
                $t = EXPLODE(".", $new_hours);
                $h = $t[0];
                IF (ISSET($t[1])) {
                    $m = $t[1];
                } ELSE {
                    $m = "00";
                }
                if ($m != '00')
                    $mm = ($h * 60) + 30;
                else
                    $mm = ($h * 60);
                $end_time = trim(date('H:i:s', strtotime('+ ' . $mm . ' minutes', strtotime("$start_time"))));
                //echo $mm."==>".$new_hours."==>".$start_time."==>".$end_time."<br/>";

                //$end_time   =trim(date('H:i:s',strtotime('+'.$total_hours.' hours',strtotime($start_time))));
                 $timeframe_detail = $wpdb->get_results("SELECT * FROM tblassignjobs WHERE date='$date' and user_id=$user_id and status='processing' and 
                                 ( ( (('$start_time' BETWEEN `start_time` AND `timeframe_end_time`) or ('$end_time' BETWEEN `start_time` AND `timeframe_end_time`)) and timeframe_end_time!='$start_time') or (timeframe_end_time='$end_time'))");
                //print_r($timeframe_sql);
                if (empty($timeframe_detail)) {
                    $orders_array[] = $jobDetails->ID;
                }
            }
        }
        //print_r($orders_array);
        if (!empty($orders_array))
            $orders_ids = implode(",", $orders_array);
        else
            $orders_ids = "0";

    $consulta ="SELECT $tbl1.*,pm2.meta_value AS assigned_user_id
                    FROM $tbl1
                    RIGHT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='assigned_user_id' and pm2.meta_value='$current_user->ID') 
                    WHERE $tbl1.post_type = 'shop_order' and ( $tbl1.post_status ='wc-processing' )";

    $orders = $wpdb->get_results($consulta);

    $order_times = array();
    if (!empty($orders)) {
        foreach ($orders as $order) {

            $job = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items " . "WHERE order_id = " . $order->ID . " GROUP BY order_id; ";
            $job_detail = $wpdb->get_results($job);

            $order_item_id = $job_detail[0]->order_item_id;

            $recommended_hour = wc_get_order_item_meta($order_item_id, 'recommended_hour', true);
            $extra_hours = wc_get_order_item_meta($order_item_id, 'extra_hours', true);
            $total_hours = $recommended_hour + $extra_hours;
            $date = wc_get_order_item_meta($order_item_id, 'date', true);
            $time = wc_get_order_item_meta($order_item_id, 'time', true);

            $orderstart = date('Y-m-d H:i:00', strtotime($date." ".$time));
            $orderend   = date('Y-m-d H:i:s', strtotime($orderstart." +".$total_hours."hour"));

            $orderstart = date('Y-m-d H:i:00', strtotime($orderstart." -2hour"));
            $orderend = date('Y-m-d H:i:00', strtotime($orderend." +2hour"));

            $avoidorder = false;

            $order_times[] = array(
                "start" => $orderstart,
                "end"  => $orderend,
                "hour"  => $total_hours
            );

        }
    }
    $orders = array();

	 $query_distance = "SELECT wpstg2_posts.ID,	 pm2.meta_value AS cust_latitude, pm3.meta_value AS cust_longitude, ( 6371 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( pm2.meta_value ) ) * COS( RADIANS( pm3.meta_value ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( pm2.meta_value ) ) ) ) AS distance  FROM $tbl1 
LEFT JOIN wpstg2_postmeta AS pm2 ON (wpstg2_posts.ID = pm2.post_id AND pm2.meta_key='cust_latitude') 
LEFT JOIN wpstg2_postmeta AS pm3 ON (wpstg2_posts.ID = pm3.post_id AND pm3.meta_key='cust_longitude') 
Where wpstg2_posts.post_type = 'shop_order' and wpstg2_posts.post_status= 'wc-processing'
 HAVING distance < $area_radius ORDER BY pm2.meta_value ASC LIMIT 0,12 ";

    $simple_query = "SELECT $tbl1.*,  
                                pm2.meta_value AS cust_latitude, 
                                pm3.meta_value AS cust_longitude, 
                                pm4.meta_value AS product_service, 
                                pm5.meta_value AS assigned_user_id, 
                                pm6.meta_value AS order_date,
                                ( 6371 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( pm2.meta_value ) ) * COS( RADIANS( pm3.meta_value ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( pm2.meta_value ) ) ) ) AS distance   
                         FROM $tbl1 
                         LEFT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='cust_latitude') 
                         LEFT JOIN $tbl2 AS pm3 ON ($tbl1.ID = pm3.post_id AND pm3.meta_key='cust_longitude')
                         LEFT JOIN $tbl2 AS pm4 ON ($tbl1.ID = pm4.post_id)
                         LEFT JOIN $tbl2 AS pm5 ON ($tbl1.ID = pm5.post_id)
                         LEFT JOIN $tbl2 AS pm6 ON ($tbl1.ID = pm6.post_id)
                         Where $tbl1.post_type = 'shop_order' and $tbl1.post_status= 'wc-processing'
                         AND pm4.meta_key='product_service' and pm4.meta_value IN($service) 
                         AND pm5.meta_key='assigned_user_id' and pm5.meta_value='0'
                         AND $tbl1.ID IN ($orders_ids)
                         AND pm6.meta_key='order_date'
                         HAVING distance < $area_radius
                         ORDER BY pm6.meta_value ASC";
		       
		        $total_count = count($wpdb->get_results($simple_query));
		        $per_page = 12;
		        $pid = 0;
		        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
		        $pid = (($paged - 1) * $per_page);
		        $consulta = "SELECT $tbl1.*,  
                                pm2.meta_value AS cust_latitude, 
                                pm3.meta_value AS cust_longitude, 
                                pm4.meta_value AS product_service, 
                                pm5.meta_value AS assigned_user_id, 
                                pm6.meta_value AS order_date,
                                ( 6371 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( pm2.meta_value ) ) * COS( RADIANS( pm3.meta_value ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( pm2.meta_value ) ) ) ) AS distance   
                         FROM $tbl1 
                         LEFT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='cust_latitude') 
                         LEFT JOIN $tbl2 AS pm3 ON ($tbl1.ID = pm3.post_id AND pm3.meta_key='cust_longitude')
                         LEFT JOIN $tbl2 AS pm4 ON ($tbl1.ID = pm4.post_id)
                         LEFT JOIN $tbl2 AS pm5 ON ($tbl1.ID = pm5.post_id)
                         LEFT JOIN $tbl2 AS pm6 ON ($tbl1.ID = pm6.post_id)
                         Where $tbl1.post_type = 'shop_order' and $tbl1.post_status= 'wc-processing'
                         AND pm4.meta_key='product_service' and pm4.meta_value IN($service) 
                         AND pm5.meta_key='assigned_user_id' and pm5.meta_value='0'
                         AND $tbl1.ID IN ($orders_ids)
                         AND pm6.meta_key='order_date'
                         HAVING distance < $area_radius
                         ORDER BY pm6.meta_value ASC
                         LIMIT $pid,$per_page";
        //echo $consulta;//exit;
        $orders = $wpdb->get_results($query_distance);
        
       
        ?>

        <div class="assigned_jobs" >
            <?php

            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $Billing = get_post_meta($order->ID, '_billing_address_index', true);
                    $total = get_post_meta($order->ID, '_order_total', true);
                    $assign_user_id = get_post_meta($order->ID, 'assigned_user_id', true);
                    $billing_city = get_post_meta($order->ID, '_billing_city', true);
                    $billing_address_1 = get_post_meta($order->ID, '_billing_address_1', true);
                    $keywords = preg_split("/[\s,]+/", $billing_address_1);
                    $current_user = wp_get_current_user();
                    $related_products = get_the_author_meta('related_products', $current_user->ID);
                    $region = get_the_author_meta('region', $current_user->ID);
                    $product_ids = !empty($related_products) ? array_map('absint', $related_products) : null;

                    $job = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items " . "WHERE order_id = " . $order->ID . " GROUP BY order_id; ";
                    $job_detail = $wpdb->get_results($job);

                    $order_item_id = $job_detail[0]->order_item_id;
                    $order_pro_id = wc_get_order_item_meta($order_item_id, '_product_id', true);
                    //timeframe shedule==============================================================
                    $recommended_hour = wc_get_order_item_meta($order_item_id, 'recommended_hour', true);
                    $service_hour = wc_get_order_item_meta($order_item_id, 'service_hour', true);
                    $extra_hours = wc_get_order_item_meta($order_item_id, 'extra_hours', true);
                    $total_hours = $recommended_hour + $extra_hours;
                    $date = wc_get_order_item_meta($order_item_id, 'date', true);
                    $time = wc_get_order_item_meta($order_item_id, 'time', true);
                    $start_time = date("H:i:s", strtotime($time));
                    $end_time = trim(date('H:i:s', strtotime('+' . $total_hours . ' hours', strtotime($start_time))));
                    $timeframe_sql = "SELECT * FROM tblassignjobs WHERE date='$date' and user_id=$user_id and status='processing' and (('$start_time' BETWEEN `start_time` AND `timeframe_end_time` and timeframe_end_time!='$start_time') or (timeframe_end_time='$end_time'))";
                    //echo $timeframe_sql;
                    $timeframe_detail = $wpdb->get_results($timeframe_sql);


                    $orderstart = date('Y-m-d H:i:00', strtotime($date." ".$time));
                    $orderend   = date('Y-m-d H:i:s', strtotime($orderstart." +".$total_hours."hour"));

                    $avoidorder = false;

                    foreach($order_times as $order_time)
                    {
                        if(($orderstart > $order_time['start'] && $orderstart <$order_time['end']) || ($orderend > $order_time['start'] && $orderend < $order_time['end'])){
                            $avoidorder = true;
                            break;
                        }
                    }

                   if($avoidorder){ continue; }


                    //if (empty($timeframe_detail))
                    {
						$status =  get_post_status($order->ID);
                        $order_id = $order->ID;
                        $first_name = get_post_meta($order_id, '_billing_first_name', true);
                        $last_name = get_post_meta($order_id, '_billing_last_name', true);
                        $Billing = get_post_meta($order_id, '_billing_address_index', true);
                        $address = get_post_meta($order_id, '_billing_address_1', true);
						$service_description = get_post_meta( $order->ID, 'service_description', true );
                        $phone = get_post_meta($order_id, '_billing_phone', true);
                        //$address = str_replace($first_name, '', $Billing);
                        //$address = str_replace($last_name, '', $address);
                        $getorder = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items " ."WHERE order_id = " . $order_id . "; ";
                        $order_details = $wpdb->get_results(($getorder));
                        $phn_str = '';
                        ?>

                        <div class="customer-table">
                            <?php
                            $i = 0;
                            $total_hours = $date = $time = "";
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
                                                                                <!--<p><?php echo $od->order_item_name; ?></p><hr/>-->
                                    <div class="customer">
                                        <p><?php _e("Customer Name", "Avada");?></p>
                                        <span><?php echo $first_name . ' ' . $last_name; ?></span>
                                    </div>
                                    <div class="hours" >
                                        <p><?php _e("Hours Hired", "Avada");?></p>
                                        <span><?php echo $service_hour; ?></span>
                                    </div>
                                    <div style="width:100%;border-bottom: 1px solid #eee;float: left;"></div>
                                    <p style="padding-left: 6px;"><?php _e("As Scheduled", "Avada");?>
									<?php if($status =='racactive'){ echo "  (Orden Recurrente)"; }
										?>
							
									</p>
                                    <table>
                                        <tr>
                                            <td class="jobs_table2"><?php _e("Date", "Avada");?></td>
                                            <td class="table-right"><?php echo date("d F, Y", strtotime($date)); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="jobs_table2"><?php _e("Time", "Avada");?></td>
                                            <td class="table-right"><?php echo $time; ?></td>
                                        </tr>
                                        <tr class="address_box">
                                            <td class="jobs_table2"><?php _e("Address", "Avada");?></td>
                                            <td><?php echo substr($address,0,42); ?></td>
                                        </tr>
                                          <tr class="address_box">
                                        <td class="jobs_table2"><?php _e("Descripción del servicion", "Avada");?></td>
                                        <td class="table-right "><?php echo $service_description;?></td>
                                    </tr>
                                    </table>
                                    <hr>
                                    <p style="padding-left: 6px;"><?php _e("Order Details", "Avada");?> - #<?php echo $order_id; ?></p>
                                    <table>
                                        <tr>
                                            <td class="jobs_table2"><?php _e("Bedrooms", "Avada");?></td>
                                            <td class="table-right"><?php
                                                if (get_post_meta($product_id, '_select_bedroom_filter', true) == 1)
                                                    echo $bedroom;
                                                else
                                                    echo "-"; //;if(get_post_meta($product_id,'_select_bedroom_filter',true)==1){ echo $bedroom."Bedrooms | ".$bathroom."Bathrooms"; } else echo "-";
                                                ?></td>
                                        </tr>
                                        <tr>
                                            <td class="jobs_table2"><?php _e("Bathrooms", "Avada");?></td>
                                            <td class="table-right"><?php
                                                if (get_post_meta($product_id, '_select_bedroom_filter', true) == 1)
                                                    echo $bathroom;
                                                else
                                                    echo "-"; //if(get_post_meta($product_id,'_select_bedroom_filter',true)==1){ echo $bedroom."Bedrooms | ".$bathroom."Bathrooms"; } else echo "-";
                                                ?></td>
                                        </tr>
                                        <tr>
                                            <td class="jobs_table2"><?php _e("Extras", "Avada");?></td>
                                            <td class="table-right"><?php
                                                if (!empty($extra_services))
                                                    echo $service_string;
                                                else
                                                    echo "Sin Extras"
                                                    ?></td>
                                        </tr>
                                        <?php
                                    }
                                    $i++;
                                }
                                $current_user = wp_get_current_user();
                                $start_time = date("H:i:s", strtotime($time));
                                $end_time = trim(date('H:i:s', strtotime('+' . $total_hours . ' hours', strtotime($start_time))));
                                $new_hours = ($total_hours + 2);
                                $timeframe_end_time = trim(date('H:i:s', strtotime('+' . $new_hours . ' hours', strtotime($start_time))));

                                ?>
                                <!--<tr>
                                    <td class="jobs_table2 common-phone"><p class="phone">Phone</p></td>
                                    <td class="table-right common-phone">
                                        <p class="phone_no"><?php echo get_post_meta($order_id, '_billing_phone', true); ?></p></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="jobs_table2 common-phone"><p class="phone">Cell NO</p></td>
                                    <td class="table-right common-phone">
                                        <p class="phone_no"><?php echo get_post_meta($order_id, '_billing_cell', true); ?></p></div>
                                    </td>
                                </tr>-->

                                <tr>
                                    <td colspan="2" close-btn>
                                        <form method="post" action="">
                                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
                                            <input type="hidden" name="user_id" value="<?php echo $current_user->ID; ?>" />
                                            <input type="hidden" name="hours" value="<?php echo $total_hours; ?>" />
                                            <input type="hidden" name="start_time" value="<?php echo $start_time; ?>" />
                                            <input type="hidden" name="end_time" value="<?php echo $end_time; ?>" />
                                            <input type="hidden" name="date" value="<?php echo $date; ?>" />
                                            <input type="hidden" name="timeframe_end_time" value="<?php echo $timeframe_end_time; ?>" />
                                            <button class="assign" type="submit" value="Assign" name="job_assign">Asignar</button>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </div>


                        <?php
                        $user_info = get_userdata($current_user->ID);
                    }
                }

                // echo "<div style='width: 100%; clear: both;'><pre>";
               // print_r($order_times);
              //  echo "<pre></div>";

            }
            else {
                ?>
                <h3><?php _e("You have not available jobs in your area at this moment. Please check later", "Avada");?></h3>
                <?php
            }
            //}
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
<?php
wp_footer();
 include( get_template_directory() . '/footer.php'); ?>
<?php //get_footer(); ?>