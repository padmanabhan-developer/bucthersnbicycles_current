<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('Tables'))
	include_once dirname(__FILE__) . '/../Tables.php';

/**
 * Description of GoodsDictionary
 *
 * @author ipm
 */
class GoodsDictionary
{
	/**
	 * wordpress database instance
	 * @var wpdb
	 */
	protected $db;
	
	/**
	 *	RainTPL instance
	 * @var RainTPL 
	 */
	protected $tpl;

	
	public function __construct(wpdb $wpdb, RainTPL $tpl)
	{
		$this->db = $wpdb;
		$this->tpl = $tpl;
	}
	
	/**
	 * check if commit was successfull
	 * 
	 * @return boolean
	 */
	protected function commit()
	{
		if(mysql_query("COMMIT") === false)
			return false;
			
		return true;
	}
	
	/**
	 * remove all control characters from string
	 * @param string $str where to remove
	 * @param string $what characters to remove
	 * @param string $with characters to replace with
	 * @return string trimmed string
	 */
	protected function trim_all( $str , $what = NULL , $with = ' ' )
	{
		if( $what === NULL )
		{
			//	Character      Decimal      Use
			//	"\0"            0           Null Character
			//	"\t"            9           Tab
			//	"\n"           10           New line
			//	"\x0B"         11           Vertical Tab
			//	"\r"           13           New Line in Mac
			//	" "            32           Space

			$what	= "\\x00-\\x20";	//all white-spaces and control chars
		}

		return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
	}
}