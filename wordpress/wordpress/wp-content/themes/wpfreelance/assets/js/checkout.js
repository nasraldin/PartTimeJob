( function( $ ) {
	var box_checkout = {

		init :function(){

			this.selectedPack 	= 0;
			this.gateWay 		= 0;
			this.formClass 		= '.form_js_'+bx_global.first_gateway;
			this.btn_class 		= '.btn-js-'+bx_global.first_gateway;
			this.amount 		= $("#deposit_amount").val();
			$('.btn-pay-js' ).on( 'click', this.triggerBtnSubmit );
			$(".checkbox-gateway").on('change',this.eventChangeGateway);
			$(".btn-select").on('click',    this.evenSelectPackage);
			$(".df-box-checkout-js").on('submit',this.submitDefaultCheckoutForm);

			$(".frm-membership").on('submit',this.submitSubscriptionForm);
			$("#deposit_amount").on('change',this.eventChangeAmount);
			box_ck = this;
			if( ! this.selectedPack){
				// this.hidePaymentGateways();
			}
		},
		eventChangeAmount: function(event){
			console.log('123');
			var value = $( this ).val();
			box_ck.amount = value;

		},
		submitSubscriptionForm: function(event){
			var form 	= $(event.currentTarget),
			data   	= {};
		    form.find(' input[type=text], input[type=hidden],input[type=email], input[type=number], input[type=date],input[type=password],  input[type=checkbox],textarea,select').each(function() {
		    	var key 	= $(this).attr('name');
		        data[key] 	= $(this).val();
		    });

		    form.find('input:radio:checked').each(function() {
		    	var key 	= $(this).attr('name');
		        data[key] 	= $(this).val();
		    });
		    method : '1223';
		    $.ajax({
		        emulateJSON: true,
		        method :'post',
		        url : bx_global.ajax_url,
		        data: {
	                action: 'box_membership_checkout',
	                request: data,

		        },
		        beforeSend  : function(event){

		        	form.find(".btn-submit").addClass("progressing");
		        },
		        success: function(res){
		        	if(box_ck.gateWay == 'stripe'){
		        		var stripe = Stripe('pk_test_WcPh2d4JR4Psog2FQHTHMiPK');
		        		stripe.redirectToCheckout({
			                items: [{
			                  // Define the product and plan in the Dashboard first, and use the plan
			                  // ID in your client-side code.
			                  plan: '2-boxthemes.net-59b7b14aedd06',
			                  quantity: 1,
			                  sku: 'sku_123',

			                }],
			                successUrl: 'https://boxthemes.net/success/',
			                cancelUrl: 'https://boxthemes.net/cancel/',
			                customerEmail: 'abc@gmail.com',
			               // sessionId: '123',
		             	});
		        	} else {
		        		if(res.redirect_url)
		        			window.location.href = res.redirect_url;
		        	}
		        },
		    });
		    return false;
		},
		submitDefaultCheckoutForm: function(event){

			var method = '';
			var _this = $(event.currentTarget);

			var form 	= $(event.currentTarget),
			data   	= {};
		    form.find(' input[type=text], input[type=hidden],input[type=email], input[type=number], input[type=date],input[type=password],  input[type=checkbox],textarea,select').each(function() {
		    	var key 	= $(this).attr('name');
		        data[key] 	= $(this).val();
		    });

		    form.find('input:radio:checked').each(function() {
		    	var key 	= $(this).attr('name');
		        data[key] 	= $(this).val();
		    });

		    $.ajax({
		        emulateJSON: true,
		        method :'post',
		        url : bx_global.ajax_url,
		        data: {
	                action: 'box_checkout',
	                request: data,
	                method : method,
	                amount: box_ck.amount,
		        },
		        beforeSend  : function(event){
		        	if( $(".btn-pay-js").hasClass('loading') ){
		        		return false;
		        	}
		        	$(".btn-pay-js").addClass('loading');

		        	console.log('access ajax');
		        },
		        success: function(res){
		        	$(".btn-pay-js").attr('disabled',false);
		        	$(".btn-pay-js").removeClass('loading');
					if( res.success ){
						if( res.redirect_url ){
							window.location.href = res.redirect_url;
						} else if(res.patch_form) {
							$(res.patch_form).appendTo('body').submit();
						}else {
							var t = res.custom_js;
								var custom_js = res.custom_js;			// Params to pass to the function
							window[custom_js](res.order_id,res.amount); // custom_redirect_paystack
						}
					} else {
						alert(res.msg);
					}
				},
		    });
			return false;
		},
		hidePaymentGateways: function(){
			$(".step-2").addClass('deactive');
			// $(".all-gate").addClass('hide');
			// $(".move-box").addClass('hide');
		},
		eventChangeGateway: function(event){

			var value = $( this ).val();

			box_ck.btn_class = '.btn-js-'+value;
			box_ck.formClass = '.form_js_'+value;
			console.log(box_ck.formClass);
			$(".payment-item" ).removeClass('is-selected');
		  	$( event.target ).closest( ".payment-item" ).toggleClass( "is-selected" );
		  	box_ck.gateWay = value;
		},
		evenSelectPackage: function(event){
			var _this = $(event.currentTarget);
			var pack_id = _this.attr('pack_id');
			box_ck.selectedPack = pack_id;
			$(".package-plan").removeClass('activate');
			_this.closest('.package-plan').addClass('activate ');
			$(".step-1").toggleClass('selected');
			if(box_ck.selectedPack){
				box_ck.showPaymentGateways();
			}

		},
		showPaymentGateways: function() {
			$(".step-2").removeClass('deactive');
		},
		triggerBtnSubmit:function(){

			if(box_ck.btn_class =='.btn-js-stripe'){
	    		$(box_ck.btn_class).click();
	    	} else {
	    		$(box_ck.formClass).submit();
	    	}

		},

	}
	$(document).ready(function(){
		box_checkout.init();
	});
})( jQuery, window.ajaxSend, window.package_select );