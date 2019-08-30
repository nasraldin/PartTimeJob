var ajaxSend = {}, gateways = {}, show_dropdow = 0, package_select = 0, box_map  = {}, search_args = {keyword:'',metas:{},distance:0, from:0,to:1000, lat_address:'',long_address:'',skills:{},post_type:'',cats:{}, countries:{}, paged:1, href:''};
( function( $ ) {
	console.log(window.ajaxSend);
	console.log(window.gateways);
	window.ajaxSend.Form = function(event, action, method, success){
		var form 	= $(event.currentTarget),
			data   	= {};
	    form.find(' input[type=text],input[type=url], input[type=hidden],input[type=email], input[type=number], input[type=date],input[type=password],  input[type=checkbox],textarea,select').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });

	    form.find('input:radio:checked').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });

	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: action,
	                request: data,
	                method : method,
	        },
	        beforeSend  : function(event){
	        	form.find(".btn-submit").addClass("loading");

	        },
	        success: success,
	    });
	    return false;
	};

	window.ajaxSend.checkoutAct = function(event,  method, success){
		var form 	= $(event.currentTarget),
			data   	= {};
	    form.find(' input[type=text], input[type=hidden],input[type=email], input[type=number], input[type=date],input[type=password],  input[type=checkbox],textarea,select').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });

	    form.find('input:radio:checked').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });
	    var pack_id = $(".select-package").find("input:checked").val();

	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: 'box_checkout',
	                request: data,
	                method : method,
	                pack_id : pack_id,
	        },
	        beforeSend  : function(event){

	        	form.find(".btn-submit").addClass("loading");
	        },
	        success: success,
	    });
	    return false;
	};
	window.ajaxSend.membershipCheckout = function(event,  method, success){
		var form 	= $(event.currentTarget),
			data   	= {};
	    form.find(' input[type=text], input[type=hidden],input[type=email], input[type=number], input[type=date],input[type=password],  input[type=checkbox],textarea,select').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });

	    form.find('input:radio:checked').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });

	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: 'box_membership_checkout',
	                request: data,
	                method : method,
	        },
	        beforeSend  : function(event){
	        	form.find(".btn-submit").addClass("progressing");
	        },
	        success: success,
	    });
	    return false;
	};

	window.ajaxSend.awardJob = function(event, project_id, success){
		var form 	= $(event.currentTarget),
			data   	= {};
	    form.find(' input[type=text], input[type=hidden],input[type=email], input[type=number], input[type=date],input[type=password],  input[type=checkbox],textarea,select').each(function() {
	    	var key 	= $(this).attr('name');
	        data[key] 	= $(this).val();
	    });


	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: 'award_project',
	                request: data,
	                method : 'award',
	        },
	        beforeSend  : function(event){
	        },
	        success: success,
	    });
	    return false;
	};
	window.ajaxSend.submitPost = function(data, action, method, successRes, beforeSend){

	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: data,
	        // data: {
	        //         action: action,
	        //         request: data,
	        //         method : method,
	        // },
	        beforeSend  :beforeSend ,
	        success: successRes,
	    });
	    return false;
	};

	window.ajaxSend.Custom = function(data, success){
	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: data.action,
	                request: data,
	                method : data.method,
	        },
	        beforeSend  : function(event){

	        },
	        success: success,
	    });
	    return false;
	};


	window.ajaxSend.customLoading = function(data, beforeSend, success){
	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: data.action,
	                request: data,
	                method : data.method,
	        },
	        beforeSend  : beforeSend,
	        success: success,
	    });
	    return false;
	};
	window.ajaxSend.Search = function(data){

		if( window.ajaxSend.template == null ){
			window.ajaxSend.template = wp.template( 'search-record' );
		}

	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url: bx_global.ajax_url,
	        data: {
                action: 'sync_search',
                request: data,
	        },
	        beforeSend  : function(event){

	        	$(".count-result-static").hide();

	        	if( bx_global.is_archive_profile){
	        		$(".search-adv").addClass('processing');
	        	}
	        },
	        success: function(res){
	        	$("#ajax_result").html('');
	        	if( res.job_found ){
	        		$("#ajax_result").html('<div class="col-md-12 count-result"><div class="full">'+res.job_found+'</div><div>');
	        	}

	        	$.each(res.result, function (index, value) {

					$("#ajax_result").append( window.ajaxSend.template( value ) );
				});
				//$("#ajax_result").removeClass('loading');
				if( res.pagination ){
					$("#ajax_result").append( res.pagination );

					if( res.href ){
					 	//window.location.hash = data.href; // update the url
					 	//window.location.hash.split('#')[1];
					 	window.history.pushState("object or string", "Title", res.href);
					}
				}

				if( typeof(window.box_map.renderResults) != 'undefined'){
				 	if(  ! jQuery.isEmptyObject( res.result )  ){
						window.box_map.renderResults(res.result);
					} else {
						window.box_map.resetMap(window.search_args.lat_address, window.search_args.long_address );
					}
				}
				if( bx_global.is_archive_profile){
	        		$(".search-adv").removeClass('processing');
	        	}
	        },
	    });
	};
	window.ajaxSend.socialSubmit = function(data,success){
		$.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: 'social_signup',
	                request: data,
	        },
	        beforeSend  : function(event){

	        },
	        success: function (res){
	        	if ( res.success){

			    	if(res.redirect_url){
			    		window.location.href = res.redirect_url;
			    	} else {
			    		window.location.href = bx_global.home_url;
			    	}
			    } else {
			    	if(res.redirect_url){
			    		window.location.href = res.redirect_url;
			    	} else {
			    		alert(res.msg);
			    	}
			    }
	        },
	    });
	    return false;


	}

})(jQuery, window.ajaxSend, window.show_dropdow, window.package_select, window.gateways);