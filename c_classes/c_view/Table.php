<?php


/**
* Table Class
*
* This class provides a backend to comunicate a js-ajax table system 
* with a cogumelo data controller, selecting what cools we want to show and 
* what to do with data in special cases.
*
* @author: pablinhob
*/

class Table
{
	public $post_data;
	public $cols = array();
	public $col_opts = array();
	

	/*
	*  @param array $data  generally is the full $_POST data variable
	*/
	function __construct($data)
	{

	}

	/*
	* @param string $col_id id of col in VO
	* @param string $col_name  col name, we can use function T() to translate that. if false it gets the VO's col description.
	* @return void
	*/
	function addCol($col_id, $col_name = false) {

	}


	/*
	* @param string $col_id id of col added with addCol method0
	* @param mixed $value the regular expression to match col's row value
	* @param string $transform is the result that we want to provide when 
	*  variable $value matches (Usually a text). Can be too an operation with other cols
	* @return void
	*/
	function colOpt($col_id, $value, $transform) {

	}



	/*
	* @return array filters
	*/
	function getFilters() {

	}

	/*
	* @return array range
	*/
	function getRange() {

	}

	/*
	* @return array order
	*/
	function getOrder() {

	}


	/*
	* @param object $control: id of col added with addCol method0
	* @param mixed $value the regular expression to match col's row value
	* @param string $transform is the result that we want to provide when 
	*  variable $value matches (Usually a text). Can be too an operation with other cols
	* @return void
	*/
	function return_table_data($control) {

		// is executing a command (delete or update)


    	header("Content-Type: application/json"); //return only JSON data
 		echo json_encode($control->listItems() );
	}


}