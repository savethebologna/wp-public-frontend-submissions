<?php
/** Create the Custom Post Type**/
add_action('init', 'frontend_post_register');  

function frontend_post_register() {
    
    //Arguments to create post type.
    $args = array(  
        'label' => __('Frontend Posts'),  
        'singular_label' => __('Frontend Post'),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'frontend', 'with_front' => false), //FUTURE make frontend an option
       );  
  
  	//Register type and custom taxonomy for type.
    register_post_type( 'frontend' , $args );  
    register_taxonomy( "frontend_post_type", array("frontend"), array("hierarchical" => true, "label" => "Frontend Post Types", "singular_label" => "Frontend Post Type", "rewrite" => true, "slug" => 'frontend_post_type')); 
}//FUTURE make frontend_post_type slug an option

add_action("admin_init", "public_posts_manager_add_meta");  
  

function public_posts_manager_add_meta(){  
    add_meta_box("public_posts-meta", "More Information", "public_posts_manager_meta_options", "frontend", "normal", "high");   
}  
  

//Create area for extra fields
function public_posts_manager_meta_options(){  
	global $post; 
	$tz = date_default_timezone_get(); // get current PHP timezone
	date_default_timezone_set ( 'America/New_York' );
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
	
	$custom = get_post_custom($post->ID);
	global $fields;
?>
<style type="text/css">
</style>
<div class="public_posts_manager_extras">
<?php
	foreach( $custom as $fef_field_id => $value ){
		$field_id = str_replace('fef_', '', $fef_field_id);
		$label = $fields[$field_id]['label'];
		$active = $fields[$field_id]['active'];
		if( empty($label) || is_array($label) ) $label = $fef_field_id;
		if( $active === false ){ $disabled = 'disabled'; }else{ $disabled = ''; }
		echo "<div><label>".$label." (ID: ".$fef_field_id.")</label><input name='".$fef_field_id."' value='".$value[0]."' ".$disabled." /></div>\n";
	}
?>
	<input type="hidden" name="prevent_delete_meta_movetotrash" id="prevent_delete_meta_movetotrash" value="<?php echo wp_create_nonce(plugin_basename(__FILE__).$post->ID); ?>" />
</div>
<?php
	date_default_timezone_set($tz); // set the PHP timezone back the way it was
}
add_action('save_post', 'public_posts_manager_save_extras'); 
  
function public_posts_manager_save_extras(){  
    global $post;  
    $tz = date_default_timezone_get(); // get current PHP timezone
	date_default_timezone_set ( 'America/New_York' );
	if (!wp_verify_nonce($_POST['prevent_delete_meta_movetotrash'], plugin_basename(__FILE__).$post->ID)) { return $post_id; } //fix delete-custom-meta-on-trash bug
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){ //if you remove this the sky will fall on your head.
		return $post_id;
	}else{
		foreach( $_POST as $fef_field_id => $value ){
			$fefpos = strpos($fef_field_id, 'fef_');
			if( $fefpos === 0 ) update_post_meta($post->ID, $fef_field_id, $value);
		}
    }
	date_default_timezone_set($tz); // set the PHP timezone back the way it was
}  

add_filter("manage_edit-public_posts_columns", "public_posts_manager_edit_columns");   

/*function public_posts_manager_edit_columns($columns){ //Not ready yet.
        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => "Event Name",
            "startdate" => "Start Date",
			"enddate" => "End Date",
            "venue" => "Venue", 
			"city_state" => "City, State Zip", 
            "cat" => "Event Type",
        );

        return $columns;
}*/

?>