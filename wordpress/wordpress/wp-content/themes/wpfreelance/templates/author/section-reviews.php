<!-- Start work history !-->
<!-- Line work history !-->

<?php global $author_id, $profile;?>
<div class="bg-section" id="section-reviews">
	<div class="col-md-8" id="left-section-review">
		<div class="header-title"><h3> <?php _e('Work History and Feedback','boxtheme');?></h3></div>
		<?php

		$args = array(
			'post_type' 	=> BID,
			'author' 		=> $author_id,
			'post_status' 	=> COMPLETE,
		);
		$result =  new WP_Query($args);

		if( $result->have_posts() ){ ?>
			<div class ="full-width" >
				<div class="row row-heading">
					<div class="col-md-3 no-padding-right col-xs-3"><?php _e('Date','boxtheme');?> </div>
					<div class="col-md-7 col-xs-7"> <?php _e('Description','boxtheme');?>	</div>
					<div class="col-md-2 col-xs-2 align-right">	<?php _e('Price','boxtheme');?>	</div>
				</div> <?php

				while( $result->have_posts()){
					$result->the_post();
					global $post;
					$bid = BX_Bid::get_instance()->convert($post);
					?>

					<div class="row bid-history-item wpfreelance\template-parts\profile\list-bid-done-loop.php">
						<div class="col-md-3 no-padding-right col-xs-3">
							<small><?php echo get_the_date(); ?></small>
						</div>
						<div class="col-md-7 col-xs-7">
							<h5><a href="<?php echo $bid->project_link;?>" class="primary-color"><?php echo $bid->project_title;?> </a></h5>

						</div>
						<div class="col-md-2 align-right col-xs-2"><?php echo box_get_price_format($bid->_bid_price); ?></div>
						<div class="col-md-9 col-md-offset-3 col-xs-9">
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
								__('No reviews','boxtheme');
							} ?>
						</div>
					</div>
					<?php


				} ?>

			</div> <!-- end list_bidding !-->
			<?php
			bx_pagenate($result);
			wp_reset_query();
		} else {
			echo '<p>';	echo '<br />'; _e('There is no feedback yet.','boxtheme'); echo '</p>';
		}?>
	</div>
	<div class="col-md-4 p-activity sidebar-section-review">
		<div class="header-title"><h3 class=""> &nbsp;</h3></div> <br />
		<p> <label> Profile link</label> <br /><a class="nowrap" href="<?php get_author_posts_url($profile->post_author);?>"><?php echo get_author_posts_url($author_id);?></a> </p>
		<p> <label>Activity</label> <br /> <span>24X7 hours</span> </p>
	</div>
</div>
<!-- end history + feedback line !-->