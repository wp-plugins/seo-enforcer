<?php

if(!class_exists('seoe_admin')) {
	class seoe_admin {
		function __construct() {
			$this->plugin_folder = plugin_basename(dirname(__FILE__));
		}		
		function options() {
			$seo_title_length = get_option('seoe_title_length');
			$seoe_desc_length = get_option('seoe_desc_length');
			$seoe_title_trunc_type = get_option('seoe_title_trunc_type');
			$seoe_desc_trunc_type = get_option('seoe_desc_trunc_type');


			$code = '
				<form method="post" action="?page=' . $this->plugin_folder . '">
				<input type="hidden" name="mode" value="options">
				<h1>' . SEOE_NAME . '</h1>
				<p>
				<strong>Title Checks:</strong> Enforce title lengths to <input type="text" name="seoe_title_length" size="4" value="' . (($seo_title_length) ? $seo_title_length : 70) . '"> characters.<br />
				<input type="radio" name="seoe_title" value="1"' . ((get_option('seoe_title')) ? ' checked' : '') . '> Yes <input type="radio" name="seoe_title" value="0"' . ((!get_option('seoe_title')) ? ' checked' : '') . '> No
				</p>
				<p>
				<strong>Title Truncation:</strong><br />
				<input type="radio" name="seoe_title_trunc_type" value="1"' . (($seoe_title_trunc_type == 1) ? ' checked' : '') . '> Terminate at character count, IE: This Title is too Lo<br />
				<input type="radio" name="seoe_title_trunc_type" value="2"' . (($seoe_title_trunc_type == 2 || !$seoe_title_trunc_type) ? ' checked' : '') . '> Terminate with "...", IE: This Title is too...<br />
				</p>
				<p>
				<strong>Description Checks:</strong> Enforce description lengths to <input type="text" name="seoe_desc_length" size="4" value="' . (($seoe_desc_length) ? $seoe_desc_length : 160) . '"> characters.<br />
				<input type="radio" name="seoe_desc" value="1"' . ((get_option('seoe_desc')) ? ' checked' : '') . '> Yes <input type="radio" name="seoe_desc" value="0"' . ((!get_option('seoe_desc')) ? ' checked' : '') . '> No
				</p>
				<p>
				<strong>Description Truncation:</strong><br />
				<input type="radio" name="seoe_desc_trunc_type" value="1"' . (($seoe_desc_trunc_type == 1) ? ' checked' : '') . '> Terminate at character count, IE: This Description is too Lo<br />
				<input type="radio" name="seoe_desc_trunc_type" value="2"' . (($seoe_desc_trunc_type == 2 || !$seoe_desc_trunc_type) ? ' checked' : '') . '> Terminate with "...", IE: This Title is too...<br />
				</p>
				<p>
				<strong>H1 Checks:</strong> Enforce no H1 tags in the content since it should be done by the theme. This will replace any H1 tags in content with an H2 tag instead.<br />
				<input type="radio" name="seoe_h1" value="1"' . ((get_option('seoe_h1')) ? ' checked' : '') . '> Yes <input type="radio" name="seoe_h1" value="0"' . ((!get_option('seoe_h1')) ? ' checked' : '') . '> No
				</p>
				<p>
				<strong>H1 Check Exceptions:</strong> <input type="text" name="seoe_h1_ex" size="50" value="' . get_option('seoe_h1_ex') . '"><br />List of post or page IDs that are an exception to the HQ check if enabled. List IDs separated by a comma, IE: 50,99,60
				</p>
				<p>
				<input type="submit" value="Save" class="button button-primary">
				</form>
				</p>';

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
					update_option($key, $value);
				}
			}
		}

		$seoe_admin->options();
	break;
}

?>