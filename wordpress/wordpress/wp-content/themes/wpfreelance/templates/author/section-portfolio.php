
<?php
/**
 *@keyword: author-portfolios.php
*/
	global $author_id;
	$args = array(
		'post_type' 	=> 'portfolio',
		'author' 		=> $author_id,
		'posts_per_page' => 6
	);
	$result =  new WP_Query($args);
	$i = 1;

	if( $result->have_posts() ){ ?>
		<div class="bg-section" id="section-portfolio">

			<div class="col-md-12"> <div class="header-title"><h3><?php _e('Portfolio','boxtheme');?> </h3></div></div>
			<div class=" res-line"> <?php
				while ( $result->have_posts() ) {
					$result->the_post();
					global $post;
					if( has_post_thumbnail() ){

						$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(),'full' ); ?>

						<a href="<?php echo $thumbnail_url; ?>?image=<?php echo $i;?>" data-toggle="lightbox" data-gallery="portfolio-gallery" type="image" data-title="<?php the_title();?>" data-footer="<?php the_content();?>" class="col-md-4 port-item ">
								<img class="img-fluid" src="<?php echo $thumbnail_url;?>?image=<?php echo $i;?>" />
								<div class="full"><h5 class="h5 port-title"><?php the_title();?></h5></div>
						</a>

					<?php
					$i++;
					} else {

						$att_file_id = get_post_meta($post->ID,'port_file_id', true);
						$attach_file = get_post($att_file_id);
						if( !is_wp_error($attach_file)){
							if( $attach_file->post_mime_type == 'audio/mpeg' ){
								echo '<a download class="col-md-4 port-media-item" href="'.wp_get_attachment_url($att_file_id).'"><img class="img-fluid" src ="'.get_template_directory_uri().'/img/mp3.jpg">
								<div class="full"><h5 class="h5 port-title">'.get_the_title().'</h5></div> </a>';
							}
						}

					}
				} ?>
			</div>
		</div> <?php
	} else {
		echo '<p>';	echo '<br />';	echo '</p>';
	}
?>