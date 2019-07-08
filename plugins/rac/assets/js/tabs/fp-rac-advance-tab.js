
jQuery(function ($) {

    var Advance_Tab = {
        init: function () {

            this.trigger_on_page_load();
            $(document).on('click', '#fp_rac_email_cartlink_logo_button', this.cartlink_uploader_open);

            $(document).on('change', '#rac_cart_link_options', this.toggle_email_cart_link_option);
            $(document).on('change', '#rac_hide_product_name_product_info_shortcode', this.toggle_product_name_option);
            $(document).on('change', '#rac_hide_product_image_product_info_shortcode', this.toggle_product_image_option);
            $(document).on('change', '#rac_hide_product_quantity_product_info_shortcode', this.toggle_product_quantity_option);
            $(document).on('change', '#rac_hide_product_price_product_info_shortcode', this.toggle_product_price_option);
            $(document).on('change', '#rac_unsub_myaccount_option', this.toggle_unsubscription_option);
            $(document).on('change', '#rac_enable_guest_add_to_cart_popup', this.toggle_guest_popup_option);
            $(document).on('change', '#rac_show_hide_name_in_popup', this.toggle_guest_popup_name_option);
            $(document).on('change', '#rac_show_hide_contactno_in_popup', this.toggle_guest_popup_contactno_option);
            $(document).on('change', '#rac_hide_tax_row_product_info_shortcode', this.toggle_product_info_tax_option);
            $(document).on('change', '#rac_hide_shipping_row_product_info_shortcode', this.toggle_product_info_shipping_option);
            $(document).on('change', 'input[name="rac_unsubscription_type"]:radio', this.toggle_email_unsubcribe_type_option);
            $(document).on('change', '#rac_hide_tax_total_product_info_shortcode', this.toggle_hide_product_info_total_option);
            $('table#rac_drag_n_drop_product_info').sortable({
                axis: "y",
                items: 'tbody',
                update: function (event, ui) {
                    var data = $(this).sortable("toArray");
                    $.ajax({
                        data: ({
                            action: 'rac_drag_n_drop_product_info_column',
                            data: data,
                        }),
                        type: 'POST',
                        url: ajaxurl,
                        success: function (response) {
                            console.log(response);
                        },
                    });
                }
            });
        },
        trigger_on_page_load: function () {
            this.get_email_cart_link_option('#rac_cart_link_options');
            this.get_product_name_option('#rac_hide_product_name_product_info_shortcode');
            this.get_product_image_option('#rac_hide_product_image_product_info_shortcode');
            this.get_product_quantity_option('#rac_hide_product_quantity_product_info_shortcode');
            this.get_product_price_option('#rac_hide_product_price_product_info_shortcode');
            this.get_unsubscription_option('#rac_unsub_myaccount_option');
            this.get_guest_popup_option('#rac_enable_guest_add_to_cart_popup');
            this.get_guest_popup_name_option('#rac_show_hide_name_in_popup');
            this.get_guest_popup_contactno_option('#rac_show_hide_contactno_in_popup');
            this.get_email_unsubcribe_type_option("input[name='rac_unsubscription_type']:checked");
            this.get_hide_product_info_total_option('#rac_hide_tax_total_product_info_shortcode');
        },
        cartlink_uploader_open: function (e) {
            e.preventDefault();

            var rac_cartlink_logo_uploader;

            rac_cartlink_logo_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {text: 'Choose Image'
                },
                multiple: false
            });
            //When a file is selected, grab the URL and set it as the text field's value
            rac_cartlink_logo_uploader.on('select', function () {
                attachment = rac_cartlink_logo_uploader.state().get('selection').first().toJSON();
                jQuery('#fp_rac_email_cartlink_logo_text').val(attachment.url);

            });
            //Open the uploader dialog
            rac_cartlink_logo_uploader.open();
        },
        toggle_email_cart_link_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_email_cart_link_option($this);
        },
        toggle_product_name_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_product_name_option($this);
        },
        toggle_product_image_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_product_image_option($this);
        },
        toggle_product_quantity_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_product_quantity_option($this);
        },
        toggle_product_price_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_product_price_option($this);
        },
        toggle_unsubscription_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_unsubscription_option($this);
        },
        toggle_guest_popup_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_guest_popup_option($this);
        },
        toggle_guest_popup_name_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_guest_popup_name_option($this);
        },
        toggle_guest_popup_contactno_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_guest_popup_contactno_option($this);
        },
        toggle_email_unsubcribe_type_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_email_unsubcribe_type_option($this);
        },
        toggle_hide_product_info_total_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_hide_product_info_total_option($this);
        },
        toggle_product_info_tax_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_hide_product_info_tax_option($this.val());
        },
        toggle_product_info_shipping_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Advance_Tab.get_hide_product_info_shipping_option($this.val());
        },
        get_email_cart_link_option: function ($this) {
            if ($($this).val() === '4') {
                $('.racbutton').closest('tr').hide();
                $('.raclink').closest('tr').hide();
                $('.fp_rac_class_cartlink_image').closest('tr').show();
            } else if ($($this).val() === '3') {
                $('.racbutton').closest('tr').show();
                $('.raclink').closest('tr').hide();
                $('.fp_rac_class_cartlink_image').closest('tr').hide();
            } else if ($($this).val() === '2') {
                $('.raclink').closest('tr').hide();
                $('.racbutton').closest('tr').hide();
                $('.fp_rac_class_cartlink_image').closest('tr').hide();
            } else {
                $('.racbutton').closest('tr').hide();
                $('.raclink').closest('tr').show();
                $('.fp_rac_class_cartlink_image').closest('tr').hide();
            }
        },
        get_email_unsubcribe_type_option: function ($this) {
            var option = $($this).val();
            if (option == '1') {
                $('.rac_unsub_auto').closest('tr').show();
                $('.rac_unsub_manual').closest('tr').hide();
            } else {
                $('.rac_unsub_auto').closest('tr').hide();
                $('.rac_unsub_manual').closest('tr').show();
            }
        },
        get_product_name_option: function ($this) {
            var option = $($this).val();
            if (option == 'yes') {
                $('#rac_product_info_product_name').closest('tr').hide();
            } else {
                $('#rac_product_info_product_name').closest('tr').show();
            }
        },
        get_product_image_option: function ($this) {
            var option = $($this).val();
            if (option == 'yes') {
                $('#rac_product_info_product_image').closest('tr').hide();
            } else {
                $('#rac_product_info_product_image').closest('tr').show();
            }
        },
        get_product_quantity_option: function ($this) {
            var option = $($this).val();
            if (option == 'yes') {
                $('#rac_product_info_quantity').closest('tr').hide();
            } else {
                $('#rac_product_info_quantity').closest('tr').show();
            }
        },
        get_product_price_option: function ($this) {
            var option = $($this).val();
            if (option == 'yes') {
                $('#rac_product_info_product_price').closest('tr').hide();
            } else {
                $('#rac_product_info_product_price').closest('tr').show();
            }
        },
        get_unsubscription_option: function ($this) {
            var option = $($this).is(":checked");
            if (option == true) {
                $('.rac_unsubscribe_hide').closest('tr').show();
            } else {
                $('.rac_unsubscribe_hide').closest('tr').hide();
            }
        },
        get_guest_popup_option: function ($this) {
            var option = $($this).is(":checked");
            if (option == true) {
                $('.rac_show_hide_settings_for_guest_popup').closest('tr').show();
            } else {
                $('.rac_show_hide_settings_for_guest_popup').closest('tr').hide();
            }
        },
        get_guest_popup_name_option: function ($this) {
            var option = $($this).val();
            if (option == '1') {
                $('#rac_force_guest_to_enter_first_last_name').closest('tr').hide();
            } else {
                $('#rac_force_guest_to_enter_first_last_name').closest('tr').show();
            }
        },
        get_guest_popup_contactno_option: function ($this) {
            var option = $($this).val();
            if (option == '1') {
                $('#rac_force_guest_to_enter_phoneno').closest('tr').hide();
            } else {
                $('#rac_force_guest_to_enter_phoneno').closest('tr').show();
            }
        },
        get_hide_product_info_total_option: function ($this) {
            var product_info_rows_sh_option = $($this).val();
            if (product_info_rows_sh_option == 'yes') {
                $('.rac_hide_total_info').closest('tr').hide();
                Advance_Tab.get_hide_product_info_shipping_option('yes');
                Advance_Tab.get_hide_product_info_tax_option('yes');
            } else {
                $('.rac_hide_total_info').closest('tr').show();
                Advance_Tab.get_hide_product_info_shipping_option($('#rac_hide_shipping_row_product_info_shortcode').val());
                Advance_Tab.get_hide_product_info_tax_option($('#rac_hide_tax_row_product_info_shortcode').val());
            }
        },
        get_hide_product_info_shipping_option: function ($val) {
            if ($val == 'yes') {
                $('#rac_product_info_shipping').closest('tr').hide();
            } else {
                $('#rac_product_info_shipping').closest('tr').show();
            }
        },
        get_hide_product_info_tax_option: function ($val) {
            if ($val == 'yes') {
                $('#rac_product_info_tax').closest('tr').hide();
            } else {
                $('#rac_product_info_tax').closest('tr').show();
            }
        },

    };
    Advance_Tab.init();
});
