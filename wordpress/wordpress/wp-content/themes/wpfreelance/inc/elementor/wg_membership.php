<?php
/**
 * Adds Foo_Widget widget.
 */
class Box_MemberShip_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Box_MemberShip_Widget', // Base ID
			esc_html__( 'BoxTheme MemberShip Plans', 'boxtheme' ), // Name
			array( 'description' => esc_html__( 'MemberShip Plans', 'boxtheme' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $widget_title;
		if ( ! empty( $instance['title'] ) ) {
			$widget_title = $instance['title'];
		} else{
			$widget_title = __('Looking for Professional Freelancers?','boxtheme');
		}

		?>
		<?php echo do_shortcode('[box_membership_plans]'); ?>
		<?php
		//echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Looking for Professional Freelancers?', 'text_domain' );
		?>
		 <p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<?php

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Foo_Widget


// register Foo_Widget widget
function Register_MemberShip_Widget() {
    register_widget( 'Box_MemberShip_Widget' );
}
//add_action( 'widgets_init', 'Register_MemberShip_Widget' );