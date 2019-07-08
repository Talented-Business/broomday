<?php
function broomday_get_order_item_id($order_id){
    if(is_numeric($order_id)){
        $order     = new WC_Order( $order_id );
    }else{
        $order = $order_id;
    }
    $order_items = $order->get_items();
    if(is_array($order_items)==false || count($order_items)==0)return null;
    $tmp = array_slice($order_items, 0, 1);
    $order_item = array_shift($tmp);
    return $order_item->get_id();
}
function get_orders_employee($employee){
    $args = array(
        'limit' => -1,
        'return' => 'ids',
        'status' => 'completed'
       );
    $orders = wc_get_orders($args);
    $employee_orders = array();
    foreach($orders as $order_id){
        $user_id = get_post_meta($order_id,'assigned_user_id',true);
        if($user_id == $employee){
            $employee_orders[] = $order_id;
        }
    }
    return $employee_orders;
}