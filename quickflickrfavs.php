<?php 
/*
* Plugin Name: QuickFlickrFavs
* Description: shortcode for showing flickr favourites from a specifed flickr user
* Version: 0.1
* Author: John Johnston
* Author URI: http://johnjohnston.info
* License:     GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

 
function quickflickrfavs_shortcode_routine( $args ) {
		extract( shortcode_atts( array(
			'user' => '',
			'count' => 50,
		), $args ) );
	$return = "";	
	// pay attention John
	$return= sprintf(
		"<div data-user='%s' data-count='%s' id='quickflickrfavs'>",
		esc_attr( $user ),
		esc_attr( $count )
			//not sure if I need to escape, is there a better way??
	);
	
	
	
	$localize = array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		// Securing your WordPress plugin AJAX calls using nonces
		'auth' => wp_create_nonce('_check__ajax_100')
	);
	wp_localize_script('quickflickrfavs', 'custom_ajax_vars', $localize);

 	 
	 
	//enqueue here so only add script & styles when needed
	wp_enqueue_script( 'quickflickrfavs');	
	wp_enqueue_style ( 'quickflickrfavs' );
	// return the result
	return $return;
}


//add_shortcode('quickflickrfavs', 'gifmovie_shortcode_routine');
//Above comment out because:  however when running in the plugin context you must hook the shortcode registration to init.
//see https://developer.wordpress.org/plugins/shortcodes/basic-shortcodes/
//[gifmovie gif="value1" mp3="value2"]

function quickflickrfavs_register_shortcode() {
    add_shortcode( 'quickflickrfavs' , 'quickflickrfavs_shortcode_routine' );
}
 
add_action( 'init', 'quickflickrfavs_register_shortcode' );

 
function add_quickflickrfavs_scripts_basic(){
// wp_register_script Registers a script file in WordPress to be linked to a page later using the wp_enqueue_script() function, which safely handles the script dependencies.
		wp_register_script( 'quickflickrfavs', plugins_url( 'quickflickrfavs.js', __FILE__ ), array( 'jquery' ),false,true );	
		wp_register_style ( 'quickflickrfavs', plugins_url( 'quickflickrfavs.css', __FILE__ ) );
		
}
add_action( 'init', 'add_quickflickrfavs_scripts_basic' );



function get_flickr_api($cat){
	check_ajax_referer( '_check__ajax_100', 'nonce_field' );
	echo esc_attr(get_option('quickflickrfavs_flickr_apikey'));
	die();
}
add_action( 'wp_ajax_nopriv_flickr_api_key', 'get_flickr_api' );
add_action( 'wp_ajax_flickr_api_key', 'get_flickr_api' );

/* Settings  */

add_action( 'admin_init', 'quickflickrfavs_plugin_settings' );

function quickflickrfavs_plugin_settings()
{
    register_setting( 'quickflickrfavs-settings-group', 'quickflickrfavs_flickr_apikey' );
     register_setting( 'quickflickrfavs-settings-group', 'quickflickrfavs_showmode' );
}

 function quickflickrfavs_plugin_menu()
 {
     add_options_page( 'quickflickrfavs Options', 'QuickFlickrFavs', 'manage_options', __FILE__, 'quickflickrfavs_plugin_options' );
 }

function quickflickrfavs_plugin_options()
{
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die(__( 'You do not have sufficient permissions to access this page.' ));
    }

    ?>
	<div class="wrap">
	<h2>QuickFlickrFavs Settings</h2>
	<p>You can create/find your api key at <a href="https://www.flickr.com/services/apps/create/">Request an API key</a>. You will need to sign in/create an account.</p>	 
 	<form method="post" action="options.php">
	<?php settings_fields( 'quickflickrfavs-settings-group' );
    ?>
	<?php do_settings_sections( 'quickflickrfavs-settings-group' );
    ?>
	<table class="form-table">	
	<tr valign="top">
	<th scope="row">Flickr API Key</th>
	<td>
<input type="text" name="quickflickrfavs_flickr_apikey" value="<?php echo esc_attr( get_option( 'quickflickrfavs_flickr_apikey' ) );
    ?>" />
</td>
	</tr>
	<!--
	<tr>
		<td>Use mode popup: <input name="quickflickrfavs_showmode" type="checkbox" value="1" <?php checked('1', get_option( 'quickflickrfavs_showmode' ));
	?> />
		<em>This dosen't work yet, the popup is always there.</em>
		</td></tr>-->
	
	</table> 

	<?php submit_button();
    ?>
 
	</form>
	</div>
	<?php 
    echo '</div>';
}
add_action( 'admin_menu', 'quickflickrfavs_plugin_menu' );
?>