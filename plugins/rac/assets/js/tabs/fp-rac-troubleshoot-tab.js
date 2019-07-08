/* global fp_rac_troubleshoot_tab_obj, ajaxurl */

jQuery(function ($) {
    var Troubleshoot_Tab = {
        init: function () {

            this.get_troubleshoot_webmaster_option('#rac_webmaster_mail');

            $(document).on('change', '#rac_webmaster_mail', this.toggle_troubleshoot_webmaster_option);
            $(document).on('click', '#senttestmail', this.send_test_mail);
            $(document).on('click', '#rac-update-data', this.check_old_odrers);

            if (fp_rac_remaining_tabs_obj.rac_wc_version > parseFloat('2.2.0')) {
                this.get_troubleshoot_mail_function_option('#rac_trouble_mail');
                $(document).on('change', '#rac_trouble_mail', this.toggle_troubleshoot_mail_function_option);
            }
        },
        toggle_troubleshoot_mail_function_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Troubleshoot_Tab.get_troubleshoot_mail_function_option($this);
        },
        toggle_troubleshoot_webmaster_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Troubleshoot_Tab.get_troubleshoot_webmaster_option($this);
        },
        get_troubleshoot_webmaster_option: function ($this) {
            if ($($this).val() == 'webmaster1') {
                $('#rac_textarea_mail').closest('tr').show();
            } else {
                $('#rac_textarea_mail').closest('tr').hide();
            }
        },
        get_troubleshoot_mail_function_option: function ($this) {
            var troubleemail = $($this).val();
            $('.prependedrc').remove();
            if (troubleemail === 'mail') {
                $('#rac_trouble_mail').parent().append('<span class="prependedrc">' + fp_rac_troubleshoot_tab_obj.rac_email_function_msg + '</span>');
            }
        },
        send_test_mail: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            var data = {
                action: 'rac_send_test_mail',
                rac_test_mail_to: $('#testemailto').val(),
                rac_plain_or_html: $('#rac_test_mail_format').val(),
                rac_security: fp_rac_troubleshoot_tab_obj.test_email
            };
            var cur_button = $($this);
            $($this).prop("disabled", true);
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data
            }).done(function (response) {
                $('#test_mail_result').css('display', 'block');
                if (response == "sent") {
                    $('#test_mail_result').html(fp_rac_troubleshoot_tab_obj.rac_mail_success_message);
                } else {
                    $('#test_mail_result').html(fp_rac_troubleshoot_tab_obj.rac_mail_failure_message);
                }
                cur_button.prop('disabled', false);
            });
        },
        check_old_odrers: function (event) {
            event.preventDefault();
            var alert_result = confirm(fp_rac_troubleshoot_tab_obj.rac_alert_message);
            if (alert_result) {
                $('#rac_update_data_img').show();
                $("#rac-update-data").prop('disabled', true);
                var dataparam = ({
                    action: 'rac_get_old_order_data',
                    rac_order_value: $("#rac-update-data").val(),
                    rac_security: fp_rac_troubleshoot_tab_obj.update_data

                });

                $.post(ajaxurl, dataparam,
                        function (response) {
                            if (response !== 'success') {
                                var j = 1;
                                var i, j, temparray, chunk = parseFloat(fp_rac_troubleshoot_tab_obj.rac_chunk_count);
                                for (i = 0, j = response.length; i < j; i += chunk) {
                                    temparray = response.slice(i, i + chunk);
                                    Troubleshoot_Tab.get_update_old_orders(temparray);
                                }

                                $.when(Troubleshoot_Tab.get_update_old_orders('')).done(function (a1) {
                                    $('#rac-update-data').prop('disabled', false);
                                });

                            }
                        }, 'json');
            } else {
                return false;
            }
        },
        get_update_old_orders: function (id) {
            return $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: ({
                    action: 'rac_chunk_old_order_list',
                    ids: id,
                    rac_security: fp_rac_troubleshoot_tab_obj.update_data
                }),
                success: function (response) {
                    if (response == 'completed') {
                        $('#rac_update_data_img').hide();
                        $('#rac-update-data-msg').append(fp_rac_troubleshoot_tab_obj.rac_update_success_message);
                        setTimeout(function () {
                            location.reload()
                        }, '3500');
                    }
                },
                dataType: 'json',
                async: false
            });
        }
    };
    Troubleshoot_Tab.init();
});