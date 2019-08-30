<?php
//bid-loop.php
global $project, $post, $user_ID, $list_bid, $cms_setting, $bidding;

$bid = new BX_Bid();
$bid = $bid->convert( $post );


$_bid_price = $bid->_bid_price;

$pay_ifo = box_get_pay_info($_bid_price);

$bid->emp_pay = box_get_price_format($pay_ifo->emp_pay);
$bid->fre_receive = box_get_price_format($pay_ifo->fre_receive);
$bid->commission_fee = box_get_price_format($pay_ifo->cms_fee);
$bid->fre_displayname = get_the_author_link();
//$bid->fre_displayname = get_the_author();
$list_bid[$post->ID] = $bid;
$winner = 0;
$bid_class = '';
$winner_text ='';

$rating_score = get_post_meta($bid->post_author,RATING_SCORE, true);

if( empty($rating_score))
	$rating_score = 5;
$start_class = 'score-'.$rating_score;

if ( (int) $rating_score != $rating_score ){
	$start_class = 'score-'.(int)$rating_score.'-half';
}




if ( $bid->post_author == $project->{WINNER_ID} ) {
	$winner = 1;
	$bid_class = 'bid-winner';
	$winner_text = '<i class="fa fa-trophy" aria-hidden="true"></i>';
}

?>
<div class ="col-md-12 row-bid-item bid-item <?php echo $bid_class;?> bid-loop.php">
	<div class="col-md-2 no-padding-right text-center col-avatar">
		<?php echo  get_avatar($bid->post_author); ?>
		<?php  echo $winner_text;?>
	</div>
	<div class ="col-md-8 ">
		<?php
		$list_dealine  = list_dealine();
		if( empty($bid->_dealine) )
			$bid->_dealine = 0;
		?>
		<div class="full clear block">
			<h5 class="bid-title inline f-left full">
				<a class="author-url" href="<?php echo get_author_posts_url($bid->post_author , get_the_author_meta( 'user_nicename' ) ); ?>"><?php the_author(); ?> </a>
			</h5>
			<h5 class="bid-title inline f-left  primary-color full"><?php echo $bid->professional_title;?></h5>
		</div>

		<div class="full clear">
			<span><?php printf( __("Date: %s",'boxtheme'), get_the_date() ); ?></span>
		</div>
		<div class="full clear bid-content">
			<?php

			if ( user_can_see_bid_info( $bid, $project ) ) {
				the_content();
			} else {
				echo '<i>'.__('Cover letter is hidden','boxtheme').'</i>';
			}
			?>
		</div>
	</div>
	<div class ="col-md-2 no-padding-left padding-right-zero text-center">
		<span class="bid-item-price full text-center"><?php printf(__(' %s','boxtheme'), box_get_price_format($bid->_bid_price )) ?></span>
		<span class="full">
			<start class="rating-score <?php echo $start_class;?> ">
				<i class="fa fa-star" aria-hidden="true"></i>
				<i class="fa fa-star" aria-hidden="true"></i>
				<i class="fa fa-star" aria-hidden="true"></i>
				<i class="fa fa-star" aria-hidden="true"></i>
				<i class="fa fa-star" aria-hidden="true"></i>
			</start>
			<span></span>
			<!--<span class="absolute  abs-right-15"><img src="<?php // echo get_stylesheet_directory_uri();?>/img/flag.png"></span> !-->
		</span>
		<span class="bid-item-duration full text-center"><?php echo isset( $list_dealine[$bid->_dealine]) ? $list_dealine[$bid->_dealine] : '';?> </span>

			<?php
			echo "<input type='hidden' name='bid_author' class='bid_author'  value ='".$bid->post_author."' />";
			$cvs_id = is_sent_msg( $project->ID, $bid->post_author );
			if( ! $cvs_id || $cvs_id == null ){
				$cvs_id = 0;
			}
			echo "<input type='hidden' name='cvs_id' class='cvs_id' value ='".$cvs_id."' />";
			if( $user_ID == $project->post_author && $project->post_status == 'publish' ){

				if( $cvs_id ){ ?>
					<button class="btn-view-conversation btn-act-message primary-color"><!-- <img src="<?php echo get_template_directory_uri().'/img/chat.png';?>" /> -->
						<svg id="icon-send-message" class="icon-send-message primary-color" viewBox="0 0 64 64" width="100%" height="100%"><path d="M10 15h44v2H10zM10 25h44v2H10zM10 35h44v2H10z"></path><path d="M17 61.174L32.37 48H64V4H0v44h17v13.174zM2 46V6h60v40H31.63L19 56.826V46H2z"></path></svg>
						<span><?php _e('Send Message','boxtheme');?> </span>

				</button>
				<?php } else { ?>
					<button class="btn-create-conversation  btn-act-message primary-color" >
						<!-- <img src="<?php //echo get_template_directory_uri().'/img/chat.png';?>" /> -->
						<svg id="icon-send-message" class="icon-send-message primary-color" viewBox="0 0 64 64" width="100%" height="100%"><path d="M10 15h44v2H10zM10 25h44v2H10zM10 35h44v2H10z"></path><path d="M17 61.174L32.37 48H64V4H0v44h17v13.174zM2 46V6h60v40H31.63L19 56.826V46H2z"></path></svg>
						<span><?php _e('Send Message','boxtheme');?></span></button>

				<?php } ?>
			 	<button class="inline btn-status-display no-radius btn-toggle-award primary-color" id="<?php echo $bid->ID;?>" value="<?php echo $bid->post_author;?>"> <i class="fa fa-thumbs-o-up primary-colo" aria-hidden="true"></i> <?php _e('Hire Freelancer','boxtheme');?></button>
			<?php } else if ( $bidding && $bidding->ID == $bid->ID && $project->post_status == 'publish' ) { // show cancel bid for current freelancer .
				echo '<div class="full"><a class="btn-del-bid" rel="'.$bidding->ID.'">'.__('CANCEL (X)','boxtheme').' &nbsp;</a></div>';
			}?>
	</div>
</div>