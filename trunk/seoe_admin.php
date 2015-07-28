<?php

if(!class_exists('seoe_admin')) {
	class seoe_admin {
		function __construct() {
			$this->plugin_folder = plugin_basename(dirname(__FILE__));
		}		
		function options() {
			$image_path = plugins_url('images/', __FILE__);
			$settings = get_option('seoe_settings');

			if($settings) $settings = unserialize($settings);
			extract($settings);

			// $seo_title_length = get_option('seoe_title_length');
			// $seoe_desc_length = get_option('seoe_desc_length');
			// $seoe_title_trunc_type = get_option('seoe_title_trunc_type');
			// $seoe_desc_trunc_type = get_option('seoe_desc_trunc_type');

			define('SEOE_SHOPP_PATH','shopp/Shopp.php'); # Shopp
			define('SEOE_SSEO_PATH','shopp-seo/sseo.php'); # Shopp SEO

			# See if Shopp is installed but Shopp SEO is not
			if(in_array(SEOE_SHOPP_PATH, apply_filters('active_plugins', get_option('active_plugins'))) && !in_array(SEOE_SSEO_PATH, apply_filters('active_plugins', get_option('active_plugins')))) {
				$left_style = 'float: left; width: 73%; margin-right: 2%;';
				$right_style = 'float: right; width: 20%; border-left: 1px solid #c5c5c5; padding-left: 2%; padding-right: 2%;';
			}
			else {
				$left_style = 'width: 100%';
				$right_style = 'display: none;';
			}

			$code = '
				<style>
				#seoe_settings .lcol {
					clear: left;
					float: left;
					font-weight: bold;
					width: 150px;
					margin-right: 10px;
					margin-bottom: 10px;
				}
				#seoe_settings .rcol {
					max-width: 50%;
					float: left;
					margin-bottom: 10px;
				}
				</style>

				<div class="wrap">
					<form method="post" action="?page=' . $this->plugin_folder . '">
					<input type="hidden" name="mode" value="options">

					<h1>' . SEOE_NAME . '</h1>

					<h2>Admin Area</h2>

					<div id="seoe_settings">
						<div style="' . $left_style . '">
							<div class="lcol">
								<label for="seoe_post_notices">Enable SEO Notices</label>
							</div>
							<div class="rcol">
								<input type="checkbox" name="seoe_post_notices" id="seoe_post_notices" value="1"' . (($seoe_post_notices) ? ' checked' : '') . '> Yes<br />
								<em>Show notices on the admin screens where WordPress SEO or Shopp SEO are used.</em>
							</div>

							<h2 style="clear: both;">Frontend</h2>

							<h3>Title Tag</h3>

							<div class="lcol">
								<label for="seoe_title">Check:</label>
							</div>
							<div class="rcol">
								<input type="checkbox" name="seoe_title" id="seoe_title" value="1"' . (($seoe_title) ? ' checked' : '') . '> Yes<br />
								<em>Turns on the title tag check features for the below settings.</em>
							</div>
							<div class="lcol">
								<label for="seoe_title_length">Length:</label>
							</div>
							<div class="rcol">
								<input type="text" name="seoe_title_length" id="seoe_title_length" size="4" value="' . (($seoe_title_length) ? $seoe_title_length : SEOE_TITLE_LENGTH) . '"> characters
							</div>
							<div class="lcol">
								Truncation:
							</div>
							<div class="rcol">
								<input type="radio" name="seoe_title_trunc_type" id="seoe_title_trunc_type1" value="1"' . (($seoe_title_trunc_type == 1) ? ' checked' : '') . '> <label for="seoe_title_trunc_type1">Terminate at character count, IE: This Title is too</label><br />
								<input type="radio" name="seoe_title_trunc_type" id="seoe_title_trunc_type2" value="2"' . (($seoe_title_trunc_type == 2 || !$seoe_title_trunc_type) ? ' checked' : '') . '> <label for="seoe_title_trunc_type2">Terminate with "...", IE: This Title is too...</label>
							</div>
							<div class="lcol">
								<label for="seoe_title_trunc_ex">Truncation Exceptions:</label>
							</div>
							<div class="rcol">
								<input type="text" name="seoe_title_trunc_ex" id="seoe_title_trunc_ex" size="50" value="' . $seoe_title_trunc_ex . '"><br /><em>List of post or page IDs that are an exception to the Title Truncation if enabled. List IDs separated by a comma.<br />
								Example: 50,99,60. If you want to list your blog index then enter: blog</em>
							</div>

							<h3 style="clear: both;">Meta Description</h3>

							<div class="lcol">
								<label for="seoe_desc">Check:</label>
							</div>
							<div class="rcol">
								<input type="checkbox" name="seoe_desc" id="seoe_desc" value="1"' . (($seoe_desc) ? ' checked' : '') . '> Yes<br />
								<em>Turns on the title meta description check features for the below settings.</em>
							</div>
							<div class="lcol">
								<label for="seoe_desc_length">Length:</label> 
							</div>
							<div class="rcol">
								<input type="text" name="seoe_desc_length" id="seoe_desc_length" size="4" value="' . (($seoe_desc_length) ? $seoe_desc_length : SEOE_DESC_LENGTH) . '"> characters
							</div>
							<div class="lcol">
								Truncation:
							</div>
							<div class="rcol">
								<input type="radio" name="seoe_desc_trunc_type" id="seoe_desc_trunc_type1" value="1"' . (($seoe_desc_trunc_type == 1) ? ' checked' : '') . '> <label for="seoe_desc_trunc_type1">Terminate at character count, IE: This Description is too</label><br />
								<input type="radio" name="seoe_desc_trunc_type" id="seoe_desc_trunc_type2" value="2"' . (($seoe_desc_trunc_type == 2 || !$seoe_desc_trunc_type) ? ' checked' : '') . '> <label for="seoe_desc_trunc_type2">Terminate with "...", IE: This Title is too...</label>
							</div>
							<div class="lcol">
								<label for="seoe_desc_trunc_ex">Truncation Exceptions:</label>
							</div>
							<div class="rcol">
								<input type="text" name="seoe_desc_trunc_ex" id="seoe_desc_trunc_ex" size="50" value="' . $seoe_desc_trunc_ex . '"><br />
								<em>List of post or page IDs that are an exception to the Description Truncation if enabled. List IDs separated by a comma.<br />
								Example: 50,99,60. If you want to list your blog index then enter: blog</em>
							</div>

							<h3 style="clear: both;">H1</h3>

							<div class="lcol">
								<label for="seoe_h1">Check:</label>
							</div>
							<div class="rcol">
								<input type="checkbox" name="seoe_h1" id="seoe_h1" value="1"' . (($seoe_h1) ? ' checked' : '') . '> Yes<br />
								<em>Enforce no H1 tags in the content since it should be done by the theme. This will replace any H1 tags in content with an H2 tag instead.</em>
							</div>
							<div class="lcol">
								<label for="seoe_h1_ex">Exceptions:</label>
							</div>
							<div class="rcol">
								<input type="text" name="seoe_h1_ex" id="seoe_h1_ex" size="50" value="' . $seoe_h1_ex . '"><br />
								<em>List of post or page IDs that are an exception to the HQ check if enabled. List IDs separated by a comma, IE: 50,99,60. If you want to list your blog index then enter: blog</em>
							</div>

							<h3 style="clear: both;">Image</h3>

							<div class="lcol">
								<label for="seoe_img">Check:</label> 
							</div>
							<div class="rcol">
								<input type="checkbox" name="seoe_img" id="seoe_img" value="1"' . (($seoe_img) ? ' checked' : '') . '> Yes<br />
								<em>This will check if your images have the alt and title attribute.<br />
									If neither is present then an alt and title will be created using the image name.<br />
									If either an alt or title is present, but not both, then the missing attribute will be created using the value of the one that is present.</em>
							</div>
							<p style="clear: both;">
							<input type="submit" value="Save" class="button button-primary">
							</form>
							</p>
						</div>
						<div style="' . $right_style . '">
							<a href="https://wordpress.org/plugins/shopp-seo/" target="_blank"><img src="' . $image_path . 'shopp_seo.png" style="max-width: 100%;"></a>
							<p>
							If you use Shopp then check out <a href="https://wordpress.org/plugins/shopp-seo/" target="_blank">Shopp SEO</a> to fix the integration of WordPress SEO and Shopp.						
							</p>
						</div>
					</div>
				</div>';

			echo $code;
		}
	}
}

$seoe_admin = new seoe_admin();

switch($mode) {
	default:
		if($_POST) {
			foreach($_POST as $key=>$value) {
				if($key != 'mode' && $key != 'submit') {
					$settings[$key] = $value;
				}
			}

			update_option('seoe_settings', serialize($settings));
		}

		$seoe_admin->options();
	break;
}

?>