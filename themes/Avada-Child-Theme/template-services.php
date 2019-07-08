<?php 

/*

 * Template Name: Service

 */



// Do not allow directly accessing this file.

if ( ! defined( 'ABSPATH' ) ) {

	exit( 'Direct script access denied.' );

}



global $wp_query;



// Set the correct post container layout classes.

$blog_layout = avada_get_blog_layout();

$post_class  = 'fusion-post-' . $blog_layout;



$container_class = 'fusion-posts-container ';

$wrapper_class = 'fusion-blog-layout-' . $blog_layout . '-wrapper ';

if ( 'grid' == $blog_layout ) {

	$container_class = 'fusion-blog-layout-' . $blog_layout . ' fusion-blog-layout-' . $blog_layout . '-' . Avada()->settings->get( 'blog_grid_columns' ) . ' isotope ';

} elseif ( 'timeline' !== $blog_layout ) {

	$container_class .= 'fusion-blog-layout-' . $blog_layout . ' ';

}



// Set class for scrolling type.

if ( 'Infinite Scroll' === Avada()->settings->get( 'blog_pagination_type' ) ) {

	$container_class .= 'fusion-posts-container-infinite ';

	$wrapper_class .= 'fusion-blog-infinite ';

} elseif ( 'load_more_button' === Avada()->settings->get( 'blog_pagination_type' ) ) {

	$container_class .= 'fusion-posts-container-infinite fusion-posts-container-load-more ';

} else {

	$container_class .= 'fusion-blog-pagination ';

}



if ( ! Avada()->settings->get( 'featured_images' ) ) {

	$container_class .= 'fusion-blog-no-images ';

}



$number_of_pages = $wp_query->max_num_pages;

if ( is_search() && Avada()->settings->get( 'search_results_per_page' ) ) {

	$number_of_pages = ceil( $wp_query->found_posts / Avada()->settings->get( 'search_results_per_page' ) );

}

?>

<?php get_header(); 



$services_posts = get_posts(array(

    'post_type' => 'services',

    'post_status' => 'publish',

    'posts_per_page' => -1,

    'orderby' => 'ID', 'order' => 'ASC',

    'tax_query'=> array(

           'relation' => 'AND',

           array(

            'taxonomy' => 'services_category',

            'field' => 'slug',

            'terms' => 'popular',

        ))

    )

);

//echo "<pre>";print_r($services_posts);exit;

$category_services = get_terms('services_category');

?>

<div id="content" <?php Avada()->layout->add_class('content_class'); ?> <?php Avada()->layout->add_style('content_style'); ?>>

    <div id="posts-container" class="fusion-blog-archive <?php echo esc_attr( $wrapper_class ); ?>fusion-clearfix">

	<div class="<?php echo esc_attr( $container_class ); ?>" data-pages="<?php echo (int) $number_of_pages; ?>">

            <div class="fusion-timeline-icon"><i class="fusion-icon-bubbles"></i></div>

                <div class="fusion-blog-layout-timeline fusion-clearfix">

                    <?php while (have_posts()) : the_post(); ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?>>

                            <div class="fusion-post-content post-content">

                                <!--<h1 style="text-align:center;" data-fontsize="50" data-lineheight="80"><?php echo the_title(); ?></h1>-->

                                <h3 style="text-align:center;">Popular Services</h3>

                                <div class="service_popular_categories">

                                    <?php 

                                        foreach ($services_posts as $popular_services){

                                            ?>

                                            <div class="popular_services">

                                                <?php echo get_the_post_thumbnail( $popular_services->ID, 'thumbnail' ); ?>

                                                <?php echo $popular_services->post_title; ?>

                                            </div>

                                            <?php

                                        }

                                    ?>

                                </div>

                                <h3 style="text-align:center;">All Services</h3>

                                <div class="services_categories">

                                    <?php

                                    foreach($category_services as $cat_services){

                                       ?>

                                        <div class="cat_sections">

                                            <?php echo $cat_services->name; ?>

                                            <br/>

                                            <?php 

                                            $thiscat_posts = get_posts(array(

                                                    'post_type' => 'services',

                                                    'post_status' => 'publish',

                                                    'posts_per_page' => -1,

                                                    'orderby' => 'ID', 'order' => 'ASC',

                                                    'tax_query'=> array(

                                                           'relation' => 'AND',

                                                           array(

                                                            'taxonomy' => 'services_category',

                                                            'field' => 'term_id',

                                                            'terms' => $cat_services->term_id,

                                                        ))

                                                    )

                                                );

                                            ?>

                                            <?php

                                            if(!empty($thiscat_posts)){

                                                foreach($thiscat_posts as $cat){

                                            ?>

                                            <span ><?php echo $cat->post_title;?></span>

                                            <?php 

                                                }

                                            }

                                            ?>

                                        </div>

                                       <?php

                                    }

                                    ?>

                                </div>

                            </div>

                        </article>

                    <?php endwhile; ?>

                    <?php echo $msg; ?>

                </div>

        </div>

    </div>

</div>
<?php
wp_footer();
 include( get_template_directory() . '/footer.php'); ?>
<?php //get_footer();