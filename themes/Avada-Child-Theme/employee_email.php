<?php

/* Template Name: Employee Mail */
if(wp_mail("noreply@bukerz.com","testing2","testing")){
 echo json_encode(array("result"=>"complete"));
 }
 $roles = array('employees', 'freelancers');
    $blogusers=array();
    foreach ($roles as $role) :
        $args1 = array(
            'role' => $role,
            'orderby' => 'user_nicename',
            'order' => 'ASC'
        );
        $results = get_users($args1);     
        if($results) $blogusers = array_merge($blogusers, $results);
    endforeach;

$date = date("Y-m-d");
// Array of WP_User objects.
$area_radius = get_option('area_radius');
foreach ($blogusers as $user) {
    $user_id = $user->ID;
    $service = '';
    $user_services = unserialize(get_user_meta($user_id, "user_services", true));
    if (!empty($user_services)) {
        $service = implode(",", $user_services);
    }
    $latitude = get_user_meta($user_id, "latitude", true);
    $longitude = get_user_meta($user_id, "longitude", true);

    if ($latitude && $longitude && $service != '') {
        $tbl1 = $wpdb->prefix . 'posts';
        $tbl2 = $wpdb->prefix . 'postmeta';

        $consulta = "SELECT $tbl1.*,  
                                pm2.meta_value AS cust_latitude, 
                                pm3.meta_value AS cust_longitude, 
                                pm4.meta_value AS product_service, 
                                pm5.meta_value AS assigned_user_id, 
                                ( 6371 * ACOS( COS( RADIANS($latitude) ) * COS( RADIANS( pm2.meta_value ) ) * COS( RADIANS( pm3.meta_value ) - RADIANS($longitude) ) + SIN( RADIANS($latitude) ) * SIN( RADIANS( pm2.meta_value ) ) ) ) AS distance   
                         FROM $tbl1 
                         LEFT JOIN $tbl2 AS pm2 ON ($tbl1.ID = pm2.post_id AND pm2.meta_key='cust_latitude') 
                         LEFT JOIN $tbl2 AS pm3 ON ($tbl1.ID = pm3.post_id AND pm3.meta_key='cust_longitude')
                         LEFT JOIN $tbl2 AS pm4 ON ($tbl1.ID = pm4.post_id)
                         LEFT JOIN $tbl2 AS pm5 ON ($tbl1.ID = pm5.post_id)
                         Where $tbl1.post_type = 'shop_order' and $tbl1.post_status= 'wc-processing'
                         AND pm4.meta_key='product_service' and pm4.meta_value IN($service) 
                         AND pm5.meta_key='assigned_user_id' and pm5.meta_value='0'
                         and $tbl1.post_date like '%$date%'
                         HAVING distance < $area_radius
                         ORDER BY $tbl1.post_date DESC";
                 
        //echo $con;exit;                 

        $orders = $wpdb->get_results($consulta);

        $i = 1;
        if (!empty($orders)) {
            $message = '';
            $message = '<table id="template_container" style="box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important; background-color: #ffffff; border: 1px solid #dedede; border-radius: 3px !important" width="600" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                    <tr>
                        <td valign="top" align="center">
                            <table id="template_header" style=" border-radius: 3px 3px 0 0 !important; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; background-color:#e5e5e5;" width="600" cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                    <tr>
                                        <td id="header_wrapper" style="padding: 36px 48px; display: block; text-align: center; ">
                                            <img  src="' . site_url('wp-content/uploads/2017/05/logo.png') . '" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="center">
                            <table id="template_body" width="600" cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                    <tr>
                                        <td id="body_content" style="background-color: #ffffff" valign="top">
                                            <table width="100%" cellspacing="0" cellpadding="20" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="padding: 48px" valign="top">
                                                            <div id="body_content_inner" style="color: #636363; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left">
                                                                <p style="margin: 0 0 16px; font-size:24px; font-weight:900;">Nuevas oportunidades disponibles en tu 谩rea</p>';

            foreach ($orders as $ord) {
                if ($i < 6) {
                    $service_hr = 0;
                    $extra_service = 0;
                    $order_meta = get_post_meta($ord->ID);
                    $orderitem = new WC_Order($ord->ID);
                    $items = $orderitem->get_items();
                    foreach ($items as $key => $product) {
                        $tblit = $wpdb->prefix . 'woocommerce_order_itemmeta';
                        $itemmeta = "SELECT * FROM $tblit Where order_item_id = $key AND meta_key='service_hour'";
                        $itemmetadata = $wpdb->get_results($itemmeta);
                        $service_hr = $itemmetadata[0]->meta_value;

                        $itemmetaextra = "SELECT * FROM $tblit Where order_item_id = $key AND meta_key='extra_services'";
                        $itemmetadataext = $wpdb->get_results($itemmetaextra);
                        $extra_data = unserialize($itemmetadataext[0]->meta_value);
                        $all_extra_service = array();
                        if (!empty($extra_data)) {
                            foreach ($extra_data as $extra) {
                                $all_extra_service[] = $extra['title'];
                            }
                        }
                    }

                    $extra_service = implode(',', $all_extra_service);
                    if ($extra_service == '') {
                        $extra_service = '-';
                    }

                    $_paid_date = date_create($order_meta['_paid_date'][0]);
                    $booking_date = date_format($_paid_date, "F d G:ia");

                    $message .='<table class="td" style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; color: #636363;border-bottom: 1px solid #eee; border-top: 1px solid #eee;" cellspacing="0" cellpadding="6">
                                                                        <tbody>                                                                            
                                                                            <tr class="order_item">
                                                                                <td class="td" style="text-align: left; vertical-align: top; word-wrap: break-word; color: #636363; width: 30%;">
                                                                                    <b>Direcci贸n</b>
                                                                                </td>
                                                                                <td class="td" style="text-align: right; vertical-align: middle; color: #636363; width: 30%;">
                                                                                    ' . $order_meta['_billing_address_1'][0] . '
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="order_item">
                                                                                <td class="td" style="text-align: left; vertical-align: top; word-wrap: break-word; color: #636363; width: 30%;">
                                                                                    <b>Fecha de Limpieza</b>
                                                                                </td>
                                                                                <td class="td" style="text-align: right; vertical-align: middle; color: #636363; width: 30%;">
                                                                                    ' . $booking_date . '
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="order_item">
                                                                                <td class="td" style="text-align: left; vertical-align: top; word-wrap: break-word; color: #636363; width: 30%;">
                                                                                    <b>Horas contratadas</b>
                                                                                </td>
                                                                                <td class="td" style="text-align: right; vertical-align: middle; color: #636363;width: 30%;">
                                                                                    ' . $service_hr . ' Hour
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="order_item">
                                                                                <td class="td" style="text-align: left; vertical-align: top; word-wrap: break-word; color: #636363; width: 30%;">
                                                                                    <b>Extra Servicios</b>
                                                                                </td>
                                                                                <td class="td" style="text-align: right; vertical-align: middle; color: #636363; width: 30%;">
                                                                                    ' . $extra_service . '
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>';
                }
                $i++;
            }

            $message.='</div>
                                                        </td>
                                                    </tr></tbody></table></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr> 
                    <tr><td valign="top" align="center">
                    <table id="addresses" style=" margin:20 px; width: 100%; vertical-align: top" cellspacing="0" cellpadding="0" border="0"><tbody><tr>
                    <td class="td" style="color: #636363; padding: 12px; text-align:center;" width="50%" valign="top">
			<a style="background-color:#4CAF50 !important; color:#ffffff !important;  text-decoration: none; margin-top:20px; font-size:16px; border: 1px solid #4CAF50; padding:10px; font-weight:900; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; " href="' . site_url() . '">MÁS OPORTUNIDADES</a>
                        </td>
			</tr>                       
                        </tbody>
                        </table>
                    </tr> 
                    <tr><td valign="top" align="center">
                    <table id="addresses" style=" margin:20 px; width: 100%; vertical-align: top" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="td" style="text-align: left; color: #636363; padding: 12px" width="50%" valign="top">
			<p class="text" style="margin-left:40px !important; color: #3c3c3c; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; margin: 0 0 16px">
                        Gracias por hacer negocios con nosotros,<br>Equipo de Bukerz
                        </p>
		         </td>
			</tr></tbody></table>
                    </tr> 
                    
                    <tr><td valign="top" align="center">
                            <table id="template_footer" width="600" cellspacing="0" cellpadding="10" border="0"><tbody><tr><td style="padding: 0; -webkit-border-radius: 6px" valign="top">
                                            <table width="100%" cellspacing="0" cellpadding="10" border="0">
                                                <tbody><tr><td colspan="2" id="credit" style="padding: 0 48px 48px 48px; -webkit-border-radius: 6px; border: 0; color: #636363; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center" valign="middle">
                                                            <p>Bukerz.com - Relax and enjoy!</p>
                                                        </td>
                                                    </tr></tbody></table></td>
                                    </tr></tbody></table></td>
                    </tr>
                </tbody>
            </table>';

            $email = $user->user_email;
            $fullname = $user->display_name;
            //$message=$message;
            $email_subject = "Nuevas Oportunidades en bukerz.com: $email";
            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: info@bukerz.com');
            //$headers = 'From: '. $fullname .' <'. $email .'>' . "\r\n";
            if (wp_mail($email, $email_subject, $message, $headers)) {
               //echo "Send";
            } else {
               //var_dump($GLOBALS['phpmailer']->ErrorInfo);
           }
        }
    }
}
?>
<?php
wp_footer();
 include( get_template_directory() . '/footer.php'); ?>