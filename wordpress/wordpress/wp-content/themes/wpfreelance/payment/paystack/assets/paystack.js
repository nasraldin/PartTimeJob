
console.log('custom_redirect_paystack');
console.log(box_paystack_params);
function custom_redirect_paystack(order_id, amount){
		var handler = PaystackPop.setup({
	      	key: box_paystack_params.key,
	      	email: box_paystack_params.customer_email,
	      	amount: amount*100,
	      	currency: box_paystack_params.currency_code,
	      	firstname: box_paystack_params.firstname,
	  		lastname: box_paystack_params.lastname,
	      	ref: order_id,
	     	metadata: {
	        custom_fields: [
	            {
	                display_name: "Mobile Number",
	                variable_name: "mobile_number",
	                value: "+2348012345678"
	            }
	         ]
	      	},
	      	callback: function(response){
	      		console.log(response);
	   			//  message: "Approved"
				// reference: "1421"
				// status: "success"
				// trans: "150274653"
				// transaction: "150274653"
				// trxref: "1421"
	          	jQuery.ajax({
			        emulateJSON: true,
			        method :'post',
			        url : bx_global.ajax_url,
			        data: {
		                action: 'manual_approved_paystack_order',
		                order_id:  response.reference,

			        },
			        beforeSend: function(){
			        	console.log('beforeSend run');
			        },
			        success: function(res){
			        	console.log('approve_order_of_paystack done');
			        	console.log(res);
			        	console.log(res.rediect_url);
			        	window.location.href = res.rediect_url;
			        },
			        error:function(){
			        	console.log('reror');
			        }
			    });
	      	},
	      	onClose: function(){
	          alert('window closed');
	      	}
		});

		handler.openIframe();
}
