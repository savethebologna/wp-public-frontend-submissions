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

add_action("admin_init", "fish_manager_add_meta");  
  

function fish_manager_add_meta(){  
    add_meta_box("fish-meta", "More Information", "fish_manager_meta_options", "event", "normal", "high");   
}  
  

//Create area for extra fields
function fish_manager_meta_options(){  
        global $post; 
		$tz = date_default_timezone_get(); // get current PHP timezone
		date_default_timezone_set ( 'America/New_York' );
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
        
        $custom = get_post_custom($post->ID);
		$street = $custom["street"][0];
		$city_state = $custom["city_state"][0];
		$feature = $custom["feature"][0];
		$venue = $custom["venue"][0];
		$venueurl = $custom["venueurl"][0];
		$ubuilding = $custom["ubuilding"][0];
		$cost = $custom["cost"][0];
		$costurl = $custom["costurl"][0];
		$room = $custom["room"][0];
		$startdate = $custom["startdate"][0];
		$enddate = $custom["enddate"][0];
		$host = $custom["host"][0];
		$hosturl = $custom["hosturl"][0];
		$contactemail = $custom["contactemail"][0];
		$enddate2 = $custom["enddate2"][0];
		$startdate2 = $custom["startdate2"][0];
?>  
<style type="text/css">
<?php include('fish-manager.css'); ?>
</style>
<div class="fish_manager_extras">
	<div><label>Venue (recommended):</label><input name="venue" value="<?php echo $venue; ?>" /></div>
	<div><label>Venue website:</label><input name="venueurl" value="<?php echo $venueurl; ?>" /></div>
	<div><label>Room #:</label><input name="room" value="<?php echo $room; ?>" /></div>
	<div><label>Building:</label><input name="ubuilding" value="<?php echo $ubuilding; ?>" /></div>
	<div><label>Street Address:</label><input name="street" value="<?php echo $street; ?>" /></div>
	<div><label>City, State Zip (required):</label><input name="city_state" value="<?php echo $city_state; ?>" /></div>
	<div><label>Feature:</label><input name="feature" value="<?php echo $feature; ?>" /></div>
	<div><label>Hosted by:</label><input name="host" value="<?php echo $host; ?>" /></div>
	<div><label>Host website:</label><input name="hosturl" value="<?php echo $hosturl; ?>" /></div>
	<div><label>Cost (required):</label><input name="cost" value="<?php echo $cost; ?>" /></div>
	<div><label>Purchase Link:</label><input name="costurl" value="<?php echo $costurl; ?>" /></div>
	<div><label>Start Date (required):</label><input name="startdate" value="<?php echo date('g:iA M d\, Y', $startdate) ?>" /></div>
	<div><label>End Date (required):</label><input name="enddate" value="<?php echo date('g:iA M d\, Y', $enddate); ?>" /></div>
	<div><label>Contact Email:</label><input type="email" name="contactemail" value="<?php echo $contactemail; ?>" required /></div>
	<div>User Entered Start Date: <?php echo $startdate2; ?></div>
	<div>User Entered End Date: <?php echo $enddate2; ?></div>
	<input type="hidden" name="prevent_delete_meta_movetotrash" id="prevent_delete_meta_movetotrash" value="<?php echo wp_create_nonce(plugin_basename(__FILE__).$post->ID); ?>" />
</div>
<?php date_default_timezone_set($tz); // set the PHP timezone back the way it was
    }
add_action('save_post', 'fish_manager_save_extras'); 
  
function fish_manager_save_extras(){  
    global $post;  
    $tz = date_default_timezone_get(); // get current PHP timezone
	date_default_timezone_set ( 'America/New_York' );
	if (!wp_verify_nonce($_POST['prevent_delete_meta_movetotrash'], plugin_basename(__FILE__).$post->ID)) { return $post_id; } //fix delete-custom-meta-on-trash bug
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){ //if you remove this the sky will fall on your head.
		return $post_id;
	}else{
    	update_post_meta($post->ID, "street", $_POST["street"]); 
		update_post_meta($post->ID, "city_state", $_POST["city_state"]);
		update_post_meta($post->ID, "feature", $_POST["feature"]);
		update_post_meta($post->ID, "venue", $_POST["venue"]);
		update_post_meta($post->ID, "venueurl", $_POST["venueurl"]);
		update_post_meta($post->ID, "ubuilding", $_POST["ubuilding"]);
		update_post_meta($post->ID, "cost", $_POST["cost"]);
		update_post_meta($post->ID, "costurl", $_POST["costurl"]);
		update_post_meta($post->ID, "room", $_POST["room"]);
		update_post_meta($post->ID, "startdate", strtotime($_POST["startdate"]));
		update_post_meta($post->ID, "enddate", strtotime($_POST["enddate"]));
		update_post_meta($post->ID, "host", $_POST["host"]);
		update_post_meta($post->ID, "hosturl", $_POST["hosturl"]);
		update_post_meta($post->ID, "contactemail", $_POST["contactemail"]);
    }
	date_default_timezone_set($tz); // set the PHP timezone back the way it was
}  

add_filter("manage_edit-event_columns", "fish_manager_edit_columns");   

function fish_manager_edit_columns($columns){
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
}  

add_action("manage_event_posts_custom_column",  "fish_manager_custom_columns"); 

function fish_manager_custom_columns($column){
        global $post;
        $custom = get_post_custom();
        switch ($column)
        {
                        
			case "startdate":
                if($custom["startdate"][0]!=""){echo date('g:iA \o\n M jS',$custom["startdate"][0]);}
                break;
			case "feature":
				echo $custom["feature"][0];
				break;
			case "street":
				echo $custom["street"][0];
				break;
            case "city_state":
                echo $custom["city_state"][0];
                break;
			case "venue":
                echo $custom["venue"][0];
                break;
			case "enddate":
                if($custom["enddate"][0]!=""){echo date('g:iA \o\n M jS',$custom["enddate"][0]);}
                break;
            case "cat":
                echo get_the_term_list($post->ID, 'event_type');
                break;
        }
}

// Add term page
function aquarium_new_meta_field() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[custom_term_meta]">Short description for the homepage</label>
		<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
		<p class="description">This will appear under the tank title on the front page.</p>
	</div>
<?php
}
add_action( 'aquarium_add_form_fields', 'aquarium_new_meta_field', 10, 2 );

// Edit term page
function aquarium_edit_meta_field($term) {
 
	// put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); ?>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[custom_term_meta]">Short description</label></th>
		<td>
			<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="<?php echo esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : ''; ?>">
			<p class="description">This will appear under the tank title on the front page.</p>
		</td>
	</tr>
<?php
}
add_action( 'aquarium_edit_form_fields', 'aquarium_edit_meta_field', 10, 2 );

// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  
add_action( 'edited_aquarium', 'save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_aquarium', 'save_taxonomy_custom_meta', 10, 2 );

?>