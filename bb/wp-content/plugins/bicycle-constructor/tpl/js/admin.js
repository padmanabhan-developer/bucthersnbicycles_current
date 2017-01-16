ConstructorAdmin = {
    target: "",
    
	init: function() {
		var that = this;		

		jQuery('.delete_model_input').unbind('click');
		jQuery('.delete_model_input').click(function(event) {
			that.dialogConfirm(event, that.deleteModelConfirm);
		});

		jQuery('.add_model_input').unbind('click');
		jQuery('.add_model_input').click(function() {
			that.addModelInput();
		});

		jQuery('.input_type_add_btn').unbind('click');
		jQuery('.input_type_add_btn').click(function(event) {
			that.addPartTypeInput(event);
		});

		jQuery('.add_external_part_input').unbind('click');
		jQuery('.add_external_part_input').click(function(event) {
			that.addExternalPartInput(event);
		});

		jQuery('.upgrades_set_image').unbind('click');
		jQuery('.upgrades_set_image').click(function(event) {
			that.uploadImageDialog(event);
		});

		jQuery('.add_upgrades_input').unbind('click');
		jQuery('.add_upgrades_input').click(function() {
			that.addUpgradesPartInput();
		});

		jQuery('#bk_download_btn').unbind('click');
		jQuery('#bk_download_btn').click(function() {
			var table = that.getCheckedRadioInputAttr(".table_select", "data-table");
			var format = that.getCheckedRadioInputAttr(".format_select", "data-format");
			var nonce = jQuery('#n').val();
			var url = '/wp-content/plugins/bicycle-constructor/inc/data/index.php';
			window.location.href = url + '?n=' + nonce + '&fmt=' + format + '&tbl=' + table;
		});
		
		jQuery('#add_delivery_input_field').unbind('click');
		jQuery('#add_delivery_input_field').click(function() {
			that.addDeliveryServiceInput();
		});
		
		jQuery('.del_country_btn').unbind('click');
		jQuery('.del_country_btn').click(function(event) {
			that.dialogConfirm(event, that.deleteDeliveryCountryConfirm);
		});
		
		jQuery('.del_region_btn').unbind('click');
		jQuery('.del_region_btn').click(function(event) {
			that.dialogConfirm(event, that.deleteDeliveryRegionConfirm);
		});
		
		jQuery('.del_service_btn').unbind('click');
		jQuery('.del_service_btn').click(function(event) {
			that.dialogConfirm(event, that.deleteDeliveryServiceConfirm);
		});
		
		jQuery('.delete_record_btn').unbind('click');
		jQuery('.delete_record_btn').click(function(event) {
			that.dialogConfirm(event, that.deleteDeliveryCostRecordConfirm);
		});
		
		jQuery('#users_emails').unbind('change');
		jQuery('#users_emails').change(function(event) {
			that.userEmailsChange(event);
		});
		
		jQuery('#users_configurations').unbind('change');
		jQuery('#users_configurations').change(function(event) {
			that.userConfigurationChange(event);
		});
	
		that.displayDetailsInputs();
		gCalc.loadConfiguration(jQuery('.conf_id').text());
		that.hideDetailsInputs();
	},
	
	ajaxCall: function(data, success) {
		jQuery.ajax({
			type: "post",
			url: "/wp-admin/admin-ajax.php",
			dataType: "json",
			data: data,
			success: success
		});
	},

	dialogConfirm: function(event, confirm) {
		jQuery('#delete_model_dialog').dialog({
			modal: true,
			buttons: {
				"Delete" : function() {
					confirm(event, this);
				},
				"Cancel" : function() {
					jQuery(this).dialog("close");
				}
			}
		});
	},

	deleteModelConfirm: function (event, that) {
		ConstructorAdmin.deleteModelInput(event);
		jQuery(that).dialog("close");
	},

	deleteModelInput: function(event) {
		var data = {
			id: jQuery(event.target).attr('data-model-id'),
			action: 'remove_basic_model'
		}

		this.ajaxCall(data, this.RemoveBasicModelSuccess);
		jQuery(event.target).parent().parent().remove();
	},

	RemoveBasicModelSuccess: function (data) {
		console.log(data);
	},

	countElements: function(selector) {
		var i = 0;
		jQuery(selector).each(function() {
			i++;
		});
		return i;
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
		
		this.init();
	},

	addModelInput: function() {
		data = {
			id: this.countElements('.model_input') + 1,
			action: 'add_basic_model_inputs'
		};

		this.ajaxCall(data, this.AddInputCallSuccess);			
	},

	AddInputCallSuccess: function(data) {
		ConstructorAdmin.appendHtml(data, '.basic_models_form');
	},

	addPartTypeInput: function(event) {
		data = {
			id: this.countElements('.part_type_input') + 1,
			action: 'add_part_type_inputs'
		};

		this.ajaxCall(data, this.addPartTypeInputSuccess);
	},

	addPartTypeInputSuccess: function(data) {
		ConstructorAdmin.appendHtml(data, '.part-types-form');
	},

	addExternalPartInput: function(event) {
		data = {
			id: this.countElements('.external_detail_input') + 1,
			action: 'add_external_part_input'
		};

		this.ajaxCall(data, this.addExternalPartInputSuccess);
	},

	addExternalPartInputSuccess: function (data) {
		ConstructorAdmin.appendHtml(data, '.external-details-form');
	},

	uploadImageDialog: function (event) {
		var id = jQuery(event.target).attr('data-input-id');
		window.adminUpgradesImgInputField = 'upgrades\\['+id+'\\]\\[img_path\\]';
		window.adminUpgradesImgField = 'upgrades_set_image_' + id;
		window.tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	},

	uploadImageDialogSuccess: function (html, inputFldSelector, imgSelector) {
		var fileurl = jQuery('img', html).attr('src');
		jQuery(inputFldSelector).val(fileurl);
		jQuery(imgSelector).attr('src', fileurl);
		tb_remove();
	},

	addUpgradesPartInput: function() {
		data = {
			id: this.countElements('.upgrades_input'),
			action: 'add_upgrades_part_input'
		};

		this.ajaxCall(data, this.addUpgradesPartInputSuccess);
	},

	addUpgradesPartInputSuccess: function(data) {
		ConstructorAdmin.appendHtml(data, '.admin-upgrades-parts-form');
	},

	getCheckedRadioInputAttr: function(where, attr) {
		return jQuery(where).find('input[type="radio"]:checked').attr(attr);
	},
	
	addDeliveryServiceInput: function() {
		var data = {
			id: this.countElements('.del_cost_inp'),
			action: 'add_empty_delivery_service_inp'
		};
		this.ajaxCall(data, this.addDeliveryServiceInputSuccess);
	},
	
	addDeliveryServiceInputSuccess: function(data) {
		ConstructorAdmin.appendHtml(data.form, '.delivery_costs');
	},
	
	deleteDeliveryCountryConfirm: function(event, that) {
		ConstructorAdmin.deleteDeliveryCountry(event);
		jQuery(that).dialog("close");
	},
	
	deleteDeliveryOption: function(event, id_suffix, action, success) {
		var id = jQuery(event.target).attr('data-input-id');
		var input = jQuery("#delivery_costs\\["+id+"\\]\\["+id_suffix+"\\]");
		var data = {
			name: jQuery(input).val(),
			action: action
		};
        
        this.target = input;
		
		this.ajaxCall(data, success);
	},
	
	deleteDeliveryOptionSuccess: function(data, selector) {
		if (data && data.hasOwnProperty('error')) {
			alert(data.error);
		}
		else {
			jQuery(selector).each(function() {
				if(jQuery(this).text().trim() == data.name.trim())
					jQuery(this).remove();
			});

			jQuery(this.target).val("");
		}
	},
	
	deleteDeliveryCountry: function(event) {
		this.deleteDeliveryOption(event, 'country_name', 'delete_country', this.deleteDeliveryCountrySuccess);
	},
	
	deleteDeliveryCountrySuccess: function(data) {
		ConstructorAdmin.deleteDeliveryOptionSuccess(data, "#countries option");
	},
    
    deleteDeliveryRegionConfirm: function(event, that) {
        ConstructorAdmin.deleteDeliveryRegion(event);
		jQuery(that).dialog("close");
    },
    
    deleteDeliveryRegion: function(event) {
        this.deleteDeliveryOption(event, 'region_name', 'delete_region', this.deleteDeliveryRegionSuccess);
    },
    
    deleteDeliveryRegionSuccess: function(data) {
      ConstructorAdmin.deleteDeliveryOptionSuccess(data, "#regions option");
    },
	
	deleteDeliveryServiceConfirm: function(event, that) {
		ConstructorAdmin.deleteDeliveryService(event);
		jQuery(that).dialog("close");
	},
	
	deleteDeliveryService: function(event) {
		this.deleteDeliveryOption(event, 'delivery_service_name', 'delete_delivery_service', this.deleteDeliveryServiceSuccess);
	},
	
	deleteDeliveryServiceSuccess: function(data) {
		ConstructorAdmin.deleteDeliveryOptionSuccess(data, "#delivery_services option");
	},
	
	deleteDeliveryCostRecordConfirm: function(event, that) {
		ConstructorAdmin.deleteDeliveryCostRecord(event);
		jQuery(that).dialog("close");
	},
	
	deleteDeliveryCostRecord: function(event) {
		var data = {
			input_id: jQuery(event.target).attr('data-input-id'),
			record_id: jQuery(event.target).attr('data-record-id'),
			action: 'delete_delivery_cost_record'
		};
		
		if(data.record_id == '')
			this.deleteDeliveryCostRecordSuccess(data);
		else
			this.ajaxCall(data, this.deleteDeliveryCostRecordSuccess);
	},
	
	deleteDeliveryCostRecordSuccess: function(data) {
		if(data && data.hasOwnProperty('success') && data.success == 1) {
			jQuery("#del_cost_inp_"+data.input_id).remove();
		}
		else if(data && data.hasOwnProperty('error')) {
			alert(data.error);
		}
	},
	
	displayDetailsInputs: function() {
		jQuery('#user_configurations .detail_main_part').css('opacity', '0.5');
		jQuery('#user_confururations .detail_required').css('opacity', '0.5');
		jQuery('#user_configurations .bc-external-item').css('opacity', '0.5');
	},
	
	hideDetailsInputs: function() {
		jQuery('#user_configurations input.detail').each(function() {
			jQuery(this).attr('disabled', 'disabled');
			if(jQuery(this).attr('checked')) {
				if(jQuery(this).is('input[type="radio"]'))
					jQuery(this).parent().css('opacity', '1');
				else if(jQuery(this).is('input[type="checkbox"]'))
					jQuery(this).parent().parent().css('opacity', '1');
			}
		});
	},
	
	userEmailsChange: function(event) {
		var data = {
			email: jQuery(event.target).val(),
			action: 'get_user_configurations_by_email'
		};
		
		this.ajaxCall(data, this.getUserConfigurationsSuccess);
	},
	
	getUserConfigurationsSuccess: function(data) {
		if(data && data.hasOwnProperty('success') && data.success == 1 && data.confs.length > 0) {
			var options = [];
			for (var i=0; i<data.confs.length; i++){
				options.push(
					"&lt;option data-conf-id=&quot;"+data.confs[i].conf_id+"&quot;&gt;" + data.confs[i].conf_name
				);
			}

			ConstructorAdmin.appendHtml(options, '#users_configurations', true);
			ConstructorAdmin.appendHtml(data.confs[0].conf_id, '.conf_id', true);
			ConstructorAdmin.displayDetailsInputs();
			gCalc.loadConfiguration(data.confs[0].conf_id, false);
			ConstructorAdmin.hideDetailsInputs();
		}
	},
	
	userConfigurationChange: function(event) {
		var id = jQuery(event.target).find(':selected').attr('data-conf-id');
		ConstructorAdmin.appendHtml(id, '.conf_id', true);
		ConstructorAdmin.displayDetailsInputs();
		gCalc.loadConfiguration(id, false);
		ConstructorAdmin.hideDetailsInputs();
	}
};


jQuery(document).ready(function($) {
    
    $('input,textarea').attr('autocomplete', 'off');

	ConstructorAdmin.init();

	window.original_send_to_editor = window.send_to_editor;

	window.send_to_editor = function (html) {
		if(window.adminUpgradesImgInputField) {
		 	ConstructorAdmin.uploadImageDialogSuccess(
		 		html, 
		 		'#'+window.adminUpgradesImgInputField, 
		 		'#'+window.adminUpgradesImgField
		 	);
		}
		//for constructor admin main page
		else if(window.adminMainUploadImage)
		{
			ConstructorAdmin.uploadImageDialogSuccess(
				html,
				'#'+window.adminMainUploadImage,
				'.'+window.adminMainImgField
			);
		}
		else {
		 	window.original_send_to_editor(html);
		}
	};
});