<!DOCTYPE html>
<html lang="en-US" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#" style="height: 100%; position: relative;">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Broomday Panamá</title>
</head>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" class="kt-woo-wrap order-items-normal title-style-none" style="height: 100%; position: relative; background-color: #f7f7f7;">
    <div id="wrapper" dir="ltr" style="background-color: #f7f7f7; margin: 0; padding: 70px 0 70px 0; width: 100%; padding-top: 70px; padding-bottom: 70px; -webkit-text-size-adjust: none;">
        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
            <tr>
                <td align="center" valign="top">
                    <div id="template_header_image_container" style="background-color: transparent;">
                        <div id="template_header_image" style="width: 602px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header_image_table">
                                <tr>
                                    <td align="center" valign="middle" style="text-align: center; padding-top: 0px; padding-bottom: 0px;">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="background-color: #ffffff; overflow: hidden; border-style: solid; width: 602px; border-width: 1px; border-color: #dedede; border-radius: 3px; box-shadow: 0 1px 4px 1px rgba(0,0,0,0.1);">
                        <tr>
                            <td align="center" valign="top">
                                <!-- Header -->
                                <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header" style='background-color: #ffffff; color: #1e73be; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; width: 602px;'>
                                    <tr>
                                        <td id="header_wrapper" style="padding: 36px 48px; display: block; text-align: center; padding-top: 36px; padding-bottom: 36px; padding-left: 48px; padding-right: 48px;">
                                            <h1 style='color: #1e73be; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 400; line-height: 40px; margin: 0; text-align: center; font-style: normal;'><?php echo $email_subject ?></h1>
                                        </td>
                                    </tr>
                                </table>
                                <!-- End Header -->
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">
                                <!-- Body -->
                                <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body" style="width: 602px;">
                                    <tr>
                                        <td valign="top" id="body_content" style="background-color: #ffffff; padding-top: 0px; padding-bottom: 0px;">
                                            <!-- Content -->
                                            <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                <tr>
                                                    <td valign="top" style="padding: 0px 48px 48px;">
                                                        <div id="body_content_inner" style='color: #747474; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 24px; text-align: left; font-weight: 400;'>

                                                        <p>¡Hola <?=$billing_first_name?>!</p>
                                                        <p><?=$employee_first_name?> se ha asignado la orden/suscripción <?=$order_subscription?> que se llevará a cabo el <?=$order_date?> <?=$billing_address?>.</p>
                                                        <div>
                                                            <div style='display:inline-block;float: left;    margin-top: 23px; margin-right: 20px;'>
                                                                <img src='<?=$employee_avatar?>'>
                                                            </div>
                                                            <div style='display:inline-block;width:300px'>
                                                                <h3><?php echo $employee_first_name." ".$employee_last_name?></h3>
                                                                <table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #747474; border: 1px solid #e5e5e5; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border-collapse: collapse; border-color: #e4e4e4; border-width: 1px; border-style: solid; width: 100%;">
                                                                <tbody style="border-bottom-style: solid;">
                                                                    <tr class="order_item">
                                                                        <td class="td" style="color: #747474; border: 1px solid #e5e5e5; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding: 12px; border-color: #e4e4e4; border-width: 1px; border-style: solid; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                                            Identificación Personal</td>
                                                                        <td  class="td" style="color: #747474; border: 1px solid #e5e5e5; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding: 12px; border-color: #e4e4e4; border-width: 1px; border-style: solid; text-align: left; vertical-align: top;">
                                                                            <?=$employee?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Número de pedidos completados</td>
                                                                        <td><?=$order_count?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Calcificación media</td>
                                                                        <td><?=$average_rating?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Ubicación</td>
                                                                        <td><?=$employee_address?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            </div>
                                                        </div>
                                                        <p>
                                                        <?=$billing_first_name?>, te agradecemos calificar al profesional y dejar tus comentarios al culminar el servicio, este feedback es de gran ayuda para conocer tus necesidades y poder complacerlas. También recuerda que posterior al servicio podrás solicitar una rotación de personal de ser necesario, sin cargos adicionales.</p>
                                                        <p>Gracias por confiar en nosotros.</p>
                                                        <p>¡Nos vemos pronto!</p>

                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- End Content -->
                                        </td>
                                    </tr>
                                </table>
                                <!-- End Body -->
                            </td>
                        </tr>
                    </table>
                    <!-- End template container -->
                </td>
            </tr>
        </table>
    </div>
</body>

</html>