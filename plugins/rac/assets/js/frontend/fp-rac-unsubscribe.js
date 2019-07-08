/* global fp_unsubscribe_obj */

jQuery(function ($) {

    var Unsubscribe = {
        init: function () {
            $(document).on('change', '#fp_rac_unsubscribe_option', this.toggle_unsubscribe_email);
        },
        toggle_unsubscribe_email: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            var dataclicked = $($this).is(':checked') ? 'false' : 'true';
            var data = {
                action: 'fp_rac_undo_unsubscribe',
                getcurrentuser: fp_unsubscribe_obj.rac_current_userid,
                dataclicked: dataclicked,
                rac_security: fp_unsubscribe_obj.rac_unsubcribe_nonce
            };
            $.post(fp_unsubscribe_obj.rac_admin_url, data,
                    function (response) {
                        response = response.replace(/\s/g, '');
                        $("p.un_sub_email_css").hide();
                        if (response === '1') {
                            alert(fp_unsubscribe_obj.rac_unsub_message);
                        } else {
                            alert(fp_unsubscribe_obj.rac_sub_message);
                        }
                    });
        }

    };
    Unsubscribe.init();
});