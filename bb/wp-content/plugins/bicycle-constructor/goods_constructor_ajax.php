<?php

/**
 * AJAX functions
 */

/**
 * wp_ajax_nopriv allows users to send ajax queries while not logged in
 */
add_action('wp_ajax_get_bicycle_image', 'goods_constructor_get_bicycle_image');
add_action('wp_ajax_nopriv_get_bicycle_image', 'goods_constructor_get_bicycle_image');

function goods_constructor_get_bicycle_image()
{
	$constructor = get_goods_constructor_instance();
	
	goods_constructor_display_success_json($constructor->getImageUrl($_REQUEST));
}


add_action('wp_ajax_get_bicycle_configuration', 'goods_constructor_get_bicycle_configuration');
add_action('wp_ajax_nopriv_get_bicycle_configuration', 'goods_constructor_get_bicycle_configuration');

function goods_constructor_get_bicycle_configuration()
{	
	if(empty($_POST['conf_id']))
		goods_constructor_display_error_json('Empty conf_id');
	
	$conf = get_goods_constructor_instance()->getUserCofigurationByID($_POST['conf_id']);
	
	if(empty($conf))
		goods_constructor_display_error_json ('Configuration is not exist');
	
	goods_constructor_display_json($conf);
}


add_action('wp_ajax_save_bicycle_configuration', 'goods_constructor_save_bicycle_configuration');

function goods_constructor_save_bicycle_configuration()
{
	$imageUrl = filter_var($_POST['image_url'], FILTER_SANITIZE_URL);
	$constructor = get_goods_constructor_instance();
	$imgId = $constructor->saveConfigurationImage($imageUrl);
	
	$mainPart = filter_var($_POST['main_part'], FILTER_SANITIZE_NUMBER_INT);
	$reqDetails = filter_var_array($_POST['required_details'], FILTER_SANITIZE_NUMBER_INT);
	$extDetails = empty($_POST['external_details']) ? array() : filter_var_array($_POST['external_details'], FILTER_SANITIZE_NUMBER_INT);
	
	if ($constructor->saveConfiguration($imgId, $mainPart, $reqDetails, $extDetails))
		goods_constructor_display_json (array('success' => 1));
	else
		goods_constructor_display_error_json ('Configuration exists');
}


add_action('wp_ajax_save_user_bicycle_configuration', 'goods_constructor_save_user_bicycle_configuration');
add_action('wp_ajax_nopriv_save_user_bicycle_configuration', 'goods_constructor_save_user_bicycle_configuration');

function goods_constructor_save_user_bicycle_configuration()
{		
	$userID = goods_constructor_get_user_id_or_send_error();
	
	if(empty($_POST['main_part']))
		goods_constructor_display_error_json('Empty main detail');
	
	if(empty($_POST['required_details']))
		goods_constructor_display_error_json('Empty required details');
	
	$extDetails = empty($_POST['external_details']) ? array() : $_POST['external_details'];
	
	$existConf = get_goods_constructor_instance()->getUserConfHeadByParts(
		$userID,
		$_POST['main_part'],
		$_POST['required_details'],
		$extDetails
	);
	
	if(count($existConf) > 0)
		goods_constructor_display_json(array('message'=>'Configuration exist', 'conf_id' => $existConf[0]->conf_id));
	
	$resultSave = get_goods_constructor_instance()->saveUserConfiguration(
		$userID,
		$_POST['main_part'],
		$_POST['required_details'],
		$extDetails,
		$_POST['image_url'],
		$_POST['datetime']
	);
	
	if(!$resultSave)
		goods_constructor_display_error_json('Failed to save configuration');
	
	goods_constructor_display_success_json(array('message' => 'Configuration saved', 'conf_id' => $resultSave));
}


add_action('wp_ajax_change_saved_config', 'goods_constructor_change_saved_config');

function goods_constructor_change_saved_config()
{	
	$confId = filter_var($_POST['id'], FILTER_VALIDATE_INT);
	$userId = goods_constructor_get_user_id_or_send_error();
	$sc = get_user_saved_configurations_instance();
	
	goods_constructor_display_success_json($sc->getUserConfById($userId, $confId));
}


add_action('wp_ajax_get_tech_details', 'goods_constructor_get_tech_details');
add_action('wp_ajax_nopriv_get_tech_details', 'goods_constructor_get_tech_details');

function goods_constructor_get_tech_details()
{
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$gc = get_goods_constructor_instance();
	
	$external = $gc->getExternalParts();
	
	$mainpart = $gc->getMainPartByName($name);
	if (count($mainpart) > 0)
	{
		goods_constructor_display_success_json(array(
				'name' => $name,
				'descr' => htmlspecialchars($mainpart[0]->descr),
				'external' => $external
			)
		);
	}
	else
	{
		goods_constructor_display_error_json("model not found");
	}
}

add_action('wp_ajax_add_basic_model_inputs', 'goods_constructor_add_basic_model_inputs');

function goods_constructor_add_basic_model_inputs()
{
	$gd = get_basic_model_dictionary_instance();
	$html = $gd->getBasicModelInputHtml($gd->getEmptyModel(), $_POST['id']);
	
	goods_constructor_display_success_json(array('form' => htmlspecialchars($html)));
}

add_action('wp_ajax_remove_basic_model', 'goods_constructor_remove_basic_model');

function goods_constructor_remove_basic_model()
{
	$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
	

	if (get_basic_model_dictionary_instance()->removeBasicModel($id))
		goods_constructor_display_success_json();
	else
		goods_constructor_display_error_json ('could not delete model');
}

add_action('wp_ajax_add_part_type_inputs', 'goods_constructor_add_part_type_inputs');

function goods_constructor_add_part_type_inputs()
{
	$gd = get_part_types_dictionary_instance();
	$html = $gd->getPartTypeInputHtml($gd->getEmptyPartType(), $_POST['id']);
	
	goods_constructor_display_success_json(array('form' => htmlspecialchars($html)));
}

add_action('wp_ajax_add_external_part_input', 'goods_constructor_add_external_part_input');

function goods_constructor_add_external_part_input()
{
	$gd = get_external_details_dictionary_instance();
	$html = $gd->getExternalPartInputHtml($gd->getEmptyExternal(), $_POST['id']);
	
	goods_constructor_display_success_json(array('form' => htmlspecialchars($html)));
}

add_action('wp_ajax_add_upgrades_part_input', 'goods_constructor_add_upgrades_part_input');

function goods_constructor_add_upgrades_part_input()
{
	$gd = get_upgrades_parts_dictionary_instance();
	$html = $gd->getUpgradePartInputHtml($gd->getEmptyUpgrade(), $_POST['id']);
	
	goods_constructor_display_success_json(array('form' => htmlspecialchars($html)));
}

add_action('wp_ajax_delete_selected_configurations', 'goods_constructor_delete_selected_configurations');
add_action('wp_ajax_nopriv_delete_selected_configurations', 'goods_constructor_delete_selected_configurations');

function goods_constructor_delete_selected_configurations()
{
	$id = filter_var($_POST['user_conf_id'], FILTER_VALIDATE_INT);
	
	if ($id <= 0) 
		goods_constructor_display_error_json ('wrong configuration selected');
	
	if (get_user_saved_configurations_instance()->deleteUserConfigurationById($id))
		goods_constructor_display_json (array('success' => 1, 'conf_id' => $id));
	else
		goods_constructor_display_error_json ('could not delete this configuration');
}

add_action('wp_ajax_add_empty_delivery_service_inp', 'goods_constructor_add_empty_delivery_service_inp');

function goods_constructor_add_empty_delivery_service_inp()
{
	$dc = get_delivery_costs_instance();
	$html = $dc->getDeliveryCostsInputsHtml($dc->getEmpty(), $_POST['id']);
	
	goods_constructor_display_success_json(array('form' => htmlspecialchars($html)));
}

add_action('wp_ajax_delete_country', 'goods_constructor_delete_country');

function goods_constructor_delete_country()
{
	$dc = get_delivery_costs_instance();
	if($dc->deleteCountry(filter_var($_POST['name'], FILTER_SANITIZE_STRIPPED)))
		goods_constructor_display_success_json(array('name' => $_POST['name']));
	else
		goods_constructor_display_error_json ('could not delete country');
}

add_action('wp_ajax_delete_region', 'goods_constructor_delete_region');

function goods_constructor_delete_region()
{
	$dc = get_delivery_costs_instance();
	if($dc->deleteRegion(filter_var($_POST['name'], FILTER_SANITIZE_STRIPPED)))
		goods_constructor_display_success_json(array('name' => $_POST['name']));
	else
		goods_constructor_display_error_json ('could not delete region');
}

add_action('wp_ajax_delete_delivery_service', 'goods_constructor_delete_delivery_service');

function goods_constructor_delete_delivery_service()
{
	$dc = get_delivery_costs_instance();
	if($dc->deleteDeliveryService(filter_var($_POST['name'], FILTER_SANITIZE_STRIPPED)))
		goods_constructor_display_success_json();
	else
		goods_constructor_display_error_json ('could not delete delivery service');
}

add_action('wp_ajax_delete_delivery_cost_record', 'goods_constructor_delete_delivery_cost_record');

function goods_constructor_delete_delivery_cost_record()
{
	$dc = get_delivery_costs_instance();
	if($dc->deleteDeliveryCostsRecord(filter_var($_POST['record_id']), FILTER_VALIDATE_INT))
		goods_constructor_display_success_json($_POST);
	else
		goods_constructor_display_error_json ('could not delete record');
}

add_action('wp_ajax_change_country', 'goods_constructor_change_country');
add_action('wp_ajax_nopriv_change_country', 'goods_constructor_change_country');

function goods_constructor_change_country()
{
	$country = filter_var($_POST['country'], FILTER_SANITIZE_STRIPPED);
	$dc = get_delivery_costs_instance();
    
	goods_constructor_display_success_json(array(
			'form' => htmlspecialchars($dc->getRegionsSelectOptionsHtml($country))
		)
	);
}

add_action('wp_ajax_change_region', 'goods_constructor_change_region');
add_action('wp_ajax_nopriv_change_region', 'goods_constructor_change_region');

function goods_constructor_change_region()
{
	$region = filter_var($_POST['region'], FILTER_SANITIZE_STRIPPED);
	$dc = get_delivery_costs_instance();
	
	$services = $dc->getServices($region);
	$costs = $dc->getDeliveryCost($region, $services[0]['delivery_service_name']);
	    
	goods_constructor_display_success_json(array(
			'form' => htmlspecialchars($dc->getDeliveryServicesOptionsHtml($region)),
			'cost' => $costs[0]['delivery_cost'],
			'currency' => $costs[0]['currency']
		)
	);
}

add_action('wp_ajax_change_delivery_service', 'goods_constructor_change_delivery_service');
add_action('wp_ajax_nopriv_change_delivery_service', 'goods_constructor_change_delivery_service');

function goods_constructor_change_delivery_service() 
{
	$region = filter_var($_POST['region'], FILTER_SANITIZE_STRIPPED);
	$service = filter_var($_POST['service'], FILTER_SANITIZE_STRIPPED);
	
	$dc = get_delivery_costs_instance();
	$costs = $dc->getDeliveryCost($region, $service);
	
	goods_constructor_display_success_json(array(
			'cost' => $costs[0]['delivery_cost'],
			'currency' => $costs[0]['currency']
		)
	);
}

add_action('wp_ajax_get_user_configurations_by_email', 'goods_constructor_get_user_configurations_by_email');

function goods_constructor_get_user_configurations_by_email()
{
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	
	$sc = get_user_saved_configurations_instance();
	$confs = $sc->getUserConfigurationsByEmail($email);
	
	goods_constructor_display_success_json($confs);
}

add_action('wp_ajax_change_currency', 'goods_constructor_change_currency');

function goods_constructor_change_currency()
{	
	$args = array(
		'amount' => FILTER_VALIDATE_FLOAT,
		'from' => FILTER_SANITIZE_ENCODED,
		'to' => FILTER_SANITIZE_ENCODED
	);
	
	$currency = filter_var_array($_POST['currency'], $args);
	
	$url  = "https://www.google.com/finance/converter?a="
			. $currency['amount']
			. "&from=" . $currency['from']
			. "&to=" . $currency['to'];

    $data = file_get_contents($url);
	
    preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);
	if (count($converted) > 0) 
	{
		$converted = preg_replace("/[^0-9.]/", "", $converted[1]);
    
		goods_constructor_display_json(array(
			'message' => round($converted, 3), 
			'success' => 1, 
			'currency' => $currency
			)
		);
	}
	else
	{
		goods_constructor_display_error_json("Could not convert currency");
	}
}