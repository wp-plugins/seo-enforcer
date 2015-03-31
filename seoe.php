<?php

/*
	Plugin Name: SEO Enforcer
	Plugin URI: https://mainehost.com/wordpress-plugins/	
	Description: Enforces SEO restrictions. Requires WordPress SEO by Yoast.
	Author: Maine Hosting Solutions
	Author URI: http://mainehost.com/
	Version: 1.1.1	
*/

if(!class_exists("seo_enforcer")) {
	/**
	 * @package default
	 */
	class seo_enforcer {
		/**
		 * @var string Lets the plugin know what folder this lives in.
		 */
		protected $plugin_folder = '';

	    /**
	     * Setup hooks, actions, filters, and whatever is needed for the plugin to run.
	     */
		function __construct() {
			// require 'constants.php';

			register_activation_hook( __FILE__, array($this,'activate'));

			// add_action('plugins_loaded', array($this,'maybe_deactivate'));
			add_action('plugins_loaded', array($this,'notice_check'));
			add_action('admin_menu', array($this,'menu'));

			DEFINE('SEOE_NAME','SEO Enforcer');
			DEFINE('SEOE_MENU_NAME','SEO Enforcer');
			DEFINE('SEOE_WP_SEO_NAME','WordPress SEO by Yoast');
			DEFINE('SEOE_WPSEO_PATH','wordpress-seo/wp-seo.php');
			DEFINE('SEOE_WPSEOP_PATH','wordpress-seo-premium/wp-seo-premium.php');

			// DEFINE('SEOE_DEP_ERROR','<p>%s must be installed and active.</p>');
			DEFINE('SEOE_DEP_ERROR','<div class="error"><p>%s is not installed or active. ' . SEOE_NAME . ' will not function until %s is installed and activated.</p></div>');
			// DEFINE('SEOE_DEP_DEACT_ERROR','<div class="error"><p>%s is not installed or active. ' . SEOE_NAME . ' will not function until %s is installed and activated.</p></div>');			
		}
		/**
		 * Creates the menu in WP admin for the plugin.
		 */
		function menu() {
			$this->plugin_folder = plugin_basename(dirname(__FILE__));

			add_submenu_page('options-general.php', SEOE_MENU_NAME, SEOE_MENU_NAME,'administrator', $this->plugin_folder, array($this,'admin'));
		}
		/**
		 * Admin area for this plugin.
		 */
		function admin() {
			require 'seoe_admin.php';
		}	
		/**
		 * Run when this plugin is activated.
		 */
		function activate() {
			$this->check_dependencies();
		}			
		/**
		 * Used to check if dependencies are active when a plugin is deactivated.
		 */
		// function maybe_deactivate() {
		function notice_check() {		
			// $this->dependencies('deactivate');
			$this->dependencies();

			if($this->dep_error) {
	            // require_once(ABSPATH . 'wp-admin/includes/plugin.php');
	            // deactivate_plugins(plugin_basename( __FILE__ ));
	            add_action('admin_notices', array($this,'deactivate_notice'));
	        }
	        else {
				if(get_option('seoe_title')) add_filter('wpseo_title', array($this,'title_check'), 99);
				if(get_option('seoe_desc')) add_filter('wpseo_metadesc', array($this,'desc_check'), 99);
				if(get_option('seoe_h1')) add_filter('the_content', array($this,'content_check'), 99);
	        }
		}		
	    /**
	     * Gives an error if trying to activate the plugin without dependencies.
	     * @param string $message The error message returned.
	     * @param mixed $errno Error number returned.
	     * @return mixed It will either echo the error or fire trigger_error() as needed.
	     */
		function br_trigger_error($message = false, $errno) {
		    if(isset($_GET['action']) && $_GET['action'] == 'error_scrape') {
		        echo '<strong>' . $message . '</strong>';
		        exit;
		    }
		    else {
		        trigger_error($message, $errno);
		    }
		}
		/**
		 * Echos the error generated by deactivating a dependency.
		 * @return string
		 */
		function deactivate_notice() {
			echo $this->dep_error;
		}
		/**
		 * Checks to see that the dependencies are installed and active.
		 * @param type $stage Whether it's currently activating or deactivating a plugin.
		 */
		// function dependencies($stage) {
		function dependencies() {		
			if((!in_array(SEOE_WPSEO_PATH, apply_filters('active_plugins', get_option('active_plugins')))) && ((!in_array(SEOE_WPSEOP_PATH, apply_filters('active_plugins', get_option('active_plugins')))))) {
				// if($stage == 'activate') $this->dep_error .= sprintf(SEOE_DEP_ERROR, SEOE_WP_SEO_NAME);
				// else $this->dep_error .= sprintf(SEOE_DEP_DEACT_ERROR, SEOE_WP_SEO_NAME, SEOE_WP_SEO_NAME);
				$this->dep_error .= sprintf(SEOE_DEP_ERROR, SEOE_WP_SEO_NAME, SEOE_WP_SEO_NAME);				
			}
		}
		/**
		 * Core function to check for dependencies.
		 */
		function check_dependencies() {
			// $this->dependencies('activate');
			$this->dependencies();

			if($this->dep_error) $this->br_trigger_error($this->dep_error, E_USER_ERROR);
		}
	    /**
	     * Filter function for wpseo_title. Ensures titles are no longer than 70 characters.
	     * @param string $title The title passed in from wpseo_title.
	     * @return string Returns the adjusted title.
	     */
		function title_check($title) {
			global $post;

			if(get_option('seoe_title')) {
				$ex = get_option('seoe_title_trunc_ex');
				$length = get_option('seoe_title_length');

				if(!$length) $length = 70;

				if($ex) {
					$ex = array_map('trim', explode(',', $ex));					

					if(is_home()) {
						if(!in_array('blog', $ex)) $proceed = 1;
						else $procedd = 0;
					}
					else {
						if(!in_array($post->ID, $ex)) $proceed = 1;
						else $proceed = 0;
					}
				}
				else {
					$proceed = 1;
				}
				if($proceed && strlen($title) > $length) {
					$type = get_option('seoe_title_trunc_type');

					if($type == 1) {
						$new_title = substr($title, 0, $length);						
					}
					else {
						$new_length = $length - 3;
						if($new_length < 0) $new_length = 0;
						$new_title = substr($title, 0, $new_length) . '...';
					} 

					if($new_title) return $new_title;
					else return $title;
				}
				else {
					return $title;
				}
			}
			else {
				return $title;
			}
		}
	    /**
	     * Filter function for wpseo_metadesc. Ensures descriptions are no more than 160 charaters.
	     * @return string The meta description tag on success.
	     */
		function desc_check($desc) {
			global $post;			

			if(get_option('seoe_desc')) {
				$ex = get_option('seoe_desc_trunc_ex');
				$length = get_option('seoe_desc_length');

				if(!$length) $length = 160;

				if($ex) {
					$ex = array_map('trim', explode(',', $ex));					

					if(is_home()) {
						if(!in_array('blog', $ex)) $proceed = 1;
						else $procedd = 0;
					}
					else {
						if(!in_array($post->ID, $ex)) $proceed = 1;
						else $proceed = 0;
					}
				}
				else {
					$proceed = 1;
				}
				if($proceed && strlen($desc) > $length) {
					$type = get_option('seoe_desc_trunc_type');

					if($type == 1) {
						$new_desc = substr($desc, 0, $length);
					}
					else {
						$new_length = $length - 3;
						if($new_length < 0) $new_length = 0;
						$new_desc = substr($desc, 0, $new_length) . '...';						
					}

					if($new_desc) return $new_desc;
					else return $desc;
				}
				else {
					return $desc;
				}
			}
			else {
				return $desc;
			}
		}
		/**
		 * Determines if the content needs to be checked based on settings.
		 * @param mixed $content Content passed in from the filter.
		 * @return mixed
		 */
		function content_check($content) {
			global $post;

			if($ex = get_option('seoe_h1_ex')) {
				$ex = array_map('trim', explode(',', $ex));

				if(is_home()) {
					if(!in_array('blog', $ex)) $proceed = 1;
					else $procedd = 0;
				}
				else {
					if(!in_array($post->ID, $ex)) $proceed = 1;
					else $proceed = 0;
				}
			}
			else {
				$proceed = 1;
			}
			if($proceed) {
				$content = $this->content_clean($content);				
			}

			return $content;
		}
		/**
		 * This will check content to make sure there are no H1 tags and if so it will change
		 * those to H2 tags.
		 * @param mixed $content The content passed it from the filter function.
		 * @return mixed
		 */		
		function content_clean($content) {
			$content = str_replace('<h1','<h2', $content);
			$content = str_replace('</h1>','</h2>', $content);
			return $content;
		}
	}
}

$seo_enforcer = new seo_enforcer();

?>