<?php
/* Template Name: Order Confirm Mail */

global $wpdb;

 
$tbl1 = $wpdb->prefix . 'posts';
$tbl2 = $wpdb->prefix . 'postmeta';
$job_data = $wpdb->get_results("SELECT $tbl1.*,pm5.meta_value AS assigned_user_id
                         FROM $tbl1 
                         LEFT JOIN $tbl2 AS pm5 ON ($tbl1.ID = pm5.post_id)
                         Where $tbl1.post_type = 'shop_order' and $tbl1.post_status= 'wc-processing'
                         AND pm5.meta_key='assigned_user_id' and pm5.meta_value!='0'
                         ORDER BY $tbl1.post_date DESC");

if(!empty($job_data)){
    foreach($job_data as $job){
        $order_id=$job->ID;
        $order_date=get_post_meta($order_id,"order_date",true);
        $billing_address=get_post_meta($order_id,"_billing_address_1",true);
        $billing_phone=get_post_meta($order_id,"_billing_phone",true);
        $billing_cell=get_post_meta($order_id,"_billing_cell",true);
        $assigned_user_id=get_post_meta($order_id,"assigned_user_id",true);
        $user_info = get_userdata($assigned_user_id);
        $staff_id=get_user_meta($assigned_user_id,"personal_id",true);
        $phone1=get_user_meta($assigned_user_id,"phone1",true);
        $phone2=get_user_meta($assigned_user_id,"phone2",true);
        $query = "SELECT * " . "FROM {$wpdb->prefix}woocommerce_order_items " .
                        "WHERE order_id = " .$order_id . " GROUP BY order_id; ";
        $detail = $wpdb->get_results($query);
        $order_item_id = $detail[0]->order_item_id;
        $date = wc_get_order_item_meta($order_item_id, 'date', true);
        $time = wc_get_order_item_meta($order_item_id, 'time', true);
        $newdate=date("jS F Y",strtotime($date));
        $cdate=date("Y-m-d H:i:s",strtotime("$date $time"));
        $check_date_time=date("Y-m-d H",strtotime('-12 hours',strtotime($cdate)));
        $current_time=date("Y-m-d H");
        //echo $date."==>".$cdate."==>".$check_date_time."==>".$current_time."<br/>";//exit;
        if($check_date_time==$current_time){
            $recommended_hour = wc_get_order_item_meta($order_item_id, 'recommended_hour', true);
            $extra_hours = wc_get_order_item_meta($order_item_id, 'extra_hours', true);
            $total_hours = $recommended_hour + $extra_hours;
            $bedroom = wc_get_order_item_meta($order_item_id, 'bedroom', true);
            $bathroom = wc_get_order_item_meta($order_item_id, 'bathroom', true);
            $extra_services = wc_get_order_item_meta($order_item_id, 'extra_services', true);

            $services_array = array();
            if (!empty($extra_services)) {
                foreach ($extra_services as $services) {
                    $services_array[] = $services['title'];
                }
            }
            $service_string = "";
            if (!empty($services_array))
                $service_string = implode(",", $services_array);
        
            $message.='<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%"><tbody><tr><td align="center" valign="top">
                        <div id="template_header_image"></div>
                        <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important; background-color: #ffffff; border: 1px solid #dedede; border-radius: 3px !important">
                        <tbody><tr><td align="center" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header" style="background-color: #6cabdd; border-radius: 3px 3px 0 0 !important; color: #202020; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif"><tbody><tr><td id="header_wrapper" style="padding: 36px 48px; display: block">
												<h1 style="color: #202020; font-family:Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #89bce4; -webkit-font-smoothing: antialiased">Recordatorio de Cita</h1>
											</td>
										</tr></tbody></table></td>
							</tr><tr><td align="center" valign="top">
									
									<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body"><tbody><tr><td valign="top" id="body_content" style="background-color: #ffffff">
												
												<table border="0" cellpadding="20" cellspacing="0" width="100%"><tbody><tr><td valign="top" style="padding: 48px;padding-bottom:10px !important;">
															<div id="body_content_inner" style="color: #636363; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left">

                                                        <p style="margin: 0 0 16px">Este es un recordatorio para su cita con Bukerz el <strong>'.$newdate." ".$time.'.</strong> Por favor revise los detalles de su orden</p>

                                                      
                                                        <h2 style="color: #6cabdd; display: block; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 16px 0 8px; text-align: left">Orden #'.$order_id.'</h2>       
                                                        <ul>
                                                         <li>
                                                        <strong>Direcci&oacute;n:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$billing_address.'</span>
                                                        </li>
                                                        <li>
                                                        <strong>Tel&eacute;fono:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$billing_phone.'/'.$billing_cell.'</span>
                                                        </li>
                                                        <li>
                                                        <strong>Hora:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$total_hours.'</span>
                                                        </li>
                                                            <li>
                                                        <strong>Habitaciones:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$bedroom.'</span>
                                                        </li>
                                                        <li>
                                                        <strong>Ba&ntilde;os:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$bathroom.'</span>
                                                        </li>
                                                        <li>
                                                        <strong>Extras:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$service_string.'</span>
                                                        </li>
                                                        </ul>
                                                        <h2 style="color: #6cabdd; display: block; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 16px 0 8px; text-align: left">
                                                        <p>Informaci&oacute;n</p> de empleado asignado</h2>
                                                        <div style="float:left;">'.get_avatar( $user->ID, 100).'</div>
                                                        <ul style="float:left;">
                                                         <li>
                                                        <strong>Nombre:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$user_info->user_login.'</span>
                                                        </li>
                                                        <li>
                                                        <strong>ID de Personal:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$staff_id.'</span>
                                                        </li>
                                                        <li>
                                                        <strong>Tel&eacute;fono:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$phone1.'</span>
                                                        </li>
                                                            <li>
                                                        <strong>Tel&eacute;fono Alterno:</strong> <span class="text" style="color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">'.$phone2.'</span>
                                                        </li>
                                                        </ul><br>
                                                        </td>
                                                       
							</tr>
                                                        <tr><td style="padding-left: 48px;padding-right: 48px;line-height:150%;font-size:14px;color: #3c3c3c; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif"><p style="margin: 0 0 16px">Si usted tiene alguna pregunta acerca de esta orden sientase libre de contactarnos a info@bukerz.com o ll&aacute;menos al 6342 6597</td></tr>
                                                        <tr><td align="center" valign="top">
									
                                                        <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer"><tbody><tr><td valign="top" style="padding: 0; -webkit-border-radius: 6px">
                                                                    <table border="0" cellpadding="10" cellspacing="0" width="100%"><tbody><tr><td colspan="2" valign="middle" id="credit" style="padding: 0 48px 48px 48px; -webkit-border-radius: 6px; border: 0; color: #a7cdeb; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center">
                                                                                <p>2017 – Bukerz Panama<br>Vivendi Towers, Torre 300<br>Edison Park, Calle Samuel J. Eses,<br>+507 000 0000</p>
                                                                                    </td>
                                                                            </tr></tbody></table></td>
                                                    </tr></tbody></table></td>
							</tr></tbody></table></td>
				</tr></tbody></table>';   
                    //echo $message;
                    $email=$user_info->user_email;
                    $fullname=$user_info->display_name;
                    $email_subject = "Recordatorio de Cita: $email";
                    $headers = array('Content-Type: text/html; charset=UTF-8','From: <bukerz.com');
                    if(wp_mail($email,$email_subject,$message,$headers)) {
                        //echo json_encode(array("result"=>"complete"));
                    }
        }   
        
    }
}

/*$date=date("Y-m-d");
//echo $message;
$args1 = array(
    'role' => 'employees',
    'orderby' => 'user_nicename',
    'order' => 'ASC'
);
$employees = get_users($args1);
if(!empty($employees)){
    foreach ($employees as $user) {
        $email=$user->user_email;
        $fullname=$user->display_name;
        $email_subject = "example intro: $email";
        $headers = array('Content-Type: text/html; charset=UTF-8','From: <bukrez.com');
        //$headers = 'From: '. $fullname .' <'. $email .'>' . "\r\n";
        if(wp_mail($email,$email_subject,$message,$headers)) {
            //echo json_encode(array("result"=>"complete"));
        }
    }
}*/
?>
<?php
wp_footer();
 include( get_template_directory() . '/footer.php'); ?>


