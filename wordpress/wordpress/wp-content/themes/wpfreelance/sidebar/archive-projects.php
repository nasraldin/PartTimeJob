
<div class="full search-adv">
	<div class="block full">
		<h3 class="block-title"> <?php _e('Skills','boxtheme');?>  <i class="toggle-check fa fa-sort-desc pull-right" aria-hidden="true"></i></span></h3>
		<select class="form-control required chosen-select" name="skill" required  multiple data-placeholder="<?php _e('Enter skills','boxtheme');?> ">
	       	<?php
	       	$skills = get_terms(
	       		array(
	           		'taxonomy' => 'skill',
	           		'hide_empty' => false,
	          	)
	       	);
	       if ( ! empty( $skills ) && ! is_wp_error( $skills ) ) {
	       		$i = 1;
	            foreach ( $skills as $skill ) {
	              	echo '<option  value="' . $skill->slug . '" alt="'.$i.'">' . $skill->name . '</option>';
	              	$i++;
	            }
	        }
	       ?>
	    </select>
	    <div id="selected_html"></div>
	</div>
	<div class="block full">
		<h3 class="block-title toggle-check"><?php _e('Categories','boxtheme');?>  <i class="toggle-check fa fa-sort-desc pull-right" aria-hidden="true"></i></h3>
		<ul class="list-checkbox ul-cats">
			<?php
				$terms = get_terms( array(
	                'taxonomy' => 'project_cat',
	                'hide_empty' => false,
	            ) );
	            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
	                foreach ( $terms as $key=>$term ) {
	                   echo '<li><label class="skil-item"> <input type="checkbox" name="cat" class="search_cat" alt="'.$key.'"  value="' . $term->term_id . '">' . $term->name . '<i class="fa fa-check primary-color" aria-hidden="true"></i></label></li>';
	                }
	            }
	     	?>
	    </ul>
	</div>
	<div class="block full hide">
		<h3 class="block-title"> <?php _e('Skills','boxtheme');?> <i class="toggle-check fa fa-sort-desc pull-right" aria-hidden="true"></i></h3>

	 	<ul class="list-checkbox ul-skills">

			<?php
				// $skills = get_terms( array(
	   //              'taxonomy' => 'skill',
	   //              'hide_empty' => true,
	   //          ) );
	   //          if ( ! empty( $skills ) && ! is_wp_error( $skills ) ){
	   //              foreach ( $skills as $key=>$skill ) {
	   //                 	echo '<li><label class="skil-item"> <input type="checkbox" name="skill" class="search_skill" alt="'.$key.'" value="' . $skill->term_id . '">' . $skill->name . '<i class="fa fa-check  primary-color" aria-hidden="true"></i></label></li>';
	   //              }
	   //           }
	         ?>
	 	</ul>
	</div>
		<?php if( current_user_can('manage_option') ){ ?>

	 	<ul class="list-checkbox ul-status hide">
			<li><h3> Post status</h3><small>Admin only</small></li>
			<li><label> <input type="checkbox" name="post_status" class="post_status" alt="0"  value="publish"> Publish</label></li>
			<li><label> <input type="checkbox" name="post_status" class="post_status" alt="1"  value="pending"> Pending</label></li>
			<li><label> <input type="checkbox" name="post_status" class="post_status" alt="2"  value="awarded"> Awarded</label></li>
			<li><label> <input type="checkbox" name="post_status" class="post_status" alt="3"  value="'done"> Done</label></li>
			<li><label> <input type="checkbox" name="post_status" class="post_status" alt="4"  value="'disputing"> Disputing</label></li>
			</ul>
		 <?php } ?>
		<ul>
			<li><?php _e('Budget','boxtheme');?></li>
			<li><input type="text" name="" id="range"></li>
		</ul>
		<input type="hidden" name="post_type" id="post_type" value="project">

</div> <!-- end search adv !-->
	<button class="btn btn-adv full mobile-only no-radius"><?php _e('Advance Filter','boxtheme');?></button>

