<?php
global $user_ID;
$status = isset($_GET['status']) ? $_GET['status'] : 'publish';
$args = array(
	'post_type' 	=> BID,
	'author' 		=> $user_ID,
	'post_status' 	=> $status,
);
$label = array(
	'publish'=>__('List projects are bidding','boxtheme'),
	'awarded'=>__('List projects are working','boxtheme'),
	'done'  =>__('List projects done','boxtheme'),
	'disputing' => __('List project are disputed','boxtheme'),
);
?>
<h2> <?php if(isset($label[$status])) echo $label[$status]; ?> </h2>
<?php
$result =  new WP_Query($args);
if( $result->have_posts() ){ ?>
	<div class ="full-width" id="list_bidding">
		<div class="row">
			<div class="col-md-2 no-padding-right">	Date </div>
			<div class="col-md-6">	Description	</div>		<div class="col-md-2">	Client	</div>		<div class="col-md-2">	Balance </div>		</div>

	<?php

	while ( $result->have_posts() ){
		global $post;
		$result->the_post();
		get_template_part( 'template-parts/profile/list-bid-status', 'loop' );
	}
	wp_reset_query();
	echo '</div>';
	bx_pagenate($result);
}
?>