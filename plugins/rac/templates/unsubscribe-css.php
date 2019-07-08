<?php
/*
 * Front End CSS
 * 
 */
?>
<style type="text/css">
    p.un_sub_email_css {
        position: fixed;
        left: 0;
        right: 0;
        margin: 0;
        width: 100%;
        font-size: 1em;
        padding: 1em 0;
        text-align: center;
        background-color: #<?php echo get_option('rac_unsubscription_message_background_color') ?>;
        color: #<?php echo get_option('rac_unsubscription_message_text_color') ?>;
        z-index: 99998;
        a {
            color: 0 1px 1em rgba(0, 0, 0, 0.2);
        }
    }

    .admin-bar {
        p.un_sub_email_css {
            top: 32px;
        }
    }
    .unsubscribeContent {
        border: 1px solid #d6d4d4;
        border-radius: 10px;
        box-sizing: border-box;
        margin: 25px auto;
        padding: 45px 60px;
        text-align: center;
        width: 600px;
    }
    .mailSubscribe {
        border-bottom: 1px solid #dbdedf;
        font-size: 18px;
        line-height: 31px;
        margin-bottom: 30px;
        padding-bottom: 30px;
        color: #<?php echo get_option('rac_unsubscription_email_text_color') ?>;
    }
    .msgTitle {
        color: #<?php echo get_option('rac_confirm_unsubscription_text_color') ?>;
        display: inline-block;
        font-size: 36px;
        margin-bottom: 24px;
    }
    .unsubscribe_button {
        background-color: #FF0000;
        border: medium none;
        border-radius: 5px;
        color: #FFFFFF;
        display: inline-block;
        font-size: 25px;
        padding: 10px 24px;
        text-align: center;
        text-decoration: none;
    }
</style>