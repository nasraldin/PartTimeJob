<?php
global $post, $role;
$project = BX_Project::get_instance()->convert($post);
//the_post();
//the_title();


?>
<div class="row">
	<div class="col-md-2 no-padding-right">
		<small><?php echo get_the_date(); ?></small>
	</div>
	<div class="col-md-6">
		<h5><a href="<?php echo get_permalink($project->ID);?>"><?php echo $project->post_title;?> </a></h5>
		<?php
		if($project->post_status == DONE){
			//$bid_win_id = get_post_me($project->ID,BID_ID_WIN, true);
			$args = array(
				'post_id' => $project->ID,
				'type' => 'review',
			);
			$fre_comments = get_comments($args);
			if( !empty($fre_comments) ){

				echo '<label>'.__('Freelancer review:','boxtheme').'</label>';
				foreach($fre_comments as $comment) :

					$rating_score = get_comment_meta( $comment->comment_ID, RATING_SCORE, true );
					bx_list_start($rating_score);
					echo '<i>'.$comment->comment_content.'</i>';
				endforeach;
			} else {
				echo 'Not left a review here';
			}

		}
		?>
	</div>
	<div class="col-md-2">
		<?php // echo $bid->display_name; ?>
	</div>
	<div class="col-md-2"><small>$</small><?php echo $project->_bid_price; ?></div>
</div>