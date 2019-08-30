( function( $ ) {
var single;
var project_id;
var cvs_send, msg_send;
var full_profiles = [];
var list_bid;
var bid_select;
var act_type = '';
var single_project = {
	init: function() {

		this.job =JSON.parse( jQuery('#json_job').html() );

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
		$( ".btn-save-job").on('click', this.actSaveJob);
		single = this;

	},

	actSaveJob: function (event){
		var view = this;

		event.preventDefault();

		 $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: 'saveJob',
	                job_id: single.job.ID,
	        },
	        beforeSend  : function(event){
	        	if( $(view).attr('disable') == 1 )
	        		return false;
	        	$(view).addClass('loading');
	        },
	        success: function(event){
	        	$(view).removeClass('loading btn-save-job').delay( 3500 ).fadeIn( 3000 );;
	        	$(view).addClass('saved');
	        	$(view).attr('disable', 1);
	        	var t = $(view).find(".fa");
	        	t.removeClass('fa-heart-o');
	        	t.addClass('fa-heart');
	        }
	    });
		return false;
	},

	quitJob: function(event){
		var action = 'workspace_action', method = 'quit_job';
		var success = function(res){
			console.log(' quit done');
        	if ( res.success ){
        		//window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
	        $('#quytModal').modal().hide();
		}
		window.ajaxSend.Form(event, action, method, success);

		return false;
	},
	freMarkAsComplete : function(event){
		var action = 'workspace_action', method = 'fre_markascomplete';
		var success = function(res){
			console.log(' fre_markascomplete');
        	if ( res.success ){
        		//window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
	        $('#freMarkAsComplete').modal().hide();
		}
		window.ajaxSend.Form(event, action, method, success);

		return false;
	},
	sendDisputing: function(event){
		var action = 'workspace_action', method = 'submit_disputing';
		var success = function(res){
			console.log(' submit_disputing');
        	if ( res.success ){
        		//window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
	        $('#disputeModal').modal().hide();
		}
		window.ajaxSend.Form(event, action, method, success);

		return false;
	},
	load_more_bid: function(e){

	},
	generatePrice: function(e){
		console.log('123');
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