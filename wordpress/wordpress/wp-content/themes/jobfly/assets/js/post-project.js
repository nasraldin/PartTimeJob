( function( $ ) {
	var project_id;
	var postProject = {

		init: function() {
			$( '#submit_project' ).on('submit', this.submitProject);
			$(".chosen-select").chosen();
			this.postsubmit = [];
			this.attach_ids = [];
			var view = this;

			$(".input-pack-type").click(function(event){
				//var _this = event.currentTarget;

				$(".pack-type-item").removeClass('selected');
				$(this).closest("li").addClass('selected');
			});
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
			            {title : "Image files", extensions : "jpg,gif,png,jpeg,ico,pdf,doc,docx,zip,excel,txt"},
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
		            },
			        FilesAdded: function(up, files) {
			        	//up.disableBrowse(true);
			        },

			        Error: function(up, err) {
			            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			        },
			        FileUploaded : function(up, file, response){
			        	var obj = jQuery.parseJSON(response.response);
					    if(obj.success){

						    var new_record =  '<li class="">' + file.name +  '<span id ="'+obj.attach_id+'" class="btn-del-attachment hide">(x)</span></li>';
				            $("ul.list-attach").append(new_record);
				            view.attach_ids.push(obj.attach_id);

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
	        $(".location").on("change", function(event){
	        	console.log('keydown');
	        	var parent_id = $(this).val();
	        	var success = function(response){
	        		if(response.success){
	        			$(".sub-location").removeClass("hide");
	        			$(".sub-location").html(response.sub_locations);
	        		} else {
	        			$(".sub-location").addClass("hide");
	        			$(".sub-location").html("");
	        		}
	        	}
	        	window.ajaxSend.getSubLocations(event, parent_id, success);

	        });



		},
		submitProject: function(event) {
			event.preventDefault();
			var action = "sync_project", method = "insert";
			var form 	= $(event.currentTarget),
				data   	= {};
		    form.find(' input[type=text], input[type=number],  input[type=hidden], input[type=email],textarea,select').each(function() {
		    	var key 	= $(this).attr('name');
		        data[key] 	= $(this).val();
		    });
		    var acf = {};
		    // form.find('.acf-input-wrap input').each(function() {
		    // 	var key 	= $(this).closest(".acf-field").attr("data-key");
		    //     acf[key] 	= $(this).val();
		    // });
		    form.find('.acf-field input, .acf-field textarea, .acf-field select').each(function() {
		    	var key 	= $(this).closest(".acf-field").attr("data-key");
		        acf[key] 	= $(this).val();
		    });
		    form.find('.acf-field input input:radio:checked').each(function() {
		    	var key 	= $(this).attr('name');
		        acf[key] 	= $(this).val();
		    });
		    //data['acf'] = 1;
		    data['acf'] = acf;
		    form.find('input:radio:checked').each(function() {
		    	var key 	= $(this).attr('name');
		        data[key] 	= $(this).val();
		    });
		    var locations = [];
		    locations[0] = data['location'];
		    form.find('select.sub-location').each(function() {
		        locations[1]= $(this).val();
		    });
		    data['location'] = locations;
		    data.attach_ids = postProject.attach_ids;

		    var benefit = [];

		    form.find('.benefit').each(function() {

		        benefit.push($(this).val());
		    });

		    data['benefit'] = benefit;
		    data.attach_ids = postProject.attach_ids;


		    if(data.ID != '0'){
		    	method = 'renew';
		    }

			var successRes =  function(res){
				console.log(res);
	        	if ( res.success ) {
	        		if( res.premium_post ){
	        			console.log(res);
	        			var frm_pay = wp.template("frm_pay_premium_job");
	        			var list_package = JSON.parse( jQuery('#json_packages').html() );
	        			list_package[res.premium_post]['project_id'] = res.job;
	        			var html = frm_pay(list_package[res.premium_post]);
	        			$('#puPaymentGateways').find(".modal-content").html(html);
	        			$('#puPaymentGateways').modal().show();
	        		} else {
	        			window.location.href = res.redirect_url;
	        		}
		        } else {
		        	alert(res.msg);
		        }
	        };
			window.ajaxSend.submitPost(data, action, method, successRes);
			return false;
		},

	}
	postProject.init();


})( jQuery, window.ajaxSend );