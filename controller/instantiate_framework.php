<?php
/*
 * @class cruseremote_instantiate_allstruck_plugin_framework
 * @package create_remote_user
 * @framework AllStruck Plugin Framework
 * @author AllStruck
*/

global $cruser;
$cruser = new AllStruck_Plugin_Framework(
	array(
		'pfix' => 'cruser',
		'name_full' => 'Create User Remote',
		'name_short' => 'Create User Remote',
		'name_slug' => 'create-user-remote',
		'short_object_prefix' => 'cruser',
		'version' => '0.0.1',
		'author_name' => 'AllStruck',
		'author_url' => 'http://www.allstruck.com/',
		'plugin_support_url' => 'http://create-user-remote.allstruck.com/support/',
		'plugin_home_url' => 'http://create-user-remote.allstruck.com/',
		
		'creates_settings_submenu_page' => true,
		'settings_submenu_link_title' => 'Create User Remote',
		'settings_submenu_page_text' => 'Create User Remote Settings',
		'settings_submenu_page_donate_link' => 'http://www.paypal.com/',
		
		'has_admin_activation_notice' => true,
		'admin_activation_notice' => 'Complete installation by mapping fields in settings.'
	)
);

//Main plugin class.
if (!class_exists(create_user_remote)) {
	class create_user_remote extends AllStruck_Plugin_Framework {
		
		public $name_full = '';
		    function __construct(  ) {
		    	// php5 constructor
				$this->create_user_remote(  );
				
				$this->name_full = 'Create User Remote';
				$this->name_short = 'Cruser';
				$this->name_slug = 'create-user-remote';
				$this->name_compatable = 'create_user_remote';
				
				$this->pfix = 'cruser_';

			}
		function create_user_remote() {		
			
			register_activation_hook( __FILE__, array( &$this, 'rewrite_activate' ) );
			register_deactivation_hook( __FILE__, array( &$this, 'rewrite_deactivate' ) );
			add_action( 'init', array( &$this, 'create_rewrite_for_new_user' ) );
		}
		function create_rewrite_for_new_user() {
			add_rewrite_rule('wufoo-wp-webhook-newuser/?$', 
			str_replace( 
	    		home_url(), 
	    		"", 
	    		WP_PLUGIN_URL . '/' 
	    			. dirname( plugin_basename( __FILE__ ) ) 
	    			. '/newuser.php' 
	    	), 
	    	'top'
	    	);
		}
		function rewrite_activate() {
			$this->create_rewrite_for_new_user();
			flush_rewrite_rules();
		}
	
		function rewrite_deactivate() {
			flush_rewrite_rules();
		}

	}
}


?>