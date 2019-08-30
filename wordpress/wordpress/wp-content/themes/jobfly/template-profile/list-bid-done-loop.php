<?php
global $post;
$bid = BX_Bid::get_instance()->convert($post);

?>
<div class="row bid-history-item">
	<div class="col-md-3 no-padding-right">
		<small><?php echo get_the_date(); ?></small>
	</div>
	<div class="col-md-7">
		<h5><a href="<?php echo $bid->project_link;?>" class="primary-color"><?php echo $bid->project_title;?> </a></h5>

	</div>
	<div class="col-md-2 align-right"><?php echo box_get_price_format($bid->_bid_price); ?></div>
	<div class="col-md-9 col-md-offset-3">
	<?php

		$args = array(
			'post_id' => $bid->ID,
			'type' => 'emp_review',
			'number' => 1,
		);
		// show employer comment
		$comment = get_comments($args);
		if( !empty($comment) ){
			echo '<div class="full rating-line">';
				$rating_score = get_comment_meta( $comment[0]->comment_ID, RATING_SCORE, true );
				bx_list_start($rating_score);
				echo '<i class="emp-review-bid">'.$comment[0]->comment_content.'</i>';
			echo '</div>';
		} else {
			__('Employer did not left a review','boxtheme');
		}
		?>
	</div>

</div>
