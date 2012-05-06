<?php
/*
 * @class cruseremote_admin_settings
 * @package create_user_remote
 * @framework AllStruck Plugin Framework
 * @author AllStruck
*/

class cruseremote_admin_settings extends create_user_remote {
	
	function __construct() {
		global $cruser;
		add_action('admin_init', array(&$this, 'add_admin_settings'));
		add_action('admin_menu', array(&$this, 'add_new_users_submenu'));
	}
	
	public function add_admin_settings() {
		// Handshake setting
		register_setting($cruser->pfix.'settings', $cruser->pfix.'setting_handshake', array(&$this,'handshake_validate'));
		// Handshaek settings section
		add_settings_section($cruser->pfix.'handshake_key_settings_section', $cruser->name_full.' Handshake Key', array(&$this,'handshake_key_settings_section_content'), __FILE__);
		// Handshake settings key field
		add_settings_field($cruser->pfix.'setting_handshake', 'Handshake Key', array(&$this,'setting_handshake_key_content'), __FILE__, $cruser->pfix.'handshake_key_settings_section');
		
		// Mapping setting
		register_setting($cruser->pfix.'settings', $cruser->pfix.'setting_mapping', array(&$this, 'setting_mapping_validate'));
		// Mapping settings section
		add_settings_section($cruser->pfix.'mapping_settings_section', $cruser->name_full.' Mapping', array(&$this, 'mapping_settings_section_content'), __FILE__);
		// Mapping settings key field
		add_settings_field($cruser->pfix.'setting_mapping', 'Field Mapping', array(&$this,'setting_mapping_content'), __FILE__, $cruser->pfix.'mapping_settings_section');
	}
	public function handshake_validate($input) {
		return $input;
	}
	public function handshake_key_settings_section_content() {
		echo '<p>Enter a security key in the box below if you wish to require this handshake sent on form submit.</p>';
	}
	public function setting_handshake_key_content() {
		echo '<div><input type="text" name="'.$cruser->pfix.'setting_handshake" value="' . get_option($cruser->pfix.'setting_handshake') . '" />*</div>';
	}
	
	public function setting_mapping_validate($input) {
		return $input;
	}
	public function mapping_settings_section_content() {
		echo '<p>Map the WP user fields to each incoming form field in the table below.</p>';
	}
	public function setting_mapping_content() {
		$required_user_fields = array('ID', 'user_login', 'user_pass', 'user_email', 'user_url', 'user_nicename', 'user_registered', 'user_activation_key', 'user_status', 'display_name');
		$possible_user_fields = get_user_meta(1);
		
		$optionsFieldDisplay = '<option></option>';
		$optionsFieldCount = 0;
		
		foreach ($possible_user_fields as $key) {
			$optionsFieldCount++;
			$optionsFieldDisplay .= '<option value="Field' . $optionsFieldCount . '">Field' . $optionsFieldCount . '</option>';
		}
		
		
		echo '<table><tr style="background-color:black;color:white;"><td>WP User Field</td><td>Form Field</td></tr>';
			
		foreach($required_user_fields as $key) {
			echo "<tr><td> $key </td><td><select name='map_to_$key'> $optionsFieldDisplay </select></td></tr>";
		}
	
		
		foreach($possible_user_fields as $key => $value) {
			echo "<tr><td> $key </td><td><select name='map_to_$key'> $optionsFieldDisplay </select></td></tr>";
		}
		
		echo '</table>';
	}
	
	
	public function add_new_users_submenu() {
		add_submenu_page('users.php', $cruser->name_full, $cruser->name_full, 'delete_users', $cruser->name_slug, array(&$this, 'plugin_settings_page_content'));
	}
	
	public function plugin_settings_page_content() {
		echo '<h1>'. $cruser->name_full .'</h1>';
		
		$settings_page_uri = 'options.php';
		$save_changes_text = esc_attr_e('Save Changes');
		$settings_fields = settings_fields($cruser->pfix.'settings');
		$settings_sections = do_settings_sections(__FILE__);
		
		echo <<<ECHO
			<form action="$settings_page_uri" method="post">
				$settings_fields
				$settings_sections
				<div class="submit"><input name="Submit" class="primary-button" type="submit" value="$save_changes_text" /></div>
			</form>
	
ECHO;
	}
}

$cruseremote_admin_settings = new cruseremote_admin_settings();
$cruseremote_admin_settings->__construct();