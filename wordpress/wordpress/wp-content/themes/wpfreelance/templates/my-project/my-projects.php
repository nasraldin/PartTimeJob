<?php
/**
 * list-projects.php
 * be included in page-dashboard and list all bidded of current Employer
 * Only available for Employer or Admin account.
**/

	global $user_ID, $in_other, $active_class;
	$status = isset( $_GET['status'] ) ? $_GET['status'] : '';
	$in_other = '';
	$active_class = 'active';
	if( in_array( $status, array('disputing','pending','complete','archived','any') ) ){
		$in_other = 'active';
		$active_class = '';
	}
	?>
	<div class="my-project full">
		<div class=" full heading-top">
			<h1 class="page-title " > <?php _e('My Projects','boxtheme');?> </h1>
			<ul class="tab-heading inline">
				<li id="processing" class="<?php echo $active_class;?>"><?php _e('Work in Progress','boxtheme');?></li>
				<li id="open"><?php _e('Open','boxtheme');?></li>
				<li id="private"><?php _e('Private','boxtheme');?></li>
				<li id="other" class="<?php echo $in_other;?>"><?php _e('Other Projects','boxtheme');?></li></ul>
		</div>
		<?php get_template_part( 'templates/my-project/my-projects', 'working' ); // list-projects-working list-projects-working.php ?>
		<?php get_template_part( 'templates/my-project/my-projects', 'open' ); // list-projects-open list-projects-open.php ?>
		<?php get_template_part( 'templates/my-project/my-projects', 'private' ); // list-projects-active list-projects-active.php ?>
		<?php get_template_part( 'templates/my-project/my-projects', 'other-project' ); // list-projects-other list-projects-other.php ?>
	</div>


<script type="text/javascript">
	(function($){
		$(document).ready( function(){
			$("ul.tab-heading li").click(function(event){

				var _this = $(event.currentTarget);
				var id = _this.attr('id');
				$("ul.tab-heading li").removeClass('active');
				_this.addClass('active');

				$(".dashboard-tab").removeClass('active')
				$("#dashboard-"+id).addClass('active');
			});
		});
	})(jQuery);
</script>