<?php
/**
 * Displays top navigation
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php _e( 'Top Menu', 'twentyseventeen' ); ?>">
	<?php wp_nav_menu( array(
		'theme_location' => 'top',
		'menu_id'        => 'top-menu',
		'container' => 'div',
		'container_class' =>'menu-header'
	) ); ?>
	<i class="fa fa-bars  menu-hamburger hidden-md-up" aria-hidden="true"></i>

</nav><!-- #site-navigation -->