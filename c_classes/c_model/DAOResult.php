<?php


abstract class DAOResult {

  var $cache_fetch_index = 0;

	abstract function fetch();
	abstract function fetchAll();
	abstract function count();
	abstract function VOGenerator($res);
  abstract function fetchAll_RAW();


  function cache_fetch() {

    $ret_obj = false;
    $c_count = 0;

    foreach($this->cache as $cached_row) {
      if($c_count == $this->cache_fetch_index) {
        $ret_obj = $this->VOGenerator($cached_row);
      }
    }

    return $ret_obj;
  
  }

  function cache_fetchAll() {
    $list = array();

    foreach( $this->cache as $cached_row) {
      $rowVO = $this->VOGenerator( $row);
      $list[ $rowVO->getter($rowVO->getFirstPrimarykeyId()) ] = $rowVO;
    }
    
    return $list;
  }

  function cache_count() {
    return count($this->cache);
  }


}