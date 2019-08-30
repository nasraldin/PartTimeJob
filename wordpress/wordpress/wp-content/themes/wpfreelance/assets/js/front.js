( function( $ ) {
	var box_front,acc_menu = 0;
	var front = {
		init: function() {

			$('#signup' ).on( 'submit', this.submitSignup );
			$('form.sign-in').on( 'submit',this.signIn );
			$('.toggle-menu' ).on( 'click', this.toggleMenu );
			$( "#search_type").on( 'change',this.setSearchUrl );
			$(".btn-del-noti").on('click',this.delNotify );
			$("#toggle-msg").on( 'click', this.setNotifySeen);
			$(".toggleActiv").on('click', this.toggleActiv);
			$(".togleReview").on('click', this.togleReview);



			//$( ".pagination").on('click',this.pagingProject);
			//$(".frm-membership").on('submit',this.checkoutMemberShip);
			$('form.direct-message-js').on( 'submit',this.sendDirectMessage );
			$('form.invite-bid-js').on( 'submit',this.inviteBid );
			//$(document).on('submit', '.direct-message-js' , this.sendDirectMessage);


			var view = this;
			box_front = this;
			$(".menu-hamburger").click(function(){
				var wrap = $(this).closest(".col-nav");
				if( wrap.hasClass('visible')) {
					wrap.toggleClass('default');
				} else {
					wrap.toggleClass('visible');
				}
			});

			$(document).on('keyup', '.msg_content', function(event){
				var _this = $(event.currentTarget);
				console.log( $.trim( _this.val()) );
				if ( ! $.trim( _this.val()  ) == '' ){
					_this.closest('form').addClass('focus');
				} else {
					_this.closest('form').removeClass('focus');
				}
			});

			//$('[data-toggle="tooltip"]').tooltip()

			$(".toggle-check").click(function(event){
				var block = $(this).closest(".block");
				var display =block.find("ul").css( "display" );

				block.find("ul").slideToggle(300);
				if(display=='block'){
					$(this).removeClass('glyphicon-menu-down cs');
					$(this).addClass('glyphicon-menu-right cs ');
				} else {
					$(this).addClass('glyphicon-menu-down cs');
					$(this).removeClass('glyphicon-menu-right cs');
				}
			});
			$(".btn-adv").click(function(event){
				$(".search-adv").slideToggle(300);
			});

			$(window).scroll(function() {
			    var height = $(window).scrollTop();
			    if(height  > 0) {
			    	$("body").addClass('fixed');
			    } else {
			    	$("body").removeClass('fixed');
			    }
			});


			$(".toggleRoleViewer .auto-save").change(function(event){

				var _this = $(event.currentTarget),
					role = 'fre',
					data = {action:'toggleActivateRole', role:_this.val() };;
				window.show_dropdow = 1;

				$.ajax({
			        emulateJSON: true,
			        method :'post',
			        url : bx_global.ajax_url,
			        data: data,
			        beforeSend  : function(event){
			        	console.log('Insert message');
			        },
			        success: function(res){
			        	window.show_dropdow = 0;
			        	window.location.reload(true);
			        },
			    });
			    return true;

			})
			$('.profile-account').on('hide.bs.dropdown', function () {
				console.log('hide.bs.dropdown');
			});
			$('.profile-account').on('hidden.bs.dropdown', function () {

				console.log(window.show_dropdow);
				if( window.show_dropdow )
					$(this).addClass('open');

			});

		},

	toggleMenu: function(event){
		var _this = $(event.currentTarget);
		$(".profile-menu").toggleClass('hide');
	},
	signIn: function(event){
		var action = 'bx_login', method ='';
		var success =  function(res){
			console.log(res);
        	if ( res.success ){
        		if( res.redirect_url  )
        			window.location.href = res.redirect_url;
        		else
        			window.location.reload(true);
	        } else {
	        	if( bx_global.enable_capthca ){
	        		grecaptcha.reset();
	        	}
	        	alert(res.msg);
	        }
        };

        window.ajaxSend.Form(event,action,method,success);
		return false;
	},
	submitSignup: function(event){
		event.preventDefault();
		var action = 'bx_signup', method = 'insert';
		var success =  function(res){
			var form 	= $(event.currentTarget);
			form.find(".btn-submit").removeClass("loading");
        	if ( res.success ){

        		if( ! res.nextstep ){
        			window.location.href = res.redirect_url;
        		} else {
        			box_front.goCheckoutStep();
        		}
	        } else {
	        	$("#loginErrorMsg").html(res.msg);
	        	$("#loginErrorMsg").removeClass("hide");
	        	if( bx_global.enable_capthca ){
	        		grecaptcha.reset();
	        	}
	        }
        };
        if( bx_global.enable_capthca ){
	     //    var recaptchaRes = grecaptcha.getResponse();
	    	// if(recaptchaRes.length == 0) {
	    	// 	return false;
	    	// }

	    }

        window.ajaxSend.Form(event,action,method,success);
		return false;
	},
	goCheckoutStep: function(){
		$(".step-register").removeClass('current');
		$(".checkout-step").addClass('current');
		$(".nav-register").removeClass('current');
		$(".nav-checkout").addClass('current');
	},
	setSearchUrl : function(event){
		var _this = $(event.currentTarget);
		var status = $('option:selected',this).attr('alt');
		$("form.frm-search").attr('action',_this.val() );
		$("input#keyword").attr('placeholder', status );
	},
	setNotifySeen: function(event){
		var _this = $(event.currentTarget);
		var data = {id:_this.attr('rel'), method: 'seenall', action : 'sync_notify'};

		var success = function(res){

		}
		window.ajaxSend.Custom( data, success);
		return true;
	},
	delNotify : function(event){
		var _this = $(event.currentTarget);
		var data = {id:_this.attr('rel'), method: 'delete', action : 'sync_notify'};
		var success = function(res){
			_this.closest("li").remove();
		}
		window.ajaxSend.Custom( data, success);
		return false;
	},
	toggleActiv: function(event){
		var _this = $(event.currentTarget);

		var status = 'deactive';
		if( _this.hasClass('activate') || _this.hasClass('deactivate') ){
			if(_this.hasClass('activate'))
				status = 'activate';
			else if(_this.hasClass('deactivate') ){
				status = 'inactive';
			}
			var profile_id = _this.attr('value');
			$.ajax({
		        emulateJSON: true,
		        method :'post',
		        url : bx_global.ajax_url,
		        data: {
		                action: 'sync_profile',
		                request: {ID: profile_id ,is_available: status},
		                method : 'update_profile_status',
		        },
		        beforeSend  : function(event){
		        	console.log('bat dau submit job');
		        },
		        success : function(res){
		        	if ( res.success ){
		        		window.location.reload(true);
			        } else {
			        	alert(res.msg);
			        }
		        }
	        });
		} else if( _this.hasClass('approveproject') ||  _this.hasClass('archived') ){
			var status = 'archived';
			if( _this.hasClass('approveproject') )
				status = 'publish';
			var project_id = _this.attr('value');
			var data = {ID:project_id,post_status : status};
			$.ajax({
		        emulateJSON: true,
		        method :'post',
		        url : bx_global.ajax_url,
		        data: {
		                action: 'sync_project',
		                request: data,
		                method : 'update_status',
		        },
		        beforeSend  :function(event){

		        },
		        success: function(res){
		        	if(res.success){
		        		window.location.reload(true);
		        	} else {
		        		alert(res.msg);
		        	}
		        },
		    });
		}

	},
	togleReview: function(event){

		var _this = $(event.currentTarget);

		var status = _this.attr('rel'),
			profile_id = _this.attr('value');
		$.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: 'sync_profile',
	                request: {profile_id: profile_id ,status: status},
	                method : 'toggle_review_status',
	        },
	        beforeSend  : function(event){
	        	console.log('bat dau submit job');
	        },
	        success : function(res){
	        	if ( res.success ){
	        		window.location.reload(true);
		        } else {
		        	alert(res.msg);
		        }
	        }
        });
	},

	checkoutMemberShip: function(event){
		var _this = $(event.currentTarget);
		var data = {id:_this.attr('rel'), method: 'delete', action : 'sync_notify'};
		var method = 'membership';
		var success = function(res){
			if( !res.popup ){
				console.log('redirect here');
				_this.find(".btn-submit").removeClass("loading");
				window.location.href = res.redirect_url;
			} else {
				//res.scrip_method; no remove
				//script = $(data).text();
				//$.globalEval(res.scrip_method); no remove end.
			}
		}
		window.ajaxSend.membershipCheckout( event,  method, success );
		return false;
	},
	sendDirectMessage: function(event){
		event.preventDefault();

		var form 	= $(event.currentTarget),
			data   	= {};
	    form.find(' input, textarea,select').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });
	    data['action'] = 'send_direct_message';
	     $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data:  data,
	        beforeSend  : function(event){
	        	console.log('Insert message');
	        },
	        success: function(res){
	        	console.log(res);
				if( res.success ){
					console.log(res);
					form.find(".form-pre").hide();
					form.find(".msg-sent").removeClass('hidden');
					form.find(".view-msg").attr('href',res.link);
				}
		    },
	    });


		return false;
	},
	inviteBid: function(event){
		event.preventDefault();

		var form 	= $(event.currentTarget),
			data   	= {};
	    form.find(' input, textarea,select').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });
	    data['action'] = 'invite_bid';

		$.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data:  data,
	        beforeSend  : function(event){
	        	form.addClass('processing');
	        },

	        success: function(event){
	        	form.removeClass('processing');
	        },
	    });
		return false;
	},

}


	$(document).ready(function(){
		front.init();
		var skill_selected = [];

		window.search_args.paged =  bx_global.current_paged;
		var list = [0,1,2.5,5,10,20,50,100,200,1000];
		var check = 0;
		window.search_args.keyword = $("#keyword").val();
		window.search_args.post_type = $("#post_type").val();

		if($("#range").length) {
			$("#range").ionRangeSlider({
	            min: 0,
	            max: 1000,
	            //from: 0,
	            //type: 'double',
	           // postfix: "K",
	           // values: list,
	            onChange:function(data){

	            	var from = data.from;
			    	var to = data.to;
			    	window.search_args.distance = from;// list[from] == use values: list

			    	window.ajaxSend.Search(window.search_args);
	            },
	        });
	    }
	    $(".auto-seach").on("keyup",function(event){
	   		var meta_value = this.value;
	   		var name = $(this).attr('name');
	   		if(name == 's'){
	   			window.search_args.keyword = meta_value;
	   		} else {
	   			window.search_args.metas[name] = meta_value;
	   		}
	   		window.ajaxSend.Search(window.search_args);
	    });

		$(".search_skill").on("click", function(event){
			var element = $(event.currentTarget);
			element.closest('label').toggleClass('activate');
			var check 	= $(this).is(":checked");
			var skill 	= $(this).val();
			var pos 	= $(this).attr('alt');
		    if(check) {
		       window.search_args.skills[pos] = skill;
		    } else {
				//delete window.search_args.skills.skill;
				delete window.search_args.skills[pos];
		    }
		    window.search_args.paged = 1;
		    window.ajaxSend.Search(window.search_args);

		});
		if ( typeof jQuery.fn.chosen !== 'undefined' ){
			$(".chosen-select").chosen();
		} else {
			// console.log('undefined choosen method');
		}

		if( bx_global.is_archive ){
			$('.chosen-select').on('change', function(evt, params) {

				var optionSelected = $("option:selected", this);
				//var  valueSelected = params.selected; // outdate coding;
   				var valueSelected = this.value;
   				console.log(valueSelected);
				var pos = $(this).find("option[value='"+valueSelected+"']").attr('alt');
				var title = $(this).find("option[value='"+valueSelected+"']").html();

				window.search_args.skills[pos] = valueSelected;
				skill_selected.push(window.search_args.skills[pos]);

				var templace = '<span class="full remove-skill"  pos="'+pos+'"><span class="skill-title">' +title+ '</span> <span><i  class="fa fa-times" aria-hidden="true"></i></span></span>'
				$("#selected_html").append(templace);

				$("ul.chosen-choices").find(".search-choice").remove();
				window.search_args.paged = 1;
			    window.ajaxSend.Search(window.search_args);
			});
		}

		$(document).on('click','.remove-skill', function(event){
			//event.preventDefault();
			var _this = $(event.currentTarget);
			var pos = $(_this).attr('pos');
			var remove_item = window.search_args.skills[pos];
			delete window.search_args.skills[pos];
			var new_skill_selected = [];

			$.each(skill_selected, function( index, value ) {

			  	if(value != remove_item){
			  		new_skill_selected.push(value);
			  	}
			});
			skill_selected = new_skill_selected;

			$('.chosen-select').val(new_skill_selected).trigger('chosen:updated');
			$("ul.chosen-choices").find(".search-choice").remove();
			window.ajaxSend.Search(window.search_args);
			$(_this).remove();
		});



		$(".search_cat").on("click", function(event){
			var element = $(event.currentTarget);
			element.closest('label').toggleClass('activate');
			var check 	= $(this).is(":checked");
			var cat 	= $(this).val();
			var pos 	= $(this).attr('alt');
		    if( check ) {
		       window.search_args.cats[pos] = cat;
		    } else {
				//delete window.search_args.skills.skill;
				delete window.search_args.cats[pos];
		    }
		    window.search_args.paged = 1;
		    window.ajaxSend.Search(window.search_args);
		});
		$(".search_country").on("click", function(event){
			var element = $(event.currentTarget);
			element.closest('label').toggleClass('activate');
			var check 	= $(this).is(":checked");
			var country 	= $(this).val();
			var pos 	= $(this).attr('alt');
		    if( check ) {
		       window.search_args.countries[pos] = country;
		    } else {
				//delete window.search_args.skills.skill;
				delete window.search_args.countries[pos];
		    }
		    window.search_args.paged = 1;
		    window.ajaxSend.Search(window.search_args);
		});
		$('.pagination1').on('click', function(event){
			var element = $(event.currentTarget);
			var href = element.attr('href');
			window.search_args[href] = href;
			window.ajaxSend.Search(window.search_args);
			return false;
		});
		$(document).on('click', '.list-project .pagination' , function(event) {
			var _this = $(event.currentTarget)
			var paged = _this.attr('paged');
			var href = _this.attr('href');
			window.search_args.paged=paged;
			window.search_args.href= href;
		    window.ajaxSend.Search(window.search_args);
			return false;
		});

	});
	$(document).on('click', '.port-item', function(event) {
        event.preventDefault();
        //$(this).ekkoLightbox();
        $(this).ekkoLightbox({ wrapping: false,showArrows: true });
        //return false;
    });

})( jQuery, window.ajaxSend, window.package_select );