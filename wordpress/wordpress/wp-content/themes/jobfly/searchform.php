<div class="search-header">
	<div class="search-form-wrapper clearfix container">
		<form id="search_form" class="search-form" action="<?php echo get_post_type_archive_link( JOB );?>"  method="get">
			<div class="search_section_wrapper no_header">
				<div class="search_text_wrapper">
					<div class="ion-ios-search"></div>
					<div class="search_field_wrapper">
						<?php
						$keyword = get_query_var( 's');
						?>
						<input type="text" class="ui-widget-content ui-autocomplete-input keyword" name="s" value="<?php echo $keyword;?>" placeholder="<?php _e('Keyword skill (Java, iOS...), Job Title, Company...','boxtheme');?>" autocomplete="off">
					</div>
				</div>
			</div>
			<div class="city_section_wrapper">
				<div class="city_select_wrapper">
					<div class="ion-ios-location-outline"></div>
					<?php
					$locations = get_terms( array(
						'parent' => 0,
						'taxonomy' => 'location',
						'hide_empty' => false,
						)
					);
					echo '<select class="form-control chosen chosen-select" name="location" >';
					if ( ! empty( $locations ) && ! is_wp_error( $locations ) ){
						echo '<option value="0">Location </option>';
						$location = isset($_GET['location']) ? $_GET['location'] : '';
						foreach ( $locations as $local ) {
					   		echo '<option '.selected($location, $local->slug).'  value="' . $local->slug . '">' . $local->name . '</option>';
						}
	 				}
	 				echo '</select>';
	 				?>
				</div>
			</div>
			<button type="submit " class="btn-search"><?php _e('Search','boxtheme');?></button>
		</form>
	</div>
</div>
<div class="seach-bg"></div>