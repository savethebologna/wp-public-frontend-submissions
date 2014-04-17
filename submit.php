if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {


	$title =  wp_strip_all_tags( $_POST['title'] );
	$description = wp_strip_all_tags( $_POST['description'] );
	$tags = $_POST['post_tags'];
	$startdate = $_POST["startdate"];
	$enddate = $_POST["enddate"];
	$rawhosturl =  $_POST['hosturl'];
	$contactemail = $_POST["contactemail"];
	$emailto = array( $contactemail, 'submission@ionicevents.com');
	$subject = "Event Submitted";
	$message = "Greetings!<p>Thank you for submitting an event to Ionic Events. You will be notified when your event is approved. Event: </p><p>Thank you,<br>The Ionic Events Team<p>";
	
	if( !is_user_logged_in() ) {
	// not logged in, redirect to login form.
	$user = '2';
	} else {
	// is logged in, show form here.
	$user = get_current_user_id();
	}
	
	if(strpos($hosturl, 'http://') !== false) {
	$http = 'http://';
	$hosturl = implode('',array($http,$rawhosturl));
	} else {
	$hosturl = $rawhosturl;
	}
	
	// Add the content of the form to $post as an array
	$post = array(
		'post_title'	=> $title,
		'post_content'	=> $description,
		'tags_input'	=> $tags,
		'post_status'	=> 'pending',
		'post_author'   => $user,
		'post_category' => array('1'),
		'post_type'	=> 'event'  // Use a custom post type if you want to
	);
	$the_post_id = wp_insert_post( $post );
	__update_post_meta($the_post_id, "street", $_POST["street"]); 
	__update_post_meta($the_post_id, "city_state", $_POST["city_state"]);
	__update_post_meta($the_post_id, "feature", $_POST["feature"]);
	__update_post_meta($the_post_id, "venue", $_POST["venue"]);
	__update_post_meta($the_post_id, "ubuilding", $_POST["ubuilding"]);
	__update_post_meta($the_post_id, "cost", $_POST["cost"]);
	__update_post_meta($the_post_id, "room", $_POST["room"]);
	__update_post_meta($the_post_id, "startdate", strtotime($startdate));
	__update_post_meta($the_post_id, "enddate", strtotime($enddate));
	__update_post_meta($the_post_id, "host", $_POST["host"]);
	__update_post_meta($the_post_id, "hosturl", $_POST["hosturl"]);
	__update_post_meta($the_post_id, "contactemail", $contactemail);
	__update_post_meta($the_post_id, "startdate2", $startdate);
	__update_post_meta($the_post_id, "enddate2", $enddate);

// require two files that are included in the wp-admin but not on the front end.  These give you access to some special functions below.
require $_SERVER['DOCUMENT_ROOT'] . "/wp-admin/includes/image.php";
 
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
	wp_mail($mailto,$subject,$message);
}
   
// Do the wp_insert_post action to insert it
do_action('wp_insert_post', 'wp_insert_post');