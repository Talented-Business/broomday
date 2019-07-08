/* global fp_rac_previous_order_tab_obj, ajaxurl */

jQuery(function ($) {

    var previous_count;
    var rac_order_status = Array();

    var Previous_Orders_Tab = {
        init: function () {
            this.trigger_on_page_load();

            $(document).on('change', '#order_time', this.toggle_time_format_selection);
            $(document).on('click', '#update_order', this.check_privous_odrers);
        },
        trigger_on_page_load: function () {
            $('#specific_row').css('display', 'none');
        },
        toggle_time_format_selection: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if ($($this).val() == 'specific') {
                $('#specific_row').css('display', 'table-row');
            } else {
                $('#specific_row').css('display', 'none');
            }
        },
        check_privous_odrers: function (event) {
            event.preventDefault();
            $('.perloader_image').show();
            $("#update_order").prop('disabled', true);
            $('input[name="order_status[]"]:checked').each(function (index) {
                rac_order_status.push($(this).val());
            });
            var mycount;
            var order_time = $('#order_time').val();
            var from_time = $('#from_time').val();
            var to_time = $('#to_time').val();

            var dataparam = ({
                action: 'rac_add_old_order',
                rac_order_time: order_time,
                rac_from_time: from_time,
                rac_to_time: to_time,
                rac_order_status: rac_order_status,
                rac_security: fp_rac_previous_order_tab_obj.oldorder_cartlist

            });

            $.post(ajaxurl, dataparam,
                    function (response) {
                        if (response !== 'success') {
                            var j = 1;
                            var i, j, temparray, chunk = parseFloat(fp_rac_previous_order_tab_obj.rac_chunk_count);
                            for (i = 0, j = response.length; i < j; i += chunk) {
                                temparray = response.slice(i, i + chunk);
                                Previous_Orders_Tab.get_data_form_orders(temparray);
                            }

                            $.when(Previous_Orders_Tab.get_data_form_orders('')).done(function (a1) {
                                $('#update_order').prop('disabled', false);
                            });
                        } else {
                            var newresponse = response.replace(/\s/g, '');
                            if (newresponse === 'success') {
                                $('.submit .button-primary').trigger('click');
                            }
                        }
                    }, 'json');
        },
        get_data_form_orders: function (id) {
            return $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: ({
                    action: 'rac_chunk_previous_order_list',
                    ids: id,
                    rac_order_status: rac_order_status,
                    rac_security: fp_rac_previous_order_tab_obj.oldorder_cartlist
                }),
                success: function (response) {
                    if (response) {
                        previous_count = response.count;
                        $('.perloader_image').hide();
                        if (previous_count > 0) {
                            $('#update_response').append(previous_count + ' ' + fp_rac_previous_order_tab_obj.rac_updated_count);
                            setTimeout(function () {
                                location.reload()
                            }, '3500');
                        } else {
                            $('#update_response').append(fp_rac_previous_order_tab_obj.rac_empty_order_message);
                            setTimeout(function () {
                                location.reload()
                            }, '3500');
                        }
                    }
                },
                dataType: 'json',
                async: false
            });
        }
    };
    Previous_Orders_Tab.init();
});