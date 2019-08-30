<?php
/**
 *	Template Name: My Projects
 */
?>
<?php get_header(); ?>

<div class="full-width box-define-row dashboard-area">
	<?php box_header_link_in_dashboard();?>
	<div class="container site-container">
		<div class="row site-content" id="content" >
			<div class="col-md-12 wrap-myjob">
					<?php
					if( is_user_logged_in() ){
						global $user_ID;
						get_template_part( 'templates/my-project/my', 'projects' ); //list-projects.php

					} else {
						_e('This content only availble for user logged in','boxtheme');
					}

					?>

			</div> <!-- end left !-->

		</div>
	</div>
</div>
<style type="text/css">
	.dashboard-filter{
		width: 100%;
	}
	.dashboard-filter select{
		border-radius: 5px;

	}
</style>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$( ".dashboard-filter select" ).change(function(ev) {
			  	console.log(ev);
			  	var url = $(this).find(":checked").val();
			  	window.location.href = url;
			});
		})
	})(jQuery);
</script>
<?php get_footer();?>

