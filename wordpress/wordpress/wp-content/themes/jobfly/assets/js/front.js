( function( $ ) {

	var front = {
		init: function() {
			$(".btn-login-modal").click(this.modalLogin);
			$('#signup' ).on( 'submit', this.submitSignup );
			$('form.sign-in').on( 'submit',this.signIn );
			$('.toggle-menu' ).on( 'click', this.toggleMenu );
			$( "#search_type").on( 'change',this.setSearchUrl );
			$(".btn-del-noti").on('click',this.delNotify );
			$("#toggle-msg").on( 'click', this.setNotifySeen);
			$(".show-modal-login").on('click', this.showModalLogin);
			//$( ".pagination").on('click',this.pagingProject);
			var view = this;
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

			if( $('.chosen-select').length ){ // make sure add this class in the page is loading chosen js
			 	console.log('123 chosen');
			 	$(".chosen-select").chosen();
			} else {
				console.log(' none chosen');
			}

			$(window).scroll(function() {
			    var height = $(window).scrollTop();
			    if(height  > 0) {
			    	$("body").addClass('fixed');
			    } else {
			    	$("body").removeClass('fixed');
			    }
			});

		// for payment gateway checkout
		$(document).on('click', '.btn-select', function(event){
			var _this = $(event.currentTarget);

			_this.closest('.step ').toggleClass('selected');
			//_this.closest('form').find('.record-line').removeClass('activate');
			_this.closest('.step').find('.record-line').removeClass('activate');
			_this.closest('.record-line').addClass('activate');
			var numItems = $('div.activate').length;

			var key = _this.attr('id');
			if( _this.hasClass('btn-slect-package') ){
				var price = packages[key].price;
			}
			if ( numItems > 1 ) {
				$("button.btn-submit").removeClass('disable');
			}
		});

		$(document).on('submit', 'form.frm-main-checkout' , function(event){
			var method = '';
			var _this = $(event.currentTarget);
			var success = function(res){

				if( res.success ){

					if( !res.popup ){

						window.location.href = res.redirect_url;
					} else {
						console.log('access popup herer');
						//res.scrip_method;
						//script = $(data).text();
   						$.globalEval(res.scrip_method);
					}

				} else {
					alert(res.msg);
				}
			}
			window.ajaxSend.checkoutAct( event,  method, success );
			return false;
		});
		//end payment

	},

	toggleMenu: function(event){
		var _this = $(event.currentTarget);
		$(".profile-menu").toggleClass('hide');
	},
	modalLogin: function(event){

	},
	signIn: function(event){
		var action = 'bx_login', method ='';
		var success =  function(res){

        	if ( res.success ){
        		if( res.redirect_url  )
        			window.location.href = res.redirect_url;
        		else
        			window.location.reload(true);
	        } else {
	        	alert(res.msg);
	        }
        };
        console.log('Sign in');
        window.ajaxSend.Form(event,action,method,success);
		return false;
	},
	submitSignup: function(event){
		var action = 'bx_signup', method = 'insert';
		$("#loginErrorMsg").html('');
		$("#loginErrorMsg").addClass("hide");
		var success =  function(res){
        	if ( res.success ){
        		window.location.href = res.redirect_url;
	        } else {
	        	$("#loginErrorMsg").html(res.msg);
	        	$("#loginErrorMsg").removeClass("hide");
	        }
        };
        window.ajaxSend.Form(event,action,method,success);
		return false;
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
	showModalLogin:  function(event){
		console.log(event);
		$('#modalLogin').modal('show');
	}


}


	$(document).ready(function(){
		front.init();
		var searchForm = {keyword:'',from:0,to:1000,skills:{},post_type:'',cats:{}, countries:{},paged:1,href:0};
		var list = [0,1,2.5,5,10,20,50,100,200,1000];
		var check = 0;
		searchForm.keyword = $("#keyword").val();
		searchForm.post_type = $("#post_type").val();

		if($("#range").length) {
			$("#range").ionRangeSlider({
	            min: 0,
	            max: 100000,
	            type: 'double',
	            postfix: "k",
	            values: list,
	            onChange:function(data){

	            	var from = data.from;
			    	var to = data.to;
			    	searchForm.to = list[to];
			    	searchForm.from = list[from];
			    	window.ajaxSend.Search(searchForm);
	            },
	        });
	    }

		$(".search_skill").on("click", function(event){
			var element = $(event.currentTarget);
			element.closest('label').toggleClass('activate');
			var check 	= $(this).is(":checked");
			var skill 	= $(this).val();
			var pos 	= $(this).attr('alt');
		    if(check) {
		       searchForm.skills[pos] = skill;
		    } else {
				//delete searchForm.skills.skill;
				delete searchForm.skills[pos];
		    }
		    searchForm.paged = 1;
		    window.ajaxSend.Search(searchForm);

		});
		$(".search_cat").on("click", function(event){
			var element = $(event.currentTarget);
			element.closest('label').toggleClass('activate');
			var check 	= $(this).is(":checked");
			var cat 	= $(this).val();
			var pos 	= $(this).attr('alt');
		    if( check ) {
		       searchForm.cats[pos] = cat;
		    } else {
				//delete searchForm.skills.skill;
				delete searchForm.cats[pos];
		    }
		    searchForm.paged = 1;
		    window.ajaxSend.Search(searchForm);
		});
		$(".search_country").on("click", function(event){
			var element = $(event.currentTarget);
			element.closest('label').toggleClass('activate');
			var check 	= $(this).is(":checked");
			var country 	= $(this).val();
			var pos 	= $(this).attr('alt');
		    if( check ) {
		       searchForm.countries[pos] = country;
		    } else {
				//delete searchForm.skills.skill;
				delete searchForm.countries[pos];
		    }
		    searchForm.paged = 1;
		    window.ajaxSend.Search(searchForm);
		});
		$('.pagination1').on('click', function(){
			window.ajaxSend.Search(searchForm);
			return false;
		});
		$(document).on('click', '.list-project .pagination' , function(event) {
			var _this = $(event.currentTarget)
			var paged = _this.html();
			var href = _this.attr('href');
			searchForm.paged=paged;
			searchForm.href= href;
		    window.ajaxSend.Search(searchForm);
			return false;
		});

	});
	$(document).on('click', '.port-item', function(event) {
        event.preventDefault();
        //$(this).ekkoLightbox();
        $(this).ekkoLightbox({ wrapping: false,showArrows: true });
        //return false;
    });

})( jQuery, window.ajaxSend );