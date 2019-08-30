<div class="full post-item row">

	<div class="col-md-4 no-padding-right">
		<?php
			if( has_post_thumbnail() ){
				the_post_thumbnail( 'full' );
			}
		?>
	</div>
	<div class="col-md-8 pexcerpt no-padding-right">
		<h3 class="h3 post-title"><a class="ptitle primary-font" href="<?php the_permalink();?>"><?php the_title(); ?></a> </h3>
		<div class="full pdate"><?php the_date(); ?> | <?php the_author();?></div>

		<?php the_excerpt(); ?>
		<?php
		if(get_the_tag_list()) {
			echo '<div class="full ptag"> <span class="tag-label">'.__('Tags:','boxtheme').'</span>';
	    	echo get_the_tag_list(' <ul class="none-style inline"><li>','</li><li>','</li></ul>');
	    	echo '</div>';
		}
		?>
	</div>


</div>