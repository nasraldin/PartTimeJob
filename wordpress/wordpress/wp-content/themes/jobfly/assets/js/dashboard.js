( function( $ ) {
	var dashboard = {
		init: function() {
			$('.btn-archived-job' ).on( 'click', this.actArchivedJob);
			$('.btn-delete-job' ).on( 'click', this.actDelJob);
		},
		actArchivedJob : function(event){
			var _this = $(event.currentTarget);
			var id = _this.attr('id');
			var data = { ID:id, action: "sync_project",method:"archived"};
			var success = function(res){
				if(res.success)
					_this.closest('li').remove();
				else
					alert(res.msg);
			}

			window.ajaxSend.Custom(data, success);
			return false;
		},
		actDelJob: function(event){
			var _this = $(event.currentTarget);
			var id = _this.attr('id');
			var data = { ID:id, action: "sync_project",method:"delete"};
			var success = function(res){
				if(res.success)
					_this.closest('li').remove();
				else
					alert(res.msg);
			}
			var res = confirm('Do you want to delete this job?');
			if(res) {
				window.ajaxSend.Custom(data, success);
			}

			return false;
		}
	}


		$(document).ready(function(){
			dashboard.init();
		});
})( jQuery, window.ajaxSend );