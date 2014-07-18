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
?>
<div class="wrap">
	<h2>Public Frontend Submissions</h2>
	<div class="center">
		<div id="tscmod_reorder" class="postbox">
			<div class="inside">
				<h3>Do something</h3>
				<p>Text</p>
				<p class="submit"><input type="button" name="submit" id="submit" class="button button-primary" value="Save Order" /></p>
			</div>
		</div>
		<div style="display:none" id="hidden_form_elements"></div>
	</div>
</div>

<?php } ?>