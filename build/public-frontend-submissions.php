<?php
	/*
	Plugin Name: Public Frontend Submissions for WordPress
	Plugin URI: http://dev.ionicdevelopment.com/frontendforms
	Description: Sometimes you want to collect information from all users, even those not signed in. This is for that.
	Author: Justin J. Goreschak
	Version: 0.1.0
	Author URI: http://ionicdevelopment.com
	*/
	
//Cache current directory
$dir = dirname( __FILE__ );

//OPTIONS
$publicuser = (int) get_option('fef_publicuser'); //get the user id that anonymous submissions will use as author
$fields = get_option('fef_fields'); //get all the fields set on the options page
$emailoptions = get_option('fef_emailoptions'); //grab email options
$postsettings = get_option('fef_postsettings');

require( $dir . '/options.php' ); //build admin page for options
require( $dir . '/public-posts-manager.php' ); //Create custom post type

/*----------------SHORTCODES------------------*/
add_shortcode( 'frontendform' , 'frontend_form_shortcode' );

function frontend_form_shortcode( $atts, $content ){
	//Set defaults for attributes
	extract( shortcode_atts( array(
		'form' => 0,
	), $atts, 'frontendform' ) );

	$submitform = create_frontend_post();
	
	if( isset($submitform) ){
		return $submitform;
	}else{
		return build_frontend_form();
	}
}

function build_frontend_form(){
	$fields = get_option('fef_fields'); //shortcode won't use the global variable on the frontend
	$nofields = "<p style='margin:0;padding:5px 15px 0 15px;border-left:1px solid #dedede;'>This form is empty.</p>";
	$i = 0;
	$form = "<form method='post'>";
	if( is_array($fields) ){
		foreach( $fields as $field_id => $field_data ){
			if( $field_data[active] == true ){
				$label = $field_data[label];
				$required = "";
				if( $field_data[required] == true ) $required = "required";
				$form .= "<p style='margin:0;padding:5px 15px 0 15px;border-left:1px solid #dedede;'><label for='fef_".$field_id."'>".$label.": </label><br><input size='16' name='fef_".$field_id."' placeholder='".$required."' ".$required." /></p>\n";
				$i++;
			}
			if( $i = 0 ) $form .= $nofields;
		}
	}else{
		$form .= $nofields;
	}
	$form .= wp_nonce_field( 'new-post', '_wpnonce', true, true );
	$form .= "<p style='text-align:center;padding:10px;'><input type='submit' name='fef-submit' /></p>";
	$form .= "</form>";
	return $form;
}

function create_frontend_post(){

	if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['fef-submit'] )){
		
		$fields = get_option('fef_fields'); //shortcode won't use the global variable on the frontend
		$emailoptions = get_option('fef_emailoptions'); //shortcode won't use the global variable on the frontend
		$postsettings = get_option('fef_postsettings');
		
		//Form Validation
		if( $emailoptions['active'] == true ){
			$emailfield = $emailoptions['emailfield'];
			$posteremail = $_POST[$emailfield];
			//if( $emailoptions[posteremailrequired] == true && !filter_var( $posteremail, FILTER_VALIDATE_EMAIL ) ){
			//	return "A valid email address is required.";
			//}
		}
	
		if( is_user_logged_in() ) {
			$publicuser = get_current_user_id();
		}elseif( empty($publicuser) ){
			$publicuser = 0;
		}
		
		$post = array(
			'post_title'	=> $_POST[$postsettings['title']],
			'post_content'	=> $description,
			'tags_input'	=> $tags,
			'post_status'	=> 'pending',
			'post_author'   => $publicuser,
			'post_category' => array('1'),
			'post_type'	=> 'frontend'
		);
		$new_post_id = wp_insert_post( $post );

		foreach( $_POST as $fef_field_id => $response ){ //read off each POST value
			$response = wp_strip_all_tags( $response );
			$pos = strpos($fef_field_id , 'fef_'); //keep only field values that matter to us
			if( $pos === 0 ){
				$field = str_replace( 'fef_', '', $fef_field_id ); //fef identifier
				update_post_meta( $new_post_id, $fef_field_id, $response );
			}
		}
		
		// require two files that are included in the wp-admin but not on the front end.  These give you access to some special functions below.
		require fef_get_WP_path() . "/wp-admin/includes/image.php";
		 
		// required for wp_handle_upload() to upload the file
		$upload_overrides = array( 'test_form' => FALSE );
		 
		// count how many files were uploaded
		$count_files = count( $_FILES['files'] );
		 
		// load up a variable with the upload direcotry
		$uploads = wp_upload_dir();
		 
		// foreach file uploaded do the upload
		foreach ( range( 0, $count_files ) as $i ) {
			// create an array of the $_FILES for each file
			$file_array = array(
					'name'          => $_FILES['files']['name'][$i],
					'type'          => $_FILES['files']['type'][$i],
					'tmp_name'      => $_FILES['files']['tmp_name'][$i],
					'error'         => $_FILES['files']['error'][$i],
					'size'          => $_FILES['files']['size'][$i],
			);
	 
			// check to see if the file name is not empty
			if ( !empty( $file_array['name'] ) ) {
				$fileurl = $uploads['url'] . '/' . basename( $uploaded_file['file'] );
	 
					// upload the file to the server
				$uploaded_file = wp_handle_upload( $file_array, $upload_overrides );
				$filepath = $uploaded_file['file'];
	 
					// checks the file type and stores in in a variable
				$wp_filetype = wp_check_filetype( basename( $uploaded_file['file'] ), null );
	 
					// set up the array of arguments for "wp_insert_post();"
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => preg_replace('/.[^.]+$/', '', basename( $uploaded_file['file'] ) ),
					'post_content' => '',
					'post_author' => $user,
					'post_status' => 'inherit',
					'post_type' => 'attachment',
					'post_parent' => $the_post_id,
					'guid' => $fileurl,
				);
	 
				// insert the attachment post type and get the ID
				$attachment_id = wp_insert_attachment( $attachment, $filepath );
	 
					// generate the attachment metadata
					$attach_data = wp_generate_attachment_metadata( $attachment_id, $uploaded_file );
	 
					// update the attachment metadata
					wp_update_attachment_metadata( $attachment_id,  $attach_data );
	 
					// this is optional and only if you want to.  it is here for reference only.
					// you could set up a separate form to give a specific user the ability to change the post thumbnail
					set_post_thumbnail( $the_post_id, $attachment_id );
	 
			}
		}
		
		if($emailoptions['active'] == true){
			if( $emailoptions['sendtoposter'] == true && !empty($posteremail) ) $mailto[] = $posteremail;
			if( $emailoptions['sendtoadmin'] == true && !empty($emailoptions['adminemail']) ) $mailto[] = $emailoptions['adminemail'];
			$subject = $emailoptions['subject'];
			$message = $emailoptions['message']; //html enabled, no variables yet though
			add_filter( 'wp_mail_content_type', 'set_html_content_type' ); //set mail to HTML
			function set_html_content_type() {return 'text/html';};
			$mailstatus = wp_mail($mailto,$subject,$message);
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' ); //reset mail format
			if( $mailstatus === false ) return "The form was submitted, but our confirmation system seems to be having issues.";
		}
		
		return "The form has been successfully submitted.<br>You should receive an email shortly.";
	}
	
}
// Do the wp_insert_post action to insert it
do_action('wp_insert_post', 'wp_insert_post');

function fef_get_WP_path(){ //compliments to firestats.cc
    $base = dirname(__FILE__);
    $path = false;

    if( @file_exists(dirname(dirname($base))."/wp-load.php") ){
        $path = dirname(dirname($base));
    }elseif( @file_exists(dirname(dirname(dirname($base)) )."/wp-load.php")){
        $path = dirname(dirname(dirname($base)));
    }

    if( $path != false ){
        $path = str_replace("\\", "/", $path);
    }
	
    return $path;
}

function fef_echo($var){
	if( isset($var) ) echo $var;
}

?>