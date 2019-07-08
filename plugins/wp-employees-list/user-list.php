<?php

add_action ('init', function(){

   // If we're not in back-end we didn't expect to load these things

   if( ! is_admin() ){

       require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
       require_once( ABSPATH . 'wp-admin/includes/screen.php' );
       require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
       require_once( ABSPATH . 'wp-admin/includes/template.php' );

       global $myListTable;
       $myListTable = new My_Example_List_Table();
   }
});


function my_render_list_page(){

    global $myListTable;

    echo '</pre><div class="wrap"><h2>My List Table Test</h2>';
    $myListTable->prepare_items();

    ?>
    <form method="post">
        <input type="hidden" name="page" value="ttest_list_table">
    <?php

        $myListTable->search_box( 'search', 'search_id' );
        $myListTable->display();

    echo '</form></div>';
}
