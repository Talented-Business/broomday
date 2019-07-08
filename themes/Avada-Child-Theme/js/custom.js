window.mobilecheck = function() {
    var check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
};
jQuery(document).ready(function(){
    jQuery('.buttonc').click(function(){
        alert('sdfsdasdf');
    });
    var $sticky = jQuery('.checkout-sidebar>:first-child');
    var $stickyrStopper = jQuery('.fusion-footer');
    var stickOffset = 10;
    var windowTop,stickyTop,diff,stopPoint,generalSidebarHeight;
    if (!!$sticky.offset()&&jQuery(window).innerWidth()>1182) { // make sure ".sticky" element exists
  
      generalSidebarHeight = $sticky.innerHeight();
      stickyTop = $sticky.offset().top;
      var stickyStopperPosition = $stickyrStopper.offset().top;
  
      jQuery(window).scroll(function(){ // scroll event
        windowTop = jQuery(window).scrollTop(); // returns number
        moveSidebar();
      });
    }
    var moveSidebar = function(){
        if(mobilecheck()) return;
        let basic_height = parseInt(jQuery("#customer_details").css("height"));
        if(basic_height<generalSidebarHeight){
            $sticky.css({ position: 'relative', top: 0 });
            return;
        }
        stopPoint = stickyStopperPosition - generalSidebarHeight - stickOffset;
        diff = stopPoint + stickOffset-stickyTop;
          if (stopPoint < windowTop) {
            $sticky.css({ position: 'absolute', top: diff });
        } else if (stickyTop < windowTop+stickOffset) {
            $sticky.css({ position: 'fixed', top: stickOffset });
        } else {
            $sticky.css({position: 'absolute', top: 'initial'});
        }
    }  
    jQuery('.col-box-new .collapse').on('hidden.bs.collapse', function (event) {
        generalSidebarHeight = $sticky.innerHeight();
        console.log(parseInt(jQuery("#"+event.target.id).css("height")));
        moveSidebar();
    })
    jQuery('.col-box-new .collapse').on('shown.bs.collapse', function (event) {
        console.log(parseInt(jQuery("#"+event.target.id).css("height")));
        //stickOffset -=parseInt(jQuery("#"+event.target.id).css("height"));
        generalSidebarHeight = $sticky.innerHeight();
        moveSidebar();
    })
    jQuery('.col-box-new .collapse').on('hide.bs.collapse', function (event) {
        //stickOffset +=parseInt(jQuery("#"+event.target.id).css("height"));
        jQuery("a[href=#"+event.target.id+"] i").removeClass("fa-chevron-down").addClass("fa-chevron-right");
    })
    jQuery('.col-box-new .collapse').on('show.bs.collapse', function (event) {
        jQuery("a[href=#"+event.target.id+"] i").removeClass("fa-chevron-right").addClass("fa-chevron-down");
    })
});
function myfunc(val){
    
   var coupon_code = jQuery('.checkout_coupon #coupon_code').val();
   if(coupon_code == ''){
        alert('por favor ingrese el código');
        return false;
   }
    jQuery('.msg').text('Por favor espera..');
    var data = {
        security: wc_cart_params.apply_coupon_nonce,
        coupon_code: coupon_code
    };
    /*jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'post',
        dataType: 'json',
        success: function (data) {
            alert(data.msg);
            jQuery('.msg').text('');
            jQuery('body').trigger('update_checkout');
            jQuery('.checkout_coupon #coupon_code').focus();
            setTimeout(function () {
                jQuery('body').trigger('update_checkout');
            }, 100);
           //jQuery( 'body' ).trigger( 'update_checkout' );
           //location.reload();
        },
        data: data
    }); */
    var get_url = function( endpoint ) {
		return wc_cart_params.wc_ajax_url.toString().replace(
			'%%endpoint%%',
			endpoint
		);
	};
    var show_notice = function( html_element, $target ) {
		if ( ! $target ) {
			$target = jQuery( '.woocommerce-notices-wrapper:first' ) || jQuery( '.cart-empty' ).closest( '.woocommerce' ) || jQuery( '.woocommerce-cart-form' );
		}
		$target.prepend( html_element );
    };
    jQuery.ajax( {
        type:     'POST',
        url:      get_url( 'apply_coupon' ),
        data:     data,
        dataType: 'html',
        success: function( response ) {
            jQuery( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
            jQuery('.msg').text('');
            //show_notice( response );
            jQuery( document.body ).trigger( 'applied_coupon', [ coupon_code ] );
        },
        complete: function() {
            jQuery('.checkout_coupon #coupon_code').val( '' );
            jQuery( 'body' ).trigger( 'update_checkout' );
        }
    } );

}
/*window.onbeforeunload = function() {
    localStorage.setItem(billing_first_name, jQuery('#billing_first_name').val());
    localStorage.setItem(billing_last_name, jQuery('#billing_last_name').val());
    localStorage.setItem(billing_phone, jQuery('#billing_phone').val());
    localStorage.setItem(billing_cell, jQuery('#billing_cell').val());
    localStorage.setItem(billing_address_1, jQuery('#billing_address_1').val());
    localStorage.setItem(service_description, jQuery('#service_description').val());
    localStorage.setItem(billing_comments,jQuery('#billing_comments').val());
    localStorage.setItem(cust_latitude, jQuery('#cust_latitude').val());
    localStorage.setItem(cust_longitude, jQuery('#cust_longitude').val());
    localStorage.setItem(billing_email, jQuery('#billing_email').val()); 
    localStorage.setItem(extra_service_name, jQuery('#extra_service_name').val());
}

window.onload = function() {

    var billing_first_name = localStorage.getItem(billing_first_name);
    var billing_last_name = localStorage.getItem(billing_last_name);
    var billing_phone = localStorage.getItem(billing_phone);
    var billing_cell = localStorage.getItem(billing_cell);
    var billing_address_1 = localStorage.getItem(billing_address_1);
    var service_description = localStorage.getItem(service_description);
    var billing_comments = localStorage.getItem(billing_comments);
    var cust_latitude = localStorage.getItem(cust_latitude);
    var cust_longitude = localStorage.getItem(cust_longitude);
    var billing_email = localStorage.getItem(billing_email);
    var extra_service_name = localStorage.getItem(extra_service_name);
    
    if (billing_first_name !== null) jQuery('#billing_first_name').val(billing_first_name);
    if (billing_last_name !== null) jQuery('#billing_last_name').val(billing_last_name);
    if (billing_phone !== null) jQuery('#billing_phone').val(billing_phone);
    if (billing_cell !== null) jQuery('#billing_cell').val(billing_cell);
    if (billing_address_1 !== null) jQuery('#billing_address_1').val(billing_address_1);
    if (service_description !== null) jQuery('#service_description').val(service_description);
    if (billing_comments !== null) jQuery('#billing_comments').val(billing_comments);
    if (cust_latitude !== null) jQuery('#cust_latitude').val(cust_latitude);
    if (cust_longitude !== null) jQuery('#cust_longitude').val(cust_longitude);
    if (billing_email !== null) jQuery('#billing_email').val(billing_email);
    if (extra_service_name !== null) jQuery('#extra_service_name').val(extra_service_name);
}*/