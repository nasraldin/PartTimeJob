<?php
$p_id = isset($_GET['p_id']) ? $_GET['p_id'] : 0;
$project = array();
$lbl_btn = __('Post Job Now','boxtheme');
$skills = $cat_ids =$skill_ids = array();

?>
<form id="submit_project" class="frm-submit-project">

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

			// $cats = get_the_terms( $project, 'project_cat' );

			// if ( ! empty( $cats ) && ! is_wp_error( $cats ) ){
			// 	foreach ( $cats as $cat ) {
			// 	  	$cat_ids[] = $cat->term_id;
			// 	}

			// }
			$id_field = '<input type="hidden" value="'.$p_id.'" name="ID" />';
		}
	}
	echo $id_field;


	$symbol = box_get_currency_symbol( );
	?>
	<div class="form-group ">
	 	<h1 class="page-title"><?php if( ! $p_id){ the_title();} else { _e('Renew job','boxtheme'); } ?></h1>
	</div>
	<div class="form-group">
		<label for="job-title-input" class="col-3  col-form-label"><?php _e('Job Title:','boxtheme');?></label>
		<input class="form-control required" type="text" required name="post_title" value="<?php echo !empty($project) ? $project->post_title:'';?>"  placeholder="<?php _e('Ex: Need 2 WordPress developer expert','boxtheme');?> " id="job-title-input">

	</div>

	<div class="form-group salary-input row">
		<div class="col-md-12">
	 		<label for="budget-text-input" class="col-12 full  col-form-label"><?php printf(__('Salary for this possition?','boxtheme'), '<small>'.$symbol.'</small>');?></label>
	 	</div>
	 	<div class="col-md-6">
	 		From <input class="form-control" type="number" step="any" value="<?php echo !empty($project) ? $project->min_salary:'';?>" required name="min_salary"   placeholder="<?php printf(__('Min salary','boxtheme'), $symbol);?> " id="min_salary-text-input">
	 	</div>
	 	<div class="col-md-6">
		 	To <input class="form-control" type="number" step="any" value="<?php echo !empty($project) ? $project->max_salary:'';?>" required name="max_salary"   placeholder="<?php printf(__('Max salary','boxtheme'), $symbol);?> " id="max_salary-text-input">
		 </div>

	</div>
	<div class="form-group benefits">
		<label for="job-title-input" class="col-3  col-form-label"><?php _e('Benefits of this possition:','boxtheme');?></label>
		<div class="form-group">
			<div class="cols-sm-10">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-fw fa-lg fa-dollar"></i></span>
					<input type="text" class="form-control benefit" name="benefit" id="benefit_first"  placeholder="Ex:Additionale salary or bonus..."/>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="cols-sm-10">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-fw fa-lg fa-user-md"></i></span>
					<input type="text" class="form-control benefit" name="benefit_second" id="benefit_second"  placeholder="Ex:Company trip per year..."/>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="cols-sm-10">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-fw fa-lg fa-graduation-cap"></i></span>
					<input type="text" class="form-control benefit" name="benefit_third" id="benefit_third"  placeholder="Ex: Class to improve life skills"/>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group hide">
	 	<label for="type-text-input" class="col-form-label"><?php _e('What type of work do you require?','boxtheme');?></label>
	 	<select class="form-control chosen-select" multiple name="project_cat" data-placeholder="<?php _e('Select a category of work (optional)','boxtheme');?> ">
		    <?php
				$pcats = get_terms( array(
					'taxonomy' => 'job_cat',
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

	<div class="form-group">
	 	<label for="type-text-input" class="col-form-label"><?php _e('Location','boxtheme');?></label>
	 	<select class="form-control location"  name="location" data-placeholder="<?php _e('Select a location of your job (*)','boxtheme');?> ">
		    <?php
		    	$local_selected = get_the_terms( $project, 'location' );
				$locations = get_terms( array(
					'taxonomy' => 'location',
					'hide_empty' => false,
					'parent' => 0,
					)
				);
				if ( ! empty( $locations ) && ! is_wp_error( $locations ) ){
					echo '<option value="0">Select a location </option>';
					foreach ( $locations as $local ) {
						$selected = '';
						if ( $local->term_id ==  $local_selected ) {
							$selected = 'selected';
						}
				   		echo '<option '.$selected.' value="' . $local->term_id . '">' . $local->name . '</option>';
					}
 				}
		    ?>
	 	</select>
	 	<br />
	 	<select class="form-control hide sub-location"  name="sub_location" data-placeholder="<?php _e('Select sub location.','boxtheme');?> ">
	 	</select>
	</div>
	<div class="form-group">
		<label for="job-address-input" class="col-3  col-form-label"><?php _e('Address:','boxtheme');?></label>
		<input class="form-control " type="text"  name="address" value="<?php echo !empty($project) ? $project->address:'';?>"  placeholder="<?php _e('Ex:123 Main Street, Schenectady','boxtheme');?> " id="job-address-input">

	</div>

	<div class="form-group ">
	    <label for="skills-text-input" class="col-form-label"><?php _e('WHAT SKILLS ARE REQUIRED?','boxtheme');?></label>
	    <select class="form-control required chosen-select" name="skill" required  multiple data-placeholder="<?php _e('What skills are required?','boxtheme');?> ">
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
	              	echo '<option '.$selected.' value="' . $skill->name . '">' . $skill->name . '</option>';
	            }
	        }
	       ?>
	    </select>
	</div>

	<div class="form-group ">
	 	<label for="des-text-input" class="col-3  col-form-label"><?php _e('Job description','boxtheme');?></label>

	 	<?php wp_editor( '', 'post_content', box_editor_settings_front() ); ?>

	</div>
	<div class="form-group ">
	 	<label for="des-text-input" class="col-3  col-form-label"><?php _e('Requirement for this possition','boxtheme');?></label>

	 	<?php wp_editor( '', 'post_excerpt', box_editor_settings_front() ); ?>
	</div>
	<?php do_action('box_post_job_fields',$project);?>
	<div class="form-group hide">
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
	    	<span class="txt-term"><?php _e("By clicking 'Post Job Now', you are indicating that you have read and agree to the Terms & Conditions and Privacy Policy","boxtheme");?></span>
	    </div>
	 	<div class="col-md-5 align-right pull-right">
	    	<button type="submit " class="btn btn-action no-radius"><?php echo $lbl_btn;?></button>
	 	</div>
	</div>
</form>
<script type="text/template" id="json_packages"><?php   echo json_encode($list_package); ?></script>
<div class="hide"><?php wp_editor( '', 'postcontent', box_editor_settings() ); ?></div>