jQuery(document).ready(function($) {
	function seoe_title_check(field) {
		var max = seoe_ajax.title_length;
		var len = $('#'+field).val().length;
		var char = max - len;

		if(len > max) {
			return len - max;
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
		var max = seoe_ajax.desc_length;
		var len = $('#'+field).val().length;
		var char = max - len;

		if(len > max) {
			return len - max;
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
		else if(title_check !== true) {
			$('#title_length_set_notice').html(seoe_ajax.title_length);
			$('#title_length_char_notice').html(title_check);			
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
		else if(desc_check !== true) {
			$('#desc_length_set_notice').html(seoe_ajax.desc_length);
			$('#desc_length_char_notice').html(desc_check);						
			$('#seoe_desc_length').show();
		}
	}
});