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
        'showposts' => 10,
        'post_status'=> 'publish',
        // ...etc
    ), $atts );
    $type = isset($atts['type']) ? $atts['type'] : '';
    if($type == 'urgent'){
        $args['meta_query'] = array(
            array(
                'key'     => 'priority',
                'value'   => '5'
            ),
        );
    } else if($type == 'featured' ){
        $args['meta_query'] = array(
            array(
                'key'     => 'priority',
                'value'   => '3'
            ),
        );
    }
    $args['post_type'] = 'project';
    $projects = new WP_Query($args);
    ob_start();
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

	return ob_get_clean();
}
add_shortcode( 'list_projects', 'shortcode_list_project' );

function shortcode_list_proriles( $atts, $content = null ) {
    $args = shortcode_atts( array(
        'showposts' => 10,
        'itemsperrow' => 2,
        'post_status' => 'publish',
        'showdes' => false,
        // ...etc
    ), $atts );

    $args['post_type'] = 'profile';

    $profiles = new WP_Query($args);

    global $itemsperrow, $showdes;
    $itemsperrow = $args['itemsperrow'];
    $showdes =  ( $args['showdes'] == 'true' || $args['showdes'] == 1 || $args['showdes'] == '1' ) ? true : false;

    ob_start();
    if($profiles->have_posts()){
        echo '<ul class="none-style shortcode-list-profiles"> ';
        while($profiles->have_posts() ):
            $profiles->the_post();
            get_template_part( 'templates/profile/profile', 'inline-loop' );
        endwhile;
        echo '</ul>';
    }

    return ob_get_clean();
}
add_shortcode( 'list_profiles', 'shortcode_list_proriles' );

?>