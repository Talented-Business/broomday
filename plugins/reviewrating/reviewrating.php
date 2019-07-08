<?php
/*
* Plugin Name: Broom  Review Rating
* Description: Employee Review Rating System whenever order is completed
* Version: 1.0
* Author: Lazutina
* Author URI: https://#
*/
//comment_karma assigned_user_id
//user_id customer_id
//comment_type broom_rating
//comment_author rating
//comment_content review
function editreview($order){
    wp_register_script('reviewrating_js_wc_order', plugins_url('/',__FILE__).'/assets/js/jquery.rateyo.js');
    wp_enqueue_script('reviewrating_js_wc_order');
    wp_register_style('reviewrating_css_wc_order', plugins_url('/',__FILE__).'/assets/css/jquery.rateyo.css');
    wp_enqueue_style('reviewrating_css_wc_order');

if(!empty($_GET['msg'])){
    echo $_GET['msg'];
}

if(is_object($order)){
$assigner_user_id = get_post_meta( $order->get_id(), 'assigned_user_id', true); 
 ?>
<br>
<h3>Déjanos tu calificación</h3>
<p>En Broomday constantemente estamos mejorando. Tu opinion es importante para mejorar nuestro servicio.</p>
<div class=form-row>
    <label class="label-control" style="vertical-align:top">
        <div class="avatar">
            <?= get_avatar( $assigner_user_id);?>
        </div>
        <div>
            <?php $userdata = get_user_by('id',$assigner_user_id);?>
            <?= $userdata->user_login;?>
        </div>
    </label>
    <div class="form-long-item">
        <?php
            $comments = get_comments( array('type'=>'reviewrating','post_id'=>$order->get_id()) ); 
            if(count($comments)==0){
        ?>
        <form method="post" name="reviewrating">
            <div class="">
                <div id="input-rating-edit" data-rateyo-full-star="true"></div>
                <input id="input-rating" name="comment[comment_author]" class="" type='hidden'>
            </div>
            <div class="" style="margin-left:5px">
                <textarea rows="4" name="comment[comment_content]" class="form-control" placeholder="Enter Review..." style="width:100%"></textarea>
            </div>
            <input type="hidden" name="comment[user_id]" value="<?php echo $order->get_customer_id(); ?>">
            <input type="hidden" name="comment[comment_post_ID]" value="<?php echo $order->get_id() ?>">
            <input type="hidden" name="comment[comment_karma]" value="<?php echo $assigner_user_id; ?>">
            <input type="hidden" name="comment[comment_type]" value="reviewrating">
            <br>
            <div class=form-row>
                <label class="label-control">

                </label>
                <div class="">
                    <input class="woocommerce-button button btn" type="submit" name="submit" value="CALIFICAR AHORA">
                </div>
            </div>
            <?php wp_nonce_field( 'broom-employee-rating' ); ?>
        </form>
        <?php }else{ ?>
        <div class="">
            <div id="input-rating-view" data-rateyo-rating=<?= $comments[0]->comment_author?> data-rateyo-read-only="true"></div>
            <div style="margin-left:5px"><?= $comments[0]->comment_content?></div>
        </div>
    <?php } ?>
    </div>
 </div>
   
    <script>
        jQuery(document).ready(function(){
            jQuery("#input-rating-edit").rateYo({
                onSet: function (rating, rateYoInstance) {
                    jQuery("#input-rating").val(rating);
                }
            });
            jQuery("#input-rating-view").rateYo();
        });
    </script>
 <?php
}    
    

}
//add_shortcode('reviewrating', 'editreview');


add_action( 'show_user_profile_employees', 'crf_show_extra_profile_fields' );
add_action( 'edit_user_profile_employees', 'crf_show_extra_profile_fields' );

function crf_show_extra_profile_fields( $user_id ) {
	?>
	<div class="clientData">
	<h3><?php esc_html_e( 'Review Rating', 'crf' ); ?></h3>
	
	<table class="table">
	     <tbody>
	         <tr class="tr">
                <th class="th">Rating</th>
                <th class="th">Review</th>
                <th class="th">Customer</th>
                <th class="th">Order</th>
                <th class="th">Action</th>
            </tr>
	    <?php
	    $getreviewrating = get_user_meta($user_id);

        $comments = get_comments( array('type'=>'reviewrating','karma'=>$user_id) ); 
        if(count($comments)>0){
        foreach($comments as $comment){
            
	    ?>
			 <tr class="tr">
				<?php
    		      $user_info = get_userdata($comment->user_id);
    			  echo '<td class="td">'.$comment->comment_author.'</td>';
    			  echo '<td class="td">'.$comment->comment_content.'</td>';
    			  echo '<td class="td">'.$user_info->display_name.'</td>';
    			  echo '<td class="td">'.$comment->comment_post_ID.'</td>';
    			  
    			  ?>
    			  <td class="td">
    			    <select name="reviewed[<?= $comment->comment_ID ?>]">
                        <option value="1" <?=$comment->comment_approved == 1 ? ' selected="selected"' : '';?> >Enable</option>
                        <option value="0" <?=$comment->comment_approved == 0 ? ' selected="selected"' : '';?> >Disable</option>
    			    </select>
                    <input type="hidden" name="comments[<?= $comment->comment_ID ?>]" value="<?= $comment->comment_approved ?>">
                    <button class='button button-primary review_apply' data-id="<?= $comment->comment_ID?>">Apply</button>
    			  </td>
    			
			</tr>
           <?php }}else{ ?>
            <td colspan=5>No data available in table</td>
           <?php }?>
	</tbody>
</table>
<script>
jQuery(document).ready(function($) {
    jQuery('.review_apply').on('click',function(){
        var data = {
            'action': 'review_apply_action',
            'approved': jQuery('select[name="reviewed['+this.dataset.id+']"]').val(),
            'comment_id':this.dataset.id
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
            alert('Got this from the server: ' + response);
        });  
    });
});  
</script>
           </div>
	<?php
}
add_action( 'wp_ajax_review_apply_action', 'review_apply_action_broom' );
function review_apply_action_broom(){
    if(isset($_POST['comment_id'])){
        $comment = array();
        $comment['comment_ID'] = intval($_POST['comment_id']);
        $comment['comment_approved'] = intval($_POST['approved']);
        wp_update_comment( $comment );
        echo "success";
        wp_die();
    }
    echo "failed";
    wp_die();
}

add_action( 'personal_options_update', 'crf_update_profile_fields' );
add_action( 'edit_user_profile_update', 'crf_update_profile_fields' );

function crf_update_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
    foreach($_POST['comments'] as $comment_id=>$approved){
        if($_POST['reviewed'][$comment_id] !== $approved){
            $comment = array();
            $comment['comment_ID'] = $comment_id;
            $comment['comment_approved'] = $_POST['reviewed'][$comment_id];
            wp_update_comment( $comment );
        }
    }
}



function add_user_columns($column) {
    $column['rating'] = 'Rating';

    return $column;
}
add_filter( 'wp_employee_columns', 'add_user_columns' );

//add the data
function add_user_column_data( $val, $column_name, $user_id ) {
   // $user = get_userdata($user_id);
    $comments = get_comments( array('type'=>'reviewrating','karma'=>$user_id) ); 
    $star = array();
    foreach($comments as $comment){
        if($comment->comment_approved==1){
            $star[] = $comment->comment_author;
        }
        
    }    
    $count = count($star);
    $sum = array_sum($star);
    
    if($count == 0)$avg = null;else $avg = $sum/$count;

    switch ($column_name) {
        case 'rating' :
            return $avg;
            break;
        default:
    }
    return;
}
add_filter( 'manage_users_wp_employee_column', 'add_user_column_data', 10, 3 );
add_action('woocommerce_view_order','view_order_reviewrating',11);
function view_order_reviewrating($order_id){
    $order = wc_get_order($order_id);
    if( $order->get_status() == 'completed'){
        editreview($order);
    }
}
add_action('wp','update_employee_review_broom',10);
function update_employee_review_broom(){
    global $wp;
    if ( isset( $wp->query_vars['view-order'] ) && 'shop_order' == get_post_type( absint( $wp->query_vars['view-order'] ) ) && current_user_can( 'view_order', absint( $wp->query_vars['view-order'] ) ) && isset($_POST['comment']) ) {
        $nonce_value = wc_get_var( $_REQUEST['broom-employee-rating'], wc_get_var( $_REQUEST['_wpnonce'], '' ) );
        if(wp_verify_nonce( $nonce_value, 'broom-employee-rating' )){
            wp_insert_comment($_POST['comment']);
        }
    }    
}
function have_comments_order($order_id){
    global $wpdb;
    $comments = $wpdb->get_results("SELECT {$wpdb->comments}.* FROM {$wpdb->comments} where comment_post_ID = $order_id and comment_type='reviewrating'");
    return count($comments)>0;
}