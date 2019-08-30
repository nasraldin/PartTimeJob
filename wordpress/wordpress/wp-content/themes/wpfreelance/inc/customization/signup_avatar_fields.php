<?php
    function box_upload_avatar(){

		$post_parent_id = 0;
		$request 		= $_REQUEST;
		$tmp_file 	= $_FILES['file'];

		do_action( 'box_authentication_upload' );
        global $box_general;
        if( ! $box_general->singup_avatar_field ){
            wp_die('Doest not allow perform this action.');
        }
		if ( isset( $request['post_parent']) )
			$post_parent_id = $request['post_parent'];

		if ( ! wp_verify_nonce( $request['nonce_upload_field'], 'box_upload_file' ) ) {
			wp_die( __('secutiry issues','boxtheme') );
		}


	    $upload_overrides = array( 'test_form' => false );
		$uploaded_file 	= wp_handle_upload( $tmp_file, $upload_overrides );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();
        //if there was an error quit early
        if ( isset( $uploaded_file['error'] ) ) {
        	wp_send_json( array('success'=> false, 'msg' => $uploaded_file['error'] ) );
        } elseif ( isset($uploaded_file['file']) ) {
            // The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
            $file_name_and_location = $uploaded_file['file'];
            // Generate a title for the image that'll be used in the media library
            $file_kb = (float)  round( $tmp_file['size']/1024, 1);
            if( $file_kb >= 1024 ){
            	$file_kb = round($tmp_file['size']/1024/1024, 0) . ' mb';
            } else{
            	$file_kb .= ' kb';
            }

            $file_title_for_media_library = sanitize_file_name($tmp_file['name']) . '('. $file_kb.')';
            $wp_upload_dir = wp_upload_dir();

            // Set up options array to add this file as an attachment
            global $user_ID;
            $attachment = array(
                'guid' => $uploaded_file['url'],
                'post_mime_type' => $uploaded_file['type'],
                'post_title' => $file_title_for_media_library,
                'post_content' => '',
                'post_status' => 'inherit',
                'post_author' => $user_ID
            );

            // Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails. If you wanted to attch this image to a post, you could pass the post id as a third param and it'd magically happen.

            $attach_id = wp_insert_attachment($attachment, $file_name_and_location, $post_parent_id);

            if( !is_wp_error($attach_id) ) {
            	$attachment['id'] = $attach_id;
            	require_once (ABSPATH . "wp-admin" . '/includes/image.php');
            	$attach_data = wp_generate_attachment_metadata($attach_id, $file_name_and_location);
        	    wp_update_attachment_metadata($attach_id, $attach_data);

        	    wp_send_json( array('success' => true,'file' => $attachment, 'msg' => __('Uploaded is successful','box_theme') ,'attach_id' => $attach_id ));
        	}
       	    wp_send_json( array('success' => false, 'msg' => $attach_id->get_error_message() ) );
		}
	}

add_action( 'wp_ajax_box_upload_avatar' , 'box_upload_avatar' );

add_action( 'wp_ajax_nopriv_box_upload_avatar', 'box_upload_avatar');
