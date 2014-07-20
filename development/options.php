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
	register_setting( 'fef_settings', 'fef_emailoptions' );
	register_setting( 'fef_settings', 'fef_publicuser' );
	register_setting( 'fef_settings', 'fef_fields' );
}

function fef_settings_page() {
	$blogusers = get_users( 'orderby=nicename' );	// Array of WP_User objects.
	global $publicuser;
	global $fields;
?>
<style>
.feffield input{
	display:none;
}
</style>
<div class="wrap">
	<h2>Public Frontend Submissions</h2>
	<div class="center"><div id="tscmod_reorder" class="postbox"><div class="inside">
		<h2>Options</h2>
		<form method="post" action="options.php">
			<p><h3>Form Fields</h3>
				<?php
				if( is_array($fields) ){
					foreach( $fields as $field_id => $field_data ){
						$label = $field_data[label];
						$active = "";
						$required = "";
						if( $field_data[active] == true ) $active = "checked";
						if( $field_data[required] == true ) $required = "checked";
						echo "<div class='feffield'>
							<span class='feflabel'>
								".$label." (ID: fef_".$field_id.")
								<a href='#' class='editField'>Edit Field</a>
							</span>
							<input type='checkbox' name='fef_fields[".$field_id."][active]' value='true' ".$active." />Active
							<input type='checkbox' name='fef_fields[".$field_id."][required]' value='true' ".$required." />Required
							<input name='fef_fields[".$field_id."][label]' value='".$label."' />
							</div>\n";
					}
				}else{
					echo "\n<p>You don't have any fields yet.</p>";
				} //FIX BUG: if a new field is added, a blank field entry will still save as a new field.
				?>
				<div id="newfields"></div>
				<p><a href="#" onClick="addField();">Add a field</a></p>
			</p>
			<p><h3>More Options</h3>
				<label>Public User:</label><br>
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
			</p>
			<?php settings_fields( 'fef_settings' ); ?>
			<p class="submit" style="text-align:center"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" /></p>
		</form>
	</div></div></div>
</div>
<script>
	function addField(){
		event.preventDefault();
		var fefID = jQuery('.feffield').length;
		jQuery('#newfields').append('<div class="feffield"><input style="display:block !important" name="fef_fields[' + fefID + '][label]" value="" placeholder="Field Label"></div>');
	}
	jQuery( ".feflabel a.editField" ).click(function( event ) {
		event.preventDefault();
		jQuery( this ).parent().hide();
		jQuery( this ).parent().siblings('input').show();
	});
</script>
<?php } ?>