BBHelpers = {
	/**
	 * send ajax query to wordpress
	 * @param {object} data
	 * @param {function} success
	 * @param {boolean} async default true
	 * @returns {undefined}
	 */
	ajaxCall: function(data, success, async) {
		if(async !== false)
			async = true;
		
		jQuery.ajax({
			type: 'post',
			dataType: 'json',
			url: '/wp-admin/admin-ajax.php',
			async: async,
			data: data, 
			success: success
		});
	},
	
	formatPriceWithCommas: function(x) {
		x = Math.round(parseFloat(x) * 100) / 100;

		if (x.toString().lastIndexOf('.') == -1) {
			x += ',-';
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");	
		}
		else {
			var decimalCommaLastIndex = x.toString().length - x.toString().lastIndexOf('.');
			if (decimalCommaLastIndex == 3)
				x = x.toString().replace(/[^0-9-]/g, ',');
			else if (decimalCommaLastIndex == 2)
				x = x.toString().replace(/[^0-9-]/g, ',') + '0';
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		}
	},
	
	removePriceFormatting: function(x) {
		if(x.indexOf(',') > 0) {
			x = x.toString();
			x = parseFloat(x.replace(/[^0-9-,]/g, '').replace(/[^0-9-]/, '.'));
			return Math.round(x * 100) / 100;
		}
		else
			return x;
	},
	
	getConfSavedDate: function() {
		var now = new Date();
		var conf_saved_date = now.getFullYear() 
			+ "-" + parseInt(now.getMonth() + 1) 
			+ "-" + now.getDate() 
			+ " " + now.getHours() 
			+ ":" + now.getMinutes() 
			+ ":" + now.getSeconds();

		return conf_saved_date;
	}
}

BBShipping = {
	cost: 0.0,
	currency: '',
	
	init: function() {
		var that = this;
		jQuery('#shipping-countries').unbind('change');
		jQuery('#shipping-countries').change(function(event) {
			that.changeCountry(event);
		});
        
		jQuery('#shipping-regions').unbind('change');
		jQuery('#shipping-regions').change(function(event) {
			that.changeRegion(event);
		});
		
		jQuery('#shipping-services').unbind('change');
		jQuery('#shipping-services').change(function(event) {
			that.changeDeliveryService(event);
		});
	},
	
	/**
	 * append or replace child nodes of selected element
	 * @param {String} data to replace or append
	 * @param {String} selector element '#id' || '.class'
	 * @param {boolean} replace true to replace, false to append
	 * @returns {undefined}
	 */
	appendHtml: function(data, selector, replace) {
		if(replace === true) {
			jQuery(selector).empty();
		}
		jQuery(selector).append(
			jQuery('<div />').html(data).text()
		);
	},
	
	changeCountry: function(event) {
        jQuery("#shipping-regions").val("");
        jQuery("#shipping-services").val("");
        jQuery(".shipping-price").text("");
        
		var data = {
			country: jQuery(event.target).val(),
			action: 'change_country'
		};
		
		BBHelpers.ajaxCall(data, this.changeCountrySuccess);
	},
	
	changeCountrySuccess: function(data) {
		if (data && data.hasOwnProperty('success') && data.success == 1) {
			BBShipping.appendHtml(data.form, '#shipping-regions', true);
            jQuery("#shipping-regions").attr("disabled", false);
		}
	},
    
    changeRegion: function(event) {
        var data = {
            region: jQuery(event.target).val(),
            action: 'change_region'
        };
        
        BBHelpers.ajaxCall(data, this.changeRegionSuccess);
    },
    
    changeRegionSuccess: function(data) {
        if(data && data.hasOwnProperty('success') && data.success == 1) {
            BBShipping.appendHtml(data.form, '#shipping-services', true);
            jQuery("#shipping-services").attr("disabled", false);
			BBShipping.setAttrs(data.cost, data.currency);
        }
    },
	
	changeDeliveryService: function(event) {
		var data = {
			region: jQuery('#shipping-regions').val(),
			service: jQuery(event.target).val(),
			action: 'change_delivery_service'
		};
		
		BBHelpers.ajaxCall(data, this.changeDeliveryServiceSuccess);
	},
	
	changeDeliveryServiceSuccess: function(data) {
		if(data && data.hasOwnProperty('success') && data.success == 1) {
			BBShipping.setAttrs(data.cost, data.currency);
		}
	},
	
	setAttrs: function(cost, currency) {
		var calcCurrency = gCalc.getCurrency();
		if(currency != calcCurrency) {
			jQuery('.shipping-price').attr('data-shipping-price', cost);
			jQuery('.shipping-currency').attr('data-shipping-currency', currency);
			gCalc.changeCurrency(currency, calcCurrency, cost, this.changeCurrencySuccess);
		}
		else {
			this.appendHtml(BBHelpers.formatPriceWithCommas(cost), '.shipping-price', true);
			this.appendHtml(currency, '.shipping-currency', true);
		}
	},
	
	changeCurrencySuccess: function(data) {
		if(data && data.hasOwnProperty('success') && data.success == 1) {
			BBShipping.setAttrs(data.message, data.currency.to);
		}
	},

	setCurrency: function(currency) {
		this.currency = currency.trim();
	},
	
	setCost: function(cost) {
		this.cost = parseFloat(cost);
	},
	
	getCurrency: function() {
		this.currency = jQuery('.shipping-currency').attr('data-shipping-currency');
		return this.currency;
	},
	
	getCost: function() {
		this.cost = jQuery('.shipping-price').attr('data-shipping-price');
		return this.cost;
	}
}

jQuery(document).ready(function (){
	
	function updateCost() {
		var cost = gCalc.calculateCost('.gccost', 'data-gc-cost');
		var shipping_cost = parseFloat(jQuery('.shipping-price').text());
		gCalc.showCost(shipping_cost, '.shipping-price');
		gCalc.showCost(cost+shipping_cost, '.sc_price');

		return cost;
	}

	function updateExternal() {
		var factor = gCalc.calculateCost('.gccost', 'data-gc-cost') / parseFloat(BBHelpers.removePriceFormatting(jQuery('.sc_price').text()));
		jQuery('.gccost').each(function() {
			if (jQuery(this).text() != '') {
				var oldprice = parseFloat(jQuery(this).attr('data-gc-cost'));
				var newprice = oldprice / factor;

				jQuery(this).text(BBHelpers.formatPriceWithCommas(newprice));	
			}
		});
	}

	function goToSaved() {
		window.location.href = "#welcome";
	};

	jQuery(function () {
		gCalc.init();
		gCalc.selectFirstConfiguration();
		BBShipping.init();
		jQuery('#shipping-countries').val(jQuery("#shipping-countries option:first").val());
		
		updateCost();
		updateExternal();

		jQuery('#gotosaved_btn').click(function() {
			goToSaved();
		});
	});
});