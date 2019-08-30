var ajaxSend = {};
( function( $ ) {
	window.ajaxSend.Form = function(event, action, method, success){
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
	                action: action,
	                request: data,
	                method : method,
	        },
	        beforeSend  : function(event){
	        	console.log('beforeSend');
	        },
	        success: success,
	    });
	    return false;
	};
	window.ajaxSend.getSubLocations = function(event, parent_id, success){
		var form 	= $(event.currentTarget),
			data   	= {};

	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: 'get_sub_locations',
	                parent_id: parent_id,
	        },
	        beforeSend  : function(event){
	        	console.log('beforeSend');
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

	    $.ajax({
	        emulateJSON: true,
	        method :'post',
	        url : bx_global.ajax_url,
	        data: {
	                action: 'box_checkout',
	                request: data,
	                method : method,
	        },
	        beforeSend  : function(event){
	        	console.log('beforeSend');
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
	        	console.log('beforeSend');
	        },
	        success: success,
	    });
	    return false;
	};
	window.ajaxSend.submitPost = function(data, action, method, successRes){

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
	        	console.log('beforeSend submit Project');
	        },
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
	        	console.log('Insert message');
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
	        	//$("#ajax_result").addClass('loading');
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

					if( data.href ){
					 	//window.location.hash = data.href; // update the url
					 	//window.location.hash.split('#')[1];
					}
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
	        	console.log('Insert message');
	        },
	        success: function (){
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

})(jQuery, window.ajaxSend);