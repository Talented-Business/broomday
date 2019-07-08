<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('FP_Updating_Process')) {

    /**
     * FP_RAC_Updating_Process Class.
     */
    class FP_Updating_Process {

        public $progress_batch;
        public $identifier = 'fp_progress_ui';

        public function __construct() {
            $this->progress_batch = (int) get_site_option('fp_background_process_' . $this->identifier . '_progress', 0);

            add_action('wp_ajax_fp_progress_bar_status', array($this, 'fp_updating_status'));
        }

        /*
         * Get Updated Details using ajax
         * 
         */

        public function fp_updating_status() {
            $percent = (int) get_site_option('fp_background_process_' . $this->identifier . '_progress', 0);
            echo json_encode(array($percent));
            exit();
        }

        public function fp_delete_option() {
            delete_site_option('fp_background_process_' . $this->identifier . '_progress');
        }

        public function fp_increase_progress($progress = 0) {
            update_site_option('fp_background_process_' . $this->identifier . '_progress', $progress);
        }

        /*
         * Get Updated Details using ajax
         * 
         */

        public function fp_display_progress_bar() {
            $percent = $this->progress_batch;
            $url = add_query_arg(array('page' => 'recover-abandoned-cart-welcome-page'), admin_url('admin.php'));
            ?>

            <style type="text/css">
                .fp_prograssbar_wrapper{
                    width:500px;
                    margin:20% auto;
                }
                .fp_outer{
                    height: 20px;
                    width: 500px;
                    background:#d5d4d3;
                    box-shadow:0 1px 2px rgba(0, 0, 0, 0.1) inset;
                    border-radius:50px;
                }
                .fp_inner{
                    height: 20px;
                    width: <?php echo $percent; ?>%;
                    background:#5cb85c;
                    border-radius:50px;
                }
            </style>
            <div class="fp_prograssbar_wrapper">
                <h1>Recover Abandoned Cart</h1>
                <div id="fp_uprade_label">
                    <h3 style="font-weight:normal">Upgrade to v<?php echo RAC_VERSION; ?> is under Process...</h3>
                </div>
                <div class = "fp_outer">
                    <div class = "fp_inner fp-progress-bar">

                    </div>
                </div>
                <div id="fp_progress_status">
                    <span id = "fp_currrent_status"><?php echo $percent; ?> </span>% Completed
                </div>
            </div>
            <script type = "text/javascript">
                jQuery(document).ready(function ($) {
                    rac_prepare_progress_bar();
                    function rac_prepare_progress_bar() {
                        var data = {
                            action: 'fp_progress_bar_status',
                        };
                        $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: data,
                            dataType: 'json',
                        }).done(function ($response) {
                            if ($response < 100) {
                                $('#fp_currrent_status').html($response);
                                $('.fp-progress-bar').css("width", $response + "%");
                                rac_prepare_progress_bar();
                            } else {
                                $('#fp_uprade_label').css("display", "none");
                                $('.fp-progress-bar').css("width", "100%");
                                $('#fp_progress_status').html("<h4>Upgrade to v<?php echo RAC_VERSION; ?> Completed Successfully.</h4>");
                                window.location.href = '<?php echo $url; ?>';
                            }
                        });
                    }
                });
            </script>
            <?php
        }

    }

}