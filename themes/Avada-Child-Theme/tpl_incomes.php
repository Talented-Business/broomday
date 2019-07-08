<?php
/* Template Name: ingresos */
ob_start();

if (is_user_logged_in()) {

} elseif (!is_user_logged_in()) {
    wp_redirect(site_url());
}
get_header();
?>
<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
    tr{
        line-height: 10px !important;
    }
    td{
        line-height: 20px;
    }
    td, th {
        /*        border: 1px solid #dddddd;*/
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        /*        background-color: #dddddd;*/
    }
    .customer-table{
        width: 500px;
        height: 470px;
        background: #fff;
        padding:20px 15px;
        box-shadow: 2px 2px 6px 1px #ddd;
        border-radius: 5px;
        color: #888;
        float:left;
        margin: 20px;
        padding-bottom:20px;
    }
    .customer{
        width: 75%;
        float: left;
        padding-left: 6px;
    }
    .hours{
        width: 25%;
        float: left;
    }
    .customer-table p {
        font-size: 18px;
        font-weight: bold;
        margin: 5px 0;
        color: #3CADE0;
    }
    .table-right {
        padding-left: 10px;
    }

    .common-phone p {
        margin-top: 0;
        margin-bottom: 0;
    }

     .common-phone {
        padding-top: 0;
        padding-bottom: 0;
    }

    .assign{
        background: #3CADE0 ;
        color: #fff;
        border: 0;
        padding: 10px 80px;
        border-radius: 5px;
        font-size: 18px;
        /*float: right;*/
        margin-top: -10px !important;
        margin-left: 27%;
        box-shadow: 2px 2px 4px 1px #ddd;
    }
    .address_box {
        height: 50px !important;
        overflow: hidden;
        min-height: 50px !important;
        line-height: 20px !important;
    }
    @media only screen and (min-device-width: 769px) and (max-device-width: 1024px) {
        .customer-table {
            width: calc(50% - 5px);
            height: auto;
            background: #fff;
            padding: 20px 10px;
            box-shadow: 2px 2px 6px 1px #ddd;
            border-radius: 5px;
            color: #888;
            float: left;
            margin: 0;
            margin-right: 5px;
            margin-bottom: 15px;
        }
    }
    @media only screen and (max-device-width: 768px) {
        .customer-table {
            width: calc(100%);
            margin: 0;
            margin-bottom: 15px;
        }
        #main {
            padding-top: 30px !important;
            padding-bottom: 30px !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
    }
    @media all and (max-device-width: 320px) {
        .table-right {
            padding-left: 5px;
        }
        td, th {
            padding: 5px;
        }
    }
    li span.page-numbers.current {
        padding-top: 6px;
        padding-bottom: 6px;
        padding-left: 11px;
        padding-right: 11px;
        background-color: rgb(108,171,221);
        color:#ffffff;
    }
    li a.page-numbers {
        background-color: #eee;
        padding-left: 11px;
        padding-right: 11px;
        padding-top: 6px;
        padding-bottom: 6px;
    }
    ul.page-numbers li {
        display: inline-block;
        vertical-align: top;
        float: left;
        padding-left: 1%;
    }
    .assigned_paginattion{
        width: 100% !important;
        float: left !important;
    }
    .jobs_table2{
        width:26%;
    }

.phone{
 font-size:16px !important;

font-family: "Cabin";
}
.phone_no{
    float: left;
    font-size: 16px !important;
    padding-top: 5px;
font-family: "Cabin";
}

.jobs_table2.common-phone {
  padding-top: 10px;
}

.table-right.common-phone {
  padding-top: 5px;
}

@media all and (max-device-width: 800px)  {
.assign{
    margin-top: 10px;
    float:right;
 }
}

@media all and (max-device-width: 980px)  {
.assign{
    margin-top: 10px;
    float:right;
 }
}

@media all and (max-device-width: 768px)  {
.assign{
    margin-top: 10px;
    float:right;
 }
}

@media all and (max-device-width: 320px)  {
.assign{
    margin-top: 10px;
    float:right;
 }
}
button.btn.btn-default {
    padding: 10px 20px;
    border: navajowhite;
    border-radius: 5px;
    cursor:pointer;
}

#example12_filter {

    display: none;

}

</style>
<?php
    $uuid = $user_id = get_current_user_id();
   // echo $uuid;
    $user_info = get_userdata($user_id);
    $user_services = unserialize(get_user_meta($user_id, "user_services", true));
    // $uuid = $_GET['user_id'];
        // echo $uuid;
     /*   $getpayments = $wpdb->get_results("SELECT FROM_DAYS(TO_DAYS(`date`) -MOD(TO_DAYS(`date`) -1, 7)) AS week_beginning, SUM(`paid_payment`) AS total, id,user_id,  COUNT(*) AS total_count FROM tbluserpayment WHERE user_id = 48 GROUP BY FROM_DAYS(TO_DAYS(`date`) -MOD(TO_DAYS(`date`) -1, 7)) ORDER BY FROM_DAYS(TO_DAYS(`date`) -MOD(TO_DAYS(`date`) -1, 7))");*/
     $getpayments = $wpdb->get_results("SELECT FROM_DAYS(TO_DAYS(p.post_date) - MOD(TO_DAYS(p.post_date) -1, 7)) AS week_beginning, SUM(m1.meta_value) AS total, id, post_bono_deleted, COUNT(*) AS total_count FROM wpstg2_posts p, wpstg2_postmeta m1, wpstg2_postmeta m2
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
        <h3 style="text-transform: uppercase;margin-top:0px;margin-bottom:10px; width: fit-content; float: left;">ingresos</h3>
        <span style="position: relative; margin: 0px;top: 30px;float: right; background-color: #3cade0; color: white; padding: 5px 10px;" id = "total-bono">Bono = $100</span>
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
            $total_bono = 0;
            if (!empty($getpayments)) {
                foreach ($getpayments as $payment) {
                        $ds = $payment->week_beginning;
                        $de = date('Y-m-d',strtotime($payment->week_beginning.'+6 days'));
                        $date_start = date('M d',strtotime($payment->week_beginning));
                        $date_end = date('M d',strtotime(date('Y-m-d',strtotime($payment->week_beginning.'+6 days'))));
                    ?>
                   <tr class="tr">
                        <td class="td"><?php echo $date_start.' - '.$date_end ;?></td>
                        <td class="td">$<?php echo number_format($payment->total,2) ?></td>

                        <?php
                        $bono = 0;
                        if($payment -> post_bono_deleted){
                        ?>
                        <td class="td"><?php echo "--"; ?></td>
                        <?php
                        }
                        else {
                            $bono = $payment->total * 0.06;
                        ?>
                        <td class="td">$<?php echo number_format($payment->total,2) * 0.06; ?></td>
                        <?php
                        }

                        $total_bono += $bono;
                        ?>
                        <td class="td"> $<?php echo number_format(getFees($payment->total),2) ?> <a href="javascript:void();" data-toggle="modal" data-target="#myModal1" onclick="showFeesDetail(<?=getFees($payment->total)?>)"> (ver)</a></td>
                        <td class="td">$<?=number_format(($payment->total - getFees($payment->total) - $bono ),2);?></td>
                        <td class="td"><?=$payment->total_count; ?> <a class="tr-cls" href="javascript:void();" data-toggle="modal" data-target="#myModal2" onclick="fetch('<?=$ds?>','<?=$de?>')"> (ver)</a></td>
                    </tr>
                    <?php
                }
            }


            ?>
            </tbody>
        </table>
        <script type="text/javascript">


            var abc = document.getElementById('cst11').classList.add('current-menu-item')
        </script>
        <div class="modal fade" id="myModal1" role="dialog">
            <div class="modal-dialog modal-lg">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Servicios Broomday</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-stripe">
                          <thead>
                            <tr>
                                <th>Descripción</th>
                                <!--<th>Tarifa</th>-->
                                <th>Total</th>
                            </tr>
                          </thead>
                          <tbody class="table">
                              <tr>
                                  <td>Sistema de tarjetas de crédito (Credicorp Bank)</td>
                                  <!--<td>25.33% x <span class="siseme_amount"></span> </td>-->
                                  <td id="tdr1"></td>
                              </tr>
                              <tr>
                                  <td>Seguro de responsabilidad civil</td>
                                  <!--<td>16.11% x <span class="siseme_amount"></span></td>-->
                                  <td id="tdr2"></td>
                              </tr>
                              <tr>
                                  <td>Servicio al cliente 24/7</td>
                                  <!--<td>13.43% x <span class="siseme_amount"></span></td>-->
                                  <td id="tdr3"></td>
                              </tr>
                              <tr>
                                  <td>Marketing en Redes Sociales</td>
                                  <!--<td>20.11% x <span class="siseme_amount"></span></td>-->
                                  <td id="tdr4"></td>
                              </tr>
                              <tr>
                                  <td>Servicio y Mantenimiento de Plataforma</td>
                                  <!--<td>15.87% x <span class="siseme_amount"></span></td>-->
                                  <td id="tdr5"></td>
                              </tr>
                              <tr>
                                  <td>Adquisición clientes para profesionales</td>
                                  <!--<td>9.15% x <span class="siseme_amount"></span></td>-->
                                  <td id="tdr6"></td>
                              </tr>
                              <tr>
                                  <td>Servicio Broomday</td>
                                  <!--<td>0.00% x <span class="siseme_amount"></span></td>-->
                                  <td id="tdr7"></td>
                              </tr>
                              <tr>
                                  
                                  <td align="right"><strong>Total</strong></td>
<!--                                  <td></td>-->
                                  <td id="tdr_total"></td>
                              </tr>
                          </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
              
            </div>
        </div>

        <div class="modal fade" id="myModal2" role="dialog">
            <div class="modal-dialog modal-lg">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" >Ordenes <span id="md-change-title"></span></h4>
                </div>
                <div class="modal-body">
                  <table class="table table-stripe">
                          <thead>
                            <tr>
                                <th>ID de Orden</th>
                                <th>Total</th>
                                <th>Horas Contratadas</th>
                                <th>Fecha Completada</th>
                            </tr>
                          </thead>
                          <tbody id="countable_rows">
                            
                          </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
              
            </div>
        </div>
        <script type="text/javascript">

            

            function showFeesDetail(amount){
                var am_divs = document.getElementsByClassName('siseme_amount');
                for(let dv of am_divs){
                    dv.innerHTML = '$'+parseFloat(amount).toFixed(2)
                }
                const rates = [0.2533,0.1611,0.1343,0.2011,0.1587,0.0915,0.0];
                for(let i=0;i<rates.length;i++){
                    document.getElementById('tdr'+(i+1)).innerHTML = '$'+(parseFloat(amount)*rates[i]).toFixed(2);
                }
                document.getElementById('tdr_total').innerHTML = '<strong>$'+parseFloat(amount).toFixed(2)+'</strong>';
            }
           
        </script>
    <script>
    jQuery(function(){

	jQuery(document).on('click','.tr-cls', function(){
		console.log(jQuery(this).parent().siblings(":first").text());
		jQuery('#md-change-title').html(jQuery(this).parent().siblings(":first").text());
	})
	jQuery("#total-bono").text("Bono = $" + <?=number_format($total_bono, 2)?>);

	
    });
</script>
<?php
wp_footer();
 include( get_template_directory() . '/footer.php'); ?>
 
<?php //get_footer(); ?>