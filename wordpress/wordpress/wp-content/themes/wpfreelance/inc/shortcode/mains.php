<?php
function show_box_categories( $atts, $content = null ) {
	$args = shortcode_atts( array(
        'hide_empty' => true,
        'style' => 1,
        // ...etc
    ), $atts );
    ob_start();
	box_list_categories($args);
	return ob_get_clean();
}
add_shortcode( 'box_categories', 'show_box_categories' );

function shortcode_list_project( $atts, $content = null ) {
	$args = shortcode_atts( array(
        'post_type' => 'project',
        'showposts' => 10,
        // ...etc
    ), $atts );
    $projects = new WP_Query($args);
    if($projects->have_posts()){
    	echo '<ul class="none-style shortcode-list-project"> ';
    	while($projects->have_posts() ):
    		$projects->the_post();	?>
    		<li class="project-item">
    			<a class="primary-color" href="<?php the_permalink();?>" > <?php the_title(); ?></a>
    		</li>
    		<?php
    	endwhile;
    	echo '</ul>';
    }

	return '<span class="caption">' . $content . '</span>';
}
add_shortcode( 'list_projects', 'shortcode_list_project' );

?>