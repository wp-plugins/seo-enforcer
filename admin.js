jQuery(document).ready(function($) {
	function seoe_title_check(field) {
		var max = 59;
		var len = $('#'+field).val().length;
		var char = max - len;

		if(len >= max) {
			return false;
		}
		else {
			if(len <= 0) {
				return null;
			}
			else {
				return true;
			}
		}
	}
	function seoe_desc_check(field) {
		var max = 156;
		var len = $('#'+field).val().length;
		var char = max - len;

		if(len >= max) {
			return false;
		}
		else {
			if(len <= 0) {
				return null;
			}
			else {
				return true;
			}
		}
	}
	if($('#yoast_wpseo_title').length || $('#wpseo_title').length || $('#mhs_seo_title').length) {
		if($('#yoast_wpseo_title').length) {
			var seoe_title_field = 'yoast_wpseo_title';
		}
		else if($('#wpseo_title').length) {
			var seoe_title_field = 'wpseo_title';
		}
		else {
			var seoe_title_field = 'mhs_seo_title';
		}

		var title_check = seoe_title_check(seoe_title_field);

		if(title_check === null) {
			$('#seoe_title_error').show();
		}
		if(title_check === false) {
			$('#seoe_title_length').show();
		}
	}
	if($('#yoast_wpseo_metadesc').length || $('#wpseo_desc').length || $('#mhs_seo_desc').length) {
		if($('#yoast_wpseo_metadesc').length) {
			var seoe_desc_field = 'yoast_wpseo_metadesc';
		}
		else if($('#wpseo_desc').length) {
			var seoe_desc_field = 'wpseo_desc';			
		}
		else {
			var seoe_desc_field = 'mhs_seo_desc';
		}

		var desc_check = seoe_desc_check(seoe_desc_field);

		if(desc_check === null) {
			$('#seoe_desc_error').show();
		}
		if(desc_check === false) {
			$('#seoe_desc_length').show();
		}
	}
});