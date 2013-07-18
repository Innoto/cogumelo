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

	/*
	*	$client_data must have this structure
	*	'filters_common' = array('tab'=>1),
	*   'filters' => array('search' => 'Xan o a curva'),
	*	'range' => array(10,20)
	*/
	var $client_data = array();
	var $cols_def = array();
	var $allow_methods = array();
	

	/*
	*  @param array $data  generally is the full $_POST data variable
	*/
	function __construct($postdata)
	{
		$this->client_data = json_decode($postdata['cogumelo_table']);
	}

	/*
	* @param string $col_id id of col in VO
	* @param string $col_name  col name, we can use function T() to translate that. if false it gets the VO's col description.
	* @return void
	*/
	function setCol($col_id, $col_name = false) {
		$this->cols_def[$col_id] = array('name' => $col_name, 'rule' => false); 
	}


	/*
	* @param string $col_id id of col added with setCol method0
	* @param mixed $regexp the regular expression to match col's row value
	* @param string $action is the result that we want to provide when 
	*  variable $value matches (Usually a text). Can be too an operation with other cols
	* @return void
	*/
	function colRule($col_id, $regexp, $code) {
		$this->cols_def[$col_id]['rule'] = array('regexp' => $regexp, 'code' => $code );
	}


	/*
	* @param string $allow_methods array of method names allowed in this table view
	* @return void
	*/
	function allowMethods( $allow_methods = array() ) {
		$this->allow_methods = $allow_methods;
	}


	/*
	* @param object $control: is the data controller
	* @return string JSON with table
	*/
	function return_table_json($control) {

		// if is executing a method ( like delete or update) and have permissions to do it
		if($this->client_data->method && array_key_exists( $this->client_data->method->name, $this->allow_methods ))
		{
			eval( '$control->'. $this->client_data->method->name. '('.$this->client_data->method->value.')');
		}


		// doing a query to the controller
		$lista = $control->listItems( array_merge($this->client_data->filters_common, $this->client_data->filters) , $this->client_data->range, $this->client_data->order);


		// printing json table...
    	header("Content-Type: application/json"); //return only JSON data
    	echo "{";
    	echo "'total_table_rows':" . $control->listCount($this->client_data->filters_common) . ","; // only assign common filters
    	echo "'cols_def':".json_encode($this->cols_def).",";

		while( $rowVO = $lista->fetch() ) {
			// dump rowVO into row
			$row = array();

			foreach($this->cols_def as $col_def_key => $col_def){
				$row[$col_def_key] = $rowVO->getter($col_def_key);

			}
			
			// modify row value if have colRules
			foreach($this->cols_def as $col_def_key => $col_def){
				// if have rules and matches with regexp
				if($col_def['rule'] && preg_match( $col_def['rule']['regexp'], $row[$col_def_key] )) {
					eval('$row[$col_def_key]] = '.$col_def->rule->code.';');
				}
			}

			echo json_encode($row); 

		}
		echo "}";
	}


}