<?php
/* $current_user = wp_get_current_user();
  $user_info = get_userdata($current_user->ID);
  echo 'Username: ' . $user_info->user_login . "\n";
  echo 'User roles: ' . implode(', ', $user_info->roles) . "\n";
  echo 'User ID: ' . $user_info->ID . "\n"; */
//date_default_timezone_set('America/Panama');
//date_default_timezone_set('asia/kolkata');
//require_once(  get_stylesheet_directory().'/test/test.php' );
require_once(  get_stylesheet_directory().'/wc/woo.php' );
global $woocommerce;
//echo date("Y-m-d H:i:s");
function theme_enqueue_styles() {
    wp_register_style('avada-parent-stylesheet', get_template_directory_uri() . '/style.css');
    wp_register_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('avada-parent-stylesheet'));
    wp_register_script('child-sp-datepicker', get_stylesheet_directory_uri() . '/js/datepicker-sq.js');
    wp_register_script('child-vi-datepicker', get_stylesheet_directory_uri() . '/js/datepicker-vi.js');
    wp_register_script('custom', get_stylesheet_directory_uri() . '/js/custom.js');
    wp_localize_script( 'custom', 'ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    
    wp_enqueue_style('avada-parent-stylesheet');
    wp_enqueue_style('child-style');
    wp_enqueue_script('child-sp-datepicker');
    wp_enqueue_script('child-vi-datepicker');
    wp_enqueue_script('custom');
  
}

add_action('wp_enqueue_scripts', 'theme_enqueue_styles');


function filter_price( $price, $product ) {
	    
	    $price = $price + $product->get_regular_price();

		if ( WCS_ATT_Product::is_subscription( $product ) ) {
			$price = WCS_ATT_Product_Prices::get_price( $product, '', 'edit' );
		}

		return $price;
	}
add_action( 'woocommerce_product_get_price', 'filter_price', 0, 2 );

//add_action("admin_init", "order_test");

/*function order_test()
{

    global $woocommerce;
    $subscription = new WC_Order(12280);
 
    update_post_meta( $subscription->id, '_payment_method', 'nmi_gateway_woocommerce_credit_card' );
    update_post_meta( $subscription->id, '_payment_method_title', 'Credit Card (NMI)');

    WC()->session->order_awaiting_payment = $subscription->id;

    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

    //$result = $available_gateways['nmi_gateway_woocommerce_credit_card']->process_payment($subscription->id);
 
    //echo "<pre>";
    //print_r($result);
    //die;
}*/

function avada_lang_setup() {
    $lang = get_stylesheet_directory() . '/languages';
    load_child_theme_textdomain('Avada', $lang);
}

add_action('after_setup_theme', 'avada_lang_setup');


load_theme_textdomain('Avada');

/**
 * Add endpoint
 */
function iconic_add_available_jobs_endpoint() {

    add_rewrite_endpoint('available-jobs', EP_PAGES);
}

add_action('init', 'iconic_add_available_jobs_endpoint');

/**
 * Information content
 */
function iconic_available_jobs_endpoint_content() {
    echo 'Your new content';
}

add_action('woocommerce_account_available_jobs_endpoint', 'iconic_available_jobs_endpoint_content');

/**
 * Helper: is endpoint
 */
function iconic_is_endpoint($endpoint = false) {
    global $wp_query;
    if (!$wp_query)
        return false;
    return isset($wp_query->query[$endpoint]);
}
 
global $avada_woocommerce;
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 1);
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
//add_action('woocommerce_review_order_before_payment', 'woocommerce_checkout_coupon_form');
remove_action( 'woocommerce_before_checkout_form', 'checkout_coupon_form', 10 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );

//add_action('woocommerce_review_order_after_shipping', 'woocommerce_checkout_coupon_form', 10);
//add_filter('woocommerce_checkout_coupon_message', 'bbloomer_have_coupon_message');

//function bbloomer_have_coupon_message() {
   // return '';
//}

remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
add_action('woocommerce_show_paymentform', 'woocommerce_checkout_payment', 10);

remove_action('woocommerce_single_product_summary', array($avada_woocommerce, 'avada_woocommerce_template_single_title'), 5);



/////////New Code////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Remove Extra Tabs from product details page========================================
add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);

function woo_remove_product_tabs($tabs) {

    unset($tabs['description']);        // Remove the description tab
    unset($tabs['reviews']);            // Remove the reviews tab
    unset($tabs['additional_information']);      // Remove the additional information tab
  
    //if (!is_user_logged_in())
    unset($tabs['refer_tab']);
    return $tabs;
}
 
//Add Register and login,logout in menu================================================
add_filter('wp_nav_menu_items', 'add_loginout_link', 10, 2);

function add_loginout_link($items, $args) {
    
    //echo $args->theme_location;
    if (is_user_logged_in() && $args->theme_location == 'main_navigation') {
        $current_user = wp_get_current_user();
        $user_info = get_userdata($current_user->ID);
        $user = wp_get_current_user();
        $total_amount = get_user_meta($current_user->ID, "_total_payment", true);
        if ($total_amount != "")
            $total_amount = $total_amount;
        else
            $total_amount = "0.00";
        if ($user->roles[0] == 'freelancers') {
            // $newmenuitem = '<li style="margin-top: 24px;color:rgb(108,171,221);">Pagos $' .number_format((float)$total_amount, 2, '.', ''). '</li> ';
            $newmenuitem = '<li role="menuitem" class="menu-item menu-item-type-post_type menu-item-object-page" id="cst11">
                                <a href="https://www.broomday.com/final/ingresos" class="fusion-background-highlight"><span class="menu-text">Ingresos</span></a>
                            </li>';
            $items = $newmenuitem.$items;
        }
        $items .= '<li class="fusion-last-menu-item"><lable class="header_name"><a  class="header_account" href="' . site_url("mi-cuenta/edit-account") . '">'.__("Hola ", "Avada-Child"). ucfirst(substr($user_info->first_name, 0, 10)) . '</a></label><div class="header_links"><a class="header_account" href="';
        if ($user->roles[0] == 'freelancers') {
            $items .=site_url("nuevas-oportunidades");
        }else{
            $items .=site_url("mi-cuenta/orders");
        }
        $items .= '">Mi Cuenta</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a  class="header_logout" href="' . wp_logout_url(home_url()) . '">Cerrar sesión</a></div></li> ';
    } elseif (!is_user_logged_in() && $args->theme_location == 'main_navigation') {
        //$items .= '<li><a href="' . get_permalink(woocommerce_get_page_id('myaccount')) . '">Log In</a></li> ';
    }
    return $items;
}

// Front End Cart form Custom Fields=============================================
/**
 * The following hook will add a input field right before "add to cart button"
 * will be used for getting Custom field before add to cart button
  <input pattern="[0-9]*" inputmode="numeric" type="number" step="1" min="1" max="10" name="bathroom" placeholder="<?php esc_html_e('Select Bathroom', 'Avada'); ?>" class="input-text qty text form-input">
 */
function add_custom_field_before_cartbutton() {
    global $post;
    $id = $post->ID;
    $recommended_hours = get_post_meta(get_the_ID(), '_minimum_recommended_hours', true);
    $hourly_charges = get_post_meta(get_the_ID(), '_hourly_charges', true);
    $total_charges = ($recommended_hours * $hourly_charges);

    $service_hour = '';
    //for ($i = $recommended_hours; $i <= 10; $i += 0.5) {
    for ($i = $recommended_hours; $i <= 10; $i+= 0.5) {
        $service_hour.= '<option value="' . $i . '">' . $i . ' horas</option>';
    }

    $term_list = wp_get_post_terms($id, 'product_cat', array('fields' => 'all'));
    ?>
    <!--<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/2.1.0/css/bootstrap.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker.css" type="text/css" media="all" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" type="text/css" media="all" />

    <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/2.1.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/js/bootstrap-datepicker.js"></script>-->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <!-- <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri();?>/css/mobiscroll.jquery.min.css">
   <script src="<?php echo get_stylesheet_directory_uri();?>/js/mobiscroll.jquery.min.js"></script> -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        #ui-datepicker-div, .ui-datepicker-div, .ui-datepicker-inline {
            z-index: 9999 !important;
        }
    </style>
    <table <?php if(get_post_meta($id, '_select_bedroom_filter', true)){?> class="variations cleaning" <?php } else { ?> class="variations" <?php } ?> cellspacing="0" >
        <tbody>
            <tr>
                <td class="value">
               <h2 class="selectduration"><?php _e("Tell Us About Your Job", "Avada");?></h2>
                    <?php
                    if (get_post_meta($id, '_select_bedroom_filter', true) == 1) {
                        ?>
                        <div class="quantity">
                            <input class="valminus" type="button" value="-">
                            <input type="number" required name="bedroom" id="bedroom"  class="form-input ninput" value="1">
                            <input type="text" placeholder="<?php esc_html_e('Bedrooms', 'Avada'); ?>"  class="form-input">
                            <input class="valplus" type="button" value="+">
                        </div>
                        <?php
                    }
                    if (get_post_meta($id, '_select_bathroom_filter', true) == 1) {
                        ?>
                        <div class="quantity">
                            <input class="valminus" type="button" value="-">
                            <input type="number" required name="bathroom" id="bathroom"  class="form-input ninput" value="1">
                            <input type="text" placeholder="<?php esc_html_e('Bathrooms', 'Avada'); ?>"  class="form-input">
                            <input class="valplus" type="button" value="+">
                        </div>
                        <?php
                    }
                    if (get_post_meta($id, '_select_hall_filter', true) == 1) {
                        ?>
                        <div class="quantity">
                            <input class="valminus" type="button" value="-">
                            <input type="number" required name="hall" id="hall" placeholder="Select Hall" class="form-input ninput" value="1">
                            <input class="valplus" type="button" value="+">
                        </div>
                        <?php
                    }
                     if (get_post_meta($id, '_select_bedroom_filter', true) == 1 || get_post_meta($id, '_select_bathroom_filter', true) == 1 || get_post_meta($id, '_select_hall_filter', true) == 1) {
                         ?>
                         <label for="Apto"><input type="radio" name="rhcheck" checked="checked" value="Apto" id="Apto" class="rhcheck" /> Apto</label>
                         <label for="Casa"><input type="radio" name="rhcheck" value="Casa" id="Casa" class="rhcheck" /> Casa</label>
                         <label for="Vacacional"><input type="radio" name="rhcheck" value="Vacacional" id="Vacacional" class="rhcheck" /> Vacacional</label>
                         <?php
                     }
                    if ($term_list[0]->slug == "cleaning") {
                        ?>
                        <h2 class="selectduration"><?php _e("Select Duration", "Avada");?></h2>
                        <div class="order-alert"><?php _e("You select less hours than recommended, is possible the worker didnt finish the required services", "Avada");?></div>
                        <select name="service_hour" required id="service_hour" class="form-dropdown" style="margin-bottom: 0px;">
                            <!--<option value=""><?php _e("Select Hours", "Avada");?></option>-->
                            <?php echo $service_hour; ?>
                        </select>
                        <div class="Recommend">
                            <p><?php _e("We recommend", "Avada");?> <span id="recommended_hour_span"><?php echo get_post_meta(get_the_ID(), '_minimum_recommended_hours', true); ?></span> <?php _e("hours", "Avada");?></p>
                        </div>
                    <?php } else { ?>
                        <input type="hidden" name="service_hour" id="service_hour" value="<?php echo $recommended_hours; ?>">
                    <?php } ?>
                    <h2><?php _e("Booking Date", "Avada");?></h2>
                    <input type="hidden" name="recommended_hour" id="recommended_hour" value="<?php echo $recommended_hours; ?>">
                    <input type="hidden" name="recommended_hour_cal" id="recommended_hour_cal" value="<?php
                    if ($recommended_hours != 1)
                        echo $recommended_hours;
                    else
                        echo "0";
                    ?>">
                    <input type="hidden" name="hourly_charges" id="hourly_charges" value="<?php
                    if ($hourly_charges != 1)
                        echo $hourly_charges;
                    else
                        echo "0";
                    ?>">
                    <input type="hidden" name="total_charges" id="total_charges" value="<?php
                    if ($total_charges != 1)
                        echo $total_charges;
                    else
                        echo "0";
                    ?>">
                    <input type="hidden" name="product_service" id="product_service" value="<?php echo $id; ?>">
                    <input type="text" required name="date" placeholder="<?php esc_html_e('Select Date', 'Avada'); ?>" id="date" class="date form-dropdown i-date " readonly>
                    <p>
                        <select name="time" required id="time" class="form-dropdown" style="margin-bottom: 0px;">
                            <option value="07:00 AM">7:00 AM</option>
                            <option value="07:30 AM">7:30 AM</option>
                            <option value="08:00 AM">8:00 AM</option>
                            <option value="08:30 AM">8:30 AM</option>
                            <option value="09:00 AM">9:00 AM</option>
                            <option value="09:30 AM">9:30 AM</option>
                            <option value="10:00 AM">10:00 AM</option>
                            <option value="10:30 AM">10:30 AM</option>
                            <option value="11:00 AM">11:00 AM</option>
                            <option value="11:30 AM">11:30 AM</option>
                            <option value="12:00 PM">12:00 PM</option>
                            <option value="12:30 PM">12:30 PM</option>
                            <option value="01:00 PM">1:00 PM</option>
                            <option value="01:30 PM">1:30 PM</option>
                            <option value="02:00 PM">2:00 PM</option>
                            <option value="02:30 PM">2:30 PM</option>
                            <option value="03:00 PM">3:00 PM</option>
                            <option value="03:30 PM">3:30 PM</option>
                            <option value="04:00 PM">4:00 PM</option>
                            <option value="04:30 PM">4:30 PM</option>
                            <option value="05:00 PM">5:00 PM</option>
                            <option value="05:30 PM">5:30 PM</option>
                            <option value="06:00 PM">6:00 PM</option>
                            <option value="06:30 PM">6:30 PM</option>
                            <option value="07:00 PM">7:00 PM</option>
                            <option value="07:30 PM">7:30 PM</option>
                            <option value="08:00 PM">8:00 PM</option>
                        </select>
                    </p>
                    <?php 
                        if(isset($_GET['customer'])){
                            $user_id = $_GET['customer'];
                        }else{
                            $user_id = get_current_user_id(); 
                        }
                        $user_info = get_userdata($user_id);
                        if($user_info)$userEmail = $user_info->user_email;else $userEmail='';
                    ?>
                    <input type="hidden" name="user_id" class="user_id" id="user_id" value="<?=$user_id?>" />
                    <input type="email" required name="email" id="order_email" placeholder="<?php esc_html_e('Enter your email', 'Avada'); ?>" value="<?php if(is_user_logged_in()){ echo $userEmail; }?>" class="form-dropdown i-mail" <?php if(is_user_logged_in()){ ?> disabled <?php } ?>>
                </td>
            </tr>
        </tbody>
    </table>
    <style>
        .wcsatt-options-wrapper{
            display:none;
        }
    </style>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
    /*
        mobiscroll.settings = {
        theme: 'wp-light',
        lang: 'en'
    };*/

    jQuery(function ($) {
        $('.one-time-option>label>input[type="radio"]').click(); 
        var time="<?php echo date("H:i:s");?>";
        var new_time="<?php echo date('12:00:00');?>";
        if(time >= new_time){
            var date = new Date();
            date.setDate(date.getDate() + 1);
        }
        else{
            var date = new Date();
        }

        // Mobiscroll Calendar initialization
        /*
        $('#date').mobiscroll().calendar({
            theme: 'wp-light',  // Specify theme like: theme: 'ios' or omit setting to use default
            lang: 'en',         // Specify language like: lang: 'pl' or omit setting to use default
            display: 'center',  // Specify display mode like: display: 'bottom' or omit setting to use default
            min: date,           // More info about max: https://docs.mobiscroll.com/3-2-3/calendar#!opt-max
            dateFormat :'yy-mm-dd',
        });

        $('#date').click(function () {
            $('#date').mobiscroll('show');
            return false;
        });

        $('#clear').click(function () {
            $('#date').mobiscroll('clear');
            return false;
        });
        */

        var currentTime = "<?php echo date_i18n("H:i:s");?>";
        var hours = "<?php echo date('12:00:00');?>";
        if (currentTime >= hours) {
            $('#date').datepicker(
            {
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                dateFormat:"yy-mm-dd",
                minDate: 2,

            }
            );
        } else {
            $('#date').datepicker(
            {
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                dateFormat:"yy-mm-dd",
                minDate: 1,
            }
            );
        }

    });
    
        jQuery('input.rhcheck').change(function(){
            
            // Get values
            $quantitySelector = ".ninput";
            var $quantityBox = jQuery(this).parent().find($quantitySelector),
                    $currentQuantity = parseFloat($quantityBox.val()),
                    $maxQuantity = parseFloat($quantityBox.attr("max")),
                    $minQuantity = parseFloat($quantityBox.attr("min")),
                    $step = $quantityBox.attr("step");

            // Fallback default values
            if (!$currentQuantity || "" === $currentQuantity || "NaN" === $currentQuantity) {
                $currentQuantity = 0;
            }
            if ("" === $maxQuantity || "NaN" === $maxQuantity) {
                $maxQuantity = "";
            }

            if ("" === $minQuantity || "NaN" === $minQuantity) {
                $minQuantity = 0;
            }
             if ("any" === $step || "" === $step || undefined === $step || "NaN" === parseFloat($step)) {
                    $step = 1;
                }
            // Change the value
            if (jQuery(this).is(".valplus")) {

                if ($maxQuantity && ($maxQuantity == $currentQuantity || $currentQuantity > $maxQuantity)) {
                    $quantityBox.val($maxQuantity);
                } else {
                    $quantityBox.val($currentQuantity + parseFloat($step));
                }

            } else {

                if ($minQuantity && ($minQuantity == $currentQuantity || $currentQuantity < $minQuantity)) {
                    $quantityBox.val($minQuantity);
                } else if ($currentQuantity > 0) {
                    $quantityBox.val($currentQuantity - parseFloat($step));
                }

            }

            // Trigger change event
            $quantityBox.trigger("change");

            var bedroom = jQuery("#bedroom").val();
            var bathroom = jQuery("#bathroom").val();
            var recommended_hour = jQuery("#recommended_hour_cal").val();

            var cal_bedroom = 0;
            if (parseInt(bedroom) > 1)
            {
                var cal_bedroom = parseInt(bedroom) - 1;
            }

            var cal_bathroom = 0;
            if (parseInt(bathroom) > 1)
            {
                var cal_bathroom = parseInt(bathroom) - 1;
            }
            var iv;
            if($("input.rhcheck:checked").val()=="Apto"){
                var iv = 0; 
            }
            else if($("input.rhcheck:checked").val()=="Casa"){
                var iv = 0.5;
            }
            else if($("input.rhcheck:checked").val()=="Vacacional"){
                var iv = 1;
            }

            console.log("cal_bedroom = " + cal_bedroom);
            var increased_hour_b = parseFloat(cal_bathroom) * 0.5;
            var increased_hour = parseFloat(cal_bedroom) * 0.5;
            console.log("increased_hour = " + increased_hour);
            var total = parseFloat(increased_hour_b) + parseFloat(increased_hour) + parseFloat(recommended_hour) + parseFloat(iv);
            
            var hourly_charges = jQuery("#hourly_charges").val();
            console.log("hourly_charges = " + hourly_charges);
            var total_charges = parseFloat(hourly_charges) * parseFloat(total);
            console.log("total_charges = " + total_charges);
            console.log("total = " + total);
            jQuery("#recommended_hour").val(total);
            jQuery("#recommended_hour_span").html(total);
            jQuery("#total_charges").val(total_charges);
            if (typeof total === "number") {
                if (total % 1 === 0) {
                    // int
                } else {
                    //total = parseFloat(total) + parseFloat(.5);
                }
            } else {
                // not a number
            }

            jQuery("#service_hour").val(total).attr("selected", "selected");
        
        });
    
        jQuery(".valplus, .valminus").on("click", function () {
            // Get values
            $quantitySelector = ".ninput";
            var $quantityBox = jQuery(this).parent().find($quantitySelector),
                    $currentQuantity = parseFloat($quantityBox.val()),
                    $maxQuantity = parseFloat($quantityBox.attr("max")),
                    $minQuantity = parseFloat($quantityBox.attr("min")),
                    $step = $quantityBox.attr("step");

            // Fallback default values
            if (!$currentQuantity || "" === $currentQuantity || "NaN" === $currentQuantity) {
                $currentQuantity = 0;
            }
            if ("" === $maxQuantity || "NaN" === $maxQuantity) {
                $maxQuantity = "";
            }

            if ("" === $minQuantity || "NaN" === $minQuantity) {
                $minQuantity = 0;
            }
             if ("any" === $step || "" === $step || undefined === $step || "NaN" === parseFloat($step)) {
                    $step = 1;
                }
            // Change the value
            if (jQuery(this).is(".valplus")) {

                if ($maxQuantity && ($maxQuantity == $currentQuantity || $currentQuantity > $maxQuantity)) {
                    $quantityBox.val($maxQuantity);
                } else {
                    $quantityBox.val($currentQuantity + parseFloat($step));
                }

            } else {

                if ($minQuantity && ($minQuantity == $currentQuantity || $currentQuantity < $minQuantity)) {
                    $quantityBox.val($minQuantity);
                } else if ($currentQuantity > 0) {
                    $quantityBox.val($currentQuantity - parseFloat($step));
                }

            }

            // Trigger change event
            $quantityBox.trigger("change");

            var bedroom = jQuery("#bedroom").val();
            var bathroom = jQuery("#bathroom").val();
            var recommended_hour = jQuery("#recommended_hour_cal").val();

            var cal_bedroom = 0;
            if (parseInt(bedroom) > 1)
            {
                var cal_bedroom = parseInt(bedroom) - 1;
            }

            var cal_bathroom = 0;
            if (parseInt(bathroom) > 1)
            {
                var cal_bathroom = parseInt(bathroom) - 1;
            }
            
            var iv;
            if($("input.rhcheck:checked").val()=="Apto"){
                var iv = 0; 
            }
            else if($("input.rhcheck:checked").val()=="Casa"){
                var iv = 0.5;
            }
            else if($("input.rhcheck:checked").val()=="Vacacional"){
                var iv = 1;
            }

            console.log("cal_bedroom = " + cal_bedroom);
            var increased_hour_b = parseFloat(cal_bathroom) * 0.5;
            var increased_hour = parseFloat(cal_bedroom) * 0.5;
            console.log("increased_hour = " + increased_hour);
            var total = parseFloat(increased_hour_b) + parseFloat(increased_hour) + parseFloat(recommended_hour) + parseFloat(iv);
            
            var hourly_charges = jQuery("#hourly_charges").val();
            console.log("hourly_charges = " + hourly_charges);
            var total_charges = parseFloat(hourly_charges) * parseFloat(total);
            console.log("total_charges = " + total_charges);
            console.log("total = " + total);
            jQuery("#recommended_hour").val(total);
            jQuery("#recommended_hour_span").html(total);
            jQuery("#total_charges").val(total_charges);
            if (typeof total === "number") {
                if (total % 1 === 0) {
                    // int
                } else {
                    //total = parseFloat(total) + parseFloat(.5);
                }
            } else {
                // not a number
            }

            jQuery("#service_hour").val(total).attr("selected", "selected");
        }
        );

        jQuery(document).on("keyup", "#bathroom", function () {
            var bathroom = jQuery(this).val();
            var bedroom = jQuery("#bedroom").val();
            var recommended_hour = jQuery("#recommended_hour_cal").val();
            var cal_bathroom = 0;
            if (parseInt(bathroom) > 1)
            {
                var cal_bathroom = parseInt(bathroom) - 1;
            }

            var cal_bedroom = 0;
            if (parseInt(bedroom) > 1)
            {
                var cal_bedroom = parseInt(bedroom) - 1;
            }
            
            var iv;
            if($("input.rhcheck:checked").val()=="Apto"){
                var iv = 0; 
            }
            else if($("input.rhcheck:checked").val()=="Casa"){
                var iv = 0.5;
            }
            else if($("input.rhcheck:checked").val()=="Vacacional"){
                var iv = 1;
            }
            
            console.log("cal_bathroom = " + cal_bathroom);
            var increased_hour_b = parseFloat(cal_bedroom) * 0.5;
            var increased_hour = parseFloat(cal_bathroom) * 0.5;
            console.log("increased_hour = " + increased_hour);
            var total = parseFloat(increased_hour_b) + parseFloat(increased_hour) + parseFloat(recommended_hour)  + parseFloat(iv);
            console.log("total = " + total);
            var hourly_charges = jQuery("#hourly_charges").val();
            console.log("hourly_charges = " + hourly_charges);
            var total_charges = parseFloat(hourly_charges) * parseFloat(total);
            console.log("total_charges = " + total_charges);
            jQuery("#recommended_hour").val(total);
            jQuery("#recommended_hour_span").html(total);
            jQuery("#total_charges").val(total_charges);
            if (typeof total === "number") {
                if (total % 1 === 0) {
                    // int
                } else {
                    total = parseFloat(total) + parseFloat(.5);
                }
            } else {
                // not a number
            }
            jQuery("#service_hour").val(total).attr("selected", "selected");
        });

        jQuery(document).on("keyup", "#bedroom", function () {
            var bedroom = jQuery(this).val();
            var bathroom = jQuery("#bathroom").val();
            var recommended_hour = jQuery("#recommended_hour_cal").val();

            var cal_bedroom = 0;
            if (parseInt(bedroom) > 1)
            {
                var cal_bedroom = parseInt(bedroom) - 1;
            }

            var cal_bathroom = 0;
            if (parseInt(bathroom) > 1)
            {
                var cal_bathroom = parseInt(bathroom) - 1;
            }
            
            var iv;
            if($("input.rhcheck:checked").val()=="Apto"){
                var iv = 0; 
            }
            else if($("input.rhcheck:checked").val()=="Casa"){
                var iv = 0.5;
            }
            else if($("input.rhcheck:checked").val()=="Vacacional"){
                var iv = 1;
            }
            
            console.log("cal_bedroom = " + cal_bedroom);
            var increased_hour_b = parseFloat(cal_bathroom) * 0.5;
            var increased_hour = parseFloat(cal_bedroom) * 0.5;
            console.log("increased_hour = " + increased_hour);
            var total = parseFloat(increased_hour_b) + parseFloat(increased_hour) + parseFloat(recommended_hour) + parseFloat(iv);
            var hourly_charges = jQuery("#hourly_charges").val();
            console.log("hourly_charges = " + hourly_charges);
            var total_charges = parseFloat(hourly_charges) * parseFloat(total);
            console.log("total_charges = " + total_charges);
            console.log("total = " + total);
            jQuery("#recommended_hour").val(total);
            jQuery("#recommended_hour_span").html(total);
            jQuery("#total_charges").val(total_charges);
            if (typeof total === "number") {
                if (total % 1 === 0) {
                    // int
                } else {
                    total = parseFloat(total) + parseFloat(.5);
                }
            } else {
                // not a number
            }
            jQuery("#service_hour").val(total).attr("selected", "selected");
        });

        jQuery(document).on("change", "#service_hour", function () {
            var optval = jQuery(this).val();
            var hours = jQuery(this).val();
            var cost = jQuery("#hourly_charges").val();
            var total_charges = parseFloat(hours * cost);
            var recommended_hour = jQuery("#recommended_hour").val();
            if (parseFloat(optval) < parseFloat(recommended_hour))
            {
                jQuery(".selectduration").hide();
                jQuery(".order-alert").show();
            } else {
                jQuery(".selectduration").show();
                jQuery(".order-alert").hide();
            }
            jQuery("#total_charges").val(total_charges);
        });
        jQuery(".cart").validate({
            rules: {
                email: { required:true }
            },
            message:{
                email: { required:"E-mail is Required!" }
            }
            
        });


        
        
    </script>
    <?php
}

add_action('init', "add_for_export");

function add_for_export()
{
    if(isset($_POST['add-to-cart'])&&isset($_POST['email']))
    {
        global $wpdb;
        $insert_data = array(
            "bedroom" => $_POST['bedroom'],
            "bathroom" => $_POST['bathroom'],
            "service_hour" => $_POST['service_hour'],
            "hourly_charges" => $_POST['hourly_charges'],
            "total_charges" => $_POST['total_charges'],
            "product_service" => $_POST['product_service'],
            "email" => $_POST['email'],
            "quantity" => $_POST['quantity'],
            "date" => $_POST['date'],
            "time" => $_POST['time']
        );

        return $wpdb->insert("wp_reservation", $insert_data);
    }
}


add_action('woocommerce_before_add_to_cart_button', 'add_custom_field_before_cartbutton');
// Display Fields at admin to particular product===============================================
add_action('woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields');


function woo_add_custom_general_fields() {
    $category_extra_services = get_terms('extra_services_category');
    $extra_services = array();
    if (!empty($category_extra_services)) {
        foreach ($category_extra_services as $extra_service) {
            $extra_services[$extra_service->term_id] = "$extra_service->name";
        }
    }

    global $woocommerce, $post;
    echo '<div class="options_group">';
    woocommerce_wp_text_input(
            array(
                'id' => '_minimum_recommended_hours',
                //'label' => __('Minimum Hours Recommended (include 1 bathroom and 1 bedroom)', 'woocommerce'),
                'label' => __('Minimum Hours Recommended', 'woocommerce'),
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => __('Enter your Recommended hours.', 'woocommerce'),
                'type' => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min' => '0'
                )
            )
    );

    woocommerce_wp_text_input(
            array(
                'id' => '_hourly_charges',
                'label' => __('Hourly Charges', 'woocommerce'),
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => __('Enter your hourly price.', 'woocommerce'),
                'type' => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min' => '0'
                )
            )
    );
    woocommerce_wp_select(
            array(
                'id' => '_select_extra_service',
                'label' => __('Extra Service Category', 'woocommerce'),
                'options' => $extra_services
            )
    );
    //Select Filters=============================================
    /* woocommerce_wp_select(
      array(
      'id'      => '_select_filters',
      'label'   => __( 'My Select Filters', 'woocommerce' ),
      'options' => array(
      'bedroom'   => __( 'Bedroom', 'woocommerce' ),
      'bathroom'   => __( 'Bathroom', 'woocommerce' ),
      'hall'   => __( 'hall', 'woocommerce' )
      )
      )
      );
      woocommerce_wp_select(
      array(
      'id' => '_select_jobdetails_filter',
      'label' => __('Job Details Filter', 'woocommerce'),
      'options' => array(
      '0' => __('No', 'woocommerce'),
      '1' => __('Yes', 'woocommerce'),
      )
      )
      );
     *  */
    // Select Filters========================================

    woocommerce_wp_select(
            array(
                'id' => '_select_bedroom_filter',
                'label' => __('Bedroom Filter', 'woocommerce'),
                'options' => array(
                    '2' => __('No', 'woocommerce'),
                    '1' => __('Yes', 'woocommerce'),
                )
            )
    );
    woocommerce_wp_select(
            array(
                'id' => '_select_bathroom_filter',
                'label' => __('Bathroom Filter', 'woocommerce'),
                'options' => array(
                    '2' => __('No', 'woocommerce'),
                    '1' => __('Yes', 'woocommerce'),
                )
            )
    );
    //Filters Price================================================
    /* woocommerce_wp_text_input(
      array(
      'id' => '_bedroom_charges',
      'label' => __('Bedroom Charges', 'woocommerce'),
      'placeholder' => '',
      'desc_tip' => 'true',
      'description' => __('Enter price for per bedroom.', 'woocommerce'),
      'type' => 'number',
      'custom_attributes' => array(
      'step' => 'any',
      'min' => '0'
      )
      )
      );
      woocommerce_wp_text_input(
      array(
      'id' => '_bathroom_charges',
      'label' => __('Bathroom Charges', 'woocommerce'),
      'placeholder' => '',
      'desc_tip' => 'true',
      'description' => __('Enter price for per bathroom.', 'woocommerce'),
      'type' => 'number',
      'custom_attributes' => array(
      'step' => 'any',
      'min' => '0'
      )
      )
      ); */
    echo '</div>';
}

function woocommerce_wp_select_multiple($field) {
    global $thepostid, $post, $woocommerce;

    $thepostid = empty($thepostid) ? $post->ID : $thepostid;
    $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
    $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
    $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
    $field['value'] = isset($field['value']) ? $field['value'] : ( get_post_meta($thepostid, $field['id'], true) ? get_post_meta($thepostid, $field['id'], true) : array() );

    echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label><select id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['name']) . '" class="' . esc_attr($field['class']) . '" multiple="multiple">';

    foreach ($field['options'] as $key => $value) {

        echo '<option value="' . esc_attr($key) . '" ' . ( in_array($key, $field['value']) ? 'selected="selected"' : '' ) . '>' . esc_html($value) . '</option>';
    }

    echo '</select> ';

    if (!empty($field['description'])) {

        if (isset($field['desc_tip']) && false !== $field['desc_tip']) {
            echo '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />';
        } else {
            echo '<span class="description">' . wp_kses_post($field['description']) . '</span>';
        }
    }
    echo '</p>';
}

// Save product Fields ==============================================================================
add_action('woocommerce_process_product_meta', 'woo_add_custom_general_fields_save');

function woo_add_custom_general_fields_save($post_id) {


    $woocommerce_extra_service = $_POST['_select_extra_service'];
    if (!empty($woocommerce_extra_service))
        update_post_meta($post_id, '_select_extra_service', esc_attr($woocommerce_extra_service));

    // Number Field
    $woocommerce_hourly_charges = $_POST['_hourly_charges'];
    if (!empty($woocommerce_hourly_charges))
        update_post_meta($post_id, '_hourly_charges', esc_attr($woocommerce_hourly_charges));

    $woocommerce_minimum_recommended_hours = $_POST['_minimum_recommended_hours'];
    if (!empty($woocommerce_minimum_recommended_hours))
        update_post_meta($post_id, '_minimum_recommended_hours', esc_attr($woocommerce_minimum_recommended_hours));

    $woocommerce_minimum_product_service = $_POST['product_service'];
    if (!empty($woocommerce_minimum_product_service))
        update_post_meta($post_id, 'product_service', esc_attr($woocommerce_minimum_product_service));


    //Select Options=====================
    $woocommerce_bedroom_select = $_POST['_select_bedroom_filter'];
    if (!empty($woocommerce_bedroom_select))
        update_post_meta($post_id, '_select_bedroom_filter', esc_attr($woocommerce_bedroom_select));

    $woocommerce_bathroom_select = $_POST['_select_bathroom_filter'];
    if (!empty($woocommerce_bathroom_select))
        update_post_meta($post_id, '_select_bathroom_filter', esc_attr($woocommerce_bathroom_select));



    //Charges=================================
    /* $woocommerce_bedroom_charges = $_POST['_bedroom_charges'];
      if (!empty($woocommerce_bedroom_charges))
      update_post_meta($post_id, '_bedroom_charges', esc_attr($woocommerce_bedroom_charges));


      $woocommerce_bathroom_charges = $_POST['_bathroom_charges'];
      if (!empty($woocommerce_bathroom_charges))
      update_post_meta($post_id, '_bathroom_charges', esc_attr($woocommerce_bathroom_charges)); */
}

//Add Validation to booking form==========================================================
function form_cart_validation() {
    global $post;
    extract($_REQUEST);
    $id = $post->ID;
    if (empty($_REQUEST['bathroom']) && get_post_meta($product_id, '_select_bathroom_filter', true) == 1) {
        wc_add_notice(__('Please enter no of bathroom &hellip;', 'woocommerce'), 'error');
        return false;
    }
    if (empty($_REQUEST['bedroom']) && get_post_meta($product_id, '_select_bedroom_filter', true) == 1) {
        wc_add_notice(__('Please enter no of bedroom &hellip;', 'woocommerce'), 'error');
        return false;
    }
    if ($_REQUEST['recommended_hour'] > $_REQUEST['service_hour']) {
        // wc_add_notice( __( 'Please select greater than or equal to recommended hours &hellip;', 'woocommerce' ), 'error' );
        //    return false;
    }
    $start_time = date("H:i:s", strtotime($time));
    $t = EXPLODE(".", $recommended_hour);
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
    //wc_add_notice(__($time."==>".$start_time."==>".$h."=>".$m.'='.$mm.'Appointment must start after 7:00 AM and end before 11:00 PM==>'.$end_time, 'woocommerce'), 'error');
    if ($end_time > "23:00:00" || $end_time < "07:00:00") {
        wc_add_notice(__('Appointment must start after 7:00 AM and end before 11:00 PM', 'Avada'), 'error');
        return false;
    }
    return true;
}

add_action('woocommerce_add_to_cart_validation', 'form_cart_validation', 10, 3);

//Save Custom Cart form Booking form============================================================
function save_custom_cart_form($cart_item_data, $product_id) {
    global $woocommerce;
    $woocommerce->cart->empty_cart();
    WC()->session->__unset('extra_services');
    //WC()->session->__unset( 'extra_hours' );

    if (isset($_REQUEST['bathroom']) && get_post_meta($product_id, '_select_bathroom_filter', true) == 1) {
        $cart_item_data['bathroom'] = $_REQUEST['bathroom'];
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['bedroom']) && get_post_meta($product_id, '_select_bedroom_filter', true) == 1) {
        $cart_item_data['bedroom'] = $_REQUEST['bedroom'];
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['recommended_hour'])) {
        $cart_item_data['recommended_hour'] = $_REQUEST['recommended_hour'];
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['hourly_charges'])) {
        $cart_item_data['hourly_charges'] = $_REQUEST['hourly_charges'];
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['total_charges'])) {
        $cart_item_data['total_charges'] = $_REQUEST['total_charges'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['date'])) {
        $cart_item_data['date'] = $_REQUEST['date'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['time'])) {
        $cart_item_data['time'] = $_REQUEST['time'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['email'])) {
        $cart_item_data['email'] = $_REQUEST['email'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['service_hour'])) {
        //if($_REQUEST['service_hour'])
        $value = $_REQUEST['service_hour'];
        $str = floor($value) . ':' . (($value * 60) % 60);
        $cart_item_data['service_hour'] = $str;
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

 

    return $cart_item_data;
}

add_action('woocommerce_add_cart_item_data', 'save_custom_cart_form', 10, 2);

//Add/save Custome total Price to particular order======================================
function add_custom_total_price($cart_object) {

    if (!WC()->session->__isset("reload_checkout")) {
        $cart = WC()->cart->get_cart();
        $ar = array();
        foreach ( $cart_object->cart_contents as $key => $value ) {
            $ar =  $value['extra_services'];
            $hrc =  $value['hourly_charges'];
        }
        $total_extra_minutes = 0;
        $emc = 0;
        
        $item = "";
        foreach($cart as $items)
        {
            $item = $items; 
        }
        if(!empty($ar)){
            foreach($ar as $key => $services){ 
                if($services['id'] == 1573 && $item['wcsatt_data']['active_subscription_scheme'] != 0)
                { 
    
                }
                $total_extra_minutes = $total_extra_minutes + $services['extra_time'];
    
            }
        }
       if($total_extra_minutes>0){
            $emc = ($total_extra_minutes * $hrc)/60;
        }
        $cart_tc = $value['total_charges'] + $emc;
         
        foreach ($cart_object->cart_contents as $key => $value) {
            $value['data']->set_price( $cart_tc);
        }


    }
}


add_action('woocommerce_before_calculate_totals', 'add_custom_total_price', 10);

function extra_free_service_subscription(){
      
    $items = WC()->cart->get_cart();
    $produt = "";

    foreach($items as $item_id => $item)
    {
        $services = $item['extra_services'];
        foreach($services as $key => $service)
        {
            if($service['id'] == 1573 && $item['wcsatt_data']['active_subscription_scheme'] != 0)
             { 
                $services[$key]['title'] = "Productos de Limpieza (Gratis)";
                $services[$key]['charges'] = 0;
             }
        }
        $item['extra_services'] = $services;

        $items[$item_id] = $item;
    }

}

add_action('woocommerce_review_order_before_order_total', 'extra_free_service_subscription', 11);
 

//Render booking meta data on cart and checkout=========================================
function render_meta_on_cart_and_checkout($cart_data, $cart_item = null) {

    $custom_items = array();
    /* Woo 2.4.2 updates */
    if (!empty($cart_data)) {
        $custom_items = $cart_data;
    }
    if (isset($cart_item['bathroom'])) {
        $custom_items[] = array("name" => _e("Bathroom", "Avada"), "value" => $cart_item['bathroom']);
    }
    if (isset($cart_item['bedroom'])) {
        $custom_items[] = array("name" => _e("Bedroom", "Avada"), "value" => $cart_item['bedroom']);
    }

    if (isset($cart_item['recommended_hour'])) {
        $custom_items[] = array("name" => 'Recommended Hours', "value" => $cart_item['recommended_hour']);
    }
    if (isset($cart_item['hourly_charges'])) {
        $custom_items[] = array("name" => 'Hourly Charges', "value" => $cart_item['hourly_charges']);
    }
    if (isset($cart_item['total_charges'])) {
        
         $total_extra_minutes = 0;
         $emc = 0;
         if($cart_item["extra_services"]!=""){
            foreach($cart_item["extra_services"] as $services){
                $total_extra_minutes = $total_extra_minutes + $services['extra_time'];
            }
        }
        if($total_extra_minutes>0){
            $emc = ($total_extra_minutes * $cart_item['hourly_charges'])/60;
        }
        $cart_tc = $cart_item['total_charges'] + $emc;
        
        $custom_items[] = array("name" => 'Total Charges', "value" => $cart_tc);
    }
    if (isset($cart_item['date'])) {
        $custom_items[] = array("name" => 'Date', "value" => $cart_item['date']);
    }
    if (isset($cart_item['time'])) {
        $custom_items[] = array("name" => 'Time', "value" => $cart_item['time']);
    }
    if (isset($cart_item['email'])) {
        $custom_items[] = array("name" => 'Email', "value" => $cart_item['email']);
    }
    if (isset($cart_item['service_hour'])) {
         $sh = explode(':',$cart_item['service_hour']);
         $sh_h = $sh[0];
         $sh_m = $sh[1];
         if($sh_m == ''){
             $sh_m = 0;
         }
        $total_extra_minutes = 0;
        $total_extra_minutes = $total_extra_minutes + $sh_m;
        if($cart_item["extra_services"]!=""){
            foreach($cart_item["extra_services"] as $services){
                $total_extra_minutes = $total_extra_minutes + $services['extra_time'];
            }
            if($total_extra_minutes>=60){
                $total_extra_hours = intdiv($total_extra_minutes, 60);
                $total_extra_minutes = ($total_extra_minutes % 60);
            }
        }
        $total_hours = 0;
        $total_minutes = '';
        if($total_extra_hours > 0){
            $total_hours = $sh_h + $total_extra_hours;
        }
        else{
            $total_hours = $sh_h;
        }
        if($total_extra_minutes!=0){
            $total_hours = $total_hours.':'.$total_extra_minutes;
        }


        $custom_items[] = array("name" => 'Service Hours', "value" =>  $total_hours);
    }
    if (isset($cart_item['extraservices'])) {
        $custom_items[] = array("name" => 'Extra Services', "value" => $cart_item['extraservices'][0]['title']);
    }
    if (isset($values['extra_services'])) {
        wc_add_order_item_meta($item_id, "extra_services", $values['extra_services']);
    }
    if (isset($values['service_hour'])) {
        wc_add_order_item_meta($item_id, "service_hour", $values['service_hour']);
    }
    /* if (isset($values['extra_hours'])) {
      wc_add_order_item_meta($item_id, "extra_hours", $values['extra_hours']);
      } */

    return $custom_items;
}

add_filter('woocommerce_get_item_data', 'render_meta_on_cart_and_checkout', 10, 2);

//custom_cart_form_order_meta_handler===================================================================
function custom_cart_form_order_meta_handler($item_id, $values, $cart_item_key) {
    if(isset($values->legacy_values)==false)return;
    $item_meta =$values->legacy_values;
    
 
    if (isset($item_meta['bathroom'])) {
        wc_add_order_item_meta($item_id, "bathroom", $item_meta['bathroom']);
    }
    if (isset($item_meta['bedroom'])) {
        wc_add_order_item_meta($item_id, "bedroom", $item_meta['bedroom']);
    }

    if (isset($item_meta['recommended_hour'])) {
        wc_add_order_item_meta($item_id, "recommended_hour", $item_meta['recommended_hour']);
    }
    if (isset($item_meta['hourly_charges'])) {
        wc_add_order_item_meta($item_id, "hourly_charges", $item_meta['hourly_charges']);
    }
    if (isset($item_meta['total_charges'])) {
        
         $total_extra_minutes = 0;
         $emc = 0;
         if($item_meta["extra_services"]!=""){
            foreach($item_meta["extra_services"] as $services){
                $total_extra_minutes = $total_extra_minutes + $services['extra_time'];
            }
        }
        if($total_extra_minutes>0){
            $emc = ($total_extra_minutes * $cart_item['hourly_charges'])/60;
        }
        $cart_tc = $item_meta['total_charges'] + $emc;
        
        wc_add_order_item_meta($item_id, "total_charges", $cart_tc);
    }
    if (isset($item_meta['date'])) {
        wc_add_order_item_meta($item_id, "date", $item_meta['date']);
    }
    if (isset($item_meta['time'])) {
        wc_add_order_item_meta($item_id, "time", $item_meta['time']);
    }
    if (isset($item_meta['email'])) {
        wc_add_order_item_meta($item_id, "email", $item_meta['email']);
    }
    
    
        $sh = explode(':',$item_meta['service_hour']);
         $sh_h = $sh[0];
         $sh_m = $sh[1];
         if($sh_m == ''){
             $sh_m = 0;
         }
        $total_extra_minutes = 0;
        $total_extra_minutes = $total_extra_minutes + $sh_m;
        $total_extra_hours = 0;
        if($item_meta["extra_services"]!=""){
            foreach($item_meta["extra_services"] as $services){
                $total_extra_minutes = $total_extra_minutes + $services['extra_time'];
            }
            if($total_extra_minutes>=60){
                $total_extra_hours = intdiv($total_extra_minutes, 60);
                $total_extra_minutes = ($total_extra_minutes % 60);
            }
        }
        $total_hours = 0;
        $total_minutes = '';
        if($total_extra_hours > 0){
            $total_hours = $sh_h + $total_extra_hours;
        }
        else{
            $total_hours = $sh_h;
        }
        if($total_extra_minutes!=0){
            $total_hours = $total_hours.':'.$total_extra_minutes;
        }

    if (isset($item_meta['service_hour'])) {
        wc_add_order_item_meta($item_id, "service_hour", $total_hours);
    }
    if (isset($item_meta['extraservices'])) {
        wc_add_order_item_meta($item_id, "extraservices", $item_meta['extraservices'][0]['title']);
    }
    if (isset($item_meta['extra_services'])) { 
        wc_add_order_item_meta($item_id, "extra_services", $item_meta['extra_services']);
    }

    /* if (isset($values['extra_hours'])) {
      wc_add_order_item_meta($item_id, "extra_hours", $values['extra_hours']);
      } */
}

add_action('woocommerce_new_order_item', 'custom_cart_form_order_meta_handler', 1, 3);

// remove quantity on single product page ===================================================
function wc_remove_all_quantity_fields($return, $product) {
    return true;
}

add_filter('woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2);

//replace product details template with new one===========================================
function woocommerce_template_product_description() {
    wc_get_template('single-product/tabs/description.php');
}

add_action('woocommerce_before_single_product_summary', 'woocommerce_template_product_description', 20);

//redirect to checkout from add cart button========================================
add_filter('add_to_cart_redirect', 'direct_add_to_cart_redirect');

function direct_add_to_cart_redirect() {
    global $woocommerce;
    $checkout_url = $woocommerce->cart->get_checkout_url();
    return $checkout_url;
}

//remove_checkout_fields=================================================================
add_filter('woocommerce_checkout_fields', 'remove_checkout_fields');

function remove_checkout_fields($fields) {
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_email']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_postcode']);
    $fields['billing']['billing_cell'] = array(
        'placeholder' => _x('Celular', 'placeholder', 'woocommerce'),
        'class' => array('form-row-last'),
        'clear' => false
    );
    return $fields;
}

// WooCommerce Checkout Fields Hook===================================================================
add_filter('woocommerce_checkout_fields', 'custom_wc_checkout_fields_no_label');

// Our hooked in function - $fields is passed via the filter!
// Action: remove label from $fields
function custom_wc_checkout_fields_no_label($fields) {
    // loop by category
    foreach ($fields as $category => $value) {
        // loop by fields
        foreach ($fields[$category] as $field => $property) {
            // remove label property
            unset($fields[$category][$field]['label']);
        }
    }
    return $fields;
}

//Add New Order Fields==================================================================================
add_filter("woocommerce_checkout_fields", "new_order_fields");

function new_order_fields($fields) {

    $order = array(
        "billing_first_name",
        "billing_last_name",
        "billing_phone",
        "billing_cell",
        "billing_address_1",
        "billing_comments",
        "service_description"
    );
    foreach ($order as $field) {
        if(isset($fields["billing"][$field]))$ordered_fields[$field] = $fields["billing"][$field];
    }

    $fields["billing"] = $ordered_fields;
    $fields["billing"]["billing_cell"]["required"]=true;
    return $fields;
}

//Override checkout fields======================================================
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields($fields) {
    $fields['billing']['billing_first_name']['placeholder'] = 'Nombre';
    $fields['billing']['billing_last_name']['placeholder'] = 'Apellido';
    $fields['billing']['billing_phone']['placeholder'] = 'Teléfono';
    $fields['billing']['billing_address_1']['placeholder'] = 'Ingrese su direccion, nombre de barriada o edificio';
    $fields['billing']['billing_address_1']['required'] = true;
    $fields['billing']['billing_comments']['required'] = true;
    $fields['billing']['billing_comments']['placeholder'] = 'Número de casa, apto, piso, o referencias para llegar a su ubicacion.';
    $fields['billing']['service_description']['placeholder'] = 'Descripción del trabajo a detalle (opcional)';
    return $fields;
}

//Remove price ==========================================================================
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

//Change add to cart button text===================================================
add_filter('woocommerce_product_single_add_to_cart_text', 'themeprefix_cart_button_text');

function themeprefix_cart_button_text() {
    return __('GET STARTED', 'Avada');
}

//Add Extra Services on checkout page========================================================
add_action('woocommerce_check_extra_services', 'custom_checkout_extra_fields');

function custom_checkout_extra_fields($order_id) {
    echo '<div class="extraservices">';
    global $woocommerce, $post;
    $items = $woocommerce->cart->get_cart();
    $currentitem = "";
    foreach ($items as $item) {
        $currentitem = $item;
        $product_id = $item['product_id'];
        $extra_service_id = get_post_meta($product_id, "_select_extra_service", true);
    }
    $product = wc_get_product( $product_id );
    echo ''.WCS_ATT_Display_Product::get_subscription_options_content( $product ).'';
    
    echo '<h4>'. __("Seleccione Servicios Adicionales", "Avada-Child").'</h4>';
    $extra_services = WC()->session->get('extra_services');
    
    $extraserv = array();
    if (!empty($extra_services[0])) {
        foreach ($extra_services as $ser) {
            $extraserv[] = $ser['id'];
        }
    }
    
    
    
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
        echo '<ul class="products clearfix products-6 ">';
        $i=1;
        while ($loop->have_posts()) : $loop->the_post();
            global $product;
            $on_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail');
            $off_image = wp_get_attachment_url(get_post_meta(get_the_ID(), '_listing_off_image_id', true ));
            $price = get_post_meta(get_the_ID(), 'price', true);    
            $unit = "";$all_price = array();



            //echo $price;
            //echo '<!--<input type="text" name="service1" value="100"/><input type="text" name="service2" value="150"/>-->'
            ?>
            <li class="product type-product status-publish has-post-thumbnail product-type-simple extra-products additional_services <?php if (in_array($loop->post->ID, $extraserv)==false) { ?> unselected <?php } ?>" data-id="<?php echo $loop->post->ID; ?>"style="width:140px !important; float: left !important;">
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
                            <?php 
                                $extra_servie_title =  esc_html(get_the_title()); 
                                if($currentitem['wcsatt_data']['active_subscription_scheme'] != 0 && get_the_ID() == 1573)
                                {
                                    $extra_servie_title = "Productos de Limpieza (Gratis)";
                                }
                                echo $extra_servie_title;
                            ?>
                            <br/>
                        </p>
                        <?php // if( esc_html(get_the_title()) == 'Productos de Limpieza'){ ?>
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
                                                 $<input type="text" name="final_price_<?php print_r($loop->post->ID); ?>" id="final_price_<?= $i ?>" class="final_price" value="<?= $all_price[0][0]; ?>" readonly>
                                            </div>
                                            <?php }else{ ?>
                                                <input type="hidden" id="price_value_<?= $i; ?>_<?= $j; ?>" value="<?php print_r($u[0]); ?>" />
                                            <?php } $j++; } ?>
                                    </div>                              
                            <?php }else{?>
                                 <input type="text" name="final_price_<?php print_r($loop->post->ID); ?>" id="final_price_<?= $i ?>" class="no_price final_price" value="<?="$".$price; ?>" readonly>
                                
                            <?php } ?>
                        <div style="text-align:center;">
                            <input type="checkbox" id="checkbox<?php echo $loop->post->ID; ?>" name="services[]" <?php if (in_array($loop->post->ID, $extraserv)) { ?> checked <?php } ?> value="<?php echo $loop->post->ID; ?>" />
                        </div>
                    </div>
                </div>
            </li>
            <?php
            $i++;
        endwhile;
        echo '</ul>';
        wp_reset_query();
        ?>
        <script type="text/javascript"> 
            jQuery(document).on('click', '.extra-products', function () { 
                
                    //jQuery('body').trigger('update_checkout');
                 
                   //jQuery(document).trigger( 'wc_fragment_refresh');

                setTimeout(function () { 
                    //jQuery('body').trigger('update_checkout');
                }, 350);
                
            });  

            jQuery('.additional_services').click(function (e) {
                if(e.target.getAttribute("name") != "services[]")
                {
                    var classid = jQuery(this).attr('data-id');
                    var test=jQuery('#checkbox' + classid).prop("checked");
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
                    }
                    
                    jQuery('body').trigger('update_checkout');
                    jQuery(document).trigger( 'wc_fragment_refresh');
                }
                
                
            });
            
        </script>

        <?php
    } else {
        echo "No Extra Services Found";
    }
    echo '</div>';
}

function get_cart_items_from_session($item, $values, $key) {
    $item['extra_services'] = WC()->session->get('extra_services');
    //$item[ 'extra_hours' ]    =  WC()->session->get('extra_hours');
    return $item;
}

add_filter('woocommerce_get_cart_item_from_session', 'get_cart_items_from_session', 1, 3);
add_action('woocommerce_checkout_update_order_review', 'checkout_update_order_review_callback');

function checkout_update_order_review_callback($data) {
    global $woocommerce;
    $cartdata = $woocommerce->cart->get_cart();
    
    $item = "";

    foreach($cartdata as $items)
    {
        $item = $items;
    }

    //print_r($data); die;
    parse_str($data, $output);
    $selected_services = isset($output['services'])?$output['services']:array();
    $service_details = array();
    $service_hours = 0;
    do_action('woocommerce_calculated_total',$woocommerce->cart->get_total( 'total' ),$woocommerce->cart);
    if (!empty($selected_services)) {
        foreach ($selected_services as $service) {
            $Extra_service = get_post($service);
            $title = $Extra_service->post_title;
            $Extra_charges = get_post_meta($service, 'price', true);
        
            $Extra_hours = 0;
            if((get_post_meta($service, 'time_in_minutes', true) != '' || get_post_meta($service, 'time_in_minutes', true) != false) && is_numeric(get_post_meta($service, 'time_in_minutes', true))){
                $Extra_hours = get_post_meta($service, 'time_in_minutes', true);
            }

            if($service == 1573 && $item['wcsatt_data']['active_subscription_scheme'] != 0){
                $title = "Productos de Limpieza (Gratis)";
                $Extra_charges = 0.00;
            } 

            $extraarray = array('id' => $service, 'title' => $title, 'charges' => $Extra_charges, 'extra_time' => $Extra_hours);
            $service_details[] = $extraarray;
            //$service_hours=$service_hours+$Extra_hours;
            //do_action('woocommerce_cart_calculate_fees',$woocommerce->cart);
            WC()->session->set('final_price_'.$service, $output['final_price_'.$service]);  
        }
    }

    WC()->session->set('extra_services', $service_details);
    //WC()->session->set('extra_hours',$service_hours);
    WC()->cart->get_cart_from_session();
}

function woo_add_cart_fee($array) {
    global $woocommerce;
    $cartdata = $woocommerce->cart->cart_contents;
    foreach ($cartdata as $items) {
        $services = $items['extra_services'];
        if (!empty($services)) {
            foreach ($services as $serv) { 
                //$final_price =  WC()->session->get('final_price_'.$serv["id"],null);
               if($serv['id'] == 1573 && $items['wcsatt_data']['active_subscription_scheme'] != 0){
                    $serv['title'] = "Productos de Limpieza (Gratis)";
                    $serv['charges'] = 0.00;
                } 

               $woocommerce->cart->add_fee(__($serv['title'], 'woocommerce'), $serv['charges']);
            }
        }
    }
}

add_action('woocommerce_cart_calculate_fees', 'woo_add_cart_fee');

add_action('woocommerce_before_cart_totals', 'checkout_update_order_review_callback',10);

//add_action( 'woocommerce_before_calculate_totals', 'add_custom_price_data', 10, 1);
/*function add_custom_price_data( $cart_object ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    global $woocommerce;
    $cartdata = $woocommerce->cart->cart_contents;
    $eprice = 0;
    foreach ($cartdata as $items) {
        $services = $items['extra_services'];
        if (!empty($services)) {
            foreach ($services as $serv) {
                $eprice = $eprice + $serv['charges'];
            }
        }
    }

    $cart_object->subtotal = $cart_object->subtotal + $eprice;
}*/


//Call Ajax for add extra services======================================================================
/* function add_extra_services_ajax_ha ndler() {
  global $wpdb,$woocommerce; // this is how you get access to the database
  $items = $woocommerce->cart->cart_contents;
  $Extra_service = get_post($_POST['service_id']);
  $title = $Extra_service->post_title;
  $Extra_charges = get_post_meta($_POST['service_id'], 'price', true);
  $Extra_hours = get_post_meta($_POST['service_id'], 'hours', true);
  $extraarray = array('id' => $_POST['service_id'], 'title' => $title, 'charges' => $Extra_charges,'hours' => $Extra_hours);
  $cart_item_data["extra_service_title"]=$title;
  $cart_item_data["extra_service_price"]=$Extra_charges;
  echo json_encode($extraarray);
  exit;
  }
  add_action('wp_ajax_add_extra_services', 'add_extra_services_ajax_handler'); // add action for logged users
  add_action('wp_ajax_nopriv_add_extra_services', 'add_extra_services_ajax_handler'); // add action for unlogged users */

//Add Custom fields in admin registration========================================================

function custom_user_profile_fields($user) {
    global $wpdb;
    $services_posts = get_posts(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'ID', 'order' => 'ASC'
            )
    );
    $getservices = unserialize(get_the_author_meta('user_services', $user->ID));
    //$getaddress = $wpdb->get_results('SELECT * FROM tbladdress where user_id='.$user->ID );
    $pending_payment = get_user_meta($user->ID, '_total_payment', true);
    $paid_payment = get_user_meta($user->ID, '_total_paid', true);
    ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
        <tr>
            <th><label for="company">Phone 1</label></th>
            <td>
                <input type="text" class="regular-text" name="phone1" value="<?php echo esc_attr(get_the_author_meta('phone1', $user->ID)); ?>" id="company" />
            </td>
        </tr>
        <tr>
            <th><label for="company">Phone 2</label></th>
            <td>
                <input type="text" class="regular-text" name="phone2" value="<?php echo esc_attr(get_the_author_meta('phone2', $user->ID)); ?>" id="company" />
            </td>
        </tr>
        <tr>
            <th><label for="company">Personal ID:</label></th>
            <td>
                <input type="text" class="regular-text" name="personal_id" value="<?php echo esc_attr(get_the_author_meta('personal_id', $user->ID)); ?>" id="company" />
            </td>
        </tr>
        <tr>
            <th><label for="company">Commision Type:</label></th>
            <td>
                <label for="percentage"><input type="radio"  name="user_commision_type" value="percentage" id="percentage" <?php if(get_the_author_meta('user_commision_type', $user->ID) == 'percentage') { echo "checked='checked'"; } ?> /> Percentage</label>&nbsp;&nbsp;
                <label for="hourly"><input type="radio"  name="user_commision_type" value="hourly" id="hourly" <?php if(get_the_author_meta('user_commision_type', $user->ID) == 'hourly') { echo "checked='checked'"; } ?> /> Hourly</label>
            </td>
        </tr>
        <tr>
            <th><label for="company">Commission Percentage:</label></th>
            <td>
                <input type="text" class="regular-text" name="user_commision" value="<?php echo esc_attr(get_the_author_meta('user_commision', $user->ID)); ?>" id="company" />
            </td>
        </tr>
         <tr>
            <th><label for="company">Pay by Hour:</label></th>
            <td>
                <input type="text" class="regular-text" name="user_pay_by_hour" value="<?php echo esc_attr(get_the_author_meta('user_pay_by_hour', $user->ID)); ?>" id="user_pay_by_hour" />
            </td>
        </tr>
        <tr>
            <th><label for="company">Services :</label></th>
            <td>
                <select class="form-control" name="user_services[]" id="user_services" multiple="" style="width: 500px;" required="">
                    <?php
                    if (!empty($services_posts)) {
                        foreach ($services_posts as $services) {
                            ?>
                            <option value="<?php echo $services->ID; ?>" <?php
                            if (!empty($getservices)) {
                                if (in_array($services->ID, $getservices)) {
                                    ?> selected=""  <?php
                                        }
                                    }
                                    ?>><?php echo $services->post_title; ?></option>
                                    <?php
                                }
                            }
                            ?>
                </select>
            </td>
        </tr>

        <?php
        //$add_count=1;
        /* if(!empty($getaddress)){
          foreach ($getaddress as $add){
          ?>
          <!--<tr>
          <th><label for="company">Address:</label></th>
          <td>
          <input type="text" name="address<?php echo $add_count;?>" class="regular-text locationTextField"  id="locationTextField<?php echo $add_count;?>" value="<?php echo $add->address;?>">
          <div id="map<?php echo $add_count;?>"></div>
          <input id="lat<?php echo $add_count;?>" class="form-control" name="latitude<?php echo $add_count;?>" value="<?php echo $add->latitude;?>" type="hidden">
          <input id="lon<?php echo $add_count;?>" class="form-control" name="longitude<?php echo $add_count;?>" value="<?php echo $add->longitude;?>" type="hidden">
          </td>
          </tr>-->
          <tr>
          <th><label for="company">Address:</label></th>
          <td>
          <input type="text" name="address1" class="regular-text locationTextField"  id="locationTextField1" required="">
          <div id="map1"></div>
          <input id="lat1" class="form-control" name="latitude1" value="" type="hidden">
          <input id="lon1" class="form-control" name="longitude1" value="" type="hidden">
          </td>
          </tr>
          <?php
          $add_count++;
          }
          }
          else{ */
        ?>
        <tr>
            <th><label for="company">Address:</label></th>
            <td>
                <input type="text" name="address1" class="regular-text locationTextField"  id="locationTextField1" value="<?php echo get_the_author_meta('address', $user->ID); ?>" required="">
                <div id="map1"></div>
                <input id="lat1" class="form-control" name="latitude1" value="<?php echo get_the_author_meta('latitude', $user->ID); ?>" type="hidden">
                <input id="lon1" class="form-control" name="longitude1" value="<?php echo get_the_author_meta('longitude', $user->ID); ?>" type="hidden">
            </td>
        </tr>
        <!--<tr>
            <th><label for="company">Address:</label></th>
            <td>
                <input type="text" name="address1" class="regular-text locationTextField"  id="locationTextField1" required="">
                <div id="map1"></div>
                <input id="lat1" class="form-control" name="latitude1" value="" type="hidden">
                <input id="lon1" class="form-control" name="longitude1" value="" type="hidden">
            </td>
        </tr>-->
        <?php
        //}
        ?>
    <!--<input id="address_count" class="form-control" name="address_count" value="<?php echo ($add_count - 1); ?>" type="hidden">
    <tr id="add_more">
        <th><label for="company">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></th>
        <td>
            <a href="javascript:void(0)" id="add_more_address">Add More Addresses</a>
        </td>
    </tr>-->

    </table>
    <?php
    if ($pending_payment || !$pending_payment) {
       
        global $wpdb;
        $gettotal = $wpdb->get_results("select * from tbluserpayment where user_id=" . $_GET['user_id']);
        $count = count($gettotal);
        $per_page = 10;
        $pid = 0;
        if (isset($_GET['page_id']))
            $pid = ($_GET['page_id'] * $per_page);
        // $getpayments = $wpdb->get_results("select * from tbluserpayment where user_id=" . $_GET['user_id'] . " order by date DESC limit $pid,$per_page");
        $uuid = $_GET['user_id'];
        // echo $uuid;
        $getpayments = $wpdb->get_results("SELECT FROM_DAYS(TO_DAYS(p.post_date) - MOD(TO_DAYS(p.post_date) -1, 7)) AS week_beginning, SUM(m1.meta_value) AS total, id,  COUNT(*) AS total_count FROM wpstg2_posts p, wpstg2_postmeta m1, wpstg2_postmeta m2
    WHERE p.ID = m1.post_id and p.ID = m2.post_id
    AND m1.meta_key = '_order_total'
    AND m2.meta_key = 'assigned_user_id' AND m2.meta_value =  $uuid
    AND p.post_type = 'shop_order' AND p.post_status = 'wc-completed' GROUP BY FROM_DAYS(TO_DAYS(p.post_date) -MOD(TO_DAYS(p.post_date) -1, 7)) ORDER BY FROM_DAYS(TO_DAYS(p.post_date) -MOD(TO_DAYS(p.post_date) -1, 7))");
        ?>
        <style>
            .table {
                border-collapse: collapse;
                width: 100%;
            }

            .table .th, .td {
                text-align: left;
                padding: 8px;
                border-bottom: 1px solid #ddd;
            }

            /*.table .tr:nth-child(even){background-color: #f2f2f2}*/

            .table .th {
                background-color: #939B93;
                color: white;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
        <?php
           
            function getFees($value){
                $rate_chart = array(
                    array('min'=>1,'max'=>49,'fee'=>10),
                    array('min'=>50,'max'=>99,'fee'=>20),
                    array('min'=>100,'max'=>149,'fee'=>30),
                    array('min'=>150,'max'=>199,'fee'=>40),
                    array('min'=>200,'max'=>249,'fee'=>45),
                    array('min'=>250,'max'=>299,'fee'=>48),
                    array('min'=>300,'max'=>100000000000000,'fee'=>50)
                );
                foreach ($rate_chart as $arr) {
                    if((int)$value <= $arr['max'] && (int)$value >= $arr['min']){
                        return $arr['fee'];
                    }
                }
            }
        ?>
        <h3>Payment Details</h3>
        <table class="table stripe" id="example12">
            <thead>   
            <tr class="tr">
                <th class="th">Semana</th>
                <th class="th">Ventas</th>
                <th class="th">Servicios Broomday</th>
                <th class="th">Ingreso Neto</th>
                <th class="th">Ordenes</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($getpayments)) {
                foreach ($getpayments as $payment) {
                        $date_start = date('M d',strtotime($payment->week_beginning));
                        $date_end = date('M d',strtotime(date('Y-m-d',strtotime($payment->week_beginning.'+6 days'))));
                    ?>
                   <tr class="tr">
                        <td class="td"><?php echo $date_start.' - '.$date_end ;?></td>
                        <td class="td">$<?php echo $payment->total; ?></td>
                        <td class="td"> $<?php echo number_format(getFees($payment->total),2) ?> </td>
                        <td class="td">$<?=number_format(($payment->total - getFees($payment->total)),2);?></td>
                        <td class="td"><?=$payment->total_count; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
      
      
        <br/><br/><br/>
        
    <?php } ?>
    <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDc3rlwwWUHNOFL-jOOk9WilLBmhHtNDHU&libraries=places"></script>-->
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBmlFvgfwzMpTlMMA8AyZQym9YMZRxdUAg"></script>
    <script>
            jQuery(document).ready(function ($) {
                $("#payment_info").one("click", function (event) {
                    event.preventDefault();
                    var amount = $("#amount").val();
                    var note = $("#note").val();
                    var user_id = "<?php echo $_GET["user_id"]; ?>";
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {action: 'pay_payment_info', amount: amount, note: note, user_id: user_id},
                        success: function (result) {
                            window.location.reload();
                        }
                    });
                });
                $(".delete_payment").on("click", function () {
                    if (confirm('Are you sure you want to delete this record?')) {
                        var payment_id = $(this).data('id');
                        var user_id = "<?php echo $_GET["user_id"]; ?>";
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {action: 'delete_payment_info', payment_id: payment_id, user_id: user_id},
                            success: function (result) {
                                window.location.reload();
                            }
                        });
                    }

                });
                //initialize();
                $("#add_more_address").click(function () {
                    var get_count = $("#address_count").val();
                    var new_count = (parseInt(get_count) + parseInt(1));
                    $("#address_count").val(new_count);
                    $("#add_more").before('<tr><th><label for="company">Address:</label></th><td><input type="text" name="address' + new_count + '" class="regular-text address" id="locationTextField' + new_count + '">\n\
                                    <div id="map' + new_count + '"></div><input id="lat' + new_count + '" class="form-control" name="latitude' + new_count + '" value="" type="hidden"><input id="lon' + new_count + '" class="form-control" name="longitude' + new_count + '" value="" type="hidden"></td></tr>');
                    init();
                });


                google.maps.event.addDomListener(window, 'load', init);
                function init() {
                    //var get_count=$("#address_count").val();
                    //for(var j=1;j<=get_count;j++){
                    var A = 1;
                    var map = new google.maps.Map(document.getElementById('map' + A));
                    var input = document.getElementById('locationTextField' + A);

                    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
                    var autocomplete = new google.maps.places.Autocomplete(input);

                    autocomplete.bindTo('bounds', map);
                    autocomplete.addListener('place_changed', function () {
                        var place = autocomplete.getPlace();
                        var address = '';
                        if (place.address_components) {
                            address = [
                                (place.address_components[0] && place.address_components[0].short_name || ''),
                                (place.address_components[1] && place.address_components[1].short_name || ''),
                                (place.address_components[2] && place.address_components[2].short_name || '')
                            ].join(' ');
                        }
                        document.getElementById('lat' + A).value = place.geometry.location.lat();
                        document.getElementById('lon' + A).value = place.geometry.location.lng();
                    });
                    //}
                }
            });
            /*function initialize() {
             var acInputs = document.getElementsByClassName("address");
             for (var i = 0; i < acInputs.length; i++) {
             var autocomplete = new google.maps.places.Autocomplete(acInputs[i]);
             autocomplete.inputId = acInputs[i].id;
             google.maps.event.addListener(autocomplete, 'place_changed', function () {
             //document.getElementById("log").innerHTML = 'You used input with id ' + this.inputId;
             });
             }
             }*/
    </script>
    <?php
}

add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');
add_action("user_new_form", "custom_user_profile_fields");

function save_custom_user_profile_fields($user_id) {
    if (!current_user_can('manage_options'))
        return false;
    # save my custom field
    extract($_POST);
    $service = serialize($user_services);
    /* if(!empty($address_count)){
      global $wpdb;
      $wpdb->delete( "tbladdress", array( 'user_id' => $user_id ) );
      for($i=1;$i<=$address_count;$i++){
      $address=$_POST['address'.$i];
      $latitude=$_POST['latitude'.$i];
      $longitude=$_POST['longitude'.$i];
      $data_array=array('user_id'=>$user_id,'address'=>$address,'latitude'=>$latitude,'longitude'=>$longitude);
      $wpdb->insert("tbladdress",$data_array);
      }
      } */
    if ($total_paid != "") {
        $newtotal_payment = ($total_payment - $total_paid);
        $total_paid_amount = get_user_meta($user_id, '_total_paid', true);
        $total_paid_amount = ($total_paid_amount + $total_paid);
        update_usermeta($user_id, '_total_payment', $newtotal_payment);
        update_usermeta($user_id, '_total_paid', $total_paid_amount);
    }
    update_usermeta($user_id, 'phone1', $phone1);
    update_usermeta($user_id, 'phone2', $phone2);
    update_usermeta($user_id, 'personal_id', $personal_id);
    update_usermeta($user_id, 'user_services', $service);
    update_usermeta($user_id, 'user_commision', $user_commision);
    update_usermeta($user_id, 'user_commision_type', $user_commision_type);
     update_usermeta($user_id, 'user_pay_by_hour', $user_pay_by_hour);
    update_usermeta($user_id, 'address', $address1);
    update_usermeta($user_id, 'latitude', $latitude1);
    update_usermeta($user_id, 'longitude', $longitude1);
}

add_action('user_register', 'save_custom_user_profile_fields');
add_action('personal_options_update', 'save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');

//change place order button text============================================
add_filter('woocommerce_order_button_text', 'woo_custom_order_button_text');

function woo_custom_order_button_text() {
    return __('Book Service', 'Avada');
}

//Add condition for submit before place order======================================
function service_checkout_field_validation() {
    global $woocommerce;
    $items = WC()->cart->get_cart();
    $i = 0;
    foreach ($items as $cart_item_key => $cart_item) {
        if ($i == 0)
            $product_id = $cart_item["product_id"];
    }
    $term_list = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
    if ($term_list[0]->slug == "other-services") {
        if (empty($_POST['services']))
            wc_add_notice(__("Please Select Any One Extra Service."), 'error');
    }
    if($_POST['billing_commentts']==""){
        //wc_add_notice(__("Please add referencias adicionales para la direccion"), 'error');
    }
}

add_action('woocommerce_checkout_process', 'service_checkout_field_validation');
//Add the field to the checkout=========================================================================
add_action('woocommerce_show_customer_address_map', 'my_custom_checkout_field');

function my_custom_checkout_field($checkout) {
    $items = WC()->cart->get_cart();
    $i = 0;
    foreach ($items as $cart_item_key => $cart_item) {
    //  echo '<pre>';
            //  print_r($cart_item);
        if ($i == 0) {
           $product_id = $cart_item["product_id"];
            $email = isset($cart_item["email"])?$cart_item["email"]:"";
            $date = $cart_item["date"];
            $time = $cart_item["time"];
            $order_date= date('Y-m-d H:i:s', strtotime("$date $time"));
        }
    }
//  echo $email.'==>';
    /*echo '<input type="text" name="billing_comments" id="billing_comments" placeholder="Número de casa, apto, piso, o referencias para llegar a su ubicacion.">';*/
    echo '<div class="maphelp"></div><div id="map"></div>';
    echo '<input type="hidden" name="extra_service_name" id="extra_service_name">';
    echo '<input type="hidden" name="order_date" id="order_date" value="'.$order_date.'">';
    echo '<input type="hidden" name="cust_latitude" id="cust_latitude">';
    echo '<input type="hidden" name="cust_longitude" id="cust_longitude">';
    echo '<input type="hidden" name="product_service" id="product_service" value="' . $product_id . '">';
    echo '<input type="hidden" name="billing_email" id="billing_email" value="' . $product_id . '">';
    echo '<input type="hidden" name="billing_email" id="billing_email" value="' . $email . '">';

}

//Save Custom Filed on checkout page========================================================================
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');

function my_custom_checkout_field_update_order_meta($order_id) {
    if (!empty($_POST['billing_comments'])) {
        update_post_meta($order_id, 'billing_comments', sanitize_text_field($_POST['billing_comments']));
    }
    if (!empty($_POST['service_description'])) {
        update_post_meta($order_id, 'service_description', sanitize_text_field($_POST['service_description']));
    }
    if (!empty($_POST['cust_latitude'])) {
        update_post_meta($order_id, 'cust_latitude', sanitize_text_field($_POST['cust_latitude']));
    }
    if (!empty($_POST['cust_longitude'])) {
        update_post_meta($order_id, 'cust_longitude', sanitize_text_field($_POST['cust_longitude']));
    }
    if (!empty($_POST['billing_email'])) {
        update_post_meta($order_id, 'billing_email', sanitize_text_field($_POST['billing_email']));
        update_post_meta($order_id, '_billing_email', sanitize_text_field($_POST['billing_email']));
    }
    if (!empty($_POST['product_service'])) {
        update_post_meta($order_id, 'product_service', sanitize_text_field($_POST['product_service']));
    }
    if (!empty($_POST['order_date'])) {
        update_post_meta($order_id, 'order_date', sanitize_text_field($_POST['order_date']));
    }
}

//Set default order status to on-hold=======================================================================
add_action("woocommerce_thankyou", "custom_woocommerce_auto_complete_order");

function custom_woocommerce_auto_complete_order($order_id) {
    update_post_meta($order_id, 'assigned_user_id', "0");
}

//Add New Column in Users listing of admin ===================================================================
function new_modify_user_table($column) {
    $column['phone1'] = 'Phone';
    $column['total_paid'] = 'Total Paid';
    $column['pending_payment'] = 'Pending Payment';
    $column['job_completed'] = 'Jobs Completed';
    return $column;
}

add_filter('manage_users_columns', 'new_modify_user_table', 10, 1);

function new_modify_user_table_row($val, $column_name, $user_id) {
    $pending_payment = get_the_author_meta('_total_payment', $user_id);
    $paid_payment = get_the_author_meta('_total_paid', $user_id);
    $jobs = get_the_author_meta('_jobs_completed', $user_id);
    switch ($column_name) {
        case 'phone1' :
            return get_the_author_meta('phone1', $user_id);
            break;
        case 'total_paid' :
            if ($paid_payment != "")
                return "$" .number_format((float)$paid_payment, 2, '.', '') ;
            else
                return "$0.00";
            break;
        case 'pending_payment' :
            if ($pending_payment != "")
                return "$" .number_format((float)$pending_payment, 2, '.', '');
            else
                return "$0.00";
            break;
        case 'job_completed' :
            if ($jobs != "")
                return $jobs;
            else
                return "0";
            break;
        default:
    }
    return $val;
}

add_filter('manage_users_custom_column', 'new_modify_user_table_row', 10, 3);

/* * ******* Sortable User Columns ********** */
add_filter('manage_users_sortable_columns', 'mytheme_user_sortable_columns');

function mytheme_user_sortable_columns($sortable_columns) {
    $sortable_columns['phone1'] = 'Phone';
    $sortable_columns['total_paid'] = 'Total Paid';
    $sortable_columns['pending_payment'] = 'Pending Payment';
    return $sortable_columns;
}

add_action('pre_user_query', 'user_column_orderby');

function user_column_orderby($user_search) {
    global $wpdb, $current_screen;

    if (isset($current_screen->id) && 'users' != $current_screen->id) {
        return;
    }

    $vars = $user_search->query_vars;

    if ('phone1' == $vars['orderby']) {
        $user_search->query_from .= " INNER JOIN {$wpdb->usermeta} m1 ON {$wpdb->users}.ID=m1.user_id AND (m1.meta_key='phone1')";
        $user_search->query_orderby = ' ORDER BY UPPER(m1.meta_value) ' . $vars['order'];
    } elseif ('total_paid' == $vars['orderby']) {
        $user_search->query_from .= " INNER JOIN {$wpdb->usermeta} m1 ON {$wpdb->users}.ID=m1.user_id AND (m1.meta_key='total_paid')";
        $user_search->query_orderby = ' ORDER BY UPPER(m1.meta_value) ' . $vars['order'];
    } elseif ('pending_payment' == $vars['orderby']) {
        $user_search->query_from .= " INNER JOIN {$wpdb->usermeta} m1 ON {$wpdb->users}.ID=m1.user_id AND (m1.meta_key='pending_payment')";
        $user_search->query_orderby = ' ORDER BY UPPER(m1.meta_value) ' . $vars['order'];
    }
}

//Remove column form users listing======================================================================================
add_filter('manage_users_columns', 'remove_users_columns');

function remove_users_columns($column_headers) {
    unset($column_headers['posts']);
    return $column_headers;
}

//Add Cutom field on my account page===============================================================================
add_action('woocommerce_edit_account_form', 'my_woocommerce_edit_account_form');

function my_woocommerce_edit_account_form() {
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    if (!$user)
        return;
    $phone1 = get_user_meta($user_id, 'phone1', true);
    ?>
    <fieldset>
        <legend>Additional Information</legend>
        <p class="form-row form-row-thirds">
            <label for="birthdate">Teléfono:</label>
            <input type="text" name="phone1" value="<?php echo esc_attr($phone1); ?>" class="input-text" />
        </p>
    </fieldset>

    <?php
}

// end func

/**
 * This is to save user input into database
 * hook: woocommerce_save_account_details
 */
add_action('woocommerce_save_account_details', 'my_woocommerce_save_account_details');

function my_woocommerce_save_account_details($user_id) {
    update_user_meta($user_id, 'phone1', htmlentities($_POST['phone1']));
}

// end func

function storefront_child_remove_unwanted_form_fields($fields) {
    unset($fields ['account_first_name']);
    unset($fields ['account_last_name']);
    return $fields;
}

add_filter('woocommerce_default_address_fields', 'storefront_child_remove_unwanted_form_fields');

//Add something near to apply filters of user listing=======================================================================
add_action('restrict_manage_users', 'restrict_abc_manage_list');

function restrict_abc_manage_list() {
    global $wpdb;
    $total_paid = $total_pending = "";
    $users = get_users(array('fields' => array('ID')));
    foreach ($users as $user_id) {
        $pending_payment = get_the_author_meta('_total_payment', $user_id->ID);
        $paid_payment = get_the_author_meta('_total_paid', $user_id->ID);
        $total_pending = $total_pending + $pending_payment;
        $total_paid = $total_paid + $paid_payment;
    }
    ?>
    <label class="button" style="color:#000000;"><b>TOTAL PAID : $<?php echo $total_paid; ?> | PENDING COMMISIONS : $<?php echo $total_pending; ?>  </b></label>
    <?php
}

//When post status is changed completed to cancelled=============================================================================
add_action('woocommerce_order_status_changed', 'status_changed_processsing');

function status_changed_processsing($order_id, $checkout = null) {
    global $woocommerce;
    $order = new WC_Order($order_id);
    
    $total = $order->get_total();
    if ($order->get_status() == 'cancelled') {
        $user_id = get_post_meta($order_id, 'assigned_user_id', true);
        if ($user_id != "") {
            $jobs_completed = get_user_meta($user_id, '_jobs_completed', true);
            //if($jobs_completed!="" || $jobs_completed!="0")
            $jobs_completed = $jobs_completed - 1;
            update_user_meta($user_id, '_jobs_completed', $jobs_completed);
            $commision = get_user_meta($user_id, 'user_commision', true);
             $user_commision_type = get_user_meta($user_id, 'user_commision_type', true);
             $user_pay_by_hour = get_user_meta($user_id, 'user_pay_by_hour', true);
            $total_commision = (($total * $commision) / 100);
            $total_payment = get_user_meta($user_id, '_total_payment', true);
            $total_payment = ($total_payment - $total_commision);
            update_user_meta($user_id, '_total_payment', $total_payment);
        }
        global $wpdb;
        $wpdb->query("UPDATE tblassignjobs SET status='$order->status' WHERE order_id='$order_id'");
    }
}

//Hide Added to Cart message on checkout page in Woocommerce===============================================================
function empty_wc_add_to_cart_message($message, $product_id) {
    return '';
}

;
add_filter('wc_add_to_cart_message', 'empty_wc_add_to_cart_message', 10, 2);

//Hide Featured Image on WooCommerce Single Product Page==================================================================
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);

//user edit admin payment list insert records===============================================================================
add_action('wp_ajax_pay_payment_info', 'pay_payment_info');
add_action('wp_ajax_nopriv_pay_payment_info', 'pay_payment_info');

function pay_payment_info() {
    global $wpdb;
    $current_user = wp_get_current_user();
    $user_id = $_POST["user_id"];
    $amount = $_POST["amount"];
    $note = $_POST["note"];
    $pending_payment = get_user_meta($user_id, '_total_payment', true);
    $paid_payment = get_user_meta($user_id, '_total_paid', true);
    $total_pending = $pending_payment - $amount;
    $total_paid = $paid_payment + $amount;
    $data_array = array('user_id' => $user_id, 'paid_payment' => $amount, 'note' => $note, 'applied_by' => $current_user->ID);
    $wpdb->insert("tbluserpayment", $data_array);
    update_user_meta($user_id, '_total_payment', $total_pending);
    update_user_meta($user_id, '_total_paid', $total_paid);
    die();
}

//Admin payment list Delete Ajax===============================================================================
add_action('wp_ajax_delete_payment_info', 'delete_payment_info');
add_action('wp_ajax_nopriv_delete_payment_info', 'delete_payment_info');

function delete_payment_info() {
    global $wpdb;
    $payment_id = $_POST["payment_id"];
    $user_id = $_POST["user_id"];
    $paymnet_data = $wpdb->get_results("select * from tbluserpayment where id=$payment_id");
    $amount = $paymnet_data[0]->paid_payment;
    $pending_payment = get_user_meta($user_id, '_total_payment', true);
    $paid_payment = get_user_meta($user_id, '_total_paid', true);
    $total_pending = $pending_payment + $amount;
    $total_paid = $paid_payment - $amount;
    update_user_meta($user_id, '_total_payment', $total_pending);
    update_user_meta($user_id, '_total_paid', $total_paid);
    $wpdb->query("DELETE FROM tbluserpayment where id=$payment_id");
    //echo "heer".$amount."=>".$pending_payment."=>".$total_pending.'=>'.$total_paid;
    //print_r($paymnet_data[0]->id);
    die();
}
//Admin payment bono Delete Ajax===============================================================================
add_action('wp_ajax_delete_bono', 'delete_bono');
add_action('wp_ajax_nopriv_delete_bono', 'delete_bono');

function delete_bono() {
    global $wpdb;
    $post_id = $_POST["post_id"];
    $user_id = $_POST["user_id"];
    $wpdb->query("UPDATE wpstg2_posts SET post_bono_deleted = 1 where id=$post_id");
    // $paymnet_data = $wpdb->get_results("select * from tbluserpayment where id=$payment_id");
    // $amount = $paymnet_data[0]->paid_payment;
    // $pending_payment = get_user_meta($user_id, '_total_payment', true);
    // $paid_payment = get_user_meta($user_id, '_total_paid', true);
    // $total_pending = $pending_payment + $amount;
    // $total_paid = $paid_payment - $amount;
    // update_user_meta($user_id, '_total_payment', $total_pending);
    // update_user_meta($user_id, '_total_paid', $total_paid);
    // $wpdb->query("DELETE FROM tbluserpayment where id=$payment_id");
    //echo "heer".$amount."=>".$pending_payment."=>".$total_pending.'=>'.$total_paid;
    //print_r($paymnet_data[0]->id);
    die();
}



//Hide profile fileds from edit user if admin=====================================================
add_action('admin_init', 'user_profile_fields_disable');

function user_profile_fields_disable() {
    global $pagenow;
    // apply only to user profile or user edit pages
    if ($pagenow == 'user-edit.php') {
        add_action('admin_footer', 'user_profile_fields_disable_js');
    }
}

function user_profile_fields_disable_js() {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            var fields_to_disable = ['user-rich-editing-wrap', 'user-admin-color-wrap', 'user-comment-shortcuts-wrap',
                'show-admin-bar user-admin-bar-front-wrap', 'user-author_facebook-wrap', 'user-author_twitter-wrap', 'user-author_linkedin-wrap'
                        , 'user-author_dribble-wrap', 'user-author_gplus-wrap', 'user-author_custom-wrap', 'user-description-wrap'];
            for (i = 0; i < fields_to_disable.length; i++) {
                if ($('.' + fields_to_disable[i]).length) {
                    //$('#'+ fields_to_disable[i]).attr("disabled", "disabled");
                    $('.' + fields_to_disable[i]).css("display", "none");
                }
            }
        });
    </script>
    <?php
}

add_filter('woocommerce_login_redirect', 'pro_login_redirect',10,2);

function pro_login_redirect($redirect_to,$user) {
    
    
    return site_url("assigned-jobs");
}




//Add columns in order listing page=================================================================================
add_filter('manage_edit-shop_order_columns', 'custom_shop_order_column', 11);

function custom_shop_order_column($columns) {
    //add columns
    $columns['booking_date'] = __('Fecha de servicio', 'Avada');
    $columns['assinged_to'] = __('Asignado a:', 'Avada');
    return $columns;
}

add_action('manage_shop_order_posts_custom_column', 'custom_orders_list_column_content', 10, 2);

add_filter("manage_edit-shop_order_sortable_columns", 'booking_date_sort_function');

    function booking_date_sort_function($columns) {
        $custom = array(
            'booking_date' => 'booking_date',
        );
        return wp_parse_args($custom, $columns);
    }


function custom_orders_list_column_content($column) {
    global $post, $woocommerce, $the_order;
    if(is_null($the_order))return;
    $order_id = $the_order->get_id();
    //$order = wc_get_order($order_id);
    //$items = $order->get_items();
    switch ($column) {
        case 'booking_date' :
            /*if (!empty($items)) {
                $i = 0;
                foreach ($items as $key => $product) {
                    if ($i == 0) {
                        $item_id = $item['product_id'];
                        echo wc_get_order_item_meta($key, 'order_date', true) . "  " . wc_get_order_item_meta($key, 'time', true);
                    }
                }
            }*/
            //$order_id = $the_order->id;
            $booking_date = get_post_meta($order_id, 'order_date', true);
            echo $booking_date;
            break;

       case 'assinged_to' :
            $order_id = $the_order->get_id();
            $myVarTwo = get_post_meta($order_id, 'assigned_user_id', true);
            $users = get_user_by("id", $myVarTwo);
            if($users)echo $users->data->user_login;
            break;
    }
}

add_filter('manage_edit-shop_order_columns', 'remove_order_columns');

function remove_order_columns($column_headers) {
    unset($column_headers['order_notes']);
    unset($column_headers['customer_message']);
    return $column_headers;
}

//Thank u Page orders remove custom meta==============================================================================
if (!function_exists('wc_display_item_meta')) {

    /**
     * Display item meta data.
     * @since  3.0.0
     * @param  WC_Item $item
     * @param  array   $args
     * @return string|void
     */
    function wc_display_item_meta($item, $args = array()) {
        
         
        $strings = array();
        $html = '';
        $args = wp_parse_args($args, array(
            'before' => '<ul class="wc-item-meta"><li>',
            'after' => '</li></ul>',
            'separator' => '</li><li>',
            'echo' => true,
            'autop' => false,
        ));

        foreach ($item->get_formatted_meta_data() as $meta_id => $meta) {
            //print_r($meta);
            if ($meta->key == 'time' || $meta->key == "hourly_charges" || $meta->key == 'total_charges' || $meta->key == 'date' || $meta->key == 'email' || $meta->key == 'recommended_hour') {

            } else {
                if ($meta->key == 'service_hour')
                    $meta->display_key = "Hours";
                //if($meta->key=='time') $meta->display_key="Time";
                $value = $args['autop'] ? wp_kses_post($meta->display_value) : wp_kses_post(make_clickable(trim(strip_tags($meta->display_value))));
                $strings[] = '<strong class="wc-item-meta-label">' . wp_kses_post($meta->display_key) . ':</strong>' .'<br/>'. $value;
            }
        }

        if ($strings) {
            $html = $args['before'] . implode($args['separator'], $strings) . $args['after'];
        }

        $html = apply_filters('woocommerce_display_item_meta', $html, $item, $args);

        if ($args['echo']) {
            echo $html;
        } else {
            return $html;
        }
    }

}

//Add Area Radius in wp_options table================================================================
function myplugin_register_settings() {
    // add_option( 'add_radius_page', 'Enter Area Radius');
    register_setting('myplugin_options_group', 'area_radius', 'myplugin_callback');
}

add_action('admin_init', 'myplugin_register_settings');

function myplugin_register_options_page() {
    add_options_page('Page Title', 'Add Area Radius', 'manage_options', 'areaRa', 'myplugin_options_page');
}

add_action('admin_menu', 'myplugin_register_options_page');

function myplugin_options_page() {
    ?>

    <div>
        <?php screen_icon(); ?>
        <h2>Add Area Radius</h2>
        <form method="post" action="options.php">
            <?php settings_fields('myplugin_options_group'); ?>
            <table>
                <tr valign="top">
                    <th scope="row"><label for="inputnumber">Enter Area Radius</label></th>
                    <td><input type="text" id="area_radius" name="area_radius" value="<?php echo get_option('area_radius'); ?>" /> Km</td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

//Add Assigned By Dropdown in edit order page================================================
add_action('woocommerce_admin_order_data_after_order_details', 'misha_editable_order_meta_general');
//add_action('admin_enqueue_scripts', 'rudr_select2_enqueue');

function rudr_select2_enqueue() {
    //wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css');
    //wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery'));
    // please create also an empty JS file in your theme directory and include it too
    //wp_enqueue_script('mycustom', get_stylesheet_directory_uri() . '/mycustom.js', array('jquery', 'select2'));
}

function misha_editable_order_meta_general($order) {
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
    $order_id = $order->get_id();
    $assigned_user_id = get_post_meta($order_id, "assigned_user_id", true);
    ?>
    <p class="form-field form-field-wide wc-customer-user">
        <label for="customer_user">
            Assigned User ID:
        </label>
        <select class="user_list_dropdown" name="assigned_user_id">
            <option value="0">Select Assigned User</option>
            <?php
                if ($assigned_user_id == 0) {
                    ?>
                    <option value="0"></option>
                    <?php
            }
            foreach ($blogusers as $user) {
                $first_name = get_user_meta($user->ID, "first_name", true);
                $last_name = get_user_meta($user->ID, "last_name", true);
                ?>
                <option value="<?php echo $user->ID; ?>" <?php if ($assigned_user_id == $user->ID) { ?> selected="" <?php } ?>><?php echo $first_name . " " . $last_name . '(#' . $user->ID . " " . $user->user_email . ")"; ?></option>
            <?php }
            ?>
        </select>
    </p>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $(".user_list_dropdown").select2({
            });
        });
    </script>
    <?php
}

add_action( 'woocommerce_checkout_subscription_created', 'checkout_subscription_created_nextpayment');
 
function checkout_subscription_created_nextpayment($subscription) {
    if($subscription)
    {
        $order_date = get_post_meta($subscription->get_id(),"order_date", true);
        $order_date = date('Y-m-d H:i:s',strtotime($order_date)+18000);
        list($next_payment, $end_date) = generate_next_order_dates($order_date,$subscription->get_billing_interval(),$subscription->get_billing_period());
        $subscription->update_dates(array('next_payment'=>$next_payment,'end'=>$end_date));
    }
}


//When Order is updated call a function=====================================================
add_action("woocommerce_process_shop_order_meta","update_assigned_user_id");
function update_assigned_user_id($orderid){
    session_start();
    
    $_SESSION['prev_assigned_user_id']= get_post_meta($orderid,"assigned_user_id",true);
      
    $previous_employee = get_post_meta($orderid,"assigned_user_id",true);

    $orderdata = wc_get_order($orderid);
    //$employee = get_user_by("id", 109);/** failed from subscription **/
    
    $order_item = $orderdata->get_items();
 
    foreach($order_item as $item) 
    {  
        $order_item = $item;
                
    }
    
    if(is_object($order_item))$order_item = $order_item->get_id();
    
    $assigned_user_id= $_POST["assigned_user_id"];
    $standard_logo = Avada()->images->get_logo_image_srcset( 'logo', 'logo_retina' );
 
    if($previous_employee != $assigned_user_id && $assigned_user_id != 0)
    { 
        
        assign_employee_to_order($orderid, $assigned_user_id);

    }else
    { 
        if( $assigned_user_id ==0 && $previous_employee != "")
        { 
            update_post_meta($orderid,'assigned_user_id',0);
            $employee = get_user_by("id", $previous_employee);
             
            $billing_address=get_post_meta($orderdata->id,"_billing_address_1",true);
            $billing_phone=get_post_meta($orderdata->id,"_billing_phone",true);
            $billing_address_2 = get_post_meta($orderdata->id,"_billing_comments",true);
            $order_date=get_post_meta($orderid,"order_date",true);
            $mail_template = "<div><img src='".esc_url_raw( $standard_logo['url'] )."' /></div>";

            $mail_template .= "Hola <b>".$employee->display_name."</b><br/>

            <p>La orden <b>#".$orderdata->id."</b> que tenías asignada ha sido editada. Para evitar conflictos con tus otras citas hemos colocado la orden disponible nuevamente. 

            <h4>Los nuevos datos de la orden son:</h4>

            Fecha: ".date("Y-m-d h:i a", strtotime($order_date))."<br/>
            Tiempo contratado:  ".wc_get_order_item_meta($order_item,"service_hour")."<br/>
            Dirección: ".$billing_address_2.",".$billing_address."<br/>

            <p>Puedes verificar los datos completos iniciando sesión en nuestro sitio web y volver a asignarse la orden de acuerdo a tu disponibilidad.</p></br>

            Atentamente,<br/>
            Equipo de Broomday<br/>";

            $emailn= $employee->user_email;
            $email_subjectv = "IMPORTANTE - Orden asignada editada";
            $headerss = array('Content-Type: text/html; charset=UTF-8','From: <bukerz.com');
            if(wp_mail($emailn, $email_subjectv, $mail_template, $headerss)) {

                //echo json_encode(array("result"=>"complete"));
            }
        }

 
    } 
    
    global $wpdb;
    $tbl1 = $wpdb->prefix . 'posts';
    $tbl2 = $wpdb->prefix . 'postmeta';
    $job_data = $wpdb->get_results("SELECT $tbl1.*,pm5.meta_value AS assigned_user_id
                             FROM $tbl1 
                             LEFT JOIN $tbl2 AS pm5 ON ($tbl1.ID = pm5.post_id)
                             Where $tbl1.post_type = 'shop_order' and $tbl1.post_status= 'wc-processing' AND $tbl1.ID=$orderid 
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
            
            
            if($previous_employee != $_POST["assigned_user_id"]){
                 
                   
                   $user_infov = get_userdata($_SESSION['prev_assigned_user_id']);
                    $messagen = 'Hola '.$user_infov->display_name.'<br>
                    La orden '.$order_id.' que tenías asignada ha sido editada. Para evitar conflictos con tus otras citas hemos colocado la orden disponible nuevamente. <br>
                    Los nuevos datos de la orden son:<br>
                    Fecha: '.date("Y-m-d h:i a", strtotime($order_date)).'<br>
                    Tiempo contratado: '.wc_get_order_item_meta($order_item_id, 'service_hour', true).'<br>
                    Dirección: '.$billing_address.','.$billing_phone.'<br>
                    Puedes verificar los datos completos iniciando sesión en nuestro sitio web y volver a asignarse la orden de acuerdo a tu disponibilidad.<br><br>

                    Atentamente,<br>
                    Equipo de Broomday<br>';
                    
                    $emailn= $user_infov->user_email;
                    $email_subjectv = "IMPORTANTE - Orden asignada editada";
                    $headerss = array('Content-Type: text/html; charset=UTF-8','From: <bukerz.com');
                    if(wp_mail($emailn,$email_subjectv,$messagen,$headerss)) {
                        //echo json_encode(array("result"=>"complete"));
                    }
            }
            
            
            $newdate=date("jS F Y",strtotime($date));
            $cdate=date("Y-m-d H:i:s",strtotime("$date $time"));
            $check_date_time=date("Y-m-d H",strtotime('-12 hours',strtotime($cdate)));
            $current_time=date("Y-m-d H");
            $cur_date = date("Y-m-d");
            //echo $date."==>".$cdate."==>".$check_date_time."==>".$current_time."<br/>";
            //exit;
            //echo $date."==>".$cur_date."<br/>";
            //die();
            //if($check_date_time==$current_time){
            if($date==$cur_date){
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
    
}
add_action("woocommerce_order_actions_end","admin_update_order_action");
function admin_update_order_action($orderid){
    global $wpdb;
    session_start();
    $assigned_user_id= isset($_SESSION['prev_assigned_user_id'])?$_SESSION['prev_assigned_user_id']:null;
    $myorder = "SELECT * " ."FROM {$wpdb->prefix}woocommerce_order_items " .
                "WHERE order_id = ".$orderid." GROUP BY order_id; ";
    $job_detail = $wpdb->get_results($myorder);
    $order_item_id = $job_detail[0]->order_item_id;
    $date = wc_get_order_item_meta( $order_item_id, 'date', true );
    $time = wc_get_order_item_meta( $order_item_id, 'time', true );
    $order_date=date('Y-m-d H:i:s', strtotime("$date $time"));;
    //update_post_meta($orderid,'order_date',$order_date);
    $recommended_hour = wc_get_order_item_meta($order_item_id, 'recommended_hour', true);
    $extra_hours = wc_get_order_item_meta($order_item_id, 'extra_hours', true);
    $total_hours=$recommended_hour+$extra_hours;
    $start_time =date("H:i:s", strtotime($time));
    $t = EXPLODE(".", $total_hours);
    $h = $t[0];
    IF (ISSET($t[1])) {
        $m = $t[1];
    } ELSE {
        $m = "00";
    }
    if($m!='00') $mm =($h*60)+30;else $mm=($h*60);
    $end_time=trim(date('H:i:s',strtotime('+'.$mm.' minutes',strtotime($start_time))));
    $timeframe_end_time=trim(date('H:i:s',strtotime('+2 hours',strtotime($end_time))));
    $order = new WC_Order( $orderid );
    $status=$order->get_status();
    $select_data=$wpdb->get_results("select * from tblassignjobs where order_id=$orderid");
    $assigned_user_id=get_post_meta($orderid,"assigned_user_id",true);
    if(empty($select_data)){
        $data_array2=array('user_id'=>$assigned_user_id,'hours'=>$total_hours,'status'=>$status,'order_id'=>$orderid,
                'end_time'=>$end_time,'timeframe_end_time'=>$timeframe_end_time,'date'=>$date);
        $insert=$wpdb->insert("tblassignjobs",$data_array2);
    }
    else{
        $data_array=array('user_id'=>$assigned_user_id,'hours'=>$total_hours,'status'=>$status,
                'start_time'=>$start_time,'end_time'=>$end_time,'timeframe_end_time'=>$timeframe_end_time,'date'=>$date);
        $update=$wpdb->update("tblassignjobs",$data_array,array('order_id'=>$orderid));
    }
    unset($_SESSION['prev_assigned_user_id']);

}
//Send canclled email to customer=====================================================
 function wc_cancelled_order_add_customer_email( $recipient, $order ){
     return $recipient . ',' . $order->billing_email;
 }
 add_filter( 'woocommerce_email_recipient_cancelled_order', 'wc_cancelled_order_add_customer_email', 10, 2 );

//======================================================================================


add_action('woocommerce_admin_order_data_after_billing_address', 'show_order_count');
function show_order_count($order){
  $order_data = $order->get_data();
  $order_id = $order_data['id'];
  $cell=get_post_meta($order_id,'_billing_cell',true);;
  echo "<label>Cell NO: </label><br/>";echo "<label>$cell</label>";
}




function check_background_image() {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                var ch = $(".background-image").length;
                if (ch > 0) {
                    $('.background-image').css({
                        'background-position': 'unset'
                    });

                  $('#main').attr('style', 'padding-top: 0px !important');

                }
            });
        </script>
        <?php
    }


add_action('wp_footer', 'check_background_image');



add_filter( 'wc_order_is_editable', 'wc_make_processing_orders_editable', 10, 2 );
function wc_make_processing_orders_editable( $is_editable, $order ) {
    if ( $order->get_status() == 'processing' ) {
        $is_editable = true;
    }

    return $is_editable;
}

function replace_meta_label(){
    ?>
<script type="text/javascript">
jQuery(document).ready(function(){
    
        jQuery(".add").click(function() {
            var data_id = jQuery(this).data("id");
            var seq_id = jQuery(this).data("seq");
            var val_id = jQuery(this).data("val");
            var id = data_id+1;
            jQuery('.unitprice_'+seq_id).val(id);
            jQuery('.decrement_'+val_id).data("id",id);
           var per_unit_price = jQuery('#price_value_'+val_id+'_'+id).val();
           //alert(per_unit_price);
           if(per_unit_price == null){
               jQuery('#final_price_'+val_id).val(0);
           }else{
                jQuery('#final_price_'+val_id).val(per_unit_price);
           }
            jQuery(this).data("id",id);
                text.val(parseInt(text.val()));
            });
        jQuery(".decrement").click(function() {
            var data_id = jQuery(this).data("id");
            var seq_id = jQuery(this).data("seq");
            var val_id = jQuery(this).data("val");
            var id = data_id-1;
            jQuery('.unitprice_'+seq_id).val(id);
           var per_unit_price = jQuery('#price_value_'+seq_id+'_'+id).val();
         if(per_unit_price == null){
              jQuery('#final_price_'+seq_id).val(0);
           }else{
            jQuery('#final_price_'+seq_id).val(per_unit_price);
           }
           jQuery(this).data("id",id);
                text.val(parseInt(text.val()));
        });
    
    /*var data1 = jQuery("#final_price").val()
     setTimeout(function(){
            jQuery('.finalprice').text("$"+data1);
     }, 1000);
     */
});
</script>
<script type="text/javascript" charset="utf-8" async defer>
    jQuery(document).ready(function(){
        jQuery('.wc-item-meta-label').each(function(){
            var text = jQuery(this).text();
            jQuery(this).text(text.replace("bathroom", "Baños"));
        });
        jQuery('.wc-item-meta-label').each(function(){
            var text = jQuery(this).text();
            jQuery(this).text(text.replace("bedroom", "Habitaciones"));
        });
        jQuery('.wc-item-meta-label').each(function(){
            var text = jQuery(this).text();
            jQuery(this).text(text.replace("Hours", "Horas"));
        });
         jQuery('.subscription-next-payment').each(function(){
            var text = jQuery(this).text();
            jQuery(this).text(text.replace("Credit Card", "VIA cREDIT cARD"));
        });
        jQuery( "input[name=_wp_http_referer]" ).remove(); 
      /*var label = jQuery('.wc-item-meta-label').text();
      var bathroom = label.replace("bathroom", "Baños");
      jQuery('.wc-item-meta-label').text(bathroom);
      label = jQuery('.wc-item-meta-label').text();
      var bedroom = label.replace("bedroom", "Habitaciones");
      jQuery('.wc-item-meta-label').text(bathroom);*/
    });
</script>
    <?php
}
add_action('wp_head', 'replace_meta_label' );

add_action("woocommerce_subcription_renwal_order_date","renewal_order_update_date",10,2);
function generate_next_order_dates($date,$interval,$period){//payment and booking
    if($interval>1){
        $period = $period.'s';
    }
    if($period=="day" || $period=="days" ){
        $payment_date = date('Y-m-d H:i:s', strtotime("+$interval $period -3minutes", strtotime($date)));
    }else{
        $payment_date = date('Y-m-d H:i:s', strtotime("+$interval $period -3days", strtotime($date)));
    }
    $booking_date = date('Y-m-d H:i:s', strtotime("+$interval $period", strtotime($date)));
    return array($payment_date,$booking_date);
}
function renewal_order_update_date($renewal_order, $subscription){
    $order_date = $subscription->get_date('end');
    //$active_scheme = $subscription->billing_period."_".$subscription->billing_interval;
    if($subscription)
    {
        list($next_payment, $end_date) = generate_next_order_dates($order_date,$subscription->get_billing_interval(),$subscription->get_billing_period());
        $subscription->update_dates(array('next_payment'=>$next_payment,'end'=>$end_date));
        //update_post_meta($renewal_order->id , '_schedule_next_payment', $next_payment);
        update_post_meta($renewal_order->get_id(),"_schedule_end", $end_date);
        update_post_meta($renewal_order->get_id(),"order_date", date("Y-m-d H:i:s",strtotime($order_date)-18000));
        if($renewal_order->get_total()!=$subscription->get_total()){
            $renewal_order->set_total($subscription->get_total());
            $renewal_order->save();
        }
        $order_item_id = broomday_get_order_item_id($renewal_order->get_id());
        wc_update_order_item_meta( $order_item_id, "date", date("Y-m-d",strtotime($order_date)-18000));
        $employee=get_post_meta($subscription->get_id(),'assigned_user_id',true);
        if($employee==null){
            update_post_meta($renewal_order->get_id(),"assigned_user_id", "0");
        }else{
            assign_employee_to_order($renewal_order->get_id(), $employee);
        }
        //$renewal_order->update_status('processing');
    }
}
add_action("woocommerce_subcription_order_edit","woocommerce_order_edit",10,3);
function woocommerce_order_edit($order_id)
{
    $order = wc_get_order($order_id);
    
    $order_date = get_post_meta($order_id,"order_date", true);
    
    if($order->order_type == 'shop_subscription')
    {
        $subscription = wc_get_order($order_id);
        $active_scheme = $subscription->billing_period."_".$subscription->billing_interval;
        if($subscription)
        {
            
            
            $end_date = "";
            if($active_scheme == "week_1")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +7 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +4 day"));
            }
            if($active_scheme == "week_2")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +15 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +12 day"));
            }
            if($active_scheme == "month_1")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +30 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +26 day"));
            }

            update_post_meta($subscription->ID,"order_date", $order_date);
            update_post_meta($subscription->ID , '_schedule_next_payment', $next_payment);
            update_post_meta($subscription->ID,"_schedule_end", $end_date);
            
           }
        
    
    }
    
    
    $subcription_id = get_post_meta($order_id, "_subscription_renewal", true);
    if($subcription_id != ''){
        $subscription = wc_get_order($subcription_id);
        $active_scheme = $subscription->billing_period."_".$subscription->billing_interval;
        if($subscription)
        {
            
            
            $end_date = "";
            if($active_scheme == "week_1")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +7 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +4 day"));
            }
            if($active_scheme == "week_2")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +15 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +12 day"));
            }
            if($active_scheme == "month_1")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +30 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +26 day"));
            }

            update_post_meta($subscription->ID,"order_date", $order_date);
            update_post_meta($subscription->ID , '_schedule_next_payment', $next_payment);
            update_post_meta($subscription->ID,"_schedule_end", $end_date);
            update_post_meta($order_id , '_schedule_next_payment', $next_payment);
            update_post_meta($order_id,"_schedule_end", $end_date);
            update_post_meta($order_id,"order_date", $order_date);
           }
    
    }
    
    $order_date = get_post_meta($order->ID,"order_date", true);
    
    
    $subscriptions_parents = get_children([
        "post_parent" => $order->ID,
        "post_type" => "shop_subscription"
    ]);
    
    foreach($subscriptions_parents as $subscription)
    {
    
        $subscription = wc_get_order($subscription->ID);
        $active_scheme = $subscription->billing_period."_".$subscription->billing_interval;
        if($subscription)
        {
            
            
            $end_date = "";
            if($active_scheme == "week_1")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +7 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +4 day"));
            }
            if($active_scheme == "week_2")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +15 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +12 day"));
            }
            if($active_scheme == "month_1")
            {
                $end_date = date("Y-m-d H:i:s", strtotime($order_date." +30 day"));
                $next_payment = date("Y-m-d H:i:s", strtotime($order_date." +26 day"));
            }

            update_post_meta($subscription->ID,"order_date", $order_date);
            update_post_meta($subscription->ID , '_schedule_next_payment', $next_payment);
            update_post_meta($subscription->ID,"_schedule_end", $end_date);
            update_post_meta($order->ID , '_schedule_next_payment', $next_payment);
            update_post_meta($order->ID,"_schedule_end", $end_date);
            update_post_meta($order->ID,"order_date", $order_date);
           }
        
        
        
            
    }
    

}
add_action("woocommerce_subcription_order_edit_total","woocommerce_order_edit_total",10,3);
function woocommerce_order_edit_total($order_id){
    
    $order = wc_get_order($order_id);
    
    foreach ( $order->get_items() as $item_id => $item ) {
     $active_schem = wc_get_order_item_meta( $item_id,"_wcsatt_scheme",true);
     $hourly_charges = wc_get_order_item_meta( $item_id,"hourly_charges",true);
     $recommended_hour = wc_get_order_item_meta( $item_id,"recommended_hour",true);
     $extra_services = wc_get_order_item_meta( $item_id,"extra_services",true);
     $bathroom = wc_get_order_item_meta( $item_id,"bathroom",true);
     $bedroom = wc_get_order_item_meta( $item_id,"bedroom",true);
     $active= wc_get_order_item_meta( $item_id);
        $total_extra_minutes = 0;
         foreach($extra_services as $services){
                $total_extra_minutes = $total_extra_minutes + $services['extra_time'];
            }
        $extra_service = "";
        $fee_minut = 0 ;
            foreach( $order->get_items('fee') as $item_id => $item ){
                    $extra_service = $item->get_name();
                if (\strpos($extra_service, '45') !== false) {
                     $totat_minut = $fee_minut + 45;
                    
                  }
                            
                            
            }
        
        //echo $totat_minut; die;
        //die;
        
    }
    if($active_schem == '1_week_4'){
        $discount = 30;
    }
    if($active_schem == '2_week_3'){
        $discount = 25;
    }
    if($active_schem == '1_month'){
        $discount = 20;
    }
    
    if($bathroom > 1)
    {
    
        
            
    }
    if($bedroom > 1)
    {
    
        
            
    }
    
    $recommended_minut = $recommended_hour * 60;
    
    $totalminut = $recommended_minut + $total_extra_minutes;
        
    
    
    

}
add_action('woocommerce_checkout_process', 'woocommerce_checkout_process_func');

function woocommerce_checkout_process_func() {

    $cust_latitude = $_POST['cust_latitude'];
    $cust_longitude = $_POST['cust_longitude'];
    if($cust_latitude == "" || $cust_longitude == ""){
        wc_add_notice(__('Invalid <strong>Location</strong>, please check your input.'), 'error');
    }

}

function customjqueryscript() {
    if( wp_script_is( 'jquery', 'done' ) ) {
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function($){
          if($("body.woocommerce-checkout").length){
            $(document).on("input change","input#billing_address_1" ,function(){
                if($("input#cust_latitude").val() == "" || $("input#cust_longitude").val() == ""){
                    $("#billing_address_1_field").addClass("woocommerce-invalid-custom");
                    $("#billing_address_1_field").addClass("woocommerce-invalid-required-field-custom");
                    setTimeout(latLongChange, 1000);
                }else{
                    $("#billing_address_1_field").removeClass("woocommerce-invalid-custom");
                    $("#billing_address_1_field").removeClass("woocommerce-invalid-required-field-custom");
                }
            });

            $(document).on("input change","input#cust_longitude , input#cust_latitude" ,function(){
                if($(this).val() != "" && $("#billing_address_1_field").hasClass("woocommerce-invalid-custom") && $("#billing_address_1_field").hasClass("woocommerce-invalid-required-field-custom")){

                    $("#billing_address_1_field").removeClass("woocommerce-invalid-custom");
                    $("#billing_address_1_field").removeClass("woocommerce-invalid-required-field-custom");
                }
            });
          }
        var latLongChange =  function(){
            if($("input#cust_longitude").val() != "" && $("#billing_address_1_field").hasClass("woocommerce-invalid-custom") && $("#billing_address_1_field").hasClass("woocommerce-invalid-required-field-custom")){
                $("#billing_address_1_field").removeClass("woocommerce-invalid-custom");
                $("#billing_address_1_field").removeClass("woocommerce-invalid-required-field-custom");
            }else{
                setTimeout(latLongChange, 500);


            }
        };
      });

    </script>
    <?php
    }
}

add_action( 'wp_footer', 'customjqueryscript' );

add_action( 'wp_ajax_wcld_action', 'wcld_action' );
add_action( 'wp_ajax_nopriv_wcld_action', 'wcld_action' );

function wcld_action() {
    global $woocommerce;
    $coupon_code = $_POST['code']; // your coupon code here

    if( $woocommerce->cart->has_discount( $coupon_code ) ){
        return;
    }
 
    $woocommerce->cart->add_discount( $coupon_code );
    $msg = wc_get_notices();
    if(isset($msg['error'])){
        wp_send_json(array('msg' => $msg['error'][0]));
    }
    else if(isset($msg['success'])){
        wp_send_json(array('msg' => $msg['success'][0]));
    }
    //print_r(wc_get_notices());

    //if ( $woocommerce->cart->cart_contents_total >= 500 ) {
        
    //}*/
}

function custom_style_sheet() {
    wp_register_style('techybirds-style', get_stylesheet_directory_uri() . '/techybirds.css');
    wp_enqueue_style( 'techybirds-style');
}
add_action('wp_enqueue_scripts', 'custom_style_sheet');



add_action( 'woocommerce_email_order_meta', 'misha_add_email_order_meta', 10, 3 );
function misha_add_email_order_meta( $order_obj, $sent_to_admin, $plain_text ){
    

   

    // this order meta checks if order is marked as a gift
    $isd = get_post_meta( $order_obj->get_order_number(), 'service_description', true );
 
    // we won't display anything if it is not a gift
    if( empty( $isd ) )
        return;
 
    // ok, if it is the gift order, get all the other fields 
    // ok, we will add the separate version for plaintext emails
    if ( $plain_text === false ) {
        if($isd != ''){
        // you shouldn't have to worry about inline styles, WooCommerce adds them itself depending on the theme you use
        echo '<h2>Detalle adicional</h2>
        <ul>
        <li><strong>Descripción del servicion:</strong> ' . $isd . '</li>
        </ul>';
        }
        //Extra Servics
        $order = wc_get_order(  $order_obj->get_order_number() );
        $items = $order->get_items(); 
        echo '<h2>Extra Servicios: </h2>';
        $cnt = 0;
        $extra_service = "";
        echo '<ul>';
            foreach( $order->get_items('fee') as $item_id => $item ){
                
                echo '  <li><strong>'.$item->get_name().'</strong></li>';
                $cnt++;
            }
        echo '</ul>';
        /*foreach ( $order->get_items() as  $key => $item ) {
                
            // Compatibility for woocommerce 3+
            //$product_id = version_compare( WC_VERSION, '3.0', '<' ) ? $item['product_id'] : $item->get_product_id();
        
            // Here you get your data (without "true" argument as it is an array)
            $data = wc_get_order_item_meta( $key, 'extra_services'); 
            if(is_array($data) && count($data)>0){
                echo '<ul>';
                foreach($data as $md){
                    echo '  <li><strong>'.$md['title'].':</strong> ' . $md['extra_time'] . 'm</li>';
                    $cnt++;
                }
                echo '</ul>';
            }
            // To test data output (uncomment the line below)
            // print_r($data);
            
        }*/
        if($cnt==0){
            echo '<ul>
        <li>No  Extra Servicios</li>
        </ul>';
        }
        //Extra Services
    }
}


add_action( 'woocommerce_thankyou', 'misha_view_order_and_thankyou_page', 5 );
add_action( 'woocommerce_view_order', 'misha_view_order_and_thankyou_page', 5 );
 
function misha_view_order_and_thankyou_page( $order_id ){
    $isd =  get_post_meta( $order_id, 'service_description', true );
     if( $isd ) : ?>
         <!--<h2>Detalle adicional</h2>
    <table class="woocommerce-table shop_table gift_info">
        <tbody>    
            <tr>
                <th><strong>Descripción del servicion</strong></th>
                <td><?php echo get_post_meta( $order_id, 'service_description', true ); ?></td>
            </tr>
        </tbody>
    </table>-->
     <?php endif;
     }
     
add_action('woocommerce_thankyou', 'nextpaymentdatechange', 10, 1);  

function nextpaymentdatechange($order_id){
    
   //  global $woocommerce;
    //    $woocommerce->cart->empty_cart();
    
//if (WC_Subscriptions_Order::order_contains_subscription($order_id)) {
  // $order = wc_get_order( $order_id );
  // $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id );
  // foreach( $subscriptions_ids as $subscription_id => $subscription_obj )
     //   if($subscription_obj->order->id == $order_id) break;
   //echo $subscription_id."--hi";
   //echo "<pre>"; print_r($order); echo "</pre>";
   //echo "<pre>"; print_r($subscriptions_ids); echo "</pre>";
   // $subid = $order_id+1;
    //$nextdate = get_post_meta( $subid, '_schedule_next_payment', true );
    //$threedays_ago = date('Y-m-d H:i:s', strtotime('-3 days', strtotime($nextdate)));
    //update_post_meta($subid , '_schedule_next_payment', $threedays_ago);
//}
}

function action_woocommerce_order_status_processing($order_id) {
    
    
    //echo $order_id;die;
    
    
     $order = wc_get_order( $order_id );
    $items = $order->get_items(); 

     $related_subscriptions = wcs_get_subscriptions_for_renewal_order( $order );
    if ( wcs_is_order( $order ) && ! empty( $related_subscriptions ) ) {
        //$is_renewal = true;
        foreach ( $items as $item_id=>$item ) {
            $order_data = $order->get_data();
            $order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
            $thrag = date('Y-m-d', strtotime('+3 days', strtotime($order_date_created)));
            wc_update_order_item_meta($item_id,'date',$thrag);
        }
        
    } else {
        //$is_renewal = false;
    }
 

    
    
    
    
    
    //echo 'sdfsdf';die;
    if (wcs_order_contains_subscription($order_id)) {
        
     $order = wc_get_order( $order_id );
    $items = $order->get_items(); 
     $activeschem = '';
     $schem = array();
     foreach ( $items as $item_id=>$item ) {
        $activeschem .= wc_get_order_item_meta( $item_id, '_wcsatt_scheme', true );
        $product_id = $item->get_product_id();
        $rr = get_post_meta($product_id, '_wcsatt_schemes', true) ;
           
        $sch = maybe_unserialize($rr);
        
         foreach($sch as $schs){
                
                $printerval = $schs['subscription_period_interval'];
                $prperiod = $schs['subscription_period'];
                 $prlabel = $schs['subscription_label'];
                if($schs['subscription_length'] != 0){
                $prlenght = "_".$schs['subscription_length'];
                }
               
              $schem[$prlabel] = $printerval."_".$prperiod.$prlenght;  
               
            }
        
    }
   // echo $activeschem;
   // echo "<pre>"; print_r($schem); echo "</pre>";
    // exit;
    /* echo '<pre>';
     print_r($order);
     die;*/
     
     $subscriptions_ids = wcs_get_subscriptions_for_order( $order_id );
     foreach( $subscriptions_ids as $subscription_id => $subscription_obj )
        if($subscription_obj->order->id == $order_id) break;
 
       $bookindate = get_post_meta( $subscription_id, 'order_date', true );
        
       $sren = get_post_meta( $order_id, '_subscription_renewal', true );

        wp_update_post(
            array (
               'ID'            => $subscription_id,
                'post_date'     => $bookindate,
               'post_date_gmt' => $bookindate
            )
        ); 

     $bellingpe = get_post_meta( $subscription_id, '_billing_period', true );
     $interval = get_post_meta( $subscription_id, '_billing_interval', true );
     
      foreach($schem as $schemsname => $schems){
            if($schems == $activeschem){
                
                 if($schemsname == 'Monthly'){
                     
                     $paiddate = get_post_meta( $order_id, '_paid_date', true );
                      if(empty($sren)){
                      $threedays_ago = date('Y-m-d H:i:s', strtotime('+26 days', strtotime($bookindate)));
                      update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
                      }
                 }
                 
                 if($schemsname == 'Bi-Weekly'){
                     
                     $paiddate = get_post_meta( $order_id, '_paid_date', true );
                      if(empty($sren)){
                      $threedays_ago = date('Y-m-d H:i:s', strtotime('+12 days', strtotime($bookindate)));
                      update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
                      }
                     
                 }
                 
                 if($schemsname == 'Weekly'){
                     $paiddate = get_post_meta( $order_id, '_paid_date', true );
                     if(empty($sren)){
                      $threedays_ago = date('Y-m-d H:i:s', strtotime('+4 days', strtotime($bookindate)));
                      update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
                      }
                 }
                
            }
            
      }
     
     
   //  if($bellingpe == "week" && $interval == 1){
         
     //     $paiddate = get_post_meta( $order_id, '_paid_date', true );
       //   if(empty($sren)){
         // $threedays_ago = date('Y-m-d H:i:s', strtotime('+5 days', strtotime($bookindate)));
         // update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
         // }else{
              
               //$threedays_ago = date('Y-m-d H:i:s', strtotime('+4 days', strtotime($paiddate)));
              // update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
         // }
         
     //}elseif($bellingpe == "week" && $interval == 2){
       //  $paiddate = get_post_meta( $order_id, '_paid_date', true );
         // if(empty($sren)){
         // $threedays_ago = date('Y-m-d H:i:s', strtotime('+12 days', strtotime($bookindate)));
         // update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
         // }else{
              
              // $threedays_ago = date('Y-m-d H:i:s', strtotime('+11 days', strtotime($paiddate)));
              // update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
          //}
     //}
     
     //if($bellingpe == "week" && $interval == 4){
       //  $paiddate = get_post_meta( $order_id, '_paid_date', true );
         // if(empty($sren)){
         // $threedays_ago = date('Y-m-d H:i:s', strtotime('+26 days', strtotime($bookindate)));
          //update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
         // }else{
              
              // $threedays_ago = date('Y-m-d H:i:s', strtotime('+27 days', strtotime($paiddate)));
               //update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
          //}
     //}

     // $nextdate = get_post_meta( $subscription_id, '_schedule_next_payment', true );
     // $threedays_ago = date('Y-m-d H:i:s', strtotime('-3 days', strtotime($nextdate)));
     // update_post_meta($subscription_id , '_schedule_next_payment', $threedays_ago);
}
}
 
add_action( 'woocommerce_order_status_processing', 'action_woocommerce_order_status_processing', 10, 2 ); 




function wooc_extra_register_fields() {?>
<p class="form-row form-row-first">
<label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
<input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
</p>
<p class="form-row form-row-last">
<label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
<input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
</p>
 <p class="form-row form-row-wide">
<label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?></label>
<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_last_name'] ) )esc_attr_e( $_POST['billing_phone'] ); ?>" />
</p>
<?php
 }
 add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );
 function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {

if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {

$validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );

}

if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {

$validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );

}
         return $validation_errors;
}

add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );



function user_last_login( $user_login, $user ) {
    update_user_meta( $user->ID, 'last_logins', time() );
    if ( in_array( 'freelancers', $user->roles ) ) {
            // redirect them to the default place
           $redirect =  site_url('nuevas-oportunidades/');
          wp_redirect( $redirect);
        exit;
    }
    else if(in_array( 'employees', $user->roles )){ 
        $redirect =  site_url('nuevas-oportunidades/');
        exit;
    }
    else if(in_array( 'subscriptor', $user->roles )){ 
        $redirect =  site_url('nuevas-oportunidades/');
        exit;
    }
    else if(in_array( 'administrator', $user->roles )){ 
        $redirect =  site_url('wp-admin');
    }
    else {
            $redirect = home_url('product/limpieza');
            wp_redirect( $redirect);
            exit;
            }
    
   
}


add_action( 'wp_login', 'user_last_login',10,3);

function redirect_login_page(){
    if(isset($_GET['cronjob'])&&$_GET['cronjob']==true){
        //wp_mail('sui201837@gmail.com','cronjob','success');
    }
}

add_action( 'init','redirect_login_page' );


add_filter( 'manage_edit-shop_subscription_columns', 'MY_COLUMNS_FUNCTION' );
function MY_COLUMNS_FUNCTION( $columns ) {
    //echo 'sdfsdf';die;
    $new_columns = ( is_array( $columns ) ) ? $columns : array();
    unset( $new_columns[ 'order_actions' ] );
    
    //edit this for your column(s)
    //all of your columns will be added before the actions column
    $new_columns['customer_name'] = 'Customer Name';
    $new_columns['frequency'] = 'Frequency';
  //    $new_columns['MY_COLUMN_ID_2'] = 'MY_COLUMN_2_TITLE';
    
    //stop editing
    $new_columns[ 'order_actions' ] = $columns[ 'order_actions' ];
    //print_r($new_columns);
    
    return $new_columns;
}

add_filter( "manage_edit-shop_subscription_sortable_columns", 'MY_COLUMNS_SORT_FUNCTION' );
function MY_COLUMNS_SORT_FUNCTION( $columns ) 
{
    $custom = array(
            'customer_name'    => 'Customer Name', 
            'frequency'    => 'Frequency' 
            );
            
    //print_r($custom);     
    return wp_parse_args( $custom, $columns );
}
//add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'custom_valid_order_statuses_for_cancel', 10, 2 );
function custom_valid_order_statuses_for_cancel( $statuses, $order ){
         $order_status = $order->get_status();
    if($order_status == 'processing'){
        
         $subscriptions_ids = wcs_get_subscriptions_for_order( $order->id );
     foreach( $subscriptions_ids as $subscription_id => $subscription_obj )
        if($subscription_obj->order->id == $order->id) break;
                $Order_date = get_post_meta($order->id, 'order_date', true );
                
                $subscription = new WC_Subscription($subscription_id);
                $relared_orders_ids_array = $subscription->get_related_orders();
        
                  $current_date=  date('Y-m-d');
                 $Order_date = date('Y-m-d', strtotime('-3 days', strtotime($Order_date)));  
                //$diff=date_diff($current_date,$Order_date);
                    
                
                    $date2=date_create($current_date);
                    $date1=date_create($Order_date);        
                    $diff=date_diff($date1,$date2);
                    $threeday= $diff->format("%a");
                    
        
                if($current_date < $Order_date){
                       
                    //echo "yescancel";   
                 
                        
                    echo '<button class="woocommerce-button button btn" onclick="openForm('.$order->id.')">Cancel</button>';
                     
                     echo '<a href="'.esc_url( get_page_link( 6429 ) ).'?orderid='.$order->id.'" class="woocommerce-button button btn">Edit</a>';
                   
                    }   
                
            if(count($relared_orders_ids_array) == 0){    
                
                foreach ( $order->get_items() as $item_id => $item ) {
                   $simpledate = wc_get_order_item_meta($item_id , 'date', true);
                    $Order_date = get_post_meta($order->id, 'order_date', true );
                    
                   $issuborder = wc_get_order_item_meta($item_id , '_wcsatt_scheme', true);
                   $st_ago = date('Y-m-d H:i:s', strtotime('-2 days', strtotime($simpledate)));  
                   $current_date=  date('Y-m-d H:i:s');
                    $Order_date = date('Y-m-d H:i:s', strtotime('+3 days', strtotime($Order_date)));  
                    
                  /* if(strtotime('Y-m-d') < strtotime($st_ago)){
                       
                    //echo "yescancel";   
                    if(empty($issuborder)){
                    echo '<button class="woocommerce-button button view" onclick="openForm('.$order->id.')">Cancel</button>';
                     
                     echo '<a href="'.esc_url( get_page_link( 6429 ) ).'?orderid='.$order->id.'" class="woocommerce-button button view">Edit</a>';
                    }*/
                    
                    ?>
               
                    
                      <div class="form-popup" id="myForm" style='background: gray;'>
                 <?php echo do_shortcode("[contact-form-7 id='6335' title='Cancel Order Request']"); ?>
                <button type="button" class="btn cancel woocommerce-button button view" onclick="closeForm()">Close</button>
             </div>
             <script>
         
function openForm(name) {
    document.getElementsByClassName("ordercls")[0].disabled = true;
    document.getElementsByClassName("ordercls")[0].value = "Cancel order Request id # "+name;
    document.getElementById("myForm").style.display = "block";
}

function closeForm() {
    document.getElementById("myForm").style.display = "none";
}
</script>
                    <?php
                   
                }
            }
    }
    
}


function sv_wc_add_my_account_orders_column( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $name ) {
        if( $key == 'order-date' ){$new_columns[ $key ] = __( 'Fecha de Limpieza', 'woocommerce' );}
        else    $new_columns[ $key ] = $name;
    }
    return $new_columns;
}
add_filter( 'woocommerce_my_account_my_orders_columns', 'sv_wc_add_my_account_orders_column' );


function sv_wc_my_orders_ship_to_column( $order ) { 
     //foreach ( $order->get_items() as $item_id => $item ) { 
         $date = get_post_meta($order->id , 'order_date', true);
         
         $date =  __( date("F d, Y", strtotime($date)), 'woocommerce' );
         
          echo spanish_date($date);
        
     //}
}

function spanish_date($date)
{
    $nmeng = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $nmtur = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

    $dt = str_ireplace($nmeng, $nmtur, $date);

    return $dt;
}

add_action( 'woocommerce_my_account_my_orders_column_order-ship-to', 'sv_wc_my_orders_ship_to_column' );


add_action( 'woocommerce_admin_order_actions_end', 'add_content_to_wcactions_column' );
function add_content_to_wcactions_column($order) {
   
    // create some tooltip text to show on hover
    $tooltip = __('Edit Order.', 'woocommerce');

    // create a button label
    $label = __('Edit', 'woocommerce');
    
    $order = new WC_Order( $order->get_id() );

    // $url = get_home_url()."/wp-login.php?action=logout&redirect_to=".get_home_url()."/login/?editoid=".$order->id;

    echo '<a target="_blank" class="button tips custom-class" href="'.get_edit_post_link($order->get_id()).'" data-tip="'.$tooltip.'">'.$label.'</a>';
}


/*sp1996*/
/*function onboarding_update_fields( $fields = array() ) {
   $token = ( ! empty( $_GET['token'] ) ) ? $_GET['token'] : '';

   if( is_user_logged_in() ) {
       // Assign the value to the $_POST superglobal ONLY if not already set
        $user_id = get_current_user_id(); 
        $user_info = get_userdata($user_id);
        //print_r($user_info); 
        
        $phoneno = get_user_meta($user_id,'phone1' );
        $first_name = get_user_meta($user_id, "first_name", true);
        $last_name = get_user_meta($user_id, "last_name", true);

        
        if ( empty( $POST['billing_first_name'] ) ) {
           $_POST['billing_first_name'] = $first_name;
        }
        
        if ( empty( $POST['billing_last_name'] ) ) {
           $_POST['billing_last_name'] = $last_name;
        }
        
        if ( empty( $POST['billing_phone'] ) ) {
           $_POST['billing_phone'] = $phoneno[0];
        }
   }

   return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'onboarding_update_fields' );*/

/**
* @snippet Hide Edit Address Tab @ My Account
* @how-to Watch tutorial @ https://businessbloomer.com/?p=19055
* @sourcecode https://businessbloomer.com/?p=21253
* @author Rodolfo Melogli
* @testedwith WooCommerce 3.1.2
*/
 
//add_filter( 'woocommerce_account_menu_items', 'bbloomer_remove_address_my_account', 999 );
 
function bbloomer_remove_address_my_account( $items ) {
unset($items['edit-address']);
unset($items['dashboard']);
return $items;
}
 
add_action( 'woocommerce_before_add_to_cart_button', 'action_woocommerce_before_add_to_cart_button', 10, 0 ); 
add_action('wp_logout', 'action_woocommerce_before_add_to_cart_button');

function action_woocommerce_before_add_to_cart_button()
{
    global $woocommerce;
    $woocommerce->cart->empty_cart();
}
 
 

function filter_woocommerce_process_login_errors($validation_error, $post_username, $post_password)
{
    //if (strpos($post_username, '@') == FALSE)
    if (!filter_var($post_username, FILTER_VALIDATE_EMAIL)) //<--recommend option
    {
        throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'Please Enter a Valid Email ID.', 'woocommerce' ) );
    }
    return $validation_error;
}

// add the filter 
add_filter('woocommerce_process_login_errors', 'filter_woocommerce_process_login_errors', 10, 3);

?>
<?php
/*
 * Change the order of the endpoints that appear in My Account Page - WooCommerce 2.6
 * The first item in the array is the custom endpoint URL - ie http://mydomain.com/my-account/my-custom-endpoint
 * Alongside it are the names of the list item Menu name that corresponds to the URL, change these to suit
 */
function wpb_woo_my_account_order() {
    $myorder = array(
        'orders'       => __( 'Pedidos', 'woocommerce' ),
        'subscriptions'             => __( 'Suscripciones', 'woocommerce' ),
        'payment-methods'       => __( 'Método de pago', 'woocommerce' ),
        'edit-account'    => __( 'Detalles de la cuenta', 'woocommerce' ),
        'myreferrals'    => __( 'Recomendar un amigo', 'woocommerce' ),
        'customer-logout'    => __( 'Cerrar sesión', 'woocommerce' ),
    );
    return $myorder;
}
add_filter ( 'woocommerce_account_menu_items', 'wpb_woo_my_account_order' );


function assign_employee_to_order($order_id, $fixed_emplyee)
{
    update_post_meta($order_id,'assigned_user_id',$fixed_emplyee);
    global $wpdb;
    $order     = new WC_Order( $order_id );
    $subscriptions = wcs_get_subscriptions_for_order( wcs_get_objects_property( $order, 'id' ), array( 'order_type' => 'parent' ) );
    $fixed_emplyee = get_post_meta($order_id,"assigned_user_id", true);
    foreach($subscriptions as $subcription)
    {
        
        $assigned_emplyee = get_post_meta($subcription->get_id(),"assigned_user_id", true);
        
        if($assigned_emplyee == "" && $fixed_emplyee != "")
        {
            $is_employee_available = get_post_meta($fixed_emplyee, "employee_available", true);
             
            if($is_employee_available && $is_employee_available == 1)
            {
                update_post_meta($subcription->get_id(), "assigned_user_id", $fixed_emplyee);
                update_post_meta($fixed_emplyee,"employee_available",1);

                $assign_query = $wpdb->update(
                        "tblassignjobs",
                        [
                            "user_id" => $fixed_emplyee
                        ],
                        ['order_id' => $subcription->get_id()],
                        ['%d'],
                        ['%d']
                    ); 
            }else
            {
                $employees = get_users([
                    "role" => "freelancers" 
                ]);

                foreach($employees as $freelancer)
                {
                    if($freelancer->user_status == 0)
                    {
                        update_post_meta($subscription->get_id(), "assigned_user_id", $fixed_emplyee);
                        update_post_meta($fixed_emplyee,"employee_available",1);
                    }
                }
            }
        }

    }
    $order_item_id = broomday_get_order_item_id($order);
    $date = wc_get_order_item_meta( $order_item_id, 'date', true );
    $time = wc_get_order_item_meta( $order_item_id, 'time', true );
    $order_date=date('Y-m-d H:i:s', strtotime("$date $time"));
    //update_post_meta($order_id,'order_date',$order_date);
    $recommended_hour = wc_get_order_item_meta($order_item_id, 'recommended_hour', true);
    $extra_hours = wc_get_order_item_meta($order_item_id, 'extra_hours', true);
    $total_hours=$recommended_hour+$extra_hours;
    $start_time =date("H:i:s", strtotime($time));
    $t = EXPLODE(".", $total_hours);
    $h = $t[0];
    IF (ISSET($t[1])) {
        $m = $t[1];
    } ELSE {
        $m = "00";
    }
    if($m!='00') $mm =($h*60)+30;else $mm=($h*60);
    $end_time=trim(date('H:i:s',strtotime('+'.$mm.' minutes',strtotime($start_time))));
    $timeframe_end_time=trim(date('H:i:s',strtotime('+2 hours',strtotime($end_time))));
    $status=$order->get_status();
    $select_data=$wpdb->get_results("select * from tblassignjobs where order_id=$order_id");
    if(empty($select_data)){
        $data_array2=array('user_id'=>$fixed_emplyee,'hours'=>$total_hours,'status'=>$status,'order_id'=>$order_id,
            'start_time'=>$start_time,'end_time'=>$end_time,'timeframe_end_time'=>$timeframe_end_time,'date'=>$date);
        $insert=$wpdb->insert("tblassignjobs",$data_array2);
    }
    else{
        $data_array=array('user_id'=>$fixed_emplyee,'hours'=>$total_hours,'status'=>$status,
                'start_time'=>$start_time,'end_time'=>$end_time,'timeframe_end_time'=>$timeframe_end_time,'date'=>$date);
        $update=$wpdb->update("tblassignjobs",$data_array,array('order_id'=>$order_id));
    }
    if($status == "processing")send_email_customer_broomday($order_id, $fixed_emplyee,$order_item_id);
}
function iconic_remove_password_strength() {
    if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
        wp_dequeue_script( 'wc-password-strength-meter' );
    }
}
add_action( 'wp_print_scripts', 'iconic_remove_password_strength', 10 );
function load_custom_wp_admin_style() {
    wp_enqueue_script( 'my_admin_custom_script', get_stylesheet_directory_uri() . '/js/admincustom.js' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );
add_action( 'add_meta_boxes', 'listing_image_add_metabox' );
function listing_image_add_metabox () {
    add_meta_box( 'listingimagediv', __( 'Off Image', 'text-domain' ), 'listing_image_metabox', 'extra_services', 'side', 'low');
}
function listing_image_metabox ( $post ) {
    global $content_width, $_wp_additional_image_sizes;
    $image_id = get_post_meta( $post->ID, '_listing_off_image_id', true );
    $old_content_width = $content_width;
    $content_width = 254;
    if ( $image_id && get_post( $image_id ) ) {
    if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
        $thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
    } else {
        $thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnail' );
    }
    if ( ! empty( $thumbnail_html ) ) {
        $content = $thumbnail_html;
        $content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_listing_image_button" >' . esc_html__( 'Remove Off image', 'text-domain' ) . '</a></p>';
        $content .= '<input type="hidden" id="upload_listing_image" name="_listing_off_image" value="' . esc_attr( $image_id ) . '" />';
    }
    $content_width = $old_content_width;
    } else {
    $content = '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
    $content .= '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set Off image', 'text-domain' ) . '" href="javascript:;" id="upload_listing_image_button" id="set-listing-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'text-domain' ) . '" data-uploader_button_text="' . esc_attr__( 'Set listing image', 'text-domain' ) . '">' . esc_html__( 'Set listing image', 'text-domain' ) . '</a></p>';
    $content .= '<input type="hidden" id="upload_listing_image" name="_listing_off_image" value="" />';
    }
    echo $content;
}
add_action( 'save_post', 'listing_image_save', 10, 1 );
function listing_image_save ( $post_id ) {
    if( isset( $_POST['_listing_off_image'] ) ) {
    $image_id = (int) $_POST['_listing_off_image'];
        update_post_meta( $post_id, '_listing_off_image_id', $image_id );
    }
}
function get_next_booking_datetime_for_subscription($subscription){
    if($subscription->get_time( 'end' )!=0)return spanish_date(date('F j, Y h:i a',strtotime($subscription->get_date( 'end' ).' +5')));
    if($subscription->get_time( 'next_payment' )!=0){
        $date = $subscription->get_date("next_payment");
        if($subscription->get_billing_period()!="day"){
            return spanish_date(date('F j, Y h:i a',strtotime("+3days", strtotime($date.' +5'))));
        }
        return spanish_date(date('F j, Y h:i a',strtotime($date.' +5')));
    }
    $last_order = $subscription->get_last_order();
    $last_order = wc_get_order($last_order);
    $order_items = $last_order->get_items();
    $tmp = array_slice($order_items, 0, 1);
    $order_item = array_shift($tmp);
    $date = wc_get_order_item_meta( $order_item->get_id(), "date");
    $time = wc_get_order_item_meta( $order_item->get_id(), "time");
    $date =  spanish_date(date("F d, Y h:i a", strtotime($date." ".$time.' +5')));
    return $date;
}
add_action('woocommerce_view_order','view_order_broomday',10);
function view_order_broomday($order_id){
    /**
     * Order details table
     *
     * @author  Prospress
     * @package WooCommerce_Subscription/Templates
     */
    $order = wc_get_order($order_id);
    $order_items = $order->get_items();
    $order_item = array_shift($order_items);
    $booking_date =  get_order_start_date_broom($order);
    if($booking_date) $booking_date = spanish_date($booking_date);
    $bathrooms = wc_get_order_item_meta( $order_item->get_id(), "bathroom");
    $bedrooms = wc_get_order_item_meta( $order_item->get_id(), "bedroom");
    $service_hour = wc_get_order_item_meta( $order_item->get_id(), "service_hour");
    $services = array();
    foreach( $order->get_items('fee') as $item_id => $item ){
        $services[] = $item->get_name();
    }           
    $subscriptions = wcs_get_subscriptions_for_order( $order_id );
    foreach($subscriptions as $id=>$subscription){
        $subscription_id = $id;
    }
    ?>
    
    <table class="shop_table order_details">
        <tr>
            <td><?php esc_html_e( 'Estado', 'Avada' ); ?></td>
            <td><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></td>
        </tr>
        <tr>
            <td><?php esc_html_e( 'Booking Date', 'Avada' ); ?></td>
            <td><?php echo $booking_date; ?></td>
        </tr>
        <tr>
            <td><?php esc_html_e( 'Horas contratadas', 'Avada' ); ?></td>
            <td><?= "$bedrooms Cuartos $bathrooms Baños ($service_hour Horas)";?></td>
        </tr>
        <tr>
            <td><?php esc_html_e( 'Extra Services', 'Avada' ); ?></td>
            <td><?php echo implode(',',$services); ?></td>
        </tr>
        <tr>
            <td><?php esc_html_e( 'Address', 'Avada' ); ?></td>
            <td><?php echo $order->get_billing_address_1(); ?></td>
        </tr>
        <tr>
            <td><?php esc_html_e( 'Apto o No. de  Casa', 'Avada' ); ?></td>
            <td><?php echo get_post_meta($order_id,"billing_comments",true); ?></td>
        </tr>
        <tr>
            <td><?php esc_html_e( 'Total', 'Avada' ); ?></td>
            <td><?php echo wc_price($order->get_total()); ?></td>
        </tr>
   <!--  <tr>
            <td colspan=2><?php if(isset($subscription_id)){?>
                <a href="<?php echo esc_url( $subscription->get_view_order_url() ) ?>" class="button view"><?php echo esc_html_x( 'Ver a subscription', 'view a subscription', 'woocommerce-subscriptions' ); ?></a>
                <?php } ?>
            </td>
        </tr> -->
    </table>
    <?php 
}
add_filter('woocommerce_subscription_dates','woocommerce_subscription_dates_change',10);
function woocommerce_subscription_dates_change($dates){
    $dates['end'] =_x( 'Next Booking Date', 'table heading', 'Avata-child' );
    return $dates;
}
function get_order_start_date_broom($order){
    $date = get_order_start_datetime_broom($order);
    if($date != 0){
        return date("F d, Y  h:i a", strtotime($date));
    }
    return null;
}
function get_order_start_datetime_broom($order){
    $start_date = get_post_meta($order->get_id(),"order_date", true);
    if($start_date == 0){
        $order_items = $order->get_items();
        if(count($order_items)>0){
            $tmp = array_slice($order_items, 0, 1);
            $order_item = array_shift($tmp);
            $date = wc_get_order_item_meta( $order_item->get_id(), "date");
            $time = wc_get_order_item_meta( $order_item->get_id(), 'time', true );
            $start_date = $date." ".$time;
            return $start_date;
        }
    }
    return $start_date;
}
function get_subscription_title($order){
    $order_items = $order->get_items();
    $order_item = array_shift($order_items);
    $dd = get_post_meta($order_item["product_id"],'_wcsatt_schemes',true);
    $label = maybe_unserialize($dd);    
    $subscription_scheme  = wc_get_order_item_meta($order_item->get_id(),'_wcsatt_scheme');
    $title = "Orden de Limpieza";
    if($subscription_scheme != 0){
        foreach($label as $item){
            if($item['subscription_period_interval']."_".$item['subscription_period'] == $subscription_scheme)
            return $title = "Plan ".$item['subscription_label']." de Limpieza";
        }
    }
    return $title;
}
add_filter('woocommerce_email_heading_new_order','email_heading_new_order_broomday',10,2);
function email_heading_new_order_broomday($email_heading,$object=null){
    return get_subscription_title($object);
}
add_action('woocommerce_admin_order_data_after_shipping_address','add_booking_date_broom');
function add_booking_date_broom($order){
    $booking_date =  get_order_start_date_broom($order);
    if($booking_date) {$booking_date = spanish_date($booking_date);
    ?>
    <div><label>Booking Date</label>
        <?=$booking_date?>
    </div>
    <?php
    }
}
add_action('wp_head', 'checkout_ajax_spinner', 1000 );
function checkout_ajax_spinner() {
    ?>
    <style>
    .woocommerce .woocommerce-checkout #payment .blockUI.blockOverlay:before
     {
        height: 3em;
        width: auto;
        position: absolute;
        top: 20%;
        margin-left: -.5em;
        padding-top: 4.5em;
        display: block;
        content: "Por favor espere… estamos procesando su orden";
        -webkit-animation: none;
        -moz-animation: none;
        animation: none;
        background-image:url('<?php echo get_stylesheet_directory_uri() . "/img/loading.gif"; ?>') !important;
        background-position: top;
        background-size: 50px;
        background-repeat: no-repeat;
        line-height: 1;
        text-align: center;
        font-size: 1em;
        color:red;
    }
    </style>
    <?php
}
add_action( 'wp_logout', 'auto_redirect_homepage');
function auto_redirect_homepage(){
  wp_redirect( home_url('signout') );
  exit();
}
add_action('woocommerce_registration_redirect', 'registration_redirect_broom', 9);
function registration_redirect_broom(){
    return home_url('product/limpieza');
}
add_action('woocommerce_created_customer','created_customer_broom',10,2);
function created_customer_broom($customer_id,$new_customer_data){
    if(isset($_POST['billing_first_name'])){
        $billing_first_name = sanitize_text_field($_POST['billing_first_name']);
        update_user_meta($customer_id,'billing_first_name',$billing_first_name);
        update_user_meta($customer_id,'first_name',$billing_first_name);
    }
    if(isset($_POST['billing_last_name'])){
        $billing_last_name = sanitize_text_field($_POST['billing_last_name']);
        update_user_meta($customer_id,'billing_last_name',$billing_last_name);
        update_user_meta($customer_id,'last_name',$billing_last_name);
    }
    if(isset($new_customer_data['user_email'])){
        update_user_meta($customer_id,'billing_email',$new_customer_data['user_email']);
    }
    if(isset($_POST['billing_phone'])){
        $billing_phone = sanitize_text_field($_POST['billing_phone']);
        update_user_meta($customer_id,'billing_phone',$billing_phone);
        update_user_meta($customer_id,'phone1',$billing_phone);
    }
}
add_filter('woocommerce_account_menu_item_classes','account_menu_item_classes_broomday',10,2);
function account_menu_item_classes_broomday($classes, $endpoint){
    global $wp;
    if($endpoint == 'orders' && isset( $wp->query_vars['view-order'] ))$classes[] = 'is-active';
    if($endpoint == 'subscriptions' && isset( $wp->query_vars['view-subscription'] ))$classes[] = 'is-active';
    return $classes;
}
add_action( 'template_redirect', 'template_redirect_broomday' );
function template_redirect_broomday(){
    global $wp;
    if ( isset( $wp->query_vars['pagename'] ) && $wp->query_vars['pagename']=='signout' && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'customer-logout' ) ) { // WPCS: input var ok, CSRF ok.
        // Logout.
        wp_safe_redirect( str_replace( '&amp;', '&', wp_logout_url( wc_get_page_permalink( 'myaccount' ) ) ) );
        exit;

    }    
}
add_filter('woocommerce_get_endpoint_url','get_endpoint_url_broomday',10,2);
function get_endpoint_url_broomday($url,$endpoint){
    if(strpos($url,'customer-logout')>0){
        return home_url('signout');
    }
    return $url;
}
add_filter('woocommerce_subscriptions_is_recurring_fee','subscriptions_is_recurring_fee_broomday',10,2);
function subscriptions_is_recurring_fee_broomday($fee, $cart){
    return true;
}

add_action('searching_orders','fetch_orders_freelancers');
function fetch_orders_freelancers(){
   echo 'Hello';
   die();
}

add_action( 'wp_ajax_testFunction', 'testFunction' );
add_action( 'wp_ajax_nopriv_testFunction', 'testFunction' );
function testFunction()
{
   $args = array( 
                 'post_type' => 'shop_order',
                 'post_status' => 'wc-completed',
                  'date_query' => array(
                                        array(
                                            'after'     => date('Y-m-d',strtotime($_POST['date_start'])),
                                            'before'    => date('Y-m-d',strtotime($_POST['date_end'])),
                                            'inclusive' => true,
                                        ),
                                    ),
                 'meta_query' =>  array( 
                      array(
                             'key' => 'assigned_user_id',
                             'value' => $_POST['user_id'],
                             'compare' => '='
                           ),
                              
                  
                )
            );
    $the_query = new WP_Query( $args );
    $response = 'No record found!';
    if( $the_query->have_posts() ) :
        while( $the_query->have_posts() ): 
            $the_query->the_post();
            $response .= "<tr>
                            <td>".get_the_ID()."</td>
                            <td>".wc_price(get_post_meta(get_the_ID(), '_order_total', true ))."</td>
                            <td>".get_post_meta(get_the_ID(),'_assigned_hours',true)." Hrs</td>
                            <td>".get_the_date('M d H:i A', get_the_ID())."</td>
                          </tr>";
        endwhile;
        // wp_reset_postdata();  
    endif;
    // print_r( $the_query);
    echo $response;
    die();
}

add_action( 'wp_footer', 'ajax_fetch' );
function ajax_fetch() {
?>
    <script type="text/javascript">
        function fetch(ds,de){
            let htm = '<tr><td colspan="4">Cargando ...</td></tr>';
            jQuery('#countable_rows').html( htm );
            let id = <?= get_current_user_id()?>;
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: { action: 'testFunction', user_id: id,date_start:ds,date_end:de},
                success: function(data) {
                    jQuery('#countable_rows').html( data );
                }
            });
        
        }
    </script>

<?php
}
add_filter('woocommerce_my_account_message','broomday_my_account_message');
function broomday_my_account_message(){
    global $wp;
    if(isset($wp->query_vars['pagename'])&&$wp->query_vars['pagename'] == 'mi-cuenta'){
        return "Para calificar al personal debe ingresar a su cuenta.";
    }
    return "";
}
function send_email_customer_broomday($order_id,$employee,$order_item_id=false){
    if($order_item_id == 'testing'){
        $timestamp = time();
        $timestamp = $timestamp + 300;
        wp_schedule_single_event( $timestamp,'send_email_schedule_customer_broomday',array($order_id,$employee));
    }else{
        if(!$order_item_id){
            $order_item_id = broomday_get_order_item_id($order_id);
        }
        $booking_date = get_post_meta($order_id,'order_date',true);
        if($booking_date == null){
            $date = wc_get_order_item_meta( $order_item_id, 'date', true );
            $time = wc_get_order_item_meta( $order_item_id, 'time', true );
            $booking_date = "$date $time";
        }
        //$booking_date = "2019-06-17 7:43 AM";
        $timestamp = strtotime($booking_date)+18000-3600*4;
        if($timestamp>time()){
            //wp_schedule_single_event( time()+15,'send_email_schedule_customer_broomday',array($order_id,$employee));
            wp_schedule_single_event( $timestamp,'send_email_schedule_customer_broomday',array($order_id,$employee));
        }else{
            //echo "not created";
        }
    }
}
add_action('send_email_schedule_customer_broomday','send_email_schedule_customer_broomday_event',10,2);
function send_email_schedule_customer_broomday_event($order_id,$employee){
    //wp_mail("sui201837@gmail.com", "cron booking email", "$order_id, $employee");
    $order     = wc_get_order( $order_id );
    $subscriptions = wcs_get_subscriptions_for_order( $order_id, array( 'order_type' => 'parent' ) );
    if(empty($subscriptions)){
        $order_subscription="Order #$order_id";
    }else{
        $tmp = array_slice($subscriptions, 0, 1);
        $subscription_item = array_shift($tmp);
        $order_subscription="Order #$order_id/Subscription #".$subscription_item->get_id();
    }
    $order_date = get_post_meta($order_id,'order_date',true);
    $order_date =  date("F d, Y H:i:s", strtotime($order_date));
         
    $order_date =  spanish_date($order_date);

    $billing_first_name = $order->get_billing_first_name();
    $billing_address = $order->get_billing_address_1();
    $employee_first_name = get_user_meta($employee,'first_name',true);
    $employee_last_name = get_user_meta($employee,'last_name',true);
    $order_count = count(get_orders_employee($employee));
    $comments = get_comments( array('type'=>'reviewrating','karma'=>$employee) ); 
    $star = array();
    foreach($comments as $comment){
        if($comment->comment_approved==1){
            $star[] = $comment->comment_author;
        }
    }    
    $count = count($star);
    $sum = array_sum($star);
    
    if($count == 0)$average_rating = null;else $average_rating = $sum/$count;
    $employee_avatar = get_avatar_url($employee);
    $employee_address = get_user_meta($employee,'address',true);
    $customer_email = $order->get_billing_email();
    //$message=$message;
    $email_subject = "Empleado asignado a tu limpieza";
    $woocommerce_email = get_option('woocommerce_email_from_address');
    //$blog_email = get_option('admin_email');
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: BroomDay <'.$woocommerce_email.'>');
    ob_start();
    include  get_stylesheet_directory().'/templates/email-order.php';
    $message = ob_get_clean();
    wp_mail($customer_email, $email_subject, $message, $headers);
}