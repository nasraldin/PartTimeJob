( function( $ ) {
	$(document).ready(function(){


		$(".btn-approve-order").click( function(event){
			var _this = $(event.currentTarget);
			var type = 'approve_deposit_act';
			var data = {order_id: _this.attr('id') };

			window.ajaxSend.Approve(data, type, _this);

		});

		$(".btn-approve-withdrawal").click(function(event){
			var _this = $(event.currentTarget);
			var type = 'approve_withdraw_act';
			var data = {order_id: _this.attr('id') };

			window.ajaxSend.Approve(data, type, _this);

		});

	});
})(jQuery, window.ajaxSend);
