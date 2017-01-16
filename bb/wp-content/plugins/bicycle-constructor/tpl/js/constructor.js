function BicycleConstructor() {
	this.confData = {};
};

BicycleConstructor.prototype.getImageUrl = function() {
	this.confData.image_url = jQuery('#upload_image').val();
	return this.confData.image_url;
};

BicycleConstructor.prototype.getConfData = function() {
	this.getImageUrl();
	confDetails = gCalc.getSelectedConfData();

	this.confData.main_part = confDetails.main_part;
	this.confData.required_details = confDetails.required_details;
	this.confData.external_details = confDetails.external_details;

	return this.confData;
};

BicycleConstructor.prototype.init = function() {

	jQuery('#upload_image_button').click(function() {
		adminMainUploadImage = jQuery('#upload_image').attr('name');
		adminMainImgField = 'constructor_img';
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});

	this.getConfData();
};

BicycleConstructor.prototype.saveConfig = function() {
	var data = this.getConfData();
	data.action = 'save_bicycle_configuration';

	BBHelpers.ajaxCall(data, this.saveConfigSuccess);
};

BicycleConstructor.prototype.saveConfigSuccess = function(data) {
	if (data && data.hasOwnProperty('success') && data.success == 1)
				gCalc.showResponse("Configuration saved", ".bc_ajax_response");
			else
				gCalc.showResponse(data.error, ".bc_ajax_response");
}

jQuery(document).ready(function() {
	var bc = new BicycleConstructor();
	bc.init();

	jQuery('#bc_submit').click(function() {
		bc.saveConfig();
	});
});