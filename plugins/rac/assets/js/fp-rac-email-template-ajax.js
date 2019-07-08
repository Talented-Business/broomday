/* global fp_email_template_ajax_obj, ajaxurl, fp_rac_remaining_tabs_obj */

jQuery(function ($) {
    var Email_Template_Ajax = {
        init: function () {
            $(document).on('change', '#rac_load_mail', this.load_email_template);
            $(document).on('click', '#rac_send_cart_list', this.send_email_template);
            $(document).on('click', '#rac_send_template_preview', this.send_email_template);
            $(document).on('click', '.rac_mail_active', this.change_email_template_status);
        },
        load_email_template: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            var new_post_id = $($this).val();
            var old_post_id = $('#post_ID').val();
            var url = location.href.replace("post=" + old_post_id, "post=" + new_post_id);
            window.location.href = url;
        },
        send_email_template: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            var sendby = jQuery($this).data('sendby');
            var bool = (sendby == 'test_mail') ? Email_Template_Ajax.validate_email() : true;
            var rac_mail_row_ids = (sendby == 'test_mail') ? '' : $('#rac_cart_row_ids').val();
            if (bool) {
                $('div#rac_hide_block').block({message: null});
                var j = 1;
                var i, j, temparray, chunk = parseInt(fp_rac_remaining_tabs_obj.rac_chunk_count);
                var chunkarray = rac_mail_row_ids.split(',');
                for (i = 0, j = chunkarray.length; i < j; i += chunk) {
                    temparray = chunkarray.slice(i, i + chunk);
                    var data = $('#post').serialize();
                    var content = Email_Template_Ajax.get_tinymce_content();
                    if (sendby == 'test_mail') {
                        var email = $('.rac_send_test_email_for_this_template').val();
                        data += "&action=rac_send_template_preview_email&rac_to=" + encodeURIComponent(email) + "&rac_content=" + encodeURIComponent(content) + "&rac_security=" + encodeURIComponent(fp_email_template_ajax_obj.manual_send_email_template);
                    } else {
                        data += "&action=rac_manual_mail_ajax&rac_email_row_ids=" + encodeURIComponent(temparray) + "&rac_content=" + encodeURIComponent(content) + "&rac_security=" + encodeURIComponent(fp_email_template_ajax_obj.manual_send_email_template);
                    }
                    var obj = $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data,
                        dataType: 'json',
                        complete: function () {
                            $('div#rac_hide_block').unblock();
                        },
                    });
                }
            }
            if (sendby == 'test_mail') {
                $("#rac_test_mail_sent").css('display', 'block');
                $(".rac_hide_this_message").css('display', 'none');
            } else {
                $('span#rac_mail_result').css('display', 'block');
            }

        }, get_tinymce_content: function () {
            if ($('#wp-rac_email_template_timce-wrap').hasClass('tmce-active')) {
                //rac_email_template_new
                return tinyMCE.get('rac_email_template_timce').getContent();
            } else {
                return $("#rac_email_template_timce").val();
            }
        },
        validate_email: function () {
            var rac_to_email = $('.rac_send_test_email_for_this_template').val();
            var atpos = rac_to_email.indexOf("@");
            var dotpos = rac_to_email.lastIndexOf(".");
            if (rac_to_email !== '') {
                if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= rac_to_email.length) {
                    alert(fp_email_template_ajax_obj.rac_valid_email_id_msg);
                    return false;
                }
            } else {
                alert(fp_email_template_ajax_obj.rac_valid_email_id_msg);
                return false;
            }
            return rac_to_email;
        },
        change_email_template_status: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            var post_id = jQuery($this).data('racmailid');
            var obj = jQuery($this);
            jQuery(obj).css('display', 'none');
            jQuery('#rac_load_image' + post_id).css('display', 'block');
            var status = jQuery($this).data('currentstate');
            var data = {
                row_id: post_id,
                status: status,
                action: 'rac_email_template_status',
                rac_security: fp_email_template_ajax_obj.email_template_status
            }
            jQuery.ajax({type: 'POST',
                url: ajaxurl,
                data: data}).done(function (res) {
                window.location.reload();
            });
        }
    };
    Email_Template_Ajax.init();
});