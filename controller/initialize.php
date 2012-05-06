<?php
/*
 * @class cruseremote_initialize
 * @package create_remote_user
 * @framework AllStruck Plugin Framework
 * @author AllStruck
*/

// Start up the framework for this plugin with options
require_once('instantiate_framework.php');

if (!class_exists(cruseremote_initialize)) {
	class cruseremote_initialize extends create_user_remote {
		function __construct( ) {
			require_once(dirname( __FILE__ ) . '/../view/admin-settings.php');
		}
	}
}
	global $create_user_remote;
	global $cruser;
	$create_user_remote = new create_user_remote();
	$cruseremote_initialize = new cruseremote_initialize();


add_action('init', 'cruser_plugin_wakeup');
function cruser_plugin_wakeup() {
}
?>