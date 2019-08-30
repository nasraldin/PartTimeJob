( function( $ ) {
	$(document).ready(function(){


		$(".btn-approve-order").click(function(){
			var _this = $(event.currentTarget);
			var type = 'approve_buy_credit';
			var data = {order_id: _this.attr('id') };

			window.ajaxSend.Approve(data, type, _this);

		});

		$(".btn-approve-widthraw").click(function(){
			var _this = $(event.currentTarget);
			var type = 'approve_withdraw';
			var data = {order_id: _this.attr('id') };

			window.ajaxSend.Approve(data, type, _this);

		});

	});
})(jQuery, window.ajaxSend);
