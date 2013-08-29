<?php

Cogumelo::load('c_model/DAOResult');


class MysqlDAOResult extends DAOResult {

	var $result;
	var $VO;
	var $cache = false;


	function __construct($voObj, $result, $is_cached_result = false) {
		$this->VO = $voObj;
		$this->result = $result;
		if( $is_cached_result ) {
			$this->cache = $result;
		}
	}


	// fetch just one result
	function fetch() {

		if($this->cache){ // is cached query ?
			$ret_obj = $this->cache_fetch();
		}
		else {
			$row = $this->result->fetch_assoc();

			if($row)
				$ret_obj = $this->VOGenerator( $row );
			else
				$ret_obj = false;
		}

		return $ret_obj;
	}


	// resturn an VO array with all the result rows
	function fetchAll() {

		if($this->cache){ // is cached query ?
				$list = $this->cache_fetchAll();
		}
		else {

				$list = array();

				while( $row = $this->result->fetch_assoc() ) {
					$rowVO = $this->VOGenerator( $row);
					$list[ $rowVO->getter($rowVO->getFirstPrimarykeyId()) ] = $rowVO;
				}
		}
		
		return $list;
		
	}

	// count total numer of query result
	function count() {

		if($this->cache){ // is cached query ?
			$ret = $this->cache_count();
		}
		else {
			$ret = $this->result->count();
		}

		return $ret;
	}


	//
	// Creates an VO from a query result;
	// Returns: Obj/false
	function VOGenerator($row) // antes utilizaba & na variable res
	{
		return new $this->VO($row);

	}

	function fetchAll_RAW() {
		$list = array();

		while( $row = $this->result->fetch_assoc() ) {
			$list[] = $row;
		}
		
		return $list;	}

	function __destroy() {
		$this->result->close();
	}

}