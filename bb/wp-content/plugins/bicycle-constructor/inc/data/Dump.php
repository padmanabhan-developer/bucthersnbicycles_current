<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dump
 *
 * @author ipm
 */
class Dump 
{
	/**
	 * wpdb
	 * @var \wpdb 
	 */
	protected $db;
	
	protected $data;
	
	public function __construct(\wpdb $db)
	{
		$this->db = $db;
	}
	
	/**
	 * 
	 * @param string $table Table to select from
	 * @param array $fields fields to select
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 * @return mixed Database query results
	 */
	public function fetchFromTable($table, array $fields = array(), $output = ARRAY_A)
	{
		$f = (count($fields) == 0) ? '*' : implode(', ', $fields);
		$query = "SELECT $f FROM $table";
		
		$this->data = $this->db->get_results($query, $output);
		
		return $this->data;
	}
	
	/**
	 * helper function
	 * @param array $data data to insert
	 * @return array $keys keys to insert quoted with `
	 */
	protected function getKeysForInsert(array $array)
	{
		if(!is_array($array[0]))
			$keys = array_values ($array);
		else
			$keys = array_keys($array[0]);
		
		for($i=0; $i<count($keys); $i++)
		{
			$keys[$i] = '`' . $keys[$i] . '`';
		}
		
		return $keys;
	}
	
	/**
	 * prepare query for safe insert
	 * @param array $data data to insert
	 * @return string quoted string like (1, "String value", "")
	 */
	protected function prepareForMultiple(array $data)
	{
		if(is_array($data[0]))
		{
			$queries = array();
			foreach($data as $d)
			{
				array_push($queries, $this->prepareAssocArrayForMultipleInsert($d));
			}
			
			return implode(',', $queries);
		}
		else
		{
			return $this->prepareAssocArrayForMultipleInsert($data);
		}
	}
	
	protected function prepareAssocArrayForMultipleInsert(array $d)
	{
		$values = array_values($d);
		$query = array();
		foreach ($values as $v)
		{
			if (is_int($v))
				array_push ($query, '%d');
			else if(is_float($v))
				array_push ($query, '$f');
			else
				array_push ($query, '%s');
		}
		
		return '('.$this->db->prepare(implode(',', $query), $values).')';
	}


	/**
	 * helper function
	 * @param array $data data to insert
	 * @return array keys for update like `key` = VALUES(`key`)
	 */
	protected function getUpdateKeysForInsert(array $data)
	{
		$keys = $this->getKeysForInsert($data);
		$k = array();
		foreach ($keys as $key)
		{
			array_push($k, $key . ' = VALUES(' . $key . ')');
		}
		
		return $k;
	}
	
	/**
	 * insert
	 * @param string $table table to insert to
	 * @param array $data data to insert. must be indexed array containing assoc array or assoc array
	 * @return boolean
	 */
	public function insertIntoTable($table, array $data)
	{
		$query = "INSERT INTO $table (" . implode(',', $this->getKeysForInsert($data)) . ")
			VALUES " . $this->prepareForMultiple($data)
			. " ON DUPLICATE KEY 
				UPDATE " . implode(',', $this->getUpdateKeysForInsert($data));
		
		$this->db->query($query);
		
		if($this->db->last_error)
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