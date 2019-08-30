<?php

Class Box_Milestone  {
	public $project_id;
	public $post_type;
	public $is_private;
	function __construct(){
		$this->project_id = 0;
		$this->is_private = '';
		$this->post_type = 'milestone';
		$this->post_status = 'publish';
		add_action('init', array( $this,'init_milestone') );
		add_action('box_add_milestone_form',array($this,'box_add_milestone_field'));
		add_action('box_after_insert_job',array($this,'save_milestone_of_project'), 10 , 2);

		add_action( 'wp_enqueue_scripts', array( $this, 'add_milestone_scripts' ));
		add_action( 'show_milestone', array( $this, 'show_milestone'), 10 ,1);


	}
	function get_instance(){

	}
	function add_milestone_scripts() {

	    if( is_page_template( 'page-post-project.php') ) {
	    	wp_enqueue_script( 'milestone-js', get_template_directory_uri(). '/inc/milestones/js/front.js' ,  array( 'front' ) );
	    	wp_enqueue_style( 'milestone-css', get_template_directory_uri(). '/inc/milestones/css/milestone.css' , array( 'boxtheme-style' ), BOX_VERSION );
		}

	}
	static function init_milestone(){

	    $labels = array(
			'name'               => _x( 'Milestones', 'post type general name', 'your-plugin-textdomain' ),
			'singular_name'      => _x( 'Milestone', 'post type singular name', 'your-plugin-textdomain' ),
			'menu_name'          => _x( 'Milestones', 'admin menu', 'your-plugin-textdomain' ),
			'name_admin_bar'     => _x( 'Milestone', 'add new on admin bar', 'your-plugin-textdomain' ),
			'add_new'            => _x( 'Add New', 'milestone', 'your-plugin-textdomain' ),
			'add_new_item'       => __( 'Add New Milestone', 'your-plugin-textdomain' ),
			'new_item'           => __( 'New Milestone', 'your-plugin-textdomain' ),
			'edit_item'          => __( 'Edit Milestone', 'your-plugin-textdomain' ),
			'view_item'          => __( 'View Milestone', 'your-plugin-textdomain' ),
			'all_items'          => __( 'All Milestones', 'your-plugin-textdomain' ),
			'search_items'       => __( 'Search Milestones', 'your-plugin-textdomain' ),
			'parent_item_colon'  => __( 'Parent Milestones:', 'your-plugin-textdomain' ),
			'not_found'          => __( 'No milestones found.', 'your-plugin-textdomain' ),
			'not_found_in_trash' => __( 'No milestones found in Trash.', 'your-plugin-textdomain' )
		);

		$args = array(
			'labels'             => $labels,
	                'description'        => __( 'Description.', 'your-plugin-textdomain' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'milestone' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		register_post_type( $this->post_type, $args );

	}

	function save_milestone_of_project($project_id, $args){

		$this->project_id = $project_id;


		$mile1 = isset( $args['mile'] ) ? $args['mile'] : '';
		$mile2 = isset($args['mile2']) ? $args['mile2'] : '';
		$mile3 = isset($args['mile3']) ? $args['mmile3'] : '';
		$is_private = isset($args['is_private']) ? $args['is_private'] : '';
		if($is_private == 'yes'){
			$this->post_status  = 'private';
		}
		if( is_array( $mile1) && ! empty( $mile1) )
			$this->save_1_milestone($mile1);
		if( is_array($mile2) && ! empty( $mile2) )
			$this->save_1_milestone($mile2);
		if( is_array($mile3) && ! empty( $mile3) )
			$this->save_1_milestone($mile3);

	}
	function save_1_milestone($args){
		$p_temp = $args;
		$p_temp['post_parent'] = $this->project_id;
		$p_temp['post_type'] = $this->post_type;
		$p_temp['post_status'] = $this->post_status;

		$p_temp['post_title'] = $args['mile_title'];
		$p_temp['post_content'] = $args['mile_content'];
		$p_temp['meta_input']['key'] = 'mile_budget';
		$p_temp['meta_input']['value'] = $args['mile_budget'];


		$t =  wp_insert_post($p_temp);
		unset($p_temp);
	}

	function show_milestone($project){
		$this->project_id = $project->ID;
		$args = array(
			'post_type'=> $this->post_type,
			'post_parent'=>$this->project_id

		);
		$miles = new WP_Query($args);
		$key = 1;
		if( $miles->have_posts() ){
			echo '<h2>'.__('List Milestones','boxtheme').'</h2>';
			while ($miles->have_posts()) {
				$miles->the_post();
				global $post;
				$this->show_a_mislestone($post,$key);
				$key++;
			}
		}

	}
	function show_a_mislestone($post, $key){

		$status_icon = '<i class="fa fa-plus" aria-hidden="true"></i>';
		if( $post->post_status == 'completed' ){
			$status_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
		}
	 ?>

		<li class="milestone-row full">

			<h2><?php echo $status_icon;?> &nbsp; <?php echo $post->post_title;?><span class="f-right"><i class="fa fa-caret-down hide"></i> <i class="fa fa-angle-down"></i></span></h2>
			<p class="full milestone-content hide">
				<?php echo $post->post_content;?>
			</p>
		</li>
		<?php
	}
	function box_add_milestone_field($project){	global $symbol;?>
		<div class="wrap-milestone">

			<div id="milestone_forms" class="full">
				<div class="milestone_form">
					<div class="form-group">
						<label class="col-3  col-form-label"><?php _e('Milestone name:','boxtheme');?></label>
						<input class="form-control required " type="text" required name="mile][mile_title]" value=""  placeholder="<?php _e('Ex: Step 1.','boxtheme');?> " >
					</div>
					<div class="form-group ">
					 	<label  class="col-3  col-form-label"><?php printf(__('Budget of this milestone(%s)?','boxtheme'), '<small>'.$symbol.'</small>');?></label>
					 	<input class="form-control" type="number" step="any" value="" required name="mile][mile_budget]"   placeholder="<?php printf(__('Ex: 100','boxtheme'), $symbol);?> " >
					</div>
					<div class="form-group ">
					 	<label for="example-text-input" class="col-3  col-form-label"><?php _e('DESCRIBE OF THIS MILESTONE','boxtheme');?></label>
					 	<textarea name="mile][mile_content]" class="form-control required no-radius" required rows="6" cols="43" placeholder="<?php _e('Describe this milestone here...','boxtheme');?>"><?php echo !empty($project) ? $project->post_content :'';?></textarea>
					</div>
				</div><!-- end !-->
			</div>
			<div class="full"><label class="btn-add-milestone"><i class="fa fa-plus text-color "></i> &nbsp; Add Milestone <span>?</span></label></div>
		</div>

		<script type="text/html" id="tmpl-new_milestone_form">
			<div class="milestone_form">
				<label>Milestone {{{data.position}}}</label>
				<div class="form-group">
					<label  class="col-3  col-form-label"><?php _e('Milestone name:','boxtheme');?></label>
					<input class="form-control required" type="text" required name="mile{{{data.position}}}][mile_title]" value="<?php echo !empty($project) ? $project->milestone_name:'';?>"  placeholder="<?php _e('Ex: Step 1.','boxtheme');?> " >
				</div>
				<div class="form-group ">
				 	<label  class="col-3  col-form-label"><?php printf(__('Budget of this milestone(%s)?','boxtheme'), '<small>'.$symbol.'</small>');?></label>
				 	<input class="form-control" type="number" step="any" value="<?php echo !empty($project) ? $project->{BUDGET}:'';?>" required name="mile{{{data.position}}}][mile_budget]"   placeholder="<?php printf(__('Ex: 100','boxtheme'), $symbol);?> " >
				</div>
				<div class="form-group ">
				 	<label for="example-text-input" class="col-3  col-form-label"><?php _e('DESCRIBE OF THIS MILESTONE','boxtheme');?></label>
				 	<textarea name="mile{{{data.position}}}][mile_content]" class="form-control required no-radius" required rows="6" cols="43" placeholder="<?php _e('Describe this milestone here...','boxtheme');?>"><?php echo !empty($project) ? $project->post_content :'';?></textarea>
				</div>
			</div>
		</script>
		<?php

	}
}
new Box_Milestone();