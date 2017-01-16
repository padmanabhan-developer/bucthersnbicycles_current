BBThemeAdmin = {
	init: function () {
		var that = this;

		jQuery('.bb_theme_set_image').unbind('click');
		jQuery('.bb_theme_set_image').click(function(event) {
			that.uploadImageDialog(event);
		});
	},

	uploadImageDialog: function(event) {
		var id = jQuery(event.target).attr('data-input-id');
		window.bbThemeAdminImgInputField = id;
		window.bbThemeAdminImgField = jQuery(event.target).attr('id');
		window.tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	},

	uploadImageDialogSuccess: function(html, inputFldSelector, imgSelector) {
		var fileurl = jQuery('img', html).attr('src');
		jQuery(inputFldSelector).val(fileurl);
		jQuery(imgSelector).attr('src', fileurl);
		tb_remove();
	}
}

jQuery(document).ready(function() {

	BBThemeAdmin.init();

	window.original_send_to_editor = window.send_to_editor;

	window.send_to_editor = function (html) {
		if(window.bbThemeAdminImgInputField) {
		 	BBThemeAdmin.uploadImageDialogSuccess(
		 		html, 
		 		'#'+window.bbThemeAdminImgInputField, 
		 		'#'+window.bbThemeAdminImgField
		 	);
		}
		else {
		 	window.original_send_to_editor(html);
		}
	};
});