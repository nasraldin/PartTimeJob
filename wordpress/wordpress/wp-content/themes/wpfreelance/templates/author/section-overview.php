<?php


global $author, $author_id,$profile_id, $profile, $user_ID;
$symbol = box_get_currency_symbol();

$skills 	= get_the_terms( $profile_id, 'skill' );
$skill_text = '';
$status = $profile->post_status;

if ( $skills && ! is_wp_error( $skills ) ){
	$draught_links = array();
	foreach ( $skills as $term ) {
		$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
	}
	$skill_text = join( "", $draught_links );
}

$url = get_user_meta($author_id,'avatar_url', true);
$projects_worked = (int) get_user_meta($author_id, PROJECTS_WORKED, true);
$earned = (int) get_user_meta($author_id, EARNED, true);
$country_slug = box_get_country_args()->slug;
$pcountry = get_the_terms( $profile_id, $country_slug );

global $post;
$post = $profile;
setup_postdata($profile);

?>
<div class="bg-section" id="section-overview">
	<div id="author-view" class=" author-view">
		<div class="full bd-bottom">
			<div class="col-md-3 update-avatar align-center no-padding-right">
	    		<?php
	    		if ( ! empty( $url ) ) { echo '<img title="'.get_the_title($profile->ID).'" alt="'.get_the_title($profile->ID).'" class="avatar" src=" '.$url.'" />';}
	    		else {	echo get_avatar($author_id);	}

	    		?>
	    	</div>
	      	<div class="col-md-9">
	      		<div class="col-md-9 col-xs-9 no-padding">
	      			<h1 class="profile-title no-margin"> <?php echo $profile->profile_name;?></h1>
	      			<div class="full clear">
		        		<h4 class="professional-title no-margin primary-color" ><?php echo !empty ($profile->professional_title) ? $profile->professional_title : __('WordPress Developer','boxtheme');?></h4>
		        	</div>
		        	<div class="full">
		        		<span class="clear block author-address"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo ! empty( $profile->address ) ? $profile->address .',' : ''; if( !empty( $pcountry ) ){ echo $pcountry[0]->name; };?></span>
		        		<span class="clear author-skills"><?php echo $skill_text;;?></span>
		        	</div>
	      		</div>
	      		<div class="col-md-3 col-xs-3 author-fre-buttons">
	      			<div class="full text-center "><span class="hour-rate"><?php echo $symbol ;?> <span><?php echo $profile->hour_rate;?></span>/hr</span></div>
	      			<div class="full  hidden-xs">
	      				<?php if( box_allow_directly_message() && $author_id != $user_ID  ){

	      						$c_id = has_directly_message($author_id);

	      						if( ! $c_id ){
	      							if( ! is_user_logged_in() ){ ?>
	      								<div class="full"><a href="<?php echo add_query_arg( 'redirect', get_author_posts_url($author_id), box_get_static_link('login')) ;?>" class="btn-action "><?php _e('Send Message','boxtheme');?></a></div>
	      							<?php } else { ?>
	      								<div class="full"><button class="btn-action btn-send-msg-js" data-toggle="modal" data-target="#directMessage" data-whatever="<?php echo $author->user_login;?>">Send Message</button></div>
	      							<?php } ?>
	      						<?php } else {

	      							$inbox = box_get_static_link('inbox');
	      							$link = add_query_arg('c',$c_id, $inbox);
	      							?>
	      						<div class="full"><a href="<?php echo $link;?>" class="btn-action "><?php _e('Send Message','boxtheme');?></a></div>
	      					<?php }
	      				} ?>
	      				<?php if(  get_role_active() == 'employer' && $author_id != $user_ID ){ ?>
	      					<div class="full"><button class="btn-action btn-modal-login btn-invite-js"  data-toggle="modal" data-target="#inviteModal" ><?php _e('Invite','boxthee');?></button></div>
	      				<?php }?>
      				</div>
	      		</div>

	      	</div>
      	</div> <!-- .full !-->

		<!-- Ovreview line !-->
		<div class="full bd-bottom">
			<div class="col-sm-8 text-justify">
				<h3>  <?php _e('About me','boxtheme');?><?php if(function_exists('box_social_link_of_profile')) box_social_link_of_profile($profile);?> </h3>
				<div class="full author-overview  second-font1"><?php the_content();?></div>
				<?php

				if( function_exists('box_show_equipment') ){ box_show_equipment($profile); }

				$video_id = get_post_meta($profile->ID, 'video_id', true);

				if( !empty( $video_id ) ){ ?>
					<div class="video-container ">
					  <iframe width="635" height="315" src="https://www.youtube-nocookie.com/embed/<?php echo $video_id;?>?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
					</div>
				<?php } ?>
			</div>
			<div class="col-md-4">

				<ul class="work-status">
					<li class="count-project-worked"><?php printf(__("<label>Job worked:</label> %d",'boxtheme'), $projects_worked);?></li>
					<li class="count-earned"><?php printf(__("<label>Total earned:</label> %s",'boxtheme'), box_get_price_format(max(0,$earned)));?></li>
			      	<li class="language-available"><label> Language:</label> English </li>
				</ul>
			</div>
		</div><!-- End Ovreview !-->
	</div> <!-- .end author-view !-->
</div> <!-- end bg section !-->