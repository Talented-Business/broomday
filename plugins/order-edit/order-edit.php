<?php
/*
Plugin Name: Subscription Edit
Plugin URI: https://#/
Description: Subscription Order Edit.
Author: Lazutina
Author URI: https://#/
Version: 1.0
*/
add_action( 'woocommerce_my_subscriptions_actions', 'custom_valid_subscription_statuses_for_cancel', 10, 2 );
function custom_valid_subscription_statuses_for_cancel( $subscription ){
   $subscription_status = $subscription->get_status();
   //if($subscription_status == "active"){
       
       $nextpydate = get_post_meta($subscription->get_id(),'_schedule_next_payment',true);
       $st_ago = date('Y-m-d H:i:s', strtotime('-2 days', strtotime($nextpydate)));  
                   
                   if(strtotime('Y-m-d') < strtotime($st_ago)){
                        echo '<a href="'.esc_url( home_url('order-edit') ).'?orderid='.$subscription->get_id().'&type=subscriptions" class="woocommerce-button button view edit-button btn">Editar</a>';
                        
                        
                        ?>
                    <?php
                   }else{
                       
                       //echo "no cancel";
                       //echo '<a href="#" onclick="myFunction()" class="woocommerce-button button view">Cancel</a>';
                       ?>
               <script>
                function myFunction() {
                    alert("You can not cancel");
                }
                </script>
                        
                        <?php
                   }
       
   //}
    //echo "<pre>"; print_r($subscription); echo "</pre>";
}
function get_next_booking_date($subscription,$order,$order_item){
    $period = $subscription->get_billing_period();
    $interval = $subscription->get_billing_interval();
    $last_order_date = $subscription->get_date( 'last_order_date_created' );
    if($order){
        $order_id = $order->get_id();
        $start_date = get_post_meta($order_id,"order_date", true);
        if($start_date==0){
            $start_date = $last_order_date;
        }
        if(is_object($order_item)){
            $order_item_id = $order_item->get_id();
            $start_date = wc_get_order_item_meta( $order_item_id, 'date', true );
            $working_time = wc_get_order_item_meta( $order_item_id, 'time', true );
            $job_hours = wc_get_order_item_meta( $order_item_id, 'recommended_hour', true );
        }
    }else{
        $start_date = $last_order_date;
        $working_time = null;
        $job_hours = 3;
    }
    if($period=='day') $next_booking_date = date("Y-m-d H:i:s", strtotime($start_date." +$interval $period"));
    else $next_booking_date = date("Y-m-d H:i:s", strtotime($start_date." +$interval $period +3days"));
    if(strtotime($next_booking_date)<strtotime(date("Y-m-d H:i:s"))){
        $next_booking_date = date("Y-m-d H:i:s");
    }
    return array($next_booking_date,$working_time,$job_hours);
}
function time_options($time){
    $start_times = array(
        "07:00 AM"=>"7:00 AM",
        "07:30 AM"=>"7:30 AM",
        "08:00 AM"=>"8:00 AM",
        "08:30 AM"=>"8:30 AM",
        "09:00 AM"=>"9:00 AM",
        "09:30 AM"=>"9:30 AM",
        "10:00 AM"=>"10:00 AM",
        "10:30 AM"=>"10:30 AM",
        "11:00 AM"=>"11:00 AM",
        "11:30 AM"=>"11:30 AM",
        "12:00 PM"=>"12:00 PM",
        "12:30 PM"=>"12:30 PM",
        "01:00 PM"=>"1:00 PM",
        "01:30 PM"=>"1:30 PM",
        "02:00 PM"=>"2:00 PM",
        "02:30 PM"=>"2:30 PM",
        "03:00 PM"=>"3:00 PM",
        "03:30 PM"=>"3:30 PM",
        "04:00 PM"=>"4:00 PM",
        "04:30 PM"=>"4:30 PM",
        "05:00 PM"=>"5:00 PM",
        "05:30 PM"=>"5:30 PM",
        "06:00 PM"=>"6:00 PM",
        "06:30 PM"=>"6:30 PM",
        "07:00 PM"=>"7:00 PM",
        "07:30 PM"=>"7:30 PM",
        "08:00 PM"=>"8:00 PM"
    );
    $options = "";
    foreach($start_times as $key => $value){
        if($time == $key)$options .= "<option value='$key' selected>$value</option>"; 
        else $options .= "<option value='$key'>$value</option>"; 
    }
    return $options;
}
function get_discount_from_order_item($order_item_id,$product_id){
    $scheme = wc_get_order_item_meta($order_item_id, '_wcsatt_scheme', true);
    list($interval, $period) = explode('_',$scheme);
    $dd = get_post_meta($product_id,'_wcsatt_schemes',true);
    $types = maybe_unserialize($dd);
    $discount = 0;
    foreach($types as $type){
        if(($interval == $type['subscription_period_interval'])&&($period==$type['subscription_period']))
        $discount = $type['subscription_discount'];
    }
    return $discount;
}
function subscription_edit_func(){
    include_once 'edit-template.php';
}
add_shortcode( 'orderedit', 'subscription_edit_func' );
add_action('wp','update_subscription',10,0);
function update_subscription(){
    global $wp;
	if ( isset( $wp->query_vars['view-subscription'] ) && 'shop_subscription' == get_post_type( absint( $wp->query_vars['view-subscription'] ) ) && current_user_can( 'view_order', absint( $wp->query_vars['view-subscription'] ) ) && isset($_POST['subscription']) ) {
        $nonce_value = wc_get_var( $_REQUEST['woocommerce-order-edit'], wc_get_var( $_REQUEST['_wpnonce'], '' ) );
        if(wp_verify_nonce( $nonce_value, 'woocommerce-order-edit' )){
            $end_date = sanitize_text_field($_POST['subscription']['date']);
            $end_date .= " ".sanitize_text_field($_POST['subscription']['time']);
            $subscription = wcs_get_subscription( $wp->query_vars['view-subscription'] );
            //subscription update
            $timezone = get_option('timezone_string');
            if($subscription->get_billing_period()=="day" || $subscription->get_billing_period()=="days" ){
                $payment_date = date('Y-m-d H:i:s', strtotime("-3minutes", strtotime($end_date." ".$timezone)));
            }else{
                $payment_date = date('Y-m-d H:i:s', strtotime("-3days", strtotime($end_date." ".$timezone)));
            }
            $end_date = date('Y-m-d H:i:s', strtotime($end_date." ".$timezone));
            $subscription->update_dates(array('next_payment'=>$payment_date,'end'=>$end_date));   
            //billing_comments and subscription_description 
            $subscription->set_billing_address_1(sanitize_text_field($_POST['subscription']['billing_address_1']));
            $subscription->update_meta_data('billing_comments',sanitize_text_field($_POST['subscription']['billing_comments']));
            $subscription->update_meta_data('service_description',sanitize_text_field($_POST['subscription']['service_description']));
            $subscription->update_meta_data('assigned_user_id',null);
            $subscription->set_total(sanitize_text_field($_POST['subscription']['total']));
            //extra_services
            $subscription_items = $subscription->get_items();
            $subscription_item = array_shift($subscription_items);   
            $extra_services = array();
            $fees = $subscription->get_items('fee');
            if(isset($_POST['services'])){
                foreach($_POST['services'] as $service){
                    $Extra_service = get_post($service);
                    $title = $Extra_service->post_title;
                    $Extra_charges = get_post_meta($service, 'price', true);
                
                    $Extra_hours = 0;
                    if((get_post_meta($service, 'time_in_minutes', true) != '' || get_post_meta($service, 'time_in_minutes', true) != false) && is_numeric(get_post_meta($service, 'time_in_minutes', true))){
                        $Extra_hours = get_post_meta($service, 'time_in_minutes', true);
                    }
        
                    /*if($service == 1573 && $item['wcsatt_data']['active_subscription_scheme'] != 0){
                        $title = "Productos de Limpieza (Free)";
                        $Extra_charges = 0.00;
                    } */
        
                    $extraarray = array('id' => $service, 'title' => $title, 'charges' => $Extra_charges, 'extra_time' => $Extra_hours);
                    $extra_services[] = $extraarray;
                    $new = true;
                    foreach($fees as $key=>$fee){
                        if($fee->get_name() == $title){
                            $new = false;
                            unset($fees[$key]);
                        }
                    }
                    if($new){
                        $fee = array('name'=>$title);
                        $item = create_subscription_fee( $fee );
                        $subscription->add_item($item);
                    }
                }
            }
            if(count($fees)>0){
                foreach($fees as $fee){
                    $subscription->remove_item($fee->get_id());
                }
            }
            wc_update_order_item_meta($subscription_item->get_id(), 'extra_services',$extra_services); 
            wc_update_order_item_meta($subscription_item->get_id(), 'service_hour',sanitize_text_field($_POST['subscription']['service_hour'])); 
            wc_update_order_item_meta($subscription_item->get_id(), 'date',date('Y-m-d', strtotime($end_date))); 
            wc_update_order_item_meta($subscription_item->get_id(), 'time',date('H:i A', strtotime($end_date.' +5'))); 
            wc_update_order_item_meta($subscription_item->get_id(), '_line_subtotal',sanitize_text_field($_POST['subscription']['total'])); 
            wc_update_order_item_meta($subscription_item->get_id(), '_line_total',sanitize_text_field($_POST['subscription']['total'])); 
            $store = ActionScheduler_Store::instance();
            $query = array(
                'args'    => array("subscription_id"=>intval($wp->query_vars['view-subscription'])),
            );
            $actions = $store->query_actions($query);
            if(!empty($actions)){
                $action_id = max($actions);
                $action = $store->fetch_action( $action_id );
                $next = $action->get_schedule()->next();
                if($next){
                    //$schedule = as_get_datetime_object("2019-02-28");
                    $schedule = new ActionScheduler_IntervalSchedule( $schedule, 1000000 );
                    update_post_meta( $action_id, '_action_manager_schedule', $schedule );
                }
            }else{
                if($subscription->get_status() != "active"){
                    $subscription->update_status( 'active' );
                }
            }
            $subscription->save();
        }
    }elseif ( isset( $wp->query_vars['view-order'] ) && 'shop_order' == get_post_type( absint( $wp->query_vars['view-order'] ) ) && current_user_can( 'view_order', absint( $wp->query_vars['view-order'] ) ) && isset($_POST['order']) ) {
        $nonce_value = wc_get_var( $_REQUEST['woocommerce-order-edit'], wc_get_var( $_REQUEST['_wpnonce'], '' ) );
        if(wp_verify_nonce( $nonce_value, 'woocommerce-order-edit' )){
            //var_dump($_POST['order']);
            //var_dump($_POST['services']);
            $order_date = sanitize_text_field($_POST['order']['date']);
            $order_date .= " ".sanitize_text_field($_POST['order']['time']);
            $order = wc_get_order( $wp->query_vars['view-order'] );
            $original_total = $order->get_total();
            //billing_comments and service_description 
            $order->set_billing_address_1(sanitize_text_field($_POST['order']['billing_address_1']));
            $order->update_meta_data('billing_comments',sanitize_text_field($_POST['order']['billing_comments']));
            $order->update_meta_data('service_description',sanitize_text_field($_POST['order']['service_description']));
            $order->update_meta_data('order_date',$order_date);
            $order->set_total(sanitize_text_field($_POST['order']['total']));
            //extra_services
            $order_items = $order->get_items();
            $order_item = array_shift($order_items);   
            $extra_services = array();
            $fees = $order->get_items('fee');
            if(isset($_POST['services'])){
                foreach($_POST['services'] as $service){
                    $Extra_service = get_post($service);
                    $title = $Extra_service->post_title;
                    $Extra_charges = get_post_meta($service, 'price', true);
                
                    $Extra_hours = 0;
                    if((get_post_meta($service, 'time_in_minutes', true) != '' || get_post_meta($service, 'time_in_minutes', true) != false) && is_numeric(get_post_meta($service, 'time_in_minutes', true))){
                        $Extra_hours = get_post_meta($service, 'time_in_minutes', true);
                    }
        
                    $extraarray = array('id' => $service, 'title' => $title, 'charges' => $Extra_charges, 'extra_time' => $Extra_hours);
                    $extra_services[] = $extraarray;
                    $new = true;
                    foreach($fees as $key=>$fee){
                        if($fee->get_name() == $title){
                            $new = false;
                            unset($fees[$key]);
                        }
                    }
                    if($new){
                        $fee = array('name'=>$title);
                        $item = create_subscription_fee( $fee );
                        $order->add_item($item);
                    }
                }
            }
            if(count($fees)>0){
                foreach($fees as $fee){
                    $order->remove_item($fee->get_id());
                }
            }
            wc_update_order_item_meta($order_item->get_id(), 'extra_services',$extra_services); 
            wc_update_order_item_meta($order_item->get_id(), 'service_hour',sanitize_text_field($_POST['order']['service_hour'])); 
            wc_update_order_item_meta($order_item->get_id(), 'date',date('Y-m-d', strtotime($order_date))); 
            wc_update_order_item_meta($order_item->get_id(), 'time',date('H:i A', strtotime($order_date))); 
            //if($original_total!=sanitize_text_field($_POST['order']['total'])){
            $order_id = $order->get_id();
            $order->update_status('on-hold');
            $order->save();
            $employee_id = get_post_meta($order_id,'assigned_user_id',true);
            if($employee_id>0){
                $order->update_meta_data('assigned_user_id',null);
                $order->save();
                $employee = get_user_by("id", $employee_id);
                $standard_logo = Avada()->images->get_logo_image_srcset( 'logo', 'logo_retina' );
                $mail_template = "<div><img src='".esc_url_raw( $standard_logo['url'] )."' /></div>";

                $mail_template .= "Hola <b>".$employee->display_name."</b><br/>
    
                <p>Acaba de ser desasignado el pedido({$order_id}). 
    
                <p>Puedes verificar los datos completos iniciando sesi��n en nuestro sitio web y volver a asignarse la orden de acuerdo a tu disponibilidad.</p></br>
    
                Atentamente,<br/>
                Equipo de Broomday<br/>";
    
                $emailn= $employee->user_email;
                $email_subjectv = "Unassigned #{$order_id}";
                $headerss = array('Content-Type: text/html; charset=UTF-8','From: <bukerz.com');
                if(wp_mail($emailn, $email_subjectv, $mail_template, $headerss)) {
                    //echo json_encode(array("result"=>"complete"));
                }    
            }
            //}
        }
    }
    if(isset($_GET['order_id'])){
        $order = wc_get_order($_GET['order_id']);
        echo get_subscription_title($order);
        die;
    }
}
add_action('woocommerce_my_account_my_orders_actions','my_account_my_orders_actions_broom',10,2);
function my_account_my_orders_actions_broom($actions,$order){
    unset($actions['cancel']);
    $start_date = get_order_start_datetime_broom($order);
    if(strtotime($start_date)-time()>18000+3600*24*3){
        $actions['edit']=array(
            'url'  => get_permalink( get_page_by_path( 'order-edit' ) ).'?orderid='.$order->get_id().'&type=order',
            'name' => __( 'Edit', 'woocommerce' ),
        );
    }
    return $actions;
}
function create_subscription_fee( $fee ) {
    $item                 = new WC_Order_Item_Fee();
    $item->set_props(
        array(
            'name'      => $fee['name'],
            'tax_class' => 0,
            'amount'    => 0,
            'total'     => 0,
            'total_tax' => 0,
            'taxes'     => array(
                'total' => 0,
            ),
        )
    );
    return $item;
}
