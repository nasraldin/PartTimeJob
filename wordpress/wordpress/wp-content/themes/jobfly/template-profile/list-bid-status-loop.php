<?php
global $post, $role;
$bid = BX_Bid::get_instance()->convert($post);

?>
<div class="row bid-history-item">
	<div class="col-md-2 no-padding-right">
		<small><?php echo get_the_date(); ?></small>
	</div>
	<div class="col-md-6">
		<h5><a href="<?php echo $bid->project_link;?>"><?php echo $bid->project_title;?> </a></h5>
		<?php
		if($bid->post_status == DONE){
			$args = array(
						'post_id' => $bid->ID,
						'type' => 'review',
						'number' => 1
					);

			if($role == FREELANCER){
				// show employer comment
				$emp_comment = get_comments($args);
				if( !empty($emp_comment) ){
					echo '<div class="full rating-line">';
					$rating_score = get_comment_meta( $emp_comment[0]->comment_ID, RATING_SCORE, true );
					bx_list_start($rating_score);
					echo '<i>'.$emp_comment[0]->comment_content.'</i>';
					echo '</div>';
				} else {
					_e('Employer did not left a review','boxtheme');
				}
			} else {
				$args = array(
					'post_id' => $bid->post_parent,
					'type' => 'review',
					'number' => 1
				);
				$fre_comment = get_comments($args);
				if( !empty($fre_comment) ){
					echo '<div class="full rating-line">';
					echo '<label>'.__('Rating and review by freelancer:','boxtheme').'</label>';

						$rating_score = get_comment_meta( $fre_comment[0]->comment_ID, RATING_SCORE, true );
						bx_list_start($rating_score);
						echo '<i>'.$fre_comment[0]->comment_content.'</i>';

					echo '</div>';
				} else {
					echo _e('Not left a review here','boxtheme');
				}
			}
		}
		?>
	</div>
	<div class="col-md-2">
		<?php echo $bid->display_name; ?>
	</div>
	<div class="col-md-2"><small>$</small><?php echo $bid->_bid_price; ?></div>
</div>