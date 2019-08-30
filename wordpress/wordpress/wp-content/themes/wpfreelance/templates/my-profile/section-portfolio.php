<div class="col-md-12 center frame-add-port edit-profile-section portfolio-section">
	<div class="full">
		<button class="btn btn-show-portfolio-modal"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; <?php _e('Add portfolio','boxtheme');?></button>
	</div>

	<div class="row-section col-md-12" id="list_portfolio">
		<!-- portfolio !-->

		<?php
		global $user_ID, $list_portfolio;
		$args = array(
			'post_type' 	=> 'portfolio',
			'author' 		=> $user_ID,
		);
		$result =  new WP_Query($args);
		$list_portfolio = array();
		if( $result->have_posts() ){
			while ($result->have_posts()) {

				$result->the_post();
				global $post;

				$post->feature_image = get_the_post_thumbnail_url($post->ID, 'full');
				$post->thumbnail_id = get_post_thumbnail_id($post->ID);
				$list_portfolio[$post->ID] = $post;

				echo '<div class="col-md-4 port-item" id="'.$post->ID.'">';
					if( has_post_thumbnail() ){
						the_post_thumbnail('full' );
					} else  {
						$att_file_id = get_post_meta($post->ID,'port_file_id', true);
						$attach_file = get_post($att_file_id);
						if( !is_wp_error($attach_file)){
							if( $attach_file->post_mime_type == 'audio/mpeg' ){
								echo '<a download href="'.wp_get_attachment_url($att_file_id).'"><img src ="'.get_template_directory_uri().'/img/mp3.jpg"></a>';
							}
						}

					}
					echo '<div class="btns-act"><span class="btn-sub-act btn-edit-port "><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>';
					echo '<span class="btn-sub-act btn-del-port" ><i class="fa fa-times" aria-hidden="true"></i></span></div>';
				echo '</div>';
			}
			wp_reset_query();
		} else {
			_e('There is no any portfolio yet','boxtheme');
		}
		?>

	</div>
	<!-- end portfolio !-->
</div>