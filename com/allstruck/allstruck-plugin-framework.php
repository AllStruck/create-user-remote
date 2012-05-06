<?php
/*
This is the class container for the AllStruck Plugin Framework.
Only use this file as an include from an existing or new plugin.
The purpose for using this framework is to easily create new plugins, to create new functionality within existing plugins, or to replace existing functionality within existing plugins in an easy and standardized way.

Currently this framework mostly just helps to create a new plugin with methods and properties stored in classes to avoid incompatability among other installed plugins and themes. Along with that comes some easy methods to create new settings pages, groups, and fields.
*/

define( 'PHP_VERSION_MINIMUM', '5.0.0' );

if (!class_exists('AllStruck_Plugin_Framework')) {
	if (version_compare( PHP_VERSION, PHP_VERSION_MINIMUM ) >= 0 || true) {
		class AllStruck_Plugin_Framework {
			function __construct() {
				$this->AllStruck_Plugin_Framwork($settings_array);
			}
			public function ifset($value, $else) {
				return isset($value)? $value : ($else? $else : '');
			}
			function AllStruck_Plugin_Framwork($settings_array) {
				$this->name_full 							= $this->ifset($settings_array['name_full']);
				$this->name_short 							= $settings_array['name_short'];
				$this->name_slug 							= $settings_array['name_slug'];
				$this->name_sanitized 						= sanitize_title( $settings_array['name_sanitized'] );
				$this->short_object_prefix 					= $settings_array['short_object_prefix'];
				$this->version 								= $settings_array['version'];
				$this->release_date 						= $settings_array['release_date'];
				$this->last_update 							= $settings_array['last_update'];
				$this->author_name 							= $settings_array['author_name'];
				$this->author_url 							= $settings_array['author_url'];
				$this->plugin_support_url 					= $settings_array['plugin_support_url'];
				$this->plugin_home_url 						= $settings_array['plugin_support_url'];

				$this->pfix			 						= $settings_array['pfix'];
				
				//$this->creates_settings_submenu_page 		= $settings_array['creates_settings_submenu_page'];
				$this->settings_submenu_link_title 			= $settings_array['settings_submenu_link_title'];
				$this->settings_submenu_page_title 			= $settings_array['settings_submenu_page_title'];				
				$this->settings_submenu_page_donate_link 	= $settings_array['settings_submenu_page_donate_link'];

				$this->has_translation						= $settings_array['has_translation'];
				$this->translation_domain					= $settings_array['translation_domain'];
				$this->translations							= $settings_array['translations'];
				$this->plugin_file							= $settings_array['plugin_file'];
				$this->plugin_directory_path 				= $settings_array['plugin_directory_path'];
				$this->has_activation_notice				= $settings_array['has_activation_notice'];
				$this->admin_activation_notice				= $settings_array['admin_activation_notice'];


				register_activation_hook( $this->plugin_file, array( &$this, 'reset_options' ) );
				
				
				if( $this->creates_settings_submenu_page ) {
					add_action( 'admin_menu', array( &$this, 'create_settings_submenu' ) );
					add_filter( 'plugin_action_links', array( &$this, 'plugin_actions' ), 10, 2 );
					add_filter( 'plugin_row_meta', array( &$this, 'add_settings_link' ), 10, 2 );
				}
				if( $this->has_activation_notice )	add_action( 'admin_notices',	array( &$this, 'admin_activation_notice' ) );
			}
			
			function create_menu() {
				
			}
			function plugin_actions() {
				
			}
			function admin_activation_notice() {
				echo '<div class="updated"><p>';
					echo $this->admin_activation_notice;
				echo '</p></div>';
			}
			function load_translation () {
				load_plugin_textdomain(
					$this->tdomain,
					WP_PLUGIN_URL . '/' . $this->plugin_directory_path . '/languages', 
					$this->plugin_directory_path . '/languages' 
				);
				if( $this->has_option_page ) {
					load_plugin_textdomain(
						'yd-options-page', // must be same as in YD_OptionPage tpl_tdomain private setting 
						'wp-content/plugins/' . $this->plugin_directory_path . '/languages', 
						$this->plugin_directory_path . '/languages' 
					);
				}
			}
			
			function reset_options() {
				$this->reset_options();
			}
		}
	}
}


?>