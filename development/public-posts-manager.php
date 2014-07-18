<?php
//Create the Custom Post Type
add_action('init', 'orientation_log_register');
function orientation_log_register() {  
    
    //Arguments to create post type.
    $args = array(  
        'label' => __('Orientation Log'),
        'singular_label' => __('Student Consultant'),
        'public' => true,
		'exclude_from_search' => true,
        'show_ui' => true,
		'menu_icon' => 'dashicons-clipboard',
		'capability_type' => 'page',
		'capabilities' => array(
			'edit_post'          => 'switch_themes',
			'read_post'          => 'delete_pages',
			'delete_post'        => 'delete_pages',
			'edit_posts'         => 'delete_pages',
			'edit_others_posts'  => 'switch_themes',
			'publish_posts'      => 'switch_themes',
			'read_private_posts' => 'delete_pages'
		),
        'has_archive' => true,
        'supports' => array('title', 'editor'),
        'rewrite' => array('slug' => 'consultant', 'with_front' => false),
       );
	   
  	//Register post type.
    register_post_type( 'consultant' , $args );
}

//Add custom fields
add_action("admin_init", "orientation_log_add_meta");  

function orientation_log_add_meta(){  
    add_meta_box("consultant-meta", "Module Progress", "orientation_log_meta_options", "consultant", "normal", "high");   
}

//Create area for extra fields
function orientation_log_meta_options(){  
        global $post; 
		
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
		
?>
<style type="text/css">
.orientation_log_extras{
padding-left:5px;
}
.orientation_log_extras div{
margin: 10px 0;
font-size:1em;
min-height:2em;
}
.orientation_log_extras div label,.orientation_log_extras div p{
width: 180px;
font-size:1.3em;
display:inline-block;
vertical-align:middle;
}
.orientation_log_extras div input{
display:inline-block;
vertical-align:middle;
width:180px;
}
</style>
<?php
$modules = load_module_results($post);
echo create_module_dropdown_script($modules,'displaymodules',true);
?>
<div class="orientation_log_extras">
<?php if( !create_module_dropdown($modules) ) {
		echo
			"<div style='height:auto;'><strong>Important:</strong> No results have been saved yet.
			You may have clicked \"New Post\" to create this rather than letting this happen automatically through the training modules.
			If you really want to have made this post yourself, it will not be attached to the activities of a user unless the slug is the set as their username.</div>";
	} ?>
	<span id="displaymodules"></span>
	<p>ADD CUSTOM RESULT - Both fields <strong>required</strong> to save:</p>
	<div><label><input name="new_tscmod_key" placeholder="Module Name" /></label><input name="new_tscmod_result" placeholder="Result or Score" /></div>
	<input type="hidden" name="prevent_delete_meta_movetotrash" id="prevent_delete_meta_movetotrash" value="<?php echo wp_create_nonce(plugin_basename(__FILE__).$post->ID); ?>" />
</div>
<?php 
}

//Saving meta
add_action('save_post', 'orientation_save_meta', 10, 2);

function orientation_save_meta($post_id, $post){
	if (!wp_verify_nonce($_POST['prevent_delete_meta_movetotrash'], plugin_basename(__FILE__).$post->ID) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){ //if you remove this the sky will fall on your head.
		return $post_id;
	}else{
		foreach( $_POST as $fullkey => $result ){
			$pos = strpos($fullkey , "tscmod_");
			$pos2 = strpos($fullkey , "new_tscmod_result");
			if( !is_array( $custommeta ) ) $custommeta = array();
			if( $pos === 0 && $result != ''){
				$keyunits = explode( '_', $fullkey );
				$module = $keyunits[1];
				$modulekey = 'tscmod_'.$keyunits[1];
				$key = $keyunits[2];
				$custommeta[$modulekey][$key] = $result;
			}
			if( $pos2 === 0 && $result != ''){
				$customkey = $_POST['new_tscmod_key'];
				if($customkey != '') $custommeta['tscmod_custom-addition'][$customkey] = $result;
			}
		}
		if( is_array( $custommeta ) ) {
			foreach( $custommeta as $module => $results ){
				update_post_meta($post->ID, $module, $results);
			}
		}
    }
}
?>