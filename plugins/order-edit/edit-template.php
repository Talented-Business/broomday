<?php
  wp_enqueue_script('child_theme_script_handle', "https://maps.googleapis.com/maps/api/js?key=AIzaSyBmlFvgfwzMpTlMMA8AyZQym9YMZRxdUAg&libraries=places", array('jquery'));

  wp_enqueue_script('child_theme_mapscript_handle', get_stylesheet_directory_uri() . "/map.js", array('jquery'));
  function menu_item_classes($endpoint){
    $classes = '';
    if($endpoint == 'orders'){
        if(isset($_GET['type'])&&$_GET['type']=='order'){
            $classes = 'is-active';
        }
    }elseif($endpoint == 'subscriptions'){
        if(isset($_GET['type'])&&$_GET['type']=='subscriptions'){
            $classes = 'is-active';
        }
    }
    return $classes;
  }
?>
<div style="max-width:1100px;margin:auto">
<nav class="woocommerce-MyAccount-navigation">
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?> <?=menu_item_classes($endpoint)?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<div class="woocommerce-MyAccount-content woocommerce" style="padding-top:30px!important;">
    <?php
    if(!empty($_REQUEST['orderid'])){
      if(isset($_GET['type'])&&$_GET['type']=='subscriptions'){
        $subscription_id = $_REQUEST['orderid'];
        $subscription = wcs_get_subscription( $subscription_id );
        $last_order = $subscription->get_last_order();
        $subscription_items = $subscription->get_items();
        $subscription_item = array_shift($subscription_items);
        if(is_object($last_order)){
            $order_items = $last_order->get_items();
            $tmp = array_slice($order_items, 0, 1);
            $order_item = array_shift($tmp);
        }else if($last_order>0){
            $last_order = wc_get_order($last_order);
            $order_items = $last_order->get_items();
            $tmp = array_slice($order_items, 0, 1);
            $order_item = array_shift($tmp);
        }
        else {
            $last_order = null;
            $order_item = null;
            $order_items = array();
        }
        $product_id = get_post_meta( $subscription_id, 'product_service', true );
        $hourly_charges = get_post_meta($product_id,'_hourly_charges',true);
        if(is_object($order_item)){
            $order_item_id = $order_item->get_id();
        }else{
            $order_item_id = null;
        }
        if($product_id == ''){
            if(is_object($order_item)){
                $product_id = wc_get_order_item_meta( $order_item_id, '_product_id', true );        
            }else{
                $product_id = 1479;
            }
        }
        $start_date = wc_get_order_item_meta( $order_item_id, 'date', true );
        $date_diff = round((strtotime($start_date)-strtotime(date('Y-m-d')))/86400);
        if($date_diff<1)$date_diff = 0;
    ?>
<form name="subscription-edit" method="post" class="order-edit" enctype="multipart/form-data" action="<?= $subscription->get_view_order_url()?>">
<h4 class="subscription"><?= get_subscription_title($subscription);?></h4>
    <?php
    $next_booking_date = $subscription->get_date('end');
    if($next_booking_date == 0)list($next_booking_date,$working_time) = get_next_booking_date($subscription,$last_order, $order_item);
    else{
        $working_time = date("h:i A", strtotime($next_booking_date.' +5'));
    }
    $next_booking_date = date("d-m-Y", strtotime($next_booking_date));
    $billing_address = $subscription->get_billing_address_1();
    $billing_comments = get_post_meta($subscription_id, 'billing_comments',true);
    $service_description = get_post_meta($subscription_id, 'service_description',true);;
    $extra_service_id = get_post_meta($product_id, "_select_extra_service", true);
    $service_hour = wc_get_order_item_meta($subscription_item->get_id(), 'service_hour', true);
    $discount = get_discount_from_order_item($order_item_id,$product_id);
    sscanf($service_hour, "%d:%d", $hours, $minutes);
    $time_hours = $hours + $minutes / 60;
    if(is_object($last_order)){
        $total = $subscription->get_total();
    }else{
        $total = $time_hours*$hourly_charges*(1-$discount/100);
    }
    $extra_services= wc_get_order_item_meta($subscription_item->get_id(), 'extra_services', true);
    $key = "subscription";
  }else{
    $order_id = $_REQUEST['orderid'];
    $order = wc_get_order( $order_id );
    $order_items = $order->get_items();
    $order_item = array_shift($order_items);
    $product_id = get_post_meta( $order_id, 'product_service', true );
    $hourly_charges = get_post_meta($product_id,'_hourly_charges',true);
    if(is_object($order_item)){
        $order_item_id = $order_item->get_id();
    }else{
        $order_item_id = null;
    }
    if($product_id == ''){
        if(is_object($order_item)){
            $product_id = wc_get_order_item_meta( $order_item_id, '_product_id', true );        
        }else{
            $product_id = 1479;
        }
    }
    $date_diff = 0;
  ?>
    <h4 class="rich-snippet-hidden"><?php "#$order_id".__('Order');?></h4>
    <div id="warning" class="alert alert-warning hidden">Si hay cargos adicional o reembolsos como resultado de la edicion de la orden , un representante de Broomday lo contactará realizar los tramites correspondientes</div>
    <form name="order-edit" method="post" class="order-edit" enctype="multipart/form-data" action="<?= $order->get_view_order_url()?>">
    <?php
    $booking_date = get_order_start_datetime_broom($order);
    $working_time = date("h:i A",strtotime($booking_date));
    $booking_date = date("d-m-Y",strtotime($booking_date));
    $billing_address = get_post_meta($order_id, '_billing_address_1',true);
    $billing_comments = get_post_meta($order_id, 'billing_comments',true);
    $service_description = get_post_meta($order_id, 'service_description',true);
    $extra_service_id = get_post_meta($product_id, "_select_extra_service", true);
    $service_hour = wc_get_order_item_meta($order_item_id, 'service_hour', true);
    $discount = get_discount_from_order_item($order_item_id,$product_id);
    sscanf($service_hour, "%d:%d", $hours, $minutes);
    $time_hours = $hours + $minutes / 60;
    if(is_object($order)){
      $total = $order->get_total();
    }else{
      $total = $time_hours*$hourly_charges*(1-$discount/100);
    }
    $extra_services= wc_get_order_item_meta($order_item_id, 'extra_services', true);
    $key = "order";
  }
    if($extra_services==false || !is_array($extra_services))$extra_services = array();
    $extraserv = array();
    foreach($extra_services as $service){
        $extraserv[] = $service['id'];
    }
    if($service_hour<3)$service_hour = get_post_meta($product_id, '_minimum_recommended_hours', true);
    echo '<div class="extraservices">';
    if ($extra_service_id != "0") {
        $args = array(
            'post_type' => 'extra_services',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'ID', 'order' => 'ASC',
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'extra_services_category',
                    'field' => 'term_id',
                    'terms' => $extra_service_id,
                )
            )
        );
        $loop = new WP_Query($args);
        echo '<ul class="products clearfix products-6">';
        $i=1;
        $time_in_minutes = array();
        $working_hours = $time_hours;
        while ($loop->have_posts()) : $loop->the_post();
            global $product;
            $on_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');
            $off_image = wp_get_attachment_url(get_post_meta(get_the_ID(), '_listing_off_image_id', true ));
            $price = get_post_meta(get_the_ID(), 'price', true);	
            $unit = "";$all_price = array();
            $time_in_minutes[$loop->post->ID] = get_post_meta($loop->post->ID,'time_in_minutes',true);
            if (in_array($loop->post->ID, $extraserv)){
                $working_hours -= $time_in_minutes[$loop->post->ID]/60.0;
            }
            //echo $price;
            //echo '<!--<input type="text" name="service1" value="100"/><input type="text" name="service2" value="150"/>-->'
            ?>
            <li class="product type-product status-publish has-post-thumbnail product-type-simple extra-products additional_services <?php if (in_array($loop->post->ID, $extraserv)==false) { ?> unselected <?php } ?>" data-id="<?php echo $loop->post->ID; ?>">
                <a href="javascript:void(0)" class="product-images" data-classid="<?php echo $loop->post->ID; ?>">
                    <span class="featured-image">
                        <img width="148" height="148" src="<?php if (in_array($loop->post->ID, $extraserv)) {echo $on_image[0];}else{echo $off_image;} ?>" 
                            data-onimage="<?php echo $on_image[0]?>"
                            data-offimage="<?php echo $off_image?>"
                            data-id="<?php echo $loop->post->ID; ?>">
                    </span>
                </a>
                <div class="product-details">
                    <div class="product-details-container">
                        <p class="product-title" style="text-align:center; margin:0px !important;">
                            <?php echo esc_html(get_the_title()); ?>
                            <br/>
                        </p>
                        <?php 
                            $unit = explode(",", $price);
                            foreach($unit as $p){
                                $all_price[] = explode("-", $p);
                            }
                            $price_count= count($all_price);
                                                                
                            if($price_count >1){
                                ?>     
                                <div class="product_price">
                                    <?php
                                        $j=1;
                                        foreach($all_price as $u){ 
                                            if($j == 1){?>
                                            <div class="unt_prc">
                                            
                                                <input type="text" value="<?php print_r($u[1]); ?>" data-id="<?= $j ?>"  class="unitprice unitprice_<?= $i ?>" readonly />
                                                <input type="hidden" id="price_value_<?= $i; ?>_<?= $j; ?>" value="<?php print_r($u[0]); ?>" />
                                                <a href="#" class="add" data-val="<?= $i ?>" data-id="1" data-seq="<?= $i ?>">+</a>
                                                <a href="#" class="decrement decrement_<?= $i ?>" id="decrement" data-id="1" data-seq="<?= $i ?>">-</a>
                                                $<input type="text" name="final_price_<?php print_r($loop->post->ID); ?>" id="final_price_<?= $i ?>" class="final_price" value="<?php echo $all_price[0][0]; ?>" readonly>
                                            </div>
                                        <?php }else{ ?>
                                            <input type="hidden" id="price_value_<?= $i; ?>_<?= $j; ?>" value="<?php print_r($u[0]); ?>" />
                                    <?php } $j++; }	?>
                                </div>								
                            <?php }else{?>
                                <input type="text" name="final_price_<?php print_r($loop->post->ID); ?>" id="final_price_<?= $i ?>" class="no_price final_price" value="<?php echo "$".$price; ?>" readonly>
                                
                            <?php } ?>
                        <div style="text-align:center;">
                            <input type="checkbox" id="checkbox<?php echo $loop->post->ID; ?>" name="services[]" <?php if (in_array($loop->post->ID, $extraserv)) { ?> checked="" <?php } ?> value="<?php echo $loop->post->ID; ?>" />
                        </div>
                    </div>
                </div>
            </li>
            <?php
            $i++;
        endwhile;
        echo '</ul>';
        wp_reset_query();
    } else {
        echo "No Extra Services Found";
    }        
    $remain = $total - $time_hours*$hourly_charges*(1-$discount/100);
    ?>
    </div>
    <div class="form-row ">
        <label class="label-control"><?php _e('Booking Date', 'Avada'); ?></label>
        <div class="form-item">
            <input type="text" required name="<?= $key?>[date]" id="date" class="date form-dropdown i-date " readonly value="<?php
              if(isset($_GET['type'])&&$_GET['type']=='subscriptions'){
                echo $next_booking_date;
              }else{
                echo $booking_date;
              }
            ?>">
        </div>
    </div>
    <div class="form-row ">
        <label class="label-control"><?php _e('Hora de Inicio', 'Avada'); ?></label>
        <div class="form-item">
            <select name="<?= $key?>[time]" required id="time" class="form-dropdown">
            <?= time_options($working_time)?>
            </select>
        </div>
    </div>
    <div class="form-row ">
        <label class="label-control"><?php _e('Escoger Duración', 'Avada'); ?></label>
        <div class="form-item">
            <select name="<?= $key?>[hours]" required id="working_hours" class="form-dropdown">
                <?php for($h = 3;$h<=10;$h+=0.5){?>
                    <option value=<?= $h?> <?php if($working_hours==$h) echo "selected";?>><?=$h?>horas</option>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="form-row form-row-wide address-field validate-required" id="billing_address_1_field" data-priority="50">
        <label class="label-control">Dirección</label>
        <div class="form-item">
            <input type="text" class="input-text " name="<?= $key?>[billing_address_1]" id="billing_address_1" placeholder="Ingrese su direccion, nombre de barriada o edificio" value="<?= $billing_address?>" autocomplete="off">
        </div>
    </div>        
    <div class="form-row validate-required" id="billing_comments_field" data-priority="">
        <label class="label-control">No. de casa o Apto</label>
        <div class="form-item">
            <input type="text" class="input-text " name="<?= $key?>[billing_comments]" id="billing_comments" placeholder="Número de casa, apto, piso, o referencias para llegar a su ubicacion." value="<?=$billing_comments?>">
        </div>
    </div>
    <div class="form-row " id="service_description_field" data-priority="">
        <label class="label-control">Descripción</label>
        <div class="form-item">
            <input type="text" class="input-text " name="<?= $key?>[service_description]" id="service_description" placeholder="Descripción del trabajo a detalle (opcional)" value="<?= $service_description?>">
        </div>
    </div>
    <div class="form-row ">
        <div class="maphelp"></div><div id="map"></div>
    </div>    
    <input type="hidden" name="cust_latitude" id="cust_latitude">
    <input type="hidden" name="cust_longitude" id="cust_longitude">
    <div class="form-row ">
        <h5><strong>
            <label class="label-control"><?php _e('Hours', 'Avada'); ?>:
            <span >
                <span id="service_hour">
                    <?= $service_hour?>
                </span>
                horas
            </span>
            </label>
            <input type='hidden' name="<?= $key?>[service_hour]" value="<?= $service_hour?>">
            <label class="label-control"><?php _e('Por Limpieza', 'Avada'); ?>:
            <span >
                <span class="order-total" >
                    <?= wc_price($total)?>
                    <input type='hidden' name="<?= $key?>[total]" value="<?= $total?>">
                </span>
            </span>
            </label>
        </strong></h5>
    </div>
    <div class="form-row ">
        <div class="form-long-item">
            <input type="submit" class="woocommerce-button button btn" value="<?php if($_GET['type']=='order') _e("EDITAR ORDEN"); else _e("EDITAR SUSCRIPCIÓN")?>" style="width:45%;margin-left:10%;">
            <input type="button" class="woocommerce-button button btn" onclick="openForm(<?=$subscription_id ?>)" value="<?php if($_GET['type']=='order') _e("Cancelar ORDEN"); else _e("Cancelar SUSCRIPCIÓN")?>" style="width:45%;">
        </div>    
    </div>
    <?php wp_nonce_field( 'woocommerce-order-edit' ); ?>
</div>
</form>
<?php
}
?>
</div>
</div>
<div class="form-popup" id="myForm" style='background: gray;'>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeForm()" style="width: 27px;opacity: 1;">
        <span aria-hidden="true">&times;</span>
    </button>
    <?php echo do_shortcode("[contact-form-7 id='6335' title='Cancel Order Request']"); ?>
    <!--<button type="button" class="btn cancel woocommerce-button button view" onclick="closeForm()">Close</button>-->
    </div>
<?php $current_user = wp_get_current_user();?>
<script type="text/javascript">
    var discount = <?= $discount?>;
    var hourly_charges = <?= $hourly_charges?>;
    var remain = <?= $remain?>;
    var original_total = <?= $total?>;
    var service_hour = "<?=$service_hour?>";
    var date_diff = <?=$date_diff?>;
    service_hour = service_hour.split(':');
    if(service_hour.length==2)service_hour = parseInt(service_hour[0])+service_hour[1]/60;
    else service_hour = parseInt(service_hour[0]);
    var time_in_minutes = JSON.parse('<?=json_encode($time_in_minutes)?>');
    jQuery(function ($) {
        var time="<?php echo date("H:i:s");?>";
        var new_time="<?php echo date('12:00:00');?>";
        if(time >= new_time){
            var date = new Date();
            date.setDate(date.getDate() + 1);
        }
        else{
            var date = new Date();
        }

		var currentTime = "<?php echo date_i18n("H:i:s");?>";
		var hours = "<?php echo date('12:00:00');?>";
		if (currentTime >= hours) {
			$('#date').datepicker(
            {
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				dateFormat:"dd-mm-yy",
				minDate: 4+date_diff,
			}
            );
		} else {
			$('#date').datepicker(
            {
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				dateFormat:"dd-mm-yy",
				minDate: 3+date_diff,
			}
            );
        }
        jQuery('.additional_services').click(function (e) {
            if(e.target.getAttribute("name") != "services[]")
            {
                var classid = jQuery(this).attr('data-id');
                var test=jQuery('#checkbox' + classid).prop("checked");
                var sign = 1;
                var image = jQuery(this).find('img');
                if (test == false)
                {
                    jQuery('#checkbox' + classid).attr('checked', true);
                    jQuery(this).removeClass("unselected");
                    image.attr("src",image.data("onimage"));
                }
                else
                {
                    jQuery('#checkbox' + classid).attr('checked', false);
                    jQuery(this).addClass("unselected");
                    image.attr("src",image.data("offimage"));
                    sign = -1;
                }
                change_working_hours()
            }
        });
        jQuery("#working_hours").change((obj)=>{
            change_working_hours()
        })
        function change_working_hours(){
          var working_hours = parseFloat(jQuery('#working_hours').val());
          jQuery("input[name='services[]']").each(function(){
              if(jQuery(this).attr('checked')=='checked'){
                  working_hours += time_in_minutes[jQuery(this).val()]/60;
              }
          })
          service_hour = working_hours;
          var total = Math.round((parseFloat(remain) + working_hours*hourly_charges*(1-discount/100))*100)/100;
          var service_hour_str = (service_hour-parseInt(service_hour))*60;
          if(service_hour_str == 0) service_hour_str = '00';
          service_hour_str = parseInt(service_hour)+":"+service_hour_str;
          var currency = jQuery('.order-total .woocommerce-Price-amount.amount .woocommerce-Price-currencySymbol').html();
          jQuery('.order-total .woocommerce-Price-amount.amount').html("<span class='woocommerce-Price-currencySymbol'>"+currency+"</span>"+total);
          jQuery('#service_hour').text(service_hour_str);
          jQuery('input[name="<?= $key?>[total]"]').val(total);
          jQuery('input[name="<?= $key?>[service_hour]"]').val(service_hour_str);
          if(original_total!=total){
            //if(jQuery('#warning').hasClass('hidden'))jQuery('#warning').removeClass('hidden');
            jQuery('#warning').fadeIn(2000);
          }else{
            //if(jQuery('#warning').hasClass('hidden')==false)jQuery('#warning').addClass('hidden');
            jQuery('#warning').fadeOut(2000);
          }
        }
    });
</script>

<script>
    function openForm(name) {
        
        document.getElementsByClassName("ordercls")[0].disabled = true;
        document.getElementsByClassName("ordercls")[0].value = "Cancelar suscripción Solicitar ID # "+name;
        document.getElementById("myForm").style.display = "block";
        document.getElementById("subcription-id").value = name;
        document.getElementById("user-name").value = "<?php echo $current_user->display_name; ?>";
        document.getElementById("user-email").value = "<?php echo $current_user->user_email; ?>";
    }

    function closeForm() {
        document.getElementById("myForm").style.display = "none";
    }
</script>

<?php
