( function( $ ) {
var gproject;
var project_id;
var cvs_send, msg_send;
var full_profiles = [];
var list_bid;
var bid_select;
var act_type = '';
var bid_element;
var single_project = {
	init: function() {
		this.project =JSON.parse( jQuery('#json_project').html() );
		list_bid =JSON.parse( jQuery('#json_list_bid').html() );

		gproject = this.project;
		//cvs_send = {action: 'sync_conversations',method: '',cvs_content:'', project_id:this.project.ID,receiver_id:0 };
		msg_send = {action: 'sync_message', method: 'insert',cvs_id:0, msg_content:'',receiver_id:0, project_id: this.project.ID };
		project_id: this.project.ID;

		$( '#bid_form' ).on( 'submit', this.submitBid );
		$(".btn-cancel-bid").on('click',this.quitBidForm);
		$(".btn-del-bid").on('click',this.delBid);
		$( ".btn-toggle-bid-form").on('click', this.toggleBidForm);

		$( ".input-price").on('change keyup', this.generatePrice);
		$( ".btn-act-message").on('click',this.showSendMessageForm);
		//$( "form.frm-create-conversation").live('submit', this.createConversation); // creater conversaion or reply
		$( "form.emp-send-message").live('submit', this.empSendMessage); // in right scroll bar

		$( "form.frm-send-message").on('submit', this.sendMessage); // in workspace section
		$( ".btn-toggle-award").on('click',this.showAwardForm);

		$(document).on('submit', '.frm-award' , this.awardProject);

		$( "span.btn-del-attachment").on('click', this.removeAttachment);
		$( "form#frm_emp_review").on('submit', this.reviewFreelancer);
		$( "form#frm_fre_review").on('submit', this.reviewEmployer);
		$(".btn-close").on('click',this.closeFrame);

		$( "form#frm_quit_job").on('submit', this.quitJob);
		$( "form.swp-send-message").on('submit', this.sendMessageWSP); // in workspace section
		$( "form#frmAdminAct").on('submit', this.frmAdminAct); // in workspace section

		$( "form#fre_markascomplete").on('submit', this.freMarkAsComplete); // in workspace section
		$( "form#frm_disputing").on('submit', this.sendDisputing); // in workspace section
		$( "#send_email").on('click', this.showModalSendMail); // in workspace section
		$(document).on('submit', '#form_invite_mail' , this.sendMailInvite);


		msg_send.cvs_id = $("#cvs_id").val(); // set default in for workspace page;

		if($("#container_msg").length) {
			var textarea = document.getElementById('container_msg');
			textarea.scrollTop = textarea.scrollHeight;
		}
		var height = $("body").css("height")-100;


		var h_right = $(".column-right-detail").css('height');
			h_left = $(".column-left-detail").css('height');

		if( parseInt( h_right ) > parseInt( h_left ) ){

			$(".column-left-detail").css('min-height',h_right);
		}

		$("#frame_chat").css('height',height);
		var view = this;

		var uploader = new plupload.Uploader({
		    runtimes : 'html5,flash,silverlight,html4',
		    browse_button : 'pickfiles', // you can pass in id...
		    container: document.getElementById('container_file'), // ... or DOM Element itself
		    url : bx_global.ajax_url,
		    filters : {
		        max_file_size : '10mb',
		        mime_types: [
		            {title : "Image files", extensions : "jpg,gif,png,jpeg,ico,pdf,doc,docx,zip,rar,excel,txt,mp3,wav"},
		        ]
		    },
		    multipart_params: {
		    	action: 'upload_file',
		    	post_parent: view.project.ID,
		    	project_tile: view.project.post_title,
		    	cvs_id: $("#cvs_id").val(),
		    	section :'workspace',
		    },
		    init: {
		        PostInit: function() {

		        },
		        FilesAdded: function(up, files) {

		        },

		        Error: function(up, err) {
		            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
		        },
		        FileUploaded : function(up, file, response){
		        	var obj = jQuery.parseJSON(response.response);
				    if(obj.success){
					    var new_record =  '<li class="inline f-left">' + file.name + ' (' + plupload.formatSize(file.size) + ')<span id ="'+obj.attach_id+'" class="btn-del-attachment hide">(x)</span></li>';
			            $("ul.list-attach").prepend(new_record);
				    } else{
				    	alert(obj.msg);
				    }
		        }
		    }
		});
		uploader.init();
		uploader.bind('FilesAdded', function(up, files) {
        	//view.$el.find("i.loading").toggleClass("hide");
            up.refresh();
            up.start();
        });
        $(".modal-review .fa-star").click(function(event){
        	var _this = $(event.currentTarget);
        	var score = _this.attr('title');
        	var css = 'score-'+score;
        	$(".rating-score").removeClass('score-1 score-2 score-3 score-4 score-5');
        	$(".rating-score").addClass(css);
        	$("#rating_scrore").val(score);
        })
	},

	submitBid: function(event){
		var action = "sync_bid", method = "insert";
		var successRes = function(res){
        	if ( res.success ){
        		window.location.reload(true);
	        } else {
	        	console.log(res.msg);
	        	alert(res.msg);
	        }
	    }
		window.ajaxSend.Form(event, action, method, successRes);
		return false;

	},
	quitBidForm: function(event){
		var _this = $(event.currentTarget);
		var res = confirm('Do you want to cancel?');
		return res;

	},
	delBid: function(event){
		var _this = $(event.currentTarget);
		var res = confirm('Do you want to cancel this bid?');
		if( ! res ){
			return false;
		}
		var bid_id = _this.attr('rel');
		var data = {action:'sync_bid', ID:bid_id, method:'delete'};

		var success = function(res){
			console.log(res);
			window.location.reload(true);
		}

		window.ajaxSend.Custom( data, success);
		return false;
	},
	toggleBidForm: function(event){

		$("#bid_form").slideToggle("slow");
		var _this = $(event.currentTarget);
	},

	showSendMessageForm: function(event){
		var _this = $(event.currentTarget);
		bid_element = _this.closest(".bid-item");
        var cvs_id = bid_element.find(".cvs_id").val();
        var bid_author = _this.closest(".bid-item").find(".bid_author").val();


		var data = {action: 'sync_msg', method: 'get_converstaion', id:cvs_id};
		msg_send.receiver_id = bid_author;
		msg_send.cvs_id = cvs_id;
		console.log(msg_send);
		var success = function(res){
			console.log('666');
			var content = '<div id="container_msg">';
			$.each( res.data, function( key, msg ) {
				var user_name = 'Freelancer:';
				if(msg.sender_id == gproject.post_author){
					user_name = 'You:';
				}
				content = content + '<div class="full"><label class="mauthor">'+ user_name + "</label>" + msg.msg_content + '</div>';
			});
			content = content + '</div>';

			$(".frm_content").html( content );
			var frm_send_message = wp.template("send_message");
			$(".reply_input").html(frm_send_message({}));
			$(".reply_input").show();
			$('#frame_chat').addClass('nav-view');
		}
		var beforeSend = function(event){
			if(act_type != 'cre_converstation'){
				$('#frame_chat').removeClass('nav-view');
			}
			console.log('loading');
		}
		window.ajaxSend.customLoading(data,beforeSend,success);

	},
	empSendMessage: function(event){ // send in list bidding.

		var _this = $(event.currentTarget);
		msg_send.msg_content = _this.find(".msg_content").val();

		var success = function(res){
        	if ( res.success ){
        		var record = '<div class="msg-record msg-item row"><div class="col-md-12">';
        		record = record + '<span class="msg-author f-left col-md-2"> &nbsp; </span> <span class="msg-content col-md-10">' + msg_send.msg_content;
        		record = record + '</span></div></div>';
        		$("#container_msg").append( record );
        		var textarea = document.getElementById('container_msg');
				textarea.scrollTop = textarea.scrollHeight;
        		$("form.emp-send-message").trigger("reset");

        		if(msg_send.cvs_id == 0 ){
        			msg_send.cvs_id = res.result.cvs_id; // make sure, in the next msg will be not create conversation.

         			bid_element.find(".cvs_id").val(res.result.cvs_id);
         			console.log('assig done');
         		}
         		console.log(msg_send);
	        }
		}
		msg_send.method = 'insert';
		window.ajaxSend.Custom(msg_send, success);
		return false;
	},

	showAwardForm: function(event){

		$('#frame_chat').remove('nav-view');
		var _this = $(event.currentTarget);

        var bid_id = _this.attr('id');
        var bid_author = _this.val();
        var data = {action: 'sync_profile', method: 'get_full_info', user_id: bid_author};

		var award_form = wp.template("award_form");

		$(".frm_content").html( award_form(list_bid[bid_id] ) );
		$(".reply_input").hide();

		var beforeSend = function(event){
			$('#frame_chat').removeClass('nav-view');
		}
		var success = function(event){
			$('#frame_chat').addClass('nav-view');
		};

 		window.ajaxSend.customLoading(data,beforeSend,success);


	},
	closeFrame: function(e){
		console.log('close');
		$('#frame_chat').removeClass('nav-view');
	},


	sendMessage: function(e){
		var success = function(res){

	       	if ( res.success ){

        		var record = '<div class="msg-record msg-item row"><div class="col-md-12">';
        		record = record + '<span class="msg-author f-left col-md-2"> &nbsp; </span> <span class="msg-content col-md-10">' + res.msg;
        		record = record + '</span></div></div>';
        		$("#container_msg").append( record );
        		var textarea = document.getElementById('container_msg');
				textarea.scrollTop = textarea.scrollHeight;
        		$("form.send-message").trigger("reset");

	        } else {
	        	alert(res.msg);
	        }
		}

		var _this = $(event.currentTarget);
		msg_send.msg_content = _this.find(".msg_content").val();

		window.ajaxSend.Custom(msg_send, success);
		return false;
	},
	sendMessageWSP : function(e){ // send in worksapce
		var action = 'sync_message', method = 'insert';
		var success = function(res){

        	if ( res.success ) {
        		if( typeof res.data.msg_type !== 'undefined' && res.data.msg_type == 'disputing'){
        			window.location.reload(true);
        		} else {

	        		var frm_send_message = wp.template("msg_record_wsp");
	        		$("#container_msg").append( frm_send_message(res.data) );
	        		$("form.swp-send-message").trigger("reset");
	        		var textarea = document.getElementById('container_msg');
					textarea.scrollTop = textarea.scrollHeight;
				}
	        } else {
	        	alert(res.msg);
	        }
		}
		window.ajaxSend.Form(event, action, method, success);
		return false;
	},
	frmAdminAct: function(event){
		var action = 'workspace_act', method = 'admin_act';
		var success = function(res){
			console.log(' fre_markascomplete');
        	if ( res.success ){
        		window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }

		}
		window.ajaxSend.Form(event, action, method, success);
		return false
	},
	awardProject: function(event){
		event.preventDefault();
		var success = function(res){
			console.log(res);
        	if ( res.success ){

        		if( res.url_redirect ){
	        		window.location.href = res.url_redirect; // redirect to paypal to pay
	        	} else {
        			window.location.reload(true);
        		}
	        } else {
	        	console.log(res);

	        	alert(res.msg);
	        }
		}

		window.ajaxSend.awardJob(event, project_id,success); //gproject == project id

		return false;
	},
	removeAttachment: function (event){
		event.preventDefault();
		var form = $(event.currentTarget),
			id 	= form.attr('id');

		var success = function(res){
        	if ( res.success ){
        		form.closest("li").remove();
	        } else {
	        	alert(res.msg);
	        }
		}
		var data = {id: id, action: 'sync_attachment',method:'remove'}
		window.ajaxSend.Custom(data, success);
		return false;
	},
	reviewFreelancer: function(event){
		var action = 'act_review', method = 'review_fre';
		var success = function(res){
	        console.log(res);
        	if ( res.success ){
        		window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
		}
		window.ajaxSend.Form(event, action, method, success);

		return false;
	},
	reviewEmployer: function(event){
		var action = 'act_review', method = 'review_emp';
		var success = function(res){
	        console.log(res);
        	if ( res.success ){
        		window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
		}

		window.ajaxSend.Form(event, action, method, success);
		return false;
	},
	quitJob: function(event){
		var action = 'workspace_act', method = 'quit_job';
		var success = function(res){
			console.log(' quit done');
        	if ( res.success ){
        		window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
	        $('#quytModal').modal().hide();
		}
		window.ajaxSend.Form(event, action, method, success);

		return false;
	},
	freMarkAsComplete : function(event){
		var action = 'workspace_act', method = 'fre_markascomplete';
		var success = function(res){

        	if ( res.success ){
        		window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
	        $('#freMarkAsComplete').modal().hide();
		}
		window.ajaxSend.Form(event, action, method, success);

		return false;
	},
	sendDisputing: function(event){
		var action = 'workspace_act', method = 'submit_disputing';
		var success = function(res){
			console.log(' submit_disputing');
        	if ( res.success ){
        		window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
	        $('#disputeModal').modal().hide();
		}
		window.ajaxSend.Form(event, action, method, success);

		return false;
	},
	showModalSendMail: function(event){
		$('#mail_invite_friend').modal().show();
	},
	sendMailInvite: function(event){

		var method = '';
		var successRes = function(respond){
			$("#mail_result").html(respond.msg);
			setTimeout(function(){
  				$('#mail_invite_friend').modal().hide();
			}, 1500);

		}
		var action = 'send_job_to_email';

		window.ajaxSend.Form(event, action, method, successRes);
		return false;
	},
	load_more_bid: function(e){

	},
	generatePrice: function(e){

		var input = $(e.currentTarget);

		var data = { action:'generate_price',price: this.value };
		var success = function(respond){
			$("#_bid_receive").val(respond.data.fre_receive);
			$("#fee_servicce").val(respond.data.cms_fee)

		}
		window.ajaxSend.Custom(data, success);
	},
}

	 $(document).ready(function(){
		single_project.init();
	});

})( jQuery,window.ajaxSend );

//https://stackoverflow.com/questions/7410063/how-can-i-listen-to-the-form-submit-event-in-javascript