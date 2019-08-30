<div class="modal fade" tabindex="-1" role="dialog" id="modal_avatar">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<form class="frm-avatar">
	      	<div class="modal-body">
	      		<div class="demo-wrap upload-demo"  id="full_avatar">
	      			<div align="center  upload-msg">
	      				<?php
	      				global $user_ID;
	      				$avatar_url = '';
	      				$avatar_att_id = get_user_meta( $user_ID, 'avatar_att_id', true );
	      				if( empty($avatar_att_id) ){
	      					$avatar_url =  get_template_directory_uri().'/img/avatar_login.png';
	      				} else {
	      					$avatar_url = wp_get_attachment_url($avatar_att_id);
	      				}
	      				?>
	      				<div class="img-container">
	      					<div class="centar-avatar">
		              			<img id="btn-upload-avatar1" class="avatar-popup" src="<?php echo $avatar_url;?>" id="thumbnail">
		              			<span id="btn-upload-avatar" class="btn-upload-avatar"> <i class="fa fa-camera" aria-hidden="true"></i> </span>
	              			</div>
	              		</div>
						<br style="clear:both;">
					</div>
					<input type="hidden" id="avatar_att_id" name="avatar_att_id" />
	      		<!-- end test !-->
	      		</div>
	      		<div class="modal-footer">
		        <button type="button" class="btn btn-close" data-dismiss="modal"><?php _e('Close','boxtheme');?></button>
		        <button type="submit" class="btn btn-primary upload-result"><?php _e('Save changes','boxtheme');?></button>
		    	</div>
		    </div>
		</form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade modal-portfolio" tabindex="-1" role="dialog" id="modal_add_portfolio">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<form class="add-portfolio" id="modal_add_port">
	      	<div class="modal-body ">
	      		<div class="form-group">
			      	<center><h2><?php _e('Add Portfolio','boxtheme');?></h2></center>
			   	</div>
	      		<div class="form-group">
			      	<input type="text" class="post_title form-control required" required  name="post_title" id="post_title" value="" placeholder="<?php _e("Portfolio name",'boxtheme');?>" />
			      	<input type="hidden" class="form-control required"  name="post_content" value="" placeholder="<?php _e("Post content ",'boxtheme');?>" />
			      	<input type="hidden" class="form-control"  name="ID" id="port_id" value="" />
			   	</div>

			   	<div class="form-group row body-bg">
			   		<div class="col-md-12">
				      	<div id="container_file">
						   	<div class="wrap-port-img" id="pickfiles"><span class="txt-label"><img src="<?php echo get_template_directory_uri().'/img/clould-upload.png';?>"><span class="txt-lbupload"><?php _e('Select an image','boxtheme');?></span> </span></div>
						</div>
						<input type="text" class="form-control "   name="thumbnail_id" id="thumbnail_id" value="" />
					</div>
			   	</div>

	      	</div>
	      	<div class="modal-footer">
		        <button type="button" class="btn btn-close" data-dismiss="modal"><?php _e('Close','boxtheme');?></button>
		        <button type="submit" class="btn btn-primary"><?php _e('Save','boxtheme');?></button>
		    </div>
	    </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/template" id="json_list_portfolio"><?php global $list_portfolio;   echo json_encode($list_portfolio); ?></script>
<script type="text/html" id="tmpl-add_portfolio">
	<div class="modal-body ">
  		<div class="form-group">
	      	<center><h2><?php _e('Add portfolio','boxtheme');?></h2></center>
	   	</div>
  		<div class="form-group">
	      	<input type="text" class="form-control "  name="post_title" value="{{{data.post_title}}}" placeholder="<?php _e("Set title",'boxtheme');?>" />
	      	<input type="hidden" class="form-control "  name="ID" value="{{{data.ID}}}" />
	      	<input type="hidden" class="form-control "  name="thumbnail_id" value="{{{data.thumbnail_id}}}" />

	      	<input type="hidden" class="form-control "  name="post_content" value="" placeholder="<?php _e("Set title",'boxtheme');?>" />
	   	</div>

	   	<div class="form-group">
	      	<div id="container_file1">
	      		<div class="wrap-port-img" id="pickfiles12"><img src="{{{data.feature_image}}}" /></div>
			</div>
	   	</div>

  	</div>
  	<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><?php _e('Save','button');?></button>
    </div>
</script>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$(".box-tab a").click(function(event){
				var element= $(event.currentTarget);
				$(".box-tab li").removeClass('active');
				element.closest("li").addClass('active');
				var panel = element.attr('href');
				console.log(panel);
				$(panel).removeClass('fade');
				$(".tab-pane").removeClass('active');
				$(panel).addClass('active');
				return false;
			});
		})
	})(jQuery);
</script>