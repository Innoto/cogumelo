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

Cogumelo::load('c_model/DAO');
Cogumelo::load('c_model/mysql/MysqlDAOResult');

class MysqlDAO extends DAO
{
	var $VO;

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
	function execSQL($connection, $sql, $val_array = array())
	{

		// obtaining debug data
		$d = debug_backtrace();
		$caller_method = $d[1]['class'].'.'.$d[1]['function'].'()';

 		//set prepare sql
		$stmt = $connection->prepare( $sql ); 

		if( $stmt ) {  //set prepare sql

			$bind_vars_type = $this->getPrepareTypes($val_array);
			$bind_vars_str = "";
			foreach($val_array as $ak=>$vk){
				$bind_vars_str .= ', $val_array['.$ak.']';
			}


			// bind params
			if($bind_vars_type != "") {
				eval('$stmt->bind_param("'. $bind_vars_type .'"'. $bind_vars_str .');');
			}
		    $stmt->execute();


		    if($stmt->error == ''){
		    	return $stmt;
		    }
			else {
				Cogumelo::error( "MYSQL STMT ERROR on ".$caller_method.": ".$stmt->error);
				return false;
			}
		}
		else {
			Cogumelo::error( "MYSQL QUERY ERROR on ".$caller_method.": ".$connection->error);
			return false;
		}
	}


	//
	// get string of chars according prepare type 
	// ex. i:integer, d:double, s:string, b:boolean
	function getPrepareTypes($values_array){

		$return_str = "";
		foreach($values_array as $value) {
			if(is_integer($value)) $return_str.= 'i';
			else
			if(is_string($value)) $return_str.= 's';
			else
			if(is_float($value)) $return_str.= 'd';
			else
			if(is_bool($value)) $return_str.= 'b';
		}

		return $return_str;
	}

	

	


	//
	//	Chose filter SQL from
	//	returns an array( where_string ,variables_array )
	function getFilters($filters){

		$where_str = "";
		$val_array = array();


		if($filters) {
			foreach($filters as $fkey => $filter_val) {
				if( array_key_exists($fkey, $this->filters) ) {
					$fstr = " AND ".$this->filters[$fkey];
					$var_count = substr_count( $fstr , "?");
					for($c=0; $c < $var_count; $c++) {
						$val_array[] = $filter_val;
					}
					$where_str.=$fstr; 

				}
			}
		}

		return array(
				'string' => "WHERE true".$where_str,
				'values' => $val_array
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
	function find($connection, $search, $key = false)
	{
		$VO = new $this->VO();

		if(!$key) {
			$key = $VO->getFirstPrimarykeyId();
		}

		// SQL Query
		$strSQL = "SELECT * FROM `" . $VO::$tableName . "` WHERE `".$key."` = ?;";
	
		if( $res = $this->execSQL($connection, $strSQL, array($search)) ) {
			if($res->num_rows != 0) {
				$DAOres  = new MysqlDAOResult( $this->VO , $res);
				return( $DAOres->fetch());
			}
			else {
				return null;
			}
		}
		else {
			return false;
		}
	}

	//
	//	Generic listItems
	//
	//	Return: array [array_list, number_of_rows]
	function listItems($connection, $filters, $range, $order)
	{

		// where string and vars
		$whereArray = $this->getFilters($filters);
		
		// order string
		$orderSTR = ($order)? $this->orderByString($order): "";


		// range string
				$rangeSTR = ($range != array() && is_array($range) )? sprintf(" LIMIT %s, %s ", $range[0], $range[1]): "";


	
		// SQL Query
		$VO = new $this->VO();
		$strSQL = "SELECT * FROM `" . $VO::$tableName . "` ".$whereArray['string'].$orderSTR.$rangeSTR.";";


		if( $res = $this->execSQL($connection,$strSQL, $whereArray['values']) )
		{
			return new MysqlDAOResult( $this->VO , $res);
		}
		else
		{
			return false;
		}
		
	}


	//
	//	Generic listCount
	//
	//	Return: array [array_list, number_of_rows]
	function listCount($connection, $filters)
	{

		// where string and vars
		$whereArray = $this->getFilters($filters);
	
		// SQL Query
		$VO = new $this->VO();
		$StrSQL = "SELECT count(*) FROM `" . $VO::$tableName . "` ".$whereArray['string'].";";


		if( $res = $this->execSQL($connection,$StrSQL, $whereArray['values']) )	{
				$res->bind_result($num_res);
				$res->fetch();
				return $num_res;
		}
		else {
			return false;
		}
	}


	//
	//	Generic Create
	//
	function create($connection, $VOobj) 
	{

		$cols = array();
		foreach( $VOobj::$cols as $colk => $col) {
			if($VOobj->getter($colk) !== null) {
				$cols[$colk] = $col;
			}
		}


		$campos = '`'.implode('`,`', array_keys($cols)) .'`';


		$valArray = array();
		$answrs = "";
		foreach( array_keys($cols) as $colName ) {
			$val = $VOobj->getter($colName);
			$valArray[] = $val;
			$answrs .= ', ?';
		}

		$strSQL = "INSERT INTO `".$VOobj::$tableName."` (".$campos.") VALUES(".substr($answrs,1).");";
		
		if($res = $this->execSQL($connection, $strSQL, $valArray)) {

			$VOobj->setter($VOobj->getFirstPrimarykeyId(), $connection->insert_id);

			// find row with insert id and return VO
			return $VOobj;
		}
		else {
			return false;
		}
	}
	
	//
	//  Generic Update
	// return: Vo updated from DB
	function update($connection, $VOobj)
	{

		// primary key value
		$pkValue = $VOobj->getter( $VOobj->getFirstPrimarykeyId() );


		// add getter values to values array
		$setvalues = '';
		$valArray = array();
		foreach( $VOobj::$cols as $colk => $col) {
			if($VOobj->getter($colk) !== null) {
				$setvalues .= 'AND '.$colk.'= ? ';
				$valArray[] = $VOobj->getter($colk);
			}
		}

		// add primary key value to values array
		$valArray[] = $pkValue;

		$strSQL = "UPDATE `".$VOobj::$tableName."` SET (".substr($setvalues,3)." WHERE ".$VOobj->getFirstPrimarykeyId()."= ? ;";
		
		if($res = $this->execSQL($connection, $strSQL, $valArray)) {
			return $VOobj;
		}
		else {
			return false;
		}
	}	
	
	//
	//	Generic Deletev
	// 
	function delete($connection, $pkeyIdValue)
	{

		$VO = new $this->VO();
		// SQL Query
		$strSQL = "DELETE FROM `" . $VO::$tableName . "` WHERE `".$VO->getFirstPrimarykeyId()."` = ?;";

		if( $this->execSQL($connection, $strSQL, array($pkeyIdValue)) ){
			return $true;
		}
		else {
			return false;
		}
	}
	
}
?>
