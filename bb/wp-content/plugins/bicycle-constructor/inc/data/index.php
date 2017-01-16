<?php

require_once 'DumpCsv.php';
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
	
global $wpdb;

$e = new DumpCsv($wpdb);

if(isset($_GET['tbl']) && isset($_GET['fmt']) && isset($_GET['n']))
{
	$table = filter_var($_GET['tbl'], FILTER_SANITIZE_STRIPPED);
	$format = filter_var($_GET['fmt'], FILTER_SANITIZE_STRIPPED);
	
	if(!wp_verify_nonce($_GET['n'], 'bb_dump_tables') 
			&& ($table != 'gc_main_parts' || $table != 'gc_external_parts'))
	{
		die('hacking attempt!');
	}
	
	$data = $e->fetchFromTable($table);
	
	switch($format)
	{
		case 'csv':
			$e->downloadCsv($data, $table);
		break;
		
		default:
			header ('Location: /');
		break;
	}
}
else
	header ('Location: /');