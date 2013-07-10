<?php

Cogumelo::load('c_model/DAOResult');


class MysqlDAOResult extends DAOResult {

	var $resutl;
	var $VO;


	function __construct($voObj, $resutl) {
		$this->resutl = $resutl;
	}


	// fetch just one result
	function fetch() {
		return $this->VOGenerator( $this->resutl->fetch() );
	}


	// resturn an VO array with all the result rows
	abstract function fetchAll() {
		$list = array();

		while( $row = $this->mysql_result->fetch() ) {
			$rowVO = $this->VOGenerator( $row);
			$list[ $rowVO->getter($rowVO->getFirstPrimarykeyId()) ] = $rowVO;
		}
		
		return $list;
		
	}

	// count total numer of query result
	abstract function count() {
		return 	$this->resutl->count();
	}


	//
	// Creates an VO from a query result;
	// Returns: Obj/false
	function VOGenerator($res) // antes utilizaba & na variable res
	{
		// IMPORTANTE: Avanza cursor en $res !!!
		$newObj = false;
		if( $row = mysqli_fetch_assoc($res) ) {
			$comando = '$newObj = new $this->VO($row);';

			eval($comando);
		}
	
		return $newObj;
	}



	function __destroy() {
		$this->result->close();
	}

}