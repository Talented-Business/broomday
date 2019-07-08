<?php 
add_action('wp','testing');
function testing(){
    if(isset($_GET['test'])){
        if(function_exists('testing_'.$_GET['test'])){
            call_user_func('testing_'.$_GET['test']);
        }else{
            'testing_'.$_GET['test']." function doesn't exit";
        }
        die;
    }
}
function testing_assign_employee_to_order(){
    $order_id = 17145;
    $subscriptions = wcs_get_subscriptions_for_order( $order_id, array( 'order_type' => 'any' ) );
    var_dump($subscriptions);
}
function testing_send_email_schedule_customer_broomday_event(){
    $order_id = 17090;
    $employee = 115;
    //send_email_customer_broomday($order_id,$employee);
    send_email_schedule_customer_broomday_event($order_id,$employee);
}
function testing_send_email_customer_broomday(){
    $order_id = 17090;
    $employee = 115;
    send_email_customer_broomday($order_id,$employee,'testing');
    //send_email_schedule_customer_broomday_event($order_id,$employee);
}
function testing_get_orders_employee() {
    $employee = 115;
    $count = get_orders_employee($employee);
    var_dump(count($count));
}
