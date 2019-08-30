( function( $ ) {
	function preview(img, selection) {
	    var scaleX = 100 / selection.width;
	    var scaleY = 100 / selection.height;

	    // $("#thumbnail + div > img").css({
	    //     width: Math.round(scaleX * 200) + "px",
	    //     height: Math.round(scaleY * 300) + "px",
	    //     marginLeft: "-" + Math.round(scaleX * selection.x1) + "px",
	    //     marginTop: "-" + Math.round(scaleY * selection.y1) + "px"
	    // });
	    $("#x1").val(selection.x1);
	    $("#y1").val(selection.y1);
	    $("#x2").val(selection.x2);
	    $("#y2").val(selection.y2);
	    $("#w").val(selection.width);
	    $("#h").val(selection.height);
	}

	var cropper;
	var cropBoxData;
	var canvasData;
	$(document).on('click', '#add_ig' , function(Event){

    	var left = $('#full_avatar').offset().left;

	    console.log(left);

    });
	var emtpy_html = $("#modal_add_portfolio .wrap-port-img").html();

	var profile = {
		init: function() {
			$(".frm-avatar").on('submit',this.saveAvatar);

			$( '#update_profile' ).on( 'submit', this.update_profile);
			$( '.update-profile' ).on( 'submit', this.update_profile_meta);
			$( '.update-one-meta' ).on( 'submit', this.updateOneMeta);

			$( ".add-portfolio").on( 'submit',this.addPortfolio);
			$(".chosen-select").chosen();
			$( ".btn-del-port").on( 'click',this.delPortfolio);

			// open modal
			$('.update-avatar img').on('click', function() {
			    $('#modal_avatar').modal('show');
		    });


			var list_portfolio =JSON.parse( jQuery('#json_list_portfolio').html() );
		    var add_portfolio_form = wp.template("add_portfolio");

		    $('.btn-show-portfolio-modal').on('click', function() {

		        $("#modal_add_portfolio .wrap-port-img").html(emtpy_html);
		        $("#modal_add_portfolio").find(".post_title").val('');
		        $('#modal_add_portfolio').modal('show');

		    });
		    $('.btn-edit-port').on('click', function(event) { // update
		    	var _this = $(event.currentTarget);
		    	var p_id = _this.closest(".port-item").attr('id');
		    	$("#modal_add_portfolio #post_title").val(list_portfolio[p_id].post_title);
		    	$("#modal_add_portfolio #port_id").val(list_portfolio[p_id].ID);
		    	$("#modal_add_portfolio #thumbnail_id").val(list_portfolio[p_id].thumbnail_id);
		    	$("#modal_add_portfolio .wrap-port-img").html("<img src="+list_portfolio[p_id].feature_image +" />");

		        $('#modal_add_portfolio').modal('show');
		    });
		    $(".btn-emp-edit").click(function(event){
		    	var _this = $(event.currentTarget);
		    	_this.closest("form").toggleClass('is-edit');
		    })
			$(".btn-edit-default").click(function(event){
				var _this 	= $(event.currentTarget);
				var form = _this.closest("form");

				form.find(".update").toggleClass('hide');
				form.find(".static").toggleClass('hide');
			});
			$(".btn-edit-second").click(function(event){
				var _this 	= $(event.currentTarget);
				var form = _this.closest("form");
				form.toggleClass("is-edit");
				// form.closest('form').find('.visible-default').toggleClass('invisible');
				// form.closest('form').find('.invisible-default').toggleClass('visible');
			});
			$(".btn-edit").click(function(){
				var element 	= $(event.currentTarget);
				element.closest('.block').toggleClass('update');

			});

			var uploader = new plupload.Uploader({
			    runtimes : 'html5,flash,silverlight,html4',
			    browse_button : 'pickfiles', // you can pass in id...
			    container: document.getElementById('modal_add_port'), // ... or DOM Element itself
			    url : bx_global.ajax_url,
			    filters : {
			        max_file_size : '10mb',
			        mime_types: [
			            {title : "Image files", extensions : "jpg,gif,png,jpeg,ico,pdf,doc,docx,zip,excel,txt"},
			        ]
			    },
			    multipart_params: {
			    	action: 'upload_file',
			    	method:'add_portfolio',
			    },
			    init: {
			        PostInit: function() {

			        },
			        FilesAdded: function(up, files) {

			        },
			        BeforeUpload: function(up, file) {
			        	$(up.settings.container).addClass('uploading');
		                up.disableBrowse(true);
		            },

			        Error: function(up, err) {
			            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			        },
			        FileUploaded : function(up, file, response){
			        	var obj = jQuery.parseJSON(response.response);
					    if(obj.success){
						    var new_record =  '<img src="'+obj.file.guid+'">';
						    $("#thumbnail_id").val( obj.attach_id);
				            $("#pickfiles").html(new_record);
					    } else{
					    	alert(obj.msg);
					    }
			        }
			    }
			});
			uploader.init();
			uploader.bind('FilesAdded', function(up, files) {
	            up.refresh();
	            up.start();
	        });
			var upload = new plupload.Uploader({
			    runtimes : 'html5,flash,silverlight,html4',
			    browse_button : 'btn-upload-avatar', // you can pass in id...
			    container: document.getElementById('full_avatar'), // ... or DOM Element itself
			    url : bx_global.ajax_url,
			    filters : {
			        max_file_size : '10mb',
			        mime_types: [
			            {title : "Image files", extensions : "jpg,gif,png,jpeg,ico,pdf,doc,docx,zip,excel,txt"},
			        ]
			    },
			    multipart_params: {
			    	action: 'upload_file',
			    	method: 'upload_full_avatar',
			    },
			    init: {
			        PostInit: function() {
			        },
			        BeforeUpload: function(up, file) {
			        	$(up.settings.container).addClass('uploading');
		                up.disableBrowse(true);
		            },
			        FilesAdded: function(up, files) { },

			        Error: function(up, err) {
			            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			        },
			        FileUploaded : function(up, file, response){
			        	var obj = jQuery.parseJSON(response.response);
					    if(obj.success){
				            $("#thumbnail").attr('src',obj.file.guid);
				            $(".avatar-popup").attr('src',obj.file.guid);
				            $("#avatar_att_id").val(obj.attach_id);
				            //attach_id
					    } else{
					    	alert(obj.msg);
					    }
					    up.disableBrowse(false);
			        }
			    }
			});
			upload.init();
			upload.bind('FilesAdded', function(up, files) {
	            up.refresh();
	            up.start();
	        });

		},
		saveAvatar : function(event){
			var form 	= $(event.currentTarget);

			var success = function(res){
				if( res ){
					$(".update-avatar").find(".avatar").attr('src',res.avatar_url);
					$('#modal_avatar').modal('hide');
				}
			}
			window.ajaxSend.Form(event, 'custom_avatar', 'insert', success);

			return false;
		},
		updateOneMeta: function(e){
			var form 	= $(e.currentTarget);
	  		var data   	= {};
	  		var select = {};

            form.find('input, textarea, select').each(function() {
            	var key 	= $(this).attr('name');
                data[key] 	= $(this).val();
            });
	  		$.ajax({
		        emulateJSON: true,
		        method :'post',
		        url : bx_global.ajax_url,
		        data: {
		                action: 'sync_profile',
		                request: data,
		                method : 'update_one_meta',
		        },
		        beforeSend  : function(event){
		        	console.log('bat dau submit job');
		        },
		        success : function(res){
		        	console.log(res);
		        	if ( res.success ){
		        		window.location.reload(true);

			        } else {
			        	//alert(res.msg);
			        }
		        }
	        });
			return false;
		},
		addPortfolio: function(event){

			var success = function(res){
	        	if ( res.success ){
	        		$("#list_portfolio").prepend("<div class='col-md-4 port-item' id='"+res.data.ID+"'><img src='"+res.data.feature_image+"'></div>");
	        		//$('#modal_add_portfolio').modal('show');
	        		$('#modal_add_portfolio').modal('hide');

		        } else {
		        	alert(res.msg);
		        }
			}

			window.ajaxSend.Form(event, 'sync_portfolio', 'insert', success);
			return false;
		},
		delPortfolio: function(event){
			var _this = $(event.currentTarget);
		    var p_id = _this.closest(".port-item").attr('id');

		    window.ajaxSend.Form(event, 'sync_portfolio', 'insert', success);
		    var data = {action:'sync_portfolio',method:'delete',ID:p_id};
		    var success = function(res){
	    		console.log('del ok');
	    		if(res.success){
	    			_this.closest(".port-item").remove();
	    		}

		    }
		    window.ajaxSend.Custom(data, success);
		},


		update_profile: function(e){
			var form 	= $(e.currentTarget);
	  		var data   	= {};
	  		var select = {};

            form.find('input, textarea, select').each(function() {
            	var key 	= $(this).attr('name');
                data[key] 	= $(this).val();
            });
            var umeta = {};
            form.find('.umeta').each(function() {
		        //umeta.push($(this).val());
		        umeta[$(this).attr('name')] =$(this).val();
		    });
		    data['umeta'] = umeta;
	  		$.ajax({
		        emulateJSON: true,
		        method :'post',
		        url : bx_global.ajax_url,
		        data: {
		                action: 'sync_profile',
		                request: data,
		                method : 'update',
		        },
		        beforeSend  : function(event){
		        	console.log('bat dau submit job');
		        },
		        success : function(res){
		        	console.log(res);
		        	if ( res.success ){
		        		window.location.reload(true);
			        } else {
			        	alert(res.msg);
			        }
		        }
	        });
			return false;
		},

		update_profile_meta: function(e){
			var form 	= $(e.currentTarget);
	  		var data   	= {};
	  		var select = {};

            form.find('input, textarea, select').each(function() {
            	var key 	= $(this).attr('name');
                data[key] 	= $(this).val();
            });
	  		$.ajax({
		        emulateJSON: true,
		        method :'post',
		        url : bx_global.ajax_url,
		        data: {
		                action: 'sync_profile',
		                request: data,
		                method : 'update_profile_meta',
		        },
		        beforeSend  : function(event){
		        	console.log('bat dau submit job');
		        },
		        success : function(res){
		        	console.log(res);
		        	if ( res.success ){
		        		window.location.reload(true);
			        } else {
			        	//alert(res.msg);
			        }
		        }
	        });
			return false;
		}
	}
	profile.init();

})( jQuery, window.ajaxSend );