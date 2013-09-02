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
Cogumelo::load('c_model/DAOCache');
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
	function execSQL(&$connectionControl, $sql, $val_array = array())
	{

		$connectionControl->connect();

		// obtaining debug data
		$d = debug_backtrace();
		$caller_method = $d[1]['class'].'.'.$d[1]['function'].'()';

 		//set prepare sql
		$connectionControl->stmt = $connectionControl->db->prepare( $sql ); 

		if( $connectionControl->stmt ) {  //set prepare sql

			$bind_vars_type = $this->getPrepareTypes($val_array);
			$bind_vars_str = "";
			foreach($val_array as $ak=>$vk){
				$bind_vars_str .= ', $val_array['.$ak.']';
			}


			// bind params
			if($bind_vars_type != "") {
				eval('$connectionControl->stmt->bind_param("'. $bind_vars_type .'"'. $bind_vars_str .');');
			}
		    $connectionControl->stmt->execute();


		    if($connectionControl->stmt->error == ''){
		    	if($ret = $connectionControl->stmt->get_result()){
		    		$ret_data = $ret;
		    	}
		    	else{
		    		$ret_data = true;
		    	}
		    }
			else {
				Cogumelo::error( "MYSQL STMT ERROR on ".$caller_method.": ".$stmt->error.' - '.$sql);
				$ret_data = false;
			}

		}
		else {
			Cogumelo::error( "MYSQL QUERY ERROR on ".$caller_method.": ".$connectionControl->db->error.' - '.$sql);

			$ret_data = false;
		}

		return $ret_data;
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

			$VO = new $this->VO();


			foreach($filters as $fkey => $filter_val) {

				if( array_key_exists($fkey, $this->filters) ) {
					$fstr = " AND ".$this->filters[$fkey];
				}
				else if(array_key_exists($fkey, $VO::$cols) ) {
					$fstr = " AND ".$VO::$tableName.".".$fkey." = ?";
				}
				else { 
					Cogumelo::error( $fkey." not found on wherearray or into (".$this->VO.") VO. Omiting..." );
				}

				// where string
				$where_str.=$fstr; 


				// dump value or array value into $values array
				if( is_array($filter_val) ) {
					foreach($filter_val as $val) {
						$val_array[] = $val;
					}
				}
				else {
					$var_count = substr_count( $fstr , "?");
					for($c=0; $c < $var_count; $c++) {
						$val_array[] = $filter_val;
					}
				}
			


			}
		}

		return array(
				'string' => "WHERE true".$where_str,
				'values' => $val_array
			);
	}

	
	


	//
	//	Generic Find by key
	//
	function find(&$connectionControl, $search, $key = false, $cache = false)
	{
		$VO = new $this->VO();

		if(!$key) {
			$key = $VO->getFirstPrimarykeyId();
		}

		$filter = array($key => $search);

		if($res = $this->listItems($connectionControl, $filter, false, false, $cache) ) {
			return $res->fetch();
		}
		else 
			return false;
			
	}

	//
	//	Generic listItems
	//
	//	Return: array [array_list, number_of_rows]
	function listItems(&$connectionControl, $filters, $range, $order, $cache = false)
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


		if ( $cache && DB_ALLOW_CACHE  )
		{
			$queryId = md5($strSQL.serialize($whereArray['values']));
			$cached =  new DAOCache();

			if($cache_data = $cached->getCache($queryId)  ) {
				// With cache, serving cache ...
				Cogumelo::log('Using cache: cache Get with ID: '.$queryId );
				$queryID = $daoresult = new MysqlDAOResult( $this->VO , $cache_data, true); //is a cached result
			}
			else{
				
				// With cache, but not cached yet. Caching ...
				if($res = $this->execSQL($connectionControl,$strSQL, $whereArray['values'])) {
					Cogumelo::log('Using cache: cache Set with ID: '.$queryId );
					$daoresult = new MysqlDAOResult( $this->VO , $res);
					$cached->setCache($queryId, $daoresult->fetchAll_RAW() );
				}
				else{
					$daoresult = false;
				}
			}
		}
		else
		{
			//	Without cache!
			if($res = $this->execSQL($connectionControl,$strSQL, $whereArray['values']))
				$daoresult = new MysqlDAOResult( $this->VO , $res);
			else
				$daoresult = false;
		}

		return $daoresult;


	}


	//
	//	Generic listCount
	//
	//	Return: array [array_list, number_of_rows]
	function listCount(&$connectionControl, $filters)
	{

		// where string and vars
		$whereArray = $this->getFilters($filters);
	
		// SQL Query
		$VO = new $this->VO();
		$StrSQL = "SELECT count(*) as number_elements FROM `" . $VO::$tableName . "` ".$whereArray['string'].";";


		if( $res = $this->execSQL($connectionControl,$StrSQL, $whereArray['values']) )	{

				//$res->fetch_assoc();
				$row = $res->fetch_assoc();
				return $row['number_elements'];
		}
		else {
			return false;
		}
	}


	//
	//	Generic Create
	//
	function create(&$connectionControl, $VOobj) 
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


		if($res = $this->execSQL($connectionControl, $strSQL, $valArray)) {

			$VOobj->setter($VOobj->getFirstPrimarykeyId(), $connectionControl->db->insert_id);

			return $VOobj;

		}
		else {
			return false;
		}
	}
	
	//
	//  Generic Update
	// return: Vo updated from DB
	function update(&$connectionControl, $VOobj)
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
		
		if($res = $this->execSQL($connectionControl, $strSQL, $valArray)) {
			return $VOobj;
		}
		else {
			return false;
		}
	}	
	
	//
	//	Generic Deletev
	// 
	function delete(&$connectionControl, $pkeyIdValue)
	{

		$VO = new $this->VO();
		// SQL Query
		$strSQL = "DELETE FROM `" . $VO::$tableName . "` WHERE `".$VO->getFirstPrimarykeyId()."` = ?;";

		if( $this->execSQL($connectionControl, $strSQL, array($pkeyIdValue)) ){
			return $true;
		}
		else {
			return false;
		}
	}
	
}
?>
