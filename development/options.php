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
	register_setting( 'fef_settings', 'fef_postsettings' );
}

function fef_settings_page() {
	$blogusers = get_users( 'orderby=nicename' );	// Array of WP_User objects.
	global $publicuser, $fields, $emailoptions, $postsettings;
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
				</select><br><br>
				<label>Field to Use for Post Title:</label><br>
				<select name="fef_postsettings[title]">
					<option value="">--Choose a Field--</option>
				<?php
				foreach( $fields as $field_id => $field_data ){
					if($postsettings['title'] == "fef_" . $field_id){ $selected = "selected"; }else{ $selected = ""; }
					$option = '<option value="fef_' . $field_id . '" '.$selected.'>';
					$option .= $field_data[label] . " (ID: fef_" . $field_id . ")";
					$option .= '</option>';
					echo $option;
				}
				?>
				</select><br><br>
				<label>Email on Submission:</label><br>
				<?php if( $emailoptions['active'] == true ){$checked = "checked";}else{$checked = "";} ?>
				<input type="checkbox" name="fef_emailoptions[active]" value="true" <?php echo $checked ?> />Active<br>
				<?php if( $emailoptions['sendtoposter'] == true ){$checked = "checked";}else{$checked = "";} ?>
				<input type="checkbox" name="fef_emailoptions[sendtoposter]" value="true" <?php echo $checked ?> />Send to Poster<br>
				<?php if( $emailoptions['sendtoadmin'] == true ){$checked = "checked";}else{$checked = "";} ?>
				<input type="checkbox" name="fef_emailoptions[sendtoadmin]" value="true" <?php echo $checked ?> />Send to Admin<br>
				<?php $checked = ""; ?>
				<label>Admin Email</label><input type="email" name="fef_emailoptions[adminemail]" value="<?php fef_echo($emailoptions['adminemail']); ?>" /><br>
				<label>Subject</label><input name="fef_emailoptions[subject]" value="<?php fef_echo($emailoptions['subject']); ?>" /><br>
				<label>Message</label><input name="fef_emailoptions[message]" value="<?php fef_echo($emailoptions['message']); ?>" /><br>
				<label>Poster Email Field:</label><br>
				<select name="fef_emailoptions[emailfield]">
					<option value="">--Choose a Field--</option>
				<?php
				foreach( $fields as $field_id => $field_data ){
					if($emailoptions['emailfield'] == "fef_".$field_id){ $selected = "selected"; }else{ $selected = ""; }
					$option = '<option value="fef_' . $field_id . '" '.$selected.'>';
					$option .= $field_data['label'] . " (ID: fef_" . $field_id . ")";
					$option .= '</option>';
					echo $option;
				}
				?>
				</select><br>
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