<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('Dump'))
	require_once 'Dump.php';

/**
 * Description of ExportToCsv
 *
 * @author ipm
 */
class DumpCsv extends Dump
{
	public function __construct(\wpdb $db) 
	{		
		parent::__construct($db);
		
		ini_set("auto_detect_line_endings", true);
	}
	
	/**
	 * create csv from array
	 * @param array $data data to write to
	 * @return csv as string
	 */
	public function getCsv(array $data)
	{	
		if (count($data) == 0)
			return null;
		
		ob_start();
		$file = fopen("php://output", 'w');

		fputcsv($file, array_keys(reset($data)));
		foreach ($data as $row)
		{
			fputcsv($file, $row);
		}
		
		fclose($file);
		
		return ob_get_clean();
	}
	
	/**
	 * read csv file to array
	 * @param string $file_path
	 * @return array
	 */
	public function readCsv($file_path)
	{
		$rows = array_map('str_getcsv', file($file_path));
		$header = array_shift($rows);
		$csv = array();
		foreach ($rows as $row)
		{
			$csv[] = array_combine($header, $row);
		}
		
		return $csv;
	}
	
	/**
	 * send headers
	 * @param array $data to create csv
	 * @param string $filename to send in headers
	 */
	public function downloadCsv(array $data, $filename='')
	{
		$data = $this->getCsv($data);
		
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename='.$filename.'_'.date('Ymd').'_'.date('His').'.csv');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Transfer-Encoding: binary');
		
		exit($data);
	}
	
	public function uploadCsv($path)
	{
		
	}
}