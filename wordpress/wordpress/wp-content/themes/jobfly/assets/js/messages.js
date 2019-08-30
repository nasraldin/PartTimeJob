( function( $ ) {
	var username_display = '';
	var username_receiver = 'You';
	var msg_submit;
	var avatars = {};
	var msg = {
		init: function(){
			avatars = JSON.parse( jQuery('#json_avatar').html() );
			console.log(avatars);

			msg_submit = {action: 'sync_message', msg_content: '', method: 'insert', cvs_id:0 };
			msg_submit.cvs_id = $("#first_cvs").val();
			$( '.render-conv' ).on('click', this.renderConversation);
			$(document).on('submit', '.frm-send-message' , this.sendMessage);

			console.log('init MSG');
			var textarea = document.getElementById('container_msg');
				textarea.scrollTop = textarea.scrollHeight;


		},

		renderConversation: function(event){
			var element = $(event.currentTarget);
			$(".cv-item").removeClass("acti");
			element.closest(".cv-item").addClass('acti');
			var id = element.attr('id');
			msg_submit.cvs_id = id;
			var success = function(res){
				$("#container_msg").html('');

				var notme_template = wp.template( 'msg_record_not_me' );
				var me_template = wp.template( 'msg_record_me' );
				$.each( res.data, function( key, value ) {
					value['avatar'] = avatars[id];

					username_display = 'You: ';
					if( value.sender_id != bx_global.user_ID){
						username_display = 'Partner: ';
						$("#container_msg").append(notme_template(value) );
					} else {
						$("#container_msg").append(me_template(value) );
					}
				});
				var textarea = document.getElementById('container_msg');
				textarea.scrollTop = textarea.scrollHeight;

				$("#form_reply").html('<form class="frm-send-message" ><textarea required name="msg_content"  class="full msg_content required" rows="3" placeholder="Type your message here"></textarea><button type="submit" class="btn btn-send-message align-right f-right">Send</button></form>');
			};

			var data = {action: 'sync_msg', method: 'get_converstaion', id:id};

			window.ajaxSend.Custom(data,success);
			return false;
		},
		sendMessage: function(){
			var element = $(event.currentTarget);
			msg_submit.msg_content = element.find(".msg_content").val();

			var success = function(res){

		        var me_template = wp.template( 'msg_record_me' );
	        	if ( res.success ){
	        		$("#container_msg").append(me_template(res.data));
	        		$(".msg_content").html('');
	        		var textarea = document.getElementById('container_msg');
					textarea.scrollTop = textarea.scrollHeight;
					console.log('reset');
	        		$("form.frm-send-message").trigger("reset");
		        } else {
		        	alert(res.msg);
		        }

			}
			msg_submit.method = 'insert';
			window.ajaxSend.Custom(msg_submit, success);
			return false;
		},

		paging: function(event){

		}
	}
	msg.init();
})( jQuery, window.ajaxSend );