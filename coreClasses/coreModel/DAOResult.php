<?php

/**
 * Result interface from DAO
 *
 * @package Cogumelo Model
 */
abstract class DAOResult {

  var $cacheFetchIndex = 0;

	abstract function fetch();
	abstract function fetchAll();
	abstract function count();
	abstract function VOGenerator($res);
  abstract function fetchAllRaw();
  abstract function resetFetch();


  /**
  * Fetch when query is cached
  *
  * @return object
  */
  function cacheFetch() {

    if (array_key_exists($this->cacheFetchIndex, $this->cache)) {
      $retObj = $this->VOGenerator( $this->cache[$this->cacheFetchIndex] );
      //$retObj = $this->cache[$this->cacheFetchIndex] ;
      $this->cacheFetchIndex++;
    }
    else {
      $retObj = false;
    }

    return $retObj;

  }

  /**
  *  Fetch all when query is cached
  *
  * @return array
  */
  function cacheFetchAll() {
    $list = array();

    foreach( $this->cache as $cached_row) {
      $rowVO = $this->VOGenerator( $cached_row);
      $list[ $rowVO->getter($rowVO->getFirstPrimarykeyId()) ] = $rowVO;
    }

    return $list;
  }

  /**
  * Count elements from caches
  *
  * @return int
  */
  function cache_count() {
    return count($this->cache);
  }


}
