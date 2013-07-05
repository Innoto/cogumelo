<?php
/*
Cogumelo v0.5 - Innoto S.L.
Copyright (C) 2010 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
USA.
*/

Cogumelo::load('c_model/DAO')

class MysqlDAO extends DAO
{
	//
	// Creates an "ORDER BY" String from $ORDER array
	//
	function orderByString($ORDArray)
	{
		// Direction (ASC, DESC) Array
		if( is_array($ORDArray, $var_array) )
		{
			$orderSTR = " ORDER BY ";
			$coma = "";
			foreach ($ORDArray as $elementK => $elementV)
			{
				if ($elementV < 0)
					$orderSTR .= $coma .$elementK." DESC";
				else
					$orderSTR .= $coma .$elementK." ASC";
				
				$coma=", ";
			}
			return $orderSTR;
		}
		else
			return "";
	}

	
	//
	// Execute a SQL query command
	//
	function execSQL($connection, $sql, $val_array)
	{
		
		$stmt = $connection->prepare( $sql ); // set prepare sql

		$bind_array = array_merge($this->getPrepareTypes() , $val_array);

		call_user_func_array(array(&$stmt, 'bind_param'), $bind_array);
		
	    $stmt->execute();
	    $result = $stmt->get_result();

		// log SQL line if LOG_RAW_SQL is enabled (Enable it in setup.inc under your responsability)
		//if(LOG_RAW_SQL === true) Cogumelo::addLog('SQL EXEC: '.$sql , 'RAW_SQL_EXEC'); 


		// obtaining debug data
		$d = debug_backtrace();
		$caller_method = $d[1]['class'].'.'.$d[1]['function'].'()';

		// procesing query result
		if( !$c )
		{
			// log query error
			Cogumelo::error( $caller_method.": {$connection->error} MYSQL QUERY: ".$sql);

			return false;
		}		
		else
		{
			return result;
		}	

	}


	//
	// get string of chars according prepare type 
	// ex. i:integer, d:double, s:string, b:boolean
	function getPrepareTypes($values_array){

		$return_str = "";
		foreach($values_array) {
			if(is_integer($value)) $return_str.= 'i';
			else
			if(is_string($value)) $return_str.= 's';
			else
			if(is_double($value)) $return_str.= 'd';
			else
			if(is_boolean($value)) $return_str.= 'b';
		}

		return $return_str;
	}

	
	//
	// Creates an VO from a query result;
	// Returns: Obj/false
	function VOGenerator(&$res)
	{
		// IMPORTANTE: Avanza cursor en $res !!!
		$newObj = false;
		if( $row = mysqli_fetch_assoc($res) ) {
			$comando = '$newObj = new $this->VO($row);';

			eval($comando);
		}
	
		return $newObj;
	}
	
	//
	// Creates an VO array from a query result;
	// Returns: Obj's Array/false
	function VOArrayGenerator($res)
	{
		$list = false;
		
		while( $rowVO = $this->VOGenerator( $res) ) {
			$list[ $rowVO->getter($this->VO::$keyId) ] = $rowVO;
		}
		
		return $list;
	}

	//
	//	Chose filter SQL from
	//	returns an array( where_string ,variables_array )
	function getFilters($filters){

		$where_str = "";
		$val_array = array();

		foreach($filters as $fkey => $filter_val) {
			if( array_key_exists($fkey, $this->filters) ) {
				$fstr = " AND ".$this->filters[$fkey];
				$var_count = substr_count( $fstr , "?");
				for($c=0; $c < $var_count; $c++) {
					$val_array[] = $filter_val;
				}

			}
		}


		return array(
				'str' => "WHERE true".$where_str,
				'vars' => $val_array
			);
	}

	
	




	 /****************************
	*******************************
		GENERIC ENTITY METHODS
	*******************************
	 *****************************/

	//
	//	Generic Find by key
	//
	public static function find($search, $key = false)
	{
		if(!$key) {
			$key = $this->VO::$keyId;
		}

		// SQL Query
		$StrSQL = "SELECT * FROM `" . $this->VO::$tableName . "` WHERE `".$key."` = ".$search;
	
		if( !$res = self::execSQL($connection, $StrSQL) )
		{
			Cogumelo::Error(__METHOD__.": {$connection->error} QUERY: ".$StrSQL, 1001);
			return false;
		}
		else
		{
			Cogumelo::Log("SQL Query: ".__METHOD__.": ({$res->num_rows} rows found)".$StrSQL, 5);
			if($res->num_rows != 0)
				return self::VOGenerator($VO, $res);
			else
				return false;
		}
	}

	//
	//	Generic list
	//
	//	Return: array [array_list, number_of_rows]
	function list($connection, $range, $order, $filter)
	{
		
		$whereSTR = $this->generateFilter($connection, $filter);
		
		// ORDER IT !
		if($ORDER)
			$orderSTR = $this->orderByString($order);
		else
			$orderSTR =" ";
		
		// If is RANGE
		if(is_array($range))
			$rangeSTR = sprintf(" LIMIT %s, %s ", $RANGE[0], $RANGE[1]);
		//else
			//$rangeSTR = sprintf(" LIMIT %s", $RANGE);
		
		// SQL Query
		$StrSQL = "SELECT * FROM `" . $VO::$tableName . "` ".$whereSTR.$orderSTR.$rangeSTR.";";


		if( !$res = self::execSQL($connection,$StrSQL) )
		{
			Cogumelo::Error(__METHOD__.": {$connection->error} QUERY: ".$StrSQL, 1001);
			return false;
		}
		else
		{
			Cogumelo::Log("SQL Query: ".__METHOD__.": ({$res->num_rows} rows found)".$StrSQL, 5);
			if($res->num_rows != 0){
				return self::VOArrayGenerator($VO, $res);
			}
		else
			return false;
		}
	}


	//
	//	Generic Create
	//
	function create($VOobj) 
	{
		// SQL Query
		$campos = '`'.implode('`,`', array_keys( $VOobj::$keys)) .'`';

		$valArray = array();
		foreach( array_keys($VOobj::$keys) as $colName ) {
			$val = eval('return $VOobj->getter("'.$colName.'");');
			$valArray[] = "'".$connection->real_escape_string( $val )."'";
		}
		$valores = implode(',', $valArray);
		
		$StrSQL = "INSERT INTO `".$VO::$tableName."` (".$campos.") VALUES(".$valores.");";
		if(!$customlogtext)	$customlogtext = $StrSQL;
		
		if($res = self::execSQL($connection,$StrSQL)) {
			Cogumelo::Log("SQL Query: mysql DAO:Create(): ".$customlogtext, 5);
			$VOobj->setter($VOobj::$keyId, $connection->insert_id);
			return $VOobj;
		}
		else {
			Cogumelo::Error(__METHOD__.$connection->error." QUERY: ".$customlogtext);
		}
	}
	
	//
	//  Generic Update
	// return: Vo updated from DB
	function update($connection, $VOobj)
	{
		$listvalues=" ";
	
		foreach(array_keys($VOobj::$keys) as $k){
			if($VOobj->getter($k) !== null){
				if($listvalues != " ")
					$listvalues .= " ,";

				$listvalues .= " ".$k." = '". $connection->real_escape_string($VOobj->getter($k)) ."' ";
			}
		}
	
		$StrSQL = "UPDATE `".$this->VO::$tableName."` SET
			    ".$listvalues." 
			  WHERE `". $VO::$keyId ."` = " . $connection->real_escape_string($VOobj->getter($VO::$keyId)). " ;";											


		if($res = self::execSQL($connection,$StrSQL)) {
			Cogumelo::Log("SQL Query: mysql DAO:Create(): ".$customlogtext, 5);
			return self::Find($VO, $connection, $connection->real_escape_string($VOobj->getter($VO::$keyId)));
				
		}
		else {
			Cogumelo::Error(__METHOD__.$connection->error." QUERY: ".$customlogtext);
		}
	
	}	
	
	//
	//	Generic Delete
	// 
	function delete($rowID)
	{
		// SQL Query
		$StrSQL = "DELETE FROM `" . $VO::$tableName . "` WHERE `".$VO::$keyId."` = ".$rowID;

	
		if( !$res = self::execSQL($connection, $StrSQL) )
		{
			Cogumelo::Error(__METHOD__.": {$connection->error} QUERY: ".$StrSQL, 1001);
			return false;
		}
		else
		{
			Cogumelo::Log("SQL Query: ".__METHOD__.": ".$StrSQL, 5);
			return true;
		}
	}
	
}
?>
