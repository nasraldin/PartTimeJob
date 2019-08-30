(function($){

   	var stripe = Stripe(box_stripe.publishable_key);

  	var elements = stripe.elements();

  	var card = elements.create('card', {
		iconStyle: 'solid',
		hidePostalCode: true,
		style: {
		base: {
		//iconColor: '#8898AA',
		lineHeight: '39px',
		height: '39px',
		border:'1px solid #eaeaea',
		fontWeight: 300,
		fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
		fontSize: '15px',
		color: '#666',

		'::placeholder': {
		color: '#666',
		},
		},
		invalid: {
		iconColor: '#e85746',
		color: '#e85746',
		}
		},
		classes: {
		// focus: 'is-focused',
		// empty: 'is-empty',
		},
  	});
  	card.mount('#card-element');

	card.addEventListener('change', function(event) {
		var displayError = document.getElementById('card-errors');
		if (event.error) {
			//displayError.textContent = event.error.message;
			displayError.textContent = event.error.invalid_request_error;

		} else {
			displayError.textContent = '';
		}
	});
	var inputs = document.querySelectorAll('input.field');

	Array.prototype.forEach.call(inputs, function(input) {

		input.addEventListener('focus', function() {
			input.classList.add('is-focused');
		});

		input.addEventListener('blur', function() {
			input.classList.remove('is-focused');
		});
		input.addEventListener('keyup', function() {
			if (input.value.length === 0) {
				input.classList.add('is-empty');
			} else {
				input.classList.remove('is-empty');
			}
		});
		input.addEventListener('change', function() {
			console.log('change');
			//input.classList.add('is-focused');
		});
	});

	function setOutcome(result) {
		var successElement = document.querySelector('.success');
		var errorElement = document.querySelector('.error');
		successElement.classList.remove('visible');
		errorElement.classList.remove('visible');

		if (result.token) {
		// Use the token to create a charge or a customer
		// https://stripe.com/docs/charges
		successElement.querySelector('.token').textContent = result.token.id;
		successElement.classList.add('visible');
		} else if (result.error) {
			errorElement.textContent = result.error.message;
			errorElement.classList.add('visible');
		}

	}
	function stripeTokenHandler(token) {
		// Insert the token ID into the form so it gets submitted to the server
		var form = document.getElementById('payment-form');
		var hiddenInput = document.createElement('input');
		hiddenInput.setAttribute('type', 'hidden');
		hiddenInput.setAttribute('name', 'stripeToken');
		hiddenInput.setAttribute('value', token.id);
		form.appendChild(hiddenInput);
		var method = 'order_gig';
		var form   = $(".act_stripe_js");
		var data = $(form).serialize();
		var pack_id = $(".select-package").find("input:checked").val();

		$.ajax({
			emulateJSON: true,
			method :'post',
			url : bx_global.ajax_url,
			data: {
			  	action: 'box_stripe_ajax',
			  	request: data,
			  	pack_id: pack_id,
			  	amount: $("#deposit_amount").val(),
			},
			beforeSend  : function(event){
				$(form).find(".btn-submit").addClass("loading");

			},
			success: function(res){
				$(".btn-pay-js").removeClass('loading');
				if( res.success ){
					window.location.href = res.url;
				}
				return false;
			},
		});

		return false;
	}

	function createToken() {
		stripe.createToken(card).then(function(result) {
			if (result.error) {
				// Inform the user if there was an error
				var errorElement = document.getElementById('card-errors');
				errorElement.textContent = result.error.message;
			} else {
				$(".btn-pay-js").addClass('loading');
				console.log('createToken');
				stripeTokenHandler(result.token);
			}
		});
	};
  	// end checkout submit
	var form = document.getElementById('payment-form');
	form.addEventListener('submit', function(e) {
		console.log('catch submit event and createToken');
		e.preventDefault();
		createToken();
	});

})(jQuery);