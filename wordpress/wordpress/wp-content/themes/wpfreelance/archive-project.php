<?php
/**
 * @key:archive-project.php
 */
get_header(); ?>

<div class="container site-container">
	<div class="row"  id="content" >
		<div class="set-bg full">
			<div class="col-md-3 sidebar sidebar-search set-bg box-shadown" id="sidebar">
				<?php get_template_part( 'sidebar/archive', 'projects' ); ?>
			</div>
			<div class="col-md-9 no-padding-right" id="right_column">
				<div class="full set-bg box-shadown">
					<div class="col-md-12" id = "search_line">
						<form action="<?php echo get_post_type_archive_link(PROJECT);?>" class="full frm-search">
							<div class="input-group full">
						       <input type="text" name="s" id="keyword" required  placeholder="<?php _e('Search...','boxtheme');?>" value="<?php echo get_search_query();?>" class="form-control required" />
						       <div class="input-group-btn">  <button class="btn btn-info primary-bg"><i class="fa fa-search" aria-hidden="true"></i></button> </div>
						   </div>
						</form>
						<div class="full hide" id="count_results">
							<h5> &nbsp;<?php printf( __('There are %s jobs.','boxtheme'), $wp_query->found_posts )?>	</h5>
						</div>
					</div>

					<div class="list-project" id="ajax_result">
						<?php
							if( have_posts() ): ?>
								<div class="col-md-12 count-result"><div class="full"><h5><?php printf(__('There are %s projects.','boxtheme'),$wp_query->found_posts);?></h5></div><div></div></div>
								<?php
								while( have_posts() ): the_post();
									get_template_part( 'templates/project/project', 'loop' );
								endwhile;
								bx_pagenate();

							else:?>
								<div class="col-md-12 count-result"><div class="full"><h5><?php _e('0 jobs found','boxtheme');?></h5></div><div></div></div>

							<?php

							endif;
							wp_reset_query();
						?>
					</div>
				</div> <!-- set bg !-->
			</div><!-- end .col-md-9 !-->
		</div> <!-- set-bg !-->
	</div> <!-- .row !-->
</div>


<script type="text/template" data-template="project_template">
    <a href="${url}" class="list-group-item">
        <table>
            <tr>
                <td><img src="${img}"></td>
                <td><p class="list-group-item-text">${title}</p></td>
            </tr>
        </table>
    </a>
</script>
<?php
$premium_type = box_get_premium_types();
?>
<script type="text/html" id="tmpl-search-record">
	<div class="project-loop-item">
		<div class="col-md-12">
			<h3 class="project-title "><a class="primary-color second-font" href="{{{data.guid}}}">{{{data.post_title}}}</a></h3>
			<# if(data.priority){#>
				<span id="premium_type">
				<# if( data.priority == 3 ){ #>
				<?php echo $premium_type[3];?>
				<#} else if ( data.priority == 5) { #>
				<?php echo $premium_type[5];?>
				<# } #>
			</span>
			<# } #>
		</div>
		<div class="col-md-12 project-second-line">
			<span class="text-muted display-inline-block m-sm-bottom m-sm-top">
			   	<span class="js-type"><?php _e('Fixed Price','boxtheme');?></span>
		        <span class="js-budget"> - <span  data-itemprop="baseSalary">{{{data.budget_txt}}} </span></span>
			</span>
			<span class="js-posted"> - <time>{{{data.time_txt}}}</time></span>
		</div>
		<div class="col-md-12 project-third-line">{{{data.short_des}}}</div>
		<div class="col-md-12 employer-info">
			<span>
	            <strong class="text-muted display-inline-block m-sm-top">Client:</strong>
				<span class="inline"><span class="client-spendings display-inline-block">{{{data.total_spent_txt}}}</span></span>
				<span  class="nowrap">
					<span class="glyphicon glyphicon-md air-icon-location m-0"></span>
				    <span class="text-muted client-location"><i class="fa fa-map-marker" aria-hidden="true"></i> {{{data.location_txt}}}</span>
				</span><!---->
	        </span>
		</div> <!-- . employer-info !-->
	</div>
</script>
<script type="text/javascript">
	(function($){
		var h_right = $("#right_column").css('height'),
			h_left = $("#sidebar").css('height');
			$(".list-project").css('min-height',h_left);
		if( parseInt(h_left) > parseInt(h_right) ){
			$(".list-project").css('height',h_left);
		}
	})(jQuery);
</script>
<?php  get_footer();