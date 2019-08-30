( function( $ ) {
	var username_display = '';
	var username_receiver = 'You';
	var msg_submit;
	var avatars = {};
	var msg = {
		init: function(){
			avatars = JSON.parse( jQuery('#json_avatar').html() );
			msg_submit = {action: 'sync_message', msg_content: '', method: 'insert', cvs_id:0, attach_ids: [] };
			msg_submit.cvs_id = $("#first_cvs").val();
			$( '.render-conv' ).on('click', this.renderConversation);
			$(document).on('submit', '.frm-send-message' , this.sendMessage);


			var textarea = document.getElementById('container_msg');
				textarea.scrollTop = textarea.scrollHeight;

			var window_height = $(window).height();
			var header_height = $("#full_header").css('height');
			//var footer_height =
			var min_height = parseInt(window_height) - parseInt(header_height) - 240;
			var right_mh = min_height - 44;
			var right_cms = min_height + 16;

			//$("#list_converstaion").css('height',min_height+'px');
			//$("#container_msg").css('height',right_mh+'px');

			$("#container_msg.cvs-null").css('height',right_cms+'px');

			// upload file
			var nonce = $("#fileupload-container").find('.nonce_upload_field').val();
			var uploader = new plupload.Uploader({
			    runtimes: 'html5,gears,flash,silverlight,browserplus,html4',
                multiple_queues: true,
                multipart: true,
                urlstream_upload: true,
                multi_selection: false,
                upload_later: false,

			    browse_button : 'sp-upload', // you can pass in id...
			    container: document.getElementById('fileupload-container'), // ... or DOM Element itself
			    url : bx_global.ajax_url,
			    filters : {
			        max_file_size : '10mb',
			        mime_types: [
			            {title : "Image files", extensions : "jpg,gif,png,jpeg,ico,pdf,doc,docx,zip,excel,txt,mp3,wav"},
			        ]
			    },
			    multipart_params: {
			    	action: 'box_upload_file',
			    	nonce_upload_field: nonce,

			    },
			    init: {
			        PostInit: function() {


			        },
			        BeforeUpload: function(up, file) {
			        	$(up.settings.container).addClass('uploading');
		                up.disableBrowse(true);
		                $(".btn-send-message").html('Uploading');
		                $(".btn-send-message").addClass('dot-loading');
		            },
			        FilesAdded: function(up, files) {
			        	up.disableBrowse(true);
			        },

			        Error: function(up, err) {
			        	alert(err);
			            //document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			        },
			        FileUploaded : function(up, file, response){
			        	var obj = jQuery.parseJSON(response.response);
					    if(obj.success){
				            $(".msg_content ").val(file.name);
				            msg_submit.attach_ids.push(obj.attach_id);
				            $(".frm-send-message").focus();
				            $(".frm-send-message").addClass('focus');
				            $(".btn-send-message").html('Attach file');
				            $(".btn-send-message").removeClass('dot-loading');

					    } else{
					    	alert(obj.msg);
					    }

					    setTimeout(function(){ $(up.settings.container).removeClass('uploading'); }, 300);
					    up.disableBrowse(false);
			        }
			    }
			});
			uploader.init();
			uploader.bind('FilesAdded', function(up, files) {
	        	//view.$el.find("i.loading").toggleClass("hide");
	            up.refresh();
	            up.start();
	        });
			//end upload file

		},

		renderConversation: function(event){
			event.preventDefault();
			var element = $(event.currentTarget);
			$(".cv-item").removeClass("acti");
			element.closest(".cv-item").addClass('acti');
			var id = element.attr('id');
			var display_name = element.html();
			var is_profile = element.attr('has_profile');

			if( is_profile ){
				var profile_link = element.attr('href');
				display_name = '<a target="_blank" href="'+profile_link+'">'+display_name+'</a>';
			}
			$("#display_name").html(display_name);
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

				//$("#form_reply").html('<form class="frm-send-message" ><textarea required name="msg_content"  class="full msg_content required" rows="3" placeholder="'+inbox.type_msg+'"></textarea><button type="submit" class="btn btn-send-message align-right f-right">'+inbox.btn_send+'</button></form>');
			};

			var data = {action: 'sync_msg', method: 'get_converstaion', id:id};

			window.ajaxSend.Custom(data,success);
			return false;
		},
		sendMessage: function(event){
			var element = $(event.currentTarget);
			msg_submit.msg_content = element.find(".msg_content").val();

			var success = function(res){

		        var me_template = wp.template( 'msg_record_me' );
	        	if ( res.success ){
	        		$("#container_msg").append(me_template(res.data));
	        		$(".msg_content").html('');
	        		var textarea = document.getElementById('container_msg');
					textarea.scrollTop = textarea.scrollHeight;

	        		$("form.frm-send-message").trigger("reset");

		        } else {
		        	alert(res.msg);
		        }

		        if( msg_submit.attach_ids.length > 0 ){
		        	$(".btn-send-message").html('Send');
		        	msg_submit.attach_ids = [];
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