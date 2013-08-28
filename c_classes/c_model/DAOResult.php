<?php


class DAOResult {

  var $fetch_index = 0;

	abstract function fetch();
	abstract function fetchAll();
	abstract function count();
	abstract function VOGenerator($res);
  abstract function fetchAll_RAW();
  abstract function destroy();



  function cache_fetch() {

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