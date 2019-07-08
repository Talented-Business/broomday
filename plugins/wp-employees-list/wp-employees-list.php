<?php
/*
Plugin Name: Wp Employees Lists
Description: Get all freelancer/empoyees user type inside woocommerce Employees page.
Author: Sanjay Parmar
Version: 0.1
*/


define( 'MY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

if( isset($_GET['page']) && $_GET['page']== 'wp-employees'){
    
    add_action('admin_enqueue_scripts', 'callback_for_setting_up_scripts');
    function callback_for_setting_up_scripts() {
        wp_register_script( 'datatable-jquery', '//code.jquery.com/jquery-3.3.1.js');
        wp_enqueue_script('datatable-jquery');
        
        wp_register_style( 'custom-style', plugins_url() .'/wp-employees-list/assets/css/custom.css' );
        wp_enqueue_style( 'custom-style' );
        
        wp_register_style( 'datatable-style', '//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' );
        wp_enqueue_style( 'datatable-style' );
        
        wp_register_script( 'datatable-script', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js');
        wp_enqueue_script('datatable-script');
        
        wp_register_script( 'custom-script', plugins_url().'/wp-employees-list/assets/js/custom.js');
        wp_enqueue_script('custom-script');
        
    }
}






/*
if(is_admin())
{
    new SP_Plugin();
}
*/

/**
 * Paulund_Wp_List_Table class will create the page to load the table
 */
class SP_Plugin
{
    
    // class instance
	static $instance;

	// customer WP_List_Table object
	public $customers_obj;
	
    /**
     * Constructor will create the menu item
     */
    public function __construct()
    {
        add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
        add_action( 'admin_menu', array($this, 'add_menu_employees_list_table_page' ));
    }
    
    public static function set_screen( $status, $option, $value ) {
		return $value;
	}

    /**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_employees_list_table_page()
    {
      $hook = add_submenu_page( 'woocommerce', 'Employees', 'Employees', 'manage_options', 'wp-employees', array($this, 'plugin_settings_page') );
      add_action( "load-$hook", [ $this, 'screen_option' ] );
    }
	

    /**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		?>
		<div class="wrap">
			<h2>All Employees</h2>
			

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns">
					<p class="search-box">
				<label class="screen-reader-text" for="user-search-input">Buscar usuarios:</label>
				<input type="search" id="user-search-input" name="s" value="">
				<input type="submit" id="search-submit" class="button" value="Buscar usuarios">
			</p>
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								
								$this->customers_obj->prepare_items();
								$this->customers_obj->display(); ?>
							</form>
							
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
<script type="text/javascript">
	/*jQuery("#search-submit").click(function() {
    var value = $("#user-search-input").val();

    jQuery("table tr").each(function(index) {
        if (index != 0) {

            $row = jQuery(this);

            var id = $row.find("td").text();

            if (id.indexOf(value) != 0) {
                jQuery(this).hide();
            }
            else {
                jQuery(this).show();
            }
        }
    });
});*/
	jQuery("#search-submit").click(function() {
    var value = $("#user-search-input").val().toLowerCase();
		 //var value = $(this).val().toLowerCase();
    jQuery("table tr").filter(function() {
      jQuery(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    
});
	
</script>

	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Customers',
			'default' => 5,
			'option'  => 'customers_per_page'
		];

		add_screen_option( $option, $args );

		$this->customers_obj = new Customers_List();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
}

add_action( 'plugins_loaded', function () {
	SP_Plugin::get_instance();
} );


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Customers_List extends WP_List_Table {

	/** Class constructor */
/*	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Customer', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Customers', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}*/


	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_customers( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		//$sql = "SELECT * FROM {$wpdb->prefix}users";
		$sql = "SELECT {$wpdb->prefix}users.* FROM {$wpdb->prefix}users INNER JOIN {$wpdb->prefix}usermeta ON {$wpdb->prefix}users.ID = {$wpdb->prefix}usermeta.user_id WHERE (({$wpdb->prefix}usermeta.meta_key = '{$wpdb->prefix}capabilities' AND {$wpdb->prefix}usermeta.meta_value LIKE '%freelancers%') OR ({$wpdb->prefix}usermeta.meta_key = '{$wpdb->prefix}capabilities' AND {$wpdb->prefix}usermeta.meta_value LIKE '%employees%' )) ";
    
        
        
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
        //echo"<pre>"; print_r($result);
		return $result;
	}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}users",
			[ 'ID' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		//$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}users where ";
		$sql = "SELECT {$wpdb->prefix}users.*, COUNT(*) FROM {$wpdb->prefix}users INNER JOIN {$wpdb->prefix}usermeta ON {$wpdb->prefix}users.ID = {$wpdb->prefix}usermeta.user_id WHERE (({$wpdb->prefix}usermeta.meta_key = '{$wpdb->prefix}capabilities' AND {$wpdb->prefix}usermeta.meta_value LIKE '%freelancers%') OR ({$wpdb->prefix}usermeta.meta_key = '{$wpdb->prefix}capabilities' AND {$wpdb->prefix}usermeta.meta_value LIKE '%employees%' )) ORDER BY {$wpdb->prefix}users.user_nicename";
       // print_r($wpdb->get_var( $sql ));
		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No customers avaliable.', 'sp' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
	   $userId =  $item['ID'];
		switch ( $column_name ) {
			case 'user_login':
			    return $item[ $column_name ];
			    break;
			case 'user_email':
				return '<a href="mailto'. $item[ $column_name ] .'">'. $item[ $column_name ] ."</a>";
				break;
			case 'phone':
				return get_user_meta($userId, "phone1", true);
				break;
			default:
				return apply_filters('manage_users_wp_employee_column',$item,$column_name,$userId);//print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {
	    //print_r($item);

		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		$title = '<strong class="user-name">' . $item['user_nicename'] . '</strong>';

		$actions = [
		    'edit'      => sprintf('<a href="?page=%s&action=%s&employees=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
			'delete' => sprintf( '<a href="?page=%s&action=%s&employees=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

        $args = array( "class" => 'user_icon' );
		return get_avatar( $item['ID'], 40, $args ) . $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Username', 'sp' ),
			'user_login' => __( 'Name', 'sp' ),
			'user_email'    => __( 'Email', 'sp' ),
			'phone'    => __( 'Phone', 'sp' )
		];

		return apply_filters('wp_employee_columns',$columns);
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'username', true ),
			'user_email' => array( 'email', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_customers( $per_page, $current_page );
	}

	public function process_bulk_action() {


        $action = $this->current_action();
        
        switch($action){
            
            case 'delete':
    		    //Detect when a bulk action is being triggered...
        		if ( 'delete' === $this->current_action() ) {
        
        			// In our file that handles the request, verify the nonce.
        			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
        
        			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
        				die( 'Go get a life script kiddies' );
        			}
        			else {
        				self::delete_customer( absint( $_GET['employees'] ) );
        
        		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
        		                // add_query_arg() return the current url
        		                wp_redirect( esc_url_raw(add_query_arg()) );
        				exit;
        			}
        
        		}
        		
    		    break;
    		case 'edit':
    		    
    		    
    		    
    global $wpdb;
    $user_id = $_GET['employees'];
    
    $services_posts = get_posts(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'ID', 'order' => 'ASC'
            )
    );
    $getservices = unserialize(get_the_author_meta('user_services', $user_id));
    $pending_payment = get_user_meta($user_id, '_total_payment', true);
    $paid_payment = get_user_meta($user_id, '_total_paid', true);
    ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmlFvgfwzMpTlMMA8AyZQym9YMZRxdUAg&libraries=places&callback=initAutocomplete"></script>
    <h3>Extra profile information</h3>
    <form action="" method="POST">
        <table class="form-table">
            <tr>
                <th><label for="company">Phone 1</label></th>
                <td>
                    <input type="text" class="regular-text" name="phone1" value="<?php echo esc_attr(get_the_author_meta('phone1', $user_id)); ?>" id="company" />
                </td>
            </tr>
            <tr>
                <th><label for="company">Phone 2</label></th>
                <td>
                    <input type="text" class="regular-text" name="phone2" value="<?php echo esc_attr(get_the_author_meta('phone2', $user_id)); ?>" id="company" />
                </td>
            </tr>
            <tr>
                <th><label for="company">Personal ID:</label></th>
                <td>
                    <input type="text" class="regular-text" name="personal_id" value="<?php echo esc_attr(get_the_author_meta('personal_id', $user_id)); ?>" id="company" />
                </td>
            </tr>
            <tr>
                <th><label for="company">Commision Type:</label></th>
                <td>
                	<label for="percentage"><input type="radio"  name="user_commision_type" value="percentage" id="percentage" <?php if(get_the_author_meta('user_commision_type', $user_id) == 'percentage') { echo "checked='checked'"; } ?> /> Percentage</label>&nbsp;&nbsp;
                    <label for="hourly"><input type="radio"  name="user_commision_type" value="hourly" id="hourly" <?php if(get_the_author_meta('user_commision_type', $user_id) == 'hourly') { echo "checked='checked'"; } ?> /> Hourly</label>
                </td>
            </tr>
            <tr>
                <th><label for="company">Commission Percentage:</label></th>
                <td>
                    <input type="text" class="regular-text" name="user_commision" value="<?php echo esc_attr(get_the_author_meta('user_commision', $user_id)); ?>" id="company" />
                </td>
            </tr>
             <tr>
                <th><label for="company">Pay by Hour:</label></th>
                <td>
                    <input type="text" class="regular-text" name="user_pay_by_hour" value="<?php echo esc_attr(get_the_author_meta('user_pay_by_hour', $user_id)); ?>" id="user_pay_by_hour" />
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
            <tr>
                <th><label for="company">Address:</label></th>
                <td>
                    <input type="text" name="address1" class="regular-text"  id="autocomplete" onFocus="geolocate()" value="<?php echo get_the_author_meta('address', $user_id); ?>" required="">
                    <div id="map1"></div>
                    <input id="lat1" class="form-control" name="latitude1" value="<?php echo get_the_author_meta('latitude', $user_id); ?>" type="hidden">
                    <input id="lon1" class="form-control" name="longitude1" value="<?php echo get_the_author_meta('longitude', $user_id); ?>" type="hidden">
                </td>
            </tr>
            <tr>
                <td><input type="submit" name="update_personalInfo" id="update_info" class="button button-primary"  value="Save"> </td>
                <td></td>
            </tr> 
    
        </table>
    </form>
    
    <hr >
    
    <?php 
    if(isset($_POST['update_personalInfo'])){
        
        if (!current_user_can('manage_options')){
            return false;
        }
        
        # save my custom field
        extract($_POST);
        
        $service = serialize($user_services);
        
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
        
        wp_redirect($_SERVER['HTTP_REFERER']);
        
    }
    
    ?>


      <?php
    if ($pending_payment || !$pending_payment ) {
       
        global $wpdb;
        $gettotal = $wpdb->get_results("select * from tbluserpayment where user_id=" . $_GET['employees']);
        $count = count($gettotal);
        $per_page = 10;
        $pid = 0;
        if (isset($_GET['page_id']))
            $pid = ($_GET['page_id'] * $per_page);
        // $getpayments = $wpdb->get_results("select * from tbluserpayment where user_id=" . $_GET['user_id'] . " order by date DESC limit $pid,$per_page");
        $uuid = $_GET['employees'];
        // echo $uuid;
        $getpayments = $wpdb->get_results("SELECT FROM_DAYS(TO_DAYS(p.post_date) - MOD(TO_DAYS(p.post_date) -1, 7)) AS week_beginning, SUM(m1.meta_value) AS total, id, post_bono_deleted, p.ID as p_ID,  COUNT(*) AS total_count FROM wpstg2_posts p, wpstg2_postmeta m1, wpstg2_postmeta m2
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

            #total_bono{
        	    float: right;
			    position: relative;
			    right: 250px;
			    top: 23px;
			    font-size: 1.5em;
            }
        </style>
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
        <span id = "total_bono">bono = $0</span>
       <table class="table stripe" id="example12">
            <thead>   
            <tr class="tr">
                <th class="th">Semana</th>
                <th class="th">Ventas</th>
                <th class="th">Bono</th>
                <th class="th">Servicios Broomday</th>
                <th class="th">Ingreso Neto</th>
                <th class="th">Ordenes</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($getpayments)) {
            	$total_bono = 0;
                foreach ($getpayments as $payment) {
                        $date_start = date('M d',strtotime($payment->week_beginning));
                        $date_end = date('M d',strtotime(date('Y-m-d',strtotime($payment->week_beginning.'+6 days'))));
                    ?>
                   <tr class="tr" row_id = <?=$payment -> p_ID?>>
                        <td class="td"><?php echo $date_start.' - '.$date_end ;?></td>
                        <td class="td">$<?php echo round(number_format($payment->total,2), 2); ?></td>
                        <?php
                        $bono = 0;
                        if($payment -> post_bono_deleted){
                        ?>
                        <td class="td">--</td>
                        <?php
                        }
                        else {
                        	$bono = number_format($payment->total,2) * 0.06;
                        	$total_bono += $bono;
                        ?>
                        <td class="td" bono-var = <?=$bono?>>$<?php echo round(number_format($payment->total,2) * 0.06, 2); ?><button class = "clear-button" style = "float:right;">Clear</button></td>
                        <?php }?>
                        <td class="td"> $<?php echo number_format(getFees($payment->total),2) ?> </td>
                        <td class="td" id = <?="total-value".$payment -> p_ID?>>$<?=number_format(($payment->total  - getFees($payment->total) - $bono),2);?></td>
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
    
           
   <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>      
   <script type="text/javascript">
   		var total_bono = <?=number_format($total_bono, 2)?>;
   		$("#total_bono").text("bono = $" + <?=number_format($total_bono, 2)?>);
   </script>            
    <script>
        $(document).ready(function() {
            $('#example12').DataTable({
              aaSorting: [[0, 'asc']]
            });

            $('#example12 tbody').on( 'click', '.clear-button', function () {
                var td = $(this).closest('td');
                var user_id = "<?php echo $_GET["employees"]; ?>";
                var bono = td.attr("bono-var");
                debugger;
                var retVal = confirm("Are you sure want to clear this bono ?");
                if( retVal == true ) {
                    var post_id = td.parent().attr("row_id");
                    $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {action: 'delete_bono', post_id: post_id, user_id: user_id},
                            success: function (result) {
                                td.text("--");
                                debugger;
                                total_bono = total_bono - bono;
                                $("#total_bono").text("bono = $" + total_bono.toFixed(2));
                                var total_value = $("#total-value" + post_id).text().substring(1);
                                total_value = parseFloat(total_value) + parseFloat(bono);
                                $("#total-value" + post_id).text("$" + total_value.toFixed(2));
                            }
                        });

                    
               } 
            } );
        });
    </script>  
        
        
    
        
        
     
        <?php     do_action('edit_user_profile_employees',$user_id);   ?>
        
        <script>
            
            jQuery(document).ready(function ($) {
                jQuery("#payment_info").one("click", function (event) {
                     //alert('test');
                    event.preventDefault();
                    var amount = jQuery("#amount").val();
                    var note = jQuery("#note").val();
                    var user_id = "<?php echo $_GET["employees"]; ?>";
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {action: 'pay_payment_info', amount: amount, note: note, user_id: user_id},
                        success: function (result) {
                            window.location.reload();
                            //alert(result);
                        }
                    });
                });
                jQuery(".delete_payment").on("click", function () {
                    if (confirm('Are you sure you want to delete this record?')) {
                        var payment_id = jQuery(this).data('id');
                        var user_id = "<?php echo $_GET["employees"]; ?>";
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
                    var input = document.getElementById('autocomplete');

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
      
        </script>
    		    
    		   <?php  
    		    exit();
    		    
    		    break;    
    		    
            default:
                
                		// If the delete bulk action is triggered
        		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
        		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        		) {
        
        			$delete_ids = esc_sql( $_POST['bulk-delete'] );
        
        			// loop over the array of record IDs and delete them
        			foreach ( $delete_ids as $id ) {
        				self::delete_customer( $id );
        
        			}
        
        			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
        		        // add_query_arg() return the current url
        		        wp_redirect( esc_url_raw(add_query_arg()) );
        			exit;
        		}
                
                break;
    		
    		
        }
		
		
	}
	

    //user edit admin payment list insert records===============================================================================
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
    


}

add_action('wp_ajax_pay_payment_info', 'pay_payment_info');
add_action('wp_ajax_nopriv_pay_payment_info', 'pay_payment_info');

add_action('wp_ajax_delete_payment_info', 'delete_payment_info');
add_action('wp_ajax_nopriv_delete_payment_info', 'delete_payment_info');


?>
