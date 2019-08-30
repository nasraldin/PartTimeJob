			<div class="col-md-3 no-padding col-xs-12 col-search hide">
				<?php
					$project_link = $default =  get_post_type_archive_link(PROJECT);
					$placeholder = __('Find a job','boxtheme');
					$profile_link = get_post_type_archive_link(PROFILE);
					if( is_post_type_archive(PROFILE) ){
						$default = $profile_link;
						$placeholder = __('Find a freelancer','boxtheme');
					}
				?>
				<form class="frm-search" action="<?php echo $default;?>">
					<span class="glyphicon glyphicon-search absolute search-icon"></span>
					<select class="input-control absolute"  id="search_type">
						<option value="<?php echo $project_link;?>" alt="<?php _e('Find a job','boxtheme');?>">
							<?php _e('Job','boxtheme');?>
						</option>
						<option value="<?php echo $profile_link;?>" alt="<?php _e('Find a freelancer','boxtheme');?>">
							<?php _e('Freelancer','boxtheme');?>
						</option>
					</select>
					<input type="text" name="s" id="keyword" class="keyword full no-radius" value="<?php echo get_query_var('s');?>" placeholder="<?php echo $placeholder;?>" />
					<button class="mobile-only" type="submit"><span class="glyphicon glyphicon-search"></span></button>
				</form>
			</div>