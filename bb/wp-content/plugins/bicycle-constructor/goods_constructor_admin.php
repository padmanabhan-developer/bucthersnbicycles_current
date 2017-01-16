<?php 

/*
	Admin panel page for B&B Bicycle Constructor Plugin

	Author: Igor Malinovskiy
	Author URI: http://woto-info.com/
	License: GPLv2 or later
*/
	
function goods_constructor_admin_settings() 
{
	add_options_page(
		'B&B Bicycle Constructor', 						//page title
		'B&B Bicycle Constructor', 						//menu title
		'manage_options', 								//capability
		'goods_constructor_admin', 						//menu_slug
		'goods_constructor_admin_settings_page'				//function
	);
	goods_constructor_load_user_styles();
}

function goods_constructor_admin_settings_page()
{	
	if (empty($_POST))
	{
		$tpl = goods_constructor_get_tpl_instance();
	
		goods_constructor_assign_admin_pages($tpl);
		$tpl->draw('admin-layout');
	}
	else if (!empty($_POST['model']))
	{
		$dictionary = get_basic_model_dictionary_instance();
		goods_constructor_display_message($dictionary->saveBasicModels($_POST['model']));
	}
	else if (!empty ($_POST['type']))
	{
		$dict = get_part_types_dictionary_instance();
		goods_constructor_display_message($dict->savePartTypes($_POST['type']));
	}
	else if (!empty($_POST['external']))
	{
		$dict = get_external_details_dictionary_instance();
		goods_constructor_display_message($dict->saveExternalParts($_POST['external']));
	}
	else if (!empty ($_POST['upgrades']))
	{
		$dict = get_upgrades_parts_dictionary_instance();
		goods_constructor_display_message($dict->saveUpgradeParts($_POST['upgrades']));
	}
	else if (!empty($_POST['table']) && !empty($_FILES['file']))
	{
		$table = filter_var($_POST['table']['name'], FILTER_SANITIZE_STRIPPED);
		$format = filter_var($_POST['table']['format'], FILTER_SANITIZE_STRIPPED);

		if(!class_exists('Tables'))
			require_once 'inc/Tables.php';
		
		if($format === 'csv' 
				&& ($table === Tables::GC_MAIN_PARTS || $table === Tables::GC_EXTERNAL_PARTS) 
				&& $_FILES['file']['type'] === 'text/csv'
				&& $_FILES['file']['error'] == 0)
		{
			$h = get_dump_to_csv_instance();
			$data = $h->readCsv($_FILES['file']['tmp_name']);
			
			goods_constructor_display_message($h->insertIntoTable($table, $data));
		}
	}
	else if (!empty($_POST['bb_delivery_costs_submit']))
	{
		$dict = get_delivery_costs_instance();
		goods_constructor_display_message($dict->saveDeliveryCosts($_POST['delivery_costs']));
	}
}

function goods_constructor_assign_admin_pages(RainTPL $tpl)
{
	$constructorShortcode = goods_display_constructor_shortcode_function();
	$tpl->assign('constructor_shortcode', $constructorShortcode);
	$tpl->assign('basic_models_input', get_basic_model_dictionary_instance()->getBasicModelsInputHtml());
	
	$tpl->assign('admin_users_configurations', get_user_saved_configurations_instance()->getUserConfigurationHtml($constructorShortcode));
	
	$tpl->assign('part_types_inputs', get_part_types_dictionary_instance()->getPartTypesInputHtml());
	$tpl->assign('external_details_inputs', get_external_details_dictionary_instance()->getExternalPartsInputHtml());
	$tpl->assign('upgrades_inputs', get_upgrades_parts_dictionary_instance()->getUpgradePartsInputsHtml());
	
	$tpl->assign('wp_nonce_field', wp_nonce_field('bb_constructor_settings', 'settings_submit'));
	$tpl->assign('dump_nonce_field', wp_nonce_field('bb_dump_tables', 'n'));
    
	$tpl->assign('countries_options', get_delivery_costs_instance()->getCountriesSelectOptionsHtml());
    $tpl->assign('regions_options', get_delivery_costs_instance()->getRegionsSelectOptionsHtml());
	$tpl->assign('delivery_services_options', get_delivery_costs_instance()->getDeliveryServicesOptionsHtml());
    $tpl->assign('admin_delivery_costs_inputs', get_delivery_costs_instance()->getDeliveryCostsInputs());
    $tpl->assign('delivery_nonce_field', wp_nonce_field('bb_delivery_costs', 'bb_delivery_costs_submit'));
}

?>