
jQuery(function ($) {
    var Email_Settings = {
        init: function () {

            this.trigger_on_page_load();

            $(document).on('click', '.upload_button', this.uploader_open);

            $(document).on('change', '.rac_sender_opt:checked', this.toggle_sender_option);
            $(document).on('change', '#rac_template_seg_type', this.toggle_segmentation);
            $(document).on('change', '.rac_template_mail', this.toggle_email_template_type);
            $(document).on('change', '#rac_template_coupon_mode', this.toggle_template_coupon);
            $(document).on('change', '.rac_template_coupon_prefix_type', this.toggle_template_coupon_prefix_type);
            $(document).on('change', '.rac_template_seg_cart_product_category', this.toggle_segmentation_product_in_cart_option);

        },
        trigger_on_page_load: function () {
            this.get_sender_option('.rac_sender_opt:checked');
            this.get_template_type_option('.rac_template_mail');
            this.get_segmentation_option('#rac_template_seg_type');
            this.get_template_coupon('#rac_template_coupon_mode');
            $("span#rac_duration").html($('#rac_duration_type').val());
        },
        toggle_segmentation: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Email_Settings.get_segmentation_option($this);
        },
        toggle_template_coupon: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Email_Settings.get_template_coupon($this);
        },
        toggle_template_coupon_prefix_type: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Email_Settings.get_template_coupon_prefix_type($this);
        },
        toggle_sender_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Email_Settings.get_sender_option($this);
        },
        toggle_email_template_type: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Email_Settings.get_template_type_option($this);
        },
        toggle_segmentation_product_in_cart_option: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Email_Settings.get_segmentation_product_in_cart_option($this)
        },
        uploader_open: function (e) {
            e.preventDefault();
            var uploader_open;
            if (uploader_open) {
                uploader_open.open();
                return;
            }

            uploader_open = wp.media.frames.uploader_open = wp.media({
                title: 'Media Uploader',
                button: {
                    text: 'Media Uploader'
                },
                multiple: false
            });
            //When a file is selected, grab the URL and set it as the text field's value
            uploader_open.on('select', function () {
                attachment = uploader_open.state().get('selection').first().toJSON();
                $('#rac_template_link').val(attachment.url);
            });
            uploader_open.open();
            //mail logo upload
        },
        get_sender_option: function ($this) {
            var value = $($this).val();
            if (value == 'woo') {
                $('.rac_local_senders').css('display', 'none');
            } else {
                $('.rac_local_senders').css('display', 'table-row');
            }
        },
        get_template_coupon: function ($this) {
            if ($($this).val() == 'global') {
                $('.rac_template_coupon').hide();
            } else {
                $('.rac_template_coupon').show();
                Email_Settings.get_template_coupon_prefix_type(".rac_template_coupon_prefix_type");
            }
        },
        get_template_coupon_prefix_type: function ($this) {
            if ($($this).val() == '1') {
                $('.rac_template_coupon_prefix').closest('tr').hide();
            } else {
                $('.rac_template_coupon_prefix').closest('tr').show();
            }
        },
        get_segmentation_option: function ($this) {
            var current_seg_type = $($this).val();
            $('.rac_colsh').hide();
            $('.' + current_seg_type).show();

            if (current_seg_type == 'rac_template_seg_cart_product') {
                Email_Settings.get_segmentation_product_in_cart_option('.rac_template_seg_cart_product_category');
            }
        },
        get_segmentation_product_in_cart_option: function ($this) {
            var current_product_selection = $($this).val();
            $('.rac_col_product_sh').hide();
            $('.rac_' + current_product_selection).show();
        },
        get_template_type_option: function ($this) {
            var currentplainhtml = $($this).val();
            if (currentplainhtml === 'PLAIN') {
                $('.rac_logo_link').show();
            } else {
                $('.rac_logo_link').hide();
            }
        },
    };

    Email_Settings.init();
});