
<?php
global $user_ID;
$status = isset($_GET['status']) ? $_GET['status'] : 'publish';
$args = array(
	'post_type' 	=> PROJECT,
	'author' 		=> $user_ID,
	'post_status' 	=> $status,
	'paged' 	=> max( 1, get_query_var('paged') ),
);
$label = array(
	'publish'=>__('List project are publish','boxtheme'),
	'awarded'=>__('List project are working','boxtheme'),
	'done'  =>__('List project are done','boxtheme'),
);
?>
<h2> <?php if(isset($label[$status])) echo $label[$status]; ?> </h2>
<?php
$result =  new WP_Query($args);
if( $result->have_posts() ){ ?>
	<div class ="full-width" id="list_bidding">
		<div class="row">
			<div class="col-md-2 no-padding-right">	Date </div>
			<div class="col-md-6">	Description	</div>
			<div class="col-md-2">	Client	</div>
			<div class="col-md-2">	Balance </div>
		</div>

	<?php
	while ( $result->have_posts() ){
		$result->the_post();
		get_template_part( 'template-parts/profile/list-project-status', 'loop' );
	}
	echo '</div>';
	bx_pagenate($result);
}
?>