<?php
/* Template Name: Asigned Jobs */
ob_start();
if (is_user_logged_in()) {

} elseif (!is_user_logged_in()) {
   wp_redirect(site_url());
}
get_header(); ?>
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



td, th {
/*    border: 1px solid #dddddd;*/
    text-align: left;
    padding: 5px;
}

tr:nth-child(even) {
/*    background-color: #dddddd;*/
}
.customer-table{
    width: 500px;
    height: 540px;
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
    /*background: #6cabdd;*/
    background: #ea6767;
    color: #fff;
    border: 0;
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 16px;
    box-shadow: 2px 2px 4px 1px #ddd;
    display: table;
    margin:0px auto;
}


#close_form textarea#note {
    width: 100%;
    height: 80px;
}
.modal-header h4 {
    margin: 0px;
    line-height: 20px;
}
.modal-header {
    padding: 5px 20px;
}
.modal-body {
    position: relative;
    padding: 0px 20px;
}
.modal-content {
    background-color: #cce9ff;
}
.modal-title p {
    font-size: 30px;
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
td{
    line-height: 20px;
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

</style>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
        global $wpdb;
        $current_user = wp_get_current_user();
        $user_id=$current_user->ID;
        $user_info = get_userdata($user_id);
        $user_info = get_userdata($current_user->ID);
        if(isset($_POST['note'])){
            extract($_POST);
            $user = get_user_by( 'id',$user_id );
            if($user && wp_check_password( $password, $user->data->user_pass, $user_id)){
                $jobs_completed = get_user_meta( $user_id, '_jobs_completed', true );
                $jobs_completed=$jobs_completed+1;
                update_user_meta($user_id,'_jobs_completed',$jobs_completed);
                $order = new WC_Order($order_id);
                $order->update_status("completed");
                $order->add_order_note($note);
                update_post_meta($order_id,'completed_date',date("Y-m-d H:i:s"));
                $total=$order->get_total();
                $user_meta=get_userdata($user_id);
                $user_roles=$user_meta->roles;
                $user_role=$user_roles[0];
                if($user_role=="freelancers"){
					$user_commision_type = get_user_meta($user_id,'user_commision_type',true);
					if($user_commision_type == 'hourly'){
						$commision=get_user_meta($user_id,'user_pay_by_hour',true);
						 
						$order = wc_get_order($order_id);
   						$items = $order->get_items();
						
						 if (!empty($items)) {
							$i = 0;
							foreach ($items as $key => $product) {
								if ($i == 0) {
									$service_hour = wc_get_order_item_meta($key, 'service_hour', true);
								}
							}
						}
						 
						 $csh = explode(':', $service_hour);
						 $m_comm =  (($csh[1]*$commision)/60);
						 $h_comm = $csh[0]*$commision;
						 $t_comm = $m_comm + $h_comm;
						 $total_commision = $t_comm;
					}
					else{
                    	$commision=get_user_meta($user_id,'user_commision',true);
                    	$total_commision=(($total*$commision)/100);
					}
					 update_post_meta($order_id,'user_commission',$total_commision);
                    $total_payment = get_user_meta( $user_id, '_total_payment', true );
                    $total_payment=($total_payment+$total_commision);
                    update_user_meta($user_id,'_total_payment',$total_payment);
                }
               /* $mailer = WC()->mailer();
                $mails = $mailer->get_emails();
                if ( ! empty( $mails ) ) {
                    foreach ( $mails as $mail ) {
                        if ( $mail->id == 'customer_completed_order' ) {
                           $mail->trigger($order_id);
                        }
                     }
                }*/

                /*Order user email=======================================================
                $first_name = get_post_meta( $order_id, '_billing_first_name', true );
                $last_name = get_post_meta($order_id, '_billing_last_name', true );
                $email = get_post_meta($order_id, 'billing_email', true );
                $fullname=$first_name." ".$last_name;
                $email_subject = "Order Completed: $email";
                $feedback_url=site_url("contact");
                $message="Hello $fullname,"
                        . "<br/>Your order is completed successfuly."
                        . "<br/>Please give feedback : <a href='$feedback_url'>Feedback</a>"
                        . "<br/>Thanks & Regards"
                        . "<br/><a href='bukrez.com'>bukrez.com</a>";
                $headers = array('Content-Type: text/html; charset=UTF-8');
                if(wp_mail($email,$email_subject,$message,$headers)) {
                    //echo json_encode(array("result"=>"complete"));
                }
                //Assign User mail==========================================================
                $email_subject = "Order Completed: $user_info->email";
                $message="Hello $user_info->first_name  $user_info->last_name,"
                        . "<br/>Order #$order_id is completed successfuly."
                        . "<br/>Thanks & Regards"
                        . "<br/><a href='bukrez.com'>bukrez.com</a>";
                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($user_info->email,$email_subject,$message,$headers);

                //Admin mail=================================================================
                $args1 = array(
                    'role' => 'administrator',
                    'orderby' => 'user_nicename',
                    'order' => 'ASC'
                );
                $employees = get_users($args1);
                if(!empty($employees)){
                    foreach ($employees as $user) {
                        $email=$user->user_email;
                        $fullname=$user->display_name;
                        $message="Hello $fullname,"
                        . "<br/>Order #$order_id is completed successfuly."
                        . "<br/>Thanks & Regards"
                        . "<br/><a href='bukrez.com'>bukrez.com</a>";
                        $email_subject = "Order Completed: $email";
                        $headers = array('Content-Type: text/html; charset=UTF-8','From: <swatidatir@moderni.in>');
                        wp_mail($email,$email_subject,$message,$headers);
                    }
                }

                //===========================================================================*/

                global $wpdb;
                $wpdb->query("UPDATE tblassignjobs SET status='completed' WHERE order_id=".$order_id);

                echo '<div class="alert alert-success">
                            <strong>Completado!</strong> Su trabajo se ha cerrado correctametne.
                      </div>';
            }
            else{
                echo '<div class="alert alert-danger">
                        <strong>Oops!</strong> Por favor ingrese su clave correctamente.
                      </div>';
            }
        }

        $tbl1=$wpdb->prefix.'posts';
        $tbl2=$wpdb->prefix.'postmeta';
        $simple_query="SELECT $tbl1.*,pm2.meta_value AS assigned_user_id
                    FROM $tbl1
                    RIGHT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='assigned_user_id' and pm2.meta_value='$current_user->ID') 
                    WHERE $tbl1.post_type = 'shop_order' and ( $tbl1.post_status ='wc-processing' )
                    ORDER BY $tbl1.post_date DESC";
        $total_count=count($wpdb->get_results($simple_query));
        $per_page=12;
        $pid=0;
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
        $pid=(($paged-1)*$per_page);
        //echo $pid."==>".$per_page;
        $consulta ="SELECT $tbl1.*,pm2.meta_value AS assigned_user_id
                    FROM $tbl1
                    RIGHT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='assigned_user_id' and pm2.meta_value='$current_user->ID') 
                    WHERE $tbl1.post_type = 'shop_order' and ( $tbl1.post_status ='wc-processing' )
                    ORDER BY $tbl1.post_date DESC LIMIT $pid,$per_page";
        $orders = $wpdb->get_results($consulta);
        if(!empty($orders)){
            $order_times = array();
        ?>
        <div class="assigned_jobs" >
        <?php
		foreach ($orders as $order) {
			$Billing = get_post_meta( $order->ID, '_billing_address_index', true );
			$total = get_post_meta( $order->ID, '_order_total', true );
			$assign_user_id = get_post_meta( $order->ID, 'assigned_user_id', true );
			$odr_status = '';
            $order_status=$order->post_status;
			if($order->post_status == 'wc-processing') $odr_status = 'In Process';
			if($order->post_status == 'wc-completed') $odr_status = 'Closed';
            $job = "SELECT * " ."FROM {$wpdb->prefix}woocommerce_order_items " . "WHERE order_id = ".$order->ID." GROUP BY order_id; ";
            
            $job_detail = $wpdb->get_results($job);
            $order_item_id = $job_detail[0]->order_item_id;
            $recommended_hour = wc_get_order_item_meta($order_item_id, 'recommended_hour', true);
            $extra_hours = wc_get_order_item_meta($order_item_id, 'extra_hours', true);
            $total_hours = $recommended_hour + $extra_hours;
            $date = wc_get_order_item_meta( $order_item_id, 'date', true );
            $time = wc_get_order_item_meta( $order_item_id, 'time', true );
            $user_info = get_userdata($assign_user_id);
            $orderstart = date('Y-m-d H:i:00', strtotime($date." ".$time));
            $orderend   = date('Y-m-d H:i:s', strtotime($orderstart." +".$total_hours."hour"));
            $orderstart = date('Y-m-d H:i:00', strtotime($orderstart." -2hour"));
            $orderend = date('Y-m-d H:i:00', strtotime($orderend." +2hour"));
            $avoidorder = false;
            if(count($order_times) == 0)
            {
                $order_times[] = array(
                    "start" => $orderstart,
                    "end"  => $orderend,
                    "hour"  => $total_hours
                );
            }else{
                foreach($order_times as $order_time)
                {
                    if(($orderstart >= $order_time['start'] && $orderstart <= $order_time['end']) || ($orderend >= $order_time['start'] && $orderend <= $order_time['end'])){
                        $avoidorder = true;
                        break;
                    }
                }
                if($avoidorder != false){
                    $order_times[] = array(
                        "start" => $orderstart,
                        "end"  => $orderend,
                        "hour"  => $total_hours
                    );
                }
            }

            //if($avoidorder){ continue; }

            ///////////////////////////////////////////////////////////////////////////
            $current_user = wp_get_current_user();
            $user_jobs = "SELECT post_id " ."FROM {$wpdb->prefix}postmeta " .
                            "WHERE meta_key = 'assigned_user_id' AND meta_value = ".$current_user->ID."; ";
            $user_assign_orderids = $wpdb->get_results(( $user_jobs ) );
            
            $my_order_ids = array();
            foreach ($user_assign_orderids as $uaoi) {
                    $my_order_ids[] = $uaoi->post_id;
            }
            $my_jobs_datetime = array();
            if(count($my_order_ids) > 0)
            {
                $myorder = "SELECT * " ."FROM {$wpdb->prefix}woocommerce_order_items " .
                            "WHERE order_id in (".implode(',', $my_order_ids).") group by order_id;";
                $myorder_details = $wpdb->get_results(( $myorder ) );
                
                $r = 0;
                foreach ($myorder_details as $mod) {
                    $mydate = wc_get_order_item_meta( $mod->order_item_id, 'date', true );
                    $mytime = wc_get_order_item_meta( $mod->order_item_id, 'time', true );
                    $myjob_hours = wc_get_order_item_meta( $mod->order_item_id, 'recommended_hour', true );
                    $my_jobs_datetime[$r]['dt'] = $mydate.' '.$mytime;
                    $my_jobs_datetime[$r]['hours'] = $myjob_hours;
                    $r++;
                }
            }
            $first_name = get_post_meta( $order->ID, '_billing_first_name', true );
            $last_name = get_post_meta( $order->ID, '_billing_last_name', true );
            $email = get_post_meta( $order->ID, 'billing_email', true );
            $Billing = get_post_meta( $order->ID, '_billing_address_index', true );
            $phone = get_post_meta( $order->ID, '_billing_phone', true );
            
            //	Sanket added
            $billing_comments = get_post_meta( $order->ID, '_billing_comments', true );
            $service_description = get_post_meta( $order->ID, 'service_description', true );
            //	Sanket end
            
            $address = str_replace($first_name, '', $Billing);
            $address = str_replace($last_name, '', $address);
            $address = get_post_meta($order->ID, '_billing_address_1', true);
            $order_query = "SELECT * " ."FROM {$wpdb->prefix}woocommerce_order_items
                            WHERE order_id = " . $order->ID . " GROUP BY order_id; ";
            $order_details = $wpdb->get_results($order_query);
            $assign_user_id = get_post_meta( $order->ID, 'assigned_user_id', true );
            $phn_str = '';
            if($assign_user_id > 0){
                $phn_str = '<tr>
                        <td class="jobs_table2 common-phone"><p class="phone">Teléfono</p></td>
                        <td class="table-right common-phone"><span class="phone_no">'.$phone.'</span></td>
                    </tr>';
            }
            ?>
            <div class="customer-table">
            <?php
                $i=0;
                if(!empty($order_details)){
                    
                    foreach ($order_details as $od) {
                        if($i==0){
                            $product_id = wc_get_order_item_meta($od->order_item_id, '_product_id', true);
                            $scheme = wc_get_order_item_meta($od->order_item_id, '_wcsatt_scheme', true);
                            //$this->key = implode( '_', array_filter( array( $this->data[ 'interval' ], $this->data[ 'period' ], $this->data[ 'length' ] ) ) );                            
                            if(strpos($scheme,'_')>0)list($interval, $period) = explode('_',$scheme);
                            $start_date = get_post_meta($order->ID,"order_date", true);
                            $time = wc_get_order_item_meta( $od->order_item_id, 'time', true );
                            $recommended_hour = wc_get_order_item_meta( $od->order_item_id, 'recommended_hour', true );
                            $service_hour = wc_get_order_item_meta( $od->order_item_id, 'service_hour', true );
                            //$date = wc_get_order_item_meta( $od->order_item_id, 'date', true );
                            $time = wc_get_order_item_meta( $od->order_item_id, 'time', true );
                            $bedroom = wc_get_order_item_meta( $od->order_item_id, 'bedroom', true );
                            $bathroom = wc_get_order_item_meta( $od->order_item_id, 'bathroom', true );
                            
                            $extra_services = wc_get_order_item_meta($od->order_item_id, 'extra_services', true);
                            $extra_hours = wc_get_order_item_meta($od->order_item_id, 'extra_hours', true);
                            $end_time = get_post_meta($order->ID, '_assigned_end_time', true );
                            $hours = get_post_meta($order->ID, '_assigned_hours', true );
                            $combinedDT = date('Y-m-d H:i:s', strtotime("$start_date $time"));
                            //$combinedDT = date('Y-m-d H:i:s', strtotime("$start_date $end_time"));

                            $total_hours=($recommended_hour+$extra_hours);
                            //$new_hours=($total_hours+2);
                            $new_hours=($total_hours);
                            $t = EXPLODE(":", $service_hour);
                            $h = $t[0];
                            if (isset($t[1])) {
                                $m = $t[1];
                            } else {
                                $m = "00";
                            }
                            if($m!='00') $mm =($h*60)+$m;else $mm=($h*60);
                            $stop_date = date("Y-m-d H:i:s", strtotime($start_date)+60*$mm);
                            //$new_time=trim(date('H:i:s',strtotime('+ '.$mm.' minutes',strtotime("$time"))));
                            //$close_hour=trim(date('Y-m-d H:i:s',strtotime(("$start_date $new_time"))));
                            
                            //echo $new_hours."==>".$close_hour."==>".$total_hours;
                            $services_array=array();
                            if(!empty($extra_services)){
                                foreach($extra_services as $services){
                                    $services_array[]=$services['title'];
                                }
                            }
                            $service_string="";
                            if(!empty($services_array))
                                $service_string=implode(",",$services_array);
                            ?>
                            <div class="customer">
                                <p><?php _e("Customer Name", "Avada");?></p>
                                                         
                                <span><?php echo  $first_name.' '.$last_name;//$order->ID;?></span>
                            </div>
                            <div class="hours">
                                <p><?php _e("Hours Hired", "Avada");?></p>
                                <span><?php echo $service_hour;?> </span>
                            </div>   
                            <hr>
                            <table>
                                <?php
                                echo $phn_str;
                                    ?>
                
                                    <tr><td class="jobs_table2 common-phone"><p class="phone"><?php _e("Cellphone", "Avada");?></p></td>
                                        <td class="table-right common-phone">
                                            <span class="phone_no"><?php  echo get_post_meta( $order->ID, '_billing_cell', true );?></span>
                                        </td>
                                    </tr>
                <!--<tr><td class='close-btn' colspan="2"><button class="btn btn-primary btn-lg assign open_model" disabled="" style=" background-color: #f0c7c7 !important; ">CLOSE JOB</button></td></tr>-->
                            </table>
                            <p style="padding-left: 6px;"><?php _e("As Scheduled", "Avada");?>
                            <?php   if($scheme != 0){ echo "  (Orden Recurrente)"; }
                                    ?>
                            </p>
                            <table>
                                <tr>
                                    <td class="jobs_table2"><?php _e("Fecha hora", "Avada");?></td>
                                    <td class="table-right"><?php  $date= date("d F, Y  h:i a",strtotime($start_date)); echo spanish_date($date);?></td>
                                </tr>
                                <!--<tr>
                    
                                    <td class="jobs_table2"><?php _e("Time", "Avada");?></td>
                                    <td class="table-right"><?php echo $time;?></td>
                                </tr>-->
                                <!--                                    <tr>
                                    <td style="width:20%;">End-Time</td>
                                    <td class="table-right"><?php //echo date('h:i A', strtotime($end_time));?></td>
                                </tr>-->
                                <tr class="">
                                    <td class="jobs_table2"><?php _e("Address", "Avada");?></td>
                                    <td><a href="https://maps.google.com/?q=<?php echo $address; ?>" target="_blank"><?php echo $address; ?></a></td>
                                </tr> 
                                <tr class="">
                                    <td class="jobs_table2"><?php _e("Referencias", "Avada");?></td>
                                    <td class="table-right "><?php echo $billing_comments;?></td>
                                </tr>
                                <tr class="">
                                    <td class="jobs_table2"><?php _e("Descripción del servicio", "Avada");?></td>
                                    <td class="table-right "><?php echo $service_description;?></td>
                                </tr>
                            </table>
                            <hr>
                            <p style="padding-left: 6px;"><?php _e("Order Details", "Avada");?> - #<?php echo $order->ID;?></p>
                            <span>
                                <?php  if(get_post_meta($product_id,'_select_bedroom_filter',true)==1) echo $bedroom."cuartos,";//;if(get_post_meta($product_id,'_select_bedroom_filter',true)==1){ echo $bedroom."Bedrooms | ".$bathroom."Bathrooms"; } else echo "-";?>
                                <?php if(get_post_meta($product_id,'_select_bedroom_filter',true)==1) echo $bathroom."baños,";//if(get_post_meta($product_id,'_select_bedroom_filter',true)==1){ echo $bedroom."Bedrooms | ".$bathroom."Bathrooms"; } else echo "-";?>
                                <?php  
                                    $order1 = new WC_Order($order->ID);
                                    $postmeta = get_post_meta($order->ID);
                                    $items = $order1->get_items();
                                    
                                                
                                    foreach ( $order1->get_items() as $item_id => $items ) { 
                                    
                                        $post = get_post_meta($order1->get_id());
                                        //print_r($order1);die;
                                    }
                    
                                    $cnt = 0;
                                    foreach( $order1->get_items('fee') as $item_id => $item ){
                                        echo $item->get_name().", ";
                                        $cnt++;
                                    }
                                ?>
                                <?php  if($cnt==0){ echo "NO Extras";}?>                                
                            </span>
                        <?php
                        }
                        $i++;
                    }
                }

                //$get_job = get_post( $order->ID );
                //echo $order_status."==>".$close_hour."<".date("Y-m-d H:i:s");
    
                ?>    </br>
                <?php
                                    if(($order_status == 'wc-processing') && (strtotime($stop_date)+18000 < time()) )
                                    {
                                        ?>
                                            <button class="btn btn-primary btn-lg assign open_model" data-toggle="modal"  data-id="<?php echo $order->ID;?>"><?php _e("Cerrar Trabajo", "Avada");?></button>
                                        <?php
                                    }
                                ?>   
                <div class="hidden">
                    <span><?= $start_date?></span>
                    <span><?= $stop_date?></span>
            </div>
        </div>
        <?php
		}
		?>
            </div>
        <br/>
        <div class="assigned_paginattion">
        <?php
            $big = 999999999; // need an unlikely integer
            $total=  round($total_count/$per_page);
            echo paginate_links( array(
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $total,
                'mid_size' => 1,
                'prev_text'    => '<<',
                'next_text'    => '>>',
                'type'         => 'list'
            ) );
        ?>
        </div>
	<?php

        }
        else{
            ?>
                <h3><?php _e("You have not assigned job at this moment. Please visit Available Jobs section to get new works", "Avada");?></h3>
            <?php
        }
        ?>

<?php endwhile; endif; ?>
<!-- Modal -->
<div class="modal fade" id="myModalNorm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <button type="button" class="close"
               data-dismiss="modal">
                   <span aria-hidden="true">&times;</span>
                   <span class="sr-only"><?php _e("Close", "Avada");?></span>
            </button>
            <h3 class="modal-title" id="myModalLabel" style="text-align:center;">
                <p<?php _e("Close Job ", "Avada");?></p>
            </h3>
            <h4 style="text-align:center;"><?php _e("Close the job you confirm you finish the work  <br/>correctly according our agreement. ", "Avada");?>
                   </h4>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
            <form role="form" method="post" name="close_form" id="close_form" class="close_form">
              <div class="form-group">
                <label for="exampleInputPassword1"><?php _e("Password", "Avada");?></label>
                <input type="password" class="form-control" id="password" name="password" placeholder="" required=""/>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1"><?php _e("Comments", "Avada");?></label>
                <!--<input type="text" class="form-control" id="note" name="note" placeholder="Note" required=""/>-->
                <textarea class="form-control" id="note" name="note" placeholder="" required=""></textarea>
              </div>
             
              <input type="hidden" name="order_id" id="model_order_id" value="" /><br/>
              <button type="button" id="close_button" name="close_button" class="btn btn-default assign close_button" style="width:100% !important;"><?php _e("Submit", "Avada");?></button>
            </form>
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer">
        </div>
    </div>
</div>
</div>
<?php 


?>
<script>
jQuery(document).ready(function($){
    $(".close_button" ).one("click", function() {
        $(".close_form").submit();
    });
   $(".open_model").click(function(event){ // Click to only happen on announce links cancel_model
      //event.preventDefault();
     $("#model_order_id").val($(this).data('id'));
     $('#myModalNorm').modal('show');
   });
   $(".cancel_model").click(function(){ // Click to only happen on announce linkscancel_model
     $("#modelcancel_order_id").val($(this).data('id'));
     $('#Cancelmodel').modal('show');
   });
});
</script>
<?php

wp_footer();
 include( get_template_directory() . '/footer.php'); ?>