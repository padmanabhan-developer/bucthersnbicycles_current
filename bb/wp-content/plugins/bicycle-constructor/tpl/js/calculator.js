function GoodsCalculator(){
	this.cost = 0;

	this.currency = new Object();
	this.currency.from = '';
	this.currency.to = '';
	this.currency.amount = 1;
};

GoodsCalculator.prototype.getCurrency = function() {
	this.currency.from = jQuery('.sc-currency').text().trim();
	
	return this.currency.from;
};

GoodsCalculator.prototype.init = function (){
	this.loadImageUrl();
	this.calculateCost('input.detail:checked', 'data-cost');
	this.changeCurrency('DKK', jQuery('#bc_change_currency').val());
	
	var that = this;
	
	//do not unbind, another script uses this action
	jQuery('input.detail').change(function() {
		that.inputDetailChange();
	});
	
	jQuery('#bc_change_currency').unbind('change');
	jQuery('#bc_change_currency').change(function(event){
		that.selectCurrencyChange(event);
	});
	
	jQuery('#sc_change_currency').unbind('change');
	jQuery('#sc_change_currency').change(function(event) {
		that.savedConfCurrencyChange(event);
	});
	
	jQuery('#load_conf_btn').unbind('click');
	jQuery('#load_conf_btn').click(function() {
		that.loadConfiguration(jQuery('#conf_input').val())
	});
	
	jQuery('input.bc_btn_show[type="button"]').unbind('click');
	jQuery('input.bc_btn_show[type="button"]').click(function() {
		that.showConfiguration(jQuery(this).attr('id').substr(7));
	});
	
	jQuery('input.gc-conf').unbind('click');
	jQuery('input.gc-conf').click(function() {
		that.changeConfig(
			jQuery(this).val().substr(8), 
			that.changeConfigSuccessReplaceData
		);
	});
	
	jQuery('#save_conf_btn').unbind('click');
	jQuery('#save_conf_btn').click(function() {
		that.saveConfiguration();
	});
	
	jQuery('#delete_selected').unbind('click');
	jQuery('#delete_selected').click(function() {
		that.deleteSelectedConfig(Conf.user_conf_id)
	});
};

GoodsCalculator.prototype.getSelectedConfData = function (){
	var main_part	= jQuery("input[name=main_part]:checked").val(),
		required	= jQuery("input.required_detail:checked"),
		external	= jQuery("input.external_detail:checked"),
		image_url	= jQuery(".constructor_img").attr('src'),
		req_details = [],
		ext_details = [];

	if(!main_part || !required)
		return false;
	
	jQuery.each(required, function (index, input){
		req_details.push(jQuery(input).val());
	});
	
	jQuery.each(external, function (index, input){
		ext_details.push(jQuery(input).val());
	});
	
	return {
		main_part:main_part,
		required_details:req_details,
		external_details:ext_details,
		image_url:image_url
	};
};

GoodsCalculator.prototype.loadImageUrl = function (){
	var selData = this.getSelectedConfData();
	
	if(selData === false)
		return;
	
	selData.action = 'get_bicycle_image';
	
	BBHelpers.ajaxCall(selData, this.loadImageUrlSuccess);
};

GoodsCalculator.prototype.loadImageUrlSuccess = function(data) {
	jQuery('.constructor_img').attr("src", data.img_url);
	if (data && data.hasOwnProperty('success') && data.success == 1) {
		Conf.conf_id = data.conf_id;
	}
}

GoodsCalculator.prototype.calculateCost = function (selector, attr){
	this.cost = 0;
	var that = this;
	jQuery(selector).each(function() {
		that.cost += parseFloat(jQuery(this).attr(attr));
	});
	
	return this.cost;
};

GoodsCalculator.prototype.selectFirstConfiguration = function() {
	var radioFirst = jQuery('input.gc-conf:first');
		
	if (radioFirst.length > 0) {
		radioFirst.attr('checked', true);
		Conf.user_conf_id = radioFirst.val().substr(8);
	}
};
		
GoodsCalculator.prototype.savedConfCurrencyChange = function(event) {
	this.currency.to = jQuery(event.target).val();
	this.calculateCost('.gccost', 'data-gc-cost');
	this.currency.amount = this.cost + BBHelpers.removePriceFormatting(
		jQuery('.shipping-price').text().trim()
	);
	
	this.changeCurrency(
		'DKK', 
		this.currency.to, 
		this.currency.amount,
		this.savedConfCurrencyChangeSuccess
	);
};

GoodsCalculator.prototype.savedConfCurrencyChangeSuccess = function(data) {
	if (data && data.hasOwnProperty('success') && data.success == 1) {
		gCalc.updateCurrency(parseFloat(data.message), '.sc_price', '.sc-currency');
		gCalc.recalcShipping(data);
	}
};

GoodsCalculator.prototype.recalcShipping = function(data) {
	var factor = data.currency.amount / data.message;
	var newprice = BBShipping.getCost() / factor;
	BBShipping.changeDeliveryServiceSuccess({
		success: 1, 
		cost: newprice, 
		currency: data.currency.to
	});
};

GoodsCalculator.prototype.selectCurrencyChange = function(event) {
	this.currency.from = 'DKK';
	this.currency.to = jQuery(event.target).val();
	this.currency.amount = this.cost;
	
	this.calculateCost('input.detail:checked', 'data-cost');
	this.changeCurrency('DKK', jQuery('#bc_change_currency').val());

	this.loadImageUrl();
	this.calculateCost('input.detail:checked', 'data-cost');
	this.changeCurrency('DKK', jQuery('#bc_change_currency').val());
};

/**
 * convert currency
 * @param {string} from
 * @param {string} to
 * @param {float} amount
 * @param {function} success
 * @returns {undefined}
 */
GoodsCalculator.prototype.changeCurrency = function(from, to, amount, success) {
	this.currency.from = from;
	this.currency.to = to;
	
	if(amount === undefined) {
		if (this.cost == 0)
			return;
		
		this.currency.amount = this.cost;
	}
	else
		this.currency.amount = amount;
	
	if(success === undefined)
		success = this.changeCurrencySuccess;
	
	if (this.currency.from == this.currency.to) {
		success({success: 1, currency: this.currency, message: this.currency.amount});
	}
	else {
		var data = {
			action: 'change_currency',
			currency: this.currency,
		};
		
		BBHelpers.ajaxCall(data, success);
	}
};

GoodsCalculator.prototype.changeCurrencySuccess = function(data) {
	if (data && data.hasOwnProperty('success') && data.success == 1)
	{
		gCalc.updateCurrency(parseFloat(data.message), '.bc_price', '.bc-currency');
	}
};

GoodsCalculator.prototype.updateCurrency = function(price, price_selector, currency_selector) {
	this.showCost(price, price_selector);
	jQuery(currency_selector).empty();
	jQuery(currency_selector).append(this.currency.to);
	this.recalcExternal();
};

GoodsCalculator.prototype.inputDetailChange = function() {
	this.loadImageUrl();
	this.calculateCost('input.detail:checked', 'data-cost');
	this.changeCurrency('DKK', jQuery('#bc_change_currency').val());
}

GoodsCalculator.prototype.saveConfiguration = function (){
	var postData = this.getSelectedConfData();
	if (postData == false)
		postData = {};
	
	postData.datetime = BBHelpers.getConfSavedDate();
	postData.action = 'save_user_bicycle_configuration';
	
	BBHelpers.ajaxCall(postData, this.saveConfigurationSuccess);
};

GoodsCalculator.prototype.saveConfigurationSuccess = function(data) {
	Conf.user_conf_id = data.conf_id;
	gCalc.showResponse(data.message, '.bc_ajax_response');
};

GoodsCalculator.prototype.loadConfiguration = function (conf_id, async) {
	if(async !== false)
		async = true;
	
	var data = {
		action: 'get_bicycle_configuration', 
		conf_id:conf_id
	};
	
	BBHelpers.ajaxCall(data, this.loadConfigurationSuccess, async);
};

GoodsCalculator.prototype.loadConfigurationSuccess = function(data) {
	jQuery('input.external_detail').attr('checked', false);
	jQuery('input.required_detail').attr('checked', false);
			
	jQuery("input.main_part[value=" + data.head.main_part_id + "]").attr('checked', 'checked');
			
	jQuery.each(data.body,function(index, val){
		var inputName = (val.is_required == 1 ? ("required_" + val.type_id) : ("external_" + val.external_part_id));
		var selector = 'input[name="' + inputName + '"][value="' + val.external_part_id + '"]';
		var input= jQuery(selector);
		jQuery(input).attr('checked', 'checked');
	});
    if(jQuery.fn.customBtns) jQuery.fn.customBtns.init();
	jQuery('.constructor_img').attr("src", data.head.img_path);
};

GoodsCalculator.prototype.showConfiguration = function(id) {
	this.loadConfiguration(id);
	window.location.href = '#bicycle-constructor';
}

GoodsCalculator.prototype.showResponse = function(text, selector) {
	jQuery(selector).empty();
	jQuery(selector).append('<p>' + text + '</p>');
	jQuery(selector).slideToggle("slow").delay(3000).slideToggle("slow");
};

GoodsCalculator.prototype.showCost = function(cost, selector) {
	jQuery(selector).empty();
	jQuery(selector).append(BBHelpers.formatPriceWithCommas(cost));
};

GoodsCalculator.prototype.recalcExternal = function() {
	var factor = gCalc.cost / BBHelpers.removePriceFormatting(jQuery('.bc_price').text());
	var items = jQuery('.bc-item-price');
	var i = 0;

	jQuery('input.external_detail').each(function() {
		var oldprice = jQuery(this).attr('data-cost');
		var newprice = oldprice / factor; 
		items[i].innerHTML = BBHelpers.formatPriceWithCommas(Math.round(newprice * 100) / 100);
		i++;
	});
}; 

GoodsCalculator.prototype.changeConfig = function(id, success) {
	var data = {
		action: 'change_saved_config',
		id: parseInt(id)
	};
		
	BBHelpers.ajaxCall(data, success);
};

GoodsCalculator.prototype.changeConfigSuccessReplaceData = function(data) {
	if (data && data.hasOwnProperty('success') && data.success == 1) {
		gCalc.replaceSavedConfData(data);
		Conf.user_conf_id = data.conf_id;
		Conf.user_conf_name = data.conf_name;
		Conf.user_conf_saved_date = data.conf_saved_date;
		//sc_currency_change();
	}
};

GoodsCalculator.prototype.replaceSavedConfData = function(data) {
	jQuery('.gc-conf-img').find('img').attr('src', data.conf_img);
	jQuery('.conf_name').text(data.conf_name);
	jQuery('.main_part_name').text(data.main_part_name);
	jQuery('.gc_saved_on').text(data.conf_saved_date);
	jQuery('.gc-main-descr').html(jQuery('<div />').html(data.main_part_descr).text());
	jQuery('.external_parts_descr').html(jQuery('<div />').html(data.external_parts_descr).text());
};

GoodsCalculator.prototype.deleteSelectedConfig = function(conf_id) {
	var data = {
		action: 'delete_selected_configurations',
		user_conf_id: conf_id
	};
		
	BBHelpers.ajaxCall(data, this.deleteSelectedConfigSuccess);
};

GoodsCalculator.prototype.deleteSelectedConfigSuccess = function(data) {
	if (data && data.hasOwnProperty('success') && data.success == 1) {
		jQuery('#gc_conf_' + data.conf_id).remove();
		gCalc.selectFirstConfiguration();
	}
	else {
		jQuery('.gc-conf-head').append(data.error);
	}
}

var gCalc = new GoodsCalculator();