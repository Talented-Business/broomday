<?php
global $woocommerce;
?>
<style type="text/css">
    #image_uploader {
        color: blueviolet;
    }
    tbody>tr.rac_product_info_drag_n_drop  {
        border: 1px solid #ccc;
    }
    .postbox h3 {
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
        padding: 8px 12px;
    }
    .form-field label {
        display:table-row;
        font-weight:  bold;
        font-size:14px;

    }
    tbody>tr.rac_product_info_drag_n_drop {
        background:#fff;
        margin-bottom:10px;
        display: table-row-group;
        cursor:move;
    }
    table>tbody>tr.rac_product_info_drag_n_drop:hover {
        background:#dedec6;
    }
    <?php if ((float) $woocommerce->version >= (float) ('3.0')) { ?>
        widefat td, .widefat td ul{
            overflow: visible !important;
        }
        .chosen-container{
            border:1px solid #ddd;
        }
    <?php } ?>
</style>
<?php
