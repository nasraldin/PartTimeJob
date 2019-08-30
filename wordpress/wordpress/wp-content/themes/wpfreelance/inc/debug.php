<?php
function box_debug_show(){?>
	<a target="_blank" href="<?php echo home_url().'/wp-content/box_log.txt';?>">Log file</a>
	<?php
}
//add_action('wp_footer','box_debug_show');