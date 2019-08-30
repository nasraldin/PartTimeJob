<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'bx_get_user_role') ){
	function bx_get_user_role($user = ''){
		if( empty($user) ){
			global $user_ID;
			if( ! $user_ID ){
				return 'visitor';
			}
			$user = $user_ID;
		}
		if( is_numeric($user) ) {
			$user = get_userdata($user);
		}
		$user_roles =  $user->roles ;

		$user_role  = reset( $user_roles );

		return $user_role;
	}
}
function get_conversation_id_of_user($freelancer_id, $project_id){
	global $wpdb;
	$check = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}box_conversations
		 	WHERE receiver_id = %d
		 	AND cvs_project_id = %d",
	        $freelancer_id, $project_id
        );

	$convs = $wpdb->get_row(
		$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}box_conversations
		 	WHERE receiver_id = %d
		 	AND cvs_project_id = %d",
	        $freelancer_id, $project_id
        ) );

	return  $convs;
}
function show_conversation( $freelancer_id, $project, $cvs_id = 0) {
	$project_id = $project->ID;
	$employer_id = $project->post_author;
	global $user_ID;

	$messages = BX_Message::get_instance()->get_converstaion(array('id' => $cvs_id));

	$avatar = array(
		$freelancer_id => get_avatar($freelancer_id),
		$employer_id => get_avatar($employer_id),
	);

	if(null !== $messages){
		echo '<div id="container_msg">';
		if ( $messages ){
			foreach ( $messages as $msg ){ $date = date_create( $msg->msg_date );?>
				<div class="msg-record msg-item full">
					<div class="msg-record msg-item">
						<div class="col-md-1 no-padding-right col-chat-avatar"><?php echo $avatar[$msg->sender_id]; ?></div>
						<div class="col-md-9 no-padding-right col-msg-content">
							<label><?php ?></label>
							<span class="wrap-text "><span class="triangle-border left"><?php echo $msg->msg_content;?></span></span>
						</div>
						<div class="col-md-2 col-msg-time"><span class="msg-mdate"><?php echo date_format($date,"m/d/Y");?></span></div>
					</div>
				</div><?php
			}
		}
		echo '</div>';?>
		<?php // if( $user_ID == $freelancer_id || $employer_id == $user_ID ){ // admin can not send message. ?>
			<form class="swp-send-message"  >
				<input type="hidden" name="cvs_id" value="<?php echo $cvs_id;?>">
				<input type="hidden" name="receiver_id" value="<?php echo $freelancer_id;?>">
				<input type="hidden" name="project_id" value="<?php echo $project_id;?>">
				<input type="hidden" name="method" value="insert">
				<?php if( $project->post_status != 'disputing' ){ ?>
				<textarea name="msg_content" class="full msg_content" required rows="3" placeholder="<?php _e('Leave your message here','boxtheme');?>"></textarea>

				<button type="submit" class="btn btn-send-message align-right f-right"><?php _e('Send','boxtheme');?></button>
				<?php } else {?>
				<textarea name="msg_content" disabled class="full msg_content requred" required rows="3" placeholder="<?php _e('Chat is disabled','boxtheme');?>"></textarea>
				<button type="reset" class="btn btn-send-message align-right f-right"><?php _e('Send','boxtheme');?></button>
				<?php } ?>
			</form>
			<?php
		//}
	}
	global $cvs_id;
	if( isset($messages[0]) )
		$cvs_id = $messages[0]->cvs_id;

}
function get_conversation($cvs_id){
	global $wpdb, $user_ID, $convs_id;
	$convs = get_conversation_id_of_user($freelancer_id, $project_id);

	if( !empty( $convs ) ) {
		$convs_id  = $convs->ID;
		$messages = $wpdb->get_results("
			SELECT *
			FROM {$wpdb->prefix}messages
			WHERE cvs_id = {$convs_id}"
		);

		if ( $messages ){
			$result .= '<div id="container_msg">';
			foreach ( $messages as $msg ){
				$result.= '<div class="msg-record msg-item row">';
				$result.= '<div class="row">';

				if($msg->msg_author == $user_ID){
					$result.= '<span class="msg-author f-left col-md-2">You: </span> <span class="msg-content f-left col-md-10">' .$msg->msg_content .'</span>';
				} else {
					$result.= '<span class="msg-author f-left col-md-2">User: </span> <span class="msg-content f-left col-md-10">' .$msg->msg_content .'</span>';;
				}
				$result.= '</div>';
				$result.= '</div>';
			}
			$result.= '</div>';
		}
		$result .= '
		<form class="send-message"  >
			<textarea name="msg_content" class="full" required rows="3" placeholder="Leave your message here"></textarea>
			<br />
			<button type="submit" class="btn btn-send-message align-right f-right">'._e('Send','boxtheme').'</button>
		</form>';
		return $result;

	}
}
/*
* Mofify the column_date function in core WordPress
*/
function bx_show_time( $post ) {
		$t_time = get_the_time( __( 'Y/m/d g:i:s a' ) );
		$m_time = $post->post_date;
		$time = get_post_time( 'G', true, $post );

		$time_diff = time() - $time;

		if ( $time_diff > 0 && $time_diff < MONTH_IN_SECONDS ) {
			$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
		} else {
			$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			$h_time = date(get_option('date_format'),strtotime($h_time));
		}
		/** This filter is documented in wp-admin/includes/class-wp-posts-list-table.php */
		return '<abbr title="' . $t_time . '"> Posted ' . $h_time . '</abbr>';
	}
	// overrfide function paginate_links
function box_paginate_links( $args = '' ) {
	global $wp_query, $wp_rewrite;

	// Setting up default values based on the current URL.
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	// Get max pages and current page out of the current query, if available.
	$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
	$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

	// Append the format placeholder to the base URL.
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

	// URL base depends on permalink settings.
	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

	$defaults = array(
		'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format'             => $format, // ?page=%#% : %#% is replaced by the page number
		'total'              => $total,
		'current'            => $current,
		'aria_current'       => 'page',
		'show_all'           => false,
		'prev_next'          => true,
		'prev_text'          => __( '&laquo; Previous' ),
		'next_text'          => __( 'Next &raquo;' ),
		'end_size'           => 1,
		'mid_size'           => 2,
		'type'               => 'plain',
		'add_args'           => array(), // array of query args to add
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( ! is_array( $args['add_args'] ) ) {
		$args['add_args'] = array();
	}

	// Merge additional query vars found in the original URL into 'add_args' array.
	if ( isset( $url_parts[1] ) ) {
		// Find the format argument.
		$format = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
		$format_query = isset( $format[1] ) ? $format[1] : '';
		wp_parse_str( $format_query, $format_args );

		// Find the query args of the requested URL.
		wp_parse_str( $url_parts[1], $url_query_args );

		// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
		foreach ( $format_args as $format_arg => $format_arg_value ) {
			unset( $url_query_args[ $format_arg ] );
		}

		$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
	}

	// Who knows what else people pass in $args
	$total = (int) $args['total'];
	if ( $total < 2 ) {
		return;
	}
	$current  = (int) $args['current'];
	$end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
	if ( $end_size < 1 ) {
		$end_size = 1;
	}
	$mid_size = (int) $args['mid_size'];
	if ( $mid_size < 0 ) {
		$mid_size = 2;
	}
	$add_args = $args['add_args'];
	$r = '';
	$page_links = array();
	$dots = false;

	if ( $args['prev_next'] && $current && 1 < $current ) :
		$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current - 1, $link );
		if ( $add_args )
			$link = add_query_arg( $add_args, $link );
		$link .= $args['add_fragment'];

		/**
		 * Filters the paginated links for the given archive pages.
		 *
		 * @since 3.0.0
		 *
		 * @param string $link The paginated link URL.
		 */
		$pre_number = $current - 1;
		$pre_number = max(0, $pre_number);
		$page_links[] = '<a class="prev page-numbers"  paged = "'.$pre_number.'" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['prev_text'] . '</a>';
	endif;
	for ( $n = 1; $n <= $total; $n++ ) :
		if ( $n == $current ) :
			$page_links[] = "<span aria-current='" . esc_attr( $args['aria_current'] ) . "' class='page-numbers current'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</span>";
			$dots = true;
		else :
			if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
				if ( $add_args )
					$link = add_query_arg( $add_args, $link );
				$link .= $args['add_fragment'];

				/** This filter is documented in wp-includes/general-template.php */
				$page_links[] = "<a class='page-numbers' paged = '".$n ."'  href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</a>";
				$dots = true;
			elseif ( $dots && ! $args['show_all'] ) :
				$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';
				$dots = false;
			endif;
		endif;
	endfor;
	if ( $args['prev_next'] && $current && $current < $total ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current + 1, $link );
		if ( $add_args )
			$link = add_query_arg( $add_args, $link );
		$link .= $args['add_fragment'];

		/** This filter is documented in wp-includes/general-template.php */
		$next_number = $current + 1;
		$page_links[] = '<a  paged = "'.$next_number.'" class="next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['next_text'] . '</a>';
	endif;
	switch ( $args['type'] ) {
		case 'array' :
			return $page_links;

		case 'list' :
			$r .= "<ul class='page-numbers'>\n\t<li>";
			$r .= join("</li>\n\t<li>", $page_links);
			$r .= "</li>\n</ul>\n";
			break;

		default :
			$r = join("\n", $page_links);
			break;
	}
	return $r;
}

if (  ! function_exists( 'bx_pagenate' )):

		/**
		 * paginaate the listing
		 * @version 1.0
		 * @since   1.0
		 * @author boxtheme
		 * @return  void
		 */
		function bx_pagenate( $jb_query = false, $add_query = array(), $echo = true ,$bid_paging = 0 ){
			global $wp_query;
			if ( $jb_query )
				$wp_query = $jb_query;

	        $big = 999999999; // need an unlikely integer


	        $default = array(
	        	'type' 		=> 'list',
	            'base' 		=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	            'format' 	=> '?paged=%#%',
	            'current' 	=> max( 1, get_query_var('paged') ),
	            'total' 	=> $wp_query->max_num_pages,
	        );

	        if( isset( $add_query['base']) ) {
	        	$default['base'] = $add_query['base'];
	        }
	        if( $bid_paging ){
	        	//$default['base'] = add_query_arg( array('pid'=>get_query_var('paged')), get_the_permalink() );
	        	$default['base'] = add_query_arg( 'pid', '%#%', $add_query['base']);
	        }

	        $paginate = box_paginate_links( $default);
	        $paginate = str_replace('page-numbers', 'pagination f-right', $paginate);
	        if( !$echo ){
	        	return $paginate;
	        } else {
	        	echo $paginate;
	    	}
		}
	endif;

if ( ! function_exists( 'signup_nonce_fields')){
	function signup_nonce_fields() {
		$id = mt_rand();
		echo "<input type='hidden' name='signup_form_id' value='{$id}' />";
		wp_nonce_field('signup_form_' . $id, '_signup_form', false);
	}
}

if ( ! function_exists( 'signup_nonce_check')){
	function signup_nonce_check( $request ) {
		$nonce_value = wp_create_nonce('signup_form_' . $request[ 'signup_form_id' ]);
		if ( $nonce_value != $request['_signup_form'] ){
			$response= array( 'success'=> false,
				'msg' => __('Invalid nonce field','boxtheme')
			);
			wp_send_json( $response );
			wp_die( __( 'Please try again.','boxtheme' ) );
		}
	}
}

?>