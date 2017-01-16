jQuery(document).ready(function($) {
	
	function changeConfig(id, success) {
		var data = {};
		data.action = 'change_saved_config';
		data.id = parseInt(id);
		BBHelpers.ajaxCall(data, success, false);
	}

	var changeConfigSuccessSetConfigurationInfo = function(data) {
		Conf.user_conf_id = data.conf_id;
		Conf.setUserConfName(data.conf_name);
		Conf.user_conf_saved_date = data.conf_saved_date;
	}

	var btn1ClickSuccess = function(data) {
		if (data && data.hasOwnProperty('success') && data.success == 1) {
			document.cookie = 'payment=' + data.id;
			document.cookie = 'conf_id=' + data.user_conf_id;
			window.location.href = data.approval_url;
		}
		else {
			alert(data.error);
			hideLoader();
		}
	}

	function bbPayPalBtnClick(amount, currency) {
		showLoader();
		gCalc.saveConfiguration();
		changeConfig(Conf.user_conf_id, changeConfigSuccessSetConfigurationInfo);
		
		var data = {
			amount: amount,
			currency: currency,
			user_conf_id: Conf.user_conf_id,
			conf_name: Conf.getUserConfName(),
			conf_saved_date: Conf.user_conf_saved_date,
			action: 'proceed_to_checkout'
		};
		
		BBHelpers.ajaxCall(data, btn1ClickSuccess);
	}

	$('#bb-paypal-btn1').click(function() {
		var text = $('.bc_price').text();
		bbPayPalBtnClick(BBHelpers.removePriceFormatting(text), $('.bc-currency').text());
	});

	$('#bb-paypal-btn2').click(function() {
		var text = $('.sc_price').text();
		bbPayPalBtnClick(BBHelpers.removePriceFormatting(text), $('.sc-currency').text());
	});
	
	function showLoader() {
		jQuery('#maincontent').append('<div class="cl_wrapper"><div class="load-animation-holder"></div>');
		//jQuery('#canvas_loader_container').show();
	}

	function hideLoader() {
		jQuery('.cl_wrapper').remove();
	}

	$('#bbpp-accordion').accordion();
});