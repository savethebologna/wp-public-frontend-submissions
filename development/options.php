<?php
// create plugin settings admin page
add_action('admin_menu', 'create_fef_settings');

function create_fef_settings() {

	//create new top-level menu
	add_menu_page('Public Frontend Submissions', 'Frontend Forms', 'delete_pages', __FILE__, 'fef_settings_page','dashicons-forms');

	//call register settings function
	add_action( 'admin_init', 'register_fef_settings' );
}


function register_fef_settings() {
	//register our settings
	register_setting( 'fef_settings', 'fef_frontendfields' );
	register_setting( 'fef_settings', 'fef_publicuser' );
	register_setting( 'fef_settings', 'fef_fields' );
}

function fef_settings_page() {
	$blogusers = get_users( 'orderby=nicename' );	// Array of WP_User objects.
	global $publicuser;
	global $fields;
?>
<div class="wrap">
	<h2>Public Frontend Submissions</h2>
	<div class="center">
		<div id="tscmod_reorder" class="postbox">
			<div class="inside">
			<h3>Options</h3>
				<form method="post" action="options.php">
					<p>Customize your form.</p>
					<label>Public User:</label>
					<select name="fef_publicuser">
					<?php
					foreach( $blogusers as $user ){
						if($publicuser === $user->ID){ $selected = "selected"; }else{ $selected = ""; }
						$option = '<option value="' . $user->ID . '" '.$selected.'>';
						$option .= $user->display_name;
						$option .= '</option>';
						echo $option;
					}
					?>
					</select>
					<?php
					if( is_array($fields) ){
						foreach( $fields as $field_id => $field_data ){
							$label = $field_data['label'];
							echo "<div class='feffield'><label>".$label." (ID: fef_".$field_id.")</label><input name='fef_fields[".$field_id."][label]' value='".$label."'></div>\n";
						}
					}else{
						echo "\n<p>You don't have any fields yet.</p>";
					} //FIX BUG: if a new field is added, a blank field entry will still save as a new field.
					?>
					<div id="newfields"></div>
					<p><a href="javascript:void(0)" onClick="addField();">Add a field</a></p>
					<?php settings_fields( 'fef_settings' ); ?>
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" /></p>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	function addField(){
		var fefID = jQuery('.feffield').length;
		jQuery('#newfields').append('<div class="feffield"><label>New Field:</label><input name="fef_fields[' + fefID + '][label]" value=""></div>');
	}
</script>
<?php } ?>