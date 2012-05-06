<?php
/*
 * @class cruseremote_remote_create_user
 * @package create_user_remote
 * @framework AllStruck Plugin Framework
 * @author AllStruck
*/

// Need to load WordPress manually since this file is called directly by outside request, in a way...
require_once( '../../../../wp-load.php' );

// A debugging test for development via GET requests…



global $cruser;
global $all_user_fields;
global $all_other_fields;


global $mappings; global $current_mappings;
$mappings = $current_mappings;

global $formdata;
$formdata['handshake'] = isset($_POST['HandshakeKey'])? $_POST['HandshakeKey'] : '';

global $new_user;
global $new_client_post_fields;

foreach ($all_user_fields as $key => $value) {
	if ($mappings[$key.'_mapping'] != '') {
		$formFieldName = isset($mappings[$key.'_mapping'])? get_option($cruser['pfix'].'setting_fields_prefix').$mappings[$key.'_mapping'] : '';
		$postedData = isset($_POST[$formFieldName])? $_POST[$formFieldName] : '';
		if ($postedData != '' && $formFieldName != '') {
			$new_user[$key] = $_POST[$formFieldName];
		}
	}
	$count = count($new_user);
}
	debug_say("new_user array populated with $count rows.");
foreach ($all_other_fields as $key => $value) {
	debug_say("$key => $value");
	debug_say($mappings[$value.'_mapping'], '$mappings[]');
	if ($mappings[$value.'_mapping'] != '') {
		$formFieldName = isset($mappings[$value.'_mapping'])? get_option($cruser['pfix'].'setting_fields_prefix').$mappings[$value.'_mapping'] : '';
		$postedData = isset($_POST[$formFieldName])? $_POST[$formFieldName] : '';
		debug_say("postedData: $postedData  -  formFieldName: $formFieldName.");
		if ($postedData != '' && $formFieldName != '') {
			$new_client_post_fields[$value] = $_POST[$formFieldName];
			debug_say("$new_client_post_fields[$key] = $_POST[$formFieldName]");
		}
	}
	$count = count($new_client_post_fields);
}
	debug_say("new_client_post_fields populated with $count rows.");


$handshakeKey = get_option($cruser['pfix'].'setting_handshake');
if ($formdata['handshake'] === $handshakeKey || $handshakeKey == '') {
	debug_say("Sending request to create new user.");
	create_new_user();
} else {
	debug_say('Handshake Fail<br/>');
}

	function create_new_user() {
		debug_say("Function called - create_new_user()");
		global $cruser;
		global $new_user;
		global $new_client_post_fields;
		
		$login = isset($new_user['user_login'])? $new_user['user_login'] : '';
		if ($login == '') { $message = "No user_login found."; debug_say($message); die($message); }
		$email = isset($new_user['user_email'])? $new_user['user_email'] : '';
		if ($email == '') { $message = "No user_email found."; debug_say($message); die($message); }
		$name = isset($new_user['user_name'])? $new_user['user_name'] : $new_user['user_login'];
		
		
		if ($login != '' && $email != '' && !username_exists($name) && !email_exists($email)) {
			$password = wp_generate_password( $length=12, $include_standard_special_chars=false );
			$new_user['user_pass'] = $password;
			$new_user['user_name'] = $name;
			$new_user['role'] = get_option($cruser['pfix'].'setting_new_user_role');
			$new_user_id = wp_insert_user($new_user);
			
			// If new user was created successfully:
			if ($new_user_id) { 
				// Email the new user.
				debug_say("Emailing the new WordPress user their password and such.");
				wp_new_user_notification($new_user_id, $password);
				
				// Set up the new Client post.			
				$client_post = array(
				  //'ID' => [ <post id> ] //Are you updating an existing post?
				  'post_author' => $new_user_id, //The user ID number of the author.
				  'post_name' => $login, // The name (slug) for your post
				  'post_status' =>  'private', //Set the status of the new post. 
				  'post_title' => $name, //The title of your post.
				  'post_type' => strtolower(get_option($cruser['pfix'].'setting_post_type')), //You may want to insert a regular post, page, link, a menu item or some custom post type
				);
				debug_say("Creating the new Client post relating to this new user.");
				$new_client_post_id = wp_insert_post($client_post);
				
				
				// Add additional field data to the Client post.
				if ($new_client_post_id ) {
					debug_say("new_client_post_id was TRUE.");

					debug_say("Adding custom taxonomy term(s).");
					$taxonomy = get_option($cruser['pfix'].'setting_taxonomy_name');
					$taxonomy_terms = get_option($cruser['pfix'].'setting_taxonomy_terms');
					$new_client_post_type_terms = wp_set_object_terms($new_client_post_id, $taxonomy_terms, $taxonomy);
					
					if (count($new_client_post_fields) > 0) {
						debug_say("new_client_post_fields was greater than zero");
						foreach($new_client_post_fields as $key => $value) {
							debug_say("Adding $value to $key in new Client.");
							add_post_meta($new_client_post_id, $key, $value, true);
						}
					} else {
						debug_say('Created new Client post but no extra fields were given.');
					}
				} else {
					debug_say('Creating new client post type failed.');
				}
			}
		} else {
			debug_say('User already exists');
		}
		return $new_user_id;
	}	
	get_current_user()
?>
