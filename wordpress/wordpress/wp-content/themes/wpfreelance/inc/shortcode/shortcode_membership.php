<?php
function box_html_membership(){
	wp_reset_query();
	$buy_link = box_get_static_link('membership');

	$pack_type = 'membership';

	$args = array(
		'post_type' => '_package',
		'posts_per_page' =>3,
		'meta_key' => 'pack_type',
		'meta_value' => $pack_type,
	);

	$result = new WP_Query($args);
	if( $result->have_posts() ){ ?>
		<section class="full-width package-plan1">
			<div class="container">
				<div class="row"><?php

						while( $result->have_posts() ){
							$result->the_post();
							$price = get_post_meta(get_the_ID(),'price', true); ?>
							<div class="col-md-4 package-item">
								<div class="pricing-table-plan">
									<header data-plan="basic" class="pricing-plan-header basic-plan"><span class="plan-name"><?php the_title();?></span></header>
						    		<div class="plan-features">
							    		<span class="plan-monthly primary-color"><?php echo box_get_price_format($price);?></span>
							    		<span class="pack-des">	<?php the_content();?> </span>
									</div>
									<?php
										
						            	$membership_link = box_get_static_link("membership");
						            	$link = add_query_arg( array('id' =>get_the_ID() ), $membership_link ); ?>
						            	<a class="btn btn-primary btn-xlarge " href="<?php echo esc_url($link);?>"><?php _e('Get it Now','boxtheme');?></a>

								</div>
							</div>
						<?php } ?>


				</div> <!-- end row !-->
			</div>
		</section>
	<?php }
}
function box_membership_plans( $atts, $content = null ) {
	$args = shortcode_atts( array(
        'hide_empty' => true,
        'style' => 1,
        // ...etc
    ), $atts );
    ob_start();
	box_html_membership($args);
	return ob_get_clean();
}
add_shortcode( 'box_membership_plans', 'box_membership_plans' );