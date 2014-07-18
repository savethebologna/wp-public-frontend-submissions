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
	register_setting( 'fef_settings', 'frontendfields' );
}

function fef_settings_page() {
?>
<style>
.postbox{
width:45%;
margin: 0 5px;
min-width:300px;
display:inline-block;
}
.tscmod_sortable{
width:auto;
padding: 1em 1em;
background-color: #eee;
box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.tscmod_sortable li{
font-size:1.3em;
display:block;
height:2em;
background-color: #fff;
text-align:center;
line-height:2em;
box-shadow: 0 1px 1px rgba(0,0,0,.04);
border: 1px solid #e5e5e5;
}
#savestate{
width: 100%;
text-align: center;
}
#savestate .spinner{
vertical-align:text-bottom;
display:inline-block;
float:none;
}
.hidden{
display:none !important;
}
p.submit{
text-align:center;
padding-bottom:0;
}
.center{
text-align:center;
}
.inside{
text-align:left;
}
.js .postbox h3{
cursor:auto;
}
</style>
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