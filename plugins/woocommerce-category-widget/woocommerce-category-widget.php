<?php

/*

Plugin Name: Woocommerce Category

Plugin URI: http://www.supportlive24x7.com/

Description: This is widget plugin which shows the list of woocommerce category.

Version: 1.2.0

Author: Arun Kushwaha- arunkushwaha87@gmail.com

Author URI: http://www.supportlive24x7.com/

License: GPL2

*/





class woocommerce_category extends WP_Widget {



	// constructor

	function __construct() {


        parent::__construct(false, $name = __('Woocommerce Category listing', 'woocommerce_category') );


    }





	// widget form creation

		function form($instance) {



		// Check values

		if( $instance) {

		     $title = esc_attr($instance['title']);

		     $select = $instance['category_id'];

		     $category_image = $instance['category_image'];

		     $show_title = $instance['show_title'];

		     // print_r( $category_image[0]);

		} else {

		     $title = '';

		     $select ='';

		     $category_image='';

		     $show_title = '';

		    

		}

		?>



		<p>

		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'woocommerce_category'); ?></label>

		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />

		</p>



		<?php

		  $taxonomy     = 'product_cat';

		  $orderby      = 'name';  

		  $show_count   = 0;      // 1 for yes, 0 for no

		  $pad_counts   = 0;      // 1 for yes, 0 for no

		  $hierarchical = 1;      // 1 for yes, 0 for no  

		  $title        = '';  

		  $empty        = 0;

		$args = array(

  'taxonomy'     => $taxonomy,

  'orderby'      => $orderby,

  'show_count'   => $show_count,

  'pad_counts'   => $pad_counts,

  'hierarchical' => $hierarchical,

  'title_li'     => $title,

  'hide_empty'   => $empty

);

?>

<?php $all_categories = get_categories( $args );



		?>

		<label for="<?php echo $this->get_field_id('category_id'); ?>"><?php _e('Please select category to show', 'woocommerce_category'); ?></label>

		<?php

		if(!empty($all_categories))

		{

		printf (

                '<select multiple="multiple" name="%s[]" id="%s" class="widefat" size="15" style="margin-bottom:10px">',

                $this->get_field_name('category_id'),

                $this->get_field_id('category_id')

            );

			

			



			// The Loop



			foreach ($all_categories as $cat) {

    //print_r($cat);

		    if($cat->category_parent == 0) {

		        $category_id = $cat->term_id;



			



				 printf(

                    '<option value="%s" class="hot-topic" %s style="margin-bottom:3px;">%s</option>',

                    $category_id,

                    in_array( $category_id, $select) ? 'selected="selected"' : '',

                    $cat->name

                );



				}

			}

			 echo '</select>';

			}

			else {



			// No posts were found

            echo 'No woocommerce category found ';



		}

			?>

		<p>

		<label for="<?php echo $this->get_field_id('category_image'); ?>"><?php _e('Show category image', 'woocommerce_category'); ?></label>

		

		<?php



		printf (

                '<select name="%s[]" id="%s" >',

                $this->get_field_name('category_image'),

                $this->get_field_id('category_image')

            );

            ?>

            <option value="yes" class="hot-topic" <?php echo $category_image[0] == 'yes' ? ' selected="selected"' : '';?> style="margin-bottom:3px;">

			    	Yes

			    </option>

			    <option value="no" class="hot-topic" <?php echo $category_image[0] == 'no' ? ' selected="selected"' : '';?> style="margin-bottom:3px;">

			    	No

			    </option>

            <?php



		echo '</select>';

		echo '</p>';

?>



		<p>

		<label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Show Title', 'woocommerce_category'); ?></label>

		

		<?php



		printf (

                '<select name="%s[]" id="%s" >',

                $this->get_field_name('show_title'),

                $this->get_field_id('show_title')

            );

            ?>

            <option value="yes" class="hot-topic" <?php echo $show_title[0] == 'yes' ? ' selected="selected"' : '';?> style="margin-bottom:3px;">

			    	Yes

			    </option>

			    <option value="no" class="hot-topic" <?php echo $show_title[0] == 'no' ? ' selected="selected"' : '';?> style="margin-bottom:3px;">

			    	No

			    </option>

            <?php



		echo '</select>';

		echo '</p>';





		}



			// update widget

		function update($new_instance, $old_instance) {

		      $instance = $old_instance;

		      // Fields

		      $instance['title'] = strip_tags($new_instance['title']);

	      $instance['category_id'] = esc_sql($new_instance['category_id']);

	      $instance['category_image'] = esc_sql($new_instance['category_image']);

	      $instance['show_title'] = esc_sql($new_instance['show_title']);

	      // print_r($instance);

		     return $instance;

		}



	// widget display

	// display widget

		function widget($args, $instance) {

		   extract( $args );

		   // these are the widget options

		   $title = apply_filters('widget_title', $instance['title']);

		   $category_ids = $instance['category_id'];

		   $showcategory_image = $instance['category_image'];

			$show_title = $instance['show_title'];



		   echo $before_widget;

		   // Display the widget

		   if($showcategory_image[0]=='yes')

			{

				$category_class_name=' woocommerce_category_box';

			}

			else

			{

				$category_class_name=' woocommerce_category_listing_box';

			}



			$category_class_name=$category_class_name.'  '.$this->id;

		   echo '<div class="widget-text '.$category_class_name.  '">';

		   	

		   // Check if title is set

		   if ( $title ) {

		      echo $before_title . $title . $after_title;

		   }



		   // Check if text is set

		   $taxonomy     = 'product_cat';

		  $orderby      = 'id';  

		  $show_count   = 0;      // 1 for yes, 0 for no

		  $pad_counts   = 0;      // 1 for yes, 0 for no

		  $hierarchical = 1;      // 1 for yes, 0 for no  

		  $title        = '';  

		  $empty        = 0;

		   $args = array(

  'taxonomy'     => $taxonomy,

  'orderby'      => $orderby,

  'show_count'   => $show_count,

  'pad_counts'   => $pad_counts,

  'hierarchical' => $hierarchical,

  'title_li'     => $title,

  'hide_empty'   => $empty

);

$all_categories = get_categories( $args );

				$term_slug = get_query_var( 'term' );



								

		   		echo '<div class="woocommerce_category_listings_box">';

		   

			if($showcategory_image[0]=='no')

		   	{

		   		echo '<ul class="woocommerce_category_listing">';

		   	}

		   foreach ($category_ids as  $categoryid) {

		   	// echo $category_id;

		   	

//print_r($all_categories);

				foreach ($all_categories as $cat) {

				    //print_r($cat);

				    if($cat->category_parent == 0) {

					        $category_id = $cat->term_id;



					   

					        if($category_id==$categoryid)

							{  



								if($showcategory_image[0]=='no')

							   	{

							   		$active='';

							   		if($term_slug==$cat->slug){

							   			$active=' active_list ';

							   		}

							   		echo '<li class="woocommercer_catgory_'.$category_id.' category_list '.$active.'" >';

							   	} 



								echo '<a href="'. get_term_link($cat->slug, 'product_cat') .'" class="single_list">';

								

								echo '<div class="category_name">';

								

								if($showcategory_image[0]=='yes' && $show_title[0]=='yes')

								{

									echo $cat->name;

								}

								elseif($showcategory_image[0]=='no')

								{

									echo $cat->name;

								}

								if($showcategory_image[0]=='yes' && $show_title[0]=='yes')

							   	{

							   		echo '<i class="icon-double-angle-right icon-large pull-right">&raquo;</i>';

							   	}

								echo '</div>';

								if($showcategory_image[0]=='yes')

							   	{

							   		$thumbnail_id = get_woocommerce_term_meta( $category_id, 'thumbnail_id', true );

									// get the image URL

									$image = wp_get_attachment_url( $thumbnail_id,'full' );

									// print the IMG HTML

									if(empty($image))

									{

										echo '<img src="' . plugins_url( '/tcp-no-image.jpg' , __FILE__ ) . '" width="200px"> ';

									}

									else

									{

										echo '<img src="'.$image.'" alt="" />';

									}

									

							   	}

								

								echo '</a>'; 



								if($showcategory_image[0]=='no')

							   	{

							   		echo '</li>';

							   	} 

							}

						}

					}

		       		   



		}

		   if($showcategory_image[0]=='no')

		   	{

		   		echo '</ul>';

		   	}

		   echo '</div>'.'</div>';

		   echo $after_widget;

		}

}



// register widget

//add_action('widgets_init', create_function('', 'return register_widget("woocommerce_category");'));





// Register style sheet.

add_action( 'wp_enqueue_scripts', 'woocommerce_category_style' );



/**

 * Register style sheet.

 */

function woocommerce_category_style() {

	wp_register_style( 'woocommerce_category_style', plugins_url( 'woocommerce-category-widget/css/style.css' ) );

	wp_enqueue_style( 'woocommerce_category_style' );

	

}


add_action('admin_notices', 'woocommerce_category_cat_admin_notice');
function woocommerce_category_cat_admin_notice(){
    
    global $current_user ;
    
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if ( ! get_user_meta($user_id, 'woocommerce_category_cat_ignore_notice',true)) {
        
        echo '<div class="updated">
           <p>Thank you for installing WooCommerce Category Plugin. Please consider a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=helplive24x7@gmail.com&lc=CA&item_name=Donation%20for%20Car%20Seller%20-%20Auto%20Classifieds%20Script&amount=0&currency_code=USD&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank">
           donation</a> to support this plugin. <a href="?woocommerce_category_cat_notice_ignore=0">Discard</a>
           
           

           </p>
           
           </div>';
    }       
}

add_action('admin_init', 'woocommerce_category_cat_notice_ignore');
function woocommerce_category_cat_notice_ignore() {
    global $current_user;
    $user_id = $current_user->ID;

    // add_user_meta($user_id, 'woocommerce_category_cat_ignore_notice', false, true);
    /* If user clicks to ignore the notice, add that to their user meta */
    if ( isset($_GET['woocommerce_category_cat_notice_ignore']) && $_GET['woocommerce_category_cat_notice_ignore']=='0' ) {
          add_user_meta($user_id, 'woocommerce_category_cat_ignore_notice', 'true', true);
    }
}
