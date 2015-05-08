<?php

/*
	Plugin Name: SEO Enforcer
	Plugin URI: https://mainehost.com/wordpress-plugins/	
	Description: Enforces SEO restrictions. Requires WordPress SEO by Yoast.
	Author: Maine Hosting Solutions
	Author URI: http://mainehost.com/
	Version: 1.2.3
*/

if(!class_exists("seo_enforcer")) {
	/**
	 * @package default
	 */
	class seo_enforcer {
		/**
		 * @var string - Lets the plugin know what folder this lives in.
		 */
		protected $plugin_folder = '';

		/**
		 * @var bool - Flags if there's a dependency error.
		 */
		protected $dep_error = false;

	    /**
	     * Setup hooks, actions, filters, and whatever is needed for the plugin to run.
	     */
		function __construct() {
			register_activation_hook( __FILE__, array($this,'activate'));

			add_action('plugins_loaded', array($this,'notice_check'));
			add_action('admin_menu', array($this,'menu'));
			add_action('current_screen', array($this,'check_screen'));
			add_action('plugins_loaded', array($this,'upgrade_check'));

			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this,'add_action_link'));

			define('SEOE_SETTINGS_VER', 1);
			define('SEOE_NAME','SEO Enforcer');
			define('SEOE_MENU_NAME','SEO Enforcer');
			define('SEOE_WP_SEO_NAME','WordPress SEO by Yoast');
			define('SEOE_WPSEO_PATH','wordpress-seo/wp-seo.php');
			define('SEOE_WPSEOP_PATH','wordpress-seo-premium/wp-seo-premium.php');
			define('SEOE_TITLE_LENGTH', 59);
			define('SEOE_DESC_LENGTH', 156);

			define('SEOE_DEP_ERROR','<div class="error"><p>%s is not installed or active. ' . SEOE_NAME . ' will not function until %s is installed and activated.</p></div>');
		}
		/**
		 * Adds the extra links on the plugins page.
		 * @param array $links - The exsting default links.
		 * @return array - Merge in my link array to the existing and return that.
		 */
		function add_action_link($links) {
			$path = admin_url();

			$mylinks = array(
				'<a href="https://wordpress.org/support/view/plugin-reviews/seo-enforcer" target="_blank">Rate and Review</a>',
				'<a href="' . $path . 'options-general.php?page=seo-enforcer">Settings</a>'
			);

			return array_merge($mylinks, $links);
		}	
		function upgrade_check() {
			$version = get_option('seoe_settings_version');

			if(!$version) {
				update_option('seoe_settings_version', SEOE_SETTINGS_VER);
			}
			elseif($version && $version != SEOE_SETTINGS_VER) {
				update_option('seoe_settings_version', SEOE_SETTINGS_VER);
			}
		}	
		/**
		 * Creates the menu in WP admin for the plugin.
		 */
		function menu() {
			$this->plugin_folder = plugin_basename(dirname(__FILE__));

			add_submenu_page('options-general.php', SEOE_MENU_NAME, SEOE_MENU_NAME,'administrator', $this->plugin_folder, array($this,'admin'));
		}

		/**
		 * Determines if this is a post screen to enable the SEO checks.
		 */
		function check_screen() {
			$notice_types = array('post','edit-tags','toplevel_page_shopp-products','catalog_page_shopp-categories');
			$screen = get_current_screen();

			if(is_admin() && in_array($screen->base, $notice_types) && get_option('seoe_post_notices')) {
				$title_check = get_option('seoe_title');
				$desc_check = get_option('seoe_desc');

				if($title_check) {
					$title_trunc_type = get_option('seoe_title_trunc_type');
					$title_length = get_option('seoe_title_length');
					$title_exceptions = get_option('seoe_title_trunc_ex');
				
					if($title_exceptions) {
						$ex = array_map('trim', explode(',', $title_exceptions));
						$post_id = $_GET['post'];

						if($screen->base == 'post' && in_array($post_id, $ex)) $title_length = 9999;
					}
				}
				else {
					$title_length = SEOE_TITLE_LENGTH;
				}
				if($desc_check) {
					$desc_trunc_type = get_option('seoe_desc_trunc_type');
					$desc_length = get_option('seoe_desc_length');
					$desc_exceptions = get_option('seoe_desc_trunc_ex');

					if($desc_exceptions) {
						$ex = array_map('trim', explode(',', $desc_exceptions));
						$post_id = $_GET['post'];

						if($screen->base == 'post' && in_array($post_id, $ex)) $desc_length = 9999;
					}					
				}
				else {
					$desc_length = SEOE_DESC_LENGTH;
				}

				wp_enqueue_script('mhs_seoe_admin', plugin_dir_url( __FILE__ ) . 'admin.js', array( 'jquery'), false, true);
				wp_localize_script('mhs_seoe_admin', 'seoe_ajax', array('ajaxurl' => admin_url('admin-ajax.php'),'title_length'=>$title_length,'desc_length'=>$desc_length));

				add_action('admin_notices', array($this,'post_notice'));
			}
		}
		/**
		 * Creates the notices to be shown for SEO checks.
		 */
		function post_notice() {
       		echo '<div class="update-nag" style="display: none;" id="seoe_title_error">The SEO Title field should be manually filled in.</div>';
       		echo '<div class="update-nag" style="display: none;" id="seoe_desc_error">The Meta Description field should be manually filled in.</div>'; 
       		echo '<div class="error" style="display: none;" id="seoe_title_length"><p>The SEO Title field is longer than the recommended length of <span id="title_length_set_notice"></span> by <span id="title_length_char_notice"></span> characters</p></div>';
       		echo '<div class="error" style="display: none;" id="seoe_desc_length"><p>The Meta Description is longer than the recommended length of  <span id="desc_length_set_notice"></span> by <span id="desc_length_char_notice"></span> characters</p></div>'; 	       		
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
		function notice_check() {		
			$this->dependencies();

			if($this->dep_error) {
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
		function dependencies() {		
			if((!in_array(SEOE_WPSEO_PATH, apply_filters('active_plugins', get_option('active_plugins')))) && ((!in_array(SEOE_WPSEOP_PATH, apply_filters('active_plugins', get_option('active_plugins')))))) {
				$this->dep_error .= sprintf(SEOE_DEP_ERROR, SEOE_WP_SEO_NAME, SEOE_WP_SEO_NAME);				
			}
		}
		/**
		 * Core function to check for dependencies.
		 */
		function check_dependencies() {
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
				$type = get_option('seoe_title_trunc_type');

				if(!$length) $length = SEOE_TITLE_LENGTH;

				if($type == 2) {
					$length -= 3;

					if($length < 0) $length = SEOE_TITLE_LENGTH; # If it would be 0 characters or less then give it the default length
				} 	
				if($ex) {
					$ex = array_map('trim', explode(',', $ex));					

					if(is_home()) {
						if(!in_array('blog', $ex)) $proceed = 1;
						else $proceed = 0;
					}
					else {
						if(!in_array($post->ID, $ex)) $proceed = 1;
						else $proceed = 0;
					}
				}
				else {
					$proceed = 1; # No exceptions
				}
				if($proceed && strlen($title) > $length) {
					$raw_title = explode(' ', $title);

					foreach($raw_title as $key=>$value) {
						$test_title = $test_title . $value . ' ';

						if(strlen($test_title) <= $length) {
							$new_title .= $value . ' ';
						}
						else {
							$new_title = rtrim($new_title,' ');
							if($type == 2) $new_title .= '...';
							break;
						}
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
				$type = get_option('seoe_title_trunc_type');

				if(!$length) $length = SEOE_DESC_LENGTH;

				if($type == 2) {
					$length -= 3;

					if($length < 0) $length = SEOE_DESC_LENGTH; # If it would be 0 characters or less then give it the default length
				} 

				if($ex) {
					$ex = array_map('trim', explode(',', $ex));					

					if(is_home()) {
						if(!in_array('blog', $ex)) $proceed = 1;
						else $proceed = 0;
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
					$raw_desc = explode(' ', $desc);

					foreach($raw_desc as $key=>$value) {
						$test_desc = $test_desc . $value . ' ';

						if(strlen($test_desc) <= $length) {
							$new_desc .= $value . ' ';
						}
						else {
							$new_desc = rtrim($new_desc,' ');
							if($type == 2) $new_desc .= '...';
							break;
						}
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