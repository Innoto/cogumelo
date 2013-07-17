<?php

Cogumelo::load('c_model/DAOResult');


class MysqlDAOResult extends DAOResult {

	var $result;
	var $VO;


	function __construct($voObj, $result) {
		$this->VO = $voObj;
		$this->result = $result;
	}


	// fetch just one result
	function fetch() {
		$row = $this->result->fetch_assoc();

		if($row)
			$ret_obj = $this->VOGenerator( $row );
		else
			$ret_obj = false;

		return $ret_obj;
	}


	// resturn an VO array with all the result rows
	function fetchAll() {
		$list = array();

		while( $row = $this->mysql_result->fetch() ) {
			$rowVO = $this->VOGenerator( $row);
			$list[ $rowVO->getter($rowVO->getFirstPrimarykeyId()) ] = $rowVO;
		}
		
		return $list;
		
	}

	// count total numer of query result
	function count() {
		return 	$this->resutl->count();
	}


	//
	// Creates an VO from a query result;
	// Returns: Obj/false
	function VOGenerator($row) // antes utilizaba & na variable res
	{
		return new $this->VO($row);

	}



	function __destroy() {
		$this->result->close();
	}

}