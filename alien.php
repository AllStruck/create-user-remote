<?php
/*
 * @package create_user_remote
 * @author AllStruck
 * @version 0.1.0
 */

/*
 Plugin Name: Create User Remote
 Plugin URI: http://create-user-remote.allstruck.com
 Description: Leverage WebHooks in Wufoo to create new wordpress users with custom field mapping.
 Version: 0.0.1
 Author: AllStruck
 Author URI: http://www.allstruck.com/
 License: GPL2
 */
 
/* 

	This plugin is created using the AllStruck Plugin Framework; MVC and OO principles implied.
	
	This file is named "alien" because it rests alone outside the rest of the directories:
		- com = class files including "allstruck" directory where plugin framework resides.
		- controller = all main actions performed here, connecting the view and model.
		- model = defines all object types, a type of informational and computational backend.
		- view = layout of user displayed information delivered by model is presented here.
*/

/*
 * @copyright 2012  David Monaghan & AllStruck ( email: wufoo.wp.webhook@allstruck.com  )
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/*
 Revision 0.0.1:
 - Original alpha version
 	- Start of AllStruck Plugin Framework
 	- Originally hand coded method for one form
 	- Converted hard coded form input mapping and "Handshake Key" to admin settings 
 */

// Include AllStruck Plugin Framework
//require_once( dirname( __FILE__ ) . '/com/allstruck/allstruck-plugin-framework.php' );


// Include this plugin's initialization (everything else included from there.
//require_once( dirname( __FILE__ ) . '/controller/initialize.php' );


create_user_remote_permalink();
	global $debug;
	global $remoteDebug;
	
	$debug = false;
	$remoteDebug = $debug;
	if ($debug) {
		if (!isset($_POST['HandshakeKey']) || $_POST['HandshakeKey'] == '') {
			$_POST['HandshakeKey'] = (isset($_GET['handshake']) && $_GET['handshake'] != '') ? $_GET['handshake'] : (isset($_POST['HandshakeKey'])?$_POST['HandshakeKey']:'');	
		}
		for ($i=1;$i<=150;$i++) {
			$fieldname = 'Field'.$i;
			if (isset($_GET[$fieldname]) && $_GET[$fieldname] != '') {
				$_POST[$fieldname] = $_GET[$fieldname];
			}
		}
	}
	

function debug_say($message, $key = 'General') {
	global $debug;
	global $remoteDebug;
	if ($debug) {
		// Echo to console/browser:
		echo "$key: $message <br />" ;
	}
	if ($remoteDebug) {
		// Send debug message to request bin so we can see it during POST requests.. 
		//$r = new HttpRequest('http://requestb.in/mpqp8kmp', HttpRequest::METH_GET);
		//$r->setOptions(array('lastmodified' => filemtime('local.rss')));
		//$r->addQueryData(array($key => $message));
		   	$url = 'http://requestb.in/mpqp8kmp';
			$url .= '?' . $key . '=' . sanitize_title_for_query($message) ;
			$fp = fopen($url, 'r');
	}
}
			//debug_say("We are going to start up now");
			global $cruser;
		  	$cruser = array();
			$cruser['pfix'] = 'cruser_';
			$cruser['name_full'] = 'Create User Remote';
			$cruser['name_slug'] = 'cruser';

			add_option($cruser['pfix'].'setting_new_user_role', '');
			update_option($cruser['pfix'].'setting_new_user_role', 'subscriber');

		global $require_user_fields;
		$standard_user_fields = array(
			'user_login'=>'user_login',
			'user_email'=>'user_email',
			'first_name'=>'first_name',
			'last_name'=>'last_name',
			'user_url'=>'user_url', 
			'user_nicename'=>'user_nicename', 
			'user_registered'=>'user_registration', 
			'user_activation_key'=>'user_activation_key', 
			'display_name'=>'display_name'
			);
		/*
		global $possible_user_fields;
		$possible_user_fields = array();//get_user_meta(1);
		$possible_user_fields_associative_array = array();
		foreach ($possible_user_fields as $key => $value) {
			$possible_user_fields_associative_array[(string)$key] = $key;
		}*/
		
		global $all_user_fields;
		$all_user_fields = array_merge((array)$standard_user_fields);
		global $current_mappings;
		$current_mappings = unserialize(get_option($cruser['pfix'].'setting_mapping')); 
		global $all_other_fields;
		$all_other_fields = explode(',', str_replace(array(' ', "\r\n", "\n", "\r"),'', get_option($cruser['pfix'].'setting_post_type_fields'))); /*array(
			'wpcf-shipping-address-street',
			'wpcf-shipping-address-line-2',
			'wpcf-shipping-address-city',
			'wpcf-shipping-address-state',
			'wpcf-shipping-address-postal',
			'wpcf-shipping-address-country',
			'wpcf-billing-address-street',
			'wpcf-billing-address-line-2',
			'wpcf-billing-address-city',
			'wpcf-billing-address-state',
			'wpcf-billing-address-postal',
			'wpcf-billing-address-country',
			'wpcf-request-fax-order-form',
			'wpcf-request-email-order-form',
			'wpcf-fax-number',
			'wpcf-phone-number',
			'wpcf-company'
		);*/


			add_action('admin_init', 'add_admin_settings');
			add_action('admin_menu', 'add_new_users_submenu');



	function add_admin_settings() {
			global $cruser;
		// Handshake setting
		register_setting(
			$cruser['pfix'].'settings', 
			$cruser['pfix'].'setting_handshake', 
			'handshake_validate');
		// Handshake settings section
		add_settings_section(
			$cruser['pfix'].'handshake_key_settings_section', 
			$cruser['name_full'].' Handshake Key', 
			'handshake_key_settings_section_content', 
			__FILE__);
		// Handshake settings key field
		add_settings_field(
			$cruser['pfix'].'setting_handshake', 
			'Handshake Key', 
			'setting_handshake_key_content', 
			__FILE__, 
			$cruser['pfix'].'handshake_key_settings_section');





		// Permalink setting
		register_setting(
			$cruser['pfix'].'settings', 
			$cruser['pfix'].'setting_permalink', 
			'permalink_validate');
		// Permalink settings section
		add_settings_section(
			$cruser['pfix'].'permalink_settings_section', 
			$cruser['name_full'].' Permalink', 
			'permalink_settings_section_content', 
			__FILE__);
		// Permalink settings key field
		add_settings_field(
			$cruser['pfix'].'setting_permalink', 
			'Permalink', 
			'setting_permalink_content', 
			__FILE__, 
			$cruser['pfix'].'permalink_settings_section');
		
		
		
		
		// Extended data post type setting
		register_setting(
			$cruser['pfix'].'settings', 
			$cruser['pfix'].'setting_post_type', 
			'post_type_validate');
		// Post type settings section
		add_settings_section(
			$cruser['pfix'].'post_type_settings_section', 
			$cruser['name_full'].' Post Type', 
			'post_type_settings_section_content', 
			__FILE__);
		// Post type settings  field
		add_settings_field(
			$cruser['pfix'].'setting_post_type', 
			'Post Type', 
			'setting_post_type_content', 
			__FILE__, 
			$cruser['pfix'].'post_type_settings_section');
		
		
		
		
		// Post type fields setting
		register_setting(
			$cruser['pfix'].'settings', 
			$cruser['pfix'].'setting_post_type_fields', 
			'post_type_fields_validate');
		// Post type fields section
		add_settings_section(
			$cruser['pfix'].'post_type_fields_settings_section', 
			$cruser['name_full'].' Post Tyoe Fields', 
			'post_type_fields_settings_section_content', 
			__FILE__);
		// Post type fields settings field
		add_settings_field(
			$cruser['pfix'].'setting_post_type_fields', 
			'Post Type Fields', 
			'setting_post_type_fields_content', 
			__FILE__, 
			$cruser['pfix'].'post_type_fields_settings_section');
		
		
		
		
		$setting_title = 'Fields Prefix';
		$setting_slug = 'fields_prefix';
		// Field prefix setting
		register_setting(
			$cruser['pfix'].'settings', 
			$cruser['pfix'].'setting_'.$setting_slug, 
			$setting_slug.'_validate');
		// Field prefix settings section
		add_settings_section(
			$cruser['pfix'].$setting_slug.'_settings_section', 
			$cruser['name_full'].' '.$setting_title, 
			$setting_slug.'_settings_section_content', 
			__FILE__);
		// Field prefix settings key field
		add_settings_field(
			$cruser['pfix'].'setting_'.$setting_slug, 
			$setting_title, 
			'setting_'. $setting_slug .'_content', 
			__FILE__, 
			$cruser['pfix'].$setting_slug.'_settings_section');
		
		
		
		
		$setting_title = 'Taxonomy Name';
		$setting_slug = 'taxonomy_name';
		// Field prefix setting
		register_setting(
			$cruser['pfix'].'settings', 
			$cruser['pfix'].'setting_'.$setting_slug, 
			$setting_slug.'_validate');
		// Field prefix settings section
		add_settings_section(
			$cruser['pfix'].$setting_slug.'_settings_section', 
			$cruser['name_full'].' '.$setting_title, 
			$setting_slug.'_settings_section_content', 
			__FILE__);
		// Field prefix settings key field
		add_settings_field(
			$cruser['pfix'].'setting_'.$setting_slug, 
			$setting_title, 
			'setting_'. $setting_slug .'_content', 
			__FILE__, 
			$cruser['pfix'].$setting_slug.'_settings_section');
		
		
		
		
		$setting_title = 'Taxonomy Terms';
		$setting_slug = 'taxonomy_terms';
		// Field prefix setting
		register_setting(
			$cruser['pfix'].'settings', 
			$cruser['pfix'].'setting_'.$setting_slug, 
			$setting_slug.'_validate');
		// Field prefix settings section
		add_settings_section(
			$cruser['pfix'].$setting_slug.'_settings_section', 
			$cruser['name_full'].' '.$setting_title, 
			$setting_slug.'_settings_section_content', 
			__FILE__);
		// Field prefix settings key field
		add_settings_field(
			$cruser['pfix'].'setting_'.$setting_slug, 
			$setting_title, 
			'setting_'. $setting_slug .'_content', 
			__FILE__, 
			$cruser['pfix'].$setting_slug.'_settings_section');
		
		
		
		
		// Mapping setting
		register_setting(
			$cruser['pfix'].'settings', 
			$cruser['pfix'].'setting_mapping', 
			'setting_mapping_validate');
		// Mapping settings section
		add_settings_section(
			$cruser['pfix'].'mapping_settings_section', 
			$cruser['name_full'].' Mapping', 
			'mapping_settings_section_content', 
			__FILE__);
		// Mapping settings key field
		add_settings_field(
			$cruser['pfix'].'setting_mapping', 
			'Field Mapping', 
			'setting_mapping_content', 
			__FILE__, 
			$cruser['pfix'].'mapping_settings_section');




	}
	
	// Handshake key setting functions
	function handshake_validate($input) {
		return $input;
	}
	function handshake_key_settings_section_content() {
		echo '<p>Enter a security key in the box below if you wish to require this handshake sent on form submit.</p>';
	}
	function setting_handshake_key_content() {
			global $cruser;
		echo '<div><input type="text" name="'.$cruser['pfix'].'setting_handshake" value="' . get_option($cruser['pfix'].'setting_handshake') . '" /></div>';
	}
	
	// Permalink setting functions
	function permalink_validate($input) {
		return $input;
	}
	function permalink_settings_section_content() {
		echo '<p>Enter a slug in the box below, this is the URI where the WebHook will listen from.</p>';
	}
	function setting_permalink_content() {
			global $cruser;
		echo '<div><label for="'. $cruser['pfix'].'setting_permalink' .'">'.bloginfo('url').'/</label><input type="text" name="'.$cruser['pfix'].'setting_permalink" value="' . get_option($cruser['pfix'].'setting_permalink') . '" /></div>';
	}



	// Post type setting functions
	function post_type_validate($input) {
		return $input;
	}
	function post_type_settings_section_content() {
		echo '<p>Enter the slug for the custom post type you want to enter new posts with meta data in.</p>';
	}
	function setting_post_type_content() {
			global $cruser;
		echo '<div><input type="text" name="'.$cruser['pfix'].'setting_post_type" value="' . get_option($cruser['pfix'].'setting_post_type') . '" /></div>';
	}
	// Post type fields setting functions
	function post_type_fields_validate($input) {
		return $input;
	}
	function post_type_fields_settings_section_content() {
		echo '<p>Enter the full field key for each meta field of the post you want to map.</p>';
	}
	function setting_post_type_fields_content() {
			global $cruser;
		echo '<div><textarea  name="'.$cruser['pfix'].'setting_post_type_fields">' . get_option($cruser['pfix'].'setting_post_type_fields') . '</textarea></div>';
	}



	// Fields prefix setting functions
	function fields_prefix_validate($input) {
		return $input;
	}
	function fields_prefix_settings_section_content() {
		echo '<p>Enter a slug in the box below, this is the URI where the WebHook will listen from.</p>';
	}
	function setting_fields_prefix_content() {
			global $cruser;
			$setting_slug = 'fields_prefix';
		echo '<div><label for="'. $cruser['pfix'].'setting_'. $setting_slug .'">If every form field name has same prefix:</label><input type="text" name="'.$cruser['pfix'].'setting_'. $setting_slug .'" value="' . get_option($cruser['pfix'].'setting_'.$setting_slug) . '" /></div>';
	}



	// Taxonomy name setting functions
	function taxonomy_name_validate($input) {
		return $input;
	}
	function taxonomy_name_settings_section_content() {
		echo '<p>Enter a taxonomy name to fill with the terms from the terms list.</p>';
	}
	function setting_taxonomy_name_content() {
			global $cruser;
			$setting_slug = 'taxonomy_name';
		echo '<div><label for="'. $cruser['pfix'].'setting_'. $setting_slug .'">Lowercase slug</label><input type="text" name="'.$cruser['pfix'].'setting_'. $setting_slug .'" value="' . get_option($cruser['pfix'].'setting_'.$setting_slug) . '" /></div>';
	}



	// Taxonomy terms setting functions
	function taxonomy_terms_validate($input) {
		return $input;
	}
	function taxonomy_terms_settings_section_content() {
		echo '<p>Enter a list of taxonomy terms to fill the taxonomy with.</p>';
	}
	function setting_taxonomy_terms_content() {
			global $cruser;
			$setting_slug = 'taxonomy_terms';
		echo '<div><label for="'. $cruser['pfix'].'setting_'. $setting_slug .'">Lowercase slugs or IDs separated by commas</label><input type="text" name="'.$cruser['pfix'].'setting_'. $setting_slug .'" value="' . get_option($cruser['pfix'].'setting_'.$setting_slug) . '" /></div>';
	}



	// Mapping setting functions
	function setting_mapping_validate($input) {
		global $cruser;
		global $all_user_fields;
		global $all_other_fields;
		
		
		$fields = array();
		foreach ($all_user_fields as $key => $value) {
			$fields[$key.'_mapping'] = isset($_POST[$key.'_mapping'])?$_POST[$key.'_mapping']:'';
		}
		foreach ($all_other_fields as $key) {
			$fields[$key.'_mapping'] = isset($_POST[$key.'_mapping'])?$_POST[$key.'_mapping']:'';
		}
		return serialize($fields);
	}
	function mapping_settings_section_content() {
		echo '<p>Map the WP user fields to each incoming form field in the table below.</p>';
	}
	function setting_mapping_content() {
		global $cruser;
		global $all_user_fields;		
		global $current_mappings;
		global $all_other_fields;
		
		$optionsFieldDisplay = '';
		
		// User Fields
		echo '<table><tr style="background-color:black;color:white;"><td>WP User Field</td><td>Form Field</td></tr>';
		
		foreach($all_user_fields as $key=>$value) {
			$optionFieldValue = isset($current_mappings[$key.'_mapping'])? $current_mappings[$key.'_mapping'] : '' ;
			$optionsFieldDisplay = '<input name="'. $key .'_mapping" value="' . $optionFieldValue . '">';
			echo "<tr><td> $key </td><td> $optionsFieldDisplay </td></tr>";
		}
		echo '</table>';
		
		// Client CPT Fields
		echo '<table><tr style="background-color:black;color:white;"><td>Client CPT Fields</td><td>Form Field</td></tr>';
		
		foreach($all_other_fields as $key) {
			$optionFieldValue = isset($current_mappings[$key.'_mapping'])? $current_mappings[$key.'_mapping'] : '' ;
			$optionsFieldDisplay = '<input name="'. $key .'_mapping" value="' . $optionFieldValue . '">';
			echo "<tr><td> $key </td><td> $optionsFieldDisplay </td></tr>";
		}
		echo '</table>';

	}
	
	
	function add_new_users_submenu() {
		global $cruser;
		add_submenu_page('users.php', $cruser['name_full'], $cruser['name_full'], 'delete_users', $cruser['name_slug'], 'plugin_settings_page_content');
	}
	
	function plugin_settings_page_content() {
			global $cruser;
		echo '<h1>'. $cruser['name_full'] .'</h1>';
		
		$settings_page_uri = 'options.php?redirect_to=/wp-admin/users.php?page=cruser';
		
		?>
			<form action="<?php echo $settings_page_uri ?>" method="post">
				<?php settings_fields($cruser['pfix'].'settings'); ?>
				<?php do_settings_sections(__FILE__); ?>
				<div class="submit"><input name="Submit" class="primary-button" type="submit" value="<?php esc_attr_e('Save Changes');?>" /></div>
			</form>
	
<?php
	}
	
	// Override permalink given in settings.
	function create_user_remote_permalink() {		
		
		register_activation_hook( __FILE__, 'rewrite_activate'  );
		register_deactivation_hook( __FILE__, 'rewrite_deactivate'  );
		add_action( 'init', 'create_rewrite_for_new_user' );
	}
	function create_rewrite_for_new_user() {
		global $cruser;
		add_rewrite_rule(get_option($cruser['pfix'].'setting_permalink') . '/?$', 
		str_replace( 
    		home_url(), 
    		"", 
    		WP_PLUGIN_URL . '/' 
    			. dirname( plugin_basename( __FILE__ ) ) 
    			. '/controller/remote_create_user.php' 
    	), 
    	'top'
    	);
	}
	function rewrite_activate() {
		create_rewrite_for_new_user();
		flush_rewrite_rules();
	}

	function rewrite_deactivate() {
		flush_rewrite_rules();
	}