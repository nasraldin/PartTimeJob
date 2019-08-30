<?php
$p_id = isset($_GET['p_id']) ? $_GET['p_id'] : 0;
$project = array();
$lbl_btn = __('Post Job Now','boxtheme');
$skills = $cat_ids =$skill_ids = array();

?>
<form id="submit_project" class="frm-submit-project"  >

	<?php
	$id_field =  '<input type="hidden" value="0" name="ID" />'; // check insert or renew
	if($p_id){
		global $user_ID;
		$project = get_post($p_id);

		if( $project && $user_ID == $project->post_author ){ // only author can renew or view detail of this project

			$project = BX_Project::get_instance()->convert($project);
			$lbl_btn = __('Renew Your Job','boxtheme');


			$skills = get_the_terms( $project, 'skill' );

			if ( ! empty( $skills ) && ! is_wp_error( $skills ) ){
				foreach ( $skills as $skill ) {
				  	$skill_ids[] = $skill->term_id;
				}
			}

			$cats = get_the_terms( $project, 'project_cat' );

			if ( ! empty( $cats ) && ! is_wp_error( $cats ) ){
				foreach ( $cats as $cat ) {
				  	$cat_ids[] = $cat->term_id;
				}

			}
			$id_field = '<input type="hidden" value="'.$p_id.'" name="ID" />';
		}
	}
	echo $id_field;


	$symbol = box_get_currency_symbol( );
	?>
	<div class="form-group ">
	 	<h1 class="page-title"><?php if(  !$p_id){ the_title();} else { _e('Renew project','boxtheme'); } ?></h1>
	</div>
	<div class="form-group">
		<p>
		<?php _e('Get free quotes from skilled freelancers within minutes, view profiles, ratings and portfolios and chat with them. Pay the freelancer only when you are 100% satisfied with their work.','boxtheme');?>
		</p>
	</div>
	<div class="form-group">
		<label for="post-title-input" class="col-3  col-form-label"><?php _e('Choose a name for your project','boxtheme');?></label>
		<input class="form-control" type="text"  name="post_title" value="<?php echo !empty($project) ? $project->post_title:'';?>"  placeholder="<?php _e('Ex: I want to build a website','boxtheme');?> " id="post-title-input">
	</div>

	<div class="form-group ">
	 	<label for="budget-text-input" class="col-3  col-form-label"><?php printf(__('What budget do you have in mind(%s)?','boxtheme'), '<small>'.$symbol.'</small>');?></label>
	 	<input class="form-control" type="number" step="any" min="1" value="<?php echo !empty($project) ? $project->{BUDGET}:'';?>"  name="<?php echo BUDGET;?>"   placeholder="<?php printf(__('Set your budget here(%s)','boxtheme'), $symbol);?> " id="budget-text-input">
	</div>
	<div class="form-group ">

	 	<label for="example-text-input" class="col-form-label"><?php _e('What type of work do you require?','boxtheme');?></label>
	 	<select class="form-control chosen-select " multiple name="project_cat[]"  data-placeholder="<?php _e('Select a category of work (optional)','boxtheme');?> ">
		    <?php
				$pcats = get_terms( array(
					'taxonomy' => 'project_cat',
					'hide_empty' => false,
					)
				);
				if ( ! empty( $pcats ) && ! is_wp_error( $pcats ) ){
					foreach ( $pcats as $cat ) {
						$selected = '';
						if( in_array($cat->term_id, $cat_ids) ){
							$selected = 'selected';
						}
				   		echo '<option '.$selected.' value="' . $cat->term_id . '">' . $cat->name . '</option>';
					}
 				}
		    ?>
	 	</select>
	</div>

	<div class="form-group ">
	    <label for="skills-text-input" class="col-form-label"><?php _e('What skills are required?','boxtheme');?></label>
	    <select class="form-control  chosen-select " name="skill[]" id="skill"   rel="chosen"   multiple data-placeholder="<?php _e('What skills are required?','boxtheme');?> ">
	       	<?php
	       	$skills = get_terms(
	       		array(
	           		'taxonomy' => 'skill',
	           		'hide_empty' => false,
	          	)
	       	);
	       if ( ! empty( $skills ) && ! is_wp_error( $skills ) ) {
	            foreach ( $skills as $skill ) {
	            	$selected = '';
						if( in_array($skill->term_id, $skill_ids) ){
							$selected = 'selected';
						}
	              	echo '<option '.$selected.' value="' . $skill->slug . '">' . $skill->name . '</option>';
	            }
	        }
	       ?>
	    </select>

	</div>

	<div class="form-group ">
	 	<label for="example-text-input" class="col-3  col-form-label"><?php _e('Describe your project','boxtheme');?></label>
	 	<textarea name="post_content" class="form-control  no-radius"  rows="6" cols="43" placeholder="<?php _e('Describe your project here...','boxtheme');?>"><?php echo !empty($project) ? $project->post_content :'';?></textarea>
	</div>
	<input type="hidden" name="action" value="sync_project">
	<input type="hidden" name="method" value="insert">
	<?php do_action('box_post_job_fields',$project);?>
	<?php do_action('box_add_milestone_form',$project);?>
	<div class="form-group ">
	 	<div id="fileupload-container" class="file-uploader-area">
		    <span class="btn btn-plain btn-file-uploader border-color ">

		      	<span class="fl-icon-plus"></span>
		      	<input type="hidden" class="nonce_upload_field" name="nonce_upload_field" value="<?php echo wp_create_nonce( 'box_upload_file' ); ?>" />
		      	<span id="file-upload-button-text " class="text-color"><i class="fa fa-plus text-color" aria-hidden="true"></i> <?php _e('Upload Files','boxtheme');?></span>
		      	<input type="file" name="upload[]" id="sp-upload" multiple="" class="fileupload-input">
		      	<input type="hidden" name="fileset" class="upload-fileset">
		      	<i class='fa fa-spinner fa-spin '></i>
		  	</span>

	  		<p class="file-upload-text txt-term"><?php _e('Drag drop any images or documents that might be helpful in explaining your project brief here','boxtheme');?></p>

	 	</div>
	 	<ul class="list-attach"></ul>
	 	<div id="fileupload-error" class="alert alert-error upload-alert fileupload-error hide"><?php _e('You have uploaded this file before','boxtheme');?></div>

	</div>

	<!-- 1.1 !-->
	<?php
	wp_reset_query();
	global $box_currency;
	$symbol = box_get_currency_symbol($box_currency->code);
	$args = array(
                'post_type' => '_package',
                'meta_key' => 'pack_type',
                'meta_value' => 'premium_post'
            );
        $list_package = array();
        $the_query = new WP_Query($args);

        // The Loop
        if ( $the_query->have_posts() ) { 	?>
			<div class="form-group step">
				<label for="example-upgrade-fields" class="col-3  col-form-label"><?php _e('Optional Upgrades','boxtheme');?></label>
				<ul class="none-style ul-pack-type">
					<li class="pack-type-item selected">
						<div class="col-md-1"><img src="https://www.f-cdn.com/assets/img/ppp/standard-project-icon-08417247.svg"></div>

						<div class="col-md-9">
							<label class="pay-type">
								<input type="radio" name="premium_post" class="input-pack-type " value="0" checked><?php _e('Standard Project','boxtheme');?>
								<p><?php _e('Your project will go live instantly, quotes will come in within minutes.','boxtheme');?></p>
							</label>
						</div>
						<div class="col-md-2 text-right pack-price">
			               	<span id="featured-upgrade-price" data-robots="FeaturedUpgradePrice">Free</span>
						</div>
					</li>

					<?php  while ( $the_query->have_posts() ) {

						$the_query->the_post();
						global $post;
						$price = get_post_meta(get_the_ID(),'price', true);
						$post->price = $price;
						$list_package[$post->ID] = $post;
						?>
						<li class="pack-type-item">
							<div class="col-md-1"><img src="https://www.f-cdn.com/assets/img/ppp/standard-project-icon-08417247.svg"></div>

							<div class="col-md-9">
								<label class="pay-type">
									<input type="radio" name="premium_post" class="input-pack-type " value="<?php the_ID();?>"> <?php the_title();?>
									<p class="UpgradeListing-desc  pack-type-desc"><?php the_content();?></p>
								</label>
							</div>
							<div class="col-md-2 text-right pack-price">
				                <span class="currency-sign"><?php echo $symbol;?></span><span id="featured-upgrade-price" data-robots="FeaturedUpgradePrice"><?php echo $price;?></span>
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>
	<!-- End 1.1 !-->
	<?php wp_nonce_field( 'sync_project', 'nonce_insert_project' ); ?>
	<div class="form-group row">

	 	<div class="col-md-7">
	    	<span class="txt-term"><?php
	    	$tos_link =box_get_static_link('tos');
	    	printf(__("By clicking 'Post Job Now', you are indicating that you have read and agree to the <a href='%s' target='_blank' >Terms & Conditions and Privacy Policy</a>.","boxtheme"), $tos_link);?></span>
	    </div>
	 	<div class="col-md-5 align-right pull-right">
	    	<button type="submit " class="btn btn-action no-radius btn-submit"><?php echo $lbl_btn;?> &nbsp; <i class="fa fa-spinner fa-spin"></i></button>
	 	</div>
	</div>
</form>
<script type="text/template" id="json_packages"><?php   echo json_encode($list_package); ?></script>