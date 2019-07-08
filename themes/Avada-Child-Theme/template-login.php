<?php 
/*
 * Template Name: Login
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
if ( ! function_exists( 'get_editable_roles' ) ) {
    require_once ABSPATH . 'wp-admin/includes/user.php';
}
?>
<?php get_header(); 

$services_posts = get_posts(array(
    'post_type' => 'services',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'ID', 'order' => 'ASC'
        )
);

if(isset($_POST["form_login"])){
    extract($_POST);
}
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
            <?php while ( have_posts() ) : the_post(); ?>
            <?php echo wp_kses_post( avada_render_post_title( get_the_ID() ) ); ?>
            <?php endwhile;?>
            <?php echo $msg;?>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="pwd">Username:</label>
                    <input type="text" class="form-control" id="username" placeholder="Enter Username" name="username" required="">
                </div>
                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password" required="">
                </div>
                <?php 
                $roles = get_editable_roles();
                ?>
                <div class="form-group">
                    <label for="pwd">User Type:</label>
                    <?php 
                    $i=0;
                    foreach (get_editable_roles() as $role_name => $role_info): 
                       if($role_name=="freelancers" || $role_name=="employees"){
                        ?>
                    <input type="radio" name="user_role" id="user_role" value="<?php echo $role_name;?>" <?php if($i==0) { ?> checked="" <?php }?> /> <?php echo ucfirst($role_name);?>
                    <?php
                          $i++;
                        }
                    endforeach; ?>
                </div>
                <br/>
                <input type="submit" name="form_login" class="fusion-button button-flat fusion-button-square button-large button-default button-1" value="Login" id="register" />
            </form>
	</div>
<?php
wp_footer();
 include( get_template_directory() . '/footer.php'); ?>